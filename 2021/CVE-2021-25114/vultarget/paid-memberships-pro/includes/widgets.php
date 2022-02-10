<?php
/**
 * Widgets for Paid Memberships Pro
 *
 */


/**
 * Member Login Widget for Paid Memberships Pro
 *
 */
class PMPro_Widget_Member_Login extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_pmpro_member_login',
			'description' => __( 'Display a login form and optional "Logged In" member content.', 'paid-memberships-pro' ),
		);
		parent::__construct( 'pmpro-member-login', esc_html__( 'Log In - PMPro', 'paid-memberships-pro' ), $widget_ops );
		$this->alt_option_name = 'widget_pmpro_member_login';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_pmpro_member_login', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start(); ?>
		
		<?php
			// Get widget settings for this instance.
			extract( $args );

			$display_if_logged_in = isset( $instance['display_if_logged_in'] ) ? $instance['display_if_logged_in'] : false;

			$show_menu = isset( $instance['show_menu'] ) ? $instance['show_menu'] : false;

			$show_logout_link = isset( $instance['show_logout_link'] ) ? $instance['show_logout_link'] : false;
		?>

		<?php
			// Display the widget if there is anything to show.			
			$content = pmpro_login_forms_handler( $show_menu, $show_logout_link, $display_if_logged_in, 'widget', false );			
			if ( ! empty( $content ) ) {
				echo $before_widget;
				echo $content;
				echo $after_widget;
			}
		?>
			
		<?php if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_pmpro_member_login', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['display_if_logged_in'] = isset( $new_instance['display_if_logged_in'] ) ? (bool) $new_instance['display_if_logged_in'] : false;
		$instance['show_menu'] = isset( $new_instance['show_menu'] ) ? (bool) $new_instance['show_menu'] : false;
		$instance['show_logout_link'] = isset( $new_instance['show_logout_link'] ) ? (bool) $new_instance['show_logout_link'] : false;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_pmpro_member_login'] ) ) {
			delete_option( 'widget_pmpro_member_login' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_pmpro_member_login', 'widget' );
	}

	function form( $instance ) { 
		$display_if_logged_in = isset( $instance['display_if_logged_in'] ) ? (bool) $instance['display_if_logged_in'] : false;
		$show_menu = isset( $instance['show_menu'] ) ? (bool) $instance['show_menu'] : false;
		$show_logout_link = isset( $instance['show_logout_link'] ) ? (bool) $instance['show_logout_link'] : false;
		?>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $display_if_logged_in ); ?> id="<?php echo $this->get_field_id( 'display_if_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'display_if_logged_in' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'display_if_logged_in' ); ?>"><?php esc_html_e( 'Display "Welcome" content when logged in.', 'paid-memberships-pro' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_logout_link ); ?> id="<?php echo $this->get_field_id( 'show_logout_link' ); ?>" name="<?php echo $this->get_field_name( 'show_logout_link' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_logout_link' ); ?>"><?php esc_html_e( 'Display a "Log Out" link.', 'paid-memberships-pro' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_menu ); ?> id="<?php echo $this->get_field_id( 'show_menu' ); ?>" name="<?php echo $this->get_field_name( 'show_menu' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_menu' ); ?>"><?php esc_html_e( 'Display the "Log In Widget" menu.', 'paid-memberships-pro' ); ?></label>
		</p>
		<?php
			$allowed_nav_menus_link_html = array (
				'a' => array (
					'href' => array(),
					'target' => array(),
					'title' => array(),
				),
			);
			echo '<p class="description">' . sprintf( wp_kses( __( 'Customize this menu per level using the <a href="%s" title="Paid Memberships Pro - Nav Menus Add On" target="_blank">Nav Menus Add On</a>. Assign the menu under Appearance > Menus.', 'paid-memberships-pro' ), $allowed_nav_menus_link_html ), 'https://www.paidmembershipspro.com/add-ons/pmpro-nav-menus/?utm_source=plugin&utm_medium=pmpro-membershiplevels&utm_campaign=add-ons&utm_content=nav-menus' ) . '</p>';
	}
}
/* End Member Login Widget */

/**
 * Register the Widgets
 */
function pmpro_register_widgets() {
	register_widget( 'PMPro_Widget_Member_Login' );
}
add_action( 'widgets_init', 'pmpro_register_widgets' );
