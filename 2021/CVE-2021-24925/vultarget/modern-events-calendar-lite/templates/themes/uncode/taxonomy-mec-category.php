<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * The Template for displaying mec-category taxonomy events
 * 
 * @author Webnus <info@webnus.biz>
 * @package MEC/Templates
 * @version 1.0.0
 */

get_header();

/**
 * DATA COLLECTION - START
 *
 */

/** Init variables **/
$limit_width = $limit_content_width = $the_content = $main_content = $layout = $bg_color = $sidebar_style = $sidebar_bg_color = $sidebar = $sidebar_size = $sidebar_sticky = $sidebar_padding = $sidebar_inner_padding = $sidebar_content = $title_content = $media_content = $navigation_content = $page_custom_width = $row_classes = $main_classes = $footer_content = $footer_classes = '';
$with_builder = false;

$post_type = 'post';

/** Get general datas **/
if (isset($metabox_data['_uncode_specific_style'][0]) && $metabox_data['_uncode_specific_style'][0] !== '') {
	$style = $metabox_data['_uncode_specific_style'][0];
	if (isset($metabox_data['_uncode_specific_bg_color'][0]) && $metabox_data['_uncode_specific_bg_color'][0] !== '') {
		$bg_color = $metabox_data['_uncode_specific_bg_color'][0];
	}
} else {
	$style = ot_get_option('_uncode_general_style');
	if (isset($metabox_data['_uncode_specific_bg_color'][0]) && $metabox_data['_uncode_specific_bg_color'][0] !== '') {
		$bg_color = $metabox_data['_uncode_specific_bg_color'][0];
	} else $bg_color = ot_get_option('_uncode_general_bg_color');
}
$bg_color = ($bg_color == '') ? ' style-'.$style.'-bg' : ' style-'.$bg_color.'-bg';


/** Get page width info **/
$boxed = ot_get_option('_uncode_boxed');

if ($boxed !== 'on')
{
	$page_content_full = (isset($metabox_data['_uncode_specific_layout_width'][0])) ? $metabox_data['_uncode_specific_layout_width'][0] : '';
	if ($page_content_full === '')
	{

		/** Use generic page width **/
		$generic_content_full = ot_get_option('_uncode_' . $post_type . '_layout_width');
		if ($generic_content_full === '')
		{
			$main_content_full = ot_get_option('_uncode_body_full');
			if ($main_content_full === '' || $main_content_full === 'off') $limit_content_width = ' limit-width';
		}
		else
		{
			if ($generic_content_full === 'limit')
			{
				$generic_custom_width = ot_get_option('_uncode_' . $post_type . '_layout_width_custom');
				if ($generic_custom_width[1] === 'px')
				{
					$generic_custom_width[0] = 12 * round(($generic_custom_width[0]) / 12);
				}
				if (is_array($generic_custom_width) && !empty($generic_custom_width))
				{
					$page_custom_width = ' style="max-width: ' . implode("", $generic_custom_width) . '; margin: auto;"';
				}
			}
		}
	}
	else
	{

		/** Override page width **/
		if ($page_content_full === 'limit')
		{
			$limit_content_width = ' limit-width';
			$page_custom_width = (isset($metabox_data['_uncode_specific_layout_width_custom'][0])) ? unserialize($metabox_data['_uncode_specific_layout_width_custom'][0]) : '';
			if (is_array($page_custom_width) && !empty($page_custom_width) && $page_custom_width[0] !== '')
			{
				if ($page_custom_width[1] === 'px')
				{
					$page_custom_width[0] = 12 * round(($page_custom_width[0]) / 12);
				}
				$page_custom_width = ' style="max-width: ' . implode("", $page_custom_width) . '; margin: auto;"';
			} else $page_custom_width = '';
		}
	}
}

$media = get_post_meta($post->ID, '_uncode_featured_media', 1);
$media_display = get_post_meta($post->ID, '_uncode_featured_media_display', 1);
$featured_image = get_post_thumbnail_id($post->ID);
if ($featured_image === '') $featured_image = $media;

/** Collect header data **/
if (isset($metabox_data['_uncode_header_type'][0]) && $metabox_data['_uncode_header_type'][0] !== '')
{
	$page_header_type = $metabox_data['_uncode_header_type'][0];
	if ($page_header_type !== 'none')
	{
		$meta_data = uncode_get_specific_header_data($metabox_data, $post_type, $featured_image);
		$metabox_data = $meta_data['meta'];
		$show_title = $meta_data['show_title'];
	}
}
else
{
	$page_header_type = ot_get_option('_uncode_' . $post_type . '_header');
	if ($page_header_type !== '' && $page_header_type !== 'none')
	{
		$metabox_data['_uncode_header_type'] = array($page_header_type);
		$meta_data = uncode_get_general_header_data($metabox_data, $post_type, $featured_image);
		$metabox_data = $meta_data['meta'];
		if ($meta_data['media']) $metabox_data['_uncode_header_background'] = array(array('background-image' => $meta_data['media']));
		$show_title = $meta_data['show_title'];
	}
}

/** Get layout info **/
if (isset($metabox_data['_uncode_active_sidebar'][0]) && $metabox_data['_uncode_active_sidebar'][0] !== '')
{
	if ($metabox_data['_uncode_active_sidebar'][0] !== 'off')
	{
		$layout = (isset($metabox_data['_uncode_sidebar_position'][0])) ? $metabox_data['_uncode_sidebar_position'][0] : '';
		$sidebar = (isset($metabox_data['_uncode_sidebar'][0])) ? $metabox_data['_uncode_sidebar'][0] : '';
		$sidebar_size = (isset($metabox_data['_uncode_sidebar_size'][0])) ? $metabox_data['_uncode_sidebar_size'][0] : 4;
		$sidebar_sticky = (isset($metabox_data['_uncode_sidebar_sticky'][0]) && $metabox_data['_uncode_sidebar_sticky'][0] === 'on') ? ' sticky-element' : '';
		$sidebar_fill = (isset($metabox_data['_uncode_sidebar_fill'][0])) ? $metabox_data['_uncode_sidebar_fill'][0] : '';
		$sidebar_style = (isset($metabox_data['_uncode_sidebar_style'][0])) ? $metabox_data['_uncode_sidebar_style'][0] : $style;
		$sidebar_bg_color = (isset($metabox_data['_uncode_sidebar_bgcolor'][0]) && $metabox_data['_uncode_sidebar_bgcolor'][0] !== '') ? ' style-' . $metabox_data['_uncode_sidebar_bgcolor'][0] . '-bg' : '';
	}
}
else
{
	$activate_sidebar = ot_get_option('_uncode_' . $post_type . '_activate_sidebar');
	if ($activate_sidebar !== 'off')
	{
		$layout = ot_get_option('_uncode_' . $post_type . '_sidebar_position');
		if ($layout === '') $layout = 'sidebar_right';
		$sidebar = ot_get_option('_uncode_' . $post_type . '_sidebar');
		$sidebar_style = ot_get_option('_uncode_' . $post_type . '_sidebar_style');
		$sidebar_size = ot_get_option('_uncode_' . $post_type . '_sidebar_size');
		$sidebar_sticky = ot_get_option('_uncode_' . $post_type . '_sidebar_sticky');
		$sidebar_sticky = ($sidebar_sticky === 'on') ? ' sticky-element' : '';
		$sidebar_fill = ot_get_option('_uncode_' . $post_type . '_sidebar_fill');
		$sidebar_bg_color = ot_get_option('_uncode_' . $post_type . '_sidebar_bgcolor');
		$sidebar_bg_color = ($sidebar_bg_color !== '') ? ' style-' . $sidebar_bg_color . '-bg' : '';
	}
}
if ($sidebar_style === '') $sidebar_style = $style;

/** Get breadcrumb info **/
$generic_breadcrumb = ot_get_option('_uncode_' . $post_type . '_breadcrumb');
$page_breadcrumb = (isset($metabox_data['_uncode_specific_breadcrumb'][0])) ? $metabox_data['_uncode_specific_breadcrumb'][0] : '';
if ($page_breadcrumb === '')
{
	$breadcrumb_align = ot_get_option('_uncode_' . $post_type . '_breadcrumb_align');
	$show_breadcrumb = ($generic_breadcrumb === 'off') ? false : true;
}
else
{
	$breadcrumb_align = (isset($metabox_data['_uncode_specific_breadcrumb_align'][0])) ? $metabox_data['_uncode_specific_breadcrumb_align'][0] : '';
	$show_breadcrumb = ($page_breadcrumb === 'off') ? false : true;
}

/** Get title info **/
$generic_show_title = ot_get_option('_uncode_' . $post_type . '_title');
$page_show_title = (isset($metabox_data['_uncode_specific_title'][0])) ? $metabox_data['_uncode_specific_title'][0] : '';
if ($page_show_title === '')
{
	$show_title = ($generic_show_title === 'off') ? false : true;
}
else
{
	$show_title = ($page_show_title === 'off') ? false : true;
}

/** Get media info **/
$generic_show_media = ot_get_option('_uncode_' . $post_type . '_media');
$page_show_media = (isset($metabox_data['_uncode_specific_media'][0])) ? $metabox_data['_uncode_specific_media'][0] : '';
if ($page_show_media === '')
{
	$show_media = ($generic_show_media === 'off') ? false : true;
}
else
{
	$show_media = ($page_show_media === 'off') ? false : true;
}

/**
 * DATA COLLECTION - END
 *
 */



	/** Build header **/
	if ($page_header_type !== '' && $page_header_type !== 'none')
	{
		$page_header = new unheader($metabox_data, $post->post_title);

		$header_html = $page_header->html;
		if ($header_html !== '') {
			echo '<div id="page-header">';
			echo do_shortcode( shortcode_unautop( $page_header->html ) );
			echo '</div>';
		}

		if (!empty($page_header->poster_id) && $page_header->poster_id !== false && $media !== '')
		{
			$media = $page_header->poster_id;
		}
	}
	echo '<script type="text/javascript">UNCODE.initHeader();</script>';
	/** Build breadcrumb **/

	if ($show_breadcrumb && !is_front_page() && !is_home())
	{
		if ($breadcrumb_align !== '') $breadcrumb_align = ' text-' . $breadcrumb_align;
		else
		{
			$breadcrumb_align = ' text-right';
		}
		$content_breadcrumb = uncode_breadcrumbs();
		$breadcrumb_title = '<h5 class="breadcrumb-title">' . get_the_title() . '</h5>';
		echo uncode_get_row_template(($breadcrumb_align === 'left' ? $content_breadcrumb . $breadcrumb_title : $breadcrumb_title . $content_breadcrumb) , '', ($page_custom_width !== '' ? ' limit-width' : $limit_content_width), $style, ' row-breadcrumb row-breadcrumb-' . $style . $breadcrumb_align, 'half', true, 'half');
	}

	/** Build media **/

	if ($media !== '' && !$with_builder && $show_media && !post_password_required())
	{
		if ($layout === 'sidebar_right' || $layout === 'sidebar_left')
		{
			$media_size = 12 - $sidebar_size;
		}
		else $media_size = 12;

		$media_array = explode(',', $media);
		$media_counter = count($media_array);
		$rand_id = big_rand();
		if ($media_counter === 0) $media_array = array(
			$media
		);

		if ($media_display === 'isotope') $media_content.=
			'<div id="gallery-' . $rand_id . '" class="isotope-system post-media">
				<div class="isotope-wrapper half-gutter">
	      	<div class="isotope-container isotope-layout style-masonry" data-type="masonry" data-layout="masonry" data-lg="1000" data-md="600" data-sm="480">';

		foreach ($media_array as $key => $value)
		{
			if ($media_display === 'carousel') $value = $media;
			$block_data = array();
			$block_data['media_id'] = $value;
			$block_data['classes'] = array(
				'tmb'
			);
			$block_data['text_padding'] = 'no-block-padding';
			if ($media_display === 'isotope')
			{
				$block_data['single_width'] = 4;
				$block_data['classes'][] = 'tmb-iso-w4';
			}
			else $block_data['single_width'] = $media_size;
			$block_data['single_style'] = $style;
			$block_data['single_text'] = 'under';
			$block_data['classes'][] = 'tmb-' . $style;
			if ($media_display === 'isotope')
			{
				$block_data['classes'][] = 'tmb-overlay-anim';
				$block_data['classes'][] = 'tmb-overlay-text-anim';
				$block_data['single_icon'] = 'fa fa-plus2';
				$block_data['overlay_color'] = ($style == 'light') ? 'style-black-bg' : 'style-white-bg';
				$block_data['overlay_opacity'] = '20';
				$lightbox_classes = array();
				$lightbox_classes['data-noarr'] = false;
			}
			else
			{
				$lightbox_classes = false;
				$block_data['link_class'] = 'inactive-link';
				$block_data['link'] = '#';
			}
			$block_data['title_classes'] = array();
			$block_data['tmb_data'] = array();
			$block_layout['media'] = array();
			$block_layout['icon'] = array();
			$media_html = uncode_create_single_block($block_data, $rand_id, 'masonry', $block_layout, $lightbox_classes, false, true);
			if ($media_display !== 'isotope') $media_content.= '<div class="post-media">' . $media_html . '</div>';
			else
			{
				$media_content.= $media_html;
			}
			if ($media_display === 'carousel') break;
		}

		if ($media_display === 'isotope') $media_content.=
					'</div>
				</div>
			</div>';
	}

	/** Build title **/

	if ($show_title)
	{
		$title_content .= apply_filters('uncode_before_body_title', '');
		$title_content .= '<div class="post-title-wrapper"><h1 class="post-title">' . get_the_title() . '</h1>';
		$title_content .= uncode_post_info() . '</div>';
		$title_content .= apply_filters('uncode_after_body_title', '');
	}

?>
    
	
		<section id="<?php echo apply_filters('mec_category_page_html_id', 'main-content'); ?>" class="<?php echo apply_filters('mec_category_page_html_class', 'container'); ?>">
	
		<?php do_action('mec_before_main_content'); ?>

			<?php do_action('mec_before_events_loop'); ?>

				<?php $MEC = MEC::instance(); echo $MEC->category(); ?>

			<?php do_action('mec_after_events_loop'); ?>

        </section>

    <?php do_action('mec_after_main_content'); ?>

<?php get_footer('mec');