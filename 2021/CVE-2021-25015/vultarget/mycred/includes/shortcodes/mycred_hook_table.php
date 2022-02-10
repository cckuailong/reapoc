<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook Table
 * Renders a table of all the active hooks and how much a user can
 * earn / lose from each hook.
 * @since 1.6
 * @version 1.1
 */
if ( ! function_exists( 'mycred_render_shortcode_hook_table' ) ) :
	function mycred_render_shortcode_hook_table( $atts ) {

		extract( shortcode_atts( array(
			'type'    => MYCRED_DEFAULT_TYPE_KEY,
			'gains'   => 1,
			'user'    => '-user-',
			'post'    => '-post-',
			'comment' => '-comment-',
			'amount'  => '',
			'nothing' => __( 'No instances found for this point type', 'mycred' )
		), $atts, MYCRED_SLUG . '_hook_table' ) );

		if ( ! mycred_point_type_exists( $type ) ) return __( 'Point type not found.', 'mycred' );

		$mycred     = mycred( $type );
		$id         = str_replace( '_', '-', $type );
		$prefs_key  = apply_filters( 'mycred_option_id', 'mycred_pref_hooks' );

		if ( $type != MYCRED_DEFAULT_TYPE_KEY )
			$prefs_key .= '_' . $type;

		$applicable = array();

		$hooks      = get_option( $prefs_key, false );
		if ( isset( $hooks['active'] ) && ! empty( $hooks['active'] ) ) {

			foreach ( $hooks['active'] as $active_hook_id ) {

				$hook_prefs = $hooks['hook_prefs'][ $active_hook_id ];

				// Single Instance
				if ( isset( $hook_prefs['creds'] ) ) {

					if ( ( $gains == 1 && $hook_prefs['creds'] > 0 ) || ( $gains == 0 && $hook_prefs['creds'] < 0 ) )
						$applicable[ $active_hook_id ] = $hook_prefs;

				}

				// Multiple Instances
				else {

					foreach ( $hook_prefs as $instance_id => $instance_prefs ) {

						if ( ! isset( $instance_prefs['creds'] ) ) continue;

						if ( ( $gains == 1 && $instance_prefs['creds'] > 0 ) || ( $gains == 0 && $instance_prefs['creds'] < 0 ) ){
							if($active_hook_id==='deleted_content' || $active_hook_id==='publishing_content' || $active_hook_id==='view_contents'  ){
								$applicable[ $active_hook_id."_".$instance_id ] = $instance_prefs;
							}
							else{
									$applicable[ $instance_id ] = $instance_prefs;
							}
						}

					}

				}

			}

		}

		ob_start();

		if ( ! empty( $applicable ) ) {

?>
<div class="table-responsive">
	<table class="table mycred-hook-table hook-table-<?php echo $id; ?>">
		<thead>
			<tr>
				<th class="column-instance" style="width: 60%;"><?php _e( 'Instance', 'mycred' ); ?></th>
				<th class="column-amount" style="width: 20%;"><?php _e( 'Amount', 'mycred' ); ?></th>
				<th class="column-limit" style="width: 20%;"><?php _e( 'Limit', 'mycred' ); ?></th>
			</tr>
		</thead>
		<tbody>
<?php

			foreach ( $applicable as $id => $prefs ) {

				$log = $mycred->template_tags_general( $prefs['log'] );

				$log = strip_tags( $log );
				$log = str_replace( array( '%user_id%', '%user_name%', '%user_name_en%', '%display_name%', '%user_profile_url%', '%user_profile_link%', '%user_nicename%', '%user_email%', '%user_url%', '%balance%', '%balance_f%' ), $user, $log );
				$log = str_replace( array( '%post_title%', '%post_url%', '%link_with_title%', '%post_type%' ), $post, $log );
				$log = str_replace( array( 'comment_id', 'c_post_id', 'c_post_title', 'c_post_url', 'c_link_with_title' ), $comment, $log );
				$log = str_replace( array( '%cred%', '%cred_f%' ), $amount, $log );
				$log = apply_filters( 'mycred_hook_table_log', $log, $id, $prefs, $atts );

				$limit = '';
				if ( isset( $prefs['limit'] ) )
					$limit = $prefs['limit'];
					
				if( $id == "approved" ) {
				    if ( isset( $hooks["hook_prefs"]["comments"]["limits"] ) ) {
				        $approved_limits = $hooks["hook_prefs"]["comments"]["limits"];
				        if( (int) $approved_limits["per_post"] > 0 && (int) $approved_limits["per_day"] > 0 ) {
				        	
				            $limit = sprintf( __( 'Maximum %s times per post and Maximum %s times per day', 'mycred' ), $approved_limits["per_post"],  $approved_limits["per_day"] );
				        }
				        elseif( (int) $approved_limits["per_post"] > 0 && (int) $approved_limits["per_day"] < 1 ) {
				        	
				            $limit = sprintf( __( 'Maximum %s times per post', 'mycred' ), $approved_limits["per_post"] );
				        }
				        elseif( (int) $approved_limits["per_post"] < 1 && (int) $approved_limits["per_day"] > 0 ) {

				            $limit = sprintf( __( 'Maximum %s times per day', 'mycred' ), $approved_limits["per_day"] );
				        }
				        else {
				            $limit = __('No limit', 'mycred');
				        }
				    }   
			    }
			    else {
			        $limit = mycred_translate_limit_code( $limit, $id, $mycred );
			    }

				$creds = apply_filters( 'mycred_hook_table_creds', $mycred->format_creds( $prefs['creds'] ), $id, $prefs, $atts );

?>
			<tr>
				<td class="column-instance"><?php echo $log; ?></td>
				<td class="column-amount"><?php echo $creds; ?></td>
				<td class="column-limit"><?php echo $limit; ?></td>
			</tr>
<?php

			}

?>
		</tbody>
	</table>
</div>
<?php

		}
		else {
			echo '<p>' . $nothing . '</p>';
		}

		$content = ob_get_contents();
		ob_end_clean();

		return apply_filters( 'mycred_render_hook_table', $content, $atts );

	}
endif;
add_shortcode( MYCRED_SLUG . '_hook_table', 'mycred_render_shortcode_hook_table' );
