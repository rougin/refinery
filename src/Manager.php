<?php

namespace Rougin\Refinery;

use Rougin\SparkPlug\Controller;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Manager
{
    /**
     * @var \Rougin\SparkPlug\Controller
     */
    protected $ci;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param \Rougin\SparkPlug\Controller $ci
     * @param string                       $path
     */
    public function __construct(Controller $ci, $path)
    {
        $this->ci = $ci;

        $this->ci->load->library('migration');

        $this->path = $path;
    }

    /**
     * @return array<string, string>[]
     */
    public function getMigrations()
    {
        /** @var array<string, string> */
        $items = $this->ci->migration->find_migrations();

        $result = array();

        foreach ($items as $version => $file)
        {
            $row = array('file' => $file);

            $row['version'] = $version;

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param string $version
     *
     * @return array<string, string>
     */
    public function getLastMigration($version)
    {
        $files = $this->getMigrations();

        if (count($files) === 0)
        {
            return array();
        }

        $parsed = array();

        foreach (array_reverse($files) as $item)
        {
            $current = $item['version'];

            if (strtotime($current) < strtotime($version))
            {
                $parsed[] = $item;

                break;
            }
        }

        /**
         * By default, it should always return the first file
         * with its version number always defined to "0".
         */
        $last = $files[0];

        /** @var array<string, string>|false */
        $next = end($parsed);

        $prev = 0;

        /**
         * If there is a previous file before the specified version,
         * use that as the new version but the last migration file is
         * still the same.
         */
        if ($next !== false)
        {
            /** @var array<string, string> */
            $last = end($files);

            $prev = $next['version'];
        }

        $last['version'] = $prev;

        return $last;
    }

    /**
     * @param string $version
     *
     * @return boolean
     */
    public function migrate($version)
    {
        $this->startMigration();

        $this->ci->load->library('migration');

        $self = $this->ci->migration;

        $result = $self->version($version);

        $this->stopMigration();

        return is_string($result);
    }

    /**
     * @return string
     */
    public function getLatestVersion()
    {
        $file = $this->path . '/config/migration.php';

        /** @var string */
        $config = file_get_contents($file);

        $pattern = '/\$config\[\\\'migration_version\\\'\] = (.*?);/i';

        preg_match($pattern, $config, $matches);

        return $matches ? $matches[1] : '';
    }

    /**
     * @param string $version
     *
     * @return void
     */
    public function saveLatest($version)
    {
        $file = $this->path . '/config/migration.php';

        $replace = '$config[\'migration_version\'] = ' . $version . ';';

        /** @var string */
        $config = file_get_contents($file);

        $pattern = '/\$config\[\\\'migration_version\\\'\] = (.*?);/i';

        $result = preg_replace($pattern, $replace, $config);

        file_put_contents($file, $result);
    }

    /**
     * @return void
     */
    protected function startMigration()
    {
        $this->ci->config->set_item('migration_enabled', 'true');
    }

    /**
     * @return void
     */
    protected function stopMigration()
    {
        $this->ci->config->set_item('migration_enabled', 'false');
    }
}
