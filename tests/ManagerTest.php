<?php

namespace Rougin\Refinery;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class ManagerTest extends Testcase
{
    /**
     * @var \Rougin\Refinery\Console
     */
    protected $app;

    /**
     * @var \Rougin\Describe\Driver\DriverInterface
     */
    protected $describe;

    /**
     * @return void
     */
    public function doSetUp()
    {
        $app = new Console(__DIR__ . '/Fixture');

        /** @var \Rougin\Slytherin\Container\ContainerInterface */
        $container = $app->getContainer();

        $class = 'Rougin\Describe\Driver\DriverInterface';

        /** @var \Rougin\Describe\Driver\DriverInterface */
        $describe = $container->get($class);

        $this->app = $app;

        $this->describe = $describe;
    }

    /**
     * @return void
     */
    public function test_00_empty_migrations()
    {
        $test = $this->findCommand('migrate');

        $test->execute(array());

        $expected = '[PASS] Nothing to migrate.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_00_empty_migrations
     *
     * @return void
     */
    public function test_01_migrating_files()
    {
        $this->useMysqlConfig();

        $this->clearFiles();

        // Create the required migration files -------------
        $test = $this->findCommand('create');

        $input = array('name' => 'create_users_table');
        $test->execute($input);

        $date = date('YmdHis');

        sleep(1);

        $input = array('name' => 'add_name_in_users_table');
        $input['--length'] = 100;
        $input['--null'] = true;
        $test->execute($input);
        // -------------------------------------------------

        // Perform the migration of the first file ---
        $test = $this->findCommand('migrate');

        $test->execute(array('--target' => $date));
        // -------------------------------------------

        $expected = 1;

        $actual = $this->describe->columns('users');

        $this->assertCount($expected, $actual);
    }

    /**
     * @depends test_01_migrating_files
     *
     * @return void
     */
    public function test_02_migrating_next_file()
    {
        $test = $this->findCommand('migrate');

        $test->execute(array());

        $expected = 2;

        $actual = $this->describe->columns('users');

        $this->assertCount($expected, $actual);
    }

    /**
     * @depends test_02_migrating_next_file
     *
     * @return void
     */
    public function test_03_nothing_to_migrate()
    {
        $test = $this->findCommand('migrate');

        $test->execute(array());

        $expected = '[PASS] Nothing to migrate.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_03_nothing_to_migrate
     *
     * @return void
     */
    public function test_04_rolling_back()
    {
        $test = $this->findCommand('rollback');

        $test->execute(array());

        $expected = 1;

        $actual = $this->describe->columns('users');

        $this->assertCount($expected, $actual);
    }

    /**
     * @depends test_04_rolling_back
     *
     * @return void
     */
    public function test_05_reset_to_0()
    {
        $this->setExpectedException('Exception');

        $test = $this->findCommand('reset');

        $test->execute(array());

        $this->describe->columns('users');
    }

    /**
     * @depends test_05_reset_to_0
     *
     * @return void
     */
    public function test_06_nothing_to_rollback()
    {
        $test = $this->findCommand('rollback');

        $test->execute(array());

        $expected = '[PASS] Nothing to roll back.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_06_nothing_to_rollback
     *
     * @return void
     */
    public function test_07_create_with_database()
    {
        // Migrate again the said migration files ---
        $test = $this->findCommand('migrate');
        $test->execute(array());
        // ------------------------------------------

        // Then clear the said files ---
        $this->clearFiles();
        $this->useMysqlConfig();
        // -----------------------------

        // Create a new migration based on database ---
        $test = $this->findCommand('create');

        $input = array('name' => 'create_users_table');
        $input['--from-database'] = true;
        $test->execute($input);
        // --------------------------------------------

        // Clean database by rolling back to version 0 ---
        $test = $this->findCommand('reset');
        $test->execute(array());
        // -----------------------------------------------

        $expected = $this->getTemplate('WithDatabase');

        $actual = $this->getActualFile($input['name']);

        $this->clearFiles();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_07_create_with_database
     *
     * @return void
     */
    public function test_08_create_with_database_on_invalid_prefix()
    {
        $test = $this->findCommand('create');

        $input = array('name' => 'add_name_in_users_table');
        $input['--from-database'] = true;
        $test->execute($input);

        $expected = '[FAIL] The option "--from-database" is only applicable to "create" prefix.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    protected function clearFiles()
    {
        $path = $this->app->getAppPath();

        /** @var string[] */
        $files = glob($path . '/migrations/*.php');

        array_map('unlink', $files);
    }

    /**
     * @param string $name
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function findCommand($name)
    {
        return new CommandTester($this->app->make()->find($name));
    }

    /**
     * @param \Symfony\Component\Console\Tester\CommandTester $tester
     *
     * @return string
     */
    protected function getActualDisplay(CommandTester $tester)
    {
        $actual = $tester->getDisplay();

        $actual = str_replace("\r\n", '', $actual);

        return str_replace("\n", '', $actual);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getActualFile($name)
    {
        $path = $this->app->getAppPath();

        /** @var string[] */
        $files = glob($path . '/migrations/*.php');

        $selected = '';

        foreach ($files as $file)
        {
            $base = basename($file);

            $parsed = substr($base, 15, strlen($base));

            if ($parsed === $name . '.php')
            {
                $selected = $file;

                break;
            }
        }

        /** @var string */
        $result = file_get_contents($selected);

        return str_replace("\r\n", "\n", $result);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getTemplate($name)
    {
        $path = __DIR__ . '/Fixture/Plates/' . $name . '.php';

        /** @var string */
        $file = file_get_contents($path);

        return str_replace("\r\n", "\n", $file);
    }

    /**
     * @param string $type
     *
     * @return void
     */
    protected function useDatabaseConfig($type)
    {
        $path = $this->app->getAppPath();

        // Replace the "database.php" file --------
        $file = $path . '/config/database.php';

        $new = $path . '/config/database.' . $type;
        /** @var string */
        $new = file_get_contents($new);

        file_put_contents($file, $new);
        // ----------------------------------------

        // Reset the "migration.php" config file ---
        $file = $path . '/config/migration.php';

        $new = $path . '/config/migration.bak';
        /** @var string */
        $new = file_get_contents($new);

        file_put_contents($file, $new);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    protected function useMysqlConfig()
    {
        $this->useDatabaseConfig('mysql');
    }
}
