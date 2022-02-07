<div class="wrap">
	<br>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="postbox">
					<h3 class="hndle"><span><?php _e( 'About the plugin & developer', '404-to-301' ); ?></span></h3>
					<div class="inside">
						<div class="c4p-clearfix">
							<div class="c4p-left">
								<img src="<?php echo I4T3_PATH . 'admin/images/coder.png'; ?>" class="c4p-author-image" />
							</div>
							<div class="c4p-left" style="width: 70%">
							<?php $uname = ( $current_user->user_firstname == '' ) ? $current_user->user_login : $current_user->user_firstname; ?>
								<p>Yo <strong><?php echo $uname; ?></strong>! <?php _e( 'Thank you for using 404 to 301', '404-to-301' ); ?></p>
								<p>
									<?php _e( 'This plugin is brought to you by', '404-to-301' ); ?> <a href="http://iscode.co/" class="i4t3-author-link" target="_blank" title="<?php _e( 'Visit author website', '404-to-301' ); ?>"><strong>is_code()</strong></a>, <?php _e( 'a web store developed and managed by Joel James.', '404-to-301' ); ?>
								</p>
								<p>
									<hr/>
								</p>
								<p>
									So you installed this plugin and how is it doing? Feel free to <a href="http://iscode.co/contact/" class="i4t3-author-link" target="_blank" title="<?php _e( 'Contact the developer', '404-to-301' ); ?>">get in touch with me</a> anytime for help. I am always happy to help.
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="postbox">
					<h3 class="hndle"><span>Debugging Data</span></h3>
					<div class="inside">
						<div class="c4p-clearfix">
							<div class="c4p-left" style="width: 70%">
								<?php echo _404_To_301_Admin::i4t3_get_debug_data(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
			
				<div class="postbox">
					<h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-info"></span> Plugin Information</h3>
					<div class="inside">
						<div class="misc-pub-section">
							<label>Name : </label>
							<span><strong>404 to 301</strong></span>
						</div>
						<div class="misc-pub-section">
							<label>Version : v<?php echo $this->version; ?></label>
							<span></span>
						</div>
						<div class="misc-pub-section">
							<label>Author : <a href="http://iscode.co/" class="i4t3-author-link" target="_blank" title="Visit author website">is_code()</a></label>
							<span></span>
						</div>
						<div class="misc-pub-section">
							<label><a href="http://iscode.co/docs_cat/404-to-301/" class="i4t3-author-link" target="_blank" title="Visit plugin website"><strong>Plugin documentation</strong></a></label>
							<span></span>
						</div>
						<div class="misc-pub-section">
							<label><a href="http://iscode.co/product/404-to-301/" class="i4t3-author-link" target="_blank" title="Visit plugin website"><strong>More plugin details</strong></a></label>
							<span></span>
						</div>
						<div class="misc-pub-section">
							<label>Need help?</label>
							<span><strong><a href="http://iscode.co/support/">contact support</a></strong></span>
						</div>
					</div>
				</div>
				<div class="postbox">
					<h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-smiley"></span> Like the plugin?</h3>
					<div class="inside">
						<div class="misc-pub-section">
							<span class="dashicons dashicons-star-filled"></span> <label><strong><a href="https://wordpress.org/support/view/plugin-reviews/404-to-301?filter=5#postform" target="_blank" title="Rate now">Rate this on WordPress</a></strong></label>
						</div>
						<div class="misc-pub-section">
							<label><span class="dashicons dashicons-heart"></span> <strong><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XUVWY8HUBUXY4" target="_blank" title="Donate now">Make a small donation</a></strong></label>
						</div>
						<div class="misc-pub-section">
							<label><span class="dashicons dashicons-admin-plugins"></span> <strong><a href="https://github.com/joel-james/404-to-301/" target="_blank" title="Contribute now">Contribute to the Plugin</a></strong></label>
						</div>
						<div class="misc-pub-section">
							<label><span class="dashicons dashicons-twitter"></span> <strong><a href="https://twitter.com/home?status=I%20am%20using%20404%20to%20301%20plugin%20by%20%40Joel_James%20to%20handle%20all%20404%20errors%20in%20my%20%40WordPress%20site%20-%20it%20is%20awesome!%20%3E%20https://wordpress.org/plugins/404-to-301/" target="_blank" title="Tweet now">Tweet about the Plugin</a></strong></label>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
