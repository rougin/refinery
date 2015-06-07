<?php require realpath('vendor') . '/autoload.php';

use Rougin\Refinery\CreateMigrationCommand;
use Rougin\Refinery\MigrateCommand;
use Rougin\Refinery\MigrateResetCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$codeigniter = require realpath('vendor') . '/rougin/refinery/src/CodeIgniterInstance.php';
$application = new Application('Refinery', '0.1.0');

$application->add(new MigrateCommand($codeigniter));
$application->add(new MigrateResetCommand($codeigniter));
$application->add(new CreateMigrationCommand());
$application->run();