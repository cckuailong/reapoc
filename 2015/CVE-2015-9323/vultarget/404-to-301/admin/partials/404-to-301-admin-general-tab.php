<div class="wrap">
	<form method="post" action="options.php">
	<p>
	<?php settings_fields( 'i4t3_gnrl_options' ); ?>
	<?php $options = get_option( 'i4t3_gnrl_options' ); ?>
	<?php
		// To show/hide options
		$cp_style = 'style="display: none;"';
		$cl_style = 'style="display: none;"';
		switch ( $options['redirect_to'] ) {
			case 'page':
				$cp_style = '';
				break;

			case 'link':
				$cl_style = '';
				break;
			
			default:
				break;
		}
		?>
		<table class="form-table">
			<tbody>
				
				<tr>
					<th>Redirect type</th>
					<td>
						<select name='i4t3_gnrl_options[redirect_type]'>
							<option value='301' <?php selected( $options['redirect_type'], '301' ); ?>>301 Redirect (SEO)</option>
							<option value='302' <?php selected( $options['redirect_type'], '302' ); ?>>302 Redirect</option>
							<option value='307' <?php selected( $options['redirect_type'], '307' ); ?>>307 Redirect</option>
						</select>
						<p class="description"><a target="_blank" href="https://moz.com/learn/seo/redirection"><strong>Learn more</strong></a> about these redirect types</p>
					</td>
				</tr>
				<tr>
					<th>Redirect to</th>
					<td>
						<select name='i4t3_gnrl_options[redirect_to]' id='i4t3_redirect_to'>
							<option value='page' <?php selected( $options['redirect_to'], 'page' ); ?>>Existing Page</option>
							<option value='link' <?php selected( $options['redirect_to'], 'link' ); ?>>Custom URL</option>
							<option value='none' <?php selected( $options['redirect_to'], 'none' ); ?>>No Redirect</option>
						</select>
						<p class="description"><strong>Existing Page:</strong> Select any WordPress page as a 404 page.</p>
						<p class="description"><strong>Custom URL:</strong> Redirect 404 requests to a specific URL.</p>
						<p class="description"><strong>No Redirect:</strong> To disable redirect.</p>
					</td>
				</tr>
				<tr id="custom_page" <?php echo $cp_style; ?>>
					<th>Select the page</th>
					<td>
						<select name='i4t3_gnrl_options[redirect_page]'>
							<?php foreach( $pages as $page ) { ?>
								<option value='<?php echo $page->ID; ?>' <?php selected( $options['redirect_page'], $page->ID ); ?>><?php echo $page->post_title; ?></option>
							<?php } ?>
						</select>
						<p class="description">The default 404 page will be replaced by the page you choose in this list.</p>
					</td>
				</tr>
				<tr id="custom_url"<?php echo $cl_style; ?>>
					<th>Custom URL</th>
					<td>
						<input type="text" placeholder="<?php echo home_url(); ?>" name="i4t3_gnrl_options[redirect_link]" value="<?php echo $options['redirect_link']; ?>">
						<p class="description">Enter any url (including http://)</p>
					</td>
				</tr>
				<tr>
					<th>Email notifications</th>
					<td>
						<?php $email_notify = 0; if( isset( $options['email_notify'] ) ) { $email_notify = $options['email_notify']; } ?>
						<input type="checkbox" name="i4t3_gnrl_options[email_notify]" value="1" <?php checked( $email_notify, 1 ); ?> />
						<p class="description">If you check this, an email will be sent on every 404 log on the admin's email account.</p>
					</td>
				</tr>
				<tr>
					<th>Log 404 Errors</th>
					<td>
						<select name='i4t3_gnrl_options[redirect_log]'>
							<option value='1' <?php selected( $options['redirect_log'], 1 ); ?>>Enable Error Logs</option>
							<option value='0' <?php selected( $options['redirect_log'], 0 ); ?>>Disable Error Logs</option>
						</select>
						<p class="description">Enable/Disable Logging</p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button( 'Save All Changes' ); ?>
		</p>
	</form>
</div>