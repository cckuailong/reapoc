<?php

class Wptc_On_Demand_Backup_Hooks_Hanlder extends Wptc_Base_Hooks_Handler{
	protected $on_demand_backup;
	protected $config;

	public function __construct() {
		$this->on_demand_backup = WPTC_Pro_Factory::get('Wptc_On_Demand_Backup');
		$this->config = WPTC_Pro_Factory::get('Wptc_On_Demand_Backup_Config');
	}

	public function get_html($other_process_going_on){
		if( is_wptc_filter_registered('hide_this_option_wl_wptc') 
			&& apply_filters('hide_this_option_wl_wptc', 'trigger_backup')){
			return '';
		}

		$disable 	 		 = ($other_process_going_on === 'Staging Process') ? 'disabled' : '';
		$status_start_note	 = ($other_process_going_on === "Backup Process")  ? 'style="display: none;"' : 'style="display: block;"';
		$status_stop_note	 = ($other_process_going_on === 'Backup Process')  ? "style='display: block;'" : "style='display: none;'";

		return	'<tr>
				<th scope="row"> ' . __( 'On-Demand Backup', 'wp-time-capsule' ) . ' </th>
				<td>
					<fieldset>
						<label>
							<a id="start_backup_from_settings" action="start" class="button-secondary ' . $disable . '" style="margin-top: -3px;" >Backup now</a>
						</label>
						<p ' . $status_start_note . '  class="description setting_backup_start_note_wptc"><?php esc_attr_e( "Click Backup Now to backup the latest changes.", "wp-time-capsule" ); ?></p>
					<p ' . $status_stop_note . ' class="description setting_backup_stop_note_wptc"><?php esc_attr_e( "Clicking on Stop Backup will erase all progress made in the current backup..", "wp-time-capsule" ); ?></p>
					</fieldset>
				</td>
			</tr>';
	}
}