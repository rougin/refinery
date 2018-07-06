<?php

namespace Rougin\Refinery\Console;

/**
 * Revert Command Test
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class RevertCommandTest extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $command = 'rollback';

    /**
     * Tests RevertCommand::execute.
     *
     * @return void
     */
    public function testExecuteMethod()
    {
        $this->create('create_users_table');

        $this->create('create_posts_table');

        $this->manager->migrate('002');

        $this->tester->execute(array(''));

        $expected = '001';

        $result = $this->manager->current();

        $this->assertEquals($expected, $result);

        $this->manager->migrate(0);

        $this->delete('001_create_users_table');

        $this->delete('002_create_posts_table');
    }

    /**
     * Tests RevertCommand::execute with a specified version.
     *
     * @return void
     */
    public function testExecuteMethodWithVersion()
    {
        $this->create('create_users_table');

        $this->create('create_posts_table');

        $this->manager->migrate('002');

        $this->tester->execute(array('version' => '001'));

        $expected = '001';

        $result = $this->manager->current();

        $this->assertEquals($expected, $result);

        $this->manager->migrate(0);

        $this->delete('001_create_users_table');

        $this->delete('002_create_posts_table');
    }

    /**
     * Tests RevertCommand::execute without database migrations.
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
