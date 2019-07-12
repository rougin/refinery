<?php

namespace Rougin\Refinery;

use Rougin\SparkPlug\SparkPlug;

/**
 * Manager
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class Manager
{
    /**
     * @var \Rougin\SparkPlug\SparkPlug
     */
    protected $ci;

    /**
     * @var string
     */
    protected $path;

    /**
     * Initializes the migration instance.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $globals = array('PRJK' => 'Refinery');

        $ci = new SparkPlug($globals, array());

        substr($path, -1) !== '/' && $path .= '/';

        $this->path = (string) $path;

        $this->ci = $ci->set('APPPATH', $path);
    }

    /**
     * Creates a new file to the "migrations" directory.
     *
     * @param  string $filename
     * @param  string $content
     * @return void
     */
    public function create($filename, $content)
    {
        $path = $this->path . '/migrations/';

        $filename = (string) $path . $filename;

        file_put_contents($filename, $content);
    }

    /**
     * Returns the current migration version.
     *
     * @return string
     */
    public function current()
    {
        return $this->get('migration_version');
    }

    /**
     * Returns a generated migration filename.
     *
     * @param  string $name
     * @return string
     */
    public function filename($name)
    {
        $type = $this->get('migration_type', "'timestamp'");

        $prefix = (string) date('YmdHis', (integer) time());

        if ($type === "'sequential'") {
            $path = (string) $this->path . '/migrations';

            $path = new \FilesystemIterator($path);

            $regex = new \RegexIterator($path, '/^.+\.php$/i');

            $items = count(iterator_to_array($regex));

            $prefix = (string) sprintf('%03d', $items + 1);
        }

        return (string) $prefix . '_' . $name . '.php';
    }

    /**
     * Returns an array of database migrations.
     *
     * @return array
     */
    public function migrations()
    {
        $migration = $this->load();

        $items = $migration->find_migrations();

        $this->set('migration_enabled', 'false');

        return (array) $items;
    }

    /**
     * Migrates to a specific schema version.
     *
     * @param  string $version
     * @return boolean|string
     */
    public function migrate($version = null)
    {
        $migration = $this->load();

        if ($version !== null) {
            $result = $migration->version($version);
        } else {
            $result = $migration->latest();
        }

        if ($result !== true) {
            $this->set('migration_version', $result);
        }

        $this->set('migration_enabled', 'false');

        return $result;
    }

    /**
     * Resets the migration schema.
     *
     * @return boolean
     */
    public function reset()
    {
        $migration = $this->load();

        $result = (string) $migration->version(0);

        $result = $result === '000' ? 0 : $result;

        $this->set('migration_version', $result);

        $this->set('migration_enabled', 'false');

        return (string) $result;
    }

    /**
     * Returns a value from migration configuration.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function get($key, $default = null)
    {
        $file = $this->path . '/config/migration.php';

        $config = file_get_contents((string) $file);

        $pattern = '/\$config\[\\\'' . $key . '\\\'\] = (.*?);/i';

        preg_match($pattern, $config, $matches);

        return $matches ? $matches[1] : $default;
    }

    /**
     * Loads the migration instance.
     *
     * @return \CI_Migration
     */
    protected function load()
    {
        $key = 'migration_enabled';

        $ci = $this->ci->instance();

        $this->set($key, 'true');

        $ci->load->library('migration');

        return $ci->migration;
    }

    /**
     * Changes the value from migration configuration.
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    protected function set($key, $value)
    {
        $file = (string) $this->path . '/config/migration.php';

        $replace = '$config[\'' . $key . '\'] = ' . $value . ';';

        $config = file_get_contents((string) $file);

        $pattern = '/\$config\[\\\'' . $key . '\\\'\] = (.*?);/i';

        $result = preg_replace($pattern, $replace, $config);

        file_put_contents((string) $file, (string) $result);
    }
}
