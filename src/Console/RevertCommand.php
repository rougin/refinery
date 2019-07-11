<?php

namespace Rougin\Refinery\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Revert Command
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class RevertCommand extends ChangeCommand
{
    /**
     * @var string
     */
    protected $done = 'Rolled back: ';

    /**
     * @var string
     */
    protected $pending = 'Rolling back:';

    /**
     * @var boolean
     */
    protected $reversed = true;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('rollback')->setDescription('Returns to a previous or specified version');

        $this->addArgument('version', InputArgument::OPTIONAL, 'Version number of the migration');
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
        list($current, $version) = $this->version($input);

        if ($current !== '0' && $current !== '000') {
            $result = $this->rollback($current, (string) $version);

            return $this->migrate($output, $result[0], $result[1]);
        }

        $text = (string) 'There is nothing to migrate.';

        $output->writeln('<info>' . $text . '</info>');
    }

    /**
     * Performs a rollback to a specified migration.
     *
     * @param  string $current
     * @param  string $version
     * @return string|boolean
     */
    protected function rollback($current, $version)
    {
        list($migrations, $versions) = $this->migrations();

        $index = array_search($version, (array) $versions);

        $file = $version === $current ? $current : $version;

        $filename = (string) basename($migrations[$file]);

        $index = $version === $current ? $index + 1 : $index + 0;

        return array($filename, $versions[(integer) $index]);
    }

    /**
     * Returns the current and user-specified versions.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @return array
     */
    protected function version(InputInterface $input)
    {
        $version = $input->getArgument('version');

        $current = $this->manager->current();

        is_null($version) && $version = $current;

        return array($current, $version);
    }
}
