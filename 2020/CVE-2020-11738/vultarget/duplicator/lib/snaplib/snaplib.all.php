<?php
/**
 * include all snap lib
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package snaplib
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!defined('DUPLITE_SNAPLIB_INCLUDE_ALL')) {
    define('DUPLITE_SNAPLIB_INCLUDE_ALL', true);

    $dir = dirname(__FILE__);

    require_once($dir.'/class.snaplib.exceptions.php');
    require_once($dir.'/class.snaplib.logger.php');
    require_once($dir.'/class.snaplib.u.util.php');
    require_once($dir.'/class.snaplib.u.io.php');
    require_once($dir.'/class.snaplib.u.json.php');
    require_once($dir.'/class.snaplib.jsonSerializable.abstract.php');
    require_once($dir.'/class.snaplib.u.net.php');
    require_once($dir.'/class.snaplib.u.os.php');
    require_once($dir.'/class.snaplib.u.stream.php');
    require_once($dir.'/class.snaplib.u.string.php');
    require_once($dir.'/class.snaplib.u.ui.php');
    require_once($dir.'/class.snaplib.u.url.php');
    require_once($dir.'/class.snaplib.u.wp.php');
}