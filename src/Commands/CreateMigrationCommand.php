<?php

namespace Rougin\Refinery\Commands;

use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Describe\Column;
use Rougin\Describe\Describe;

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
     * @return bool
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
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the migration file'
            )->addOption(
                'from-database',
                null,
                InputOption::VALUE_NONE,
                'Generates a migration based from the database'
            )->addOption(
                'sequential',
                null,
                InputOption::VALUE_NONE,
                'Generates a migration file with a sequential identifier'
            )->addOption(
                'type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Data type of the column',
                'varchar'
            )->addOption(
                'length',
                null,
                InputOption::VALUE_OPTIONAL,
                'Length of the column',
                50
            )->addOption(
                'auto_increment',
                null,
                InputOption::VALUE_OPTIONAL,
                'Generates an "AUTO_INCREMENT" flag on the column',
                false
            )->addOption(
                'default',
                null,
                InputOption::VALUE_OPTIONAL,
                'Generates a default value in the column definition',
                ''
            )->addOption(
                'null',
                null,
                InputOption::VALUE_OPTIONAL,
                'Generates a "NULL" value in the column definition',
                false
            )->addOption(
                'primary',
                null,
                InputOption::VALUE_OPTIONAL,
                'Generates a "PRIMARY" value in the column definition',
                false
            )->addOption(
                'unsigned',
                null,
                InputOption::VALUE_OPTIONAL,
                'Generates an "UNSIGNED" value in the column definition',
                false
            );
    }

    /**
     * Executes the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return object|OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = underscore($input->getArgument('name'));
        $path = APPPATH . 'migrations';

        // Creates a "application/migrations" directory if it doesn't exist yet
        if (! file_exists($path)) {
            mkdir($path);
        }

        $fileName = $path . '/' . date('YmdHis') . '_' . $name . '.php';

        $isSequential = strpos(
            file_get_contents(APPPATH . '/config/migration.php'),
            '$config[\'migration_type\'] = \'timestamp\''
        );

        if ($input->getOption('sequential') || $isSequential === false) {
            $number = 1;

            $skipDots = FilesystemIterator::SKIP_DOTS;
            $files = new FilesystemIterator($path . '/', $skipDots);

            if (iterator_count($files) > 0) {
                $number += iterator_count($files);
            }

            $sequence = sprintf('%03d', $number);
            $fileName = $path . '/' . $sequence . '_' . $name . '.php';
        }

        $keywords = explode('_', $name);

        if (
            $input->getOption('from-database')
            && $keywords[0] != 'create'
            && count($keywords) != 3
        ) {
            $command = '--from-database';
            $keyword = 'create_*table*_table';

            $message = "$command is only available to $keyword keyword";

            return $output->writeln('<error>' . $message . '</error>');
        }

        $data = [];
        $data['columns'] = [];
        $data['command'] = $keywords[0];
        $data['defaultColumns'] = [];
        $data['description'] = str_replace($path . '/', '', $fileName);
        $data['name'] = $name;
        $data['table'] = (isset($keywords[1])) ? $keywords[1] : '';

        $data['dataTypes'] = [
            'string' => 'VARCHAR',
            'integer' => 'INT'
        ];

        switch ($data['command']) {
            case 'create':
                if (! $input->getOption('from-database')) {
                    break;
                }

                $data['columns'] = $this->describe->getTable($data['table']);

                break;
            case 'add':
            case 'delete':
                $field = $keywords[1];
                $data['table'] = (isset($keywords[3])) ? $keywords[3] : '';

                $column = new Column;
                $column->setField($field);

                array_push($data['columns'], $this->setColumn($column, $input));

                break;
            case 'modify':
                $field = $keywords[1];
                $data['table'] = (isset($keywords[3])) ? $keywords[3] : '';

                $column = new Column;
                $column->setField($field);

                array_push($data['columns'], $this->setColumn($column, $input));

                $table = $this->describe->getTable($data['table']);

                foreach ($table as $column) {
                    if ($column->getField() == $field) {
                        array_push($data['defaultColumns'], $column);

                        break;
                    }
                }

                break;
        }

        $template = $this->renderer->render('Migration.tpl', $data);

        $file = fopen($fileName, 'wb');
        file_put_contents($fileName, $template);
        fclose($file);

        $fileName = str_replace($path . '/', '', $fileName);
        $message = '"' . $fileName . '" has been created.';

        return $output->writeln('<info>' . $message . '</info>');
    }

    /**
     * Sets properties for a specified column
     *
     * @param  Column         $column
     * @param  InputInterface $input
     * @return Column
     */
    private function setColumn(Column $column, InputInterface $input)
    {
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
