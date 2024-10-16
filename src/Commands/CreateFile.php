<?php

namespace Rougin\Refinery\Commands;

use Rougin\Blueprint\Command;
use Rougin\Refinery\Refinery;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateFile extends Command
{
    /**
     * @var string
     */
    protected $name = 'create';

    /**
     * @var string
     */
    protected $description = 'Create a new database migration';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @param \Rougin\Refinery\Refinery $combustor
     */
    public function __construct(Refinery $combustor)
    {
        $this->path = $combustor->getAppPath();
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        return self::RETURN_SUCCESS;
    }
}
