<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access





add_shortcode('post_grid_today_date','post_grid_today_date');

function post_grid_today_date(){
	
	return date('Y-m-d');
	
	}