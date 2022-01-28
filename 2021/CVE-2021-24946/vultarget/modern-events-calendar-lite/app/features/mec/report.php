<?php
/** no direct access **/
defined('MECEXEC') or die();

$query = new WP_Query(array(
    'post_type' => $this->main->get_main_post_type(),
    'posts_per_page' => '-1',
    'post_status' => array('pending', 'draft', 'future', 'publish')
));
?>
<div id="webnus-dashboard" class="wrap about-wrap mec-addons">
    <div class="welcome-head w-clearfix">
        <div class="w-row">
            <div class="w-col-sm-9">
                <h1> <?php echo __('Booking Report', 'modern-events-calendar-lite'); ?> </h1>
                <p><?php echo esc_html__('Using this section, you can see the list of participant attendees by the order of date.', 'modern-events-calendar-lite'); ?></p>
            </div>
            <div class="w-col-sm-3">
                <?php $styling = $this->main->get_styling(); $darkadmin_mode = isset($styling['dark_mode']) ? $styling['dark_mode'] : ''; if($darkadmin_mode == 1): $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w2.png'; else: $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w.png'; endif; ?>
                <img src="<?php echo $darklogo; ?>" />
                <span class="w-theme-version"><?php echo __('Version', 'modern-events-calendar-lite'); ?> <?php echo MEC_VERSION; ?></span>
            </div>
        </div>
    </div>
    <div class="welcome-content w-clearfix extra">
        <div class="mec-report-wrap">
            <div class="mec-report-select-event-wrap">
                <div class="w-row">
                    <div class="w-col-sm-12">
                        <select name="mec-report-event-id" class="mec-reports-selectbox mec-reports-selectbox-event">
                            <option value="none"><?php echo esc_html__( 'Select event' , 'modern-events-calendar-lite'); ?></option>
                            <?php 
                                if($query->have_posts())
                                {
                                    $date_format = get_option('date_format');
                                    while($query->have_posts())
                                    {
                                        $query->the_post();

                                        $ID = get_the_ID();
                                        if($this->main->get_original_event($ID) !== $ID) $ID = $this->main->get_original_event($ID);

                                        $start_date = get_post_meta($ID, 'mec_start_date', true);

                                        echo '<option value="'.$ID.'">' . sprintf(__('%s (from %s)', 'modern-events-calendar-lite'), get_the_title(), date($date_format, strtotime($start_date))) . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mec-report-sendmail-wrap"><div class="w-row"><div class="w-col-sm-12"></div></div></div>
            <div class="mec-report-backtoselect-wrap"><div class="w-row"><div class="w-col-sm-12"><button><?php echo esc_html__('Back to list', 'modern-events-calendar-lite'); ?></button></div></div></div>
            <div class="mec-report-selected-event-attendees-wrap"><div class="w-row"><div class="w-col-sm-12"></div></div></div>
            <div class="mec-report-sendmail-form-wrap"><div class="w-row"><div class="w-col-sm-12">
                <?php $send_email_label = __('Send Email', 'modern-events-calendar-lite'); ?>
                <div class="mec-send-email-form-wrap">
                    <h2><?php echo __('Bulk Email', 'modern-events-calendar-lite'); ?></h2>
                    <h4 class="mec-send-email-count"><?php echo sprintf(__('You are sending email to %s attendees', 'modern-events-calendar-lite'), '<span>0</span>'); ?></h4>
                    <input type="text" class="widefat" id="mec-send-email-subject" placeholder="<?php echo __('Email Subject', 'modern-events-calendar-lite'); ?>"/><br><br>
                    <div id="mec-send-email-editor-wrap"></div>
                    <br>
                    <label><input type="checkbox" id="mec-send-admin-copy" value="1"><?php echo __('Send a copy to admin', 'modern-events-calendar-lite'); ?></label>
                    <br><br><p class="description"><?php echo __('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                    <ul>
                        <li><span>%%name%%</span>: <?php echo __('Attendee Name', 'modern-events-calendar-lite'); ?></li>
                    </ul>
                    <div id="mec-send-email-message" class="mec-util-hidden mec-error"></div>
                    <input type="hidden" id="mec-send-email-label" value="<?php echo $send_email_label; ?>" />
                    <input type="hidden" id="mec-send-email-label-loading" value="<?php echo esc_attr__('Loading...', 'modern-events-calendar-lite'); ?>" />
                    <input type="hidden" id="mec-send-email-success" value="<?php echo esc_attr__('Emails successfully sent', 'modern-events-calendar-lite'); ?>" />
                    <input type="hidden" id="mec-send-email-no-user-selected" value="<?php echo esc_attr__('No user selected!', 'modern-events-calendar-lite'); ?>" />
                    <input type="hidden" id="mec-send-email-empty-subject" value="<?php echo esc_attr__('Email subject cannot be empty!', 'modern-events-calendar-lite'); ?>" />
                    <input type="hidden" id="mec-send-email-empty-content" value="<?php echo esc_attr__('Email content cannot be empty!', 'modern-events-calendar-lite'); ?>" />
                    <input type="hidden" id="mec-send-email-error" value="<?php echo esc_attr__('There was an error please try again!', 'modern-events-calendar-lite'); ?>" />
                    <span class="mec-send-email-button"><?php echo $send_email_label; ?></span>
                </div>
                <?php wp_enqueue_editor(); ?>
            </div></div></div>
        </div>
    </div>
</div>