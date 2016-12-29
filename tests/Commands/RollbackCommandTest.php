<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\SparkPlug\Instance;

class RollbackCommandTest extends \Rougin\Refinery\TestCase
{
    /**
     * Tests "migrate" command.
     *
     * @return void
     */
    public function testRollbackCommand()
    {
        $ci = Instance::create($this->path);

        $this->setDefaults();

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
        $rollbackCommand = new CommandTester($this->rollbackCommand);
        $rollbackCommand->execute([]);

        $pattern = '/Database is reverted back to version/';
        $this->assertRegExp($pattern, $rollbackCommand->getDisplay());

        $this->setDefaults();
    }

    /**
     * Tests "migrate" command.
     *
     * @return void
     */
    public function testRollbackCommandWithVersion()
    {
        $this->setExpectedException('InvalidArgumentException');

        $ci = Instance::create($this->path);

        $this->setDefaults();

        $ci->load->dbforge();
        $ci->dbforge->drop_table('country', true);
        $ci->dbforge->drop_table('region', true);

        // Create table
        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => 'create_country_table' ]);

        // Migrate
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        $rollbackCommand = new CommandTester($this->rollbackCommand);
        $rollbackCommand->execute(['version' => '999']);

        // Migrate
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        $rollbackCommand = new CommandTester($this->rollbackCommand);
        $rollbackCommand->execute(['version' => '001']);

        $this->setDefaults();
    }

    /**
     * Tests "migrate" command.
     *
     * @return void
     */
    public function testRollbackCommandWithoutMigrations()
    {
        $ci = Instance::create($this->path);

        $this->setDefaults();

        $ci->load->dbforge();
        $ci->dbforge->drop_table('country', true);
        $ci->dbforge->drop_table('region', true);

        // Rollback
        $rollbackCommand = new CommandTester($this->rollbackCommand);
        $rollbackCommand->execute([]);

        $pattern = '/There\'s nothing to be rollbacked at/';
        $this->assertRegExp($pattern, $rollbackCommand->getDisplay());

        $this->setDefaults();
    }
}
