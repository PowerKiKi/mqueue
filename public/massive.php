<form action="" method="post"><textArea name="search"
	style="width: 800px; height: 280px;"><?php if (isset($_POST['search'])) echo $_POST['search']; ?></textArea>
<input type="submit"></input></form>

<?php
require('../application/debug.php');

$t = "http://translate.google.com/translate_a/t?client=t&text=%EB%A8%B9%EB%8B%A4%EB%A8%B9%EB%8B%A4&sl=en&tl=ko&otf=2&pc=1";

set_time_limit(3600);

$tag = "%SEARCH%";
$imdbUrl = "http://www.imdb.com/find?q=%SEARCH%;s=tt";
$dir = sys_get_temp_dir() . 'imdb\\';

function fetchResults($dir)
{
	if (!isset($_POST['search']))
		return;

	if (!is_dir($dir))
	mkdir($dir, 0777, true);

	foreach (explode("\n", $_POST['search']) as $line)
	{
		$line = trim($line);
		if (!$line)
		continue;
			
		$url = str_replace($tag, urlencode($line), $imdbUrl);

		$file = '';
		$file = file_get_contents($url);
		$file = $tag . $line . $tag . "\n\n" . $file;

		$filename = $dir . str_replace(array('\\', '/', ':', '?', '*', '<', '>', '|', '"'), '_', $line) . '.htm';
		echo $filename . "<br>\n";
		flush();
		$fp = fopen($filename, 'w');
		fwrite($fp, $file);
		fclose($fp);

	}
}

function merge($dir, $tag)
{
	$notFound = array();
	$d = dir($dir);
	while (false !== ($entry = $d->read()))
	{
		if (!is_file($dir . $entry))
			continue;
			
		$file = file_get_contents($dir . $entry);
		if (!$file)
		continue;

		preg_match("/$tag(.*)$tag/", $file, $m);
		//
		$title = $m[1];
		
		
		$r = '|(<a href="/title/tt\d\d\d\d\d\d\d/" onclick[^>]*>.*)</td></tr>|U';
		preg_match_all($r, $file, $m);
	
		if (!count($m[1]) && trim($file))
		{
			$r = '|<link rel="canonical" href="(http://www.imdb.com/title/tt\d\d\d\d\d\d\d/)|U';
			preg_match_all($r, $file, $m);
			if (!count($m[1]))
			{
				$notFound[] = $title;
				continue;
			}
			$m[1][0] = '<a href="'.$m[1][0]."\">" . $title . "</a>";
		}
		
	
		echo "<h1>$title</h1>";
		echo '<div style="height: 10em; overflow: scroll; background: #EEEEEE">';
		echo "<table>";
			
		$i = 0;
		foreach ($m[1] as $x)
		{
			$x = str_replace('href="/title/tt', 'href="http://www.imdb.com/title/tt', $x);
			if (strpos('<td>', $x))
				echo "<tr><td>$x</td></tr>";
			else
				echo "<tr><td></td><td>$x</td></tr>";
				
			if (++$i > 10)
				break;
		}
		
		echo "</table>";
		echo "</div>";
	}
	$d->close();

	echo "<h1>::: NOT FOUND :::</h1>";
	foreach ($notFound as $t)
	{
	
		echo "<br>\n" . $t;
	}
}

merge($dir, $tag);