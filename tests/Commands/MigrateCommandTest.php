<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\SparkPlug\Instance;
use Rougin\Refinery\Fixture\CommandBuilder;
use Rougin\Refinery\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class MigrateCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $createCommand;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $resetCommand;

    /**
     * @var string
     */
    protected $appPath;

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function setUp()
    {
        $this->appPath = __DIR__ . '/../TestApp/application';

        $command = 'Rougin\Refinery\Commands\MigrateCommand';
        $createCommand = 'Rougin\Refinery\Commands\CreateMigrationCommand';
        $resetCommand = 'Rougin\Refinery\Commands\ResetCommand';

        $this->command = CommandBuilder::create($command);
        $this->createCommand = CommandBuilder::create($createCommand);
        $this->resetCommand = CommandBuilder::create($resetCommand);
    }

    /**
     * Tests "migrate" command.
     * 
     * @return void
     */
    public function testMigrateCommand()
    {
        $ci = Instance::create($this->appPath);

        CodeIgniterHelper::setDefaults($this->appPath);

        $ci->load->dbforge();
        $ci->dbforge->drop_table('country', true);
        $ci->dbforge->drop_table('region', true);

        // Reset
        $resetCommand = new CommandTester($this->resetCommand);
        $resetCommand->execute([]);

        // Create table
        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => 'create_country_table' ]);

        sleep(1);

        // Migrate
        $command = new CommandTester($this->command);
        $command->execute([]);

        // Create table
        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => 'create_region_table' ]);

        // Migrate
        $command = new CommandTester($this->command);
        $command->execute([]);

        $pattern = '/has been migrated to the database$/';
        $this->assertRegExp($pattern, $command->getDisplay());

        // Migrate
        $command = new CommandTester($this->command);
        $command->execute([]);

        $pattern = '/^Database is up to date.$/';
        $this->assertRegExp($pattern, $command->getDisplay());

        // Reset again
        $resetCommand = new CommandTester($this->resetCommand);
        $resetCommand->execute([]);

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}