<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Rollback Command
 *
 * Returns to a previous or specified migration.
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class RollbackCommand extends AbstractCommand
{
    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('rollback')
            ->setDescription('Returns to a previous or specified migration')
            ->addArgument('version', InputArgument::OPTIONAL, 'Specified version of the migration');
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
        $end     = count($migrations) - 1;

        if (intval($current) <= 0) {
            return $output->writeln('<error>There\'s nothing to be rollbacked at.</error>');
        }

        $version = $input->getArgument('version');
        $found   = false;

        foreach ($migrations as $migration) {
            if ($migration == $version || empty($version)) {
                $found = true;

                break;
            }
        }

        if (! $found) {
            return $output->writeln('<error>Cannot rollback to version ' . $version . '.</error>');
        }

        $migration = $migrations[$end];
        $fileName  = $filenames[$end];

        if ($version) {
            $migration = $version;
        }

        // Enable migration and change the current version to a latest one
        $this->toggleMigration(true);
        $this->changeVersion($current, $migration);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        $this->toggleMigration();

        $message = "Database is reverted back to version $migration ($fileName)";

        return $output->writeln('<info>' . $message . '</info>');
    }
}
