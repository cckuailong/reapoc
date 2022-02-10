<?php

// BuddyPress component name
define('LIKEBTN_BP_COMPONENT_NAME', 'likebtn');

// BuddyPress types
define('LIKEBTN_BP_ACTIVITY_TYPE', 'likebtn_vote');

// https://webdevstudios.com/2015/10/06/buddypress-adding-custom-notifications/
function likebtn_notifications_get_registered_components($component_names = array())
{ 
    // Force $component_names to be an array
    if (!is_array($component_names)) {
        $component_names = array();
    }
    // Add 'custom' component to registered components array
    array_push($component_names, LIKEBTN_BP_COMPONENT_NAME);
    // Return component's with 'custom' appended
    return $component_names;
}
add_filter('bp_notifications_get_registered_components', 'likebtn_notifications_get_registered_components');

function likebtn_notifications_get_notifications_for_user($action, $item_id, $secondary_item_id, $total_items, $format = 'string')
{
    global $wp_filter;
    global $wp_version;

	$return = '';

    // Parse action
    preg_match("/likebtn_(.*?)(?:_\d+)?_(like|dislike)/", $action, $m);

    if (count($m) == 3) {

    	$entity_name = $m[1];
    	$type = $m[2];

  		$voter_id = $secondary_item_id;

    	$entity_type_name = _likebtn_get_entity_name_title($entity_name, true);

    	if ($type == 'like') {
    		$type_name = __('liked', 'likebtn-like-button');
    	} else {
    		$type_name = __('disliked', 'likebtn-like-button');
    	}

    	$author_name = bp_core_get_user_displayname($voter_id);
    	$entity_title = _likebtn_get_entity_title($entity_name, $item_id);

        $link  = _likebtn_get_entity_url($entity_name, $item_id);

        // Modify link
        $likebtn_bp_params = json_encode(array(
            //'user_id'           => $user_id,
            'item_id'           => $item_id,
            'secondary_item_id' => $secondary_item_id,
            'component_action'  => $action
        ));
        $none_name = 'likebtn_bp_mark_read_'.md5($action.$item_id.$secondary_item_id);
        $link = wp_nonce_url(add_query_arg(array('action'=>'likebtn_bp_mark_read', 'likebtn_bp_params'=>base64_encode($likebtn_bp_params)), $link), $none_name);

        if ($entity_name == LIKEBTN_ENTITY_USER) {
            $pattern = __('%author% %action% your profile', 'likebtn-like-button');
        } else {
            $pattern = __('%author% %action% your %entity_name% "%entity_title%"', 'likebtn-like-button');
        }
        $text  = strtr($pattern, array(
            '%author%' => $author_name,
            '%action%' => $type_name,
            '%entity_name%' => $entity_type_name,
            '%entity_title%' => $entity_title
        ));
        $title = $text;

        if ('string' === $format) {
        	// WordPress Toolbar
            $return = apply_filters('likebtn', '<a href="' . esc_url( $link ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $text ) . '</a>', $text, $link);
        } else {
        	// Deprecated BuddyBar
            $return = apply_filters('likebtn', array(
                'text' => $text,
                'link' => $link
            ), $link, (int) $total_items, $text, $title );
        }

        // We modify global wp_filter to call our bbPress wrapper function
        if ( function_exists('bbp_get_version') && version_compare( bbp_get_version(), '2.6.0' , '<') ) {
            if (isset($wp_filter['bp_notifications_get_notifications_for_user'][10]['bbp_format_buddypress_notifications'])) {
                if (version_compare($wp_version, '4.7', '>=' )) {
                    // https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
                    $wp_filter['bp_notifications_get_notifications_for_user']->callbacks[10]['bbp_format_buddypress_notifications']['function'] = 'likebtn_bbp_format_buddypress_notifications';
                } else {
                    $wp_filter['bp_notifications_get_notifications_for_user'][10]['bbp_format_buddypress_notifications']['function'] = 'likebtn_bbp_format_buddypress_notifications';
                }
            }
        }

    	return $return;
    }

    return $action;
}
// bbPres has a bug: 
// https://bbpress.org/forums/topic/return-value-in-bbp_format_buddypress_notifications/
// https://buddypress.trac.wordpress.org/ticket/6669
// Filter must be called before corresponding bbPress filter
add_filter('bp_notifications_get_notifications_for_user', 'likebtn_notifications_get_notifications_for_user', 5, 5);

// Add Like/Dislike notification for BuddyPress user
// user_id - user who voted
function _likebtn_bp_notifications_add_notification($entity_name, $entity_id, $voter_id, $action)
{
    if (!in_array($action, array('like', 'dislike'))) {
        $action = 'like';
    }
	// No notifications from Anonymous
	if (!$voter_id) {
		return false;
	}
	$author_id = _likebtn_get_author_id($entity_name, $entity_id);

	if (!$author_id || $author_id == $voter_id) {
		return false;
	}
    $args = array(
        'user_id'           => $author_id,
        'item_id'           => $entity_id,
        'secondary_item_id' => $voter_id,
        'component_name'    => LIKEBTN_BP_COMPONENT_NAME,
        // BuddyPress is grouping notifications by this field in the top counter
        //'component_action'  => 'likebtn_'.$entity_name.'_'.$action,
        'component_action'  => 'likebtn_'.$entity_name.'_'.$entity_id.'_'.$action,
        'date_notified'     => bp_core_current_time(),
        'is_new'            => 1,
    );

    bp_notifications_add_notification($args);
    // bp_notifications_add_meta($notification_id, 'entity_name', $entity_name, true)
}

// Wrapper for bbp_format_buddypress_notifications function as it is not returning $action
function likebtn_bbp_format_buddypress_notifications($action, $item_id, $secondary_item_id, $total_items, $format = 'string')
{
    $result = bbp_format_buddypress_notifications($action, $item_id, $secondary_item_id, $total_items, $format);
    if (!$result) {
        $result = $action;
    }
    return $result;
}

// Mark notifications as read
function likebtn_bbp_buddypress_mark_notifications()
{
    global $wp;

    $action = '';
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }

    // Sanitizing:
    // Bail if action is not for this function
    if ('likebtn_bp_mark_read' !== $action ) {
        return;
    }

    if (empty($_GET['likebtn_bp_params']) || empty($_GET['_wpnonce'])) {
        return;
    }

    // Sanitising is not needed here as likebtn_bp_params are used  only in md5() function
    // and bp_notifications_mark_notifications_by_item_id() which performs it's own sanitizing
    $params = base64_decode($_GET['likebtn_bp_params']);

    if (empty($params['item_id']) || empty($params['secondary_item_id']) || empty($params['component_action'])) {
        return;
    }

    // Get required data
    $user_id  = bp_loggedin_user_id();
    $errors = false;
    $none_name = 'likebtn_bp_mark_read_'.md5($params['component_action'].$params['item_id'].$params['secondary_item_id']);

    // Check nonce
    if (!wp_verify_nonce($_GET['_wpnonce'], $none_name)) {
        $errors = true;
    } elseif ( !current_user_can( 'edit_user', $user_id ) ) {
        // Check current user's ability to edit the user
        $errors = true;
    }

    // Bail if we have errors
    if (!$errors) {
        // Attempt to clear notifications for the current user from this topic
        $success = bp_notifications_mark_notifications_by_item_id( $user_id, sanitize_text_field($params['item_id']), LIKEBTN_BP_COMPONENT_NAME, sanitize_text_field($params['component_action']), sanitize_text_field($params['secondary_item_id']));
    }

    // Redirect url
    $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));

    $redirect_url = add_query_arg(array(
        'likebtn_bp_params' => false,
        'action' => false,
        '_wpnonce' => false
    ), $current_url);

    // Redirect
    wp_safe_redirect($redirect_url);

    // For good measure
    exit();
}
add_action('template_redirect', 'likebtn_bbp_buddypress_mark_notifications');

// Add activity to BuddyPress
function _likebtn_bp_activity_add($entity_name, $entity_id, $voter_id, $vote_type, $hide_sitewide, $snippet, $snippet_html)
{
    global $likebtn_entities_config;

    if (!$voter_id) {
        return false;
    }
    if ($vote_type == LIKEBTN_VOTE_LIKE) {
        $type_name = __('liked', 'likebtn-like-button');
    } else {
        $type_name = __('disliked', 'likebtn-like-button');
    }
    $primary_link = _likebtn_get_entity_url($entity_name, $entity_id);

    $pattern = __('<a href="%user_url%" title="%user_name%">%user_name%</a> %type_name% %entity_name%, <a href="%entity_url%">%entity_title%</a>', 'likebtn-like-button');

    $title = _likebtn_get_entity_title($entity_name, $entity_id);

    $action = strtr($pattern, array(
        '%user_url%' => _likebtn_get_entity_url(LIKEBTN_ENTITY_BP_MEMBER, $voter_id),
        '%user_name%' => bp_core_get_user_displayname($voter_id),
        '%type_name%' => $type_name,
        '%entity_url%' => _likebtn_get_entity_url($entity_name, $entity_id),
        '%entity_name%' => mb_strtolower(_likebtn_get_entity_name_title($entity_name, true)),
        '%entity_title%' => $title
    ));

    // Add snippet
    $content = "<!--{$entity_name}_{$entity_id}-->";
    if ($snippet == '1') {
        $image_thumbnail = _likebtn_get_entity_image($entity_name, $entity_id, 'thumbnail');
        $excerpt = _likebtn_get_entity_excerpt($entity_name, $entity_id);
        $entity_content = '';

        if (!$snippet_html) {
            if (isset($likebtn_entities_config['bp_snippet_tpl'][$entity_name]['value'])) {
                $snippet_html = $likebtn_entities_config['bp_snippet_tpl'][$entity_name]['value'];
            } else {
                $snippet_html = LIKEBTN_BP_SNIPPET_TPL;
            }
        }

        if (strstr('%content%', $snippet_html)) {
            $entity_content = _likebtn_get_entity_content($entity_name, $entity_id);
        }

        // Some hostings or plugins redirect to 404 (options.php) if there is "%text%" in one of the post fields.
        $symbols = array('%', '@');

        foreach ($symbols as $symbol) {
            $snippet_html = strtr($snippet_html, array(
                $symbol.'image_thumbnail'.$symbol => $image_thumbnail,
                $symbol.'title'.$symbol           => $title,
                $symbol.'excerpt'.$symbol         => $excerpt,
                $symbol.'content'.$symbol         => $entity_content,
            ));
        }

        $content .= $snippet_html;

        // ob_start();
        // include(_likebtn_get_template_path(LIKEBTN_TEMPLATE_ACTIVITY_SNIPPET));
        // $content .= ob_get_contents();
        // ob_get_clean();
    }

    $component = LIKEBTN_BP_COMPONENT_NAME;
    $item_id = $entity_id;

    // Determine hide_sitewide 
    $private_group_activity = false;
    $group_id = 0;

    // Set hide_sitewide to true:
    // - BuddyPress forum
    // - Activity update in private group
    if ($entity_name == LIKEBTN_ENTITY_BBP_POST) {
        $group_post_id = (int)get_post_meta($entity_id, '_bbp_forum_id', true);

        if ($group_post_id) {
            $group_id_array = get_post_meta($group_post_id, '_bbp_group_ids', true);
            if ($group_id_array && isset($group_id_array[0])) {
                $group_id = (int)$group_id_array[0];
                //$group = groups_get_group( array( 'group_id' => $group_id ) );
                //$group_permalink = trailingslash*t( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/' ) );
                $group_post = get_post($group_post_id);
                if ($group_post && !empty($group_post->post_status) && $group_post->post_status == 'private') {
                    $private_group_activity = true;
                }
            }
        }
    }

    if (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC)))
    {
        $get_activity = bp_activity_get_specific(array('activity_ids' => $entity_id));

        if (!empty($get_activity['activities']) && !empty($get_activity['activities'][0])) {
            $activity = $get_activity['activities'][0];
            if ($activity->component == 'groups' && isset($activity->item_id) && (int)$activity->hide_sitewide == 1) {
                $private_group_activity = true;
                $group_id = $activity->item_id;
            }
        }
    }

    if ($private_group_activity && $group_id) {
        $hide_sitewide = 1;
        $component = 'groups';
        $item_id = $group_id;
    }

    bp_activity_add(array(
        'user_id'   => $voter_id,
        'item_id'   => $item_id,
        'secondary_item_id'   => $vote_type,
        'primary_link'   => $primary_link,
        'action'    => $action,
        'content' => $content,
        'component' => $component,
        'type'      => LIKEBTN_BP_ACTIVITY_TYPE,
        'hide_sitewide' => (int)$hide_sitewide
    ));
}

// Display and option in BuddyPress activity filter
function likebtn_activity_filter_options() {
    if (get_option('likebtn_bp_filter') == '1') {
        ?>
        <option value="<?php echo LIKEBTN_BP_ACTIVITY_TYPE; ?>"><?php _e('Votes'); ?></option>
        <?php
    }
}
 
// Activity Directory
add_action( 'bp_activity_filter_options', 'likebtn_activity_filter_options' );
// Member's profile activity
add_action( 'bp_member_activity_filter_options', 'likebtn_activity_filter_options' ); 
// Group's activity
add_action( 'bp_group_activity_filter_options', 'likebtn_activity_filter_options' );

// Add extra allowed tags
function likebtn_bp_activity_allowed_tags( $allowedtags ) {
    if (empty($allowedtags['table'])) {
        $allowedtags['table'] = array();    
    }
    if (empty($allowedtags['tr'])) {
        $allowedtags['tr'] = array();    
    }
    if (empty($allowedtags['td'])) {
        $allowedtags['td'] = array();    
    }

    return $allowedtags;
}
add_filter('bp_activity_allowed_tags', 'likebtn_bp_activity_allowed_tags');

// Sorting new
function likebtn_bp_activity_paged_activities_sql($sql, $args) {
    global $bp;

    $likebtn_bp_act_sort = get_option('likebtn_bp_act_sort');

    $join_sql = " LEFT JOIN {$bp->activity->table_name_meta} m ON m.activity_id = a.id AND m.meta_key = '{$likebtn_bp_act_sort}' ";

    $sql = str_replace(' WHERE ', $join_sql.' WHERE ', $sql);
    $sql = str_replace('ORDER BY a.date_recorded DESC', ' ORDER BY m.meta_value+0 DESC ', $sql);

    return $sql;
}

// Sorting legacy
function likebtn_bp_activity_get_user_join_filter($sql, $select_sql, $from_sql, $where_sql )
{
    global $bp;

    $likebtn_bp_act_sort = get_option('likebtn_bp_act_sort');
    
    $sql = "
        {$select_sql}
        {$from_sql}
        LEFT JOIN {$bp->activity->table_name_meta} m ON m.activity_id = a.id AND m.meta_key = '{$likebtn_bp_act_sort}' 
        {$where_sql}
        ORDER BY m.meta_value+0 DESC
    ";

    return $sql;
}

// Sorting legacy
function likebtn_bp_activity_total_activities_sql($sql, $where_sql, $sort)
{
    global $bp;

    $likebtn_bp_act_sort = get_option('likebtn_bp_act_sort');

    $sql = "
        SELECT count(DISTINCT a.id)
        FROM dwp_bp_activity a 
        LEFT JOIN {$bp->activity->table_name_meta} m ON m.activity_id = a.id AND m.meta_key = '{$likebtn_bp_act_sort}'
        {$where_sql}
    ";

    return $sql;
}

if (get_option('likebtn_bp_act_sort')) {
    add_filter('bp_activity_paged_activities_sql', 'likebtn_bp_activity_paged_activities_sql', 10, 3);
    //add_filter('bp_has_activities', 'likebtn_bp_activity_sort', 10, 3);
    
    // Legacy
    add_filter('bp_activity_get_user_join_filter', 'likebtn_bp_activity_get_user_join_filter', 10, 6 );
    add_filter('bp_activity_total_activities_sql', 'likebtn_bp_activity_total_activities_sql', 10, 3 );
}
