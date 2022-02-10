<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bpfwpDeactivationSurvey' ) ) {
/**
 * Class to handle plugin deactivation survey
 *
 * @since 2.0.4
 */
class bpfwpDeactivationSurvey {

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
		wp_enqueue_style( 'bpfwp-deactivation-css', BPFWP_PLUGIN_URL . '/assets/css/plugin-deactivation.css', array(), BPFWP_VERSION );
		wp_enqueue_script( 'bpfwp-deactivation-js', BPFWP_PLUGIN_URL . '/assets/js/plugin-deactivation.js', array( 'jquery' ), BPFWP_VERSION );

		wp_localize_script( 'bpfwp-deactivation-js', 'bpfwp_deactivation_data', array( 'site_url' => site_url() ) );
	}

	public function add_deactivation_html() {

		$install_time = get_option( 'bpfwp-installation-time' );
		
		$options = array(
			1 => array(
				'title'   => esc_html__( 'I no longer need the plugin', 'business-profile' ),
			),
			2 => array(
				'title'   => esc_html__( 'I\'m switching to a different plugin', 'business-profile' ),
				'details' => esc_html__( 'Please share which plugin', 'business-profile' ),
			),
			3 => array(
				'title'   => esc_html__( 'I couldn\'t get the plugin to work', 'business-profile' ),
				'details' => esc_html__( 'Please share what wasn\'t working', 'business-profile' ),
			),
			4 => array(
				'title'   => esc_html__( 'It\'s a temporary deactivation', 'business-profile' ),
			),
			5 => array(
				'title'   => esc_html__( 'Other', 'business-profile' ),
				'details' => esc_html__( 'Please share the reason', 'business-profile' ),
			),
		);
		?>
		<div class="bpfwp-deactivate-survey-modal" id="bpfwp-deactivate-survey-business-profile">
			<div class="bpfwp-deactivate-survey-wrap">
				<form class="bpfwp-deactivate-survey" method="post" data-installtime="<?php echo esc_attr( $install_time ); ?>">
					<span class="bpfwp-deactivate-survey-title"><span class="dashicons dashicons-testimonial"></span><?php echo ' ' . __( 'Quick Feedback', 'business-profile' ); ?></span>
					<span class="bpfwp-deactivate-survey-desc"><?php echo __('If you have a moment, please share why you are deactivating Five-Star Business Profile:', 'business-profile' ); ?></span>
					<div class="bpfwp-deactivate-survey-options">
						<?php foreach ( $options as $id => $option ) : ?>
							<div class="bpfwp-deactivate-survey-option">
								<label for="bpfwp-deactivate-survey-option-business-profile-<?php echo esc_attr( $id ); ?>" class="bpfwp-deactivate-survey-option-label">
									<input id="bpfwp-deactivate-survey-option-business-profile-<?php echo esc_attr( $id ); ?>" class="bpfwp-deactivate-survey-option-input" type="radio" name="code" value="<?php echo esc_attr( $id ); ?>" />
									<span class="bpfwp-deactivate-survey-option-reason"><?php echo esc_html( $option['title'] ); ?></span>
								</label>
								<?php if ( ! empty( $option['details'] ) ) : ?>
									<input class="bpfwp-deactivate-survey-option-details" type="text" placeholder="<?php echo esc_attr( $option['details'] ); ?>" />
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="bpfwp-deactivate-survey-footer">
						<button type="submit" class="bpfwp-deactivate-survey-submit button button-primary button-large"><?php _e('Submit and Deactivate', 'business-profile' ); ?></button>
						<a href="#" class="bpfwp-deactivate-survey-deactivate"><?php _e('Skip and Deactivate', 'business-profile' ); ?></a>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}

}