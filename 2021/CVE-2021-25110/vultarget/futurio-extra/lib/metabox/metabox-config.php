<?php
/*
|| --------------------------------------------------------------------------------------------
|| Metabox
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Metabox
|| @version		2.3
|| @since		Dilaz Metabox 1.0
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @License		GPL-2.0+
|| @License URI	http://www.gnu.org/licenses/gpl-2.0.txt
|| 
*/

defined('ABSPATH') || exit;

# Metabox parameters
$parameters = array(
	  'prefix'          => 'futurio_meta', # must be unique. Any time its changed, saved settings are no longer used. New settings will be saved. Set this once.
		'use_type'        => 'plugin', # 'theme' if used within a theme or 'plugin' if used within a plugin
		'use_type_error'  => false, # error when wrong "use_type" is declared, default is false
		'default_options' => false, # whether to load default options
		'custom_options'  => false, # whether to load custom options
);

# Load metabox options
require_once dirname(__FILE__) .'/includes/load.php';