<?php

namespace Rougin\Refinery\Template;

use Rougin\Classidy\Classidy;
use Rougin\Classidy\Method;
use Rougin\Describe\Column;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Migration extends Classidy
{
    const STYLE_SEQUENCE = 0;

    const STYLE_TIMESTAMP = 1;

    const TYPE_ADD = 'add';

    const TYPE_CREATE = 'create';

    const TYPE_DELETE = 'delete';

    const TYPE_MODIFY = 'modify';

    const TYPE_REMOVE = 'remove';

    const TYPE_UPDATE = 'update';

    /**
     * @var \Rougin\Describe\Column[]
     */
    protected $columns = array();

    /**
     * @var \Rougin\Refinery\Parser
     */
    protected $parser;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        // Converts the migration name into snake_case ---
        /** @var string */
        $name = preg_replace('/[\s]+/', '_', trim($name));

        $this->name = 'Migration_' . $name;
        // -----------------------------------------------
    }

    /**
     * @return self
     */
    public function init()
    {
        $this->extendsTo('Rougin\Refinery\Migration');

        $this->setUpMethod();

        $this->setDownMethod();

        return $this;
    }

    /**
     * @param \Rougin\Refinery\Parser $parser
     *
     * @return self
     */
    public function setParser($parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @param \Rougin\Describe\Column[] $columns
     *
     * @return self
     */
    public function withColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return void
     */
    protected function setDownMethod()
    {
        $method = new Method('down');

        $method->setReturn('void');

        $table = $this->parser->getTable();

        $fn = $this->getDeleteTable($table);

        if ($this->parser->isCreateColumn())
        {
            $fn = $this->getDeleteColumn($table);
        }

        if ($this->parser->isDeleteColumn())
        {
            $fn = $this->getCreateColumn($table);
        }

        if ($this->parser->isDeleteTable())
        {
            $fn = $this->getCreateTable($table);
        }

        $method->setCodeLine($fn);

        $this->addMethod($method);
    }

    /**
     * @return void
     */
    protected function setUpMethod()
    {
        $method = new Method('up');

        $method->setReturn('void');

        $table = $this->parser->getTable();

        $fn = $this->getCreateTable($table);

        if ($this->parser->isCreateColumn())
        {
            $fn = $this->getCreateColumn($table);
        }

        if ($this->parser->isDeleteColumn())
        {
            $fn = $this->getDeleteColumn($table);
        }

        if ($this->parser->isDeleteTable())
        {
            $fn = $this->getDeleteTable($table);
        }

        $method->setCodeLine($fn);

        $this->addMethod($method);
    }

    /**
     * @param string $table
     *
     * @return callable
     */
    protected function getCreateColumn($table)
    {
        $columns = $this->columns;

        $self = $this;

        return function ($lines) use ($self, $columns, $table)
        {
            foreach ($columns as $column)
            {
                $lines = $self->parseColumn($lines, $table, $column);
            }

            return $lines;
        };
    }

    /**
     * @param string $table
     *
     * @return callable
     */
    protected function getCreateTable($table)
    {
        $columns = $this->columns;

        if (count($columns) === 0)
        {
            $columns[] = $this->newIdColumn();
        }

        $self = $this;

        return function ($lines) use ($self, $columns, $table)
        {
            foreach ($columns as $index => $column)
            {
                if ($index !== 0)
                {
                    $lines[] = '';
                }

                $lines = $self->parseColumn($lines, $table, $column);
            }

            $lines[] = '';
            $lines[] = '$this->dbforge->create_table(\'' . $table . '\');';

            return $lines;
        };
    }

    /**
     * @param string $table
     *
     * @return callable
     */
    protected function getDeleteTable($table)
    {
        return function ($lines) use ($table)
        {
            $lines[] = '$this->dbforge->drop_table(\'' . $table . '\');';

            return $lines;
        };
    }

    /**
     * @param string $table
     *
     * @return callable
     */
    protected function getDeleteColumn($table)
    {
        $columns = $this->columns;

        return function ($lines) use ($columns, $table)
        {
            foreach ($columns as $column)
            {
                $name = $column->getField();

                $lines[] = '$this->dbforge->drop_column(\'' . $table . '\', \'' . $name . '\');';
            }

            return $lines;
        };
    }

    /**
     * @return \Rougin\Describe\Column
     */
    protected function newIdColumn()
    {
        $column = new Column;

        $column->setDataType('integer');
        $column->setField('id');
        $column->setAutoIncrement(true);
        $column->setLength(10);
        $column->setNull(false);
        $column->setPrimary(true);

        return $column;
    }

    /**
     * @param string[]                $lines
     * @param string                  $table
     * @param \Rougin\Describe\Column $column
     *
     * @return string[]
     */
    protected function parseColumn($lines, $table, Column $column)
    {
        $default = $column->getDefaultValue();
        $default = $default ? '"' . $default . '"' : 'null';
        $increment = $column->isAutoIncrement() ? 'true' : 'false';
        $length = $column->getLength();
        $name = $column->getField();
        $null = $column->isNull() ? 'true' : 'false';
        $type = strtolower($column->getDataType());
        $type = str_replace('string', 'varchar', $type);
        $unsigned = $column->isUnsigned() ? 'true' : 'false';

        $lines[] = '$data = array(\'' . $name . '\' => array());';
        $lines[] = '$data[\'' . $name . '\'][\'type\'] = \'' . $type . '\';';
        $lines[] = '$data[\'' . $name . '\'][\'auto_increment\'] = ' . $increment . ';';
        $lines[] = '$data[\'' . $name . '\'][\'constraint\'] = ' . $length . ';';

        if (! $column->isPrimaryKey())
        {
            $lines[] = '$data[\'' . $name . '\'][\'default\'] = ' . $default . ';';
            $lines[] = '$data[\'' . $name . '\'][\'null\'] = ' . $null . ';';
            $lines[] = '$data[\'' . $name . '\'][\'unsigned\'] = ' . $unsigned . ';';
        }

        if ($this->parser->isCreateTable() || $this->parser->isDeleteTable())
        {
            $lines[] = '$this->dbforge->add_field($data);';
        }
        else
        {
            $lines[] = '$this->dbforge->add_column(\'' . $table . '\', $data);';
        }

        if ($column->isPrimaryKey())
        {
            $lines[] = '$this->dbforge->add_key(\'' . $name . '\', true);';
        }

        return $lines;
    }
}
