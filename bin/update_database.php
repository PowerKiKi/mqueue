<?php

require_once __DIR__ . '/../public/index.php';

$settingName = 'databaseVersion'; // This is the setting name used in the database to store the version information
$sqlPath = __DIR__ . '/sql/'; // This is the path where all SQL patches resides

/**
 * Returns the last version available in SQL file
 *
 * @return int last version of patches
 */
function getPatchVersion()
{
    global $sqlPath;
    $lastVersion = 0;
    $d = dir($sqlPath);
    while (false !== ($entry = $d->read())) {
        if (preg_match('/^version\.(\d+)\.sql$/i', $entry, $a)) {
            if ((int) $a[1] > $lastVersion) {
                $lastVersion = (int) $a[1];
            }
        }
    }
    $d->close();

    return $lastVersion;
}

/**
 * Returns the whole SQL (enclosed in transaction) needed to update from
 * specified version to specified target version.
 *
 * @param int $currentVersion the version currently found in database
 * @param int $targetVersion the target version to reach wich patches
 *
 * @return string the SQL
 */
function buildSQL($currentVersion, $targetVersion)
{
    global $sqlPath;

    if ($currentVersion > $targetVersion) {
        throw new Exception('Cannot downgrade versions. Target version must be higher than current version');
    }

    $sql = "START TRANSACTION;\n";

    $missingVersions = [];
    for ($v = $currentVersion + 1; $v <= $targetVersion; ++$v) {
        $file = $sqlPath . 'version.' . $v . '.sql';
        if (is_file($file)) {
            $sql .= "\n-- -------- VERSION $v BEGINS ------------------------\n";
            $sql .= file_get_contents($file);
            $sql .= "\n-- -------- VERSION $v ENDS --------------------------\n";
        } else {
            $missingVersions[] = $v;
        }
    }

    $sql .= "\nCOMMIT;\n";

    if (count($missingVersions)) {
        throw new Exception('Missing SQL file for versions: ' . implode(',', $missingVersions));
    }

    return $sql;
}

/**
 * Executes a batch of SQL commands.
 * (This is a workaround to Zend limitation to have only one command at once)
 *
 * @param string $sql to be executed
 */
function executeBatchSql($sql): void
{
    $affectedRows = 0;
    $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
    foreach ($queries as $query) {
        if (mb_strlen(trim($query)) > 0) {
            try {
                $result = Zend_Registry::get('db')->query($query);
            } catch (\Exception $exception) {
                echo 'FAILED QUERY: ' . $query . PHP_EOL;

                throw $exception;
            }

            $affectedRows += $result->rowCount();
        }
    }

    echo "\n" . 'affected rows count: ' . $affectedRows . "\n";
}

/**
 * Do the actual update
 */
function doUpdate(): void
{
    global $settingName;

    try {
        $currentVersion = (int) \mQueue\Model\Setting::get($settingName, 0)->value;
    } catch (Exception $e) {
        if (mb_strpos($e->getMessage(), 'SQLSTATE[42S02]') >= 0) {
            $currentVersion = -1;
        } else {
            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    $targetVersion = getPatchVersion();

    echo 'current version is: ' . $currentVersion . "\n";
    echo 'target version is : ' . $targetVersion . "\n";

    if ($currentVersion == $targetVersion) {
        echo "already up-to-date\n";

        return;
    }

    $sql = buildSQL($currentVersion, $targetVersion);
    echo $sql;
    echo "\n_________________________________________________\n";
    echo "updating...\n";
    executeBatchSql($sql);
    \mQueue\Model\Setting::set($settingName, $targetVersion);

    echo "\nsuccessful update to version $targetVersion !\n";
}

doUpdate();
