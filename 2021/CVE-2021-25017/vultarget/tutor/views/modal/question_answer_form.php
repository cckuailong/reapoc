<?php
if ($question_type === 'open_ended' || $question_type === 'short_answer'){
	echo '<p class="open-ended-notice" style="color: #ff0000;">'.__('No option is necessary for this answer type', 'tutor').'</p>';
	return '';
}


?>

<div class="tutor-quiz-question-answers-form">

	<?php
	if ($question_type === 'true_false'){
		?>
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Select the correct option', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col auto-width">
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][true_false]" value="true" checked="checked">
                        <?php _e('True', 'tutor'); ?>
                    </label>
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][true_false]" value="false">
                        <?php _e('False', 'tutor'); ?>
                    </label>
                </div>
            </div>
        </div>

		<?php
	}elseif($question_type === 'multiple_choice' || $question_type === 'single_choice' || $question_type === 'ordering' ){
		?>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Answer title', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <div class="quiz-modal-field-wrap">
                        <input type="text" name="quiz_answer[<?php echo $question_id; ?>][answer_title]" value="">
                    </div>
                </div>
            </div>
        </div>


        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Upload Image', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <div class="tutor-media-upload-wrap">
                        <input type="hidden" name="quiz_answer[<?php echo $question_id; ?>][image_id]" value="">
                        <div class="tutor-media-preview">
                            <a href="javascript:;" class="tutor-media-upload-btn"><i class="tutor-icon-image1"></i></a>
                        </div>
                        <div class="tutor-media-upload-trash-wrap">
                            <a href="javascript:;" class="tutor-media-upload-trash">&times;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Display format for options', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col auto-width">
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][answer_view_format]" value="text" checked="checked">
                        <?php _e('Only text', 'tutor'); ?>
                    </label>
                </div>
                <div class="tutor-quiz-builder-col auto-width">
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][answer_view_format]" value="image">
                        <?php _e('Only Image', 'tutor'); ?>
                    </label>
                </div>
                <div class="tutor-quiz-builder-col auto-width">
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][answer_view_format]" value="text_image">
                        <?php _e('Text &amp; Image both', 'tutor'); ?>
                    </label>
                </div>
            </div>
        </div>

		<?php
	}elseif($question_type === 'fill_in_the_blank'){ ?>
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Question Title', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][answer_title]" value="">
                </div>
            </div>
            <p class="help"><?php _e( 'Please make sure to use the <strong>{dash}</strong> variable in your question title to show the blanks in your question. You can use multiple <strong>{dash}</strong> variables in one question.', 'tutor' ); ?></p>
        </div> <!-- /.tutor-quiz-builder-group -->

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Correct Answer(s)', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][answer_two_gap_match]" value="">
                </div>
            </div>
            <p class="help"><?php _e( 'Separate multiple answers by a vertical bar <strong>|</strong>. 1 answer per <strong>{dash}</strong> variable is defined in the question. Example: Apple | Banana | Orange', 'tutor' ); ?></p>
        </div> <!-- /.tutor-quiz-builder-group -->


		<?php
	}elseif ($question_type === 'answer_sorting'){ ?>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Answer title', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][answer_title]" value="">
                </div>
            </div>
        </div> <!-- /.tutor-quiz-builder-group -->

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Matched Answer title', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][matched_answer_title]" value="">
                </div>
            </div>
            <p class="help"></p>
        </div> <!-- /.tutor-quiz-builder-group -->

		<?php
	}elseif($question_type === 'matching'){
		?>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Answer title', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][answer_title]" value="">
                </div>
            </div>
            <p class="help"></p>
        </div> <!-- /.tutor-quiz-builder-group -->
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Matched Answer title', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][matched_answer_title]" value="">
                </div>
            </div>
            <p class="help"></p>
        </div> <!-- /.tutor-quiz-builder-group -->

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Upload Image', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <div class="tutor-media-upload-wrap">
                        <input type="hidden" name="quiz_answer[<?php echo $question_id; ?>][image_id]" value="">
                        <div class="tutor-media-preview">
                            <a href="javascript:;" class="tutor-media-upload-btn"><i class="tutor-icon-image1"></i></a>
                        </div>
                        <div class="tutor-media-upload-trash-wrap">
                            <a href="javascript:;" class="tutor-media-upload-trash">&times;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /.tutor-quiz-builder-group -->
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Display format for options', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col auto-width">
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][answer_view_format]" value="text" checked="checked">
                        <?php _e('Only text', 'tutor'); ?>
                    </label>
                </div>
                <div class="tutor-quiz-builder-col auto-width">
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][answer_view_format]" value="image">
                        <?php _e('Only Image', 'tutor'); ?>
                    </label>
                </div>
                <div class="tutor-quiz-builder-col auto-width">
                    <label>
                        <input type="radio" name="quiz_answer[<?php echo $question_id; ?>][answer_view_format]" value="text_image">
                        <?php _e('Text &amp; Image both', 'tutor'); ?>
                    </label>
                </div>
            </div>
            <p class="help"></p>
        </div> <!-- /.tutor-quiz-builder-group -->

		<?php
	}elseif ($question_type === 'image_matching'){ ?>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Upload Image', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <div class="tutor-media-upload-wrap">
                        <input type="hidden" name="quiz_answer[<?php echo $question_id; ?>][image_id]" value="">
                        <div class="tutor-media-preview">
                            <a href="javascript:;" class="tutor-media-upload-btn"><i class="tutor-icon-image1"></i></a>
                        </div>
                        <div class="tutor-media-upload-trash-wrap">
                            <a href="javascript:;" class="tutor-media-upload-trash">&times;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /.tutor-quiz-builder-group -->
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Image matched text', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][answer_title]" value="">
                </div>
            </div>
        </div> <!-- /.tutor-quiz-builder-group -->


		<?php
	}elseif($question_type === 'image_answering'){ ?>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Upload Image', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <div class="tutor-media-upload-wrap">
                        <input type="hidden" name="quiz_answer[<?php echo $question_id; ?>][image_id]" value="">
                        <div class="tutor-media-preview">
                            <a href="javascript:;" class="tutor-media-upload-btn"><i class="tutor-icon-image1"></i></a>
                        </div>
                        <div class="tutor-media-upload-trash-wrap">
                            <a href="javascript:;" class="tutor-media-upload-trash">&times;</a>
                        </div>
                    </div>
                </div>
            </div>
            <p class="help"></p>
        </div> <!-- /.tutor-quiz-builder-group -->
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Answer input value', 'tutor'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="quiz_answer[<?php echo $question_id; ?>][answer_title]" value="">
                </div>
            </div>
            <p class="help"><?php _e('The answers that students enter should match with this text. Write in <strong>small caps</strong>','tutor'); ?></p>
        </div> <!-- /.tutor-quiz-builder-group -->

		<?php
	}
	?>

    <div class="tutor-quiz-answers-form-footer  tutor-quiz-builder-form-row">
        <button type="button" id="quiz-answer-save-btn" class="tutor-answer-save-btn"><i class="tutor-icon-add-line"></i> <?php _e('Save Answer', 'tutor'); ?></button>

    </div>

</div>