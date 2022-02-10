<?php
	//only admins can get this
	if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_addons")))
	{
		die(__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
	}	
	
	global $wpdb, $msg, $msgt, $pmpro_addons;
	
	wp_enqueue_script( 'plugin-install' );
	add_thickbox();
	wp_enqueue_script( 'updates' );
	
	require_once(dirname(__FILE__) . "/admin_header.php");	

	//force a check of plugin versions?
	if(!empty($_REQUEST['force-check']))
	{
		wp_version_check(array(), true);
		wp_update_plugins();
		$pmpro_license_key = get_option("pmpro_license_key", "");
		pmpro_license_isValid($pmpro_license_key, NULL, true);
	}
	
	//some vars
	$addons = pmpro_getAddons();
	$addons_timestamp = get_option("pmpro_addons_timestamp", false);
	$plugin_info = get_site_transient( 'update_plugins' );
	$pmpro_license_key = get_option("pmpro_license_key", "");
	
	//get plugin status for filters
	if(!empty($_REQUEST['plugin_status']))
		$status = pmpro_sanitize_with_safelist($_REQUEST['plugin_status'], array('', 'all', 'active', 'inactive', 'update', 'uninstalled'));

	//make sure we have an approved status
	$approved_statuses = array('all', 'active', 'inactive', 'update', 'uninstalled');
	if(empty($status) || !in_array($status, $approved_statuses))
		$status = "all";
	
	// Split Add Ons into groups for filtering
	$all_visible_addons = array();
	$all_hidden_addons = array();
	$active_addons = array();
	$inactive_addons = array();
	$update_available_addons = array();
	$not_installed_addons = array();
	
	// Build array of Visible, Hidden, Active, Inactive, Installed, and Not Installed Add Ons.
	foreach ( $addons as $addon ) {
		
		$plugin_file = $addon['Slug'] . '/' . $addon['Slug'] . '.php';
		$plugin_file_abs = ABSPATH . 'wp-content/plugins/' . $plugin_file;

		// Build Visible and Hidden arrays.
		if ( empty ( $addon['HideFromAddOnsList'] ) || file_exists( $plugin_file_abs ) ) {
			$all_visible_addons[] = $addon;
		} else {
			$all_hidden_addons[] = $addon;
		}
		
		// Build Active and Inactive arrays - exclude hidden Add Ons that are not installed.
		if ( is_plugin_active( $plugin_file ) ) {
			$active_addons[] = $addon;
		} elseif ( empty ( $addon['HideFromAddOnsList'] ) || file_exists( $plugin_file_abs ) ) {
			$inactive_addons[] = $addon;
		}
		
		// Build array of Add Ons that have an update available.
		if ( isset( $plugin_info->response[$plugin_file] ) ) {
			$update_available_addons[] = $addon;
		}
		
		// Build array of Add Ons that are visible and not installed.
		if ( empty ( $addon['HideFromAddOnsList'] ) && ! file_exists( $plugin_file_abs ) ) {
			$not_installed_addons[] = $addon;
		}
			
	}

	?>
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Add Ons', 'paid-memberships-pro' ); ?></h1>
	<hr class="wp-header-end">
	
	<?php
		pmpro_showMessage();
	?>
	
	<p>
		<?php printf(__('Last checked on %s at %s.', 'paid-memberships-pro' ), date_i18n(get_option('date_format'), $addons_timestamp), date_i18n(get_option('time_format'), $addons_timestamp));?> &nbsp;	
		<a class="button" href="<?php echo admin_url("admin.php?page=pmpro-addons&force-check=1&plugin_status=" . $status);?>"><?php _e('Check Again', 'paid-memberships-pro' ); ?></a>
	</p>

	<ul class="subsubsub">
		<li class="all"><a href="admin.php?page=pmpro-addons&plugin_status=all" <?php if(empty($status) || $status == "all") { ?>class="current"<?php } ?>><?php _e('All', 'paid-memberships-pro' ); ?> <span class="count">(<?php echo count($all_visible_addons);?>)</span></a> |</li>
		<li class="active"><a href="admin.php?page=pmpro-addons&plugin_status=active" <?php if($status == "active") { ?>class="current"<?php } ?>><?php _e('Active', 'paid-memberships-pro' ); ?> <span class="count">(<?php echo count($active_addons);?>)</span></a> |</li>
		<li class="inactive"><a href="admin.php?page=pmpro-addons&plugin_status=inactive" <?php if($status == "inactive") { ?>class="current"<?php } ?>><?php _e('Inactive', 'paid-memberships-pro' ); ?> <span class="count">(<?php echo count($inactive_addons);?>)</span></a> |</li>
		<li class="update"><a href="admin.php?page=pmpro-addons&plugin_status=update" <?php if($status == "update") { ?>class="current"<?php } ?>><?php _e('Update Available', 'paid-memberships-pro' ); ?> <span class="count">(<?php echo count($update_available_addons);?>)</span></a> |</li>
		<li class="uninstalled"><a href="admin.php?page=pmpro-addons&plugin_status=uninstalled" <?php if($status == "uninstalled") { ?>class="current"<?php } ?>><?php _e('Not Installed', 'paid-memberships-pro' ); ?> <span class="count">(<?php echo count($not_installed_addons);?>)</span></a></li>
	</ul>

	<table class="wp-list-table widefat plugins">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
			<?php /*
			<label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All'); ?></label><input id="cb-select-all-1" type="checkbox">
			*/ ?>
		</th>	
		<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Add On Name', 'paid-memberships-pro' ); ?></th>
		<th scope="col" id="type" class="manage-column column-type" style=""><?php _e('Type', 'paid-memberships-pro' ); ?></th>
		<th scope="col" id="description" class="manage-column column-description" style=""><?php _e('Description', 'paid-memberships-pro' ); ?></th>		
	</tr>
	</thead>
	<tbody id="the-list">
		<?php
			//which addons to show?
			if ( $status == "active" ) {
				$addons = $active_addons;
			} elseif ( $status == "inactive") {
				$addons = $inactive_addons;
			} elseif ( $status == "update" ) {
				$addons = $update_available_addons;
			} elseif ( $status == "uninstalled" ) {
				$addons = $not_installed_addons;
			} else {
				$addons = $all_visible_addons;
			}
			
			//no addons for this filter?
			if(count($addons) < 1)
			{
			?>
			<tr>
				<td></td>
				<td colspan="3"><p><?php _e('No Add Ons found.', 'paid-memberships-pro' ); ?></p></td>	
			</tr>
			<?php
			}

			foreach($addons as $addon)
			{
				$plugin_file = $addon['Slug'] . '/' . $addon['Slug'] . '.php';
				$plugin_file_abs = ABSPATH . 'wp-content/plugins/' . $plugin_file;
				
				if(file_exists($plugin_file_abs))
					$plugin_data = get_plugin_data( $plugin_file_abs, false, true); 					
				else
					$plugin_data = $addon;
				
				//make sure plugin value is set
				if(empty($plugin_data['plugin']))
					$plugin_data['plugin'] = $plugin_file;
				
				$plugin_name = $plugin_data['Name'];
				$id = sanitize_title( $plugin_name );
				$checkbox_id =  "checkbox_" . md5($plugin_name);	
								
				if(!empty($plugin_data['License']))
				{
					$context = 'uninstalled inactive';
				}
				elseif(isset($plugin_info->response[$plugin_file]))
				{
					$context = 'active update';
				}
				elseif(is_plugin_active($plugin_file))
				{
					$context = 'active';
				}
				elseif(file_exists($plugin_file_abs))
				{
					$context = 'inactive';
				}
				else
				{
					$context = false;
				}
				?>
				<tr id="<?php echo $id; ?>" class="<?php echo $context;?>" data-slug="<?php echo $id; ?>">					
					<th scope="row" class="check-column">
					<?php /*
						<label class="screen-reader-text" for="<?php echo $checkbox_id; ?>"><?php sprintf( __( 'Select %s' ), $plugin_name ); ?></label>
						<input type="checkbox" name="checked[]" value="<?php esc_attr( $plugin_file ); ?>" id="<?php echo $checkbox_id; ?>">
					*/ ?>
					</th>
					<td class="plugin-title">
						<strong><?php echo $plugin_name; ?></strong>
						<div class="row-actions visible">
						<?php
							$actions = array();
							if($context === 'uninstalled inactive')
							{
								if($plugin_data['License'] == 'wordpress.org')
								{
									//wordpress.org
									$actions['install'] = '<span class="install"><a href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_data['Slug']), 'install-plugin_' . $plugin_data['Slug']) . '">' . __('Install Now', 'paid-memberships-pro' ) . '</a></span>';
								}
								elseif($plugin_data['License'] == 'free')
								{
									//free
									$actions['install'] = '<span class="install"><a href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_data['Slug']), 'install-plugin_' . $plugin_data['Slug']) . '">' . __('Install Now', 'paid-memberships-pro' ) . '</a></span>';
									$actions['download'] = '<span class="download"><a target="_blank" href="' . $plugin_data['Download'] . '?key=' . $pmpro_license_key . '">' . __('Download', 'paid-memberships-pro' ) . '</a></span>';
								}
								elseif(empty($pmpro_license_key))
								{
									//no key
									$actions['settings'] = '<span class="settings"><a href="' . admin_url('admin.php?page=pmpro-license') . '">' . __('Update License', 'paid-memberships-pro' ) . '</a></span>';
									$actions['download'] = '<span class="download"><a target="_blank" href="' . $plugin_data['PluginURI'] . '">' . __('Download', 'paid-memberships-pro' ) . '</a></span>';
								}
								elseif(pmpro_license_isValid($pmpro_license_key, $plugin_data['License']))
								{
									//valid key
									$actions['install'] = '<span class="install"><a href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_data['Slug']), 'install-plugin_' . $plugin_data['Slug']) . '">' . __('Install Now', 'paid-memberships-pro' ) . '</a></span>';
									$actions['download'] = '<span class="download"><a target="_blank" href="' . $plugin_data['Download'] . '?key=' . $pmpro_license_key . '">' . __('Download', 'paid-memberships-pro' ) . '</a></span>';									
								}
								else
								{
									//invalid key
									$actions['settings'] = '<span class="settings"><a href="' . admin_url('admin.php?page=pmpro-license') . '">' . __('Update License', 'paid-memberships-pro' ) . '</a></span>';
									$actions['download'] = '<span class="download"><a target="_blank" href="' . $plugin_data['PluginURI'] . '">' . __('Download', 'paid-memberships-pro' ) . '</a></span>';
								}
							}
							elseif($context === 'active' || $context === 'active update')
							{
								$actions['deactivate'] = '<span class="deactivate"><a href="' . wp_nonce_url(self_admin_url('plugins.php?action=deactivate&plugin=' . $plugin_file), 'deactivate-plugin_' . $plugin_file ) . '" aria-label="' . esc_attr( sprintf( __( 'Deactivate %s' ), $plugin_data['Name'] ) ) . '">' . __('Deactivate') . '</a></span>';
							}
							elseif($context === 'inactive')
							{
								$actions['activate'] = '<span class="activate"><a href="' . wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $plugin_file), 'activate-plugin_' . $plugin_file) . '" class="edit" aria-label="' . esc_attr( sprintf( __( 'Activate %s' ), $plugin_data['Name'] ) ) . '">' . __('Activate') . '</a></span>';
								$actions['delete'] = '<span class="delete"><a href="' . wp_nonce_url(self_admin_url('plugins.php?action=delete-selected&checked[]=' . $plugin_file), 'bulk-plugins') . '" class="delete" aria-label="' . esc_attr( sprintf( __( 'Delete %s' ), $plugin_data['Name'] ) ) . '">' . __('Delete') . '</a></span>';
							}
							$actions = apply_filters( 'plugin_action_links_' . $plugin_file, $actions, $plugin_file, $plugin_data, $context );
							echo implode(' | ',$actions);
						?>
						</div>
					</td>
					<td class="column-type">
						<?php
							if($addon['License'] == 'free')
								_e("PMPro Free", 'paid-memberships-pro' );
							elseif($addon['License'] == 'core')
								_e("PMPro Core", 'paid-memberships-pro' );
							elseif($addon['License'] == 'plus')
								_e("PMPro Plus", 'paid-memberships-pro' );
							elseif($addon['License'] == 'wordpress.org')
								_e("WordPress.org", 'paid-memberships-pro' );
							else
								_e("N/A", 'paid-memberships-pro' );
						?>
					</td>
					<td class="column-description desc">
						<div class="plugin-description"><p><?php echo $plugin_data['Description']; ?></p></div>
						<div class="inactive second plugin-version-author-uri">
						<?php
						$plugin_meta = array();
							if ( !empty( $plugin_data['Version'] ) )
								$plugin_meta[] = sprintf( __( 'Version %s' ), $plugin_data['Version'] );
							if ( !empty( $plugin_data['Author'] ) ) {
								$author = $plugin_data['Author'];
								if ( !empty( $plugin_data['AuthorURI'] ) )
									$author = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';
								$plugin_meta[] = sprintf( __( 'By %s' ), $author );
							}
							// Details link using API info, if available
							if ( isset( $plugin_data['slug'] ) && current_user_can( 'install_plugins' ) ) {
								$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
									esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data['slug'] .
										'&TB_iframe=true&width=600&height=550' ) ),
									esc_attr( sprintf( __( 'More information about %s' ), $plugin_name ) ),
									esc_attr( $plugin_name ),
									__( 'View details' )
								);
							} elseif ( ! empty( $plugin_data['PluginURI'] ) ) {
								$plugin_meta[] = sprintf( '<a target="_blank" href="%s">%s</a>',
									esc_url( $plugin_data['PluginURI'] ) . '?utm_source=plugin&utm_medium=pmpro-addons&utm_campaign=add-ons',
									__( 'Visit plugin site' )
								);
							}
							$plugin_meta = apply_filters( 'plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $status);
							echo implode( ' | ', $plugin_meta );
							?>
						</div>
					</td>					
				</tr>
				<?php
								
				ob_start();
				wp_plugin_update_row( $plugin_file, $plugin_data );
				$row = ob_get_contents();
				ob_end_clean();
				
				echo str_replace('colspan="0"', 'colspan="4"', $row);
			}
		?>
		</tbody>
	</table>				

<?php
	require_once(dirname(__FILE__) . "/admin_footer.php");
	wp_print_request_filesystem_credentials_modal();
?>
