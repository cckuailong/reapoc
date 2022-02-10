<?php

function likebtn_bbp_has_replies_query( $query = array() ) {
    // Identify post type
    $bbPress_post_id = get_the_ID();
    $bbPress_post_type = get_post_type($bbPress_post_id);
    
    if ( $bbPress_post_type =='topic' ) {
        
        // Get global settings
        $bbpress_replies_sort = get_option('likebtn_bbp_replies_sort');
        //$global_bbPress_option_no_parent = get_option('_bbp_sort_desc_global_no_parent');
        
        // Get forum for the current topic
        $bbPress_forum_id = get_post_meta($bbPress_post_id, '_bbp_forum_id', true);
        
        // Get sort setting for the current topic
        $bbPress_sort_status = get_post_meta($bbPress_post_id, '_bbp_topic_sort_desc', true);
        
        // TOPIC
        if ( $bbPress_sort_status == 'desc' ){
            $query['order']='DESC';
            return $query;
        } elseif ( $bbPress_sort_status == 'asc' ){
            $query['order']='ASC';
            return $query;
        }
        
        // FORUM 
        // Apply the settings set for the current topic's forum **/
        $bbPress_sort_status = get_post_meta($bbPress_forum_id, '_bbp_sort_desc', true);
        if ( $bbPress_sort_status == 'desc' ){
            $query['order']='DESC';
            return $query;
        }
        if ( $bbPress_sort_status == 'asc' ){
            $query['order']='ASC';
            return $query;
        }
        
        //  Apply if Topic and Forum setting are not set
        /*if ($bbPress_forum_id == $bbPress_post_id){
            // If forum has no parent, apply global setting for topics with no parent
            if ( $global_bbPress_option_no_parent == 'desc' ){
                $query['order']='DESC';
                return $query;
            } elseif ( $global_bbPress_option_no_parent == 'asc' ){
                $query['order']='ASC';
                return $query;
            }
        } else {*/
        $query['orderby'] = 'meta_value';
        $query['order'] = 'DESC';
        $query['meta_query'] = array(
            'relation' => 'OR',
            array(
                'key' => $bbpress_replies_sort,
                'compare' => 'NOT EXISTS',
                'type' => 'numeric'
            ),
            array(
                'key' => $bbpress_replies_sort,
                'compare' => 'EXISTS',
                'type' => 'numeric'
            )
        );
    }
    return $query;
}

if (_likebtn_is_bbp_active() && get_option('likebtn_bbp_replies_sort')) {
    add_filter('bbp_has_replies_query', 'likebtn_bbp_has_replies_query');
}
