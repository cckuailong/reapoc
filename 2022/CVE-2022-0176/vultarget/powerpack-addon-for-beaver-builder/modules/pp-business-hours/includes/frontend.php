<div class="pp-business-hours-content clearfix" itemscope itemtype="http://schema.org/LocalBusiness">
	<meta itemprop="name" content="<?php echo get_bloginfo('name'); ?>" />
	<?php $rows = count($settings->business_hours_rows);
	for ($i=0; $i < count($settings->business_hours_rows); $i++) :

		if(!is_object($settings->business_hours_rows[$i])) continue;

		$bhRow = $settings->business_hours_rows[$i];
		$status = '';
		$highlight = '';

		if( $bhRow->status == 'close' ) {
			$status = ' pp-closed';
		}
		if( $bhRow->highlight == 'yes' ) {
			$highlight = ' pp-highlight-row';
		}

		$title = 'short' === $bhRow->day_format ? pp_short_day_format($bhRow->title) . '.' : pp_long_day_format($bhRow->title);

		if ( $bhRow->hours_type == 'range' ) {
			$title = 'short' === $bhRow->day_format ? pp_short_day_format($bhRow->start_day) . '.' : pp_long_day_format($bhRow->start_day);
			$title .= ' - ';
			$title .= 'short' === $bhRow->day_format ? pp_short_day_format($bhRow->end_day) . '.' : pp_long_day_format($bhRow->end_day);
		}

		$opening_hours = '';
		$closing_hours = '';

		?>
		<div itemprop="openingHoursSpecification" itemscope="itemscope" itemtype="https://schema.org/OpeningHoursSpecification" class="pp-bh-row clearfix pp-bh-row-<?php echo $i; ?><?php echo $status; ?><?php echo $highlight; ?>">
			<div class="pp-bh-title">
				<?php if ( $bhRow->hours_type == 'day' ) { ?>
					<link itemprop="dayOfWeek" href="http://schema.org/<?php echo $bhRow->title; ?>" /><?php echo $title; ?>
				<?php } else { ?>
					<?php echo $title; ?>
				<?php } ?>
			</div>
			<div class="pp-bh-timing">
				<?php if( $bhRow->status == 'close' ) {
					echo $bhRow->status_text;
				} else {
					if ( is_object( $bhRow->start_time ) ) {
						$opening_hours = $bhRow->start_time->hours . ':' . $bhRow->start_time->minutes . ' ' . $bhRow->start_time->day_period;
						$closing_hours = $bhRow->end_time->hours . ':' . $bhRow->end_time->minutes . ' ' . $bhRow->end_time->day_period;
					}
					if ( is_array( $bhRow->start_time ) ) {
						$opening_hours = $bhRow->start_time['hours'] . ':' . $bhRow->start_time['minutes'] . '&nbsp;' . $bhRow->start_time['day_period'];
						$closing_hours = $bhRow->end_time['hours'] . ':' . $bhRow->end_time['minutes'] . '&nbsp;' . $bhRow->end_time['day_period'];
					}
					if ( $bhRow->hours_type == 'day' ) {
						echo '<time itemprop="opens" content="'.date("g:i A", strtotime($opening_hours)).'">' . date("g:i A", strtotime($opening_hours)) . '</time>';
						echo ' - ';
						echo '<time itemprop="closes" content="'.date("g:i A", strtotime($closing_hours)).'">' . date("g:i A", strtotime($closing_hours)) . '</time>';
					} else {
						$datetime 	= array();
						$start_day 	= 0;
						$end_day 	= 0;
						
						foreach ( pp_long_day_format() as $day => $label ) {
							if ( $day == $bhRow->start_day ) {
								$start_day = 1;
							}
							if ( ! $start_day ) {
								continue;
							}
							if ( $end_day ) {
								break;
							}
							if ( $day == $bhRow->end_day ) {
								$end_day = 1;
							}
							$datetime[] = substr( $day, 0, 2 );
						}

						$datetime_str = implode(',', $datetime);
						$datetime_str .= ' ';
						$datetime_str .= date("G:i", strtotime($opening_hours));
						$datetime_str .= '-';
						$datetime_str .= date("G:i", strtotime($closing_hours));

						echo '<time itemprop="openingHours" datetime="' . $datetime_str . '">';
						echo date("g:i A", strtotime($opening_hours));
						echo ' - ';
						echo date("g:i A", strtotime($closing_hours));
						echo '</time>';
					}
				} ?>
			</div>
		</div>
		<?php
	endfor; ?>
</div>
