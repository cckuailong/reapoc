<?php $model = $this->viewData['affiliate'];?>
<style type="text/css">
	div.detailsBlock
	{
		border: 1px solid #ddd;
		display: none;
		width: 500px;
		padding: 10px;
	}
</style>
<script type="text/javascript">
	jQuery(function() {
		function updatePaymentMethodDivs()
		{
			if (jQuery("#ddPaymentMethod").val() == 'paypal')
			{
				jQuery("#paypalDetails").show();
				jQuery("#checkDetails").hide();
			}
			else if (jQuery("#ddPaymentMethod").val() == 'check')
			{
				jQuery("#paypalDetails").hide();
				jQuery("#checkDetails").show();
			}
			else
			{
				jQuery("#paypalDetails").hide();
				jQuery("#checkDetails").hide();
			}
		}

		function updateAddressDivs()
		{
			if (jQuery("#cbMailTo").val() == 'on')
			{
				jQuery("#existingAddressDiv").show();
				jQuery("#differentAddressDiv").hide();
			}
			else
			{
				jQuery("#existingAddressDiv").hide();
				jQuery("#differentAddressDiv").show();
			}
		}
		function updateStateView()
		{
			if (jQuery("#country").val()=='US')
			{
				jQuery("#wpam_us_states").show();
			}
			else
			{
				jQuery("#wpam_us_states").hide();
			}

		}
		updateStateView();
		updateAddressDivs();
		updatePaymentMethodDivs();
		
		jQuery("#ddPaymentMethod").change(updatePaymentMethodDivs);
		jQuery("#cbMailTo").change(updateAddressDivs);
		jQuery("#country").change(updateStateView);
	});
</script>

<div class="wrap">

	 <h2><?php _e( 'Payment Details', 'affiliates-manager' ) ?></h2>
	<h3><?php _e( 'Please provide your payment details.', 'affiliates-manager' ) ?></h3>
	<p><?php _e( 'The following information will be used to disburse payments when your account reaches the minimum payout amount.', 'affiliates-manager' ) ?></p>

<?php
require_once WPAM_BASE_DIRECTORY . "/html/widget_form_errors.php";
?>
	<form method="post" action="<?php echo $this->viewData['nextStepUrl']?>">
		<table width="500">
			<tr>
				<td width="200"><label for="ddPaymentMethod"><?php _e( 'Method', 'affiliates-manager' ) ?></label> *</td>
				<td><select id="ddPaymentMethod" name="ddPaymentMethod" style="width: 150px;">
					<?php foreach ($this->viewData['paymentMethods'] as $key => $val) {
						echo '<option value="'.$key.'"';
						if ($this->viewData['request']['ddPaymentMethod'] == $key)
							echo ' selected="selected"';
						echo '>' . $val . '</option>';
					}?>
				</select></td>
			</tr>
		</table>

		<br/>
		<div id="paypalDetails" class="detailsBlock">
			<img src="<?php echo WPAM_URL . "/images/icon_paypal.png"?>" />
			<table width="500">
				<tr>
					<td width="200"><label for="txtPaypalEmail"><?php _e( 'PayPal E-Mail Address', 'affiliates-manager' ) ?></label> *</td>
					<td>
						<input id="txtPaypalEmail" type="text" name="txtPaypalEmail" size="30" value="<?php echo $this->viewData['request']['txtPaypalEmail']?>"/>
					</td>
				</tr>
			</table>
		</div>

		<div id="checkDetails" class="detailsBlock">
			<div style="float: left; width: 75px; height: 35px;">
				<img src="<?php echo WPAM_URL . "/images/bank-check.png"?>" />
			</div>
			<div style="width: 400px; height: 35px; padding-left: 10px; text-align: left; vertical-align: bottom;">
				<strong><?php _e( 'Paper Check', 'affiliates-manager' ) ?></strong>
			</div>

			<table width="500">
				<tr>
					<td width="200">
						<label for="txtCheckTo"><?php _e( 'Check Recipient', 'affiliates-manager' ) ?></label> *
					</td>
					<td>
						<input id="txtCheckTo" type="text" size="30" name="txtCheckTo" value="<?php echo $this->viewData['request']['txtCheckTo']?>" />
					</td>
				</tr>
				<tr>
					<td width="200">

					</td>
					<td>
						<input id="cbMailTo" type="checkbox" name="cbMailTo"
						<?php
						if ($this->viewData['request']['cbMailTo'] === 'on' || $this->viewData['request']['step'] != 'submit_payment_details')
						{
							echo 'checked="checked"';
						}
						?>
						/>
						<label for="cbMailTo"><?php _e( 'Mail to Address On Record', 'affiliates-manager' ) ?></label>
					</td>
				</tr>
			</table>

			<br/>
			<div id="existingAddressDiv">
				<table class="widefat">
					<tbody>
							<tr><td width="100"><?php _e( 'Recipient', 'affiliates-manager' ) ?></td>
						<td>
							<?php echo $model->firstName?> <?php echo $model->lastName?><br/>
							<?php echo $model->addressLine1?><br />
							<?php if(strlen(trim($model->addressLine2)) > 0)
							{
								echo $model->addressLine2 . "<br />";
							}?>
							<?php echo $model->addressCity?><?php if ($model->addressCountry == 'US')
							{
								echo ", " .$model->addressState;
							}?> <?php echo $model->addressZipCode?><br/>
							<?php
                                                        $countries = WPAM_Validation_CountryCodes::get_countries();
                                                        echo  $countries[$model->addressCountry]?>
						</td>
						</tr>
					</tbody>
				</table>

			</div>

			<div id="differentAddressDiv" style="display: none">
			<table class="widefat">
				<tr>
					<td width="100"><label for="txtRecipient"><?php _e( 'Recipient', 'affiliates-manager' ) ?></label> *</td>
					<td><input id="txtRecipient" type="text" name="txtRecipient" value="<?php echo $this->viewData['request']['txtRecipient']?>" /></td>
				</tr>
				<tr>
					<td><label for="address1"><?php _e( 'Address 1', 'affiliates-manager' ) ?></label> *</td>
					<td><input id="address1" type="text" name="address1" value="<?php echo $this->viewData['request']['address1']?>" size="30" /></td>
				</tr>
				<tr>
					<td><label for="address2"><?php _e( 'Address 2', 'affiliates-manager' ) ?></label></td>
					<td><input id="address2" type="text" name="address2" value="<?php echo $this->viewData['request']['address2']?>" size="30" /></td>
				</tr>
				<tr>
					<td><label for="addressCity"><?php _e( 'City', 'affiliates-manager' ) ?></label> *</td>
					<td><input id="addressCity" type="text" name="addressCity" value="<?php echo $this->viewData['request']['addressCity']?>" size="30" /></td>
				</tr>
				<tr>
					<td><label for="country"><?php _e( 'Country', 'affiliates-manager' ) ?></label> *</td>
					<td>
						<select id="country" name="country">
						<?php
							require_once WPAM_BASE_DIRECTORY . "/html/iso_country_options.php";
						?>
						</select>
					</td>
				</tr>
				<tr id="wpam_us_states">
					<td><label for="addressState"><?php _e( 'State', 'affiliates-manager' ) ?></label> *</td>
					<td>
						<select id="addressState" name="addressState">
						<?php
							require_once WPAM_BASE_DIRECTORY . "/html/iso_state_options.php";
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="addressPostalCode"><?php _e( 'Postal Code', 'affiliates-manager' ) ?></label> *</td>
					<td>
						<input id="addressPostalCode" type="text" name="addressPostalCode" value="<?php echo $this->viewData['request']['addressPostalCode']?>" size="5"/>
					</td>
				</tr>
							
			</table>
			</div>


		</div>

		<br />
		<div id="buttons" style="width:500px; text-align: center;">
			<input type="submit" class="button-primary" name="submitButton" value="<?php _e( 'Submit Payment Details', 'affiliates-manager' ) ?>"/>
		</div>
	</form>
</div>

