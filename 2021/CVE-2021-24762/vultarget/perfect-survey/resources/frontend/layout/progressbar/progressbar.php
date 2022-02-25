<div class="ps_progressbar_generalcontainer">
    <?php
    $survey_width = 100 / $ps_post->total_questions;
    $current_with = $ps_post->current_question_number * $survey_width;
    ?>
    <div class="ps_width_curret" style="width: <?php echo $current_with . '%'; ?>"></div>
</div>
