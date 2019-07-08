<?php

namespace Rougin\Refinery\Console;

use Rougin\Describe\Column;
use Rougin\Describe\Describe;
use Rougin\Refinery\Builder;
use Rougin\Refinery\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Command
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class CreateCommand extends Command
{
    /**
     * @var \Rougin\Refinery\Builder
     */
    protected $builder;

    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var \Rougin\Refinery\Manager
     */
    protected $manager;

    /**
     * Initializes the command instance.
     *
     * @param \Rougin\Refinery\Builder  $builder
     * @param \Rougin\Describe\Describe $describe
     * @param \Rougin\Refinery\Manager  $manager
     */
    public function __construct(Builder $builder, Describe $describe, Manager $manager)
    {
        $this->builder = $builder;

        $this->describe = $describe;

        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create')->setDescription('Creates a new database migration file');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the migration file');

        $this->addOption('auto-increment', null, InputOption::VALUE_OPTIONAL, 'Sets the "auto_increment" value', false);
        $this->addOption('default', null, InputOption::VALUE_OPTIONAL, 'Sets the "default" value of the column', '');
        $this->addOption('from-database', null, InputOption::VALUE_NONE, 'Creates a migration from the database');
        $this->addOption('length', null, InputOption::VALUE_OPTIONAL, 'Sets the "constraint" value of the column', 50);
        $this->addOption('null', null, InputOption::VALUE_OPTIONAL, 'Sets the column with a nullable value', false);
        $this->addOption('primary', null, InputOption::VALUE_OPTIONAL, 'Sets the column as the primary key', false);
        $this->addOption('type', null, InputOption::VALUE_OPTIONAL, 'Sets the data type of the column', 'varchar');
        $this->addOption('unsigned', null, InputOption::VALUE_OPTIONAL, 'Sets the column with unsigned value', false);
    }

    /**
     * Executes the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $column = $this->column($input);

        $this->builder->column($column);

        if ($input->getOption('from-database')) {
            $this->builder->describe($this->describe);
        }

        $file = $this->builder->make($name);

        $name = $this->manager->filename($name);

        $this->manager->create($name, $file);

        $message = '"' . $name . '" has been created.';

        $output->writeln('<info>' . $message . '</info>');
    }

    /**
     * Initializes a column instance.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @return \Rougin\Describe\Column
     */
    protected function column(InputInterface $input)
    {
        $column = new Column;

        $column->setDefaultValue($input->getOption('default'));

        $column->setDataType(strtoupper($input->getOption('type')));

        $column->setLength($input->getOption('length'));

        $column->setAutoIncrement($input->getOption('auto-increment'));

        $column->setNull($input->getOption('null'));

        $column->setUnsigned($input->getOption('unsigned'));

        $column->setPrimary($input->getOption('primary'));

        return $column;
    }
}
