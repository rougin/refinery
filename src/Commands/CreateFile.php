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
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver = null;

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
     * @param \Rougin\Refinery\Refinery $refinery
     */
    public function __construct(Refinery $refinery)
    {
        $this->driver = $refinery->getDriver();

        $this->path = $refinery->getAppPath();
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->driver !== null;
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
