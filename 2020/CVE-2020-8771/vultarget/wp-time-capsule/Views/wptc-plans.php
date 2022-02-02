<?php

class Wptc_Plans
{
	public $slot_dets;
	public $active_sites;

	public $plan_group_set;
	public $plans_interval_arr_by_group;

	function __construct() {
		$this->config = WPTC_Factory::get('config');
	}

	public function hide_if_legacy_plans_and_not_purchased($single_plan_info) {
		$plan_group_hide = '';
		if($single_plan_info['plan_status'] != 'active' && $single_plan_info['plan_status'] == 'legacy' && $single_plan_info['plan_index'] != $this->slot_dets['slot_index']){
			$plan_group_hide = 'display:none;';
		}

		return $plan_group_hide;
	}

	private function generate_plan_dec_ul($single_plan_info) {
		$sub_div = '';

		$sites_left_text = '';
		if($this->slot_dets['slot_index'] == $single_plan_info['plan_index']){
			if(!empty($single_plan_info['plan_slots'])){
				if($single_plan_info['plan_slots'] >= 200){
					$sites_left_text = '<strong>Unlimited</strong> sites';
				} else {
					$remainingSites = ($single_plan_info['plan_slots'] - $this->active_sites);
					if($remainingSites > 0){
						$sites_left_text = '<strong>' . $remainingSites . '</strong> <span class="plan_desc_sub"> sites left</span>';
					}
				}
			}
		}

		if(!empty($single_plan_info['plan_dec'])){
			$sub_div .= '<li>'.$sites_left_text.'</li>';
			foreach($single_plan_info['plan_dec'] as $v){
				$sub_div .= '<li>'.$v.'</li>';
			}
		}

		$ul_div = '<ul class="plan_desc_wptc">' . $sub_div . '</ul>';

		return $ul_div;
	}

	private function get_formatted_price_div($single_plan_info) {
		$price_text = '';

		$dont_show_dollar = '';
		if(empty($single_plan_info['plan_amt'])){
			$dont_show_dollar = 'display: none;';
			$single_plan_info['plan_amt'] = 'FREE';
		}

		// if($single_plan_info['plan_interval'] == 'yearly'){
		// 	$single_plan_info['plan_amt'] = ( (int) $single_plan_info['plan_amt'] / 12);
		// 	if($single_plan_info['plan_offer_cut_amt'] != 0){
		// 		$single_plan_info['plan_offer_cut_amt'] = ($single_plan_info['plan_offer_cut_amt'] / 12);
		// 	}
		// }

		$dont_show_offer_cut = '';
		if($single_plan_info['plan_offer_cut_amt'] == 0){
			$dont_show_offer_cut = 'display:none;';
		}

		$offer_cut_class = 'base_offer_cut offer_cut';
		if($single_plan_info['plan_offer_cut_amt'] > 10 && $single_plan_info['plan_offer_cut_amt'] < 20){
			$offer_cut_class = 'base_offer_cut offer_cut_special_10';
		} else if($single_plan_info['plan_offer_cut_amt'] > 20){
			$offer_cut_class = 'base_offer_cut offer_cut_special_20';
		}

		$monthly_yearly_or_lifetime = $single_plan_info['plan_interval'] !== 'lifetime' ? '/' . $single_plan_info['plan_interval'] : 'one-time';

		$price_text .= '<div class="dollar_group">
			<span class="dollar_wptc '.$offer_cut_class.'" style="'.$dont_show_offer_cut.'"><span class="kutty_dollar" style="'.$dont_show_dollar.'">$</span>' . $single_plan_info['plan_offer_cut_amt'] . '</span>
			<span class="dollar_wptc"><span class="kutty_dollar" style="'.$dont_show_dollar.'">$</span>' . $single_plan_info['plan_amt'] . '</span> <span class="price_interval_wptc" style="'.$dont_show_dollar.'"> '.$monthly_yearly_or_lifetime.'</span></div>';

		return $price_text;
	}

	private function juggleLifetime($is_group_has_2_plans, $cur_plan_grp) {
		//To check if this plan group has both lifetime and yearly interval or lifetime and monthly interval
		$in_arr_res = in_array('lifetime', $this->plans_interval_arr_by_group[$cur_plan_grp]);

		return ( $is_group_has_2_plans && $in_arr_res );
	}

	private function generate_sub_div($single_plan_info = null, $plan_id = null, $is_card_added = false, $encoded_email = false, $encoded_pwd = false) {
		$sub_div = '';

		$to_purchase = array();
		$to_purchase['show_purchase_dialog'] = $single_plan_info['plan_index'];
		$to_purchase['show_purchase_url'] = base64_encode(home_url());

		$query_str = http_build_query($to_purchase, '', '&');

		$is_free_plan = '';

		if(!empty($single_plan_info['is_free_plan'])){
			$is_free_plan = 'is_free_plan';
		}

		$is_slot_plan = '';

		if(!empty($single_plan_info['plan_slots']) && $single_plan_info['plan_slots'] > 1){
			$is_slot_plan = 'is_slot_plan';
		}

		$is_group_has_2_plans = ($this->plan_group_set[$single_plan_info['plan_group']] > 1) ? true : false;

		$hide_radio_buttons = '';
		$hide_yearly_style = '';

		$monthly_radio_checked = 'checked';
		$yearly_radio_checked = '';

		if(!$is_group_has_2_plans){
			$hide_radio_buttons = 'display:none;';
		}

		if($single_plan_info['plan_interval'] == 'yearly' && $is_group_has_2_plans){
			$hide_yearly_style = 'display:none;';

			if($single_plan_info['plan_status'] == 'active'){
				$yearly_radio_checked = 'checked';
				$monthly_radio_checked = '';
			}
		}

		if ($single_plan_info['plan_interval'] == 'lifetime') {
			$hide_radio_buttons = 'display:none;';
		}

		$this_plan_is_active_slot = false;
		if($this->slot_dets['slot_index'] == $single_plan_info['plan_index']){
			$this_plan_is_active_slot = true;
		}

		$plan_legacy_hide = $this->hide_if_legacy_plans_and_not_purchased($single_plan_info);

		$plan_select_btn_text = 'Select';
		$plan_disable_class = $disabled = $redirect_to_purchase_page = $purchase_url = '';
		$buy_now_class = 'select_now';
		if($single_plan_info['plan_status'] != 'active' && !$this_plan_is_active_slot){
			if($single_plan_info['plan_status'] == 'coming_soon'){
				$plan_select_btn_text = 'Coming Soon';
			}
			$plan_disable_class = 'plan_disable';
			$disabled = 'disabled';
		} else if($single_plan_info['plan_slots'] > 1 && !$this_plan_is_active_slot ) {
			$plan_select_btn_text = 'Buy now';
			$redirect_to_purchase_page = 'redirect_to_purchase_page';
			$purchase_url = $single_plan_info['purchase_url'];
			$buy_now_class = 'buy_now';
		}

		$monthly_or_lifetime = 'monthly';
		$hide_lifetime_style = '';
		if($this->juggleLifetime($is_group_has_2_plans, $single_plan_info['plan_group'])){
			$monthly_or_lifetime = 'lifetime';
			$hide_radio_buttons = '';
			if($single_plan_info['plan_interval'] != 'lifetime'){
				$hide_lifetime_style = 'display: none;';
			}
			if($single_plan_info['plan_interval'] == 'yearly'){
				$yearly_radio_checked = 'checked';
			}
			if($single_plan_info['plan_interval'] == 'monthly' || $single_plan_info['plan_interval'] == 'lifetime'){
				$monthly_radio_checked = 'checked';
			}
		}

		$sub_div .= '<div class="package_wptc '. $plan_disable_class.' '.$single_plan_info['plan_group'].' '.$single_plan_info['plan_interval'].'" plan_group_class="'.$single_plan_info['plan_group'].'" style="'.$hide_yearly_style. ' ' . $hide_lifetime_style . ' ' . $plan_legacy_hide .'">
		  <div class="package_wptc_border">
			<div class="plan_interval_group" style="'.$hide_radio_buttons.'">
				<label class="radio-inline plan_interval_change_wptc"><input type="radio" '.$monthly_radio_checked.' name="plan_int_radio_'.$plan_id.'">'.$monthly_or_lifetime.'</label>
				<label class="radio-inline plan_interval_change_wptc"><input type="radio" '.$yearly_radio_checked.' name="plan_int_radio_'.$plan_id.'">yearly</label>
			</div>
			<div class="plan_name_wptc">'.$single_plan_info['plan_name'].'</div>
			<div class="price_wptc">'.$this->get_formatted_price_div($single_plan_info).'</div>
			'.$this->generate_plan_dec_ul($single_plan_info).'
			<div style="clear:both"></div>
			<input class="selected_plan_dets_wptc" type="hidden" to_purchase_wptc="'.$query_str.'" />
			<a class="selected_plan_dets_wptc_target" style="display:none" href="'. WPTC_APSERVER_URL . '/index.php?'.$query_str.'"></a>
			<button class="plan_select_btn_wptc '.$buy_now_class . ' ' . $redirect_to_purchase_page.' '.$is_free_plan.' ' . $is_slot_plan . '" '.$disabled.' purchase_url="'.$purchase_url.'" card_added="'.$is_card_added.'" site_url="'.home_url().'" plan_index="'.$single_plan_info['plan_index'].'" plan_name="'.$single_plan_info['plan_name'].'" plan_id="'.$plan_id.'" >'.$plan_select_btn_text.'</button>
		   </div>
		</div>';

		return $sub_div;
	}

	public function echo_plan_box_div_wptc() {
		$div = '';
		$sub_div = '';

		$privileges_wptc = $this->config->get_option('privileges_wptc');
		$plan_info = $this->config->get_option('plan_info');
		$is_card_added = $this->config->get_option('card_added');

		$this->slot_dets = $this->config->get_option('user_slot_info');
		$this->slot_dets = json_decode($this->slot_dets, true);
		$this->active_sites = $this->config->get_option('active_sites');
		$this->active_sites = trim($this->active_sites, '"');

		$plan_info = json_decode($plan_info, true);

		$encoded_pwd = $this->config->get_option('wptc_main_acc_pwd_temp');

		$encoded_email = $this->config->get_option('wptc_main_acc_email_temp');

		if(!empty($plan_info) && is_array($plan_info)){
			$this->plan_group_set = array();
			$this->plans_interval_arr_by_group = array();

			foreach($plan_info as $plan_id => $single_plan_info){
				if(empty($this->plan_group_set[$single_plan_info['plan_group']])){
					$this->plan_group_set[$single_plan_info['plan_group']] = 0;
				}
				if(empty($this->plans_interval_arr_by_group[$single_plan_info['plan_group']])){
					$this->plans_interval_arr_by_group[$single_plan_info['plan_group']] = array();
				}
				if($single_plan_info['plan_status'] == 'active'){
					$this->plan_group_set[$single_plan_info['plan_group']]++;
					$this->plans_interval_arr_by_group[$single_plan_info['plan_group']][] = $single_plan_info['plan_interval'];
				}
			}

			$plan_info = $this->sort_plans($plan_info);

			foreach($plan_info as $plan_id => $plan_dets){
				$sub_div .= $this->generate_sub_div($plan_dets, $plan_id, $is_card_added);
			}
		}

		$click_here_after_checkout = '<div class="click_here_after_checkout_wptc_container" ><button class="btn_pri click_here_after_checkout_wptc" style="display:none;">Click here after successful checkout</button></div>';

		$div .= '<div class="wptc_plans_title_info_text" style="background: none; color: white;">Select Plan</div>
		<input type="hidden" class="selected_plan_index_wptc"/><input type="hidden" class="selected_user_token_wptc" value="'.$encoded_pwd.'"/>
			<input type="hidden" class="selected_user_email_wptc" value="'.$encoded_email.'" />'.$click_here_after_checkout.'<div class="wptc_plans_wrapper">' . $sub_div . '</div>';

		echo $div;
	}

	private function sort_plans($plans){

		$sorted_plans = array();

		foreach ($plans as $key => $plan) {
		    $sorted_plans[$key] = $plan['plan_name'];
		}

		array_multisort($sorted_plans, SORT_DESC, $plans);

		return $plans;
	}

	public function show_plans_tab(){
		$plans = $this->config->get_option('plan_info_limited');

		if (!$plans) {
			return true;
		}

		return false;
	}

	public function get_select_plan_html(){
		$plans = $this->config->get_option('plan_info_limited');

		if (!$plans) {
			return '';
		}

		$plans = unserialize($plans);

		$html = '<div class="wptc-select-plans-div">
					<span>Current Plan </span>
					<select class="wptc-select-plans">';


		foreach ($plans as $key => $plan) {

			$selected = $selected_plan = '';

			if (!empty($plan['currently_active']) && $plan['currently_active']) {
				$selected 		= 'selected';
				$selected_plan  = $plan['plan_name'];
			}
			$html .= "<option value='". $plan['plan_index'] ."' " . $selected . ">" . $plan['plan_name'] . "</option>";
		}

		$html .= '</select> </div> <div id="wptc-select-plans-status" class="notice notice-success notice-alt"><p> </p></div>';
		return $html;
	}

}
