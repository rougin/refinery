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
 * Rollback Command
 *
 * Returns to a previous/specified migration
 * 
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class RollbackCommand extends AbstractCommand
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
        $this->setName('rollback')
            ->setDescription('Returns to a previous/specified migration')
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'Specified version of the migration'
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

        // Might get the latest or the specified version or revert back
        $end = count($migrations) - 2;

        if ($end < 0) {
            $message = 'We can\'t rollback to that specified version.';

            return $output->writeln('<error>' . $message . '</error>');
        }

        if ($current <= 0) {
            $message = 'There\'s nothing to be rollbacked at.';

            return $output->writeln('<error>' . $message . '</error>');
        }

        $latest = $migrations[$end];
        $latestFile = $filenames[$end];

        if ($input->getArgument('version')) {
            $latest = $input->getArgument('version');
        }

        // Enable migration and change the current version to a latest one
        Tools::toggleMigration(TRUE);
        Tools::changeVersion($current, $latest);

        $codeigniter = Tools::getCodeIgniter();
        $codeigniter->load->library('migration');
        $codeigniter->migration->current();

        Tools::toggleMigration();

        $message = 'Database is reverted back to version ' .
            $latest . '. (' . $latestFile . ')';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
