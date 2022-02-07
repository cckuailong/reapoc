<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Meta_box_Job_Data Class
 * 
 * This meta box is designed to store company name, website, tagline and logo.
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.2.3
 * @since       2.4.0   Improved Sanitization & Escaping of Inputs & Outputs
 * 
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/partials/meta-boxes
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Meta_Box_Job_Data {

    /**
     * Add job data meta box options.
     * 
     * @since   2.2.3
     */
    public static function sjb_meta_box_output() {

        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'sjb_jobpost_meta_box', 'jobpost_meta_box_nonce' );

        echo '<div class="simple-job-board-metabox">';
        Simple_Job_Board_Meta_box_Job_Data::text('_company_name', esc_html__('Company Name', 'simple-job-board'), '');
        Simple_Job_Board_Meta_box_Job_Data::text('_company_website', esc_html__('Company Website', 'simple-job-board'), '');
        Simple_Job_Board_Meta_box_Job_Data::text('_company_tagline', esc_html__('Company Tagline', 'simple-job-board'), '');
        Simple_Job_Board_Meta_box_Job_Data::upload('_company_logo', esc_html__('Company Logo', 'simple-job-board'), '');
        echo '</div>';
    }

    /**
     * Job data meta box option fields.
     * 
     * @since 2.1.0
     * 
     * @param   string   $id     field id
     * @param   string   $label  field lable
     * @param   string   $desc   field description
     * @return  string   $html   field html
     */
    public static function text($id, $label, $desc = '') {
        
        global $post;
        $html = '<p class="metabox-field">';
        $html .= '<label for="simple_job_board' . $id . '">' . $label . '</label>';
        $html .= '<input type="text" id="simple_job_board' . $id . '" name="simple_job_board' . $id . '" value="' . esc_attr( get_post_meta($post->ID, 'simple_job_board' . $id, TRUE) ) . '" />';
        if ($desc) {
            $html .= '<span class="tips">' . $desc . '</span>';
        }

        $html .= '</p>';
        echo $html;
    }

    /**
     * Upload logo field
     * 
     * @since 2.1.0
     * 
     * @param   string   $id     Field id
     * @param   string   $label  Field lable
     * @param   string   $desc   Field description
     * @return  string   $html   Field html
     */
    public static function upload($id, $label, $desc = '') {        
        global $post;
        ?>

        <p class="metabox-field">
            <label for="simple_job_board<?php echo $id; ?>"><?php echo $label; ?></label>            
            <span class="file_url">
                <input type="text" name="simple_job_board<?php echo $id; ?>" id="simple_job_board<?php echo $id; ?>" class="upload_field" placeholder="URL to the company logo" value="<?php echo esc_url( get_post_meta($post->ID, 'simple_job_board' . $id, TRUE) ); ?>" />
                <button type="button" class="button simple-job-board-upload-button"><?php esc_html_e('Upload', 'simple-job-board'); ?></button>
            </span>            
            <?php if ( $desc ) : ?>
            <p><?php echo $desc; ?></p>
        <?php endif; ?>
        </p>
        
        <?php
    }

    /**
     * Save job data meta box.
     * 
     * @since   2.2.3
     * 
     * @param   int     $post_id    Post id
     * @return  void
     */
    public static function sjb_save_jobpost_meta($post_id)
    {
        $POST_data = filter_input_array( INPUT_POST );
        
        foreach ( $POST_data as $key => $value )
        {
            if (strstr($key, 'simple_job_board')) {
                update_post_meta( $post_id, sanitize_key( $key ), $value );
            }
        }
    }
}