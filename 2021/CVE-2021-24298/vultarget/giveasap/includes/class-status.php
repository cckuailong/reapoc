<?php

if( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class for creating a custom status
 */
class GA_Custom_Status {

	/**
	 * Post Types for this status
	 * @var array
	 */
	protected $post_type = array();

	/**
	 * Status slug
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Enable the button
	 * 'true', 'publish'	mixed		enables publishing from this status
	 * 'update'		string		enables updating from this status
	 * 'false'		boolean 	disabled/removes the button
	 * @var boolean
	 */
	protected $enable_action= false;

	/**
	 * Default definitons
	 * @var array
	 */
	protected $defaults = array(
		'label' => '',
		'public' => null,
		'protected' => null,
		'private' => null,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'internal' => null,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => false,
		'label_count' => ''
		);

	/**
	 * Status settings
	 * @var array
	 */
	public $settings = array();

	/**
	 * Registering the status on creation and hooking scripts
	 * @param array $args
	 */
	public function __construct( $args = array() ) {

		if( empty( $args ) ) {
			return;
		}

		if( ! isset( $args['post_type'] ) || empty( $args['post_type'] ) ) {
			return;
		}

		if( ! isset( $args['slug'] ) || $args['slug'] == '' ) {
			return;
		}

		$this->post_type = $args['post_type'];
		$this->slug = $args['slug'];

		if( isset( $args['action'] ) && in_array( $args['action'], array( false, true, 'publish', 'update' ) ) ) {
			$this->enable_action = $args['action'];
		}

		if( ! isset( $args['label'] ) || $args['label'] == '' ) {
			$args['label'] = ucfirst( $args['slug'] );
		}

		if( ! isset( $args['label_count'] ) || $args['label_count'] == '' ) {
			$args['label_count'] = _n_noop( $args['label'] . ' <span class="count">(%s)</span>',  $args['label'] . ' <span class="count">(%s)</span>');
		}

		unset( $args['slug'] );
		unset( $args['post_type'] );
		unset( $args['action'] );

		$this->settings = wp_parse_args( $args, $this->defaults );
		$this->register_status();
		add_action( 'admin_footer', array( $this, 'set_status' ) );
	}

	/**
	 * Registering the status
	 * @return void 
	 */
	public function register_status() {
		register_post_status( $this->slug, $this->settings );
	}

	/**
	 * Setting the script for adding status to dropdown
	 */
	public function set_status() {

		$set_status = apply_filters( 'ibenic_custom_post_status_' . $this->slug, true );

		if( ! $set_status ) {
			return;
		}
		
		global $post;

		if( ! $post ){
			return;
		}

		if( ! in_array( $post->post_type, $this->post_type ) ) {
			return;
		}

		$complete = '';
		$label = '';

		if( $post->post_status == $this->slug ) {
			$complete = ' selected=\"selected\"';
			$label = '<span id=\"post-status-display\">' . $this->settings['label'] . '</span>';
		}
		?>
		<script>
			( function($){
				$(document).ready(function(){
					$('select#post_status').append( "<option value='<?php echo $this->slug; ?>' <?php echo $complete; ?>><?php echo $this->settings['label']; ?></option>");
					$('.misc-pub-section label').append( "<?php echo $label; ?>");
					<?php if( $complete != '' ) {
						// If the post has this status check the preferred action
						// If true or 'publish', we leave it as default
						if( ! $this->enable_action ) {
							echo '$("#publish").remove();';
						} elseif( $this->enable_action === 'update' ) {
							echo '$("#publish").val("Update");$("#publish").attr("name","save");$("#original_publish").val("Update");';
						}
					} ?>
				});
			})( jQuery );
		</script>
		<?php
	}

}

// Adding custom statuses

new GA_Custom_Status( array(

	'post_type' => array( 'giveasap'),
	'slug' => 'giveasap_ended',
	'public' => !is_admin(),
	'label' => _x( 'Ended', 'giveasap' ),
	'action' => 'update',
	'show_in_admin_all_list' => false,
	'show_in_admin_status_list' => true,
	'label_count' => _n_noop( 'Ended <span class="count">(%s)</span>', 'Ended <span class="count">(%s)</span>', 'giveasap' ),
	));

new GA_Custom_Status( array(
	'post_type' => array( 'giveasap' ),
	'slug' => 'giveasap_winners',
	'public' => !is_admin(),
	'label' => _x( 'Selected Winners', 'giveasap' ),
	'action' => 'update',
	'show_in_admin_all_list' => false,
	'show_in_admin_status_list' => true,
	'label_count' => _n_noop( 'Selected Winners <span class="count">(%s)</span>', 'Selected Winners <span class="count">(%s)</span>', 'giveasap' ),
));

new GA_Custom_Status( array(
	'post_type' => array( 'giveasap' ),
	'slug' => 'giveasap_notified',
	'public' => !is_admin(),
	'label' => _x( 'Winners Notified', 'giveasap' ),
	'action' => 'update',
	'show_in_admin_all_list' => false,
	'show_in_admin_status_list' => true,
	'label_count' => _n_noop( 'Winners Notified <span class="count">(%s)</span>', 'Winners Notified <span class="count">(%s)</span>', 'giveasap' ),
));

