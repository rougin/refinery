<?php

namespace Rougin\Refinery\Commands;

use Rougin\Refinery\Migrator;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Migrate extends Migrator
{
    /**
     * @var string
     */
    protected $name = 'migrate';

    /**
     * @var string
     */
    protected $description = 'Migrate to the latest schema version';

    /**
     * @var integer
     */
    protected $type = self::TYPE_MIGRATE;
}
