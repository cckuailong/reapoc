<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbDeactivationSurvey' ) ) {
/**
 * Class to handle plugin deactivation survey
 *
 * @since 2.0.15
 */
class rtbDeactivationSurvey {

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
		wp_enqueue_style( 'rtb-deactivation-css', RTB_PLUGIN_URL . '/assets/css/plugin-deactivation.css' );
		wp_enqueue_script( 'rtb-deactivation-js', RTB_PLUGIN_URL . '/assets/js/plugin-deactivation.js', array( 'jquery' ) );

		wp_localize_script( 'rtb-deactivation-js', 'rtb_deactivation_data', array( 'site_url' => site_url() ) );
	}

	public function add_deactivation_html() {
		
		$install_time = get_option( 'rtb-installation-time' );

		$options = array(
			1 => array(
				'title'   => esc_html__( 'I no longer need the plugin', 'restaurant-reservations' ),
			),
			2 => array(
				'title'   => esc_html__( 'I\'m switching to a different plugin', 'restaurant-reservations' ),
				'details' => esc_html__( 'Please share which plugin', 'restaurant-reservations' ),
			),
			3 => array(
				'title'   => esc_html__( 'I couldn\'t get the plugin to work', 'restaurant-reservations' ),
				'details' => esc_html__( 'Please share what wasn\'t working', 'restaurant-reservations' ),
			),
			4 => array(
				'title'   => esc_html__( 'It\'s a temporary deactivation', 'restaurant-reservations' ),
			),
			5 => array(
				'title'   => esc_html__( 'Other', 'restaurant-reservations' ),
				'details' => esc_html__( 'Please share the reason', 'restaurant-reservations' ),
			),
		);
		?>
		<div class="rtb-deactivate-survey-modal" id="rtb-deactivate-survey-restaurant-reservations">
			<div class="rtb-deactivate-survey-wrap">
				<form class="rtb-deactivate-survey" method="post" data-installtime="<?php echo $install_time; ?>">
					<span class="rtb-deactivate-survey-title"><span class="dashicons dashicons-testimonial"></span><?php echo ' ' . __( 'Quick Feedback', 'restaurant-reservations' ); ?></span>
					<span class="rtb-deactivate-survey-desc"><?php echo __('If you have a moment, please share why you are deactivating Five-Star Restaurant Reservations:', 'restaurant-reservations' ); ?></span>
					<div class="rtb-deactivate-survey-options">
						<?php foreach ( $options as $id => $option ) : ?>
							<div class="rtb-deactivate-survey-option">
								<label for="rtb-deactivate-survey-option-restaurant-reservations-<?php echo $id; ?>" class="rtb-deactivate-survey-option-label">
									<input id="rtb-deactivate-survey-option-restaurant-reservations-<?php echo $id; ?>" class="rtb-deactivate-survey-option-input" type="radio" name="code" value="<?php echo $id; ?>" />
									<span class="rtb-deactivate-survey-option-reason"><?php echo $option['title']; ?></span>
								</label>
								<?php if ( ! empty( $option['details'] ) ) : ?>
									<input class="rtb-deactivate-survey-option-details" type="text" placeholder="<?php echo $option['details']; ?>" />
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="rtb-deactivate-survey-footer">
						<button type="submit" class="rtb-deactivate-survey-submit button button-primary button-large"><?php _e('Submit and Deactivate', 'restaurant-reservations' ); ?></button>
						<a href="#" class="rtb-deactivate-survey-deactivate"><?php _e('Skip and Deactivate', 'restaurant-reservations' ); ?></a>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}

}