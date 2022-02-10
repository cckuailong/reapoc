<?php
/*
|| --------------------------------------------------------------------------------------------
|| Custom Metaboxes Fields Example
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Custom Options
|| @since		Dilaz Metabox 1.1
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
|| 
|| NOTE 1: Rename this file from "custom-options-sample.php" to "custom-options.php". If you
||         don't rename it, all your options and settings will be overwritten
||         when updating Dilaz Metabox.
|| 
|| NOTE 2: Add all your theme/plugin custom options in this file
|| 
*/

defined('ABSPATH') || exit;


/**
 * Add custom metaboxes into dilaz metaboxes
 *
 * @param array	 $dilaz_metaboxes all registered dilaz metaboxes
 * @param string $prefix          metabox prefix
 * @param array  $parameters      metabox parameters
 *
 * @return array
 */
add_filter('metabox_option_filter_'. $prefix, function($dilaz_meta_boxes, $prefix, $parameters) {
	
	# BOX - Test Beta
	# =============================================================================================
	$dilaz_meta_boxes[] = array(
		'id'	   =>  $prefix .'custom_options_imp',
		'title'	   => __('Custom Options Implementation', 'dilaz-metabox'),
		'pages'    => array('post', 'page'),
		'context'  => 'normal',
		'priority' => 'low',
		'type'     => 'metabox_set'
	);
	
		# TAB - Beta Tab 1
		# *****************************************************************************************
		$dilaz_meta_boxes[] = array(
			'id'    =>  $prefix .'custom_options',
			'title' => __('Custom Options', 'dilaz-metabox'),
			'icon'  => 'fa-bell-o',
			'type'  => 'metabox_tab'
		);
			
			# FIELDS - Beta Tab 1
			# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$dilaz_meta_boxes[] = array(
				'id'	  =>  $prefix .'custom_1',
				'name'	  => __('Custom One:', 'dilaz-metabox'),
				'desc'	  => '',
				'type'	  => 'select',
				'options' => array('1','2','3'),
				'std'	  => 'default'
			);
			$dilaz_meta_boxes[] = array(
				'id'	  =>  $prefix .'custom_2',
				'name'	  => __('Custom Two:', 'dilaz-metabox'),
				'desc'	  => '',
				'type'	  => 'radio',
				'options' => DilazMetaboxFunction::choice('yes_no'),
				'std'     => 'no'
			);
			$dilaz_meta_boxes[] = array(
				'id'	  =>  $prefix .'custom_3',
				'name'	  => __('Custom Three:', 'dilaz-metabox'),
				'desc'	  => '',
				'type'	  => 'radio',
				'options' => DilazMetaboxFunction::choice('yes_no'),
				'std'     => 'no'
			);
	
	return $dilaz_meta_boxes;
	
}, 10, 3);


/**
 * Insert metabox field before a specific field
 *
 * @param array	 $dilaz_metaboxes all registered dilaz metaboxes
 * @param string $prefix          metabox prefix
 * @param array  $parameters      metabox parameters
 *
 * @return array
 */
add_filter('metabox_option_filter_'. $prefix, function($dilaz_meta_boxes, $prefix, $parameters) {
	
	# array data to be inserted
	$insert_custom_data = [];
	
	$insert_custom_data[] = array(
		'id'	  =>  $prefix .'custom_2_b',
		'name'	  => __('INSERTED - Custom Two B:', 'dilaz-metabox'),
		'desc'	  => __('Custom Two B inserted before Custom Two C.', 'dilaz-metabox'),
		'type'	  => 'radio',
		'options' => DilazMetaboxFunction::choice('def_yes_no'),
		'std'     => 'yes'
	);
	
	$insert_custom_data[] = array(
		'id'	  =>  $prefix .'custom_2_c',
		'name'	  => __('INSERTED - Custom Two C:', 'dilaz-metabox'),
		'desc'	  => __('Custom Two C inserted before Custom Three.', 'dilaz-metabox'),
		'type'	  => 'radio',
		'options' => DilazMetaboxFunction::choice('def_yes_no'),
		'std'     => 'yes'
	);
	
	$insert = DilazMetaboxFunction::insert_field($dilaz_meta_boxes,  $prefix .'custom_options_imp',  $prefix .'custom_3', $insert_custom_data, 'before');
	
	return ($insert != false) ? array_merge($dilaz_meta_boxes, $insert) : $dilaz_meta_boxes;
	
}, 10, 3);