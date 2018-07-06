<?php

namespace Rougin\Refinery;

/**
 * Parser
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Parser
{
    /**
     * @var string
     */
    protected $column = '';

    /**
     * @var string
     */
    protected $command = '';

    /**
     * @var string
     */
    protected $table = '';

    /**
     * Initializes the parser instance.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $allowed = implode('|', $this->commands());

        $pattern = '/(' . $allowed . ')_(.*)_table/';

        preg_match($pattern, $name, $matches);

        $this->table = (string) $matches[2];

        $this->command = (string) $matches[1];

        $pattern = (string) '/(.*)_in_(.*)/';

        preg_match($pattern, $matches[2], $matches);

        if (isset($matches[0])) {
            $this->column = $matches[1];

            $this->table = $matches[2];
        }
    }

    /**
     * Returns an array of commands.
     *
     * @return string[]
     */
    public function commands()
    {
        $commands = array();

        $commands[] = 'add';
        $commands[] = 'create';
        $commands[] = 'delete';
        $commands[] = 'modify';
        $commands[] = 'remove';
        $commands[] = 'update';

        return $commands;
    }

    /**
     * Returns the command.
     *
     * @return string
     */
    public function command()
    {
        return $this->command;
    }

    /**
     * Returns the column.
     *
     * @return string
     */
    public function column()
    {
        return $this->column;
    }

    /**
     * Returns the table.
     *
     * @return string
     */
    public function table()
    {
        return $this->table;
    }
}
