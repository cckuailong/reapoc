<?php

if (!class_exists('MyStickyElementsFrontPage_pro')) {

    class MyStickyElementsFrontPage_pro
    {
		public function __construct() {
			if ( isset($_GET['page_id']) || isset($_GET['et_fb']) || isset($_GET['so_live_editor']) || isset($_GET['siteorigin_panels_live_editor']) ) {
				return false;
			}
			add_action('wp_enqueue_scripts', array($this, 'mystickyelements_enqueue_script'), 9999);
            add_action('wp_footer', array($this, 'mystickyelement_element_footer'), 999);

            add_action('wp_ajax_mystickyelements_contact_form', array($this, 'mystickyelements_contact_form'));
            add_action('wp_ajax_nopriv_mystickyelements_contact_form', array($this, 'mystickyelements_contact_form'));
        }

        public function mystickyelements_enqueue_script() {
			$is_min = ( !WP_DEBUG ) ? '.min' : '';
            $contact_form = get_option('mystickyelements-contact-form');
            $general_settings = get_option('mystickyelements-general-settings');
			$default_fonts = array('System Stack', 'Arial', 'Tahoma', 'Verdana', 'Helvetica', 'Times New Roman', 'Trebuchet MS', 'Georgia', 'Open Sans Hebrew');
			if ( isset($general_settings['font_family']) && $general_settings['font_family'] != '' && !in_array( $general_settings['font_family'], $default_fonts) ) {
				wp_enqueue_style('mystickyelements-google-fonts', 'https://fonts.googleapis.com/css?family=' . $general_settings['font_family']  . ':400,500,600,700');
			} else if( !isset($general_settings['font_family']) || $general_settings['font_family'] == '' ) {
				
				wp_enqueue_style('mystickyelements-google-fonts', 'https://fonts.googleapis.com/css?family=Poppins:400,500,600,700');
				
			}
            wp_enqueue_style('font-awesome-css', plugins_url('/css/font-awesome.min.css', __FILE__), array() , MY_STICKY_ELEMENT_VERSION);
            wp_enqueue_style('mystickyelements-front-css', plugins_url('/css/mystickyelements-front'. $is_min .'.css', __FILE__), array(), MY_STICKY_ELEMENT_VERSION );

            // Add Themme custom CSS
           if (  isset($contact_form['form_css']) || isset($general_settings['tabs_css']) || ( isset($general_settings['font_family']) && $general_settings['font_family'] != '') ) {
                $custom_css = '';

				if ( isset($general_settings['font_family']) && $general_settings['font_family'] != '' ) {
					if(isset($general_settings['font_family'] ) && $general_settings['font_family'] == 'System Stack' ){
						$general_settings['font_family'] = '-apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"';
					}
					$custom_css .= '.mystickyelements-fixed,
									.mystickyelements-fixed ul,
									form#stickyelements-form select,
									form#stickyelements-form input,
									form#stickyelements-form textarea,
									.element-contact-form h3 {
										font-family: ' . $general_settings['font_family'] . ';
									}';
					$custom_css .= '.mystickyelements-contact-form[dir="rtl"],
									.mystickyelements-contact-form[dir="rtl"] .element-contact-form h3,
									.mystickyelements-contact-form[dir="rtl"] form#stickyelements-form input,
									.mystickyelements-contact-form[dir="rtl"] form#stickyelements-form textarea,
									.mystickyelements-fixed[dir="rtl"] .mystickyelements-social-icon,
									.mystickyelements-fixed[dir="rtl"] .mystickyelements-social-text,
									html[dir="rtl"] .mystickyelements-contact-form,
									html[dir="rtl"] .mystickyelements-contact-form .element-contact-form h3,
									html[dir="rtl"] .mystickyelements-contact-form form#stickyelements-form input,
									html[dir="rtl"] .mystickyelements-contact-form form#stickyelements-form textarea,
									html[dir="rtl"] .mystickyelements-fixed .mystickyelements-social-icon,
									html[dir="rtl"] .mystickyelements-fixed .mystickyelements-social-text {
										font-family: ' . $general_settings['font_family'] . ';
									}';
				}

                if (isset($general_settings['custom_position']) && $general_settings['custom_position'] != '') {
                    $custom_css .= '.mystickyelements-fixed {
									bottom: -' . $general_settings['custom_position'] . 'px;
									-webkit-transform: translateY(-' . $general_settings['custom_position'] . 'px);
									-moz-transform: translateY(-' . $general_settings['custom_position'] . 'px);
									transform: translateY(-' . $general_settings['custom_position'] . 'px);
								}';
                }
                if (isset($contact_form['form_css']) && $contact_form['form_css'] !='' ) {
					$custom_css .= trim(strip_tags($contact_form['form_css']));
				}
				if (isset($general_settings['tabs_css']) && $general_settings['tabs_css'] !='' ) {
					$custom_css .= trim(strip_tags($general_settings['tabs_css']));
				}
				

                if (!empty($custom_css)) {
					wp_add_inline_style('mystickyelements-front-css', $custom_css);
                }
            }
			$placeholder_color =  ( isset($general_settings['placeholder_color']) && $general_settings[	'placeholder_color'] != '' ) ? $general_settings['placeholder_color'] : '#4F4F4F';
			?>
			<style>								
					form#stickyelements-form input::-moz-placeholder{
						color: <?php echo $placeholder_color; ?>;
					} 
					form#stickyelements-form input::-ms-input-placeholder{
						color: <?php echo $placeholder_color; ?>
					} 
					form#stickyelements-form input::-webkit-input-placeholder{
						color: <?php echo $placeholder_color; ?>
					}
					form#stickyelements-form input::placeholder{
						color: <?php echo $placeholder_color; ?>
					}
					form#stickyelements-form textarea::placeholder {
						color: <?php echo $placeholder_color; ?>
					}
					form#stickyelements-form textarea::-moz-placeholder {
						color: <?php echo $placeholder_color; ?>
					}
			</style>	
			<?php
            wp_enqueue_script('mystickyelements-cookie-js', plugins_url('/js/jquery.cookie.js', __FILE__), array('jquery'), MY_STICKY_ELEMENT_VERSION, true);
            wp_enqueue_script('mystickyelements-fronted-js', plugins_url('/js/mystickyelements-fronted'. $is_min .'.js', __FILE__), array('jquery'), MY_STICKY_ELEMENT_VERSION, true);

            $locale_settings = array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('mystickyelements'),
            );
            wp_localize_script('mystickyelements-fronted-js', 'mystickyelements', $locale_settings);
        }

        public function mystickyelement_element_footer()
        {
			global $wp;
			
            $contact_form = get_option('mystickyelements-contact-form');
            $social_channels = get_option('mystickyelements-social-channels');
            $social_channels_tabs = get_option('mystickyelements-social-channels-tabs');
            $general_settings = get_option('mystickyelements-general-settings');
			$stickyelements_widgets = get_option('stickyelements_widgets');
			
			if ( !isset( $stickyelements_widgets[0]['status'])) {
				$widget_status = 1;
			}
			if ( isset( $stickyelements_widgets[0]['status']) ) {
				$widget_status = $stickyelements_widgets[0]['status'];
			}
			
			if ( $widget_status == 0 ) {
				return;
			}
            $social_channels_lists = mystickyelements_social_channels();
            if (!isset($contact_form['enable']) && !isset($social_channels['enable'])) {
                return;
            }

			$contact_field = get_option( 'mystickyelements-contact-field' );
			if ( empty( $contact_field ) ) {
				$contact_field = array( 'name', 'phone', 'email', 'message', 'dropdown' );
			}
            $contact_form_class = '';
            if (isset($contact_form['desktop']) && $contact_form['desktop'] == 1) {
                $contact_form_class .= ' element-desktop-on';
            }
            if (isset($contact_form['mobile']) && $contact_form['mobile'] == 1) {
                $contact_form_class .= ' element-mobile-on';
            }
			if (isset($general_settings['form_open_automatic']) && $general_settings['form_open_automatic'] == 1 && !isset($_COOKIE['closed_contactform'])) {
				$contact_form_class .= ' elements-active';
			}

			if ( !isset($general_settings['position_mobile']) ) {
				$general_settings['position_mobile'] = 'left';
			}

            $minimize_class = '';
			if ( isset($general_settings['minimize_tab']) && $general_settings['minimize_tab'] == 1 ) {
				if ( !isset($_COOKIE['minimize_desktop']) && isset($general_settings['minimize_desktop']) && $general_settings['minimize_desktop'] == 'desktop' && !wp_is_mobile() ) {
					$minimize_class = 'element-minimize';
				} elseif ( !isset($_COOKIE['minimize_mobile']) && isset($general_settings['minimize_mobile']) && $general_settings['minimize_mobile'] == 'mobile' && wp_is_mobile() ) {
					$minimize_class = 'element-minimize';
				} else if ( isset($_COOKIE['minimize_desktop']) && $_COOKIE['minimize_desktop'] == 'minimize' && !wp_is_mobile() ) {
					$minimize_class = 'element-minimize';
				} elseif (isset($_COOKIE['minimize_mobile']) && $_COOKIE['minimize_mobile'] == 'minimize' && wp_is_mobile()) {
					$minimize_class = 'element-minimize';
				}
			} else {
				$minimize_class = 'no-minimize';
			}
			
			/* Change Open Tabs click to hover on Mobile device */
			if ( $general_settings['open_tabs_when'] == 'click' && wp_is_mobile() ) {
				//$general_settings['open_tabs_when'] = 'hover';
			}			
			$general_settings['widget-size'] = (isset($general_settings['widget-size']) && $general_settings['widget-size']!= '') ? $general_settings['widget-size'] : 'medium';
			$general_settings['mobile-widget-size'] = (isset($general_settings['mobile-widget-size']) && $general_settings['mobile-widget-size']!= '') ? $general_settings['mobile-widget-size'] : 'medium';
			$general_settings['entry-effect'] = (isset($general_settings['entry-effect']) && $general_settings['entry-effect']!= '') ? $general_settings['entry-effect'] : 'slide-in';
			$general_settings['templates'] = (isset($general_settings['templates']) && $general_settings['templates']!= '') ? $general_settings['templates'] : 'default';
			$mystickyelements_class[] = 'mystickyelements-fixed';
			$mystickyelements_class[] = 'mystickyelements-position-' . $general_settings['position'];
			$mystickyelements_class[] = 'mystickyelements-position-screen-' . $general_settings['position_on_screen'];
			$mystickyelements_class[] = 'mystickyelements-position-mobile-' . $general_settings['position_mobile'];
			$mystickyelements_class[] = 'mystickyelements-on-' . $general_settings['open_tabs_when'];
			$mystickyelements_class[] = 'mystickyelements-size-' . $general_settings['widget-size'];
			$mystickyelements_class[] = 'mystickyelements-mobile-size-' . $general_settings['mobile-widget-size'];
			$mystickyelements_class[] = 'mystickyelements-entry-effect-' . $general_settings['entry-effect'];
			$mystickyelements_class[] = 'mystickyelements-templates-' . $general_settings['templates'];

			$mystickyelements_classes = join( ' ', $mystickyelements_class );
            ?>
            <div <?php if (isset($contact_form['direction']) && $contact_form['direction'] == 'RTL') : ?> dir="rtl" <?php endif; ?>
                class="<?php echo esc_attr($mystickyelements_classes);?>">
				<div class="mystickyelement-lists-wrap">
					<ul class="mystickyelements-lists <?php echo esc_attr('mysticky' . $minimize_class);?>">
						<?php if ( isset($general_settings['minimize_tab']) && $general_settings['minimize_tab'] == 1 ):?>
							<li class="mystickyelements-minimize <?php echo esc_attr($minimize_class);?>">
								<span class="mystickyelements-minimize minimize-position-<?php echo esc_attr($general_settings['position'])?> minimize-position-mobile-<?php echo esc_attr($general_settings['position_mobile'])?>" <?php if (isset($general_settings['minimize_tab_background_color']) && $general_settings['minimize_tab_background_color'] != ''): ?>style="background: <?php echo esc_attr($general_settings['minimize_tab_background_color']); ?>" <?php endif;
								?>>
								<?php
								if ( !isset($_COOKIE['minimize_desktop']) && isset($general_settings['minimize_desktop']) && $general_settings['minimize_desktop'] == 'desktop' && !wp_is_mobile() ) :
									echo "<i class='fas fa-envelope'></i>";
								elseif ( !isset($_COOKIE['minimize_mobile']) && isset($general_settings['minimize_mobile']) && $general_settings['minimize_mobile'] == 'mobile' && wp_is_mobile() ) :
									echo "<i class='fas fa-envelope'></i>";
								elseif ( $general_settings['position'] == 'left' && !wp_is_mobile() ) :
									echo  ($minimize_class == "" ) ? "&larr;" : "&rarr;" ;
								elseif ( $general_settings['position'] == 'right' && !wp_is_mobile() ) :
									echo  ($minimize_class == "" ) ? "&rarr;" : "&larr;";
								elseif ( $general_settings['position'] == 'bottom' && !wp_is_mobile() ) :
									echo  ($minimize_class == "" ) ? "&darr;" : "&uarr;";
								elseif ( $general_settings['position_mobile'] == 'left' && wp_is_mobile() ) :
									echo  ($minimize_class == "" ) ? "&larr;" : "&rarr;" ;
								elseif ( $general_settings['position_mobile'] == 'right' && wp_is_mobile() ) :
									echo  ($minimize_class == "" ) ? "&rarr;" : "&larr;";
								elseif ( $general_settings['position_mobile'] == 'bottom' && wp_is_mobile() ) :
									echo  ($minimize_class == "" ) ? "&darr;" : "&uarr;";
								elseif ( $general_settings['position_mobile'] == 'top' && wp_is_mobile() ) :
									echo  ($minimize_class == "" ) ? "&uarr;" : "&darr;";
								endif;
								?>
								</span>
							</li>
						<?php endif;?>

						<?php if (isset($contact_form['enable']) && $contact_form['enable'] == 1): ?>

							<li id="mystickyelements-contact-form" class="mystickyelements-contact-form <?php echo esc_attr($contact_form_class); ?>"  <?php if (isset($contact_form['direction']) && $contact_form['direction'] == 'RTL') : ?> dir="rtl" <?php endif; ?> >
								<?php 
								$contact_form_text_class = '';
								if ($contact_form['text_in_tab'] == '') {
									$contact_form_text_class = "mystickyelements-contact-notext";
								}?>
								<span class="mystickyelements-social-icon <?php echo $contact_form_text_class?>"
									  style="background-color: <?php echo esc_attr($contact_form['tab_background_color']); ?>; color: <?php echo esc_attr($contact_form['tab_text_color']); ?>;"><i
										class="far fa-envelope"></i><?php echo esc_html($contact_form['text_in_tab']); ?></span>
								<?php
								$submit_button_text = ($contact_form['submit_button_text'] != '') ? $contact_form['submit_button_text'] : 'Submit';
								$submit_button_style = ($contact_form['submit_button_background_color'] != '') ? "background-color: " . $contact_form['submit_button_background_color'] . ";" : '';
								$submit_button_style .= ($contact_form['submit_button_text_color'] != '') ? "color:" . $contact_form['submit_button_text_color'] . ";" : '';

								$heading_color = ( isset($contact_form['headine_text_color']) && $contact_form['headine_text_color'] != '') ? "color: " . $contact_form['headine_text_color'] . ";" : ( ($contact_form['submit_button_background_color'] != '') ? "color: " . $contact_form['submit_button_background_color'] . ";" : 'color:#7761DF;' );
								
								$heading_color .= (isset($contact_form['form_bg_color']) && $contact_form['form_bg_color'] != '') ? "background-color:". $contact_form['form_bg_color'] : '';

								$contact_form['name_value'] = ($contact_form['name_value'] != '') ? $contact_form['name_value'] : esc_html__('Name', 'mystickyelements');
								$contact_form['phone_value'] = ($contact_form['phone_value'] != '') ? $contact_form['phone_value'] : esc_html__('Phone', 'mystickyelements');
								$contact_form['email_value'] = ($contact_form['email_value'] != '') ? $contact_form['email_value'] : esc_html__('Email', 'mystickyelements');
								$contact_form['message_value'] = ($contact_form['message_value'] != '') ? $contact_form['message_value'] : esc_html__('Message', 'mystickyelements');
								?>
								<div class="element-contact-form" style="background-color: <?php echo ( isset($contact_form['form_bg_color']))? $contact_form['form_bg_color'] : '#ffffff'; ?>">
									<?php if( isset( $contact_form['contact_title_text'] ) && $contact_form['contact_title_text'] != '' ) {
										$contact_title_text = $contact_form['contact_title_text']; 
									} else { 
										$contact_title_text = "Contact Form"; 
									} ?>
									<h3 style="<?php echo esc_attr($heading_color); ?>">
										<?php echo $contact_title_text; ?>
										<a href="javascript:void(0);" class="element-contact-close"><i class="fas fa-times"></i></a>
									</h3>

									<form id="stickyelements-form" action="" method="post" autocomplete="off">
										<?php foreach ( $contact_field as $value ) :
											switch ( $value ) {
												case 'name' :

										if (isset($contact_form['name']) && $contact_form['name'] == 1): ?>
											<input
												class="<?php if (isset($contact_form['name_require']) && $contact_form['name_require'] == 1): ?> required<?php endif; ?>"
												type="text" id="contact-form-name" name="contact-form-name" value=""
												placeholder="<?php echo esc_attr($contact_form['name_value']); if (isset($contact_form['name_require']) && $contact_form['name_require'] == 1): echo esc_html__("*", 'mystickyelements'); endif; ?>"  <?php if (isset($contact_form['name_require']) && $contact_form['name_require'] == 1): ?> required<?php endif; ?> autocomplete="off"/>
										<?php endif;
												break;
											case 'phone' :

										if (isset($contact_form['phone']) && $contact_form['phone'] == 1): ?>
											<input
												class="<?php if (isset($contact_form['phone_require']) && $contact_form['phone_require'] == 1): ?> required<?php endif; ?>"
												type="tel" id="contact-form-phone" name="contact-form-phone" value=""
												placeholder="<?php echo esc_attr($contact_form['phone_value']); if (isset($contact_form['phone_require']) && $contact_form['phone_require'] == 1): echo esc_html__("*", 'mystickyelements'); endif; ?>" <?php if (isset($contact_form['phone_require']) && $contact_form['phone_require'] == 1): ?> required <?php endif; ?> autocomplete="off" />
										<?php endif;
												break;
											case 'email' :

										if (isset($contact_form['email']) && $contact_form['email'] == 1): ?>
											<input
												class="email <?php if (isset($contact_form['email_require']) && $contact_form['email_require'] == 1): ?> required<?php endif; ?>"
												type="email" id="contact-form-email" name="contact-form-email" value=""
												placeholder="<?php echo esc_attr($contact_form['email_value']); if (isset($contact_form['email_require']) && $contact_form['email_require'] == 1): echo esc_html__("*", 'mystickyelements'); endif; ?>" <?php if (isset($contact_form['email_require']) && $contact_form['email_require'] == 1): ?> required <?php endif; ?> autocomplete="off"/>
										<?php endif;
												break;
											case 'message' :

										if (isset($contact_form['message']) && $contact_form['message'] == 1): ?>
											<textarea
												class="<?php if (isset($contact_form['message_require']) && $contact_form['message_require'] == 1): ?> required<?php endif; ?>"
												id="contact-form-message" name="contact-form-message"
												placeholder="<?php echo esc_attr($contact_form['message_value']); if (isset($contact_form['message_require']) && $contact_form['message_require'] == 1): echo esc_html__("*", 'mystickyelements'); endif; ?>" <?php if (isset($contact_form['message_require']) && $contact_form['message_require'] == 1): ?> required <?php endif; ?>></textarea>
										<?php endif;
												break;
											case 'dropdown' :
											if (isset($contact_form['dropdown']) && $contact_form['dropdown'] == 1): ?>
											<select id="contact-form-dropdown" name="contact-form-dropdown" class="<?php if (isset($contact_form['dropdown_require']) && $contact_form['dropdown_require'] == 1): ?> required<?php endif; ?>" <?php if (isset($contact_form['dropdown_require']) && $contact_form['dropdown_require'] == 1): ?> required <?php endif; ?>>

												<option value="" disabled selected><?php echo "Select " . $contact_form['dropdown-placeholder'];?><?php if (isset($contact_form['dropdown_require']) && $contact_form['dropdown_require'] == 1): echo esc_html__("*", 'mystickyelements'); endif; ?></option>
												<?php foreach( $contact_form['dropdown-option'] as $option ):
													if ( $option == '' ) {
														continue;
													}
													?>
													<option value="<?php echo esc_html($option);?>"><?php echo esc_html($option);?></option>
												<?php endforeach;?>
											</select>

										<?php endif;
												break;

											} /* End Switch case */
										endforeach;  ?>
										<p class="mse-form-success-message" id="mse-form-error" style="display:none;"></p>
										<input id="stickyelements-submit-form" type="submit" name="contact-form-submit"
											   value="<?php echo esc_html($submit_button_text); ?>"
											   style="<?php echo esc_attr($submit_button_style); ?>"/>
										<?php $unique_id = uniqid() . time() . uniqid(); ?>
										<input type="hidden" name="nonce" value="<?php echo $unique_id ?>">
										<input type="hidden" name="form_id"
											   value="<?php echo wp_create_nonce($unique_id) ?>">
										<input type="hidden" id="stickyelements-page-link" name="stickyelements-page-link" value="<?php echo esc_url(home_url( $wp->request ))?>" />
										
									</form>
								</div>
							</li>
						<?php endif; /* Contact Form */

						
						if (!empty($social_channels_tabs) && isset($social_channels['enable']) && $social_channels['enable'] == 1) :
							$protocols = array('http', 'https', 'mailto', 'tel', 'sms','javascript','viber','skype');
							foreach ($social_channels_tabs as $key => $value):
								if ( $key == 'is_empty' ) {
									continue;
								}
								$link_target = 1;
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
								
								//$social_channels_list = $social_channels_lists[$key];
								$element_class = '';
								if (isset($value['desktop']) && $value['desktop'] == 1) {
									$element_class .= ' element-desktop-on';
								}
								if (isset($value['mobile']) && $value['mobile'] == 1) {
									$element_class .= ' element-mobile-on';
								}


								//$hover_text = ($value['hover_text'] != '') ? $value['hover_text'] : $social_channels_list['hover_text'];
								$hover_text = ($value['hover_text'] != '') ? $value['hover_text'] : '';
								$social_link = '';
								$channel_type = (isset($value['channel_type'])) ? $value['channel_type'] : '';
								switch ($key) {
									case 'whatsapp':
										$value['text'] = str_replace( array('http://', 'https://') , array('','') , $value['text']);
										if ( isset($value['pre_set_message']) && $value['pre_set_message'] != '' ) {
											$social_link = 'https://api.whatsapp.com/send?phone=' .str_replace('+', '', $value['text']) . '&text=' . $value['pre_set_message'];
										} else {
											$social_link = 'https://api.whatsapp.com/send?phone=' . str_replace('+', '', $value['text']);
										}
										if ( wp_is_mobile()) {
											$link_target = 0;
										}
										break;
									case 'phone':
										$value['text'] = str_replace( array('http://', 'https://') , array('','') , $value['text']);


										if (strpos($value['text'], 'tel:') == false) {
											$social_link = "tel:".$value['text'];
										} else {
											$social_link = $value['text'];
										}
										$link_target = 0;
										break;
									case 'email':
										if (strpos($value['text'], 'mailto:') == false) {
											$social_link = "mailto:".$value['text'];
										} else {
											$social_link = $value['text'];
										}
										$link_target = 0;
										break;
									case 'wechat':
										$social_link = '';
										break;
									case 'facebook_messenger':
										$value['text'] = str_replace( array('http://', 'https://') , array('','') , $value['text']);
										$value_dash_count = substr_count ($value['text'], '-');
										if( $value_dash_count > 0 ) {
											$split_value = explode( '-', $value['text'] );
											$value_final = $split_value[count($split_value)-1];
										} else {
											$value_final = $value['text'];
										}
										$social_link = 'https://m.me/' . $value_final;
										if ( wp_is_mobile()) {
											$link_target = 0;
										}
										break;
									case 'address':
										$social_link = '';
										$value['text'] = str_replace( array('http://', 'https://') , array('','') , $value['text']);
										if ($value['text'] != '') {
											$hover_text .= ': ' . $value['text'];
										}
										break;
									case 'business_hours':
										$social_link = '';
										$value['text'] = str_replace( array('http://', 'https://') , array('','') , $value['text']);
										if ($value['text'] != '') {
											$hover_text .= ': ' . $value['text'];
										}
										break;
									case 'telegram' :
									
										if ( strpos( $value['text'], '//t.me') == '' ) {
											$social_link = "https://t.me/" . str_replace( '@', '', $value['text'] );
										} else {
											$social_link = $value['text'];
										}
										break;
									case 'vk' :
										$social_link = 'https://vk.me/' . $value['text'];
										break;
									case 'viber' :
										$value['text'] = str_replace('+','', $value['text']);
										$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
										$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
										$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
										if( $iPod || $iPhone ){
											$value['text'] = '+'.$value['text'];
										}else if($iPad){
											$value['text'] = '+'.$value['text'];
										}
										$social_link = "viber://chat?number=" . $value['text'];
										if ( wp_is_mobile()) {
											$link_target = 0;
										}
										break;
									case 'snapchat' :
										 $social_link = "https://www.snapchat.com/add/" . $value['text'];
										break;
									case 'skype' :
										$social_link = "skype:" . $value['text'] . "?chat";
										$link_target = 0;
										break;
									case 'SMS' :
										$social_link = "sms:" . $value['text'];
										$link_target = 0;
										break;
									case 'qq':
										$social_link = '';
										$value['text'] = str_replace( array('http://', 'https://') , array('','') , $value['text']);
										if ($value['text'] != '') {
											$hover_text .= ': ' . $value['text'];
										}
										break;
									case 'tiktok':
										$pos = strpos( $value['text'] , '@' );
										if($pos === false){
											$value['text'] = '@'.$value['text'] ;
										}
										$social_link = 'https://www.tiktok.com/'.$value['text'];
										break;		
									default;
										if ( $channel_type == 'whatsapp') {
											$value['text'] = str_replace( array('http://', 'https://') , array('','') , $value['text']);
											if ( isset($value['pre_set_message']) && $value['pre_set_message'] != '' ) {
												$social_link = 'https://api.whatsapp.com/send?phone=' .str_replace('+', '', $value['text']) . '&text=' . $value['pre_set_message'];
											} else {
												$social_link = 'https://api.whatsapp.com/send?phone=' . str_replace('+', '', $value['text']);
											}
											if ( wp_is_mobile()) {
												$link_target = 0;
											}
										} else {
											$social_link = $value['text'];
										}										
										break;
								}
								if ( isset($social_channels_list['custom_html']) && $social_channels_list['custom_html'] == 1) {
									$social_link = '';
									$element_class .= ' mystickyelements-custom-html-main';
								}
								if(preg_match('/^<iframe /',$value['text'])){
									$element_class .=" mystickyelements-custom-html-iframe";
								}								
								
								if( isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 ) {
									if( isset($value['open_newtab']) && $value['open_newtab'] == 1 ) {
										$link_target = 1;
									} else {
										$link_target = 0;
									}
								} 
								?>
								<li id="mystickyelements-social-<?php echo esc_attr($key);?>"
									class="mystickyelements-social-icon-li mystickyelements-social-<?php echo esc_attr($key);?> <?php echo esc_attr($element_class);?>">
									<?php
									/*diamond template css*/
									if ( isset($value['bg_color']) && $value['bg_color'] != '' ) {
										?>
										<style>
											<?php 
											if( $general_settings['templates'] == 'diamond' ) {
											?>
												.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
													background: <?php echo $value['bg_color']; ?>;
												}
												@media only screen and (min-width: 1025px) {
													.mystickyelements-position-left.mystickyelements-on-click.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form).elements-active span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after,
													.mystickyelements-position-left.mystickyelements-on-hover.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form):hover span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after	{
														background-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-right.mystickyelements-on-click.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form).elements-active span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after,
													.mystickyelements-position-right.mystickyelements-on-hover.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form):hover span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after {
														background-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-left.mystickyelements-templates-diamond .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-left-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-right.mystickyelements-templates-diamond .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-right-color: <?php echo $value['bg_color']; ?>;
													}
												}
												@media only screen and (max-width: 1024px) {
													.mystickyelements-position-mobile-left.mystickyelements-on-click.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form).elements-active span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after,
													.mystickyelements-position-mobile-left.mystickyelements-on-hover.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form):hover span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after	{
														background-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-mobile-right.mystickyelements-on-click.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form).elements-active span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after,
													.mystickyelements-position-mobile-right.mystickyelements-on-hover.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form):hover span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after {
														background-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-mobile-left.mystickyelements-templates-diamond .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-left-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-mobile-right.mystickyelements-templates-diamond .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-right-color: <?php echo $value['bg_color']; ?>;
													}
												}
											<?php 
											}
											if( $general_settings['templates'] == 'arrow' ) {
											?>
												<?php if( $key == 'insagram' ) { ?>
												.mystickyelements-position-left.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
												.mystickyelements-position-left.mystickyelements-templates-arrow .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
													background: <?php echo $value['bg_color']; ?>;
												}
												<?php } ?>
												@media only screen and (min-width: 1025px) {
													.mystickyelements-position-left.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-left.mystickyelements-templates-arrow .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-left-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-right.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-right.mystickyelements-templates-arrow .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-right-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-bottom.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-bottom.mystickyelements-templates-arrow .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-bottom-color: <?php echo $value['bg_color']; ?>;
													}
												}
												@media only screen and (max-width: 1024px) {
													.mystickyelements-position-mobile-left.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-mobile-left.mystickyelements-templates-arrow .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-left-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-mobile-right.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-mobile-right.mystickyelements-templates-arrow .social-<?php echo esc_attr($key);?> +  span.mystickyelements-social-text:before {
														border-right-color: <?php echo $value['bg_color']; ?>;
													}
												}
											<?php 
											}
											if( $general_settings['templates'] == 'triangle' ) {
											?>
												.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
												.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::after {
													background: <?php echo $value['bg_color']; ?>;
												}
												@media only screen and (min-width: 1025px) {
													.mystickyelements-position-left.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) .social-<?php echo esc_attr($key);?> + span.mystickyelements-social-text::before {
														border-left-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-right.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) .social-<?php echo esc_attr($key);?> + span.mystickyelements-social-text::before {
														border-right-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-bottom.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-bottom.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) .social-<?php echo esc_attr($key);?> + span.mystickyelements-social-text::before {
														border-bottom-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-bottom.mystickyelements-on-click.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form).elements-active span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-bottom.mystickyelements-on-hover.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form):hover span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
														background-color: <?php echo $value['bg_color']; ?>;
													}
												}
												@media only screen and (max-width: 1024px) {
													.mystickyelements-position-mobile-left.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) .social-<?php echo esc_attr($key);?> + span.mystickyelements-social-text::before {
														border-left-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-mobile-right.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) .social-<?php echo esc_attr($key);?> + span.mystickyelements-social-text::before {
														border-right-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-mobile-left.mystickyelements-on-click.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form).elements-active span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-mobile-left.mystickyelements-on-hover.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form):hover span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before	{
														background-color: <?php echo $value['bg_color']; ?>;
													}
													.mystickyelements-position-mobile-right.mystickyelements-on-click.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form).elements-active span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before,
													.mystickyelements-position-mobile-right.mystickyelements-on-hover.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form):hover span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
														background-color: <?php echo $value['bg_color']; ?>;
													}
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
									?>										
									<span class="mystickyelements-social-icon social-<?php echo esc_attr($key);?> social-<?php echo esc_attr($channel_type);?>" data-tab-setting = '<?php echo ( isset($general_settings["open_tabs_when"]) && $general_settings["open_tabs_when"]!="" ) ? $general_settings["open_tabs_when"] : "";?>' data-click = "0"data-mobile-behavior="<?php echo (isset($general_settings['mobile_behavior']) && $general_settings['mobile_behavior'] != '' ) ? $general_settings['mobile_behavior'] : '' ?>" data-flyout="<?php echo (isset($general_settings['flyout']) && $general_settings['flyout'] != '' ) ? $general_settings['flyout'] : '' ?>"
										  <?php if (isset($value['bg_color']) && $value['bg_color'] != ''): ?> style="background: <?php echo esc_attr($value['bg_color']); ?>" <?php endif;
									?>>
										
										<?php if ( $social_link != ''  ):	?>
											<a href="<?php echo esc_url($social_link, $protocols); ?>"  <?php if ( $link_target == 1 ):?> target="_blank" rel="noopener" <?php endif;?> data-url="<?php echo esc_url($social_link, $protocols); ?>" data-tab-setting = '<?php echo ( isset($general_settings["open_tabs_when"]) && $general_settings["open_tabs_when"]!="" ) ? $general_settings["open_tabs_when"] : "";?>'  data-mobile-behavior="<?php echo (isset($general_settings['mobile_behavior']) && $general_settings['mobile_behavior'] != '' ) ? $general_settings['mobile_behavior'] : '' ?>" data-flyout="<?php echo (isset($general_settings['flyout']) && $general_settings['flyout'] != '' ) ? $general_settings['flyout'] : '' ?>">
										<?php endif;
															
										
										if (isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && $value['custom_icon'] != '' &&  $value['fontawesome_icon'] == ''): ?>
											<img class="<?php echo ( isset($value['stretch_custom_icon']) && $value['stretch_custom_icon'] == 1 ) ? 'mystickyelements-stretch-custom-img' : '';  ?>" src="<?php echo esc_url($value['custom_icon']); ?>"/>
										<?php else: 
											if ( isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && $value['fontawesome_icon'] != '' ) {
												$social_channels_list['class'] = $value['fontawesome_icon'];
											}
											
											if ( isset($social_channels_list['custom_svg_icon']) && $social_channels_list['custom_svg_icon'] != '' ) :
												echo $social_channels_list['custom_svg_icon'];
											else:
										?>
											<i class="<?php echo esc_attr($social_channels_list['class']); ?>" <?php if ( isset($value['icon_color']) && $value['icon_color'] != '') : echo "style='color:" . $value['icon_color'] . "'"; endif; ?>></i>
										<?php endif;
											endif;
										if ( isset($value['icon_text']) && $value['icon_text'] != '' && isset($general_settings['templates']) && $general_settings['templates'] == 'default' ) {
											$icon_text_size = '';
											if ( isset($value['icon_text_size']) && $value['icon_text_size'] != '') {
												$icon_text_size = "font-size: " . $value['icon_text_size'] . "px";
											}
											echo "<span class='mystickyelements-icon-below-text' style='".$icon_text_size."'>" . esc_html($value['icon_text']) . "</span>";
										}
										if ( $social_link != '' ): ?>
											</a>
										<?php endif;
										
										if ( $key == 'line') {
											echo "<style>.mystickyelements-social-icon.social-". $key ." svg .fil1{ fill:" .$value['icon_color']. "}</style>";
										}
										if ( $key == 'qzone') {
											echo "<style>.mystickyelements-social-icon.social-". $key ." svg .fil2{ fill:" . $value['icon_color'] . "}</style>";
										}
										?>
									</span>									
								<?php if ( isset($social_channels_list['custom_html']) && $social_channels_list['custom_html'] == 1  ) :?>
									<div class="mystickyelements-custom-html" <?php if (isset($value['bg_color']) && $value['bg_color'] != ''): ?>style="background: <?php echo esc_attr($value['bg_color']); ?>" <?php endif; ?>>
										<div class="mystickyelements-custom-html-wrap">
											<?php echo do_shortcode( str_replace('\"', '"', stripslashes($value['text'])));?>
										</div>
									</div>
								<?php else :
									$icon_bg_color = $icon_text_color = '';
									if (isset($value['bg_color']) && $value['bg_color'] != '') {
										$icon_bg_color = "background: " . esc_attr($value['bg_color']) . ";";
									}
									if (isset($value['icon_color']) && $value['icon_color'] != '') {
										$icon_text_color = "color: " . esc_attr($value['icon_color']) . ";";
									}
									if ( $hover_text != '') :
								?>
									<span class="mystickyelements-social-text <?php echo ($social_link == '') ? 'mystickyelements-social-no-link' : '';?>" style= "<?php echo $icon_bg_color.$icon_text_color ?>" >
										<?php if ($social_link != ''): ?>
										<a href="<?php echo esc_url($social_link, $protocols); ?>"  <?php if ( $link_target == 1 ):?> target="_blank" rel="noopener" <?php endif;?> <?php if ( isset($value['icon_color']) && $value['icon_color'] != '') : echo "style='color:" . $value['icon_color'] . "'"; endif; ?> data-tab-setting = '<?php echo ( isset($general_settings["open_tabs_when"]) && $general_settings["open_tabs_when"]!="" ) ? $general_settings["open_tabs_when"] : "";?>' data-flyout="<?php echo (isset($general_settings['flyout']) && $general_settings['flyout'] != '' ) ? $general_settings['flyout'] : '' ?>">
											<?php endif;
											?>
											<?php
											if ($key == 'wechat') {
												echo esc_html($hover_text . ': ' . $value['text']);
											} else {
												echo esc_html(stripslashes($hover_text));
											}?>
											<?php if ($social_link != ''): ?>
										</a>
									<?php endif; ?>
									</span>
								<?php endif; /* Hover Text Not equal to blank */
								
								endif;?>
								</li>

							<?php endforeach;
						endif;
						?>
					</ul>					
				</div>
            </div>


        <?php
        }

        public function mystickyelements_contact_form() {

            global $wpdb;
			
			if ( is_user_logged_in() && ! current_user_can( 'manage_options' ) ) {
				wp_die(0); 
			}
            check_ajax_referer('mystickyelements', 'security');

            $errors = array();

            $contact_form = get_option('mystickyelements-contact-form');


            if (isset($contact_form['name']) && $contact_form['name'] == 1) {
                if (isset($contact_form['name_require']) && $contact_form['name_require'] == 1 && (!isset($_POST['contact-form-name']) || empty($_POST['contact-form-name']))) {
                    $error = array(
                        'key' => "contact-form-name",
                        'message' => "This field is required"
                    );
                    $errors[] = $error;
                }
            }

            if (isset($contact_form['email']) && $contact_form['email'] == 1) {
                if (isset($contact_form['email_require']) && $contact_form['email_require'] == 1 && (!isset($_POST['contact-form-email']) || empty($_POST['contact-form-email']))) {
                    $error = array(
                        'key' => "contact-form-email",
                        'message' => "This field is required"
                    );
                    $errors[] = $error;
                } else if ( isset($contact_form['email_require']) && $contact_form['email_require'] == 1 && isset($_POST['contact-form-email']) && !filter_var($_POST['contact-form-email'], FILTER_VALIDATE_EMAIL)) {
                    $error = array(
                        'key' => "contact-form-email",
                        'message' => "Email address is not valid"
                    );
                    $errors[] = $error;
                }
            }

            if (isset($contact_form['message']) && $contact_form['message'] == 1) {
                if (isset($contact_form['message_require']) && $contact_form['message_require'] == 1 && (!isset($_POST['contact-form-message']) || empty($_POST['contact-form-message']))) {
                    $error = array(
                        'key' => "contact-form-message",
                        'message' => "This field is required"
                    );
                    $errors[] = $error;
                }
            }

            if (isset($contact_form['phone']) && $contact_form['phone'] == 1) {
                if (isset($contact_form['phone_require']) && $contact_form['phone_require'] == 1 && (!isset($_POST['contact-form-phone']) || empty($_POST['contact-form-phone']))) {
                    $error = array(
                        'key' => "contact-form-phone",
                        'message' => "This field is required"
                    );
                    $errors[] = $error;
                }
            }
			if (isset($contact_form['dropdown']) && $contact_form['dropdown'] == 1) {
                if (isset($contact_form['dropdown_require']) && $contact_form['dropdown_require'] == 1 && (!isset($_POST['contact-form-dropdown']) || empty($_POST['contact-form-dropdown']))) {
                    $error = array(
                        'key' => "contact-form-dropdown",
                        'message' => "This field is required"
                    );
                    $errors[] = $error;
                }
            }
            $message = "There is error. We are not able to complete your request";

            if (empty($errors)) {
                if (!isset($_POST['nonce']) || empty($_POST['nonce'])) {
                    $error = array(
                        'key' => "mse-form-error",
                        'message' => "There is error. We are not able to complete your request"
                    );
                    $errors[] = $error;
                } else if (!isset($_POST['form_id']) || empty($_POST['form_id'])) {
                    $error = array(
                        'key' => "mse-form-error",
                        'message' => "There is error. We are not able to complete your request"
                    );
                    $errors[] = $error;
                } else if (!wp_verify_nonce($_POST['form_id'], $_POST['nonce'])) {
                    $error = array(
                        'key' => "mse-form-error",
                        'message' => "There is error. We are not able to complete your request"
                    );
                    $errors[] = $error;
                }
                if (!empty($errors)) {
                    echo json_encode(array("status" => 0, "error" => 1, "errors" => $errors, "message" => $message));
                    die;
                }
            } else {
                echo json_encode(array("status" => 0, "error" => 1, "errors" => $errors, "message" => $message));
                die;
            }

			/* Check redirct Link set */
			$redirect_link = '';
			if ( ( isset($contact_form['redirect']) && $contact_form['redirect'] == 1 ) && ( isset($contact_form['redirect_link']) && $contact_form['redirect_link'] != '' ) ) {
				$redirect_link = $contact_form['redirect_link'];
			}

             if (isset($_POST['contact-form-email']) || isset($_POST['contact-form-name']) || isset($_POST['contact-form-phone']) || isset($_POST['contact-form-message']) ) {

                if (isset($contact_form['send_leads']) && $contact_form['send_leads'] == 'mail') {

                    $send_mail = (isset($contact_form['sent_to_mail']) && $contact_form['sent_to_mail'] != '') ? $contact_form['sent_to_mail'] : get_option('admin_email');

                    $subject = "New lead from MyStickyElements - " . $_POST['contact-form-name'];
                    $message = "You got a new lead via your 'MyStickyElements' contact form:" . "\r\n\r\n";

                    if (isset($_POST['contact-form-name']) && $_POST['contact-form-name'] != '') {
                        $message .= "<p>Name: " . sanitize_text_field($_POST['contact-form-name']) . "<p>\r\n";
                    }
                    if (isset($_POST['contact-form-phone']) && $_POST['contact-form-phone'] != '') {
                        $message .= "<p>Phone: " . sanitize_text_field($_POST['contact-form-phone']) . "</p>\r\n";
                    }
                    if (isset($_POST['contact-form-email']) && $_POST['contact-form-email'] != '') {
                        $message .= "<p>Email: " . sanitize_email($_POST['contact-form-email']) . "</p>\r\n";
                    }
					if (isset($_POST['contact-form-dropdown']) && $_POST['contact-form-dropdown'] != '') {
                        $message .= "<p>" . $contact_form['dropdown-placeholder'] . ": " . sanitize_text_field($_POST['contact-form-dropdown']) . "</p>\r\n";
                    }
                    if (isset($_POST['contact-form-message']) && $_POST['contact-form-message'] != '') {
                        $message .= "<p>Message: " . sanitize_text_field(stripslashes($_POST['contact-form-message'])) . "</p>\r\n \r\n";
                    }

                    $message .= "<p>Thank You" . "</p>\r\n";
                    $message .= "<p>" . get_bloginfo('name') . "</p>\r\n";

                    $blog_name = get_bloginfo('name');
                    $blog_email = get_bloginfo('admin_email');

                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= 'From: ' . $blog_name . ' <' . $blog_email . '>' . "\r\n";
                    $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";

                    if (wp_mail($send_mail, $subject, $message, $headers)) {
                        $message = esc_html__('Your request is submitted successfully', 'mystickyelements');
                        echo json_encode(array("status" => 1, "error" => 0, "errors" => array(), "message" => $message, "redirect_link" => $redirect_link));
                        die;
                    } else {
                        $message = esc_html__('Something went wrong. Please contact site administrator', 'mystickyelements');
                        echo json_encode(array("status" => 0, "error" => 0, "errors" => array(), "message" => $message));
                    }

                } else {

                    $resultss = $wpdb->insert(
                        $wpdb->prefix . 'mystickyelement_contact_lists',
                        array(
                            'contact_name' 		=> isset($_POST['contact-form-name']) ? esc_sql(sanitize_text_field($_POST['contact-form-name'])) : '',
                            'contact_phone' 	=> isset($_POST['contact-form-phone']) ? esc_sql(sanitize_text_field($_POST['contact-form-phone'])) : '',
                            'contact_email' 	=> isset($_POST['contact-form-email']) ? esc_sql(sanitize_email($_POST['contact-form-email'])) : '',
                            'contact_message' 	=> isset($_POST['contact-form-message']) ? sanitize_textarea_field(stripslashes($_POST['contact-form-message'])) : '',
							'contact_option' 	=> (isset($_POST['contact-form-dropdown'])) ? esc_sql(sanitize_textarea_field($_POST['contact-form-dropdown'])) : '',
							'message_date' 		=> date('Y-m-d H:i:s'),
							'page_link' 		=> esc_sql(sanitize_text_field($_POST['stickyelements-page-link'])),
                        )
                    );
					
                    $message = esc_html__('Your message was sent successfully', 'mystickyelements');
                    echo json_encode(array("status" => 1, "error" => 0, "errors" => array(), "message" => $message, "redirect_link" => $redirect_link));
                    die;
                }
            }
            wp_die();
        }
		
		function get_user_ipaddress() {
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
				//ip from share internet
				$ip = sanitize_text_field( wp_unslash($_SERVER['HTTP_CLIENT_IP']));
			}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				//ip pass from proxy
				$ip = sanitize_text_field( wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
			}else{
				$ip = sanitize_text_field( wp_unslash($_SERVER['REMOTE_ADDR']));
			}
			return $ip;
		}
    }

}
global $front_settings_page;
$front_settings_page = new MyStickyElementsFrontPage_pro();
