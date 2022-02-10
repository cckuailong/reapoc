<?php
/**
 * Display for Calendar post metas
 */
global $post;


?>

<div class="wrap">
	<div id="ecwd-settings">
		<div id="ecwd-settings-content">
			<h2 id="add_on_title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<div id="ecwd-display-options-wrap">
				<br />
				<span style="font-size: 15px; font-weight: bold;">The calendar currently uses default theme. Upgrade to Premium version to modify theme options and get fancy 5 more themes.</span>
				<br/>
				<br/>
				<div class="ecwd-meta-control">
					<img width="100%" height="100%"
					     src="<?php echo plugins_url( '/assets/themes1.jpg', ECWD_MAIN_FILE ); ?>">
          <img width="100%" height="100%"
					     src="<?php echo plugins_url( '/assets/themes4.jpg', ECWD_MAIN_FILE ); ?>">
          <img width="100%" height="100%"
					     src="<?php echo plugins_url( '/assets/themes3.jpg', ECWD_MAIN_FILE ); ?>">
           <img width="100%" height="100%"
					     src="<?php echo plugins_url( '/assets/themes2.jpg', ECWD_MAIN_FILE ); ?>">
				</div>
			</div>
		</div>
	</div>
</div>

