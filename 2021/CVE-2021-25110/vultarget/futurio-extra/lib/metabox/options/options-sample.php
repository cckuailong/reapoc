<?php
/*
|| --------------------------------------------------------------------------------------------
|| Theme/Plugin Metaboxes Fields
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Main Options
|| @since		Dilaz Metabox 1.1
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
|| 
|| NOTE 1: Rename this file from "options-sample.php" to "options.php". If you
||         don't rename it, all your options and settings will be overwritten
||         when updating Dilaz Metabox.
|| 
|| NOTE 2: Add all your theme/plugin metabox options in this file
|| 
*/

defined('ABSPATH') || exit;


# BOX - Sample Set 1
# =============================================================================================
$dilaz_meta_boxes[] = array(
	'id'	   => $prefix .'samp_set_1',
	'title'	   => __('Sample Set 1', 'dilaz-metabox'),
	'pages'    => array('post', 'page'),
	'context'  => 'normal',
	'priority' => 'high',
	'type'     => 'metabox_set'
);
	
	# TAB - Sample 1 Tab 1
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'samp_1_tab_1',
		'title' => __('Sample 1 - Tab 1', 'dilaz-metabox'),
		'icon'  => 'fa-bank',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Sample 1 Tab 1
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'samp_1_tab_1_opt_1',
			'name'	  => __('Tab 1 - Option 1:', 'dilaz-metabox'),
			'desc'	  => '',
			'type'	  => 'radio',
			'options' => DilazMetaboxFunction::choice('yes_no'),
			'std'     => 'no'
		);
		
		
	# TAB - Sample 1 Tab 2
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'samp_1_tab_2',
		'title' => __('Sample 1 - Tab 2', 'dilaz-metabox'),
		'icon'  => 'fa-automobile',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Sample 1 Tab 2
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'samp_1_tab_1_opt_2',
			'name'	  => __('Tab 2 - Option 1:', 'dilaz-metabox'),
			'desc'	  => '',
			'type'	  => 'radio',
			'options' => DilazMetaboxFunction::choice('yes_no'),
			'std'     => 'no'
		);
		
		
		
		
# BOX - Sample Set 2
# =============================================================================================
$dilaz_meta_boxes[] = array(
	'id'	   => $prefix .'samp_set_2',
	'title'	   => __('Sample Set 2', 'dilaz-metabox'),
	'pages'    => array('post', 'page'),
	'context'  => 'normal',
	'priority' => 'high',
	'type'     => 'metabox_set'
);
	
	# TAB - Sample 2 Tab 1
	# *****************************************************************************************
	$dilaz_meta_boxes[] = array(
		'id'    => $prefix .'samp_2_tab_1',
		'title' => __('Sample 2 - Tab 1', 'dilaz-metabox'),
		'icon'  => 'fa-bank',
		'type'  => 'metabox_tab'
	);
		
		# FIELDS - Sample 2 Tab 1
		# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$dilaz_meta_boxes[] = array(
			'id'	  => $prefix .'samp_2_tab_1_opt_1',
			'name'	  => __('Tab 1 - Option 1:', 'dilaz-metabox'),
			'desc'	  => '',
			'type'	  => 'radio',
			'options' => DilazMetaboxFunction::choice('yes_no'),
			'std'     => 'no'
		);
		
		
		
return $dilaz_meta_boxes;