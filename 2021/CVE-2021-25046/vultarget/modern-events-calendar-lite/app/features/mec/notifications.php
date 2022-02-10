<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_mec $this */

$multilingual = $this->main->is_multilingual();
$locale = $this->main->get_backend_active_locale();

$notifications = $this->main->get_notifications(($multilingual ? $locale : NULL));
$settings = $this->main->get_settings();

// Fix Notices
if(!isset($notifications['event_finished'])) $notifications['event_finished'] = array();

// Additional Organizers
$additional_organizers = (isset($settings['additional_organizers']) and $settings['additional_organizers']);
?>
<div class="wns-be-container wns-be-container-sticky">
    <div id="wns-be-infobar">
        <div class="mec-search-settings-wrap">
            <i class="mec-sl-magnifier"></i>
            <input id="mec-search-settings" type="text" placeholder="<?php esc_html_e('Search...' ,'modern-events-calendar-lite'); ?>">
        </div>
        <a href="" id="" class="dpr-btn dpr-save-btn"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></a>
    </div>

    <div class="wns-be-sidebar">
        <?php $this->main->get_sidebar_menu('notifications'); ?>
    </div>

    <div class="wns-be-main">
        <div id="wns-be-notification"></div>
        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                <div class="mec-container">

                    <form id="mec_notifications_form">

                        <?php if($this->main->getPRO() and isset($this->settings['booking_status']) and $this->settings['booking_status']): ?>
                        <?php do_action('mec_notification_menu_start', $this->main, $notifications); ?>

                        <div id="booking_notification_section" class="mec-options-fields active">

                            <h4 class="mec-form-subtitle"><?php _e('Booking', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                            <div class="mec-col-12">
                                <label>
                                    <input type="hidden" name="mec[notifications][booking_notification][status]" value="0" />
                                    <input onchange="jQuery('#mec_notification_booking_notification_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][booking_notification][status]" <?php if(!isset($notifications['booking_notification']['status']) or (isset($notifications['booking_notification']['status']) and $notifications['booking_notification']['status'])) echo 'checked="checked"'; ?> /> <?php _e('Enable booking notification', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <p class="mec-col-12 description"><?php _e('Sent to attendee after booking to notify them.', 'modern-events-calendar-lite'); ?></p>
                            </div>
                            <div id="mec_notification_booking_notification_container_toggle" class="<?php if(isset($notifications['booking_notification']) and isset($notifications['booking_notification']['status']) and !$notifications['booking_notification']['status']) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_notification_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_notification][subject]" id="mec_notifications_booking_notification_subject" value="<?php echo (isset($notifications['booking_notification']['subject']) ? stripslashes($notifications['booking_notification']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                               <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_notification_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $users = isset($notifications['booking_notification']['receiver_users']) ? $notifications['booking_notification']['receiver_users'] : array();
                                            echo $this->main->get_users_dropdown($users, 'booking_notification');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_notification_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $roles = isset($notifications['booking_notification']['receiver_roles']) ? $notifications['booking_notification']['receiver_roles'] : array();
                                            echo $this->main->get_roles_dropdown($roles, 'booking_notification');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_notification_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_notification][recipients]" id="mec_notifications_booking_notification_recipients" value="<?php echo (isset($notifications['booking_notification']['recipients']) ? $notifications['booking_notification']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][booking_notification][send_to_organizer]" value="1" id="mec_notifications_booking_notification_send_to_organizer" <?php echo ((isset($notifications['booking_notification']['send_to_organizer']) and $notifications['booking_notification']['send_to_organizer'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_booking_notification_send_to_organizer"><?php _e('Send the email to event organizer', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>

                                <?php if($additional_organizers): ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][booking_notification][send_to_additional_organizers]" value="1" id="mec_notifications_booking_notification_send_to_additional_organizers" <?php echo ((isset($notifications['booking_notification']['send_to_additional_organizers']) and $notifications['booking_notification']['send_to_additional_organizers'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_booking_notification_send_to_additional_organizers"><?php _e('Send the email to additional organizers', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="mec-form-row">
                                    <label for="mec_notifications_booking_notification_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['booking_notification']) ? stripslashes($notifications['booking_notification']['content']) : ''), 'mec_notifications_booking_notification_content', array('textarea_name'=>'mec[notifications][booking_notification][content]')); ?>
                                </div>

                                <?php
                                    $section = 'booking_notification';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_date%%</span>: <?php _e('Event Start Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_date%%</span>: <?php _e('Event End Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_time%%</span>: <?php _e('Event Start Time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_time%%</span>: <?php _e('Event End Time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_timezone%%</span>: <?php _e('Event Timezone', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%invoice_link%%</span>: <?php _e('Invoice Link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ics_link%%</span>: <?php _e('Download ICS file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link%%</span>: <?php _e('Add to Google Calendar', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link_next_occurrences%%</span>: <?php _e('Add to Google Calendar Links for next 20 occurrences', 'modern-events-calendar-lite'); ?></li>
                                            <?php do_action('mec_extra_field_notifications'); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="booking_verification" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Booking Verification', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <p class="mec-col-12 description"><?php _e('It sends to attendee email for verifying their booking/email.', 'modern-events-calendar-lite'); ?></p>
                            </div>

                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <label for="mec_notifications_email_verification_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                </div>
                                <div class="mec-col-9">
                                    <input type="text" name="mec[notifications][email_verification][subject]" id="mec_notifications_email_verification_subject" value="<?php echo (isset($notifications['email_verification']['subject']) ? stripslashes($notifications['email_verification']['subject']) : ''); ?>" />
                                </div>
                            </div>

                            <!-- Start Receiver Users -->
                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <label for="mec_notifications_email_verification_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                </div>
                                <div class="mec-col-9">
                                    <?php
                                        $users = isset($notifications['email_verification']['receiver_users']) ? $notifications['email_verification']['receiver_users'] : array();
                                        echo $this->main->get_users_dropdown($users, 'email_verification');
                                    ?>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <!-- End Receiver Users -->

                            <!-- Start Receiver Roles -->
                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <label for="mec_notifications_email_verification_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                </div>
                                <div class="mec-col-9">
                                    <?php
                                        $roles = isset($notifications['email_verification']['receiver_roles']) ? $notifications['email_verification']['receiver_roles'] : array();
                                        echo $this->main->get_roles_dropdown($roles, 'email_verification');
                                    ?>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <!-- End Receiver Roles -->

                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <label for="mec_notifications_email_verification_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                </div>
                                <div class="mec-col-9">
                                <input type="text" name="mec[notifications][email_verification][recipients]" id="mec_notifications_email_verification_recipients" value="<?php echo (isset($notifications['email_verification']['recipients']) ? $notifications['email_verification']['recipients'] : ''); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label for="mec_notifications_email_verification_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                <?php wp_editor((isset($notifications['email_verification']) ? stripslashes($notifications['email_verification']['content']) : ''), 'mec_notifications_email_verification_content', array('textarea_name'=>'mec[notifications][email_verification][content]')); ?>
                            </div>

                            <?php
                                $section = 'email_verification';
                                do_action('mec_display_notification_settings',$notifications,$section);
                            ?>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                    <ul>
                                        <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_start_date%%</span>: <?php _e('Event Start Date', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_end_date%%</span>: <?php _e('Event End Date', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_start_time%%</span>: <?php _e('Event Start Time', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_end_time%%</span>: <?php _e('Event End Time', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_timezone%%</span>: <?php _e('Event Timezone', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%verification_link%%</span>: <?php _e('Email/Booking verification link.', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%ics_link%%</span>: <?php _e('Download ICS file', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%google_calendar_link%%</span>: <?php _e('Add to Google Calendar', 'modern-events-calendar-lite'); ?></li>
                                        <li><span>%%google_calendar_link_next_occurrences%%</span>: <?php _e('Add to Google Calendar Links for next 20 occurrences', 'modern-events-calendar-lite'); ?></li>
                                        <?php do_action('mec_extra_field_notifications'); ?>
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div id="booking_confirmation" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Booking Confirmation', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][booking_confirmation][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_booking_confirmation_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][booking_confirmation][status]" <?php if(!isset($notifications['booking_confirmation']['status']) or (isset($notifications['booking_confirmation']['status']) and $notifications['booking_confirmation']['status'])) echo 'checked="checked"'; ?> /> <?php _e('Enable booking confirmation', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <p class="mec-col-12 description"><?php _e('Sent to attendee after confirming the booking by admin.', 'modern-events-calendar-lite'); ?></p>
                            </div>
                            <div id="mec_notification_booking_confirmation_container_toggle" class="<?php if(isset($notifications['booking_confirmation']) and isset($notifications['booking_confirmation']['status']) and !$notifications['booking_confirmation']['status']) echo 'mec-util-hidden'; ?>">

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_confirmation_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_confirmation][subject]" id="mec_notifications_booking_confirmation_subject" value="<?php echo (isset($notifications['booking_confirmation']['subject']) ? stripslashes($notifications['booking_confirmation']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_confirmation_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $users = isset($notifications['booking_confirmation']['receiver_users']) ? $notifications['booking_confirmation']['receiver_users'] : array();
                                        echo $this->main->get_users_dropdown($users, 'booking_confirmation');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_confirmation_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $roles = isset($notifications['booking_confirmation']['receiver_roles']) ? $notifications['booking_confirmation']['receiver_roles'] : array();
                                        echo $this->main->get_roles_dropdown($roles, 'booking_confirmation');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_confirmation_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_confirmation][recipients]" id="mec_notifications_booking_confirmation_recipients" value="<?php echo (isset($notifications['booking_confirmation']['recipients']) ? $notifications['booking_confirmation']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][booking_confirmation][send_single_one_email]" value="1" id="mec_notifications_booking_confirmation_send_single_one_email" <?php echo ((isset($notifications['booking_confirmation']['send_single_one_email']) and $notifications['booking_confirmation']['send_single_one_email'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_booking_confirmation_send_single_one_email"><?php _e('Send one single email only to first attendee', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label for="mec_notifications_booking_confirmation_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['booking_confirmation']) ? stripslashes($notifications['booking_confirmation']['content']) : ''), 'mec_notifications_booking_confirmation_content', array('textarea_name'=>'mec[notifications][booking_confirmation][content]')); ?>
                                </div>

                                <?php
                                    $section = 'booking_confirmation';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendee_price%%</span>: <?php _e('Attendee Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_date%%</span>: <?php _e('Event Start Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_date%%</span>: <?php _e('Event End Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_time%%</span>: <?php _e('Event Start Time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_time%%</span>: <?php _e('Event End Time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_timezone%%</span>: <?php _e('Event Timezone', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%cancellation_link%%</span>: <?php _e('Booking cancellation link.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%invoice_link%%</span>: <?php _e('Invoice Link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ics_link%%</span>: <?php _e('Download ICS file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link%%</span>: <?php _e('Add to Google Calendar', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link_next_occurrences%%</span>: <?php _e('Add to Google Calendar Links for next 20 occurrences', 'modern-events-calendar-lite'); ?></li>
                                            <?php do_action('mec_extra_field_notifications'); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="booking_rejection" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Booking Rejection', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][booking_rejection][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_booking_rejection_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][booking_rejection][status]" <?php if((isset($notifications['booking_rejection']) and isset($notifications['booking_rejection']['status']) and $notifications['booking_rejection']['status'])) echo 'checked="checked"'; ?> /> <?php _e('Enable booking rejection', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <p class="mec-col-12 description"><?php _e('Sent to attendee after booking rejection by admin.', 'modern-events-calendar-lite'); ?></p>
                            </div>
                            <div id="mec_notification_booking_rejection_container_toggle" class="<?php if(!isset($notifications['booking_rejection']) or (isset($notifications['booking_rejection']) and isset($notifications['booking_rejection']['status']) and !$notifications['booking_rejection']['status'])) echo 'mec-util-hidden'; ?>">

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_rejection_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_rejection][subject]" id="mec_notifications_booking_rejection_subject" value="<?php echo (isset($notifications['booking_rejection']['subject']) ? stripslashes($notifications['booking_rejection']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_rejection_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $users = isset($notifications['booking_rejection']['receiver_users']) ? $notifications['booking_rejection']['receiver_users'] : array();
                                        echo $this->main->get_users_dropdown($users, 'booking_rejection');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_rejection_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $roles = isset($notifications['booking_rejection']['receiver_roles']) ? $notifications['booking_rejection']['receiver_roles'] : array();
                                        echo $this->main->get_roles_dropdown($roles, 'booking_rejection');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box top">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_rejection_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_rejection][recipients]" id="mec_notifications_booking_rejection_recipients" value="<?php echo (isset($notifications['booking_rejection']['recipients']) ? $notifications['booking_rejection']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][booking_rejection][send_to_admin]" value="1" id="mec_notifications_booking_rejection_send_to_admin" <?php echo ((!isset($notifications['booking_rejection']['send_to_admin']) or $notifications['booking_rejection']['send_to_admin'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_booking_rejection_send_to_admin"><?php _e('Send the email to admin', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][booking_rejection][send_to_organizer]" value="1" id="mec_notifications_booking_rejection_send_to_organizer" <?php echo ((isset($notifications['booking_rejection']['send_to_organizer']) and $notifications['booking_rejection']['send_to_organizer'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_booking_rejection_send_to_organizer"><?php _e('Send the email to event organizer', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>

                                <?php if($additional_organizers): ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][booking_rejection][send_to_additional_organizers]" value="1" id="mec_notifications_booking_rejection_send_to_additional_organizers" <?php echo ((isset($notifications['booking_rejection']['send_to_additional_organizers']) and $notifications['booking_rejection']['send_to_additional_organizers'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_booking_rejection_send_to_additional_organizers"><?php _e('Send the email to additional organizers', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][booking_rejection][send_to_user]" value="1" id="mec_notifications_booking_rejection_send_to_user" <?php echo ((isset($notifications['booking_rejection']['send_to_user']) and $notifications['booking_rejection']['send_to_user'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_booking_rejection_send_to_user"><?php _e('Send the email to the booked user', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>

                                <div class="mec-form-row">
                                    <label for="mec_notifications_booking_rejection_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['booking_rejection']) ? stripslashes($notifications['booking_rejection']['content']) : ''), 'mec_notifications_booking_rejection_content', array('textarea_name'=>'mec[notifications][booking_rejection][content]')); ?>
                                </div>

                                <?php
                                    $section = 'booking_rejection';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendee_price%%</span>: <?php _e('Attendee Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_date%%</span>: <?php _e('Event Start Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_date%%</span>: <?php _e('Event End Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_time%%</span>: <?php _e('Event Start Time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_time%%</span>: <?php _e('Event End Time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_timezone%%</span>: <?php _e('Event Timezone', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%cancellation_link%%</span>: <?php _e('Booking cancellation link.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%invoice_link%%</span>: <?php _e('Invoice Link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ics_link%%</span>: <?php _e('Download ICS file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link%%</span>: <?php _e('Add to Google Calendar', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link_next_occurrences%%</span>: <?php _e('Add to Google Calendar Links for next 20 occurrences', 'modern-events-calendar-lite'); ?></li>
                                            <?php do_action('mec_extra_field_notifications'); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="cancellation_notification" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Booking Cancellation', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][cancellation_notification][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_cancellation_notification_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][cancellation_notification][status]" <?php if((isset($notifications['cancellation_notification']['status']) and $notifications['cancellation_notification']['status'])) echo 'checked="checked"'; ?> /> <?php _e('Enable cancellation notification', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <p class="mec-col-12 description"><?php _e('Sent to selected recipients after booking cancellation to notify them.', 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>
                            <div id="mec_notification_cancellation_notification_container_toggle" class="<?php if((isset($notifications['cancellation_notification']) and !$notifications['cancellation_notification']['status']) or !isset($notifications['cancellation_notification'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_cancellation_notification_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][cancellation_notification][subject]" id="mec_notifications_cancellation_notification_subject" value="<?php echo (isset($notifications['cancellation_notification']['subject']) ? stripslashes($notifications['cancellation_notification']['subject']) : 'Your booking is canceled.'); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_cancellation_notification_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $users = isset($notifications['cancellation_notification']['receiver_users']) ? $notifications['cancellation_notification']['receiver_users'] : array();
                                            echo $this->main->get_users_dropdown($users, 'cancellation_notification');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_cancellation_notification_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $roles = isset($notifications['cancellation_notification']['receiver_roles']) ? $notifications['cancellation_notification']['receiver_roles'] : array();
                                            echo $this->main->get_roles_dropdown($roles, 'cancellation_notification');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_cancellation_notification_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][cancellation_notification][recipients]" id="mec_notifications_cancellation_notification_recipients" value="<?php echo (isset($notifications['cancellation_notification']['recipients']) ? $notifications['cancellation_notification']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="hidden" name="mec[notifications][cancellation_notification][send_to_admin]" value="0" />
                                        <input type="checkbox" name="mec[notifications][cancellation_notification][send_to_admin]" value="1" id="mec_notifications_cancellation_notification_send_to_admin" <?php echo ((!isset($notifications['cancellation_notification']['send_to_admin']) or (isset($notifications['cancellation_notification']['send_to_admin']) and $notifications['cancellation_notification']['send_to_admin'] == 1)) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_cancellation_notification_send_to_admin"><?php _e('Send the email to admin', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][cancellation_notification][send_to_organizer]" value="1" id="mec_notifications_cancellation_notification_send_to_organizer" <?php echo ((isset($notifications['cancellation_notification']['send_to_organizer']) and $notifications['cancellation_notification']['send_to_organizer'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_cancellation_notification_send_to_organizer"><?php _e('Send the email to event organizer', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>

                                <?php if($additional_organizers): ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][cancellation_notification][send_to_additional_organizers]" value="1" id="mec_notifications_cancellation_notification_send_to_additional_organizers" <?php echo ((isset($notifications['cancellation_notification']['send_to_additional_organizers']) and $notifications['cancellation_notification']['send_to_additional_organizers'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_cancellation_notification_send_to_additional_organizers"><?php _e('Send the email to additional organizers', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][cancellation_notification][send_to_user]" value="1" id="mec_notifications_cancellation_notification_send_to_user" <?php echo ((isset($notifications['cancellation_notification']['send_to_user']) and $notifications['cancellation_notification']['send_to_user'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_cancellation_notification_send_to_user"><?php _e('Send the email to the booked user', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label for="mec_notifications_cancellation_notification_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['cancellation_notification']) ? stripslashes($notifications['cancellation_notification']['content']) : ''), 'mec_notifications_cancellation_notification_content', array('textarea_name'=>'mec[notifications][cancellation_notification][content]')); ?>
                                </div>

                                <?php
                                    $section = 'cancellation_notification';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%admin_link%%</span>: <?php _e('Admin booking management link.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="admin_notification" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Admin', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][admin_notification][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_admin_notification_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][admin_notification][status]" <?php if(!isset($notifications['admin_notification']['status']) or (isset($notifications['admin_notification']['status']) and $notifications['admin_notification']['status'])) echo 'checked="checked"'; ?> /> <?php _e('Enable admin notification', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <p class="mec-col-12 description"><?php _e('Sent to admin to notify them that a new booking has been received.', 'modern-events-calendar-lite'); ?></p>
                            </div>
                            <div id="mec_notification_admin_notification_container_toggle" class="<?php if(isset($notifications['admin_notification']) and isset($notifications['admin_notification']['status']) and !$notifications['admin_notification']['status']) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_admin_notification_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][admin_notification][subject]" id="mec_notifications_admin_notification_subject" value="<?php echo (isset($notifications['admin_notification']['subject']) ? stripslashes($notifications['admin_notification']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_admin_notification_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $users = isset($notifications['admin_notification']['receiver_users']) ? $notifications['admin_notification']['receiver_users'] : array();
                                            echo $this->main->get_users_dropdown($users, 'admin_notification');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_admin_notification_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $roles = isset($notifications['admin_notification']['receiver_roles']) ? $notifications['admin_notification']['receiver_roles'] : array();
                                            echo $this->main->get_roles_dropdown($roles, 'admin_notification');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_admin_notification_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][admin_notification][recipients]" id="mec_notifications_admin_notification_recipients" value="<?php echo (isset($notifications['admin_notification']['recipients']) ? $notifications['admin_notification']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="hidden" name="mec[notifications][admin_notification][send_to_admin]" value="0" />
                                        <input type="checkbox" name="mec[notifications][admin_notification][send_to_admin]" value="1" id="mec_notifications_admin_notification_send_to_admin" <?php echo ((!isset($notifications['admin_notification']['send_to_admin']) or (isset($notifications['admin_notification']['send_to_admin']) and $notifications['admin_notification']['send_to_admin'] == 1)) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_admin_notification_send_to_admin"><?php _e('Send the email to admin', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][admin_notification][send_to_organizer]" value="1" id="mec_notifications_admin_notification_send_to_organizer" <?php echo ((isset($notifications['admin_notification']['send_to_organizer']) and $notifications['admin_notification']['send_to_organizer'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_admin_notification_send_to_organizer"><?php _e('Send the email to event organizer', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>

                                <?php if($additional_organizers): ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][admin_notification][send_to_additional_organizers]" value="1" id="mec_notifications_admin_notification_send_to_additional_organizers" <?php echo ((isset($notifications['admin_notification']['send_to_additional_organizers']) and $notifications['admin_notification']['send_to_additional_organizers'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_admin_notification_send_to_additional_organizers"><?php _e('Send the email to additional organizers', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="mec-form-row">
                                    <label for="mec_notifications_admin_notification_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['admin_notification']) ? stripslashes($notifications['admin_notification']['content']) : ''), 'mec_notifications_admin_notification_content', array('textarea_name'=>'mec[notifications][admin_notification][content]')); ?>
                                </div>

                                <?php
                                    $section = 'admin_notification';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%admin_link%%</span>: <?php _e('Admin booking management link.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="event_soldout" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Event Soldout', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][event_soldout][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_event_soldout_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][event_soldout][status]" <?php if(!isset($notifications['event_soldout']['status']) or (isset($notifications['event_soldout']['status']) and $notifications['event_soldout']['status'])) echo 'checked="checked"'; ?> /> <?php _e('Enable event soldout notification', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <p class="mec-col-12 description"><?php _e('Sent to admin and / or event organizer to notify them that an event is soldout.', 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>
                            <div id="mec_notification_event_soldout_container_toggle" class="<?php if(isset($notifications['event_soldout']) and isset($notifications['event_soldout']['status']) and !$notifications['event_soldout']['status']) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_soldout_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][event_soldout][subject]" id="mec_notifications_event_soldout_subject" value="<?php echo (isset($notifications['event_soldout']['subject']) ? stripslashes($notifications['event_soldout']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_soldout_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $users = isset($notifications['event_soldout']['receiver_users']) ? $notifications['event_soldout']['receiver_users'] : array();
                                        echo $this->main->get_users_dropdown($users, 'event_soldout');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_soldout_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $roles = isset($notifications['event_soldout']['receiver_roles']) ? $notifications['event_soldout']['receiver_roles'] : array();
                                        echo $this->main->get_roles_dropdown($roles, 'event_soldout');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_soldout_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][event_soldout][recipients]" id="mec_notifications_event_soldout_recipients" value="<?php echo (isset($notifications['event_soldout']['recipients']) ? $notifications['event_soldout']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="hidden" name="mec[notifications][event_soldout][send_to_admin]" value="0" />
                                        <input type="checkbox" name="mec[notifications][event_soldout][send_to_admin]" value="1" id="mec_notifications_event_soldout_send_to_admin" <?php echo ((!isset($notifications['event_soldout']['send_to_admin']) or (isset($notifications['event_soldout']['send_to_admin']) and $notifications['event_soldout']['send_to_admin'] == 1)) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_event_soldout_send_to_admin"><?php _e('Send the email to admin', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][event_soldout][send_to_organizer]" value="1" id="mec_notifications_event_soldout_send_to_organizer" <?php echo ((isset($notifications['event_soldout']['send_to_organizer']) and $notifications['event_soldout']['send_to_organizer'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_event_soldout_send_to_organizer"><?php _e('Send the email to event organizer', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>

                                <?php if($additional_organizers): ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="checkbox" name="mec[notifications][event_soldout][send_to_additional_organizers]" value="1" id="mec_notifications_event_soldout_send_to_additional_organizers" <?php echo ((isset($notifications['event_soldout']['send_to_additional_organizers']) and $notifications['event_soldout']['send_to_additional_organizers'] == 1) ? 'checked="checked"' : ''); ?> />
                                        <label for="mec_notifications_event_soldout_send_to_additional_organizers"><?php _e('Send the email to additional organizers', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="mec-form-row">
                                    <label for="mec_notifications_event_soldout_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['event_soldout']) ? stripslashes($notifications['event_soldout']['content']) : ''), 'mec_notifications_event_soldout_content', array('textarea_name'=>'mec[notifications][event_soldout][content]')); ?>
                                </div>

                                <?php
                                    $section = 'event_soldout';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%admin_link%%</span>: <?php _e('Admin booking management link.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="booking_reminder" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Booking Reminder', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][booking_reminder][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_booking_reminder_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][booking_reminder][status]" <?php if(isset($notifications['booking_reminder']) and $notifications['booking_reminder']['status']) echo 'checked="checked"'; ?> /> <?php _e('Enable booking reminder notification', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                            <div id="mec_notification_booking_reminder_container_toggle" class="<?php if((isset($notifications['booking_reminder']) and !$notifications['booking_reminder']['status']) or !isset($notifications['booking_reminder'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <?php $cron = MEC_ABSPATH.'app'.DS.'crons'.DS.'booking-reminder.php'; ?>
                                    <p class="mec-col-12 description"><strong><?php _e('Important Note', 'modern-events-calendar-lite'); ?>: </strong><?php echo sprintf(__("Set a cronjob to call %s file once per hour otherwise it won't send the reminders. Please note that you should call this file %s otherwise it may send the reminders multiple times.", 'modern-events-calendar-lite'), '<code>'.$cron.'</code>', '<strong>'.__('only once per hour', 'modern-events-calendar-lite').'</strong>'); ?></p>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_reminder_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_reminder][subject]" id="mec_notifications_booking_reminder_subject" value="<?php echo ((isset($notifications['booking_reminder']) and isset($notifications['booking_reminder']['subject'])) ? stripslashes($notifications['booking_reminder']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_reminder_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $users = isset($notifications['booking_reminder']['receiver_users']) ? $notifications['booking_reminder']['receiver_users'] : array();
                                            echo $this->main->get_users_dropdown($users, 'booking_reminder');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_reminder_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $roles = isset($notifications['booking_reminder']['receiver_roles']) ? $notifications['booking_reminder']['receiver_roles'] : array();
                                            echo $this->main->get_roles_dropdown($roles, 'booking_reminder');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_reminder_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_reminder][recipients]" id="mec_notifications_booking_reminder_recipients" value="<?php echo ((isset($notifications['booking_reminder']) and isset($notifications['booking_reminder']['recipients'])) ? $notifications['booking_reminder']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_booking_reminder_hours"><?php _e('Hours', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][booking_reminder][hours]" id="mec_notifications_booking_reminder_hours" value="<?php echo ((isset($notifications['booking_reminder']) and isset($notifications['booking_reminder']['hours'])) ? $notifications['booking_reminder']['hours'] : '24,72,168'); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Reminder hours', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Please, insert comma to separate reminder hours.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label for="mec_notifications_booking_reminder_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['booking_reminder']) ? stripslashes($notifications['booking_reminder']['content']) : ''), 'mec_notifications_booking_reminder_content', array('textarea_name'=>'mec[notifications][booking_reminder][content]')); ?>
                                </div>

                                <?php
                                    $section = 'booking_reminder';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>

                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%cancellation_link%%</span>: <?php _e('Booking cancellation link.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%invoice_link%%</span>: <?php _e('Invoice Link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ics_link%%</span>: <?php _e('Download ICS file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link%%</span>: <?php _e('Add to Google Calendar', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link_next_occurrences%%</span>: <?php _e('Add to Google Calendar Links for next 20 occurrences', 'modern-events-calendar-lite'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php endif; ?>

                        <?php do_action('mec_notifications_tabs_content',$notifications); ?>

                        <div id="new_event" class="mec-options-fields  <?php if(isset($this->settings['booking_status']) and $this->settings['booking_status'] == 0) echo 'active'; ?>">

                            <h4 class="mec-form-subtitle"><?php _e('New Event', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][new_event][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_new_event_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][new_event][status]" <?php if(isset($notifications['new_event']['status']) and $notifications['new_event']['status']) echo 'checked="checked"'; ?> /> <?php _e('Enable new event notification', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                            <div id="mec_notification_new_event_container_toggle" class="<?php if((isset($notifications['new_event']) and !$notifications['new_event']['status']) or !isset($notifications['new_event'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <label>
                                            <input type="hidden" name="mec[notifications][new_event][send_to_admin]" value="0" />
                                            <input value="1" type="checkbox" name="mec[notifications][new_event][send_to_admin]" <?php if((!isset($notifications['new_event']['send_to_admin'])) or (isset($notifications['new_event']['send_to_admin']) and $notifications['new_event']['send_to_admin'])) echo 'checked="checked"'; ?> /> <?php _e('Send the email to admin', 'modern-events-calendar-lite'); ?>
                                        </label>
                                    </div>
                                    <p class="mec-col-12 description"><?php _e('Sent after adding a new event from frontend event submission or from website backend.', 'modern-events-calendar-lite'); ?></p>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_new_event_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][new_event][subject]" id="mec_notifications_new_event_subject" value="<?php echo (isset($notifications['new_event']['subject']) ? stripslashes($notifications['new_event']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_new_event_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $users = isset($notifications['new_event']['receiver_users']) ? $notifications['new_event']['receiver_users'] : array();
                                            echo $this->main->get_users_dropdown($users, 'new_event');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_new_event_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $roles = isset($notifications['new_event']['receiver_roles']) ? $notifications['new_event']['receiver_roles'] : array();
                                            echo $this->main->get_roles_dropdown($roles, 'new_event');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_new_event_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][new_event][recipients]" id="mec_notifications_new_event_recipients" value="<?php echo (isset($notifications['new_event']['recipients']) ? $notifications['new_event']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label for="mec_notifications_new_event_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['new_event']) ? stripslashes($notifications['new_event']['content']) : ''), 'mec_notifications_new_event_content', array('textarea_name'=>'mec[notifications][new_event][content]')); ?>
                                </div>

                                <?php
                                    $section = 'new_event';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%event_title%%</span>: <?php _e('Title of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Link of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_date%%</span>: <?php _e('Event Start Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_date%%</span>: <?php _e('Event End Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_timezone%%</span>: <?php _e('Event Timezone', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_status%%</span>: <?php _e('Status of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_note%%</span>: <?php _e('Event Note', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%admin_link%%</span>: <?php _e('Admin events management link.', 'modern-events-calendar-lite'); ?></li>
                                            <?php do_action('mec_extra_field_notifications'); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- MEC Event Published -->
                        <div id="user_event_publishing" class="mec-options-fields  <?php if(isset($this->settings['booking_status']) and $this->settings['booking_status'] == 0) echo 'active'; ?>">

                            <h4 class="mec-form-subtitle"><?php _e('User Event Publishing', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][user_event_publishing][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_user_event_publishing_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][user_event_publishing][status]" <?php if(isset($notifications['user_event_publishing']['status']) and $notifications['user_event_publishing']['status']) echo 'checked="checked"'; ?> /> <?php _e('Enable user event publishing notification', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <p class="mec-col-12 description"><?php _e('Sent after publishing a new event from frontend event submission or from website backend.', 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>
                            <div id="mec_notification_user_event_publishing_container_toggle" class="<?php if((isset($notifications['user_event_publishing']) and !$notifications['user_event_publishing']['status']) or !isset($notifications['user_event_publishing'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_user_event_publishing_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][user_event_publishing][subject]" id="mec_notifications_user_event_publishing_subject" value="<?php echo (isset($notifications['user_event_publishing']['subject']) ? stripslashes($notifications['user_event_publishing']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_user_event_publishing_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $users = isset($notifications['user_event_publishing']['receiver_users']) ? $notifications['user_event_publishing']['receiver_users'] : array();
                                            echo $this->main->get_users_dropdown($users, 'user_event_publishing');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_user_event_publishing_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                            $roles = isset($notifications['user_event_publishing']['receiver_roles']) ? $notifications['user_event_publishing']['receiver_roles'] : array();
                                            echo $this->main->get_roles_dropdown($roles, 'user_event_publishing');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_user_event_publishing_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][user_event_publishing][recipients]" id="mec_notifications_user_event_publishing_recipients" value="<?php echo (isset($notifications['user_event_publishing']['recipients']) ? $notifications['user_event_publishing']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label for="mec_notifications_user_event_publishing_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor((isset($notifications['user_event_publishing']) ? stripslashes($notifications['user_event_publishing']['content']) : ''), 'mec_notifications_user_event_publishing_content', array('textarea_name'=>'mec[notifications][user_event_publishing][content]')); ?>
                                </div>
                                <?php
                                    $section = 'user_event_publishing';
                                    do_action('mec_display_notification_settings',$notifications,$section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%name%%</span>: <?php _e('Event sender name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Title of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Link of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_start_date%%</span>: <?php _e('Event Start Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_end_date%%</span>: <?php _e('Event End Date', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_timezone%%</span>: <?php _e('Event Timezone', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_status%%</span>: <?php _e('Status of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_note%%</span>: <?php _e('Event Note', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%admin_link%%</span>: <?php _e('Admin events management link.', 'modern-events-calendar-lite'); ?></li>
                                            <?php do_action('mec_extra_field_notifications'); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Event Finished -->
                        <div id="event_finished" class="mec-options-fields <?php if(isset($this->settings['booking_status']) and $this->settings['booking_status'] == 0) echo 'active'; ?>">

                            <h4 class="mec-form-subtitle"><?php _e('Event Finished', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[notifications][event_finished][status]" value="0" />
                                        <input onchange="jQuery('#mec_notification_event_finished_container_toggle').toggle();" value="1" type="checkbox" name="mec[notifications][event_finished][status]" <?php if(isset($notifications['event_finished']['status']) and $notifications['event_finished']['status']) echo 'checked="checked"'; ?> /> <?php _e('Enable event finished notification', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <p class="mec-col-12 description"><?php _e('It sends after an event finish. You can use it to say thank you to the attendees.', 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>
                            <div id="mec_notification_event_finished_container_toggle" class="<?php if((isset($notifications['event_finished']) and !$notifications['event_finished']['status']) or !isset($notifications['event_finished'])) echo 'mec-util-hidden'; ?>">

                                <div class="mec-form-row">
                                    <?php $cron = MEC_ABSPATH.'app'.DS.'crons'.DS.'event-finished.php'; ?>
                                    <p class="mec-col-12 description"><strong><?php _e('Important Note', 'modern-events-calendar-lite'); ?>: </strong><?php echo sprintf(__("Set a cronjob to call %s file once per hour otherwise it won't send the notifications. Please note that you should call this file %s otherwise it may send the notifications multiple times.", 'modern-events-calendar-lite'), '<code>'.$cron.'</code>', '<strong>'.__('only once per hour', 'modern-events-calendar-lite').'</strong>'); ?></p>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_finished_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][event_finished][subject]" id="mec_notifications_event_finished_subject" value="<?php echo (isset($notifications['event_finished']['subject']) ? stripslashes($notifications['event_finished']['subject']) : ''); ?>" />
                                    </div>
                                </div>

                                <!-- Start Receiver Users -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_finished_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $users = isset($notifications['event_finished']['receiver_users']) ? $notifications['event_finished']['receiver_users'] : array();
                                        echo $this->main->get_users_dropdown($users, 'event_finished');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Users -->

                                <!-- Start Receiver Roles -->
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_finished_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <?php
                                        $roles = isset($notifications['event_finished']['receiver_roles']) ? $notifications['event_finished']['receiver_roles'] : array();
                                        echo $this->main->get_roles_dropdown($roles, 'event_finished');
                                        ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- End Receiver Roles -->

                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_finished_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="text" name="mec[notifications][event_finished][recipients]" id="mec_notifications_event_finished_recipients" value="<?php echo (isset($notifications['event_finished']['recipients']) ? $notifications['event_finished']['recipients'] : ''); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-3">
                                        <label for="mec_notifications_event_finished_hour"><?php _e('Hour', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                    <div class="mec-col-9">
                                        <input type="number" name="mec[notifications][event_finished][hour]" id="mec_notifications_event_finished_hour" value="<?php echo ((isset($notifications['event_finished']) and isset($notifications['event_finished']['hour'])) ? $notifications['event_finished']['hour'] : '2'); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Send After x Hour', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e('It specify the interval between event finish and sending the notification in hour.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label for="mec_notifications_user_event_publishing_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
                                    <?php wp_editor(((isset($notifications['event_finished']) and isset($notifications['event_finished']['content'])) ? stripslashes($notifications['event_finished']['content']) : ''), 'mec_notifications_event_finished_content', array('textarea_name'=>'mec[notifications][event_finished][content]')); ?>
                                </div>
                                <?php
                                    $section = 'event_finished';
                                    do_action('mec_display_notification_settings', $notifications, $section);
                                ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                                        <ul>
                                            <li><span>%%first_name%%</span>: <?php _e('First name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%last_name%%</span>: <?php _e('Last name of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%user_email%%</span>: <?php _e('Email of attendee', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date%%</span>: <?php _e('Booked date of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_time%%</span>: <?php _e('Booked time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime%%</span>: <?php _e('Booked date and time of event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_other_datetimes%%</span>: <?php _e('Other date and times of booking for multiple date booking system', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_date_next_occurrences%%</span>: <?php _e('Date of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_datetime_next_occurrences%%</span>: <?php _e('Date and Time of next 20 occurrences of booked event (including the booked date)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_price%%</span>: <?php _e('Booking Price', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%book_order_time%%</span>: <?php _e('Date and time of booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_name%%</span>: <?php _e('Your website title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_url%%</span>: <?php _e('Your website URL', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%blog_description%%</span>: <?php _e('Your website description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_title%%</span>: <?php _e('Event title', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_description%%</span>: <?php _e('Event Description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_tags%%</span>: <?php _e('Event Tags', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_labels%%</span>: <?php _e('Event Labels', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_categories%%</span>: <?php _e('Event Categories', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_cost%%</span>: <?php _e('Event Cost', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_link%%</span>: <?php _e('Event link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_speaker_name%%</span>: <?php _e('Speaker name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_name%%</span>: <?php _e('Organizer name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_tel%%</span>: <?php _e('Organizer tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_email%%</span>: <?php _e('Organizer email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_organizer_url%%</span>: <?php _e('Organizer url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_name%%</span>: <?php _e('Additional organizers name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_tel%%</span>: <?php _e('Additional organizers tel of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_email%%</span>: <?php _e('Additional organizers email of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_organizers_url%%</span>: <?php _e('Additional organizers url of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_name%%</span>: <?php _e('Location name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_location_address%%</span>: <?php _e('Location address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_name%%</span>: <?php _e('Additional locations name of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_locations_address%%</span>: <?php _e('Additional locations address of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_featured_image%%</span>: <?php _e('Featured image of booked event', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_more_info%%</span>: <?php _e('Event more info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%event_other_info%%</span>: <?php _e('Event other info link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%online_link%%</span>: <?php _e('Event online link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%attendees_full_info%%</span>: <?php _e('Full Attendee info such as booking form data, name, email etc.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_id%%</span>: <?php _e('Booking ID', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%booking_transaction_id%%</span>: <?php _e('Transaction ID of Booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%cancellation_link%%</span>: <?php _e('Booking cancellation link.', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%invoice_link%%</span>: <?php _e('Invoice Link', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%total_attendees%%</span>: <?php _e('Total attendees of current booking', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%amount_tickets%%</span>: <?php _e('Amount of Booked Tickets (Total attendees of all bookings)', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name%%</span>: <?php _e('Ticket name', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_time%%</span>: <?php _e('Ticket time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_name_time%%</span>: <?php _e('Ticket name & time', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ticket_private_description%%</span>: <?php _e('Ticket private description', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%payment_gateway%%</span>: <?php _e('Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%dl_file%%</span>: <?php _e('Link to the downloadable file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%ics_link%%</span>: <?php _e('Download ICS file', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link%%</span>: <?php _e('Add to Google Calendar', 'modern-events-calendar-lite'); ?></li>
                                            <li><span>%%google_calendar_link_next_occurrences%%</span>: <?php _e('Add to Google Calendar Links for next 20 occurrences', 'modern-events-calendar-lite'); ?></li>
                                            <?php do_action('mec_extra_field_notifications'); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="notifications_per_event" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Notifications Per Event', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[settings][notif_per_event]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][notif_per_event]" <?php if(isset($settings['notif_per_event']) and $settings['notif_per_event']) echo 'checked="checked"'; ?> /> <?php _e('Edit Notifications Per Event', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="notification_template" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Notification Template', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label>
                                        <input type="hidden" name="mec[settings][notif_template_disable]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][notif_template_disable]" <?php if(isset($settings['notif_template_disable']) and $settings['notif_template_disable']) echo 'checked="checked"'; ?> /> <?php _e('Disable Notification Template of MEC', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mec-options-fields">
                            <?php wp_nonce_field('mec_options_form'); ?>
                            <?php if($multilingual): ?>
                            <input name="mec_locale" type="hidden" value="<?php echo esc_attr($locale); ?>" />
                            <?php endif; ?>
                            <button style="display: none;" id="mec_notifications_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="wns-be-footer">
        <a href="" id="" class="dpr-btn dpr-save-btn"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></a>
    </div>

</div>

<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery(".dpr-save-btn").on('click', function(event)
    {
        event.preventDefault();
        jQuery("#mec_notifications_form_button").trigger('click');
    });
});

jQuery("#mec_notifications_form").on('submit', function(event)
{
    event.preventDefault();

    <?php
        $notifications = array(
            "booking_notification",
            "email_verification",
            "booking_confirmation",
            "booking_rejection",
            "admin_notification",
            "booking_reminder",
            "event_finished",
            "new_event",
            "user_event_publishing",
            "event_soldout",
        );

        $content_type = apply_filters('mec_settings_notifications_js_content_types',array(""));

        $notifications = apply_filters('mec_settings_notifications_js_notifications',$notifications);
    ?>
    var notifications = <?php echo json_encode($notifications); ?>;
    var content_types = <?php echo json_encode($content_type); ?>;

    jQuery.each(notifications,function(i,notification_type)
    {
        jQuery.each(content_types,function(j,type)
        {
            jQuery("#mec_notifications_"+notification_type+type+"_content-html").click();
            jQuery("#mec_notifications_"+notification_type+type+"_content-tmce").click();
        });
    });

    <?php do_action( 'mec_notification_menu_js' ); ?>
});
</script>

<script type="text/javascript">
jQuery("#mec_notifications_form").on('submit', function(event)
{
    event.preventDefault();

    // Add loading Class to the button
    jQuery(".dpr-save-btn").addClass('loading').text("<?php echo esc_js(esc_attr__('Saved', 'modern-events-calendar-lite')); ?>");
    jQuery('<div class="wns-saved-settings"><?php echo esc_js(esc_attr__('Settings Saved!', 'modern-events-calendar-lite')); ?></div>').insertBefore('#wns-be-content');

    if(jQuery(".mec-purchase-verify").text() != '<?php echo esc_js(esc_attr__('Verified', 'modern-events-calendar-lite')); ?>')
    {
        jQuery(".mec-purchase-verify").text("<?php echo esc_js(esc_attr__('Checking ...', 'modern-events-calendar-lite')); ?>");
    }

    var settings = jQuery("#mec_notifications_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=mec_save_notifications&"+settings,
        beforeSend: function()
        {
            jQuery('.wns-be-main').append('<div class="mec-loarder-wrap mec-settings-loader"><div class="mec-loarder"><div></div><div></div><div></div></div></div>');
        },
        success: function(data)
        {
            // Remove the loading Class to the button
            setTimeout(function()
            {
                jQuery(".dpr-save-btn").removeClass('loading').text("<?php echo esc_js(esc_attr__('Save Changes', 'modern-events-calendar-lite')); ?>");
                jQuery('.wns-saved-settings').remove();
                jQuery('.mec-loarder-wrap').remove();
                if(jQuery(".mec-purchase-verify").text() != '<?php echo esc_js(esc_attr__('Verified', 'modern-events-calendar-lite')); ?>')
                {
                    jQuery(".mec-purchase-verify").text("<?php echo esc_js(esc_attr__('Please Refresh Page', 'modern-events-calendar-lite')); ?>");
                }
            }, 1000);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            setTimeout(function()
            {
                jQuery(".dpr-save-btn").removeClass('loading').text("<?php echo esc_js(esc_attr__('Save Changes', 'modern-events-calendar-lite')); ?>");
                jQuery('.wns-saved-settings').remove();
                jQuery('.mec-loarder-wrap').remove();
            }, 1000);
        }
    });
});
</script>