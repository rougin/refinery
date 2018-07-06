<?php

namespace Rougin\Refinery\Console;

/**
 * Migrate Command Test
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MigrateCommandTest extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $command = 'migrate';

    /**
     * Tests CreateCommand::execute.
     *
     * @return void
     */
    public function testExecuteMethod()
    {
        $this->create('create_users_table');

        $this->create('create_posts_table');

        $this->tester->execute(array(''));

        $expected = '002';

        $result = $this->manager->current();

        $this->assertEquals($expected, $result);

        $path = __DIR__ . '/../Weblog/migrations/';

        $this->manager->migrate(0);

        unlink($path . '001_create_users_table.php');

        unlink($path . '002_create_posts_table.php');
    }

    /**
     * Tests CreateCommand::execute without database migrations.
     *
     * @return void
     */
    public function testExecuteMethodWithoutMigrations()
    {
        $this->tester->execute(array(''));

        $expected = '/There is nothing to migrate/';

        $result = $this->tester->getDisplay();

        $this->assertRegExp($expected, $result);
    }
}
