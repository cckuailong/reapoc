<?php
/** no direct access **/
defined('MECEXEC') or die();

$ix_options = $this->main->get_ix_options();
?>
<div class="wrap" id="mec-wrap">
    <h1><?php _e('MEC Import / Export', 'modern-events-calendar-lite'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo $this->main->remove_qs_var('tab'); ?>" class="nav-tab nav-tab-active"><?php echo __('Google Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-g-calendar-export'); ?>" class="nav-tab"><?php echo __('Google Cal. Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-f-calendar-import'); ?>" class="nav-tab"><?php echo __('Facebook Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-meetup-import'); ?>" class="nav-tab"><?php echo __('Meetup Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-sync'); ?>" class="nav-tab"><?php echo __('Synchronization', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-export'); ?>" class="nav-tab"><?php echo __('Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-import'); ?>" class="nav-tab"><?php echo __('Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-thirdparty'); ?>" class="nav-tab"><?php echo __('Third Party Plugins', 'modern-events-calendar-lite'); ?></a>
    </h2>
    <div class="mec-container">
        <div class="import-content w-clearfix extra">
            <div class="mec-google-import">
                <form id="mec_google_import_form" action="<?php echo $this->main->get_full_url(); ?>" method="POST">
                    <h3><?php _e('Import from Google Calendar', 'modern-events-calendar-lite'); ?></h3>
                    <p class="description"><?php _e('This will import all of your Google calendar events into MEC.', 'modern-events-calendar-lite'); ?></p>
                    <div class="mec-form-row">
                        <label class="mec-col-3" for="mec_ix_google_import_api_key"><?php _e('Google API Key', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4">
                            <input type="text" id="mec_ix_google_import_api_key" name="ix[google_import_api_key]" value="<?php echo (isset($ix_options['google_import_api_key']) ? $ix_options['google_import_api_key'] : ''); ?>" />
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <label class="mec-col-3" for="mec_ix_google_import_calendar_id"><?php _e('Calendar ID', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4">
                            <input type="text" id="mec_ix_google_import_calendar_id" name="ix[google_import_calendar_id]" value="<?php echo (isset($ix_options['google_import_calendar_id']) ? $ix_options['google_import_calendar_id'] : ''); ?>" />
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <label class="mec-col-3" for="mec_ix_google_import_start_date"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4">
                            <input type="text" id="mec_ix_google_import_start_date" name="ix[google_import_start_date]" value="<?php echo (isset($ix_options['google_import_start_date']) ? $ix_options['google_import_start_date'] : date('Y-m-d', strtotime('-1 Month'))); ?>" class="mec_date_picker" />
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <label class="mec-col-3" for="mec_ix_google_import_end_date"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4">
                            <input type="text" id="mec_ix_google_import_end_date" name="ix[google_import_end_date]" value="<?php echo (isset($ix_options['google_import_end_date']) ? $ix_options['google_import_end_date'] : date('Y-m-d', strtotime('+3 Months'))); ?>" class="mec_date_picker" />
                        </div>
                    </div>
                    <div class="mec-options-fields">
                        <input type="hidden" name="mec-ix-action" value="google-calendar-import-start" />
                        <button id="mec_ix_google_import_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Start', 'modern-events-calendar-lite'); ?></button>
                    </div>
                </form>
                <?php if($this->action == 'google-calendar-import-start'): ?>
                <div class="mec-ix-google-import-started">
                    <?php if($this->response['success'] == 0): ?>
                    <div class="mec-error"><?php echo $this->response['error']; ?></div>
                    <?php else: ?>
                    <form id="mec_google_import_do_form" action="<?php echo $this->main->get_full_url(); ?>" method="POST">
                        <div class="mec-xi-google-import-events mec-options-fields">
                            <h4><?php _e('Google Calendar Events', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-success"><?php echo sprintf(__('We found %s events for %s calendar. Please select your desired events to import.', 'modern-events-calendar-lite'), '<strong>'.$this->response['data']['count'].'</strong>', '<strong>'.$this->response['data']['title'].'</strong>'); ?></div>
                            <ul class="mec-select-deselect-actions" data-for="#mec_import_g_calendar_events">
                                <li data-action="select-all"><?php _e('Select All', 'modern-events-calendar-lite'); ?></li>
                                <li data-action="deselect-all"><?php _e('Deselect All', 'modern-events-calendar-lite'); ?></li>
                                <li data-action="toggle"><?php _e('Toggle', 'modern-events-calendar-lite'); ?></li>
                            </ul>
                            <ul id="mec_import_g_calendar_events">
                                <?php $timezone = $this->main->get_timezone(); foreach($this->response['data']['events'] as $event): if(trim($event['title']) == '') continue; ?>
                                <?php
                                    $date_start = new DateTime((trim($event['start']->date) ? $event['start']->date : $event['start']->dateTime));
                                    if($timezone != $this->response['data']['timezone']) $date_start->setTimezone(new DateTimeZone($timezone));

                                    $date_end = new DateTime((trim($event['end']->date) ? $event['end']->date : $event['end']->dateTime));
                                    if($timezone != $this->response['data']['timezone']) $date_end->setTimezone(new DateTimeZone($timezone));
                                ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="g-events[]" value="<?php echo $event['id']; ?>" checked="checked" />
                                        <span><?php echo sprintf(__('Event Title: %s Event Date: %s - %s', 'modern-events-calendar-lite'), '<strong>'.$event['title'].'</strong>', '<strong title="'.esc_attr('First Date of Event', 'modern-events-calendar-lite').'">'.($date_start->format('Y-m-d H:i:s')).'</strong>', '<strong title="'.esc_attr('First Date of Event', 'modern-events-calendar-lite').'">'.($date_end->format('Y-m-d H:i:s')).'</strong>'); ?></span>
                                    </label>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="mec-options-fields">
                            <h4><?php _e('Import Options', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="checkbox" name="ix[import_organizers]" value="1" checked="checked" />
                                    <?php _e('Import Organizers', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="checkbox" name="ix[import_locations]" value="1" checked="checked" />
                                    <?php _e('Import Locations', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <input type="hidden" name="mec-ix-action" value="google-calendar-import-do" />
                            <input type="hidden" name="ix[google_import_api_key]" value="<?php echo (isset($this->ix['google_import_api_key']) ? $this->ix['google_import_api_key'] : ''); ?>" />
                            <input type="hidden" name="ix[google_import_calendar_id]" value="<?php echo (isset($this->ix['google_import_calendar_id']) ? $this->ix['google_import_calendar_id'] : ''); ?>" />
                            <button id="mec_ix_google_import_do_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Import', 'modern-events-calendar-lite'); ?></button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
                <?php elseif($this->action == 'google-calendar-import-do'): ?>
                <div class="mec-ix-google-import-do">
                    <?php if($this->response['success'] == 0): ?>
                    <div class="mec-error"><?php echo $this->response['error']; ?></div>
                    <?php else: ?>
                    <div class="mec-success"><?php echo sprintf(__('%s events successfully imported to your website from Google Calendar.', 'modern-events-calendar-lite'), '<strong>'.count($this->response['data']).'</strong>'); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>