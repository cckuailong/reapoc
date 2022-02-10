<?php

add_action( 'customize_register', 'futurio_extra_theme_customize_register_woo', 15 );

function futurio_extra_theme_customize_register_woo( $wp_customize ) {
	// relocating default WooCommerce sections
	$wp_customize->get_section( 'woocommerce_store_notice' )->panel		 = 'woo_section_main';
	$wp_customize->get_section( 'woocommerce_product_catalog' )->panel	 = 'woo_section_main';
	$wp_customize->get_section( 'woocommerce_product_images' )->panel	 = 'woo_section_main';
	$wp_customize->get_section( 'woocommerce_checkout' )->panel			 = 'woo_section_main';
}


add_action( 'after_setup_theme', 'futurio_extra_images_action', 15 );

function futurio_extra_images_action() {

	if ( get_theme_mod( 'woo_gallery_zoom', 1 ) == 0 ) {
		remove_theme_support( 'wc-product-gallery-zoom' );
	}
	if ( get_theme_mod( 'woo_gallery_lightbox', 1 ) == 0 ) {
		remove_theme_support( 'wc-product-gallery-lightbox' );
	}
	if ( get_theme_mod( 'woo_gallery_slider', 1 ) == 0 ) {
		remove_theme_support( 'wc-product-gallery-slider' );
	}
  // Remove related products output
  if ( get_theme_mod( 'woo_remove_related', 1 ) == 0 ) {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
  }
}

add_filter( 'loop_shop_per_page', 'futurio_extra_new_loop_shop_per_page', 20 );

function futurio_extra_new_loop_shop_per_page( $cols ) {
	// $cols contains the current number of products per page based on the value stored on Options -> Reading
	// Return the number of products you wanna show per page.
	$cols = absint( get_theme_mod( 'archive_number_products', 24 ) );
	return $cols;
}

add_filter( 'loop_shop_columns', 'futurio_extra_loop_columns' );

if ( !function_exists( 'futurio_extra_loop_columns' ) ) {

	function futurio_extra_loop_columns() {
		return absint( get_theme_mod( 'archive_number_columns', 4 ) );
	}

}

 
if ( !function_exists( 'futurio_extra_product_categories' ) ) {

	function futurio_extra_product_categories() {

		if ( get_theme_mod( 'woo_archive_product_categories', 1 ) == 1 ) {
			global $product;

			$id		 = $product->get_id();
			$cat_ids = $product->get_category_ids();

			// if product has categories, concatenate cart item name with them
			if ( $cat_ids ) {
				$name = wc_get_product_category_list( $id, ',', '<div class="archive-product-categories text-center">', '</div>' );
			}

			echo $name;
		}
	}

	add_action( 'woocommerce_after_shop_loop_item_title', 'futurio_extra_product_categories', 10 );
}

add_action( 'woocommerce_before_single_product', 'futurio_extra_prev_next_product', 10 );
 
function futurio_extra_prev_next_product() {
  if ( get_theme_mod('woo_prev_next_nav', 1) == 0 || ! is_product() ) {
    return;
  }
  global $woocommerce, $product;
  $excluted_terms = '';
  $in_category = false;
  if( get_theme_mod( 'shop-nav-in-category', 0 ) == 1 ) {
      $in_category = true;
  }
  
  $prev_post = get_previous_post( $in_category, $excluted_terms, 'product_cat' );
  $next_post = get_next_post( $in_category, $excluted_terms, 'product_cat' );
  
  $prev_post_content = ( $prev_post != '' ) ? '<div class="prev-product"><h5>' . $prev_post->post_title . '</h5>' . get_the_post_thumbnail( $prev_post->ID, 'shop_thumbnail' ) . '</div>' : '';
  $next_post_content = ( $next_post != '' ) ? '<div class="next-product"><h5>' . $next_post->post_title . '</h5>' . get_the_post_thumbnail( $next_post->ID, 'shop_thumbnail' ) . '</div>' : '';
  
  $prev = get_previous_post_link( '%link', '<span class="fa fa-chevron-left"></span>' . $prev_post_content, $in_category, $excluted_terms, 'product_cat' );
  $next = get_next_post_link( '%link', '<span class="fa fa-chevron-right"></span>' . $next_post_content , $in_category, $excluted_terms, 'product_cat' );

  ?>
  
  <div id="product-nav" class="clear">
           
          <?php if ( $prev != '' ) :
                  echo $prev;
                  echo '<span class="prev-label">' . __( 'Prev', 'futurio-extra' ) . '</span>';
          endif; ?>
  
          <?php if ( $next != '' ) :
                  echo $next;
                  echo '<span class="next-label">' . __( 'Next', 'futurio-extra' ) . '</span>';
          endif; ?>
  </div>
  <?php
         
}

Kirki::add_panel( 'woo_section_main', array(
	'title'		 => esc_attr__( 'WooCommerce', 'futurio-extra' ),
	'priority'	 => 10,
) );
Kirki::add_section( 'woo_section', array(
	'title'		 => esc_attr__( 'General Settings', 'futurio-extra' ),
	'panel'		 => 'woo_section_main',
	'priority'	 => 1,
) );
Kirki::add_section( 'main_typography_woo_archive_section', array(
	'title'		 => esc_attr__( 'Archive/Shop', 'futurio-extra' ),
	'panel'		 => 'woo_section_main',
	'priority'	 => 2,
) );
Kirki::add_section( 'main_typography_woo_product_section', array(
	'title'		 => esc_attr__( 'Product Page', 'futurio-extra' ),
	'panel'		 => 'woo_section_main',
	'priority'	 => 3,
) );
Kirki::add_section( 'woo_global_buttons_section', array(
	'title'		 => esc_attr__( 'Buttons', 'futurio-extra' ),
	'panel'		 => 'woo_section_main',
	'priority'	 => 4,
) );


/**
 * WooCommerce
 */
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'toggle',
	'settings'	 => 'woo_gallery_zoom',
	'label'		 => esc_attr__( 'Gallery zoom', 'futurio-extra' ),
	'section'	 => 'woo_section',
	'default'	 => 1,
	'priority'	 => 10,
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'toggle',
	'settings'	 => 'woo_gallery_lightbox',
	'label'		 => esc_attr__( 'Gallery lightbox', 'futurio-extra' ),
	'section'	 => 'woo_section',
	'default'	 => 1,
	'priority'	 => 10,
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'toggle',
	'settings'	 => 'woo_gallery_slider',
	'label'		 => esc_attr__( 'Gallery slider', 'futurio-extra' ),
	'section'	 => 'woo_section',
	'default'	 => 1,
	'priority'	 => 10,
) );
Kirki::add_field( 'futurio_extra', array(
	'type'			 => 'slider',
	'settings'		 => 'archive_number_products',
	'label'			 => esc_attr__( 'Number of items', 'futurio-extra' ),
	'description'	 => esc_attr__( 'Change number of products displayed per page in archive(shop) page.', 'futurio-extra' ),
	'section'		 => 'woo_section',
	'default'		 => 24,
	'priority'		 => 10,
	'choices'		 => array(
		'min'	 => 2,
		'max'	 => 60,
		'step'	 => 1,
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'			 => 'slider',
	'settings'		 => 'archive_number_columns',
	'label'			 => esc_attr__( 'Items per row', 'futurio-extra' ),
	'description'	 => esc_attr__( 'Change the number of products columns per row in archive(shop) page.', 'futurio-extra' ),
	'section'		 => 'woo_section',
	'default'		 => 4,
	'priority'		 => 10,
	'choices'		 => array(
		'min'	 => 2,
		'max'	 => 5,
		'step'	 => 1,
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'toggle',
	'settings'	 => 'woo_open_header_cart',
	'label'		 => esc_attr__( 'Open header cart automatically', 'futurio-extra' ),
	'section'	 => 'woo_section',
	'default'	 => 1,
	'priority'	 => 10,
) );
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'label' => esc_html__('Header cart icon', 'futurio-extra'),
  'section' => 'woo_section',
  'settings' => 'header_cart_icon',
  'default' => 'shopping-bag',
  'choices' => array(
      'shopping-cart' => '<i class="fa fa-shopping-cart"></i>',
      'shopping-bag' => '<i class="fa fa-shopping-bag"></i>',
      'shopping-basket' => '<i class="fa fa-shopping-basket"></i>'
  ), 
));
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'label' => esc_html__('Header my account icon', 'futurio-extra'),
  'section' => 'woo_section',
  'settings' => 'header_my_account_icon',
  'default' => 'user',
  'choices' => array(
      'user' => '<i class="fa fa-user"></i>',
      'user-o' => '<i class="fa fa-user-o"></i>',
      'user-circle' => '<i class="fa fa-user-circle"></i>',
      'user-circle-o' => '<i class="fa fa-user-circle-o"></i>'
  ), 
));

/**
 * Woo archive styling
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'woo_archive_product_tab',
  'section' => 'main_typography_woo_archive_section',
  'transport' => 'postMessage',
  'default' => 'desktop',
  'choices' => array(
      'desktop' => '<i class="dashicons dashicons-desktop"></i>',
      'tablet' => '<i class="dashicons dashicons-tablet"></i>',
      'mobile' => '<i class="dashicons dashicons-smartphone"></i>',
  ),
)); 
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_archive_product_title',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'transport'	 => 'auto',
  'choices' => futurio_extra_g_fonts(),
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '18px',
		'variant'		 => '500',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce ul.products li.product h3, li.product-category.product h3, .woocommerce ul.products li.product h2.woocommerce-loop-product__title, .woocommerce ul.products li.product h2.woocommerce-loop-category__title',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_archive_product_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_archive_product_price',
	'label'		 => esc_attr__( 'Price', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'transport'	 => 'auto',
  'choices' => futurio_extra_g_fonts(),
	'priority'	 => 10,
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '18px',
		'variant'		 => '300',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px',
    futurio_extra_col()	=> '',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce ul.products li.product .price',
			'property'	 => 'color',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_archive_product_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_archive_product_title_tablet',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce ul.products li.product h3, li.product-category.product h3, .woocommerce ul.products li.product h2.woocommerce-loop-product__title, .woocommerce ul.products li.product h2.woocommerce-loop-category__title',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_archive_product_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_archive_product_price_tablet',
	'label'		 => esc_attr__( 'Price', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'transport'	 => 'auto',
	'priority'	 => 10,
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce ul.products li.product .price',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_archive_product_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_archive_product_title_mobile',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'variant'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce ul.products li.product h3, li.product-category.product h3, .woocommerce ul.products li.product h2.woocommerce-loop-product__title, .woocommerce ul.products li.product h2.woocommerce-loop-category__title',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_archive_product_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_archive_product_price_mobile',
	'label'		 => esc_attr__( 'Price', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'transport'	 => 'auto',
	'priority'	 => 10,
	'default'	 => array(
		'font-size'		 => '',
		'variant'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce ul.products li.product .price',
      'media_query'	 => '@media (max-width: 767px)',
			//'property'	 => 'color',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_archive_product_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );


Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'toggle',
	'settings'	 => 'woo_archive_product_categories',
	'label'		 => esc_attr__( 'Categories', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'default'	 => 1,
	'priority'	 => 10,
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_archive_product_button_border_radius',
	'label'		 => esc_attr__( 'Button border radius', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'default'	 => 0,
	'transport'	 => 'auto',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '20',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce ul.products li.product .button',
			'property'	 => 'border-radius',
			'units'		 => 'px',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_archive_product_border_radius',
	'label'		 => esc_attr__( 'Product border radius', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'default'	 => 0,
	'transport'	 => 'auto',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '20',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce ul.products li.product',
			'property'	 => 'border-radius',
			'units'		 => 'px',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_archive_image_border_radius',
	'label'		 => esc_attr__( 'Image border radius', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'default'	 => 0,
	'transport'	 => 'auto',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '20',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce ul.products li.product a img',
			'property'	 => 'border-radius',
			'units'		 => 'px',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_archive_image_padding',
	'label'		 => esc_attr__( 'Product padding', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'default'	 => 0,
	'transport'	 => 'auto',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '20',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce ul.products li.product',
			'property'	 => 'padding',
			'units'		 => 'px',
		),
    array(
			'element'	 => '.futurio-has-gallery .secondary-image',
			'property'	 => 'padding',
			'value_pattern' => '$px $px 0 $px',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_archive_product_shadow',
	'label'		 => esc_attr__( 'Product shadow', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'default'	 => 0,
	'transport'	 => 'auto',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '30',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'		 => '.woocommerce ul.products li.product, .woocommerce-page ul.products li.product',
			'property'		 => 'box-shadow',
			'value_pattern'	 => '0px 0px $px 0px rgba(0,0,0,0.25)'
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_archive_product_shadow_hover',
	'label'		 => esc_attr__( 'Product shadow on hover', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_archive_section',
	'default'	 => 10,
	'transport'	 => 'auto',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '30',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'		 => '.woocommerce ul.products li.product:hover, .woocommerce-page ul.products li.product:hover',
			'property'		 => 'box-shadow',
			'value_pattern'	 => '0px 0px $px 0px rgba(0,0,0,0.38)'
		),
	),
) );

/**
 * Woo single styling
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'woo_single_product_tab',
  'section' => 'main_typography_woo_product_section',
  'transport' => 'postMessage',
  'default' => 'desktop',
  'choices' => array(
      'desktop' => '<i class="dashicons dashicons-desktop"></i>',
      'tablet' => '<i class="dashicons dashicons-tablet"></i>',
      'mobile' => '<i class="dashicons dashicons-smartphone"></i>',
  ),
));  
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_single_product_title',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'transport'	 => 'auto',
  'choices' => futurio_extra_g_fonts(),
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '36px',
		'variant'		 => '500',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce div.product .product_title',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_single_product_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_single_product_price',
	'label'		 => esc_attr__( 'Price', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'transport'	 => 'auto',
  'choices' => futurio_extra_g_fonts(),
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '18px',
		'variant'		 => '300',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce div.product p.price, .woocommerce div.product span.price',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_single_product_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_single_product_title_tablet',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce div.product .product_title',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_single_product_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_single_product_price_tablet',
	'label'		 => esc_attr__( 'Price', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce div.product p.price, .woocommerce div.product span.price',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_single_product_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_single_product_title_mobile',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce div.product .product_title',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_single_product_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'woo_single_product_price_mobile',
	'label'		 => esc_attr__( 'Price', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.woocommerce div.product p.price, .woocommerce div.product span.price',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'woo_single_product_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );

Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_single_image_width',
	'label'		 => esc_attr__( 'Image area width (in %)', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'default'	 => '48',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '100',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images',
			'property'	 => 'width',
			'units'		 => '%',
      'media_query'	 => '@media (min-width: 769px)',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'toggle',
	'settings'	 => 'woo_prev_next_nav',
	'label'		 => esc_attr__( 'Product Navigation', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'default'	 => 1,
	'priority'	 => 10,
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'radio-buttonset',
	'settings'	 => 'woo_single_tab_position',
	'label'		 => __( 'Tab titles align', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'default'	 => 'left',
	'priority'	 => 10,
	'choices'	 => array(
		'left'			=> '<i class="dashicons dashicons-editor-alignleft"></i>',
    'center'		=> '<i class="dashicons dashicons-editor-aligncenter"></i>',
		'right'		  => '<i class="dashicons dashicons-editor-alignright"></i>',
	),
  'output'	 => array(
		array(
			'element' => '.woocommerce div.product .woocommerce-tabs ul.tabs',
      'property'	 => 'text-align',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'toggle',
	'settings'	 => 'woo_remove_related',
	'label'		 => esc_attr__( 'Related products', 'futurio-extra' ),
	'section'	 => 'main_typography_woo_product_section',
	'default'	 => 1,
	'priority'	 => 10,
) );
/**
 * Woo buttons styling
 */
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'woo_global_product_buttons_radius',
	'label'		 => esc_attr__( 'Button border radius', 'futurio-extra' ),
	'section'	 => 'woo_global_buttons_section',
	'default'	 => 0,
	'transport'	 => 'auto',
	'priority'	 => 10,
	'choices'	 => array(
		'min'	 => '0',
		'max'	 => '20',
		'step'	 => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt',
			'property'	 => 'border-radius',
			'units'		 => 'px',
		),
	),
) );

/**
 * Add custom CSS styles
 */
function futurio_extra_woo_enqueue_header_css() {
  
  $css = '';
  $img_width = get_theme_mod( 'woo_single_image_width', '48' );
	$summary_width = ( 100 - $img_width );
  $summary_width = ( $summary_width == 0 ) ? 100 :  $summary_width;

  $css .= '@media only screen and (min-width: 769px) {.woocommerce #content div.product div.summary, .woocommerce div.product div.summary, .woocommerce-page #content div.product div.summary, .woocommerce-page div.product div.summary{width: ' . $summary_width . '%; padding-left: 4%;}}';

	
	wp_add_inline_style( 'futurio-stylesheet', $css );
}

add_action( 'wp_enqueue_scripts', 'futurio_extra_woo_enqueue_header_css', 9999 );

/**
 * Add custom class to body
 */
function futurio_extra_body_class( $classes ) {
    
    if ( get_theme_mod( 'woo_open_header_cart', 1 ) == 1 ) {
  		$classes[] = 'open-head-cart';
  	}

    return $classes;
}

add_filter( 'body_class', 'futurio_extra_body_class' );
