<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/class.duparchive.state.create.php');

if (!class_exists('DupArchiveSimpleCreateState')) {
class DupArchiveSimpleCreateState extends DupArchiveCreateState
{
    function __construct()
    {
        $this->currentDirectoryIndex = 0;
        $this->currentFileIndex = 0;
        $this->currentFileOffset = 0;
    }

    public function save()
    {

    }
}
}