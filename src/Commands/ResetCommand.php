<?php

namespace Rougin\Refinery\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Reset Command
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
        $this->setName('reset')->setDescription('Resets all migrations');
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
        $current = $this->getLatestVersion();

        if ($current <= 0) {
            return $output->writeln('<info>Database\'s version is now in 0.</info>');
        }

        $this->toggleMigration(true);
        $this->changeVersion($current, 0);

        $this->codeigniter->load->library('migration');
        $this->codeigniter->migration->current();

        $this->toggleMigration();

        return $output->writeln('<info>Database has been resetted.</info>');
    }
}
