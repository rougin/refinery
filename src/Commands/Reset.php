<?php

namespace Rougin\Refinery\Commands;

use Rougin\Blueprint\Command;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Reset extends Command
{
    /**
     * @var string
     */
    protected $name = 'reset';

    /**
     * @var string
     */
    protected $description = 'Reset the entire database schema version back to 0';

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        $input = array('--target' => '0');

        $this->runCommand('rollback', $input);

        return self::RETURN_SUCCESS;
    }
}
