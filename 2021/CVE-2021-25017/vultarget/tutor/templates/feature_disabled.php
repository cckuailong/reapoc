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
  
?>


<div class="tutor-disabled-wrapper">

    <div class="tutor-disabled-content-wrapper">
        <?php if ( isset( $image_path ) &&  $image_path !== ''): ?>
            <div>
                <center>
                    <img src="<?php echo esc_url( $image_path );?>" alt="disabled">
                </center>
            </div>
        <?php endif;?>
                        
        <div>
            <?php if( isset( $title ) && $title !== '' ): ?>
                <h3>
                    <?php echo $title; ?>
                </h3>
            <?php endif;?>

            <?php if( isset( $description ) && $description !== '' ): ?>
                <p>
                    <?php echo $description; ?>
                </p>
            <?php endif;?>
        </div>
        
        <div>
            <?php if ( isset( $button ) && count( $button) ): ?>
                <a href="<?php echo esc_url( $button['url'] );?>" class="<?php esc_attr_e( $button['class'] );?>">
                    <?php echo $button['text']; ?>
                </a>
            <?php endif;?>
        </div>

    </div>

</div>


