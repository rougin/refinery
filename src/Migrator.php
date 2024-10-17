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
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        $files = $this->manager->getMigrations();

        $latest = $this->manager->getLatestVersion();

        if ($files && $this->type === self::TYPE_ROLLBACK)
        {
            $last = $this->manager->getLastMigration($latest);

            $files = array($last);
        }

        $current = null;

        foreach ($files as $item)
        {
            $before = 'Rolling back';

            $after = 'rolled back';

            $version = $item['version'];

            $file = $item['file'];

            if ($this->type === self::TYPE_MIGRATE)
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

        if ($current)
        {
            $this->manager->saveLatest($current);
        }
        else
        {
            $this->showPass('Nothing to migrate.');
        }

        return self::RETURN_SUCCESS;
    }
}
