<?php

/* 
 * Uninstall clear all data from database
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$option_default = 'ps_all_global_options';
$option_custom = 'ps_all_global_options_default';

delete_option($option_default);
delete_option($option_custom);

global $wpdb;

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ps");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ps_answers");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ps_answers_values");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ps_data");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ps_questions");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ps_logic_conditions");
$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type='ps'");
$wpdb->query("DELETE pm.* FROM {$wpdb->prefix}posts p INNER JOIN {$wpdb->prefix}postmeta pm ON(pm.post_id = p.ID) WHERE p.post_type='ps'");