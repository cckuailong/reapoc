<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/**
 * Methods used to work with WordPress MU sites
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 *
 * @todo Refactor out IO methods into class.io.php file
 */

// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

class DUP_MU
{
    public static function isMultisite()
    {
        return self::getMode() > 0;
    }

    // 0 = single site; 1 = multisite subdomain; 2 = multisite subdirectory
    public static function getMode()
    {
		if(is_multisite()) {
            if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return 0;
        }
    }
}