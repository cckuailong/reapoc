<?php
/*
	Code that runs on the init, set_current_user, or wp hooks to set up PMPro
*/
//init code
function pmpro_init() {
	require_once(PMPRO_DIR . '/includes/countries.php');
	require_once(PMPRO_DIR . '/includes/states.php');
	require_once(PMPRO_DIR . '/includes/currencies.php');
	require_once(PMPRO_DIR . '/includes/email-templates.php');

	global $pmpro_pages, $pmpro_core_pages, $pmpro_ready, $pmpro_currencies, $pmpro_currency, $pmpro_currency_symbol;
	$pmpro_pages = array();
	$pmpro_pages["account"] = pmpro_getOption("account_page_id");
	$pmpro_pages["billing"] = pmpro_getOption("billing_page_id");
	$pmpro_pages["cancel"] = pmpro_getOption("cancel_page_id");
	$pmpro_pages["checkout"] = pmpro_getOption("checkout_page_id");
	$pmpro_pages["confirmation"] = pmpro_getOption("confirmation_page_id");
	$pmpro_pages["invoice"] = pmpro_getOption("invoice_page_id");
	$pmpro_pages["levels"] = pmpro_getOption("levels_page_id");
	$pmpro_pages["login"] = pmpro_getOption("login_page_id");
	$pmpro_pages["member_profile_edit"] = pmpro_getOption("member_profile_edit_page_id");

	//save this in case we want a clean version of the array with just the core pages
	$pmpro_core_pages = $pmpro_pages;

	$pmpro_ready = pmpro_is_ready();

	/**
	 * This action is documented in /adminpages/pagesettings.php
	 */
	$extra_pages = apply_filters('pmpro_extra_page_settings', array());
	foreach($extra_pages as $name => $page)
		$pmpro_pages[$name] = pmpro_getOption($name . '_page_id');


	//set currency
	$pmpro_currency = pmpro_getOption("currency");
	if(!$pmpro_currency)
	{
		global $pmpro_default_currency;
		$pmpro_currency = $pmpro_default_currency;
	}

	//figure out what symbol to show for currency
	if(!empty($pmpro_currencies[$pmpro_currency]) && is_array($pmpro_currencies[$pmpro_currency])) {
		if ( isset( $pmpro_currencies[$pmpro_currency]['symbol'] ) ) {
			$pmpro_currency_symbol = $pmpro_currencies[$pmpro_currency]['symbol'];
		} else {
			$pmpro_currency_symbol = '';
		}
	} elseif(!empty($pmpro_currencies[$pmpro_currency]) && strpos($pmpro_currencies[$pmpro_currency], "(") !== false)
		$pmpro_currency_symbol = pmpro_getMatches("/\((.*)\)/", $pmpro_currencies[$pmpro_currency], true);
	else
		$pmpro_currency_symbol = $pmpro_currency . " ";	//just use the code
}
add_action("init", "pmpro_init");

//this code runs after $post is set, but before template output
function pmpro_wp()
{
	if(!is_admin())
	{
		global $post, $pmpro_pages, $pmpro_core_pages, $pmpro_page_name, $pmpro_page_id, $pmpro_body_classes;

		//no pages yet?
		if(empty($pmpro_pages))
			return;

		//run the appropriate preheader function
		foreach($pmpro_core_pages as $pmpro_page_name => $pmpro_page_id)
		{
			if(!empty($post->post_content) && ( strpos($post->post_content, "[pmpro_" . $pmpro_page_name . "]") !== false || has_block( 'pmpro/' . $pmpro_page_name . '-page', $post ) ) )
			{
				//preheader
				require_once(PMPRO_DIR . "/preheaders/" . $pmpro_page_name . ".php");

				//add class to body
				$pmpro_body_classes[] = "pmpro-" . str_replace("_", "-", $pmpro_page_name);

				//shortcode
				function pmpro_pages_shortcode($atts, $content=null, $code="")
				{
					global $pmpro_page_name;
					$temp_content = pmpro_loadTemplate($pmpro_page_name, 'local', 'pages');
					return apply_filters("pmpro_pages_shortcode_" . $pmpro_page_name, $temp_content);
				}
				add_shortcode("pmpro_" . $pmpro_page_name, "pmpro_pages_shortcode");
				break;	//only the first page found gets a shortcode replacement
			}
			elseif(!empty($pmpro_page_id) && is_page($pmpro_page_id))
			{
				//add class to body
				$pmpro_body_classes[] = "pmpro-" . str_replace("_", "-", $pmpro_page_name);
				
				//shortcode has params, but we still want to load the preheader
				require_once(PMPRO_DIR . "/preheaders/" . $pmpro_page_name . ".php");
			}
		}
	}
}
add_action("wp", "pmpro_wp", 2);

/*
	Add PMPro page names to the BODY class.
*/
function pmpro_body_class($classes)
{
	global $pmpro_body_classes;

	if(is_array($pmpro_body_classes))
		$classes = array_merge($pmpro_body_classes, $classes);

	return $classes;
}
add_filter("body_class", "pmpro_body_class");

//add membership level to current user object
function pmpro_set_current_user()
{
	//this code runs at the beginning of the plugin
	global $current_user, $wpdb;
	wp_get_current_user();
	$id = intval($current_user->ID);
	if($id)
	{
		$current_user->membership_level = pmpro_getMembershipLevelForUser($current_user->ID);
		if(!empty($current_user->membership_level))
		{
			$current_user->membership_level->categories = pmpro_getMembershipCategories($current_user->membership_level->ID);
		}
		$current_user->membership_levels = pmpro_getMembershipLevelsForUser($current_user->ID);
	}

	//hiding ads?
	$hideads = pmpro_getOption("hideads");
	$hideadslevels = pmpro_getOption("hideadslevels");
	if(!is_array($hideadslevels))
		$hideadslevels = explode(",", $hideadslevels);
	if($hideads == 1 && pmpro_hasMembershipLevel() || $hideads == 2 && pmpro_hasMembershipLevel($hideadslevels))
	{
		//disable ads in ezAdsense
		if(class_exists("ezAdSense"))
		{
			global $ezCount, $urCount;
			$ezCount = 100;
			$urCount = 100;
		}

		//disable ads in Easy Adsense (newer versions)
		if(class_exists("EzAdSense"))
		{
			global $ezAdSense;
			$ezAdSense->ezCount = 100;
			$ezAdSense->urCount = 100;
		}

		//set a global variable to hide ads
		global $pmpro_display_ads;
		$pmpro_display_ads = false;
	}
	else
	{
		global $pmpro_display_ads;
		$pmpro_display_ads = true;
	}

	do_action("pmpro_after_set_current_user");
}
add_action('set_current_user', 'pmpro_set_current_user');
add_action('init', 'pmpro_set_current_user');

/*
 * Add Membership Level to Users page in WordPress dashboard.
 */
function pmpro_manage_users_columns($columns) {
    $columns['pmpro_membership_level'] = __('Membership Level', 'paid-memberships-pro' );
    return $columns;
}

function pmpro_sortable_column($columns)
{
	$columns['pmpro_membership_level'] = array( 'level', 'desc' );
	return $columns;
}

function pmpro_manage_users_custom_column($column_data, $column_name, $user_id) {

    if($column_name == 'pmpro_membership_level') {
        $levels = pmpro_getMembershipLevelsForUser($user_id);
        $level_names = array();
        if(!empty($levels)) {
            foreach($levels as $key => $level)
                $level_names[] = $level->name;
            $column_data = implode(', ', $level_names);
        }
        else
            $column_data = __('None', 'paid-memberships-pro' );
    }
    return $column_data;
}

function pmpro_sortable_column_query( $query ) {
    global $wpdb;

	$vars = $query->query_vars;

	if ( $vars['orderby'] == 'level' ){
		$order = pmpro_sanitize_with_safelist( $vars['order'], array( 'asc', 'desc', 'ASC', 'DESC' ) );

		if ( ! empty( $order ) ) {
			$query->query_from .= " LEFT JOIN $wpdb->pmpro_memberships_users AS pmpro_mu ON $wpdb->users.ID = pmpro_mu.user_id AND pmpro_mu.status = 'active' LEFT JOIN $wpdb->pmpro_membership_levels AS pmpro_ml ON pmpro_mu.membership_id = pmpro_ml.id";
			$query->query_orderby = "ORDER BY pmpro_ml.name " . esc_sql( $order ) . ", $wpdb->users.user_registered";
		}
	}
}

add_filter('manage_users_columns', 'pmpro_manage_users_columns');
add_filter('manage_users_custom_column', 'pmpro_manage_users_custom_column', 10, 3);
add_filter( 'manage_users_sortable_columns', 'pmpro_sortable_column' );
add_action('pre_user_query','pmpro_sortable_column_query');
