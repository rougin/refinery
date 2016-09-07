<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\SparkPlug\Instance;
use Rougin\Refinery\Common\MigrationHelper;

/**
 * Migrate Command
 *
 * Migrates the list of migrations found in "application/migrations" directory.
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MigrateCommand extends AbstractCommand
{
    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('migrate')
            ->setDescription('Migrates the database');
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
        $migration = file_get_contents(APPPATH . '/config/migration.php');
        $current = MigrationHelper::getLatestVersion($migration);
        $migrationsPath = APPPATH . 'migrations';

        list($filenames, $migrations) = MigrationHelper::getMigrations($migrationsPath);

        $end = count($migrations) - 1;
        $latest = $migrations[$end];

        // Enable migration and change the current version to a latest one
        MigrationHelper::toggleMigration(true);
        MigrationHelper::changeVersion($current, $latest);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        MigrationHelper::toggleMigration();

        // Show messages of migrated files
        if ($current == $latest) {
            $message = 'Database is up to date.';

            return $output->writeln('<info>' . $message . '</info>');
        }

        $migrationsCount = count($migrations);

        for ($counter = 0; $counter < $migrationsCount; $counter++) {
            if ($current >= $migrations[$counter]) {
                continue;
            }

            $filename = $filenames[$counter];
            $message = "$filename has been migrated to the database";

            $output->writeln('<info>' . $message . '</info>');
        }
    }
}
