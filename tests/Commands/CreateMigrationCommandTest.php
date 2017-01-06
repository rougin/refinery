<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class CreateMigrationCommandTest extends \Rougin\Refinery\TestCase
{
    /**
     * Tests "create" command in "create" keyword.
     *
     * @return void
     */
    public function testCreateTable()
    {
        $this->setDefaults();

        $name = 'create_role_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => $name ]);

        $this->assertRegExp('/has been created/', $createCommand->getDisplay());

        $this->setDefaults();
    }

    /**
     * Tests "create" command in "create" keyword with "--from-database" option.
     *
     * @return void
     */
    public function testCreateTableFromDatabase()
    {
        $this->setDefaults();

        $name = 'create_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => $name, '--from-database' => true ]);

        $this->assertRegExp('/has been created/', $createCommand->getDisplay());

        $this->setDefaults();
    }

    /**
     * Tests "create" command in "create" keyword with "--sequential" option.
     *
     * @return void
     */
    public function testCreateTableWithSequentialOption()
    {
        $this->setDefaults();

        $name = 'create_user_table';
        $file = APPPATH . 'migrations/002_' . $name . '.php';

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => $name, '--sequential' => true ]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => $name, '--sequential' => true ]);

        $this->assertFileExists($file);

        $this->setDefaults();
    }

    /**
     * Tests "create" command in "add" keyword.
     *
     * @return void
     */
    public function testAddColumnInTable()
    {
        $this->setDefaults();

        $name = 'add_created_at_in_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => $name ]);

        $this->assertRegExp('/has been created/', $createCommand->getDisplay());

        $this->setDefaults();
    }

    /**
     * Tests "create" command in "modify" keyword.
     *
     * @return void
     */
    public function testModifyColumnInTable()
    {
        $this->setDefaults();

        $name = 'modify_name_in_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => $name ]);

        $this->assertRegExp('/has been created/', $createCommand->getDisplay());

        $this->setDefaults();
    }

    /**
     * Tests other commands with "--from-database" option.
     *
     * @return void
     */
    public function testFromDatabaseError()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->setDefaults();

        $name = 'modify_name_in_user_table';
        $file = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([ 'name' => $name, '--from-database' => true ]);

        $this->setDefaults();
    }
}
