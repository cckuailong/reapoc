<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: mycred_video
 * This shortcode allows points to be given to the current user
 * for watchinga YouTube video.
 * @see http://codex.mycred.me/shortcodes/mycred_video/
 * @since 1.2
 * @version 1.2.2
 */
if ( ! function_exists( 'mycred_render_shortcode_video' ) ) :
	function mycred_render_shortcode_video( $atts ) {

		global $mycred_video_points;

		extract( shortcode_atts( array(
			'id'       => NULL,
			'width'    => 560,
			'height'   => 315,
			'amount'   => '',
			'logic'    => '',
			'interval' => '',
			'ctype'    => MYCRED_DEFAULT_TYPE_KEY
		), $atts, MYCRED_SLUG . '_video' ) );

		$prf_hook = apply_filters( 'mycred_option_id', 'mycred_pref_hooks' );
		$hooks    = mycred_get_option( $prf_hook, false );
		if ( $ctype != MYCRED_DEFAULT_TYPE_KEY )
			$hooks = mycred_get_option( 'mycred_pref_hooks_' . sanitize_key( $ctype ), false );

		if ( $hooks === false || ! is_array( $hooks ) || ! array_key_exists( 'video_view', $hooks['hook_prefs'] ) ) return;
		$prefs    = $hooks['hook_prefs']['video_view'];

		if ( $amount == '' )
			$amount = $prefs['creds'];

		if ( $logic == '' )
			$logic = $prefs['logic'];

		if ( $interval == '' )
			$interval = $prefs['interval'];

		// ID is required
		if ( $id === NULL || empty( $id ) ) return __( 'A video ID is required for this shortcode', 'mycred' );

		// Interval
		if ( strlen( $interval ) < 3 ) {
		   $interval = (float) $interval;
           $interval = abs( $interval * 1000 );
        }

		// Video ID
		$video_id = str_replace( '-', '__', $id );

		// Create key
		$key      = mycred_create_token( array( 'youtube', $video_id, $amount, $logic, $interval, $ctype ) );

		if ( ! isset( $mycred_video_points ) || ! is_array( $mycred_video_points ) )
			$mycred_video_points = array();

		// Construct YouTube Query
		$query    = apply_filters( 'mycred_video_query_youtube', array(
			'enablejsapi' => 1,
			'version'     => 3,
			'playerapiid' => 'mycred_vvideo_v' . $video_id,
			'rel'         => 0,
			'controls'    => 1,
			'showinfo'    => 0
		), $atts, $video_id );

		if ( ! is_user_logged_in() )
			unset( $query['playerapiid'] );

		// Construct Youtube Query Address
		$url      = 'https://www.youtube.com/embed/' . $id;
		$url      = add_query_arg( $query, $url );

		$mycred_video_points[] = 'youtube';

		// Make sure video source ids are unique
		$mycred_video_points   = array_unique( $mycred_video_points );

		ob_start();

?>
<div class="row mycred-video-wrapper youtube-video">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<iframe id="mycred_vvideo_v<?php echo $video_id; ?>" class="mycred-video mycred-youtube-video" data-vid="<?php echo $video_id; ?>" src="<?php echo esc_url( $url ); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
	</div>
</div>
<?php

		if ( is_user_logged_in() ) :

?>
<script type="text/javascript">function mycred_vvideo_v<?php echo $video_id; ?>( state ) { duration[ "<?php echo $video_id; ?>" ] = state.target.getDuration(); mycred_view_video( "<?php echo $video_id; ?>", state.data, "<?php echo $logic; ?>", "<?php echo $interval; ?>", "<?php echo $key; ?>", "<?php echo $ctype; ?>" ); }</script>
<?php

		endif;

		$output = ob_get_contents();
		ob_end_clean();

		// Return the shortcode output
		return apply_filters( 'mycred_video_output', $output, $atts );

	}
endif;
add_shortcode( MYCRED_SLUG . '_video', 'mycred_render_shortcode_video' );
