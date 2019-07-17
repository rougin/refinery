<?php

namespace Rougin\Refinery\Console;

/**
 * Reset Command Test
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class ResetCommandTest extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $command = 'reset';

    /**
     * Tests ResetCommand::execute.
     *
     * @return void
     */
    public function testExecuteMethod()
    {
        $this->create('create_users_table');

        $this->create('create_posts_table');

        $this->manager->migrate('002');

        $this->tester->execute(array(''));

        $this->delete('001_create_users_table');

        $this->delete('002_create_posts_table');

        $expected = '000';

        $result = $this->manager->current();

        $this->assertEquals($expected, $result);
    }
}
