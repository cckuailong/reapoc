<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if ( isset($_POST['niteoCS_custom_mobile_imgs']) ) {
		update_option('niteoCS_custom_mobile_imgs', $this->sanitize_checkbox($_POST['niteoCS_custom_mobile_imgs']));
	} else {
		update_option('niteoCS_custom_mobile_imgs', '0');
	}

	if (isset($_POST['niteoCS_mobile_banner_id'])) {
		$allnums = true;
		
		$ids_check = explode( ',', $_POST['niteoCS_mobile_banner_id'] );
		foreach ( $ids_check as $id ) {

			if ( !is_numeric($id) ) {
				$allnums = false;
			}
		}

		if ( $allnums === true || $_POST['niteoCS_mobile_banner_id'] == '' ) {
			update_option('niteoCS_mobile_banner_id', sanitize_text_field($_POST['niteoCS_mobile_banner_id']));
		}

	}
}
$gradient_array  	= array(
	'#d53369:#cbad6d' => __('Blury Beach', 'cmp-coming-soon-maintenance'),
	'#FC354C:#0ABFBC' => __('Miaka', 'cmp-coming-soon-maintenance'),
	'#C04848:#480048' => __('Influenza', 'cmp-coming-soon-maintenance'),
	'#5f2c82:#49a09d' => __('Calm Darya', 'cmp-coming-soon-maintenance'),
	'#5C258D:#4389A2' => __('Shroom Haze', 'cmp-coming-soon-maintenance'),
	'#1D2B64:#F8CDDA' => __('Purlple Paradise', 'cmp-coming-soon-maintenance'),
	'#1A2980:#26D0CE' => __('Aqua Marine', 'cmp-coming-soon-maintenance'),
	'#FF512F:#DD2476' => __('Bloody Mary', 'cmp-coming-soon-maintenance'),
	'#E55D87:#5FC3E4' => __('Rose Water', 'cmp-coming-soon-maintenance'),
	'#003973:#E5E5BE' => __('Horizon', 'cmp-coming-soon-maintenance'),
	'#e52d27:#b31217' => __('Youtube', 'cmp-coming-soon-maintenance'),
	'#FC466B:#3F5EFB' => __('Sublime Vivid', 'cmp-coming-soon-maintenance'),
	'#ED5565:#D62739' => __('Red', 'cmp-coming-soon-maintenance'),
	'#FC6E51:#DB391E' => __('Orange', 'cmp-coming-soon-maintenance'),
	'#FFDA7C:#F6A742' => __('Yellow', 'cmp-coming-soon-maintenance'),
	'#A0D468:#6EAF26' => __('Green', 'cmp-coming-soon-maintenance'),
	'#48CFAD:#19A784' => __('Green Pastel', 'cmp-coming-soon-maintenance'),
	'#4FC1E9:#0B9BD0' => __('Sky blue', 'cmp-coming-soon-maintenance'),
	'#5D9CEC:#0D65D8' => __('Purple', 'cmp-coming-soon-maintenance'),
	'#EC87C0:#BF4C90' => __('Violet', 'cmp-coming-soon-maintenance'),
	'#EDF1F7:#C9D7E9' => __('Light grey', 'cmp-coming-soon-maintenance'),
	'#CCD1D9:#8F9AA8' => __('Grey', 'cmp-coming-soon-maintenance'),
	'#656D78:#2F3640' => __('Dark grey', 'cmp-coming-soon-maintenance'),
);

$custom_mobile_imgs 		= get_option('niteoCS_custom_mobile_imgs', '0');
$mobile_banner_custom_id	= get_option('niteoCS_mobile_banner_id', '');
?>

<div class="table-wrapper content background-media">
	<h3><?php _e('Graphic Background', 'cmp-coming-soon-maintenance');?></h3>
	<table class="theme-setup">
	<tbody>
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Banner Settings', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Default Banner">
						 	<input type="radio" class="background-type" name="niteoCS_banner" value="2" <?php checked( '2', $banner_type ); ?>>&nbsp;<?php _e('Default Media', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Custom banner">
						 	<input type="radio" class="background-type" name="niteoCS_banner" value="0" <?php checked( '0', $banner_type ); ?>>&nbsp;<?php _e('Custom Images', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Unsplash banner">
						 	<input type="radio" class="background-type" name="niteoCS_banner" value="1" <?php checked( '1', $banner_type ); ?>>&nbsp;<?php _e('Unsplash library', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>
					<p>
						<label title="Video Banner">
						 	<input type="radio" class="background-type" name="niteoCS_banner" value="5" <?php checked( '5', $banner_type ); ?>>&nbsp;<?php _e('Video', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>
					<p>
						<label title="Pattern Banner">
						 	<input type="radio" class="background-type" name="niteoCS_banner" value="3" <?php checked( '3', $banner_type ); ?>>&nbsp;<?php _e('Graphic Pattern', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>
					<p>
						<label title="Solid Color Banner">
						 	<input type="radio" class="background-type" name="niteoCS_banner" value="4" <?php checked( '4', $banner_type ); ?>>&nbsp;<?php _e('Solid Color', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>
					<p>
						<label title="Gradient Banner">
						 	<input type="radio" class="background-type" name="niteoCS_banner" value="6" <?php checked( '6', $banner_type ); ?>>&nbsp;<?php _e('Gradient Color', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>
				</fieldset>
			</th>

			<td class="theme_background">

				<!-- DEFAULT BACKGROUND -->
				<fieldset class="background-type-switch default_banner x2">

					<?php if ( $this->cmp_selectedTheme() === 'pluto' ) { ?>
						<div class="chameleon color-preview"></div>
						<?php 
					} else { ?>

					<div class="background-thumb-wrapper">
						<?php 
						$src = '';
						if ( file_exists( $this->cmp_theme_dir( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.jpg' ) ) {
							$src = $this->cmp_themeURL( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.jpg';
						} 

						if ( file_exists( $this->cmp_theme_dir( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.png' ) ) {
							$src = $this->cmp_themeURL( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.png';
						} ?>
						
						<img src="<?php echo esc_url($src);?>" alt="Default Media">
						<div class="thumbnail-overlay"></div>
					</div>
					<?php 
					} ?>

					<p class="info"><?php _e('Every CMP Theme is using unique default background.','cmp-coming-soon-maintenance')?></p>
				</fieldset>
				
				<!-- CUSTOM BACKGROUND -->
				<fieldset class="background-type-switch custom_banner x0">

			        <input type="hidden" class="widefat" id="niteoCS-images-id" name="niteoCS_banner_id" value="<?php echo esc_attr( $niteoCS_banner_custom_id ); ?>" />
			        <input id="add-images" type="button" class="button" value="Media Library" />

			        <p class="cmp-hint"><?php _e('Pro Tip! You can select multiple Media from your library by holding CTRL+click (Command+click if you use MacOS) while selecting photos.','cmp-coming-soon-maintenance')?></p>

			        <div class="background-thumb-wrapper">

				        <?php 
				        if ( isset( $niteoCS_banner_custom_id ) && $niteoCS_banner_custom_id != '' ) {
							$ids = explode( ',', $niteoCS_banner_custom_id );
				        } ?>

			        	<div class="images-wrapper custom-gallery gallery-<?php echo ( isset( $ids ) && is_array( $ids ) ? count( $ids ) : '1');?>">
							<?php 

							if ( isset( $ids ) && is_array( $ids ) ) {

				        		$i = 0;
				        		foreach ( $ids as $id ) {
									$img = wp_get_attachment_image_src( $id, 'large' );
				        			if ( $i == 0 ) {  ?>
				        				<div class="big-thumb">
				        					<div class="thumbnail-overlay"></div>
				        					<?php 
						        			if ( isset ($img[0] ) ) {
						        				echo '<img src="' . $img[0] . '" alt="">';
						        			} ?>
				        				</div>
				        				<?php
				        			} else {
					        			if ( isset ($img[0] ) ) {
					        				echo '<img src="' . $img[0] . '" alt="" class="no-blur">';
					        			}
				        			}
				        			$i++;
				        		}
				        	} else { ?>
				        		<div class="big-thumb">
				        			<div class="thumbnail-overlay"></div>
				        		</div>
				        		<?php 
				        	} ?>

			        	</div>

			        </div>
					<br>

			        <input id="delete-images" type="button" class="button" value="<?php _e('Delete Images', 'cmp-coming-soon-maintenance');?>" style="display:<?php echo ( isset( $ids ) && !empty( $ids ) ) ? 'block' : 'none';?>"/><br>
				</fieldset>
				
				<!-- mobile devices images -->
				<fieldset class="background-type-switch custom_banner x0">
					<label for="cmp-custom-mobile-imgs">
						<input type="checkbox" class="custom-mobile-imgs" name="niteoCS_custom_mobile_imgs" id="cmp-custom-mobile-imgs" value="1" <?php checked('1', $custom_mobile_imgs);?>><?php _e('Display different image on Mobile devices(upload button is below)', 'cmp-coming-soon-maintenance');?>
						<br><br>
					</label>

					<div class="custom-mobile-imgs-switch x1">
						<input type="hidden" class="widefat" id="niteoCS-mobile-images-id" name="niteoCS_mobile_banner_id" value="<?php echo esc_attr( $mobile_banner_custom_id ); ?>" />
						<p class="cmp-hint" style="margin-top:-0.5em"><?php _e('* original version of these images will be displayed on mobile devices. Please make sure they are not excessively large. We recommend max width of 1366px.','cmp-coming-soon-maintenance')?></p>
						<input id="add-mobile-images" type="button" class="button" value="Media Library" />
						<br><br>

						<div class="mobile-background-thumb-wrapper">
							<?php 
							if ( isset( $mobile_banner_custom_id ) && $mobile_banner_custom_id != '' ) {
								$mobile_ids = explode( ',', $mobile_banner_custom_id );
							} ?>

							<div class="mobile-images-wrapper mobile-custom-gallery">
								<?php 
								if ( isset( $mobile_ids ) && is_array( $mobile_ids ) ) {
									$i = 0;
									foreach ( $mobile_ids as $mobile_id ) {

										$img = wp_get_attachment_image_src( $mobile_id, 'medium' );
										if ( isset ($img[0] ) ) {
											echo '<img src="' . $img[0] . '" alt="">';
										}
									}
								} ?>
							</div>

						</div>
						<br>

						<input id="delete-mobile-images" type="button" class="button" value="<?php _e('Delete Images', 'cmp-coming-soon-maintenance');?>" style="display:<?php echo ( isset( $mobile_ids ) && is_array( $mobile_ids ) ) ? 'block' : 'none';?>"/><br>

					</div>
				</fieldset>
				
				<!-- UNSPLASH BACKGROUND -->
				<fieldset class="background-type-switch unsplash_banner x1">

					<h4><?php _e('Choose Unsplash Feed', 'cmp-coming-soon-maintenance');?></h4>
					<select name="unsplash_feed" id="unsplash_feed">
					  <option value="3" <?php selected( '3', $niteoCS_unsplash_feed ); ?>><?php _e('Random Photo', 'cmp-coming-soon-maintenance');?></option>
					  <option value="0" <?php selected( '0', $niteoCS_unsplash_feed ); ?>><?php _e('Specific Photo', 'cmp-coming-soon-maintenance');?></option>
					  <option value="2" <?php selected( '2', $niteoCS_unsplash_feed ); ?>><?php _e('Random from Collection', 'cmp-coming-soon-maintenance');?></option>
					  <option value="1" <?php selected( '1', $niteoCS_unsplash_feed ); ?>><?php _e('Random from User', 'cmp-coming-soon-maintenance');?></option>
					</select><br><br>
					
					<div class="unsplash-feed unsplash-feed-0">
						<h4><?php _e('Enter Unsplash Photo URL or Unsplash Photo ID', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" class="widefat" id="niteoCS-unsplash-0" name="niteoCS_unsplash_0" value="<?php echo esc_attr( $niteoCS_unsplash_0 ); ?>" />

						<br><br>
						<button id="test-unsplash" class="button" data-security="<?php echo esc_attr( $ajax_nonce );?>"><?php _e('Display Unsplash Photo', 'cmp-coming-soon-maintenance');?></button>
					</div>

					<div class="background-thumb-wrapper unsplash-feed unsplash-feed-0">
						<div id="unsplash-media">
							<div class="thumbnail-overlay"></div>
						</div>

						<span class="unsplash-id"></span>
						
					</div>

					<div class="unsplash-feed unsplash-feed-2">
						<h4><?php printf(__('Enter <a href="%s">Unsplash Collection</a> URL or Collection ID.', 'cmp-coming-soon-maintenance'), 'https://unsplash.com/collections/');?></h4>
						<input type="text" class="widefat" id="niteoCS-unsplash-2" name="niteoCS_unsplash_2" value="<?php echo esc_attr( $niteoCS_unsplash_2 ); ?>" />

					</div>

					<div class="unsplash-feed unsplash-feed-3">
						<h4><?php _e('Limit photos to specific keyword (fashion, nature, technology..)', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" class="widefat" id="niteoCS-unsplash-3" name="niteoCS_unsplash_3" value="<?php echo esc_attr( $niteoCS_unsplash_3 ); ?>" />
						<br><br>
						<input type="checkbox" name="niteoCS_unsplash_feat" id="niteoCS_unsplash_feat" value="1" <?php checked( '1', get_option( 'niteoCS_unsplash_feat', '0' ) ); ?> class="regular-text code"><label for="niteoCS_unsplash_feat"><?php _e('Select Unsplash Featured Photos only', 'cmp-coming-soon-maintenance');?></label>
					</div>

					<div class="unsplash-feed unsplash-feed-1">
						<h4><?php _e('Enter Unsplash User ID', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" class="widefat" id="niteoCS-unsplash-1" name="niteoCS_unsplash_1" value="<?php echo esc_attr( $niteoCS_unsplash_1 ); ?>" placeholder="@"/>
					</div>
					
					<p class="unplash-description cmp-hint"><a href="http://unsplash.com" target="_blank"><?php _e('Unsplash');?></a> <?php _e('is a world leading source for free to use high quality stock images. All of the images that are submitted and published on Unsplash fall under under the <a href="https://unsplash.com/license"> Unsplash license</a>, which means you can use the image for any personal or commercial use.', 'cmp-coming-soon-maintenance');?></p>
					
				</fieldset>
				
				
				<!-- VIDEO BACKGROUND -->
				<fieldset class="background-type-switch video_banner x5">
					<h4 for="niteoCS_banner_video"><?php _e('Select Video Source', 'cmp-coming-soon-maintenance');?></h4>

					<select name="niteoCS_banner_video" id="niteoCS_banner_video" class="banner-video-source">
						<option value="youtube" <?php selected( 'youtube', $niteoCS_banner_video ); ?>><?php _e('YouTube', 'cmp-coming-soon-maintenance');?></option>
						<option value="local" <?php selected( 'local', $niteoCS_banner_video ); ?>><?php _e('Custom Video File', 'cmp-coming-soon-maintenance');?></option>
						<option disabled value="vimeo" <?php selected( 'vimeo', $niteoCS_banner_video ); ?>><?php _e('Vimeo (coming soon...)', 'cmp-coming-soon-maintenance');?></option>
					</select><br><br>


					<div class="banner-video-source youtube">
						<h4><?php _e('Enter Youtube URL', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" class="widefat" id="niteoCS-youtube-url" name="niteoCS_youtube_url" value="<?php echo esc_attr( $niteoCS_youtube_url ); ?>" />

					</div>

					<div class="banner-video-source vimeo">
						<h4><?php _e('Enter Vimeo URL', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" class="widefat" id="niteoCS-vimeo-url" name="niteoCS_vimeo_url" value="<?php echo esc_attr( $niteoCS_vimeo_url ); ?>" />
					</div>

					<div class="banner-video-source local">
						<h4><?php _e('Select or Upload custom Video file', 'cmp-coming-soon-maintenance');?></h4>
						<input id="add-video-local" type="button" class="button" value="Media Library"/>
						<input type="hidden" class="widefat" id="niteoCS-video-local-id" name="niteoCS_video_file_url" value="<?php echo esc_attr( $niteoCS_video_file_url ); ?>"  data-url="<?php echo esc_url( wp_get_attachment_url( $niteoCS_video_file_url ) ); ?>"/>
					</div>

					<!-- Local video image -->
					<div class="banner-video-source local">
						
						<div class="video-local-wrapper background-thumb-wrapper">
				        	<div class="thumbnail-overlay"></div>
						</div>

						<p class="file-source-input">
							<input id="delete-video" type="button" class="button" value="Remove Video" style="display:none"/>	
						</p>

				    </div>
					
					<!-- YouTube video image -->
				    <div class="banner-video-source youtube">
				    	<div class="video-yt-wrapper">
				    		<div class="video-yt-thumb-wrapper background-thumb-wrapper"></div>
				    		<div class="thumbnail-overlay"></div>
				    	</div>
				    </div>

					<!-- Video mobile Thumbnail -->
				    <div class="banner-video-source youtube local">

						<br>
						<input type="checkbox" name="niteoCS_video_autoloop" value="1" <?php checked( '1', get_option( 'niteoCS_video_autoloop', '1' ) ); ?> class="regular-text code"><?php _e('Loop video automatically', 'cmp-coming-soon-maintenance');?>


						<p><?php _e('YouTube background doesn`t work on mobile devices therefore only thumbnail will be displayed on mobile devices. You can upload custom thumbnail image by pressing button below. This Image will be also displayed as a placeholder before video is loaded. ', 'cmp-coming-soon-maintenance');?></p>
						<input type="hidden" class="widefat" id="niteoCS-video-thumb-id" name="niteoCS_video_thumb" value="<?php echo esc_attr( $niteoCS_video_thumb ); ?>" />
			        	<input id="add-video-thumb" type="button" class="button" value="<?php _e('Media Library', 'cmp-coming-soon-maintenance');?>" />

				        <div class="video-thumb-wrapper background-thumb-wrapper">
				        	<?php 
				        	if ( isset( $niteoCS_video_thumb ) && $niteoCS_video_thumb != '' ) {
				        		$img = wp_get_attachment_image_src( $niteoCS_video_thumb, 'large' );
			        			if ( isset( $img[0] ) ) {
			        				echo '<img src="'  .$img[0] . '" alt="">';
			        			}
				        	} ?>
				        	<div class="thumbnail-overlay"></div>
				        </div>

				        <input id="delete-video-thumb" type="button" class="button" value="Remove Thumbnail" />

				    </div>
					
				</fieldset>

				<!-- PATTERN BACKGROUND -->
				<fieldset class="background-type-switch graphic_pattern x3">
						<h4 for="niteoCS_banner_pattern"><?php _e('Select Pattern', 'cmp-coming-soon-maintenance');?></h4>
						<select name="niteoCS_banner_pattern" id="niteoCS_banner_pattern" data-url="<?php echo esc_url(WP_PLUGIN_URL . '/cmp-coming-soon-maintenance/img/patterns/');?>">
							<?php 
							foreach ( $patterns as $pattern ) { ?>
								<option value="<?php echo esc_attr( $pattern );?>" <?php selected( $pattern, $niteoCS_banner_pattern ); ?>><?php echo esc_html(ucfirst(str_replace('_', ' ', $pattern)));?></option>
								<?php 
							} ?>
							<option value="custom" <?php selected( 'custom', $niteoCS_banner_pattern ); ?>><?php _e('Custom Pattern...', 'cmp-coming-soon-maintenance');?></option>
						</select><br>

				        <input type="hidden" class="widefat" id="niteoCS-pattern-id" name="niteoCS_banner_pattern_custom" value="<?php echo esc_attr( $niteoCS_banner_pattern_custom ); ?>" />
						
				        <input id="add-pattern" type="button" class="button" value="Media Library" style="display:<?php echo ( $niteoCS_banner_pattern == 'custom' ) ? 'block' : 'none'?>;"/>
			        
						<div class="pattern-wrapper background-thumb-wrapper" style="background-image: url('<?php echo esc_url($pattern_url);?>');">
							<div class="thumbnail-overlay"></div>
						</div>
				</fieldset>

				<!-- SOLID COLOR BACKGROUND -->
				<fieldset class="background-type-switch solid_color x4">
						<h4><?php _e('Select Color', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" name="niteoCS_banner_color" id="niteoCS_banner_color" value="<?php echo esc_attr( $niteoCS_banner_color ); ?>" data-default-color="#e5e5e5" class="regular-text code"><br>
						<div class="color-preview" style="background-color:<?php echo esc_attr( $niteoCS_banner_color); ?>"></div>		
				</fieldset>
				
				<!-- GRADIENT BACKGROUND -->
				<fieldset class="background-type-switch gradient_background x6">

						<h4><?php _e('Select Gradient Background', 'cmp-coming-soon-maintenance');?></h4>

						<select name="niteoCS_gradient" id="niteoCS_gradient" class="background-gradient">
							<?php

							foreach ( $gradient_array as $color => $name ) { ?>
								<option value="<?php echo esc_attr( $color );?>" <?php selected ($color, $niteoCS_gradient ); ?>><?php echo esc_attr( $name );?></option>
								<?php 
							} ?>
							
							<option value="custom" <?php selected('custom', $niteoCS_gradient); ?>><?php _e('Custom Gradient', 'cmp-coming-soon-maintenance');?></option>

						</select><br><br>

						<div class="custom-gradient" style="display:<?php echo ( $niteoCS_gradient == 'custom' ) ? 'block' : 'none'; ?>">
							<h4><?php _e('Select first gradient color:', 'cmp-coming-soon-maintenance');?></h4>
							<input type="text" name="niteoCS_banner_gradient_one" id="niteoCS_gradient_one" value="<?php echo esc_attr( $niteoCS_gradient_one_custom); ?>" data-default-color="#e5e5e5" class="regular-text code"><br>
							
							<h4><?php _e('Select second gradient color:', 'cmp-coming-soon-maintenance');?></h4>
							<input type="text" name="niteoCS_banner_gradient_two" id="niteoCS_gradient_two" value="<?php echo esc_attr( $niteoCS_gradient_two_custom); ?>" data-default-color="#e5e5e5" class="regular-text code"><br>
						</div>

						<div class="gradient-preview" style="background:-moz-linear-gradient(-45deg, <?php echo ( $niteoCS_gradient == 'custom' ) ? esc_attr( $niteoCS_gradient_one_custom ) : esc_attr( $niteoCS_gradient_one ); ?> 0%, <?php echo ( $niteoCS_gradient == 'custom' ) ? esc_attr( $niteoCS_gradient_two_custom ) : esc_attr( $niteoCS_gradient_two ); ?> 100%);background:-webkit-linear-gradient(-45deg, <?php echo ( $niteoCS_gradient == 'custom' ) ? esc_attr( $niteoCS_gradient_one_custom ) : esc_attr( $niteoCS_gradient_one ); ?> 0%, <?php echo ( $niteoCS_gradient == 'custom' ) ? esc_attr( $niteoCS_gradient_two_custom ) : esc_attr( $niteoCS_gradient_two ); ?> 100%);background:linear-gradient(135deg, <?php echo ( $niteoCS_gradient == 'custom' ) ? esc_attr( $niteoCS_gradient_one_custom ) : esc_attr( $niteoCS_gradient_one ); ?> 0%, <?php echo ( $niteoCS_gradient == 'custom' ) ? esc_attr( $niteoCS_gradient_two_custom ) : esc_attr( $niteoCS_gradient_two ); ?> 100%)"></div>		
				</fieldset>

				<!-- BACKGROUND OVERLAY SETTINGS -->
				<div class="background-type-switch x0 x1 x3 x5 <?php echo ( $this->cmp_selectedTheme() === 'pluto' ) ? '' : 'x2';?>">

					<fieldset>
						<legend class="screen-reader-text">
							<span><?php _e('Background Overlay', 'cmp-coming-soon-maintenance');?></span>
						</legend>

						<h4><?php _e('Set Background Overlay', 'cmp-coming-soon-maintenance');?></h4>

						<select name="niteoCS_overlay" id="niteoCS_overlay" class="background-overlay">

							<option value="solid-color" <?php selected( 'solid-color', $overlay ); ?>><?php _e('Solid Color', 'cmp-coming-soon-maintenance');?></option>

							<option value="gradient" <?php selected( 'gradient', $overlay ); ?>><?php _e('Gradient', 'cmp-coming-soon-maintenance');?></option>

							<option value="disabled" <?php selected( 'disabled', $overlay ); ?>><?php _e('Disabled', 'cmp-coming-soon-maintenance');?></option>

						</select>

					</fieldset>

					
					<!-- BACKGROUND OVERLAY SOLID COLOR -->
					<fieldset class="background-overlay solid-color" style="margin: 1em 0">
						<h4><?php _e('Background Overlay Color', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" name="niteoCS_overlay_color" id="niteoCS_overlay_color" value="<?php echo esc_attr( $overlay_color ); ?>" data-default-color="#0a0a0a" class="regular-text code">
					</fieldset>
				
					<!-- BACKGROUND OVERLAY GRADIENT -->
					<fieldset class="background-overlay gradient" style="margin: 1em 0">

							<h4><?php _e('Select Gradient Overlay', 'cmp-coming-soon-maintenance');?></h4>

							<select name="niteoCS_overlay_gradient" id="niteoCS_overlay_gradient" class="overlay-gradient" style="margin: 1em 0">
								<?php

								foreach ( $gradient_array as $color => $name ) { ?>
									<option value="<?php echo esc_attr( $color );?>" <?php selected ( $color, $overlay_gradient ); ?>><?php echo esc_attr( $name );?></option>
									<?php 
								} ?>
								
								<option value="custom" <?php selected('custom', $overlay_gradient); ?>><?php _e('Custom Gradient', 'cmp-coming-soon-maintenance');?></option>
								
							</select><br>

							<div class="custom-overlay-gradient" style="display:<?php echo ( $overlay_gradient == 'custom' ) ? 'block;' : 'none'; ?>">

								<h4 style="margin-top:1em"><?php _e('Select first gradient color:', 'cmp-coming-soon-maintenance');?></h4>
								<input type="text" name="niteoCS_overlay_gradient_one" value="<?php echo esc_attr( $overlay_gradient_one_custom); ?>" id="niteoCS_overlay_gradient_one" data-default-color="#e5e5e5" class="regular-text code"><br>

								<h4 style="margin-top:1em"><?php _e('Select second gradient color:', 'cmp-coming-soon-maintenance');?></h4>
								<input type="text" name="niteoCS_overlay_gradient_two" value="<?php echo esc_attr( $overlay_gradient_two_custom); ?>" id="niteoCS_overlay_gradient_two" data-default-color="#e5e5e5" class="regular-text code"><br>
							
							</div>
		
					</fieldset>

					<fieldset class="background-overlay solid-color gradient" style="margin: 1em 0">
						<h4><?php _e('Background Overlay Opacity', 'cmp-coming-soon-maintenance');?>: <span><?php echo esc_attr( $overlay_opa ); ?></span></h4>
						<input type="range" id="niteoCS_overlay_opacity" class="background-overlay-opacity" name="niteoCS_overlay_opacity" min="0" max="1" step="0.1" value="<?php echo esc_attr( $overlay_opa ); ?>" />
					</fieldset>


					<fieldset class="background-effect blur" style="margin: 1em 0">
						<h4 for="niteoCS_effect_blur"><?php _e('Background Blur Amount', 'cmp-coming-soon-maintenance');?>: <span><?php echo esc_attr( $effect_blur ); ?></span>px</h4>
						<input type="range" id="niteoCS_effect_blur" class="blur-range" name="niteoCS_effect_blur" min="0.0" max="10" step="0.5" value="<?php echo esc_attr( $effect_blur ); ?>" />
					</fieldset>

				</div>
				
				<?php if ( isset( $theme_supports['text_overlay'] ) && $theme_supports['text_overlay'] ) : ?>
				<!-- TEXT OVERLAY SETTINGS -->
				<div class="background-type-switch x0 x1 x2 x3 x4 x5 x6" style="padding-top: 1em">

					<fieldset>
						<legend class="screen-reader-text">
							<span><?php _e('Text Overlay', 'cmp-coming-soon-maintenance');?></span>
						</legend>

						<label><input type="hidden" name="niteoCS_overlay_text_status" value="off"><input type="checkbox" name="niteoCS_overlay_text_status" class="overlay-text" <?php checked( $overlay_text_status, '1' );?>/><?php _e('Display Text Overlay', 'cmp-coming-soon-maintenance');?></label><br><br>

						<div class="overlay-text-switch on">
							<h4 for="niteoCS_overlay_text_heading"><?php _e('Overlay Heading', 'cmp-coming-soon-maintenance');?></label>
							<input type="text" id="niteoCS_overlay_text_heading" name="niteoCS_overlay_text_heading" value="<?php echo esc_attr( $overlay_text_heading ); ?>" class="regular-text code" placeholder="<?php echo _e('Leave empty to disable', 'cmp-coming-soon-maintenance');?>"/><br><br>

							<h4 for="niteoCS_overlay_text_paragraph"><?php _e('Overlay Text', 'cmp-coming-soon-maintenance');?></h4>
							<textarea id="niteoCS_overlay_text_paragraph" name="niteoCS_overlay_text_paragraph" class="regular-text code" rows="4" placeholder="<?php echo _e('Leave empty to disable', 'cmp-coming-soon-maintenance');?>"><?php echo esc_attr( $overlay_text_paragraph ); ?></textarea><br><br>

							<h4 for="niteoCS_overlay_button_text"><?php _e('Overlay Call To Action Button Text', 'cmp-coming-soon-maintenance');?></h4>
							<input type="text" id="niteoCS_overlay_button_text" name="niteoCS_overlay_button_text" value="<?php echo esc_attr( $overlay_button_text ); ?>" class="regular-text code" placeholder="<?php echo _e('Leave empty to disable', 'cmp-coming-soon-maintenance');?>"/><br><br>

							<h4 for="niteoCS_overlay_button_url"><?php _e('Overlay Call To Action Button URL', 'cmp-coming-soon-maintenance');?></h4>
							<input type="text" id="niteoCS_overlay_button_url" name="niteoCS_overlay_button_url" value="<?php echo esc_attr( $overlay_button_url ); ?>" class="regular-text code" placeholder="<?php echo _e('Insert Valid URL', 'cmp-coming-soon-maintenance');?>"/>
						</div>
	
					</fieldset>
				</div>
				<?php endif;?>

			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
		
		</tbody>
	</table>

</div>
