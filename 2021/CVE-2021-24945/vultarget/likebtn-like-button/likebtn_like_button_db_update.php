<?php

// database update function
function likebtn_db_update_1() {
    global $wpdb;

    $table_name = $wpdb->prefix . LIKEBTN_TABLE_ITEM;

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `identifier` text NOT NULL,
        `url` text,
        `title` text,
        `description` text,
        `image` text,
        `likes` int(11) NOT NULL DEFAULT '0',
        `dislikes` int(11) NOT NULL DEFAULT '0',
        `likes_minus_dislikes` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`ID`),
        UNIQUE KEY `identifier` (`identifier`(1)),
        KEY `title` (`title`(1)),
        KEY `likes` (`likes`),
        KEY `dislikes` (`dislikes`),
        KEY `likes_minus_dislikes` (`likes_minus_dislikes`)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// database update function
function likebtn_db_update_2() {
    global $wpdb;

    $table_name = $wpdb->prefix . LIKEBTN_TABLE_ITEM;

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {

        // Remove UNIQUE from identifier
        $sql = "DROP INDEX identifier ON {$table_name};";
        $wpdb->query($sql);

        $sql = "CREATE INDEX identifier ON {$table_name} (identifier(1));";
        $wpdb->query($sql);

        $sql = "ALTER TABLE {$table_name} ADD identifier_hash varchar(32) DEFAULT NULL, ADD UNIQUE (identifier_hash);";
        $wpdb->query($sql);

        $sql = "UPDATE {$table_name} SET identifier_hash = md5(identifier);";
        $wpdb->query($sql);
    }
}

// database update function
function likebtn_db_update_3() {
    global $wpdb;

    $table_name = $wpdb->prefix . LIKEBTN_TABLE_ITEM;

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {

        // Check if there duplicates
        $duplicates_count = $wpdb->get_results("SELECT count(*) FROM {$table_name} GROUP BY identifier HAVING count(*) > 1");

        if (count($duplicates_count) > 0) {
            // Remove duplicates by creating a proxy table
            $sql = "CREATE TABLE {$table_name}_tmp AS SELECT * FROM {$table_name};";
            $wpdb->query($sql);
            $sql = "DROP INDEX identifier_hash ON {$table_name}_tmp;";
            $wpdb->query($sql);
            $sql = "UPDATE {$table_name}_tmp SET identifier_hash = md5(identifier);";
            $wpdb->query($sql);
            $sql = "DELETE FROM {$table_name};";
            $wpdb->query($sql);
            $sql = "CREATE UNIQUE INDEX identifier_hash ON {$table_name} (identifier_hash);";
            $wpdb->query($sql);
            $sql = "INSERT IGNORE INTO {$table_name} SELECT * FROM {$table_name}_tmp;";
            $wpdb->query($sql);
            $sql = "DROP TABLE {$table_name}_tmp;";
            $wpdb->query($sql);
            // Set identifier_hash once more
            $sql = "UPDATE {$table_name} SET identifier_hash = md5(identifier);";
            $wpdb->query($sql);
            $sql = "ALTER TABLE {$table_name} MODIFY identifier_hash varchar(32) NOT NULL;";
            $wpdb->query($sql);
        } else {
            // Add index once more
            $sql = "CREATE UNIQUE INDEX identifier_hash ON {$table_name} (identifier_hash);";
            $wpdb->query($sql);
            $sql = "UPDATE {$table_name} SET identifier_hash = md5(identifier);";
            $wpdb->query($sql);
            $sql = "ALTER TABLE {$table_name} MODIFY identifier_hash varchar(32) NOT NULL;";
            $wpdb->query($sql);
        }
    }
}

// rename options
function likebtn_db_update_4() {

    // rename options
    global $likebtn_settings;
    global $likebtn_settings_deprecated;
    global $likebtn_settings_options;
    global $likebtn_buttons_options;
    global $likebtn_internal_options;
    global $wpdb;

    // On actiovation at this point options are not added yet
    _likebtn_add_options();

    // no need to rename options
    //if (!get_option('likebtn_like_button_plan')) {
    //    return true;
    //}

    foreach ($likebtn_settings_options as $option_name=>$option_value) {
        $old_option_name = str_replace('likebtn_', 'likebtn_like_button_', $option_name);
        update_option($option_name, get_option($old_option_name));
        delete_option($old_option_name);
    }

    $likebtn_entities = _likebtn_get_entities();
    foreach ($likebtn_entities as $entity_name => $entity_title) {
        foreach ($likebtn_buttons_options as $option_name=>$option_value) {
            $option_name = $option_name.'_'.$entity_name;
            $old_option_name = str_replace('likebtn_', 'likebtn_like_button_', $option_name);
            update_option($option_name, get_option($old_option_name));
            delete_option($old_option_name);
        }
        // settings
        $likebtn_settings_all = array_merge($likebtn_settings, $likebtn_settings_deprecated);

        foreach ($likebtn_settings_all as $option_name => $option_info) {
            $new_option_name = 'likebtn_settings_'.$option_name.'_'.$entity_name;
            $old_option_name = str_replace('likebtn_', 'likebtn_like_button_', $new_option_name);

            $option_exists = $wpdb->get_row($wpdb->prepare("
                SELECT option_value
                FROM ".$wpdb->prefix."options
                WHERE option_name = %s
            ", $old_option_name));
            if ($option_exists) {
                update_option($new_option_name, get_option($old_option_name));
                delete_option($old_option_name);
            }
        }
    }

    foreach ($likebtn_internal_options as $option_name=>$option_value) {
        $old_option_name = str_replace('likebtn_', 'likebtn_like_button_', $option_name);
        update_option($option_name, get_option($old_option_name));
        delete_option($old_option_name);
    }
}

// fix voting_enabled
function likebtn_db_update_5() {
    global $wpdb;

    $likebtn_entities = _likebtn_get_entities();
    foreach ($likebtn_entities as $entity_name => $entity_title) {

        $option_name = 'likebtn_settings_voting_enabled_'.$entity_name;

        $option_exists = $wpdb->get_row($wpdb->prepare("
            SELECT option_value
            FROM ".$wpdb->prefix."options
            WHERE option_name = %s
        ", $option_name));
        if ($option_exists) {
            update_option($option_name, '1');
        }
    }
}

// New options
function likebtn_db_update_6() {
    global $likebtn_buttons_options;

    $likebtn_entities = _likebtn_get_entities();
    foreach ($likebtn_entities as $entity_name => $entity_title) {
        update_option('likebtn_newline_'.$entity_name, $likebtn_buttons_options['likebtn_newline']);
        update_option('likebtn_wrap_'.$entity_name, $likebtn_buttons_options['likebtn_wrap']);
    }
}

// fix voting_enabled
function likebtn_db_update_7() {
    global $wpdb;

    //_likebtn_add_options();

    $likebtn_entities = _likebtn_get_entities();
    foreach ($likebtn_entities as $entity_name => $entity_title) {

        $option_name = 'likebtn_settings_popup_enabled_'.$entity_name;

        $option_exists = $wpdb->get_row($wpdb->prepare("
            SELECT option_value
            FROM ".$wpdb->prefix."options
            WHERE option_name = %s
        ", $option_name));
        if ($option_exists) {
            if ((int)$option_exists->option_value != 1) {
                update_option('likebtn_settings_popup_disabled_'.$entity_name, '1');
            }
        }
    }
}

function likebtn_db_update_8() {
    global $wpdb;

    $table_vote = $wpdb->prefix . LIKEBTN_TABLE_VOTE;

    $sql = "CREATE TABLE IF NOT EXISTS {$table_vote} (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `identifier` text NOT NULL,
        `identifier_hash` varchar(32) NOT NULL,
        `client_identifier` varchar(32) NOT NULL,
        `type` tinyint(1) NOT NULL,
        `user_id` bigint(20) DEFAULT 0,
        `ip` varchar(40) NOT NULL,
        `lat` float(10, 6) NULL,
        `lng` float(10, 6) NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`ID`),
        UNIQUE KEY `identifiers` (`identifier_hash`, `client_identifier`, `user_id`),
        KEY `identifier` (`identifier`(7)),
        KEY `created_at` (`created_at`),
        KEY `user_id` (`user_id`),
        KEY `ip` (`ip`)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

function likebtn_db_update_9() {
    $likebtn_entities = _likebtn_get_entities();

    foreach ($likebtn_entities as $entity_name => $entity_title) {

        // New options added
        _likebtn_add_default_options($entity_name);

        // Backward compatibility
        if (get_option('likebtn_settings_display_only_' . $entity_name)) {
            if (get_option('likebtn_settings_display_only_' . $entity_name) == '1') {
                update_option('likebtn_settings_voting_enabled_' . $entity_name, '0');
            }
            delete_option('likebtn_settings_display_only_' . $entity_name);
        }
        if (get_option('likebtn_settings_unlike_allowed_' . $entity_name)) {
            if (get_option('likebtn_settings_unlike_allowed_' . $entity_name) == '1') {
                update_option('likebtn_settings_voting_cancelable_' . $entity_name, '1');
            }
            delete_option('likebtn_settings_unlike_allowed_' . $entity_name);
        }
        if (get_option('likebtn_settings_like_dislike_at_the_same_time_' . $entity_name)) {
            if (get_option('likebtn_settings_like_dislike_at_the_same_time_' . $entity_name) == '1') {
                update_option('likebtn_settings_voting_both_' . $entity_name, '1');
            }
            delete_option('likebtn_settings_like_dislike_at_the_same_time_' . $entity_name);
        }
        if (get_option('likebtn_settings_show_copyright_' . $entity_name) === '0') {
            update_option('likebtn_settings_white_label_' . $entity_name, '1');
            delete_option('likebtn_settings_show_copyright_' . $entity_name);
        }
        if (get_option('likebtn_settings_style_' . $entity_name)) {
            update_option('likebtn_settings_theme_' . $entity_name, get_option('likebtn_settings_style_' . $entity_name));
            delete_option('likebtn_settings_style_' . $entity_name);
        }
        if (get_option('likebtn_settings_revote_period_' . $entity_name)) {
            update_option('likebtn_settings_voting_frequency_' . $entity_name, get_option('likebtn_settings_revote_period_' . $entity_name));
            delete_option('likebtn_settings_revote_period_' . $entity_name);
        }
        // Process likebtn_post_view_mode_ for Excerpt entities
        if (_likebtn_has_list_flag($entity_name)) {
            $original_name = _likebtn_cut_list_flag($entity_name);

            if (get_option('likebtn_post_view_mode_' . $entity_name) && get_option('likebtn_show_' . $original_name) == '1') {
                if (in_array(get_option('likebtn_post_view_mode_' . $entity_name), array('excerpt', 'both')) &&
                    get_option('likebtn_show_' . $entity_name) != '1')
                {
                    update_option('likebtn_show_' . $entity_name, '1');
                    update_option('likebtn_use_settings_from_' . $entity_name, $original_name);
                }
                delete_option('likebtn_post_view_mode_' . $entity_name);
            }
        }
    }
}

function likebtn_db_update_10() {
    if (get_option('likebtn_acc_data_correct') != '1') {
        require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
        $likebtn = new LikeBtnLikeButton();
        $test_response = $likebtn->checkAccount(get_option('likebtn_account_email'), get_option('likebtn_account_api_key'), get_option('likebtn_site_id'));

        if ($test_response['connect_result'] == 'success') {
            if ($test_response['result'] == 'success') {
                update_option('likebtn_acc_data_correct', '1');
            }
        }
    }
}

function likebtn_db_update_11() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_item = $wpdb->prefix . LIKEBTN_TABLE_ITEM;
    $sql = "
    CREATE TABLE IF NOT EXISTS {$table_item} (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `identifier` text NOT NULL,
        `identifier_hash` varchar(32) NOT NULL,
        `url` text,
        `title` text,
        `description` text,
        `image` text,
        `likes` int(11) NOT NULL DEFAULT '0',
        `dislikes` int(11) NOT NULL DEFAULT '0',
        `likes_minus_dislikes` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`ID`),
        UNIQUE KEY `identifier_hash` (`identifier_hash`),
        KEY `title` (`title`(1)),
        KEY `likes` (`likes`),
        KEY `dislikes` (`dislikes`),
        KEY `likes_minus_dislikes` (`likes_minus_dislikes`),
        KEY `identifier` (`identifier`(1))
    );";

    dbDelta($sql);
}

function likebtn_db_update_12() {
    global $wpdb;

    $table_name = $wpdb->prefix . LIKEBTN_TABLE_VOTE;

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {

        // Remove UNIQUE from identifiers key
        $sql = "DROP INDEX identifiers ON {$table_name};";
        $wpdb->query($sql);

        $sql = "CREATE INDEX identifiers ON {$table_name} (identifier_hash, client_identifier, user_id);";
        $wpdb->query($sql);
    }
}

// New options
function likebtn_db_update_13() {
    _likebtn_set_default_settings('popup_width');
}

// New options
function likebtn_db_update_14() {
    update_option('likebtn_ipvi', LIKEBTN_IP_VOTE_INTERVAL);
    update_option('likebtn_ipvi_hash', LIKEBTN_IP_VOTE_INTERVAL);
}

// database update function
function likebtn_db_update_15() {
    global $wpdb;

    $table_name = $wpdb->prefix . LIKEBTN_TABLE_VOTE;

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
        $sql = "ALTER TABLE {$table_name} ADD country varchar(2) DEFAULT NULL;";
        $wpdb->query($sql);
    }
}

// database update function
function likebtn_db_update_16() {
    global $wpdb;

    $table_name = $wpdb->prefix . LIKEBTN_TABLE_VOTE;

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
        $sql = "ALTER TABLE {$table_name} ADD country varchar(2) DEFAULT NULL;";
        $wpdb->query($sql);
    }
}

// New options
function likebtn_db_update_17() {
    update_option('likebtn_notify_to', get_option('admin_email'));
    update_option('likebtn_notify_from', likebtn_default_notify_from());
    update_option('likebtn_notify_subject', '♥ '.__('New {vote_type} on {domain}', 'likebtn-like-button'));
    update_option('likebtn_notify_text', likebtn_default_notify_text());
}

// New options
function likebtn_db_update_18() {
    update_option('likebtn_gdpr', '1');
}

// New options
function likebtn_db_update_19() {
    update_option('likebtn_bp_filter', '1');
}

function likebtn_db_update_20() {
    update_option('likebtn_info_message', '1');
}