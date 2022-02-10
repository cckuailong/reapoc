<?php
    $classname = '';
    if(version_compare(get_bloginfo('version'),'5.5', '>=')) {
        $classname = 'has-postbox-header';
        echo '<style> #tutor-course-topics .toggle-indicator:before { margin-top: 0; } </style>';
    }
?>

<div id="tutor-course-content-builder-root">
    <div class="tutor-course-builder-header <?php echo $classname; ?>">
        <a href="javascript:;" class="tutor-expand-all-topic"><?php _e('Expand all', 'tutor'); ?></a> |
        <a href="javascript:;" class="tutor-collapse-all-topic"><?php _e('Collapse all', 'tutor'); ?></a>
    </div>

    <?php $course_id = get_the_ID(); ?>
    <div id="tutor-course-content-wrap">
        <?php
        include  tutor()->path.'views/metabox/course-contents.php';
        ?>
    </div>

    <div class="new-topic-btn-wrap">
        <a href="javascript:;" class="create_new_topic_btn tutor-btn bordered-btn"> <i class="tutor-icon-text-document-add-button-with-plus-sign"></i> <?php _e('Add new topic', 'tutor'); ?></a>
    </div>

    <div class="tutor-metabox-add-topics" style="display: none">
        <h3><?php _e('Add Topic', 'tutor'); ?></h3>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for=""><?php _e('Topic Name', 'tutor'); ?></label>
            </div>
            <div class="tutor-option-field">
                <input type="text" name="topic_title" value="">
                <p class="desc">
                    <?php _e('Topic titles are displayed publicly wherever required. Each topic may contain one or more lessons, quiz and assignments.', 'tutor'); ?>
                </p>
            </div>
        </div>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for=""><?php _e('Topic Summary', 'tutor'); ?></label>
            </div>
            <div class="tutor-option-field">
                <textarea name="topic_summery"></textarea>
                <p class="desc">
                    <?php _e('The idea of a summary is a short text to prepare students for the activities within the topic or week. The text is shown on the course page under the topic name.', 'tutor'); ?>
                </p>
                <?php
                //submit_button(__('Add Topic', 'tutor'), 'primary', 'submit', true, array('id' => 'tutor-add-topic-btn')); ?>
                <input type="hidden" name="tutor_topic_course_ID" value="<?php echo $course_id; ?>">
                <button type="button" class="tutor-btn" id="tutor-add-topic-btn"><?php _e('Add Topic', 'tutor'); ?></button>
            </div>
        </div>
    </div>

    <div class="tutor-modal-wrap tutor-quiz-builder-modal-wrap">
        <div class="tutor-modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h1><?php _e('Quiz', 'tutor'); ?></h1>
                </div>
                <div class="modal-close-wrap">
                    <a href="javascript:;" class="modal-close-btn"><i class="tutor-icon-line-cross"></i> </a>
                </div>
            </div>
            <div class="modal-container"></div>
        </div>
    </div>

    <div class="tutor-modal-wrap tutor-lesson-modal-wrap">
        <div class="tutor-modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h1><?php esc_html_e('Lesson', 'tutor') ?></h1>
                </div>

                <div class="lesson-modal-close-wrap">
                    <a href="javascript:;" class="modal-close-btn"><i class="tutor-icon-line-cross"></i></a>
                </div>
            </div>
            <div class="modal-container"></div>
        </div>
    </div>

    <div class="tutor-modal-wrap tutor-assignment-builder-modal-wrap">
        <div class="tutor-modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h1><?php _e('Assignments', 'tutor'); ?></h1>
                </div>
                <div class="modal-close-wrap">
                    <a href="javascript:;" class="modal-close-btn"><i class="tutor-icon-line-cross"></i> </a>
                </div>
            </div>
            <div class="modal-container"></div>
        </div>
    </div>

    <div class="tutor-modal-wrap tutor-zoom-meeting-modal-wrap">
        <div class="tutor-modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h1><?php _e('Zoom Meeting', 'tutor'); ?></h1>
                </div>
                <div class="modal-close-wrap">
                    <a href="javascript:;" class="modal-close-btn"><i class="tutor-icon-line-cross"></i> </a>
                </div>
            </div>
            <div class="modal-container"></div>
        </div>
    </div>
</div>