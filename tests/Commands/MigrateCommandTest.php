<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class MigrateCommandTest extends \Rougin\Refinery\TestCase
{
    /**
     * Tests "migrate" command.
     *
     * @return void
     */
    public function testMigrateCommand()
    {
        $ci = \Rougin\SparkPlug\Instance::create($this->path);

        $this->setDefaults();

        $ci->load->database();
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
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        // Create table
        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => 'create_region_table' ]);

        // Migrate
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        $pattern = '/has been migrated to the database/';
        $this->assertRegExp($pattern, $migrateCommand->getDisplay());

        // Migrate
        $migrateCommand = new CommandTester($this->migrateCommand);
        $migrateCommand->execute([]);

        $pattern = '/Database is up to date./';
        $this->assertRegExp($pattern, $migrateCommand->getDisplay());

        // Reset again
        $resetCommand = new CommandTester($this->resetCommand);
        $resetCommand->execute([]);

        $this->setDefaults();
    }
}
