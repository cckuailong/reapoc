<?php
/**
 * PublishPress Capabilities [Free]
 *
 * UI output for Capabilities screen.
 *
 * Provides admin pages to create and manage roles and capabilities.
 *
 * @author		Jordi Canals, Kevin Behrens
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals, (C) 2020 PublishPress
 * @license		GNU General Public License version 2
 * @link		https://publishpress.com
 *
 *	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>
 *	Modifications Copyright 2020, PublishPress <help@publishpress.com>
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	version 2 as published by the Free Software Foundation.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

global $capsman, $cme_cap_helper, $current_user;

do_action('publishpress-caps_manager-load');

$roles = $this->roles;
$default = $this->current;

if ( $block_read_removal = _cme_is_read_removal_blocked( $this->current ) ) {
	if ( $current = get_role($default) ) {
		if ( empty( $current->capabilities['read'] ) ) {
			ak_admin_error( sprintf( __( 'Warning: This role cannot access the dashboard without the read capability. %1$sClick here to fix this now%2$s.', 'capsman-enhanced' ), '<a href="javascript:void(0)" class="cme-fix-read-cap">', '</a>' ) );
		}
	}
}

require_once (dirname(CME_FILE) . '/includes/roles/roles-functions.php');

require_once( dirname(__FILE__).'/pp-ui.php' );
$pp_ui = new Capsman_PP_UI();

if( defined('PRESSPERMIT_ACTIVE') ) {
	$pp_metagroup_caps = $pp_ui->get_metagroup_caps( $default );
} else {
	$pp_metagroup_caps = array();
}
?>
<div class="wrap publishpress-caps-manage pressshack-admin-wrapper">
	<?php /*if( defined('PRESSPERMIT_ACTIVE') ) :
		pp_icon();
		$style = 'style="height:60px;"';
		*/
	?>
	<?php /* else: */
		$style = '';
	?>
	<div id="icon-capsman-admin" class="icon32"></div>
	<?php /* endif; */ ?>

	<h1 <?php echo $style;?>><?php _e('Role Capabilities', 'capsman-enhanced') ?></h1>

	<?php
	echo pp_capabilities_roles()->notify->display();
	?>

	<script type="text/javascript">
	/* <![CDATA[ */
	jQuery(document).ready( function($) {
		$('#publishpress_caps_form').attr('action', 'admin.php?page=pp-capabilities&role=' + $('select[name="role"]').val());

		$('select[name="role"]').change(function(){
			window.location = '<?php echo admin_url('admin.php?page=pp-capabilities&role='); ?>' + $(this).val() + '';
		});
	});
	/* ]]> */
	</script>

	<form id="publishpress_caps_form" method="post" action="admin.php?page=<?php echo $this->ID ?>">
	<?php wp_nonce_field('capsman-general-manager'); ?>

	<?php 
	$tab = (!empty($_REQUEST['pp_caps_tab'])) ? sanitize_key($_REQUEST['pp_caps_tab']) : 'edit';
	?>

	<input type="hidden" name="pp_caps_tab" value="<?php echo $tab;?>" />

	<p>
		<select name="role">
			<?php
			foreach ( $roles as $role => $name ) {
				if (pp_capabilities_is_editable_role($role)) {
					$name = translate_user_role($name);
					echo '<option value="' . $role .'"'; selected($default, $role); echo '> ' . $name . ' &nbsp;</option>';
				}
			}
			?>
		</select>
	</p>

	<fieldset>
	<table id="akmin">
	<tr>
		<td class="content">

			<div style="float:right">
			<input type="submit" name="SaveRole" value="<?php echo (in_array(get_locale(), ['en_EN', 'en_US'])) ? 'Save Capabilities' : _e('Save Changes', 'capsman-enhanced'); ?>" class="button-primary" />
			</div>

			<?php
			global $capsman;
			$img_url = $capsman->mod_url . '/images/';
			?>
			<div class="publishpress-headline" style="margin-bottom:20px;">
			<span class="cme-subtext">
			<?php
			$msg = __( '<strong>Note:</strong> Capability changes <strong>remain in the database</strong> after plugin deactivation.', 'capsman-enhanced' );

			if (defined('PRESSPERMIT_ACTIVE') && function_exists('presspermit')) {
				if ($group = presspermit()->groups()->getMetagroup('wp_role', $this->current)) {
					$msg = sprintf(
						__('<strong>Note:</strong> Capability changes <strong>remain in the database</strong> after plugin deactivation. You can also configure this role as a %sPermission Group%s.', 'capsman-enhanced'),
						'<a href="' . admin_url("admin.php?page=presspermit-edit-permissions&action=edit&agent_id={$group->ID}") . '">',
						'</a>'
					);
				}
			}
			echo $msg;
			?>
			</span>
			</div>

			<?php
			if ( defined( 'PRESSPERMIT_ACTIVE' ) ) {
				$pp_ui->show_capability_hints( $default );
			}

			if ( MULTISITE ) {
				global $wp_roles;
				global $wpdb;

				if ( ! empty($_REQUEST['cme_net_sync_role'] ) ) {
					$main_site_id = (function_exists('get_main_site_id')) ? get_main_site_id() : 1;
					switch_to_blog($main_site_id);
					wp_cache_delete( $wpdb->prefix . 'user_roles', 'options' );
				}

				( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
			}
			$capsman->reinstate_db_roles();

			$current = get_role($default);

			$rcaps = $current->capabilities;

			$is_administrator = current_user_can( 'administrator' ) || (is_multisite() && is_super_admin());

			$custom_types = get_post_types( array( '_builtin' => false ), 'names' );
			$custom_tax = get_taxonomies( array( '_builtin' => false ), 'names' );

			$defined = [];
			$defined['type'] = apply_filters('cme_filterable_post_types', get_post_types(['public' => true, 'show_ui' => true], 'object', 'or'));
			$defined['taxonomy'] = apply_filters('cme_filterable_taxonomies', get_taxonomies(['public' => true, 'show_ui' => true], 'object', 'or'));

			// bbPress' dynamic role def requires additional code to enforce stored caps
			$unfiltered['type'] = apply_filters('presspermit_unfiltered_post_types', ['forum','topic','reply','wp_block']);
			$unfiltered['type'] = (defined('PP_CAPABILITIES_NO_LEGACY_FILTERS')) ? $unfiltered['type'] : apply_filters('pp_unfiltered_post_types', $unfiltered['type']);

			$unfiltered['taxonomy'] = apply_filters('presspermit_unfiltered_post_types', ['post_status', 'topic-tag']);  // avoid confusion with Edit Flow administrative taxonomy
			$unfiltered['taxonomy'] = (defined('PP_CAPABILITIES_NO_LEGACY_FILTERS')) ? $unfiltered['taxonomy'] : apply_filters('pp_unfiltered_taxonomies', $unfiltered['taxonomy']);

			$enabled_taxonomies = cme_get_assisted_taxonomies();

			/*
			if ( ( count($custom_types) || count($custom_tax) ) && ( $is_administrator || current_user_can( 'manage_pp_settings' ) ) ) {
				$cap_properties[''] = array();
				$force_distinct_ui = true;
			}
			*/

			$cap_properties['edit']['type'] = array( 'edit_posts' );

			foreach( $defined['type'] as $type_obj ) {
				if ( 'attachment' != $type_obj->name ) {
					if ( isset( $type_obj->cap->create_posts ) && ( $type_obj->cap->create_posts != $type_obj->cap->edit_posts ) ) {
						$cap_properties['edit']['type'][]= 'create_posts';
						break;
					}
				}
			}

			$cap_properties['edit']['type'][]= 'edit_others_posts';
			$cap_properties['edit']['type'] = array_merge( $cap_properties['edit']['type'], array( 'publish_posts', 'edit_published_posts', 'edit_private_posts' ) );

			$cap_properties['edit']['taxonomy'] = array( 'manage_terms' );

			if ( ! defined( 'OLD_PRESSPERMIT_ACTIVE' ) )
				$cap_properties['edit']['taxonomy'] = array_merge( $cap_properties['edit']['taxonomy'], array( 'edit_terms', 'assign_terms' ) );

			$cap_properties['delete']['type'] = array( 'delete_posts', 'delete_others_posts' );
			$cap_properties['delete']['type'] = array_merge( $cap_properties['delete']['type'], array( 'delete_published_posts', 'delete_private_posts' ) );

			if ( ! defined( 'OLD_PRESSPERMIT_ACTIVE' ) )
				$cap_properties['delete']['taxonomy'] = array( 'delete_terms' );
			else
				$cap_properties['delete']['taxonomy'] = array();

			$cap_properties['read']['type'] = array( 'read_private_posts' );
			$cap_properties['read']['taxonomy'] = array();

			$stati = get_post_stati( array( 'internal' => false ) );

			$cap_type_names = array(
				'' => __( '&nbsp;', 'capsman-enhanced' ),
				'read' => __( 'Reading', 'capsman-enhanced' ),
				'edit' => __( 'Editing', 'capsman-enhanced' ),
				'delete' => __( 'Deletion', 'capsman-enhanced' )
			);

			$cap_tips = array(
				'read_private' => __( 'can read posts which are currently published with private visibility', 'capsman-enhanced' ),
				'edit' => __( 'has basic editing capability (but may need other capabilities based on post status and ownership)', 'capsman-enhanced' ),
				'edit_others' => __( 'can edit posts which were created by other users', 'capsman-enhanced' ),
				'edit_published' => __( 'can edit posts which are currently published', 'capsman-enhanced' ),
				'edit_private' => __( 'can edit posts which are currently published with private visibility', 'capsman-enhanced' ),
				'publish' => __( 'can make a post publicly visible', 'capsman-enhanced' ),
				'delete' => __( 'has basic deletion capability (but may need other capabilities based on post status and ownership)', 'capsman-enhanced' ),
				'delete_others' => __( 'can delete posts which were created by other users', 'capsman-enhanced' ),
				'delete_published' => __( 'can delete posts which are currently published', 'capsman-enhanced' ),
				'delete_private' => __( 'can delete posts which are currently published with private visibility', 'capsman-enhanced' ),
			);

			$default_caps = array( 'read_private_posts', 'edit_posts', 'edit_others_posts', 'edit_published_posts', 'edit_private_posts', 'publish_posts', 'delete_posts', 'delete_others_posts', 'delete_published_posts', 'delete_private_posts',
								   'read_private_pages', 'edit_pages', 'edit_others_pages', 'edit_published_pages', 'edit_private_pages', 'publish_pages', 'delete_pages', 'delete_others_pages', 'delete_published_pages', 'delete_private_pages',
								   'manage_categories'
								   );
			$type_caps = array();
			$type_metacaps = array();

			// Role Scoper and PP1 adjust attachment access based only on user's capabilities for the parent post
			if ( defined('OLD_PRESSPERMIT_ACTIVE') ) {
				unset( $defined['type']['attachment'] );
			}
			?>

			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready( function($) {
				// Tabs and Content display
				$('.ppc-capabilities-tabs > ul > li').click( function() {
					var $tab = $(this).attr('data-content');
					//console.log( $tab );

					$("[name='pp_caps_tab']").val($(this).attr('data-slug'));

					// Show current Content
					$('.ppc-capabilities-content > div').hide();
					$('#' + $tab).show();

					$('#' + $tab + '-taxonomy').show();

					// Active current Tab
					$('.ppc-capabilities-tabs > ul > li').removeClass('ppc-capabilities-tab-active');
					$(this).addClass('ppc-capabilities-tab-active');
				});
			});
			/* ]]> */
			</script>

			<div id="ppc-capabilities-wrapper" class="postbox">
				<div class="ppc-capabilities-tabs">
					<ul>
						<?php
						$active_tab_slug = (!empty($_REQUEST['pp_caps_tab'])) ? sanitize_key($_REQUEST['pp_caps_tab']) : 'edit';
						$active_tab_id = "cme-cap-type-tables-{$active_tab_slug}";

						$ppc_tab_active = ' class="ppc-capabilities-tab-active"';

						// caps: edit, delete, read
						foreach( array_keys($cap_properties) as $cap_type ) {
							$tab_id = "cme-cap-type-tables-$cap_type";
							$tab_active = ($tab_id == $active_tab_id) ? $ppc_tab_active : '';

							echo '<li data-slug="'. $cap_type . '"' . ' data-content="cme-cap-type-tables-' . $cap_type . '"' . $tab_active . '>'
								. $cap_type_names[$cap_type] .
							'</li>';
						}

						if ($extra_tabs = apply_filters('pp_capabilities_extra_post_capability_tabs', [])) {
							foreach($extra_tabs as $tab_slug => $tab_caption) {
								$tab_id = "cme-cap-type-tables-{$tab_slug}";
								$tab_active = ($tab_id == $active_tab_id) ? $ppc_tab_active : '';

								echo '<li data-slug="' . $tab_slug . '"' . ' data-content="' . $tab_id . '"' . $tab_active . '>'
								. $tab_caption .
								'</li>';
							}
						}

						// caps: other
						$tab_id = "cme-cap-type-tables-other";
						$tab_active = ($tab_id == $active_tab_id) ? $ppc_tab_active : '';
						$tab_caption = __( 'WordPress Core', 'capsman-enhanced' );

						echo '<li data-slug="other" data-content="' . $tab_id . '"' . $tab_active . '>' . $tab_caption . '</li>';

						// caps: plugins
						$plugin_caps = [];
						if (defined('PUBLISHPRESS_VERSION')) {
							$plugin_caps['PublishPress'] = apply_filters('cme_publishpress_capabilities',
								array(
								'edit_metadata',
								'edit_post_subscriptions',
								'pp_manage_roles',
								'pp_set_notification_channel',
								'pp_view_calendar',
								'pp_view_content_overview',
								)
							);
						}

						if (defined('PUBLISHPRESS_MULTIPLE_AUTHORS_VERSION')) {
							if ($_caps = apply_filters('cme_multiple_authors_capabilities', array())) {
								$plugin_caps['PublishPress Authors'] = $_caps;
							}
						}

						if (defined('PRESSPERMIT_VERSION')) {
							$plugin_caps['PublishPress Permissions'] = apply_filters('cme_presspermit_capabilities',
								array(
								'edit_own_attachments',
								'list_others_unattached_files',
								'pp_administer_content',
								'pp_assign_roles',
								'pp_associate_any_page',
								'pp_create_groups',
								'pp_create_network_groups',
								'pp_define_moderation',
								'pp_define_post_status',
								'pp_define_privacy',
								'pp_delete_groups',
								'pp_edit_groups',
								'pp_exempt_edit_circle',
								'pp_exempt_read_circle',
								'pp_force_quick_edit',
								'pp_list_all_files',
								'pp_manage_capabilities',
								'pp_manage_members',
								'pp_manage_network_members',
								'pp_manage_settings',
								'pp_moderate_any',
								'pp_set_associate_exceptions',
								'pp_set_edit_exceptions',
								'pp_set_read_exceptions',
								'pp_set_revise_exceptions',
								'pp_set_term_assign_exceptions',
								'pp_set_term_associate_exceptions',
								'pp_set_term_manage_exceptions',
								'pp_unfiltered',
								'set_posts_status',
								)
							);
						}

						if (defined('WC_PLUGIN_FILE')) {
							$plugin_caps['WooCommerce'] = apply_filters('cme_woocommerce_capabilities',
								array(
								'assign_product_terms',
								'assign_shop_coupon_terms',
								'assign_shop_discount_terms',
								'assign_shop_order_terms',
								'assign_shop_payment_terms',
								'create_shop_orders',
								'delete_others_products',
								'delete_others_shop_coupons',
								'delete_others_shop_discounts',
								'delete_others_shop_orders',
								'delete_others_shop_payments',
								'delete_private_products',
								'delete_private_shop_coupons',
								'delete_private_shop_orders',
								'delete_private_shop_discounts',
								'delete_private_shop_payments',
								'delete_product_terms',
								'delete_products',
								'delete_published_products',
								'delete_published_shop_coupons',
								'delete_published_shop_discounts',
								'delete_published_shop_orders',
								'delete_published_shop_payments',
								'delete_shop_coupons',
								'delete_shop_coupon_terms',
								'delete_shop_discount_terms',
								'delete_shop_discounts',
								'delete_shop_order_terms',
								'delete_shop_orders',
								'delete_shop_payments',
								'delete_shop_payment_terms',
								'edit_others_products',
								'edit_others_shop_coupons',
								'edit_others_shop_discounts',
								'edit_others_shop_orders',
								'edit_others_shop_payments',
								'edit_private_products',
								'edit_private_shop_coupons',
								'edit_private_shop_discounts',
								'edit_private_shop_orders',
								'edit_private_shop_payments',
								'edit_product_terms',
								'edit_products',
								'edit_published_products',
								'edit_published_shop_coupons',
								'edit_published_shop_discounts',
								'edit_published_shop_orders',
								'edit_published_shop_payments',
								'edit_shop_coupon_terms',
								'edit_shop_coupons',
								'edit_shop_discounts',
								'edit_shop_discount_terms',
								'edit_shop_order_terms',
								'edit_shop_orders',
								'edit_shop_payments',
								'edit_shop_payment_terms',
								'export_shop_payments',
								'export_shop_reports',
								'import_shop_discounts',
								'import_shop_payments',
								'manage_product_terms',
								'manage_shop_coupon_terms',
								'manage_shop_discounts',
								'manage_shop_discount_terms',
								'manage_shop_payment_terms',
								'manage_shop_order_terms',
								'manage_shop_settings',
								'manage_woocommerce',
								'publish_products',
								'publish_shop_coupons',
								'publish_shop_discounts',
								'publish_shop_orders',
								'publish_shop_payments',
								'read_private_products',
								'read_private_shop_coupons',
								'read_private_shop_discounts',
								'read_private_shop_payments',
								'read_private_shop_orders',
								'view_admin_dashboard',
								'view_shop_discount_stats',
								'view_shop_payment_stats',
								'view_shop_reports',
								'view_shop_sensitive_data',
								'view_woocommerce_reports',
								)
							);
						}
						$plugin_caps = apply_filters('cme_plugin_capabilities', $plugin_caps);
						foreach($plugin_caps as $plugin => $__plugin_caps) {
							$_plugin_caps = array_fill_keys($__plugin_caps, true);

							$tab_slug = str_replace(' ', '-', strtolower($plugin));
							$tab_id = 'cme-cap-type-tables-' . $tab_slug;
							$tab_active = ($tab_id == $active_tab_id) ? $ppc_tab_active : '';

							echo '<li data-slug="' . $tab_slug . '" data-content="' . $tab_id .'"' . $tab_active . '>'
								. str_replace('_', ' ', $plugin ) .
							'</li>';
						}

						$tab_id = "cme-cap-type-tables-invalid";
						$tab_active = ($tab_id == $active_tab_id) ? $ppc_tab_active : '';
						$tab_caption = __( 'Invalid Capabilities', 'capsman-enhanced' );
						echo '<li id="cme_tab_invalid_caps" data-slug="invalid" data-content="cme-cap-type-tables-' . $tab_id . '"' . $tab_active . ' style="display:none;">' . $tab_caption . '</li>';

						$tab_id = "cme-cap-type-tables-additional";
						$tab_active = ($tab_id == $active_tab_id) ? $ppc_tab_active : '';
						$tab_caption = __( 'Additional', 'capsman-enhanced' );
						echo '<li data-slug="additional" data-content="' . $tab_id . '"' . $tab_active . '>' . $tab_caption . '</li>';
						?>
					</ul>
				</div>
				<div class="ppc-capabilities-content">
					<?php
					// caps: read, edit, deletion
					foreach( array_keys($cap_properties) as $cap_type ) {

						foreach( array_keys($defined) as $item_type ) {
							if ( ( 'delete' == $cap_type ) && ( 'taxonomy' == $item_type ) ) {
								if ( defined('OLD_PRESSPERMIT_ACTIVE') ) {
									continue;
								}

								$any_term_deletion_caps = false;
								foreach( array_keys($defined['taxonomy']) as $_tax ) {
									if ( isset( $defined['taxonomy'][$_tax]->cap->delete_terms ) && ( 'manage_categories' != $defined['taxonomy'][$_tax]->cap->delete_terms ) && ! in_array( $_tax, $unfiltered['taxonomy'] ) ) {
										$any_term_deletion_caps = true;
										break;
									}
								}

								if ( ! $any_term_deletion_caps )
									continue;
							}

							if ( ! count( $cap_properties[$cap_type][$item_type] ) )
								continue;

							$id = "cme-cap-type-tables-$cap_type";
							$div_display = ($id == $active_tab_id) ? 'block' : 'none';

							$any_caps = false;

							if ($item_type == 'taxonomy') {
								$id .= '-taxonomy';

								ob_start();
							}

							echo "<div id='$id' style='display:$div_display;'>";
							
							$caption_pattern = ('taxonomy' == $item_type) ? __('Term %s Capabilities', 'capability-manager-enhanced') : __('Post %s Capabilities', 'capability-manager-enhanced');

							echo '<h3>' .  sprintf($caption_pattern, $cap_type_names[$cap_type]) . '</h3>';
							echo "<table class='widefat fixed striped cme-typecaps cme-typecaps-$cap_type'>";

							echo '<thead><tr><th></th>';

							// label cap properties
							foreach( $cap_properties[$cap_type][$item_type] as $prop ) {
								$prop = str_replace( '_posts', '', $prop );
								$prop = str_replace( '_pages', '', $prop );
								$prop = str_replace( '_terms', '', $prop );
								$tip = ( isset( $cap_tips[$prop] ) ) ? "title='{$cap_tips[$prop]}'" : '';
								$prop = str_replace( '_', '<br />', $prop );
								$th_class = ( 'taxonomy' == $item_type ) ? ' class="term-cap"' : ' class="post-cap"';
								echo "<th style='text-align:center;' $tip{$th_class}>";

								if ( ( 'delete' != $prop ) || ( 'taxonomy' != $item_type ) || cme_get_detailed_taxonomies() ) {
									echo ucwords($prop);
								}

								echo '</th>';
							}

							echo '</tr></thead>';

							foreach( $defined[$item_type] as $key => $type_obj ) {
								if ( in_array( $key, $unfiltered[$item_type] ) )
									continue;

								$row = "<tr class='cme_type_{$key}'>";

								if ( $cap_type ) {
									if ( empty($force_distinct_ui) && empty( $cap_properties[$cap_type][$item_type] ) )
										continue;

									$type_label = (defined('CME_LEGACY_MENU_NAME_LABEL') && !empty($type_obj->labels->menu_name)) ? $type_obj->labels->menu_name : $type_obj->labels->name;

									$row .= "<td><a class='cap_type' href='#toggle_type_caps'>" . $type_label . '</a>';
									$row .= '<a href="#" class="neg-type-caps">&nbsp;x&nbsp;</a>';
									$row .= '</td>';

									$display_row = ! empty($force_distinct_ui);
									$col_count = 0;

									foreach( $cap_properties[$cap_type][$item_type] as $prop ) {
										$td_classes = array();
										$checkbox = '';
										$title = '';

										if ( ! empty($type_obj->cap->$prop) && ( in_array( $type_obj->name, array( 'post', 'page' ) )
										|| ! in_array( $type_obj->cap->$prop, $default_caps )
										|| ( ( 'manage_categories' == $type_obj->cap->$prop ) && ( 'manage_terms' == $prop ) && ( 'category' == $type_obj->name ) ) ) ) {

											// if edit_published or edit_private cap is same as edit_posts cap, don't display a checkbox for it
											if ( ( ! in_array( $prop, array( 'edit_published_posts', 'edit_private_posts', 'create_posts' ) ) || ( $type_obj->cap->$prop != $type_obj->cap->edit_posts ) )
											&& ( ! in_array( $prop, array( 'delete_published_posts', 'delete_private_posts' ) ) || ( $type_obj->cap->$prop != $type_obj->cap->delete_posts ) )
											&& ( ! in_array( $prop, array( 'edit_terms', 'delete_terms' ) ) || ( $type_obj->cap->$prop != $type_obj->cap->manage_terms ) )

											&& ( ! in_array( $prop, array( 'manage_terms', 'edit_terms', 'delete_terms', 'assign_terms' ) )
												|| empty($cme_cap_helper->all_taxonomy_caps[$type_obj->cap->$prop])
												|| ( $cme_cap_helper->all_taxonomy_caps[ $type_obj->cap->$prop ] <= 1 )
												|| $type_obj->cap->$prop == str_replace( '_terms', "_{$type_obj->name}s", $prop )
												|| $type_obj->cap->$prop == str_replace( '_terms', "_" . _cme_get_plural($type_obj->name, $type_obj), $prop )
												)

											&& ( in_array( $prop, array( 'manage_terms', 'edit_terms', 'delete_terms', 'assign_terms' ) )
												|| empty($cme_cap_helper->all_type_caps[$type_obj->cap->$prop])
												|| ( $cme_cap_helper->all_type_caps[ $type_obj->cap->$prop ] <= 1 )
												|| $type_obj->cap->$prop == 'upload_files' && 'create_posts' == $prop && 'attachment' == $type_obj->name
												|| $type_obj->cap->$prop == str_replace( '_posts', "_{$type_obj->name}s", $prop )
												|| $type_obj->cap->$prop == str_replace( '_pages', "_{$type_obj->name}s", $prop )
												|| $type_obj->cap->$prop == str_replace( '_posts', "_" . _cme_get_plural($type_obj->name, $type_obj), $prop )
												|| $type_obj->cap->$prop == str_replace( '_pages', "_" . _cme_get_plural($type_obj->name, $type_obj), $prop )
												)
											) {
												// only present these term caps up top if we are ensuring that they get enforced separately from manage_terms
												if ( in_array( $prop, array( 'edit_terms', 'delete_terms', 'assign_terms' ) ) && ( ! in_array( $type_obj->name, cme_get_detailed_taxonomies() ) || defined( 'OLD_PRESSPERMIT_ACTIVE' ) ) ) {
													continue;
												}

												$cap_name = $type_obj->cap->$prop;

												if ( 'taxonomy' == $item_type )
													$td_classes []= "term-cap";
												else
													$td_classes []= "post-cap";

												if ( ! empty($pp_metagroup_caps[$cap_name]) )
													$td_classes []='cm-has-via-pp';

												if ( $is_administrator || current_user_can($cap_name) ) {
													if ( ! empty($pp_metagroup_caps[$cap_name]) ) {
														$title = ' title="' . sprintf( __( '%s: assigned by Permission Group', 'capsman-enhanced' ), $cap_name ) . '"';
													} else {
														$title = ' title="' . $cap_name . '"';
													}

													$disabled = '';
													$checked = checked(1, ! empty($rcaps[$cap_name]), false );

													$checkbox = '<input type="checkbox"' . $title . ' name="caps[' . $cap_name . ']" autocomplete="off" value="1" ' . $checked . $disabled . ' />';

													$type_caps [$cap_name] = true;
													$display_row = true;
													$any_caps = true;
												}
											} else {
												//$td_classes []= "cap-unreg";
												$title = 'title="' . sprintf( __( 'shared capability: %s', 'capsman-enhanced' ), esc_attr( $type_obj->cap->$prop ) ) . '"';
											}

											if ( isset($rcaps[$cap_name]) && empty($rcaps[$cap_name]) ) {
												$td_classes []= "cap-neg";
											}
										} else {
											$td_classes []= "cap-unreg";
										}

										$td_class = ( $td_classes ) ? 'class="' . implode(' ', $td_classes) . '"' : '';

										$row .= "<td $td_class $title><span class='cap-x'>X</span>$checkbox";

										if ( false !== strpos( $td_class, 'cap-neg' ) )
											$row .= '<input type="hidden" class="cme-negation-input" name="caps[' . $cap_name . ']" value="" />';

										$row .= "</td>";

										$col_count++;
									}

									if ('taxonomy' == $item_type) {
										for ($i = $col_count; $i < 3; $i++) {
											$row .= "<td></td>";
										}
									}

									if (!empty($type_obj->map_meta_cap) && !defined('PP_CAPABILITIES_NO_INVALID_SECTION')) {
										if ('type' == $item_type) {
											$type_metacaps[$type_obj->cap->read_post] = true;
											$type_metacaps[$type_obj->cap->edit_post] = isset($type_obj->cap->edit_posts) && ($type_obj->cap->edit_post != $type_obj->cap->edit_posts);
											$type_metacaps[$type_obj->cap->delete_post] = isset($type_obj->cap->delete_posts) && ($type_obj->cap->delete_post != $type_obj->cap->delete_posts);

										} elseif ('taxonomy' == $item_type && !empty($type_obj->cap->edit_term) && !empty($type_obj->cap->delete_term)) {
											$type_metacaps[$type_obj->cap->edit_term] = true;
											$type_metacaps[$type_obj->cap->delete_term] = true;
										}
									}
								}

								if ( $display_row ) {
									$row .= '</tr>';
									echo $row;
								}
							}

							echo '</table>';
							echo '</div>';

							if ($item_type == 'taxonomy') {
								if ($any_caps)  {
									ob_flush();
								} else {
									ob_clean();
								}
							}

						} // end foreach item type
					}

					do_action('publishpress-caps_manager_postcaps_section', compact('current', 'rcaps', 'pp_metagroup_caps', 'is_administrator', 'default_caps', 'custom_types', 'defined', 'unfiltered', 'pp_metagroup_caps','caps_manager_postcaps_section', 'active_tab_id'));

					$type_caps = apply_filters('publishpress_caps_manager_typecaps', $type_caps);

					// clicking on post type name toggles corresponding checkbox selections
					// caps: other

					$id = "cme-cap-type-tables-other";
					$div_display = ($id == $active_tab_id) ? 'block' : 'none';
					?>
					<div id="<?php echo $id;?>" style="display:<?php echo $div_display;?>">
						<?php

						echo '<h3>' . __( 'WordPress Core Capabilities', 'capsman-enhanced' ) . '</h3>';
						echo '<table class="widefat fixed striped form-table cme-checklist"><tr>';

						$checks_per_row = get_option( 'cme_form-rows', 1 );
						$i = 0; $first_row = true;

						$core_caps = _cme_core_caps();
						foreach( array_keys($core_caps) as $cap_name ) {
							if ( ! $is_administrator && ! current_user_can($cap_name) )
								continue;

							if ( $i == $checks_per_row ) {
								echo '</tr><tr>';
								$i = 0;
							}

							if ( ! isset( $rcaps[$cap_name] ) )
								$class = 'cap-no';
							else
								$class = ( $rcaps[$cap_name] ) ? 'cap-yes' : 'cap-neg';

							if ( ! empty($pp_metagroup_caps[$cap_name]) ) {
								$class .= ' cap-metagroup';
								$title_text = sprintf( __( '%s: assigned by Permission Group', 'capsman-enhanced' ), $cap_name );
							} else {
								$title_text = $cap_name;
							}

							$disabled = '';
							$checked = checked(1, ! empty($rcaps[$cap_name]), false );
							$lock_capability = false;
							$title = $title_text;

							if ( 'read' == $cap_name ) {
								if ( ! empty( $block_read_removal ) ) {
									// prevent the read capability from being removed from a core role, but don't force it to be added
									if ( $checked || apply_filters( 'pp_caps_force_capability_storage', false, 'read', $default ) ) {
										if ( apply_filters( 'pp_caps_lock_capability', true, 'read', $default ) ) {
											$lock_capability = true;
											$class .= ' cap-locked';
											$disabled = 'disabled="disabled"';
											if ( 'administrator' != $this->current ) {
												$title = esc_attr( __('Lockout Prevention: To remove read capability, first remove WordPress admin / editing capabilities, or add "dashboard_lockout_ok" capability', 'capsman-enhanced' ) );
											}
										}
									}
								}
							}

							?>
							<td class="<?php echo $class; ?>"><span class="cap-x">X</span><label title="<?php echo $title;?>"><input type="checkbox" name="caps[<?php echo $cap_name; ?>]" autocomplete="off" value="1" <?php echo $checked . $disabled;?> />
							<span>
							<?php
							echo str_replace( '_', ' ', $cap_name );
							?>
							</span></label><a href="#" class="neg-cap">&nbsp;x&nbsp;</a>
							<?php if ( false !== strpos( $class, 'cap-neg' ) ) :?>
								<input type="hidden" class="cme-negation-input" name="caps[<?php echo $cap_name; ?>]" value="" />
							<?php endif; ?>
							</td>

							<?php

							if ( $lock_capability ) {
								echo '<input type="hidden" name="caps[' . $cap_name . ']" value="1" />';
							}

							++$i;
						}

						if ( $i == $checks_per_row ) {
							echo '</tr>';
							$i = 0;
						} elseif ( ! $first_row ) {
							// Now close a wellformed table
							for ( $i; $i < $checks_per_row; $i++ ){
								echo '<td>&nbsp;</td>';
							}
							echo '</tr>';
						}
						?>

						<tr class="cme-bulk-select">
						<td colspan="<?php echo $checks_per_row;?>">
						<span style="float:right">
						<input type="checkbox" class="cme-check-all" autocomplete="off" title="<?php _e('check/uncheck all', 'capsman-enhanced');?>">&nbsp;&nbsp;<a class="cme-neg-all" href="#" title="<?php _e('negate all (storing as disabled capabilities)', 'capsman-enhanced');?>">X</a> <a class="cme-switch-all" href="#" title="<?php _e('negate none (add/remove all capabilities normally)', 'capsman-enhanced');?>">X</a>
						</span>
						</td></tr>

						</table>
					</div>

					<?php
					$all_capabilities = apply_filters( 'capsman_get_capabilities', array_keys( $this->capabilities ), $this->ID );
					$all_capabilities = apply_filters( 'members_get_capabilities', $all_capabilities );

					/*
					$publishpress_status_change_caps = array();
					foreach( $all_capabilities as $cap_name ) {
						if (0 === strpos($cap_name, 'status_change_')) {
							$publishpress_status_change_caps []= $cap_name;
						}
					}
					*/

					// caps: plugins
					$plugin_caps = apply_filters('cme_plugin_capabilities', $plugin_caps);

					foreach($plugin_caps as $plugin => $__plugin_caps) {
						$_plugin_caps = array_fill_keys($__plugin_caps, true);

						$id = 'cme-cap-type-tables-' . str_replace( ' ', '-', strtolower($plugin));
						$div_display = ($id == $active_tab_id) ? 'block' : 'none';

						echo '<div id="' . $id . '" style="display:' . $div_display . '">';
						echo '<table class="widefat fixed striped form-table cme-checklist"><tr>';
						echo '<h3 class="cme-cap-section">' . sprintf(__( 'Plugin Capabilities &ndash; %s', 'capsman-enhanced' ), str_replace('_', ' ', $plugin )) . '</h3>';

						$checks_per_row = get_option( 'cme_form-rows', 1 );
						$i = 0; $first_row = true;

						foreach( array_keys($_plugin_caps) as $cap_name ) {
							if ( isset( $type_caps[$cap_name] ) || isset($core_caps[$cap_name]) || isset($type_metacaps[$cap_name]) ) {
								continue;
							}

							if ( ! $is_administrator && ! current_user_can($cap_name) )
								continue;

							if ( $i == $checks_per_row ) {
								echo '</tr><tr>';
								$i = 0;
							}

							if ( ! isset( $rcaps[$cap_name] ) )
								$class = 'cap-no';
							else
								$class = ( $rcaps[$cap_name] ) ? 'cap-yes' : 'cap-neg';

							if ( ! empty($pp_metagroup_caps[$cap_name]) ) {
								$class .= ' cap-metagroup';
								$title_text = sprintf( __( '%s: assigned by Permission Group', 'capsman-enhanced' ), $cap_name );
							} else {
								$title_text = $cap_name;
							}

							$disabled = '';
							$checked = checked(1, ! empty($rcaps[$cap_name]), false );
							$title = $title_text;
							?>
							<td class="<?php echo $class; ?>"><span class="cap-x">X</span><label title="<?php echo $title;?>"><input type="checkbox" name="caps[<?php echo $cap_name; ?>]" autocomplete="off" value="1" <?php echo $checked . $disabled;?> />
							<span>
							<?php
							echo str_replace( '_', ' ', $cap_name );
							?>
							</span></label><a href="#" class="neg-cap">&nbsp;x&nbsp;</a>
							<?php if ( false !== strpos( $class, 'cap-neg' ) ) :?>
								<input type="hidden" class="cme-negation-input" name="caps[<?php echo $cap_name; ?>]" value="" />
							<?php endif; ?>
							</td>

							<?php
							++$i;
						}

						if ( $i == $checks_per_row ) {
							echo '</tr>';
							$i = 0;
						} elseif ( ! $first_row ) {
							// Now close a wellformed table
							for ( $i; $i < $checks_per_row; $i++ ){
								echo '<td>&nbsp;</td>';
							}
							echo '</tr>';
						}
						?>

						<tr class="cme-bulk-select">
						<td colspan="<?php echo $checks_per_row;?>">
						<span style="float:right">
						<input type="checkbox" class="cme-check-all" title="<?php _e('check/uncheck all', 'capsman-enhanced');?>">&nbsp;&nbsp;<a class="cme-neg-all" href="#" autocomplete="off" title="<?php _e('negate all (storing as disabled capabilities)', 'capsman-enhanced');?>">X</a> <a class="cme-switch-all" href="#" title="<?php _e('negate none (add/remove all capabilities normally)', 'capsman-enhanced');?>">X</a>
						</span>
						</td></tr>

						</table>
						</div>
					<?php
					}

					// caps: invalid
					if (array_intersect(array_keys(array_filter($type_metacaps)), $all_capabilities) && array_intersect_key($type_metacaps, array_filter($rcaps))) {
						$id = "cme-cap-type-tables-invalid";
						$div_display = ($id == $active_tab_id) ? 'block' : 'none';

						echo '<div id="' . $id . '" style="display:' . $div_display . '">';
						echo '<h3 class="cme-cap-section">' . __( 'Invalid Capabilities', 'capsman-enhanced' ) . '</h3>';
						?>

						<script type="text/javascript">
						/* <![CDATA[ */
						jQuery(document).ready( function($) {
							$('#cme_tab_invalid_caps').show();
						});
						/* ]]> */
						</script>

						<div>
						<span class="cme-subtext">
							<?php _e('The following entries have no effect. Please assign desired capabilities in the Read / Edit / Delete grid above.', 'capsman-enhanced');?>
						</span>
						</div>

						<table class="widefat fixed striped form-table cme-checklist">
						<tr>
						<?php
						$i = 0; $first_row = true;

						foreach( $all_capabilities as $cap_name ) {
							if ( ! isset($this->capabilities[$cap_name]) )
								$this->capabilities[$cap_name] = str_replace( '_', ' ', $cap_name );
						}

						uasort( $this->capabilities, 'strnatcasecmp' );  // sort by array values, but maintain keys );

						foreach ( $this->capabilities as $cap_name => $cap ) :
							if (!isset($type_metacaps[$cap_name]) || empty($rcaps[$cap_name])) {
								continue;
							}

							if ( ! $is_administrator && empty( $current_user->allcaps[$cap_name] ) ) {
								continue;
							}

							if ( $i == $checks_per_row ) {
								echo '</tr><tr>';
								$i = 0; $first_row = false;
							}

							if ( ! isset( $rcaps[$cap_name] ) )
								$class = 'cap-no';
							else
								$class = ( $rcaps[$cap_name] ) ? 'cap-yes' : 'cap-neg';

							$title_text = $cap_name;

							$disabled = '';
							$checked = checked(1, ! empty($rcaps[$cap_name]), false );
						?>
							<td class="<?php echo $class; ?>"><span class="cap-x">X</span><label title="<?php echo $title_text;?>"><input type="checkbox" name="caps[<?php echo $cap_name; ?>]" autocomplete="off" value="1" <?php echo $checked . $disabled;?> />
							<span>
							<?php
							echo str_replace( '_', ' ', $cap );
							?>
							</span></label><a href="#" class="neg-cap">&nbsp;x&nbsp;</a>
							<?php if ( false !== strpos( $class, 'cap-neg' ) ) :?>
								<input type="hidden" class="cme-negation-input" name="caps[<?php echo $cap_name; ?>]" value="" />
							<?php endif; ?>
							</td>
						<?php
							$i++;
						endforeach;

						if ( ! empty($lock_manage_caps_capability) ) {
							echo '<input type="hidden" name="caps[manage_capabilities]" value="1" />';
						}

						if ( $i == $checks_per_row ) {
							echo '</tr><tr>';
							$i = 0;
						} else {
							if ( ! $first_row ) {
								// Now close a wellformed table
								for ( $i; $i < $checks_per_row; $i++ ){
									echo '<td>&nbsp;</td>';
								}
								echo '</tr>';
							}
						}
						?>

					</table>
					</div>
						<?php
					} // endif any invalid caps

					$id = "cme-cap-type-tables-additional";
					$div_display = ($id == $active_tab_id) ? 'block' : 'none';
					?>
					<div id="<?php echo $id;?>" style="display:<?php echo $div_display;?>">
						<?php
						// caps: additional
						echo '<h3 class="cme-cap-section">' . __( 'Additional Capabilities', 'capsman-enhanced' ) . '</h3>';

						?>
						<table class="widefat fixed striped form-table cme-checklist">
						<tr>
						<?php
						$i = 0; $first_row = true;

						foreach( $all_capabilities as $cap_name ) {
							if ( ! isset($this->capabilities[$cap_name]) )
								$this->capabilities[$cap_name] = str_replace( '_', ' ', $cap_name );
						}

						uasort( $this->capabilities, 'strnatcasecmp' );  // sort by array values, but maintain keys );

						$additional_caps = apply_filters('publishpress_caps_manage_additional_caps', $this->capabilities);

						foreach ($additional_caps as $cap_name => $cap) :

							if ((isset($type_caps[$cap_name]) && !isset($type_metacaps[$cap_name]))
							|| isset($core_caps[$cap_name])
							|| (isset($type_metacaps[$cap_name]) && !empty($rcaps[$cap_name])) ) {
								continue;
							}

							if (!isset($type_metacaps[$cap_name]) || !empty($rcaps[$cap_name])) {
								foreach(array_keys($plugin_caps) as $plugin) {
									if ( in_array( $cap_name, $plugin_caps[$plugin]) ) {
										continue 2;
									}
								}
							}

							if ( ! $is_administrator && empty( $current_user->allcaps[$cap_name] ) ) {
								continue;
							}

							// Levels are not shown.
							if ( preg_match( '/^level_(10|[0-9])$/i', $cap_name ) ) {
								continue;
							}

							if ( $i == $checks_per_row ) {
								echo '</tr><tr>';
								$i = 0; $first_row = false;
							}

							if ( ! isset( $rcaps[$cap_name] ) )
								$class = 'cap-no';
							else
								$class = ( $rcaps[$cap_name] ) ? 'cap-yes' : 'cap-neg';

							if ( ! empty($pp_metagroup_caps[$cap_name]) ) {
								$class .= ' cap-metagroup';
								$title_text = sprintf( __( '%s: assigned by Permission Group', 'capsman-enhanced' ), $cap_name );
							} else {
								$title_text = $cap_name;
							}

							$disabled = '';
							$checked = checked(1, ! empty($rcaps[$cap_name]), false );

							if ( 'manage_capabilities' == $cap_name ) {
								if (!current_user_can('administrator') && (!is_multisite() || !is_super_admin())) {
									continue;
								} elseif ( 'administrator' == $default ) {
									$class .= ' cap-locked';
									$lock_manage_caps_capability = true;
									$disabled = 'disabled="disabled"';
								}
							}
						?>
							<td class="<?php echo $class; ?>"><span class="cap-x">X</span><label title="<?php echo $title_text;?>"><input type="checkbox" name="caps[<?php echo $cap_name; ?>]" autocomplete="off" value="1" <?php echo $checked . $disabled;?> />
							<span>
							<?php
							echo str_replace( '_', ' ', $cap );
							?>
							</span></label><a href="#" class="neg-cap">&nbsp;x&nbsp;</a>
							<?php if ( false !== strpos( $class, 'cap-neg' ) ) :?>
								<input type="hidden" class="cme-negation-input" name="caps[<?php echo $cap_name; ?>]" value="" />
							<?php endif; ?>
							</td>
						<?php
							$i++;
						endforeach;

						if ( ! empty($lock_manage_caps_capability) ) {
							echo '<input type="hidden" name="caps[manage_capabilities]" value="1" />';
						}

						if ( $i == $checks_per_row ) {
							echo '</tr><tr>';
							$i = 0;
						} else {
							if ( ! $first_row ) {
								// Now close a wellformed table
								for ( $i; $i < $checks_per_row; $i++ ){
									echo '<td>&nbsp;</td>';
								}
								echo '</tr>';
							}
						}
						?>

						<tr class="cme-bulk-select">
						<td colspan="<?php echo $checks_per_row;?>">
						<span style="float:right">
						<input type="checkbox" class="cme-check-all" title="<?php _e('check/uncheck all', 'capsman-enhanced');?>">&nbsp;&nbsp;<a class="cme-neg-all" href="#" autocomplete="off" title="<?php _e('negate all (storing as disabled capabilities)', 'capsman-enhanced');?>">X</a> <a class="cme-switch-all" href="#" title="<?php _e('negate none (add/remove all capabilities normally)', 'capsman-enhanced');?>">X</a>
						</span>
						</td></tr>

						</table>
					</div>
				</div>
			</div>


			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready( function($) {
				$('a[href="#pp-more"]').click( function() {
					$('#pp_features').show();
					return false;
				});
				$('a[href="#pp-hide"]').click( function() {
					$('#pp_features').hide();
					return false;
				});
			});
			/* ]]> */
			</script>

			<?php /* play.png icon by Pavel: http://kde-look.org/usermanager/search.php?username=InFeRnODeMoN */ ?>

			<div id="pp_features" style="display:none"><div class="pp-logo"><a href="https://publishpress.com/presspermit/"><img src="<?php echo $img_url;?>pp-logo.png" alt="<?php _e('PublishPress Permissions', 'capsman-enhanced');?>" /></a></div><div class="features-wrap"><ul class="pp-features">
			<li>
			<?php _e( "Automatically define type-specific capabilities for your custom post types and taxonomies", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/regulate-post-type-access" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Assign standard WP roles supplementally for a specific post type", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/regulate-post-type-access" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Assign custom WP roles supplementally for a specific post type <em>(Pro)</em>", 'capsman-enhanced' );?>
			</li>

			<li>
			<?php _e( "Customize reading permissions per-category or per-post", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/category-exceptions" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Customize editing permissions per-category or per-post <em>(Pro)</em>", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/page-editing-exceptions" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Custom Post Visibility statuses, fully implemented throughout wp-admin <em>(Pro)</em>", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/custom-post-visibility" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Custom Moderation statuses for access-controlled, multi-step publishing workflow <em>(Pro)</em>", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/multi-step-moderation" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Regulate permissions for Edit Flow post statuses <em>(Pro)</em>", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/edit-flow-integration" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Customize the moderated editing of published content with Revisionary or Post Forking <em>(Pro)</em>", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/published-content-revision" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "Grant Spectator, Participant or Moderator access to specific bbPress forums <em>(Pro)</em>", 'capsman-enhanced' );?>
			</li>

			<li>
			<?php _e( "Grant supplemental content permissions to a BuddyPress group <em>(Pro)</em>", 'capsman-enhanced' );?>
			<a href="https://presspermit.com/tutorial/buddypress-content-permissions" target="_blank"><img class="cme-play" alt="*" src="<?php echo $img_url;?>play.png" /></a></li>

			<li>
			<?php _e( "WPML integration to mirror permissions to translations <em>(Pro)</em>", 'capsman-enhanced' );?>
			</li>

			<li>
			<?php _e( "Member support forum", 'capsman-enhanced' );?>
			</li>

			</ul></div>

			<?php
			echo '<div>';
			printf( __('%1$sgrab%2$s %3$s', 'capsman-enhanced'), '<strong>', '</strong>', '<span class="plugins update-message"><a href="' . cme_plugin_info_url('press-permit-core') . '" class="thickbox" title="' . sprintf( __('%s (free install)', 'capsman-enhanced'), 'Permissions Pro' ) . '">Permissions Pro</a></span>' );
			echo '&nbsp;&nbsp;&bull;&nbsp;&nbsp;';
			printf( __('%1$sbuy%2$s %3$s', 'capsman-enhanced'), '<strong>', '</strong>',  '<a href="https://publishpress.com/presspermit/" target="_blank" title="' . sprintf( __('%s info/purchase', 'capsman-enhanced'), 'Permissions Pro' ) . '">Permissions&nbsp;Pro</a>' );
			echo '&nbsp;&nbsp;&bull;&nbsp;&nbsp;';
			echo '<a href="#pp-hide">hide</a>';
			echo '</div></div>';

			///
			?>
			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready( function($) {
				$('a[href="#toggle_type_caps"]').click( function() {
					var chks = $(this).closest('tr').find('input');
					var set_checked = ! $(chks).first().is(':checked');

					$(chks).each(function(i,e) {
						$('input[name="' + $(this).attr('name') + '"]').prop('checked', set_checked);
					});

					return false;
				});

				$('input[name^="caps["]').click(function() {
					$('input[name="' + $(this).attr('name') + '"]').prop('checked', $(this).prop('checked'));
				});
			});
			/* ]]> */
			</script>

			<div style="display:inline-block; float:right;">
			<?php
			$level = ak_caps2level($rcaps);
			?>
			<span title="<?php _e('Role level is mostly deprecated. However, it still determines eligibility for Post Author assignment and limits the application of user editing capabilities.', 'capsman-enhanced');?>">

			<?php (in_array(get_locale(), ['en_EN', 'en_US'])) ? printf('Role Level:') : _e('Level:', 'capsman-enhanced');?> <select name="level">
			<?php for ( $l = $this->max_level; $l >= 0; $l-- ) {?>
					<option value="<?php echo $l; ?>" style="text-align:right;"<?php selected($level, $l); ?>>&nbsp;<?php echo $l; ?>&nbsp;</option>
				<?php }
				?>
			</select>
			</span>

			</div>

		<?php
		$support_pp_only_roles = defined('PRESSPERMIT_ACTIVE');
		cme_network_role_ui( $default );
		?>

		<p class="submit" style="padding-top:0;">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="current" value="<?php echo $default; ?>" />
			<input type="submit" name="SaveRole" value="<?php echo (in_array(get_locale(), ['en_EN', 'en_US'])) ? 'Save Capabilities' : _e('Save Changes', 'capsman-enhanced');?>" class="button-primary" /> &nbsp;

			<?php if ( current_user_can('administrator') && 'administrator' != $default ) : ?>
				<a class="ak-delete" title="<?php echo esc_attr(__('Delete this role', 'capsman-enhanced')) ?>" href="<?php echo wp_nonce_url("admin.php?page={$this->ID}&amp;action=delete&amp;role={$default}", 'delete-role_' . $default); ?>" onclick="if ( confirm('<?php echo esc_js(sprintf(__("You are about to delete the %s role.\n\n 'Cancel' to stop, 'OK' to delete.", 'capsman-enhanced'), $roles[$default])); ?>') ) { return true;}return false;"><?php _e('Delete Role', 'capsman-enhanced')?></a>
			<?php endif; ?>
		</p>

		</td>
		<td class="sidebar">
			<?php do_action('publishpress-caps_sidebar_top');?>

			<dl>
				<dt><?php _e('Add Capability', 'capsman-enhanced'); ?></dt>
				<dd style="text-align:center;">
					<p><input type="text" name="capability-name" class="regular-text" placeholder="<?php echo 'capability_name';?>" /><br />
					<input type="submit" name="AddCap" value="<?php _e('Add to role', 'capsman-enhanced') ?>" class="button" /></p>
				</dd>
			</dl>

			<?php
				$pp_ui->pp_types_ui( $defined['type'] );
				$pp_ui->pp_taxonomies_ui( $defined['taxonomy'] );

				do_action('publishpress-caps_sidebar_bottom');
			?>

			<dl>
				<dt><?php (!in_array(get_locale(), ['en_EN', 'en_US'])) ? _e('Copy this role to', 'capsman-enhanced') : printf('Copy %s Role', translate_user_role($roles[$default])); ?></dt>
				<dd style="text-align:center;">
					<?php $class = ( $support_pp_only_roles ) ? 'tight-text' : 'regular-text'; ?>
					<p><input type="text" name="copy-name"  class="<?php echo $class;?>" placeholder="<?php _e('Role Name', 'capsman-enhanced') ?>" />

					<?php if( $support_pp_only_roles ) : ?>
					<label for="copy_role_pp_only" title="<?php _e('Make role available for supplemental assignment to Permission Groups only', 'capsman-enhanced');?>"> <input type="checkbox" name="copy_role_pp_only" id="copy_role_pp_only" autocomplete="off" value="1"> <?php _e('hidden', 'capsman-enhanced'); ?> </label>
					<?php endif; ?>

					<br />
					<input type="submit" name="CopyRole" value="<?php _e('Copy', 'capsman-enhanced') ?>" class="button" />
					</p>
				</dd>
			</dl>

			<dl>
				<dt><?php _e('Rename Role', 'capsman-enhanced'); ?></dt>
				<dd style="text-align:center;">
					<p><input type="text" name="rename-name" class="regular-text" placeholder="<?php _e('New Role Name', 'capsman-enhanced') ?>" />

					<br />
					<input type="submit" name="RenameRole" value="<?php _e('Rename', 'capsman-enhanced') ?>" class="button" />
					</p>
				</dd>
			</dl>
		</td>
	</tr>
	</table>
	</fieldset>
	</form>

	<?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
		cme_publishpressFooter();
	}
	?>
</div>

<?php
function cme_network_role_ui( $default ) {
	if (!is_multisite() || !is_super_admin() || !is_main_site()) {
		return false;
	}
	?>

	<div style="float:right;margin-left:10px;margin-right:10px">
		<?php
		if ( ! $autocreate_roles = get_site_option( 'cme_autocreate_roles' ) )
			$autocreate_roles = array();
		?>
		<div style="margin-bottom: 5px">
		<label for="cme_autocreate_role" title="<?php _e('Create this role definition in new (future) sites', 'capsman-enhanced');?>"><input type="checkbox" name="cme_autocreate_role" id="cme_autocreate_role" autocomplete="off" value="1" <?php echo checked(in_array($default, $autocreate_roles));?>> <?php _e('include in new sites', 'capsman-enhanced'); ?> </label>
		</div>
		<div>
		<label for="cme_net_sync_role" title="<?php echo esc_attr(__('Copy / update this role definition to all sites now', 'capsman-enhanced'));?>"><input type="checkbox" name="cme_net_sync_role" id="cme_net_sync_role" autocomplete="off" value="1"> <?php _e('sync role to all sites now', 'capsman-enhanced'); ?> </label>
		</div>
		<div>
		<label for="cme_net_sync_options" title="<?php echo esc_attr(__('Copy option settings to all sites now', 'capsman-enhanced'));?>"><input type="checkbox" name="cme_net_sync_options" id="cme_net_sync_options" autocomplete="off" value="1"> <?php _e('sync options to all sites now', 'capsman-enhanced'); ?> </label>
		</div>
	</div>
<?php
	return true;
}

function cme_plugin_info_url( $plugin_slug ) {
	$_url = "plugin-install.php?tab=plugin-information&plugin=$plugin_slug&TB_iframe=true&width=640&height=678";
	return ( is_multisite() ) ? network_admin_url($_url) : admin_url($_url);
}
