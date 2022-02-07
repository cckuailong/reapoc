<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access




add_shortcode('post_grid_sticky_posts','post_grid_sticky_posts');

function post_grid_sticky_posts(){
	
	$sticky_posts = get_option( 'sticky_posts' );
	$sticky_posts = implode(',',$sticky_posts);
	
	return $sticky_posts;
	}