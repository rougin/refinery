<?php

namespace Rougin\Refinery\Console;

/**
 * Reset Command
 *
 * @package Refinery
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class ResetCommand extends ChangeCommand
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
        $this->setName('reset')->setDescription('Resets the database schema version to 0');
    }
}
