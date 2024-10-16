<?php

namespace Rougin\Refinery;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class PlateTest extends Testcase
{
    /**
     * @var \Rougin\Refinery\Console
     */
    protected $app;

    /**
     * @return void
     */
    public function doSetUp()
    {
        $this->app = new Console(__DIR__ . '/Fixture');
    }

    /**
     * @return void
     */
    public function test_creating_migration()
    {
        $test = $this->findCommand('create');

        $test->execute(array());

        $this->assertTrue(true);
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
}
