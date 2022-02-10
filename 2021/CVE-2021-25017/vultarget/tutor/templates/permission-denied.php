<?php
/**
 * Display Permission denied 
 *
 * @since v.1.0.0
 * @author themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 * 
 * Template content and design updated 
 * 
 * @version 1.9.6
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

get_header();

?>

<div class="tutor-denied-wrapper">

    <div class="image-wrapper">
        <img src="<?php echo esc_url( tutor()->url.'assets/images/denied.png' )?>" alt="denied">
    </div>

    <div class="tutor-denied-content-wrapper">

        <div>
            <img src="<?php echo esc_url( tutor()->url.'assets/images/tutor-logo.png' );?>" alt="tutor-logo">
        </div>
        
						
        <div>
            <h2>
                <?php echo isset($headline) ? $headline : __( 'Permission Denied', 'tutor' ); ?>
            </h2>
            <p>
                <?php echo isset($message) ? $message : __( 'You don\'t have enough privilege to access this page', 'tutor' ); ?>
            </p>
            <p> 
                <?php echo isset($description) ? $description : __('Please make sure you are logged in to correct account if the content needs authorization.', 'tutor'); ?>
            </p>
        </div>
        
        <div>
            <?php 
                if(!isset($button)) {
                    $button = array(
                        'url' => get_home_url(),
                        'text' => 'Homepage'
                    );
                }
            ?>
            <a href="<?php echo $button['url']; ?>" class="tutor-button">
                <?php echo $button['text']; ?>
            </a>
        </div>

    </div>

</div>

<?php
get_footer();
