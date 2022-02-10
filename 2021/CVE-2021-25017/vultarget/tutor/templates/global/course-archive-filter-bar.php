<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

    $sort_by = '';
    isset($_GET["tutor_course_filter"]) ? $sort_by = $_GET["tutor_course_filter"] : 0;
    isset($_POST["tutor_course_filter"]) ? $sort_by = $_POST["tutor_course_filter"] : 0;
?>


<div class="tutor-course-filter-wrap">
    <div class="tutor-course-archive-results-wrap">
		<?php
        $courseCount = tutor_utils()->get_archive_page_course_count();
        $count_text = $courseCount>1 ? __("%s Courses", "tutor") : __("%s Course", "tutor");
		echo sprintf($count_text, "<strong>{$courseCount}</strong>");
		?>
    </div>

    <div class="tutor-course-archive-filters-wrap">
        <form class="tutor-course-filter-form" method="get">
            <select name="tutor_course_filter">
                <option value="newest_first" <?php selected("newest_first", $sort_by); ?> ><?php _e("Release Date (newest first)", "tutor");
					?></option>
                <option value="oldest_first" <?php selected("oldest_first", $sort_by); ?>><?php _e("Release Date (oldest first)", "tutor"); ?></option>
                <option value="course_title_az" <?php selected("course_title_az", $sort_by); ?>><?php _e("Course Title (a-z)", "tutor"); ?></option>
                <option value="course_title_za" <?php selected("course_title_za", $sort_by); ?>><?php _e("Course Title (z-a)", "tutor"); ?></option>
            </select>
        </form>
    </div>
</div>