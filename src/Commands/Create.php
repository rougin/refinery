<?php

namespace Rougin\Refinery\Commands;

use Rougin\Blueprint\Command;
use Rougin\Classidy\Generator;
use Rougin\Describe\Column;
use Rougin\Refinery\Parser;
use Rougin\Refinery\Refinery;
use Rougin\Refinery\Template\Migration;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Create extends Command
{
    /**
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver = null;

    /**
     * @var string
     */
    protected $name = 'create';

    /**
     * @var string
     */
    protected $description = 'Create a new database migration';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @param \Rougin\Refinery\Refinery $refinery
     */
    public function __construct(Refinery $refinery)
    {
        $this->driver = $refinery->getDriver();

        $this->path = $refinery->getAppPath();
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        $this->addArgument('name', 'Name of the migration file');

        $this->addValueOption('auto-increment', 'Sets the "auto_increment" value', false);
        $this->addValueOption('default', 'Sets the "default" value of the column');
        $this->addOption('from-database', 'Creates a migration from the database');
        $this->addValueOption('length', 'Sets the "constraint" value of the column', 50);
        $this->addValueOption('null', 'Sets the column with a nullable value', false);
        $this->addValueOption('primary', 'Sets the column as the primary key', false);
        $this->addValueOption('type', 'Sets the data type of the column', 'varchar');
        $this->addValueOption('unsigned', 'Sets the column with unsigned value', false);
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        /** @var string */
        $name = $this->getArgument('name');

        $parser = new Parser($name);

        $class = new Migration($name);

        $class->setParser($parser);

        if ($parser->isCreateColumn() || $parser->isDeleteColumn())
        {
            /** @var string */
            $column = $parser->getColumn();

            $column = $this->setColumn($column);

            $class->withColumn($column);
        }

        $maker = new Generator;

        $result = $maker->make($class->init());

        $this->createFile($name, $result);

        $this->showPass('"' . $name . '" successfully created!');

        return self::RETURN_SUCCESS;
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    protected function createFile($name, $class)
    {
        $path = $this->path . '/migrations/';

        $style = $this->getNumberStyle();

        // TODO: Get latest sequence from config ---
        $prefix = null;
        // -----------------------------------------

        if ($style === Migration::STYLE_TIMESTAMP)
        {
            $prefix = date('YmdHis');
        }

        $file = $path . $prefix . '_' . $name;

        file_put_contents($file . '.php', $class);
    }

    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    protected function getConfig($name, $default = null)
    {
        $file = $this->path . '/config/migration.php';

        /** @var string */
        $config = file_get_contents($file);

        $pattern = '/\$config\[\\\'' . $name . '\\\'\] = (.*?);/i';

        preg_match($pattern, $config, $matches);

        return $matches ? $matches[1] : $default;
    }

    /**
     * @return integer
     */
    protected function getNumberStyle()
    {
        $type = $this->getConfig('migration_type');

        $style = Migration::STYLE_SEQUENCE;

        if ($type === '\'timestamp\'')
        {
            $style = Migration::STYLE_TIMESTAMP;
        }

        return $style;
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Describe\Column
     */
    protected function setColumn($name)
    {
        $column = new Column;

        $column->setField($name);

        /** @var string */
        $default = $this->getOption('default');
        $column->setDefaultValue($default);

        /** @var string */
        $type = $this->getOption('type');
        $column->setDataType(strtoupper($type));

        /** @var integer */
        $length = $this->getOption('length');
        $column->setLength($length);

        /** @var boolean */
        $increment = $this->getOption('auto-increment');
        $column->setAutoIncrement($increment);

        /** @var boolean */
        $null = $this->getOption('null');
        $column->setNull($null);

        /** @var boolean */
        $unsigned = $this->getOption('unsigned');
        $column->setUnsigned($unsigned);

        /** @var boolean */
        $primary = $this->getOption('primary');
        return $column->setPrimary($primary);
    }
}
