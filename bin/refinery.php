<?php require realpath('vendor') . '/autoload.php';

use Rougin\Describe\Describe;
use Rougin\Refinery\CreateMigrationCommand;
use Rougin\Refinery\MigrateCommand;
use Rougin\Refinery\MigrateResetCommand;
use Rougin\SparkPlug\Instance;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Load the CodeIgniter instance
 */

$instance = new Instance();
$codeigniter = $instance->get();

/**
 * Load Describe
 */

require APPPATH . 'config/database.php';

$db['default']['driver'] = $db['default']['dbdriver'];
unset($db['default']['dbdriver']);

$describe = new Describe($db['default']);

/**
 * Get the migrations.php from CodeIgniter
 */

$migration = array();
$migration['path'] = APPPATH . '/config/migration.php';
$migration['file'] = file_get_contents($migration['path']);

$application = new Application('Refinery', '0.2.1');

$application->add(new MigrateCommand($codeigniter, $migration));
$application->add(new MigrateResetCommand($codeigniter));
$application->add(new CreateMigrationCommand($describe, $migration));
$application->run();