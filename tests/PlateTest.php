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

        $input = array('name' => 'create_users_table');

        $test->execute($input);

        $expected = $this->getTemplate('SimpleTable');

        $actual = $this->getActualFile($input['name']);

        $this->assertEquals($expected, $actual);

        $this->clearFiles();
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
     * @param string $name
     *
     * @return string
     */
    protected function getActualFile($name)
    {
        $path = $this->app->getAppPath();

        /** @var string[] */
        $files = glob($path . '/migrations/*.php');

        $selected = null;

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

        if ($selected === null)
        {
            throw new \Exception('"' . $name . '" file not found');
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
}
