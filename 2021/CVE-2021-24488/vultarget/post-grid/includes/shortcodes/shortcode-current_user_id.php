<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access





add_shortcode('post_grid_current_user_id','post_grid_current_user_id');

function post_grid_current_user_id(){
	
	return get_current_user_id();
	
	}