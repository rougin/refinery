<?php

namespace Rougin\Refinery;

/**
 * Parser Test
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Parser::command.
     *
     * @return void
     */
    public function testCommandMethod()
    {
        $parser = new Parser('create_users_table');

        $expected = 'create';

        $result = $parser->command();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test Parser::column.
     *
     * @return void
     */
    public function testColumnMethod()
    {
        $migration = 'add_created_in_users_table';

        $parser = new Parser((string) $migration);

        $expected = 'created';

        $result = $parser->column();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test Parser::column with multiple words.
     *
     * @return void
     */
    public function testColumnMethodWithMultipleWords()
    {
        $migration = 'add_document_id_in_users_table';

        $parser = new Parser((string) $migration);

        $expected = 'document_id';

        $result = $parser->column();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test Parser::table.
     *
     * @return void
     */
    public function testTableMethod()
    {
        $parser = new Parser('create_users_table');

        $expected = 'users';

        $result = $parser->table();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test Parser::table with multiple words.
     *
     * @return void
     */
    public function testTableMethodWithMultipleWords()
    {
        $migration = 'create_tbl_user_logs_table';

        $parser = new Parser((string) $migration);

        $expected = 'tbl_user_logs';

        $result = $parser->table();

        $this->assertEquals($expected, $result);
    }
}
