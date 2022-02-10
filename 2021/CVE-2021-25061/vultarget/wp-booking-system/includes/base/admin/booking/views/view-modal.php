<div id="wpbs-booking-details-modal-inner" class="<?php echo (defined('WPBS_ENABLE_PRICING') && WPBS_ENABLE_PRICING === true) ? 'wpbs-pricing-enabled' : '';?>">
    <a href="#" id="wpbs-booking-details-modal-close"><i class="wpbs-icon-close"></i></a>
    <h1>
        <i class="wpbs-icon-calendar"></i> <?php echo __('Booking #','wp-booking-system');?><?php echo $this->booking->get('id');?>
    </h1>


    <!-- Tab Navigation -->
    <h2 class="wpbs-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($this->tabs as $tab_slug => $tab_name):?>
            <?php $active_class = $tab_slug == $this->get_active_tab() ? 'wpbs-active nav-tab-active' : ''; ?>
            <a href="#" data-tab="<?php echo $tab_slug;?>" class="nav-tab wpbs-nav-tab <?php echo $active_class;?>"><?php echo $tab_name;?></a>
        <?php endforeach; ?>
        <a href="javascript:window.print();" class="button button-secondary wpbs-booking-print"><i class="wpbs-icon-print"></i> Print</a>
    </h2>

    <!-- Tabs Navigation -->
    <div class="wpbs-tabs-wrapper">

        <?php foreach ($this->tabs as $tab_slug => $tab_name):?>

            <?php $active_class = $tab_slug == $this->get_active_tab() ? 'wpbs-active' : ''; ?>

            <div class="wpbs-tab wpbs-tab-<?php echo $tab_slug;?> <?php echo $active_class;?>" data-tab="<?php echo $tab_slug;?>">
                <?php 
                switch($tab_slug){
                    case 'manage-booking':
                        include 'view-modal-manage-booking.php';
                        break;

                    case 'booking-details':
                        include 'view-modal-booking-details.php';
                        break;

                    default:
                        /**
                         * Action to dynamically add content for each tab
                         *
                         */
                        do_action( 'wpbs_booking_modal_tab_' . $tab_slug, $this->booking, $this->calendar);
                    }
                 ?>
            </div>
        <?php endforeach ?>
    </div>
</div>