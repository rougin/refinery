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
     * @param boolean $reverse
     *
     * @return array<string, string>[]
     */
    public function getMigrations($reverse = false)
    {
        /** @var array<string, string> */
        $items = $this->ci->migration->find_migrations();

        if ($reverse)
        {
            $keys = array_keys($items);
            $keys = array_reverse($keys);

            array_shift($keys);
            $keys[] = '0';

            $items = array_values($items);
            $items = array_reverse($items);

            /** @var array<string, string> */
            $items = array_combine($keys, $items);
        }

        $result = array();

        foreach ($items as $current => $file)
        {
            $result[] = array('file' => $file, 'version' => $current);
        }

        return $result;
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
    public function getCurrentVersion()
    {
        $file = $this->path . '/config/migration.php';

        /** @var string */
        $config = file_get_contents($file);

        $pattern = '/\$config\[\\\'migration_version\\\'\] = (.*?);/i';

        preg_match($pattern, $config, $matches);

        return $matches ? $matches[1] : '';
    }

    /**
     * @return string
     */
    public function getLastVersion()
    {
        $items = $this->getMigrations();

        $current = $this->getCurrentVersion();

        $parsed = null;

        foreach ($items as $index => $item)
        {
            if (strtotime($item['version']) !== strtotime($current))
            {
                continue;
            }

            if (array_key_exists($index - 1, $items))
            {
                $parsed = $items[$index - 1];

                break;
            }
        }

        return $parsed ? $parsed['version'] : '0';
    }

    /**
     * @return string
     */
    public function getLatestVersion()
    {
        /** @var array<string, string> */
        $items = $this->ci->migration->find_migrations();

        if (count($items) === 0)
        {
            return '0';
        }

        $keys = array_keys($items);

        return $keys[count($keys) - 1];
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
