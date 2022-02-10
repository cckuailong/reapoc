<?php
/**
 *
 * The template for displaying custom menu bar
 */

defined( 'ABSPATH' ) || exit;

$slide_type = isset( $data['slide_type'] ) ? $data['slide_type'] : '';
$position = isset( $data['position'] ) ? $data['position'] : '';
$menu_icon_animation = isset( $data['icon_animation'] ) ? $data['icon_animation'] : '';
?>
<div class="wprmenu_bar wpr_custom_menu <?php echo $slide_type . ' ' . $position; ?>">
  <div id="custom_menu_icon" class="hamburger <?php echo $menu_icon_animation; ?>">
    <span class="hamburger-box">
      <span class="hamburger-inner"></span>
    </span>
  </div>
</div>