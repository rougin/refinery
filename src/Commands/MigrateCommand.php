<?php

namespace Rougin\Refinery\Commands;

use Rougin\Refinery\AbstractCommand;
use Rougin\Refinery\Tools;
use Rougin\SparkPlug\Instance;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Tools::isEnabled();
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Migrates the database')
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'Migrates to a specified version of the database'
            )->addOption(
                'revert',
                NULL,
                InputOption::VALUE_OPTIONAL,
                'Number of times to revert from the list of migrations'
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
        $current = Tools::getLatestVersion(
            file_get_contents(APPPATH . '/config/migration.php')
        );

        list($filenames, $migrations) = Tools::getMigrations(
            APPPATH . 'migrations'
        );

        $end = count($migrations) - 1;
        $latest = $migrations[$end];

        // Enable migration and change the current version to a latest one
        Tools::toggleMigration(TRUE);
        Tools::changeVersion($current, $latest);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        Tools::toggleMigration();

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
            $message = '"' . $filename . '" has been migrated to the database.';

            $output->writeln('<info>' . $message . '</info>');
        }
    }
}
