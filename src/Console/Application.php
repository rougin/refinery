<?php

namespace Rougin\Refinery\Console;

use Rougin\Describe\Describe;
use Rougin\Refinery\Builder;
use Rougin\Refinery\Manager;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Console Application
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Application extends BaseApplication
{
    const VERSION = '0.4.0';

    /**
     * Initializes the application instance.
     *
     * @param \Rougin\Refinery\Builder  $builder
     * @param \Rougin\Describe\Describe $describe
     * @param \Rougin\Refinery\Manager  $manager
     */
    public function __construct(Builder $builder, Describe $describe, Manager $manager)
    {
        parent::__construct('Refinery', self::VERSION);

        $this->add(new CreateCommand($builder, $describe, $manager));

        $this->add(new RevertCommand($manager));

        $this->add(new ResetCommand($manager));

        $this->add(new MigrateCommand($manager));
    }
}
