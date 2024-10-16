<?php

namespace Rougin\Refinery\Commands;

use Rougin\Blueprint\Command;
use Rougin\Classidy\Generator;
use Rougin\Refinery\Refinery;
use Rougin\Refinery\Template\Migration;

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
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        $this->addArgument('name', 'Name of the migration file');
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        /** @var string */
        $name = $this->getArgument('name');

        $style = $this->getNumberStyle();

        $class = new Migration($name);

        $maker = new Generator;

        $result = $maker->make($class->init());

        $this->createFile($name, $result);

        return self::RETURN_SUCCESS;
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    protected function createFile($name, $class)
    {
        $path = $this->path . '/migrations/';

        $file = $path . date('YmdHis') . '_' . $name;

        file_put_contents($file . '.php', $class);
    }

    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    protected function getConfig($name, $default = null)
    {
        $file = $this->path . '/config/migration.php';

        /** @var string */
        $config = file_get_contents($file);

        $pattern = '/\$config\[\\\'' . $name . '\\\'\] = (.*?);/i';

        preg_match($pattern, $config, $matches);

        return $matches ? $matches[1] : $default;
    }

    /**
     * @return integer
     */
    protected function getNumberStyle()
    {
        $type = $this->getConfig('migration_type');

        $style = Migration::TYPE_SEQUENCE;

        if ($type === '\'timestamp\'')
        {
            $style = Migration::TYPE_TIMESTAMP;
        }

        return $style;
    }
}
