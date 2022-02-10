<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdufaqDeactivationSurvey' ) ) {
/**
 * Class to handle plugin deactivation survey
 *
 * @since 2.0.0
 */
class ewdufaqDeactivationSurvey {

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
		wp_enqueue_style( 'ewd-ufaq-deactivation-css', EWD_UFAQ_PLUGIN_URL . '/assets/css/plugin-deactivation.css' );
		wp_enqueue_script( 'ewd-ufaq-deactivation-js', EWD_UFAQ_PLUGIN_URL . '/assets/js/plugin-deactivation.js', array( 'jquery' ) );

		wp_localize_script( 'ewd-ufaq-deactivation-js', 'ewd_ufaq_deactivation_data', array( 'site_url' => site_url() ) );
	}

	public function add_deactivation_html() {
		
		$install_time = get_option( 'ewd-ufaq-installation-time' );

		$options = array(
			1 => array(
				'title'   => esc_html__( 'I no longer need the plugin', 'ultimate-faqs' ),
			),
			2 => array(
				'title'   => esc_html__( 'I\'m switching to a different plugin', 'ultimate-faqs' ),
				'details' => esc_html__( 'Please share which plugin', 'ultimate-faqs' ),
			),
			3 => array(
				'title'   => esc_html__( 'I couldn\'t get the plugin to work', 'ultimate-faqs' ),
				'details' => esc_html__( 'Please share what wasn\'t working', 'ultimate-faqs' ),
			),
			4 => array(
				'title'   => esc_html__( 'It\'s a temporary deactivation', 'ultimate-faqs' ),
			),
			5 => array(
				'title'   => esc_html__( 'Other', 'ultimate-faqs' ),
				'details' => esc_html__( 'Please share the reason', 'ultimate-faqs' ),
			),
		);
		?>
		<div class="ewd-ufaq-deactivate-survey-modal" id="ewd-ufaq-deactivate-survey-ultimate-faqs">
			<div class="ewd-ufaq-deactivate-survey-wrap">
				<form class="ewd-ufaq-deactivate-survey" method="post" data-installtime="<?php echo $install_time; ?>">
					<span class="ewd-ufaq-deactivate-survey-title"><span class="dashicons dashicons-testimonial"></span><?php echo ' ' . __( 'Quick Feedback', 'ultimate-faqs' ); ?></span>
					<span class="ewd-ufaq-deactivate-survey-desc"><?php echo __('If you have a moment, please share why you are deactivating Ultimate FAQs:', 'ultimate-faqs' ); ?></span>
					<div class="ewd-ufaq-deactivate-survey-options">
						<?php foreach ( $options as $id => $option ) : ?>
							<div class="ewd-ufaq-deactivate-survey-option">
								<label for="ewd-ufaq-deactivate-survey-option-ultimate-faqs-<?php echo $id; ?>" class="ewd-ufaq-deactivate-survey-option-label">
									<input id="ewd-ufaq-deactivate-survey-option-ultimate-faqs-<?php echo $id; ?>" class="ewd-ufaq-deactivate-survey-option-input" type="radio" name="code" value="<?php echo $id; ?>" />
									<span class="ewd-ufaq-deactivate-survey-option-reason"><?php echo $option['title']; ?></span>
								</label>
								<?php if ( ! empty( $option['details'] ) ) : ?>
									<input class="ewd-ufaq-deactivate-survey-option-details" type="text" placeholder="<?php echo $option['details']; ?>" />
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="ewd-ufaq-deactivate-survey-footer">
						<button type="submit" class="ewd-ufaq-deactivate-survey-submit button button-primary button-large"><?php _e('Submit and Deactivate', 'ultimate-faqs' ); ?></button>
						<a href="#" class="ewd-ufaq-deactivate-survey-deactivate"><?php _e('Skip and Deactivate', 'ultimate-faqs' ); ?></a>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}

}