<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC colors class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_colors extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;

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
    }
    
    /**
     * Initialize colors feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        $this->factory->action('add_meta_boxes', array($this, 'register_meta_boxes'));
        $this->factory->action('save_post', array($this, 'save_event'), 3);
    }
    
    /**
     * Registers color meta box
     * @author Webnus <info@webnus.biz>
     */
    public function register_meta_boxes()
    {
        add_meta_box('mec_metabox_color', __('Event Color', 'modern-events-calendar-lite'), array($this, 'meta_box_colors'), $this->main->get_main_post_type(), 'side');
    }

    public function mec_hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);
        if(strlen($hex) == 3)
        {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        }
        else
        {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }

        return array($r, $g, $b);
     }
    
    /**
     * Show color meta box content
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_colors($post)
    {
        $color = get_post_meta($post->ID, 'mec_color', true);
        $available_colors = $this->main->get_available_colors();
    ?>
        <div class="mec-meta-box-colors-container">
            <div class="mec-form-row">
                <input type="text" id="mec_event_color" name="mec[color]" value="#<?php echo $color; ?>" data-default-color="#<?php echo $color; ?>" class="mec-color-picker" />
            </div>
            <div class="mec-form-row mec-available-color-row">
                <div class="mec-recent-color-sec" style="display: none"><?php echo __('Recent Colors', 'modern-events-calendar-lite'); ?></div>
                <?php foreach($available_colors as $available_color): $rgba_array = $this->mec_hex2rgb('#'.$available_color); ?>
                <span class="mec-recent-color-sec-wrap">
                    <?php if(!empty($rgba_array)) echo '<span class="mec-color-meta-box-popup" style="display: none;background-color: rgba('.$rgba_array[0].','.$rgba_array[1].','.$rgba_array[2].',0.14);"></span>'; ?>
                    <span class="mec-color" onclick="mec_set_event_color('<?php echo $available_color; ?>');" style="background-color: #<?php echo $available_color; ?>"></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php
    }
    
    /**
     * Save color of event
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return void
     */
    public function save_event($post_id)
    {
        // Check if our nonce is set.
        if(!isset($_POST['mec_event_nonce'])) return;

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['mec_event_nonce']), 'mec_event_data')) return;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if(defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return;

        // Get Modern Events Calendar Data
        $_mec = isset($_POST['mec']) ? $_POST['mec'] : array();
        
        $color = isset($_mec['color']) ? trim(sanitize_text_field($_mec['color']), '# ') : '';
        update_post_meta($post_id, 'mec_color', $color);
        
        // Add the new color to available colors
        if(trim($color)) $this->main->add_to_available_colors($color);
    }
}