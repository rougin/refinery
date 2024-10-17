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
    public function test_empty_migrations()
    {
        $test = $this->findCommand('migrate');

        $test->execute(array());

        $expected = '[PASS] Nothing to migrate.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_empty_migrations
     *
     * @return void
     */
    public function test_migrating_files()
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
     * @depends test_migrating_files
     *
     * @return void
     */
    public function test_migrating_next_file()
    {
        $test = $this->findCommand('migrate');

        $test->execute(array());

        $expected = 2;

        $actual = $this->describe->columns('users');

        $this->assertCount($expected, $actual);
    }

    /**
     * @depends test_migrating_next_file
     *
     * @return void
     */
    public function test_nothing_to_migrate()
    {
        $test = $this->findCommand('migrate');

        $test->execute(array());

        $expected = '[PASS] Nothing to migrate.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_reset_to_0
     *
     * @return void
     */
    public function test_nothing_to_rollback()
    {
        $test = $this->findCommand('rollback');

        $test->execute(array());

        $expected = '[PASS] Nothing to roll back.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_rolling_back
     *
     * @return void
     */
    public function test_reset_to_0()
    {
        $this->expectException('Exception');

        $test = $this->findCommand('reset');

        $test->execute(array());

        $this->describe->columns('users');
    }

    /**
     * @depends test_nothing_to_migrate
     *
     * @return void
     */
    public function test_rolling_back()
    {
        $test = $this->findCommand('rollback');

        $test->execute(array());

        $expected = 1;

        $actual = $this->describe->columns('users');

        $this->assertCount($expected, $actual);
    }

    /**
     * @return void
     */
    protected function clearFiles()
    {
        $path = $this->app->getAppPath();

        /** @var string[] */
        $files = glob($path . '/migrations/*.php');

        foreach ($files as $file)
        {
            unlink($file);
        }
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

    /**
     * @return void
     */
    protected function useSqliteConfig()
    {
        $this->useDatabaseConfig('sqlite');
    }
}
