<?php

namespace Rougin\Refinery\Console;

use Rougin\Refinery\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Change Command
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class ChangeCommand extends Command
{
    /**
     * @var string
     */
    protected $done = 'Migrated:  ';

    /**
     * @var \Rougin\Refinery\Manager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $pending = 'Migrating: ';

    /**
     * @var boolean
     */
    protected $reversed = false;

    /**
     * Initializes the command instance.
     *
     * @param \Rougin\Refinery\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * Returns the current version of migration.
     *
     * @param  array $migrations
     * @return integer
     */
    protected function current($migrations)
    {
        $current = (string) $this->manager->current();

        $start = array_search($current, $migrations);

        $value = $this->reversed ? $start : $start + 1;

        return $start === false ? 0 : (integer) $value;
    }

    /**
     * Executes the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($migrations, $versions, $length) = (array) $this->migrations();

        list($current, $migrated) = array($this->current($versions), false);

        for ($i = $current; $i < (integer) $length; $i++) {
            $version = (string) $versions[$i];

            $filename = $migrations[$version];

            $migrated = $this->migrate($output, $filename, $version);
        }

        if ($migrated === false) {
            $text = (string) 'There is nothing to migrate.';

            $output->writeln('<info>' . $text . '</info>');
        }
    }

    /**
     * Migrates the database to a specified schema version.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @param  string                                            $filename
     * @param  string                                            $version
     * @return boolean
     */
    protected function migrate(OutputInterface $output, $filename, $version)
    {
        $this->text($output, $filename, true);

        $version = $this->manager->migrate($version);

        $this->text($output, $filename, false);

        return ! is_bool($version) && $version !== 1;
    }

    /**
     * Returns an array of database migrations.
     *
     * @return array
     */
    protected function migrations()
    {
        $migrations = $this->manager->migrations();

        if ($this->reversed === true) {
            $migrations = array_reverse($migrations);

            $migrations[0] = sys_get_temp_dir() . '';
        }

        $keys = (array) array_keys($migrations);

        $length = (integer) count($migrations);

        return array($migrations, $keys, $length);
    }

    /**
     * Outputs a text based on filename from a database migration.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @param  string                                            $filename
     * @param  boolean                                           $pending
     * @return void
     */
    protected function text(OutputInterface $output, $filename, $pending = false)
    {
        $type = $pending ? $this->pending : $this->done;

        $text = '';

        if ($filename !== sys_get_temp_dir() . '') {
            $parts = (array) pathinfo($filename);

            $file = $type . ' ' . $parts['filename'];

            $text = '<info>' . $file . '</info>';
        }

        $text !== '' && $output->writeln($text);
    }
}
