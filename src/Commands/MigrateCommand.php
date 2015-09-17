<?php

namespace Rougin\Refinery\Commands;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Rougin\Blueprint\AbstractCommand;
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
        if (file_exists(APPPATH . 'migrations')) {
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
        $this->setName('migrate')
            ->setDescription('Migrate the database')
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'Migrate to a specified version of the database'
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
        $filenames  = [];
        $timestamps = [];
        $file = file_get_contents(APPPATH . '/config/migration.php');

        // Search the current migration version
        preg_match_all(
            '/\$config\[\'migration_version\'\] = (\d+);/',
            $file,
            $match
        );

        $current = $match[1][0];
        $latest = NULL;

        // Searches a listing of migration files and sorts them after
        $directory = new RecursiveDirectoryIterator(
            APPPATH . 'migrations',
            FilesystemIterator::SKIP_DOTS
        );

        $iterator = new RecursiveIteratorIterator(
            $directory,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $path) {
            $filenames[]  = str_replace('.php', '', $path->getFilename());
            $timestamp = substr($path->getFilename(), 0, 14);

            if ( ! is_numeric($timestamp)) {
                $timestamp = substr($path->getFilename(), 0, 3);
            }

            $timestamps[] = $timestamp;
        }

        sort($filenames);
        sort($timestamps);

        // Might get the latest or the specified version or revert back
        $revert = ($input->getOption('revert'))
            ? $input->getOption('revert') + 1
            : 1;

        $end = count($timestamps) - $revert;

        if ($end < 0) {
            $message = 'We can\'t revert to that specified version.';

            return $output->writeln('<error>' . $message . '</error>');
        }

        $latest = $timestamps[$end];
        $latestFile = $filenames[$end];

        if ($input->getArgument('version')) {
            $version = $input->getArgument('version');
        }

        // Enable migration and change the current version to a latest one
        $this->toggleMigration(TRUE);
        $this->changeVersion($current, $latest);

        $codeigniter = $this->getCodeIgniter();

        $codeigniter->load->library('migration');
        $codeigniter->migration->current();

        $this->toggleMigration();

        // Show messages of migrated files
        if ($current == $latest) {
            $message = 'Database is up to date.';
        }

        for ($counter = 0; $counter < count($timestamps); $counter++) {
            if ($current >= $timestamps[$counter]) {
                continue;
            }

            $filename = $filenames[$counter];
            $message = '"' . $filename . '" has been migrated to the database.';
        }

        if ($input->getArgument('version') || $input->getOption('revert')) {
            $message = 'Database is reverted back to version ' .
                $latest . '. (' . $latestFile . ')';
        }

        return $output->writeln('<info>' . $message . '</info>');
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
