<?php
//only admins can get this
if (!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_pagesettings"))) {
    die(__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
}

global $wpdb, $msg, $msgt;

//get/set settings
global $pmpro_pages;

/**
 * Adds additional page settings for use with add-on plugins, etc.
 *
 * @param array $pages {
 *     Formatted as array($name => $label)
 *
 *     @type string $name Page name. (Letters, numbers, and underscores only.)
 *     @type string $label Settings label.
 * }
 * @since 1.8.5
 */
$extra_pages = apply_filters('pmpro_extra_page_settings', array());
$post_types = apply_filters('pmpro_admin_pagesetting_post_type_array', array( 'page' ) );

//check nonce for saving settings
if (!empty($_REQUEST['savesettings']) && (empty($_REQUEST['pmpro_pagesettings_nonce']) || !check_admin_referer('savesettings', 'pmpro_pagesettings_nonce'))) {
	$msg = -1;
	$msgt = __("Are you sure you want to do that? Try again.", 'paid-memberships-pro' );
	unset($_REQUEST['savesettings']);
}

if (!empty($_REQUEST['savesettings'])) {
    //page ids
    pmpro_setOption("account_page_id", NULL, 'intval');
    pmpro_setOption("billing_page_id", NULL, 'intval');
    pmpro_setOption("cancel_page_id", NULL, 'intval');
    pmpro_setOption("checkout_page_id", NULL, 'intval');
    pmpro_setOption("confirmation_page_id", NULL, 'intval');
    pmpro_setOption("invoice_page_id", NULL, 'intval');
    pmpro_setOption("levels_page_id", NULL, 'intval');
    pmpro_setOption("login_page_id", NULL, 'intval');
	pmpro_setOption("member_profile_edit_page_id", NULL, 'intval');

    //update the pages array
    $pmpro_pages["account"] = pmpro_getOption("account_page_id");
    $pmpro_pages["billing"] = pmpro_getOption("billing_page_id");
    $pmpro_pages["cancel"] = pmpro_getOption("cancel_page_id");
    $pmpro_pages["checkout"] = pmpro_getOption("checkout_page_id");
    $pmpro_pages["confirmation"] = pmpro_getOption("confirmation_page_id");
    $pmpro_pages["invoice"] = pmpro_getOption("invoice_page_id");
    $pmpro_pages["levels"] = pmpro_getOption("levels_page_id");
	$pmpro_pages["login"] = pmpro_getOption("login_page_id");
    $pmpro_pages['member_profile_edit'] = pmpro_getOption( 'member_profile_edit_page_id' );

    //save additional pages
    if (!empty($extra_pages)) {
        foreach ($extra_pages as $name => $label) {
            pmpro_setOption($name . '_page_id', NULL, 'intval');
            $pmpro_pages[$name] = pmpro_getOption($name . '_page_id');
        }
    }

    //assume success
    $msg = true;
    $msgt = __("Your page settings have been updated.", 'paid-memberships-pro' );
}

//check nonce for generating pages
if (!empty($_REQUEST['createpages']) && (empty($_REQUEST['pmpro_pagesettings_nonce']) || !check_admin_referer('createpages', 'pmpro_pagesettings_nonce'))) {
	$msg = -1;
	$msgt = __("Are you sure you want to do that? Try again.", 'paid-memberships-pro' );
	unset($_REQUEST['createpages']);
}

//are we generating pages?
if (!empty($_REQUEST['createpages'])) {

    $pages = array();

	/**
	 * These pages were added later, and so we take extra
	 * care to make sure we only generate one version of them.
	 */
	$generate_once = array(
		'member_profile_edit' => __( 'Your Profile', 'paid-memberships-pro' ),
		'login' => 'Log In',
	);

    if(empty($_REQUEST['page_name'])) {
        //default pages
        $pages['account'] = __('Membership Account', 'paid-memberships-pro' );
        $pages['billing'] = __('Membership Billing', 'paid-memberships-pro' );
        $pages['cancel'] = __('Membership Cancel', 'paid-memberships-pro' );
        $pages['checkout'] = __('Membership Checkout', 'paid-memberships-pro' );
        $pages['confirmation'] = __('Membership Confirmation', 'paid-memberships-pro' );
        $pages['invoice'] = __('Membership Invoice', 'paid-memberships-pro' );
        $pages['levels'] = __('Membership Levels', 'paid-memberships-pro' );
		$pages['login'] = __('Log In', 'paid-memberships-pro' );
		$pages['member_profile_edit'] = __('Your Profile', 'paid-memberships-pro' );
	} elseif ( in_array( $_REQUEST['page_name'], array_keys( $generate_once ) ) ) {
		$page_name = sanitize_text_field( $_REQUEST['page_name'] );
		if ( ! empty( pmpro_getOption( $page_name . '_page_generated' ) ) ) {
			// Don't generate again.
			unset( $pages[$page_name] );

			// Find the old page
			$old_page = get_page_by_path( $page_name );
			if ( ! empty( $old_page ) ) {
				$pmpro_pages[$page_name] = $old_page->ID;
				pmpro_setOption( $page_name . '_page_id', $old_page->ID );
				pmpro_setOption( $page_name . '_page_generated', '1' );
				$msg = true;
				$msgt = sprintf( __( "Found an existing version of the %s page and used that one.", 'paid-memberships-pro' ), $page_name );
			} else {
				$msg = -1;
				$msgt = sprintf( __( "Error generating the %s page. You will have to choose or create one manually.", 'paid-memberships-pro' ), $page_name );
			}
		} else {
			// Generate the new Your Profile page and save an option that it was created.
			$pages[$page_name] = array(
				'title' => $generate_once[$page_name],
				'content' => '[pmpro_' . $page_name . ']',
			);
			pmpro_setOption( $page_name . '_page_generated', '1' );
		}
    } else {
        //generate extra pages one at a time
        $pmpro_page_name = sanitize_text_field($_REQUEST['page_name']);
        $pmpro_page_id = $pmpro_pages[$pmpro_page_name];
        $pages[$pmpro_page_name] = $extra_pages[$pmpro_page_name];
    }

    $pages_created = pmpro_generatePages($pages);

    if (!empty($pages_created)) {
        $msg = true;
        $msgt = __("The following pages have been created for you", 'paid-memberships-pro' ) . ": " . implode(", ", $pages_created) . ".";
    }
}

require_once(dirname(__FILE__) . "/admin_header.php");
?>


    <form action="<?php echo admin_url('admin.php?page=pmpro-pagesettings');?>" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('savesettings', 'pmpro_pagesettings_nonce');?>

        <h1 class="wp-heading-inline"><?php esc_html_e( 'Page Settings', 'paid-memberships-pro' ); ?></h1>
        <hr class="wp-header-end">
        <?php
		// check if we have all pages
		if ( $pmpro_pages['account'] ||
			$pmpro_pages['billing'] ||
			$pmpro_pages['cancel'] ||
			$pmpro_pages['checkout'] ||
			$pmpro_pages['confirmation'] ||
			$pmpro_pages['invoice'] ||
			$pmpro_pages['levels'] ||
			$pmpro_pages['member_profile_edit'] ) {
			$pmpro_some_pages_ready = true;
		} else {
			$pmpro_some_pages_ready = false;
		}

        if ( $pmpro_some_pages_ready ) { ?>
            <p><?php _e('Manage the WordPress pages assigned to each required Paid Memberships Pro page.', 'paid-memberships-pro' ); ?></p>
        <?php } elseif( ! empty( $_REQUEST['manualpages'] ) ) { ?>
            <p><?php _e('Assign the WordPress pages for each required Paid Memberships Pro page or', 'paid-memberships-pro' ); ?> <a
                    href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=pmpro-pagesettings&createpages=1' ), 'createpages', 'pmpro_pagesettings_nonce');?>"><?php _e('click here to let us generate them for you', 'paid-memberships-pro' ); ?></a>.
            </p>
        <?php } else { ?>
            <div class="pmpro-new-install">
                <h2><?php echo esc_attr_e( 'Manage Pages', 'paid-memberships-pro' ); ?></h2>
                <h4><?php echo esc_attr_e( 'Several frontend pages are required for your Paid Memberships Pro site.', 'paid-memberships-pro' ); ?></h4>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=pmpro-pagesettings&createpages=1'), 'createpages', 'pmpro_pagesettings_nonce' ); ?>" class="button-primary"><?php echo esc_attr_e( 'Generate Pages For Me', 'paid-memberships-pro' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-pagesettings&manualpages=1' ) ); ?>" class="button"><?php echo esc_attr_e( 'Create Pages Manually', 'paid-memberships-pro' ); ?></a>
            </div> <!-- end pmpro-new-install -->
        <?php } ?>

        <?php if ( ! empty( $pmpro_some_pages_ready ) || ! empty( $_REQUEST['manualpages'] ) ) { ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row" valign="top">
                    <label for="account_page_id"><?php _e('Account Page', 'paid-memberships-pro' ); ?>:</label>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array("name" => "account_page_id", "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro' ) . " --", "selected" => $pmpro_pages['account']));
                    ?>
                    <?php if (!empty($pmpro_pages['account'])) { ?>
                        <a target="_blank" href="post.php?post=<?php echo $pmpro_pages['account']; ?>&action=edit"
                           class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                        &nbsp;
                        <a target="_blank" href="<?php echo get_permalink($pmpro_pages['account']); ?>"
                           class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php _e('Include the shortcode', 'paid-memberships-pro' ); ?> [pmpro_account] <?php _e('or the Membership Account block', 'paid-memberships-pro' ); ?>.</p>
                </td>
            <tr>
                <th scope="row" valign="top">
                    <label for="billing_page_id"><?php _e('Billing Information Page', 'paid-memberships-pro' ); ?>:</label>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array("name" => "billing_page_id", "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro' ) . " --", "selected" => $pmpro_pages['billing']));
                    ?>
                    <?php if (!empty($pmpro_pages['billing'])) { ?>
                        <a target="_blank" href="post.php?post=<?php echo $pmpro_pages['billing'] ?>&action=edit"
                           class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                        &nbsp;
                        <a target="_blank" href="<?php echo get_permalink($pmpro_pages['billing']); ?>"
                           class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php _e('Include the shortcode', 'paid-memberships-pro' ); ?> [pmpro_billing] <?php _e('or the Membership Billing block', 'paid-memberships-pro' ); ?>.</p>
                </td>
            <tr>
                <th scope="row" valign="top">
                    <label for="cancel_page_id"><?php _e('Cancel Page', 'paid-memberships-pro' ); ?>:</label>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array("name" => "cancel_page_id", "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro') . " --", "selected" => $pmpro_pages['cancel'], "post_types" => $post_types ) );
                    ?>
                    <?php if (!empty($pmpro_pages['cancel'])) { ?>
                        <a target="_blank" href="post.php?post=<?php echo $pmpro_pages['cancel'] ?>&action=edit"
                           class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                        &nbsp;
                        <a target="_blank" href="<?php echo get_permalink($pmpro_pages['cancel']); ?>"
                           class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php _e('Include the shortcode', 'paid-memberships-pro' ); ?> [pmpro_cancel] <?php _e('or the Membership Cancel block', 'paid-memberships-pro' ); ?>.</p>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">
                    <label for="checkout_page_id"><?php _e('Checkout Page', 'paid-memberships-pro' ); ?>:</label>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array("name" => "checkout_page_id", "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro') . " --", "selected" => $pmpro_pages['checkout'], "post_types" => $post_types ));
                    ?>
                    <?php if (!empty($pmpro_pages['checkout'])) { ?>
                        <a target="_blank" href="post.php?post=<?php echo $pmpro_pages['checkout'] ?>&action=edit"
                           class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                        &nbsp;
                        <a target="_blank" href="<?php echo get_permalink($pmpro_pages['checkout']); ?>"
                           class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php _e('Include the shortcode', 'paid-memberships-pro' ); ?> [pmpro_checkout] <?php _e('or the Membership Checkout block', 'paid-memberships-pro' ); ?>.</p>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">
                    <label for="confirmation_page_id"><?php _e('Confirmation Page', 'paid-memberships-pro' ); ?>:</label>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array("name" => "confirmation_page_id", "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro') . " --", "selected" => $pmpro_pages['confirmation'], "post_types" => $post_types));
                    ?>
                    <?php if (!empty($pmpro_pages['confirmation'])) { ?>
                        <a target="_blank" href="post.php?post=<?php echo $pmpro_pages['confirmation'] ?>&action=edit"
                           class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                        &nbsp;
                        <a target="_blank" href="<?php echo get_permalink($pmpro_pages['confirmation']); ?>"
                           class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php _e('Include the shortcode', 'paid-memberships-pro' ); ?> [pmpro_confirmation] <?php _e('or the Membership Confirmation block', 'paid-memberships-pro' ); ?>.</p>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">
                    <label for="invoice_page_id"><?php _e('Invoice Page', 'paid-memberships-pro' ); ?>:</label>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array("name" => "invoice_page_id", "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro') . " --", "selected" => $pmpro_pages['invoice'], "post_types" => $post_types));
                    ?>
                    <?php if (!empty($pmpro_pages['invoice'])) { ?>
                        <a target="_blank" href="post.php?post=<?php echo $pmpro_pages['invoice'] ?>&action=edit"
                           class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                        &nbsp;
                        <a target="_blank" href="<?php echo get_permalink($pmpro_pages['invoice']); ?>"
                           class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php _e('Include the shortcode', 'paid-memberships-pro' ); ?> [pmpro_invoice] <?php _e('or the Membership Invoice block', 'paid-memberships-pro' ); ?>.</p>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">
                    <label for="levels_page_id"><?php _e('Levels Page', 'paid-memberships-pro' ); ?>:</label>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array("name" => "levels_page_id", "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro') . " --", "selected" => $pmpro_pages['levels'], "post_types" => $post_types));
                    ?>
                    <?php if (!empty($pmpro_pages['levels'])) { ?>
                        <a target="_blank" href="post.php?post=<?php echo $pmpro_pages['levels'] ?>&action=edit"
                           class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                        &nbsp;
                        <a target="_blank" href="<?php echo get_permalink($pmpro_pages['levels']); ?>"
                           class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php _e('Include the shortcode', 'paid-memberships-pro' ); ?> [pmpro_levels] <?php _e('or the Membership Levels block', 'paid-memberships-pro' ); ?>.</p>

					<?php if ( ! function_exists( 'pmpro_advanced_levels_shortcode' ) ) {
						$allowed_advanced_levels_html = array (
							'a' => array (
							'href' => array(),
							'target' => array(),
							'title' => array(),
						),
					);
					echo '<br /><p class="description">' . sprintf( wp_kses( __( 'Optional: Customize your Membership Levels page using the <a href="%s" title="Paid Memberships Pro - Advanced Levels Page Add On" target="_blank">Advanced Levels Page Add On</a>.', 'paid-memberships-pro' ), $allowed_advanced_levels_html ), 'https://www.paidmembershipspro.com/add-ons/pmpro-advanced-levels-shortcode/?utm_source=plugin&utm_medium=pmpro-pagesettings&utm_campaign=add-ons&utm_content=pmpro-advanced-levels-shortcode' ) . '</p>';
					} ?>
                </td>
            </tr>
			<tr>
				<th scope="row" valign="top">
					<label for="login_page_id"><?php esc_attr_e( 'Log In Page', 'paid-memberships-pro' ); ?>:</label>
				</th>
				<td>
					<?php
						wp_dropdown_pages(
							array(
								'name' => 'login_page_id',
								'show_option_none' => '-- ' . __('Use WordPress Default', 'paid-memberships-pro') . ' --',
								'selected' => $pmpro_pages['login'], 'post_types' => $post_types
							)
						);
					?>

					<?php if ( ! empty( $pmpro_pages['login'] ) ) { ?>
						<a target="_blank" href="post.php?post=<?php echo $pmpro_pages['login'] ?>&action=edit"
			               class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
			            &nbsp;
			            <a target="_blank" href="<?php echo get_permalink($pmpro_pages['login']); ?>"
			               class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
			        <?php } elseif ( empty( pmpro_getOption( 'login_page_generated' ) ) ) { ?>
						&nbsp;
						<a href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'pmpro-pagesettings', 'createpages' => 1, 'page_name' => esc_attr( 'login' )   ), admin_url('admin.php') ), 'createpages', 'pmpro_pagesettings_nonce' ); ?>"><?php _e('Generate Page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php printf( esc_html__('Include the shortcode %s or the Log In Form block.', 'paid-memberships-pro' ), '[pmpro_login]' ); ?></p>
			    </td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="member_profile_edit_page_id"><?php esc_attr_e( 'Member Profile Edit Page', 'paid-memberships-pro' ); ?>:</label>
				</th>
				<td>
					<?php
						wp_dropdown_pages(
							array(
								'name' => 'member_profile_edit_page_id',
								'show_option_none' => '-- ' . __('Use WordPress Default', 'paid-memberships-pro') . ' --',
								'selected' => $pmpro_pages['member_profile_edit'], 'post_types' => $post_types
							)
						);
					?>

					<?php if ( ! empty( $pmpro_pages['member_profile_edit'] ) ) { ?>
						<a target="_blank" href="post.php?post=<?php echo $pmpro_pages['member_profile_edit'] ?>&action=edit"
			               class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
			            &nbsp;
			            <a target="_blank" href="<?php echo get_permalink($pmpro_pages['member_profile_edit']); ?>"
			               class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
			        <?php } elseif ( empty( pmpro_getOption( 'member_profile_edit_page_generated' ) ) ) { ?>
						&nbsp;
						<a href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'pmpro-pagesettings', 'createpages' => 1, 'page_name' => esc_attr( 'member_profile_edit' )   ), admin_url('admin.php') ), 'createpages', 'pmpro_pagesettings_nonce' ); ?>"><?php _e('Generate Page', 'paid-memberships-pro' ); ?></a>
                    <?php } ?>
					<p class="description"><?php printf( esc_html__('Include the shortcode %s or the Member Profile Edit block.', 'paid-memberships-pro' ), '[pmpro_member_profile_edit]' ); ?></p>

					<?php if ( ! class_exists( 'PMProRH_Field' ) ) {
						$allowed_member_profile_edit_html = array (
							'a' => array (
							'href' => array(),
							'target' => array(),
							'title' => array(),
						),
					);
					echo '<br /><p class="description">' . sprintf( wp_kses( __( 'Optional: Collect additional member fields at checkout, on the profile, or for admin-use only using the <a href="%s" title="Paid Memberships Pro - Register Helper Add On" target="_blank">Register Helper Add On</a>.', 'paid-memberships-pro' ), $allowed_member_profile_edit_html ), 'https://www.paidmembershipspro.com/add-ons/pmpro-register-helper-add-checkout-and-profile-fields/?utm_source=plugin&utm_medium=pmpro-pagesettings&utm_campaign=add-ons&utm_content=pmpro-register-helper' ) . '</p>';
					} ?>
			    </td>
			</tr>
            </tbody>
        </table>

        <?php
        if (!empty($extra_pages)) { ?>
            <h2><?php _e('Additional Page Settings', 'paid-memberships-pro' ); ?></h2>
            <table class="form-table">
                <tbody>
                <?php foreach ($extra_pages as $name => $page) { ?>
                    <?php
						if(is_array($page)) {
							$label = $page['title'];
							if(!empty($page['hint']))
								$hint = $page['hint'];
							else
								$hint = '';
						} else {
							$label = $page;
							$hint = '';
						}
					?>
					<tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $name; ?>_page_id"><?php echo $label; ?></label>
                        </th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                "name" => $name . '_page_id',
                                "show_option_none" => "-- " . __('Choose One', 'paid-memberships-pro' ) . " --",
                                "selected" => $pmpro_pages[$name],
                            ));
                            if(!empty($pmpro_pages[$name])) {
                                ?>
                                <a target="_blank" href="post.php?post=<?php echo $pmpro_pages[$name] ?>&action=edit"
                                   class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
                                &nbsp;
                                <a target="_blank" href="<?php echo get_permalink($pmpro_pages[$name]); ?>"
                                   class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
                            <?php } else { ?>
                                &nbsp;
                                <a href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'pmpro-pagesettings', 'createpages' => 1, 'page_name' => esc_attr( $name ) ), admin_url('admin.php') ), 'createpages', 'pmpro_pagesettings_nonce' ); ?>"><?php _e('Generate Page', 'paid-memberships-pro' ); ?></a>
                            <?php } ?>
							<?php if(!empty($hint)) { ?>
								<p class="description"><?php echo $hint;?></p>
							<?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
        <p class="submit">
            <input name="savesettings" type="submit" class="button button-primary"
                   value="<?php _e('Save Settings', 'paid-memberships-pro' ); ?>"/>
        </p>
        <?php } ?>
    </form>

<?php
require_once(dirname(__FILE__) . "/admin_footer.php");
?>
