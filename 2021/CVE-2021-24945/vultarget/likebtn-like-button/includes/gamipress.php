<?php

if( !defined( 'ABSPATH' ) ) exit;

function likebtn_is_gamipress_active()
{
    return class_exists('GamiPress');
}

function likebtn_gamipress_process_vote($args)
{
    $entity_name = $args['entity_name'];
    $entity_id   = $args['entity_id'];
    if (empty($entity_id)) {
        // Custom item
        $entity_name = LIKEBTN_ENTITY_CUSTOM_ITEM;
        $entity_id = $args['identifier'];
    }

    $author_id  = _likebtn_get_author_id($entity_name, $entity_id);

    $custom_args = array(
        'entity_name' => $entity_name,
        'entity_id'   => $entity_id,
    );

    // To load custom post types we have to reload GamiPress triggers
    if (function_exists('gamipress_load_activity_triggers')) {
        gamipress_load_activity_triggers();
    }

    if ($args['type'] == LIKEBTN_VOTE_LIKE) {
        // Like
        likebtn_gamipress_trigger('gamipress_likebtn_like', $args['user_id'], $custom_args);
        likebtn_gamipress_trigger('gamipress_likebtn_like_'.$entity_name, $args['user_id'], $custom_args);
        if ($author_id && (int)$author_id != (int)$args['user_id']) {
            // Aauthor is awarded one, user is the user that liked
            likebtn_gamipress_trigger('gamipress_likebtn_get_like', $author_id, array_merge($custom_args, array(
                'voter_id' => $args['user_id']
            )));
            likebtn_gamipress_trigger('gamipress_likebtn_get_like_'.$entity_name, $author_id, array_merge($custom_args, array(
                'voter_id' => $args['user_id']
            )));
        }
    } elseif ($args['type'] == LIKEBTN_VOTE_DISLIKE) {
        // Dislike
        likebtn_gamipress_trigger('gamipress_likebtn_dislike', $args['user_id'], $custom_args);
        likebtn_gamipress_trigger('gamipress_likebtn_dislike_'.$entity_name, $args['user_id'], $custom_args);
        if ($author_id && (int)$author_id != (int)$args['user_id']) {
            // Aauthor is awarded one, user is the user that liked
            likebtn_gamipress_trigger('gamipress_likebtn_get_dislike', $author_id, array_merge($custom_args, array(
                'voter_id' => $args['user_id']
            )));
            likebtn_gamipress_trigger('gamipress_likebtn_get_dislike_'.$entity_name, $author_id, array_merge($custom_args, array(
                'voter_id' => $args['user_id']
            )));
        }
    }
}

function likebtn_gamipress_trigger($event, $rewarded_user_id, $custom_args = array())
{
    gamipress_trigger_event(array_merge(array(
        'event'       => $event,
        'user_id'     => $rewarded_user_id,
    ), $custom_args));
}

function likebtn_gamipress_activity_triggers($triggers)
{
    $likebtn_entities = _likebtn_get_entities(true, false, false);

    $trigger_name = __('Like Button', 'likebtn-like-button');
    $triggers[$trigger_name] = array(
        'gamipress_likebtn_like'        => __('Like anything', 'likebtn-like-button'),
        'gamipress_likebtn_get_like'    => __('Get a like anywhere', 'likebtn-like-button'),
        'gamipress_likebtn_dislike'     => __('Dislike anything', 'likebtn-like-button'),
        'gamipress_likebtn_get_dislike' => __('Get a dislike anywhere', 'likebtn-like-button'),
    );

    foreach ($likebtn_entities as $entity_name => $entity_title) {
        $triggers[$trigger_name]['gamipress_likebtn_like_'.$entity_name] = strtr(__('Like a %entity_name%', 'likebtn-like-button'), array('%entity_name%' => $entity_title));
        $triggers[$trigger_name]['gamipress_likebtn_get_like_'.$entity_name] = strtr(__('Get a like on a %entity_name%', 'likebtn-like-button'), array('%entity_name%' => $entity_title));
        $triggers[$trigger_name]['gamipress_likebtn_dislike_'.$entity_name] = strtr(__('Dislike a %entity_name%', 'likebtn-like-button'), array('%entity_name%' => $entity_title));
        $triggers[$trigger_name]['gamipress_likebtn_get_dislike_'.$entity_name] = strtr(__('Get a dislike on a %entity_name%', 'likebtn-like-button'), array('%entity_name%' => $entity_title));
    }

    $triggers[$trigger_name]['gamipress_likebtn_like_custom_item'] = strtr(__('Like a %entity_name%', 'likebtn-like-button'), array('%entity_name%' => __('Custom Item', 'likebtn-like-button')));
    $triggers[$trigger_name]['gamipress_likebtn_dislike_custom_item'] = strtr(__('Dislike a %entity_name%', 'likebtn-like-button'), array('%entity_name%' => __('Custom Item', 'likebtn-like-button')));

    return $triggers;
}


/**
 * Extended meta data for event trigger logging.
 */
function likebtn_gamipress_log_event_trigger_meta_data($log_meta, $user_id, $trigger, $site_id, $args)
{
    $log_meta['entity_name'] = $args['entity_name'];
    $log_meta['entity_id'] = $args['entity_id'];

    if (isset($args['voter_id'])) {
        $log_meta['voter_id'] = $args['voter_id'];
    }

    if (is_numeric($args['entity_id']) && !in_array($args['entity_name'], likebtn_non_post_entity_types())) {
        $log_meta['post_id'] = $args['entity_id'];
    }
    
    if ($args['entity_name'] == LIKEBTN_ENTITY_COMMENT) {
        $post_id = likebtn_comment_post_id($args['entity_id']);
        if ($post_id) {
            $log_meta['post_id'] = $post_id;
        }
    }

    return $log_meta;
}

function likebtn_gamipress_log_extra_data_fields($fields, $object_id, $type, $object)
{
    $prefix = '_gamipress_';
    $entity_name = ct_get_object_meta( $object_id, $prefix . 'entity_name', true );
    $entity_id = ct_get_object_meta( $object_id, $prefix . 'entity_id', true );

    if ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM) {
        $fields[] = array(
            'name'  => __('Liked/Disliked Entity', 'likebtn-like-button'),
            'id'    => $prefix . 'entity_id',
            'type'  => 'text',
        );
    } else {
        $entity_title = _likebtn_get_entity_title($entity_name, $entity_id);
        $entity_url = _likebtn_get_entity_url($entity_name, $entity_id);

        if ($entity_title) {
            $fields[] = array(
                'name'  => __('Liked/Disliked Entity', 'likebtn-like-button'),
                'desc'  => '<a href="'.$entity_url.'" target="_blank">'.htmlentities($entity_title).'</a>',
                'id'    => $prefix . 'entity_name',
                'type'  => 'text',
            );
        }

        $voter_id = ct_get_object_meta( $object_id, $prefix . 'voter_id', true );
        if ($voter_id) {
            $voter_name = _likebtn_get_entity_title(LIKEBTN_ENTITY_USER, $voter_id);
            $voter_url  = _likebtn_get_entity_url(LIKEBTN_ENTITY_USER, $voter_id);
            $fields[] = array(
                'name'  => __('Voter ID', 'likebtn-like-button'),
                'desc'  => '<a href="'.$voter_url.'" target="_blank">'.htmlentities($voter_name).'</a>',
                'id'    => $prefix . 'voter_id',
                'type'  => 'text',
            );
        }
    }

    return $fields;
}

if (likebtn_is_gamipress_active()) {
    add_filter('gamipress_activity_triggers', 'likebtn_gamipress_activity_triggers');
    add_filter('likebtn_vote', 'likebtn_gamipress_process_vote');
    // add_filter('gamipress_trigger_get_user_id', 'likebtn_gamipress_trigger_get_user_id', 10, 3);
    add_filter('gamipress_log_event_trigger_meta_data', 'likebtn_gamipress_log_event_trigger_meta_data', 10, 5);
    add_filter('gamipress_log_extra_data_fields', 'likebtn_gamipress_log_extra_data_fields', 10, 4);
}
