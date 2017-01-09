<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Migration Command
 *
 * Creates a new migration file based on its file name.
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateMigrationCommand extends AbstractCommand
{
    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Creates a new migration file')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the migration file')
            ->addOption('from-database', null, InputOption::VALUE_NONE, 'Generates a migration based from the database')
            ->addOption('sequential', null, InputOption::VALUE_NONE, 'Generates a migration file with a sequential identifier')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'Data type of the column', 'varchar')
            ->addOption('length', null, InputOption::VALUE_OPTIONAL, 'Length of the column', 50)
            ->addOption('auto_increment', null, InputOption::VALUE_OPTIONAL, 'Generates an "AUTO_INCREMENT" flag on the column', false)
            ->addOption('default', null, InputOption::VALUE_OPTIONAL, 'Generates a default value in the column definition', '')
            ->addOption('null', null, InputOption::VALUE_OPTIONAL, 'Generates a "NULL" value in the column definition', false)
            ->addOption('primary', null, InputOption::VALUE_OPTIONAL, 'Generates a "PRIMARY" value in the column definition', false)
            ->addOption('unsigned', null, InputOption::VALUE_OPTIONAL, 'Generates an "UNSIGNED" value in the column definition', false);
    }

    /**
     * Executes the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = $this->getFileName($input->getArgument('name'), $input->getOption('sequential'));
        $keywords = $this->getKeywords($input->getArgument('name'));

        if ($input->getOption('from-database') && $keywords[0] != 'create') {
            throw new \InvalidArgumentException('--from-database is only available to create_*table*_table keyword');
        }

        $data = $this->prepareData($input, $keywords);

        if ($data['command_name'] != 'create') {
            $data = $this->defineColumns($input, $keywords, $data);
        }

        $rendered = $this->renderer->render('Migration.twig', $data);
        $rendered = str_replace("));\n\n\t}", "));\n\t}", $rendered);

        $this->filesystem->write('application/migrations/' . $fileName . '.php', $rendered);

        return $output->writeln('<info>"' . $fileName . '" has been created.</info>');
    }

    /**
     * Defines the columns to be included in the migration.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  array                                           $keywords
     * @param  array                                           $data
     * @return array
     */
    protected function defineColumns(InputInterface $input, array $keywords, array $data)
    {
        $data['table_name'] = $keywords[1];

        array_push($data['columns'], $this->setColumn($input, $keywords[2]));

        if ($data['command_name'] == 'modify') {
            foreach ($this->describe->getTable($data['table_name']) as $column) {
                $column->getField() != $keywords[2] || array_push($data['defaults'], $column);
            }
        }

        return $data;
    }

    /**
     * Gets the file name of the specified migration.
     *
     * @param  string  $name
     * @param  boolean $isSequential
     * @return string
     */
    protected function getFileName($name, $isSequential = false)
    {
        $migrationType = $this->getMigrationType();

        $fileName = date('YmdHis') . '_' . underscore($name);

        if ($migrationType == 'sequential' || $isSequential) {
            $number = 1;

            $files = new \FilesystemIterator(APPPATH . 'migrations', \FilesystemIterator::SKIP_DOTS);

            $number += iterator_count($files);

            $sequence = sprintf('%03d', $number);
            $fileName = $sequence . '_' . substr($fileName, 15);
        }

        return $fileName;
    }

    /**
     * Gets the defined keywords from the command.
     *
     * @param  string $name
     * @return array
     */
    protected function getKeywords($name)
    {
        $empty = [ '', '', '' ];
        $path  = APPPATH . 'migrations';

        file_exists($path) || mkdir($path);

        preg_match('/(.*?)_(.*?)_table/', underscore($name), $keywords);

        if (strpos($keywords[2], '_in') !== false) {
            preg_match('/(.*?)_(.*?)_in_(.*?)_table/', underscore($name), $keywords);

            list($keywords[3], $keywords[2]) = [ $keywords[2], $keywords[3] ];
        }

        array_shift($keywords);

        return array_replace($empty, $keywords);
    }

    /**
     * Prepares the data to be inserted in the template.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  array                                           $keywords
     * @return array
     */
    protected function prepareData(InputInterface $input, array $keywords)
    {
        $data = [
            'class_name'   => underscore($input->getArgument('name')),
            'columns'      => [],
            'command_name' => $keywords[0],
            'data_types'   => [ 'string' => 'VARCHAR', 'integer' => 'INT' ],
            'defaults'     => [],
            'table_name'   => $keywords[1],
        ];

        if ($input->getOption('from-database') && $data['command_name'] == 'create') {
            $data['columns'] = $this->describe->getTable($data['table_name']);
        }

        return $data;
    }

    /**
     * Sets properties for a specified column
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  string                                          $fieldName
     * @return \Rougin\Describe\Column
     */
    protected function setColumn(InputInterface $input, $fieldName)
    {
        $column = new \Rougin\Describe\Column;

        $column->setField($fieldName);
        $column->setNull($input->getOption('null'));
        $column->setDataType($input->getOption('type'));
        $column->setLength($input->getOption('length'));
        $column->setPrimary($input->getOption('primary'));
        $column->setUnsigned($input->getOption('unsigned'));
        $column->setDefaultValue($input->getOption('default'));
        $column->setAutoIncrement($input->getOption('auto_increment'));

        return $column;
    }
}
