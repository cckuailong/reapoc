<?php
/**
 * Template for get ready wizard page
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/wizard/wizard-get-ready
 */
?>

<h4 class="wiz-style"><?php echo esc_html__('Get Ready', 'simple-job-board'); ?></h4>
<div class="sjb-section get-ready">
    <div class="sjb-content">
        <div class="sjb-wiz-hyper-links">
            <a href="https://market.presstigers.com/support/" target="blank">
                <strong><?php echo esc_html__('Get Support', 'simple-job-board'); ?></strong>
                <span><?php echo esc_html__('Submit your ticket and get support on your queries.', 'simple-job-board'); ?></span>
            </a>
        </div>
    </div>
</div>
<h4 class="wiz-style"><?php echo esc_html__('PressTigers Plugins', 'simple-job-board'); ?></h4>
<div class="sjb-section sjb-plugin-details-wrap">
    <div class="sjb-content">
        <div class="sjb-plugins-wrappers">
            <figure>
                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . '/admin/images/fb-flexy-breadcrumb.png' ?>" alt="">
                <figcaption>
                    <h4><?php echo esc_html__('Flexy BreadCrumb', 'simple-job-board'); ?></h4>
                    <a href="https://wordpress.org/plugins/flexy-breadcrumb/" target="blank"><?php echo esc_html__('Download Now', 'simple-job-board'); ?></a>
                </figcaption>
            </figure>
            <figure>
                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . '/admin/images/sep-simple-event-planner.png' ?>" alt="">
                <figcaption>
                    <h4><?php echo esc_html__('Simple Event Planner', 'simple-job-board'); ?></h4>
                    <a href="https://wordpress.org/plugins/simple-event-planner/" target="blank"><?php echo esc_html__('Download Now', 'simple-job-board'); ?></a>
                </figcaption>
            </figure>
            <figure>
                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . '/admin/images/sf-simple-folio.png' ?>" alt="">
                <figcaption>
                    <h4><?php echo esc_html__('Simple Folio', 'simple-job-board'); ?></h4>
                    <a href="https://wordpress.org/plugins/simple-folio/" target="blank"><?php echo esc_html__('Download Now', 'simple-job-board'); ?></a>
                </figcaption>
            </figure>
            <figure>
                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . '/admin/images/soc-simple-owl-carousel.png' ?>" alt="">
                <figcaption>
                    <h4><?php echo esc_html__('Simple Owl Carousel', 'simple-job-board'); ?></h4>
                    <a href="https://wordpress.org/plugins/simple-owl-carousel/" target="blank"><?php echo esc_html__('Download Now', 'simple-job-board'); ?></a>
                </figcaption>
            </figure>
            <figure>
                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . '/admin/images/sts-simple-testimonials-showcase.png' ?>" alt="">
                <figcaption>
                    <h4><?php echo esc_html__('Simple Testimonials Showcase', 'simple-job-board'); ?></h4>
                    <a href="https://wordpress.org/plugins/simple-testimonials-showcase/" target="blank"><?php echo esc_html__('Download Now', 'simple-job-board'); ?></a>
                </figcaption>
            </figure>
        </div>
    </div>
</div>

<?php
if (!file_exists(WP_PLUGIN_DIR . '/frontend-job-post/frontend-job-post.php')  && !is_plugin_active('frontend-job-post/frontend-job-post.php')) {
    ?>
    <div class="sjb-section disabled">
        <div class="buy-enable-addon">
            <h4><?php echo esc_html__('Frontend Job Posting Add-on', 'simple-job-board'); ?></h4>
            <div class="btn-group">
                <a target="blank" href="https://market.presstigers.com/product/frontend-jobposting-add-on/" target="_blank" class="btn btn-primary"><?php echo esc_html__('Go to Market Place', 'simple-job-board'); ?></a>
            </div>
        </div>
    </div>
    <?php
}
if (!file_exists(WP_PLUGIN_DIR . '/simple-job-board-email-attachment/simple-job-board-email-attachment.php')  && !is_plugin_active('hsimple-job-board-email-attachment/simple-job-board-email-attachment.php')) {
    ?>
    <div class="sjb-section disabled">
        <div class="buy-enable-addon">
            <h4><?php echo esc_html__('Email Attachment Add-on', 'simple-job-board'); ?></h4>
            <div class="btn-group">
                <a target="blank" href="https://market.presstigers.com/product/email-application-add-on/" class="btn btn-primary"><?php echo esc_html__('Go to Market Place', 'simple-job-board'); ?></a>
            </div>
        </div>
    </div>
    <?php
}
if (!file_exists(WP_PLUGIN_DIR . '/how-to-apply/how-to-apply.php')  && !is_plugin_active('how-to-apply/how-to-apply.php')) {
    ?>
    <div class="sjb-section disabled">
        <div class="buy-enable-addon">
            <h4><?php echo esc_html__('How to Apply Add-on', 'simple-job-board'); ?></h4>
            <div class="btn-group">
                <a target="blank" href="https://market.presstigers.com/product/how-to-apply-add-on/" class="btn btn-primary"><?php echo esc_html__('Go to Market Place', 'simple-job-board'); ?></a>
            </div>
        </div>
    </div>
    <?php
}
if (!file_exists(WP_PLUGIN_DIR . '/simple-job-board-multiple-attachment-fields/simple-job-board-multiple-attachment-fields.php')  && !is_plugin_active('simple-job-board-multiple-attachment-fields/simple-job-board-multiple-attachment-fields.php')) {
    ?>
    <div class="sjb-section disabled">
        <div class="buy-enable-addon disabled">
            <h4><?php echo esc_html__('Multiple Attachment Fields Add-on', 'simple-job-board'); ?></h4>
            <div class="btn-group">
                <a target="blank" href="https://market.presstigers.com/product/multiple-attachment-fields-add-on/" class="btn btn-primary"><?php echo esc_html__('Go to Market Place', 'simple-job-board'); ?></a>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="sjb-stripe"></div>
<a class="action-button previous previous_button" href="<?php echo $wizard_url ?>" class="previous-btn action-button"><?php echo esc_html__('Back', 'simple-job-board'); ?></a>
<a href="<?php echo $settings_url ?>" class="next action-button"><?php echo esc_html__('Finish', 'simple-job-board'); ?></a>
