<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

//global $ecwd_options;

?>

<div class="wrap">
	<?php settings_errors(); ?>
	<div id="ecwd-settings">
		<div id="ecwd-settings-content" >
			<h2 id="add_on_title"><?php echo esc_html(get_admin_page_title()); ?></h2>
      <div>
        <p>
          <span style="color: #ba281e; font-size: 20px;">Attention:</span> Extensions are Available in <a target="_blank" href="<?php echo 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps'; ?>">10Web Plugins Bundle</a>
        </p>
      </div>
			<?php
			if($addons){
				foreach ($addons as $name=>$cat) {
					?>

					<div style="clear: both; margin-top: 15px;"> <h3 class="ecwd-addon-subtitle"><?php echo $name?> </h3></div>
					<?php
					foreach ( $cat as $addon ) {
						?>
						<div class="ecwd-add-on">
							<h2><?php echo $addon['name'] ?></h2>
							<figure class="ecwd-figure">
								<div  class="ecwd-figure-img">
									<a href="<?php echo $addon['url'] ?>" target="_blank">
										<?php if ( $addon['image'] ) { ?>
											<img src="<?php echo $addon['image'] ?>"/>
										<?php } ?>
									</a>
								</div>

								<figcaption class="ecwd-addon-descr ecwd-figcaption">

									<?php if ( $addon['icon'] ) { ?>
										<img src="<?php echo $addon['icon'] ?>"/>
									<?php } ?>
									<?php echo $addon['description'] ?>
								</figcaption>
							</figure>
							<?php if ( $addon['url'] !== '#' ) { ?>
								<a href="<?php echo $addon['url'] ?>"
								   target="_blank" class="ecwd-addon"><span>GET THIS EXTENSION</span></a>

							<?php } else { ?>
								<div class="ecwd_coming_soon">
									<img
										src="<?php echo plugins_url( '../../assets/coming_soon.png', __FILE__ ); ?>"/>
								</div>
							<?php }  ?>
						</div>
					<?php
					}
				}
			}
			?>

		</div>
		<!-- #ecwd-settings-content -->
	</div>
	<!-- #ecwd-settings -->
</div><!-- .wrap -->
