<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Capabilities UI: PublishPress Permissions integration
 * 
 * This module also contains the Settings UI for Type-Specific Types / Taxonomies, which were previously a front end to PublishPress Permissions 
 * 
 */

class Capsman_PP_UI {
	function get_metagroup_caps( $default ) {
		global $wpdb;

		if ( defined( 'PRESSPERMIT_ACTIVE' ) ) {
			$query = $wpdb->prepare( "SELECT role_name FROM $wpdb->ppc_roles AS r INNER JOIN $wpdb->pp_groups AS g ON g.ID = r.agent_id AND r.agent_type = 'pp_group' WHERE g.metagroup_type = 'wp_role' AND g.metagroup_id = %s", $default );
			$pp_supplemental_roles = $wpdb->get_col( $query );
		} else {
			$query = $wpdb->prepare( "SELECT role_name FROM $wpdb->pp_roles AS r INNER JOIN $wpdb->pp_groups AS g ON g.ID = r.group_id AND r.group_type = 'pp_group' AND r.scope = 'site' WHERE g.metagroup_type = 'wp_role' AND g.metagroup_id = %s", $default );
			$pp_supplemental_roles = $wpdb->get_col( $query );
		}

		$pp_filtered_types = pp_get_enabled_types('post');
		$pp_metagroup_caps = array();
		$pp_cap_caster = pp_init_cap_caster();

		foreach( $pp_supplemental_roles as $_role_name ) {
			$role_specs = explode( ':', $_role_name );
			if ( empty($role_specs[2]) || ! in_array( $role_specs[2], $pp_filtered_types ) )
				continue;

			// add all type-specific caps whose base property cap is included in this pattern role
			// i.e. If 'edit_posts' is in the pattern role, grant $type_obj->cap->edit_posts
			$pp_metagroup_caps = array_merge( $pp_metagroup_caps, array_fill_keys( $pp_cap_caster->get_typecast_caps( $_role_name, 'site' ), true ) );
		}
	
		return $pp_metagroup_caps;
	}
	
	function show_capability_hints( $default ) {					
		if ( pp_capabilities_get_permissions_option('display_hints') ) {
			echo '<ul class="ul-disc publishpress-caps-extra-hints" style="margin-top:10px;display:none">';
			
			$pp_prefix = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp' : 'presspermit';

			if ( defined( 'PPCE_VERSION' ) || ! defined( 'PRESSPERMIT_ACTIVE' ) || in_array( $default, array( 'subscriber', 'contributor', 'author', 'editor' ) ) ) {
				echo '<li>';
				if ( defined( 'PPCE_VERSION' ) || ! defined( 'PRESSPERMIT_ACTIVE' ) ) {
					if ( pp_capabilities_get_permissions_option( 'advanced_options' ) )
						$parenthetical = ' (' . sprintf( __( 'see %1$sRole Usage%2$s: "Pattern Roles"', 'capsman-enhanced' ), "<a href='" . admin_url("admin.php?page={$pp_prefix}-role-usage") . "'>", '</a>' ) . ')';
					else
						$parenthetical = ' (' . sprintf( __( 'activate %1$sAdvanced settings%2$s, see Role Usage', 'capsman-enhanced' ), "<a href='" . admin_url("admin.php?page={$pp_prefix}-settings&pp_tab=advanced") . "'>", '</a>' ). ')';
				} else
					$parenthetical = '';

				if ( defined( 'PRESSPERMIT_ACTIVE' ) )
					printf( __( '"Posts" capabilities selected here also define type-specific role assignment for Permission Groups%s.', 'capsman-enhanced' ), $parenthetical ) ;
				else
					printf( __( '"Posts" capabilities selected here also define type-specific role assignment for Permit Groups%s.', 'capsman-enhanced' ), $parenthetical ) ;

				echo '</li>';
			}
			
			$status_hint = '';
			if ( defined( 'PRESSPERMIT_ACTIVE' ) )
				if ( defined( 'PPS_VERSION' ) )
					$status_hint = sprintf( __( 'Capabilities for custom statuses can be manually added here. (See %sPermissions > Post Statuses%s for applicable names). %sSupplemental status-specific roles%s are usually more convenient, though.', 'capsman-enhanced' ), "<a href='" . admin_url("admin.php?page={$pp_prefix}-statuses&show_caps=1") . "'>", '</a>', "<a href='" . admin_url("admin.php?page={$pp_prefix}-groups") . "'>", '</a>' ) ;
				elseif ( pp_capabilities_get_permissions_option( 'display_extension_hints' ) )
					$status_hint = sprintf( __( 'Capabilities for custom statuses can be manually added here. Or activate the PP Custom Post Statuses extension to assign status-specific supplemental roles.', 'capsman-enhanced' ), "<a href='" . admin_url("admin.php?page={$pp_prefix}-role-usage") . "'>", '</a>' ) ;
			
			elseif ( defined( 'PP_VERSION' ) )
				$status_hint = sprintf( __( 'Capabilities for custom statuses can be manually added to a role here (see Conditions > Status > Capability Mapping for applicable names). However, it is usually more convenient to use Permit Groups to assign a supplemental status-specific role.', 'capsman-enhanced' ), "<a href='" . admin_url("admin.php?page={$pp_prefix}-role-usage") . "'>", '</a>' ) ;
			
			if ( $status_hint )
				echo "<li>$status_hint</li>";

			echo '</ul>';
		}
	}
	
	// Note: CME can now impose type-specific capabilities without Press Permit Core active
	function pp_types_ui( $defined_types ) {
		?>
		<dl>
			<dt><?php _e('Type-Specific Capabilities', 'capsman-enhanced'); ?></dt>
			<dd style="text-align:center;">
				<?php
				$caption = __( 'Ensure permissions can be controlled separately from other post types.', 'capsman-enhanced' );
				echo "<p class='cme-hint'>$caption</p>";
				
				if ( defined( 'PRESSPERMIT_ACTIVE' ) && pp_capabilities_get_permissions_option( 'display_hints' ) ) :?>
				<div class="cme-subtext" style="margin-top:0">
				<?php /*_e( '(PP Filtered Post Types, Taxonomies)', 'capsman-enhanced' ); */?>
				</div>
				<?php endif;
				
				echo "<table style='width:100%'><tr>";
				
				// bbPress' dynamic role def requires additional code to enforce stored caps
				$unfiltered = apply_filters('presspermit_unfiltered_post_types', ['forum','topic','reply','wp_block', 'customize_changeset']);
				$unfiltered = (defined('PP_CAPABILITIES_NO_LEGACY_FILTERS')) ? $unfiltered : apply_filters('pp_unfiltered_post_types', $unfiltered);  // maintain legacy filter to support custom code
				
				$hidden = apply_filters('presspermit_hidden_post_types', []); 
				$hidden = apply_filters('pp_hidden_post_types', $hidden);  // maintain legacy filter to support custom code

				echo '<td style="width:50%">';
				
				$option_basename = 'enabled_post_types';
				$pp_prefix = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp' : 'presspermit';

				$enabled = get_option( $pp_prefix . '_' . $option_basename, array( 'post' => true, 'page' => true ) );
				
				foreach( $defined_types as $key => $type_obj ) {
					if ( ! $key )
						continue;

					if ( in_array( $key, $unfiltered ) )
						continue;
						
					$id = "$option_basename-" . $key;
					?>
					<div style="text-align:left">
					<?php if ( ! empty( $hidden[$key] ) ) :?>
						<input name="<?php echo($id);?>" type="hidden" id="<?php echo($id);?>" value="1" />
						<input name="<?php echo("{$option_basename}-options[]");?>" type="hidden" value="<?php echo($key)?>" />
					
					<?php else: ?>
						<div class="agp-vspaced_input">
						<label for="<?php echo($id);?>" title="<?php echo($key);?>">
						<input name="<?php echo("{$option_basename}-options[]");?>" type="hidden" value="<?php echo($key)?>" />
						<input name="<?php echo($id);?>" type="checkbox" id="<?php echo($id);?>" autocomplete="off" value="1" <?php checked('1', ! empty($enabled[$key]) );?> /> <?php echo($type_obj->label);?>
						
						<?php 
						echo ('</label></div>');

					endif;  // displaying checkbox UI
					
					echo '</div>';
				}
				echo '</td>';
				?>
				</tr>
				</table>
				
				<?php 
				
				$define_create_posts_cap = get_option("{$pp_prefix}_define_create_posts_cap");?>
				
					<div style="margin-top:10px;margin-bottom:10px">
					<label for="pp_define_create_posts_cap">
					<input name="pp_define_create_posts_cap" type="checkbox" id="pp_define_create_posts_cap" autocomplete="off" value="1" <?php checked('1', $define_create_posts_cap );?> title="<?php esc_attr( _e( 'Make selected post types require a different capability to add new posts.', 'capsman-enhanced') );?>" /> <?php _e('Use create_posts capability');?>
					</label>
					</div>
				
				<?php
				do_action('pp-capabilities-type-specific-ui');
				?>

				<input type="submit" name="update_filtered_types" value="<?php _e('Update', 'capsman-enhanced') ?>" class="button" />
			</dd>
		</dl>
		<?php
	}
	
	// Note: CME can now impose type-specific capabilities without Press Permit Core active
	function pp_taxonomies_ui( $defined_taxonomies ) {
		?>
		<dl>
			<dt><?php _e('Taxonomy-Specific Capabilities', 'capsman-enhanced'); ?></dt>
			<dd style="text-align:center;">
				<?php
				$caption = __( 'Ensure permissions can be controlled separately from other taxonomies.', 'capsman-enhanced' );
				echo "<p class='cme-hint'>$caption</p>";
				
				echo "<table style='width:100%'><tr>";
				
				$unfiltered = apply_filters( 'pp_unfiltered_taxonomies', array( 'post_status', 'topic-tag' ) );  // avoid confusion with Edit Flow administrative taxonomy
				$hidden = apply_filters( 'pp_hidden_taxonomies', array() );
				
				echo '<td style="width:50%">';

				$pp_prefix = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp' : 'presspermit';

				$option_basename = 'enabled_taxonomies';
				$option_name = $pp_prefix . '_' . $option_basename;
				
				$enabled = get_option( $option_name, array() );
				
				foreach( $defined_taxonomies as $taxonomy => $type_obj ) {
					if ( ! $taxonomy )
						continue;

					if ( in_array( $taxonomy, $unfiltered ) )
						continue;
					
					$id = "$option_basename-" . $taxonomy;
					?>
					<div style="text-align:left">
					<?php if ( ! empty( $hidden[$taxonomy] ) ) :?>
						<input name="<?php echo($id);?>" type="hidden" id="<?php echo($id);?>" value="1" />
						<input name="<?php echo("{$option_basename}-options[]");?>" type="hidden" value="<?php echo($taxonomy)?>" />
					
					<?php else: ?>
						<div class="agp-vspaced_input">
						<label for="<?php echo($id);?>" title="<?php echo($taxonomy);?>">
						<input name="<?php echo("{$option_basename}-options[]");?>" type="hidden" value="<?php echo($taxonomy)?>" />
						<input name="<?php echo($id);?>" type="checkbox" autocomplete="off" id="<?php echo($id);?>" value="1" <?php checked('1', ! empty($enabled[$taxonomy]) );?> /> <?php echo($type_obj->label);?>
						
						<?php 
						echo ('</label></div>');

					endif;  // displaying checkbox UI
					
					echo '</div>';
				}
				echo '</td>';

				?>
				</tr>
				</table>
				
				<input type="submit" name="update_filtered_taxonomies" value="<?php _e('Update', 'capsman-enhanced') ?>" class="button" />
			</dd>
		</dl>
		
		<dl>
			<dt><?php _e('Detailed Taxonomy Capabilities', 'capsman-enhanced'); ?></dt>
			<dd style="text-align:center;">
				<?php
				$caption = __( 'Enforce Edit, Delete and Assign capabilities separately from Management capability.', 'capsman-enhanced' );
				echo "<p class='cme-hint'>$caption</p>";
				
				echo "<table style='width:100%'><tr>";
				
				$unfiltered = apply_filters( 'pp_unfiltered_taxonomies', array( 'post_status', 'topic-tag' ) );  // avoid confusion with Edit Flow administrative taxonomy
				$hidden = apply_filters( 'pp_hidden_taxonomies', array() );
				
				echo '<td style="width:50%">';

				$option_basename = 'detailed_taxonomies';
				$option_name = 'cme_' . $option_basename;
				
				$enabled = get_option( $option_name, array() );
				
				foreach( $defined_taxonomies as $taxonomy => $type_obj ) {
					if ( ! $taxonomy )
						continue;

					if ( in_array( $taxonomy, $unfiltered ) )
						continue;
					
					$id = "$option_basename-" . $taxonomy;
					?>
					<div style="text-align:left">
					<?php if ( ! empty( $hidden[$taxonomy] ) ) :?>
						<input name="<?php echo($id);?>" type="hidden" id="<?php echo($id);?>" value="1" />
						<input name="<?php echo("{$option_basename}-options[]");?>" type="hidden" value="<?php echo($taxonomy)?>" />
					
					<?php else: ?>
						<div class="agp-vspaced_input">
						<label for="<?php echo($id);?>" title="<?php echo($taxonomy);?>">
						<input name="<?php echo("{$option_basename}-options[]");?>" type="hidden" value="<?php echo($taxonomy)?>" />
						<input name="<?php echo($id);?>" type="checkbox" autocomplete="off" id="<?php echo($id);?>" value="1" <?php checked('1', ! empty($enabled[$taxonomy]) );?> /> <?php echo($type_obj->label);?>
						
						<?php 
						echo ('</label></div>');

					endif;  // displaying checkbox UI
					
					echo '</div>';
				}
				echo '</td>';

				?>
				</tr>
				</table>
				
				<input type="submit" name="update_detailed_taxonomies" value="<?php _e('Update', 'capsman-enhanced') ?>" class="button" />
			</dd>
		</dl>
		<?php
	}
}

