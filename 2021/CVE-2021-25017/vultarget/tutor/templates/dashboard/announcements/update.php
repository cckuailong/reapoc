<!--update announcements modal-->
<div class="tutor-modal-wrap tutor-announcements-modal-wrap tutor-accouncement-update-modal">
    <div class="tutor-modal-content tutor-announcement-modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <h1><?php _e('Update Announcement', 'tutor'); ?></h1>
            </div>
            <div class="tutor-announcements-modal-close-wrap">
                <a href="#" class="tutor-announcement-close-btn">
                    <i class="tutor-icon-line-cross"></i>
                </a>
            </div>
        </div>
        <div class="modal-container">
            <form action="" class="tutor-announcements-update-form">
                <?php tutor_nonce_field(); ?>
                <input type="hidden" name="announcement_id" id="announcement_id">
                <div class="tutor-form-group">
                    <label>
                        <?php _e('Select Course', 'tutor'); ?>
                    </label>
                    <select class="ignore-nice-select" name="tutor_announcement_course" id="tutor-announcement-course-id" required>
                        <?php if ($courses) : ?>
                            <?php foreach ($courses as $course) : ?>
                                <option value="<?php echo esc_attr($course->ID) ?>">
                                    <?php echo $course->post_title; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option value=""><?php _e('No course found', 'tutor'); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="tutor-form-group">
                    <label>
                        <?php _e('Announcement Title', 'tutor'); ?>
                    </label>
                    <input type="text" name="tutor_announcement_title" id="tutor-announcement-title" value="" placeholder="<?php _e('Announcement title', 'tutor'); ?>" required>
                </div>
                <div class="tutor-form-group">
                    <label for="tutor_announcement_course">
                        <?php _e('Summary', 'tutor'); ?>
                    </label>
                    <textarea rows="6" type="text" id="tutor-announcement-summary" name="tutor_announcement_summary" value="" placeholder="<?php _e('Summary...', 'tutor'); ?>" required></textarea>
                </div>
                <?php do_action('tutor_announcement_editor/after'); ?>
                <div class="tutor-form-group">
                    <div class="tutor-announcements-update-alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="tutor-btn"><?php _e('Update', 'tutor') ?></button>
                    <button type="button" class="quiz-modal-tab-navigation-btn  quiz-modal-btn-cancel tutor-announcement-close-btn tutor-announcement-cancel-btn "><?php _e('Cancel', 'tutor') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--update announcements modal end-->