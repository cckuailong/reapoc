<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC speakers class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_speakers extends MEC_base
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
     * Initialize speakers feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Speakers Feature is Disabled
        if(!isset($this->settings['speakers_status']) or (isset($this->settings['speakers_status']) and !$this->settings['speakers_status'])) return;

        $this->factory->action('init', array($this, 'register_taxonomy'), 25);
        $this->factory->action('mec_speaker_edit_form_fields', array($this, 'edit_form'));
        $this->factory->action('mec_speaker_add_form_fields', array($this, 'add_form'));
        $this->factory->action('edited_mec_speaker', array($this, 'save_metadata'));
        $this->factory->action('created_mec_speaker', array($this, 'save_metadata'));

        $this->factory->action('wp_ajax_speaker_adding', array($this, 'fes_speaker_adding'));
        $this->factory->action('wp_ajax_nopriv_speaker_adding', array($this, 'fes_speaker_adding'));
        $this->factory->action('current_screen', array($this, 'show_notics'));

        $this->factory->filter('manage_edit-mec_speaker_columns', array($this, 'filter_columns'));
        $this->factory->filter('manage_mec_speaker_custom_column', array($this, 'filter_columns_content'), 10, 3);
    }
    
    /**
     * Registers speaker taxonomy
     * @author Webnus <info@webnus.biz>
     */
    public function register_taxonomy()
    {
        $singular_label = $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite'));
        $plural_label = $this->main->m('taxonomy_speakers', __('Speakers', 'modern-events-calendar-lite'));

        register_taxonomy(
            'mec_speaker',
            $this->main->get_main_post_type(),
            array(
                'label'=>$plural_label,
                'labels'=>array(
                    'name'=>$plural_label,
                    'singular_name'=>$singular_label,
                    'all_items'=>sprintf(__('All %s', 'modern-events-calendar-lite'), $plural_label),
                    'edit_item'=>sprintf(__('Edit %s', 'modern-events-calendar-lite'), $singular_label),
                    'view_item'=>sprintf(__('View %s', 'modern-events-calendar-lite'), $singular_label),
                    'update_item'=>sprintf(__('Update %s', 'modern-events-calendar-lite'), $singular_label),
                    'add_new_item'=>sprintf(__('Add New %s', 'modern-events-calendar-lite'), $singular_label),
                    'new_item_name'=>sprintf(__('New %s Name', 'modern-events-calendar-lite'), $singular_label),
                    'popular_items'=>sprintf(__('Popular %s', 'modern-events-calendar-lite'), $plural_label),
                    'search_items'=>sprintf(__('Search %s', 'modern-events-calendar-lite'), $plural_label),
                    'back_to_items'=>sprintf(__('← Back to %s', 'modern-events-calendar-lite'), $plural_label),
                    'not_found'=>sprintf(__('no %s found.', 'modern-events-calendar-lite'), strtolower($plural_label)),
                ),
                'rewrite'=>array('slug'=>'events-speaker'),
                'public'=>false,
                'show_ui'=>true,
                'show_in_rest'=>true,
                'hierarchical'=>false,
            )
        );
        
        register_taxonomy_for_object_type('mec_speaker', $this->main->get_main_post_type());
    }
    
    /**
     * Show edit form of speaker taxonomy
     * @author Webnus <info@webnus.biz>
     * @param object $term
     */
    public function edit_form($term)
    {
        $job_title = get_metadata('term', $term->term_id, 'job_title', true);
        $tel = get_metadata('term', $term->term_id, 'tel', true);
        $email = get_metadata('term', $term->term_id, 'email', true);
        $website = get_metadata('term', $term->term_id, 'website', true);
        $facebook = get_metadata('term', $term->term_id, 'facebook', true);
        $instagram = get_metadata('term', $term->term_id, 'instagram', true);
        $linkedin = get_metadata('term', $term->term_id, 'linkedin', true);
        $twitter = get_metadata('term', $term->term_id, 'twitter', true);
        $thumbnail = get_metadata('term', $term->term_id, 'thumbnail', true);
    ?>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_job_title"><?php _e('Job Title', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" placeholder="<?php esc_attr_e('Insert speaker job title.', 'modern-events-calendar-lite'); ?>" name="job_title" id="mec_job_title" value="<?php echo $job_title; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_tel"><?php _e('Tel', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" placeholder="<?php esc_attr_e('Insert speaker phone number.', 'modern-events-calendar-lite'); ?>" name="tel" id="mec_tel" value="<?php echo $tel; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_email"><?php _e('Email', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text"  placeholder="<?php esc_attr_e('Insert speaker email address.', 'modern-events-calendar-lite'); ?>" name="email" id="mec_email" value="<?php echo $email; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_website"><?php _e('Website', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" placeholder="<?php esc_attr_e('Insert URL of Website', 'modern-events-calendar-lite'); ?>" name="website" id="mec_website" value="<?php echo $website; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_facebook"><?php _e('Facebook Page', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" placeholder="<?php esc_attr_e('Insert URL of Facebook Page', 'modern-events-calendar-lite'); ?>" name="facebook" id="mec_facebook" value="<?php echo $facebook; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_instagram"><?php _e('Instagram', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" placeholder="<?php esc_attr_e('Insert URL of Instagram', 'modern-events-calendar-lite'); ?>" name="instagram" id="mec_instagram" value="<?php echo $instagram; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_linkedin"><?php _e('LinkedIn', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" placeholder="<?php esc_attr_e('Insert URL of LinkedIn', 'modern-events-calendar-lite'); ?>" name="linkedin" id="mec_linkedin" value="<?php echo $linkedin; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_twitter"><?php _e('Twitter Page', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" placeholder="<?php esc_attr_e('Insert URL of Twitter Page', 'modern-events-calendar-lite'); ?>" name="twitter" id="mec_twitter" value="<?php echo $twitter; ?>" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="mec_thumbnail_button"><?php _e('Thumbnail', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <div id="mec_thumbnail_img"><?php if(trim($thumbnail) != '') echo '<img src="'.$thumbnail.'" />'; ?></div>
                <input type="hidden" name="thumbnail" id="mec_thumbnail" value="<?php echo $thumbnail; ?>" />
                <button type="button" class="mec_upload_image_button button" id="mec_thumbnail_button"><?php echo __('Upload/Add image', 'modern-events-calendar-lite'); ?></button>
                <button type="button" class="mec_remove_image_button button <?php echo (!trim($thumbnail) ? 'mec-util-hidden' : ''); ?>"><?php echo __('Remove image', 'modern-events-calendar-lite'); ?></button>
            </td>
        </tr>
        <?php do_action('mec_edit_speaker_extra_fields', $term); ?>
    <?php
    }
    
    /**
     * Show add form of speaker taxonomy
     * @author Webnus <info@webnus.biz>
     */
    public function add_form()
    {
    ?>
        <div class="form-field">
            <label for="mec_job_title"><?php _e('Job Title', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="job_title" placeholder="<?php esc_attr_e('Insert speaker job title.', 'modern-events-calendar-lite'); ?>" id="mec_job_title" value="" />
        </div>
        <div class="form-field">
            <label for="mec_tel"><?php _e('Tel', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="tel" placeholder="<?php esc_attr_e('Insert speaker phone number.', 'modern-events-calendar-lite'); ?>" id="mec_tel" value="" />
        </div>
        <div class="form-field">
            <label for="mec_email"><?php _e('Email', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="email" placeholder="<?php esc_attr_e('Insert speaker email address.', 'modern-events-calendar-lite'); ?>" id="mec_email" value="" />
        </div>
        <div class="form-field">
            <label for="mec_website"><?php _e('Website', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="website" placeholder="<?php esc_attr_e('Insert URL of Website', 'modern-events-calendar-lite'); ?>" id="mec_website" value="" />
        </div>
        <div class="form-field">
            <label for="mec_facebook"><?php _e('Facebook Page', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="facebook" placeholder="<?php esc_attr_e('Insert URL of Facebook Page', 'modern-events-calendar-lite'); ?>" id="mec_facebook" value="" />
        </div>
        <div class="form-field">
            <label for="mec_instagram"><?php _e('Instagram', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="instagram" placeholder="<?php esc_attr_e('Insert URL of Instagram', 'modern-events-calendar-lite'); ?>" id="mec_instagram" value="" />
        </div>
        <div class="form-field">
            <label for="mec_linkedin"><?php _e('LinkedIn', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="linkedin" placeholder="<?php esc_attr_e('Insert URL of linkedin', 'modern-events-calendar-lite'); ?>" id="mec_linkedin" value="" />
        </div>
        <div class="form-field">
            <label for="mec_twitter"><?php _e('Twitter Page', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="twitter" placeholder="<?php esc_attr_e('Insert URL of Twitter Page', 'modern-events-calendar-lite'); ?>" id="mec_twitter" value="" />
        </div>
        <div class="form-field">
            <label for="mec_thumbnail_button"><?php _e('Thumbnail', 'modern-events-calendar-lite'); ?></label>
            <div id="mec_thumbnail_img"></div>
            <input type="hidden" name="thumbnail" id="mec_thumbnail" value="" />
            <button type="button" class="mec_upload_image_button button" id="mec_thumbnail_button"><?php echo __('Upload/Add image', 'modern-events-calendar-lite'); ?></button>
            <button type="button" class="mec_remove_image_button button mec-util-hidden"><?php echo __('Remove image', 'modern-events-calendar-lite'); ?></button>
        </div>
        <?php do_action('mec_add_speaker_extra_fields'); ?>
    <?php
    }
    
    /**
     * Save meta data of speaker taxonomy
     * @author Webnus <info@webnus.biz>
     * @param int $term_id
     */
    public function save_metadata($term_id)
    {
        // Quick Edit
        if(!isset($_POST['job_title'])) return;

        $job_title  = isset($_POST['job_title']) ? sanitize_text_field($_POST['job_title']) : '';
        $tel        = isset($_POST['tel']) ? sanitize_text_field($_POST['tel']) : '';
        $email      = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : '';
        $website   = (isset($_POST['website']) and trim($_POST['website'])) ? (strpos($_POST['website'], 'http') === false ? 'http://'.sanitize_text_field($_POST['website']) : sanitize_text_field($_POST['website'])) : '';
        $facebook   = (isset($_POST['facebook']) and trim($_POST['facebook'])) ? (strpos($_POST['facebook'], 'http') === false ? 'http://'.sanitize_text_field($_POST['facebook']) : sanitize_text_field($_POST['facebook'])) : '';
        $twitter    = (isset($_POST['twitter']) and trim($_POST['twitter'])) ? (strpos($_POST['twitter'], 'http') === false ? 'http://'.sanitize_text_field($_POST['twitter']) : sanitize_text_field($_POST['twitter'])) : '';
        $instagram  = (isset($_POST['instagram']) and trim($_POST['instagram'])) ? (strpos($_POST['instagram'], 'http') === false ? 'http://'.sanitize_text_field($_POST['instagram']) : sanitize_text_field($_POST['instagram'])) : '';
        $linkedin   = (isset($_POST['linkedin']) and trim($_POST['linkedin'])) ? (strpos($_POST['linkedin'], 'http') === false ? 'http://'.sanitize_text_field($_POST['linkedin']) : sanitize_text_field($_POST['linkedin'])) : '';
        $thumbnail  = isset($_POST['thumbnail']) ? sanitize_text_field($_POST['thumbnail']) : '';
        
        update_term_meta($term_id, 'job_title', $job_title);
        update_term_meta($term_id, 'tel', $tel);
        update_term_meta($term_id, 'email', $email);
        update_term_meta($term_id, 'website', $website);
        update_term_meta($term_id, 'facebook', $facebook);
        update_term_meta($term_id, 'twitter', $twitter);
        update_term_meta($term_id, 'instagram', $instagram);
        update_term_meta($term_id, 'linkedin', $linkedin);
        update_term_meta($term_id, 'thumbnail', $thumbnail);

        do_action('mec_save_speaker_extra_fields', $term_id);
    }
    
    /**
     * Filter columns of speaker taxonomy
     * @author Webnus <info@webnus.biz>
     * @param array $columns
     * @return array
     */
    public function filter_columns($columns)
    {
        unset($columns['name']);
        unset($columns['slug']);
        unset($columns['description']);
        unset($columns['posts']);
        
        $columns['id'] = __('ID', 'modern-events-calendar-lite');
        $columns['name'] = $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite'));
        $columns['job_title'] = __('Job Title', 'modern-events-calendar-lite');
        $columns['tel'] = __('Tel', 'modern-events-calendar-lite');
        $columns['posts'] = __('Count', 'modern-events-calendar-lite');

        return apply_filters('speaker_filter_column', $columns);
    }
    
    /**
     * Filter content of speaker taxonomy columns
     * @author Webnus <info@webnus.biz>
     * @param string $content
     * @param string $column_name
     * @param int $term_id
     * @return string
     */
    public function filter_columns_content($content, $column_name, $term_id)
    {
        switch($column_name)
        {
            case 'id':
                
                $content = $term_id;
                break;

            case 'tel':

                $content = get_metadata('term', $term_id, 'tel', true);

                break;

            case 'job_title':

                $content = get_metadata('term', $term_id, 'job_title', true);

                break;

            default:
                break;
        }

        return apply_filters('speaker_filter_column_content', $content, $column_name, $term_id);
    }

    /**
     * Adding new speaker
     * @author Webnus <info@webnus.biz>
     * @return string json
     */
    public function fes_speaker_adding()
    {
        $request = $this->getRequest();
        $content = $request->getVar('content', NULL);
        $key = $request->getVar('key', NULL);

        $content = wp_strip_all_tags($content);
        $content = sanitize_text_field($content);
        $key = intval($key);

        if(!trim($content))
        {
            echo '<p class="mec-error" id="mec-speaker-error-' . $key . '">' . __('Sorry, You must insert speaker name!', 'modern-events-calendar-lite') . '</p>';
            exit;
        }

        $content = explode(',', $content);

        foreach($content as $term)
        {
            if(term_exists($term, 'mec_speaker'))
            {
                echo '<p class="mec-error" id="mec-speaker-error-' . $key . '">' . __("Sorry, {$term} already exists!", 'modern-events-calendar-lite') . '</p>';
                exit;
            }
        }

        foreach($content as $term) wp_insert_term(trim($term), 'mec_speaker');

        $speakers = '';
        $speaker_terms = get_terms(array('taxonomy'=>'mec_speaker', 'hide_empty'=>false));
        foreach($speaker_terms as $speaker_term)
        {
            $speakers .= '<label for="mec_fes_speakers'.$speaker_term->term_id.'">
                <input type="checkbox" name="mec[speakers]['.$speaker_term->term_id.']" id="mec_fes_speakers'.$speaker_term->term_id.'" value="1">
                '.$speaker_term->name.'
            </label>';
        }

        echo $speakers;
        exit;
    }

    public function show_notics($screen)
    {
        if(isset($screen->id) and $screen->id == 'edit-mec_speaker')
        {
            add_action('admin_footer', function ()
            {
                echo "<script>
                    var xhrObject = window.XMLHttpRequest;
                    function ajaxXHR()
                    {
                        var xmlHttp = new xhrObject();
                        xmlHttp.addEventListener('readystatechange', function (xhr)
                        {
                            if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
                            {
                                if(xhr.currentTarget.responseText.indexOf('tr') != -1)
                                {
                                    jQuery('.form-wrap').find('.warning-msg').remove();
                                    jQuery('.form-wrap').append('<div class=\"warning-msg\"><p>" . __('Note: You can use the speakers in your event edit/add page > hourly schedule section and speaker widget section!', 'modern-events-calendar-lite') . "</p></div>');
                                }
                            }
                        });
                        
                        return xmlHttp;
                    }
                    window.XMLHttpRequest = ajaxXHR;
                </script>";
            });
        }
    }
}
