<?php
class Ibtana_Visual_Editor_Menu_Class {

	public $active_plugins;
	public $post_type;
	public $has_read_write_perms;
	public $allow_file_generation;

	public $config_steps = array();
	protected $theme_title = '';

	/* Constructor method for the class. */
	function __construct() {
		// ---------- Ibtana Plugin Activation End -------

		$this->active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'ibtana_visual_editor_load_custom_wp_admin_style' ) );

		// -------- Get Current Theme Name ----------
		$current_theme = wp_get_theme();
		$this->theme_title = $current_theme->get( 'Name' );

		// ---------- Setup Free Template --------
		add_action( 'wp_ajax_ibtana_visual_editor_setup_free_demo', array( $this, 'ibtana_visual_editor_setup_free_demo' ) );
		add_action( 'wp_ajax_ibtana_visual_editor_insert_component', array( $this, 'ibtana_visual_editor_insert_component' ) );
		require_once dirname( __FILE__ ) . '/activation/activation.php';

		// --------- Activate And Deactivate Plugin---------


		add_action( 'wp_ajax_ibtana_visual_editor_activate_plugin', array( $this, 'ibtana_visual_editor_activate_plugin' ) );

		// Addons Page Ajax
		add_action( 'wp_ajax_ive_addons_page_cards', array( $this, 'ive_addons_page_cards' ) );
		// Addons Page Ajax


		add_action( 'activated_plugin', array( $this, 'ive_plugin_activated' ), 100, 2 );
		add_action( 'upgrader_process_complete', array( $this, 'ive_upgrader_process_complete' ), 10, 2 );
		add_filter( 'add_meta_boxes', array( $this, 'ibtana_visual_editor_add_meta_boxes' ) );
	}


	public function ive_addon_check_license_status( $plugin_path ) {
		$iepa_key_arr_license_status = false;
		if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_path ) ) {
			$iepa_key = str_replace( '-', '_', get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path )['TextDomain'] ) . '_license_key';
			$iepa_key_arr = get_option( $iepa_key );
			if ( $iepa_key_arr ) {
				if ( isset( $iepa_key_arr['license_status'] ) ) {
					if ( true == $iepa_key_arr['license_status'] ) {
						$iepa_key_arr_license_status = true;
					}
				}
			}
		}
		return $iepa_key_arr_license_status;
	}

	public function ive_addons_page_cards() {

		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ibtana_addons_page' ) ) {
			exit;
		}

		$ive_add_on_plugins	=	array();
		$base_url =	IBTANA_LICENSE_API_ENDPOINT . 'get_client_add_on_list';
		$args 		= array(
			"site_url"	=> site_url()
		);
		$body			= wp_json_encode( $args );
		$options	= [
			'timeout'     => 60,
			'body'        => $body,
			'headers'     => [
				'Content-Type' => 'application/json',
			]
		];
		$response = wp_remote_post( $base_url, $options );

		if ( is_wp_error( $response ) ) {
			$ive_add_on_plugins	= array();
		} elseif ( $response['response']['code'] === 200 && $response['response']['message'] === 'OK' ) {
			$response				= json_decode($response['body']);
			$response_data	= $response->data;
			$ive_add_on_plugins				= $response_data->premium;
		}

		// echo "<pre>";
		// print_r($ive_add_on_plugins);
		// exit;

		$get_plugins					=	get_plugins();
		?>

		<?php if ( empty( $ive_add_on_plugins ) ): ?>
			<?php esc_html_e( 'Coming Soon...', 'ibtana-visual-editor' ); ?>
		<?php else: ?>
			<?php foreach ( $ive_add_on_plugins as $plugin ): ?>

				<?php

				$ibtana_addon_file_path				=	'';
				$is_ive_add_on_plugin_active	=	false;
				if ( isset( $plugin->add_on_main_file ) ) {
					$ibtana_addon_file_path = $plugin->domain . '/' . $plugin->add_on_main_file;
					$is_ive_add_on_plugin_active = is_plugin_active( $ibtana_addon_file_path );
				}
				?>

				<div class="plugin-card">
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<a href="<?php echo esc_url($plugin->permalink); ?>" target="_blank" class="plugin-name">
									<?php esc_html_e( $plugin->name ); ?>
									<img class="plugin-icon" src="<?php if( isset($plugin->image) && ( $plugin->image != '' ) ) { echo esc_url( $plugin->image ); } else { echo esc_url( IBTANA_PLUGIN_URI . 'dist/images/ibtana-setting-icon.svg' ); } ?>" />
								</a>
							</h3>
						</div>
						<div class="action-links">
							<ul class="plugin-action-buttons ive-ibtana-add-on-buttons">


								<?php if ( ( $ibtana_addon_file_path != '' ) && isset( $get_plugins[ $ibtana_addon_file_path ] ) ) { ?>
									<li>
										<?php if ( $is_ive_add_on_plugin_active ): ?>
											<?php
											$ibtana_add_on_deactivation_url	=	str_replace( '&amp;', '&', wp_nonce_url(
												admin_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $ibtana_addon_file_path ) ),
												'deactivate-plugin_' . $ibtana_addon_file_path
											) );
											?>
											<button type="button" class="button" data-text="Deactivating..." data-href="<?php echo esc_url( $ibtana_add_on_deactivation_url ); ?>">
												<?php esc_html_e( 'Deactivate', 'ibtana-visual-editor' ); ?>
											</button>
										<?php else: ?>
											<?php
											$ibtana_add_on_activate_url	=	str_replace( '&amp;', '&', wp_nonce_url(
												admin_url( 'plugins.php?action=activate&amp;plugin=' . urlencode( $ibtana_addon_file_path ) ),
												'activate-plugin_' . $ibtana_addon_file_path
											) );
											?>
											<button type="button" class="button button-primary" data-text="Activating..." data-href="<?php echo esc_url( $ibtana_add_on_activate_url ); ?>">
												<?php esc_html_e( 'Activate', 'ibtana-visual-editor' ); ?>
											</button>
										<?php endif; ?>
									</li>
								<?php } else { ?>

									<?php if ( 'ibtana-ecommerce-product-addons' == $plugin->domain ): ?>
										<?php
										// $ibtana_add_on_installation_url	=	str_replace( '&amp;', '&', wp_nonce_url(
										// 	admin_url( 'update.php?action=install-plugin&amp;plugin=' . urlencode( $plugin->domain ) ),
										// 	'install-plugin_' . $plugin->domain
										// ) );
										?>
										<!-- <button type="button" class="button button-primary" data-text="Installing..." data-href="<?php //echo esc_url( $ibtana_add_on_installation_url ); ?>">
											<?php //esc_html_e( 'Install', 'ibtana-visual-editor' ); ?>
										</button> -->
										<a type="button" class="button button-primary" target="_blank" href="<?php echo esc_url( $plugin->permalink ); ?>">
											<?php esc_html_e( 'Buy Now', 'ibtana-visual-editor' ); ?>
										</a>
									<?php else: ?>

										<a type="button" class="button button-primary" target="_blank" href="<?php echo esc_url( $plugin->permalink ); ?>">
											<?php esc_html_e( 'Buy Now', 'ibtana-visual-editor' ); ?>
										</a>

									<?php endif; ?>


								<?php } ?>


								<?php if ( isset( $plugin->documentation ) && ( $plugin->documentation != '' ) ): ?>
									<li>
										<a href="<?php echo esc_url( $plugin->documentation ); ?>" target="_blank">
											<?php esc_html_e( 'Documentation', 'ibtana-visual-editor' ); ?>
										</a>
									</li>
								<?php endif; ?>

							</ul>
						</div>
						<div class="desc column-description">
							<p>
								<?php esc_html_e( $plugin->description, 'ibtana-visual-editor' ); ?>
							</p>
							<p class="authors"> <cite><?php esc_html_e( 'By', 'ibtana-visual-editor' ) ?> <a href="https://www.vwthemes.com/" target="_blank">
								<?php esc_html_e( 'VW Themes', 'ibtana-visual-editor' ); ?></a></cite>
							</p>
						</div>

						<div class="card-footer">


							<?php if ( property_exists( $plugin, 'price' ) && ( $plugin->price != '' ) && ( $plugin->price != 0 ) ): ?>
								<?php if ( !$this->ive_addon_check_license_status( $ibtana_addon_file_path ) ): ?>
									<?php if ( $plugin->permalink ): ?>
										<a target="_blank" href="<?php echo esc_url( $plugin->permalink ); ?>" type="button" class="">
											<?php esc_html_e( 'Buy Now for $' . $plugin->price, 'ibtana-visual-editor' ); ?>
										</a>
										<br>

										<?php if ( isset( $get_plugins[ $ibtana_addon_file_path ] ) ): ?>
											<?php esc_html_e( ' or ' ); ?>
											<a target="_blank" href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-license' ) ); ?>" type="button" class="">
												<?php esc_html_e( 'Activate License', 'ibtana-visual-editor' ); ?>
											</a>
										<?php endif; ?>


									<?php endif; ?>
								<?php endif; ?>
							<?php else: ?>
								<?php if ( $plugin->permalink ): ?>
									<a target="_blank" href="<?php echo esc_url( $plugin->permalink ); ?>" type="button" class="">
										<?php esc_html_e( 'Get it Free!', 'ibtana-visual-editor' ); ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>

						</div>

					</div>
					<div class="plugin-card-bottom">
							<!-- <div class="vers column-rating"></div> -->

							<div class="column-updated">
							<?php if ( $plugin->add_on_updated_at ): ?>
								<strong><?php esc_html_e( 'Last Updated:', 'ibtana-visual-editor' ) ?></strong> <?php printf( __( '%s ago' ), human_time_diff( $plugin->add_on_updated_at ) ) ?>
							<?php endif; ?>
							</div>

							<!-- <div class="column-downloaded"></div> -->
							<div class="column-compatibility">
								<span class="compatibility-compatible">
									<strong><?php esc_html_e( 'Compatible', 'ibtana-visual-editor' ); ?></strong> <?php esc_html_e( 'with your version of WordPress', 'ibtana-visual-editor' ); ?>
								</span>
							</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php
		exit;
	}

	public function ibtana_visual_editor_add_meta_boxes() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		if ( $screen->base != "post" ) {
			return;
		}

		if ( $screen->is_block_editor ) {
			return;
		}

		if ( ( $screen->post_type === "product" ) && !is_plugin_active( 'ibtana-ecommerce-product-addons/plugin.php' ) ) {
			add_meta_box(
				'ive_go_pro_ecommerce_metabox',
				__( 'WooCommerce Product Add-Ons', 'ibtana-visual-editor' ),
				[ $this, 'ive_render_go_pro_ecommerce_metabox' ],
				null,
				'side',
				'high'
			);
		}

		$wp_get_theme = wp_get_theme();

		add_meta_box(
			'ive_go_pro_template_metabox',
			$wp_get_theme->get( 'Name' ),
			[ $this, 'ive_render_go_pro_meta_box' ],
			null,
			'side',
			'high'
		);

	}


	public function ive_plugin_activated( $plugin, $network_wide ) {

		if( is_admin() && current_user_can( 'manage_options' ) && ( IVE_BASE == $plugin ) ) {
			if ( !get_option( 'ive_active_vw_theme_text_domain' ) ) {
				update_option( 'ive_active_vw_theme_text_domain', 'sirat' );
			}
		}

		if( is_admin() && current_user_can( 'install_themes' ) && ( IVE_BASE == $plugin ) ) {

			if ( count( wp_get_themes() ) && ( gettype( wp_get_themes() ) == 'array' ) ) {
				if ( !isset( wp_get_themes()['sirat'] ) ) {

					$ive_sirat_theme_install_action_url   = wp_nonce_url(
						self_admin_url( 'update.php?action=install-theme&theme=sirat&ive-sirat-installed=true' ),
						'install-theme_sirat'
					);

					$ive_sirat_theme_install_action_url = str_replace( '&amp;', '&', $ive_sirat_theme_install_action_url );

					wp_redirect( $ive_sirat_theme_install_action_url );
					exit;

				}
			}

		}

	}


	public function ive_upgrader_process_complete( $upgrader_object, $options ) {
		$our_plugin = IVE_BASE;
		if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			// Iterate through the plugins being updated and check if ours is there

			foreach( $options['plugins'] as $plugin ) {
				if( $plugin == $our_plugin ) {
					// Your action if it is your plugin

					if( is_admin() && current_user_can( 'manage_options' ) ) {
						if ( !get_option( 'ive_active_vw_theme_text_domain' ) ) {
							update_option( 'ive_active_vw_theme_text_domain', 'sirat' );
						}
					}

					if( is_admin() && current_user_can( 'install_themes' ) ) {
						if ( count( wp_get_themes() ) && ( gettype( wp_get_themes() ) == 'array' ) ) {
							if ( !isset( wp_get_themes()['sirat'] ) ) {

								$ive_sirat_theme_install_action_url   = wp_nonce_url(
									self_admin_url( 'update.php?action=install-theme&theme=sirat&ive-sirat-installed=true' ),
									'install-theme_sirat'
								);

								$ive_sirat_theme_install_action_url = str_replace( '&amp;', '&', $ive_sirat_theme_install_action_url );

								wp_redirect( $ive_sirat_theme_install_action_url );
								exit;
							}
						}
					}

					break;
				}
			}

		}
	}


	public function ive_render_go_pro_ecommerce_metabox() {

		$pro_msg			= __( 'Get pre-built premium product page templates using', 'ibtana-visual-editor' );
		$plugin_name	= __( 'Ibtana - Ecommerce Product Addons.', 'ibtana-visual-editor' );
		$btn_text 		= __( 'Upgrade To Pro!', 'ibtana-visual-editor' );

		?>
		<script type="text/javascript">
			( function ( $, window, document ) {
				$( document ).ready( function () {
					jQuery(
						'#ive_go_pro_ecommerce_metabox .inside'
					).append(
						`<p id="ive_product_metabox_license" class="ive_go_pro_metabox_p">
							<?php echo $pro_msg; ?> <strong><?php echo $plugin_name; ?></strong><br>
							<a class="ive_go_pro_metabox_a2 button"
							href="https://www.vwthemes.com/plugins/woocommerce-product-add-ons/"
							target="_blank">
								<?php echo $btn_text; ?>
							</a>
						</p>`
					);
				});
			}( jQuery, window, document ) );
		</script>
		<?php
	}


	public function ive_render_go_pro_meta_box() {

		$wp_get_theme = wp_get_theme();

		$wp_get_theme_TextDomain = $wp_get_theme->get( 'TextDomain' );
		if ( is_child_theme() ) {
			$wp_get_theme_TextDomain = wp_get_theme()->get( 'Template' );
		}
		$is_CUSTOM_TEXT_DOMAIN_defined = defined( 'CUSTOM_TEXT_DOMAIN' ) ? true : false;

		$ive_active_vw_theme_text_domain = get_option( 'ive_active_vw_theme_text_domain' ) ? get_option( 'ive_active_vw_theme_text_domain' ) : 'sirat';

		?>
		<style media="screen">
			#ive_go_pro_metabox_p, #ive_product_metabox_license {
				background: rgba(229,195,52,0.25);
				padding: 10px 8px;
				border-radius: 3px;
				border: 1px solid #dadada;
				margin-top: 16px;
				text-align: center;
				color: #000;
			}
			#ive_go_pro_metabox_p a, #ive_product_metabox_license a {
				background: linear-gradient(#6ccef5, #016194) !important;
				color: #fff;
				text-transform: capitalize;
				font-weight: bold;
				text-align: center;
				margin: 4% auto 0;
				border-radius: 4px !important;
			}
			#ive_go_pro_metabox_p a.ive_go_pro_metabox_a1 {
				width: 36%;
			}
			#ive_go_pro_metabox_p a.ive_go_pro_metabox_a2 {
				width: 60%;
			}
			#iepa_product_metabox_license {
				color: #000;
			}
		</style>
		<script type="text/javascript">
			( function ( $, window, document ) {
				$( document ).ready( function () {
					var IBTANA_LICENSE_API_ENDPOINT = '<?php echo IBTANA_LICENSE_API_ENDPOINT; ?>';

					var $wp_get_theme_TextDomain				= '<?php echo $wp_get_theme_TextDomain ?>';
					var $is_CUSTOM_TEXT_DOMAIN_defined	= '<?php echo $is_CUSTOM_TEXT_DOMAIN_defined; ?>';

					$.post(
						IBTANA_LICENSE_API_ENDPOINT + 'get_client_meta_box_info',
						{
							"theme_text_domain": 			$wp_get_theme_TextDomain
						},
						function ( data ) {

							if ( !data.data.is_found ) {

								var $ive_active_vw_theme_text_domain						=	'<?php echo $ive_active_vw_theme_text_domain; ?>';

								$.post(
									IBTANA_LICENSE_API_ENDPOINT + 'get_client_meta_box_info',
									{
										"theme_text_domain": 			$ive_active_vw_theme_text_domain
									}, function ( data ) {


										if ( !data.data.is_found ) {

											// $( '#ive_go_pro_template_metabox' ).hide();
											$( '#ive_go_pro_template_metabox .inside' ).append(
												`<p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
													Get all our <strong>160+ Premium Themes</strong> worth $9440 With Our <strong>WP Theme Bundle</strong> in just <strong>$99.</strong>
													<br>
													<a class="ive_go_pro_metabox_a1 button" href="https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true" target="_blank">
														Buy Now!
													</a>
												</p>`
											);
											$( '#ive_go_pro_template_metabox h2' ).text(
												'WP Theme Bundle'
											);

										} else {

											if ( data.data.is_found.name == "Sirat" ) {
												$( '#ive_go_pro_template_metabox .inside' ).append(
													`<p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
														<strong>Get Sirat Pro At Just $40.</strong>
														<br>
														<a class="ive_go_pro_metabox_a1 button" href="` + data.data.is_found.permalink + `" target="_blank">Buy Now!</a>
													</p>`
												);
												$( '#ive_go_pro_template_metabox h2' ).text(
													'Sirat Pro'
												);
											} else {
												var vw_pro_theme_name = 'Premium Features';
												if ( data.data.is_found.hasOwnProperty( 'parent_theme_template_data' ) ) {
													if ( data.data.is_found.parent_theme_template_data.hasOwnProperty( 'name' ) ) {
														vw_pro_theme_name = data.data.is_found.parent_theme_template_data.name;
													}
												}
												$( '#ive_go_pro_template_metabox .inside' ).append(
													`<p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
														<strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
														<br>
														<a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">
															Upgrade To Pro!
														</a>
													</p>`
												);
											}


										}


									}
								);


							} else {

								// If premium theme is installed
								if ( $is_CUSTOM_TEXT_DOMAIN_defined === "1" ) {
									$( '#ive_go_pro_template_metabox .inside' ).append(
										`<p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
											Get all our <strong>160+ Premium Themes</strong> worth $9440 With Our <strong>WP Theme Bundle</strong> in just <strong>$99.</strong>
											<br>
											<a class="ive_go_pro_metabox_a1 button" href="https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true" target="_blank">Buy Now!</a>
										</p>`
									);
									$( '#ive_go_pro_template_metabox h2' ).text(
										'WP Theme Bundle'
									);
								}

								// if free theme is installed

								else {

									var vw_pro_theme_name = 'Premium Features';
									if ( data.data.is_found.hasOwnProperty( 'parent_theme_template_data' ) ) {
										if ( data.data.is_found.parent_theme_template_data.hasOwnProperty( 'name' ) ) {
											vw_pro_theme_name = data.data.is_found.parent_theme_template_data.name;
										}
									}

									$( '#ive_go_pro_template_metabox .inside' ).append(
										`<p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
											<strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
											<br>
											<a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">
												Upgrade To Pro!
											</a>
										</p>`
									);

								}

							}
						}
					);

				});
			}( jQuery, window, document ) );
		</script>
		<?php
	}

	/**
 	 * create main menu page of ibtana plugin
 	 */

	/* ---------- Ibtana Wizard Steps ------ */
	public function ibtana_visual_editor_admin_main_tab_step() {
		$dev_steps = $this->config_steps;
		$steps = array(
			'ive-wizard-first-step' => array(
				'id'				=> 'ive-wizard-first-step',
				'title'			=> __( 'Welcome to Ibtana Visual Editor', 'ibtana-visual-editor' ) . $this->theme_title,
				'view'			=> 'ibtana_visual_editor_wizard_init', // Callback for content
				'callback'	=> 'do_next_step', // Callback for JS
				'can_skip'	=> false
			),
			'ive-wizard-second-step' => array(
				'id'				=> 'ive-wizard-second-step',
				'title'			=> __( 'Click on the import button to import template demo', 'ibtana-visual-editor' ),
				'icon'			=> 'dashicons-screenoptions',
				'view'			=> 'ibtana_visual_editor_free_templates',
				'can_skip'	=> true
			),
			'ive-wizard-three-step' => array(
				'id'				=> 'ive-wizard-three-step',
				'icon'			=> 'dashicons-welcome-widgets-menus',
				'view'			=> 'ibtana_visual_editor_import_template',
				'callback'	=> 'install_widgets',
				'can_skip'	=> true
			),
			'ive-wizard-four-step' => array(
				'id'				=> 'ive-wizard-four-step',
				'icon'			=> 'dashicons-welcome-widgets-menus',
				'view'			=> 'ibtana_visual_editor_inner_template_grid',
				'callback'	=> 'ive_inner_template_grid',
				'can_skip'	=> true
			),
			'ive-wizard-five-step' => array(
				'id'				=> 'ive-wizard-five-step',
				'icon'			=> 'dashicons-welcome-widgets-menus',
				'view'			=> 'ibtana_visual_editor_woocommerce_templates',
				'callback'	=> 'ive_woocommerce_templates',
				'can_skip'	=> true
			)
		);

		// Iterate through each step and replace with dev config values
		if( $dev_steps ) {
			// Configurable elements - these are the only ones the dev can update from config.php
			$can_config = array( 'title', 'icon', 'button_text', 'can_skip','button_text_two' );
			foreach( $dev_steps as $dev_step ) {
				// We can only proceed if an ID exists and matches one of our IDs
				if( isset( $dev_step['id'] ) ) {
					$id = $dev_step['id'];
					if( isset( $steps[$id] ) ) {
						foreach( $can_config as $element ) {
							if( isset( $dev_step[$element] ) ) {
								$steps[$id][$element] = $dev_step[$element];
							}
						}
					}
				}
			}
		}
		return $steps;
	}

	// -------- First Step ------
	public function ibtana_visual_editor_wizard_init() {
		?>
		<div class="ibtana-wizard-first-step-content ibtana-free-templates">
			<div class="ive-social-theme-search">
					<input class="themesearchinput ive-admin-wizard-search" type="text" placeholder="<?php esc_attr_e( 'Search Template Here', 'ibtana-visual-editor' ); ?>">
			</div>
			<div class="ive-ibtana-cards-box">
				<div class="ive-wz-spinner-wrap" style="display: none;">
					<div class="ive-lds-dual-ring"></div>
				</div>

				<div class="ive-o-product-main-row">
					<div class="ive-o-product-col-1">
						<ul class="ive-ibtaba-wizard-inner-sub-cats"></ul>
					</div>
					<div class="ive-o-product-col-2">
						<div class="ive-ibtana-wizard-product-row">
							<div class="ibtana-wizard-no-result">
								<?php esc_html_e('No Result Found','ibtana-visual-editor'); ?>
							</div>
							<h3 class="ive-coming-soon"><?php esc_html_e( 'Coming Soon...', 'ibtana-visual-editor' ); ?></h3>
						</div>
						<div class="ive-template-load-more">
							<a href="javascript:void(0)" class="button button-primary"><?php esc_html_e('Load More...','ibtana-visual-editor'); ?></a>
						</div>
					</div>
				</div>

			</div>

		</div>
		<?php
	}

	// -------- Second Step ------
	public function ibtana_visual_editor_free_templates() {
		?>
		<div class="ibtana-wizard-templates">
			<div id="ibtana-free-templates" class="ibtana-free-templates">
				<div class="ive-wz-spinner-wrap">
					<div class="ive-lds-dual-ring"></div>
				</div>
				<div class="ive-o-product-main-row">
					<h3 class="ive-coming-soon">
						<?php esc_html_e( 'Coming Soon...', 'ibtana-visual-editor' ); ?>
					</h3>
					<div class="ive-ibtana-wiazard-product-col-2">
						<div class="ive-social-theme-search">
							<input class="themesearchinput ive-admin-wizard-search" type="text" placeholder="<?php esc_attr_e( 'Search Template Here', 'ibtana-visual-editor' ); ?>">
						</div>
						<div class="ive-ibtana-wizard-product-row" style="clear: both;">
							<div class="ibtana-wizard-no-result">
								<?php esc_html_e('No Result Found','ibtana-visual-editor'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function ibtana_visual_editor_import_template() {
		?>
		<div class="ibtana-template-import-steps">
			<div class="ive-o-product-main-row">
				<span class="dashicons dashicons-admin-collapse"></span>
				<div class="ive-wizard-spinner ive-wz-spinner-wrap">
					<div class="ive-lds-dual-ring"></div>
				</div>
				<div class="ive-template-import-sidebar">
					<div class="ive-preview-close-btn">
						<span class="ive-close-preview dashicons dashicons-no-alt"></span>
						<span class="prev dashicons dashicons-arrow-left-alt2"></span>
						<span class="next dashicons dashicons-arrow-right-alt2"></span>
					</div>
					<div class="ive-sidebar-import-button">
						<a href="javascript:void(0);" data-callback="import_free_template" class="ive-plugin-btn ive-import-demo-btn ive-do-it">
							<?php esc_html_e('Free Import','ibtana-visual-editor'); ?>
						</a>
						<!-- <div class="ive-base-theme-notice">
							<p>Notice that you dont have the base theme installed/activated. Click the below button to install/activate the base theme.</p>
							<button class="" data-slug="">Install/Activate</button>
						</div> -->
					</div>
					<div class="ive-sidebar-content">
						<a href="" class="ive-plugin-btn" target="_blank"><?php esc_html_e('Buy Now','ibtana-visual-editor'); ?></a>

						<div class="ive-pp-scrollable">
							<h4 class="ive-template-name"></h4>
							<div class="ive-template-img">
								<img src="">
							</div>
							<div class="ive-template-text">
								<p>
								</p>
							</div>
							<div class="ive-bundle-text"></div>
						</div>

					</div>
					<div class="ive-sidebar-view-icons">
						<ul>
							<!-- <li><span class="dashicons dashicons-admin-collapse"></span></li> -->
							<li><span class="dashicons dashicons-desktop"></span></li>
							<li><span class="dashicons dashicons-tablet"></span></li>
							<li><span class="dashicons dashicons-smartphone"></span></li>
						</ul>
					</div>
				</div>
				<div class="ive-template-demo-sidebar">
					<iframe src="" width="100%" height="100%"></iframe>
				</div>
			</div>
		</div>
		<?php
	}

	function isAssoc( array $arr ) {
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	public function ibtana_visual_editor_setup_woocommerce_page() {

		if ( !isset( $_POST['page_id'] ) ) {

			// Attribute Creation Code if a variable product needed to create
			if ( isset( $_POST['is_variable_product'] ) ) {
				$attribute_data = array(
					array(
						'name'					=>	'Color',
						'type'					=>	'color',
						'taxonomy'			=>	'pa_color',
						'data'					=>	array(
							'Red'			=>	'#dd3333',
							'Yellow'	=>	'#eeee22'
						)
					),
					array(
						'name'					=>	'Size',
						'type'					=>	'button',
						'taxonomy'			=>	'pa_size',
						'data'					=>	array(
							'L', 'M', 'S'
						)
					)
				);
				$new_attribute_data = array();
				$old_attribute_taxonomies = wc_get_attribute_taxonomies();
				foreach ( $attribute_data as $attribute_data_single ) {
					$is_attribute_found	=	false;
					foreach ( $old_attribute_taxonomies as $old_attribute_taxonomy ) {
						if ( $attribute_data_single['type'] === $old_attribute_taxonomy->attribute_type ) {
							$is_attribute_found = true;
							break;
						}
					}
					if ( !$is_attribute_found ) {
						array_push( $new_attribute_data, $attribute_data_single );
					}
				}
				foreach ( $new_attribute_data as $attribute_single_args ) {
					$args = array(
						// 'slug'					=> 'my_color',
						'name'					=>	__( $attribute_single_args['name'], 'ibtana-visual-editor' ),
						'type'					=>	$attribute_single_args['type'],
						'orderby'				=>	'menu_order',
						'has_archives'	=>	false,
					);
					$wc_create_attribute	=	wc_create_attribute( $args );
					register_taxonomy( $attribute_single_args['taxonomy'], array( 'product' ), array() );

					if ( !is_wp_error( $wc_create_attribute ) ) {

						if ( $this->isAssoc( $attribute_single_args['data'] ) ) {
							foreach ( $attribute_single_args['data'] as $single_data_key => $single_data ) {
								$wp_insert_term	=	 wp_insert_term( $single_data_key, $attribute_single_args['taxonomy'] );
								if ( !is_wp_error( $wp_insert_term ) ) {
									update_term_meta( $wp_insert_term['term_id'], 'product_attribute_' . $attribute_single_args['type'], $single_data );
								}
							}
						} else {
							foreach ( $attribute_single_args['data'] as $single_data_key => $single_data ) {
								wp_insert_term( $single_data, $attribute_single_args['taxonomy'] );
							}
						}
					}
				}
			}
			// Attribute Creation Code ENDs


			// Product Category Creation
			$categories = get_terms( array(
				'taxonomy'		=>	'product_cat',
				'hide_empty'	=>	false
			) );
			$category_count = count( $categories );
			$cat_ids = array();
			if ( $category_count > 2 ) {
				// Check if our categories exists

				foreach ( $categories as $key => $category_single_obj ) {
					if ( ( 'Ibtana Ecommerce' == $category_single_obj->name ) || ( 'Ecommerce Product' == $category_single_obj->name ) || ( 'Product Addons' == $category_single_obj->name ) ) {
						array_push( $cat_ids, $category_single_obj->term_id );
					}
				}

			} else {
				$new_cats = array(
					array( 'name' => 'Ibtana Ecommerce', 'description' => 'Ibtana Ecommerce Product Addons Category' ),
					array( 'name' => 'Ecommerce Product', 'description' => 'Ibtana Ecommerce Product Addons Category' ),
					array( 'name' => 'Product Addons', 'description' => 'Ibtana Ecommerce Product Addons Category' )
				);
				foreach ( $new_cats as $new_cat ) {
					$cid = wp_insert_term(
						$new_cat['name'], // the term
						'product_cat', // the taxonomy
						array(
							'description'	=>	$new_cat['description']
						)
					);
				}
				if( !is_wp_error( $cid ) ) {
					if ( isset( $cid['term_id'] ) ) {
						array_push( $cat_ids, $cid['term_id'] );
					}
				}
			}
			if ( !empty( $cat_ids ) ) {
				$cat_ids = array_map( 'intval', $cat_ids );
				$cat_ids = array_unique( $cat_ids );
			}
			// Product Category Creation END


		}

		// Fetch the template
		$buildercontent = '';

		$post_slug						=	sanitize_text_field( $_POST['slug'] );
		$post_temp_type				=	sanitize_text_field( $_POST['temp_type'] );
		$post_page_title			=	sanitize_text_field( $_POST['page_title'] );
		$post_is_pro_or_free	=	sanitize_text_field( $_POST['is_pro_or_free'] );

		$json_theme = array(
			'template_slug' 			=>	$post_slug,
			'page_template_type'	=>	isset( $_POST['page_type'] ) ? sanitize_text_field( $_POST['page_type'] ) : 'template'
		);

		if ( $post_is_pro_or_free == 1 ) {
			$ibtana_ecommerce_product_addons_license_key	=	get_option( 'ibtana_ecommerce_product_addons_license_key' );
			if ( $ibtana_ecommerce_product_addons_license_key ) {
				if ( isset( $ibtana_ecommerce_product_addons_license_key['license_key'] ) && isset( $ibtana_ecommerce_product_addons_license_key['license_status'] ) ) {
					if ( ( $ibtana_ecommerce_product_addons_license_key['license_key'] != '' ) && ( $ibtana_ecommerce_product_addons_license_key['license_status'] == 1 ) ) {
						$json_theme['domain']	=	get_home_url();
						$json_theme['key']		=	$ibtana_ecommerce_product_addons_license_key['license_key'];
					}
				}
			}
		}

		$json_args = array(
			'method' => 'POST',
			'headers'     => array(
				'Content-Type'  => 'application/json'
			),
			'body' => json_encode($json_theme),
		);

		$request_data		= wp_remote_post( IBTANA_LICENSE_API_ENDPOINT . 'get_template', $json_args );
		$response_json	= json_decode( wp_remote_retrieve_body( $request_data ) );
		$buildercontent	= $response_json->data;


		if ( !isset( $_POST['page_id'] ) ) {

			// Product Creation
			if ( isset( $_POST['is_variable_product'] ) ) {
				$post_id		=	'';
				$ive_post = array(
					'post_type'			=> 'product',
					'post_title'		=> $post_page_title,
					'post_content'	=> $buildercontent,
					'post_author'		=> 1
				);
				$post_id = wp_insert_post( wp_slash( $ive_post ) );
				wp_set_object_terms( $post_id, 'variable', 'product_type' );

				if ( class_exists( 'WC_Meta_Box_Product_Data' ) && class_exists( 'WC_Product_Factory' ) ) {

					// Attribute Creation in new pattern
					$attribute_taxonomies = wc_get_attribute_taxonomies();
					$attributes_data_to_insert	=	array();
					$index = 0;
					foreach ( $attribute_taxonomies as $key => $attribute_taxonomy ) {

						if ( 'color' != $attribute_taxonomy->attribute_name ) {
							continue;
						}

						$taxonomy_name	=	'pa_' . $attribute_taxonomy->attribute_name;
						$terms_by_taxonomy_name = get_terms( array(
							'taxonomy' 		=> 	$taxonomy_name,
							'hide_empty'	=> false
						) );
						if ( empty( $terms_by_taxonomy_name ) ) {
							continue;
						}

						$attributes_data_to_insert['attribute_names'][]				=	$taxonomy_name;
						$attributes_data_to_insert['attribute_position'][]		=	$index;
						foreach ( $terms_by_taxonomy_name as $term_by_taxonomy_name ) {
							$attributes_data_to_insert['attribute_values'][$index][]			=	$term_by_taxonomy_name->term_id;
						}
						$attributes_data_to_insert['attribute_visibility'][]	=	1;
						$attributes_data_to_insert['attribute_variation'][]		=	1;
						++$index;
						break;
					}

					$attributes   = WC_Meta_Box_Product_Data::prepare_attributes( $attributes_data_to_insert );
					$product_id 	= $post_id;
					$product_type	=	'variable';
					$classname    = WC_Product_Factory::get_product_classname( $product_id, $product_type );
					$product      = new $classname( $product_id );
					$product->set_attributes( $attributes );
					$product->save();
					// Attribute Creation in new pattern ends here

					// new product variation creation code.
					$product    = wc_get_product( $post_id );
					$data_store = $product->get_data_store();
					$data_store->create_all_product_variations( $product, 50 );
					$data_store->sort_all_product_variations( $product->get_id() );


					$product    = wc_get_product( $post_id );
					foreach( $product->get_children() as $variation_id ) {
						// 1. Updating the stock quantity
						update_post_meta( $variation_id, '_stock', 10 );
						// 2. Updating the stock quantity
						update_post_meta( $variation_id, '_stock_status', wc_clean( 'outofstock' ) );
						// 3. Updating post term relationship
						wp_set_post_terms( $variation_id, 'outofstock', 'product_visibility', true );
						// And finally (optionally if needed)

						// Updating active price and regular price
						update_post_meta( $variation_id, '_regular_price', '2' );
						update_post_meta( $variation_id, '_sale_price', "1" );
						update_post_meta( $variation_id, '_price', '1' );
						wc_delete_product_transients( $variation_id ); // Clear/refresh the variation cache
					}
					// Clear/refresh the variable product cache
					wc_delete_product_transients( $post_id );
					// Update the prices for all the variations ends here
				}

			} else {

				// If it is a simple product
				$post_id		=	'';
				$ive_post = array(
					'post_type'			=> 'product',
					'post_title'		=> $post_page_title,
					'post_content'	=> $buildercontent,
					'post_author'		=> 1
				);
				$post_id = wp_insert_post( wp_slash( $ive_post ) );
				update_post_meta( $post_id, '_regular_price', "2" );
				update_post_meta( $post_id, '_sale_price', "1" );
				update_post_meta( $post_id, '_price', "1" );
			}
			// Product Creation ENDs

			// Assign this product in categories
			if ( !empty( $cat_ids ) ) {
				wp_set_object_terms( $post_id, $cat_ids, 'product_cat' );
			} else {
				wp_set_object_terms( $post_id, 0, 'product_cat' );
			}
			// Assign this product in categories ends here

		} else {

			// Product Contents Updation by the given id
			$post_id	=	sanitize_text_field( $_POST['page_id'] );
			wp_update_post( wp_slash( array(
			    'ID'						=>	$post_id,
			    'post_content'	=>	$buildercontent,
			) ) );
		}

		update_post_meta( $post_id, 'iepa_builder', "1" );

		if( isset( $post_id ) ) {
			wp_send_json([
				'home_page_id'	=> $post_id,
				'home_page_url'	=> get_edit_post_link( $post_id, '' )
			]);
		}
		exit;
	}

	public function ibtana_visual_editor_setup_free_demo() {

		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
			exit;
		}

		$post_temp_type	=	sanitize_text_field( $_POST['temp_type'] );

		if ( $post_temp_type	==	'woocommerce' ) {

			$this->ibtana_visual_editor_setup_woocommerce_page();

		} else {
			$buildercontent = '';

			$is_pro_or_free = sanitize_text_field( $_POST['is_pro_or_free'] );
			$json_theme 		= array(
				'template_slug' 			=> sanitize_text_field( $_POST['slug'] ),
				'page_template_type'	=> isset( $_POST['page_type'] ) ? sanitize_text_field( $_POST['page_type'] ) : 'template'
			);

			if ($is_pro_or_free == 1) {
				$json_theme['domain']	=	get_home_url();
				$json_theme['key']		=	get_option('vw_pro_theme_key');
			}

			$json_args = array(
				'method' => 'POST',
				'headers'     => array(
					'Content-Type'  => 'application/json'
				),
				'body' => json_encode($json_theme),
			);

			$request_data		= wp_remote_post( IBTANA_LICENSE_API_ENDPOINT . 'get_template', $json_args );
			$response_json	= json_decode( wp_remote_retrieve_body( $request_data ) );

			$buildercontent	= $response_json->data;

			$home_id = '';
			if ( !isset( $_POST['page_id'] ) ) {
				$page_title = isset( $_POST['page_title'] ) ? sanitize_text_field( $_POST['page_title'] ) : 'Home Page';
				$ive_page = array(
					'post_type'			=> 'page',
					'post_title'		=> $page_title,
					'post_content'	=> $buildercontent,
					'post_status'		=> 'publish',
					'post_author'		=> 1
				);
				$home_id = wp_insert_post( wp_slash( $ive_page ) );
			} else {

				$home_id	=	sanitize_text_field( $_POST['page_id'] );
				wp_update_post( wp_slash( array(
					'ID'						=>	$home_id,
					'post_content'	=>	$buildercontent,
				) ) );
			}



			if( isset( $home_id ) ) {

				if ( isset( $_POST['ive_template_text_domain'] ) ) {
					update_option( 'ive_active_vw_theme_text_domain', sanitize_text_field( $_POST['ive_template_text_domain'] ) );
				}

				wp_send_json([
					'home_page_id'	=> $home_id,
					'home_page_url'	=> get_edit_post_link( $home_id, '' )
				]);
			}
			exit;
		}
	}

	public function ibtana_visual_editor_insert_component() {

		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
			exit;
		}

		$buildercontent = '';


		$post_is_pro_or_free	=	sanitize_text_field( $_POST['is_pro_or_free'] );
		$json_theme 					=	array(
			'component_slug' 			=> sanitize_text_field( $_POST['slug'] ),
		);

		if ( $post_is_pro_or_free == 1 ) {
			$ibtana_ecommerce_product_addons_license_key	=	get_option( 'ibtana_ecommerce_product_addons_license_key' );



			if ( $ibtana_ecommerce_product_addons_license_key ) {
				if ( isset( $ibtana_ecommerce_product_addons_license_key['license_key'] ) && isset( $ibtana_ecommerce_product_addons_license_key['license_status'] ) ) {
					if ( ( $ibtana_ecommerce_product_addons_license_key['license_key'] != '' ) && ( $ibtana_ecommerce_product_addons_license_key['license_status'] == 1 ) ) {
						$json_theme['domain']	=	get_home_url();
						$json_theme['key']		=	$ibtana_ecommerce_product_addons_license_key['license_key'];
					}
				}
			}
		}

		$json_args = array(
			'method' => 'POST',
			'headers'     => array(
				'Content-Type'  => 'application/json'
			),
			'body' => json_encode( $json_theme ),
		);

		$request_data		= wp_remote_post( IBTANA_LICENSE_API_ENDPOINT . 'ibtana_client_get_component', $json_args );
		$response_json	= json_decode( wp_remote_retrieve_body( $request_data ) );

		$buildercontent	= $response_json->data;

		$home_id	=	sanitize_text_field( $_POST['page_id'] );

		$post = get_post( $home_id );
		$old_content = $post->post_content;


		wp_update_post( wp_slash( array(
			'ID'						=>	$home_id,
			'post_content'	=>	$old_content . $buildercontent,
		) ) );


		if( isset( $home_id ) ) {
			wp_send_json([
				'home_page_id'	=> $home_id,
				'home_page_url'	=> get_edit_post_link( $home_id, '' )
			]);
		}
		exit;
	}

	function ibtana_visual_editor_inner_template_grid() {
		?>
		<div class="ibtana-wizard-four-step-content ibtana-free-templates">
			<div class="ive-social-theme-search">
				<input class="themesearchinput ive-admin-wizard-search" type="text" placeholder="<?php esc_attr_e( 'Search Template Here', 'ibtana-visual-editor' ); ?>">
			</div>
			<div class="ive-ibtana-cards-box">
				<div class="ive-wz-spinner-wrap" style="display: none;">
					<div class="ive-lds-dual-ring"></div>
				</div>
				<div class="ive-social-theme-back">
					<button class="button ive-go-back-special">&#8592; back</button>
				</div>
				<div class="ive-o-product-main-row">
					<div class="ive-o-product-col-1">
						<ul class="ive-ibtaba-wizard-inner-sub-cats"></ul>
					</div>
					<div class="ive-o-product-col-2">
						<div class="ive-ibtana-wizard-product-row">

						</div>
					</div>
				</div>

			</div>
			<div class="ive-template-load-more">
				<a href="javascript:void(0)" class="button button-primary"><?php esc_html_e('Load More...','ibtana-visual-editor'); ?></a>
			</div>
		</div>
	<?php }

	public static function ibtana_visual_editor_banner_head() {
		?>
		<div class="ive-get-started-head">
			<div class="ive-get-started-container">
				<div class="ive-get-started-row">
					<div class="ive-get-started-col-6">
						<img src="<?php echo esc_url(plugin_dir_url(__FILE__).'dist/images/admin-wizard/adminIcon.png'); ?>" class="ive-theme-icon">
					</div>
					<div class="ive-get-started-col-6 ive-get-started-head-text">
						<p class="ivera-theme-version">
							<strong><?php
								esc_html_e( 'v' . IVE_VER, 'ibtana-visual-editor' );
							?></strong>
						</p>
						<p><?php esc_html_e('Take Gutenberg to The Next Level! -','ibtana-visual-editor'); ?>
							<a href="<?php echo esc_url( admin_url() . 'admin.php?page=ibtana-visual-editor-templates' ) ?>">
								<?php esc_html_e( 'View Demos', 'ibtana-visual-editor' ); ?>
							</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

 	function ibtana_visual_editor_settings_page($hook) {
		if($hook=="same_admin_page"){
			return;
		}
		wp_enqueue_script( 'ibtana-admin-wizard-script' );
		$this->has_read_write_perms = IVE_Helper::has_read_write_permissions();
		$this->allow_file_generation = IVE_Helper::allow_file_generation();
		?>
		<div id="ive-get-started-page">

			<?php self::ibtana_visual_editor_banner_head(); ?>

			<div class="ive-get-started-container ive-get-started-main-container">
				<div class="">
					<div class="ive-get-started-sidebar-theme">
						<h4><span class="dashicons dashicons-admin-customizer"></span><?php esc_html_e('WP Theme Bundle','ibtana-visual-editor'); ?></h4>
						<img src="<?php echo esc_url(plugin_dir_url(__FILE__).'dist/images/welcome-screen.jpg'); ?>">
						<p>
							<?php
							esc_html_e(
								'WordPress Theme bundle is our best offer wherein we offer all our premium themes in a single package at a very good price. We at VWThemes, strongly believe in serving our customers.',
								'ibtana-visual-editor'
							);
							?>
						</p>
						<a href="https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true" target="_blank" class="ive-get-started-btn">
							<?php esc_html_e('Buy Now','ibtana-visual-editor'); ?>
						</a>


					</div>
					<div class="ive-get-started-about">
						<h3><span class="dashicons dashicons-megaphone"></span><?php esc_html_e('Welcome to the Ibtana WordPress Website Builder','ibtana-visual-editor'); ?></h3>
						<p>
							<?php esc_html_e( 'Thank you for choosing Ibtana WordPress Website Builder - the most advanced kit of gutenberg blocks to build a stunning landing page and internal page attractive than ever before!','ibtana-visual-editor' ); ?>
						</p>
						<p>
							<strong><?php esc_html_e('Ready-to-use Full Demo Websites - ','ibtana-visual-editor'); ?></strong><?php esc_html_e('Get 3+ of  professionally designed pre-built FREE starter templates built using Gutenberg, Ibtana WordPress Website Builder and the VW Themes. These can be imported in just a few clicks. Tweak them easily and build awesome websites in minutes!','ibtana-visual-editor'); ?>
						</p>
						<a href="<?php echo esc_url(admin_url().'admin.php?page=ibtana-visual-editor-templates') ?>">
							<?php esc_html_e('Know More »','ibtana-visual-editor'); ?>
						</a>
						<a class="ive-get-started-btn ive-rate-us-btn" target="_blank" rel="noopener" href="https://wordpress.org/support/plugin/ibtana-visual-editor/reviews/?filter=5#new-post">
							<?php _e( 'Rate Us', 'ibtana-visual-editor' ); ?> &#9733;&#9733;&#9733;&#9733;&#9733;
						</a>
					</div>
					<div class="ive-get-started-about-blocks">
						<h3>
							<span class="dashicons dashicons-smiley"></span><?php esc_html_e('How to use Ibtana WordPress Website Builder','ibtana-visual-editor'); ?>
						</h3>
						<p>
							<?php esc_html_e('Ibtana WordPress Website Builder comes with 15+ blocks through which you can easily create your templates using HTML and WordPress knowledge. If you want you can also try our themes package with pre-build landing and internal pages. We have different category light weight themes targeted with category wise landing and internal pages, installed through setup wizard.','ibtana-visual-editor'); ?>
						</p>
						<p>
							<?php esc_html_e('Wish to see some real design implementations with these blocks?','ibtana-visual-editor'); ?>
						</p>
						<a href="<?php echo esc_url(admin_url().'admin.php?page=ibtana-visual-editor-templates') ?>">
							<?php esc_html_e('See Demos »','ibtana-visual-editor'); ?>
						</a>

					</div>
					<div class="ive-get-started-sidebar-css-gen">
						<h4><span class="dashicons dashicons-admin-page"></span><?php esc_html_e('CSS File Generation','ibtana-visual-editor'); ?></h4>
						<p>
							<?php esc_html_e('Enabling this option will generate CSS files for Ibtana WordPress Website Builder styling instead of loading the CSS inline on page.','ibtana-visual-editor'); ?>
						</p>
						<?php
						$button_disabled = '';
						if ( 'disabled' === $this->allow_file_generation && true === $this->has_read_write_perms ) {
							$val                    = 'enabled';
							$file_generation_string = __( 'Enable File Generation', 'ibtana-visual-editor' );
						} elseif ( 'disabled' === $this->allow_file_generation && false === $this->has_read_write_perms ) {

							$val                    = 'disabled';
							$file_generation_string = __( 'Inadequate File Permission', 'ibtana-visual-editor' );
							$button_disabled        = 'disabled';

						} else {
							$val                    = 'disabled';
							$file_generation_string = __( 'Disable File Generation', 'ibtana-visual-editor' );
						}
						?>
						<button class="ive-get-started-btn ive-file-generation" id="ive_file_generation" data-value="<?php echo esc_attr( $val ); ?>" <?php echo esc_attr( $button_disabled ); ?> >
							<?php echo esc_html( $file_generation_string ); ?>
						</button>
					</div>
					<div class="ive-get-started-blocks-accordion">
						<button class="ive-block-accordion-btn">
							<span class="dashicons dashicons-screenoptions ive-accordion-title-icon"></span>
							<?php _e( 'Ibtana Blocks', 'ibtana-visual-editor' ); ?>
							<span class="dashicons dashicons-arrow-down ive-accordion-icon"></span>
						</button>
						<div class="panel ive-get-started-block-activation">
							<?php
								$ive_block_json	= file_get_contents(plugin_dir_url(__FILE__).'ive-blocks.json');
								$ive_blocks 		= json_decode($ive_block_json, true);
								foreach ($ive_blocks['ive_blocks'] as $key => $ive_bock) {

									?>
									<div class="ive-get-started-row">
										<div class="ive-get-started-col-7 ive-get-started-block-title">
											<b><?php esc_html_e($ive_bock['ive_block_title'],'ibtana-visual-editor'); ?></b>
											<p>
												<?php esc_html_e($ive_bock['ive_block_text'],'ibtana-visual-editor'); ?>
											</p>
										</div>
										<span class="ibtana-block-demo-tooltip">
											<a target="_blank" href="<?php echo esc_url( $ive_bock['ive_block_demo'] ) ?>">
												<?php _e( 'View Demo', 'ibtana-visual-editor' ); ?>
											</a>
										</span>
									</div>
									<?php
								}
								?>
						</div>
						<button class="ive-block-accordion-btn">
							<span class="dashicons dashicons-screenoptions ive-accordion-title-icon"></span>
							<?php _e( 'Woocommerce Blocks', 'ibtana-visual-editor' ); ?>
							<span class="dashicons dashicons-arrow-down ive-accordion-icon"></span>
						</button>
						<div class="panel ive-get-started-block-activation">
							<?php
							$ive_block_json = file_get_contents(plugin_dir_url(__FILE__).'ive-blocks.json');
							$ive_blocks = json_decode($ive_block_json, true);
							foreach ( $ive_blocks['iepa_blocks'] as $key => $ive_bock ) {

								?>
								<div class="ive-get-started-row">
									<div class="ive-get-started-col-7 ive-get-started-block-title">
										<b>
											<?php esc_html_e($ive_bock['iepa_block_title'],'ibtana-visual-editor'); ?>
										</b>
										<p>
											<?php esc_html_e($ive_bock['iepa_block_text'],'ibtana-visual-editor'); ?>
										</p>
									</div>
									<span class="ibtana-block-demo-tooltip">
										<a target="_blank" href="<?php echo esc_url($ive_bock['ive_block_demo']) ?>">
											<?php _e( 'View Demo', 'ibtana-visual-editor' ); ?>
										</a>
									</span>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function ibtana_visual_editor_woocommerce_templates() {
		?>
		<div class="ive-wizard-five-step-content">
			<h3 class="ive-coming-soon">
				<?php esc_html_e( 'Coming Soon', 'ibtana-visual-editor' ); ?>
			</h3>
		</div>
		<?php
	}


	public function ibtana_visual_editor_activate_plugin() {
		if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
			exit;
		}
		wp_cache_flush();
		$plugin = sanitize_text_field( $_POST['ive-addon-slug'] );
		$plugin = plugin_basename( trim( $plugin ) );
		$activate_plugin = activate_plugin( $plugin );
    return true;
	}



	/**
	 * create sub main menu page editor  of ibtana plugin
	 */

	public function ibtana_visual_editor_templates_page() {
		?>
		<div class="wrap ive-ibtana-template-page">
			<h1>
				<?php esc_html_e( 'Ibtana Visual Editor','ibtana-visual-editor' ); ?>
			</h1>

			<div class="ive-il-tab">
			  <button class="il-tablinks active">
					<?php esc_html_e( 'Premium Themes', 'ibtana-visual-editor' ); ?>
				</button>
			</div>


			<div class="il-tabcontents">

				<!-- Premium Themes -->
				<div class="ive-il-tabcontent">
					<div class="ive-il-title-search">
						<h3><?php esc_html_e('Themes','ibtana-visual-editor'); ?></h3>
	      		<div class="ive_alignright">
							<span id="ive-reload--modal--contents--admin" class="dashicons dashicons-update-alt"></span>
							<input type="text" class="search-text" placeholder="Search for names.." >
		        </div>
		        <hr />
					</div>
		      <div id="premium-templates">

		        <div class="ive-o-product-main-row">
		          <div class="ive-o-product-col-1">
		            <ul class="sub-category-wrapper">

		            </ul>
		          </div>
		          <div class="ive-o-product-col-2">
		            <div class="ive-o-product-row">

		            </div>
		          </div>
		        </div>
		      </div>

				</div>
				<!-- Premium Themes END -->
			</div>

		  <div class="ive-ibtana--modal--loader--admin">
		    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
		    <circle cx="50" cy="50" fill="none" stroke="#44a745" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">
		      <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
		    </circle>
		    </svg>
		  </div>

		</div>
		<script type="text/javascript">
			window.addEventListener('load', function() {

		    // On click tab
		    jQuery('.il-tablinks').on('click', function() {
				  jQuery('.il-tablinks').removeClass('active');
				  jQuery(this).addClass('active');
				  jQuery('.ive-il-tabcontent').hide();
				  jQuery('.ive-il-tabcontent').eq(jQuery(this).index()).show();
				});
		    // On click tab END


		    // On click subcategory
		    jQuery('#premium-templates').on('click', '.sub-cat-button', function() {
		      jQuery('.sub-category-wrapper .sub-cat-button').removeClass('active');
		      jQuery(this).addClass("active");
		        var data_ids = jQuery(this).attr('data-ids');
		        var id_arr = data_ids.split(',');
		        jQuery('#premium-templates .ive-o-product-row .ive-o-products-col[data-id]').hide();
		        for (var i = 0; i < id_arr.length; i++) {
		          var single_id = id_arr[i];
		          jQuery('#premium-templates .ive-o-product-row .ive-o-products-col[data-id="'+single_id+'"]').show();
		        }
		    });
		    // On click subcategory END
		    // Search text
		    jQuery('.search-text').on('input', function() {
		      var search_keyword = jQuery(this).val().toLowerCase();
		      var active_sub_cat = jQuery('#premium-templates .sub-cat-button.active');
		      var visible_wrapper = jQuery('#premium-templates .ive-o-product-row');
		      if (active_sub_cat.length != 0) {
		        var sub_cat_pro_ids = active_sub_cat.attr('data-ids');
		        var sub_cat_arr_ids = sub_cat_pro_ids.split(',');
		        jQuery('#premium-templates [data-id]').hide();
		        for (var i = 0; i < sub_cat_arr_ids.length; i++) {
		          var sub_cat_pro_id = sub_cat_arr_ids[i];
		          var pro_card = jQuery('#premium-templates [data-id='+sub_cat_pro_id+']');
		          var pro_card_text = pro_card.find('h3').text().toLowerCase();
		          if (pro_card_text.indexOf(search_keyword) !== -1) {
		            pro_card.show();
		          }
		        }
		      } else {
		        visible_wrapper.find('.ive-o-products-col').hide();
		        var pro_cards = visible_wrapper.find('.ive-o-products-col');
		        jQuery.each(pro_cards, function(key, pro_card) {
		          pro_card_text = jQuery(pro_card).find('h3').text().toLowerCase();
		          if (pro_card_text.indexOf(search_keyword) !== -1) {
		            jQuery(pro_card).show();
		          }
		        });
		      }
		    });
		    // Search text END

		    ibtana_visual_editor_get_premium_themes();
		    jQuery('#ive-reload--modal--contents--admin').on('click', function() {
				jQuery( ".search-text" ).val('');
		      	ibtana_visual_editor_get_premium_themes();
		    });
		    function ibtana_visual_editor_get_premium_themes() {
		      var ibtana_license_api_endpoint = '<?php echo IBTANA_LICENSE_API_ENDPOINT; ?>';
		      jQuery('.ive-ibtana--modal--loader--admin').show()
		      jQuery.ajax({
		        method: "POST",
		        url: ibtana_license_api_endpoint + "get_modal_contents",
		        data: JSON.stringify({
							"admin_user_ibtana_license_key": 	'<?php echo get_option('vw_pro_theme_key') ?>',
							"domain": 												'<?php echo site_url(); ?>'
						}),
		        dataType: 'json',
		        contentType: 'application/json',
		      }).done(function( data ) {
		        jQuery('.ive-ibtana--modal--loader--admin').hide()
		        var premium_data = data.data.products;
		        var premium_card_content = ``;
		        for (var i = 0; i < premium_data.length; i++) {
		          var premium_product = premium_data[i];
		          premium_card_content += `<div class="ive-o-products-col" data-id="`+premium_product.id+`">
		            <div class="ive-o-products-image">
		              <img src="`+premium_product.image+`">
		            </div>
		            <h3>`+premium_product.title+`</h3>
		            <a href="`+premium_product.permalink+`" target="_blank">Buy Now</a>
		            <a href="`+premium_product.demo_url+`" target="_blank">View Demo</a>
		          </div>`;
		        }
		        jQuery('#premium-templates .ive-o-product-row').empty();
		        jQuery('#premium-templates .ive-o-product-row').append(premium_card_content);


		        var sub_category_array = data.data.sub;
		        var sub_cat_html = ``;
		        for (var i = 0; i < sub_category_array.length; i++) {
		          var sub_category = sub_category_array[i];
		          sub_cat_html += `<li data-ids="`+sub_category.product_ids+`" class="sub-cat-button">
		                `+sub_category.name+`<span class="badge badge-info">`+sub_category.product_ids.length+`</span>
		              </li>`;
		        }
		        jQuery('.ive-o-product-col-1 ul').empty();
		        jQuery('.ive-o-product-col-1 ul').append(sub_cat_html);


		        // jQuery('#premium-templates .sub-cat-button:first').trigger('click');
		      });
		    }

			}, false);
		</script>
		<?php
	}


	/**
   * Register admin css for menu
   */
	function ibtana_visual_editor_load_custom_wp_admin_style( $hook ) {

		// ------------ Plugin Activation ---------

		$ibtana_key			=	'';
		$ibtana_get_key	=	get_option( 'vw_pro_theme_key' );
		if( $ibtana_get_key == NULL ) {
			$ibtana_key	=	"";
		} else {
			$ibtana_key = get_option( 'vw_pro_theme_key' );
		}

		wp_enqueue_script( 'updates' );
		// --------- wizard Script --------

		wp_register_script(
			'ibtana-admin-wizard-script',
			plugin_dir_url( __FILE__ ) . 'dist/ibtana-wizard-script.js',
			array( 'jquery' )
		);
		$ive_add_on_license_info = apply_filters( 'ive_add_on_license_info', [] );


		$theme_text_domain = wp_get_theme()->get( 'TextDomain' );
		if ( is_child_theme() ) {
			$theme_text_domain = wp_get_theme()->get( 'Template' );
		}

		wp_localize_script(
			'ibtana-admin-wizard-script',
			'ive_whizzie_params',
			array(
				'ajaxurl' 										=>	admin_url( 'admin-ajax.php' ),
				'adminUrl'										=>	admin_url(),
				'wpnonce' 										=>	wp_create_nonce( 'ive_whizzie_nonce' ),
				'verify_text'									=>	esc_html( 'verifying', 'ibtana-visual-editor' ),
				'IBTANA_LICENSE_API_ENDPOINT' =>	IBTANA_LICENSE_API_ENDPOINT,
				'ive_license_key' 						=>	get_option( 'vw_pro_theme_key' ),
				'ive_add_on_keys'							=>	$ive_add_on_license_info,
				'ive_domain_name' 						=>	get_home_url(),
				'theme_text_domain' 					=>	$theme_text_domain,
				'custom_text_domain' 					=>	defined( 'CUSTOM_TEXT_DOMAIN' ) ? CUSTOM_TEXT_DOMAIN : '',
				'is_woocommerce_available'		=>	class_exists( 'woocommerce' ) ? true : false
			)
		);
		// ------------ Plugin Activation End ---------

		// Admin Menu style
		wp_register_style(
			'ive-admin-menu',
			plugin_dir_url( __FILE__ ) . 'dist/css/ibtana-menu-css/ive-admin-menu.css',
			false,
			IVE_VER
		);
		wp_enqueue_style( 'ive-admin-menu' );


		if ( 'ibtana-settings_page_ibtana-visual-editor-addons' == $hook ) {
			wp_register_script(
				'ive-ibtana-addons-script',
				IBTANA_PLUGIN_URI . 'dist/js/addons.js',
				array( 'jquery' ),
				IVE_VER
			);
			wp_localize_script(
				'ive-ibtana-addons-script',
				'ibtana_addons',
				array(
					'admin_ajax'	=>	admin_url( 'admin-ajax.php' ),
					'wpnonce'			=>	wp_create_nonce( 'ibtana_addons_page' )
				)
			);

			wp_enqueue_script( 'ive-ibtana-addons-script' );
		}

	}

}
new Ibtana_Visual_Editor_Menu_Class;



class Ibtana_Visual_Editor_Menu_Creator extends Ibtana_Visual_Editor_Menu_Class {

	public $defaults = array(
		'page_type' => 'menu_page',
		'page_title' => '',
		'menu_title' => '',
		'capability' => '',
		'menu_slug' => '',
		'icon_url' => '',
		'position' => '',
		'parent_slug' => '',
		'priority' => 10,
		'network_page' => false,
		'page_functions' => false
	);
	public $args;
	public $ivehook;


	/* Constructor method for the class. */
	function __construct( $args ) {

		/* Global that will hold all the arguments for all the menu pages */
		global $ibtana_visual_editor_pages;


		/* Merge the input arguments and the defaults. */
		$this->args = wp_parse_args( $args, $this->defaults );

		/* Add the settings for this page to the global object */
		$ibtana_visual_editor_pages[$this->args['page_title']] = $this->args;

		if( !$this->args['network_page'] ) {
			/* Hook the page function to 'admin_menu'. */
			if($this->args['page_functions']) {
				add_action( 'admin_menu', array( &$this, 'ibtana_visual_editor_page_init' ), $this->args['priority'] );
			}
		} else {
			/* Hook the page function to 'admin_menu'. */
			add_action( 'network_admin_menu', array( &$this, 'ibtana_visual_editor_page_init' ), $this->args['priority'] );
		}
	}

	/**
	 * Function that creates the admin page
	 */
	function ibtana_visual_editor_page_init() {
		global $ibtana_visual_editor_pages_ivehooks;

    /* don't add the page at all if the user doesn't meet the capabilities */
    if( !empty( $this->args['capability'] ) ){
        if( !current_user_can( $this->args['capability'] ) )
            return;
    }

		/* Create the page using either add_menu_page or add_submenu_page functions depending on the 'page_type' parameter. */
		if( $this->args['page_type'] == 'menu_page' ) {
			$this->ivehook = add_menu_page(
				__( $this->args['page_title'], 'ibtana-visual-editor' ),
				__( $this->args['menu_title'], 'ibtana-visual-editor' ),
				$this->args['capability'],
				$this->args['menu_slug'],
				array(
					$this,
					$this->args['page_functions']
				),
				$this->args['icon_url'],
				$this->args['position']
			);

			$ibtana_visual_editor_pages_ivehooks[$this->args['menu_slug']] = $this->ivehook;
		}

		else if( $this->args['page_type'] == 'submenu_page' ) {
			$this->ivehook = add_submenu_page(
				$this->args['parent_slug'],
				__( $this->args['page_title'], 'ibtana-visual-editor' ),
				__( $this->args['menu_title'], 'ibtana-visual-editor' ),
				$this->args['capability'],
				$this->args['menu_slug'],
				array($this,$this->args['page_functions'])
			);

			$ibtana_visual_editor_pages_ivehooks[$this->args['menu_slug']] = $this->ivehook;
		}else if( $this->args['page_type'] == 'same_admin_page' ) {
			$this->ivehook = add_submenu_page(
				$this->args['parent_slug'],
				__( $this->args['page_title'], 'ibtana-visual-editor' ),
				__( $this->args['menu_title'], 'ibtana-visual-editor' ),
				$this->args['capability'],
				$this->args['menu_slug'],
				$this->ibtana_visual_editor_settings_page( "same_admin_page" )
			);

			$ibtana_visual_editor_pages_ivehooks[$this->args['menu_slug']] = $this->ivehook;
		}

		do_action( 'ibtana_visual_editor_page_creator_after_init', $this->ivehook );

		/* Create a hook for adding meta boxes. */
		add_action( "load-{$this->ivehook}", array( &$this, 'ibtana_visual_editor_settings_page_add_meta_boxes' ) );
		/* Load the JavaScript needed for the screen. */
		add_action( 'admin_enqueue_scripts', array( &$this, 'ibtana_visual_editor_page_enqueue_scripts' ) );
		add_action( "admin_head-{$this->ivehook}", array( &$this, 'ibtana_visual_editor_page_load_scripts' ) );
	}

	/**
	 * Do action 'add_meta_boxes'. This hook isn't executed by default on a admin page so we have to add it.
	 */
	function ibtana_visual_editor_settings_page_add_meta_boxes() {
		do_action( 'ibtana_visual_editor_page_creator_before_meta_boxes', $this->ivehook );
		do_action( 'add_meta_boxes', $this->ivehook, 0 );
		do_action( 'ibtana_visual_editor_page_creator_after_meta_boxes', $this->ivehook );
	}


	/**
	 * Loads the JavaScript files required for managing the meta boxes on the theme settings
	 * page, which allows users to arrange the boxes to their liking.
	 *
	 * @global string $bareskin_settings_page. The global setting page (returned by add_theme_page in function
	 * bareskin_settings_page_init ).
	 * @since 1.0.0
	 * @param string $hook The current page being viewed.
	 */
	function ibtana_visual_editor_page_enqueue_scripts( $hook ) {
		if ( $hook == $this->ivehook ) {
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );
		}
	}


	/**
	 * Loads the JavaScript required for toggling the meta boxes on the theme settings page.
	 *
	 * @global string $bareskin_settings_page. The global setting page (returned by add_theme_page in function
	 * bareskin_settings_page_init ).
	 * @since 1.0.0
	 */
	function ibtana_visual_editor_page_load_scripts() {
		?>
		<script type="text/javascript">

		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $this->ivehook; ?>' );
		});

		</script>
		<?php
	}


	public function ibtana_visual_editor_is_pro_theme_activated() {
		if ( defined( 'CUSTOM_TEXT_DOMAIN' ) ) {
			$ive_current_theme		=	wp_get_theme();
			$ive_theme_title			=	$ive_current_theme->get( 'Name' );
			$ive_theme_name				=	strtolower( preg_replace( '#[^a-zA-Z]#', '', $ive_theme_title ) );
			$page_slug						=	$ive_theme_name . '-wizard';
			$ive_theme_wizard_url	=	esc_url( admin_url( 'admin.php?page=' . $page_slug ) );
			return array(
				'ive_theme_title'				=>	$ive_theme_title,
				'ive_theme_wizard_url'	=>	$ive_theme_wizard_url
			);
		}
		return NULL;
	}


	public function ibtana_visual_editor_ive_templates_page() {
		wp_enqueue_script( 'ibtana-admin-wizard-script' );
		// check if the vw premium theme is activated.
		$ive_is_pro_theme_activated	=	$this->ibtana_visual_editor_is_pro_theme_activated();

		$ive_plugin_data= '';
 		$ive_plugin_version= '';
		?>
		<div class="ive-plugin-admin-page">

			<div class="ive-theme-page-header">
				<div class="ive-container ive-flex ive-templates-header-container">
					<div class="ive-theme-title">
						<img src="<?php echo esc_url(plugin_dir_url(__FILE__).'dist/images/admin-wizard/adminIcon.png'); ?>" class="ive-theme-icon">
					</div>
					<div class="ive-top-links">

						<p class="ivera-theme-version">
							<strong><?php esc_html_e( 'v' . IVE_VER, 'ibtana-visual-editor' ); ?></strong>
						</p>

						<p>
							<img src="<?php echo esc_url(plugin_dir_url(__FILE__).'dist/images/admin-wizard/lightning.svg'); ?>" class="ivera-lightning-icon"> <?php esc_html_e('Lightning Fast &amp; Fully Customizable WordPress theme!','ibtana-visual-editor'); ?>
						</p>


					</div>
				</div>
			</div>

			<?php if ( $ive_is_pro_theme_activated ): ?>
			<div class="notice ive-wizard-notice is-dismissible">
				<div class="ive-theme_box">
					<h4>
					<?php esc_html_e( 'Look\'s like you\'ve installed our ' . $ive_is_pro_theme_activated['ive_theme_title'] . ' theme. Click Get Started to run the setup wizard.', 'ibtana-visual-editor' ); ?>
					</h4>
				</div>
				<div class="ive-notice_button">
					<a class="button button-primary button-hero" target="_blank" href="<?php echo esc_url($ive_is_pro_theme_activated['ive_theme_wizard_url']); ?>">
						<?php esc_html_e( 'Get Started', 'ibtana-visual-editor' ); ?>
					</a>
				</div>
			</div>
			<?php endif; ?>

			<!-- Main Tabs START -->
	 		<div class="ive-ibtana-wizard-button-wrapper">
				<div class="button-wrap">
					<a class="ibtana-free-template-button button button-primary active" data-callback="do_next_step" data-step="ive-wizard-first-step" data-template-type="wordpress">
						<span class="dashicons dashicons-format-image"></span>
						<?php esc_html_e( 'Templates', 'ibtana-visual-editor' ); ?>
					</a>
				</div>
			</div>
			<!-- Main Tabs END -->


	 		<div id="ive-admin-main-tab-content-wrap">
	 			<!-- Wizard Content -->
	 			<div class="ive-tab-content-box active ive-admin-main-tab-content1">
	 				<div class="wrap">
						<?php echo '<div class="ive-whizzie-wrap">';
							// The wizard is a list with only one item visible at a time
							$steps = $this->ibtana_visual_editor_admin_main_tab_step();
							echo '<ul class="ive-wizard-content-menu">';
							foreach( $steps as $step ) {
								$class = 'step step-' . esc_attr( $step['id'] );
								echo '<li data-step="' . esc_attr( $step['id'] ) . '" class="' . esc_attr( $class ) . '" >';
									if( isset( $content['title'] ) ) {
										printf( '<h3 class="ive-wizard-main-title">%s</h3>',
											esc_html( $step['title'] )
											);
									}
									// $content is split into summary and detail
									$content = call_user_func( array( $this, $step['view'] ) );
									if( isset( $content['summary'] ) ) {
										printf(
											'<div class="summary">%s</div>',
											wp_kses_post( $content['summary'] )
										);
									}
									if( isset( $content['detail'] ) ) {
										// Add a link to see more detail
										printf( '<div class="wz-require-plugins">');
										printf(
											'<div class="detail">%s</div>',
											$content['detail'] // Need to escape this
										);
										printf('</div>');
									}
								echo '</li>';
							}
							echo '</ul>';
							?>
						<?php echo '</div>'; ?>

					</div>
	 			</div>
	 		</div>


			<div class="ive-plugin-popup">
				<div class="ive-admin-modal">
					<button class="ive-close-button">×</button>
					<div class="ive-demo-step-container">

						<div class="ive-current-step">

							<div class="ive-demo-child ive-demo-step ive-demo-step-0 active">
								<h2><?php _e( 'Install Base Theme', 'ibtana-visual-editor' ); ?></h2>
								<p><?php _e( 'We strongly recommend to install the base theme.', 'ibtana-visual-editor' ); ?></p>
								<div class="ive-checkbox-container">
									<?php _e( 'Install Base Theme', 'ibtana-visual-editor' ); ?>
									<span class="ive-checkbox active">
										<svg width="10" height="8" viewBox="0 0 11.2 9.1">
											<polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
										</svg>
									</span>
								</div>
							</div>

							<div class="ive-demo-plugins ive-demo-step ive-demo-step-1">
								<h2><?php _e( 'Install & Activate Plugins', 'ibtana-visual-editor' ); ?></h2>
								<p>
									<?php
									_e(
										'The following plugins are required for this template in order to work properly. Ignore if already installed.',
										'ibtana-visual-editor'
									);
									?>
								</p>
								<div class="ive-checkbox-container activated">
									<?php _e( 'Elementor', 'ibtana-visual-editor' ); ?>
									<span class="ive-checkbox active">
										<svg width="10" height="8" viewBox="0 0 11.2 9.1">
											<polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
										</svg>
									</span>
								</div>
								<div class="ive-checkbox-container">
									<?php _e( 'Gutenberg', 'ibtana-visual-editor' ); ?>
									<span class="ive-checkbox active">
										<svg width="10" height="8" viewBox="0 0 11.2 9.1">
											<polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
										</svg>
									</span>
								</div>
							</div>

							<div class="ive-demo-template ive-demo-step ive-demo-step-2">
								<h2><?php _e( 'Import Content', 'ibtana-visual-editor' ); ?></h2>
								<p><?php _e( 'This will import the template.', 'ibtana-visual-editor' ); ?></p>
							</div>

							<div class="ive-demo-install ive-demo-step ive-demo-step-3">
								<h2><?php _e( 'Installing...', 'ibtana-visual-editor' ); ?></h2>
								<p>
									<?php
									_e(
										"Please be patient and don't refresh this page, the import process may take a while, this also depends on your server.",
										'ibtana-visual-editor'
									);
									?>
								</p>
								<div class="ive-progress-info">
									<?php _e( 'Required plugins', 'ibtana-visual-editor' ); ?><span>10%</span>
								</div>
								<div class="ive-installer-progress"><div></div></div>
							</div>

						</div>

						<div class="ive-demo-step-controls">
							<button class="ive-demo-btn ive-demo-back-btn"><?php _e( 'Back', 'ibtana-visual-editor' ); ?></button>
							<ul class="ive-steps-pills">
								<li class="active"><?php _e( '1', 'ibtana-visual-editor' ); ?></li>
								<li class=""><?php _e( '2', 'ibtana-visual-editor' ); ?></li>
								<li class=""><?php _e( '3', 'ibtana-visual-editor' ); ?></li>
							</ul>
							<button class="ive-demo-btn ive-demo-main-btn"><?php _e( 'Next', 'ibtana-visual-editor' ); ?></button>
						</div>

					</div>
				</div>
			</div>


		</div>
		<?php
	}


	public function ibtana_visual_editor_general_settings_page() {

		wp_enqueue_style( 'ive-admin-codemirror-css', plugin_dir_url( __FILE__ ) . 'dist/assets/codemirror.min.css' );
		wp_enqueue_script( 'ive-admin-codemirror-js', plugin_dir_url( __FILE__ ) . 'dist/assets/codemirror.min.js' );
		wp_enqueue_script( 'ibtana-admin-wizard-script' );

		$iepa_key = get_option( str_replace( '-', '_', 'ibtana-ecommerce-product-addons' ) . '_license_key' );
	  $is_iepa_activated = false;
	  if ( $iepa_key ) {
	    if ( isset( $iepa_key['license_key'] ) && isset( $iepa_key['license_status'] ) ) {
	      if ( ( $iepa_key['license_key'] != '' ) && ( $iepa_key['license_status'] == 1 ) ) {
	        $is_iepa_activated = true;
	      }
	    }
	  }
		$ive_general_settings = get_option( 'ive_general_settings' );
		?>


		<?php self::ibtana_visual_editor_banner_head(); ?>


		<div class="ive-get-started-container ive-get-started-main-container">

			<div class="ive-get-started-sidebar-css-gen">
				<h4>
					<span class="dashicons dashicons-location-alt"></span>
					<?php esc_html_e( 'Google API Key', 'ibtana-visual-editor' ); ?>
				</h4>
				<p>
					<?php esc_html_e( 'A Google API key is required to use the Map block without any warning.', 'ibtana-visual-editor' ); ?>
				</p>
				<span class="ive-google_api_key">
					<input type="text" name="google_api_key" id="google_api_key" value="<?php echo isset( $ive_general_settings['google_api_key'] ) ? $ive_general_settings['google_api_key'] : '' ?>">
					<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">
						<?php esc_html_e( 'How to create a Google API Key', 'ibtana-visual-editor' ); ?>
					</a>
				</span>
			</div>

			<div class="ive-get-started-sidebar-css-gen">
				<h4>
					<span class="dashicons dashicons-editor-code"></span>
					<?php esc_html_e( 'Custom CSS', 'ibtana-visual-editor' ); ?>
				</h4>
				<?php if ( !$is_iepa_activated ): ?>
					<p>
						<?php esc_html_e( 'You need to activate your license to use this feature. To Enable This Feature', 'ibtana-visual-editor' ); ?>
						<a target="_blank" href="<?php echo admin_url( 'admin.php?page=ibtana-visual-editor-addons' ); ?>">
							<?php esc_html_e( 'Upgrade Pro', 'ibtana-visual-editor' ); ?>
						</a>
					</p>
				<?php endif; ?>
				<textarea id="ive-custom-css-code"><?php echo isset( $ive_general_settings['ive_custom_css'] ) ? $ive_general_settings['ive_custom_css'] : ''; ?></textarea>
			</div>

			<div class="ive-get-started-sidebar-css-gen">
				<h4>
					<span class="dashicons dashicons-editor-code"></span>
					<?php esc_html_e( 'Custom JS', 'ibtana-visual-editor' ); ?>
				</h4>
				<?php if ( !$is_iepa_activated ): ?>
					<p>
						<?php esc_html_e( 'You need to activate your license to use this feature. To Enable This Feature', 'ibtana-visual-editor' ); ?>
						<a target="_blank" href="<?php echo admin_url( 'admin.php?page=ibtana-visual-editor-addons' ); ?>">
							<?php esc_html_e( 'Upgrade Pro', 'ibtana-visual-editor' ); ?>
						</a>
					</p>
				<?php endif; ?>
				<textarea id="ive-custom-jss-code"><?php echo isset( $ive_general_settings['ive_custom_js'] ) ? $ive_general_settings['ive_custom_js'] : ''; ?></textarea>
			</div>

			<div class="ive-save-settings-block">
				<button type="submit" class="button button-primary pp-primary-button" id="ive-save-general-settings" name="save_settings">
					<span>
						<?php esc_html_e( 'Save', 'ibtana-visual-editor' ); ?>
					</span>
				</button>
			</div>

		</div>
		<?php
	}


	public function ibtana_visual_editor_ive_saved_templates_page() {
		include IVE_DIR . 'classes/ive-saved-templates.php';
	}


	public function ibtana_visual_editor_ive_license_page() {
		wp_enqueue_script( 'ibtana-admin-wizard-script' );
		$is_addons	= apply_filters( 'ive_is_add_on_installed', false );
		$license_messege	=	'';

		// If no addons installed.
		if ( !$is_addons ) {
			$base_url	= IBTANA_LICENSE_API_ENDPOINT . 'ibtana_license_get_language_strings';
			$args 		= array(
				"msg_key"	=>	'no_add_ons_installed_message'
			);
			$body			= wp_json_encode( $args );
			$options	= [
				'timeout'     => 0,
				'body'        => $body,
				'headers'     => [
					'Content-Type' => 'application/json',
				],
			];
			$response = wp_remote_post( $base_url, $options );

			if ( is_wp_error( $response ) ) {
				$license_messege = '<h4>'.
				sprintf(
					__(
						'No add-ons installed. See available add-ons - %1$s.',
						'ibtana-visual-editor'
					),
					'<a href="https://vwthemes.net/add-ons/" target="_blank">https://vwthemes.net/add-ons/</a>'
				).
				'</h4>';
			} else if ( ( $response['response']['code'] === 200 ) && ( $response['response']['message'] === 'OK' ) ) {
				$response					= json_decode( $response['body'] );
				$response_status	=	$response->status;
				if ( $response_status == true ) {
					$response_msg			=	$response->msg;
					$license_messege	=	$response_msg;
				} else {
					$license_messege = '<h4>'.
					sprintf(
						__(
							'No add-ons installed. See available add-ons - %1$s.','ibtana-visual-editor'
						),
						'<a href="https://vwthemes.net/add-ons/" target="_blank">https://vwthemes.net/add-ons/</a>'
					).
					'</h4>';
				}
			}
		} else {
			$license_messege = __(
				'Enter your add-ons license keys here to receive updates for purchased add-ons. If your license key has expired, please renew your license.',
				'ibtana-visual-editor'
			);
		}
		?>


		<?php self::ibtana_visual_editor_banner_head(); ?>


		<div class="ive-license-wrapper">
			<h3><?php esc_html_e( 'License', 'ibtana-visual-editor' );?></h3>
			<div class="ive_padding_space"></div>
			<div class="ive-license-desc-row"><?php echo html_entity_decode($license_messege)?></div>
			<div class="ive-license-cards-row">
				<?php do_action( 'ive_addon_license_area' ); ?>
			</div>
		</div>

		<?php
	}


	public function ibtana_visual_editor_ive_envato_page() {
		wp_enqueue_script( 'ibtana-admin-wizard-script' );
		$is_addons	= apply_filters( 'ive_is_envato_add_on_installed', false );
		$license_messege	=	'';

		// If no addons installed.
		// if ( !$is_addons ) {
			$base_url	= IBTANA_LICENSE_API_ENDPOINT . 'ibtana_license_get_language_strings';
			$args 		= array(
				"msg_key"	=>	'envato_add_ons_installed_message'
			);
			$body			= wp_json_encode( $args );
			$options	= [
				'timeout'     => 0,
				'body'        => $body,
				'headers'     => [
					'Content-Type' => 'application/json',
				],
			];
			$response = wp_remote_post( $base_url, $options );

			if ( is_wp_error( $response ) ) {
				$license_messege = '<h4>'.sprintf( __( 'Enter your add-ons license keys here to receive updates for purchased add-ons.', 'ibtana-visual-editor' ) ).'</h4>';
			} else if ( ( $response['response']['code'] === 200 ) && ( $response['response']['message'] === 'OK' ) ) {
				$response					= json_decode( $response['body'] );
				$response_status	=	$response->status;
				if ( $response_status == true ) {
					$response_msg			=	$response->msg;
					$license_messege	=	$response_msg;
				} else {
					$license_messege = '<h4>'.sprintf( __( 'Enter your add-ons license keys here to receive updates for purchased add-ons.', 'ibtana-visual-editor' ) ).'</h4>';
				}
			}
		?>


		<?php self::ibtana_visual_editor_banner_head(); ?>


		<div class="ive-license-wrapper">
			<h3><?php esc_html_e( 'Envato', 'ibtana-visual-editor' );?></h3>
			<div class="ive_padding_space"></div>
			<div class="ive-license-desc-row"><?php echo html_entity_decode( $license_messege )?></div>
			<div class="ive-license-cards-row">
				<?php do_action( 'ive_envato_addon_license_area' ); ?>
			</div>
		</div>
		<?php
	}


	public function ibtana_visual_editor_ive_addons_page() {

		// global $pagenow;
		// $screen = get_current_screen();
		// if( is_admin() && ( 'ibtana-settings_page_ibtana-visual-editor-addons' == $screen->id ) && current_user_can( 'manage_options' ) ) {
		// 	if ( !class_exists( 'WooCommerce' ) || !did_action( 'ibtana-visual-editor/loaded' ) ) {
		// 		wp_redirect( admin_url( 'tools.php?page=' . esc_attr( 'ibtanaecommerceproductaddons-setup' ) ) );
		// 		exit;
		// 	}
		// }

		self::ibtana_visual_editor_banner_head();
		?>


		<div class="ive-addons-wrapper">

		</div>


		<div class="wrap">
			<h1><?php esc_html_e( 'Addons', 'ibtana-visual-editor' ); ?></h1>
			<div class="wp-list-table widefat ive-addon-cards">

				<div id="the-ive-addons-list">
					<?php //$this->ive_addons_page_cards(); ?>
				</div>

			</div>
		</div>
		<?php
	}

}

//main menu of ibtana settings
$ibtana_visual_editor_settings = array(
	'page_type'				=>	'menu_page',
	'page_title'			=>	'Ibtana Settings',
	'menu_title'			=>	'Ibtana Settings',
	'capability'			=>	'edit_theme_options',
	'menu_slug'				=>	'ibtana-visual-editor',
	'icon_url'				=>	apply_filters(
		'ive:dashboard:icon-url',
		IBTANA_PLUGIN_URI . 'dist/images/ibtana-setting-icon.svg'
	),
	'page_functions'	=>	'ibtana_visual_editor_settings_page',
	'position' 				=>	'30,27',
	'priority' 				=>	7,
);
new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_settings );


//main menu of ibtana settings
$ibtana_visual_editor_settings = array(
	'page_type'				=>	'same_admin_page',
	'page_title' 			=>	'Getting Started',
	'menu_title' 			=>	'Getting Started',
	'capability' 			=>	'edit_theme_options',
	'menu_slug' 			=>	'ibtana-visual-editor',
	'icon_url' 				=>	'',
	'parent_slug' 		=>	'ibtana-visual-editor',
	'page_functions'	=>	'ibtana_visual_editor_settings_page',
	// 'priority'				=>	0
);
new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_settings );


/*$ibtana_visual_editor_settings = array(
	'page_type' => 'submenu_page',
	'page_title' => 'Templates',
	'menu_title' => 'Templates',
	'capability' => 'edit_theme_options',
	'menu_slug' => 'ibtana-visual-editor-templates',
	'icon_url' => '',
	'parent_slug' => 'ibtana-visual-editor',
	'page_functions' => 'ibtana_visual_editor_templates_page'
);
new Ibtana_Visual_Editor_Menu_Creator($ibtana_visual_editor_settings);*/


$ibtana_visual_editor_settings = array(
	'page_type' 			=>	'submenu_page',
	'page_title' 			=>	'Templates',
	'menu_title' 			=>	'Templates',
	'capability' 			=>	'edit_theme_options',
	'menu_slug' 			=>	'ibtana-visual-editor-templates',
	'icon_url' 				=>	'',
	'parent_slug' 		=>	'ibtana-visual-editor',
	'page_functions'	=>	'ibtana_visual_editor_ive_templates_page',
	// 'priority'				=>	1
);
new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_settings );


$ibtana_visual_editor_settings = array(
	'page_type'				=> 'submenu_page',
	'page_title'			=> 'Settings',
	'menu_title'			=> 'Settings',
	'capability'			=> 'edit_theme_options',
	'menu_slug'				=> 'ibtana-visual-editor-general-settings',
	'icon_url'				=> '',
	'parent_slug'			=> 'ibtana-visual-editor',
	'page_functions'	=> 'ibtana_visual_editor_general_settings_page'
);
new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_settings );


$ibtana_visual_editor_saved_templates = array(
	'page_type' 			=>	'submenu_page',
	'page_title'			=>	'Saved Templates',
	'menu_title'			=>	'Saved Templates',
	'capability'			=>	'edit_theme_options',
	'menu_slug'				=>	'ibtana-visual-editor-saved-templates',
	'icon_url'				=>	'',
	'parent_slug' 		=>	'ibtana-visual-editor',
	'page_functions'	=>	'ibtana_visual_editor_ive_saved_templates_page'
	// 'priority'		=>	2,
);
new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_saved_templates );


$ibtana_visual_editor_settings = array(
	'page_type'				=> 'submenu_page',
	'page_title'			=> 'License',
	'menu_title'			=> 'License',
	'capability'			=> 'edit_theme_options',
	'menu_slug'				=> 'ibtana-visual-editor-license',
	'icon_url'				=> '',
	'parent_slug'			=> 'ibtana-visual-editor',
	'page_functions'	=> 'ibtana_visual_editor_ive_license_page',
	'priority'				=>	99
);
new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_settings );


// $ibtana_visual_editor_settings = array(
// 	'page_type'				=> 'submenu_page',
// 	'page_title'			=> 'Envato License',
// 	'menu_title'			=> 'Envato License',
// 	'capability'			=> 'edit_theme_options',
// 	'menu_slug'				=> 'ibtana-visual-editor-envato',
// 	'icon_url'				=> '',
// 	'parent_slug'			=> 'ibtana-visual-editor',
// 	'page_functions'	=> 'ibtana_visual_editor_ive_envato_page',
// 	'priority'				=>	100
// );
// new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_settings );

$ibtana_visual_editor_settings = array(
	'page_type'				=>	'submenu_page',
	'page_title'			=>	'Addons',
	'menu_title'			=>	'Addons',
	'capability'			=>	'edit_theme_options',
	'menu_slug'				=>	'ibtana-visual-editor-addons',
	'icon_url'				=>	'',
	'parent_slug'			=>	'ibtana-visual-editor',
	'page_functions'	=>	'ibtana_visual_editor_ive_addons_page',
	'priority'				=>	101
);
new Ibtana_Visual_Editor_Menu_Creator( $ibtana_visual_editor_settings );
