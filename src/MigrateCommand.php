<?php

namespace Rougin\Refinery;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected $ci;
    protected $migration;

    /**
     * Get the CodeIgniter instance and the migration file
     */
    public function __construct($codeigniter, $migration)
    {
        parent::__construct();

        $this->ci = $codeigniter;
        $this->migration = $migration;
    }

    /**
     * Change the migration version
     * 
     * @param  int $current
     * @param  int $timestamp
     */
    protected function _change_version($current, $timestamp)
    {
        $migration = file_get_contents($this->migration['path']);

        $currentVersion = '$config[\'migration_version\'] = ' . $current . ';';
        $newVersion = '$config[\'migration_version\'] = ' . $timestamp . ';';

        $migration = str_replace($currentVersion, $newVersion, $migration);

        $file = fopen($this->migration['path'], 'wb');
        file_put_contents($this->migration['path'], $migration);
        fclose($file);
    }

    /**
     * Enable/disable the Migration Class
     * 
     * @param  boolean $enabled
     */
    protected function _toggle_migration($enabled = FALSE)
    {
        $migration = file_get_contents($this->migration['path']);
        $search = '$config[\'migration_enabled\'] = TRUE;';
        $replace = '$config[\'migration_enabled\'] = FALSE;';

        if ($enabled) {
            $search  = '$config[\'migration_enabled\'] = FALSE;';
            $replace = '$config[\'migration_enabled\'] = TRUE;';
        }

        $migration = str_replace($search, $replace, $migration);

        $file = fopen($this->migration['path'], 'wb');
        file_put_contents($this->migration['path'], $migration);
        fclose($file);
    }

    /**
     * Set the configurations of the specified command
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
     * Execute the command
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * Search the current migration version
         */

        preg_match_all('/\$config\[\'migration_version\'\] = (\d+);/', $this->migration['file'], $match);
        $current = $match[1][0];
        $latest = NULL;

        $filenames = array();
        $timestamps = array();

        /**
         * Searches a listing of migration files and sorts them after
         */

        $directory = new \RecursiveDirectoryIterator(APPPATH . 'migrations', \FilesystemIterator::SKIP_DOTS);
        foreach (new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST) as $path) {
            $filenames[] = str_replace('.php', '', $path->getFilename());
            $timestamp = substr($path->getFilename(), 0, 14);

            if ( ! is_numeric($timestamp)) {
                $timestamp = substr($path->getFilename(), 0, 3);
            }

            $timestamps[] = $timestamp;
        }

        sort($filenames);
        sort($timestamps);

        /**
         * Might get the latest or the specified version or revert back
         */

        $revert     = ($input->getOption('revert')) ? $input->getOption('revert') + 1 : 1;
        $end        = count($timestamps) - $revert;

        if ($end < 0) {
            $output->writeln('<error>We can\'t revert to that specified version.</error>');
            return;
        }

        $latest     = $timestamps[$end];
        $latestFile = $filenames[$end];

        if ($input->getArgument('version')) {
            $version = $input->getArgument('version');
        }

        /**
         * Enable migration and change the current version to a latest one
         */

        $this->_toggle_migration(TRUE);
        $this->_change_version($current, $latest);

        $this->ci->load->library('migration');
        $this->ci->migration->current();

        $this->_toggle_migration();

        /**
         * Show messages of migrated files
         */

        if ($current == $latest) {
            $output->writeln('<info>Database is up to date.</info>');
            return;
        }

        for ($counter = 0; $counter < count($timestamps); $counter++) {
            if ($current >= $timestamps[$counter]) {
                continue;
            }

            $filename = $filenames[$counter];
            $output->writeln('<info>"' . $filename . '" has been migrated to the database.</info>');
        }

        if ($input->getArgument('version') || $input->getOption('revert')) {
            $output->writeln('<info>Database is reverted back to version ' . $latest . '. (' . $latestFile . ')</info>');
        }
    }
}
