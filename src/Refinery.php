<?php

namespace Rougin\Refinery;

use Rougin\Describe\Driver\DriverInterface;
use Rougin\SparkPlug\Controller;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Refinery
{
    /**
     * @var \Rougin\SparkPlug\Controller|null
     */
    protected $app = null;

    /**
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver = null;

    /**
     * @var \Rougin\Refinery\Manager|null
     */
    protected $manager = null;

    /**
     * @var string
     */
    protected $root;

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * @return string
     */
    public function getAppPath()
    {
        $app = $this->root . '/application';

        $root = $this->getRootPath();

        return is_dir($app) ? $app : $root;
    }

    /**
     * @return \Rougin\Describe\Driver\DriverInterface|null
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return \Rougin\Refinery\Manager|null
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        return $this->root;
    }

    /**
     * @param \Rougin\SparkPlug\Controller $app
     *
     * @return self
     */
    public function setApp(Controller $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * @param \Rougin\Describe\Driver\DriverInterface $driver
     *
     * @return self
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param \Rougin\Refinery\Manager $manager
     *
     * @return self
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;

        return $this;
    }
}
