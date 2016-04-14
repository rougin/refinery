<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\SparkPlug\Instance;
use Rougin\Refinery\Common\MigrationHelper;

/**
 * Reset Migration Command
 *
 * Resets all migrations.
 * 
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class ResetCommand extends AbstractCommand
{
    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('reset')
            ->setDescription('Resets all migrations');
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

        if ($current <= 0) {
            $message = 'Database\'s version is now in 0.';

            return $output->writeln('<info>' . $message . '</info>');
        }

        // Enables migration and change the current version to 0
        MigrationHelper::toggleMigration(true);
        MigrationHelper::changeVersion($current, 0);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        MigrationHelper::toggleMigration();

        return $output->writeln('<info>Database has been resetted.</info>');
    }
}
