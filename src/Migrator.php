<?php

namespace Rougin\Refinery;

use Rougin\Blueprint\Command;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Migrator extends Command
{
    const TYPE_MIGRATE = 0;

    const TYPE_ROLLBACK = 1;

    /**
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver = null;

    /**
     * @var \Rougin\Refinery\Manager
     */
    protected $manager;

    /**
     * @var integer
     */
    protected $type = self::TYPE_MIGRATE;

    /**
     * @param \Rougin\Refinery\Refinery $refinery
     */
    public function __construct(Refinery $refinery)
    {
        /** @var \Rougin\Refinery\Manager */
        $manager = $refinery->getManager();

        $this->manager = $manager;

        $this->driver = $refinery->getDriver();
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        $this->addRequiredOption('target', 'The version number to migrate to', null, '-t');
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
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
        $migrate = $this->type === self::TYPE_MIGRATE;

        $rollback = $this->type === self::TYPE_ROLLBACK;

        /** @var string|null */
        $target = $this->getOption('target');

        if ($target === null)
        {
            $target = $this->manager->getLastVersion();

            if ($migrate)
            {
                $target = $this->manager->getLatestVersion();
            }
        }

        $current = $this->manager->getCurrentVersion();

        $items = $this->manager->getMigrations($rollback);

        if ($current === $target)
        {
            $items = array();
        }

        $latest = null;

        foreach ($items as $item)
        {
            $before = $migrate ? 'Migrating' : 'Rolling back';

            $after = $migrate ? 'migrated' : 'rolled back';

            $version = $item['version'];

            $file = $item['file'];

            if ($migrate)
            {
                if (strtotime($version) <= strtotime($current))
                {
                    continue;
                }

                if (strtotime($version) > strtotime($target))
                {
                    continue;
                }
            }

            if ($rollback)
            {
                if (strtotime($version) >= strtotime($current))
                {
                    continue;
                }

                if (strtotime($version) < strtotime($target))
                {
                    continue;
                }
            }

            $name = substr(basename($file), 15, -4);

            $this->showText($before . ' "' . $name . '"...');

            $this->manager->migrate($version);

            $this->showPass('"' . $name . '" ' . $after . '!');

            $latest = $version;
        }

        if ($latest !== null)
        {
            $this->manager->saveLatest($latest);
        }
        else
        {
            $text = $migrate ? 'migrate' : 'roll back';

            $this->showPass('Nothing to ' . $text . '.');
        }

        return self::RETURN_SUCCESS;
    }
}
