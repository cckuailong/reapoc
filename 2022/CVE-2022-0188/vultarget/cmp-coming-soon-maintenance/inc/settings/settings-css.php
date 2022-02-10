<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>

<div class="table-wrapper-css custom_css">
    <h3><?php _e('Enter Custom CSS', 'cmp-coming-soon-maintenance');?></h3>

    <textarea name="niteoCS_custom_css" rows="20" id="niteoCS_custom_css" class="code"><?php echo esc_attr( $niteoCS_custom_css ); ?></textarea>

    <?php echo $this->render_settings->submit(); ?>
    
</div>