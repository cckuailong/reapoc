<?php
/**
 *
 * The template for displaying search form
 */

defined( 'ABSPATH' ) || exit;

$search_placeholder = $this->option( 'search_box_text' );
$search_placeholder = function_exists( 'pll__' ) ? pll__( $search_placeholder ) : $search_placeholder;
$unique_id = esc_attr( uniqid( 'search-form-' ) );
?>
<form role="search" method="get" class="wpr-search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
  <label for="<?php echo $unique_id; ?>"></label>
  <input type="search" class="wpr-search-field" placeholder="<?php echo $search_placeholder; ?>" value="" name="s" title="Search for:">
  <button type="submit" class="wpr_submit">
    <i class="wpr-icon-search"></i>
  </button>
</form>