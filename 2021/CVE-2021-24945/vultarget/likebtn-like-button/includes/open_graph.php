<?php
/**
 * Add og meta tags
 */

function _likebtn_og_init()
{
	// Post type can not be obtained here
	add_filter('language_attributes', 'likebtn_add_og_attribute', 1);
	add_action('wp_head', 'likebtn_add_og_elements', 1);
}

function likebtn_add_og_elements()
{
	if (_likebtn_og_enabled()) {
		remove_action('wp_head', 'jetpack_og_tags');
	} else {
		return;
	}

	$metas = array();

	$metas['og:site_name'] = strip_tags(get_bloginfo('name'));
	$metas['og:locale'] = strtolower(str_replace('-', '_', get_bloginfo('language')));
	$metas['og:type'] = _likebtn_og_get_type();
	
	the_post();
	$metas['og:title'] = _likebtn_og_get_title();
	$metas['og:url'] = get_permalink();
	$metas['og:description'] = _likebtn_og_get_description();
	$metas['og:image'] = _likebtn_og_add_image();
	rewind_posts();
	
	_likebtn_og_output($metas);
}
	
function _likebtn_og_enabled()
{
	$entity_name = _likebtn_used_entity_name(get_post_type());

	if (is_singular() && get_option('likebtn_og_' . $entity_name) == '1') {
		return true;
	} else {
		return false;
	}
}

function likebtn_add_og_attribute($attr_str) {
	global $wpseo_og;
	if (_likebtn_og_enabled()) {
		if ($wpseo_og) {
			remove_filter('language_attributes', array($wpseo_og, 'add_opengraph_namespace'), 11);
		}
		return ' prefix="og: http://ogp.me/ns#" '.$attr_str;
	}
	return $attr_str;
}
	
function _likebtn_og_get_title()
{
	$title = '';
	if (function_exists('aioseop_get_version')) {
		$title = trim(get_post_meta(get_the_ID(), '_aioseop_title', true));
	} else if (function_exists('wpseo_get_value')) {
		$title = wpseo_get_value('title', get_the_ID() );
	}
	return empty($title) ? get_the_title() : $title;
}
	
function _likebtn_og_get_type()
{
	if (is_front_page()){
		return 'website';
	} else if(is_home()) {
		return 'blog';
	} else {
		return 'article';
	}
}

function _likebtn_og_get_description()
{
	$description = null;
	if (function_exists('aioseop_get_version')) {
		$description = trim(get_post_meta(get_the_ID(), '_aioseop_description', true));
	} else if (function_exists('wpseo_get_value')) {
		$description = wpseo_get_value('metadesc', get_the_ID());
	}
	if (empty($description) && get_the_ID()) {
		$description = strip_tags(get_the_excerpt());
	}
	return $description;
}

function _likebtn_og_add_image()
{	
	if (has_post_thumbnail()) {				
		return wp_get_attachment_url(get_post_thumbnail_id());
	} else {
		$attachment = get_posts(array( 'numberposts' => 1, 'post_type'=>'attachment', 'post_parent' => get_the_ID() ));
		if ($attachment) {
			return wp_get_attachment_thumb_url($attachment[0]->ID);
		} else {					
			return '';
		}
		wp_reset_query();
	}
}

function _likebtn_og_output($metas)
{
	echo "<!-- Open Graph Meta Data by LikeBtn.com plugin-->\n";
	foreach ($metas as $property => $content) { 
		$content = is_array($content) ? $content : array($content);
		foreach ($content as $content_single) {
			echo '<meta property="' . $property . '" content="' . esc_attr(trim($content_single)) . '" />' . "\n";
		} 
	}
	echo "<!-- /Open Graph Meta Data -->\n";
}
