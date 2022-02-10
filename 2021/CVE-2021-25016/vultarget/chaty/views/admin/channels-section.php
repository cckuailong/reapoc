<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div style="display: none">
    <?php
    $embedded_message = "";
    $settings = array(
        'media_buttons' => false,
        'wpautop' => false,
        'drag_drop_upload' => false,
        'textarea_name' => 'chat_editor_channel',
        'textarea_rows' => 4,
        'quicktags' => false,
        'tinymce'       => array(
            'toolbar1'      => 'bold, italic, underline',
            'toolbar2'  => '',
            'toolbar3'  => ''
        )
    );
    wp_editor($embedded_message, "chat_editor_channel", $settings );
    ?>
</div>
<svg class="read-only" aria-hidden="true" width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="linear-gradient" x1="0.892" y1="0.192" x2="0.128" y2="0.85" gradientUnits="objectBoundingBox">
            <stop offset="0" stop-color="#4a64d5"></stop>
            <stop offset="0.322" stop-color="#9737bd"></stop>
            <stop offset="0.636" stop-color="#f15540"></stop>
            <stop offset="1" stop-color="#fecc69"></stop>
        </linearGradient>
    </defs>
    <circle class="color-element" cx="19.5" cy="19.5" r="19.5" fill="url(#linear-gradient)"></circle>
    <path id="Path_1923" data-name="Path 1923" d="M13.177,0H5.022A5.028,5.028,0,0,0,0,5.022v8.155A5.028,5.028,0,0,0,5.022,18.2h8.155A5.028,5.028,0,0,0,18.2,13.177V5.022A5.028,5.028,0,0,0,13.177,0Zm3.408,13.177a3.412,3.412,0,0,1-3.408,3.408H5.022a3.411,3.411,0,0,1-3.408-3.408V5.022A3.412,3.412,0,0,1,5.022,1.615h8.155a3.412,3.412,0,0,1,3.408,3.408v8.155Z" transform="translate(10 10.4)" fill="#fff"></path>
    <path id="Path_1924" data-name="Path 1924" d="M45.658,40.97a4.689,4.689,0,1,0,4.69,4.69A4.695,4.695,0,0,0,45.658,40.97Zm0,7.764a3.075,3.075,0,1,1,3.075-3.075A3.078,3.078,0,0,1,45.658,48.734Z" transform="translate(-26.558 -26.159)" fill="#fff"></path>
    <path id="Path_1925" data-name="Path 1925" d="M120.105,28.251a1.183,1.183,0,1,0,.838.347A1.189,1.189,0,0,0,120.105,28.251Z" transform="translate(-96.119 -14.809)" fill="#fff"></path>
</svg>
<div class="preview-section-overlay"></div>
<div class="preview-section-chaty  wrap">
    <svg class="read-only" aria-hidden="true" width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="linear-gradient" x1="0.892" y1="0.192" x2="0.128" y2="0.85" gradientUnits="objectBoundingBox">
                <stop offset="0" stop-color="#4a64d5"></stop>
                <stop offset="0.322" stop-color="#9737bd"></stop>
                <stop offset="0.636" stop-color="#f15540"></stop>
                <stop offset="1" stop-color="#fecc69"></stop>
            </linearGradient>
        </defs>
        <circle class="color-element" cx="19.5" cy="19.5" r="19.5" fill="url(#linear-gradient)"></circle>
        <path id="Path_1923" data-name="Path 1923" d="M13.177,0H5.022A5.028,5.028,0,0,0,0,5.022v8.155A5.028,5.028,0,0,0,5.022,18.2h8.155A5.028,5.028,0,0,0,18.2,13.177V5.022A5.028,5.028,0,0,0,13.177,0Zm3.408,13.177a3.412,3.412,0,0,1-3.408,3.408H5.022a3.411,3.411,0,0,1-3.408-3.408V5.022A3.412,3.412,0,0,1,5.022,1.615h8.155a3.412,3.412,0,0,1,3.408,3.408v8.155Z" transform="translate(10 10.4)" fill="#fff"></path>
        <path id="Path_1924" data-name="Path 1924" d="M45.658,40.97a4.689,4.689,0,1,0,4.69,4.69A4.695,4.695,0,0,0,45.658,40.97Zm0,7.764a3.075,3.075,0,1,1,3.075-3.075A3.078,3.078,0,0,1,45.658,48.734Z" transform="translate(-26.558 -26.159)" fill="#fff"></path>
        <path id="Path_1925" data-name="Path 1925" d="M120.105,28.251a1.183,1.183,0,1,0,.838.347A1.189,1.189,0,0,0,120.105,28.251Z" transform="translate(-96.119 -14.809)" fill="#fff"></path>
    </svg>
    <div class="preview" id="admin-preview">
    <div class="h2"><span class="header-tooltip"><span class="header-tooltip-text">Please make all your live tests in <a href="https://www.computerworld.com/article/3356840/how-to-go-incognito-in-chrome-firefox-safari-and-edge.html" target="_blank">incognito mode</a>. Some of the features like call to action, attention effect, and trigger will appear for your website visitors just once until they engage with the chat widget, so to emulate it you'll need to use incognito mode</span> <span class="dashicons dashicons-editor-help"></span></span><?php esc_attr_e('Preview', CHT_OPT); ?>:</div>

    <div class="page">
        <div class="page-header">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <svg width="33" height="38" viewBox="0 0 33 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_d)">
                    <ellipse cx="0.855225" cy="0.745508" rx="0.855225" ry="0.745508" transform="translate(15.6492 13.0053) scale(1 -1)" fill="#828282"/>
                </g>
                <g filter="url(#filter1_d)">
                    <ellipse cx="0.855225" cy="0.745508" rx="0.855225" ry="0.745508" transform="translate(15.6492 15.6891) scale(1 -1)" fill="#828282"/>
                </g>
                <g filter="url(#filter2_d)">
                    <ellipse cx="0.855225" cy="0.745508" rx="0.855225" ry="0.745508" transform="translate(15.6492 18.373) scale(1 -1)" fill="#828282"/>
                </g>
                <defs>
                    <filter id="filter0_d" x="0.64917" y="0.514328" width="31.7105" height="31.491" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 255 0"/>
                        <feOffset dy="4"/>
                        <feGaussianBlur stdDeviation="7.5"/>
                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/>
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/>
                    </filter>
                    <filter id="filter1_d" x="0.64917" y="3.1981" width="31.7105" height="31.491" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 255 0"/>
                        <feOffset dy="4"/>
                        <feGaussianBlur stdDeviation="7.5"/>
                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/>
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/>
                    </filter>
                    <filter id="filter2_d" x="0.64917" y="5.88202" width="31.7105" height="31.491" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 255 0"/>
                        <feOffset dy="4"/>
                        <feGaussianBlur stdDeviation="7.5"/>
                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/>
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/>
                    </filter>
                </defs>
            </svg>
        </div>


        <?php $position = $this->get_position_style(); ?>
        <div class="page-body">
            <?php
            $cht_position = get_option("cht_position");
            $cht_position = ($cht_position == "custom")?"positionSide":"cht_position";
            $cht_position = get_option($cht_position);
            $cht_cta = get_option("cht_cta");
            $cht_cta = ($cht_cta)?$cht_cta:"Contact Us";
            $widget_icon = get_option('widget_icon');
            ?>
            <div class="chaty-widget chaty-widget-icons-<?php esc_attr_e($cht_position) ?>" style="<?php esc_attr_e($position); ?>">
                <div class="icon icon-xs active tooltip-show tooltip" data-label="<?php echo esc_attr($cht_cta) ?>" data-label="<?php echo esc_attr($cht_cta) ?>">
                    <div class="chaty-channels"></div>
                    <i id="iconWidget">
                        <?php $bg_color = $this->get_current_color(); ?>
                        <?php $fill = ($bg_color) ? $bg_color : '#A886CD'; ?>
                        <?php if ($widget_icon == 'chat-base'): ?>
                            <svg version="1.1" id="ch" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54" style="enable-background:new -496 507.7 54 54;" xml:space="preserve">
                                <style type="text/css">
                                    .st1 { fill: #FFFFFF; }
                                    .st0 { fill: #808080; }
                                </style>
                                <g><circle cx="-469" cy="534.7" r="27" fill="<?php esc_attr_e($fill) ?>"/></g>
                                <path class="st1" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"/>
                                <path class="st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,530.8-478.2,530.5-477.7,530.5z"/>
                                <path class="st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,533.9-478.2,533.5-477.7,533.5z"/>
                            </svg>
                        <?php endif; ?>
                        <?php if ($widget_icon == 'chat-smile'): ?>
                            <svg version="1.1" id="smile" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496.8 507.1 54 54" style="enable-background:new -496.8 507.1 54 54;" xml:space="preserve" >
                                <style type="text/css">
                                    .st1 { fill: #FFFFFF; }
                                    .st2 { fill: none; stroke: #808080; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }
                                </style>
                                <g>
                                    <circle cx="-469.8" cy="534.1" r="27" fill="<?php esc_attr_e($fill) ?>"/>
                                </g>
                                <path class="st1" d="M-459.5,523.5H-482c-2.1,0-3.7,1.7-3.7,3.7v13.1c0,2.1,1.7,3.7,3.7,3.7h19.3l5.4,5.4c0.2,0.2,0.4,0.2,0.7,0.2 c0.2,0,0.2,0,0.4,0c0.4-0.2,0.6-0.6,0.6-0.9v-21.5C-455.8,525.2-457.5,523.5-459.5,523.5z"/>
                                <path class="st2" d="M-476.5,537.3c2.5,1.1,8.5,2.1,13-2.7"/>
                                <path class="st2" d="M-460.8,534.5c-0.1-1.2-0.8-3.4-3.3-2.8"/>
                            </svg>
                        <?php endif; ?>
                        <?php if ($widget_icon == 'chat-bubble'): ?>
                            <?php $fill = ($bg_color) ? $bg_color : '#A886CD'; ?>
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496.9 507.1 54 54" style="enable-background:new -496.9 507.1 54 54;" xml:space="preserve">
                                <style type="text/css">.st1 {
                                    fill: #FFFFFF;
                                }</style>
                                <g>
                                    <circle cx="-469.9" cy="534.1" r="27" fill="<?php esc_attr_e($fill) ?>"/>
                                </g>
                                <path class="st1" d="M-472.6,522.1h5.3c3,0,6,1.2,8.1,3.4c2.1,2.1,3.4,5.1,3.4,8.1c0,6-4.6,11-10.6,11.5v4.4c0,0.4-0.2,0.7-0.5,0.9 c-0.2,0-0.2,0-0.4,0c-0.2,0-0.5-0.2-0.7-0.4l-4.6-5c-3,0-6-1.2-8.1-3.4s-3.4-5.1-3.4-8.1C-484.1,527.2-478.9,522.1-472.6,522.1z M-462.9,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-464.6,534.6-463.9,535.3-462.9,535.3z M-469.9,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-471.7,534.6-471,535.3-469.9,535.3z M-477,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-478.8,534.6-478.1,535.3-477,535.3z"/>
                            </svg>
                        <?php endif; ?>
                        <?php if ($widget_icon == 'chat-db'): ?>
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.1 54 54" style="enable-background:new -496 507.1 54 54;" xml:space="preserve">
                                <style type="text/css">.st1 {
                                    fill: #FFFFFF;
                                }</style>
                                <g>
                                    <circle cx="-469" cy="534.1" r="27" fill="<?php esc_attr_e($fill) ?>"/>
                                </g>
                                <path class="st1" d="M-464.6,527.7h-15.6c-1.9,0-3.5,1.6-3.5,3.5v10.4c0,1.9,1.6,3.5,3.5,3.5h12.6l5,5c0.2,0.2,0.3,0.2,0.7,0.2 c0.2,0,0.2,0,0.3,0c0.3-0.2,0.5-0.5,0.5-0.9v-18.2C-461.1,529.3-462.7,527.7-464.6,527.7z"/>
                                <path class="st1" d="M-459.4,522.5H-475c-1.9,0-3.5,1.6-3.5,3.5h13.9c2.9,0,5.2,2.3,5.2,5.2v11.6l1.9,1.9c0.2,0.2,0.3,0.2,0.7,0.2 c0.2,0,0.2,0,0.3,0c0.3-0.2,0.5-0.5,0.5-0.9v-18C-455.9,524.1-457.5,522.5-459.4,522.5z"/>
                            </svg>
                        <?php endif; ?>
                        <?php if ($widget_icon == 'chat-image'): ?>
                            <img src="<?php echo esc_url($this->getCustomWidgetImg()); ?>"/>
                        <?php else: ?>
                            <style type="text/css">.st1 {fill: #FFFFFF;}.st0{fill: #a886cd;}</style>
                            <g><circle cx="-469" cy="534.7" r="27" fill="<?php esc_attr_e($fill) ?>"/></g>
                            <path class="st1" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"/>
                            <path class="st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,530.8-478.2,530.5-477.7,530.5z"/>
                            <path class="st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,533.9-478.2,533.5-477.7,533.5z"/>
                            </svg>
                        <?php endif; ?>
                    </i>
                    <i class="chaty-close-icon">
                        <svg viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="26" cy="26" rx="26" ry="26" fill="#A886CD"></ellipse><rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(18.35 15.6599) scale(0.998038 1.00196) rotate(45)" fill="white"></rect><rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(37.5056 18.422) scale(0.998038 1.00196) rotate(135)" fill="white"></rect></svg>
                    </i>
	                <?php
	                $display_status = get_option("cht_cta".$this->widget_index);
	                $display_status = ($display_status)?"block":"none";
	                $cta = get_option('cht_cta'.$this->widget_index);
	                //$cta = str_replace(array("\r", "\n"), "", $cta);
	                $cta = html_entity_decode($cta);
	                ?>
                    <span id="cta-box" class="tooltiptext" style='display:<?php esc_attr_e($display_status) ?>'><span><?php esc_attr_e($cta); ?></span></span>
                </div>
            </div>
        </div>
    </div>
    <div class="switch-preview">
        <input data-gramm_editor="false" type="radio" id="previewDesktop" name="switchPreview" class="js-switch-preview switch-preview__input" checked="checked">
        <label for="previewDesktop" class="switch-preview__label switch-preview__desktop">
            <?php esc_attr_e('Desktop', CHT_OPT); ?>
        </label>
        <input data-gramm_editor="false" type="radio" id="previewMobile" name="switchPreview" class="js-switch-preview switch-preview__input">
        <label for="previewMobile" class="switch-preview__label switch-preview__mobile">
            <?php esc_attr_e('Mobile', CHT_OPT); ?>
        </label>
    </div>
</div>
</div>
<?php $pro_id = $this->is_pro()?"pro":""; ?>
<section class="section one chaty-setting-form" id="<?php esc_attr_e($pro_id) ?>" xmlns="http://www.w3.org/1999/html">
	<?php //if($this->widget_index != "" || $this->get_total_widgets() > 0) {
	$cht_widget_title = get_option("cht_widget_title");
	if(isset($_GET['widget_title']) && empty(!$_GET['widget_title'])) {
		$cht_widget_title = $_GET['widget_title'];
	}
	?>
    <div class="chaty-input">
        <label for="cht_widget_title"><?php esc_attr_e('Name', CHT_OPT); ?></label>
        <input id="cht_widget_title" type="text" name="cht_widget_title" value="<?php esc_attr_e($cht_widget_title) ?>">
    </div>
	<?php // } ?>
    <h1 class="section-title">
        <strong><?php esc_attr_e('Step', CHT_OPT); ?> 1:</strong> <?php esc_attr_e('Choose your channels', CHT_OPT); ?>
    </h1>
    <?php
    $social_app = get_option('cht_numb_slug');
    $social_app = trim($social_app, ",");
    $social_app = explode(",", $social_app);
    $social_app = array_unique($social_app);
    $imageUrl = plugin_dir_url("")."chaty/admin/assets/images/chaty-default.png";
    ?>
    <input type="hidden" id="default_image" value="<?php echo esc_url($imageUrl)  ?>" />
    <div class="channels-icons" id="channel-list">
        <?php if ($this->socials) :
            foreach ($this->socials as $key => $social) :
                $value = get_option('cht_social'.'_' . $social['slug']);
                $active_class = '';
                foreach ($social_app as $key_soc):
                    if ($key_soc == $social['slug']) {
                        $active_class = 'active';
                    }
                endforeach; ?>
                <div class="icon icon-sm chat-channel-<?php echo esc_attr($social['slug']); ?> <?php echo esc_attr($active_class) ?>" data-social="<?php echo esc_attr($social['slug']); ?>" data-label="<?php esc_attr_e($social['title']); ?>" >
                    <span class="icon-box">
                        <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <?php echo $social['svg']; ?>
                        </svg>
                        <?php if($social['slug'] == "Contact_Us") {
                            echo "<span>".esc_attr("Contact Form")."</span>";
                        } ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if (!$this->is_pro()) : ?>
    <div class="popover" style="">
        <a class="upgrade-link" target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl()); ?>">
            You can use any two channels in the free version.<br/>Get unlimited channels in the Pro plan
            <strong><?php esc_attr_e('Upgrade Now', CHT_OPT); ?></strong>
        </a>
    </div>
    <?php endif; ?>
    <input type="hidden" class="add_slug" name="cht_numb_slug" placeholder="test" value="<?php esc_attr_e(get_option('cht_numb_slug')); ?>" id="cht_numb_slug" >

    <div class="channels-selected" id="channels-selected-list">
        <ul id="channels-selected-list" class="channels-selected-list channels-selected">
            <?php if ($this->socials) {
                $social = get_option('cht_numb_slug');
                $social = explode(",", $social);
                $social = array_unique($social);
                foreach ($social as $key_soc){
                    foreach ($this->socials as $key => $social) {
                        if ($social['slug'] != $key_soc) {                          // compare social media slug
                            continue;
                        }
                        $value = get_option('cht_social'.$this->widget_index.'_' . $social['slug']);       // get setting for media if already saved
                        $imageUrl = plugin_dir_url("")."chaty/admin/assets/images/chaty-default.png";
                        if (empty($value)) {                                        // Initialize default values if not found
                            $value = [
                                'value' => '',
                                'is_mobile' => 'checked',
                                'is_desktop' => 'checked',
                                'image_id' => '',
                                'title' => $social['title'],
                                'bg_color' => "",
                            ];
                        }
                        if(!isset($value['bg_color']) || empty($value['bg_color'])) {
                            $value['bg_color'] = $social['color'];                  // Initialize background color value if not exists. 2.1.0 change
                        }
                        if(!isset($value['image_id'])) {
                            $value['image_id'] = '';                                // Initialize custom image id if not exists. 2.1.0 change
                        }
                        if(!isset($value['title'])) {
                            $value['title'] = $social['title'];                     // Initialize title if not exists. 2.1.0 change
                        }
                        if(!isset($value['fa_icon'])) {
                            $value['fa_icon'] = "";                     // Initialize title if not exists. 2.1.0 change
                        }
                        if(!isset($value['value'])) {
                            $value['value'] = "";                     // Initialize title if not exists. 2.1.0 change
                        }
                        $imageId = $value['image_id'];
                        $status = 0;
                        if(!empty($imageId)) {
                            $imageUrl = wp_get_attachment_image_src($imageId, "full")[0];                       // get custom image URL if exists
                            $status = 1;
                        }
                        if($imageUrl == "") {
                            $imageUrl = plugin_dir_url("")."chaty/admin/assets/images/chaty-default.png";   // Initialize with default image if custom image is not exists
                            $status = 0;
                            $imageId = "";
                        }
                        $color = "";
                        if(!empty($value['bg_color'])) {
                            $color = "background-color: ".$value['bg_color'];                                   // set background color of icon it it is exists
                        }
                        if($social['slug'] == "Whatsapp"){
                            $val = $value['value'];
                            $val = str_replace("+","", $val);
                            $value['value'] = $val;
                        } else if($social['slug'] == "Facebook_Messenger"){
                            $val = $value['value'];
                            $val = str_replace("facebook.com","m.me", $val);                                    // Replace facebook.com with m.me version 2.0.1 change
                            $val = str_replace("www.","", $val);                                                // Replace www. with blank version 2.0.1 change
                            $value['value'] = $val;

                            $val = trim($val, "/");
                            $val_array = explode("/", $val);
                            $total = count($val_array)-1;
                            $last_value = $val_array[$total];
                            $last_value = explode("-", $last_value);
                            $total_text = count($last_value)-1;
                            $total_text = $last_value[$total_text];

                            if(is_numeric($total_text)) {
                                $val_array[$total] = $total_text;
                                $value['value'] = implode("/", $val_array);
                            }
                        }
                        $value['value'] = esc_attr__(wp_unslash($value['value']));
                        $value['title'] = esc_attr__(wp_unslash($value['title']));

                        $svg_icon = $social['svg'];

                        $help_title = "";
                        $help_text = "";
                        $help_link = "";

                        if((isset($social['help']) && !empty($social['help'])) || isset($social['help_link'])) {
                            $help_title = isset($social['help_title'])?$social['help_title']:"Doesn't work?";
                            $help_text = isset($social['help'])?$social['help']:"";
                            if(isset($social['help_link']) && !empty($social['help_link'])) {
                                $help_link = $social['help_link'];
                            }
                        }

                        $channel_type = "";
                        $placeholder = $social['example'];
                        if($social['slug'] == "Link" || $social['slug'] == "Custom_Link" || $social['slug'] == "Custom_Link_3" || $social['slug'] == "Custom_Link_4" || $social['slug'] == "Custom_Link_5") {
                            if (isset($value['channel_type'])) {
                                $channel_type = esc_attr__(wp_unslash($value['channel_type']));
                            }

                            if(!empty($channel_type)) {
                                foreach($this->socials as $icon) {
                                    if($icon['slug'] == $channel_type) {
                                        $svg_icon = $icon['svg'];

                                        $placeholder = $icon['example'];

                                        if((isset($icon['help']) && !empty($icon['help'])) || isset($icon['help_link'])) {
                                            $help_title = isset($icon['help_title'])?$icon['help_title']:"Doesn't work?";
                                            $help_text = isset($icon['help'])?$icon['help']:"";
                                            if(isset($icon['help_link']) && !empty($icon['help_link'])) {
                                                $help_link = $icon['help_link'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if(empty($channel_type)) {
                            $channel_type = $social['slug'];
                        }
	                    if($channel_type == "Telegram") {
		                    $value['value'] = trim($value['value'], "@");
	                    }
                        ?>
                        <!-- Social media setting box: start -->
                        <li data-id="<?php echo esc_attr($social['slug']) ?>" class="chaty-channel" data-channel="<?php echo esc_attr($channel_type) ?>" id="chaty-social-<?php echo esc_attr($social['slug']) ?>">
                            <div class="channels-selected__item <?php esc_attr_e(($status)?"img-active":"") ?> <?php esc_attr_e(($this->is_pro()) ? 'pro' : 'free'); ?> 1 available">
                                <div class="chaty-default-settings">
                                    <div class="move-icon">
                                        <img src="<?php echo esc_url(plugin_dir_url("")."/chaty/admin/assets/images/move-icon.png") ?>">
                                    </div>
                                    <div class="icon icon-md active" data-label="<?php esc_attr_e($social['title']); ?>">
                                    <span style="" class="custom-chaty-image custom-image-<?php echo esc_attr($social['slug']) ?>" id="image_data_<?php echo esc_attr($social['slug']) ?>">
                                        <img src="<?php echo esc_url($imageUrl) ?>" />
                                        <span onclick="remove_chaty_image('<?php echo esc_attr($social['slug']) ?>')" class="remove-icon-img"></span>
                                    </span>
                                        <span class="default-chaty-icon <?php echo (isset($value['fa_icon'])&&!empty($value['fa_icon']))?"has-fa-icon":"" ?> custom-icon-<?php echo esc_attr($social['slug']) ?> default_image_<?php echo esc_attr($social['slug']) ?>" >
                                        <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <?php echo $svg_icon; ?>
                                        </svg>
                                        <span class="facustom-icon" style="background-color: <?php echo esc_attr($value['bg_color']) ?>"><i class="<?php echo esc_attr($value['fa_icon']) ?>"></i></span>
                                    </span>
                                    </div>

                                    <?php if($social['slug'] != 'Contact_Us') { ?>
                                        <!-- Social Media input  -->
	                                    <?php if(($social['slug'] == "Whatsapp" || $channel_type == "Whatsapp") && !empty($value['value'])) {
		                                    $value['value'] = trim($value['value'], "+");
		                                    $value['value'] = "+".$value['value'];
	                                    } ?>
                                        <div class="channels__input-box">
                                            <input data-label="<?php echo esc_attr($social['title']) ?>" placeholder="<?php esc_attr_e($placeholder); ?>" type="text" class="channels__input custom-channel-<?php echo esc_attr__($channel_type) ?> <?php echo isset($social['attr'])?$social['attr']:"" ?>" name="cht_social_<?php echo esc_attr($social['slug']); ?>[value]" value="<?php esc_attr_e(wp_unslash($value['value'])); ?>" data-gramm_editor="false" id="channel_input_<?php echo esc_attr($social['slug']); ?>" />
                                        </div>
                                    <?php } ?>
                                    <div class="channels__device-box">
                                        <?php
                                        $slug =  esc_attr__($this->del_space($social['slug']));
                                        $slug = str_replace(' ', '_', $slug);
                                        $is_desktop = isset($value['is_desktop']) && $value['is_desktop'] == "checked" ? "checked" : '';
                                        $is_mobile = isset($value['is_mobile']) && $value['is_mobile'] == "checked" ? "checked" : '';
                                        ?>
                                        <!-- setting for desktop -->
                                        <label class="channels__view" for="<?php echo esc_attr($slug); ?>Desktop">
                                            <input type="checkbox" id="<?php echo esc_attr($slug); ?>Desktop" class="channels__view-check js-chanel-icon js-chanel-desktop" data-type="<?php echo str_replace(' ', '_', strtolower(esc_attr__($this->del_space($social['slug'])))); ?>" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_desktop]" value="checked" data-gramm_editor="false" <?php esc_attr_e($is_desktop) ?> />
                                            <span class="channels__view-txt">Desktop</label>
                                        </label>

                                        <!-- setting for mobile -->
                                        <label class="channels__view" for="<?php echo esc_attr($slug); ?>Mobile">
                                            <input type="checkbox" id="<?php echo esc_attr($slug); ?>Mobile" class="channels__view-check js-chanel-icon js-chanel-mobile" data-type="<?php echo str_replace(' ', '_', strtolower(esc_attr__($this->del_space($social['slug'])))); ?>" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_mobile]" value="checked" data-gramm_editor="false" <?php esc_attr_e($is_mobile) ?> >
                                            <span class="channels__view-txt">Mobile</span>
                                        </label>
                                    </div>

                                    <?php if($social['slug'] == 'Contact_Us') { ?>
                                        <div class="channels__input transparent"></div>
                                    <?php } ?>

                                    <?php
                                    $close_class = "active";
                                    if($social['slug'] == 'Contact_Us') {
                                        $setting_status = get_option("chaty_contact_us_setting");
                                        if($setting_status === false) {
                                            $close_class = "";
                                        }
                                    }
                                    ?>


                                    <!-- button for advance setting -->
                                    <div class="chaty-settings <?php echo esc_attr($close_class) ?>" data-nonce="<?php echo wp_create_nonce($social['slug']."-settings") ?>" id="<?php echo esc_attr($social['slug']); ?>-close-btn" onclick="toggle_chaty_setting('<?php echo esc_attr($social['slug']); ?>')">
                                        <a href="javascript:;"><span class="dashicons dashicons-admin-generic"></span> Settings</a>
                                    </div>
                                    <?php if($social['slug'] != 'Contact_Us') { ?>

                                        <!-- example for social media -->
                                        <div class="input-example">
                                            <?php esc_attr_e('For example', CHT_OPT); ?>:
                                            <span class="inline-box channel-example">
                                                <?php if($social['slug'] == "Poptin") { ?>
                                                    <br/>
                                                <?php } ?>
                                                <?php esc_attr_e($placeholder); ?>
                                            </span>
                                        </div>

                                        <!-- checking for extra help message for social media -->
                                        <div class="help-section">
                                            <?php if((isset($social['help']) && !empty($social['help'])) || isset($social['help_link'])) { ?>
                                                <div class="viber-help">
                                                    <?php if(isset($help_link) && !empty($help_link)) { ?>
                                                        <a class="help-link" href="<?php echo esc_url($help_link) ?>" target="_blank"><?php esc_attr_e($help_title); ?></a>
                                                    <?php } else if(isset($help_text) && !empty($help_text)) { ?>
                                                        <span class="help-text"><?php echo $help_text; ?></span>
                                                        <span class="help-title"><?php esc_attr_e($help_title); ?></span>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php if($social['slug'] == "Whatsapp" || $social['slug'] == "Link" || $social['slug'] == "Custom_Link" || $social['slug'] == "Custom_Link_3" || $social['slug'] == "Custom_Link_4" || $social['slug'] == "Custom_Link_5") { ?>
                                    <div class="Whatsapp-settings advanced-settings extra-chaty-settings">
                                        <?php $embedded_window = isset($value['embedded_window'])?$value['embedded_window']:"no"; ?>
                                        <div class="chaty-setting-col">
                                            <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[embedded_window]" value="no" >
                                            <label class="chaty-switch full-width chaty-embedded-window" for="whatsapp_embedded_window_<?php echo esc_attr($social['slug']); ?>">
                                                <input type="checkbox" class="embedded_window-checkbox" name="cht_social_<?php echo esc_attr($social['slug']); ?>[embedded_window]" id="whatsapp_embedded_window_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($embedded_window, "yes") ?> >
                                                <div class="chaty-slider round"></div>
                                                WhatsApp Chat Popup &#128172;
                                                <div class="html-tooltip top">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                    <span class="tooltip-text top">
                                                        Show an embedded WhatsApp window to your visitors with a welcome message. Your users can start typing their own message and start a conversation with you right away once they are forwarded to the WhatsApp app.
                                                        <img src="<?php echo esc_url(CHT_PLUGIN_URL) ?>/admin/assets/images/whatsapp-popup.gif" />
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                        <!-- advance setting for Whatsapp -->
                                        <div class="whatsapp-welcome-message <?php echo ($embedded_window=="yes")?"active":"" ?>">
                                            <div class="chaty-setting-col">
                                                <label style="display: block; width: 100%" for="cht_social_embedded_message_<?php echo esc_attr($social['slug']); ?>">Welcome message</label>
                                                <div class="full-width">
                                                    <div class="full-width">
                                                        <?php $unique_id = uniqid(); ?>
                                                        <?php $embedded_message = isset($value['embedded_message'])?$value['embedded_message']:esc_html__("How can I help you? :)", "chaty"); ?>
                                                        <textarea class="chaty-setting-textarea chaty-whatsapp-setting-textarea" data-id="<?php echo esc_attr($unique_id) ?>" id="cht_social_embedded_message_<?php echo esc_attr($unique_id) ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[embedded_message]" ><?php echo $embedded_message ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chaty-setting-col">
                                                <?php $is_default_open = isset($value['is_default_open'])?$value['is_default_open']:""; ?>
                                                <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_default_open]" value="no" >
                                                <label class="chaty-switch" for="whatsapp_default_open_embedded_window_<?php echo esc_attr($social['slug']); ?>">
                                                    <input type="checkbox" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_default_open]" id="whatsapp_default_open_embedded_window_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($is_default_open, "yes") ?> >
                                                    <div class="chaty-slider round"></div>
                                                    Open the window on load
                                                    <span class="icon label-tooltip" data-label="Open the WhatsApp chat popup on page load, after the user sends a message or closes the window, the window will stay closed to avoid disruption"><span class="dashicons dashicons-editor-help"></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- advance setting fields: start -->
                                <?php $class_name = !$this->is_pro()?"not-is-pro":""; ?>
                                <div class="chaty-advance-settings <?php esc_attr_e($class_name); ?>" style="<?php echo (empty($close_class) && $social['slug'] == 'Contact_Us')?"display:block":""; ?>">
                                    <!-- Settings for custom icon and color -->
                                    <div class="chaty-setting-col">
                                        <label>Icon Appearance</label>
                                        <div>
                                            <!-- input for custom color -->
                                            <input type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[bg_color]" class="chaty-color-field" value="<?php esc_attr_e($value['bg_color']) ?>" />

                                            <!-- button to upload custom image -->
                                            <?php if($this->is_pro()) { ?>
                                                <a onclick="upload_chaty_image('<?php echo esc_attr($social['slug']); ?>')" href="javascript:;" class="upload-chaty-icon"><span class="dashicons dashicons-upload"></span> Custom Image</a>

                                                <!-- hidden input value for image -->
                                                <input id="cht_social_image_<?php echo esc_attr($social['slug']); ?>" type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[image_id]" value="<?php esc_attr_e($imageId) ?>" />
                                            <?php } else { ?>
                                                <div class="pro-features upload-image">
                                                    <div class="pro-item">
                                                        <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>" class="upload-chaty-icon"><span class="dashicons dashicons-upload"></span> Custom Image</a>
                                                    </div>
                                                    <div class="pro-button">
                                                        <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="clear clearfix"></div>

                                    <?php if($social['slug'] == "Link" || $social['slug'] == "Custom_Link" || $social['slug'] == "Custom_Link_3" || $social['slug'] == "4" || $social['slug'] == "Custom_Link_5") {
                                        $channel_type = "";
                                        if(isset($value['channel_type'])) {
                                            $channel_type = esc_attr__(wp_unslash($value['channel_type']));
                                        }
                                        $socials = $this->socials;
                                        ?>
                                        <div class="chaty-setting-col">
                                            <label>Channel type</label>
                                            <div>
                                                <!-- input for custom title -->
                                                <select class="channel-select-input" name="cht_social_<?php echo esc_attr($social['slug']); ?>[channel_type]" value="<?php esc_attr_e($value['channel_type']) ?>">
                                                    <option value="<?php echo esc_attr($social['slug']) ?>">Custom channel</option>
                                                    <?php foreach ($socials as $social_icon) {
                                                        $selected = ($social_icon['slug'] == $channel_type)?"selected":"";
                                                        if ($social_icon['slug'] != 'Custom_Link' && $social_icon['slug'] != 'Custom_Link_3' && $social_icon['slug'] != 'Custom_Link_4' && $social_icon['slug'] != 'Custom_Link_5' && $social_icon['slug'] != 'Contact_Us' && $social_icon['slug'] != 'Link') { ?>
                                                            <option <?php echo esc_attr($selected) ?> value="<?php echo esc_attr($social_icon['slug']) ?>"><?php echo esc_attr($social_icon['title']) ?></option>
                                                        <?php }
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="clear clearfix"></div>
                                    <?php } ?>

                                    <div class="chaty-setting-col">
                                        <label>On Hover Text</label>
                                        <div>
                                            <input type="text" class="chaty-title" name="cht_social_<?php echo esc_attr($social['slug']); ?>[title]" value="<?php esc_attr_e($value['title']) ?>">
                                        </div>
                                    </div>
                                    <div class="clear clearfix"></div>

                                    <div class="Contact_Us-settings advanced-settings">
                                        <div class="clear clearfix"></div>
                                        <div class="chaty-setting-col">
                                            <label>Contact Form Title</label>
                                            <div>
                                                <?php $contact_form_title = isset($value['contact_form_title'])?$value['contact_form_title']:esc_html__("Contact Us", "chaty"); ?>
                                                <input id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[contact_form_title]" value="<?php esc_attr_e($contact_form_title) ?>" >
                                            </div>
                                        </div>
                                        <?php
                                        $fields = array(
                                            'name' => array(
                                                'title' => "Name",
                                                'placeholder' => "Enter your name",
                                                'is_required' => 1,
                                                'type' => 'input',
                                                'is_enabled' => 1
                                            ),
                                            'email' => array(
                                                'title' => "Email",
                                                'placeholder' => "Enter your email address",
                                                'is_required' => 1,
                                                'type' => 'email',
                                                'is_enabled' => 1
                                            ),
                                            'phone' => array(
	                                            'title' => "Phone",
	                                            'placeholder' => "Enter your phone number",
	                                            'is_required' => 1,
	                                            'type' => 'input',
	                                            'is_enabled' => 1
                                            ),
                                            'message' => array(
                                                'title' => "Message",
                                                'placeholder' => "Enter your message",
                                                'is_required' => 1,
                                                'type' => 'textarea',
                                                'is_enabled' => 1
                                            )
                                        );
                                        echo '<div class="form-field-setting-col">';
                                        foreach ($fields as $label => $field) {
                                            $saved_value = isset($value[$label])?$value[$label]:array();
                                            $field_value = array(
                                                'is_active' => (isset($saved_value['is_active']))?$saved_value['is_active']:'yes',
                                                'is_required' => (isset($saved_value['is_required']))?$saved_value['is_required']:'yes',
                                                'placeholder' => (isset($saved_value['placeholder']))?$saved_value['placeholder']:$field['placeholder'],
                                            );
                                            ?>
                                            <div class="field-setting-col">
                                                <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_active]" value="no">
                                                <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_required]" value="no">

                                                <div class="left-section">
                                                    <label class="chaty-switch chaty-switch-toggle" for="field_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">
                                                        <input type="checkbox" class="chaty-field-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_active]" id="field_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>" value="yes" <?php checked($field_value['is_active'], "yes") ?>>
                                                        <div class="chaty-slider round"></div>

                                                        <?php echo $field['title'] ?>
                                                    </label>
                                                </div>
                                                <div class="right-section">
                                                    <div class="field-settings <?php echo ($field_value['is_active']=="yes")?"active":"" ?>">
                                                        <div class="inline-block">
                                                            <label class="inline-block" for="field_required_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">Required?</label>
                                                            <div class="inline-block">
                                                                <label class="chaty-switch" for="field_required_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">
                                                                    <input type="checkbox" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_required]" id="field_required_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>" value="yes" <?php checked($field_value['is_required'], "yes") ?>>
                                                                    <div class="chaty-slider round"></div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clear clearfix"></div>
                                                <div class="field-settings <?php echo ($field_value['is_active']=="yes")?"active":"" ?>">
                                                    <div class="chaty-setting-col">
                                                        <label for="placeholder_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">Placeholder text</label>
                                                        <div>
                                                            <input id="placeholder_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][placeholder]" value="<?php esc_attr_e($field_value['placeholder']) ?>" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if($label != 'message') { ?>
                                                <div class="chaty-separator"></div>
                                            <?php } ?>
                                        <?php }
                                        echo '</div>'; ?>
                                        <div class="form-field-setting-col">
                                            <div class="form-field-title">Submit Button</div>
                                            <div class="color-box">
                                                <div class="clr-setting">
                                                    <?php $field_value = isset($value['button_text_color'])?$value['button_text_color']:"#ffffff" ?>
                                                    <div class="chaty-setting-col">
                                                        <label for="button_text_color_for_<?php echo esc_attr($social['slug']); ?>">Text color</label>
                                                        <div>
                                                            <input id="button_text_color_for_<?php echo esc_attr($social['slug']); ?>" class="chaty-color-field button-color" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[button_text_color]" value="<?php esc_attr_e($field_value); ?>" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $field_value = isset($value['button_bg_color'])?$value['button_bg_color']:"#A886CD" ?>
                                                <div class="clr-setting">
                                                    <div class="chaty-setting-col">
                                                        <label for="button_bg_color_for_<?php echo esc_attr($social['slug']); ?>">Background color</label>
                                                        <div>
                                                            <input id="button_bg_color_for_<?php echo esc_attr($social['slug']); ?>" class="chaty-color-field button-color" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[button_bg_color]" value="<?php esc_attr_e($field_value); ?>" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $field_value = isset($value['button_text'])?$value['button_text']:"Chat" ?>
                                            <div class="chaty-setting-col">
                                                <label for="button_text_for_<?php echo esc_attr($social['slug']); ?>">Button text</label>
                                                <div>
                                                    <input id="button_text_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[button_text]" value="<?php esc_attr_e($field_value); ?>" >
                                                </div>
                                            </div>
                                            <?php $field_value = isset($value['thanks_message'])?$value['thanks_message']:"Your message was sent successfully" ?>
                                            <div class="chaty-setting-col">
                                                <label for="thanks_message_for_<?php echo esc_attr($social['slug']); ?>">Thank you message</label>
                                                <div>
                                                    <input id="thanks_message_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[thanks_message]" value="<?php esc_attr_e($field_value); ?>" >
                                                </div>
                                            </div>
                                            <div class="chaty-separator"></div>
                                            <?php $field_value = isset($value['redirect_action'])?$value['redirect_action']:"no" ?>
                                            <div class="chaty-setting-col">
                                                <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[redirect_action]" value="no" >
                                                <label class="chaty-switch full-width" for="redirect_action_<?php echo esc_attr($social['slug']); ?>">
                                                    <input type="checkbox" class="chaty-redirect-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[redirect_action]" id="redirect_action_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($field_value, "yes") ?> >
                                                    <div class="chaty-slider round"></div>
                                                    Redirect visitors after submission
                                                </label>
                                            </div>
                                            <div class="redirect_action-settings <?php echo ($field_value == "yes")?"active":"" ?>">
                                                <?php $field_value = isset($value['redirect_link'])?$value['redirect_link']:"" ?>
                                                <div class="chaty-setting-col">
                                                    <label for="redirect_link_for_<?php echo esc_attr($social['slug']); ?>">Redirect link</label>
                                                    <div>
                                                        <input id="redirect_link_for_<?php echo esc_attr($social['slug']); ?>" placeholder="<?php echo site_url("/") ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[redirect_link]" value="<?php esc_attr_e($field_value); ?>" >
                                                    </div>
                                                </div>
                                                <?php $field_value = isset($value['link_in_new_tab'])?$value['link_in_new_tab']:"no" ?>
                                                <div class="chaty-setting-col">
                                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_in_new_tab]" value="no" >
                                                    <label class="chaty-switch full-width" for="link_in_new_tab_<?php echo esc_attr($social['slug']); ?>">
                                                        <input type="checkbox" class="chaty-field-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_in_new_tab]" id="link_in_new_tab_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($field_value, "yes") ?> >
                                                        <div class="chaty-slider round"></div>
                                                        Open in a new tab
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="chaty-separator"></div>
                                            <?php $field_value = isset($value['close_form_after'])?$value['close_form_after']:"no" ?>
                                            <div class="chaty-setting-col">
                                                <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[close_form_after]" value="no" >
                                                <label class="chaty-switch full-width" for="close_form_after_<?php echo esc_attr($social['slug']); ?>">
                                                    <input type="checkbox" class="chaty-close_form_after-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[close_form_after]" id="close_form_after_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($field_value, "yes") ?> >
                                                    <div class="chaty-slider round"></div>
                                                    Close form automatically after submission
                                                    <span class="icon label-tooltip inline-message" data-label="Close the form automatically after a few seconds based on your choice"><span class="dashicons dashicons-editor-help"></span></span>
                                                </label>
                                            </div>
                                            <div class="close_form_after-settings <?php echo ($field_value == "yes")?"active":"" ?>">
                                                <?php $field_value = isset($value['close_form_after_seconds'])?$value['close_form_after_seconds']:"3" ?>
                                                <div class="chaty-setting-col">
                                                    <label for="close_form_after_seconds_<?php echo esc_attr($social['slug']); ?>">Close after (Seconds)</label>
                                                    <div>
                                                        <input id="close_form_after_seconds_<?php echo esc_attr($social['slug']); ?>" type="number" name="cht_social_<?php echo esc_attr($social['slug']); ?>[close_form_after_seconds]" value="<?php esc_attr_e($field_value); ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-field-setting-col no-margin">
                                            <input type="hidden" value="no" name="cht_social_<?php echo esc_attr($social['slug']); ?>[send_leads_in_email]" >
                                            <input type="hidden" value="yes" name="cht_social_<?php echo esc_attr($social['slug']); ?>[save_leads_locally]" >
                                            <?php $field_value = isset($val['save_leads_locally'])?$val['save_leads_locally']:"yes" ?>
                                            <div class="chaty-setting-col">
                                                <label for="save_leads_locally_<?php echo esc_attr($social['slug']); ?>" class="full-width chaty-switch">
                                                    <input type="checkbox" disabled id="save_leads_locally_<?php echo esc_attr($social['slug']); ?>" value="yes" name="cht_social_<?php echo esc_attr($social['slug']); ?>[save_leads_locally]" <?php checked($field_value, "yes") ?> >
                                                    <div class="chaty-slider round"></div>
                                                    Save leads to the local database
                                                    <div class="html-tooltip top no-position">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                        <span class="tooltip-text top">Your leads will be saved in your local database, you'll be able to find them <a target="_blank" href="<?php echo admin_url("admin.php?page=chaty-contact-form-feed") ?>">here</a></span>
                                                    </div>
                                                </label>
                                            </div>
                                            <?php $field_value = isset($value['send_leads_in_email'])?$value['send_leads_in_email']:"no" ?>
                                            <div class="chaty-setting-col">
                                                <label for="save_leads_to_email_<?php echo esc_attr($social['slug']); ?>" class="email-setting full-width chaty-switch">
                                                    <input class="email-setting-field" disabled type="checkbox" id="save_leads_to_email_<?php echo esc_attr($social['slug']); ?>" value="yes" name="cht_social_<?php echo esc_attr($social['slug']); ?>[send_leads_in_email]" >
                                                    <div class="chaty-slider round"></div>
                                                    Send leads to your email
                                                    <span class="icon label-tooltip" data-label="Get your leads by email, whenever you get a new email you'll get an email notification"><span class="dashicons dashicons-editor-help"></span></span>
                                                    <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>">(<?php esc_attr_e('Upgrade to Pro', CHT_OPT);?>)</a>
                                                </label>
                                            </div>
                                            <div class="email-settings <?php echo ($field_value == "yes")?"active":"" ?>">
                                                <div class="chaty-setting-col">
                                                    <label for="email_for_<?php echo esc_attr($social['slug']); ?>">Email address</label>
                                                    <div>
                                                        <?php $field_value = isset($value['email_address'])?$value['email_address']:"" ?>
                                                        <input id="email_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[email_address]" value="<?php esc_attr_e($field_value); ?>" >
                                                    </div>
                                                </div>
                                                <div class="chaty-setting-col">
                                                    <label for="sender_name_for_<?php echo esc_attr($social['slug']); ?>">Sender's name</label>
                                                    <div>
                                                        <?php $field_value = isset($value['sender_name'])?$value['sender_name']:"" ?>
                                                        <input id="sender_name_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[sender_name]" value="<?php esc_attr_e($field_value); ?>" >
                                                    </div>
                                                </div>
                                                <div class="chaty-setting-col">
                                                    <label for="email_subject_for_<?php echo esc_attr($social['slug']); ?>">Email subject</label>
                                                    <div>
                                                        <?php $field_value = isset($value['email_subject'])?$value['email_subject']:"New lead from Chaty - {name} - {date} {hour}" ?>
                                                        <input id="email_subject_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[email_subject]" value="<?php esc_attr_e($field_value); ?>" >
                                                        <div class="mail-merge-tags"><span>{name}</span><span>{phone}</span><span>{email}</span><span>{date}</span><span>{hour}</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if($this->is_pro()) { ?>
                                        <div class="clear clearfix"></div>
                                        <div class="Whatsapp-settings advanced-settings">
                                            <!-- advance setting for Whatsapp -->
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label>Pre Set Message <span class="icon label-tooltip inline-tooltip" data-label="Add your own pre-set message that's automatically added to the user's message. You can also use merge tags and add the URL or the title of the current visitor's page. E.g. you can add the current URL of a product to the message so you know which product the visitor is talking about when the visitor messages you"><span class="dashicons dashicons-editor-help"></span></span></label>
                                                <div>
                                                    <?php $pre_set_message = isset($value['pre_set_message'])?$value['pre_set_message']:""; ?>
                                                    <input id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[pre_set_message]" value="<?php esc_attr_e($pre_set_message) ?>" >
                                                    <span class="supported-tags"><span class="icon label-tooltip support-tooltip" data-label="{title} tag grabs the page title of the webpage">{title}</span> and  <span class="icon label-tooltip support-tooltip" data-label="{url} tag grabs the URL of the page">{url}</span> tags are supported</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="Email-settings advanced-settings">
                                            <!-- advance setting for Email -->
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label>Mail Subject <span class="icon label-tooltip inline-tooltip" data-label="Add your own pre-set message that's automatically added to the user's message. You can also use merge tags and add the URL or the title of the current visitor's page. E.g. you can add the current URL of a product to the message so you know which product the visitor is talking about when the visitor messages you"><span class="dashicons dashicons-editor-help"></span></span></label>
                                                <div>
                                                    <?php $mail_subject = isset($value['mail_subject'])?$value['mail_subject']:""; ?>
                                                    <input id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[mail_subject]" value="<?php esc_attr_e($mail_subject) ?>" >
                                                    <span class="supported-tags"><span class="icon label-tooltip support-tooltip" data-label="{title} tag grabs the page title of the webpage">{title}</span> and  <span class="icon label-tooltip support-tooltip" data-label="{url} tag grabs the URL of the page">{url}</span> tags are supported</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="WeChat-settings advanced-settings">
                                            <!-- advance setting for WeChat -->
                                            <?php
                                            $qr_code = isset($value['qr_code'])?$value['qr_code']:"";                               // Initialize QR code value if not exists. 2.1.0 change
                                            $imageUrl = "";
                                            $status = 0;
                                            if($qr_code != "") {
                                                $imageUrl = wp_get_attachment_image_src($qr_code, "full")[0];                       // get custom Image URL if exists
                                            }
                                            if($imageUrl == "") {
                                                $imageUrl = plugin_dir_url("")."chaty/admin/assets/images/chaty-default.png";   // Initialize with default image URL if URL is not exists
                                            } else {
                                                $status = 1;
                                            }
                                            ?>
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label>Upload QR Code</label>
                                                <div>
                                                    <!-- Button to upload QR Code image -->
                                                    <a class="cht-upload-image <?php esc_attr_e(($status)?"active":"") ?>" id="upload_qr_code" href="javascript:;" onclick="upload_qr_code('<?php echo esc_attr($social['slug']); ?>')">
                                                        <img id="cht_social_image_src_<?php echo esc_attr($social['slug']); ?>" src="<?php echo esc_url($imageUrl) ?>" alt="<?php esc_attr_e($value['title']) ?>">
                                                        <span class="dashicons dashicons-upload"></span>
                                                    </a>

                                                    <!-- Button to remove QR Code image -->
                                                    <a href="javascript:;" class="remove-qr-code remove-qr-code-<?php echo esc_attr($social['slug']); ?> <?php esc_attr_e(($status)?"active":"") ?>" onclick="remove_qr_code('<?php echo esc_attr($social['slug']); ?>')"><span class="dashicons dashicons-no-alt"></span></a>

                                                    <!-- input hidden field for QR Code -->
                                                    <input id="upload_qr_code_val-<?php echo esc_attr($social['slug']); ?>" type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[qr_code]" value="<?php esc_attr_e($qr_code) ?>" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="Link-settings Custom_Link-settings Custom_Link_3-settings Custom_Link_4-settings Custom_Link_5-settings advanced-settings">
                                            <?php $is_checked = (!isset($value['new_window']) || $value['new_window'] == 1)?1:0; ?>
                                            <!-- Advance setting for Custom Link -->
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label >Open In a New Tab</label>
                                                <div>
                                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="0" >
                                                    <label class="channels__view" for="cht_social_window_<?php echo esc_attr($social['slug']); ?>">
                                                        <input id="cht_social_window_<?php echo esc_attr($social['slug']); ?>" type="checkbox" class="channels__view-check" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="1" <?php checked($is_checked, 1) ?> >
                                                        <span class="channels__view-txt">&nbsp;</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="Linkedin-settings advanced-settings">
                                            <?php $is_checked = isset($value['link_type'])?$value['link_type']:"personal"; ?>
                                            <!-- Advance setting for Custom Link -->
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label >LinkedIn</label>
                                                <div>
                                                    <label>
                                                        <input type="radio" <?php checked($is_checked, "personal") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="personal">
                                                        Personal
                                                    </label>
                                                    <label>
                                                        <input type="radio" <?php checked($is_checked, "company") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="company">
                                                        Company
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="clear clearfix"></div>
                                        <div class="Whatsapp-settings advanced-settings">
                                            <?php $pre_set_message = isset($value['pre_set_message'])?$value['pre_set_message']:""; ?>
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label>Pre Set Message <span class="icon label-tooltip inline-tooltip" data-label="Add your own pre-set message that's automatically added to the user's message. You can also use merge tags and add the URL or the title of the current visitor's page. E.g. you can add the current URL of a product to the message so you know which product the visitor is talking about when the visitor messages you"><span class="dashicons dashicons-editor-help"></span></span></label>
                                                <div>
                                                    <div class="pro-features">
                                                        <div class="pro-item">
                                                            <div class="pre-message-whatsapp">
                                                                <input disabled id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="" value="<?php esc_attr_e($pre_set_message) ?>" >
                                                                <span class="supported-tags"><span class="icon label-tooltip support-tooltip" data-label="{title} tag grabs the page title of the webpage">{title}</span> and  <span class="icon label-tooltip support-tooltip" data-label="{url} tag grabs the URL of the page">{url}</span> tags are supported</span>
                                                                <button data-button="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0m0 22C6.486 22 2 17.514 2 12S6.486 2 12 2s10 4.486 10 10-4.486 10-10 10"></path><path d="M8 7a2 2 0 1 0-.001 3.999A2 2 0 0 0 8 7M16 7a2 2 0 1 0-.001 3.999A2 2 0 0 0 16 7M15.232 15c-.693 1.195-1.87 2-3.349 2-1.477 0-2.655-.805-3.347-2H15m3-2H6a6 6 0 1 0 12 0"></path></svg></button>
                                                            </div>
                                                        </div>
                                                        <div class="pro-button">
                                                            <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="Email-settings advanced-settings">
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label>Mail Subject <span class="icon label-tooltip inline-tooltip" data-label="Add your own pre-set message that's automatically added to the user's message. You can also use merge tags and add the URL or the title of the current visitor's page. E.g. you can add the current URL of a product to the message so you know which product the visitor is talking about when the visitor messages you"><span class="dashicons dashicons-editor-help"></span></span></label>
                                                <div>
                                                    <div class="pro-features">
                                                        <div class="pro-item">
                                                            <input disabled id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="" value="" >
                                                            <span class="supported-tags"><span class="icon label-tooltip support-tooltip" data-label="{title} tag grabs the page title of the webpage">{title}</span> and  <span class="icon label-tooltip support-tooltip" data-label="{url} tag grabs the URL of the page">{url}</span> tags are supported</span>
                                                        </div>
                                                        <div class="pro-button">
                                                            <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="WeChat-settings advanced-settings">
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label>Upload QR Code</label>
                                                <div>
                                                    <a target="_blank" class="cht-upload-image-pro" id="upload_qr_code" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>" >
                                                        <span class="dashicons dashicons-upload"></span>
                                                    </a>
                                                    <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="Link-settings Custom_Link-settings Custom_Link_3-settings Custom_Link_4-settings Custom_Link_5-settings advanced-settings">
                                            <?php $is_checked = 1; ?>
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label >Open In a New Tab</label>
                                                <div>
                                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="0" >
                                                    <label class="channels__view" for="cht_social_window_<?php echo esc_attr($social['slug']); ?>">
                                                        <input id="cht_social_window_<?php echo esc_attr($social['slug']); ?>" type="checkbox" class="channels__view-check" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="1" checked >
                                                        <span class="channels__view-txt">&nbsp;</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="Linkedin-settings advanced-settings">
                                            <?php $is_checked = "personal"; ?>
                                            <!-- Advance setting for Custom Link -->
                                            <div class="clear clearfix"></div>
                                            <div class="chaty-setting-col">
                                                <label >LinkedIn</label>
                                                <div>
                                                    <label>
                                                        <input type="radio" <?php checked($is_checked, "personal") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="personal">
                                                        Personal
                                                    </label>
                                                    <label>
                                                        <input type="radio" <?php checked($is_checked, "company") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="company">
                                                        Company
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

	                                <?php $use_whatsapp_web = isset($value['use_whatsapp_web'])?$value['use_whatsapp_web']:"yes"; ?>
                                    <div class="Whatsapp-settings advanced-settings">
                                        <div class="clear clearfix"></div>
                                        <div class="chaty-setting-col">
                                            <label>Whatsapp Web</label>
                                            <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[use_whatsapp_web]" value="no" />
                                            <div>
                                                <div class="checkbox">
                                                    <label for="cht_social_<?php echo esc_attr($social['slug']); ?>_use_whatsapp_web" class="chaty-checkbox">
                                                        <input class="sr-only" type="checkbox" id="cht_social_<?php echo esc_attr($social['slug']); ?>_use_whatsapp_web" name="cht_social_<?php echo esc_attr($social['slug']); ?>[use_whatsapp_web]" value="yes" <?php echo checked($use_whatsapp_web, "yes") ?> />
                                                        <span></span>
                                                        Use Whatsapp Web directly on desktop
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- advance setting fields: end -->


                                <!-- remove social media setting button: start -->
                                <button type="button" class="btn-cancel" data-social="<?php echo esc_attr($social['slug']); ?>">
                                    <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(2.26764 0.0615997) rotate(45)" fill="white"/>
                                        <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(13.3198 1.649) rotate(135)" fill="white"/>
                                    </svg>
                                </button>
                                <!-- remove social media setting button: end -->
                            </div>
                        </li>
                        <!-- Social media setting box: end -->
                    <?php } ?>
                <?php } ?>
            <?php }; ?>
            <?php
            $is_pro = $this->is_pro();
            $pro_class = ($is_pro)?"pro":"free";
            $text = get_option("cht_close_button_text");
            $text = ($text === false)?"Hide":$text;
            ?>
            <!-- close setting strat -->
            <li class="chaty-cls-setting" data-id="" id="chaty-social-close">
                <div class="channels-selected__item pro 1 available">
                    <div class="chaty-default-settings ">
                        <div class="move-icon">
                            <img src="<?php echo esc_url(plugins_url()."/chaty/admin/assets/images/move-icon.png") ?>" style="opacity:0"; />
                        </div>
                        <div class="icon icon-md active" data-label="close">
                            <span id="image_data_close">
                                <svg viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="26" cy="26" rx="26" ry="26" fill="#A886CD"></ellipse><rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(18.35 15.6599) scale(0.998038 1.00196) rotate(45)" fill="white"></rect><rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(37.5056 18.422) scale(0.998038 1.00196) rotate(135)" fill="white"></rect></svg>
                            </span>
                            <span class="default_image_close" style="display: none;">
                                 <svg viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="26" cy="26" rx="26" ry="26" fill="#A886CD"></ellipse><rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(18.35 15.6599) scale(0.998038 1.00196) rotate(45)" fill="white"></rect><rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(37.5056 18.422) scale(0.998038 1.00196) rotate(135)" fill="white"></rect></svg>
                            </span>
                        </div>
                        <div class="channels__input-box cls-btn-settings active">
                            <input type="text" class="channels__input" name="cht_close_button_text" value="<?php echo esc_attr($text) ?>" data-gramm_editor="false" >
                        </div>
                        <div class="input-example cls-btn-settings active">
                            <?php esc_attr_e('On hover Close button text', CHT_OPT); ?>
                        </div>
                    </div>
                </div>
            </li>
            <!-- close setting end -->
        </ul>
        <div class="clear clearfix"></div>
        <div class="channels-selected__item disabled" style="opacity: 0; display: none;">

        </div>

        <input type="hidden" id="is_pro_plugin" value="<?php esc_attr_e($this->is_pro()?"1":"0"); ?>" />
        <?php if ($this->is_pro() && $this->is_expired()) : ?>
            <div class="popover">
                <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl()); ?>">
                    <?php esc_attr_e('Your Pro Plan has expired. ', CHT_OPT); ?>
                    <strong><?php esc_attr_e('Upgrade Now', CHT_OPT); ?></strong>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="chaty-sticky-buttons">
        <!-- form sticky save button -->
        <button class="btn-save-sticky">
            <span><?php esc_attr_e('Save', CHT_OPT); ?></span>
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.5 0.5H0.5V27.5H27.5V6.5L21.5 0.5ZM14 24.5C11.51 24.5 9.5 22.49 9.5 20C9.5 17.51 11.51 15.5 14 15.5C16.49 15.5 18.5 17.51 18.5 20C18.5 22.49 16.49 24.5 14 24.5ZM18.5 9.5H3.5V3.5H18.5V9.5Z" fill="white"/>
            </svg>
        </button>

        <!-- chaty help button -->
        <a class="btn-help"><?php esc_attr_e('help', CHT_OPT); ?><span>?</span></a>

        <!-- chaty preview button -->
        <a href="javascript:;" class="preview-help-btn"><?php esc_attr_e('Preview', CHT_OPT); ?></a>
    </div>

</section>
<script>
    var PRO_PLUGIN_URL = "<?php echo esc_url(CHT_PRO_URL) ?>";
</script>

