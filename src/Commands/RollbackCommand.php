<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\SparkPlug\Instance;
use Rougin\Refinery\Common\MigrationHelper;

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
        $migration = file_get_contents(APPPATH . '/config/migration.php');
        $current = MigrationHelper::getLatestVersion($migration);
        $migrationsPath = APPPATH . 'migrations';

        list($filenames, $migrations) = MigrationHelper::getMigrations($migrationsPath);

        // Might get the latest or the specified version or revert back
        $end = count($migrations) - 1;

        if (intval($current) <= 0) {
            $message = 'There\'s nothing to be rollbacked at.';

            return $output->writeln('<error>' . $message . '</error>');
        }

        $version = $input->getArgument('version');
        $versionFound = false;

        foreach ($migrations as $migration) {
            if ($migration == $version || empty($version)) {
                $versionFound = true;

                break;
            }
        }

        if ( ! $versionFound) {
            $message = "Cannot rollback to version $version.";;

            return $output->writeln('<error>' . $message . '</error>');
        }

        $latest = $migrations[$end];
        $latestFile = $filenames[$end];

        if ($version) {
            $latest = $version;
        }

        // Enable migration and change the current version to a latest one
        MigrationHelper::toggleMigration(true);
        MigrationHelper::changeVersion($current, $latest);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        MigrationHelper::toggleMigration();

        $message = "Database is reverted back to version $latest ($latestFile)";

        return $output->writeln('<info>' . $message . '</info>');
    }
}
