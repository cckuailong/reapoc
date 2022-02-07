<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Settings_Upload_File_Extensions Class
 * 
 * This file used to define the settings for the resume extensions. Allowable
 * extensions for resume are doc, docx, rtf, txt, odt.
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since      2.2.3
 * @since      2.4.0    Revised Inputs & Outputs Sanitization & Escaping
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/settings
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Settings_Upload_File_Extensions {

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.2.3
     */
    public function __construct() {

        // Filter -> Add Settings Uploaded File Extensions Tab
        add_filter('sjb_settings_tab_menus', array($this, 'sjb_add_settings_tab'), 80);

        // Action -> Add Settings Uploaded File Extensions Section 
        add_action('sjb_settings_tab_section', array($this, 'sjb_add_settings_section'), 80);

        // Action -> Save Settings Uploaded File Extensions Section 
        add_action('sjb_save_setting_sections', array($this, 'sjb_save_settings_section'));
    }

    /**
     * Add Settings Uploaded File Extensions Tab.
     *
     * @since    2.2.3
     * 
     * @param    array  $tabs  Settings Tab
     * @return   array  $tabs  Merge array of Settings Tab with "Upload File Extensions" Tab.
     */
    public function sjb_add_settings_tab($tabs) {        
        $tabs['upload_file_ext'] = esc_html__( 'Upload File Extensions', 'simple-job-board' );
        return $tabs;
    }

    /**
     * Add Settings Uploaded File Extensions Section.
     *
     * @since    2.2.3
     */
    public function sjb_add_settings_section() {
        
        ?>
        <!-- Upload File Extensions -->
        <div data-id="settings-upload_file_ext" class="sjb-admin-settings tab">
            
            <?php
            /**
             * Action -> Add new section before file upload settings .  
             * 
             * @since 2.2.0 
             */
            do_action('sjb_file_upload_settings_before');
            ?>

            <form method="post" id="upload-file-form">
                <h4 class="first"><?php echo esc_html__('Upload File Extensions', 'simple-job-board'); ?></h4>
                <div class="sjb-section sjb-file-extensions">
                    <div class="sjb-content sjb-file-extensions">
                        <?php
                        /**
                         * Action -> Add new extension at the start of list.  
                         * 
                         * @since 2.8.3
                         */
                        do_action('sjb_file_extension_options_start');
                        ?>
                        <div class="sjb-form-group sjb-extensions">
                            <?php
                            if( FALSE !== get_option('job_board_all_extensions_check')){
                                if( 'no' === get_option('job_board_all_extensions_check') ){
                                    if( FALSE !== get_option('job_board_upload_file_ext')){
                                        if(in_array('pdf', get_option('job_board_upload_file_ext'))){
                                            $selected = 'checked';
                                        }
                                        else{
                                            $selected = '';
                                        }
                                    }
                                    else{
                                        $selected = 'checked';
                                    }
                                }
                                else{
                                    $selected = 'checked';
                                }
                            }
                            else{
                                $selected = 'checked';
                            }
                            ?>
                            
                            <input type="checkbox" id="upload_file_ext_pdf" name="file_extensions[]" value="pdf" <?php echo $selected; ?>>
                            <input type="hidden" name="upload_file_ext_pdf" value="upload_file_ext_pdf">
                            <label for="upload_file_ext_pdf"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/pdf.png' ?>"> <span><?php echo esc_html__( 'PDF', 'simple-job-board' ); ?></span> </label>
                        </div>

                        <div class="sjb-form-group sjb-extensions">
                            <?php
                            if( 'no' === get_option('job_board_all_extensions_check') ){
                                if( FALSE !== get_option('job_board_upload_file_ext')){
                                    if(in_array('doc', get_option('job_board_upload_file_ext'))){
                                        $selected = 'checked';
                                    }
                                    else{
                                        $selected = '';
                                    }
                                }
                                else{
                                    $selected = 'checked';
                                }
                            }
                            else{
                                $selected = 'checked';
                            }
                            ?>
                            
                            <input type="checkbox" id="upload_file_ext_doc" name="file_extensions[]" value="doc" <?php echo $selected; ?>>
                            <input type="hidden" name="upload_file_ext_doc" value="upload_file_ext_doc">
                            <label for="upload_file_ext_doc"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/doc.png' ?>"> <span><?php echo esc_html__( 'Doc', 'simple-job-board' ); ?></span> </label>
                        </div>

                        <div class="sjb-form-group sjb-extensions">
                            <?php
                            if( 'no' === get_option('job_board_all_extensions_check') ){
                                if( FALSE !== get_option('job_board_upload_file_ext')){
                                    if(in_array('docx', get_option('job_board_upload_file_ext'))){
                                        $selected = 'checked';
                                    }
                                    else{
                                        $selected = '';
                                    }
                                }
                                else{
                                    $selected = 'checked';
                                }
                            }
                            else{
                                $selected = 'checked';
                            }
                            ?>
                            
                            <input type="checkbox" id="upload_file_ext_docx" name="file_extensions[]" value="docx" <?php echo $selected; ?>>
                            <input type="hidden" name="upload_file_ext_docx" value="upload_file_ext_docx">
                            <label for="upload_file_ext_docx"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/docx.png' ?>"> <span><?php echo esc_html__( 'Docx', 'simple-job-board' ); ?></span> </label>
                        </div>

                        <div class="sjb-form-group sjb-extensions">
                            <?php
                            if( 'no' === get_option('job_board_all_extensions_check') ){
                                if( FALSE !== get_option('job_board_upload_file_ext')){
                                    if(in_array('odt', get_option('job_board_upload_file_ext'))){
                                        $selected = 'checked';
                                    }
                                    else{
                                        $selected = '';
                                    }
                                }
                                else{
                                    $selected = 'checked';
                                }
                            }
                            else{
                                $selected = 'checked';
                            }
                            ?>
                            
                            <input type="checkbox" id="upload_file_ext_odt" name="file_extensions[]" value="odt" <?php echo $selected; ?>>
                            <input type="hidden" name="upload_file_ext_odt" value="upload_file_ext_odt">
                            <label for="upload_file_ext_odt"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/odt.png' ?>"> <span><?php echo esc_html__( 'Odt', 'simple-job-board' ); ?></span> </label>
                        </div>

                        <div class="sjb-form-group sjb-extensions">
                            <?php
                            if( 'no' === get_option('job_board_all_extensions_check') ){
                                if( FALSE !== get_option('job_board_upload_file_ext')){
                                    if(in_array('rtf', get_option('job_board_upload_file_ext'))){
                                        $selected = 'checked';
                                    }
                                    else{
                                        $selected = '';
                                    }
                                }
                                else{
                                    $selected = 'checked';
                                }
                            }
                            else{
                                $selected = 'checked';
                            }
                            ?>
                            
                            <input type="checkbox" id="upload_file_ext_rtf" name="file_extensions[]" value="rtf" <?php echo $selected; ?>>
                            <input type="hidden" name="upload_file_ext_rtf" value="upload_file_ext_rtf">
                            <label for="upload_file_ext_rtf"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/rtf.png' ?>"> <span><?php echo esc_html__( 'Rtf', 'simple-job-board' ); ?></span> </label>
                        </div>

                        <div class="sjb-form-group sjb-extensions">
                            <?php
                            if( 'no' === get_option('job_board_all_extensions_check') ){
                                if( FALSE !== get_option('job_board_upload_file_ext')){
                                    if(in_array('txt', get_option('job_board_upload_file_ext'))){
                                        $selected = 'checked';
                                    }
                                    else{
                                        $selected = '';
                                    }
                                }
                                else{
                                    $selected = 'checked';
                                }
                            }
                            else{
                                $selected = 'checked';
                            }
                            ?>
                            <input type="checkbox" id="upload_file_ext_txt" name="file_extensions[]" value="txt" <?php echo $selected; ?> >
                            <input type="hidden" name="upload_file_ext_txt" value="upload_file_ext_txt">
                            <label for="upload_file_ext_txt"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/txt.png' ?>"> <span><?php echo esc_html__( 'Text', 'simple-job-board' ); ?></span></label>
                        </div>
                        <?php
                        /**
                         * Action -> Add new extension at the end of list.  
                         * 
                         * @since 2.2.0 
                         */
                        do_action('sjb_file_extension_options_end');

                        if( FALSE !== get_option('job_board_all_extensions_check')){
                            if( 'no' === get_option('job_board_all_extensions_check') ){
                                $check = '';
                            }
                            else{
                                $check = 'checked';
                            }
                        }
                        else{
                            $check = 'checked';
                        }
                        ?>
                        <div class="sjb-form-group">
                            <input type="checkbox" name="all_file_extensions" id="all-file-ext" value="all extension" <?php echo $check; ?> />
                            <label for="all-file-ext"><?php echo esc_html__('Enable all Extensions', 'simple-job-board'); ?></label>
                            <input type='hidden' name="empty_file_extensions" value="empty_file_extensions" />
                        </div>
                        <div class="sjb-form-group">                            
                            <i class="fa fa-info-circle" aria-hidden="true"></i> <label><?php echo esc_html__( 'Security rules have been updated since SJB version 2.4.3.', 'simple-job-board' ); ?></label>
                        </div>
                        
                        <?php
                        /**
                         * Action -> Add new fields at the end of upload section.  
                         * 
                         * @since 2.2.0 
                         */
                        do_action('sjb_file_upload_settings_end');
                        ?>
                    </div>
                </div>
                <input type="hidden" value="1" name="admin_notices" />
                <input type="submit" name="upload_file_form_submit" id="upload-file-form-submit" class="button button-primary" value="<?php echo esc_html__('Save Changes', 'simple-job-board'); ?>" />
            </form>
        </div>
        <?php
    }

    /**
     * Save Settings Upload File Extension Section.
     * 
     * This function is used to save the uploaded file extensions settings. User
     * can set the security of uploaded file by enabling/disabling it's 
     * extension & anti-hotlinking rules.
     *
     * @since    2.2.3
     */
    public function sjb_save_settings_section() {
        $file_extensions = filter_input( INPUT_POST, 'file_extensions', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        $empty_file_extensions = filter_input( INPUT_POST, 'empty_file_extensions' );
        $all_file_extensions = filter_input( INPUT_POST, 'all_file_extensions' );
        $files_hotlinking = filter_input( INPUT_POST, 'files_hotlinking' );
        
        // Save File Extension on Form Submission
        if ( !empty( $file_extensions )  || !empty( $empty_file_extensions ) ) {

            // Empty Checkboxes Status
            $file_extension = $anti_hotlinking = 'no';

            // Save Extentions Settigns
            if ( !empty( $file_extensions ) ) {

                // Save Extentions in WP Options || Add Options if not Exist
                ( FALSE !== get_option('job_board_upload_file_ext') ) ? 
                    update_option('job_board_upload_file_ext', $file_extensions ) :
                    add_option('job_board_upload_file_ext', $file_extensions, '', 'no');
            }

            // Enable All File Extensions
            if ( isset( $all_file_extensions ) ) {
                update_option('job_board_all_extensions_check', 'yes');
                $file_extension = 'yes';
            }

            // Enable File Anti-hotlinking Rules
            if ( isset( $files_hotlinking ) ) {
                update_option('job_board_anti_hotlinking', 'yes');
                $anti_hotlinking = 'yes';
                $sjbrObj = new Simple_Job_Board_Rewrite();
                $sjbrObj->job_board_rewrite();
            }

            // Disable File Extensions
            if ( 'no' === $file_extension ) {
                update_option('job_board_all_extensions_check', 'no');
            }

            // Disable Anti-Hotlinking Rules
            if ( 'no' === $anti_hotlinking ) {
                update_option('job_board_anti_hotlinking', 'no');
                $sjbrObj = new Simple_Job_Board_Rewrite();
                $sjbrObj->job_board_rewrite();
            }
        }
    }

}