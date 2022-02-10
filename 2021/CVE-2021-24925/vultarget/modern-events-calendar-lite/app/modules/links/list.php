<?php
/** no direct access **/
defined('MECEXEC') or die();

// MEC Settings
$settings = $this->get_settings();

// Social on single page is disabled
if(!isset($settings['social_network_status']) or (isset($settings['social_network_status']) and !$settings['social_network_status'])) return;

$url = isset($event->data->permalink) ? $event->data->permalink : '';
if(trim($url) == '') return;

// Get social networks
$socials = $this->get_social_networks();

foreach($socials as $social)
{
    if(!isset($settings['sn'][$social['id']]) or (isset($settings['sn'][$social['id']]) and !$settings['sn'][$social['id']])) continue;
    if(is_callable($social['function'])) echo call_user_func($social['function'], $url, $event);
}