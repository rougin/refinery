<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->setName('migrate')->setDescription('Migrates the database');
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
        list($filenames, $migrations) = $this->getMigrations(APPPATH . 'migrations');

        $current = $this->getLatestVersion();
        $latest  = $migrations[count($migrations) - 1];

        // Enable migration and change the current version to a latest one
        $this->toggleMigration(true);
        $this->changeVersion($current, $latest);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        $this->toggleMigration();

        // Show messages of migrated files
        if ($current != $latest) {
            for ($counter = 0; $counter < count($migrations); $counter++) {
                if ($current >= $migrations[$counter]) {
                    continue;
                }

                $message = $filenames[$counter] . ' has been migrated to the database.';

                return $output->writeln('<info>' . $message . '</info>');
            }
        }

        return $output->writeln('<info>Database is up to date.</info>');
    }
}
