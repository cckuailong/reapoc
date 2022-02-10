<?php
/** no direct access **/
defined('MECEXEC') or die();

// Search Form Options
$sf_options = get_post_meta($post->ID, 'sf-options', true);
?>
<div class="mec-calendar-metabox">
    
    <div class="mec-form-row mec-switcher">
        <?php $sf_status = get_post_meta($post->ID, 'sf_status', true); ?>
        <div class="mec-col-8">
            <label><?php _e('Show Search Form', 'modern-events-calendar-lite'); ?></label>
        </div>
        <div class="mec-col-4">
            <input type="hidden" name="mec[sf_status]" value="0" />
            <input type="checkbox" name="mec[sf_status]" id="mec_sf_status" value="1" <?php if($sf_status == '' or $sf_status == 1) echo 'checked="checked"'; ?> />
            <label for="mec_sf_status"></label>
        </div>
    </div>
    <div class="mec-form-row mec-switcher">
        <?php $sf_display_label = get_post_meta($post->ID, 'sf_display_label', true); ?>
        <div class="mec-col-8">
            <label><?php _e('Show Labels', 'modern-events-calendar-lite'); ?></label>
        </div>
        <div class="mec-col-4">
            <input type="hidden" name="mec[sf_display_label]" value="0" />
            <input type="checkbox" name="mec[sf_display_label]" id="mec_sf_display_label" value="1" <?php if($sf_display_label == 1) echo 'checked="checked"'; ?> />
            <label for="mec_sf_display_label"></label>
        </div>
    </div>
    <div class="mec-form-row mec-switcher">
        <?php $sf_reset_button = get_post_meta($post->ID, 'sf_reset_button', true); ?>
        <div class="mec-col-8">
            <label><?php _e('Show Reset Button', 'modern-events-calendar-lite'); ?></label>
        </div>
        <div class="mec-col-4">
            <input type="hidden" name="mec[sf_reset_button]" value="0" />
            <input type="checkbox" name="mec[sf_reset_button]" id="mec_sf_reset_button" value="1" <?php if($sf_reset_button == 1) echo 'checked="checked"'; ?> />
            <label for="mec_sf_reset_button"></label>
        </div>
    </div>
    <div class="mec-form-row mec-switcher">
        <?php $sf_refine = get_post_meta($post->ID, 'sf_refine', true); ?>
        <div class="mec-col-8">
            <label><?php _e('Refine Search Parameters', 'modern-events-calendar-lite'); ?></label>
        </div>
        <div class="mec-col-4">
            <input type="hidden" name="mec[sf_refine]" value="0" />
            <input type="checkbox" name="mec[sf_refine]" id="mec_sf_refine" value="1" <?php if($sf_refine == 1) echo 'checked="checked"'; ?> />
            <label for="mec_sf_refine"></label>
        </div>
    </div>
    
    <!-- Search Form OPTIONS -->
    <div class="mec-meta-box-fields" id="mec_meta_box_calendar_search_form_options">
        
        <div class="mec-search-forms-options-container">
            
            <!-- List View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_list_search_form_options_container">
                <?php $sf_options_list = isset($sf_options['list']) ? $sf_options['list'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][category][type]" id="mec_sf_list_category">
						<option value="0" <?php if(isset($sf_options_list['category']) and isset($sf_options_list['category']['type']) and $sf_options_list['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_list['category']) and isset($sf_options_list['category']['type']) and $sf_options_list['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_list['category']) and isset($sf_options_list['category']['type']) and $sf_options_list['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][location][type]" id="mec_sf_list_location">
						<option value="0" <?php if(isset($sf_options_list['location']) and isset($sf_options_list['location']['type']) and $sf_options_list['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_list['location']) and isset($sf_options_list['location']['type']) and $sf_options_list['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'list' ,'options'=>$sf_options_list)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][organizer][type]" id="mec_sf_list_organizer">
						<option value="0" <?php if(isset($sf_options_list['organizer']) and isset($sf_options_list['organizer']['type']) and $sf_options_list['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_list['organizer']) and isset($sf_options_list['organizer']['type']) and $sf_options_list['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][speaker][type]" id="mec_sf_list_speaker">
						<option value="0" <?php if(isset($sf_options_list['speaker']) and isset($sf_options_list['speaker']['type']) and $sf_options_list['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_list['speaker']) and isset($sf_options_list['speaker']['type']) and $sf_options_list['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][tag][type]" id="mec_sf_list_tag">
						<option value="0" <?php if(isset($sf_options_list['tag']) and isset($sf_options_list['tag']['type']) and $sf_options_list['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_list['tag']) and isset($sf_options_list['tag']['type']) and $sf_options_list['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][label][type]" id="mec_sf_list_label">
						<option value="0" <?php if(isset($sf_options_list['label']) and isset($sf_options_list['label']['type']) and $sf_options_list['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_list['label']) and isset($sf_options_list['label']['type']) and $sf_options_list['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][address_search][type]" id="mec_sf_list_address_search">
						<option value="0" <?php if(isset($sf_options_list['address_search']) and isset($sf_options_list['address_search']['type']) and $sf_options_list['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_list['address_search']) and isset($sf_options_list['address_search']['type']) and $sf_options_list['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][list][address_search][placeholder]" value="<?php if(isset($sf_options_list['address_search']) and isset($sf_options_list['address_search']['placeholder'])) echo $sf_options_list['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][event_cost][type]" id="mec_sf_list_event_cost">
                        <option value="0" <?php if(isset($sf_options_list['event_cost']) and isset($sf_options_list['event_cost']['type']) and $sf_options_list['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_list['event_cost']) and isset($sf_options_list['event_cost']['type']) and $sf_options_list['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_month_filter"><?php _e('Date Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][month_filter][type]" id="mec_sf_list_month_filter">
						<option value="0" <?php if(isset($sf_options_list['month_filter']) and isset($sf_options_list['month_filter']['type']) and $sf_options_list['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_list['month_filter']) and isset($sf_options_list['month_filter']['type']) and $sf_options_list['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Year & Month Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="date-range-picker" <?php if(isset($sf_options_list['month_filter']) and isset($sf_options_list['month_filter']['type']) and $sf_options_list['month_filter']['type'] == 'date-range-picker') echo 'selected="selected"'; ?>><?php _e('Date Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][time_filter][type]" id="mec_sf_list_time_filter">
                        <option value="0" <?php if(isset($sf_options_list['time_filter']) and isset($sf_options_list['time_filter']['type']) and $sf_options_list['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_list['time_filter']) and isset($sf_options_list['time_filter']['type']) and $sf_options_list['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_list_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][list][text_search][type]" id="mec_sf_list_text_search">
						<option value="0" <?php if(isset($sf_options_list['text_search']) and isset($sf_options_list['text_search']['type']) and $sf_options_list['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_list['text_search']) and isset($sf_options_list['text_search']['type']) and $sf_options_list['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][list][text_search][placeholder]" value="<?php if(isset($sf_options_list['text_search']) and isset($sf_options_list['text_search']['placeholder'])) echo $sf_options_list['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_list_search_form', $sf_options_list); ?>
            </div>
            
            <!-- Grid View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_grid_search_form_options_container">
                <?php $sf_options_grid = isset($sf_options['grid']) ? $sf_options['grid'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][category][type]" id="mec_sf_grid_category">
                        <option value="0" <?php if(isset($sf_options_grid['category']) and isset($sf_options_grid['category']['type']) and $sf_options_grid['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_grid['category']) and isset($sf_options_grid['category']['type']) and $sf_options_grid['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_grid['category']) and isset($sf_options_grid['category']['type']) and $sf_options_grid['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][location][type]" id="mec_sf_grid_location">
                        <option value="0" <?php if(isset($sf_options_grid['location']) and isset($sf_options_grid['location']['type']) and $sf_options_grid['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_grid['location']) and isset($sf_options_grid['location']['type']) and $sf_options_grid['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'grid', 'options'=>$sf_options_grid)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][organizer][type]" id="mec_sf_grid_organizer">
                        <option value="0" <?php if(isset($sf_options_grid['organizer']) and isset($sf_options_grid['organizer']['type']) and $sf_options_grid['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_grid['organizer']) and isset($sf_options_grid['organizer']['type']) and $sf_options_grid['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][speaker][type]" id="mec_sf_grid_speaker">
                        <option value="0" <?php if(isset($sf_options_grid['speaker']) and isset($sf_options_grid['speaker']['type']) and $sf_options_grid['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_grid['speaker']) and isset($sf_options_grid['speaker']['type']) and $sf_options_grid['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][tag][type]" id="mec_sf_grid_tag">
						<option value="0" <?php if(isset($sf_options_grid['tag']) and isset($sf_options_grid['tag']['type']) and $sf_options_grid['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_grid['tag']) and isset($sf_options_grid['tag']['type']) and $sf_options_grid['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][label][type]" id="mec_sf_grid_label">
                        <option value="0" <?php if(isset($sf_options_grid['label']) and isset($sf_options_grid['label']['type']) and $sf_options_grid['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_grid['label']) and isset($sf_options_grid['label']['type']) and $sf_options_grid['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][address_search][type]" id="mec_sf_grid_address_search">
						<option value="0" <?php if(isset($sf_options_grid['address_search']) and isset($sf_options_grid['address_search']['type']) and $sf_options_grid['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_grid['address_search']) and isset($sf_options_grid['address_search']['type']) and $sf_options_grid['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][grid][address_search][placeholder]" value="<?php if(isset($sf_options_grid['address_search']) and isset($sf_options_grid['address_search']['placeholder'])) echo $sf_options_grid['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][event_cost][type]" id="mec_sf_grid_event_cost">
                        <option value="0" <?php if(isset($sf_options_grid['event_cost']) and isset($sf_options_grid['event_cost']['type']) and $sf_options_grid['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_grid['event_cost']) and isset($sf_options_grid['event_cost']['type']) and $sf_options_grid['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_month_filter"><?php _e('Date Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][month_filter][type]" id="mec_sf_grid_month_filter">
                        <option value="0" <?php if(isset($sf_options_grid['month_filter']) and isset($sf_options_grid['month_filter']['type']) and $sf_options_grid['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_grid['month_filter']) and isset($sf_options_grid['month_filter']['type']) and $sf_options_grid['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Year & Month Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="date-range-picker" <?php if(isset($sf_options_grid['month_filter']) and isset($sf_options_grid['month_filter']['type']) and $sf_options_grid['month_filter']['type'] == 'date-range-picker') echo 'selected="selected"'; ?>><?php _e('Date Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][time_filter][type]" id="mec_sf_grid_time_filter">
                        <option value="0" <?php if(isset($sf_options_grid['time_filter']) and isset($sf_options_grid['time_filter']['type']) and $sf_options_grid['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_grid['time_filter']) and isset($sf_options_grid['time_filter']['type']) and $sf_options_grid['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_grid_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][grid][text_search][type]" id="mec_sf_grid_text_search">
                        <option value="0" <?php if(isset($sf_options_grid['text_search']) and isset($sf_options_grid['text_search']['type']) and $sf_options_grid['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_grid['text_search']) and isset($sf_options_grid['text_search']['type']) and $sf_options_grid['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][grid][text_search][placeholder]" value="<?php if(isset($sf_options_grid['text_search']) and isset($sf_options_grid['text_search']['placeholder'])) echo $sf_options_grid['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_grid_search_form', $sf_options_grid); ?>
            </div>

            <!-- Agenda View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_agenda_search_form_options_container">
                <?php $sf_options_agenda = isset($sf_options['agenda']) ? $sf_options['agenda'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][category][type]" id="mec_sf_agenda_category">
                        <option value="0" <?php if(isset($sf_options_agenda['category']) and isset($sf_options_agenda['category']['type']) and $sf_options_agenda['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_agenda['category']) and isset($sf_options_agenda['category']['type']) and $sf_options_agenda['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_agenda['category']) and isset($sf_options_agenda['category']['type']) and $sf_options_agenda['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][location][type]" id="mec_sf_agenda_location">
                        <option value="0" <?php if(isset($sf_options_agenda['location']) and isset($sf_options_agenda['location']['type']) and $sf_options_agenda['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_agenda['location']) and isset($sf_options_agenda['location']['type']) and $sf_options_agenda['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'agenda','options'=>$sf_options_agenda)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][organizer][type]" id="mec_sf_agenda_organizer">
                        <option value="0" <?php if(isset($sf_options_agenda['organizer']) and isset($sf_options_agenda['organizer']['type']) and $sf_options_agenda['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_agenda['organizer']) and isset($sf_options_agenda['organizer']['type']) and $sf_options_agenda['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][speaker][type]" id="mec_sf_agenda_speaker">
                        <option value="0" <?php if(isset($sf_options_agenda['speaker']) and isset($sf_options_agenda['speaker']['type']) and $sf_options_agenda['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_agenda['speaker']) and isset($sf_options_agenda['speaker']['type']) and $sf_options_agenda['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][tag][type]" id="mec_sf_agenda_tag">
						<option value="0" <?php if(isset($sf_options_agenda['tag']) and isset($sf_options_agenda['tag']['type']) and $sf_options_agenda['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_agenda['tag']) and isset($sf_options_agenda['tag']['type']) and $sf_options_agenda['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][label][type]" id="mec_sf_agenda_label">
                        <option value="0" <?php if(isset($sf_options_agenda['label']) and isset($sf_options_agenda['label']['type']) and $sf_options_agenda['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_agenda['label']) and isset($sf_options_agenda['label']['type']) and $sf_options_agenda['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][address_search][type]" id="mec_sf_agenda_address_search">
						<option value="0" <?php if(isset($sf_options_agenda['address_search']) and isset($sf_options_agenda['address_search']['type']) and $sf_options_agenda['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_agenda['address_search']) and isset($sf_options_agenda['address_search']['type']) and $sf_options_agenda['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][agenda][address_search][placeholder]" value="<?php if(isset($sf_options_agenda['address_search']) and isset($sf_options_agenda['address_search']['placeholder'])) echo $sf_options_agenda['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][event_cost][type]" id="mec_sf_agenda_event_cost">
                        <option value="0" <?php if(isset($sf_options_agenda['event_cost']) and isset($sf_options_agenda['event_cost']['type']) and $sf_options_agenda['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_agenda['event_cost']) and isset($sf_options_agenda['event_cost']['type']) and $sf_options_agenda['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_month_filter"><?php _e('Date Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][month_filter][type]" id="mec_sf_agenda_month_filter">
                        <option value="0" <?php if(isset($sf_options_agenda['month_filter']) and isset($sf_options_agenda['month_filter']['type']) and $sf_options_agenda['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_agenda['month_filter']) and isset($sf_options_agenda['month_filter']['type']) and $sf_options_agenda['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Year & Month Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="date-range-picker" <?php if(isset($sf_options_agenda['month_filter']) and isset($sf_options_agenda['month_filter']['type']) and $sf_options_agenda['month_filter']['type'] == 'date-range-picker') echo 'selected="selected"'; ?>><?php _e('Date Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][time_filter][type]" id="mec_sf_agenda_time_filter">
                        <option value="0" <?php if(isset($sf_options_agenda['time_filter']) and isset($sf_options_agenda['time_filter']['type']) and $sf_options_agenda['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_agenda['time_filter']) and isset($sf_options_agenda['time_filter']['type']) and $sf_options_agenda['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_agenda_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][agenda][text_search][type]" id="mec_sf_agenda_text_search">
                        <option value="0" <?php if(isset($sf_options_agenda['text_search']) and isset($sf_options_agenda['text_search']['type']) and $sf_options_agenda['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_agenda['text_search']) and isset($sf_options_agenda['text_search']['type']) and $sf_options_agenda['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][agenda][text_search][placeholder]" value="<?php if(isset($sf_options_agenda['text_search']) and isset($sf_options_agenda['text_search']['placeholder'])) echo $sf_options_agenda['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_agenda_search_form', $sf_options_agenda); ?>
            </div>
            
            <!-- Full Calendar -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_full_calendar_search_form_options_container">
                <?php $sf_options_full_calendar = isset($sf_options['full_calendar']) ? $sf_options['full_calendar'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][category][type]" id="mec_sf_full_calendar_category">
                        <option value="0" <?php if(isset($sf_options_full_calendar['category']) and isset($sf_options_full_calendar['category']['type']) and $sf_options_full_calendar['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_full_calendar['category']) and isset($sf_options_full_calendar['category']['type']) and $sf_options_full_calendar['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_full_calendar['category']) and isset($sf_options_full_calendar['category']['type']) and $sf_options_full_calendar['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][location][type]" id="mec_sf_full_calendar_location">
						<option value="0" <?php if(isset($sf_options_full_calendar['location']) and isset($sf_options_full_calendar['location']['type']) and $sf_options_full_calendar['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_full_calendar['location']) and isset($sf_options_full_calendar['location']['type']) and $sf_options_full_calendar['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'full_calendar' ,'options'=>$sf_options_full_calendar)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][organizer][type]" id="mec_sf_full_calendar_organizer">
						<option value="0" <?php if(isset($sf_options_full_calendar['organizer']) and isset($sf_options_full_calendar['organizer']['type']) and $sf_options_full_calendar['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_full_calendar['organizer']) and isset($sf_options_full_calendar['organizer']['type']) and $sf_options_full_calendar['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][speaker][type]" id="mec_sf_full_calendar_speaker">
						<option value="0" <?php if(isset($sf_options_full_calendar['speaker']) and isset($sf_options_full_calendar['speaker']['type']) and $sf_options_full_calendar['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_full_calendar['speaker']) and isset($sf_options_full_calendar['speaker']['type']) and $sf_options_full_calendar['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][tag][type]" id="mec_sf_full_calendar_tag">
						<option value="0" <?php if(isset($sf_options_full_calendar['tag']) and isset($sf_options_full_calendar['tag']['type']) and $sf_options_full_calendar['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_full_calendar['tag']) and isset($sf_options_full_calendar['tag']['type']) and $sf_options_full_calendar['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][label][type]" id="mec_sf_full_calendar_label">
						<option value="0" <?php if(isset($sf_options_full_calendar['label']) and isset($sf_options_full_calendar['label']['type']) and $sf_options_full_calendar['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_full_calendar['label']) and isset($sf_options_full_calendar['label']['type']) and $sf_options_full_calendar['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][address_search][type]" id="mec_sf_full_calendar_address_search">
						<option value="0" <?php if(isset($sf_options_full_calendar['address_search']) and isset($sf_options_full_calendar['address_search']['type']) and $sf_options_full_calendar['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_full_calendar['address_search']) and isset($sf_options_full_calendar['address_search']['type']) and $sf_options_full_calendar['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][full_calendar][address_search][placeholder]" value="<?php if(isset($sf_options_full_calendar['address_search']) and isset($sf_options_full_calendar['address_search']['placeholder'])) echo $sf_options_full_calendar['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][event_cost][type]" id="mec_sf_full_calendar_event_cost">
                        <option value="0" <?php if(isset($sf_options_full_calendar['event_cost']) and isset($sf_options_full_calendar['event_cost']['type']) and $sf_options_full_calendar['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_full_calendar['event_cost']) and isset($sf_options_full_calendar['event_cost']['type']) and $sf_options_full_calendar['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][month_filter][type]" id="mec_sf_full_calendar_month_filter">
						<option value="0" <?php if(isset($sf_options_full_calendar['month_filter']) and isset($sf_options_full_calendar['month_filter']['type']) and $sf_options_full_calendar['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_full_calendar['month_filter']) and isset($sf_options_full_calendar['month_filter']['type']) and $sf_options_full_calendar['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][time_filter][type]" id="mec_sf_full_calendar_time_filter">
                        <option value="0" <?php if(isset($sf_options_full_calendar['time_filter']) and isset($sf_options_full_calendar['time_filter']['type']) and $sf_options_full_calendar['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_full_calendar['time_filter']) and isset($sf_options_full_calendar['time_filter']['type']) and $sf_options_full_calendar['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_full_calendar_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][full_calendar][text_search][type]" id="mec_sf_full_calendar_text_search">
						<option value="0" <?php if(isset($sf_options_full_calendar['text_search']) and isset($sf_options_full_calendar['text_search']['type']) and $sf_options_full_calendar['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_full_calendar['text_search']) and isset($sf_options_full_calendar['text_search']['type']) and $sf_options_full_calendar['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][full_calendar][text_search][placeholder]" value="<?php if(isset($sf_options_full_calendar['text_search']) and isset($sf_options_full_calendar['text_search']['placeholder'])) echo $sf_options_full_calendar['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_full_calendar_search_form', $sf_options_full_calendar); ?>
            </div>
            
            <!-- Monthly View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_monthly_view_search_form_options_container">
                <?php $sf_options_monthly_view = isset($sf_options['monthly_view']) ? $sf_options['monthly_view'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][category][type]" id="mec_sf_monthly_view_category">
						<option value="0" <?php if(isset($sf_options_monthly_view['category']) and isset($sf_options_monthly_view['category']['type']) and $sf_options_monthly_view['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_monthly_view['category']) and isset($sf_options_monthly_view['category']['type']) and $sf_options_monthly_view['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_monthly_view['category']) and isset($sf_options_monthly_view['category']['type']) and $sf_options_monthly_view['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][location][type]" id="mec_sf_monthly_view_location">
						<option value="0" <?php if(isset($sf_options_monthly_view['location']) and isset($sf_options_monthly_view['location']['type']) and $sf_options_monthly_view['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_monthly_view['location']) and isset($sf_options_monthly_view['location']['type']) and $sf_options_monthly_view['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'monthly_view' ,'options'=>$sf_options_monthly_view)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][organizer][type]" id="mec_sf_monthly_view_organizer">
						<option value="0" <?php if(isset($sf_options_monthly_view['organizer']) and isset($sf_options_monthly_view['organizer']['type']) and $sf_options_monthly_view['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_monthly_view['organizer']) and isset($sf_options_monthly_view['organizer']['type']) and $sf_options_monthly_view['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][speaker][type]" id="mec_sf_monthly_view_speaker">
						<option value="0" <?php if(isset($sf_options_monthly_view['speaker']) and isset($sf_options_monthly_view['speaker']['type']) and $sf_options_monthly_view['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_monthly_view['speaker']) and isset($sf_options_monthly_view['speaker']['type']) and $sf_options_monthly_view['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][tag][type]" id="mec_sf_monthly_view_tag">
						<option value="0" <?php if(isset($sf_options_monthly_view['tag']) and isset($sf_options_monthly_view['tag']['type']) and $sf_options_monthly_view['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_monthly_view['tag']) and isset($sf_options_monthly_view['tag']['type']) and $sf_options_monthly_view['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][label][type]" id="mec_sf_monthly_view_label">
						<option value="0" <?php if(isset($sf_options_monthly_view['label']) and isset($sf_options_monthly_view['label']['type']) and $sf_options_monthly_view['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_monthly_view['label']) and isset($sf_options_monthly_view['label']['type']) and $sf_options_monthly_view['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][address_search][type]" id="mec_sf_monthly_view_address_search">
						<option value="0" <?php if(isset($sf_options_monthly_view['address_search']) and isset($sf_options_monthly_view['address_search']['type']) and $sf_options_monthly_view['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_monthly_view['address_search']) and isset($sf_options_monthly_view['address_search']['type']) and $sf_options_monthly_view['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][monthly_view][address_search][placeholder]" value="<?php if(isset($sf_options_monthly_view['address_search']) and isset($sf_options_monthly_view['address_search']['placeholder'])) echo $sf_options_monthly_view['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][event_cost][type]" id="mec_sf_monthly_view_event_cost">
                        <option value="0" <?php if(isset($sf_options_monthly_view['event_cost']) and isset($sf_options_monthly_view['event_cost']['type']) and $sf_options_monthly_view['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_monthly_view['event_cost']) and isset($sf_options_monthly_view['event_cost']['type']) and $sf_options_monthly_view['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][month_filter][type]" id="mec_sf_monthly_view_month_filter">
						<option value="0" <?php if(isset($sf_options_monthly_view['month_filter']) and isset($sf_options_monthly_view['month_filter']['type']) and $sf_options_monthly_view['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_monthly_view['month_filter']) and isset($sf_options_monthly_view['month_filter']['type']) and $sf_options_monthly_view['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][time_filter][type]" id="mec_sf_monthly_view_time_filter">
                        <option value="0" <?php if(isset($sf_options_monthly_view['time_filter']) and isset($sf_options_monthly_view['time_filter']['type']) and $sf_options_monthly_view['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_monthly_view['time_filter']) and isset($sf_options_monthly_view['time_filter']['type']) and $sf_options_monthly_view['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_monthly_view_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][monthly_view][text_search][type]" id="mec_sf_monthly_view_text_search">
						<option value="0" <?php if(isset($sf_options_monthly_view['text_search']) and isset($sf_options_monthly_view['text_search']['type']) and $sf_options_monthly_view['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_monthly_view['text_search']) and isset($sf_options_monthly_view['text_search']['type']) and $sf_options_monthly_view['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][monthly_view][text_search][placeholder]" value="<?php if(isset($sf_options_monthly_view['text_search']) and isset($sf_options_monthly_view['text_search']['placeholder'])) echo $sf_options_monthly_view['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_monthly_search_form', $sf_options_monthly_view); ?>
            </div>

            <!-- Yearly View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_yearly_view_search_form_options_container">
                <?php $sf_options_yearly_view = isset($sf_options['yearly_view']) ? $sf_options['yearly_view'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][category][type]" id="mec_sf_yearly_view_category">
                        <option value="0" <?php if(isset($sf_options_yearly_view['category']) and isset($sf_options_yearly_view['category']['type']) and $sf_options_yearly_view['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_yearly_view['category']) and isset($sf_options_yearly_view['category']['type']) and $sf_options_yearly_view['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_yearly_view['category']) and isset($sf_options_yearly_view['category']['type']) and $sf_options_yearly_view['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][location][type]" id="mec_sf_yearly_view_location">
                        <option value="0" <?php if(isset($sf_options_yearly_view['location']) and isset($sf_options_yearly_view['location']['type']) and $sf_options_yearly_view['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_yearly_view['location']) and isset($sf_options_yearly_view['location']['type']) and $sf_options_yearly_view['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'yearly_view' ,'options'=>$sf_options_yearly_view)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][organizer][type]" id="mec_sf_yearly_view_organizer">
                        <option value="0" <?php if(isset($sf_options_yearly_view['organizer']) and isset($sf_options_yearly_view['organizer']['type']) and $sf_options_yearly_view['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_yearly_view['organizer']) and isset($sf_options_yearly_view['organizer']['type']) and $sf_options_yearly_view['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][speaker][type]" id="mec_sf_yearly_view_speaker">
                        <option value="0" <?php if(isset($sf_options_yearly_view['speaker']) and isset($sf_options_yearly_view['speaker']['type']) and $sf_options_yearly_view['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_yearly_view['speaker']) and isset($sf_options_yearly_view['speaker']['type']) and $sf_options_yearly_view['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][tag][type]" id="mec_sf_yearly_view_tag">
						<option value="0" <?php if(isset($sf_options_yearly_view['tag']) and isset($sf_options_yearly_view['tag']['type']) and $sf_options_yearly_view['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_yearly_view['tag']) and isset($sf_options_yearly_view['tag']['type']) and $sf_options_yearly_view['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][label][type]" id="mec_sf_yearly_view_label">
                        <option value="0" <?php if(isset($sf_options_yearly_view['label']) and isset($sf_options_yearly_view['label']['type']) and $sf_options_yearly_view['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_yearly_view['label']) and isset($sf_options_yearly_view['label']['type']) and $sf_options_yearly_view['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][address_search][type]" id="mec_sf_yearly_view_address_search">
						<option value="0" <?php if(isset($sf_options_yearly_view['address_search']) and isset($sf_options_yearly_view['address_search']['type']) and $sf_options_yearly_view['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_yearly_view['address_search']) and isset($sf_options_yearly_view['address_search']['type']) and $sf_options_yearly_view['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][yearly_view][address_search][placeholder]" value="<?php if(isset($sf_options_yearly_view['address_search']) and isset($sf_options_yearly_view['address_search']['placeholder'])) echo $sf_options_yearly_view['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][event_cost][type]" id="mec_sf_yearly_view_event_cost">
                        <option value="0" <?php if(isset($sf_options_yearly_view['event_cost']) and isset($sf_options_yearly_view['event_cost']['type']) and $sf_options_yearly_view['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_yearly_view['event_cost']) and isset($sf_options_yearly_view['event_cost']['type']) and $sf_options_yearly_view['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][month_filter][type]" id="mec_sf_yearly_view_month_filter">
                        <option value="0" <?php if(isset($sf_options_yearly_view['month_filter']) and isset($sf_options_yearly_view['month_filter']['type']) and $sf_options_yearly_view['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_yearly_view['month_filter']) and isset($sf_options_yearly_view['month_filter']['type']) and $sf_options_yearly_view['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][time_filter][type]" id="mec_sf_yearly_view_time_filter">
                        <option value="0" <?php if(isset($sf_options_yearly_view['time_filter']) and isset($sf_options_yearly_view['time_filter']['type']) and $sf_options_yearly_view['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_yearly_view['time_filter']) and isset($sf_options_yearly_view['time_filter']['type']) and $sf_options_yearly_view['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_yearly_view_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][yearly_view][text_search][type]" id="mec_sf_yearly_view_text_search">
                        <option value="0" <?php if(isset($sf_options_yearly_view['text_search']) and isset($sf_options_yearly_view['text_search']['type']) and $sf_options_yearly_view['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_yearly_view['text_search']) and isset($sf_options_yearly_view['text_search']['type']) and $sf_options_yearly_view['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][yearly_view][text_search][placeholder]" value="<?php if(isset($sf_options_yearly_view['text_search']) and isset($sf_options_yearly_view['text_search']['placeholder'])) echo $sf_options_yearly_view['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_yearly_search_form', $sf_options_yearly_view); ?>
            </div>
            
            <!-- Map Skin -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_map_search_form_options_container">
                <?php $sf_options_map = isset($sf_options['map']) ? $sf_options['map'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][category][type]" id="mec_sf_map_category">
						<option value="0" <?php if(isset($sf_options_map['category']) and isset($sf_options_map['category']['type']) and $sf_options_map['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_map['category']) and isset($sf_options_map['category']['type']) and $sf_options_map['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_map['category']) and isset($sf_options_map['category']['type']) and $sf_options_map['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][location][type]" id="mec_sf_map_location">
						<option value="0" <?php if(isset($sf_options_map['location']) and isset($sf_options_map['location']['type']) and $sf_options_map['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_map['location']) and isset($sf_options_map['location']['type']) and $sf_options_map['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'map' ,'options'=>$sf_options_map)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][organizer][type]" id="mec_sf_map_organizer">
						<option value="0" <?php if(isset($sf_options_map['organizer']) and isset($sf_options_map['organizer']['type']) and $sf_options_map['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_map['organizer']) and isset($sf_options_map['organizer']['type']) and $sf_options_map['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][speaker][type]" id="mec_sf_map_speaker">
						<option value="0" <?php if(isset($sf_options_map['speaker']) and isset($sf_options_map['speaker']['type']) and $sf_options_map['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_map['speaker']) and isset($sf_options_map['speaker']['type']) and $sf_options_map['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][tag][type]" id="mec_sf_map_tag">
						<option value="0" <?php if(isset($sf_options_map['tag']) and isset($sf_options_map['tag']['type']) and $sf_options_map['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_map['tag']) and isset($sf_options_map['tag']['type']) and $sf_options_map['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][label][type]" id="mec_sf_map_label">
						<option value="0" <?php if(isset($sf_options_map['label']) and isset($sf_options_map['label']['type']) and $sf_options_map['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_map['label']) and isset($sf_options_map['label']['type']) and $sf_options_map['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][address_search][type]" id="mec_sf_map_address_search">
						<option value="0" <?php if(isset($sf_options_map['address_search']) and isset($sf_options_map['address_search']['type']) and $sf_options_map['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_map['address_search']) and isset($sf_options_map['address_search']['type']) and $sf_options_map['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][map][address_search][placeholder]" value="<?php if(isset($sf_options_map['address_search']) and isset($sf_options_map['address_search']['placeholder'])) echo $sf_options_map['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][event_cost][type]" id="mec_sf_map_event_cost">
                        <option value="0" <?php if(isset($sf_options_map['event_cost']) and isset($sf_options_map['event_cost']['type']) and $sf_options_map['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_map['event_cost']) and isset($sf_options_map['event_cost']['type']) and $sf_options_map['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_month_filter"><?php _e('Date Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][month_filter][type]" id="mec_sf_map_month_filter">
                        <option value="0" <?php if(isset($sf_options_map['month_filter']) and isset($sf_options_map['month_filter']['type']) and $sf_options_map['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_map['month_filter']) and isset($sf_options_map['month_filter']['type']) and $sf_options_map['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Year & Month Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="date-range-picker" <?php if(isset($sf_options_map['month_filter']) and isset($sf_options_map['month_filter']['type']) and $sf_options_map['month_filter']['type'] == 'date-range-picker') echo 'selected="selected"'; ?>><?php _e('Date Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_map_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][map][text_search][type]" id="mec_sf_map_text_search">
						<option value="0" <?php if(isset($sf_options_map['text_search']) and isset($sf_options_map['text_search']['type']) and $sf_options_map['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_map['text_search']) and isset($sf_options_map['text_search']['type']) and $sf_options_map['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][map][text_search][placeholder]" value="<?php if(isset($sf_options_map['text_search']) and isset($sf_options_map['text_search']['placeholder'])) echo $sf_options_map['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_map_search_form', $sf_options_map); ?>
            </div>
            
            <!-- Daily View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_daily_view_search_form_options_container">
                <?php $sf_options_daily_view = isset($sf_options['daily_view']) ? $sf_options['daily_view'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][category][type]" id="mec_sf_daily_view_category">
						<option value="0" <?php if(isset($sf_options_daily_view['category']) and isset($sf_options_daily_view['category']['type']) and $sf_options_daily_view['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_daily_view['category']) and isset($sf_options_daily_view['category']['type']) and $sf_options_daily_view['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_daily_view['category']) and isset($sf_options_daily_view['category']['type']) and $sf_options_daily_view['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][location][type]" id="mec_sf_daily_view_location">
						<option value="0" <?php if(isset($sf_options_daily_view['location']) and isset($sf_options_daily_view['location']['type']) and $sf_options_daily_view['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_daily_view['location']) and isset($sf_options_daily_view['location']['type']) and $sf_options_daily_view['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'daily_view' ,'options'=>$sf_options_daily_view)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][organizer][type]" id="mec_sf_daily_view_organizer">
						<option value="0" <?php if(isset($sf_options_daily_view['organizer']) and isset($sf_options_daily_view['organizer']['type']) and $sf_options_daily_view['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_daily_view['organizer']) and isset($sf_options_daily_view['organizer']['type']) and $sf_options_daily_view['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][speaker][type]" id="mec_sf_daily_view_speaker">
						<option value="0" <?php if(isset($sf_options_daily_view['speaker']) and isset($sf_options_daily_view['speaker']['type']) and $sf_options_daily_view['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_daily_view['speaker']) and isset($sf_options_daily_view['speaker']['type']) and $sf_options_daily_view['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][tag][type]" id="mec_sf_daily_view_tag">
						<option value="0" <?php if(isset($sf_options_daily_view['tag']) and isset($sf_options_daily_view['tag']['type']) and $sf_options_daily_view['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_daily_view['tag']) and isset($sf_options_daily_view['tag']['type']) and $sf_options_daily_view['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][label][type]" id="mec_sf_daily_view_label">
						<option value="0" <?php if(isset($sf_options_daily_view['label']) and isset($sf_options_daily_view['label']['type']) and $sf_options_daily_view['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_daily_view['label']) and isset($sf_options_daily_view['label']['type']) and $sf_options_daily_view['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][address_search][type]" id="mec_sf_daily_view_address_search">
						<option value="0" <?php if(isset($sf_options_daily_view['address_search']) and isset($sf_options_daily_view['address_search']['type']) and $sf_options_daily_view['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_daily_view['address_search']) and isset($sf_options_daily_view['address_search']['type']) and $sf_options_daily_view['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][daily_view][address_search][placeholder]" value="<?php if(isset($sf_options_daily_view['address_search']) and isset($sf_options_daily_view['address_search']['placeholder'])) echo $sf_options_daily_view['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][event_cost][type]" id="mec_sf_daily_view_event_cost">
                        <option value="0" <?php if(isset($sf_options_daily_view['event_cost']) and isset($sf_options_daily_view['event_cost']['type']) and $sf_options_daily_view['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_daily_view['event_cost']) and isset($sf_options_daily_view['event_cost']['type']) and $sf_options_daily_view['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][month_filter][type]" id="mec_sf_daily_view_month_filter">
						<option value="0" <?php if(isset($sf_options_daily_view['month_filter']) and isset($sf_options_daily_view['month_filter']['type']) and $sf_options_daily_view['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_daily_view['month_filter']) and isset($sf_options_daily_view['month_filter']['type']) and $sf_options_daily_view['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][time_filter][type]" id="mec_sf_daily_view_time_filter">
                        <option value="0" <?php if(isset($sf_options_daily_view['time_filter']) and isset($sf_options_daily_view['time_filter']['type']) and $sf_options_daily_view['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_daily_view['time_filter']) and isset($sf_options_daily_view['time_filter']['type']) and $sf_options_daily_view['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_daily_view_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][daily_view][text_search][type]" id="mec_sf_daily_view_text_search">
						<option value="0" <?php if(isset($sf_options_daily_view['text_search']) and isset($sf_options_daily_view['text_search']['type']) and $sf_options_daily_view['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_daily_view['text_search']) and isset($sf_options_daily_view['text_search']['type']) and $sf_options_daily_view['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][daily_view][text_search][placeholder]" value="<?php if(isset($sf_options_daily_view['text_search']) and isset($sf_options_daily_view['text_search']['placeholder'])) echo $sf_options_daily_view['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_daily_search_form', $sf_options_daily_view); ?>
            </div>
            
            <!-- Weekly View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_weekly_view_search_form_options_container">
                <?php $sf_options_weekly_view = isset($sf_options['weekly_view']) ? $sf_options['weekly_view'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][category][type]" id="mec_sf_weekly_view_category">
						<option value="0" <?php if(isset($sf_options_weekly_view['category']) and isset($sf_options_weekly_view['category']['type']) and $sf_options_weekly_view['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_weekly_view['category']) and isset($sf_options_weekly_view['category']['type']) and $sf_options_weekly_view['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_weekly_view['category']) and isset($sf_options_weekly_view['category']['type']) and $sf_options_weekly_view['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][location][type]" id="mec_sf_weekly_view_location">
						<option value="0" <?php if(isset($sf_options_weekly_view['location']) and isset($sf_options_weekly_view['location']['type']) and $sf_options_weekly_view['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_weekly_view['location']) and isset($sf_options_weekly_view['location']['type']) and $sf_options_weekly_view['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'weekly_view' ,'options'=>$sf_options_weekly_view)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][organizer][type]" id="mec_sf_weekly_view_organizer">
						<option value="0" <?php if(isset($sf_options_weekly_view['organizer']) and isset($sf_options_weekly_view['organizer']['type']) and $sf_options_weekly_view['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_weekly_view['organizer']) and isset($sf_options_weekly_view['organizer']['type']) and $sf_options_weekly_view['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][speaker][type]" id="mec_sf_weekly_view_speaker">
						<option value="0" <?php if(isset($sf_options_weekly_view['speaker']) and isset($sf_options_weekly_view['speaker']['type']) and $sf_options_weekly_view['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_weekly_view['speaker']) and isset($sf_options_weekly_view['speaker']['type']) and $sf_options_weekly_view['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][tag][type]" id="mec_sf_weekly_view_tag">
						<option value="0" <?php if(isset($sf_options_weekly_view['tag']) and isset($sf_options_weekly_view['tag']['type']) and $sf_options_weekly_view['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_weekly_view['tag']) and isset($sf_options_weekly_view['tag']['type']) and $sf_options_weekly_view['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][label][type]" id="mec_sf_weekly_view_label">
						<option value="0" <?php if(isset($sf_options_weekly_view['label']) and isset($sf_options_weekly_view['label']['type']) and $sf_options_weekly_view['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_weekly_view['label']) and isset($sf_options_weekly_view['label']['type']) and $sf_options_weekly_view['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][address_search][type]" id="mec_sf_weekly_view_address_search">
						<option value="0" <?php if(isset($sf_options_weekly_view['address_search']) and isset($sf_options_weekly_view['address_search']['type']) and $sf_options_weekly_view['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_weekly_view['address_search']) and isset($sf_options_weekly_view['address_search']['type']) and $sf_options_weekly_view['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][weekly_view][address_search][placeholder]" value="<?php if(isset($sf_options_weekly_view['address_search']) and isset($sf_options_weekly_view['address_search']['placeholder'])) echo $sf_options_weekly_view['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][event_cost][type]" id="mec_sf_weekly_view_event_cost">
                        <option value="0" <?php if(isset($sf_options_weekly_view['event_cost']) and isset($sf_options_weekly_view['event_cost']['type']) and $sf_options_weekly_view['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_weekly_view['event_cost']) and isset($sf_options_weekly_view['event_cost']['type']) and $sf_options_weekly_view['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][month_filter][type]" id="mec_sf_weekly_view_month_filter">
						<option value="0" <?php if(isset($sf_options_weekly_view['month_filter']) and isset($sf_options_weekly_view['month_filter']['type']) and $sf_options_weekly_view['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_weekly_view['month_filter']) and isset($sf_options_weekly_view['month_filter']['type']) and $sf_options_weekly_view['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][time_filter][type]" id="mec_sf_weekly_view_time_filter">
                        <option value="0" <?php if(isset($sf_options_weekly_view['time_filter']) and isset($sf_options_weekly_view['time_filter']['type']) and $sf_options_weekly_view['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_weekly_view['time_filter']) and isset($sf_options_weekly_view['time_filter']['type']) and $sf_options_weekly_view['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_weekly_view_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][weekly_view][text_search][type]" id="mec_sf_weekly_view_text_search">
						<option value="0" <?php if(isset($sf_options_weekly_view['text_search']) and isset($sf_options_weekly_view['text_search']['type']) and $sf_options_weekly_view['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_weekly_view['text_search']) and isset($sf_options_weekly_view['text_search']['type']) and $sf_options_weekly_view['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][weekly_view][text_search][placeholder]" value="<?php if(isset($sf_options_weekly_view['text_search']) and isset($sf_options_weekly_view['text_search']['placeholder'])) echo $sf_options_weekly_view['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_weekly_search_form', $sf_options_weekly_view); ?>
            </div>

            <!-- Timetable View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_timetable_search_form_options_container">
                <?php $sf_options_timetable = isset($sf_options['timetable']) ? $sf_options['timetable'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][category][type]" id="mec_sf_timetable_category">
                        <option value="0" <?php if(isset($sf_options_timetable['category']) and isset($sf_options_timetable['category']['type']) and $sf_options_timetable['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_timetable['category']) and isset($sf_options_timetable['category']['type']) and $sf_options_timetable['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_timetable['category']) and isset($sf_options_timetable['category']['type']) and $sf_options_timetable['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][location][type]" id="mec_sf_timetable_location">
                        <option value="0" <?php if(isset($sf_options_timetable['location']) and isset($sf_options_timetable['location']['type']) and $sf_options_timetable['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_timetable['location']) and isset($sf_options_timetable['location']['type']) and $sf_options_timetable['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'timetable' ,'options'=>$sf_options_timetable)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][organizer][type]" id="mec_sf_timetable_organizer">
                        <option value="0" <?php if(isset($sf_options_timetable['organizer']) and isset($sf_options_timetable['organizer']['type']) and $sf_options_timetable['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_timetable['organizer']) and isset($sf_options_timetable['organizer']['type']) and $sf_options_timetable['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][speaker][type]" id="mec_sf_timetable_speaker">
                        <option value="0" <?php if(isset($sf_options_timetable['speaker']) and isset($sf_options_timetable['speaker']['type']) and $sf_options_timetable['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_timetable['speaker']) and isset($sf_options_timetable['speaker']['type']) and $sf_options_timetable['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][tag][type]" id="mec_sf_timetable_tag">
						<option value="0" <?php if(isset($sf_options_timetable['tag']) and isset($sf_options_timetable['tag']['type']) and $sf_options_timetable['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_timetable['tag']) and isset($sf_options_timetable['tag']['type']) and $sf_options_timetable['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][label][type]" id="mec_sf_timetable_label">
                        <option value="0" <?php if(isset($sf_options_timetable['label']) and isset($sf_options_timetable['label']['type']) and $sf_options_timetable['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_timetable['label']) and isset($sf_options_timetable['label']['type']) and $sf_options_timetable['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][address_search][type]" id="mec_sf_timetable_address_search">
						<option value="0" <?php if(isset($sf_options_timetable['address_search']) and isset($sf_options_timetable['address_search']['type']) and $sf_options_timetable['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_timetable['address_search']) and isset($sf_options_timetable['address_search']['type']) and $sf_options_timetable['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][timetable][address_search][placeholder]" value="<?php if(isset($sf_options_timetable['address_search']) and isset($sf_options_timetable['address_search']['placeholder'])) echo $sf_options_timetable['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>" id="mec_sf_timetable_address_search_placeholder">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][event_cost][type]" id="mec_sf_timetable_event_cost">
                        <option value="0" <?php if(isset($sf_options_timetable['event_cost']) and isset($sf_options_timetable['event_cost']['type']) and $sf_options_timetable['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_timetable['event_cost']) and isset($sf_options_timetable['event_cost']['type']) and $sf_options_timetable['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][month_filter][type]" id="mec_sf_timetable_month_filter">
                        <option value="0" <?php if(isset($sf_options_timetable['month_filter']) and isset($sf_options_timetable['month_filter']['type']) and $sf_options_timetable['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_timetable['month_filter']) and isset($sf_options_timetable['month_filter']['type']) and $sf_options_timetable['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][time_filter][type]" id="mec_sf_timetable_time_filter">
                        <option value="0" <?php if(isset($sf_options_timetable['time_filter']) and isset($sf_options_timetable['time_filter']['type']) and $sf_options_timetable['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_timetable['time_filter']) and isset($sf_options_timetable['time_filter']['type']) and $sf_options_timetable['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_timetable_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][timetable][text_search][type]" id="mec_sf_timetable_text_search">
                        <option value="0" <?php if(isset($sf_options_timetable['text_search']) and isset($sf_options_timetable['text_search']['type']) and $sf_options_timetable['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_timetable['text_search']) and isset($sf_options_timetable['text_search']['type']) and $sf_options_timetable['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][timetable][text_search][placeholder]" value="<?php if(isset($sf_options_timetable['text_search']) and isset($sf_options_timetable['text_search']['placeholder'])) echo $sf_options_timetable['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>" id="mec_sf_timetable_txt_search_placeholder">
                </div>
                <?php do_action('mec_timetable_search_form', $sf_options_timetable); ?>
            </div>

            <!-- Masonry View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_masonry_search_form_options_container">
                <?php $sf_options_masonry = isset($sf_options['masonry']) ? $sf_options['masonry'] : array(); ?>
                <p><?php _e('No Search Options', 'modern-events-calendar-lite'); ?></p>
            </div>
            
            <!-- Cover -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_cover_search_form_options_container">
                <?php $sf_options_cover = isset($sf_options['cover']) ? $sf_options['cover'] : array(); ?>
                <p><?php _e('No Search Options', 'modern-events-calendar-lite'); ?></p>
            </div>
            
            <!-- Countdown -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_countdown_search_form_options_container">
                <?php $sf_options_countdown = isset($sf_options['countdown']) ? $sf_options['countdown'] : array(); ?>
                <p><?php _e('No Search Options', 'modern-events-calendar-lite'); ?></p>
            </div>

            <!-- Available Spot -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_available_spot_search_form_options_container">
                <?php $sf_options_available_spot = isset($sf_options['available_spot']) ? $sf_options['available_spot'] : array(); ?>
                <p><?php _e('No Search Options', 'modern-events-calendar-lite'); ?></p>
            </div>

            <!-- Carousel View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_carousel_search_form_options_container">
                <?php $sf_options_carousel = isset($sf_options['carousel']) ? $sf_options['carousel'] : array(); ?>
                <p><?php _e('No Search Options', 'modern-events-calendar-lite'); ?></p>
            </div>

            <!-- Slider -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_slider_search_form_options_container">
                <?php $sf_options_countdown = isset($sf_options['slider']) ? $sf_options['slider'] : array(); ?>
                <p><?php _e('No Search Options', 'modern-events-calendar-lite'); ?></p>
            </div>

            <!-- Tile View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_tile_search_form_options_container">
                <?php $sf_options_tile = isset($sf_options['tile']) ? $sf_options['tile'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][category][type]" id="mec_sf_tile_category">
                        <option value="0" <?php if(isset($sf_options_tile['category']) and isset($sf_options_tile['category']['type']) and $sf_options_tile['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_tile['category']) and isset($sf_options_tile['category']['type']) and $sf_options_tile['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_tile['category']) and isset($sf_options_tile['category']['type']) and $sf_options_tile['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][location][type]" id="mec_sf_tile_location">
                        <option value="0" <?php if(isset($sf_options_tile['location']) and isset($sf_options_tile['location']['type']) and $sf_options_tile['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_tile['location']) and isset($sf_options_tile['location']['type']) and $sf_options_tile['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'tile' ,'options'=>$sf_options_tile)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][organizer][type]" id="mec_sf_tile_organizer">
                        <option value="0" <?php if(isset($sf_options_tile['organizer']) and isset($sf_options_tile['organizer']['type']) and $sf_options_tile['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_tile['organizer']) and isset($sf_options_tile['organizer']['type']) and $sf_options_tile['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][speaker][type]" id="mec_sf_tile_speaker">
                        <option value="0" <?php if(isset($sf_options_tile['speaker']) and isset($sf_options_tile['speaker']['type']) and $sf_options_tile['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_tile['speaker']) and isset($sf_options_tile['speaker']['type']) and $sf_options_tile['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][tag][type]" id="mec_sf_tile_tag">
                        <option value="0" <?php if(isset($sf_options_tile['tag']) and isset($sf_options_tile['tag']['type']) and $sf_options_tile['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_tile['tag']) and isset($sf_options_tile['tag']['type']) and $sf_options_tile['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][label][type]" id="mec_sf_tile_label">
                        <option value="0" <?php if(isset($sf_options_tile['label']) and isset($sf_options_tile['label']['type']) and $sf_options_tile['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_tile['label']) and isset($sf_options_tile['label']['type']) and $sf_options_tile['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][address_search][type]" id="mec_sf_tile_address_search">
						<option value="0" <?php if(isset($sf_options_tile['address_search']) and isset($sf_options_tile['address_search']['type']) and $sf_options_tile['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_tile['address_search']) and isset($sf_options_tile['address_search']['type']) and $sf_options_tile['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][tile][address_search][placeholder]" value="<?php if(isset($sf_options_tile['address_search']) and isset($sf_options_tile['address_search']['placeholder'])) echo $sf_options_tile['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][event_cost][type]" id="mec_sf_tile_event_cost">
                        <option value="0" <?php if(isset($sf_options_tile['event_cost']) and isset($sf_options_tile['event_cost']['type']) and $sf_options_tile['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_tile['event_cost']) and isset($sf_options_tile['event_cost']['type']) and $sf_options_tile['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][month_filter][type]" id="mec_sf_tile_month_filter">
                        <option value="0" <?php if(isset($sf_options_tile['month_filter']) and isset($sf_options_tile['month_filter']['type']) and $sf_options_tile['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_tile['month_filter']) and isset($sf_options_tile['month_filter']['type']) and $sf_options_tile['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_time_filter"><?php _e('Time Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][time_filter][type]" id="mec_sf_tile_time_filter">
                        <option value="0" <?php if(isset($sf_options_tile['time_filter']) and isset($sf_options_tile['time_filter']['type']) and $sf_options_tile['time_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="local-time-picker" <?php if(isset($sf_options_tile['time_filter']) and isset($sf_options_tile['time_filter']['type']) and $sf_options_tile['time_filter']['type'] == 'local-time-picker') echo 'selected="selected"'; ?>><?php _e('Local Time Picker', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_tile_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][tile][text_search][type]" id="mec_sf_tile_text_search">
                        <option value="0" <?php if(isset($sf_options_tile['text_search']) and isset($sf_options_tile['text_search']['type']) and $sf_options_tile['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_tile['text_search']) and isset($sf_options_tile['text_search']['type']) and $sf_options_tile['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][tile][text_search][placeholder]" value="<?php if(isset($sf_options_tile['text_search']) and isset($sf_options_tile['text_search']['placeholder'])) echo $sf_options_tile['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_tile_search_form', $sf_options_tile); ?>
            </div>

            <!-- General Calendar View -->
            <div class="mec-search-form-options-container mec-util-hidden" id="mec_general_calendar_search_form_options_container">
                <?php $sf_options_general_calendar = isset($sf_options['general_calendar']) ? $sf_options['general_calendar'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_category"><?php echo $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][category][type]" id="mec_sf_general_calendar_category">
						<option value="0" <?php if(isset($sf_options_general_calendar['category']) and isset($sf_options_general_calendar['category']['type']) and $sf_options_general_calendar['category']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_general_calendar['category']) and isset($sf_options_general_calendar['category']['type']) and $sf_options_general_calendar['category']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                        <option value="checkboxes" <?php if(isset($sf_options_general_calendar['category']) and isset($sf_options_general_calendar['category']['type']) and $sf_options_general_calendar['category']['type'] == 'checkboxes') echo 'selected="selected"'; ?>><?php _e('Multiselect', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_location"><?php echo $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][location][type]" id="mec_sf_general_calendar_location">
						<option value="0" <?php if(isset($sf_options_general_calendar['location']) and isset($sf_options_general_calendar['location']['type']) and $sf_options_general_calendar['location']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_general_calendar['location']) and isset($sf_options_general_calendar['location']['type']) and $sf_options_general_calendar['location']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <?php do_action('mec_sf_options_location',array('skin'=>'general_calendar' ,'options'=>$sf_options_general_calendar)); ?>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_organizer"><?php echo $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][organizer][type]" id="mec_sf_general_calendar_organizer">
						<option value="0" <?php if(isset($sf_options_general_calendar['organizer']) and isset($sf_options_general_calendar['organizer']['type']) and $sf_options_general_calendar['organizer']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_general_calendar['organizer']) and isset($sf_options_general_calendar['organizer']['type']) and $sf_options_general_calendar['organizer']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_speaker"><?php echo $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][speaker][type]" id="mec_sf_general_calendar_speaker">
						<option value="0" <?php if(isset($sf_options_general_calendar['speaker']) and isset($sf_options_general_calendar['speaker']['type']) and $sf_options_general_calendar['speaker']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_general_calendar['speaker']) and isset($sf_options_general_calendar['speaker']['type']) and $sf_options_general_calendar['speaker']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_tag"><?php echo $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][tag][type]" id="mec_sf_general_calendar_tag">
						<option value="0" <?php if(isset($sf_options_general_calendar['tag']) and isset($sf_options_general_calendar['tag']['type']) and $sf_options_general_calendar['tag']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_general_calendar['tag']) and isset($sf_options_general_calendar['tag']['type']) and $sf_options_general_calendar['tag']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_label"><?php echo $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite')); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][label][type]" id="mec_sf_general_calendar_label">
						<option value="0" <?php if(isset($sf_options_general_calendar['label']) and isset($sf_options_general_calendar['label']['type']) and $sf_options_general_calendar['label']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_general_calendar['label']) and isset($sf_options_general_calendar['label']['type']) and $sf_options_general_calendar['label']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_address_search"><?php _e('Address', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][address_search][type]" id="mec_sf_general_calendar_address_search">
						<option value="0" <?php if(isset($sf_options_general_calendar['address_search']) and isset($sf_options_general_calendar['address_search']['type']) and $sf_options_general_calendar['address_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="address_input" <?php if(isset($sf_options_general_calendar['address_search']) and isset($sf_options_general_calendar['address_search']['type']) and $sf_options_general_calendar['address_search']['type'] == 'address_input') echo 'selected="selected"'; ?>><?php _e('Address Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][general_calendar][address_search][placeholder]" value="<?php if(isset($sf_options_general_calendar['address_search']) and isset($sf_options_general_calendar['address_search']['placeholder'])) echo $sf_options_general_calendar['address_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_event_cost"><?php _e('Event Cost', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][event_cost][type]" id="mec_sf_general_calendar_event_cost">
                        <option value="0" <?php if(isset($sf_options_general_calendar['event_cost']) and isset($sf_options_general_calendar['event_cost']['type']) and $sf_options_general_calendar['event_cost']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="minmax" <?php if(isset($sf_options_general_calendar['event_cost']) and isset($sf_options_general_calendar['event_cost']['type']) and $sf_options_general_calendar['event_cost']['type'] == 'minmax') echo 'selected="selected"'; ?>><?php _e('Min / Max Inputs', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_month_filter"><?php _e('Month Filter', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][month_filter][type]" id="mec_sf_general_calendar_month_filter">
						<option value="0" <?php if(isset($sf_options_general_calendar['month_filter']) and isset($sf_options_general_calendar['month_filter']['type']) and $sf_options_general_calendar['month_filter']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="dropdown" <?php if(isset($sf_options_general_calendar['month_filter']) and isset($sf_options_general_calendar['month_filter']['type']) and $sf_options_general_calendar['month_filter']['type'] == 'dropdown') echo 'selected="selected"'; ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-12" for="mec_sf_general_calendar_text_search"><?php _e('Text Search', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-12" name="mec[sf-options][general_calendar][text_search][type]" id="mec_sf_general_calendar_text_search">
						<option value="0" <?php if(isset($sf_options_general_calendar['text_search']) and isset($sf_options_general_calendar['text_search']['type']) and $sf_options_general_calendar['text_search']['type'] == '0') echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                        <option value="text_input" <?php if(isset($sf_options_general_calendar['text_search']) and isset($sf_options_general_calendar['text_search']['type']) and $sf_options_general_calendar['text_search']['type'] == 'text_input') echo 'selected="selected"'; ?>><?php _e('Text Input', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <input class="mec-col-12" type="text" name="mec[sf-options][general_calendar][text_search][placeholder]" value="<?php if(isset($sf_options_general_calendar['text_search']) and isset($sf_options_general_calendar['text_search']['placeholder'])) echo $sf_options_general_calendar['text_search']['placeholder']; ?>" placeholder="<?php esc_attr_e('Placeholder Text ...'); ?>" title="<?php esc_attr_e('Placeholder Text ...'); ?>">
                </div>
                <?php do_action('mec_monthly_search_form', $sf_options_general_calendar); ?>
            </div>
            
            <!-- Custom Skins -->
            <?php do_action('mec_sf_options', $sf_options); ?>
        </div>
    </div>
</div>