<?php
/**
 *
 * The template for displaying default menu bar
 */

defined( 'ABSPATH' ) || exit;

$menu_icon_animation = isset( $data['icon_animation'] ) ? $data['icon_animation'] : '';
$menu_title   = isset( $data['menu_title'] ) ? $data['menu_title'] : '';
$slide_type   = isset( $data['slide_type'] ) ? $data['slide_type'] : '';
$position     = isset( $data['position'] ) ? $data['position'] : '';
$logo_link    = isset( $data['logo_link'] ) ? $data['logo_link'] : '';
$bar_logo     = isset( $data['bar_logo'] ) ? $data['bar_logo'] : '';

?>
<div id="wprmenu_bar" class="wprmenu_bar <?php echo $slide_type . ' '. $position; ?>">
  <div class="hamburger <?php echo $menu_icon_animation; ?>">
    <span class="hamburger-box">
      <span class="hamburger-inner"></span>
    </span>
  </div>
  <div class="menu_title">
  <?php 
  if( $this->option( 'bar_logo' ) == '' 
    && $logo_link !== '' ) : ?>
    <a href="<?php echo $logo_link; ?>">
      <?php echo $menu_title; ?>
    </a>
  <?php else: ?>
    <?php echo $menu_title; ?>
  <?php endif; ?>
  <?php 
    if( $this->option('bar_logo') != '' ) :
      echo '<a href="'.$logo_link.'"><img class="bar_logo" alt="logo" src="'.$this->option('bar_logo').'"/></a>';
    endif; 
  ?>
  </div>
</div>