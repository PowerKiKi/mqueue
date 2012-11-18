<?php

/**
 * Search Engine to find sources for movies, given its title.
 * It relies on Nova to query several popular websites.
 */
class SearchEngine {
	
	/**
	 * Returns the command for the appropriate version of Nova
	 * @return string
	 */
	protected function getNovaCmd()
	{
		$cmd = shell_exec('python --version 2>&1');
		preg_match('/\\d+\\.\\d+\\.\\d+/', $cmd, $m);
		$version = $m[0];
		if (version_compare($version, '3.0.0', '>='))
		{
			return __DIR__ . '/searchengine/nova3/nova2.py';
		}
		else
		{
			return __DIR__ . '/searchengine/nova/nova2.py';
		}
	}
	
	/**
	 * Parse string content and return an array of unique sources
	 * @param string $content
	 * @return array
	 */
	public function parse($content)
	{
		$data = array();
		$keys = array('link', 'name', 'size', 'seeds', 'leech', 'engine_url', 'page');
		$duplicates = array();
		foreach (explode(PHP_EOL, trim($content)) as $line)
		{
			$values = explode('|', trim($line));
			if (count($keys) == count($values))
			{
				$source = array_combine($keys, $values);
				$duplicateKey = $source['name'] . $source['size'];
				if (!array_key_exists($duplicateKey, $duplicates))
				{
					$data[$source['link']] = $source;
					$duplicates[$duplicateKey] = true;
				}
			}
		}
		
		return $data;
	}
	
	/**
	 * Search for the given title and return an array of sources
	 * @param string $title
	 * @return array sources
	 */
	public function search($title)
	{
		$cmd = $this->getNovaCmd() . ' all movies ' . escapeshellarg(str_replace(' ', '+', $title)) . ' 2>&1';
		$content = `$cmd`;
	
		$path = sys_get_temp_dir() . '/mqueue_' . $title;
		file_put_contents($path, $content);
		
		return $this->parse($content);
	}
	
	/**
	 * Returns the given name simplified as much as possible
	 * @param string $name
	 * @return string
	 */
	protected function cleanName($name)
	{
		// Insert space before uppercase letters
		$name = preg_replace('/([A-Z])/', ' \1', $name);
		$name = strtolower($name);
		
		$name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name); // Get rid of all accents
		
		// Replace special character with common representation
		$name = str_replace('&', 'and', $name);
		

		// Remove all grouped things (may include team name at the beginning of name)
		// Or remove incomplete grouped things at the end of string
		$name = preg_replace('/\(.*(\)|$)/U', ' ', $name);
		$name = preg_replace('/\[.*(\]|$)/U', ' ', $name);
		$name = preg_replace('/\{.*(\}|$)/U', ' ', $name);

		// Keep only alphanum character
		$name = preg_replace('/[^[:alnum:]]/', ' ', $name);

		// Keep only a single space character
		$name = preg_replace('/[[:space:]]+/', ' ', $name);
		$name = trim($name);

		return $name;
	}
	
	/**
	 * Compute scores of all sources according to the title searched
	 * @param string $title
	 * @param array $sources
	 * @return array
	 */
	public function computeScores($title, array $sources)
	{
		$rules = array(
			// @see http://en.wikipedia.org/wiki/Pirated_movie_release_types
			'/\b(dvdrip|bdrip|brrip|blu-ray|bluray|bdr|bd5|bd9)\b/i' => 80, // Good sources
			'/\b(dvdr|dvd-full|full-rip|iso|dvd-5|dvd-9)\b/i' => 40, // Ok sources
			'/\b(dsr|dsrip|dthrip|dvbrip|hdtv|pdtv|tvrip|hdtvrip|vodrip|vodr)\b/i' => 10, // Soso sources
			'/\b(cam|camrip|ts|telesync|pdvd|wp|workprint|tc|telecine|ppv|ppvrip|scr|screener|dvdscr|dvdscreener|bdscr|ddc|r5)\b/i' => -20, // Low quality or unsure sources

			'/\b(maxspeed|axxo|dimension|fxg)\b/i' => 50, // Well known teams
			'/\b(swesub|nlt-release)\b/i' => -30, // Avoid teams specialized in foreign versions
			'/\b(french|fra|truefrench|italian|ita|russian|german)\b/i' => -30, // Avoid dubbed language
			'/\b1080p\b/i' => 20,
			'/\b720p\b/i' => 30, // Favor 720p instead of 1080p because of the filesize and better "compatibility" for low powered computer
			'/\b(x264|xvid)\b/i' => 20, // Good formats
			'/\b(uncut|unrated|extended|director\'s cut|director cut)\b/i' => 20, // Director's cut version is supposedly better
		);
		
		$cleanTitle = $this->cleanName($title);
		preg_match('/((18|19|20)\d{2})(– )?\)$/', $title, $m);
		$year = $m[1];

		foreach ($sources as &$source)
		{
			$identity = 0;
			$quality = 0;
			
// TODO: re-evaluate the alternate identity method with more data or drop it entirely
//			$yearPattern = '/(\D)' . $year . '\D.*$/';
//			if (preg_match($yearPattern, $source['name']))
//			{
//				$sourceWithoutYear = trim(preg_replace($yearPattern, '\1', $source['name']));
//				$cleanSource = $this->cleanName($sourceWithoutYear);
//
//				// Boost identity because we found the year
//				$identity += 20;
//			}
//			else
//			{
//
//				$length = strlen($cleanTitle);
//				$pattern = '/^(.{0,' . $length . '}\w*)/';
//				preg_match($pattern, $this->cleanName($source['name']), $m);
//				$cleanSource = $m[1];
//			}
//			v($cleanTitle, $year, $source['name'], $sourceWithoutYear, $cleanSource, $identity);
			
			// Identity mostly is matching title in source name
			$cleanSource = substr($this->cleanName($source['name']), 0, strlen($cleanTitle));
			similar_text($cleanTitle , $cleanSource, $identity);

			// If the name contains the year of the movie, boost identity
			if (preg_match("/\b$year\b/", $source['name']))
			{
				$identity += 20;
			}

			// Apply all regexp based quality rules
			foreach ($rules as $pattern => $score)
			{
				if (preg_match($pattern, $source['name'], $m))
				{
					$quality += $score;
				}
			}

			// File should be at the very minimum +500MB
			if ($source['size'] > 500 * 1024 * 1024)
			{
				$quality += 10;
			}
			
			$source['identity'] = $identity;
			$source['quality'] = $quality;
			$source['score'] = $identity >= 100 && $quality > 80 ? 2 * $identity + $quality : 0;
		}

		// Sort by score, then seeds, then leech
		usort($sources, function($a, $b) { 
			if ($b['score'] != $a['score'])
				return $b['score'] - $a['score'];
			elseif ($b['seeds'] != $a['seeds'])
				return $b['seeds'] - $a['seeds'];
			elseif ($b['leech'] != $a['leech'])
				return $b['leech'] - $a['leech'];
			else
				return strcmp($b['link'], $a['link']);
		});
		
		return $sources;
	}

}
