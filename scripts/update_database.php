<?php
require_once('../public/index.php');

$settingName = 'databaseVersion'; // This is the setting name used in the database to store the version information
$sqlPath = 'sql/'; // This is the path where all SQL patches resides

/**
 * Returns the last version available in SQL file
 * @return integer last version of patches
 */
function getPatchVersion()
{
	global $sqlPath;
	$lastVersion = 0;
	$d = dir($sqlPath);
	while (false !== ($entry = $d->read()))
	{
		if ( preg_match('/^version\.(\d+)\.sql$/i', $entry, $a))
		{
			if ((int)$a[1] > $lastVersion)
			$lastVersion = (int)$a[1];
		}
	}
	$d->close();

	return $lastVersion;
}

/**
 * Returns the whole SQL (enclosed in transaction) needed to update from
 * specified version to specified target version.
 * @param integer $currentVersion the version currently found in database
 * @param integer $targetVersion the target version to reach wich patches
 * @return string the SQL
 */
function buildSQL($currentVersion, $targetVersion)
{
	global $sqlPath;

	if ($currentVersion > $targetVersion)
	throw new Exception('Cannot downgrade versions. Target version must be higher than current version');

	$sql = "START TRANSACTION;\n";

	$missingVersions = array();
	for ($v = $currentVersion + 1; $v <= $targetVersion; $v++)
	{
		$file = $sqlPath . 'version.' . $v . '.sql';
		if (is_file($file))
		{
			$sql .= "\n-- -------- VERSION $v BEGINS ------------------------\n";
			$sql .= file_get_contents($file);
			$sql .= "\n-- -------- VERSION $v ENDS --------------------------\n";
		}
		else
		{
			$missingVersions[]= $v;
		}
	}

	$sql .= "\nCOMMIT;\n";

	if (count($missingVersions))
	throw new Exception('Missing SQL file for versions: ' . join(',', $missingVersions));

	return $sql;
}

/**
 * Executes a batch of SQL commands.
 * (This is a workaround to Zend limitation to have only one command at once)
 * @param string $sql to be executed
 * @param array $database database parameters
 * @return string the error code returned by mysql
 */
function executeBatchSql($sql, $database)
{

    mysql_connect($database['host'], $database['username'], $database['password']);
    mysql_select_db($database['dbname']);
    mysql_query("SET NAMES UTF8");

	$queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
	foreach ($queries as $query)
	{
		if (strlen(trim($query)) > 0)
		{
			if (mysql_query($query) === false)
			{
				$error = mysql_error();
				echo $error;
				
				return $error;	
			}
		}
	}
	
	return '';
}

/**
 * Do the actual update
 */
function doUpdate($database)
{
	global $settingName;

	try
	{
		$currentVersion = (integer)Default_Model_Setting::get($settingName, 0)->value;
	}
	catch (Exception $e)
	{
		if (strpos($e->getMessage(), 'SQLSTATE[42S02]') >= 0)
		{
	 		$currentVersion = -1;
		}
		else
		{
			die('Caught exception: ' . $e->getMessage() . "\n");
		}
	}

	$targetVersion = getPatchVersion();

	echo 'current version is: ' . $currentVersion . "\n";
	echo 'target version is : ' . $targetVersion . "\n";

	if ($currentVersion == $targetVersion)
	{
		echo "already up-to-date";
		return;
	}

	$sql = buildSQL($currentVersion, $targetVersion);
	echo $sql;
	echo "\n_________________________________________________\n";
	echo "updating...\n";
	if (executeBatchSql($sql, $database))
	{
		echo "\nFAILED ! see mysql error above, the update was rolled back";
	}
	else
	{
		Default_Model_Setting::set($settingName, $targetVersion);
		echo "\nsuccessful update to version $targetVersion !\n";
	}
}

$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');
$options = $bootstrap->getOption('resources');

//w($options['db']['params']);
doUpdate($options['db']['params']);

?>