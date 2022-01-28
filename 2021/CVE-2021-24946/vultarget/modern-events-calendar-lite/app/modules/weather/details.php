<?php
/** no direct access **/
defined('MECEXEC') or die();

// PRO Version is required
if(!$this->getPRO()) return;

// MEC Settings
$settings = $this->get_settings();

// The module is disabled
if(!isset($settings['weather_module_status']) or (isset($settings['weather_module_status']) and !$settings['weather_module_status'])) return;

$darksky = (isset($settings['weather_module_api_key']) and trim($settings['weather_module_api_key'])) ? $settings['weather_module_api_key'] : '';
$weatherapi = (isset($settings['weather_module_wa_api_key']) and trim($settings['weather_module_wa_api_key'])) ? $settings['weather_module_wa_api_key'] : '';

// No API key
if(!trim($darksky) and !trim($weatherapi)) return;

// Location ID
$location_id = $this->get_master_location_id($event);

// Location is not Set
if(!$location_id) return;

// Location
$location = ($location_id ? $this->get_location_data($location_id) : array());

$lat = isset($location['latitude']) ? $location['latitude'] : 0;
$lng = isset($location['longitude']) ? $location['longitude'] : 0;

// Cannot find the geo point
if(!$lat or !$lng) return;

if(trim($weatherapi)) include MEC::import('app.modules.weather.weatherapi', true, true);
elseif(trim($darksky)) include MEC::import('app.modules.weather.darksky', true, true);
