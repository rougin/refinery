<?php

namespace Rougin\Refinery;

use Rougin\Describe\Column;
use Rougin\SparkPlug\SparkPlug;

/**
 * Manager Test
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Initializes the manager instance.
     *
     * @return void
     */
    public function setUp()
    {
        $separator = (string) DIRECTORY_SEPARATOR;

        $this->path = __DIR__ . $separator . 'Weblog/';

        $this->manager = new Manager($this->path);
    }

    /**
     * Tests Manager::filename.
     *
     * @return void
     */
    public function testFilenameMethod()
    {
        $expected = '001_create_users_table.php';

        $name = (string) 'create_users_table';

        $result = $this->manager->filename($name);

        $this->assertEquals($expected, $result);
    }

    /**
     * Tests Manager::create.
     *
     * @return void
     */
    public function testCreateMethod()
    {
        $name = (string) 'create_users_table';

        $builder = new Builder;

        $content = $builder->make((string) $name);

        $filename = $this->manager->filename($name);

        $this->manager->create($filename, $content);

        $path = (string) __DIR__ . '/Weblog/templates';

        $template = $path . '/CreateUsersTable.txt';

        $expected = file_get_contents($template);

        $path = (string) $this->path . 'migrations';

        $filename = $path . '/001_create_users_table.php';

        $result = file_get_contents($filename);

        $this->assertEquals($expected, $result);

        return (string) $filename;
    }

    /**
     * Tests Manager::migrations.
     *
     * @depends testCreateMethod
     *
     * @param  string $filename
     * @return void
     */
    public function testMigrationsMethod($filename)
    {
        $expected = array((string) $filename);

        $result = $this->manager->migrations();

        $result = array_values($result);

        $this->assertEquals($expected, $result);
    }

    /**
     * Tests Manager::migrate.
     *
     * @depends testCreateMethod
     *
     * @param  string $filename
     * @return void
     */
    public function testMigrateMethod($filename)
    {
        $expected = substr(basename($filename), 0, 3);

        $result = $this->manager->migrate();

        $this->assertEquals($expected, $result);

        return $filename;
    }

    /**
     * Tests Manager::reset.
     *
     * @depends testMigrateMethod
     *
     * @param  string $filename
     * @return void
     */
    public function testResetMethod($filename)
    {
        $result = $this->manager->reset();

        $expected = (string) '000';

        $this->assertEquals($expected, $result);

        return $filename;
    }

    /**
     * Tests Manager::migrate with a specified version.
     *
     * @depends testResetMethod
     *
     * @param  string $filename
     * @return void
     */
    public function testMigrateMethodWithVersion($filename)
    {
        $name = 'add_name_in_users_table';

        $builder = new Builder;

        $column = new Column;

        $column->setLength(100);
        $column->setDataType('VARCHAR');

        $content = $builder->column($column)->make($name);

        $file = $this->manager->filename($name);

        $this->manager->create($file, $content);

        $expected = (string) '002';

        $result = $this->manager->migrate($expected);

        $this->assertEquals($expected, $result);

        $this->manager->reset();

        $path = $this->path . 'migrations';

        unlink($filename);

        unlink($path . '/002_add_name_in_users_table.php');
    }

    /**
     * Tests Manager::current.
     *
     * @return void
     */
    public function testCurrentMethod()
    {
        $result = $this->manager->current();

        $expected = '0';

        $this->assertEquals($expected, $result);
    }
}
