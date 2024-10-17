<?php

namespace Rougin\Refinery\Commands;

use Rougin\Blueprint\Commands\InitializeCommand;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Initialize extends InitializeCommand
{
    /**
     * @var string
     */
    protected $file = 'refinery.yml';

    /**
     * Returns the source directory for the specified file.
     *
     * @return string
     */
    protected function getPlatePath()
    {
        /** @var string */
        return realpath(__DIR__ . '/../Template');
    }

    /**
     * Returns the root directory from the package.
     *
     * @return string
     */
    protected function getRootPath()
    {
        $root = (string) __DIR__ . '/../../../../../';

        $exists = file_exists($root . '/vendor/autoload.php');

        return $exists ? $root : __DIR__ . '/../../';
    }
}
