<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputInterface;
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
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('migrate')->setDescription('Migrates the database');
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
        $latest  = $migrations[count($migrations) - 1];

        $this->migrate($current, $latest);

        $messages = $this->getMessages($migrations, $filenames, $current, $latest);

        foreach ($messages as $message) {
            $output->writeln('<info>' . $message . '</info>');
        }
    }

    /**
     * Generates messages for successful migrations.
     *
     * @param  array  $migrations
     * @param  string $current
     * @param  string $latest
     * @return boolean
     */
    protected function getMessages(array $migrations, array $filenames, $current, $latest)
    {
        $messages = [];

        ($current != $latest) || array_push($messages, 'Database is up to date.');

        $count = count($migrations);

        for ($counter = 0; $counter < $count; $counter++) {
            if ($current <= $migrations[$counter]) {
                $message = $filenames[$counter] . ' has been migrated to the database.';

                array_push($messages, $message);
            }
        }

        return $messages;
    }
}
