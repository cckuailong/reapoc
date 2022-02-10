<?php

/**
 * Display loop thumbnail
 *
 * @since v.1.0.0
 * @author themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

global $post;
?>

<div class="tutor-loop-author">
	<?php
	global $authordata;
	?>
    <p> <a href="<?php echo tutor_utils()->profile_url($authordata->ID); ?>"><?php echo get_the_author(); ?></a> </p>
</div>
