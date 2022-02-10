<?php
/**
 * Posts columns
 */

// Column names
define('LIKEBTN_COLUMN_LIKES', 'likebtn-likes');
define('LIKEBTN_COLUMN_DISLIKES', 'likebtn-dislikes');
define('LIKEBTN_COLUMN_LMD', 'likebtn-lmd');

// Set meta column hooks
function likebtn_meta_columns_setup_hooks()
{
	_likebtn_set_post_type_hooks();

	//add_action('restrict_manage_posts', 'likebtn_posts_filter_dropdown');
	add_filter('request', 'likebtn_column_sort_orderby');
}

add_action('admin_init', 'likebtn_meta_columns_setup_hooks');

// Set the hooks for the post_types
function _likebtn_set_post_type_hooks()
{
	$post_types = _likebtn_get_entities(true, false, false, 'keys');

	if (is_array($post_types) && !empty($post_types)) {
		foreach ($post_types as $pt) {
			if (_likebtn_is_metabox_hidden($pt) === false) {
				add_filter('manage_' . $pt . '_posts_columns', 'likebtn_column_heading', 10, 1);
				add_action('manage_' . $pt . '_posts_custom_column', 'likebtn_column_content', 10, 2);
				add_action('manage_edit-' . $pt . '_sortable_columns', 'likebtn_column_sort', 10, 2);

				/*
				 * Use the `get_user_option_{$option}` filter to change the output of the get_user_option
				 * function for the `manage{$screen}columnshidden` option, which is based on the current
				 * admin screen. The admin screen we want to target is the `edit-{$post_type}` screen.
				 */
				$filter = sprintf('get_user_option_%s', sprintf('manage%scolumnshidden', 'edit-' . $pt));
				add_filter($filter, 'likebtn_column_hidden', 10, 3);
			}
		}
	}
}

// Test whether the metabox should be hidden either by choice of the admin or because
// the post type is not a public post type
function _likebtn_is_metabox_hidden($post_type = null) {

	return false;
}

function likebtn_column_heading($columns) {
	if (_likebtn_is_metabox_hidden()) {
		return $columns;
	}

	return array_merge($columns, array(
		LIKEBTN_COLUMN_LIKES => __('Likes', 'likebtn-like-button'),
		LIKEBTN_COLUMN_DISLIKES => __('Dislikes', 'likebtn-like-button'),
		LIKEBTN_COLUMN_LMD => __('Likes minus dislikes', 'likebtn-like-button'),
	));
}

function likebtn_column_content($column_name, $post_id)
{
	if (_likebtn_is_metabox_hidden()) {
		return;
	}

	switch ($column_name) {
		case LIKEBTN_COLUMN_LIKES:
			echo (int)get_post_meta($post_id, LIKEBTN_META_KEY_LIKES, true);
			break;
		case LIKEBTN_COLUMN_DISLIKES:
			echo (int)get_post_meta($post_id, LIKEBTN_META_KEY_DISLIKES, true);
			break;
		case LIKEBTN_COLUMN_LMD:
			echo (int)get_post_meta($post_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, true);
			break;
	}
}

// Indicate which of the columns are sortable.
function likebtn_column_sort($columns) {
	if (_likebtn_is_metabox_hidden()) {
		return $columns;
	}

	$columns[LIKEBTN_COLUMN_LIKES]    = LIKEBTN_COLUMN_LIKES;
	$columns[LIKEBTN_COLUMN_DISLIKES] = LIKEBTN_COLUMN_DISLIKES;
	$columns[LIKEBTN_COLUMN_LMD]      = LIKEBTN_COLUMN_LMD;

	return $columns;
}

// Hide Likes minus dislikes by default
function likebtn_column_hidden($result, $option, $user) {
	global $wpdb;

	$prefix = $wpdb->get_blog_prefix();
	if (!$user->has_prop($prefix . $option) && !$user->has_prop($option)) {

		if (!is_array($result)) {
			$result = array();
		}

		array_push($result, LIKEBTN_COLUMN_LMD);
	}

	return $result;
}

// Modify the query based on the orderby variable in $_GET
function likebtn_column_sort_orderby($vars)
{
	if (isset($vars['orderby'])) {
		$vars = array_merge($vars,  likebtn_filter_order_by($vars['orderby']));
	}

	return $vars;
}

// Returning filters when $order_by is matched in the if-statement
function likebtn_filter_order_by($order_by) {
	switch ($order_by) {
		case LIKEBTN_COLUMN_LIKES:
			return array(
				//'meta_key' => LIKEBTN_META_KEY_LIKES,
				'orderby'  => 'meta_value_num',
				'meta_query' => array(
			        'relation' => 'OR',
			         array(
			            'key' => LIKEBTN_META_KEY_LIKES,
			            'compare' => 'NOT EXISTS',
			            'type' => 'numeric'
			         ),
			         array(
			            'key' => LIKEBTN_META_KEY_LIKES,
			            'compare' => 'EXISTS',
			            'type' => 'numeric'
			         )
			     )
			);
			break;
		case LIKEBTN_COLUMN_DISLIKES:
			return  array(
				//'meta_key' => LIKEBTN_META_KEY_DISLIKES,
				'orderby'  => 'meta_value',
				'meta_query' => array(
			        'relation' => 'OR',
			         array(
			            'key' => LIKEBTN_META_KEY_DISLIKES,
			            'compare' => 'NOT EXISTS',
			            'type' => 'numeric'
			         ),
			         array(
			            'key' => LIKEBTN_META_KEY_DISLIKES,
			            'compare' => 'EXISTS',
			            'type' => 'numeric'
			         )
			     )
			);
			break;
		case LIKEBTN_COLUMN_LMD:
			return array(
				//'meta_key' => LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES,
				'orderby'  => 'meta_value',
				'meta_query' => array(
			        'relation' => 'OR',
			         array(
			            'key' => LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES,
			            'compare' => 'NOT EXISTS',
			            'type' => 'numeric'
			         ),
			         array(
			            'key' => LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES,
			            'compare' => 'EXISTS',
			            'type' => 'numeric'
			         )
			     )
			);
			break;
	}

	return array();
}
