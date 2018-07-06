<?php

namespace Rougin\Refinery\Console;

use Rougin\Describe\Describe;
use Rougin\Describe\Driver\MySQLDriver;
use Rougin\Refinery\Builder;
use Rougin\Refinery\Manager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Abstract Test Case
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Rougin\Refinery\Builder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var Symfony\Component\Console\Command
     */
    protected $instance;

    /**
     * @var \Rougin\Refinery\Manager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    protected $tester;

    /**
     * Sets up the application instance.
     */
    public function setUp()
    {
        $this->source = str_replace('Console', 'Weblog', __DIR__);

        $this->manager = new Manager($this->source);

        $this->builder = new Builder;

        $pdo = new \PDO('mysql:host=localhost;dbname=demo', 'root', '');

        $describe = new Describe(new MySQLDriver($pdo, 'demo'));

        $this->application = new Application($this->builder, $describe, $this->manager);

        $instance = $this->application->find($this->command);

        $this->tester = new CommandTester($instance);
    }

    /**
     * Creates a new database migration file.
     *
     * @param  string $name
     * @return void
     */
    protected function create($name)
    {
        $content = $this->builder->make((string) $name);

        $filename = $this->manager->filename($name);

        $this->manager->create($filename, $content);
    }

    /**
     * Deletes a specified database migration.
     *
     * @param  string $name
     * @return void
     */
    protected function delete($name)
    {
        $path = $this->source . '/migrations';

        unlink($path . '/' . $name . '.php');
    }
}
