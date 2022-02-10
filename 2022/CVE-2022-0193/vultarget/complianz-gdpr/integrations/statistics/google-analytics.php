<?php
defined('ABSPATH') or die("you do not have acces to this page!");

add_filter('cmplz_known_script_tags', 'cmplz_googleanalytics_script');
function cmplz_googleanalytics_script($tags){
    $tags[] =  'google-analytics.com/ga.js';
    $tags[] =  'www.google-analytics.com/analytics.js';
    $tags[] =  'www.googletagmanager.com/gtag/js';
    $tags[] =  '_getTracker';
    $tags[] =  'apis.google.com/js/platform.js';

    return $tags;

}
