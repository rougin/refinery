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
     * @var \Rougin\Describe\Column|null
     */
    protected $column = null;

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
     * @param \Rougin\Describe\Column $column
     *
     * @return self
     */
    public function withColumn(Column $column)
    {
        $this->column = $column;

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

        if ($this->column)
        {
            if ($this->parser->isCreateColumn())
            {
                $fn = $this->getDeleteColumn($this->column, $table);
            }

            if ($this->parser->isDeleteColumn())
            {
                $fn = $this->getCreateColumn($this->column, $table);
            }
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

        if ($this->column)
        {
            if ($this->parser->isCreateColumn())
            {
                $fn = $this->getCreateColumn($this->column, $table);
            }

            if ($this->parser->isDeleteColumn())
            {
                $fn = $this->getDeleteColumn($this->column, $table);
            }
        }

        if ($this->parser->isDeleteTable())
        {
            $fn = $this->getDeleteTable($table);
        }

        $method->setCodeLine($fn);

        $this->addMethod($method);
    }

    /**
     * @param \Rougin\Describe\Column $column
     * @param string                  $table
     *
     * @return callable
     */
    protected function getCreateColumn(Column $column, $table)
    {
        return function ($lines) use ($column, $table)
        {
            $default = $column->getDefaultValue();
            $default = $default ? '"' . $default . '"' : 'null';
            $increment = $column->isAutoIncrement() ? 'true' : 'false';
            $length = $column->getLength();
            $name = $column->getField();
            $null = $column->isNull() ? 'true' : 'false';
            $type = $column->getDataType();
            $unsigned = $column->isUnsigned() ? 'true' : 'false';

            $lines[] = '$data = array(\'' . $name . '\' => array());';
            $lines[] = '';
            $lines[] = '$data[\'' . $name . '\'][\'type\'] = \'' . $type . '\';';
            $lines[] = '$data[\'' . $name . '\'][\'constraint\'] = ' . $length . ';';
            $lines[] = '$data[\'' . $name . '\'][\'auto_increment\'] = ' . $increment . ';';
            $lines[] = '$data[\'' . $name . '\'][\'default\'] = ' . $default . ';';
            $lines[] = '$data[\'' . $name . '\'][\'null\'] = ' . $null . ';';
            $lines[] = '$data[\'' . $name . '\'][\'unsigned\'] = ' . $unsigned . ';';
            $lines[] = '';
            $lines[] = '$this->dbforge->add_column(\'' . $table . '\', $data);';

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
        return function ($lines) use ($table)
        {
            $lines[] = '$data = array(\'id\' => array());';
            $lines[] = '$data[\'id\'][\'type\'] = \'integer\';';
            $lines[] = '$data[\'id\'][\'auto_increment\'] = true;';
            $lines[] = '$data[\'id\'][\'constraint\'] = 10;';
            $lines[] = '$this->dbforge->add_field($data);';
            $lines[] = '$this->dbforge->add_key(\'id\', true);';
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
     * @param \Rougin\Describe\Column $column
     * @param string                  $table
     *
     * @return callable
     */
    protected function getDeleteColumn(Column $column, $table)
    {
        return function ($lines) use ($column, $table)
        {
            $name = $column->getField();

            $lines[] = '$this->dbforge->drop_column(\'' . $table . '\', \'' . $name . '\');';

            return $lines;
        };
    }
}
