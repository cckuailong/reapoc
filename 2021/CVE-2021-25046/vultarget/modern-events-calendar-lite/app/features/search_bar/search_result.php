 <?php
/** no direct access **/
defined('MECEXEC') or die();

$settings = $this->main->get_settings();
$start_hour = get_post_meta(get_the_ID(), 'mec_start_time_hour', true);
$start_min = (get_post_meta(get_the_ID(), 'mec_start_time_minutes', true) < '10') ? '0' . get_post_meta(get_the_ID(), 'mec_start_time_minutes', true) : get_post_meta(get_the_ID(), 'mec_start_time_minutes', true);
$start_ampm = get_post_meta(get_the_ID(), 'mec_start_time_ampm', true);
$end_hour = get_post_meta(get_the_ID(), 'mec_end_time_hour', true);
$end_min = (get_post_meta(get_the_ID(), 'mec_end_time_minutes', true) < '10') ? '0' . get_post_meta(get_the_ID(), 'mec_end_time_minutes', true) : get_post_meta(get_the_ID(), 'mec_end_time_minutes', true);
$end_ampm = get_post_meta(get_the_ID(), 'mec_end_time_ampm', true);
$time = (get_post_meta(get_the_ID(), 'mec_allday', true) == '1' ) ? $this->main->m('all_day', __('All Day' , 'modern-events-calendar-lite')) : $start_hour . ':' .  $start_min . ' ' . $start_ampm . ' - ' . $end_hour . ':' .  $end_min . ' ' . $end_ampm;
?>
<article class="mec-search-bar-result">
	<div class="mec-event-list-search-bar-date mec-color">
		<span class="mec-date-day"><?php echo $this->main->date_i18n('d', strtotime(get_post_meta(get_the_ID(), 'mec_start_date', true))); ?></span><?php echo $this->main->date_i18n('F', strtotime(get_post_meta(get_the_ID(), 'mec_start_date', true))); ?>
	</div>
	<div class="mec-event-image">
 		<a href="<?php the_permalink(); ?>" target="_blank"><?php the_post_thumbnail('thumbnail'); ?></a>
	</div>
	<div class="mec-event-time mec-color">
 		<i class="mec-sl-clock-o"></i><?php echo $time; ?>
	</div>
	<h4 class="mec-event-title">
 		<a class="mec-color-hover" href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a>
	</h4>
	<div class="mec-event-detail">
		<?php
            $id = get_post_meta(get_the_ID(), 'mec_location_id', true);
            $term = get_term($id, 'mec_location');
            echo $term->name;
		?>
	</div>
</article>