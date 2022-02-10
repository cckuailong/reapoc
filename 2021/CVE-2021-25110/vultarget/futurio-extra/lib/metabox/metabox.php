<?php
/*
|| --------------------------------------------------------------------------------------------
|| Metabox
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Metabox
|| @version		2.5.3
|| @since		Dilaz Metabox 1.0
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @License		GPL-2.0+
|| @License URI	http://www.gnu.org/licenses/gpl-2.0.txt
|| 
|| NOTE: These metaboxes require Dilaz metabox plugin installed. 
|| 
*/

defined('ABSPATH') || exit;

# Load config
file_exists(dirname(__FILE__) .'/config.php') ? require_once dirname(__FILE__) .'/config.php' : require_once dirname(__FILE__) .'/config-sample.php';

# Load metabox options
require_once dirname(__FILE__) .'/includes/load.php';