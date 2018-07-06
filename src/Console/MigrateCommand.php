<?php

namespace Rougin\Refinery\Console;

/**
 * Migrate Command
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MigrateCommand extends ChangeCommand
{
    /**
     * @var string
     */
    protected $done = 'Migrated: ';

    /**
     * @var string
     */
    protected $pending = 'Migrating:';

    /**
     * @var boolean
     */
    protected $reversed = false;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('migrate')->setDescription('Migrates to the latest schema version');
    }
}
