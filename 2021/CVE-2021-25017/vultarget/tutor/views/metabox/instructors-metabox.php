<div class="tutor-course-instructors-metabox-wrap">
	<?php
	$instructors = tutor_utils()->get_instructors_by_course();
	?>

    <div class="tutor-course-available-instructors">
		<?php
        global $post;

        $instructor_crown_src = tutor()->url.'assets/images/crown.svg';
		if (is_array($instructors) && count($instructors)){
			foreach ($instructors as $instructor){
                $authorTag = '';
				if ($post->post_author == $instructor->ID){
					$authorTag = '<img src="'.$instructor_crown_src.'"><i class="instructor-name-tooltip" title="'. __("Author", "tutor") .'">'. __("Author", "tutor") .'</i>';
				}
				?>
                <div id="added-instructor-id-<?php echo $instructor->ID; ?>" class="added-instructor-item added-instructor-item-<?php echo $instructor->ID; ?>" data-instructor-id="<?php echo $instructor->ID; ?>">
					<span class="instructor-icon">
                        <?php echo get_avatar($instructor->ID, 30); ?>
                    </span>
                    <span class="instructor-name"> <?php echo $instructor->display_name.' '.$authorTag; ?> </span>
                    <span class="instructor-control">
						<a href="javascript:;" class="tutor-instructor-delete-btn"><i class="tutor-icon-line-cross"></i></a>
					</span>
                </div>
				<?php
			}
		}
		?>
    </div>

    <div class="tutor-add-instructor-button-wrap">
        <button type="button" class="tutor-btn tutor-add-instructor-btn bordered-btn"> <i class="tutor-icon-add-friend"></i> <?php _e('Add More Instructors', 'tutor'); ?> </button>
    </div>

	<?php
	/* if ( ! defined('TUTOR_MT_VERSION')){
		 echo '<p>'. sprintf( __('To add unlimited multiple instructors in your course, get %sTutor LMS Pro%s addon ', 'tutor'), '<a href="https://www.themeum.com/product/tutor-lms" target="_blank">', "</a>" ) .'</p>';
	 }*/
	?>
</div>


<div class="tutor-modal-wrap tutor-instructors-modal-wrap">
    <div class="tutor-modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <h1><?php _e("Add instructors", "tutor") ?></h1>
            </div>
            <div class="lesson-modal-close-wrap">
                <a href="javascript:;" class="modal-close-btn"><i class="tutor-icon-line-cross"></i></a>
            </div>
        </div>
        <div class="modal-content-body">

            <div class="search-bar">
                <input type="text" class="tutor-modal-search-input" placeholder="<?php _e( 'Search instructors...', 'tutor' ); ?>">
            </div>
        </div>
        <div class="modal-container"></div>
        <div class="modal-footer has-padding">
            <button type="button" class="tutor-btn add_instructor_to_course_btn"><?php _e('Add Instructors', 'tutor'); ?></button>
        </div>
    </div>
</div>