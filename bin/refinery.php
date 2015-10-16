<?php

// Include the Composer Autoloader
require realpath('vendor') . '/autoload.php';

// Load the Blueprint library
$refinery = new Rougin\Blueprint\Blueprint(
    new Symfony\Component\Console\Application,
    new Auryn\Injector
);

$refinery
    ->setTemplatePath(__DIR__ . '/../src/Templates')
    ->setCommandPath(__DIR__ . '/../src/Commands')
    ->setCommandNamespace('Rougin\Refinery\Commands');

$refinery->console->setName('Refinery');
$refinery->console->setVersion('0.1.3');

$refinery->injector->delegate('CI_Controller', function () {
    $sparkPlug = new Rougin\SparkPlug\SparkPlug($GLOBALS, $_SERVER);

    return $sparkPlug->getCodeIgniter();
});

$refinery->injector->delegate('Rougin\Describe\Describe', function () use ($db) {
    return new Rougin\Describe\Describe(
        new Rougin\Describe\Driver\CodeIgniterDriver($db)
    );
});

// Run the Refinery console application
$refinery->run();
