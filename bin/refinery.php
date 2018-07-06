<?php

use Rougin\Describe\Driver\CodeIgniterDriver;
use Rougin\Refinery\Console\Application;

$source = '/rougin/refinery/bin';

$slash = DIRECTORY_SEPARATOR;

$local = str_replace('bin', 'vendor', __DIR__);

$vendor = str_replace('/', DIRECTORY_SEPARATOR, $source);

$vendor = str_replace($vendor, '', __DIR__);

$testing = (string) $local . '/autoload.php';

$autoload = $vendor . '/autoload.php';

$autoload = file_exists($testing) ? $testing : $autoload;

$vendor = str_replace('/autoload.php', '', $autoload);

require (string) $autoload;

$system = str_replace('vendor', 'system', $vendor);

$source = (string) $vendor . '/rougin/codeigniter/src';

$builder = new Rougin\Refinery\Builder;

define('BASEPATH', is_dir($system) ? $system : $source);

define('ENVIRONMENT', 'development');

$basepath = str_replace('vendor', '', (string) $vendor);

$directory = new RecursiveDirectoryIterator($basepath);

$iterator = new RecursiveIteratorIterator($directory);

$regex = new RegexIterator($iterator, '/database.php$/i');

$database = array_keys(iterator_to_array($regex));

$search = $slash . 'config' . $slash . 'database.php';

$app = (string) str_replace($search, '', $database[0]);

$manager = new Rougin\Refinery\Manager($app);

require (string) $database[0];

$migrations = (string) $app . $slash . 'migrations';

is_dir($migrations) === false && mkdir($migrations);

$driver = new CodeIgniterDriver($db[$active_group]);

$describe = new Rougin\Describe\Describe($driver);

$app = new Application($builder, $describe, $manager);

$app->run();
