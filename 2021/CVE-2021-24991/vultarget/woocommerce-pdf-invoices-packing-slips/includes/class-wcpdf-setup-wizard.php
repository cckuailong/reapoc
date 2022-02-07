<?php
namespace WPO\WC\PDF_Invoices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Setup_Wizard' ) ) :

class Setup_Wizard {

	/** @var string Currenct Step */
	private $step   = '';

	/** @var array Steps for the setup wizard */
	private $steps  = array();
	
	public function __construct() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		}

	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'wpo-wcpdf-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( empty( $_GET['page'] ) || 'wpo-wcpdf-setup' !== $_GET['page'] ) {
			return;
		}

		if ( is_null ( get_current_screen() ) ) {
			set_current_screen();
		}
				
		$this->steps = array(
			'shop-name' => array(
				'name'	=> __( 'Shop Name', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/includes/views/setup-wizard/shop-name.php',
			),
			'logo' => array(
				'name'	=> __( 'Your logo', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/includes/views/setup-wizard/logo.php',
			),
			'attach-to' => array(
				'name'	=> __( 'Attachments', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/includes/views/setup-wizard/attach-to.php',
			),
			'display-options' => array(
				'name'	=> __( 'Display options', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/includes/views/setup-wizard/display-options.php',
			),
			'paper-format' => array(
				'name'	=> __( 'Paper format', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/includes/views/setup-wizard/paper-format.php',
			),
			'show-action-buttons' => array(
				'name'	=> __( 'Action buttons', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/includes/views/setup-wizard/show-action-buttons.php',
			),
			'good-to-go' => array(
				'name'	=> __( 'Ready!', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/includes/views/setup-wizard/good-to-go.php',
			),
		);
		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		wp_enqueue_style(
			'wpo-wcpdf-setup',
			WPO_WCPDF()->plugin_url() . '/assets/css/setup-wizard'.$suffix.'.css',
			array( 'dashicons', 'install' ), 
			WPO_WCPDF_VERSION
		);
		wp_register_script(
			'wpo-wcpdf-media-upload',
			WPO_WCPDF()->plugin_url() . '/assets/js/media-upload'.$suffix.'.js',
			array( 'jquery', 'media-editor', 'mce-view' ),
			WPO_WCPDF_VERSION
		);
		wp_register_script(
			'wpo-wcpdf-setup',
			WPO_WCPDF()->plugin_url() . '/assets/js/setup-wizard'.$suffix.'.js',
			array( 'jquery', 'wpo-wcpdf-media-upload' ),
			WPO_WCPDF_VERSION
		);
		wp_enqueue_media();



		$step_keys = array_keys($this->steps);
		if ( end( $step_keys ) === $this->step ) {
			wp_register_script(
				'wpo-wcpdf-setup-confetti',
				WPO_WCPDF()->plugin_url() . '/assets/js/confetti'.$suffix.'.js',
				array( 'jquery' ),
				WPO_WCPDF_VERSION
			);
		}

		if ( ! empty( $_POST['save_step'] ) ) {
			$this->save_step();
			// echo '<pre>';var_dump($_POST);echo '</pre>';die();
			// call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?> class="wpo-wizzard">
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'WooCommerce PDF Invoices & Packing Slips &rsaquo; Setup Wizard', 'woocommerce-pdf-invoices-packing-slips' ); ?></title>
			<?php wp_print_scripts( 'wpo-wcpdf-setup' ); ?>
			<?php wp_print_scripts( 'wpo-wcpdf-setup-confetti' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="wpo-wcpdf-setup wp-core-ui">
			<?php if( $this->step == 'good-to-go' ) { echo "<div id='confetti'></div>"; } ?>
			<form method="post">
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps = $this->steps;
		// array_shift( $output_steps );
		?>
		<div class="wpo-setup-card">
			<h1 class="wpo-plugin-title">PDF Invoices & Packing Slips</h1>
			<ol class="wpo-progress-bar">
				<?php foreach ( $output_steps as $step_key => $step ) : ?>
					<a href="<?php echo $this->get_step_link($step_key); ?>" ><li><div class="wpo-progress-marker <?php
						if ( $step_key === $this->step ) {
							echo 'active';
						} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
							echo 'completed';
						}
					?>"><?php //echo esc_html( $step['name'] ); ?></div></li></a>
				<?php endforeach; ?>
			</ol>
			<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="wpo-setup-content">';
		include( $this->steps[ $this->step ]['view'] );
		echo '</div>';
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
						<input type="hidden" name="wpo_wcpdf_step" value="<?php echo $this->step ?>">
						<div class="wpo-setup-buttons">
							<?php if ($step = $this->get_step(-1)): ?>
								<a href="<?php echo $this->get_step_link($step); ?>" class="wpo-button-previous"><?php _e( 'Previous', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
							<?php endif ?>
							<!-- <input type="submit" class="wpo-button-next" value="Next" /> -->
							<?php if ($step = $this->get_step(1)): ?>
								<?php wp_nonce_field( 'wpo-wcpdf-setup' ); ?>
								<input type="submit" class="wpo-button-next" value="<?php esc_attr_e( 'Next', 'woocommerce-pdf-invoices-packing-slips' ); ?>" name="save_step" />
								<a href="<?php echo $this->get_step_link($step); ?>" class="wpo-skip-step"><?php _e( 'Skip this step', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
							<?php else: ?>
								<a href="<?php echo $this->get_step_link($step); ?>" class="wpo-button-next"><?php _e( 'Finish', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
							<?php endif ?>
						</div>
					</div>
				</form>
				<?php do_action( 'admin_footer' ); // for media uploader templates ?>
			</body>
		</html>
		<?php
	}

	public function get_step_link( $step ) {
		$step_keys = array_keys( $this->steps );
		if ( end( $step_keys ) === $this->step && empty($step)) {
			return admin_url('admin.php?page=wpo_wcpdf_options_page');
		}
		return add_query_arg( 'step', $step );
	}


	public function get_step( $delta ) {
		$step_keys = array_keys( $this->steps );
		$current_step_pos = array_search( $this->step, $step_keys );
		$new_step_pos = $current_step_pos + $delta;
		if (isset($step_keys[$new_step_pos])) {
			return $step_keys[$new_step_pos];
		} else {
			return false;
		}
	}

	public function save_step() {
		if ( isset( $this->steps[ $this->step ]['handler'] ) ) {
			check_admin_referer( 'wpo-wcpdf-setup' );
			// for doing more than just saving an option value
			call_user_func( $this->steps[ $this->step ]['handler'] );
		} else {
			$user_id = get_current_user_id();
			$hidden = get_user_meta( $user_id, 'manageedit-shop_ordercolumnshidden', true );
			if (!empty($_POST['wcpdf_settings']) && is_array($_POST['wcpdf_settings'])) {
				check_admin_referer( 'wpo-wcpdf-setup' );
				foreach ($_POST['wcpdf_settings'] as $option => $settings) {
					// sanitize posted settings
					foreach ($settings as $key => $value) {
						if ( $key == 'shop_address' && function_exists('sanitize_textarea_field') ) {
							$sanitize_function = 'sanitize_textarea_field';
						} else {
							$sanitize_function = 'sanitize_text_field';							
						}

						if (is_array($value)) {
							$settings[$key] = array_map($sanitize_function, $value);
						} else {
							$settings[$key] = call_user_func($sanitize_function, $value );
						}
					}
					$current_settings = get_option( $option, array() );
					$new_settings = $settings + $current_settings;
					// echo "<pre>".var_export($settings,true)."</pre>";
					// echo "<pre>".var_export($new_settings,true)."</pre>";die();
					update_option( $option, $new_settings );
				}
			} elseif ( $_POST['wpo_wcpdf_step'] == 'show-action-buttons' ) {
				if ( !empty( $_POST['wc_show_action_buttons'] ) ) {
					$hidden = array_filter( $hidden, function( $setting ){ return $setting !== 'wc_actions'; } );
					update_user_meta( $user_id, 'manageedit-shop_ordercolumnshidden', $hidden );
				} else {
					array_push($hidden, 'wc_actions');
					update_user_meta( $user_id, 'manageedit-shop_ordercolumnshidden', $hidden );
				}
			}
		}

		wp_redirect( esc_url_raw( $this->get_step_link( $this->get_step(1) ) ) );
	}

}

endif; // class_exists

return new Setup_Wizard();