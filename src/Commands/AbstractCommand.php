<?php

namespace Rougin\Refinery\Commands;

use Rougin\Describe\Describe;
use League\Flysystem\Filesystem;

/**
 * Abstract Command
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
abstract class AbstractCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \CI_Controller
     */
    protected $codeigniter;

    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Twig_Environment
     */
    protected $renderer;

    /**
     * @param \CI_Controller               $codeigniter
     * @param \Rougin\Describe\Describe    $describe
     * @param \League\Flysystem\Filesystem $filesystem
     * @param \Twig_Environment            $renderer
     */
    public function __construct(\CI_Controller $codeigniter, Describe $describe, Filesystem $filesystem, \Twig_Environment $renderer)
    {
        parent::__construct();

        $this->codeigniter = $codeigniter;
        $this->describe    = $describe;
        $this->filesystem  = $filesystem;
        $this->renderer    = $renderer;
    }

    /**
     * Changes the migration version.
     *
     * @param  integer $current
     * @param  integer $timestamp
     * @return void
     */
    public function changeVersion($current, $timestamp)
    {
        $old = '$config[\'migration_version\'] = ' . $current . ';';
        $new = '$config[\'migration_version\'] = ' . $timestamp . ';';

        $config = $this->filesystem->read('application/config/migration.php');
        $config = str_replace($old, $new, $config);

        $this->filesystem->update('application/config/migration.php', $config);
    }

    /**
     * Gets the latest migration version
     *
     * @param  boolean $rollback
     * @return string
     */
    public function getLatestVersion($rollback = false)
    {
        $config  = $this->filesystem->read('application/config/migration.php');
        $pattern = '/\$config\[\'migration_version\'\] = (\d+);/';

        preg_match_all($pattern, $config, $match);

        if ($rollback && intval($match[1][0]) <= 0) {
            throw new \UnexpectedValueException("There's nothing to be rollbacked at.");
        }

        return $match[1][0];
    }

    /**
     * Gets list of migrations from the specified directory.
     *
     * @param  string $path
     * @return array
     */
    public function getMigrations($path)
    {
        $filenames  = [];
        $migrations = [];

        $limits = [ 'sequential' => 3, 'timestamp' => 14 ];
        $type   = $this->getMigrationType();

        // Searches a listing of migration files and sorts them after
        $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $iterator  = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $path) {
            array_push($filenames, str_replace('.php', '', $path->getFilename()));
            array_push($migrations, substr($path->getFilename(), 0, $limits[$type]));
        }

        sort($filenames);
        sort($migrations);

        return [ $filenames, $migrations ];
    }

    /**
     * Returns the current migration type.
     *
     * @return string
     */
    public function getMigrationType()
    {
        $config = $this->filesystem->read('application/config/migration.php');

        preg_match('/\$config\[\'migration_type\'\] = \'(.*?)\';/i', $config, $match);

        return $match[1] ?: 'timestamp';
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        $migrations = glob(APPPATH . 'migrations/*.php');

        return count($migrations) > 0;
    }

    /**
     * Migrates the specified versions to database.
     *
     * @param  string $old
     * @param  string $new
     * @return void
     */
    public function migrate($old, $new)
    {
        // Enable migration and change the current version to a latest one
        $this->toggleMigration(true);
        $this->changeVersion($old, $new);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        $this->toggleMigration();
    }

    /**
     * Enables/disables the Migration Class.
     *
     * @param  boolean $enabled
     * @return void
     */
    public function toggleMigration($enabled = false)
    {
        $old = [ '$config[\'migration_enabled\'] = TRUE;', '$config[\'migration_enabled\'] = true;' ];
        $new = [ '$config[\'migration_enabled\'] = FALSE;', '$config[\'migration_enabled\'] = false;' ];

        if ($enabled) {
            $old = [ '$config[\'migration_enabled\'] = FALSE;', '$config[\'migration_enabled\'] = false;' ];
            $new = [ '$config[\'migration_enabled\'] = TRUE;', '$config[\'migration_enabled\'] = true;' ];
        }

        $config = $this->filesystem->read('application/config/migration.php');
        $config = str_replace($old, $new, $config);

        $this->filesystem->update('application/config/migration.php', $config);
    }
}
