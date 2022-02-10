<?php
$home_url = home_url('/');
?>
<table class="form-table">
	<tr>
		<th width="200">
			<label for="affLandingPage">
				<?php _e('Default Landing Page', 'affiliates-manager');?>
			</label>
		</th>
		<td>
			<input type="text" size="60" name="affLandingPage" id="affLandingPage" value="<?php echo $this->viewData['request']['affLandingPage']?>" />
                        <p class="description"><?php echo sprintf( __( 'Your default landing page URL is <code>%s</code>. If you want to change to a different URL you can specify it here.', 'affiliates-manager' ), $home_url );?></p>
		</td>
	</tr>       
        <tr>
		<th width="200">
			<label for="disableOwnReferrals">
				<?php _e('Disable Own Referrals', 'affiliates-manager');?>
			</label>
		</th>
		<td>
			<input type="checkbox" id="disableOwnReferrals" name="disableOwnReferrals" <?php
			if ($this->viewData['request']['disableOwnReferrals'])
				echo 'checked="checked"';
			?>/><p class="description"><?php _e('If checked, your affiliates will not be able to earn a commission on their own purchases.', 'affiliates-manager');?></p>
		</td>
	</tr>
                <tr>
		<th width="200">
			<label for="autoDeleteWPUserAccount">
				<?php _e('Automatically Delete WordPress Account', 'affiliates-manager');?>
			</label>
		</th>
		<td>
			<input type="checkbox" id="autoDeleteWPUserAccount" name="autoDeleteWPUserAccount" <?php
			if ($this->viewData['request']['autoDeleteWPUserAccount'])
				echo 'checked="checked"';
			?>/><p class="description"><?php _e('If checked, when an affiliate account is deleted the WordPress user account for it will be automatically deleted.', 'affiliates-manager');?></p>
		</td>
	</tr>
</table>