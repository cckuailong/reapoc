<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var $this MEC_feature_books **/

$event_id = $event->ID;
$reg_fields = $this->main->get_reg_fields($event_id, $translated_event_id);
$bfixed_fields = $this->main->get_bfixed_fields($event_id, $translated_event_id);

$date_ex = explode(':', $date);
$occurrence = $date_ex[0];

$fees = $this->book->get_fees($event_id);

// WC System
$WC_status = (isset($this->settings['wc_status']) and $this->settings['wc_status'] and class_exists('WooCommerce')) ? true : false;
if($WC_status)
{
    $fees = array();
}

$event_tickets = isset($event->data->tickets) ? $event->data->tickets : array();

$total_ticket_prices = 0;
$check_free_tickets_booking = apply_filters('check_free_tickets_booking', 1);
$has_fees = count($fees) ? true : false;

$current_user = wp_get_current_user();
$first_for_all = (!isset($this->settings['booking_first_for_all']) or (isset($this->settings['booking_first_for_all']) and $this->settings['booking_first_for_all'] == 1)) ? true : false;

// Username & Password Method
$booking_register = (isset($this->settings['booking_registration']) and !$this->settings['booking_registration']) ? false : true;
$booking_userpass = (isset($this->settings['booking_userpass']) and trim($this->settings['booking_userpass'])) ? $this->settings['booking_userpass'] : 'auto';

// Lock Pre-filled Fields
$lock_prefilled = (isset($this->settings['booking_lock_prefilled']) and trim($this->settings['booking_lock_prefilled']) != '') ? $this->settings['booking_lock_prefilled'] : 0;

// Attendee Counter
$attendee_counter = (isset($this->settings['attendee_counter']) and $this->settings['attendee_counter']) ? $this->settings['attendee_counter'] : '';

$mec_email = false;
$mec_name = false;
foreach($reg_fields as $field)
{
    if(isset($field['type']))
    {
        if($field['type'] == 'mec_email') $mec_email = true;
        if($field['type'] == 'name') $mec_name = true;
    }
    else break;
}

if(!$mec_name)
{
    $reg_fields[] = array(
        'mandatory' => '0',
        'type'      => 'name',
        'label'     => esc_html__('Name', 'modern-events-calendar-lite'),
    );
}

if(!$mec_email)
{
    $reg_fields[] = array(
        'mandatory' => '0',
        'type'      => 'mec_email',
        'label'     => esc_html__('Email', 'modern-events-calendar-lite'),
    );
}

?>
<form id="mec_book_form<?php echo $uniqueid; ?>" class="mec-booking-form-container row" onsubmit="mec_book_form_submit(event, <?php echo $uniqueid; ?>);" novalidate="novalidate" enctype="multipart/form-data" method="post">
    <h4><?php echo apply_filters('mec-booking-attendees-title', __('Attendee\'s Form', 'modern-events-calendar-lite'), $event_id) ?></h4>

    <?php
        $custom_view_fields = apply_filters('mec_have_custom_view_fields', false, $bfixed_fields, 'booking_fixed_fields', $event_id);
        $have_bfixed_fields = is_array($bfixed_fields) and count($bfixed_fields);
    ?>
    <?php if (!$custom_view_fields && $have_bfixed_fields): ?>
        <ul class="mec-book-bfixed-fields-container">
            <?php foreach ($bfixed_fields as $bfixed_field_id => $bfixed_field) : if (!is_numeric($bfixed_field_id) or !isset($bfixed_field['type'])) continue; ?>
                <li class="mec-book-bfixed-field-<?php echo $bfixed_field['type']; ?> <?php echo ((isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) ? 'mec-reg-mandatory' : ''); ?>" data-field-id="<?php echo $bfixed_field_id; ?>">

                    <?php if (isset($bfixed_field['label']) and $bfixed_field['type'] != 'agreement') : ?>
                        <label for="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>"><?php _e($bfixed_field['label'], 'modern-events-calendar-lite'); ?><?php echo ((isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) ? '<span class="wbmec-mandatory">*</span>' : ''); ?></label>
                    <?php endif; ?>

                    <?php /** Text **/ if ($bfixed_field['type'] == 'text') : ?>
                        <input id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>" type="text" name="book[fields][<?php echo $bfixed_field_id; ?>]" value="" placeholder="<?php if (isset($bfixed_field['placeholder']) and $bfixed_field['placeholder']) {
                            _e($bfixed_field['placeholder'], 'modern-events-calendar-lite');
                        } else {
                            _e($bfixed_field['label'], 'modern-events-calendar-lite');
                        }; ?>" <?php if (isset($bfixed_field['placeholder']) and $bfixed_field['placeholder']) echo 'placeholder="' . $bfixed_field['placeholder'] . '" '; ?> <?php if (isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) echo 'required'; ?> />

                    <?php /** Date **/ elseif ($bfixed_field['type'] == 'date') : ?>
                        <input id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>" type="date" name="book[fields][<?php echo $bfixed_field_id; ?>]" value="" placeholder="<?php if (isset($bfixed_field['placeholder']) and $bfixed_field['placeholder']) {
                            _e($bfixed_field['placeholder'], 'modern-events-calendar-lite');
                        } else {
                            _e($bfixed_field['label'], 'modern-events-calendar-lite');
                        }; ?>" <?php if (isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) echo 'required'; ?> min="<?php echo esc_attr(date_i18n('Y-m-d', strtotime('-100 years'))); ?>" max="<?php echo esc_attr(date_i18n('Y-m-d', strtotime('+100 years'))); ?>" />

                    <?php /** Email **/ elseif ($bfixed_field['type'] == 'email') : ?>
                        <input id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>" type="email" name="book[fields][<?php echo $bfixed_field_id; ?>]" value="" placeholder="<?php if (isset($bfixed_field['placeholder']) and $bfixed_field['placeholder']) {
                            _e($bfixed_field['placeholder'], 'modern-events-calendar-lite');
                        } else {
                            _e($bfixed_field['label'], 'modern-events-calendar-lite');
                        }; ?>" <?php if (isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) echo 'required'; ?> />

                    <?php /** Tel **/ elseif ($bfixed_field['type'] == 'tel') : ?>
                        <input id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>" oninput="this.value=this.value.replace(/(?![0-9])./gmi,'')" type="tel" name="book[fields][<?php echo $bfixed_field_id; ?>]" value="" placeholder="<?php if (isset($bfixed_field['placeholder']) and $bfixed_field['placeholder']) {
                            _e($bfixed_field['placeholder'], 'modern-events-calendar-lite');
                        } else {
                            _e($bfixed_field['label'], 'modern-events-calendar-lite');
                        }; ?>" <?php if (isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) echo 'required'; ?> />

                    <?php /** Textarea **/ elseif ($bfixed_field['type'] == 'textarea') : ?>
                        <textarea id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>" name="book[fields][<?php echo $bfixed_field_id; ?>]" placeholder="<?php if (isset($bfixed_field['placeholder']) and $bfixed_field['placeholder']) {
                            _e($bfixed_field['placeholder'], 'modern-events-calendar-lite');
                        } else {
                            _e($bfixed_field['label'], 'modern-events-calendar-lite');
                        }; ?>" <?php if (isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) echo 'required'; ?>></textarea>

                    <?php /** Dropdown **/ elseif ($bfixed_field['type'] == 'select') : ?>
                        <select id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>" name="book[fields][<?php echo $bfixed_field_id; ?>]" placeholder="<?php if (isset($bfixed_field['placeholder']) and $bfixed_field['placeholder']) {
                            _e($bfixed_field['placeholder'], 'modern-events-calendar-lite');
                        } else {
                            _e($bfixed_field['label'], 'modern-events-calendar-lite');
                        }; ?>" <?php if (isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) echo 'required'; ?>>
                            <?php $bfd = 0; foreach ($bfixed_field['options'] as $bfixed_field_option) : $bfd++; ?>
                                <option value="<?php echo (($bfd == 1 and isset($bfixed_field['ignore']) and $bfixed_field['ignore']) ? '' : esc_attr__($bfixed_field_option['label'], 'modern-events-calendar-lite')); ?>"><?php _e($bfixed_field_option['label'], 'modern-events-calendar-lite'); ?></option>
                            <?php endforeach; ?>
                        </select>

                    <?php /** Radio **/ elseif ($bfixed_field['type'] == 'radio') : ?>
                        <?php foreach ($bfixed_field['options'] as $bfixed_field_option) : ?>
                            <label for="mec_book_bfixed_field_reg<?php echo $bfixed_field_id . '_' . strtolower(str_replace(' ', '_', $bfixed_field_option['label'])); ?>">
                                <input type="radio" id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id . '_' . strtolower(str_replace(' ', '_', $bfixed_field_option['label'])); ?>" name="book[fields][<?php echo $bfixed_field_id; ?>]" value="<?php _e($bfixed_field_option['label'], 'modern-events-calendar-lite'); ?>" />
                                <?php _e($bfixed_field_option['label'], 'modern-events-calendar-lite'); ?>
                            </label>
                        <?php endforeach; ?>

                    <?php /** Checkbox **/ elseif ($bfixed_field['type'] == 'checkbox') : ?>
                        <?php foreach ($bfixed_field['options'] as $bfixed_field_option) : ?>
                            <label for="mec_book_bfixed_field_reg<?php echo $bfixed_field_id . '_' . strtolower(str_replace(' ', '_', $bfixed_field_option['label'])); ?>">
                                <input type="checkbox" id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id . '_' . strtolower(str_replace(' ', '_', $bfixed_field_option['label'])); ?>" name="book[fields][<?php echo $bfixed_field_id; ?>][]" value="<?php _e($bfixed_field_option['label'], 'modern-events-calendar-lite'); ?>" />
                                <?php _e($bfixed_field_option['label'], 'modern-events-calendar-lite'); ?>
                            </label>
                        <?php endforeach; ?>

                    <?php /** Agreement **/ elseif ($bfixed_field['type'] == 'agreement') : ?>
                        <label for="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>">
                            <input type="checkbox" id="mec_book_bfixed_field_reg<?php echo $bfixed_field_id; ?>" name="book[fields][<?php echo $bfixed_field_id; ?>]" value="1" <?php echo (!isset($bfixed_field['status']) or (isset($bfixed_field['status']) and $bfixed_field['status'] == 'checked')) ? 'checked="checked"' : ''; ?> onchange="mec_agreement_change(this);" />
                            <?php echo ((isset($bfixed_field['mandatory']) and $bfixed_field['mandatory']) ? '<span class="wbmec-mandatory">*</span>' : ''); ?>
                            <?php echo sprintf(__(stripslashes($bfixed_field['label']), 'modern-events-calendar-lite'), '<a href="' . get_the_permalink($bfixed_field['page']) . '" target="_blank">' . get_the_title($bfixed_field['page']) . '</a>'); ?>
                        </label>

                    <?php /** Paragraph **/ elseif ($bfixed_field['type'] == 'p') : ?>
                        <p><?php
                            $paragraph = isset($bfixed_field['paragraph']) ? $bfixed_field['paragraph'] : '';
                            $content = isset($bfixed_field['content']) ? $bfixed_field['content'] : $paragraph;
                            echo do_shortcode(stripslashes($content)); ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif( $custom_view_fields && $have_bfixed_fields ): ?>
        <?php do_action( 'mec_custom_view_fields', $bfixed_fields, 'booking_fixed_fields',$event_id ); ?>
    <?php endif; ?>

    <ul class="mec-book-tickets-container">

        <?php $j = 0;
        foreach ($tickets as $ticket_id => $count) : if (!$count) continue;
            $ticket = $event_tickets[$ticket_id];
            for ($i = 1; $i <= $count; $i++) : $j++;
                $total_ticket_prices += $this->book->get_ticket_price($ticket, current_time('Y-m-d'), $event_id); ?>
                <li class="mec-book-ticket-container <?php echo (($j > 1 and $first_for_all) ? 'mec-util-hidden' : ''); ?>">
                    <?php if (!empty($ticket['name']) || !empty($this->book->get_ticket_price_label($ticket, current_time('Y-m-d'), $event_id))) : ?>
                        <h4 class="col-md-12">
                            <?php if ($attendee_counter == 1):?><span class="mec-ticket-attendee-counter"><?php printf( __( 'Attendee #%s details — ', 'modern-events-calendar-lite' ), $i ); ?></span><?php endif;?>
                            <span class="mec-ticket-name"><?php echo __($ticket['name'], 'modern-events-calendar-lite'); ?></span>
                            <span class="mec-ticket-price"><?php echo $this->book->get_ticket_price_label($ticket, current_time('Y-m-d'), $event_id); ?></span>
                        </h4>
                    <?php endif; ?>

                    <!-- Custom fields -->
                    <?php if (count($reg_fields)) : foreach ($reg_fields as $reg_field_id => $reg_field) : if (!is_numeric($reg_field_id) or !isset($reg_field['type'])) continue; ?>

                        <?php $reg_field_name = strtolower(str_replace([' ', ',', ':', '"', "'"], '_', $reg_field['label'])); ?>
                        <?php if (isset($reg_field['single_row']) && $reg_field['single_row'] == 'enable') : ?>
                            <div class="clearfix"></div>
                        <?php endif; ?>
                        <div class="mec-book-reg-field-<?php echo $reg_field['type']; ?> <?php echo ((isset($reg_field['mandatory']) and $reg_field['mandatory']) ? 'mec-reg-mandatory' : ''); ?><?php
                        if (isset($reg_field['inline']) && $reg_field['inline'] == 'enable') {
                            echo ' col-md-6';
                        } else if (isset($reg_field['inline_third']) && $reg_field['inline_third'] == 'enable') {
                            echo ' col-md-4';
                        } else {
                            echo ' col-md-12';
                        }
                        ?>" data-ticket-id="<?php echo $j; ?>" data-field-id="<?php echo $reg_field_id; ?>">
                            <?php if (isset($reg_field['label']) and $reg_field['type'] != 'agreement' &&  $reg_field['type'] != 'name' && $reg_field['type'] != 'mec_email') : ?><label for="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>"><?php _e($reg_field['label'], 'modern-events-calendar-lite'); ?><?php echo ((isset($reg_field['mandatory']) and $reg_field['mandatory']) ? '<span class="wbmec-mandatory">*</span>' : ''); ?></label><?php endif; ?>

                            <?php /** Name **/ if ($reg_field['type'] == 'name') : ?>
                                <?php $reg_field['label'] = ($reg_field['label']) ? $reg_field['label'] : 'Name'; ?>
                                <label for="mec_book_reg_field_name<?php echo $reg_field_id; ?>"><?php _e($reg_field['label'], 'modern-events-calendar-lite'); ?><span class="wbmec-mandatory">*</span></label>
                                <input id="mec_book_reg_field_name<?php echo $reg_field_id; ?>" type="text" name="book[tickets][<?php echo $j; ?>][name]" value="<?php echo trim((isset($current_user->user_firstname) ? $current_user->user_firstname : '') . ' ' . (isset($current_user->user_lastname) ? $current_user->user_lastname : '')); ?>" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" required <?php echo ((isset($current_user->user_firstname) and trim($current_user->user_firstname) and ($lock_prefilled == 1 or ($lock_prefilled == 2 and $j == 1))) ? 'readonly' : ''); ?> />

                            <?php /** MEC Email **/ elseif ($reg_field['type'] == 'mec_email') : ?>
                                <?php $reg_field['label'] = ($reg_field['label']) ? $reg_field['label'] : 'Email'; ?>
                                <label for="mec_book_reg_field_email<?php echo $reg_field_id; ?>"><?php _e($reg_field['label'], 'modern-events-calendar-lite'); ?><span class="wbmec-mandatory">*</span></label>
                                <input id="mec_book_reg_field_email<?php echo $reg_field_id; ?>" type="email" name="book[tickets][<?php echo $j; ?>][email]" value="<?php echo isset($current_user->user_email) ? $current_user->user_email : ''; ?>" placeholder="<?php _e('Email', 'modern-events-calendar-lite'); ?>" required <?php echo ((isset($current_user->user_email) and trim($current_user->user_email) and ($lock_prefilled == 1 or ($lock_prefilled == 2 and $j == 1))) ? 'readonly' : ''); ?> />

                            <?php /** Text **/ elseif ($reg_field['type'] == 'text') : ?>
                                <input id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" type="text" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" value="<?php echo esc_attr($this->main->get_from_mapped_field($reg_field)); ?>" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" <?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) echo 'placeholder="' . $reg_field['placeholder'] . '" '; ?> <?php if (isset($reg_field['mandatory']) and $reg_field['mandatory']) echo 'required'; ?> />

                            <?php /** Date **/ elseif ($reg_field['type'] == 'date') : ?>
                                <input class="mec-date-picker" id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" type="date" onload="mec_add_datepicker()" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" value="<?php echo esc_attr($this->main->get_from_mapped_field($reg_field)); ?>" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" <?php if (isset($reg_field['mandatory']) and $reg_field['mandatory']) echo 'required'; ?> min="<?php echo esc_attr(date_i18n('Y-m-d', strtotime('-100 years'))); ?>" max="<?php echo esc_attr(date_i18n('Y-m-d', strtotime('+100 years'))); ?>" />

                            <?php /** File **/ elseif ($reg_field['type'] == 'file') : ?>
                                <input id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" type="file" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" value="" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" <?php if (isset($reg_field['mandatory']) and $reg_field['mandatory']) echo 'required'; ?> />

                            <?php /** Email **/ elseif ($reg_field['type'] == 'email') : ?>
                                <input id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" type="email" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" value="<?php echo esc_attr($this->main->get_from_mapped_field($reg_field)); ?>" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" <?php if (isset($reg_field['mandatory']) and $reg_field['mandatory']) echo 'required'; ?> />

                            <?php /** Tel **/ elseif ($reg_field['type'] == 'tel') : ?>
                                <input id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" oninput="this.value=this.value.replace(/(?![0-9])./gmi,'')" type="tel" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" value="<?php echo esc_attr($this->main->get_from_mapped_field($reg_field)); ?>" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" <?php if (isset($reg_field['mandatory']) and $reg_field['mandatory']) echo 'required'; ?> />

                            <?php /** Textarea **/ elseif ($reg_field['type'] == 'textarea') : ?>
                                <textarea id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" <?php if (isset($reg_field['mandatory']) and $reg_field['mandatory']) echo 'required'; ?>><?php echo esc_textarea($this->main->get_from_mapped_field($reg_field)); ?></textarea>

                            <?php /** Dropdown **/ elseif ($reg_field['type'] == 'select') : ?>
                                <select id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" placeholder="<?php if (isset($reg_field['placeholder']) and $reg_field['placeholder']) {
                                    _e($reg_field['placeholder'], 'modern-events-calendar-lite');
                                } else {
                                    _e($reg_field['label'], 'modern-events-calendar-lite');
                                }; ?>" <?php if (isset($reg_field['mandatory']) and $reg_field['mandatory']) echo 'required'; ?>>
                                    <?php $rd = 0; $s_value = $this->main->get_from_mapped_field($reg_field); foreach ($reg_field['options'] as $reg_field_option) : $rd++; ?>
                                        <option value="<?php echo (($rd == 1 and isset($reg_field['ignore']) and $reg_field['ignore']) ? '' : esc_attr__($reg_field_option['label'], 'modern-events-calendar-lite')); ?>" <?php echo (($s_value and $s_value == __($reg_field_option['label'], 'modern-events-calendar-lite')) ? 'selected="selected"' : ''); ?>><?php _e($reg_field_option['label'], 'modern-events-calendar-lite'); ?></option>
                                    <?php endforeach; ?>
                                </select>

                            <?php /** Radio **/ elseif ($reg_field['type'] == 'radio') : ?>
                                <?php $r_value = $this->main->get_from_mapped_field($reg_field); foreach ($reg_field['options'] as $reg_field_option) : ?>
                                    <label for="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id . '_' . strtolower(str_replace(' ', '_', $reg_field_option['label'])); ?>">
                                        <input type="radio" id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id . '_' . strtolower(str_replace(' ', '_', $reg_field_option['label'])); ?>" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" value="<?php _e($reg_field_option['label'], 'modern-events-calendar-lite'); ?>" <?php echo (($r_value and $r_value == __($reg_field_option['label'], 'modern-events-calendar-lite')) ? 'checked="checked"' : ''); ?> />
                                        <?php _e($reg_field_option['label'], 'modern-events-calendar-lite'); ?>
                                    </label>
                                <?php endforeach; ?>

                            <?php /** Checkbox **/ elseif ($reg_field['type'] == 'checkbox') : ?>
                                <?php $c_values = array_map('trim', explode(',', $this->main->get_from_mapped_field($reg_field))); foreach ($reg_field['options'] as $reg_field_option) : ?>
                                    <label for="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id . '_' . strtolower(str_replace(' ', '_', $reg_field_option['label'])); ?>">
                                        <input type="checkbox" id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id . '_' . strtolower(str_replace(' ', '_', $reg_field_option['label'])); ?>" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>][]" value="<?php _e($reg_field_option['label'], 'modern-events-calendar-lite'); ?>" <?php echo (($c_values and is_array($c_values) and in_array(__($reg_field_option['label'], 'modern-events-calendar-lite'), $c_values)) ? 'checked="checked"' : ''); ?> />
                                        <?php _e($reg_field_option['label'], 'modern-events-calendar-lite'); ?>
                                    </label>
                                <?php endforeach; ?>

                            <?php /** Agreement **/ elseif ($reg_field['type'] == 'agreement') : ?>
                                <label for="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>">
                                    <input type="checkbox" id="mec_book_reg_field_reg<?php echo $j . '_' . $reg_field_id; ?>" name="book[tickets][<?php echo $j; ?>][reg][<?php echo $reg_field_id; ?>]" value="1" <?php echo (!isset($reg_field['status']) or (isset($reg_field['status']) and $reg_field['status'] == 'checked')) ? 'checked="checked"' : ''; ?> onchange="mec_agreement_change(this);" />
                                    <?php echo ((isset($reg_field['mandatory']) and $reg_field['mandatory']) ? '<span class="wbmec-mandatory">*</span>' : ''); ?>
                                    <?php echo sprintf(__(stripslashes($reg_field['label']), 'modern-events-calendar-lite'), '<a href="' . get_the_permalink($reg_field['page']) . '" target="_blank">' . get_the_title($reg_field['page']) . '</a>'); ?>
                                </label>

                            <?php /** Paragraph **/ elseif ($reg_field['type'] == 'p') : ?>
                                <p>
                                <?php
                                    $paragraph = isset($reg_field['paragraph']) ? $reg_field['paragraph'] : '';
                                    $content = isset($reg_field['content']) ? $reg_field['content'] : $paragraph;
                                    echo do_shortcode(stripslashes($content));
                                ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;
                    endif; ?>

                    <!-- Ticket Variations -->
                    <?php
                        $ticket_variations = $this->main->ticket_variations($event_id, $ticket_id, $translated_event_id);

                        if($WC_status) $ticket_variations = array();
                        if($this->main->has_variations_per_ticket($event_id, $ticket_id)) $first_for_all = false;

                        $has_variations = count($ticket_variations) ? true : false;
                    ?>
                    <?php if(isset($this->settings['ticket_variations_status']) and $this->settings['ticket_variations_status'] and count($ticket_variations)): foreach($ticket_variations as $ticket_variation_id => $ticket_variation): if(!is_numeric($ticket_variation_id) or !isset($ticket_variation['title']) or (isset($ticket_variation['title']) and !trim($ticket_variation['title']))) continue; ?>
                        <div class="col-md-12">
                            <div class="mec-book-ticket-variation" data-ticket-id="<?php echo $j; ?>" data-ticket-variation-id="<?php echo $ticket_variation_id; ?>">
                                <h5><span class="mec-ticket-variation-name"><?php echo $ticket_variation['title']; ?></span><span class="mec-ticket-variation-price"><?php echo $this->main->render_price($ticket_variation['price'], $event_id); ?></span></h5>
                                <input onkeydown="return event.keyCode !== 69" type="number" min="0" max="<?php echo ((is_numeric($ticket_variation['max']) and $ticket_variation['max']) ? $ticket_variation['max'] : ''); ?>" name="book[tickets][<?php echo $j; ?>][variations][<?php echo $ticket_variation_id; ?>]" onchange="mec_check_variation_min_max<?php echo $uniqueid; ?>(this);">
                            </div>
                        </div>
                    <?php endforeach;
                    endif; ?>

                    <input type="hidden" name="book[tickets][<?php echo $j; ?>][id]" value="<?php echo $ticket_id; ?>" />
                    <input type="hidden" name="book[tickets][<?php echo $j; ?>][count]" value="1" />
                </li>
        <?php endfor;
        endforeach; ?>

        <?php if ($j > 1 and $first_for_all) : ?>
            <li class="mec-first-for-all-wrapper">
                <label class="mec-fill-attendees">
                    <input type="hidden" name="book[first_for_all]" value="0" />
                    <input type="checkbox" name="book[first_for_all]" value="1" checked="checked" class="mec_book_first_for_all" id="mec_book_first_for_all<?php echo $uniqueid; ?>" onchange="mec_toggle_first_for_all<?php echo $uniqueid; ?>(this);" />
                    <label for="pages1" onclick="mec_label_first_for_all<?php echo $uniqueid; ?>(this);" class="wn-checkbox-label"></label>
                    <?php _e("Fill other attendees information like the first form.", 'modern-events-calendar-lite'); ?>
                </label>
            </li>
        <?php endif; ?>

    </ul>

    <?php if($booking_register and $booking_userpass == 'manual' and !is_user_logged_in()): ?>
    <div class="mec-book-username-password-wrapper">
        <h3><?php esc_html_e('Registration', 'modern-events-calendar-lite'); ?></h3>
        <div>
            <label for="mec_book_form_username"><?php esc_html_e('Username', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="book[username]" id="mec_book_form_username">
        </div>
        <div>
            <label for="mec_book_form_password"><?php esc_html_e('Password', 'modern-events-calendar-lite'); ?></label>
            <input type="password" name="book[password]" id="mec_book_form_password">
        </div>
    </div>
    <?php endif; ?>

    <div class="clearfix"></div>

    <?php if(isset($all_dates) and count($all_dates)): // Multiple Date ?>
        <?php foreach($all_dates as $d): ?>
        <input type="hidden" name="book[date][]" value="<?php echo $d; ?>" />
        <?php endforeach; ?>
    <?php else: ?>
    <input type="hidden" name="book[date]" value="<?php echo $date; ?>" />
    <?php endif; ?>
    <input type="hidden" name="book[event_id]" value="<?php echo $event_id; ?>" />
    <input type="hidden" name="book[translated_event_id]" value="<?php echo $translated_event_id; ?>" />
    <input type="hidden" name="lang" value="<?php echo $this->main->get_current_locale(); ?>" />
    <input type="hidden" name="action" value="mec_book_form" />
    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
    <input type="hidden" name="translated_event_id" value="<?php echo $translated_event_id; ?>" />
    <input type="hidden" name="uniqueid" value="<?php echo $uniqueid; ?>" />
    <input type="hidden" name="step" value="2" />
    <?php wp_nonce_field('mec_book_form_' . $event_id); ?>
    <div class="mec-book-form-btn-wrap" style="overflow: hidden;">
        <button id="mec-book-form-back-btn-step-2" class="mec-book-form-back-button" type="button" onclick="mec_book_form_back_btn_click(this);"><?php _e('Back', 'modern-events-calendar-lite'); ?></button>
        <button id="mec-book-form-btn-step-2" class="mec-book-form-next-button" type="submit" onclick="mec_book_form_back_btn_cache(this, <?php echo $uniqueid; ?>);"><?php echo ($WC_status ? __('Add to Cart', 'modern-events-calendar-lite') : ((!$total_ticket_prices and !$has_fees and !$has_variations && $check_free_tickets_booking) ? __('Submit', 'modern-events-calendar-lite') : __('Next', 'modern-events-calendar-lite'))); ?></button>
    </div>
</form>
<style>
    .nice-select {
        -webkit-tap-highlight-color: transparent;
        background-color: #fff;
        border-radius: 5px;
        border: solid 1px #e8e8e8;
        box-sizing: border-box;
        clear: both;
        cursor: pointer;
        display: block;
        float: left;
        font-family: inherit;
        font-size: 14px;
        font-weight: 400;
        height: 42px;
        line-height: 40px;
        outline: 0;
        padding-left: 18px;
        padding-right: 30px;
        position: relative;
        text-align: left !important;
        -webkit-transition: all .2s ease-in-out;
        transition: all .2s ease-in-out;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        white-space: nowrap;
        width: auto
    }

    .nice-select:hover {
        border-color: #dbdbdb
    }

    .nice-select.open,
    .nice-select:active,
    .nice-select:focus {
        border-color: #999
    }

    .nice-select:after {
        border-bottom: 2px solid #999;
        border-right: 2px solid #999;
        content: '';
        display: block;
        height: 5px;
        margin-top: -4px;
        pointer-events: none;
        position: absolute;
        right: 12px;
        top: 50%;
        -webkit-transform-origin: 66% 66%;
        -ms-transform-origin: 66% 66%;
        transform-origin: 66% 66%;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        -webkit-transition: all .15s ease-in-out;
        transition: all .15s ease-in-out;
        width: 5px
    }

    .nice-select.open:after {
        -webkit-transform: rotate(-135deg);
        -ms-transform: rotate(-135deg);
        transform: rotate(-135deg)
    }

    .nice-select.open .list {
        opacity: 1;
        pointer-events: auto;
        -webkit-transform: scale(1) translateY(0);
        -ms-transform: scale(1) translateY(0);
        transform: scale(1) translateY(0)
    }

    .nice-select.disabled {
        border-color: #ededed;
        color: #999;
        pointer-events: none
    }

    .nice-select.disabled:after {
        border-color: #ccc
    }

    .nice-select.wide {
        width: 100%
    }

    .nice-select.wide .list {
        left: 0 !important;
        right: 0 !important
    }

    .nice-select.right {
        float: right
    }

    .nice-select.right .list {
        left: auto;
        right: 0
    }

    .nice-select.small {
        font-size: 12px;
        height: 36px;
        line-height: 34px
    }

    .nice-select.small:after {
        height: 4px;
        width: 4px
    }

    .nice-select.small .option {
        line-height: 34px;
        min-height: 34px
    }

    .nice-select .list {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 0 1px rgba(68, 68, 68, .11);
        box-sizing: border-box;
        margin-top: 4px;
        opacity: 0;
        overflow: hidden;
        padding: 0;
        pointer-events: none;
        position: absolute;
        top: 100%;
        left: 0;
        -webkit-transform-origin: 50% 0;
        -ms-transform-origin: 50% 0;
        transform-origin: 50% 0;
        -webkit-transform: scale(.75) translateY(-21px);
        -ms-transform: scale(.75) translateY(-21px);
        transform: scale(.75) translateY(-21px);
        -webkit-transition: all .2s cubic-bezier(.5, 0, 0, 1.25), opacity .15s ease-out;
        transition: all .2s cubic-bezier(.5, 0, 0, 1.25), opacity .15s ease-out;
        z-index: 9
    }

    .nice-select .list:hover .option:not(:hover) {
        background-color: transparent !important
    }

    .nice-select .option {
        cursor: pointer;
        font-weight: 400;
        line-height: 40px;
        list-style: none;
        min-height: 40px;
        outline: 0;
        padding-left: 18px;
        padding-right: 29px;
        text-align: left;
        -webkit-transition: all .2s;
        transition: all .2s
    }

    .nice-select .option.focus,
    .nice-select .option.selected.focus,
    .nice-select .option:hover {
        background-color: #f6f6f6
    }

    .nice-select .option.selected {
        font-weight: 700
    }

    .nice-select .option.disabled {
        background-color: transparent;
        color: #999;
        cursor: default
    }

    .no-csspointerevents .nice-select .list {
        display: none
    }

    .no-csspointerevents .nice-select.open .list {
        display: block
    }
</style>
<script type="text/javascript" src="<?php echo $this->main->asset('js/jquery.nice-select.min.js'); ?>"></script>
<script>
    jQuery(document).ready(function() {
        if (jQuery('.mec-booking-shortcode').length < 0) {
            return;
        }
        // Events
        jQuery('.mec-booking-shortcode').find('select').niceSelect();

    });
</script>