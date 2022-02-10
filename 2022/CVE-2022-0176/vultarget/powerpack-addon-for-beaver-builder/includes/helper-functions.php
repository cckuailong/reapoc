<?php

/**
 * Row and Column Extensions
 */
function pp_extensions()
{
    $extensions = array(
        'row'       => array(
            'separators'    => __('Separators', 'bb-powerpack-lite'),
        ),
    );

    return $extensions;
}

/**
 * Row templates categories
 */
function pp_row_templates_categories()
{
    $cats = array(
        'pp-contact-blocks'     => __('Contact Blocks', 'bb-powerpack-lite'),
        'pp-contact-forms'      => __('Contact Forms', 'bb-powerpack-lite'),
        'pp-call-to-action'     => __('Call To Action', 'bb-powerpack-lite'),
        'pp-hero'               => __('Hero', 'bb-powerpack-lite'),
        'pp-subscribe-forms'    => __('Subscribe Forms', 'bb-powerpack-lite'),
        'pp-content'            => __('Content', 'bb-powerpack-lite'),
        'pp-blog-posts'         => __('Blog Posts', 'bb-powerpack-lite'),
        'pp-lead-generation'    => __('Lead Generation', 'bb-powerpack-lite'),
        'pp-logos'              => __('Logos', 'bb-powerpack-lite'),
        'pp-team'               => __('Team', 'bb-powerpack-lite'),
        'pp-testimonials'       => __('Testimonials', 'bb-powerpack-lite'),
        'pp-features'           => __('Features', 'bb-powerpack-lite'),
        'pp-services'           => __('Services', 'bb-powerpack-lite'),
    );

    asort($cats);

    return $cats;
}

/**
 * Templates categories
 */
function pp_templates_categories( $type )
{
	$templates = pp_get_template_data( $type );
	$data = array();

	if ( is_array( $templates ) ) {
		foreach ( $templates as $cat => $info ) {
			$data[$cat] = array(
				'title'		=> $info['name'],
				'type'		=> $info['type'],
			);
			if ( isset( $info['count'] ) ) {
				$data[$cat]['count'] = $info['count'];
			}
		}

    	ksort($data);
	}

    return $data;
}

/**
 * Templates filters
 */
function pp_template_filters()
{
	$filters = array(
		'all'				=> __( 'All', 'bb-powerpack-lite' ),
		'home'				=> __( 'Home', 'bb-powerpack-lite' ),
		'about'				=> __( 'About', 'bb-powerpack-lite' ),
		'contact'			=> __( 'Contact', 'bb-powerpack-lite' ),
		'landing'			=> __( 'Landing', 'bb-powerpack-lite' ),
		'sales'				=> __( 'Sales', 'bb-powerpack-lite' ),
		'coming-soon'		=> __( 'Coming Soon', 'bb-powerpack-lite' ),
	);

	return $filters;
}

function pp_get_template_data( $type )
{
    $file = "https://wpbeaveraddons.com/page-templates/template-data/?show={$type}&export";
	$data = @file_get_contents( $file );
	if ( $data ) {
		$data = json_decode( $data, true );
	}

    BB_PowerPack_Admin_Settings::$templates = $data;
	BB_PowerPack_Admin_Settings::$templates_count[$type] = count( $data );

	return $data;
}

/**
 * Templates demo source URL
 */
function pp_templates_preview_src( $type = 'page', $category = '' )
{
    $url = 'https://wpbeaveraddons.com/page-templates/';

    $templates = BB_PowerPack_Admin_Settings::$templates;

    if ( ! is_array( $templates ) || ! count( $templates ) > 0 ) {
        $templates = pp_get_template_data( $type );
    }

	$data = array();

	if ( is_array( $templates ) ) {

		foreach ( $templates as $cat => $info ) {
			$data[$cat] = $info['slug'];
		}

	}

    if ( '' == $category ) {
        return $data;
    }

    if ( isset( $data[$category] ) ) {
        return $data[$category];
    }

    return $url;
}

function pp_get_template_screenshot_url( $type, $category, $mode = '' )
{
	$url = 'https://s3.amazonaws.com/ppbeaver/assets/400x400/';
	$scheme = 'color';

	if ( ( $type == 'page' || $scheme == 'color' ) && $mode == '' ) {
		return $url . $category . '.jpg';
	}

	if ( $mode == 'color' ) {
		return $url . $category . '.jpg';
	}

	if ( $mode == 'greyscale' ) {
		return $url . 'greyscale/' . $category . '.jpg';
	}

	return $url . $scheme . '/' . $category . '.jpg';
}

/**
 * Hex to Rgba
 */
function pp_hex2rgba( $hex, $opacity = 1 )
{
	if ( false !== strpos( $hex, 'rgb' ) ) {
		return $hex;
	}
	
	$hex = str_replace( '#', '', $hex );

	if ( strlen($hex) == 3 ) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$opacity = ( $opacity > 1 ) ? ( $opacity / 100 ) : $opacity;
	$rgba = array($r, $g, $b, $opacity);

	return 'rgba(' . implode(', ', $rgba) . ')';
}

/**
 * Get color value hex or rgba
 */
function pp_get_color_value( $color )
{
    if ( $color == '' || ! $color ) {
        return;
    }
    if ( false === strpos( $color, 'rgb' ) ) {
        return '#' . $color;
    } else {
        return $color;
    }
}

/**
 * Returns long day format.
 *
 * @since 1.2.2
 * @param string $day
 * @return mixed
 */
function pp_long_day_format( $day = '' )
{
	$days = array(
		'Sunday'        => __('Sunday', 'bb-powerpack-lite'),
		'Monday'        => __('Monday', 'bb-powerpack-lite'),
		'Tuesday'       => __('Tuesday', 'bb-powerpack-lite'),
		'Wednesday'     => __('Wednesday', 'bb-powerpack-lite'),
		'Thursday'      => __('Thursday', 'bb-powerpack-lite'),
		'Friday'        => __('Friday', 'bb-powerpack-lite'),
		'Saturday'      => __('Saturday', 'bb-powerpack-lite'),
	);

	if ( isset( $days[$day] ) ) {
		return $days[$day];
	}
	else {
		return $days;
	}
}

/**
 * Returns short day format.
 *
 * @since 1.2.2
 * @param string $day
 * @return string
 */
function pp_short_day_format( $day )
{
	$days = array(
		'Sunday'        => __('Sun', 'bb-powerpack-lite'),
		'Monday'        => __('Mon', 'bb-powerpack-lite'),
		'Tuesday'       => __('Tue', 'bb-powerpack-lite'),
		'Wednesday'     => __('Wed', 'bb-powerpack-lite'),
		'Thursday'      => __('Thu', 'bb-powerpack-lite'),
		'Friday'        => __('Fri', 'bb-powerpack-lite'),
		'Saturday'      => __('Sat', 'bb-powerpack-lite'),
	);

	if ( isset( $days[$day] ) ) {
		return $days[$day];
	}
}

/**
 * Returns user agent.
 *
 * @since 1.2.3
 * @return string
 */
function pp_get_user_agent()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];

	if (stripos( $user_agent, 'Chrome') !== false)
	{
	    return 'chrome';
	}
	elseif (stripos( $user_agent, 'Safari') !== false)
	{
	   return 'safari';
	}
	elseif (stripos( $user_agent, 'Firefox') !== false)
	{
	   return 'firefox';
	}
	elseif (stripos( $user_agent, 'MSIE') !== false)
	{
	   return 'ie';
	}
	elseif (stripos( $user_agent, 'Trident/7.0; rv:11.0' ) !== false)
	{
	   return 'ie';
	}

	return;
}

/**
 * Returns badges data.
 *
 * @since 1.0.8
 * @param int $number
 * @return array
 */
function pp_modules_badges( $number = '' )
{
    $badges = array(
        1 => __('Unique & Popular', 'bb-powerpack-lite'),
        2 => __('Unique', 'bb-powerpack-lite'),
        3 => __('Popular', 'bb-powerpack-lite'),
        4 => __('Coming Soon', 'bb-powerpack-lite')
    );

    if ( ! $number || empty( $number ) ) {
        return $badges;
    }

    $number = absint( $number );

    if ( isset( $badges[$number] ) ) {
        return $badges[$number];
    }
}

function pp_get_modules_categories( $cat = '' )
{
	$admin_label = pp_get_admin_label();

	$cats = array(
		'creative'		=> sprintf(__('Creative Modules - %s', 'bb-powerpack-lite'), $admin_label),
		'content'		=> sprintf(__('Content Modules - %s', 'bb-powerpack-lite'), $admin_label),
		'lead_gen'		=> sprintf(__('Lead Generation Modules - %s', 'bb-powerpack-lite'), $admin_label),
		'form_style'	=> sprintf(__('Form Styler Modules - %s', 'bb-powerpack-lite'), $admin_label),
	);

	if ( empty( $cat ) ) {
		return $cats;
	}

	if ( isset( $cats[$cat] ) ) {
		return $cats[$cat];
	} else {
		return $cat;
	}
}

/**
 * Returns modules category name for Beaver Builder 2.0 compatibility.
 *
 * @since 1.2
 * @return string
 */
function pp_get_modules_cat( $cat )
{
	return class_exists( 'FLBuilderUIContentPanel' ) ? pp_get_modules_categories( $cat ) : BB_POWERPACK_CAT;
}

/**
 * Returns group name for BB 2.x.
 *
 * @since 1.2
 * @return string
 */
function pp_get_modules_group()
{
	$group_name = 'PowerPack ' . __('Modules', 'bb-powerpack-lite');

	return $group_name;
}

/**
 * Returns admin label for PowerPack settings.
 *
 * @since 1.2.3
 * @return string
 */
function pp_get_admin_label()
{
	$admin_label = 'PowerPack';

	return $admin_label;
}

/**
 * Returns Facebook App ID stored in options.
 *
 * @return mixed
 */
function pp_get_fb_app_id()
{
	$app_id = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_fb_app_id' );

	return $app_id;
}

/**
 * Build the URL of Facebook SDK.
 *
 * @return string
 */
function pp_get_fb_sdk_url()
{
	$app_id = pp_get_fb_app_id();
	
	if ( $app_id && ! empty( $app_id ) ) {
		return sprintf( 'https://connect.facebook.net/%s/sdk.js#xfbml=1&version=v2.12&appId=%s', get_locale(), $app_id );
	}

	return sprintf( 'https://connect.facebook.net/%s/sdk.js#xfbml=1&version=v2.12', get_locale() );
}

function pp_get_fb_app_settings_url()
{
	$app_id = pp_get_fb_app_id();

	if ( $app_id ) {
		return sprintf( 'https://developers.facebook.com/apps/%d/settings/', $app_id );
	} else {
		return 'https://developers.facebook.com/apps/';
	}
}

function pp_get_fb_module_desc()
{
	$app_id = pp_get_fb_app_id();

	if ( ! $app_id ) {
		// translators: %s: Setting Page link
		return sprintf( __( 'You can set your Facebook App ID in the <a href="%s" target="_blank">Integrations Settings</a>', 'bb-powerpack-lite' ), BB_PowerPack_Admin_Settings::get_form_action() );
	} else {
		// translators: %1$s: app_id, %2$s: Setting Page link.
		return sprintf( __( 'You are connected to Facebook App %1$s, <a href="%2$s" target="_blank">Change App</a>', 'bb-powerpack-lite' ), $app_id, BB_PowerPack_Admin_Settings::get_form_action() );
	}
}

function pp_get_image_alt( $img_id = false, $default = '' ) {
	if ( ! $img_id || ! absint( $img_id ) ) {
		return;
	}
	if ( ! class_exists( 'FLBuilderPhoto' ) ) {
		return;
	}
	
	$img_id = absint( $img_id );
	$attachment_data = FLBuilderPhoto::get_attachment_data( $img_id );
	$image_alt = ( ! empty( $default ) ) ? $default : '';
	
	if ( is_object( $attachment_data ) ) {
		$image_alt = $attachment_data->alt;
		if ( empty( $image_alt ) ) {
			$image_alt = $attachment_data->caption;
			if ( empty( $image_alt ) ) {
				$image_alt = $attachment_data->title;
			}
		}
	}

	return $image_alt;
}