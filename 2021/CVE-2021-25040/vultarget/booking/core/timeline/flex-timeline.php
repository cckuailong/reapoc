<?php
/**
 * @package  Load Flex Timeline files
 * @description  Templates for Timeline
 *
 * Author: wpdevelop, oplugins
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @version 1.0
 * @modified 2019-06-28 11:20
 */


/**
 * Delete Changes
 * If we need to  rollback  all  these changes,  so  then  need,
 *
 *
 * 2.   And search  for this '//FixIn: Flex TimeLine 1.0'
 *
 * 3.   Comment in wp-config.php this line
                                            if ( ! defined( 'SCRIPT_DEBUG' ) ) { define( 'SCRIPT_DEBUG', true ); }
 *      for do not load full src files.
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


require_once( WPBC_PLUGIN_DIR . '/core/timeline/v2/wpbc-class-timeline_v2.php' );          // Version 2.0   of  Timeline
