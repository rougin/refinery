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
        $this->addOptionalArgument('version', 'Version number of the migration file');
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
        $latest = $this->manager->getLatestVersion();

        $files = $this->manager->getMigrations();

        $toMigrate = $this->type === self::TYPE_MIGRATE;

        if (! $toMigrate)
        {
            $last = $this->manager->getLastMigration($latest);

            $files = $latest === '0' ? array() : array($last);
        }

        $current = null;

        foreach ($files as $item)
        {
            $before = 'Rolling back';

            $after = 'rolled back';

            $version = $item['version'];

            $file = $item['file'];

            if ($toMigrate)
            {
                $before = 'Migrating';

                $after = 'migrated';

                if (strtotime($version) <= strtotime($latest))
                {
                    continue;
                }
            }

            $name = substr(basename($file), 15, -4);

            $this->showText($before . ' "' . $name . '"...');

            $this->manager->migrate($version);

            $this->showPass('"' . $name . '" ' . $after . '!');

            $current = $version;
        }

        if ($current !== null)
        {
            $this->manager->saveLatest($current);
        }
        else
        {
            $text = $toMigrate ? 'migrate' : 'roll back';

            $this->showPass('Nothing to ' . $text . '.');
        }

        return self::RETURN_SUCCESS;
    }
}
