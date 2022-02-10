<div class="mystickyelements-preview">
	<div class="myStickyelements-preview-tab">
		<div class="myStickyelements-preview-screen">
			<div class="mystickyelements-fixed <?php echo (isset($contact_form['direction']) && $contact_form['direction'] == "RTL")?"is-rtl":"" ?> mystickyelements-position-<?php echo esc_attr($general_settings['position'])?> <?php echo (isset($general_settings['position_on_screen']) && $general_settings['position_on_screen']!= '') ? 'mystickyelements-position-screen-' .$general_settings['position_on_screen'] : 'mystickyelements-position-screen-center';?> mystickyelements-position-mobile-<?php echo esc_attr($general_settings['position_mobile'])?> <?php echo (isset($general_settings['widget-size']) && $general_settings['widget-size']!= '') ? 'mystickyelements-size-' .$general_settings['widget-size'] : 'mystickyelements-size-medium';?> <?php echo (isset($general_settings['mobile-widget-size']) && $general_settings['mobile-widget-size']!= '') ? 'mystickyelements-mobile-size-' .$general_settings['mobile-widget-size'] : 'mystickyelements-mobile-size-medium';?> <?php echo (isset($general_settings['entry-effect']) && $general_settings['entry-effect']!= '') ? 'mystickyelements-entry-effect-' .$general_settings['entry-effect'] : 'mystickyelements-entry-effect-slide-in';?> <?php echo (isset($general_settings['templates']) && $general_settings['templates']!= '') ? 'mystickyelements-templates-' .$general_settings['templates'] : 'mystickyelements-templates-default';?>">
				<ul class="myStickyelements-preview-ul <?php if ( !isset($general_settings['minimize_tab'])) :?>remove-minimize <?php endif;?> ">
					<?php if ( isset($general_settings['minimize_tab'])) :?>
					<li class="mystickyelements-minimize">
						<span class="mystickyelements-minimize minimize-position-<?php echo esc_attr($general_settings['position'])?> minimize-position-mobile-<?php echo esc_attr($general_settings['position_mobile'])?>"  <?php if (isset($general_settings['minimize_tab_background_color']) && $general_settings['minimize_tab_background_color'] != ''): ?>style="background: <?php echo esc_attr($general_settings['minimize_tab_background_color']); ?>" <?php endif;
						?>>
						<?php
						if ( $general_settings['position'] == 'left' ) :
							echo "&larr;";
						endif;
						if( $general_settings['position'] == 'right' ):
							echo "&rarr;";
						endif;
						if( $general_settings['position'] == 'bottom' ):
							echo "&darr;";
						endif;
						?>
						</span>
					</li>
					<?php endif;?>
					<li id="myStickyelements-preview-contact" class="mystickyelements-contact-form element-desktop-on element-mobile-on <?php if (!isset($contact_form['enable'])) : ?> mystickyelements-contact-form-hide <?php endif; ?>" <?php if ( !isset($contact_form['enable'])) : ?> style="display:none;" <?php endif;?>>
					<?php
					$contact_form_text_class = '';
					if ($contact_form['text_in_tab'] == '') {
						$contact_form_text_class = "mystickyelements-contact-notext";
					}?>
						<span class="mystickyelements-social-icon <?php echo $contact_form_text_class?>" style="background-color: <?php echo esc_attr($contact_form['tab_background_color']);?>; color: <?php echo $contact_form['tab_text_color'];?>;">
							<i class="far fa-envelope"></i><?php echo isset($contact_form['text_in_tab'])?$contact_form['text_in_tab']:"Contact Us";?>
						</span>
					</li>
					<?php
					if (!empty($social_channels_tabs) && !isset($social_channels_tabs['is_empty'])) {
						foreach( $social_channels_tabs as $key=>$value) {
							
							if (  strpos($key, 'custom_channel') !== false || strpos($key, 'custom_shortcode') !== false ) {
								$custom_channel_key_temp = '';
								if( strpos($key, 'custom_channel') !== false){
									$custom_channel_key_temp = $key;
									$key = 'custom_channel';
								} 
								if( strpos($key, 'custom_shortcode') !== false){
									$custom_channel_key_temp = $key;
									$key = 'custom_shortcode';
								} 
								
								$social_channels_lists = mystickyelements_custom_social_channels();
								$social_channels_list  = $social_channels_lists[$key];
								
								if ( $custom_channel_key_temp != '') {
									$key = $custom_channel_key_temp;
								}
					
							} else {
								$social_channels_lists = mystickyelements_social_channels();
								$social_channels_list = $social_channels_lists[$key];
							}
							if ( empty($value)) {
								$value['bg_color'] = $social_channels_list['background_color'];
							}
							$element_class = '';
							if (isset($value['desktop']) && $value['desktop'] == 1) {
								$element_class .= ' element-desktop-on';
							}
							if (isset($value['mobile']) && $value['mobile'] == 1) {
								$element_class .= ' element-mobile-on';
							}
							$value['is_locked'] = (isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && !$is_pro_active)?1:0;
							$social_channels_list['class'] = isset($social_channels_list['class']) ? $social_channels_list['class'] : '';
							?>
							<li id="mystickyelements-social-<?php echo esc_attr($key);?>" class="mystickyelements-social-<?php echo esc_attr($key);?> mystickyelements-social-preview  <?php echo esc_attr($element_class);?>"  >
								<?php
								/*diamond template css*/
								if ( isset($value['bg_color']) && $value['bg_color'] != '' ) {
									?>
									<style>
										.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-bottom.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>,.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-bottom.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>,
										.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-top.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>,.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-top.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?> {
											background-color: <?php echo $value['bg_color']; ?> !important;
										}
										<?php
										if( isset($general_settings['templates']) && $general_settings['templates'] == 'diamond' ) {
										?>
											.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo $value['bg_color']; ?>;
											}
										<?php
										}
										if( isset($general_settings['templates']) && $general_settings['templates'] == 'arrow' ) {
										?>
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-position-left.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-left-color: <?php echo $value['bg_color']; ?>;
											}
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-position-right.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-right-color: <?php echo $value['bg_color']; ?>;
											}
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-position-bottom.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-bottom-color: <?php echo $value['bg_color']; ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-left.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-left-color: <?php echo $value['bg_color']; ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-right.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-right-color: <?php echo $value['bg_color']; ?>;
											}
											<?php if( $key == 'insagram' ) { ?>
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo $value['bg_color']; ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo $value['bg_color']; ?>;
											}
											<?php } ?>
										<?php
										}
										if( isset($general_settings['templates']) && $general_settings['templates'] == 'triangle' ) {
										?>
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo $value['bg_color']; ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo $value['bg_color']; ?>;
											}
										<?php
										}
										?>
									</style>
									<?php
								}
								$channel_type = (isset($value['channel_type'])) ? $value['channel_type'] : '';
									if ( $channel_type != 'custom' && $channel_type != '' ) {
										if ( isset($social_channels_lists[$channel_type]['custom_svg_icon']) ) {
											$social_channels_list['custom_svg_icon'] = $social_channels_lists[$channel_type]['custom_svg_icon'];
										}
										$social_channels_list['class'] 	= $social_channels_lists[$channel_type]['class'];
										$value['fontawesome_icon']		= $social_channels_lists[$channel_type]['class'];
									}
									/*if( strpos($key, 'custom_channel') !== false ){
										$value['bg_color'] = '#E0F2FE';
									}else if( strpos($key, 'custom_shortcode') !== false ){
										$value['bg_color'] = '#faf5ff';
									}else{
										
									}*/
									$value['bg_color'] = $social_channels_list['background_color'];
								?>
								<span class="mystickyelements-social-icon social-<?php echo esc_attr($key);?> social-<?php echo esc_attr($channel_type); ?>" style="background: <?php echo esc_attr($value['bg_color']);?>">

									<?php 
									/*if ( isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && $value['custom_icon'] != '' && $value['fontawesome_icon'] == '' ):?>
										<img class="<?php echo ( isset($value['stretch_custom_icon']) && $value['stretch_custom_icon'] == 1 ) ? 'mystickyelements-stretch-custom-img' : '';  ?>" src="<?php echo esc_url($value['custom_icon']);?>" width="40" height="40" />
									<?php else:
										if ( isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && $value['fontawesome_icon'] != '' ) {
											$social_channels_list['class'] = $value['fontawesome_icon'];
										}
										if ( isset($social_channels_list['custom_svg_icon']) && $social_channels_list['custom_svg_icon'] != '' ) {
											echo $social_channels_list['custom_svg_icon'];
										}else{ 
											if( strpos($key, 'custom_channel') !== false ){
												?>
												<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.9999 2.20002C9.9999 1.20591 10.8058 0.400024 11.7999 0.400024C12.794 0.400024 13.5999 1.20591 13.5999 2.20002V2.80002C13.5999 3.46277 14.1372 4.00002 14.7999 4.00002H18.3999C19.0626 4.00002 19.5999 4.53728 19.5999 5.20002V8.80002C19.5999 9.46277 19.0626 10 18.3999 10H17.7999C16.8058 10 15.9999 10.8059 15.9999 11.8C15.9999 12.7941 16.8058 13.6 17.7999 13.6H18.3999C19.0626 13.6 19.5999 14.1373 19.5999 14.8V18.4C19.5999 19.0628 19.0626 19.6 18.3999 19.6H14.7999C14.1372 19.6 13.5999 19.0628 13.5999 18.4V17.8C13.5999 16.8059 12.794 16 11.7999 16C10.8058 16 9.9999 16.8059 9.9999 17.8V18.4C9.9999 19.0628 9.46264 19.6 8.7999 19.6H5.1999C4.53716 19.6 3.9999 19.0628 3.9999 18.4V14.8C3.9999 14.1373 3.46264 13.6 2.7999 13.6H2.1999C1.20579 13.6 0.399902 12.7941 0.399902 11.8C0.399902 10.8059 1.20579 10 2.1999 10H2.7999C3.46264 10 3.9999 9.46277 3.9999 8.80002V5.20002C3.9999 4.53728 4.53716 4.00002 5.1999 4.00002H8.7999C9.46264 4.00002 9.9999 3.46277 9.9999 2.80002V2.20002Z" fill="#0EA5E9"></path></svg> 
											<?php
											}else if( strpos($key, 'custom_shortcode') !== false ){
												?>
												<svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.399902 2.99998C0.399902 1.67449 1.47442 0.599976 2.7999 0.599976H17.1999C18.5254 0.599976 19.5999 1.67449 19.5999 2.99998V15C19.5999 16.3255 18.5254 17.4 17.1999 17.4H2.7999C1.47442 17.4 0.399902 16.3255 0.399902 15V2.99998ZM4.35137 4.55145C4.82 4.08282 5.5798 4.08282 6.04843 4.55145L9.64843 8.15145C10.1171 8.62008 10.1171 9.37988 9.64843 9.8485L6.04843 13.4485C5.5798 13.9171 4.82 13.9171 4.35137 13.4485C3.88275 12.9799 3.88275 12.2201 4.35137 11.7514L7.10285 8.99998L4.35137 6.2485C3.88275 5.77987 3.88275 5.02008 4.35137 4.55145ZM11.1999 11.4C10.5372 11.4 9.9999 11.9372 9.9999 12.6C9.9999 13.2627 10.5372 13.8 11.1999 13.8H14.7999C15.4626 13.8 15.9999 13.2627 15.9999 12.6C15.9999 11.9372 15.4626 11.4 14.7999 11.4H11.1999Z" fill="#A855F7"></path></svg>
												<?php
											}else{*/
												?>
												<i class="<?php echo esc_attr($social_channels_list['class']);?>" <?php if ( isset($value['icon_color']) && $value['icon_color'] != '') : echo "style='color:" . $value['icon_color'] . "'"; endif; ?>></i>	
											<?php
											//}
										//}
									//endif;
									$icon_text_size = "display: none;";
									$value['icon_text'] = ( isset($value['icon_text']) && $value['icon_text'] != '' ) ? $value['icon_text'] : '';
									
									if ( isset($value['icon_text']) && $value['icon_text'] != '' && isset($general_settings['templates']) && $general_settings['templates'] == 'default' ) {
										$icon_text_size .= "display: block;";
										if ( isset($value['icon_text_size']) && $value['icon_text_size'] != '') {
											$icon_text_size .= "font-size: " . $value['icon_text_size'] . "px;";
										}
									}
									echo "<span class='mystickyelements-icon-below-text' style='".$icon_text_size."'>" . esc_html($value['icon_text']) . "</span>";
									if ( $key == 'line') {
										echo "<style>.mystickyelements-social-icon.social-". $key ." svg .fil1{ fill:" .$value['icon_color']. "}</style>";
									}
									if ( $key == 'qzone') {
										echo "<style>.mystickyelements-social-icon.social-". $key ." svg .fil2{ fill:" . $value['icon_color'] . "}</style>";
									}
									?>
								</span>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>
		</div>
		<p class="description" id="myStickyelements_mobile_templete_desc" style="display: none;">
			<strong><?php esc_html_e( 'The default template is the only template that is currently available for the mobile bottom position', 'mystickyelements');?></strong>
		</p>
		<div class="mystickyelements-preivew-below-sec" data-id="<?php if(isset($is_widgest_create)) : echo $is_widgest_create; endif;?>">
			<div class="myStickyelements-header-title">
				<h3><?php _e('Live Preview', 'mystickyelements'); ?>
				<!-- <p class="description" ><strong><?php //esc_html_e( 'See the full functionality on your live site', 'mystickyelements');?></strong></p> -->
				</h3>
				<span class="myStickyelements-preview-window">
					<ul>
						<li class="preview-desktop preview-active"><i class="fas fa-desktop"></i></li>
						<li class="preview-mobile"><i class="fas fa-mobile-alt"></i></li>
					</ul>
				</span>
			</div>
			<div class="mystickyelements-preivew-save-btn">
				<p class="save">
					<button type="submit" name="submit" value="Save" id="save" class="button button-primary preview-publish"><?php _e('Save', 'mystickyelements');?></button>&nbsp;
					<button type="submit" name="next-button" id="next-button-prev" class="button button-primary"><?php _e('Next', 'mystickyelements');?></button>
				</p>
			</div>
		</div>	
	</div>
</div>
<?php

	if(isset($_POST['save_view']) && $_POST['save_view'] == 'Save View'){		
		echo '<script type="text/javascript"> jQuery(".mystickyelements-missing-link-popup").hide(); </script>';
		if( isset($_POST['widgest_status']) && $_POST['widgest_status'] == 1 ){

			echo '<script type="text/javascript"> jQuery("#loader").show(); </script>';
			echo '<script>setTimeout(function(){ window.location.href = "'.admin_url( "admin.php?page=my-sticky-elements" ).'" }, 500);</script>';
		}
		else{
			show_save_popup();
		}
	}

	if( isset($_POST['submit']) && $_POST['submit'] == 'Publish'){
		if( isset($_POST['widgest_status']) && $_POST['widgest_status'] == 0 ){
			show_save_popup();
		}
		else{
			
			echo '<script type="text/javascript"> jQuery("#loader").show(); </script>';
			echo '<script>setTimeout(function(){ window.location.href = "'.admin_url( "admin.php?page=my-sticky-elements" ).'" }, 500);</script>';
		}
		echo '<script type="text/javascript"> jQuery(".mystickyelements-missing-link-popup").hide(); </script>';
	}
	else if(isset($_POST['submit']) && $_POST['submit'] == 'Save'){
		echo '<script type="text/javascript"> jQuery("#flash_message").addClass("show");setTimeout(function(){jQuery("#flash_message").removeClass("show");}, 3000);</script>';
	}
	
	function show_save_popup(){
		
		$popup_html = '<div class="stickyelement-action-popup-open stickyelement-save-popup">';
		$popup_html .= '<div class="stickyelement-save-close-wrap"><a href="' . admin_url( "admin.php?page=my-sticky-elements" ) . '"><img draggable="false" role="img" class="emoji" alt="✖" src="https://s.w.org/images/core/emoji/13.0.1/svg/2716.svg"></a></div>';
		$popup_html .= '<div class="stickyelement-save-check-wrap"><i class="far fa-check-circle"></i></div>';
		$popup_html .= '<div class="stickyelement-save-content-wrap">';
		$popup_html .= '<div class="stickyelement-save-content-title">';
		
		if( isset($_POST['widgest_status']) && $_POST['widgest_status'] == 0 )
			$popup_html .= '<h2>First Widget Successfully Added 🎉</h2>';
		else
			$popup_html .= '<h2>First Widget Successfully Updated 🎉</h2>';
		$popup_html .= '</div>';
		$popup_html .= '<div class="stickyelement-save-content">';
		$popup_html .= '<p>Congrats! You have added your first widget. If you have any questions, please check out <a href="https://premio.io/help/mystickyelements/" target="_blank">knowledge base</a></p>';
		//$popup_html .= '<p>Go to our knowledge base My Sticky Elements doc</a> for more helpful resources and explore different ways My Sticky Elements can be used.</p>';1		
		$popup_html .= '</div>';
		$popup_html .= '<div class="stickyelement-save-content-btn">';
		$popup_html .= '<a href="' . admin_url( "admin.php?page=my-sticky-elements" ) . '">Go to Dashboard</a>';
		$popup_html .= '</div>';
		$popup_html .= '</div>';
		$popup_html .= '</div>';
		$popup_html .= '<div class="stickyelement-overlay" id="success-popup-overlay" data-id="'.admin_url( "admin.php?page=my-sticky-elements" ).'"></div>';

		echo @$popup_html;
	}
	
?>
<div class="mystickyelements-action-popup-open mystickyelements-missing-link-popup mystickyelements-action-popup-status" id="mystickyelements-missing-link-popup" style="display:none;">
											
	<div class="popup-ui-widget-header">
		<span id="ui-id-1" class="ui-dialog-title"><?php esc_html_e('Missing link','mystickyelement')?></span>
		<span class="close-dialog" data-id="0" data-from='widget-social-link'>&#10006</span>
	</div>
	<div id="widget-delete-confirm" class="ui-widget-content">
		<p>Please fill out the link information for all the selected channels</p>
	</div>
	
	<div class="popup-ui-dialog-buttonset"><button type="button" class="mystickyelement-cancel-widget-btn mystickyelement-dolater-widget-btn"  data-popupfrom=""><?php esc_html_e("I'll do it later",'mystickyelement');?></button><button type="button" class="mystickyelement-btn-orange mystickyelement-btn-ok"><?php esc_html_e('Ok','mystickyelement');?></button></div>
</div>
<div id="mystickyelement-missing-link-overlay" class="stickyelement-overlay" style="display:none;"></div>