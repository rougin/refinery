<?php

namespace Rougin\Refinery\Commands;

use Rougin\Blueprint\AbstractCommand;
use Rougin\SparkPlug\Instance;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Reset Migration Command
 *
 * Rollbacks all migrations.
 * 
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MigrateResetCommand extends AbstractCommand
{
    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('migrate:reset')
            ->setDescription('Rollback all migrations');
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
        $file = file_get_contents(APPPATH . '/config/migration.php');

        // Search the current migration version
        preg_match_all(
            '/\$config\[\'migration_version\'\] = (\d+);/',
            $file,
            $match
        );

        $current = $match[1][0];

        // Enables migration and change the current version to 0
        $this->toggleMigration(TRUE);
        $this->changeVersion($current, 0);

        $codeigniter = $this->getCodeIgniter();

        $codeigniter->load->library('migration');
        $codeigniter->migration->current();

        $this->toggleMigration();

        return $output->writeln('<info>Database has been resetted.</info>');
    }

    /**
     * Changes the migration version.
     * 
     * @param  int $current
     * @param  int $timestamp
     * @return void
     */
    protected function changeVersion($current, $timestamp)
    {
        $path = APPPATH . '/config/migration.php';
        $migrationFile = file_get_contents($path);

        $currentVersion = '$config[\'migration_version\'] = ' . $current . ';';
        $newVersion = '$config[\'migration_version\'] = ' . $timestamp . ';';

        $migrationFile = str_replace(
            $currentVersion,
            $newVersion,
            $migrationFile
        );

        $file = fopen($path, 'wb');
        file_put_contents($path, $file);
        fclose($file);
    }

    /**
     * Gets an instance of CodeIgniter.
     * 
     * @return Rougin\SparkPlug\Instace
     */
    private function getCodeIgniter()
    {
        $instance = new Instance();

        return $instance->get();
    }

    /**
     * Enables/disables the Migration Class.
     * 
     * @param  boolean $enabled
     * @return void
     */
    protected function toggleMigration($enabled = FALSE)
    {
        $path = APPPATH . '/config/migration.php';
        $migrationFile = file_get_contents($path);

        $search = '$config[\'migration_enabled\'] = TRUE;';
        $replace = '$config[\'migration_enabled\'] = FALSE;';

        if ($enabled) {
            $search = '$config[\'migration_enabled\'] = FALSE;';
            $replace = '$config[\'migration_enabled\'] = TRUE;';
        }

        $migrationFile = str_replace($search, $replace, $migrationFile);

        $file = fopen($path, 'wb');
        file_put_contents($path, $migrationFile);
        fclose($file);
    }
}