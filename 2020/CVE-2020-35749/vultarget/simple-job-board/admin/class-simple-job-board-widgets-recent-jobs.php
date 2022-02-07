<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Widgets_Recent_Jobs class
 * 
 * @link        http://presstigers.com
 * @since       2.4.3
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Widgets_Recent_Jobs extends WP_Widget {

    /**
     * Register widget with WordPress
     * 
     * @since   2.4.3
     *
     * @param void
     * @param return void
     */
    public function __construct() {
        $widget_opts = array(
            'classname' => 'sjb-recent-jobs-widget',
            'description' => __('Your site\'s most recent Jobs.', 'simple-job-board')
        );
        parent::__construct('Simple_Job_Board_Widgets_Recent_Jobs', __('SJB Recent Jobs', 'simple-job-board'), $widget_opts);
    }

    /**
     * @SJB Core: Widgets Recent Jobs form Back-end.
     *
     * @since   2.4.3
     * 
     * @param   array   $instance   Previously saved values from database.
     * return   void
     */
    public function form($instance) {
        $instance = wp_parse_args((array) $instance, array('title' => ''));

        // Widgets Form Default Parameters
        $title = $instance['title'];
        $showcount = isset($instance['showcount']) ? esc_attr($instance['showcount']) : '5';
        $job_category = isset($instance['job_category']) ? $instance['job_category'] : '0';

        // Job Categories
        $cat_arg = array(
            'type' => 'jobpost',
            'child_of' => 0,
            'taxonomy' => 'jobpost_category',
            'hide_empty' => FALSE,
        );

        $categories = get_categories($cat_arg);
        ?>

        <!-- Widget Title -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"> <?php _e('Title:', 'simple-job-board'); ?>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </label>
        </p>

        <!-- Job Categories -->
        <p>
            <label for="<?php echo $this->get_field_id('job_category'); ?>"> <?php _e('Select Category:', 'simple-job-board'); ?>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('job_category')); ?>" name="<?php echo esc_attr($this->get_field_name('job_category')); ?>">
                    <option value="0"><?php echo __('All', 'simple-job-board'); ?></option>
                    <?php
                    if (isset($categories) && $categories) {
                        foreach ($categories as $category) {
                            ?>
                            <option <?php selected($job_category, $category->slug); ?> value="<?php echo esc_attr($category->slug); ?>" ><?php echo esc_attr($category->name); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </label>
        </p>

        <!-- Number of Job Posts -->
        <p>
            <label for="<?php echo $this->get_field_id('showcount'); ?>"> <?php _e('Number of posts to display:', 'simple-job-board'); ?>
                <input type="text" value="<?php echo $showcount; ?>" id="<?php echo esc_attr($this->get_field_id('showcount')); ?>" size='2' name="<?php echo esc_attr($this->get_field_name('showcount')); ?>" />
            </label>
        </p>
        <?php
    }

    /**
     * @SJB Core: Widgets Recent Jobs Sanitize widget form values as they are saved.
     * 
     * @since   2.4.3
     *
     * @param   array   $new_instance   Values just sent to be saved, 
     * @param   array   $old_instance   Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {

        // Updated Widget Data
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['showcount'] = $new_instance['showcount'];
        $instance['job_category'] = $new_instance['job_category'];
        return $instance;
    }

    /**
     * @SJB Core: Widgets Recent Jobs Front-end display of widget.
     * 
     * @since   2.4.3
     *
     * @param   array   $args       Widget arguments
     * @param   array   $instance   Saved values from database.
     * @return void
     */
    public function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
        $showcount = empty($instance['showcount']) ? '5' : $instance['showcount'];
        $jobpost_category = empty($instance['job_category']) ? '' : $instance['job_category'];
        $title = htmlspecialchars_decode(stripslashes($title));
        $html = '';

        $arguments = array(
            'posts_per_page' => $showcount,
            'post_type' => 'jobpost',
        );

        if (isset($jobpost_category) && $jobpost_category != '0') {
            $arguments['jobpost_category'] = $jobpost_category;
        }
        // Job Query
        $wp_recent_jobs = new WP_Query($arguments);


        // List Jobs
        if ($wp_recent_jobs->have_posts()) {

            // Display Widget Before HTML
            echo $before_widget;

            // Widget Title
            if (!empty($title) && $title != ' ') {
                echo $before_title . $title . $after_title;
            }

            /**
             * Template -> Widget Start Wrapper
             * 
             * @since   2.4.3
             */
            get_simple_job_board_template('widget/job-widget-start.php');

            while ( $wp_recent_jobs->have_posts() ) {
                $wp_recent_jobs->the_post();

                /**
                 * Template -> Recent Job Widget Content
                 * 
                 * @since   2.4.3
                 */
                get_simple_job_board_template('widget/content-recent-jobs-widget.php');
            }

            /**
             * Template -> Widget End Wrapper
             * 
             * @since   2.4.3
             */
            get_simple_job_board_template('widget/job-widget-end.php');

            wp_reset_query();
            
            // Display Widget After HTML
            echo $after_widget;
        }
    }

}
