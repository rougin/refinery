<?php

namespace Rougin\Refinery\Commands;

use Rougin\Refinery\Migrator;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Revert extends Migrator
{
    /**
     * @var string
     */
    protected $name = 'rollback';

    /**
     * @var string
     */
    protected $description = 'Rollback to the previous schema version';

    /**
     * @var integer
     */
    protected $type = self::TYPE_ROLLBACK;
}
