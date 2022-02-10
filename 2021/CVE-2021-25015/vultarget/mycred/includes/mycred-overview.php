<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Dashboard Widget: Overview
 * @see https://codex.wordpress.org/Example_Dashboard_Widget
 * @since 1.3.3
 * @version 1.3.3
 */
if ( ! class_exists( 'myCRED_Dashboard_Widget_Overview' ) ) :
	class myCRED_Dashboard_Widget_Overview {

		const mycred_wid = 'mycred_overview';

		/**
		 * Init Widget
		 */
		public static function init() {

			if ( ! current_user_can( apply_filters( 'mycred_overview_capability', 'edit_theme_options' ) ) ) return;

			// Add widget
			wp_add_dashboard_widget(
				self::mycred_wid,
				sprintf( __( '%s Overview', 'mycred' ), mycred_label() ),
				array( 'myCRED_Dashboard_Widget_Overview', 'widget' )
			);

			add_action( 'admin_enqueue_scripts', array( 'myCRED_Dashboard_Widget_Overview', 'enqueue' ) );

		}

		/**
		 * Widget Enqueue
		 */
		public static function enqueue() {

			$screen = get_current_screen();

			if ( $screen->id == 'dashboard' ) {

?>
<style type="text/css">
#mycred_overview .inside { margin: 0; padding: 0; }
div.overview-module-wrap { margin: 0; padding: 0; }

div.overview-module-wrap div.module-title { line-height: 48px; height: 48px; font-size: 18px; border-bottom: 1px solid #eee; }
div.overview-module-wrap div.module-title a { float: right; padding-right: 12px; }
div.overview-module-wrap div.module-title .type-icon { display: block; width: 48px; height: 48px; float: left; line-height: 48px; text-align: center; }
div.overview-module-wrap div.module-title .type-icon > div { line-height: inherit; }
div.overview-module-wrap div.module-title .type-label { display: block; float: left; line-height: 48px; height: 48px; padding-right: 12px; }
div.overview-module-wrap div.module-title svg { display: block; float: left; height: 20px; margin: 14px 0; }
div.overview-module-wrap div.mycred-type { border-top: 1px solid #ddd; }
div.overview-module-wrap div.mycred-type.first { border-top: none; }
div.overview-module-wrap div.mycred-type .overview { padding: 0; float: none; clear: both; margin-bottom: -1px; }
div.overview-module-wrap div.mycred-type .overview .section { height: 48px; float: left; margin: 0; border-right: 1px solid #eee; }
div.overview-module-wrap div.mycred-type .overview .section.border { border-bottom: 1px solid #eee; }
div.overview-module-wrap div.mycred-type .overview .section.dimm p { opacity: 0.3; }
div.overview-module-wrap div.mycred-type .overview .section:last-child { border-right: none; }
div.overview-module-wrap div.mycred-type .overview .section strong { padding: 0 6px 0 12px; }
</style>
<?php

				do_action( 'mycred_overview_enqueue' );

			}

		}

		/**
		 * Widget output
		 */
		public static function widget() {

			global $wpdb;

			$counter = 0;
			$types   = mycred_get_types();

?>
<div class="overview-module-wrap clear">
<?php

			do_action( 'mycred_overview_before', $types );

			foreach ( $types as $point_type => $label ) {

				$mycred       = mycred( $point_type );

				$page         = MYCRED_SLUG;
				if ( $point_type != MYCRED_DEFAULT_TYPE_KEY )
					$page .= '_' . $point_type;

				$url          = admin_url( 'admin.php?page=' . $page );
				$total        = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( meta_value ) FROM {$wpdb->usermeta} WHERE meta_key = %s;", mycred_get_meta_key( $point_type ) ) );

				if ( $total === NULL ) $total = $mycred->zero();

				$cred_type=mycred_get_meta_key($point_type);
				$data         = $wpdb->get_row("SELECT SUM( CASE WHEN creds > 0 THEN creds END) as gains, SUM( CASE WHEN creds < 0 THEN creds END) as losses FROM {$mycred->log_table} WHERE ctype='{$cred_type}';");

				$awarded      = ( isset( $data->gains ) ) ? $data->gains : 0;
				$awarded_url  = add_query_arg( array( 'num' => 0, 'compare' => urlencode( '>' ) ), $url );

				$deducted     = ( isset( $data->losses ) ) ? $data->losses : 0;
				$deducted_url = add_query_arg( array( 'num' => 0, 'compare' => urlencode( '<' ) ), $url );

?>
	<div class="mycred-type clear<?php if ( $counter == 0 ) echo ' first'; ?>">
		<div class="module-title">

			<div class="type-icon"><div class="dashicons dashicons-star-filled"></div></div>

			<span class="type-label"><?php echo $mycred->plural(); ?></span>

			<?php do_action( 'mycred_overview_total_' . $point_type, $point_type, $total, $data ); ?>

			<a href="<?php echo $url; ?>" title="<?php _e( 'Total amount in circulation', 'mycred' ); ?>"><?php echo $mycred->format_creds( $total ); ?></a>

		</div>
		<div class="overview clear">
			<div class="section border" style="width: 50%;">
				<p>

					<strong style="color:green;"><?php _e( 'Awarded', 'mycred' ); ?>:</strong>

					<?php do_action( 'mycred_overview_awarded_' . $point_type, $point_type, $total, $data ); ?>

					<a href="<?php echo esc_url( $awarded_url ); ?>"><?php echo $mycred->format_creds( $awarded ); ?></a>

				</p>
			</div>
			<div class="section border" style="width: 50%; margin-left: -1px;">
				<p>

					<strong style="color:red;"><?php _e( 'Deducted', 'mycred' ); ?>:</strong>

					<?php do_action( 'mycred_overview_deducted_' . $point_type, $point_type, $total, $data ); ?>

					<a href="<?php echo esc_url( $deducted_url ); ?>"><?php echo $mycred->format_creds( $deducted ); ?></a>

				</p>
			</div>
		</div>
	</div>
<?php
				$counter++;

			}

			do_action( 'mycred_overview_after', $types );

?>
	<div class="clear"></div>
</div>
<?php

		}

	}
endif;
add_action( 'wp_dashboard_setup', array( 'myCRED_Dashboard_Widget_Overview', 'init' ) );
