<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
if(!class_exists('DupArchiveLoggerBase')) {
abstract class DupArchiveLoggerBase
{
    abstract public function log($s, $flush = false, $callingFunctionOverride = null);
}
}
