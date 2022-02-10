<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_fes $this */

// The Query
$query = new WP_Query(array('post_type'=>$this->PT, 'author'=>get_current_user_id(), 'posts_per_page'=>'-1', 'post_status'=>array('pending', 'draft', 'future', 'publish')));

// Date Format
$date_format = get_option('date_format');

// Display Date
$display_date = (isset($this->settings['fes_display_date_in_list']) ? (boolean) $this->settings['fes_display_date_in_list'] : false);

// Generating javascript code of countdown module
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery(".mec-fes-event-remove").on("click", function(event)
    {
        var id = jQuery(this).data("id");
        var confirmed = jQuery(this).data("confirmed");
        
        if(confirmed == "0")
        {
            jQuery(this).data("confirmed", "1");
            jQuery(this).addClass("mec-fes-waiting");
            jQuery(this).text("'.esc_attr__('Click again to remove!', 'modern-events-calendar-lite').'");
            
            return false;
        }

        // Add loading class to the row
        jQuery("#mec_fes_event_"+id).addClass("loading");
        
        jQuery.ajax(
        {
            type: "POST",
            url: "'.admin_url('admin-ajax.php', NULL).'",
            data: "action=mec_fes_remove&_wpnonce='.wp_create_nonce('mec_fes_remove').'&post_id="+id,
            dataType: "JSON",
            success: function(response)
            {
                if(response.success == "1")
                {
                    // Remove the row
                    jQuery("#mec_fes_event_"+id).remove();
                }
                else
                {
                    // Remove the loading class from the row
                    jQuery("#mec_fes_event_"+id).removeClass("loading");
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Remove the loading class from the row
                jQuery("#mec_fes_event_"+id).removeClass("loading");
            }
        });
    });
});
</script>';

// Include javascript code into the footer
$this->factory->params('footer', $javascript);
?>
<div class="mec-fes-list">
    <?php if($query->have_posts()): ?>
    <div class="mec-fes-list-top-actions">
        <a href="<?php echo $this->link_add_event(); ?>"><?php echo __('Add new', 'modern-events-calendar-lite'); ?></a>
    </div>
    <?php do_action('mec_fes_list'); ?>
    <ul>
        <?php 
            while($query->have_posts()): $query->the_post();
            // Show Post Status
            global $post;
            $status = $this->main->get_event_label_status(trim($post->post_status));
        ?>
        <li id="mec_fes_event_<?php echo get_the_ID(); ?>">
            <span class="mec-event-title">
                <a href="<?php echo $this->link_edit_event(get_the_ID()); ?>"><?php the_title(); ?></a>
                <?php if($display_date): ?>
                <span>(<?php echo $this->main->date_label(array(
                    'date' => get_post_meta(get_the_ID(), 'mec_start_date', true)
                ), array(
                    'date' => get_post_meta(get_the_ID(), 'mec_end_date', true)
                ), $date_format); ?>)</span>
                <?php endif; ?>
            </span>
            <?php 
                $event_status = get_post_status(get_the_ID());
                if(isset($event_status) and strtolower($event_status) == 'publish'):
            ?>
            <span class="mec-fes-event-export"><a href="#mec-fes-export-wrapper-<?php echo get_the_ID(); ?>" data-lity><div class="wn-p-t-right"><div class="wn-p-t-text-content"><?php echo esc_html__('Download Attendees', 'modern-events-calendar-lite'); ?></div><i></i></div></a></span>
            <?php endif; ?>

            <span class="mec-fes-event-view"><a href="<?php the_permalink(); ?>"><div class="wn-p-t-right"><div class="wn-p-t-text-content"><?php echo esc_html__('View Event', 'modern-events-calendar-lite'); ?></div><i></i></div></a></span>
            <?php if(current_user_can('delete_post', get_the_ID())): ?>
            <span class="mec-fes-event-remove" data-confirmed="0" data-id="<?php echo get_the_ID(); ?>"><div class="wn-p-t-right"><div class="wn-p-t-text-content"><?php echo esc_html__('Remove Event', 'modern-events-calendar-lite'); ?></div><i></i></div></span>
            <?php endif; ?>
            <span class="mec-fes-event-view mec-event-status <?php echo $status['status_class']; ?>"><?php echo $status['label']; ?></span>
            <div class="mec-fes-export-wrapper mec-modal-wrap lity-hide" id="mec-fes-export-wrapper-<?php echo get_the_ID(); ?>" data-event-id="<?php echo get_the_ID(); ?>">
                <div class="mec-fes-btn-date">                    
                    <?php $mec_repeat_info = get_post_meta(get_the_ID(), 'mec_repeat', true); if(isset($mec_repeat_info['status']) and $mec_repeat_info['status']): ?>
                        <ul class="mec-export-list-wrapper">
                            <?php
                            $past_week = strtotime('-7 days', current_time('timestamp', 0));

                            $render = $this->getRender();
                            $dates = $render->dates(get_the_ID(), NULL, 15, date('Y-m-d', $past_week));

                            $book = $this->getBook();
                            foreach($dates as $date)
                            {
                                if(isset($date['start']['date']) and isset($date['end']['date']))
                                {
                                    $attendees_count = 0;

                                    $bookings = $this->main->get_bookings(get_the_ID(), $date['start']['timestamp']);
                                    foreach($bookings as $booking)
                                    {
                                        $attendees_count += $book->get_total_attendees($booking->ID);
                                    }
                            ?>
                                <li class="mec-export-list-item" data-time="<?php echo $date['start']['timestamp']; ?>"><?php echo $this->main->date_label($date['start'], $date['end'], $date_format); ?> <span class="mec-export-badge"><?php echo $attendees_count; ?></span></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="mec-fes-btn-export">
                    <span class="mec-event-export-csv"><?php esc_html_e('CSV', 'modern-events-calendar-lite'); ?></span>
                    <span class="mec-event-export-excel"><?php esc_html_e('MS EXCEL', 'modern-events-calendar-lite'); ?></span>
                </div>
            </div>
        </li>
        <?php endwhile; wp_reset_postdata(); // Restore original Post Data ?>
    </ul>
    <?php else: ?>
    <p><?php echo sprintf(__('No events found! %s', 'modern-events-calendar-lite'), '<a href="'.$this->link_add_event().'">'.__('Add new', 'modern-events-calendar-lite').'</a>'); ?></p>
    <?php endif; ?>
</div>