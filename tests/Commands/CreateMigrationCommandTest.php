<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Refinery\Fixture\CommandBuilder;
use Rougin\Refinery\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class CreateMigrationCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

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
        $command = 'Rougin\Refinery\Commands\CreateMigrationCommand';

        $this->command = CommandBuilder::create($command);
    }

    /**
     * Tests "create" command in "create" keyword.
     *
     * @return void
     */
    public function testCreateTable()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $name = 'create_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $command = new CommandTester($this->command);
        $command->execute([ 'name' => $name ]);

        $this->assertRegExp('/has been created/', $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests "create" command in "create" keyword with "--from-database" option.
     *
     * @return void
     */
    public function testCreateTableFromDatabase()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $name = 'create_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $command = new CommandTester($this->command);
        $command->execute([ 'name' => $name, '--from-database' => true ]);

        $this->assertRegExp('/has been created/', $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests "create" command in "create" keyword with "--sequential" option.
     *
     * @return void
     */
    public function testCreateTableWithSequentialOption()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $name = 'create_user_table';
        $file = APPPATH . 'migrations/002_' . $name . '.php';

        $command = new CommandTester($this->command);
        $command->execute([ 'name' => $name, '--sequential' => true ]);

        $command = new CommandTester($this->command);
        $command->execute([ 'name' => $name, '--sequential' => true ]);

        $this->assertFileExists($file);

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests "create" command in "add" keyword.
     *
     * @return void
     */
    public function testAddColumnInTable()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $name = 'add_name_in_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $command = new CommandTester($this->command);
        $command->execute([ 'name' => $name ]);

        $this->assertRegExp('/has been created/', $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests "create" command in "modify" keyword.
     *
     * @return void
     */
    public function testModifyColumnInTable()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $name = 'modify_name_in_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $command = new CommandTester($this->command);
        $command->execute([ 'name' => $name ]);

        $this->assertRegExp('/has been created/', $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests other commands with "--from-database" option.
     *
     * @return void
     */
    public function testFromDatabaseError()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $name = 'modify_name_in_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $command = new CommandTester($this->command);
        $command->execute([ 'name' => $name, '--from-database' => true ]);

        $this->assertRegExp('/--from-database is only/', $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
