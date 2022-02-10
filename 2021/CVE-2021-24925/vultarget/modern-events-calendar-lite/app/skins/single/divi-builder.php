<?php
/** no direct access **/
defined('MECEXEC') or die();

if(class_exists('\MEC_DIVI_Single_Builder\Core\Controller\Admin'))
{
    \MEC_DIVI_Single_Builder\Core\Controller\Admin::load_the_builder($event);

    do_action('mec_esdb_content', $event);
    do_action('mec_schema', $event);
}
