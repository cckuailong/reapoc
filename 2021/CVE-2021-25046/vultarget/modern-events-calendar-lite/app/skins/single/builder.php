<?php
/** no direct access **/
defined('MECEXEC') or die();

if(did_action('elementor/loaded'))
{
    if(Elementor\Plugin::$instance->editor->is_edit_mode() || Elementor\Plugin::$instance->preview->is_preview_mode()) the_content();

    do_action('mec_esb_content', $event);
    do_action('mec_schema', $event);
}