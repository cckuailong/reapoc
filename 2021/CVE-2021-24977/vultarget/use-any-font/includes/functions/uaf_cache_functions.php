<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function uaf_clear_plugins_cache(){
  if (function_exists('sg_cachepress_purge_cache')) {sg_cachepress_purge_cache();} // FOR SG OPTIMIZER
	if (function_exists('w3tc_flush_all')){w3tc_flush_all();} // FOR W3 TOTAL CACHE
	if (function_exists('wpfc_clear_all_cache')){wpfc_clear_all_cache(true);} // FOR WP Fastest Cache
	if (function_exists('wp_cache_clear_cache')){wp_cache_clear_cache();} // FOR WP Super Cache
	if (function_exists('rocket_clean_domain')){rocket_clean_domain();} // FOR WP ROCKET
	if (class_exists( 'WpeCommon' ) && method_exists( 'WpeCommon', 'purge_memcached' ) ) { // FOR WP ENGINE
      	WpeCommon::purge_memcached();
      	WpeCommon::purge_varnish_cache();
    }
    if (class_exists('LiteSpeed_Cache_API') && method_exists('LiteSpeed_Cache_API', 'purge_all') ) { // FOR LITE SPEED
     	LiteSpeed_Cache_API::purge_all();
    }
    if (class_exists('Cache_Enabler') && method_exists('Cache_Enabler', 'clear_total_cache') ) { // FOR CACHE ENABLER
    	Cache_Enabler::clear_total_cache();
    }
    if (class_exists('PagelyCachePurge') && method_exists('PagelyCachePurge','purgeAll') ) { // FOR PAGELY
      	//PagelyCachePurge::purgeAll(); REMOVED AFTER THIS TICKET https://wordpress.org/support/topic/version-6-1-1-causes-fatal-errors-on-pagely-servers/
        $uaf_purger = new PagelyCachePurge();
        $uaf_purger->purgeAll();
    }
    if (class_exists('autoptimizeCache') && method_exists( 'autoptimizeCache', 'clearall') ) { // FOR AUTOOPTIMIZSE
      	autoptimizeCache::clearall();
    }
    if (class_exists('comet_cache') && method_exists('comet_cache', 'clear') ) { // FOR COMET CACHE
      	comet_cache::clear();
    }
    if (class_exists('\Hummingbird\WP_Hummingbird') && method_exists('\Hummingbird\WP_Hummingbird', 'flush_cache')) { // FOR HUMINBIRD CACHE
     	\Hummingbird\WP_Hummingbird::flush_cache();
    }
    if (class_exists( '\Kinsta\Cache' ) && !empty( $kinsta_cache ) ) { // FOR KINSTA CACHE
        $kinsta_cache->kinsta_cache_purge->purge_complete_caches();
    }
    if (class_exists( '\WPaaS\Cache' ) ) { // FOR GODADDY Cache
        if (function_exists('ccfm_godaddy_purge')){ccfm_godaddy_purge();}
    }
    if ( class_exists( 'Breeze_Admin' ) ) { // FOR BREEZE (Cloudways)
        do_action('breeze_clear_all_cache');
    }
}

function uaf_check_if_cdn_plugin_in_use(){ // NOT BEING USED NOW.
	//if (function_exists('sg_cachepress_purge_cache')) {return true;} // IS SG OPTIMIZER ENABLED.
	//return false;
}