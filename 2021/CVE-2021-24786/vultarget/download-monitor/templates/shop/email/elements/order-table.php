<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<table cellpadding="0" cellspacing="0" border="0" class="dlm-order-table">
    <tbody>
	<?php if ( count( $items ) > 0 ) : ?>
		<?php foreach ( $items as $item ) : ?>
            <tr>
                <th><?php echo $item['key']; ?></th>
                <td><?php echo $item['value']; ?></td>
            </tr>
		<?php endforeach; ?>
	<?php endif; ?>
    </tbody>
</table>