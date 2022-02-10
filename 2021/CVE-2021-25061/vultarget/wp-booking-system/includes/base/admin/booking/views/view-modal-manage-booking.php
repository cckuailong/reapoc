<h3 class="wpbs-no-margin"><?php echo __('Edit the calendar availability', 'wp-booking-system'); ?></h3>

<!-- Calendar Editor -->
<div class="wpbs-booking-details-modal-column wpbs-booking-details-modal-column-left">
    <?php echo $this->calendar_editor(); ?>
</div>

<!-- Bulk Date Editor -->
<div class="wpbs-booking-details-modal-column wpbs-booking-details-modal-column-right">
    <h3><?php echo __('Bulk Edit Dates', 'wp-booking-system'); ?></h3>
    <div id="wpbs-bulk-edit-availability-booking-wrap">
        <p>
            <label for="wpbs-bulk-edit-availability-booking-legend-item"><?php echo __('Legend Item', 'wp-booking-system'); ?></label>
            <select id="wpbs-bulk-edit-availability-booking-legend-item">
                <?php echo $this->get_legends_as_options(); ?>
            </select>
        </p>
        <p>
            <label for="wpbs-bulk-edit-availability-booking-description"><?php echo __('Description', 'wp-booking-system'); ?></label>
            <input id="wpbs-bulk-edit-availability-booking-description" type="text" />
        </p>
        <p>
            <label for="wpbs-bulk-edit-availability-booking-tooltip"><?php echo __('Tooltip', 'wp-booking-system'); ?></label>
            <input id="wpbs-bulk-edit-availability-booking-tooltip" type="text" />
        </p>
        <a id="wpbs-bulk-edit-availability-booking" class="button-secondary" href="#"><?php echo __('Apply Changes', 'wp-booking-system'); ?></a>
    </div>
</div>

<div class="wpbs-clear"><!-- --></div>


<div class="wpbs-booking-details-modal-footer-actions">

    <hr>

    <?php if ($this->booking->get('status') == 'trash'): ?>

        <button class="button button-primary wpbs-action-update-booking" data-action="restore" data-booking-id="<?php echo $this->booking->get('id'); ?>">
            <?php echo $this->get_manage_booking_button_label(); ?>
        </button>

        <a href="<?php echo wp_nonce_url(add_query_arg(array('page' => 'wpbs-calendars', 'wpbs_action' => 'permanently_delete_booking', 'booking_id' => $this->booking->get('id'), 'calendar_id' => $this->calendar->get('id')), admin_url('admin.php')), 'wpbs_permanently_delete_booking', 'wpbs_token'); ?>" class="button button-secondary wpbs-permanently-delete-booking"><?php echo __('Permanently delete booking', 'wp-booking-system') ?></a>
    <?php else: ?>

        <button class="button button-primary wpbs-action-update-booking" data-action="accept" data-booking-id="<?php echo $this->booking->get('id'); ?>">
            <?php echo $this->get_manage_booking_button_label(); ?>
        </button>

        <button class="button button-secondary wpbs-action-update-booking wpbs-delete-booking" data-action="delete" data-booking-id="<?php echo $this->booking->get('id'); ?>">
            <?php echo __('Delete Booking', 'wp-booking-system'); ?>
        </button>

        <?php echo wpbs_get_output_tooltip(__('Accepting, Updating or Deleting the booking will also update the calendar availability as per the form above.', 'wp-booking-system')) ?>

    <?php endif;?>

</div>