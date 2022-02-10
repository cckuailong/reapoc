

<?php
if ( ! empty($_POST['tutor_quiz_builder_quiz_id'])){
    $quiz_id = sanitize_text_field($_POST['tutor_quiz_builder_quiz_id']);
    echo '<input id="tutor_quiz_builder_quiz_id" value="'.$quiz_id.'" />';
}
if ( isset($_POST['current_topic_id']) && $_POST['current_topic_id'] !== '' ) {
    echo '<input type="hidden" id="current_topic_id_for_quiz" value="'.sanitize_text_field( $_POST['current_topic_id'] ).'" />';
}
?>

<div class="tutor-quiz-builder-modal-contents">

    <div id="tutor-quiz-modal-tab-items-wrap" class="tutor-quiz-modal-tab-items-wrap">

        <a href="#quiz-builder-tab-quiz-info" class="tutor-quiz-modal-tab-item active">
            <i class="tutor-icon-list"></i> <?php _e('Quiz Info', 'tutor'); ?>
        </a>
        <a href="#quiz-builder-tab-questions" class="tutor-quiz-modal-tab-item">
            <i class="tutor-icon-doubt"></i> <?php _e('Questions', 'tutor'); ?>
        </a>
        <a href="#quiz-builder-tab-settings" class="tutor-quiz-modal-tab-item">
            <i class="tutor-icon-settings-1"></i> <?php _e('Settings', 'tutor'); ?>
        </a>
        <a href="#quiz-builder-tab-advanced-options" class="tutor-quiz-modal-tab-item">
            <i class="tutor-icon-filter-tool-black-shape"></i> <?php _e('Advanced Options', 'tutor'); ?>
        </a>

    </div>



    <div id="tutor-quiz-builder-modal-tabs-container" class="tutor-quiz-builder-modal-tabs-container">
        <div id="quiz-builder-tab-quiz-info" class="quiz-builder-tab-container">
            <div class="quiz-builder-tab-body">
                <div class="tutor-quiz-builder-group">
                    <div class="tutor-quiz-builder-row">
                        <div class="tutor-quiz-builder-col">
                            <input type="text" name="quiz_title" placeholder="<?php _e('Type your quiz title here', 'tutor'); ?>">
                        </div>
                    </div>
                    <p class="warning quiz_form_msg"></p>
                </div>
                <div class="tutor-quiz-builder-group">
                    <div class="tutor-quiz-builder-row">
                        <div class="tutor-quiz-builder-col">
                            <textarea name="quiz_description" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tutor-quiz-builder-modal-control-btn-group">
                <div class="quiz-builder-btn-group-left">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-first-step"><?php _e('Save &amp; Next', 'tutor'); ?></a>
                </div>
                <div class="quiz-builder-btn-group-right">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn  quiz-modal-btn-cancel"><?php _e('Cancel', 'tutor');
						?></a>
                </div>
            </div>
        </div>
        <div id="quiz-builder-tab-questions" class="quiz-builder-tab-container" style="display: none;">
            <div class="quiz-builder-tab-body">
                <h1>Questions</h1>
            </div>
            <div class="tutor-quiz-builder-modal-control-btn-group">
                <div class="quiz-builder-btn-group-left">
                    <a href="#quiz-builder-tab-quiz-info" class="quiz-modal-tab-navigation-btn quiz-modal-btn-back"><?php _e('Back', 'tutor');
						?></a>
                    <a href="#quiz-builder-tab-settings" class="quiz-modal-tab-navigation-btn quiz-modal-btn-next"><?php _e('Next', 'tutor'); ?></a>
                </div>
                <div class="quiz-builder-btn-group-right">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-cancel"><?php _e('Cancel', 'tutor');
						?></a>
                </div>
            </div>
        </div>

        <div id="quiz-builder-tab-settings" class="quiz-builder-tab-container" style="display: none;">
            <div class="quiz-builder-tab-body">
                <h1>Settings</h1>
            </div>
            <div class="tutor-quiz-builder-modal-control-btn-group">
                <div class="quiz-builder-btn-group-left">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-back"><?php _e('Back', 'tutor');
						?></a>
                    <a href="#quiz-builder-tab-advanced-options" class="quiz-modal-tab-navigation-btn quiz-modal-btn-next"><?php _e('Next', 'tutor'); ?></a>
                </div>
                <div class="quiz-builder-btn-group-right">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-cancel"><?php _e('Cancel', 'tutor');
						?></a>
                </div>
            </div>


        </div>

        <div id="quiz-builder-tab-advanced-options" class="quiz-builder-tab-container" style="display: none;">
            <h1>Advanced Options</h1>
        </div>



    </div>
    <div class="tutor-quiz-builder-modal-tabs-notice">
        <?php
            $knowledge_base_link = sprintf("<a href='%s' target='_blank'>%s</a>", "https://docs.themeum.com/tutor-lms/", __("Knowledge Base", "tutor"));

            $documentation_link = sprintf("<a href='%s' target='_blank'>%s</a>", "https://docs.themeum.com/tutor-lms/", __("Documentation", "tutor"));
            printf(__("Need any Help? Please visit our %s and %s.", "tutor"), $knowledge_base_link, $documentation_link);
        ?>
    </div>

</div>