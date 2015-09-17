<?php

// Define the VENDOR path
$vendor = realpath('vendor');

// Include the Composer Autoloader
require $vendor . '/autoload.php';

$filePath = realpath(__DIR__ . '/../refinery.yml');
$directory = str_replace('/refinery.yml', '', $filePath);

define('BLUEPRINT_FILENAME', $filePath);
define('BLUEPRINT_DIRECTORY', $directory);

// Load the CodeIgniter instance
$instance = new Rougin\SparkPlug\Instance();

// Include the Inflector helper from CodeIgniter
require BASEPATH . 'helpers/inflector_helper.php';

// Load the Blueprint library
$blueprint = include($vendor . '/rougin/blueprint/bin/blueprint.php');

if ($blueprint->hasError) {
    exit($blueprint->showError());
}

$blueprint->console->setName('Refinery');
$blueprint->console->setVersion('0.1.3');

// Run the Refinery console application
$blueprint->console->run();