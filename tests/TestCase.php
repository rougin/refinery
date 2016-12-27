<?php

namespace Rougin\Refinery;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Rougin\Refinery\Commands\CreateCommand
     */
    protected $createCommand;

    /**
     * @var \Rougin\Refinery\Commands\MigrateCommand
     */
    protected $migrateCommand;

    /**
     * @var \Rougin\Refinery\Commands\ResetCommand
     */
    protected $resetCommand;

    /**
     * @var \Rougin\Refinery\Commands\RollbackCommand
     */
    protected $rollbackCommand;

    /**
     * @var array
     */
    protected $commands = [
        'Rougin\Refinery\Commands\CreateMigrationCommand',
        'Rougin\Refinery\Commands\MigrateCommand',
        'Rougin\Refinery\Commands\ResetCommand',
        'Rougin\Refinery\Commands\RollbackCommand',
    ];

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function setUp()
    {
        $this->path = __DIR__ . '/Application';

        $this->createCommand   = $this->buildCommand('Rougin\Refinery\Commands\CreateMigrationCommand');
        $this->migrateCommand  = $this->buildCommand('Rougin\Refinery\Commands\MigrateCommand');
        $this->resetCommand    = $this->buildCommand('Rougin\Refinery\Commands\ResetCommand');
        $this->rollbackCommand = $this->buildCommand('Rougin\Refinery\Commands\RollbackCommand');
    }

    /**
     * Injects a command with its dependencies.
     *
     * @param  string $command
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function buildCommand($command)
    {
        $injector = new \Auryn\Injector;

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Templates');
        $twig   = new \Twig_Environment($loader);

        $ci = \Rougin\SparkPlug\Instance::create($this->path);

        $ci->load->helper('inflector')->database();

        $database = (array) $ci->db;

        if (strpos($database['dsn'], 'sqlite') !== false) {
            $database['hostname'] = $database['dsn'];
        }

        $driver   = new \Rougin\Describe\Driver\CodeIgniterDriver($database);
        $describe = new \Rougin\Describe\Describe($driver);

        $adapter    = new \League\Flysystem\Adapter\Local($this->path);
        $filesystem = new \League\Flysystem\Filesystem($adapter);

        $injector->share($describe)->share($filesystem)->share($twig)->share($ci);

        return $injector->make($command);
    }

    /**
     * Deletes files in the specified directory.
     *
     * @param  string  $directory
     * @param  boolean $delete
     * @return void
     */
    protected function emptyDirectory($directoryName, $delete = false)
    {
        $directory = new \RecursiveDirectoryIterator($directoryName, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator  = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            $isErrorDirectory = strpos($file->getRealPath(), 'errors');
            $isIndexHtmlFile  = strpos($file->getRealPath(), 'index.html');

            if ($isErrorDirectory !== false || $isIndexHtmlFile !== false) {
                continue;
            }

            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }

        ! $delete || rmdir($directoryName);
    }

    /**
     * Gets the application with the loaded classes.
     *
     * @return \Symfony\Component\Console\Application
     */
    protected function getApplication()
    {
        $application = new \Symfony\Component\Console\Application;

        foreach ($this->commands as $commandName) {
            $command = $this->buildCommand($commandName);

            $application->add($command);
        }

        return $application;
    }

    /**
     * Sets default configurations.
     *
     * @return void
     */
    protected function setDefaults()
    {
        $contents = file_get_contents($this->path . '/application/config/default/migration.php');

        file_put_contents($this->path . '/application/config/migration.php', $contents);

        $this->emptyDirectory($this->path . '/application/migrations', false);
    }
}
