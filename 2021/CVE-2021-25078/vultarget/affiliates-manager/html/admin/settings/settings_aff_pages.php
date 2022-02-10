<table class="form-table">
	<tr>
		<th width="200">
			<label for="affHomePage">
				<?php _e('Home Page', 'affiliates-manager');?>
			</label>                       
		</th>
		<td>
			<input type="text" size="60" name="affHomePage" id="affHomePage" value="<?php echo $this->viewData['request']['affHomePage']?>" />
                        <p class="description"><?php _e('This is the URL of your Affiliate Home page', 'affiliates-manager');?></p>
		</td>
	</tr>
        <tr>
		<th width="200">
			<label for="affRegPage">
				<?php _e('Registration Page', 'affiliates-manager');?>
			</label>                       
		</th>
		<td>
			<input type="text" size="60" name="affRegPage" id="affRegPage" value="<?php echo $this->viewData['request']['affRegPage']?>" />
                        <p class="description"><?php _e('This is the URL of your Affiliate Registration page', 'affiliates-manager');?></p>
		</td>
	</tr>
        <tr>
		<th width="200">
			<label for="affLoginPage">
				<?php _e('Login Page', 'affiliates-manager');?>
			</label>                       
		</th>
		<td>
			<input type="text" size="60" name="affLoginPage" id="affLoginPage" value="<?php echo $this->viewData['request']['affLoginPage']?>" />
                        <p class="description"><?php _e('This is the URL of your Affiliate Login page', 'affiliates-manager');?></p>
		</td>
	</tr>
        <tr>
		<th width="200">
			<label for="affTncPage">
				<?php _e('Terms and Conditions Page', 'affiliates-manager');?>
			</label>                       
		</th>
		<td>
			<input type="text" size="60" name="affTncPage" id="affTncPage" value="<?php echo $this->viewData['request']['affTncPage']?>" />
                        <p class="description"><?php _e('This is the URL of your Terms and Conditions page', 'affiliates-manager');?></p>
		</td>
	</tr>
        
</table>