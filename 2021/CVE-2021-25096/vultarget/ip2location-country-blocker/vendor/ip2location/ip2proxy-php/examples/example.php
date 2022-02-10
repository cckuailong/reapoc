<?php

require 'vendor/autoload.php';

// Lookup by local BIN database
$db = new \IP2Proxy\Database('./data/PX10.SAMPLE.BIN', \IP2PROXY\Database::FILE_IO);

echo 'Module Version: ' . $db->getModuleVersion() . PHP_EOL. PHP_EOL;
echo 'Package: PX'. $db->getPackageVersion() . PHP_EOL;
echo 'Database Date: ' . $db->getDatabaseVersion() . PHP_EOL;
echo '$records = $db->lookup(\'1.0.0.8\', \IP2PROXY\Database::ALL);' . PHP_EOL;
$records = $db->lookup('1.0.0.8', \IP2PROXY\Database::ALL);
print_r($records);

echo PHP_EOL . PHP_EOL;

echo 'Web Service' . PHP_EOL;

// Lookup by Web API
$ws = new \IP2Proxy\WebService('demo', 'PX10', false);

$results = $ws->lookup('1.0.0.8');
print_r($results);
