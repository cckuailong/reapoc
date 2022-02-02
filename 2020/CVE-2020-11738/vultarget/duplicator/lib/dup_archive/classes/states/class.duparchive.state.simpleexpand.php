<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/class.duparchive.state.expand.php');

if (!class_exists('DupArchiveSimpleExpandState')) {
class DupArchiveSimpleExpandState extends DupArchiveExpandState
{
    function __construct()
    {        
    }

    public function save()
    {

    }
}
}