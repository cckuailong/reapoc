<?php

/**
 * Single next previous pagination
 *
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.7
 */

?>

<div class="tutor-next-previous-pagination-wrap">
	<?php
	if ($previous_id){
		?>
		<a class="tutor-previous-link tutor-previous-link-<?php echo $previous_id; ?>" href="<?php echo get_the_permalink($previous_id);
		?>">&leftarrow; <?php _e('Previous') ?></a>
		<?php
	}

	if ($next_id){
		?>
		<a class="tutor-next-link tutor-next-link-<?php echo $next_id; ?>" href="<?php echo get_the_permalink($next_id); ?>"><?php _e('Next') ?>
			&rightarrow;
		</a>
		<?php
	}
	?>
</div>
