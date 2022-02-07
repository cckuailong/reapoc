<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<div class="wrap dlm-order-overview">

	<?php
	$table = new \Never5\DownloadMonitor\Shop\Admin\OrderTable();
	$table->prepare_items();
	?>

    <div id="icon-edit" class="icon32 icon32-posts-dlm_download"><br/></div>

    <h1><?php _e( 'Orders', 'download-monitor' ); ?></h1>

    <form method="post">
		<?php $table->display() ?>
    </form>

</div>