<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */


global $post;
$currentPost = $post;
$quiz_id = get_the_ID();
?>

<div id="tutor-quiz-content" class="tutor-quiz-content tutor-quiz-content-<?php the_ID(); ?>">
	<?php

    do_action('tutor_quiz/content/before', $quiz_id);

    the_content();
    do_action('tutor_quiz/content/after', $quiz_id);
	?>
</div>