<?php

namespace Rougin\Refinery;

use CI_Controller;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Rougin\SparkPlug\Instance;

/**
 * Tools
 *
 * Provides common methods used in other commands
 * 
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Tools
{
    /**
     * Changes the migration version.
     * 
     * @param  int $current
     * @param  int $timestamp
     * @return void
     */
    public static function changeVersion($current, $timestamp)
    {
        $path = APPPATH . '/config/migration.php';
        $migrationFile = file_get_contents($path);

        $currentVersion = '$config[\'migration_version\'] = ' . $current . ';';
        $newVersion = '$config[\'migration_version\'] = ' . $timestamp . ';';

        $migrationFile = str_replace(
            $currentVersion,
            $newVersion,
            $migrationFile
        );

        $file = fopen($path, 'wb');
        file_put_contents($path, $migrationFile);
        fclose($file);
    }

    /**
     * Gets the latest migration version
     * 
     * @return string
     */
    public static function getLatestVersion($file)
    {
        preg_match_all(
            '/\$config\[\'migration_version\'\] = (\d+);/',
            $file,
            $match
        );

        return $match[1][0];
    }

    /**
     * Gets list of migrations from the specified directory.
     * 
     * @param  string $path
     * @return array
     */
    public static function getMigrations($path)
    {
        $filenames = [];
        $migrations = [];

        // Searches a listing of migration files and sorts them after
        $directory = new RecursiveDirectoryIterator(
            $path,
            FilesystemIterator::SKIP_DOTS
        );

        $iterator = new RecursiveIteratorIterator(
            $directory,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $path) {
            $filenames[] = str_replace('.php', '', $path->getFilename());
            $migration = substr($path->getFilename(), 0, 14);

            if ( ! is_numeric($migration)) {
                $migration = substr($path->getFilename(), 0, 3);
            }

            $migrations[] = $migration;
        }

        sort($filenames);
        sort($migrations);

        return [$filenames, $migrations];
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        $migrations = glob(APPPATH . 'migrations/*.php');

        if (count($migrations) > 0) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Enables/disables the Migration Class.
     * 
     * @param  boolean $enabled
     * @return void
     */
    public static function toggleMigration($enabled = FALSE)
    {
        $path = APPPATH . '/config/migration.php';
        $migrationFile = file_get_contents($path);

        $search = '$config[\'migration_enabled\'] = TRUE;';
        $replace = '$config[\'migration_enabled\'] = FALSE;';

        if ($enabled) {
            $search = '$config[\'migration_enabled\'] = FALSE;';
            $replace = '$config[\'migration_enabled\'] = TRUE;';
        }

        $migrationFile = str_replace($search, $replace, $migrationFile);

        $file = fopen($path, 'wb');
        file_put_contents($path, $migrationFile);
        fclose($file);
    }
}
