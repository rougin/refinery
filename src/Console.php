<?php

namespace Rougin\Refinery;

use Rougin\Blueprint\Blueprint;
use Rougin\Blueprint\Container;
use Rougin\Refinery\Packages\DescribePackage;
use Rougin\Refinery\Packages\RefineryPackage;
use Rougin\Refinery\Packages\SparkplugPackage;
use Symfony\Component\Yaml\Yaml;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Console extends Blueprint
{
    /**
     * @var string
     */
    protected $name = 'Refinery';

    /**
     * @var string
     */
    protected $file = 'refinery.yml';

    /**
     * @var string
     */
    protected $root;

    /**
     * @var string
     */
    protected $version = '0.4.0';

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $namespace = __NAMESPACE__ . '\Commands';

        $this->setCommandNamespace($namespace);

        $this->setCommandPath(__DIR__ . '/Commands');

        $this->root = $root;

        $this->setPackages();
    }

    /**
     * @return string
     */
    public function getAppPath()
    {
        /** @var string */
        $path = realpath($this->root);

        if (! file_exists($path . '/' . $this->file))
        {
            return $path;
        }

        $parsed = $this->getParsed();

        if (array_key_exists('app_path', $parsed))
        {
            /** @var string */
            $path = $parsed['app_path'];
        }

        /** @var string */
        return realpath($path);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getParsed()
    {
        /** @var string */
        $path = realpath($this->root);

        // TODO: Add unit test in this condition ----
        // @codeCoverageIgnoreStart
        if (! file_exists($path . '/' . $this->file))
        {
            return array();
        }
        // @codeCoverageIgnoreEnd
        // ------------------------------------------

        $file = $path . '/' . $this->file;

        /** @var string */
        $file = file_get_contents($file);

        // Replace the constant with root path ----
        $search = '%%CURRENT_DIRECTORY%%';

        $file = str_replace($search, $path, $file);
        // ----------------------------------------

        /** @var array<string, mixed> */
        return Yaml::parse($file);
    }

    /**
     * @return void
     */
    protected function setPackages()
    {
        $container = new Container;

        $path = $this->getAppPath();

        $sparkPlug = new SparkplugPackage($path);
        $container->addPackage($sparkPlug);

        $describe = new DescribePackage;
        $container->addPackage($describe);

        $refinery = new RefineryPackage($path);
        $container->addPackage($refinery);

        $this->setContainer($container);
    }
}
