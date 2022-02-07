<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var array $item */
?>
<tr>
	<td><?php echo $item['label']; ?></td>
	<td><?php echo $item['subtotal']; ?></td>
</tr>