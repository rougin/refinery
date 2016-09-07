<?php

namespace Rougin\Refinery\Common;

use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Migration Helper
 *
 * Provides common methods used in other commands
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MigrationHelper
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
        $pattern = '/\$config\[\'migration_version\'\] = (\d+);/';

        preg_match_all($pattern, $file, $match);

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

        if (! is_dir($path)) {
            return [$filenames, $migrations];
        }

        $skipDots = FilesystemIterator::SKIP_DOTS;
        $selfFirst = RecursiveIteratorIterator::SELF_FIRST;

        // Searches a listing of migration files and sorts them after
        $directory = new RecursiveDirectoryIterator($path, $skipDots);
        $iterator = new RecursiveIteratorIterator($directory, $selfFirst);

        foreach ($iterator as $path) {
            $filenames[] = str_replace('.php', '', $path->getFilename());
            $migration = substr($path->getFilename(), 0, 14);

            if (! is_numeric($migration)) {
                $migration = substr($path->getFilename(), 0, 3);
            }

            $migrations[] = $migration;
        }

        sort($filenames);
        sort($migrations);

        return [$filenames, $migrations];
    }

    /**
     * Enables/disables the Migration Class.
     *
     * @param  boolean $enabled
     * @return void
     */
    public static function toggleMigration($enabled = false)
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
