<?php

namespace Rougin\Refinery\Fixture;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * CodeIgniter Helper
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CodeIgniterHelper
{
    /**
     * Sets default configurations.
     *
     * @param  string $appPath
     * @return void
     */
    public static function setDefaults($appPath)
    {
        $contents = file_get_contents("$appPath/config/default/migration.php");
        file_put_contents("$appPath/config/migration.php", $contents);

        self::emptyDirectory($appPath . '/migrations', true);
    }

    /**
     * Deletes files in the specified directory.
     *
     * @param  string  $directory
     * @param  boolean $delete
     * @return void
     */
    protected static function emptyDirectory($directory, $delete = false)
    {
        if (! is_dir($directory)) {
            return;
        }

        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            $isErrorDirectory = strpos($file->getRealPath(), 'errors');
            $isIndexHtml = strpos($file->getRealPath(), 'index.html');

            if ($isErrorDirectory !== false || $isIndexHtml !== false) {
                continue;
            }

            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        if ($delete) {
            rmdir($directory);
        }
    }
}
