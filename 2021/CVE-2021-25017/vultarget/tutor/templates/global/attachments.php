<?php
/**
 * Display attachments
 *
 * @since v.1.0.0
 * @author themeum
 * @url https://themeum.com
 *
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$attachments = tutor_utils()->get_attachments();
$open_mode_view = apply_filters('tutor_pro_attachment_open_mode', null)=='view' ? ' target="_blank" ' : null;

do_action('tutor_global/before/attachments');

if (is_array($attachments) && count($attachments)){
	?>
    <div class="tutor-page-segment tutor-attachments-wrap">
        <h3><?php _e('Attachments', 'tutor'); ?></h3>
        <?php
        foreach ($attachments as $attachment){
            ?>
            <a href="<?php echo $attachment->url; ?>" <?php echo ($open_mode_view ? $open_mode_view : ' download="'.$attachment->name.'" ' ); ?> class="tutor-lesson-attachment clearfix">
                <div class="tutor-attachment-icon">
                    <i class="tutor-icon-<?php echo $attachment->icon; ?>"></i>
                </div>
                <div class="tutor-attachment-info">
                    <span><?php echo $attachment->name; ?></span>
                    <span><?php echo $attachment->size; ?></span>
                </div>
            </a>
            <?php
        }
        ?>
    </div>
<?php }

do_action('tutor_global/after/attachments'); ?>