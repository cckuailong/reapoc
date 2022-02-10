<?php
// Get all elementor page templates
function pp_elements_lite_get_page_templates( $type = '' ) {
	$args = [
		'post_type'         => 'elementor_library',
		'posts_per_page'    => -1,
	];

	if ( $type ) {
		$args['tax_query'] = [
			[
				'taxonomy' => 'elementor_library_type',
				'field'    => 'slug',
				'terms' => $type,
			],
		];
	}

	$page_templates = get_posts( $args );

	$options = array();

	if ( ! empty( $page_templates ) && ! is_wp_error( $page_templates ) ) {
		foreach ( $page_templates as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}
	}
	return $options;
}

// Get all forms of Contact Form 7 plugin
function pp_elements_lite_get_contact_form_7_forms() {
	if ( function_exists( 'wpcf7' ) ) {
		$options = array();

		$args = array(
			'post_type'         => 'wpcf7_contact_form',
			'posts_per_page'    => -1,
		);

		$contact_forms = get_posts( $args );

		if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

			$i = 0;

			foreach ( $contact_forms as $post ) {
				if ( $i == 0 ) {
					$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
				}
				$options[ $post->ID ] = $post->post_title;
				$i++;
			}
		}
	} else {
		$options = array();
	}

	return $options;
}

// Get all forms of Gravity Forms plugin
function pp_elements_lite_get_gravity_forms() {
	if ( class_exists( 'GFCommon' ) ) {
		$options = array();

		$contact_forms = RGFormsModel::get_forms( null, 'title' );

		if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

			$i = 0;

			foreach ( $contact_forms as $form ) {
				if ( $i == 0 ) {
					$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
				}
				$options[ $form->id ] = $form->title;
				$i++;
			}
		}
	} else {
		$options = array();
	}

	return $options;
}

// Get all forms of Ninja Forms plugin
function pp_elements_lite_get_ninja_forms() {
	if ( class_exists( 'Ninja_Forms' ) ) {
		$options = array();

		$contact_forms = Ninja_Forms()->form()->get_forms();

		if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

			$i = 0;

			foreach ( $contact_forms as $form ) {
				if ( $i == 0 ) {
					$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
				}
				$options[ $form->get_id() ] = $form->get_setting( 'title' );
				$i++;
			}
		}
	} else {
		$options = array();
	}

	return $options;
}

// Get all forms of Caldera plugin
function pp_elements_lite_get_caldera_forms() {
	if ( class_exists( 'Caldera_Forms' ) ) {
		$options = array();

		$contact_forms = Caldera_Forms_Forms::get_forms( true, true );

		if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

			$i = 0;

			foreach ( $contact_forms as $form ) {
				if ( $i == 0 ) {
					$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
				}
				$options[ $form['ID'] ] = $form['name'];
				$i++;
			}
		}
	} else {
		$options = array();
	}

	return $options;
}

// Get all forms of WPForms plugin
function pp_elements_lite_get_wpforms_forms() {
	if ( class_exists( 'WPForms' ) ) {
		$options = array();

		$args = array(
			'post_type'         => 'wpforms',
			'posts_per_page'    => -1,
		);

		$contact_forms = get_posts( $args );

		if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

			$i = 0;

			foreach ( $contact_forms as $post ) {
				if ( $i == 0 ) {
					$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
				}
				$options[ $post->ID ] = $post->post_title;
				$i++;
			}
		}
	} else {
		$options = array();
	}

	return $options;
}

// Get all forms of Formidable Forms plugin
if ( ! function_exists( 'pp_elements_lite_get_formidable_forms' ) ) {
	function pp_elements_lite_get_formidable_forms() {
		if ( class_exists( 'FrmForm' ) ) {
			$options = array();

			$forms = FrmForm::get_published_forms( array(), 999, 'exclude' );
			if ( count( $forms ) ) {
				$i = 0;
				foreach ( $forms as $form ) {
					if ( 0 === $i ) {
						$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
					}
					$options[ $form->id ] = $form->name;
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}
}

// Get all forms of Fluent Forms plugin
if ( ! function_exists( 'pp_elements_lite_get_fluent_forms' ) ) {
	function pp_elements_lite_get_fluent_forms() {
		$options = array();

		if ( function_exists( 'wpFluentForm' ) ) {

			global $wpdb;

			$result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fluentform_forms" );
			if ( $result ) {
				$options[0] = esc_html__( 'Select a Contact Form', 'powerpack' );
				foreach ( $result as $form ) {
					$options[ $form->id ] = $form->title;
				}
			} else {
				$options[0] = esc_html__( 'No forms found!', 'powerpack' );
			}
		}

		return $options;
	}
}

// Get categories
function pp_elements_lite_get_post_categories() {

	$options = array();

	$terms = get_terms( array(
		'taxonomy'      => 'category',
		'hide_empty'    => true,
	));

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
	}

	return $options;
}

// Get Post Types
function pp_elements_lite_get_post_types() {

	$pp_post_types = get_post_types( array(
		'public'            => true,
		'show_in_nav_menus' => true,
	) );

	return $pp_post_types;
}

// Get all Authors
function pp_elements_lite_get_auhtors() {

	$options = array();

	$users = get_users();

	foreach ( $users as $user ) {
		$options[ $user->ID ] = $user->display_name;
	}

	return $options;
}

// Get all Authors
function pp_elements_lite_get_tags() {

	$options = array();

	$tags = get_tags();

	foreach ( $tags as $tag ) {
		$options[ $tag->term_id ] = $tag->name;
	}

	return $options;
}

// Get all Posts
function pp_elements_lite_get_posts() {

	$post_list = get_posts( array(
		'post_type'         => 'post',
		'orderby'           => 'date',
		'order'             => 'DESC',
		'posts_per_page'    => -1,
	) );

	$posts = array();

	if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
		foreach ( $post_list as $post ) {
			$posts[ $post->ID ] = $post->post_title;
		}
	}

	return $posts;
}

// Custom Excerpt
function pp_elements_lite_custom_excerpt( $limit = '' ) {
	$excerpt = explode( ' ', get_the_excerpt(), $limit );
	if ( count( $excerpt ) >= $limit ) {
		array_pop( $excerpt );
		$excerpt = implode( ' ', $excerpt ) . '...';
	} else {
		$excerpt = implode( ' ', $excerpt );
	}
	$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );
	return $excerpt;
}
add_filter( 'get_the_excerpt', 'do_shortcode' );

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( class_exists( 'WooCommerce' ) || is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

	// Get all Products
	function pp_elements_lite_get_products() {

		$post_list = get_posts( array(
			'post_type'         => 'product',
			'orderby'           => 'date',
			'order'             => 'DESC',
			'posts_per_page'    => -1,
		) );

		$posts = array();

		if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
			foreach ( $post_list as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}
		}

		return $posts;
	}

	// Woocommerce - Get product categories
	function pp_elements_lite_get_product_categories() {

		$options = array();

		$terms = get_terms( array(
			'taxonomy'      => 'product_cat',
			'hide_empty'    => true,
		));

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}

	// WooCommerce - Get product tags
	function pp_elements_lite_product_get_tags() {

		$options = array();

		$tags = get_terms( 'product_tag' );

		if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
			foreach ( $tags as $tag ) {
				$options[ $tag->term_id ] = $tag->name;
			}
		}

		return $options;
	}
}

function pp_elements_lite_get_modules() {
	$modules = array(
		'pp-advanced-accordion'     => esc_html__( 'Advanced Accordion', 'powerpack' ),
		'pp-link-effects'           => esc_html__( 'Link Effects', 'powerpack' ),
		'pp-divider'                => esc_html__( 'Divider', 'powerpack' ),
		'pp-flipbox'                => esc_html__( 'Flipbox', 'powerpack' ),
		'pp-image-accordion'        => esc_html__( 'Image Accordion', 'powerpack' ),
		'pp-info-box'               => esc_html__( 'Info Box', 'powerpack' ),
		'pp-info-box-carousel'      => esc_html__( 'Info Box Carousel', 'powerpack' ),
		'pp-info-list'              => esc_html__( 'Info List', 'powerpack' ),
		'pp-info-table'             => esc_html__( 'Info Table', 'powerpack' ),
		'pp-pricing-table'          => esc_html__( 'Pricing Table', 'powerpack' ),
		'pp-price-menu'             => esc_html__( 'Price Menu', 'powerpack' ),
		'pp-business-hours'         => esc_html__( 'Businsess Hours', 'powerpack' ),
		'pp-team-member'            => esc_html__( 'Team Member', 'powerpack' ),
		'pp-team-member-carousel'   => esc_html__( 'Team Member Carousel', 'powerpack' ),
		'pp-counter'                => esc_html__( 'Counter', 'powerpack' ),
		'pp-hotspots'               => esc_html__( 'Image Hotspots', 'powerpack' ),
		'pp-icon-list'              => esc_html__( 'Icon List', 'powerpack' ),
		'pp-dual-heading'           => esc_html__( 'Dual Heading', 'powerpack' ),
		'pp-promo-box'              => esc_html__( 'Promo Box', 'powerpack' ),
		'pp-logo-carousel'          => esc_html__( 'Logo Carousel', 'powerpack' ),
		'pp-logo-grid'              => esc_html__( 'Logo Grid', 'powerpack' ),
		'pp-image-comparison'       => esc_html__( 'Image Comparison', 'powerpack' ),
		'pp-instafeed'              => esc_html__( 'Instagram Feed', 'powerpack' ),
		'pp-content-ticker'         => esc_html__( 'Content Ticker', 'powerpack' ),
		'pp-scroll-image'           => esc_html__( 'Scroll Image', 'powerpack' ),
		'pp-buttons'                => esc_html__( 'Buttons', 'powerpack' ),
		'pp-twitter-buttons'        => esc_html__( 'Twitter Buttons', 'powerpack' ),
		'pp-twitter-grid'           => esc_html__( 'Twitter Grid', 'powerpack' ),
		'pp-twitter-timeline'       => esc_html__( 'Twitter Timeline', 'powerpack' ),
		'pp-twitter-tweet'          => esc_html__( 'Twitter Tweet', 'powerpack' ),
		'pp-fancy-heading'          => esc_html__( 'Fancy Heading', 'powerpack' ),
		'pp-posts'                  => esc_html__( 'Posts', 'powerpack' ),
		'pp-content-reveal'         => __( 'Content Reveal', 'powerpack' ),
		'pp-random-image'           => esc_html__( 'Random Image', 'powerpack' ),
	);

	// Contact Form 7
	if ( function_exists( 'wpcf7' ) ) {
		$modules['pp-contact-form-7'] = esc_html__( 'Contact Form 7', 'powerpack' );
	}

	// Gravity Forms
	if ( class_exists( 'GFCommon' ) ) {
		$modules['pp-gravity-forms'] = esc_html__( 'Gravity Forms', 'powerpack' );
	}

	// Ninja Forms
	if ( class_exists( 'Ninja_Forms' ) ) {
		$modules['pp-ninja-forms'] = esc_html__( 'Ninja Forms', 'powerpack' );
	}

	// Caldera Forms
	if ( class_exists( 'Caldera_Forms' ) ) {
		$modules['pp-caldera-forms'] = esc_html__( 'Caldera Forms', 'powerpack' );
	}

	// WPForms
	if ( function_exists( 'wpforms' ) ) {
		$modules['pp-wpforms'] = esc_html__( 'WPForms', 'powerpack' );
	}

	// Formidable Forms
	if ( class_exists( 'FrmForm' ) ) {
		$modules['pp-formidable-forms'] = __( 'Formidable Forms', 'powerpack' );
	}

	// Fluent Forms
	if ( function_exists( 'wpFluentForm' ) ) {
		$modules['pp-fluent-forms'] = __( 'Fluent Forms', 'powerpack' );
	}

	ksort( $modules );

	return $modules;
}

function pp_elements_lite_get_extensions() {
	$extensions = array(
		'pp-display-conditions'           => __( 'Display Conditions', 'powerpack' ),
		'pp-wrapper-link'                 => __( 'Wrapper Link', 'powerpack' ),
		'pp-animated-gradient-background' => __( 'Animated Gradient Background', 'powerpack' ),
	);

	return $extensions;
}

function pp_elements_lite_get_enabled_modules() {
	$enabled_modules = \PowerpackElementsLite\Classes\PP_Admin_Settings::get_option( 'pp_elementor_modules', true );

	if ( ! is_array( $enabled_modules ) ) {
		return array_keys( pp_elements_lite_get_modules() );
	} else {
		return $enabled_modules;
	}
}

function pp_elements_lite_get_enabled_extensions() {
	$enabled_extensions = \PowerpackElementsLite\Classes\PP_Admin_Settings::get_option( 'pp_elementor_extensions', true );

	if ( ! is_array( $enabled_extensions ) ) {
		return array();
	} else {
		return $enabled_extensions;
	}

	//return $enabled_extensions;
}

// Get templates
function pp_elements_lite_get_saved_templates( $templates = array() ) {
	if ( empty( $templates ) ) {
		return array();
	}

	$options = array();

	foreach ( $templates as $template ) {
		$options[ $template['template_id'] ] = $template['title'];
	}

	return $options;
}

/**
 * Elementor
 *
 * Retrieves the elementor plugin instance
 *
 * @since  1.2.9
 * @return \Elementor\Plugin|$instace
 */
function pp_lite_get_elementor() {
	return \Elementor\Plugin::$instance;
}
