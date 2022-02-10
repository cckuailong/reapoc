		<p><?php printf(__( 'Displaying %1$d of %2$d impressions', 'affiliates-manager' ), count($this->viewData['impressions']), $this->viewData['impressionCount']); ?></p>

		<table class="widefat">
			<thead>
			<tr>
				 <th width="25"><?php _e( 'ID', 'affiliates-manager' ) ?></th>
				 <th width="200"><?php _e( 'Date Occurred', 'affiliates-manager' ) ?></th>
				 <th width="100"><?php _e( 'Creative', 'affiliates-manager' ) ?></th>
				 <th><?php _e( 'Referrer', 'affiliates-manager' ) ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			$creativeNames = $this->viewData['creativeNames'];

			foreach ( $this->viewData['impressions'] as $impression ) {
			?>
			<tr class="impression">
				<td><?php echo $impression->impressionId?></td>
				<td><?php echo date("m/d/Y H:i:s", $impression->dateCreated)?></td>
				<td><?php echo $creativeNames[$impression->sourceCreativeId]?></td>
				<td><?php echo $impression->referer?></td>
			</tr>
			<?php } ?>

			</tbody>
		</table>
		<?php
		 if ( ! count( $this->viewData['impressions'] ) ):
		?>
			 <div class="daterange-form"><p><?php _e( 'No records found for the date range selected.', 'affiliates-manager' ) ?></p></div>
		<?php endif; ?>
