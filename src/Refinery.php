<?php

namespace Rougin\Refinery;

/**
 * Refinery Console
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Refinery extends \Rougin\Blueprint\Console
{
    /**
     * @var \Rougin\Blueprint\Blueprint
     */
    protected static $application;

    /**
     * @var string
     */
    protected static $name = 'Refinery';

    /**
     * @var string
     */
    protected static $version = '0.3.0';

    /**
     * Prepares the console application.
     *
     * @param  string               $filename
     * @param  \Auryn\Injector|null $injector
     * @param  string|null          $directory
     * @return \Rougin\Blueprint\Blueprint
     */
    public static function boot($filename = 'refinery.yml', \Auryn\Injector $injector = null, $directory = null)
    {
        \Rougin\SparkPlug\Instance::create($directory);

        self::$application = parent::boot($filename, $injector, $directory);

        self::prepareDependencies();

        $templates = self::$application->getTemplatePath();

        self::$application->setTemplatePath($templates, null);
        self::$application->setCommandPath(__DIR__ . DIRECTORY_SEPARATOR . 'Commands');
        self::$application->setCommandNamespace('Rougin\Refinery\Commands');

        return self::$application;
    }

    /**
     * Prepares the dependencies to be used.
     *
     * @return void
     */
    protected static function prepareDependencies()
    {
        $basePath = BASEPATH;

        require APPPATH . 'config/database.php';

        if (is_dir('vendor/rougin/codeigniter/src/')) {
            $basePath = 'vendor/rougin/codeigniter/src/';
        }

        require $basePath . 'helpers/inflector_helper.php';

        $driver = new \Rougin\Describe\Driver\CodeIgniterDriver($db[$active_group]);

        self::$application->injector->share(new \Rougin\Describe\Describe($driver));
    }
}
