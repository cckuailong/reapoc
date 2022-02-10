 <div class="wpbs-booking-details-modal-booking-details">

    <div class="wpbs-booking-details-modal-column wpbs-booking-details-modal-column-left">

        <h3><?php echo __('Booking Data', 'wp-booking-system') ?></h3>

        <table>
            <?php foreach($this->get_booking_data() as $data): ?>
                <tr><td><strong><?php echo $data['label'] ?>:</strong></td><td><p><?php echo $data['value'] ?></p></td></tr>
            <?php endforeach ?>
        </table>

    </div>

    <div class="wpbs-booking-details-modal-column wpbs-booking-details-modal-column-right">
        
        <h3><?php echo __('Form Data', 'wp-booking-system') ?></h3>

        <table>
            <?php foreach($this->get_form_data() as $data): ?>
                <tr><td><strong><?php echo $data['label'] ?>:</strong></td><td><p><?php echo $data['value'] ?></p></td></tr>
            <?php endforeach ?>
        </table>
    </div>
</div>