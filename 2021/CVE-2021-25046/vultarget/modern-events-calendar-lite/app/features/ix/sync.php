<?php
/** no direct access **/
defined('MECEXEC') or die();

$ix = $this->main->get_ix_options();
?>
<div class="wrap" id="mec-wrap">
    <h1><?php _e('Auto Synchronization', 'modern-events-calendar-lite'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo $this->main->remove_qs_var('tab'); ?>" class="nav-tab"><?php echo __('Google Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-g-calendar-export'); ?>" class="nav-tab"><?php echo __('Google Cal. Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-f-calendar-import'); ?>" class="nav-tab"><?php echo __('Facebook Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-meetup-import'); ?>" class="nav-tab"><?php echo __('Meetup Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-sync'); ?>" class="nav-tab nav-tab-active"><?php echo __('Synchronization', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-export'); ?>" class="nav-tab"><?php echo __('Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-import'); ?>" class="nav-tab"><?php echo __('Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-thirdparty'); ?>" class="nav-tab"><?php echo __('Third Party Plugins', 'modern-events-calendar-lite'); ?></a>
    </h2>
    <div class="mec-container">
        <div class="sync-content w-clearfix extra">
            <?php if(!$this->main->getPRO()): ?>
            <div class="info-msg"><?php echo sprintf(__("%s is required to use synchronization feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
            <?php else: ?>
            <form id="mec_ix_sync_form" action="<?php echo $this->main->get_full_url(); ?>" method="POST">
                <div class="mec-form-row mec-syn-schedule">
                    <input type="hidden" name="ix[sync_g_import]" value="0" />
                    <label class="mec-col-3" for="mec_ix_sync_g_import">
                        <input type="checkbox" id="mec_ix_sync_g_import" name="ix[sync_g_import]" value="1" <?php echo (isset($ix['sync_g_import']) and $ix['sync_g_import'] == '1') ? 'checked="checked"' : ''; ?> onchange="jQuery('#mec_sync_g_import_cron').toggleClass('mec-util-hidden');" />
                        <?php _e('Auto Google Import', 'modern-events-calendar-lite'); ?>
                    </label>
                    <br>
                    <br>
                    <?php $cron = MEC_ABSPATH.'app'.DS.'crons'.DS.'g-import.php'; ?>
                    <p id="mec_sync_g_import_cron" class="mec-col-12 <?php echo (isset($ix['sync_g_import']) and $ix['sync_g_import'] == '1') ? '' : 'mec-util-hidden'; ?>"><strong><?php _e('Important Note', 'modern-events-calendar-lite'); ?>: </strong><?php echo sprintf(__("Set a cronjob to call %s file by php atleast once per day otherwise it won't import Google Calendar events.", 'modern-events-calendar-lite'), '<code>'.$cron.'</code>'); ?></p>
                </div>
                <div class="mec-form-row mec-syn-schedule">
                    <input type="hidden" name="ix[sync_g_export]" value="0" />
                    <label class="mec-col-3" for="mec_ix_sync_g_export">
                        <input type="checkbox" id="mec_ix_sync_g_export" name="ix[sync_g_export]" value="1" <?php echo (isset($ix['sync_g_export']) and $ix['sync_g_export'] == '1') ? 'checked="checked"' : ''; ?> onchange="jQuery('#mec_sync_g_export_cron').toggleClass('mec-util-hidden');" />
                        <?php _e('Auto Google Export', 'modern-events-calendar-lite'); ?>
                    </label>
                    <br>
                    <br>
                    <?php $cron = MEC_ABSPATH.'app'.DS.'crons'.DS.'g-export.php'; ?>
                    <div id="mec_sync_g_export_cron" class="mec-col-12 <?php echo (isset($ix['sync_g_export']) and $ix['sync_g_export'] == '1') ? '' : 'mec-util-hidden'; ?>" style="margin-top: 20px;">
                        <div class="mec-col-12">
                            <label>
                                <input type="hidden" name="ix[sync_g_export_attendees]" value="0" />
                                <input type="checkbox" name="ix[sync_g_export_attendees]" value="1" <?php echo (isset($ix['sync_g_export_attendees']) and $ix['sync_g_export_attendees'] == '1') ? 'checked="checked"' : ''; ?> />
                                <?php _e('Export Attendees', 'modern-events-calendar-lite'); ?>
                            </label>
                            <br>
                            <br>
                        </div>
                        <div class="mec-col-12">
                            <strong><?php _e('Important Note', 'modern-events-calendar-lite'); ?>: </strong><?php echo sprintf(__("Set a cronjob to call %s file by php atleast once per day otherwise it won't export your website events into Google Calendar.", 'modern-events-calendar-lite'), '<code>'.$cron.'</code>'); ?>
                        </div>
                    </div>
                </div>

                <?php if(false): // Disabled for Now ?>
                <div class="mec-form-row mec-syn-schedule">
                    <input type="hidden" name="ix[sync_f_import]" value="0" />
                    <label class="mec-col-3" for="mec_ix_sync_f_import">
                        <input type="checkbox" id="mec_ix_sync_f_import" name="ix[sync_f_import]" value="1" <?php echo (isset($ix['sync_f_import']) and $ix['sync_f_import'] == '1') ? 'checked="checked"' : ''; ?> onchange="jQuery('#mec_sync_f_import_cron').toggleClass('mec-util-hidden');" />
                        <?php _e('Auto Facebook Import', 'modern-events-calendar-lite'); ?>
                    </label>
                    <br>
                    <br>
                    <?php $cron = MEC_ABSPATH.'app'.DS.'crons'.DS.'f-import.php'; ?>
                    <p id="mec_sync_f_import_cron" class="mec-col-12 <?php echo (isset($ix['sync_f_import']) and $ix['sync_f_import'] == '1') ? '' : 'mec-util-hidden'; ?>"><strong><?php _e('Important Note', 'modern-events-calendar-lite'); ?>: </strong><?php echo sprintf(__("Set a cronjob to call %s file by php atleast once per day otherwise it won't import any event from Facebook.", 'modern-events-calendar-lite'), '<code>'.$cron.'</code>'); ?></p>
                </div>
                <?php endif; ?>

                <div class="mec-form-row mec-syn-schedule">
                    <input type="hidden" name="ix[sync_meetup_import]" value="0" />
                    <label class="mec-col-3" for="mec_ix_sync_meetup_import">
                        <input type="checkbox" id="mec_ix_sync_meetup_import" name="ix[sync_meetup_import]" value="1" <?php echo (isset($ix['sync_meetup_import']) and $ix['sync_meetup_import'] == '1') ? 'checked="checked"' : ''; ?> onchange="jQuery('#mec_sync_meetup_import_cron').toggleClass('mec-util-hidden');" />
                        <?php _e('Auto Meetup Import', 'modern-events-calendar-lite'); ?>
                    </label>
                    <br>
                    <br>
                    <?php $cron = MEC_ABSPATH.'app'.DS.'crons'.DS.'meetup-import.php'; ?>
                    <p id="mec_sync_meetup_import_cron" class="mec-col-12 <?php echo (isset($ix['sync_meetup_import']) and $ix['sync_meetup_import'] == '1') ? '' : 'mec-util-hidden'; ?>"><strong><?php _e('Important Note', 'modern-events-calendar-lite'); ?>: </strong><?php echo sprintf(__("Set a cronjob to call %s file by php atleast once per day otherwise it won't import any event from Meetup.", 'modern-events-calendar-lite'), '<code>'.$cron.'</code>'); ?></p>
                </div>

                <div class="mec-form-row mec-syn-schedule">
                    <h2><?php _e('Auto set cronjobs (Once Daily)', 'modern-events-calendar-lite'); ?></h2>
                    <h4>- <?php _e('First you need to enable the above options for each to be able to use this.', 'modern-events-calendar-lite'); ?></h4>
                    <h4>- <?php _e('If you cannot set CronJob on your server, you can use the options below. Please make sure to NOT use the following options and setting up the server manually together.', 'modern-events-calendar-lite'); ?></h4>
                    <input type="hidden" name="ix[sync_g_import_auto]" value="0" />
                    <label class="mec-col-3" for="mec_ix_sync_g_import_auto">
                        <input type="checkbox" id="mec_ix_sync_g_import_auto" name="ix[sync_g_import_auto]" value="1" <?php echo (isset($ix['sync_g_import_auto']) and $ix['sync_g_import_auto'] == '1') ? 'checked="checked"' : ''; ?> />
                        <?php _e('Google import', 'modern-events-calendar-lite'); ?>
                    </label>
                    <input type="hidden" name="ix[sync_g_export_auto]" value="0" />
                    <label class="mec-col-3" for="mec_ix_sync_g_export_auto">
                        <input type="checkbox" id="mec_ix_sync_g_export_auto" name="ix[sync_g_export_auto]" value="1" <?php echo (isset($ix['sync_g_export_auto']) and $ix['sync_g_export_auto'] == '1') ? 'checked="checked"' : ''; ?> />
                        <?php _e('Google export', 'modern-events-calendar-lite'); ?>
                    </label>
                    <input type="hidden" name="ix[sync_meetup_import_auto]" value="0" />
                    <label class="mec-col-3" for="mec_ix_sync_meetup_import_auto">
                        <input type="checkbox" id="mec_ix_sync_meetup_import_auto" name="ix[sync_meetup_import_auto]" value="1" <?php echo (isset($ix['sync_meetup_import_auto']) and $ix['sync_meetup_import_auto'] == '1') ? 'checked="checked"' : ''; ?> />
                        <?php _e('Meetup import', 'modern-events-calendar-lite'); ?>
                    </label>
                    
                </div>

                <div class="mec-options-fields">
                    <input type="hidden" name="mec-ix-action" value="save-sync-options" />
                    <button class="button button-primary mec-button-primary" type="submit"><?php _e('Save', 'modern-events-calendar-lite'); ?></button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>