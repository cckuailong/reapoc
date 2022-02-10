<?php
$quiz = null;
if (!empty($_POST['tutor_quiz_builder_quiz_id'])) {
    $quiz_id = sanitize_text_field($_POST['tutor_quiz_builder_quiz_id']);
    $quiz = get_post($quiz_id);

    echo '<input type="hidden"  id="tutor_quiz_builder_quiz_id" value="' . $quiz_id . '" />';
} elseif (!empty($quiz_id)) {
    $quiz = get_post($quiz_id);

    echo '<input type="hidden" id="tutor_quiz_builder_quiz_id" value="' . $quiz_id . '" />';
}

if (!$quiz) {
    die('No quiz found');
}

if ( $topic_id != '' ) {
    echo '<input type="hidden" id="current_topic_id_for_quiz" value="'.sanitize_text_field( $_POST['topic_id'] ).'" />';
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
        <a href="#quiz-builder-tab-advanced-options" class="advanced-options-tab-item tutor-quiz-modal-tab-item">
            <i class="tutor-icon-filter-tool-black-shape"></i> <?php _e('Advanced Options', 'tutor'); ?>
        </a>

    </div>

    <div id="tutor-quiz-builder-modal-tabs-container" class="tutor-quiz-builder-modal-tabs-container">
        <div id="quiz-builder-tab-quiz-info" class="quiz-builder-tab-container">
            <div class="quiz-builder-tab-body">
                <div class="tutor-quiz-builder-group">
                    <div class="tutor-quiz-builder-row">
                        <div class="tutor-quiz-builder-col">
                            <input type="text" name="quiz_title" placeholder="<?php _e('Type your quiz title here', 'tutor'); ?>" value="<?php echo htmlspecialchars( stripslashes($quiz->post_title) ); ?>">
                        </div>
                    </div>
                    <p class="warning quiz_form_msg"></p>
                </div>
                <div class="tutor-quiz-builder-group">
                    <div class="tutor-quiz-builder-row">
                        <div class="tutor-quiz-builder-col">
                            <textarea name="quiz_description" rows="5"><?php echo stripslashes($quiz->post_content); ?></textarea>
                        </div>
                    </div>
                </div>

                <?php do_action('tutor_quiz_edit_modal_info_tab_after', $quiz) ?>

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
                <div class="quiz-builder-questions-wrap">

                    <?php
                    $questions = tutor_utils()->get_questions_by_quiz($quiz_id);

                    if ($questions) {
                        foreach ($questions as $question) {
                    ?>
                            <div class="quiz-builder-question-wrap" data-question-id="<?php echo $question->question_id; ?>">
                                <div class="quiz-builder-question">
                                    <span class="question-sorting">
                                        <i class="tutor-icon-move"></i>
                                    </span>

                                    <span class="question-title"><?php echo stripslashes($question->question_title); ?></span>

                                    <span class="question-icon">
                                        <?php
                                        $type = tutor_utils()->get_question_types($question->question_type);
                                        echo $type['icon'] . ' ' . $type['name'];
                                        ?>
                                    </span>

                                    <span class="question-edit-icon">
                                        <a href="javascript:;" class="tutor-quiz-open-question-form" data-question-id="<?php echo $question->question_id; ?>"><i class="tutor-icon-pencil"></i> </a>
                                    </span>
                                </div>

                                <div class="quiz-builder-qustion-trash">
                                    <a href="javascript:;" class="tutor-quiz-question-trash" data-question-id="<?php echo $question->question_id; ?>"><i class="tutor-icon-garbage"></i> </a>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>

                <div class="tutor-quiz-builder-form-row">
                    <a href="javascript:;" class="tutor-quiz-add-question-btn tutor-quiz-open-question-form">
                        <i class="tutor-icon-add-line"></i>
                        <?php _e('Add Question', 'tutor'); ?>
                    </a>
                </div>

            </div>

            <div class="tutor-quiz-builder-modal-control-btn-group">
                <div class="quiz-builder-btn-group-left">
                    <a href="#quiz-builder-tab-quiz-info" class="quiz-modal-tab-navigation-btn quiz-modal-btn-back"><?php _e('Back', 'tutor'); ?></a>
                    <a href="#quiz-builder-tab-settings" class="quiz-modal-tab-navigation-btn quiz-modal-btn-next"><?php _e('Next', 'tutor'); ?></a>
                </div>
                <div class="quiz-builder-btn-group-right">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-cancel"><?php _e('Cancel', 'tutor'); ?></a>
                </div>
            </div>

        </div>

        <div id="quiz-builder-tab-settings" class="quiz-builder-tab-container" style="display: none;">
            <div class="quiz-builder-tab-body">

                <div class="quiz-builder-modal-settins">
                    <div class="tutor-quiz-builder-group">
                        <h4> <?php _e('Time Limit', 'tutor'); ?> </h4>
                        <div class="tutor-quiz-builder-row">
                            <div class="tutor-quiz-builder-col auto-width">
                                <input type="text" name="quiz_option[time_limit][time_value]" value="<?php echo tutor_utils()->get_quiz_option($quiz_id, 'time_limit.time_value', 0) ?>">
                            </div>
                            <div class="tutor-quiz-builder-col auto-width">
                                <?php $limit_time_type = tutor_utils()->get_quiz_option($quiz_id, 'time_limit.time_type', 'minutes') ?>
                                <select name="quiz_option[time_limit][time_type]">
                                    <option value="seconds" <?php selected('seconds', $limit_time_type); ?>><?php _e('Seconds', 'tutor'); ?></option>
                                    <option value="minutes" <?php selected('minutes', $limit_time_type); ?>><?php _e('Minutes', 'tutor'); ?></option>
                                    <option value="hours" <?php selected('hours', $limit_time_type); ?>><?php _e('Hours', 'tutor'); ?></option>
                                    <option value="days" <?php selected('days', $limit_time_type); ?>><?php _e('Days', 'tutor'); ?></option>
                                    <option value="weeks" <?php selected('weeks', $limit_time_type); ?>><?php _e('Weeks', 'tutor'); ?></option>
                                </select>
                            </div>
                            <div class="tutor-quiz-builder-col auto-width">
                                <label class="btn-switch">
                                    <input type="checkbox" value="1" name="quiz_option[hide_quiz_time_display]" <?php checked('1', tutor_utils()->get_quiz_option($quiz_id, 'hide_quiz_time_display')); ?> />
                                    <div class="btn-slider btn-round"></div>
                                </label>
                                <span><?php _e('Hide quiz time - display', 'tutor'); ?></span>
                            </div>
                        </div>
                        <p class="help"><?php _e('Time limit for this quiz. 0 means no time limit.', 'tutor'); ?></p>
                    </div> <!-- .tutor-quiz-builder-group -->

                    <div class="tutor-quiz-builder-group">
                        <h4><?php _e('Quiz Feedback Mode', 'tutor'); ?> </h4>

                        <p class="help">(<?php _e('Pick the quiz system"s behaviour on choice based questions', 'tutor'); ?>)</p>

                        <div class="tutor-quiz-builder-row">
                            <div class="tutor-quiz-builder-col">
                                <label class="tutor-quiz-feedback-mode">
                                    <input type="radio" name="quiz_option[feedback_mode]" value="default" <?php checked('default', tutor_utils()->get_quiz_option($quiz_id, 'feedback_mode')); ?>>
                                    <span class="radio-icon"></span>
                                    <div class="tutor-quiz-feedback-mode-option">
                                        <h4 class="tutor-quiz-feedback-option-option-title"><?php _e('Default', 'tutor'); ?></h4>
                                        <p class="tutor-quiz-feedback-option-subtitle"><?php _e('Answers shown after quiz is finished', 'tutor'); ?></p>
                                    </div>
                                </label>
                            </div>
                            <div class="tutor-quiz-builder-col">
                                <label class="tutor-quiz-feedback-mode">
                                    <input type="radio" name="quiz_option[feedback_mode]" value="retry" <?php checked('retry', tutor_utils()->get_quiz_option($quiz_id, 'feedback_mode')); ?>>
                                    <span class="radio-icon"></span>
                                    <div class="tutor-quiz-feedback-mode-option">
                                        <h4 class="tutor-quiz-feedback-option-option-title"><?php _e('Retry Mode', 'tutor'); ?></h4>
                                        <p class="tutor-quiz-feedback-option-subtitle"><?php _e('Unlimited attempts on each question.', 'tutor'); ?></p>
                                    </div>
                                </label>
                            </div>
                            <div class="tutor-quiz-builder-col">
                                <label class="tutor-quiz-feedback-mode">
                                    <input type="radio" name="quiz_option[feedback_mode]" value="reveal" <?php checked('reveal', tutor_utils()->get_quiz_option($quiz_id, 'feedback_mode')); ?>>
                                    <span class="radio-icon"></span>
                                    <div class="tutor-quiz-feedback-mode-option">
                                        <h4 class="tutor-quiz-feedback-option-option-title"><?php _e('Reveal Mode', 'tutor'); ?></h4>
                                        <p class="tutor-quiz-feedback-option-subtitle"><?php _e('Show result after the attempt.', 'tutor'); ?></p>
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div> <!-- .tutor-quiz-builder-group -->

                    <div class="tutor-quiz-builder-group">
                        <h4><?php _e('Attempts Allowed', 'tutor'); ?> <span>(<?php _e('Optional', 'tutor'); ?>)</span></h4>
                        <div class="tutor-quiz-builder-row">
                            <div class="tutor-quiz-builder-col">
                                <?php
                                $default_attempts_allowed = tutor_utils()->get_option('quiz_attempts_allowed');
                                $attempts_allowed = (int) tutor_utils()->get_quiz_option($quiz_id, 'attempts_allowed', $default_attempts_allowed);
                                ?>

                                <div class="tutor-field-type-slider" data-min="0" data-max="20">
                                    <p class="tutor-field-type-slider-value"><?php echo $attempts_allowed; ?></p>
                                    <div class="tutor-field-slider"></div>
                                    <input type="hidden" value="<?php echo $attempts_allowed; ?>" name="quiz_option[attempts_allowed]" />
                                </div>
                            </div>
                        </div>
                        <p class="help"><?php _e('Restriction on the number of attempts a student is allowed to take for this quiz. 0 for no limit', 'tutor'); ?></p>
                    </div> <!-- .tutor-quiz-builder-group -->

                    <div class="tutor-quiz-builder-group">
                        <h4><?php _e('Passing Grade (%)', 'tutor'); ?></h4>
                        <div class="tutor-quiz-builder-row">
                            <div class="tutor-quiz-builder-col">
                                <input type="number" name="quiz_option[passing_grade]" value="<?php echo tutor_utils()->get_quiz_option($quiz_id, 'passing_grade', 80) ?>" size="10">
                            </div>
                        </div>
                        <p class="help"><?php _e('Set the passing percentage for this quiz', 'tutor'); ?></p>
                    </div> <!-- .tutor-quiz-builder-group -->

                    <div class="tutor-quiz-builder-group">
                        <h4><?php _e('Max questions allowed to answer', 'tutor'); ?></h4>
                        <div class="tutor-quiz-builder-row">
                            <div class="tutor-quiz-builder-col">
                                <input type="number" name="quiz_option[max_questions_for_answer]" value="<?php echo tutor_utils()->get_quiz_option($quiz_id, 'max_questions_for_answer', 10) ?>">
                            </div>
                        </div>
                        <p class="help"><?php _e('This amount of question will be available for students to answer, and question will comes randomly from all available questions belongs with a quiz, if this amount greater than available question, then all questions will be available for a student to answer.', 'tutor'); ?></p>
                    </div> <!-- .tutor-quiz-builder-group -->

                    <?php do_action('tutor_quiz_edit_modal_settings_tab_after', $quiz) ?>

                </div>
            </div>

            <div class="tutor-quiz-builder-modal-control-btn-group">
                <div class="quiz-builder-btn-group-left">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-back"><?php _e('Back', 'tutor'); ?></a>
                    <a href="#quiz-builder-tab-advanced-options" class="quiz-modal-tab-navigation-btn quiz-modal-settings-save-btn" data-toast_success_message="<?php _e('Saved', 'tutor'); ?>"><?php _e('Save', 'tutor'); ?></a>
                </div>
                <!--<div class="quiz-builder-btn-group-right">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-cancel"><?php /*_e('Cancel', 'tutor'); */ ?></a>
                </div>-->
            </div>
        </div>

        <div id="quiz-builder-tab-advanced-options" class="quiz-builder-tab-container" style="display: none;">

            <div class="tutor-quiz-builder-group">
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col auto-width">
                        <label class="btn-switch">
                            <input type="checkbox" value="1" name="quiz_option[quiz_auto_start]" <?php checked('1', tutor_utils()->get_quiz_option($quiz_id, 'quiz_auto_start')); ?> />
                            <div class="btn-slider btn-round"></div>
                        </label>
                        <span><?php _e('Quiz Auto Start', 'tutor'); ?></span>
                    </div>
                </div>
                <p class="help"><?php _e('If you enable this option, the quiz will start automatically after the page is loaded.', 'tutor'); ?></p>
            </div>

            <div class="tutor-quiz-builder-group">
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col auto-width">
                        <h4><?php _e('Question Layout', 'tutor'); ?></h4>

                        <select name="quiz_option[question_layout_view]">
                            <option value=""><?php _e('Set question layout view', 'tutor'); ?></option>
                            <option value="single_question" <?php selected('single_question', tutor_utils()->get_quiz_option($quiz_id, 'question_layout_view')); ?>> <?php _e('Single Question', 'tutor'); ?> </option>
                            <option value="question_pagination" <?php selected('question_pagination', tutor_utils()->get_quiz_option($quiz_id, 'question_layout_view')); ?>> <?php _e('Question Pagination', 'tutor'); ?> </option>
                            <option value="question_below_each_other" <?php selected('question_below_each_other', tutor_utils()->get_quiz_option($quiz_id, 'question_layout_view')); ?>> <?php _e('Question below each other', 'tutor'); ?> </option>
                        </select>
                    </div>

                    <div class="tutor-quiz-builder-col auto-width">
                        <h4><?php _e('Questions Order', 'tutor'); ?></h4>

                        <select name="quiz_option[questions_order]">
                            <option value="rand" <?php selected('rand', tutils()->get_quiz_option($quiz_id, 'questions_order')); ?>> <?php _e('Random', 'tutor'); ?> </option>
                            <option value="sorting" <?php selected('sorting', tutils()->get_quiz_option($quiz_id, 'questions_order')); ?>> <?php _e('Sorting', 'tutor'); ?> </option>

                            <option value="asc" <?php selected('asc', tutils()->get_quiz_option($quiz_id, 'questions_order')); ?>> <?php _e('Ascending', 'tutor'); ?> </option>
                            <option value="desc" <?php selected('desc', tutils()->get_quiz_option($quiz_id, 'questions_order')); ?>> <?php _e('Descending', 'tutor'); ?> </option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="tutor-quiz-builder-group">
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col auto-width">
                        <label class="btn-switch">
                            <input type="checkbox" value="1" name="quiz_option[hide_question_number_overview]" <?php checked('1', tutor_utils()->get_quiz_option($quiz_id, 'hide_question_number_overview')); ?> />
                            <div class="btn-slider btn-round"></div>
                        </label>
                        <span><?php _e('Hide question number', 'tutor'); ?></span>
                    </div>
                </div>
                <p class="help"><?php _e('Show/hide question number during attempt.', 'tutor'); ?></p>
            </div>

            <div class="tutor-quiz-builder-group">
                <h4><?php _e('Short answer characters limit', 'tutor'); ?></h4>
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col">
                        <input type="number" name="quiz_option[short_answer_characters_limit]" value="<?php echo tutor_utils()->get_quiz_option($quiz_id, 'short_answer_characters_limit', 200); ?>">
                    </div>
                </div>
                <p class="help"><?php _e('Student will place answer in short answer question type within this characters limit.', 'tutor'); ?></p>
            </div>

            <div class="tutor-quiz-builder-group">
                <h4><?php _e('Open-Ended/Essay questions answer character limit', 'tutor'); ?></h4>
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col">
                        <input type="number" name="quiz_option[open_ended_answer_characters_limit]" value="<?php echo tutor_utils()->get_quiz_option($quiz_id, 'open_ended_answer_characters_limit', 500); ?>">
                    </div>
                </div>
                <p class="help"><?php _e('Students will place the answer in the Open-Ended/Essay question type within this character limit.', 'tutor'); ?></p>
            </div>

            <div class="tutor-quiz-builder-modal-control-btn-group">
                <div class="quiz-builder-btn-group-left">
                    <a href="#quiz-builder-tab-settings" class="quiz-modal-tab-navigation-btn quiz-modal-btn-back"><?php _e('Back', 'tutor'); ?></a>
                    <a href="#quiz-builder-tab-advanced-options" class="quiz-modal-tab-navigation-btn quiz-modal-settings-save-btn" data-toast_success_message="<?php _e('Saved', 'tutor'); ?>"><?php _e('Save', 'tutor'); ?></a>
                </div>
                <!--<div class="quiz-builder-btn-group-right">
                    <a href="#quiz-builder-tab-questions" class="quiz-modal-tab-navigation-btn quiz-modal-btn-cancel"><?php /*_e('Cancel', 'tutor'); */ ?></a>
                </div>-->
            </div>
        </div>

    </div>

    <div class="tutor-quiz-builder-modal-tabs-notice">
        <?php
        // TODO: These links are must be updated
        $knowledge_base_link = sprintf("<a href='%s' target='_blank'>%s</a>", "https://docs.themeum.com/tutor-lms/", __("Knowledge Base", "tutor"));

        $documentation_link = sprintf("<a href='%s' target='_blank'>%s</a>", "https://docs.themeum.com/tutor-lms/", __("Documentation", "tutor"));
        printf(__("Need any Help? Please visit our %s and %s.", "tutor"), $knowledge_base_link, $documentation_link);
        ?>
    </div>

</div>