<?php

namespace Rougin\Refinery;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Refinery\Fixture\CommandBuilder;
use Rougin\Refinery\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class RefineryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $appPath;

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
        $this->appPath = __DIR__ . '/TestApp/application';
    }

    /**
     * Tests if the initial commands exists.
     *
     * @return void
     */
    public function testCommandsExist()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $application = $this->getApplication();

        $this->assertTrue($application->has('create'));
    }

    /**
     * Gets the application with the loaded classes.
     *
     * @return \Symfony\Component\Console\Application
     */
    protected function getApplication()
    {
        $application = new Application;

        foreach ($this->commands as $commandName) {
            $command = CommandBuilder::create($commandName);

            $application->add($command);
        }

        return $application;
    }
}
