<?php
/*
 * Widget Recent Jobs Front End
 * 
 * * Override this template by copying it to yourtheme/simple_job_board/widget/content-recent-jobs-widget.php
 * 
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/widget/content-recent-jobs-widget.php
 * @version     1.0.0
 */
global $post;
?>
<li >
    <a href="<?php the_permalink(); ?>"><?php esc_attr(the_title()); ?></a>
    <div><i class="fa fa-calendar-times-o"></i> <?php echo date_i18n(get_option('date_format'), strtotime(get_the_date('F jS, Y'))); ?></div>
</li>