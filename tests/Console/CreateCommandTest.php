<?php

namespace Rougin\Refinery\Console;

/**
 * Create Command Test
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class CreateCommandTest extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $command = 'create';

    /**
     * Tests CreateCommand::execute.
     *
     * @return void
     */
    public function testExecuteMethod()
    {
        $name = (string) 'create_users_table';

        $this->tester->execute(array('name' => $name));

        $expected = '/001_create_users_table/';

        $result = $this->tester->getDisplay();

        $this->assertRegExp($expected, $result);

        $filename = __DIR__ . '/../Weblog/migrations/';

        unlink($filename . '001_' . $name . '.php');
    }

    /**
     * Tests CreateCommand::execute with a database.
     *
     * @return void
     */
    public function testExecuteMethodWithDatabase()
    {
        $path = __DIR__ . '/../Weblog/templates';

        $template = $path . '/CreateUserTable.txt';

        $expected = file_get_contents($template);

        $properties = array('name' => 'create_user_table');

        $properties['--from-database'] = true;

        $this->tester->execute($properties);

        $filename = $this->source . '/migrations/';

        $result = $filename . '001_create_user_table.php';

        $result = file_get_contents((string) $result);

        $this->assertEquals($expected, $result);

        $this->delete('001_create_user_table');
    }
}
