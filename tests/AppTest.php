<?php

namespace Rougin\Refinery;

/**
 * @runTestsInSeparateProcesses
 *
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class AppTest extends Testcase
{
    /**
     * @return void
     */
    public function test_refinery_yml_file()
    {
        $app = new Console(__DIR__ . '/Fixture');

        $expected = 'Rougin\Blueprint\Wrapper';

        $actual = $app->make()->find('create');

        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_without_refinery_yml()
    {
        $app = new Console(__DIR__ . '/../');

        $expected = 'Rougin\Blueprint\Wrapper';

        $actual = $app->make()->find('initialize');

        $this->assertInstanceOf($expected, $actual);
    }
}
