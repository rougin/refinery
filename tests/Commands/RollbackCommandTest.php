<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\SparkPlug\Instance;
use Rougin\Refinery\Fixture\CommandBuilder;
use Rougin\Refinery\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class RollbackCommandTest extends PHPUnit_Framework_TestCase
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
    protected $migrateCommand;

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

        $command = 'Rougin\Refinery\Commands\RollbackCommand';
        $migrateCommand = 'Rougin\Refinery\Commands\MigrateCommand';
        $createCommand = 'Rougin\Refinery\Commands\CreateMigrationCommand';
        $resetCommand = 'Rougin\Refinery\Commands\ResetCommand';

        $this->command = CommandBuilder::create($command);
        $this->migrateCommand = CommandBuilder::create($migrateCommand);
        $this->createCommand = CommandBuilder::create($createCommand);
        $this->resetCommand = CommandBuilder::create($resetCommand);
    }

    /**
     * Tests "migrate" command.
     * 
     * @return void
     */
    public function testRollbackCommand()
    {
        $ci = Instance::create($this->appPath);

        CodeIgniterHelper::setDefaults($this->appPath);

        $ci->load->dbforge();
        $ci->dbforge->drop_table('country', true);
        $ci->dbforge->drop_table('region', true);

        // Create table
        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => 'create_country_table' ]);

        // Migrate
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        // Rollback
        $command = new CommandTester($this->command);
        $command->execute([]);

        $pattern = '/Database is reverted back to version/';
        $this->assertRegExp($pattern, $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests "migrate" command.
     * 
     * @return void
     */
    public function testRollbackCommandWithVersion()
    {
        $ci = Instance::create($this->appPath);

        CodeIgniterHelper::setDefaults($this->appPath);

        $ci->load->dbforge();
        $ci->dbforge->drop_table('country', true);
        $ci->dbforge->drop_table('region', true);

        // Create table
        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => 'create_country_table' ]);

        // Migrate
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        $command = new CommandTester($this->command);
        $command->execute(['version' => '999']);

        $pattern = '/Cannot rollback to version/';
        $this->assertRegExp($pattern, $command->getDisplay());

        // Migrate
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        $command = new CommandTester($this->command);
        $command->execute(['version' => '001']);

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests "migrate" command.
     * 
     * @return void
     */
    public function testRollbackCommandWithoutMigrations()
    {
        $ci = Instance::create($this->appPath);

        CodeIgniterHelper::setDefaults($this->appPath);

        $ci->load->dbforge();
        $ci->dbforge->drop_table('country', true);
        $ci->dbforge->drop_table('region', true);

        // Rollback
        $command = new CommandTester($this->command);
        $command->execute([]);

        $pattern = '/There\'s nothing to be rollbacked at/';
        $this->assertRegExp($pattern, $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
