<?php

namespace Rougin\Refinery;

use Rougin\Describe\Column;
use Rougin\Describe\Describe;
use Rougin\Describe\Driver\MySQLDriver;

/**
 * Builder Test
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Rougin\Refinery\Builder
     */
    protected $builder;

    /**
     * Initializes the builder instance.
     */
    public function setUp()
    {
        $this->builder = new Builder;
    }

    /**
     * Tests Builder::make.
     *
     * @return void
     */
    public function testMakeMethod()
    {
        $path = (string) __DIR__ . '/Weblog/templates';

        $template = $path . '/CreateUsersTable.txt';

        $expected = file_get_contents($template);

        $result = $this->builder->make('create_users_table');

        $this->assertEquals($expected, $result);
    }

    /**
     * Tests Builder::make with a column.
     *
     * @return void
     */
    public function testMakeMethodWithColumn()
    {
        $path = (string) __DIR__ . '/Weblog/templates';

        $template = $path . '/AddNameInUsersTable.txt';

        $expected = file_get_contents($template);

        $column = new Column;

        $column->setField('name');
        $column->setLength(100);
        $column->setDataType('VARCHAR');

        $this->builder->column($column);

        $result = $this->builder->make('add_name_in_users_table');

        $this->assertEquals($expected, $result);
    }

    /**
     * Tests Builder::make with database schema.
     *
     * @return void
     */
    public function testMakeMethodWithDatabase()
    {
        $path = (string) __DIR__ . '/Weblog/templates';

        $template = $path . '/CreateUserTable.txt';

        $expected = file_get_contents($template);

        $dsn = 'mysql:host=localhost;dbname=demo';

        $pdo = new \PDO($dsn, 'root', '');

        $driver = new MySQLDriver($pdo, 'demo');

        $describe = new Describe($driver);

        $this->builder->describe($describe);

        $result = $this->builder->make('create_user_table');

        $this->assertSame($expected, (string) $result);
    }
}
