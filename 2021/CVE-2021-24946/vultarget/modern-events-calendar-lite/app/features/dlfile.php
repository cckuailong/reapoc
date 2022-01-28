<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Downloadable File class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_dlfile extends MEC_base
{
    public $factory;
    public $main;
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();

        // Import MEC Main
        $this->main = $this->getMain();
        
        // MEC Settings
        $this->settings = $this->main->get_settings();
    }
    
    /**
     * Initialize locations feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Booking Status
        $booking_status = (isset($this->settings['booking_status']) and $this->settings['booking_status']) ? true : false;

        // Downloadable Status
        $downloadable_status = (isset($this->settings['downloadable_file_status']) and $this->settings['downloadable_file_status']);

        // Feature is not enabled
        if(!$booking_status or !$downloadable_status) return;

        // Metabox
        $this->factory->action('mec_metabox_booking', array($this, 'meta_box_downloadable_file'), 17);

        // Downloadable File for FES
        if(!isset($this->settings['fes_section_downloadable_file']) or (isset($this->settings['fes_section_downloadable_file']) and $this->settings['fes_section_downloadable_file'])) $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_downloadable_file'), 47);

        // AJAX
        $this->factory->action('wp_ajax_mec_downloadable_file_upload', array($this, 'upload'));
    }

    /**
     * Show downloadable file of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_downloadable_file($post)
    {
        // Disable For Guest
        if(!get_current_user_id()) return;

        $file_id = get_post_meta($post->ID, 'mec_dl_file', true);
        if(trim($file_id) == '') $file_id = '';

        $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
        ?>
        <script>
        jQuery(document).ready(function()
        {
            jQuery("#mec_downloadable_file_uploader").on('change', function()
            {
                var fd = new FormData();
                fd.append("action", "mec_downloadable_file_upload");
                fd.append("_wpnonce", "<?php echo wp_create_nonce('mec_downloadable_file_upload'); ?>");
                fd.append("file", jQuery("#mec_downloadable_file_uploader").prop("files")[0]);

                jQuery("#mec_downloadable_file_error").html("").addClass("mec-util-hidden");
                jQuery.ajax(
                {
                    url: "<?php echo admin_url('admin-ajax.php', NULL); ?>",
                    type: "POST",
                    data: fd,
                    dataType: "json",
                    processData: false,
                    contentType: false
                })
                .done(function(response)
                {
                    if(response.success)
                    {
                        jQuery("#mec_downloadable_file_link").html('<a href="'+response.data.url+'" target="_blank">'+response.data.url+'</a>').removeClass("mec-util-hidden");
                        jQuery("#mec_downloadable_file").val(response.data.id);
                        jQuery("#mec_downloadable_file_remove_image_button").removeClass("mec-util-hidden");
                    }
                    else
                    {
                        jQuery("#mec_downloadable_file_error").html(response.message).removeClass("mec-util-hidden");
                    }

                    // Reset File Input
                    jQuery("#mec_downloadable_file_uploader").val('');
                });

                return false;
            });

            jQuery("#mec_downloadable_file_remove_image_button").on('click', function()
            {
                jQuery("#mec_downloadable_file_link").html('').addClass("mec-util-hidden");
                jQuery("#mec_downloadable_file").val('');
                jQuery("#mec_downloadable_file_remove_image_button").addClass("mec-util-hidden");
            });
        });
        </script>
        <div class="mec-meta-box-fields mec-booking-tab-content" id="mec-downloadable-file">
            <h4><?php _e('Downloadable File', 'modern-events-calendar-lite'); ?></h4>
            <div id="mec_meta_box_downloadable_file_options">
                <input type="hidden" id="mec_downloadable_file" name="mec[downloadable_file]" value="<?php echo $file_id; ?>">
                <input type="file" id="mec_downloadable_file_uploader">
                <p class="description"><?php esc_html_e('pdf,zip,png,jpg and gif files are allowed.', 'modern-events-calendar-lite'); ?></p>
                <div id="mec_downloadable_file_link" class="<?php echo (trim($file_id) ? '' : 'mec-util-hidden'); ?>"><?php echo ($file_id ? '<a href="'.$file_url.'" target="_blank">'.$file_url.'</a>' : ''); ?></div>
                <button type="button" id="mec_downloadable_file_remove_image_button" class="<?php echo (trim($file_id) ? '' : 'mec-util-hidden'); ?>"><?php _e('Remove File', 'modern-events-calendar-lite'); ?></button>
                <div class="mec-error mec-util-hidden" id="mec_downloadable_file_error"></div>
            </div>
        </div>
        <?php
    }

    public function upload()
    {
        // Check if our nonce is set.
        if(!isset($_POST['_wpnonce'])) $this->main->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'mec_downloadable_file_upload')) $this->main->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        $uploaded_file = isset($_FILES['file']) ? $_FILES['file'] : NULL;

        // No file
        if(!$uploaded_file) $this->main->response(array('success'=>0, 'code'=>'NO_FILE', 'message'=>esc_html__('Please upload a file.', 'modern-events-calendar-lite')));

        $allowed = array('gif', 'jpeg', 'jpg', 'png', 'pdf', 'zip');

        $ex = explode('.', $uploaded_file['name']);
        $extension = end($ex);

        // Invalid Extension
        if(!in_array($extension, $allowed)) $this->main->response(array('success'=>0, 'code'=>'INVALID_EXTENSION', 'message'=>sprintf(esc_html__('File extension is invalid. You can upload %s files.', 'modern-events-calendar-lite'), implode(', ', $allowed))));

        // Maximum File Size
        $max_file_size = isset($this->settings['fes_max_file_size']) ? (int) ($this->settings['fes_max_file_size'] * 1000) : (5000 * 1000);

        // Invalid Size
        if($uploaded_file['size'] > $max_file_size) $this->main->response(array('success'=>0, 'code'=>'IMAGE_IS_TOO_BIG', 'message'=>sprintf(esc_html__('File is too big. Maximum size is %s KB.', 'modern-events-calendar-lite'), ($max_file_size / 1000))));

        // Include the functions
        if(!function_exists('wp_handle_upload'))
        {
            require_once ABSPATH.'wp-admin/includes/file.php';
            require_once(ABSPATH.'wp-admin/includes/image.php');
        }

        $upload = wp_upload_bits($uploaded_file['name'], NULL, file_get_contents($uploaded_file['tmp_name']));
        $wp_filetype = wp_check_filetype(basename($upload['file']), NULL);

        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
            'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path($upload['file']),
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload['file'])),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $upload['file']);
        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $upload['file']));

        $success = 0;
        $data = array();

        if($attach_id and (!isset($upload['error']) or (isset($upload['error']) and !$upload['error'])))
        {
            $success = 1;
            $message = __('File uploaded!', 'modern-events-calendar-lite');

            $data['url'] = $upload['url'];
            $data['id'] = $attach_id;
        }
        else
        {
            $message = $upload['error'];
        }

        $this->main->response(array('success'=>$success, 'message'=>$message, 'data'=>$data));
    }
}