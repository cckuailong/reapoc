<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdupcpDeactivationSurvey' ) ) {
/**
 * Class to handle plugin deactivation survey
 *
 * @since 5.0.0
 */
class ewdupcpDeactivationSurvey {

	public function __construct() {
		add_action( 'current_screen', array( $this, 'maybe_add_survey' ) );
	}

	public function maybe_add_survey() {
		if ( in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ), true) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_deactivation_scripts') );
			add_action( 'admin_footer', array( $this, 'add_deactivation_html') );
		}
	}

	public function enqueue_deactivation_scripts() {
		wp_enqueue_style( 'ewd-upcp-deactivation-css', EWD_UPCP_PLUGIN_URL . '/assets/css/plugin-deactivation.css' );
		wp_enqueue_script( 'ewd-upcp-deactivation-js', EWD_UPCP_PLUGIN_URL . '/assets/js/plugin-deactivation.js', array( 'jquery' ) );

		wp_localize_script( 'ewd-upcp-deactivation-js', 'ewd_upcp_deactivation_data', array( 'site_url' => site_url() ) );
	}

	public function add_deactivation_html() {
		
		$install_time = get_option( 'ewd-upcp-installation-time' );

		$options = array(
			1 => array(
				'title'   => esc_html__( 'I no longer need the plugin', 'ultimate-product-catalogue' ),
			),
			2 => array(
				'title'   => esc_html__( 'I\'m switching to a different plugin', 'ultimate-product-catalogue' ),
				'details' => esc_html__( 'Please share which plugin', 'ultimate-product-catalogue' ),
			),
			3 => array(
				'title'   => esc_html__( 'I couldn\'t get the plugin to work', 'ultimate-product-catalogue' ),
				'details' => esc_html__( 'Please share what wasn\'t working', 'ultimate-product-catalogue' ),
			),
			4 => array(
				'title'   => esc_html__( 'It\'s a temporary deactivation', 'ultimate-product-catalogue' ),
			),
			5 => array(
				'title'   => esc_html__( 'Other', 'ultimate-product-catalogue' ),
				'details' => esc_html__( 'Please share the reason', 'ultimate-product-catalogue' ),
			),
		);
		?>
		<div class="ewd-upcp-deactivate-survey-modal" id="ewd-upcp-deactivate-survey-ultimate-product-catalogue">
			<div class="ewd-upcp-deactivate-survey-wrap">
				<form class="ewd-upcp-deactivate-survey" method="post" data-installtime="<?php echo $install_time; ?>">
					<span class="ewd-upcp-deactivate-survey-title"><span class="dashicons dashicons-testimonial"></span><?php echo ' ' . __( 'Quick Feedback', 'ultimate-product-catalogue' ); ?></span>
					<span class="ewd-upcp-deactivate-survey-desc"><?php echo __('If you have a moment, please share why you are deactivating Ultimate Product Catalog:', 'ultimate-product-catalogue' ); ?></span>
					<div class="ewd-upcp-deactivate-survey-options">
						<?php foreach ( $options as $id => $option ) : ?>
							<div class="ewd-upcp-deactivate-survey-option">
								<label for="ewd-upcp-deactivate-survey-option-ultimate-product-catalogue-<?php echo $id; ?>" class="ewd-upcp-deactivate-survey-option-label">
									<input id="ewd-upcp-deactivate-survey-option-ultimate-product-catalogue-<?php echo $id; ?>" class="ewd-upcp-deactivate-survey-option-input" type="radio" name="code" value="<?php echo $id; ?>" />
									<span class="ewd-upcp-deactivate-survey-option-reason"><?php echo $option['title']; ?></span>
								</label>
								<?php if ( ! empty( $option['details'] ) ) : ?>
									<input class="ewd-upcp-deactivate-survey-option-details" type="text" placeholder="<?php echo $option['details']; ?>" />
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="ewd-upcp-deactivate-survey-footer">
						<button type="submit" class="ewd-upcp-deactivate-survey-submit button button-primary button-large"><?php _e('Submit and Deactivate', 'ultimate-product-catalogue' ); ?></button>
						<a href="#" class="ewd-upcp-deactivate-survey-deactivate"><?php _e('Skip and Deactivate', 'ultimate-product-catalogue' ); ?></a>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}

}