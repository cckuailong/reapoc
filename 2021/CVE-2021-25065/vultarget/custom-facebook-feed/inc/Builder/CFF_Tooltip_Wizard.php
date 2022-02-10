<?php
/**
 * CFF Tooltip Wizard
 *
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder;

class CFF_Tooltip_Wizard {

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	function __construct(){
		$this->init();
	}


	/**
	 * Initialize class.
	 *
	 * @since 4.0
	 */
	public function init() {
		/*
		if (
			! wpforms_is_admin_page( 'builder' ) &&
			! wp_doing_ajax() &&
			! $this->is_form_embed_page()
		) {
			return;
		}
		*/

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 4.0
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
		add_action( 'admin_footer', [ $this, 'output' ] );
	}


	/**
	 * Enqueue assets.
	 *
	 * @since 4.0
	 */
	public function enqueues() {

		wp_enqueue_style(
			'cff-tooltipster-css',
			CFF_PLUGIN_URL . 'admin/builder/assets/css/tooltipster.css',
			null,
			CFFVER
		);

		wp_enqueue_script(
			'cff-tooltipster-js',
			CFF_PLUGIN_URL . 'admin/builder/assets/js/jquery.tooltipster.min.js',
			[ 'jquery' ],
			CFFVER,
			true
		);

		wp_enqueue_script(
			'cff-admin-tooltip-wizard',
			CFF_PLUGIN_URL . 'admin/builder/assets/js/tooltip-wizard.js',
			[ 'jquery' ],
			CFFVER
		);

		$wp_localize_data = [];
		if( $this->check_gutenberg_wizard() ){
			$wp_localize_data['cff_wizard_gutenberg'] = true;
		}

		wp_localize_script(
			'cff-admin-tooltip-wizard',
			'cff_admin_tooltip_wizard',
			$wp_localize_data
		);
	}

	/**
	 * Output HTML.
	 *
	 * @since 4.0
	 */
	public function output() {
		if( $this->check_gutenberg_wizard() ){
			$this->gutenberg_tooltip_output();
		}

	}

	/**
	 * Gutenberg Tooltip Output HTML.
	 *
	 * @since 4.0
	 */
	public function check_gutenberg_wizard() {
		global $pagenow;
		return  (	( $pagenow == 'post.php' ) || (get_post_type() == 'page') )
				&& ! empty( $_GET['cff_wizard'] );
	}


	/**
	 * Gutenberg Tooltip Output HTML.
	 *
	 * @since 4.0
	 */
	public function gutenberg_tooltip_output() {
		?>
		<div id="cff-gutenberg-tooltip-content">
			<div class="cff-tlp-wizard-cls cff-tlp-wizard-close"></div>
			<div class="cff-tlp-wizard-content">
				<strong class="cff-tooltip-wizard-head"><?php echo __('Add a Block','custom-facebook-feed') ?></strong>
				<p class="cff-tooltip-wizard-txt"><?php echo __('Click the plus button, search for Custom Facebook','custom-facebook-feed'); ?>
                    <br/><?php echo __('Feed, and click the block to embed it.','custom-facebook-feed') ?> <a href="https://smashballoon.com/doc/wordpress-5-block-page-editor-gutenberg/?facebook" rel="noopener" target="_blank"><?php echo __('Learn More','custom-facebook-feed') ?></a></p>
				<div class="cff-tooltip-wizard-actions">
					<button class="cff-tlp-wizard-close"><?php echo __('Done','custom-facebook-feed') ?></button>
				</div>
			</div>
		</div>
		<?php
	}


}