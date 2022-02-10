<tr class="<?php $this->tr_class(); ?>">
	<td>
		<label for="ticket_price"><?php esc_html_e( 'Price:', 'event-tickets' ); ?></label>
	</td>
	<td>
		<input type='text' id='ticket_price' name='ticket_price' class="ticket_field" size='7' value='' />
		<p class="description"><?php esc_html_e( '(0 or empty for free tickets)', 'event-tickets' ) ?></p>
	</td>
</tr>
<tr class="<?php $this->tr_class(); ?> sale_price">
	<td>
		<label for="ticket_sale_price"><?php esc_html_e( 'Sale Price:', 'event-tickets' ) ?></label>
	</td>
	<td>
		<input type='text' id='ticket_sale_price' name='ticket_sale_price' class="ticket_field" size='7' value='' readonly />
		<p class="description"><?php esc_html_e( '(Current sale price - this can be managed via the product editor)', 'event-tickets' ) ?></p>
	</td>
</tr>
