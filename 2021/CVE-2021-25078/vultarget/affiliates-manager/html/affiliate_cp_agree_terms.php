<div class="wrap">

	 <h2><?php _e( 'Affiliate Confirmation', 'affiliates-manager' ) ?></h2>

	 <h3><?php _e( 'Contract Terms', 'affiliates-manager' ) ?></h3>

	<div style="text-align: center;">
			<br/>
			<table class="wpam_terms_table" style="width: 700px;">
				<tr>
					<td style="font-weight: bold; width: 150px">
						<?php _e( 'Commission Rate', 'affiliates-manager' ) ?>
					</td>
					<td style="width: 550px;">
						<?php echo wpam_format_bounty($this->viewData['affiliate']->bountyType, $this->viewData['affiliate']->bountyAmount)?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<br/>
							<strong><?php _e( 'Terms & Conditions', 'affiliates-manager' ) ?></strong><br/>
						<div id="termsBox" style="padding: 20px; width: auto; height: 300px; overflow: scroll; background-color: white; color: black; border: 1px solid black; white-space: pre-wrap;"><?php echo $this->viewData['tnc']?></div>
						<br />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center"><a class="button-primary" href="<?php echo $this->viewData['nextStepUrl']?>"><?php _e( 'Agree to Terms', 'affiliates-manager' ) ?></a></td>
				</tr>
			</table>
	</div>

</div>