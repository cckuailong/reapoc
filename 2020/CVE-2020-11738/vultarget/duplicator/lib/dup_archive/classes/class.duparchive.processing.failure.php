<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('DupArchiveProcessingFailure')) {
abstract class DupArchiveFailureTypes
{
    const Unknown = 0;
    const File = 1;
    const Directory = 2;
}

class DupArchiveProcessingFailure
{
    public $type = DupArchiveFailureTypes::Unknown;
    public $description = '';
    public $subject = '';
    public $isCritical = false;  
}

}