<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

if ( ! function_exists( 'cmplz_uses_google_analytics' ) ) {

	/**
	 * Check if site uses google analytics
	 * @return bool
	 */

	function cmplz_uses_google_analytics() {
		return COMPLIANZ::$cookie_admin->uses_google_analytics();
	}
}

if ( ! function_exists( 'cmplz_consent_mode' ) ) {

	/**
	 * Check if site uses google analytics
	 * @return bool
	 */

	function cmplz_consent_mode() {
		return cmplz_get_value( 'consent-mode' ) === 'yes';
	}
}



if ( !function_exists('cmplz_upgraded_to_current_version')){

	/**
	 * Check if the user has upgraded to the current version, or if this is a fresh install with this version.
	 */

	function cmplz_upgraded_to_current_version() {
		$first_version = get_option( 'cmplz_first_version' );
		//if there's no first version yet, we assume it's not upgraded
		if ( !$first_version ) {
			return false;
		}
		//if the first version is below current, we just upgraded.
		if ( version_compare($first_version,cmplz_version ,'<') ){
			return true;
		}
		return false;
	}
}

if ( ! function_exists('cmplz_subscription_type') ) {
    /**
     * Get subscription type
     * @return string
     */
    function cmplz_subscription_type()
    {
        return defined('cmplz_free') ? 'free' : 'premium';
    }
}

if ( ! function_exists( 'cmplz_get_template' ) ) {
	/**
	 * Get a template based on filename, overridable in theme dir
	 * @param string $filename
	 * @param array $args
	 * @param string $path
	 * @return string
	 */

	function cmplz_get_template( $filename , $args = array(), $path = false ) {
		$path = $path ? $path : trailingslashit( cmplz_path ) . 'templates/';
		$file = apply_filters('cmplz_template_file', $path . $filename, $filename);
		$theme_file = trailingslashit( get_stylesheet_directory() )
		              . trailingslashit( basename( cmplz_path ) )
		              . 'templates/' . $filename;

		if ( !file_exists( $file ) ) {
		    return false;
        }

		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( strpos( $file, '.php' ) !== false ) {
			ob_start();
			require $file;
			$contents = ob_get_clean();
		} else {
			$contents = file_get_contents( $file );
		}

		if ( !empty($args) && is_array($args) ) {
			foreach($args as $fieldname => $value ) {
				$contents = str_replace( '{'.$fieldname.'}', $value, $contents );
			}
		}

		return $contents;
	}
}

if ( ! function_exists( 'cmplz_tagmanager_conditional_helptext' ) ) {

	function cmplz_tagmanager_conditional_helptext() {

		if ( !cmplz_consent_required_for_anonymous_stats() ) {
			$text = __( "Based on your Analytics configuration you should fire Analytics as a functional cookie on event cmplz_event_functional.", 'complianz-gdpr' );
		} else {
			$text = __( "Based on your Analytics configuration you should fire Analytics as a non-functional cookie with a category of your choice, for example cmplz_event_0.", 'complianz-gdpr' );
		}

		return $text;
	}
}

if ( ! function_exists( 'cmplz_cookiebanner_category_conditional_helptext' ) ) {

	function cmplz_cookiebanner_category_conditional_helptext() {
		$text = '';
		if ( cmplz_get_value('country_company') == "FR"
		) {
			$text
				= sprintf( __( "Due to the French CNIL guidelines we suggest using the Accept + View preferences template. For more information, read about the CNIL updated privacy guidelines in this %sarticle%s.",
                    'complianz-gdpr' ),
                    '<a href="https://complianz.io/cnil-updated-privacy-guidelines/" target="_blank">', "</a>" );
		}
		return $text;
	}
}

if ( ! function_exists( 'cmplz_manual_stats_config_possible' ) ) {

	/**
	 * Checks if the statistics are configured so no consent is need for statistics
	 *
	 * @return bool
	 */

	function cmplz_manual_stats_config_possible() {
		$stats = cmplz_get_value( 'compile_statistics' );
		if ( $stats === 'matomo' && cmplz_no_ip_addresses() ) {
			return true;
		}

		//Google Tag Manager should also be possible to embed yourself if you haven't integrated it anonymously
		if ( $stats === 'google-tag-manager' ) {
			return true;
		}

		if ( $stats === 'google-analytics' ) {
			if ( !cmplz_consent_required_for_anonymous_stats()
			) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_complianz_can_configure_stats' ) ) {

	/**
	 * Checks if the plugin can configure this type of statistics automatically
	 *
	 * @return bool
	 */

	function cmplz_complianz_can_configure_stats() {
		$stats = cmplz_get_value( 'compile_statistics' );
		if ( $stats === 'google-analytics' ||
			 $stats === 'matomo' ||
			 $stats === 'yandex' ||
			 $stats === 'clicky' ||
			 $stats === 'google-tag-manager'
		){
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'cmplz_get_stats_tool_nice' ) ) {
	function cmplz_get_stats_tool_nice() {
		//if the user just changed the setting, we use the posted data. The data is not saved yet, so would yield the previous setting
		if ( isset($_POST['cmplz_compile_statistics'])) {
			$stats = sanitize_text_field($_POST['cmplz_compile_statistics']);
		} else {
			$stats = cmplz_get_value( 'compile_statistics', false, 'wizard', false );

		}
		switch ( $stats ){
			case 'google-analytics':
				return "Google Analytics";
			case 'matomo':
				return "Matomo";
			case 'clicky':
				return "Clicky";
			case 'yandex':
				return "Yandex";
			case 'google-tag-manager':
				return "Google Tag Manager";
			default:
				return __("Not found","complianz-gdpr");
		}
	}
}

if ( !function_exists('cmplz_detected_cookie_plugin')) {
	function cmplz_detected_cookie_plugin( $return_name = false ){
		$plugin = false;
		if (defined('CLI_LATEST_VERSION_NUMBER')){
			$plugin = "GDPR Cookie Consent";
		} elseif(defined('MOOVE_GDPR_VERSION')) {
			$plugin = "GDPR Cookie Compliance";
		}elseif(defined('CTCC_PLUGIN_URL')) {
			$plugin = "GDPR Cookie Consent Banner";
		}elseif(defined('RCB_FILE')) {
			$plugin = "Real Cookie Banner";
		}elseif(class_exists('Cookiebot_WP')) {
			$plugin = "Cookiebot";
		}elseif(function_exists('bst_plugin_install')) {
			$plugin = "BST DSGVO Cookie";
		}elseif(function_exists('jlplg_lovecoding_set_cookie')) {
			$plugin = "Simple Cookie Notice";
		}elseif(class_exists('SCCBPP_WpCookie_Save')) {
			$plugin = "Seers Cookie Consent Banner Privacy Policy";
		}elseif(function_exists('daextlwcnf_customize_action_links')) {
			$plugin = "Lightweight Cookie Notice Free";
		}elseif(defined('GDPRCN_VERSION')) {
			$plugin = "GDPR Cookie Notice";
		}elseif(function_exists('wp_gdpr_cookie_notice_check_requirements')) {
			$plugin = "WP GDPR Cookie Notice";
		}elseif(defined('SURBMA_GPGA_PLUGIN_VERSION_NUMBER')) {
			$plugin = "Surbma | GDPR Proof Cookie Consent & Notice Bar";
		}elseif(class_exists('dsdvo_wp_backend')) {
			$plugin = "DSGVO All in one for WP";
		}elseif(class_exists('Cookie_Notice')) {
			$plugin = "Cookie Notice & Compliance";
		}elseif(defined('CNCB_VERSION')) {
			$plugin = "Cookie Notice and Consent Banner";
		}elseif(function_exists('add_cookie_notice')) {
			$plugin = "Cookie Notice Lite";
		}elseif(function_exists('fhw_dsgvo_cookie_insert')) {
			$plugin = "GDPR tools: cookie notice + privacy";
		}elseif(defined('GDPR_COOKIE_CONSENT_PLUGIN_URL')) {
			$plugin = "GDPR Cookie Consent";
		}elseif(defined('WP_GDPR_C_SLUG')) {
			$plugin = "WP GDPR Compliance";
		}elseif(defined('TERMLY_VERSION')) {
			$plugin = "Termly | GDPR/CCPA Cookie Consent Banner";
		}

		if ( $plugin !== false && !$return_name ) {
			return true;
		} else {
			return $plugin;
		}
	}
}

if ( ! function_exists( 'cmplz_revoke_link' ) ) {
	/**
	 * Output a revoke button
	 * @param bool $text
	 *
	 * @return string
	 */
	function cmplz_revoke_link( $text = false ) {
		$text = $text ? $text : __( 'Manage Consent', 'complianz-gdpr' );
		$text = apply_filters( 'cmplz_revoke_button_text', $text );
		$css
		      = '<style>.cmplz-status-accepted,.cmplz-status-denied {display: none;}</style>';
		$html = $css . '<button class="cc-revoke-custom">' . $text
		        . '</button>&nbsp;<span class="cmplz-status-accepted">'
		        . sprintf( __( 'Current status: %s', 'complianz-gdpr' ),
				__( "Accepted", 'complianz-gdpr' ) )
		        . '</span><span class="cmplz-status-denied">'
		        . sprintf( __( 'Current status: %s', 'complianz-gdpr' ),
				__( "Denied", 'complianz-gdpr' ) ) . '</span>';

		return apply_filters( 'cmplz_revoke_link', $html );
	}
}

if ( ! function_exists( 'cmplz_do_not_sell_personal_data_form' ) ) {
	/**
	 * Shortcode for DNSMPI form
	 * @return string
	 */
	function cmplz_do_not_sell_personal_data_form() {

		$html = cmplz_get_template( 'do-not-sell-my-personal-data-form.php' );

		return $html;
	}
}
if ( ! function_exists( 'cmplz_sells_personal_data' ) ) {
	function cmplz_sells_personal_data() {
		$purposes = cmplz_get_value( 'purpose_personaldata' , false, 'wizard');
		if ( isset( $purposes['selling-data-thirdparty'] )
		     && $purposes['selling-data-thirdparty']
		) {
			return true;
		}

		return false;
	}
}
if ( ! function_exists( 'cmplz_sold_data_12months' ) ) {
	function cmplz_sold_data_12months() {
		return COMPLIANZ::$company->sold_data_12months();
	}
}
if ( ! function_exists( 'cmplz_disclosed_data_12months' ) ) {
	function cmplz_disclosed_data_12months() {
		return COMPLIANZ::$company->disclosed_data_12months();
	}
}

if ( ! function_exists( 'cmplz_get_value' ) ) {

	/**
	 * Get value for an a complianz option
	 * For usage very early in the execution order, use the $page option. This bypasses the class usage.
	 *
	 * @param string $fieldname
	 * @param bool|int $post_id
	 * @param bool|string $page
	 * @param bool $use_default
	 * @param bool $use_translate
	 *
	 * @return array|bool|mixed|string
	 */

	function cmplz_get_value(
		$fieldname, $post_id = false, $page = false, $use_default = true, $use_translate = true
	) {
		if ( ! is_numeric( $post_id ) ) {
			$post_id = false;
		}

		if ( ! $page && ! isset( COMPLIANZ::$config->fields[ $fieldname ] ) ) {
			return false;
		}

		//if  a post id is passed we retrieve the data from the post
		if ( ! $page ) {
			$page = COMPLIANZ::$config->fields[ $fieldname ]['source'];
		}
		if ( $post_id && ( $page !== 'wizard' ) ) {
			$value = get_post_meta( $post_id, $fieldname, true );
		} else {
			$fields = get_option( 'complianz_options_' . $page );

			$default = ( $use_default && $page && isset( COMPLIANZ::$config->fields[ $fieldname ]['default'] ) )
                ? COMPLIANZ::$config->fields[ $fieldname ]['default'] : '';
            //@todo $default = apply_filters( 'cmplz_default_value', $default, $fieldname );

			$value   = isset( $fields[ $fieldname ] ) ? $fields[ $fieldname ] : $default;
		}

		/*
         * Translate output
         *
         * */
        if ($use_translate) {

            $type = isset(COMPLIANZ::$config->fields[$fieldname]['type'])
                ? COMPLIANZ::$config->fields[$fieldname]['type'] : false;
            if ($type === 'cookies' || $type === 'thirdparties'
                || $type === 'processors'
            ) {
                if (is_array($value)) {

                    //this is for example a cookie array, like ($item = cookie("name"=>"_ga")

                    foreach ($value as $item_key => $item) {
                        //contains the values of an item
                        foreach ($item as $key => $key_value) {
                            if (function_exists('pll__')) {
                                $value[$item_key][$key] = pll__($item_key . '_'
                                    . $fieldname
                                    . "_" . $key);
                            }
                            if (function_exists('icl_translate')) {
                                $value[$item_key][$key]
                                    = icl_translate('complianz',
                                    $item_key . '_' . $fieldname . "_" . $key,
                                    $key_value);
                            }

                            $value[$item_key][$key]
                                = apply_filters('wpml_translate_single_string',
                                $key_value, 'complianz',
                                $item_key . '_' . $fieldname . "_" . $key);
                        }
                    }
                }
            } else {
                if (isset(COMPLIANZ::$config->fields[$fieldname]['translatable'])
                    && COMPLIANZ::$config->fields[$fieldname]['translatable']
                ) {
                    if (function_exists('pll__')) {
                        $value = pll__($value);
                    }
                    if (function_exists('icl_translate')) {
                        $value = icl_translate('complianz', $fieldname, $value);
                    }

                    $value = apply_filters('wpml_translate_single_string', $value,
                        'complianz', $fieldname);
                }
            }

        }

		return $value;
	}
}

if ( ! function_exists( 'cmplz_eu_site_needs_cookie_warning' ) ) {
	/**
	 * Check if EU targeted site needs a cookie warning
	 *
	 * @return bool
	 */
	function cmplz_eu_site_needs_cookie_warning() {
		return COMPLIANZ::$cookie_admin->site_needs_cookie_warning( 'eu' );
	}
}

if ( ! function_exists( 'cmplz_za_site_needs_cookie_warning' ) ) {
	/**
	 * Check if ZA targeted site needs a cookie warning
	 *
	 * @return bool
	 */
	function cmplz_za_site_needs_cookie_warning() {
		return COMPLIANZ::$cookie_admin->site_needs_cookie_warning( 'za' );
	}
}

if ( ! function_exists( 'cmplz_uk_site_needs_cookie_warning' ) ) {
	/**
	 * Check if EU targeted site needs a cookie warning
	 *
	 * @return bool
	 */
	function cmplz_uk_site_needs_cookie_warning() {
		return COMPLIANZ::$cookie_admin->site_needs_cookie_warning( 'uk' );
	}
}

if ( ! function_exists( 'cmplz_eu_site_uses_cookie_warning_cats' ) ) {

	/**
	 * Check if optin site needs cookie warning with categories
	 * @return bool
	 */
	function cmplz_eu_site_uses_cookie_warning_cats() {
		$cookiebanner = new CMPLZ_COOKIEBANNER( apply_filters( 'cmplz_user_banner_id',  cmplz_get_default_banner_id() ) );
		if ( $cookiebanner->use_categories !== 'no'
		) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_uk_site_uses_cookie_warning_cats' ) ) {

	/**
	 * Check if optin site needs cookie warning with categories
	 * @return bool
	 */
	function cmplz_uk_site_uses_cookie_warning_cats() {
		$cookiebanner = new CMPLZ_COOKIEBANNER( apply_filters( 'cmplz_user_banner_id',  cmplz_get_default_banner_id() ) );
		if ( $cookiebanner->use_categories_optinstats !== 'no' ) {

			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_za_site_uses_cookie_warning_cats' ) ) {

	/**
	 * Check if optin site needs cookie warning with categories
	 * @return bool
	 */
	function cmplz_za_site_uses_cookie_warning_cats() {
		$cookiebanner = new CMPLZ_COOKIEBANNER( apply_filters( 'cmplz_user_banner_id',  cmplz_get_default_banner_id() ) );
		if ( $cookiebanner->use_categories_optinstats !== 'no' ) {

			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_company_located_in_region' ) ) {

	/**
	 * Check if this company is located in a certain region
	 *
	 * @param $region
	 *
	 * @return bool
	 */
	function cmplz_company_located_in_region( $region ) {
		$country_code = cmplz_get_value( 'country_company' );

		return ( cmplz_get_region_for_country( $country_code ) === $region );
	}
}

if ( ! function_exists( 'cmplz_has_region' ) ) {
	/**
	 * Check if this company has this region selected.
	 *
	 * @param $code
	 *
	 * @return bool
	 */
	function cmplz_has_region( $code ) {
		$regions = cmplz_get_regions(true);
		if ( isset( $regions[ $code ] ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_get_region_from_legacy_type' ) ) {
	function cmplz_get_region_from_legacy_type( $type ) {
		$region = false;
		if ( strpos( $type, 'disclaimer' ) !== false || strpos( $type, 'all' ) !== false ) {
			$region = 'all';
		}
		//get last three chars of string. if not contains -, it's eu or disclaimer.
		if ( substr( $type, - 3, 1 ) === '-' ) {
			$region = substr( $type, - 2 );
		}
		if ( ! $region ) {
			$region = 'eu';
		}

		return $region;
	}
}

if ( ! function_exists( 'cmplz_get_regions' ) ) {
	function cmplz_get_regions( $ad_all_category = false, $use_full_label = false ) {
		$regions = cmplz_get_value( 'regions', false, 'wizard' );

		if ( ! is_array( $regions ) && ! empty( $regions ) ) {
			$regions = array( $regions => 1 );
		}
		$output = array();
		if ( ! empty( $regions ) ) {
			foreach ( $regions as $region => $enabled ) {
				if ( ! $enabled ) {
					continue;
				}
				if ($use_full_label) {
					$label = isset( COMPLIANZ::$config->regions[ $region ] ) ? COMPLIANZ::$config->regions[ $region ]['label_full'] : '';
				} else {
					$label = isset( COMPLIANZ::$config->regions[ $region ] ) ? COMPLIANZ::$config->regions[ $region ]['label'] : '';
				}
				$output[ $region ] = $label;
			}
		}

		if ( $ad_all_category ) {
			$output['all'] = __( 'General', 'complianz-gdpr' );
		}

		return $output;
	}
}

if ( ! function_exists( 'cmplz_multiple_regions' ) ) {

	function cmplz_multiple_regions() {
		//if geo ip is not enabled, return false anyway
		if ( ! cmplz_geoip_enabled() ) {
			return false;
		}

		$regions = cmplz_get_regions();

		return count( $regions ) > 1;
	}
}

if ( ! function_exists( 'cmplz_get_region_for_country' ) ) {

	function cmplz_get_region_for_country( $country_code ) {
		$region = false;

		$regions = COMPLIANZ::$config->regions;
		foreach ( $regions as $region_code => $region_data ) {
			if ( in_array( $country_code, $region_data['countries'] ) ) {
				$region = $region_code;
				break;
			}
		}

		return apply_filters( "cmplz_region_for_country", $region, $country_code );
	}
}

if ( ! function_exists( 'cmplz_get_consenttype_for_country' ) ) {
	function cmplz_get_consenttype_for_country( $country_code ) {
		$regions       = COMPLIANZ::$config->regions;
		$used_regions = cmplz_get_regions();
		foreach ( $regions as $key => $region ) {
			if ( !array_key_exists( $key, $used_regions )) {
				unset($regions[$key]);
			}
		}

		$actual_region = cmplz_get_region_for_country( $country_code );
		if ( isset( $regions[ $actual_region ]) && isset( $regions[ $actual_region ]['type'] ) ) {
			return apply_filters( 'cmplz_consenttype', $regions[ $actual_region ]['type'], $actual_region );
		}

		return false;
	}
}
if ( ! function_exists( 'cmplz_intro' ) ) {

	/**
	 * @param string $msg
	 *
	 * @return string|void
	 */

	function cmplz_intro( $msg ) {
		if ( $msg == '' ) {
			return;
		}
		$html = "<div class='cmplz-panel cmplz-notification cmplz-intro'>{$msg}</div>";

		echo $html;

	}
}

if ( ! function_exists( 'cmplz_notice' ) ) {
	/**
	 * Notification without arrow on the left. Should be used outside notifications center
	 * @param string $msg
	 * @param string $type notice | warning | success
	 * @param bool   $echo
	 *
	 * @return string|void
	 */
	function cmplz_notice( $msg, $type = 'notice', $echo = true ) {
		if ( $msg == '' ) {
			return;
		}

		$html = "<div class='cmplz-panel-wrap'><div class='cmplz-panel cmplz-notification cmplz-{$type}'><div>{$msg}</div></div></div>";

		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'cmplz_conclusion' ) ) {
	/**
	 * Conclusion list drop down
	 * @param string $msg
	 * @param string $type notice | warning | success
	 * @param bool   $echo
	 *
	 * @return string|void
	 */
	function cmplz_conclusion( $title, $conclusions, $animate = true, $echo = true ) {
		if ( is_array( $conclusions ) == false ) {
			return;
		}

		ob_start();

		 echo '<div id="cmplz-conclusion"><h3>' . $title . '</h3><ul class="cmplz-conclusion__list">';
				foreach($conclusions as $conclusion) {
					$icon = $animate ? 'icon-loading' : 'icon-' . $conclusion['report_status'];
					$displayOpac = $animate ? 'style="opacity: 0"' : '';
					$display = $animate ? 'style="display: none"' : '';
					echo '<li ' . $displayOpac . 'class="cmplz-conclusion__check '  .$icon . '" data-status="' . $conclusion['report_status'] . '">';
						if ($animate) echo '<p class="cmplz-conclusion__check--check-text">' . $conclusion['check_text'] . '</p>';
						echo '<p ' . $display . ' class="cmplz-conclusion__check--report-text">' . $conclusion['report_text'] . '</p>';
					echo '</li>';
				}
		echo '</ul></div>';
		if ($animate) {
			?>
			<script>
				jQuery('.cmplz-conclusion__check--report-text').hide();
				// We initialise this to the first text element
				var firstText = jQuery(".cmplz-conclusion__check:first-child");
				var time = 0;
				var timeSmall = 0;

				jQuery(".cmplz-conclusion__check").each(function(){
					console.log(this);
					jQuery(firstText).css('opacity', 1);
					var that = this;
					time += getRandomInt(5, 10) * 100;;
					setTimeout( function(){
						setTimeout( function() {

							//jQuery(that).text(jQuery(that).data('text'));

							jQuery(that).removeClass('icon-loading').addClass('icon-' + jQuery(that).data('status'));
							jQuery(that).find('.cmplz-conclusion__check--check-text').hide();
							jQuery(that).find('.cmplz-conclusion__check--report-text').show();
							jQuery(that).next().css('opacity', 1);
						}, timeSmall );
						timeSmall = getRandomInt(5, 10) * 100;
					}, time);

					console.log(Math.floor(time));
				});

				function getRandomInt(min, max) {
					min = Math.ceil(min);
					max = Math.floor(max);
					return Math.floor(Math.random() * (max - min) + min); //The maximum is exclusive and the minimum is inclusive
				}

			</script>
			<?php
		}
		$html = ob_get_clean();

		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'cmplz_sidebar_notice' ) ) {
	/**
	 * @param string $msg
	 * @param string $type notice | warning | success
	 * @param bool|array  $condition
	 *
	 * @return string|void
	 */

	function cmplz_sidebar_notice( $msg, $type = 'notice', $condition = false ) {
		if ( $msg == '' ) {
			return;
		}

		// Condition
		$condition_check = "";
		$condition_question = "";
		$condition_answer = "";
		$cmplz_hidden = "";
		if ($condition) {
			//get first
			$questions = array_keys($condition);
			$question = reset($questions);
			$answer = reset($condition);
			$condition_check = "condition-check-1";
			$condition_question = "data-condition-question-1='{$question}'";
			$condition_answer = "data-condition-answer-1='{$answer}'";
			$args = array('condition'=> $condition);
			$cmplz_hidden = cmplz_field::this()->condition_applies( $args ) ? "" : "cmplz-hidden";;
		}

		echo "<div class='cmplz-help-modal cmplz-notice cmplz-{$type} {$cmplz_hidden} {$condition_check}' {$condition_question} {$condition_answer}>{$msg}</div>";
	}
}

if ( !function_exists('cmplz_admin_notice')) {
	/**
	 * @param $msg
	 */
	function cmplz_admin_notice( $msg ) {
		/**
		 * Prevent notice from being shown on Gutenberg page, as it strips off the class we need for the ajax callback.
		 *
		 * */
		$screen = get_current_screen();
		if ( $screen && $screen->parent_base === 'edit' ) {
			return;
		}
		?>
		<div id="message"
			 class="updated fade notice is-dismissible cmplz-admin-notice really-simple-plugins"
			 style="border-left:4px solid #333">
			<div class="cmplz-admin-notice-container">
				<div class="cmplz-logo"><img width=80px"
													 src="<?php echo cmplz_url ?>assets/images/icon-logo.svg"
													 alt="logo">
				</div>
				<div style="margin-left:30px">
					<?php echo $msg ?>
				</div>
			</div>
		</div>
		<?php

	}
}

if ( ! function_exists( 'cmplz_panel' ) ) {

	function cmplz_panel($title, $html, $custom_btn = '', $validate = '', $echo = true, $open = false) {
		if ( $title == '' ) {
			return '';
		}

		$open_class = $open ? 'style="display: block;"' : '';

		$output = '
        <div class="cmplz-panel cmplz-slide-panel cmplz-toggle-active">
            <div class="cmplz-panel-title">

                <span class="cmplz-panel-toggle">
                    '. cmplz_icon('arrow-right', 'success') .'
                    <span class="cmplz-title">' . $title . '</span>
                 </span>

                <span>' . $validate . '</span>

                <span>' . $custom_btn . '</span>

            </div>
            <div class="cmplz-panel-content" ' . $open_class . '>
                ' . $html . '
            </div>
        </div>';

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}

	}
}

if ( ! function_exists( 'cmplz_list_item' ) ) {

	function cmplz_list_item( $title, $link, $btn, $selected ) {
		if ( $title == '' ) {
			return;
		}
		$selected = $selected ? "selected" : '';
		?>

		<div class="cmplz-panel cmplz-link-panel <?php echo $selected ?>">
			<div class="cmplz-panel-title">
				<a class="cmplz-panel-link" href="<?php echo $link ?>">
                <span class="cmplz-panel-toggle">
                    <i class="fa fa-edit"></i>
                    <span class="cmplz-title"><?php echo $title ?></span>
                 </span>
				</a>

				<?php echo $btn ?>
			</div>
		</div>
		<?php

	}
}

/**
 * Check if the scan detected social media on the site.
 *
 *
 * */
if ( ! function_exists( 'cmplz_scan_detected_social_media' ) ) {

	function cmplz_scan_detected_social_media() {
		$social_media = get_option( 'cmplz_detected_social_media', array() );
		if ( ! is_array( $social_media ) ) {
			$social_media = array( $social_media );
		}
		$social_media = array_filter( $social_media );

		$social_media = apply_filters( 'cmplz_detected_social_media',
			$social_media );

		//nothing scanned yet, or nothing found
		if ( ! $social_media || ( count( $social_media ) == 0 ) ) {
			$social_media = false;
		}

		return $social_media;
	}
}

if ( ! function_exists( 'cmplz_scan_detected_thirdparty_services' ) ) {

	function cmplz_scan_detected_thirdparty_services() {
		$thirdparty = get_option( 'cmplz_detected_thirdparty_services',
			array() );
		if ( ! is_array( $thirdparty ) ) {
			$thirdparty = array( $thirdparty );
		}
		$thirdparty = array_filter( $thirdparty );
		$thirdparty = apply_filters( 'cmplz_detected_services', $thirdparty );

		//nothing scanned yet, or nothing found
		if ( ! $thirdparty || ( count( $thirdparty ) == 0 ) ) {
			$thirdparty = false;
		}

		return $thirdparty;
	}
}


if ( ! function_exists( 'cmplz_scan_detected_stats' ) ) {

	function cmplz_scan_detected_stats() {
		$stats = get_option( 'cmplz_detected_stats', array() );
		if ( ! is_array( $stats ) ) {
			$stats = array( $stats );
		}
		$stats = array_filter( $stats );
		//nothing scanned yet, or nothing found
		if ( ! $stats || ( count( $stats ) == 0 ) ) {
			$stats = false;
		}

		$stats = apply_filters( 'cmplz_detected_stats', $stats );

		return $stats;
	}
}

if ( ! function_exists( 'cmplz_update_option' ) ) {
	/**
	 * Save a complianz option
	 * @param string $page
	 * @param string $fieldname
	 * @param mixed $value
	 */
	function cmplz_update_option( $page, $fieldname, $value ) {
		$options               = get_option( 'complianz_options_' . $page );
		$options[ $fieldname ] = $value;
		if ( ! empty( $options ) ) {
			update_option( 'complianz_options_' . $page, $options );
		}
	}
}

if ( ! function_exists( 'cmplz_uses_statistics' ) ) {
	function cmplz_uses_statistics() {
		$stats = cmplz_get_value( 'compile_statistics' );
		if ( $stats !== 'no' ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_uses_only_functional_cookies' ) ) {
	function cmplz_uses_only_functional_cookies() {
		return COMPLIANZ::$cookie_admin->uses_only_functional_cookies();
	}
}

if ( ! function_exists( 'cmplz_site_shares_data' ) ) {
	/**
	 * Function to check if site shares data. Used in canada cookie policy
	 * @return bool
	 */
	function cmplz_site_shares_data() {
		return COMPLIANZ::$cookie_admin->site_shares_data();
	}
}

if ( ! function_exists( 'cmplz_strip_spaces' ) ) {

	function cmplz_strip_spaces( $string ) {
		return preg_replace( '/\s*/m', '', $string );

	}
}

if ( ! function_exists( 'cmplz_localize_date' ) ) {

	function cmplz_localize_date( $date ) {
		$month             = date( 'F', strtotime( $date ) ); //june
		$month_localized   = __( $month ); //juni
		$date              = str_replace( $month, $month_localized, $date );
		$weekday           = date( 'l', strtotime( $date ) ); //wednesday
		$weekday_localized = __( $weekday ); //woensdag
		$date              = str_replace( $weekday, $weekday_localized, $date );

		return $date;
	}
}

if (!function_exists('cmplz_strpos_arr')) {
	/**
	 * check if there is a partial match between a key of the array and the haystack
	 * We cannot use array_search, as this would not allow partial matches.
	 *
	 * @param string $haystack
	 * @param array  $needle
	 *
	 * @return bool|string
	 */

	function cmplz_strpos_arr( $haystack, $needle ) {
		if ( empty( $haystack ) ) {
			return false;
		}

		if ( ! is_array( $needle ) ) {
			$needle = array( $needle );
		}
		foreach ( $needle as $key => $value ) {
			if ( strlen($value) === 0 ) continue;
			if ( ( $pos = strpos( $haystack, $value ) ) !== false ) {
				return ( is_numeric( $key ) ) ? $value : $key;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_wp_privacy_version' ) ) {

	function cmplz_wp_privacy_version() {
		global $wp_version;

		return ( $wp_version >= '4.9.6' );
	}
}

/**
 * callback for privacy document Check if there is a text entered in the custom privacy statement text
 *
 * */
if ( ! function_exists( 'cmplz_has_custom_privacy_policy' ) ) {
	function cmplz_has_custom_privacy_policy() {
		$policy = cmplz_get_value( 'custom_privacy_policy_text' );
		if ( empty( $policy ) ) {
			return false;
		}

		return true;
	}
}

/**
 * callback for privacy statement document, check if google is allowed to share data with other services
 *
 * */
if ( ! function_exists( 'cmplz_statistics_no_sharing_allowed' ) ) {
	function cmplz_statistics_no_sharing_allowed() {

		$statistics       = cmplz_get_value( 'compile_statistics', false,
			'wizard' );
		$tagmanager       = ( $statistics === 'google-tag-manager' ) ? true
			: false;
		$google_analytics = ( $statistics === 'google-analytics' ) ? true
			: false;

		if ( $google_analytics || $tagmanager ) {
			$thirdparty = $google_analytics
				? cmplz_get_value( 'compile_statistics_more_info', false,
					'wizard' )
				: cmplz_get_value( 'compile_statistics_more_info_tag_manager',
					false, 'wizard' );

			$no_sharing = ( isset( $thirdparty['no-sharing'] )
			                && ( $thirdparty['no-sharing'] == 1 ) ) ? true
				: false;
			if ( $no_sharing ) {
				return true;
			} else {
				return false;
			}
		}

		//only applies to google
		return false;
	}
}

if ( ! function_exists( 'cmplz_consent_required_for_anonymous_stats' ) ) {
	function cmplz_consent_required_for_anonymous_stats() {
		if ( ! cmplz_uses_optin() ) {
			return false;
		}

		if ( cmplz_get_value( 'compile_statistics' ) !== 'no' )  {
			return true;
		}

		return false;
	}
}

/**
 * callback for privacy statement document. Check if ip addresses are stored.
 *
 * */
if ( ! function_exists( 'cmplz_no_ip_addresses' ) ) {
	function cmplz_no_ip_addresses() {
		$statistics = cmplz_get_value( 'compile_statistics', false, 'wizard' );

		//not anonymous stats.
		if ( $statistics === 'yes' ) {
			return false;
		}

		$tagmanager       = ( $statistics === 'google-tag-manager' ) ? true
			: false;
		$matomo           = ( $statistics === 'matomo' ) ? true : false;
		$google_analytics = ( $statistics === 'google-analytics' ) ? true
			: false;

		if ( $google_analytics || $tagmanager ) {
			$thirdparty   = $google_analytics
				? cmplz_get_value( 'compile_statistics_more_info', false,
					'wizard' )
				: cmplz_get_value( 'compile_statistics_more_info_tag_manager',
					false, 'wizard' );
			$ip_anonymous = ( isset( $thirdparty['ip-addresses-blocked'] )
			                  && ( $thirdparty['ip-addresses-blocked'] == 1 ) )
				? true : false;
			if ( $ip_anonymous ) {
				return true;
			} else {
				return false;
			}
		}

		if ( $matomo ) {
			if ( cmplz_get_value( 'matomo_anonymized', false, 'wizard' )
			     === 'yes'
			) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}
}

if (!function_exists('cmplz_get_console_errors')){
	/**
	 * Get console errors as detected by complianz
	 * @return string
	 */
	function cmplz_get_console_errors(){
		$errors = get_option('cmplz_detected_console_errors');
		$location = isset($errors[2]) && strlen($errors[2])>0 ? $errors[2] : __('the page source', 'complianz-gdpr');
		$line_no = isset($errors[1]) ? $errors[1] : 0;
		if ( $errors && isset($errors[0]) && $line_no>1 ) {
			return sprintf(__('%s on line %s of %s', 'complianz-gdpr'), $errors[0], $errors[1], $location);
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_cookie_warning_required_stats_eu' ) ) {
	function cmplz_cookie_warning_required_stats_eu() {
		return COMPLIANZ::$cookie_admin->cookie_warning_required_stats('eu');
	}
}

if ( ! function_exists( 'cmplz_cookie_warning_required_stats_uk' ) ) {
	function cmplz_cookie_warning_required_stats_uk() {
		return COMPLIANZ::$cookie_admin->cookie_warning_required_stats('uk');
	}
}

if ( ! function_exists( 'cmplz_cookie_warning_required_stats_za' ) ) {
	function cmplz_cookie_warning_required_stats_za() {
		return COMPLIANZ::$cookie_admin->cookie_warning_required_stats('za');
	}
}




if ( ! function_exists( 'cmplz_accepted_processing_agreement' ) ) {
	function cmplz_accepted_processing_agreement() {
		$statistics       = cmplz_get_value( 'compile_statistics', false,
			'wizard' );
		$tagmanager       = ( $statistics === 'google-tag-manager' ) ? true
			: false;
		$google_analytics = ( $statistics === 'google-analytics' ) ? true
			: false;

		if ( $google_analytics || $tagmanager ) {
			$thirdparty = $google_analytics
				? cmplz_get_value( 'compile_statistics_more_info', false,
					'wizard' )
				: cmplz_get_value( 'compile_statistics_more_info_tag_manager',
					false, 'wizard' );
			$accepted_google_data_processing_agreement
			            = ( isset( $thirdparty['accepted'] )
			                && ( $thirdparty['accepted'] == 1 ) ) ? true
				: false;
			if ( $accepted_google_data_processing_agreement ) {
				return true;
			} else {
				return false;
			}
		}

		//only applies to google
		return false;
	}
}

if ( ! function_exists( 'cmplz_init_cookie_blocker' ) ) {
	function cmplz_init_cookie_blocker() {

		if ( ! COMPLIANZ::$cookie_admin->site_needs_cookie_warning()
		) {
			return;
		}

		//only admin when ajax
		if ( ! wp_doing_ajax() && is_admin() ) {
			return;
		}

		if ( is_feed() ) {
			return;
		}

		//don't fire on the back-end
		if ( is_preview() || cmplz_is_pagebuilder_preview() || isset($_GET["cmplz_safe_mode"]) ) {
			return;
		}

		if ( defined( 'CMPLZ_DO_NOT_BLOCK' ) && CMPLZ_DO_NOT_BLOCK ) {
			return;
		}

		if ( cmplz_get_value( 'disable_cookie_block' ) ) {
			return;
		}

		/* Do not block when visitors are from outside EU or US, if geoip is enabled */
		//check cache, as otherwise all users would get the same output, while this is user specific
		//@todo better check for any caching plugin, as this check does not work with wp rocket for example.
		//if (!defined('wp_cache') && class_exists('cmplz_geoip') && COMPLIANZ::$geoip->geoip_enabled() && (COMPLIANZ::$geoip->region() !== 'eu') && (COMPLIANZ::$geoip->region() !== 'us')) return;

		//do not block cookies during the scan
		if ( isset( $_GET['complianz_scan_token'] )
		     && ( sanitize_title( $_GET['complianz_scan_token'] )
		          == get_option( 'complianz_scan_token' ) )
		) {
			return;
		}

		/* Do not fix mixed content when call is coming from wp_api or from xmlrpc or feed */
		if ( defined( 'JSON_REQUEST' ) && JSON_REQUEST ) {
			return;
		}
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			add_action( "admin_init",
				array( COMPLIANZ::$cookie_blocker, "start_buffer" ) );
		} else {
			if (cmplz_is_amp()) {
				add_action( "wp", array( COMPLIANZ::$cookie_blocker, "start_buffer" ) , 20);
			} else {
				add_action( "template_redirect", array( COMPLIANZ::$cookie_blocker, "start_buffer" ) );
			}
		}
		add_action( "shutdown",
			array( COMPLIANZ::$cookie_blocker, "end_buffer" ), 999 );

	}
}

/**
 * check if a pdf document is being generated
 *
 * @return bool
 */

if ( !function_exists('cmplz_is_loading_pdf')) {
	function cmplz_is_loading_pdf() {
		return is_user_logged_in() && isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'cmplz_pdf_nonce' );
	}
}

/**
 *
 * Check if we are currently in preview mode from one of the known page builders
 *
 * @return bool
 * @since 2.0.7
 *
 */
if ( ! function_exists( 'cmplz_is_pagebuilder_preview' ) ) {
	function cmplz_is_pagebuilder_preview() {
		$preview = false;
		global $wp_customize;
		if ( isset( $wp_customize ) || isset( $_GET['fb-edit'] )
		     || isset( $_GET['et_pb_preview'] )
		     || isset( $_GET['et_fb'] )
		     || isset( $_GET['elementor-preview'] )
		     || isset( $_GET['vc_action'] )
		     || isset( $_GET['vcv-action'] )
		     || isset( $_GET['fl_builder'] )
		     || isset( $_GET['tve'] )
		) {
			$preview = true;
		}

		return apply_filters( 'cmplz_is_preview', $preview );
	}
}

if (!function_exists('cmplz_dnsmpi_required')) {
	/**
	 * Check if the site requires DNSMPI logic
	 *
	 * @return bool
	 */
	function cmplz_dnsmpi_required() {
		return cmplz_has_region( 'us' ) && cmplz_sells_personal_data();
	}
}
if (!function_exists('cmplz_file_exists_on_url')) {
	function cmplz_file_exists_on_url($url){
		$uploads    = wp_upload_dir();
		$upload_dir = $uploads['basedir'];
		$upload_url = $uploads['baseurl'];
		$path        = str_replace( $upload_url, $upload_dir, $url );
		return file_exists($path);
	}
}

if ( ! function_exists( 'cmplz_geoip_enabled' ) ) {
	function cmplz_geoip_enabled() {
		return apply_filters( 'cmplz_geoip_enabled', false );
	}
}

if ( ! function_exists( 'cmplz_tcf_active' ) ) {
	function cmplz_tcf_active() {
		return apply_filters( 'cmplz_tcf_active', false );
	}
}

if (!function_exists('cmplz_read_more')) {
	/**
	 * Create a generic read more text with link for help texts.
	 *
	 * @param string $url
	 * @param bool   $add_space
	 *
	 * @return string
	 */
	function cmplz_read_more( $url, $add_space = true ) {
		$html
			= sprintf( __( "For more information, please read this %sarticle%s.",
			'complianz-gdpr' ), '<a target="_blank" href="' . $url . '">',
			'</a>' );
		if ( $add_space ) {
			$html = '&nbsp;' . $html;
		}

		return $html;
	}
}

if (!function_exists('cmplz_settings_overlay')) {
	function cmplz_settings_overlay($msg) {
		echo '<div class="cmplz-settings-overlay"><div class="cmplz-settings-overlay-message">'.$msg.'</div></div>';
	}
}

/**
 * Get string of supported laws
 *
 * */

if ( ! function_exists( 'cmplz_supported_laws' ) ) {

	function cmplz_supported_laws() {
		$regions = cmplz_get_regions();

		$arr = array();
		foreach ( $regions as $region => $enabled ) {
			//fallback
			if ( ! isset( COMPLIANZ::$config->regions[ $region ]['law'] ) ) {
				break;
			}

			$arr[] = COMPLIANZ::$config->regions[ $region ]['law'];
		}

		if ( count( $arr ) == 0 ) {
			return __( '(select a region)', 'complianz-gdpr' );
		}

		return implode( '/', $arr );
	}
}

register_activation_hook( __FILE__, 'cmplz_set_activation_time_stamp' );
if ( ! function_exists( 'cmplz_set_activation_time_stamp' ) ) {
	function cmplz_set_activation_time_stamp( $networkwide ) {
		update_option( 'cmplz_activation_time', time() );
	}
}

/*
 * For all legal documents for the US, privacy statement, dataleaks or processing agreements, the language should always be en_US
 *
 * */
add_filter( 'locale', 'cmplz_set_plugin_language', 19, 1 );
if ( ! function_exists( 'cmplz_set_plugin_language' ) ) {
	function cmplz_set_plugin_language( $locale ) {
		//@todo the region can't be detected here, because the term is not defined yet.
		//this is a bit of an edge case, as most users wouls require the processing agreement in their own language, which is what
		//we have by default.
//        $post_id = false;
//        if (isset($_GET['post'])) $post_id = $_GET['post'];
//        if (isset($_GET['post_id'])) $post_id = $_GET['post_id'];
//        $region = (isset($_GET['region'])) ? $_GET['region'] : false;
//
//        if ($post_id) {
//            if (!$region) {
//	            $term = wp_get_post_terms( $post_id, 'cmplz-region' );
//	            if ( ! is_wp_error( $term ) && isset( $term[0] ) ) {
//		            $region = $term[0]->slug;
//	            }
//            }
//            $post_type = get_post_type($post_id);
//
//            if (($region === 'us'|| $region === 'uk') && ($post_type === 'cmplz-dataleak' || $post_type === 'cmplz-processing')) {
//                $locale = 'en_US';
//            }
//        }
		if ( isset( $_GET['clang'] ) && $_GET['clang'] === 'en' ) {
			$locale = 'en_US';
		}

		return $locale;
	}
}

/**
 * To make sure the US documents are loaded entirely in English on the front-end,
 * We check if the locale is a not en- locale, and if so, redirect with a query arg.
 * This allows us to recognize the page on the next page load is needing a force US language.
 * */

add_action( 'wp', 'cmplz_add_query_arg' );
if ( ! function_exists( 'cmplz_add_query_arg' ) ) {
	function cmplz_add_query_arg() {
		$cmplz_lang = isset( $_GET['clang'] ) ? $_GET['clang'] : false;
		if ( ! $cmplz_lang && ! cmplz_is_pagebuilder_preview() ) {
			global $wp;
			$type = false;

			$post   = get_queried_object();
			$locale = get_locale();

			//if the locale is english, don't add any query args.
			if ( strpos( $locale, 'en' ) !== false ) {
				return;
			}

			if ( $post && property_exists( $post, 'post_content' ) ) {
				$pattern = '/cmplz-document.*type=".*?".*region="(.*?)"/i';
				$pattern_gutenberg = '/<!-- wp:complianz\/document {.*?selectedDocument":"(.*?)"} \/-->/i';
				if ( preg_match_all( $pattern, $post->post_content, $matches,
						PREG_PATTERN_ORDER )
				) {
					if ( isset( $matches[1][0] ) ) {
						$type = $matches[1][0];
					}
				} elseif ( preg_match_all( $pattern_gutenberg,
						$post->post_content, $matches, PREG_PATTERN_ORDER )
				) {
					if ( isset( $matches[1][0] ) ) {
						$type = $matches[1][0];
					}
				}

				if ( strpos( $type, 'us' ) !== false
					 || strpos( $type, 'uk' ) !== false
					 || strpos( $type, 'au' ) !== false
				) {
					//remove lang property, add our own.
					wp_redirect( home_url( add_query_arg( 'clang', 'en',
							remove_query_arg( 'lang', $wp->request ) ) ) );
					exit;
				}
			}

		}
	}
}


if ( ! function_exists( 'cmplz_array_filter_multidimensional' ) ) {
	function cmplz_array_filter_multidimensional(
		$array, $filter_key, $filter_value
	) {
		$new = array_filter( $array,
			function ( $var ) use ( $filter_value, $filter_key ) {
				return isset( $var[ $filter_key ] ) ? ( $var[ $filter_key ]
				                                        == $filter_value )
					: false;
			} );

		return $new;
	}
}

if ( ! function_exists( 'cmplz_is_amp' ) ) {
	/**
	 * Check if we're on AMP, and AMP integration is active
	 * Function should be run not before the 'wp' hook!
	 *
	 * @return bool
	 */
	function cmplz_is_amp() {

		$amp_on = false;

		if ( !$amp_on && function_exists( 'ampforwp_is_amp_endpoint' ) ) {
			$amp_on = ampforwp_is_amp_endpoint();
		}

		if ( !$amp_on && function_exists( 'is_amp_endpoint' ) ) {
			$amp_on = is_amp_endpoint();
		}

		if ( $amp_on ) {
			$amp_on = cmplz_amp_integration_active();
		}

		return $amp_on;
	}
}

if ( ! function_exists( 'cmplz_is_amp_endpoint' ) ) {
	/**
	 * Check if the site is loading as AMP
	 * Function should be run not before the 'wp' hook!
	 *
	 * @return bool
	 */
	function cmplz_is_amp_endpoint() {

		$amp_on = false;

		if ( !$amp_on && function_exists( 'ampforwp_is_amp_endpoint' ) ) {
			$amp_on = ampforwp_is_amp_endpoint();
		}

		if ( !$amp_on && function_exists( 'is_amp_endpoint' ) ) {
			$amp_on = is_amp_endpoint();
		}

		return $amp_on;
	}
}

if ( ! function_exists( 'cmplz_amp_integration_active' ) ) {
	/**
	 * Check if AMP integration is active
	 *
	 * @return bool
	 */
	function cmplz_amp_integration_active() {
		return cmplz_is_integration_enabled( 'amp' );
	}
}


if ( ! function_exists( 'cmplz_allowed_html' ) ) {
	function cmplz_allowed_html() {

		$allowed_tags = array(
			'a'          => array(
				'class'  => array(),
				'href'   => array(),
				'rel'    => array(),
				'title'  => array(),
				'target' => array(),
				'id' => array(),
			),
			'button'     => array(
				'id'  => array(),
				'class'  => array(),
				'href'   => array(),
				'rel'    => array(),
				'title'  => array(),
				'target' => array(),
			),
			'b'          => array(),
			'br'         => array(),
			'blockquote' => array(
				'cite' => array(),
			),
			'div'        => array(
				'class' => array(),
				'id'    => array(),
			),
			'h1'         => array(),
			'h2'         => array(),
			'h3'         => array(),
			'h4'         => array(),
			'h5'         => array(),
			'h6'         => array(),
			'i'          => array(),
			'input'      => array(
				'type'        => array(),
				'class'       => array(),
				'id'          => array(),
				'required'    => array(),
				'value'       => array(),
				'placeholder' => array(),
				'data-category' => array(),
				'style' => array(
					'color' => array(),
				),			),
			'img'        => array(
				'alt'    => array(),
				'class'  => array(),
				'height' => array(),
				'src'    => array(),
				'width'  => array(),
			),
			'label'      => array(
				'for' => array(),
				'class' => array(),
				'style' => array(
					'visibility' => array(),
				),
			),
			'li'         => array(
				'class' => array(),
				'id'    => array(),
			),
			'ol'         => array(
				'class' => array(),
				'id'    => array(),
			),
			'p'          => array(
				'class' => array(),
				'id'    => array(),
			),
			'span'       => array(
				'class' => array(),
				'title' => array(),
				'style' => array(
					'color' => array(),
					'display' => array(),
				),
				'id'    => array(),
			),
			'strong'     => array(),
			'table'      => array(
				'class' => array(),
				'id'    => array(),
			),
			'tr'         => array(),
			'details' => array(
				'class' => array(),
				'id'    => array(),
			),
			'summary' => array(
				'class' => array(),
				'id'    => array(),
			),
			'svg'         => array(
				'width' => array(),
				'height' => array(),
				'viewBox' => array(),
			),
			'polyline'    => array(
				'points' => array(),

			),
			'path'    => array(
				'd' => array(),

			),
			'style'      => array(),
			'td'         => array( 'colspan' => array(), 'scope' => array() ),
			'th'         => array( 'scope' => array() ),
			'ul'         => array(
				'class' => array(),
				'id'    => array(),
			),
		);

		return apply_filters( "cmplz_allowed_html", $allowed_tags );
	}
}

if ( ! function_exists( 'cmplz_flag' ) ) {
	/**
	 * Get URL for a flag image
	 *
	 * @param $regions
	 *
	 * @return string
	 */

	function cmplz_flag( $regions, $echo = true ) {
		if ( ! $regions ) {
		    if ($echo) {
                return;
            } else {
		        return '';
            }
		}
		if (!is_array( $regions ) ) $regions = array($regions);

		$html = '<div class="cmplz-region-indicator">';
		foreach ( $regions as $region ) {
            $html .= cmplz_region_icon($region);
		}
        $html .= '</div>';

		if ($echo) {
		    echo $html;
        } else {
		    return $html;
        }
	}
}


if ( ! function_exists( 'cmplz_placeholder' ) ) {
	/**
	 * Get placeholder for any type of blocked content
	 *
	 * @param bool|string $type
	 * @param string      $src
	 *
	 * @return string url
	 *
	 * @since 2.1.0
	 */
	function cmplz_placeholder( $type = false, $src = '' ) {
		if ( ! $type ) {
			$type = cmplz_get_service_by_src( $src );
		}

		$new_src = cmplz_default_placeholder( $type );
		$new_src = apply_filters( "cmplz_placeholder_$type", $new_src, $src );
		$new_src = apply_filters( 'cmplz_placeholder', $new_src, $type, $src );
		return $new_src;
	}
}

if ( ! function_exists( 'cmplz_count_socialmedia' ) ) {
	/**
	 * count the number of social media used on the site
	 *
	 * @return int
	 */
	function cmplz_count_socialmedia() {
		$sm = cmplz_get_value( 'socialmedia_on_site', false, 'wizard' );
		if ( ! $sm ) {
			return 0;
		}
		if ( ! is_array( $sm ) ) {
			return 1;
		} else {
			return count( array_filter( $sm ) );
		}
	}
}

if ( ! function_exists( 'cmplz_download_to_site' ) ) {
	/**
	 * Download a placeholder from youtube or video to this website
	 *
	 * @param string      $src
	 * @param bool|string $id
	 * @param bool        $use_filename //some filenames are too long to use.
	 *
	 * @return string url
	 *
	 *
	 * @since 2.1.5
	 */
	function cmplz_download_to_site( $src, $id = false, $use_filename = true ) {
		if ( strpos( $src, "https://" ) === false
		     && strpos( $src, "http://" ) === false
		) {
			$src = str_replace( '//', 'https://', $src );
		}
		if ( ! $id ) {
			$id = time();
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$uploads    = wp_upload_dir();
		$upload_dir = $uploads['basedir'];

		if ( ! file_exists( $upload_dir ) ) {
			mkdir( $upload_dir );
		}

		if ( ! file_exists( $upload_dir . "/complianz" ) ) {
			mkdir( $upload_dir . "/complianz" );
		}

		if ( ! file_exists( $upload_dir . "/complianz/placeholders" ) ) {
			mkdir( $upload_dir . "/complianz/placeholders" );
		}

		//set the path
		$filename = $use_filename ? "-" . basename( $src ) : '.jpg';
		$file     = $upload_dir . "/complianz/placeholders/" . $id . $filename;

		//set the url
		$new_src = $uploads['baseurl'] . "/complianz/placeholders/" . $id
		           . $filename;

		//download file
		$tmpfile = download_url( $src, $timeout = 25 );

		//check for errors
		if ( is_wp_error( $tmpfile ) ) {
			$new_src = cmplz_default_placeholder();
		} else {
			//remove current file
			if ( file_exists( $file ) ) {
				unlink( $file );
			}

			//in case the server prevents deletion, we check it again.
			if ( ! file_exists( $file ) ) {
				copy( $tmpfile, $file );
			}
		}

		if ( is_string( $tmpfile ) && file_exists( $tmpfile ) ) {
			unlink( $tmpfile );
		} // must unlink afterwards

		if ( ! file_exists( $file ) ) {
			return cmplz_default_placeholder();
		}

		return $new_src;
	}
}

if ( ! function_exists( 'cmplz_used_cookies' ) ) {
	function cmplz_used_cookies() {
		$services_template = cmplz_get_template( 'cookiepolicy_services.php' );

		$cookies_row    = cmplz_get_template( 'cookiepolicy_cookies_row.php' );
		$purpose_row    = cmplz_get_template( 'cookiepolicy_purpose_row.php' );
		$language       = substr( get_locale(), 0, 2 );

		$args = array(
			'language'     => $language,
			'showOnPolicy' => true,
			'hideEmpty'    => true,
			'ignored'      => false
		);
		if ( cmplz_get_value( 'wp_admin_access_users' ) === 'yes' ) {
			$args['isMembersOnly'] = 'all';
		}

		$cookies = COMPLIANZ::$cookie_admin->get_cookies_by_service( $args );

		$servicesHTML = '';
		foreach ( $cookies as $serviceID => $serviceData ) {
			$has_empty_cookies = false;
			$allPurposes = array();

			$service    = new CMPLZ_SERVICE( $serviceID,
				substr( get_locale(), 0, 2 ) );

            $cookieHTML = "";
			foreach ( $serviceData as $purpose => $service_cookies ) {

				$cookies_per_purpose_HTML = "";
				foreach ( $service_cookies as $cookie ) {
					$has_empty_cookies = $has_empty_cookies
					                     || strlen( $cookie->retention ) == 0;
					$link_open         = $link_close = '';

					if ( cmplz_get_value( 'use_cdb_links' ) === 'yes'
					     && strlen( $cookie->slug ) !== 0
					) {
						$service_slug = ( strlen( $service->slug ) === 0 )
							? 'unknown-service' : $service->slug;
						$link_open
						              = '<a target="_blank" rel="noopener noreferrer nofollow" href="https://cookiedatabase.org/cookie/'
						                . $service_slug . '/' . $cookie->slug
						                . '">';
						$link_close   = '</a>';
					}
                    $cookies_per_purpose_HTML .= str_replace( array(
						'{name}',
						'{retention}',
						'{cookieFunction}',
						'{link_open}',
						'{link_close}'
					), array(
						$cookie->name,
						$cookie->retention,
						ucfirst( $cookie->cookieFunction ),
						$link_open,
						$link_close
					), $cookies_row );
				}

                $cookieHTML .= str_replace( array( '{purpose}' ), array( $purpose ), $purpose_row );
				$cookieHTML = str_replace(array('{cookies_per_purpose}'), array($cookies_per_purpose_HTML), $cookieHTML);
				array_push($allPurposes, $purpose);
			}

			$service_name = $service->ID && strlen( $service->name ) > 0 ? $service->name : __( 'Miscellaneous', 'complianz-gdpr' );

			$sharing = '';
			if ( $service_name === 'Complianz' ) {
				if ( strlen( $service->privacyStatementURL ) != 0 ) {
					$link = '<a target="_blank" rel="noopener noreferrer" href="http://complianz.io/legal/privacy-statement/">';
					$sharing = __( 'This data is not shared with third parties.', 'complianz-gdpr' )
							.'&nbsp;'
							.sprintf( __( 'For more information, please read the %s%s Privacy Statement%s.', 'complianz-gdpr' ), $link, $service_name, '</a>' );
				}
			} else if ( $service->sharesData  ) {
				$attributes = "noopener noreferrer nofollow";
				if ( strlen( $service->privacyStatementURL ) != 0 ) {
					$link    = '<a target="_blank" rel="'.$attributes.'" href="' . $service->privacyStatementURL . '">';
					$sharing = sprintf( __( 'For more information, please read the %s%s Privacy Statement%s.', 'complianz-gdpr' ), $link, $service_name, '</a>' );
				}
			} elseif ( strlen( $service->name )>0 ) { //don't state sharing info on misc services
				$sharing = __( 'This data is not shared with third parties.', 'complianz-gdpr' );
			} else {
				$sharing = __( 'Sharing of data is pending investigation', 'complianz-gdpr' );
			}
			$purposeDescription = ( ( strlen( $service_name ) > 0 ) && ( strlen( $service->serviceType ) > 0 ) )
				? sprintf( _x( "We use %s for %s.", 'Legal document cookie policy', 'complianz-gdpr' ), $service_name, $service->serviceType ) : '';

			if ( cmplz_get_value( 'use_cdb_links' ) === 'yes'
			     && strlen( $service->slug ) !== 0
			     && $service->slug !== 'unknown-service'
			) {
				$link_open = '<a target="_blank" rel="noopener noreferrer nofollow" href="https://cookiedatabase.org/service/' . $service->slug . '">';
				$purposeDescription .= ' ' . $link_open . __( 'Read more', "complianz-gdpr" ) . '</a>';
			}
			$allPurposes = implode (", ", $allPurposes);
			$servicesHTML .= str_replace( array(
				'{service}',
				'{sharing}',
				'{purposeDescription}',
				'{cookies}',
				'{allPurposes}'
			), array(
				$service_name,
				$sharing,
				$purposeDescription,
				$cookieHTML,
				$allPurposes
			), $services_template );
		}

		$servicesHTML = '<div id="cmplz-cookies-overview">'.$servicesHTML.'</div>';

		return str_replace( '{plugin_url}',cmplz_url, $servicesHTML);
	}
}


/**
 * Check if this field is translatable
 *
 * @param $fieldname
 *
 * @return bool
 */

if ( ! function_exists( 'cmplz_translate' ) ) {
	function cmplz_translate( $value, $fieldname ) {
		if ( function_exists( 'pll__' ) ) {
			$value = pll__( $value );
		}

		if ( function_exists( 'icl_translate' ) ) {
			$value = icl_translate( 'complianz', $fieldname, $value );
		}

		$value = apply_filters( 'wpml_translate_single_string', $value,
			'complianz', $fieldname );

		return $value;

	}
}

if ( !function_exists('cmplz_get_server') ) {
	/**
	 * Get server type
	 *
	 * @return string
	 */

	function cmplz_get_server() {
		$server_raw = strtolower( filter_var( $_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING ) );
		//figure out what server they're using
		if ( strpos( $server_raw, 'apache' ) !== false ) {
			return 'Apache';
		} elseif ( strpos( $server_raw, 'nginx' ) !== false ) {
			return 'NGINX';
		} elseif ( strpos( $server_raw, 'litespeed' ) !== false ) {
			return 'Litespeed';
		} else { //unsupported server
			return 'Not recognized';
		}
	}
}

/**
 * Show a reference to cookiedatabase if user has accepted the API
 *
 * @return bool
 */

if ( ! function_exists( 'cmplz_cdb_reference_in_policy' ) ) {
	function cmplz_cdb_reference_in_policy() {
        $use_reference = COMPLIANZ::$cookie_admin->use_cdb_api();
		return apply_filters( 'cmplz_use_cdb_reference', $use_reference );
	}
}

/**
 * Registrer a translation
 *
 * @param $fieldname
 *
 * @return bool
 */

if ( ! function_exists( 'cmplz_register_translation' ) ) {

	function cmplz_register_translation( $string, $fieldname ) {
		//polylang
		if ( function_exists( "pll_register_string" ) ) {
			pll_register_string( $fieldname, $string, 'complianz' );
		}

		//wpml
		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'complianz', $fieldname, $string );
		}

		do_action( 'wpml_register_single_string', 'complianz', $fieldname,
			$string );

	}
}

if ( ! function_exists( 'cmplz_default_placeholder' ) ) {
	/**
	 * Return the default placeholder image
	 *
	 * @return string placeholder
	 * @since 2.1.5
	 */
	function cmplz_default_placeholder( $type = 'default' ) {
		//treat open streetmaps same as google maps.
		if ( $type === 'openstreetmaps' ) {
			$type = 'google-maps';
		}

		$style = cmplz_get_value('placeholder_style');
		$ratio = $type === 'google-maps' ? cmplz_get_value( 'google-maps-format' ) : '';
		$path = cmplz_path . "assets/images/placeholders";
		//check if this type exists as placeholder
		if ( file_exists( "$path/$type-$style-$ratio.jpg" ) ) {
			$img = "$type-$style-$ratio.jpg";
		} else if ( file_exists( "$path/$type-$style.jpg" ) ) {
			$img = "$type-$style.jpg";
		} else {
			$img = "default-$style.jpg";
		}

		$img_url = cmplz_url . 'assets/images/placeholders/' . $img;

		//check for image in themedir/complianz-gpdr-premium
		$theme_img = trailingslashit( get_stylesheet_directory() ) . trailingslashit( basename( cmplz_path ) ) . $img;
		if ( file_exists( $theme_img ) ) {
			$img_url = trailingslashit( get_stylesheet_directory_uri() ) . trailingslashit( basename( cmplz_path ) ) . $img;
		}

		return apply_filters( 'cmplz_default_placeholder', $img_url, $type, $style );
	}
}


if ( ! function_exists( 'cmplz_us_cookie_statement_title' ) ) {
	/**
	 * US Cookie policy can have two different titles depending on the Californian targeting
	 *
	 * @return string $title
	 * @since 2.0.6
	 */

	function cmplz_us_cookie_statement_title() {
		if ( cmplz_ccpa_applies() ) {
			$title = "Do Not Sell My Personal Information";
		} else {
			$title = "Cookie Policy (US)";
		}

		return apply_filters( 'cmplz_us_cookie_statement_title', $title );
	}
}

if ( ! function_exists( 'cmplz_get_document_url' ) ) {
	/**
	 * Get url to legal document
	 *
	 * @param string $region
	 *
	 * @return string URL
	 */

	function cmplz_get_document_url( $type, $region = 'eu' ) {

		return COMPLIANZ::$document->get_page_url( $type,
			$region );
	}
}


if ( ! function_exists( 'cmplz_update_cookie_policy_title' ) ) {
	/**
	 * Adjust the cookie policy title according to the california setting
	 * $return void
	 */
	function cmplz_update_cookie_policy_title() {
		//get page id of US cookie policy
		$page_id
			   = COMPLIANZ::$document->get_shortcode_page_id( 'cookie-statement',
			'us' );
		$title = cmplz_us_cookie_statement_title();
		$post  = array(
			'ID'         => intval( $page_id ),
			'post_title' => $title,
			'post_name'  => sanitize_title( $title ),
		);
		wp_update_post( $post );

		cmplz_update_option( 'cookie_settings', 'readmore_us', $title );
	}
}

if ( ! function_exists( 'cmplz_ccpa_applies' ) ) {
	function cmplz_ccpa_applies() {
		return cmplz_has_region('us') && cmplz_get_value( 'california' , false, 'wizard' ) === 'yes' && cmplz_sells_personal_data();
	}
}

if ( ! function_exists( 'cmplz_has_async_documentwrite_scripts' ) ) {
	function cmplz_has_async_documentwrite_scripts() {
		$social_media = cmplz_get_value( 'socialmedia_on_site' );
		if ( isset( $social_media['instagram'] )
		     && $social_media['instagram'] == 1
		) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_remote_file_exists' ) ) {
	function cmplz_remote_file_exists( $url ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		// don't download content
		curl_setopt( $ch, CURLOPT_NOBODY, 1 );
		curl_setopt( $ch, CURLOPT_FAILONERROR, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		$result = curl_exec( $ch );
		curl_close( $ch );
		if ( $result !== false ) {
			return true;
		} else {
			return false;
		}
	}

}

if ( ! function_exists( 'cmplz_detected_firstparty_marketing' )) {

	/**
	 * Check if we detect first party marketing scripts
	 * @return bool
	 */

	function cmplz_detected_firstparty_marketing(){
		global $cmplz_integrations_list;
		$active_plugins = array();
		foreach ( $cmplz_integrations_list as $plugin => $details ) {
			if ( cmplz_integration_plugin_is_active( $plugin ) ) {
				$active_plugins[$plugin] = $details;
			}
		}
		$firstparty_plugins = array_filter(array_column($active_plugins, 'firstparty_marketing'));

		return count($firstparty_plugins)>0;
	}
}

if ( ! function_exists( 'cmplz_detected_custom_marketing_scripts' )) {

	/**
	 * Check if site uses custom marketing scripts
	 * @return bool
	 */

	function cmplz_detected_custom_marketing_scripts(){
		$has_marketing_scripts = false;
		$script = cmplz_get_value( 'cookie_scripts' );
		$async_script = cmplz_get_value( 'cookie_scripts_async' );
		if (strlen($script ) > 0 || strlen($async_script) >0 ){
			$has_marketing_scripts = true;
		}
		return $has_marketing_scripts;
	}
}

if ( ! function_exists( 'cmplz_uses_gutenberg' ) ) {
	function cmplz_uses_gutenberg() {

		if ( function_exists( 'has_block' )
		     && ! class_exists( 'Classic_Editor', false )
		) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'get_regions_for_consent_type' ) ) {
	/**
	 * Get comma separated list of regions, which are using this consent type
	 *
	 * @param string $consenttype
	 *
	 * @return array $regions
	 */
	function get_regions_for_consent_type( $consenttype ) {
		$regions = array();
		foreach ( COMPLIANZ::$config->regions as $region_id => $region ) {
			if ( $region['type'] === $consenttype ) {
				$regions[] = $region_id;
			}
		}

		return apply_filters( 'cmplz_regions_for_consenttype', $regions, $consenttype );
	}
}

if ( ! function_exists( 'cmplz_get_used_consenttypes' ) ) {
	/**
	 * Get list of consenttypes in use on this site, based on the selected regions
	 * @param bool $add_labels
	 * @return array consenttypes
	 */
	function cmplz_get_used_consenttypes( $add_labels = false ) {
		//get all regions in use on this site
		$regions       = cmplz_get_regions();
		$consent_types = array();
		//for each region, get the consenttype
		foreach ( $regions as $region => $label ) {
			if ( ! isset( COMPLIANZ::$config->regions[ $region ]['type'] ) ) {
				continue;
			}

			$consent_types[] = apply_filters( 'cmplz_consenttype', COMPLIANZ::$config->regions[ $region ]['type'], $region );
		}
		//remove duplicates
		$consent_types = array_unique( $consent_types );

		if ( $add_labels ) {
			$consent_types_labelled = array();
			foreach ( $consent_types as $consent_type ) {
				$consent_types_labelled[$consent_type] = cmplz_get_consenttype_nice_name($consent_type);
			}
			$consent_types = $consent_types_labelled;
		}

		return $consent_types;
	}
}

if ( ! function_exists( 'cmplz_get_consenttype_nice_name' ) ) {
	/**
	 * Get a human readable name for a consenttype
	 * @param string $consent_type
	 *
	 * @return string
	 */
	function cmplz_get_consenttype_nice_name( $consent_type ){
		switch ($consent_type) {
			case 'optout':
				return __('Opt-out', 'complianz-gdpr');
			case 'optinstats':
				return __('Opt-in statistics', 'complianz-gdpr');
			case 'optin':
			default:
				return __('Opt-in', 'complianz-gdpr');
		}
	}
}

if ( ! function_exists( 'cmplz_short_date_format') ) {
	/**
	 * Make sure the date formate is always the short version. If "F" (February) is used, replace with "M" (Feb)
	 * @return string
	 */
	function cmplz_short_date_format(){
		return str_replace( array('F', 'Y'), array('M', 'y'), get_option( 'date_format' ) );
	}
}

if ( ! function_exists( 'cmplz_uses_preferences_cookies' ) ) {

    /**
     * Check if the site uses preferences cookies
     *
     * @return bool
     */
    function cmplz_uses_preferences_cookies()
    {
        return cmplz_consent_mode() || cmplz_consent_api_active();
    }
}

if ( ! function_exists( 'cmplz_uses_statistic_cookies' ) ) {

    /**
     * Check if the site uses statistic cookies
     *
     * @return bool
     */
    function cmplz_uses_statistic_cookies()
    {
        return cmplz_get_value( 'compile_statistics' ) !== 'google-tag-manager' && COMPLIANZ::$cookie_admin->cookie_warning_required_stats();
    }
}

if ( ! function_exists( 'cmplz_uses_marketing_cookies' ) ) {

    /**
     * Check if the site uses marketing cookies
     *
     * @return bool
     */
    function cmplz_uses_marketing_cookies() {

        $uses_marketing_cookies =
				cmplz_get_value('uses_ad_cookies') === 'yes'
			|| cmplz_get_value('uses_firstparty_marketing_cookies') === 'yes'
            || cmplz_get_value('uses_thirdparty_services') === 'yes'
            || cmplz_get_value('uses_social_media') === 'yes' ;

		return apply_filters( 'cmplz_uses_marketing_cookies', $uses_marketing_cookies );
	}
}

if ( ! function_exists( 'cmplz_impressum_required' ) ) {

    /**
     * Check if the site requires an impressum
     *
     * @return bool
     */
    function cmplz_impressum_required() {
        return cmplz_get_value( 'eu_consent_regions' ) === 'yes' && cmplz_get_value( 'impressum' ) !== 'none' ;
    }
}

if ( ! function_exists( 'cmplz_uses_optin' ) ) {

	/**
	 * Check if the site uses one of the optin types
	 *
	 * @return bool
	 */
	function cmplz_uses_optin() {
		return ( in_array( 'optin', cmplz_get_used_consenttypes() )
		         || in_array( 'optinstats', cmplz_get_used_consenttypes() ) );
	}
}

if ( ! function_exists( 'cmplz_set_multisite_root' ) ) {

	/**
	 * Check if the cookies should get set as special multisite root cookies
	 *
	 * Don't do this
	 *
	 * @return bool
	 */
	function cmplz_set_multisite_root() {
		return is_multisite() && is_main_site() && cmplz_get_value( 'set_cookies_on_root' );
	}
}


if ( ! function_exists( 'cmplz_uses_optout' ) ) {
	function cmplz_uses_optout() {
		return ( in_array( 'optout', cmplz_get_used_consenttypes() ) );
	}
}

if ( ! function_exists( 'cmplz_ab_testing_enabled' ) ) {
	function cmplz_ab_testing_enabled() {
		return apply_filters( 'cmplz_ab_testing_enabled', false );
	}
}


if ( ! function_exists( 'cmplz_consenttype_nicename' ) ) {
	/**
	 * Get nice name for consenttype
	 *
	 * @param string $consenttype
	 *
	 * @return string nicename
	 */
	function cmplz_consenttype_nicename( $consenttype ) {
		switch ( $consenttype ) {
			case 'optinstats':
				return __( 'Opt-in settings (consent for statistics)',
					'complianz-gdpr' );
			case 'optin':
				return __( 'Opt-in', 'complianz-gdpr' );
			case 'optout':
				return __( 'Opt-out', 'complianz-gdpr' );
			case 'tcf':
				return __( 'TCF', 'complianz-gdpr' );
			default :
				return __( 'All consent types', 'complianz-gdpr' );
		}
	}
}


if ( ! function_exists( 'cmplz_uses_sensitive_data' ) ) {
	/**
	 * Check if site uses sensitive data
	 *
	 * @return bool uses_sensitive_data
	 */
	function cmplz_uses_sensitive_data() {
		$special_data = array(
			'bank-account',
			'financial-information',
			'medical',
			'health-insurcance'
		);
		foreach ( COMPLIANZ::$config->purposes as $key => $label ) {

			if ( ! empty( COMPLIANZ::$config->details_per_purpose_us ) ) {
				foreach ( $special_data as $special_data_key ) {
					$value = cmplz_get_value( $key . '_data_purpose_us' );
					if ( isset( $value[ $special_data_key ] )
					     && $value[ $special_data_key ]
					) {
						return true;
					}
				}
			}

		}

		return false;
	}
}


if ( ! function_exists( 'cmplz_get_consenttype_for_region' ) ) {
	/**
	 * Get the consenttype which is used in this region
	 *
	 * @param string $region
	 *
	 * @return string consenttype
	 */
	function cmplz_get_consenttype_for_region( $region ) {

		//fallback
		if ( ! isset( COMPLIANZ::$config->regions[ $region ]['type'] ) ) {
			$consenttype = 'optin';
		} else {
			$consenttype = COMPLIANZ::$config->regions[ $region ]['type'];
		}

		return apply_filters( 'cmplz_consenttype', $consenttype, $region );
	}
}

if ( ! function_exists( 'cmplz_uses_consenttype' ) ) {
	/**
	 * Check if a specific consenttype is used
	 *
	 * @param string $check_consenttype
	 * @param string $region
	 *
	 * @return bool $uses_consenttype
	 */
	function cmplz_uses_consenttype( $check_consenttype, $region = false ) {
		if ( $region ) {
			//get consenttype for region
			$consenttype = cmplz_get_consenttype_for_region( $region );
			if ( $consenttype === $check_consenttype ) {
				return true;
			}
		} else {
			//check if any region has a consenttype $check_consenttype
			$regions = cmplz_get_regions();
			foreach ( $regions as $region => $label ) {
				$consenttype = cmplz_get_consenttype_for_region( $region );
				if ( $consenttype === $check_consenttype ) {
					return true;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'cmplz_get_default_banner_id' ) ) {

	/**
	 * Get the default banner ID
	 *
	 * @return int default_ID
	 */
	function cmplz_get_default_banner_id() {
		global $wpdb;
		$cookiebanners
			= $wpdb->get_results( "select * from {$wpdb->prefix}cmplz_cookiebanners as cb where cb.default = true" );

		//if nothing, try the first entry
		if ( empty( $cookiebanners ) ) {
			$cookiebanners
				= $wpdb->get_results( "select * from {$wpdb->prefix}cmplz_cookiebanners" );
		}

		if ( ! empty( $cookiebanners ) ) {
			return $cookiebanners[0]->ID;
		}

		return false;
		//nothing yet, return false
	}
}

if ( ! function_exists( 'cmplz_user_can_manage' ) ) {
	function cmplz_user_can_manage() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( !is_multisite() && cmplz_wp_privacy_version()
		     && ! current_user_can( 'manage_privacy_options' )
		) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return true;
	}
}
if ( ! function_exists( 'cmplz_get_cookiebanners' ) ) {

	/**
	 * Get array of banner objects
	 *
	 * @param array $args
	 *
	 * @return array
	 */

	function cmplz_get_cookiebanners( $args = array() ) {
		$args = wp_parse_args( $args, array( 'status' => 'active' ) );
		$sql  = '';
		global $wpdb;
		if ( $args['status'] === 'archived' ) {
			$sql = 'AND cdb.archived = true';
		}
		if ( $args['status'] === 'active' ) {
			$sql = 'AND cdb.archived = false';
		}

		if ( isset( $args['default'] ) && $args['default'] == true ) {
			$sql = 'AND cdb.default = true LIMIT 1';
		}
		if ( isset( $args['default'] ) && $args['default'] === false ) {
			$sql = 'AND cdb.default = false';
		}
		if ( isset( $args['limit'] ) && $args['limit'] !== false ) {
			$sql = ' LIMIT '.intval($args['limit']);
		}
		$cookiebanners
			= $wpdb->get_results( "select * from {$wpdb->prefix}cmplz_cookiebanners as cdb where 1=1 $sql" );


		return $cookiebanners;
	}
}

if ( ! function_exists( 'cmplz_sanitize_language' ) ) {

	/**
	 * Validate a language string
	 *
	 * @param $language
	 *
	 * @return bool|string
	 */

	function cmplz_sanitize_language( $language ) {
		$pattern = '/^[a-zA-Z]{2}$/';
		if ( ! is_string( $language ) ) {
			return false;
		}
		$language = substr( $language, 0, 2 );

		if ( (bool) preg_match( $pattern, $language ) ) {
			$language = strtolower( $language );

			return $language;
		}

		return false;
	}
}


if ( ! function_exists( 'cmplz_sanitize_languages' ) ) {

	/**
	 * Validate a languages array
	 *
	 * @param array $language
	 *
	 * @return array
	 */

	function cmplz_sanitize_languages( $languages ) {
		$output = array();
		foreach ( $languages as $language ) {
			$output[] = cmplz_sanitize_language( $language );
		}

		return $output;
	}
}

if ( ! function_exists( 'cmplz_remove_free_translation_files' ) ) {

	/**
	 *   Get a list of files from a directory, with the extensions as passed.
	 */

	function cmplz_remove_free_translation_files() {
		//can't use cmplz_path here, it may not have been defined yet on activation
		$path = plugin_dir_path(__FILE__);
		$path = dirname( $path, 2 ) . "/languages/plugins/";
		$extensions = array( "po", "mo" );
		if ( $handle = opendir( $path ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != "." && $file != ".." ) {
					$file = $path . '/' . $file;
					$ext  = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

					if ( is_file( $file ) && in_array( $ext, $extensions )
					     && strpos( $file, 'complianz-gdpr' ) !== false
					     && strpos( $file, 'backup' ) === false
					) {
						//copy to new file
						$new_name = str_replace( 'complianz-gdpr',
							'complianz-gdpr-backup-' . time(), $file );

						rename( $file, $new_name );
					}
				}
			}
			closedir( $handle );
		}
	}
}

if ( ! function_exists( 'cmplz_has_free_translation_files' ) ) {

	/**
	 * Get a list of files from a directory, with the extensions as passed.
	 *
	 * @return bool
	 */

	function cmplz_has_free_translation_files() {
		//can't use cmplz_path here, it may not have been defined yet on activation
		$path = plugin_dir_path(__FILE__);
		$path = dirname( $path, 2 ) . "/languages/plugins/";

		if ( ! file_exists( $path ) ) {
			return false;
		}

		$has_free_files = false;
		$extensions     = array( "po", "mo" );
		if ( $handle = opendir( $path ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != "." && $file != ".." ) {
					$file = $path . '/' . $file;
					$ext  = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

					if ( is_file( $file ) && in_array( $ext, $extensions )
					     && strpos( $file, 'complianz-gdpr' ) !== false
					) {
						$has_free_files = true;
						break;
					}
				}
			}
			closedir( $handle );
		}

		return $has_free_files;
	}
}

if (!function_exists('array_key_first')) {
    function array_key_first(array $array) {
		reset($array);
		return key($array);
    }
}
