<?php

namespace Rougin\Refinery;

use Rougin\Describe\Describe;
use Rougin\Describe\Column;

/**
 * Builder
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Builder
{
    /**
     * @var \Rougin\Describe\Column
     */
    protected $column;

    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var \Rougin\Refinery\Parser
     */
    protected $parser;

    /**
     * Initializes the column instance.
     */
    public function __construct()
    {
        $column = new Column;

        $column->setDataType('integer');

        $column->setLength(10);

        $column->setAutoIncrement(true);

        $this->column = $column;
    }

    /**
     * Sets the column instance to be used.
     *
     * @param  \Rougin\Describe\Column $column
     * @return self
     */
    public function column(Column $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Sets the Describe instance.
     *
     * @param  \Rougin\Describe\Describe $describe
     * @return self
     */
    public function describe(Describe $describe)
    {
        $this->describe = $describe;

        return $this;
    }

    /**
     * Builds the migration file.
     *
     * @param  string $name
     * @return boolean
     */
    public function make($name)
    {
        $this->parser = new Parser((string) $name);

        $migration = __DIR__ . '/Templates/Migration.txt';

        $file = file_get_contents($migration);

        $file = str_replace('$NAME', $name, $file);

        if ($this->describe instanceof Describe) {
            $file = str_replace('$UP', $this->database(), $file);

            $down = $this->template('DeleteTable');

            return str_replace('$DOWN', (string) $down, $file);
        }

        return $this->prepare($file, $this->column);
    }

    /**
     * Returns a boolean string value.
     *
     * @param  boolean $value
     * @return string
     */
    protected function boolean($value)
    {
        return $value ? 'TRUE' : 'FALSE';
    }

    /**
     * Generates code based from the column instance.
     *
     * @param  \Rougin\Describe\Column $column
     * @return string
     */
    protected function create(Column $column, $file)
    {
        $unsigned = $this->boolean($column->isUnsigned());

        $increment = $this->boolean($column->isAutoIncrement());

        $null = (string) $this->boolean($column->isNull());

        $file = str_replace('$DEFAULT', $column->getDefaultValue(), $file);

        $file = str_replace('$INCREMENT', $increment, $file);

        $file = str_replace('$LENGTH', $column->getLength(), $file);

        $file = str_replace('$NAME', $column->getField(), $file);

        $file = str_replace('$NULL', $null, (string) $file);

        $file = str_replace('$TYPE', $column->getDataType(), $file);

        return str_replace('$UNSIGNED', $unsigned, $file);
    }

    /**
     * Creates a migration based from a database schema.
     *
     * @return string
     */
    protected function database()
    {
        $table = (string) $this->parser->table();

        $up = '$this->dbforge->create_table(\'' . $table . '\');';

        $columns = $this->describe->columns($table);

        foreach ($columns as $column) {
            $create = $this->template('CreateColumn');

            $data = $this->create($column, $create);

            $up = $data . "\r\n\r\n        " . $up;
        }

        return $up;
    }

    /**
     * Prepares the contents of the migration file.
     *
     * @param  string                  $file
     * @param  \Rougin\Describe\Column $column
     * @return string
     */
    protected function prepare($file, Column $column)
    {
        $text = $this->parser->column() ? 'Column' : 'Table';

        $up = $this->template('Create' . $text);

        $up = (string) $this->create($column, $up);

        $down = $this->template((string) 'Delete' . $text);

        $delete = array('delete', 'remove');

        $exists = in_array($this->parser->command(), $delete);

        $exists && list($up, $down) = array($down, $up);

        $file = (string) str_replace('$UP', $up, $file);

        return (string) str_replace('$DOWN', $down, $file);
    }

    /**
     * Creates a template based on given filename.
     *
     * @param  string $filename
     * @return string
     */
    protected function template($filename)
    {
        $file = __DIR__ . '/Templates/' . $filename . '.txt';

        $file = (string) file_get_contents((string) $file);

        $file = str_replace('$TABLE', $this->parser->table(), $file);

        $column = (string) $this->parser->column();

        $updated = str_replace('$NAME', $column, $file);

        return $column === '' ? $file : $updated;
    }
}
