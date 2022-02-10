<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Flatsome
 *
 * @version 3.13.1
 *
 * @slug flatsome
 *
 * @url http://flatsome.uxthemes.com/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "flatsome";

$name = "Flatsome Theme";

$available = true;

$tinvwl_integrations = is_array( $tinvwl_integrations ) ? $tinvwl_integrations : [];

$tinvwl_integrations[$slug] = array(
		'name' => $name,
		'available' => $available,
);

if (!tinv_get_option('integrations', $slug)) {
	return;
}

if (!$available) {
	return;
}

add_action('after_setup_theme', 'tinvwl_flatsome');

function tinvwl_flatsome()
{
	if (!class_exists('Flatsome_Default')) {
		return;
	}

	// Catalog mode
	if (!function_exists('tinvwl_flatsome_init')) {

		/**
		 * Run hooks after theme init.
		 */
		function tinvwl_flatsome_init()
		{

			if (get_theme_mod('catalog_mode')) {

				add_filter('tinvwl_allow_addtowishlist_single_product_summary', 'tinvwl_flatsome_woocommerce_catalog_mode', 10, 2);

				switch (tinv_get_option('add_to_wishlist', 'position')) {
					case 'before':
						add_action('woocommerce_single_variation', 'tinvwl_view_addto_html', 10);
						break;
					case 'after':
						add_action('woocommerce_single_variation', 'tinvwl_view_addto_html', 20);
						break;
				}

				add_action('woocommerce_single_variation', 'tinvwl_tinvwl_flatsome_woocommerce_catalog_mode_variable', 20);

			}
		}

		add_action('init', 'tinvwl_flatsome_init');
	}

	if (!function_exists('tinvwl_tinvwl_flatsome_woocommerce_catalog_mode_variable')) {

		/**
		 * Output variation hidden field.
		 *
		 */
		function tinvwl_tinvwl_flatsome_woocommerce_catalog_mode_variable()
		{
			echo '<input type="hidden" name="variation_id" class="variation_id" value="0" />';
		}
	}

	if (!function_exists('tinvwl_flatsome_woocommerce_catalog_mode')) {

		/**
		 * Output wishlist button for Flatsome catalog mode
		 *
		 * @param bool $allow allow output.
		 *
		 * @return bool
		 */
		function tinvwl_flatsome_woocommerce_catalog_mode($allow, $product)
		{
			if (!$product->is_type('variable')) {
				return true;
			}

			return $allow;
		}
	}

	// Header wishlist counter
	if (!function_exists('tinvwl_flatsome_header_wishlist')) {
		/**
		 * Header Wishlist element
		 *
		 * @param $elements
		 *
		 * @return mixed
		 */
		function tinvwl_flatsome_header_wishlist($elements)
		{
			$elements['wishlist'] = __('Wishlist', 'ti-woocommerce-wishlist');

			return $elements;
		}
	}
	add_filter('flatsome_header_element', 'tinvwl_flatsome_header_wishlist');

	if (!function_exists('tinvwl_flatsome_refresh_wishlist_partials')) {

		function tinvwl_flatsome_refresh_wishlist_partials(WP_Customize_Manager $wp_customize)
		{

			// Abort if selective refresh is not available.
			if (!isset($wp_customize->selective_refresh)) {
				return;
			}


			$wp_customize->selective_refresh->add_partial('header-wishlist', array(
					'selector' => '.header-wishlist-icon',
					'container_inclusive' => true,
					'settings' => array(
							'wishlist_title',
							'wishlist_icon',
							'wishlist_title',
							'wishlist_icon_style',
							'header_wishlist_label'
					),
					'render_callback' => tinvwl_flatsome_render_header_wishlist(),
			));

		}
	}
	add_action('customize_register', 'tinvwl_flatsome_refresh_wishlist_partials');


	$transport = 'postMessage';
	if (!isset($wp_customize->selective_refresh)) {
		$transport = 'refresh';
	}

	$image_url = get_template_directory_uri() . '/inc/admin/customizer/img/';
	Flatsome_Option::add_section('header_wishlist', array(
			'title' => __('Wishlist', 'ti-woocommerce-wishlist'),
			'panel' => 'header',
			'priority' => 110,
	));

	Flatsome_Option::add_field('option', array(
			'type' => 'select',
			'settings' => 'wishlist_icon',
			'label' => __('Wishlist Icon', 'ti-woocommerce-wishlist'),
			'transport' => $transport,
			'section' => 'header_wishlist',
			'default' => 'heart',
			'choices' => array(
					'' => "None",
					"heart" => "Heart (Default)",
					"heart-o" => "Heart Outline",
					"star" => "Star",
					"star-o" => "Star Outline",
					"menu" => "List",
					"pen-alt-fill" => "Pen",
			),
	));


	Flatsome_Option::add_field('option', array(
			'type' => 'radio-image',
			'settings' => 'wishlist_icon_style',
			'label' => __('Wishlist Icon Style', 'ti-woocommerce-wishlist'),
			'section' => 'header_wishlist',
			'transport' => $transport,
			'default' => '',
			'choices' => array(
					'' => $image_url . 'icon-plain.svg',
					'outline' => $image_url . 'icon-outline.svg',
					'fill' => $image_url . 'icon-fill.svg',
					'fill-round' => $image_url . 'icon-fill-round.svg',
					'outline-round' => $image_url . 'icon-outline-round.svg',
			),
	));


	Flatsome_Option::add_field('option', array(
			'type' => 'checkbox',
			'settings' => 'wishlist_title',
			'label' => __('Show Wishlist Title', 'ti-woocommerce-wishlist'),
		//'description' => __( 'This is the control description', 'ti-woocommerce-wishlist' ),
		//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'ti-woocommerce-wishlist' ),
			'section' => 'header_wishlist',
			'transport' => $transport,
			'default' => 1,
	));

	Flatsome_Option::add_field('option', array(
			'type' => 'text',
			'settings' => 'header_wishlist_label',
			'label' => __('Custom Title', 'ti-woocommerce-wishlist'),
			'section' => 'header_wishlist',
			'transport' => $transport,
			'default' => '',
	));


	function tinvwl_flatsome_render_header_wishlist()
	{
		$icon = get_theme_mod('wishlist_icon', flatsome_defaults('wishlist_icon'));
		$icon_style = get_theme_mod('wishlist_icon_style', flatsome_defaults('wishlist_icon_style'));
		ob_start();
		?>
		<li class="header-wishlist-icon">
			<?php if ($icon_style) { ?>
			<div class="header-button"><?php } ?>
				<a href="<?php echo tinv_url_wishlist_default(); ?>"
				   class="wishlist-link <?php echo get_flatsome_icon_class($icon_style, 'small'); ?>">
					<?php if (get_theme_mod('wishlist_title', flatsome_defaults('wishlist_title'))) { ?>
						<span class="hide-for-medium header-wishlist-title">
						  <?php if (get_theme_mod('header_wishlist_label', flatsome_defaults('header_wishlist_label'))) {
							  echo get_theme_mod('header_wishlist_label', flatsome_defaults('header_wishlist_label'));
						  } else {
							  _e('Wishlist', 'ti-woocommerce-wishlist');
						  } ?>
						</span>
					<?php } ?>
					<?php if ($icon) { ?>
						<i class="wishlist-icon icon-<?php echo $icon; ?>"
						   <?php if (TInvWL_Public_WishlistCounter::counter() > 0){ ?>data-icon-label="<?php echo TInvWL_Public_WishlistCounter::counter(); ?>" <?php } ?>>
						</i>
					<?php } ?>
				</a>
				<?php if ($icon_style) { ?> </div> <?php } ?>
		</li> <?php
		return ob_get_clean();
	}

	add_action('flatsome_header_elements', 'tinvwl_flatsome_hook_header_element');

	function tinvwl_flatsome_hook_header_element($value)
	{
		if ('wishlist' === $value) {
			echo tinvwl_flatsome_render_header_wishlist();
		}

	}

	// Add to wishlist button
	if (!function_exists('tinvwl_flatsome_product_wishlist_button')) {
		/**
		 * Add wishlist Button to Product Image
		 */
		function tinvwl_flatsome_product_wishlist_button()
		{
			$icon = get_theme_mod('wishlist_icon', 'heart');
			if (!$icon) {
				$icon = 'heart';
			}
			?>
			<div class="wishlist-icon">
				<button class="wishlist-button button is-outline circle icon"
						aria-label="<?php echo __('Wishlist', 'ti-woocommerce-wishlist'); ?>">
					<?php echo get_flatsome_icon('icon-' . $icon); ?>
				</button>
				<div class="wishlist-popup dark">
					<?php echo do_shortcode('[ti_wishlists_addtowishlist loop="yes"]'); ?>
				</div>
			</div>
			<?php
		}
	}
	add_action('flatsome_product_image_tools_top', 'tinvwl_flatsome_product_wishlist_button', 2);
	if (tinv_get_option('add_to_wishlist_catalog', 'show_in_loop')) {
		add_action('flatsome_product_box_tools_top', 'tinvwl_flatsome_product_wishlist_button', 2);
	}


	function tinv_add_to_wishlist_flatsome()
	{
		wp_add_inline_script('tinvwl', "
					jQuery(document).ready(function($){
						 $('body').on('click', '.wishlist-button', function (e) {
							$(this).addClass('loading');
							jQuery(this).parent().find('a.tinvwl_add_to_wishlist_button').click();
							e.preventDefault();
						});
					});

					jQuery('body').on('tinvwl_wishlist_mark_products tinvwl_modal_closed', function(e, data){
						jQuery('.wishlist-button').removeClass('wishlist-added loading');
					});

					jQuery('body').on('tinvwl_wishlist_product_marked', function(e, el,status){
						jQuery(el).closest('div.wishlist-icon').find('.wishlist-button').toggleClass('wishlist-added', status);
					});
			");
	}

	add_action('wp_enqueue_scripts', 'tinv_add_to_wishlist_flatsome', 100, 1);
}
