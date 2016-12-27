<?php

require 'vendor/autoload.php';

$directory = __DIR__ . '/../build'; // TEST DIRECTORY

Rougin\Refinery\Refinery::boot('refinery.yml', null, $directory)->run();
