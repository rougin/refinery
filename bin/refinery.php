<?php require realpath('vendor') . '/autoload.php';

use Rougin\Refinery\CreateMigrationCommand;
use Rougin\Refinery\MigrateCommand;
use Rougin\Refinery\MigrateResetCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Load the CodeIgniter instance
 */

$instance = new Rougin\SparkPlug\Instance();
$codeigniter = $instance->get();

$application = new Application('Refinery', '0.2.1');

$application->add(new MigrateCommand($codeigniter));
$application->add(new MigrateResetCommand($codeigniter));
$application->add(new CreateMigrationCommand());
$application->run();