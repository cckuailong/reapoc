<?php

function ajax_site_url_carseller() {
  if ( wp_script_is( 'jquery', 'done' ) ) {
?>
<script type="text/javascript">
var ajaxurl='<?php echo admin_url()?>admin-ajax.php';
var pluginurl='<?php echo plugins_url('cars-seller-auto-classifieds-script')?>';
</script>
<?php
  }
}
add_action( 'wp_footer', 'ajax_site_url_carseller' );