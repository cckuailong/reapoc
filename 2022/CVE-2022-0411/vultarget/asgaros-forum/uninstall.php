<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

if (function_exists('is_multisite') && is_multisite()) {
    global $wpdb;
    $old_blog =  $wpdb->blogid;

    // Get all blog ids
    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    foreach ($blogids as $blog_id) {
        switch_to_blog($blog_id);
        deleteData();
    }

    switch_to_blog($old_blog);
} else {
    deleteData();
}

function recursiveDelete($str) {
    if (is_file($str)) {
        return @unlink($str);
    } else if (is_dir($str)) {
        $scan = glob(rtrim($str, '/').'/*');

        foreach ($scan as $path) {
            recursiveDelete($path);
        }

        return @rmdir($str);
    }
}

function deleteData() {
    global $wpdb;

    delete_option('asgarosforum_options');
    delete_option('asgarosforum_appearance');
    delete_option('asgarosforum_db_version');

    // For site options in multisite
    delete_site_option('asgarosforum_options');
    delete_site_option('asgarosforum_appearance');
    delete_site_option('asgarosforum_db_version');

    // Delete user meta data
    delete_metadata('user', 0, 'asgarosforum_role', '', true);
    delete_metadata('user', 0, 'asgarosforum_signature', '', true);
    delete_metadata('user', 0, 'asgarosforum_mention_notify', '', true);
    delete_metadata('user', 0, 'asgarosforum_subscription_topic', '', true);
    delete_metadata('user', 0, 'asgarosforum_subscription_forum', '', true);
    delete_metadata('user', 0, 'asgarosforum_subscription_global_posts', '', true);
    delete_metadata('user', 0, 'asgarosforum_subscription_global_topics', '', true);
    delete_metadata('user', 0, 'asgarosforum_unread_cleared', '', true);
    delete_metadata('user', 0, 'asgarosforum_unread_exclude', '', true);
    delete_metadata('user', 0, 'asgarosforum_online', '', true);
    delete_metadata('user', 0, 'asgarosforum_online_timestamp', '', true);

    // Delete category terms.
    $terms = $wpdb->get_col('SELECT t.term_id FROM '.$wpdb->terms.' AS t INNER JOIN '.$wpdb->term_taxonomy.' AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = "asgarosforum-category";');

    foreach ($terms as $term) {
        wp_delete_term($term, 'asgarosforum-category');
    }

    // Delete usergroup terms.
    $terms = $wpdb->get_col('SELECT t.term_id FROM '.$wpdb->terms.' AS t INNER JOIN '.$wpdb->term_taxonomy.' AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = "asgarosforum-usergroup";');

    foreach ($terms as $term) {
        wp_delete_term($term, 'asgarosforum-usergroup');
    }

    // Drop custom tables
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_forums;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_topics;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_posts;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_reports;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_reactions;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_polls;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_polls_options;");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_polls_votes;");

    // Delete uploaded files
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'].'/asgarosforum/';
    recursiveDelete($upload_path);

    // Delete themes
    $theme_path = WP_CONTENT_DIR.'/themes-asgarosforum';
    recursiveDelete($theme_path);

    // Delete data which has been used in old versions of the plugin.
    delete_metadata('user', 0, 'asgarosforum_lastvisit', '', true);
    delete_metadata('user', 0, 'asgarosforum_moderator', '', true);
    delete_metadata('user', 0, 'asgarosforum_banned', '', true);
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}forum_threads;");
}
