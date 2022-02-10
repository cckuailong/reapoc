<?php
if ( ! defined( 'MYCRED_CASHCRED' ) ) exit;

/**
 * Shortcode: mycred_cashcred
 * @see http://codex.mycred.me/shortcodes/mycred_cashcred/
 * @since 1.0
 * @version 1.3
 */
  
if ( ! function_exists( 'mycred_render_cashcred' ) ) :
	function mycred_render_cashcred( $atts = array(), $content = '' ) {

		extract( shortcode_atts( array(
			'button'       => __( 'Submit Request', 'mycred' ),
			'gateways'     => '',
			'types'        => '',
			'amount'       => '',
			'excluded'     => 'You have excluded from this point type.',
			'insufficient' => 'Insufficient Points for Withdrawal.'
		), $atts, MYCRED_SLUG . '_cashcred' ) );

		// If we are not logged in
		if ( ! is_user_logged_in() ) return $content;

		global $cashcred_instance, $mycred_modules, $cashcred_withdraw;
		
		// Prepare
		$user_id = get_current_user_id();

		$gateways = cashcred_get_usable_gateways( $gateways );

		// Make sure we have a gateway we can use
		if ( empty( $gateways ) ) return __( 'No gateway available.', 'mycred' );

		$point_types = cashcred_get_point_types( $types, $user_id );
		
		//We are excluded
		if ( empty( $point_types ) ) return $excluded;

		$point_types = cashcred_is_user_have_balances( $point_types, $user_id );

		//Insufficient points for withdrawal.
		if ( empty( $point_types ) ) return $insufficient;

		// From this moment on, we need to indicate the shortcode usage for scripts and styles.
		$cashcred_withdraw = true;

		// Button Label
		$button_label = $point_types[ current(array_keys($point_types)) ]->template_tags_general( $button );

		ob_start();
			
		$pending_withdrawal = cashcred_get_withdraw_requests('Pending');
	?>
<div id="cashcred" class="container">
	<ul class="cashcred-nav-tabs">
		<li id="tab1" class="active"><a href="javascript:void(0);"><?php _e( 'Withdraw Request', 'mycred' ); ?></a></li>
		<li id="tab2"><a href="javascript:void(0);"><?php _e( 'Approved Requests', 'mycred' ); ?></a></li>
		<li id="tab3"><a href="javascript:void(0);"><?php _e( 'Cancelled Requests', 'mycred' ); ?></a></li>
		<li id="tab4"><a href="javascript:void(0);"><?php _e( 'Payment Settings', 'mycred' ); ?></a></li>
	</ul>
	<div id="cashcred_tab_content">
		<!--------First tab--------->
		<div id="tab1c" class="cashcred-tab">
			<?php cashcred_display_message(); ?>
			
			<?php if( count( $pending_withdrawal ) > 0 ){ ?>
			<h4><?php  echo "You have pending withdrawal"; ?></h4>
			<table>
				<thead>
					<tr>
						<th><span class="nobr">ID</span></th>
						<th><span class="nobr">Points</span></th>
						<th><span class="nobr">Amount</span></th>
						<th><span class="nobr">Point Type</span></th>
						<th><span class="nobr">Gateway</span></th>
						<th><span class="nobr">Date</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $pending_withdrawal as $post ):?>
					<tr>
						<td><?php echo $post->post_name; ?></td>
						<td><?php echo get_post_meta($post->ID,'points',true);?></td>
						<td>
							<?php echo get_post_meta($post->ID,'currency',true). " " .get_post_meta($post->ID,'points',true) * get_post_meta($post->ID,'cost',true); ?>
						</td>
						<td><?php echo mycred_get_types()[get_post_meta($post->ID,'point_type',true)];?></td>
						<td>
					 		<?php 
								$gateway = get_post_meta($post->ID,'gateway',true);
								$installed = $mycred_modules['solo']['cashcred']->get();
								if ( isset( $installed[ $gateway ] ) )
									echo $installed[ $gateway ]['title'];
								else
									echo $gateway;
							?>
						</td>
						<td><?php echo $post->post_date; ?></td>
					</tr>
					<?php endforeach;?>				
				</tbody>
			</table>	
			<?php } else {
                $mycred_cashcred_gateway_notice = apply_filters( 'mycred_cashcred_gateway_notice', 'Selected gateway details are incomplete.' );
			    ?>

			<div class="cashcred_gateway_notice"><?php _e( $mycred_cashcred_gateway_notice, 'mycred' ) ?></div>

			<form method="post" class="mycred-cashcred-form" action="">

				<?php wp_nonce_field( 'cashCred-withdraw-request', 'cashcred_withdraw_wpnonce' ); ?>
				
				<?php if( count( $point_types ) > 1 ) {?>
					<div class="form-group"> 
						<label for="gateway"><?php _e( 'Withdraw Point Type', 'mycred' ); ?></label>
						<select id="cashcred_point_type" name="cashcred_point_type" class="form-control">
							<?php 
								foreach( $point_types as $point_type_id => $point_type_obj ) {
									echo '<option value="' . $point_type_id . '">' . esc_html( $point_type_obj->plural() ) . '</option>'; 
								}
							?>
						</select>
					</div>
				<?php } else {?>
					<input type="hidden" id="cashcred_point_type" name="cashcred_point_type" value="<?php echo esc_attr( current(array_keys($point_types)) ); ?>" />
				<?php } ?>

				<?php if ( count( $gateways ) > 1 ) { ?>
					<div class="form-group"> 
						<label for="gateway"><?php _e( 'Withdrawal Payment Method', 'mycred' ); ?></label>
						<select id="cashcred_pay_method" name="cashcred_pay_method" class="form-control">
							<?php 
								foreach ( $gateways as $gateway_id => $gateway_data ) {
									echo '<option value="' . esc_attr( $gateway_id ) . '">' . esc_html( $gateway_data['title'] ) . '</option>';
								}
							?>
						</select>
					</div>
				<?php } else { ?>
					<input type="hidden" id="cashcred_pay_method" name="cashcred_pay_method" value="<?php echo esc_attr( current(array_keys($gateways)) ); ?>" />
				<?php } ?>

			<div class="form-group">  
				<label><?php echo sprintf( __('Withdraw %s value', 'mycred'), $point_types[ current(array_keys($point_types)) ]->plural() ); ?></label>
				<?php 
					$amount = ! empty( $amount ) ? floatval( $amount ) : 0;
				?> 
				<input type="number" id="withdraw_points" name="points" class="form-control" placeholder="0" value="<?php echo ! empty($amount) ? $amount : 0; ?>" required />
				<p class="cashcred-min"><?php echo __('Minimum Amount: ');?><span></span></p>
			</div>
			<div id="cashcred_total" class="form-group">
				<strong>
					<span class="amount_label">Amount:&nbsp;</span>
					<span id="cashcred_currency_symbol"></span> 
					<span id="cashcred_total_amount"></span>
				</strong>
			</div>
			<div id="submit_button" class="form-group">
				<input type="submit" class="button btn btn-block btn-lg" value="<?php echo $button_label; ?>" />
			</div>
		</form>
		<?php } ?>
		</div>
		<!--------End First tab--------->

		<!--------Secound tab--------->
		<div id="tab2c" class="cashcred-tab">
			<h4>Approved Requests</h4>
			<?php	
				$posts = cashcred_get_withdraw_requests('Approved');
			?>		
			<table>
				<thead>
					<tr>
						<th><span class="nobr">ID</span></th>
						<th><span class="nobr">Points</span></th>
						<th><span class="nobr">Amount</span></th>
						<th><span class="nobr">Point Type</span></th>
						<th><span class="nobr">Gateway</span></th>
						<th><span class="nobr">Date</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($posts as $post) {?>
					<tr>
						<td><?php echo $post->post_name; ?></td>
						<td><?php echo get_post_meta($post->ID,'points',true);?></td>
						<td>
							<?php echo  get_post_meta($post->ID,'currency',true). " " .get_post_meta($post->ID,'points',true) * get_post_meta($post->ID,'cost',true);?>
						</td>
						<td><?php echo mycred_get_types()[get_post_meta($post->ID,'point_type',true)]; ?></td>
						<td>
							<?php 
								$gateway = get_post_meta($post->ID,'gateway',true);
								$installed = $mycred_modules['solo']['cashcred']->get();
								if ( isset( $installed[ $gateway ] ) )
									echo $installed[ $gateway ]['title'];
								else
									echo $gateway;
							?>
						</td>
						<td><?php echo $post->post_date; ?></td>
					</tr>
					<?php } ?>
		 		</tbody>
			</table>
		</div>
		<!--------End Secound tab--------->

		<!--------Third tab--------->
		<div id="tab3c" class="cashcred-tab">
			<h4>Cancelled Requests</h4>
			<?php
				$posts = cashcred_get_withdraw_requests('Cancelled');
			?>
			<table>
				<thead>
					<tr>
						<th><span class="nobr">ID</span></th>
						<th><span class="nobr">Points</span></th>
						<th><span class="nobr">Amount</span></th>
						<th><span class="nobr">Point Type</span></th>
						<th><span class="nobr">Gateway</span></th>
						<th><span class="nobr">Date</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($posts as $post) {?>
					<tr>
					 	<td><?php echo $post->post_name; ?></td>
						
						<td><?php echo get_post_meta($post->ID,'points',true);?></td>
						<td>
							<?php echo get_post_meta($post->ID,'currency',true). " " .get_post_meta($post->ID,'points',true) * get_post_meta($post->ID,'cost',true);?>
						</td>
						<td><?php echo mycred_get_types()[get_post_meta($post->ID,'point_type',true)];?></td>
						<td>
						<?php 
							$gateway = get_post_meta($post->ID,'gateway',true);
							$installed = $mycred_modules['solo']['cashcred']->get();
							if ( isset( $installed[ $gateway ] ) )
								echo $installed[ $gateway ]['title'];
							else
								echo $gateway;
							?>
						</td>
						<td><?php echo $post->post_date; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<!--------End Third tab--------->

		<!--------Fourth tab--------->
		<div id="tab4c" class="cashcred-tab">
			<form action="" method="POST">
				<?php if ( count( $gateways ) > 1 ):?>
					<select class="form-control" name="cashcred_save_settings" id="cashcred_save_settings">
						<?php 
							foreach ( $gateways as $key => $active_gateways_value ) {
								echo '<option value="' . $key . '"> '. $active_gateways_value['title'] .' </option>';
							}
						?>
					</select>
				<?php else:?>
					<input type="hidden" name="cashcred_save_settings" id="cashcred_save_settings" value="<?php echo esc_attr( current(array_keys($gateways)) ); ?>" />
				<?php endif;?>
				<?php 
					wp_nonce_field( 'cashCred-payment-settings', 'cashcred_settings_wpnonce' );

				    foreach ( $gateways as $key => $active_gateways_value ) {
						
						$MyCred_payment_setting_call = new $active_gateways_value['callback'][0]($key);
						$MyCred_payment_setting_call->cashcred_payment_settings($key) ;
							
					}
				?>
				<div id="cashcred_save_settings" class="form-group">
					<input type="submit" class="button btn btn-block btn-lg" value="Save" />
				</div>
			</form>	
		</div>
		<!--------End Fourth tab--------->

	</div>
</div>
<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
endif;