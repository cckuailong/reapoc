<?php
global $wpdb;
$settings = maybe_unserialize($question->question_settings);
?>

<div class="quiz-questions-form">


    <div class="question-form-header">
        <a href="javascript:;" class="back-to-quiz-questions-btn open-tutor-quiz-modal" data-quiz-id="<?php echo $quiz_id; ?>"
           data-back-to-tab="#quiz-builder-tab-questions"><i class="tutor-icon-next-2"></i> <?php _e('Back', 'tutor'); ?></a>
    </div>


    <div class="quiz-question-form-body">


        <div class="quiz_question_form">

            <div class="tutor-quiz-builder-group">
                <h4><?php _e('Write your question here', 'tutor'); ?></h4>
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col">
                        <input type="text" name="tutor_quiz_question[<?php echo $question_id; ?>][question_title]" placeholder="<?php _e('Type your question here', 'tutor'); ?>" value="<?php echo htmlspecialchars( stripslashes($question->question_title) ); ?>">
                    </div>
                </div>
            </div>

            <div class="tutor-quiz-builder-group">
                <h4><?php _e('Question Type', 'tutor'); ?></h4>
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col">
                        <div class="tutor-select">
                            <div class="select-header">
                                <span class="lead-option"> <i class="tutor-icon-yes-no"></i> <?php _e('True or False', 'tutor'); ?> </span>
                                <span class="select-dropdown"><i class="tutor-icon-light-down"></i> </span>
                                <input type="hidden" class="tutor_select_value_holder" name="tutor_quiz_question[<?php echo $question_id; ?>][question_type]" value="" >
                            </div>

							<?php $question_types = tutor_utils()->get_question_types(); ?>

                            <div class="tutor-select-options" style="display: none;">
								<?php
								$has_tutor_pro = tutor()->has_pro;

								foreach ($question_types as $type => $question_type){
									?>
                                    <p class="tutor-select-option" data-value="<?php echo $type; ?>" <?php echo $question->question_type===$type ? ' data-selected="selected"' : ''; ?> data-is-pro="<?php echo (! $has_tutor_pro &&  $question_type['is_pro']) ? 'true' : 'false' ?>" >
										<?php echo $question_type['icon'].' '.$question_type['name']; ?>

										<?php
										if (! $has_tutor_pro && $question_type['is_pro']){
											$svg_lock = '<svg width="12" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M11.667 6h-1V4.667A4.672 4.672 0 0 0 6 0a4.672 4.672 0 0 0-4.667 4.667V6h-1A.333.333 0 0 0 0 6.333v8.334C0 15.402.598 16 1.333 16h9.334c.735 0 1.333-.598 1.333-1.333V6.333A.333.333 0 0 0 11.667 6zm-4.669 6.963a.334.334 0 0 1-.331.37H5.333a.333.333 0 0 1-.331-.37l.21-1.89A1.319 1.319 0 0 1 4.667 10c0-.735.598-1.333 1.333-1.333S7.333 9.265 7.333 10c0 .431-.204.824-.545 1.072l.21 1.891zM8.667 6H3.333V4.667A2.67 2.67 0 0 1 6 2a2.67 2.67 0 0 1 2.667 2.667V6z" fill="#E2E2E2" fill-rule="nonzero"/></svg>';
											printf("<span class='question-type-pro' title='%s'>%s</span>",__('Pro version required', 'tutor'), $svg_lock );
										}
										?>
                                    </p>
									<?php
								}
								?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tutor-quiz-builder-group">
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col auto-width">
                        <label class="btn-switch">
                            <input type="checkbox" value="1" name="tutor_quiz_question[<?php echo $question_id; ?>][answer_required]" <?php checked('1', tutor_utils()->avalue_dot('answer_required', $settings)); ?> />
                            <div class="btn-slider btn-round"></div>
                        </label>
                        <span><?php _e('Answer Required', 'tutor'); ?></span>
                    </div>
                    <div class="tutor-quiz-builder-col auto-width">
                        <label class="btn-switch">
                            <input type="checkbox" value="1" name="tutor_quiz_question[<?php echo $question_id; ?>][randomize_question]" <?php checked('1', tutor_utils()->avalue_dot('randomize_question', $settings)); ?> />
                            <div class="btn-slider btn-round"></div>
                        </label>
                        <span><?php _e('Randomize', 'tutor'); ?></span>
                    </div>
                </div>
            </div>

            <div class="tutor-quiz-builder-group">
                <h4><?php _e('Point(s) for this answer', 'tutor'); ?></h4>
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col">
                        <input type="text" name="tutor_quiz_question[<?php echo $question_id; ?>][question_mark]" placeholder="<?php _e('set the mark ex. 10', 'tutor'); ?>" value="<?php
						echo $question->question_mark; ?>">
                    </div>
                </div>
            </div>

            <div class="tutor-quiz-builder-group">
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col auto-width">
                        <label class="btn-switch">
                            <input type="checkbox" value="1" name="tutor_quiz_question[<?php echo $question_id; ?>][show_question_mark]" <?php checked('1', tutor_utils()->avalue_dot('show_question_mark', $settings)); ?> />
                            <div class="btn-slider btn-round"></div>
                        </label>
                        <span><?php _e('Display Points', 'tutor'); ?></span>
                    </div>
                </div>
            </div>

            <div class="tutor-quiz-builder-group">
                <h4><?php _e('Description', 'tutor'); ?> <span>(<?php _e('Optional', 'tutor'); ?>)</span></h4>
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col">
                        <textarea name="tutor_quiz_question[<?php echo $question_id; ?>][question_description]"><?php echo stripslashes($question->question_description);?></textarea>
                    </div>
                </div>
            </div>

            <div class="tutor-quiz-builder-group">
                <h4>
					<?php
					switch ($question->question_type){
						case 'true_false':
							echo __('Input options for the question and select the correct answer.', 'tutor');
							break;
						case 'ordering':
							echo __('Make sure you’re saving the answers in the right order. Students will have to match this order.', 'tutor');
							break;
					}
					?>
                </h4>
                <div class="tutor-quiz-builder-row">
                    <div class="tutor-quiz-builder-col">
                        <div id="tuotr_question_options_for_quiz" class="quiz-modal-field-wrap">
                            <div id="tutor_quiz_question_answers" data-question-id="<?php echo $question_id; ?>"><?php

								$answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tutor_quiz_question_answers where belongs_question_id = %d AND belongs_question_type = %s order by answer_order asc ;", $question_id, $question->question_type));
								if (is_array($answers) && count($answers)){
									foreach ($answers as $answer){
										?>
                                        <div class="tutor-quiz-answer-wrap" data-answer-id="<?php echo $answer->answer_id; ?>">
                                            <div class="tutor-quiz-answer">
                                        <span class="tutor-quiz-answer-title">
                                            <?php
                                            echo stripslashes($answer->answer_title);
                                            if ($answer->belongs_question_type === 'fill_in_the_blank'){
	                                            echo ' ('.__('Answer', 'tutor').' : ';
	                                            echo '<strong>'.stripslashes($answer->answer_two_gap_match).'</strong>)';
                                            }
                                            if ($answer->belongs_question_type === 'matching'){
	                                            echo ' - '.stripslashes($answer->answer_two_gap_match);
                                            }
                                            ?>
                                        </span>

												<?php
												if ($answer->image_id){
													echo '<span class="tutor-question-answer-image"><img src="'.wp_get_attachment_image_url($answer->image_id).'" /> </span>';
												}
												if ($question->question_type === 'true_false' || $question->question_type === 'single_choice'){
													?>
                                                    <span class="tutor-quiz-answers-mark-correct-wrap">
                                                <input type="radio" name="mark_as_correct[<?php echo $answer->belongs_question_id; ?>]"
                                                       value="<?php echo $answer->answer_id; ?>" title="<?php _e('Mark as correct', 'tutor'); ?>" <?php checked(1, $answer->is_correct); ?> >
                                            </span>
													<?php
												}elseif ($question->question_type === 'multiple_choice'){
													?>
                                                    <span class="tutor-quiz-answers-mark-correct-wrap">
                                                <input type="checkbox" name="mark_as_correct[<?php echo $answer->belongs_question_id; ?>]"
                                                       value="<?php echo $answer->answer_id; ?>" title="<?php _e('Mark as correct', 'tutor'); ?>" <?php checked(1, $answer->is_correct); ?> >
                                            </span>
													<?php
												}
												?>
                                                <span class="tutor-quiz-answer-edit">
                                                    <?php if ( $question->question_type !== 'true_false' ){ ?>
                                                        <a href="javascript:;"><i class="tutor-icon-pencil"></i> </a>
                                                    <?php } ?>
                                                </span>
                                                <span class="tutor-quiz-answer-sort-icon"><i class="tutor-icon-menu-2"></i> </span>
                                            </div>

                                            <?php if ( $question->question_type !== 'true_false' ){ ?>
                                                <div class="tutor-quiz-answer-trash-wrap">
                                                    <a href="javascript:;" class="answer-trash-btn" data-answer-id="<?php echo $answer->answer_id; ?>"><i class="tutor-icon-garbage"></i> </a>
                                                </div>
                                            <?php } ?>
                                        </div>
										<?php
									}
								}
								?></div>


                            <div id="tutor_quiz_question_answer_form"></div>

                            <a href="javascript:;" class="add_question_answers_option" data-question-id="<?php echo $question_id; ?>">
                                <i class="tutor-icon-block tutor-icon-plus"></i>
								<?php _e('Add An Option', 'tutor'); ?>
                            </a>
                        </div>

                        <div id="quiz_validation_msg_wrap"></div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>


<div class="tutor-quiz-builder-modal-control-btn-group question_form_inner">
    <div class="quiz-builder-btn-group-left">
        <a href="javascript:;" class="quiz-modal-tab-navigation-btn quiz-modal-question-save-btn"><?php _e('Save &amp; Continue', 'tutor'); ?></a>
    </div>
    <div class="quiz-builder-btn-group-right">
        <a href="javascript:;" class="quiz-modal-tab-navigation-btn quiz-modal-btn-cancel"><?php _e('Cancel', 'tutor'); ?></a>
    </div>
</div>