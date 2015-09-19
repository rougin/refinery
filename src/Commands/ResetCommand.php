<?php

namespace Rougin\Refinery\Commands;

use Rougin\Blueprint\AbstractCommand;
use Rougin\Refinery\Tools;
use Rougin\SparkPlug\Instance;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     *
     * @return bool
     */
    public function isEnabled()
    {
        $migrations = glob(APPPATH . 'migrations/*.php');

        if (count($migrations) > 0) {
            return TRUE;
        }

        return FALSE;
    }

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
        $current = Tools::getLatestVersion(
            file_get_contents(APPPATH . '/config/migration.php')
        );

        if ($current <= 0) {
            $message = 'Database\'s version is now in 0.';

            return $output->writeln('<info>' . $message . '</info>');
        }

        // Enables migration and change the current version to 0
        Tools::toggleMigration(TRUE);
        Tools::changeVersion($current, 0);

        $codeigniter = Tools::getCodeIgniter();
        $codeigniter->load->library('migration');
        $codeigniter->migration->current();

        Tools::toggleMigration();

        return $output->writeln('<info>Database has been resetted.</info>');
    }
}