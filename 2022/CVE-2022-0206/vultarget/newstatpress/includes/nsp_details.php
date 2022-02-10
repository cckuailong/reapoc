<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * Display details page
 */
function nsp_DisplayDetails() {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  //$querylimit="LIMIT 10";

  # Top days
  nsp_GetDataQuery2("date", __('Top days','newstatpress') ,(get_option('newstatpress_el_top_days')=='') ? 5:get_option('newstatpress_el_top_days'), FALSE);
  # O.S.
  nsp_GetDataQuery2("os",__('OSes','newstatpress') ,(get_option('newstatpress_el_os')=='') ? 10:get_option('newstatpress_el_os'),"","","AND feed='' AND spider='' AND os<>''");

  # Browser
  nsp_GetDataQuery2("browser",__('Browsers','newstatpress') ,(get_option('newstatpress_el_browser')=='') ? 10:get_option('newstatpress_el_browser'),"","","AND feed='' AND spider='' AND browser<>''");

  # Feeds
  nsp_GetDataQuery2("feed",__('Feeds','newstatpress') ,(get_option('newstatpress_el_feed')=='') ? 5:get_option('newstatpress_el_feed'),"","","AND feed<>''");

  # SE
  nsp_GetDataQuery2("searchengine",__('Search engines','newstatpress') ,(get_option('newstatpress_el_searchengine')=='') ? 10:get_option('newstatpress_el_searchengine'),"","","AND searchengine<>''");

  # Search terms
  nsp_GetDataQuery2("search",__('Top search terms','newstatpress') ,(get_option('newstatpress_el_search')=='') ? 20:get_option('newstatpress_el_search'),"","","AND search<>''");

  # Top referrer
  nsp_GetDataQuery2("referrer",__('Top referrers','newstatpress') ,(get_option('newstatpress_el_referrer')=='') ? 10:get_option('newstatpress_el_referrer'),"","","AND referrer<>'' AND referrer NOT LIKE '%".get_bloginfo('url')."%'");

  # Languages
  nsp_GetDataQuery2("nation",__('Countries','newstatpress').'/'.__('Languages','newstatpress') ,(get_option('newstatpress_el_languages')=='') ? 20:get_option('newstatpress_el_languages'),"","","AND nation<>'' AND spider=''");

  # Spider
  nsp_GetDataQuery2("spider",__('Spiders','newstatpress') ,(get_option('newstatpress_el_spiders')=='') ? 10:get_option('newstatpress_el_spiders'),"","","AND spider<>''");

  # Top Pages
  nsp_GetDataQuery2("urlrequested",__('Top pages','newstatpress') ,(get_option('newstatpress_el_pages')=='') ? 5:get_option('newstatpress_el_pages'),"","urlrequested","AND feed='' and spider=''");

  # Top Days - Unique visitors
  nsp_GetDataQuery2("date",__('Top days','newstatpress').' - '.__('Unique visitors','newstatpress') ,(get_option('newstatpress_el_visitors')=='') ? 5:get_option('newstatpress_el_visitors'),"distinct","ip","AND feed='' and spider=''"); /* Maddler 04112007: required patching iriValueTable */

  # Top Days - Pageviews
  nsp_GetDataQuery2("date",__('Top days','newstatpress').' - '.__('Pageviews','newstatpress'),(get_option('newstatpress_el_daypages')=='') ? 5:get_option('newstatpress_el_daypages'),"","urlrequested","AND feed='' and spider=''"); /* Maddler 04112007: required patching iriValueTable */

  # Top IPs - Pageviews
  nsp_GetDataQuery2("ip",__('Top IPs','newstatpress').' - '.__('Pageviews','newstatpress'),(get_option('newstatpress_el_ippages')=='') ? 5:get_option('newstatpress_el_ippages'),"","urlrequested","AND feed='' and spider=''"); /* Maddler 04112007: required patching iriValueTable */
}
?>
