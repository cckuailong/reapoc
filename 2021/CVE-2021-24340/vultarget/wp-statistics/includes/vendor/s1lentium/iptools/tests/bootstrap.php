<?php

if (!class_exists('\PHPUnit_Framework_TestCase') && class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}

require __DIR__ . '/../src/PropertyTrait.php';
require __DIR__ . '/../src/IP.php';
require __DIR__ . '/../src/Network.php';
require __DIR__ . '/../src/Range.php';
