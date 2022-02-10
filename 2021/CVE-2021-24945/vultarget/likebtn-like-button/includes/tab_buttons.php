<?php

// Buttons tab
function likebtn_admin_buttons() 
{
    global $likebtn_styles;
    global $likebtn_default_locales;
    global $likebtn_settings;
    global $likebtn_buttons_options;
    global $likebtn_entities_config;
    global $likebtn_addthis_service_codes;
    global $likebtn_icons;
    global $likebtn_fonts;
    global $likebtn_fstyles;
    global $user_logged_in_alert_default;
    global $likebtn_buttons_options_shortcode;
    global $likebtn_voting_effects;

    // Enque scripts
    wp_register_script('select2-likebtn', _likebtn_get_public_url().'js/jquery/select2/select2.js', array('jquery'), LIKEBTN_VERSION, true);
    wp_register_script('select2-sortable', _likebtn_get_public_url().'js/jquery/select2/select2.sortable.js', array('select2-likebtn'), LIKEBTN_VERSION, true);
    wp_register_style('select2-css', _likebtn_get_public_url().'css/jquery/select2/select2.css', false, LIKEBTN_VERSION, 'all');
    // Custom jQuery UI was used for buttonset. In general jQuery UI is needed also for datepicker, durationPicker
    // Includes: buttons
    // Theme: UI lightness
    // wp_register_script('likebtn-jquery-ui', _likebtn_get_public_url().'js/jquery/jquery-ui/jquery-ui.js', array('jquery'), LIKEBTN_VERSION, true);
    // wp_register_style('likebtn-jquery-ui-css', _likebtn_get_public_url().'css/jquery/jquery-ui/jquery-ui.css', false, LIKEBTN_VERSION, 'all');
    wp_register_style('likebtn-addthis', _likebtn_get_public_url().'css/addthis.css', false, LIKEBTN_VERSION, 'all');
    wp_register_style('likebtn-icons', '//w.likebtn.com/css/w/icons.css', false, LIKEBTN_VERSION, 'all');
    // Select2 locale
    $blog_locale = get_locale();
    list($blog_locale_main) = explode("_", $blog_locale);
    $plugin_dir = plugin_dir_path(__FILE__);

    $select2_locale_script = '';
    if (file_exists($plugin_dir.'public/js/jquery/select2/locale/select2_locale_'.$blog_locale.'.js')) {
        $select2_locale_script = _likebtn_get_public_url().'js/jquery/select2/locale/select2_locale_'.$blog_locale.'.js';
    } else if (file_exists($plugin_dir.'public/js/jquery/select2/locale/select2_locale_'.$blog_locale_main.'.js')) {
        $select2_locale_script = _likebtn_get_public_url().'js/jquery/select2/locale/select2_locale_'.$blog_locale_main.'.js';
    }

    if ($select2_locale_script) {
        wp_register_script('select2-locale', $select2_locale_script, LIKEBTN_VERSION, true);
    }

    wp_enqueue_media();
    wp_enqueue_script('select2-likebtn');
    wp_enqueue_script('select2-sortable');
    wp_enqueue_style('select2-css');
    if ($select2_locale_script) {
        wp_enqueue_script('select2-locale');
    }

    // wp_enqueue_script('likebtn-jquery-ui');
    // wp_enqueue_style('likebtn-jquery-ui-css');
    wp_enqueue_style('likebtn-addthis');
    wp_enqueue_style('likebtn-icons');
    wp_enqueue_script('wp-color-picker-alpha', _likebtn_get_public_url().'js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), LIKEBTN_VERSION);
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('likebtn-dateparse', _likebtn_get_public_url().'js/dateparse.js', array(), LIKEBTN_VERSION);
    wp_enqueue_script('likebtn-datetimepicker', _likebtn_get_public_url().'js/jquery/jquery.datetimepicker.js', array('jquery'/*, 'likebtn-jquery-ui'*/), LIKEBTN_VERSION);
    wp_enqueue_style('likebtn-datetimepicker', _likebtn_get_public_url().'css/jquery/jquery.datetimepicker.css', array(), LIKEBTN_VERSION, 'all');
    wp_enqueue_script('likebtn-durationpicker', _likebtn_get_public_url().'js/jquery/jquery.ui.durationPicker.js', array('jquery'/*, 'likebtn-jquery-ui'*/), LIKEBTN_VERSION);
    wp_enqueue_style('likebtn-durationpicker', _likebtn_get_public_url().'css/jquery/jquery.ui.durationPicker.css', array(), LIKEBTN_VERSION, 'all');

    $likebtn_entities = _likebtn_get_entities(false, false, false);

    // retrieve post formats
    $post_formats = _likebtn_get_post_formats();

    // run sunchronization
    require_once(dirname(__FILE__) . '/../likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();

    // Languages
    //$locales = get_option('likebtn_locales');
    $locales = $likebtn_default_locales;
    $languages = array();
    $languages['auto'] = array(
        'name' => __("Automatic", 'likebtn-like-button'),
        'en_name' => __("Automatic", 'likebtn-like-button')
    );

    foreach ($locales as $locale_code => $locale_info) {
        $languages[$locale_code] = array(
            'name' => $locale_info['name'],
            'en_name' => $locale_info['en_name']
        );
    }

    // Get styles
    $styles = get_option('likebtn_styles');

    $style_options = array();
    if (!$styles) {
      // Styles have not been loaded using API yet, load default languages
      $styles = $likebtn_styles;
    }
    foreach ($styles as $style) {
      $style_options[] = $style;
    }

    // Select tab
    $subpage = _likebtn_get_subpage();
    //$entity_title = $likebtn_entities[$entity_name];

    // JS and Styles
    global $likebtn_website_locales;
    $likebtn_website_locale = substr(get_bloginfo('language'), 0, 2);
    if (!in_array($likebtn_website_locale, $likebtn_website_locales)) {
        $likebtn_website_locale = 'en';
    }

    likebtn_admin_header();

    likebtn_check_max_vars();
    ?>
    
    <script>(function(d, e, s) {a = d.createElement(e);m = d.getElementsByTagName(e)[0];a.async = 1;a.src = s;m.parentNode.insertBefore(a, m)})(document, 'script', '//<?php echo LIKEBTN_WEBSITE_DOMAIN; ?>/<?php echo $likebtn_website_locale ?>/js/donate_generator.js');
    </script>

    <script type="text/javascript">
        var reset_settings = [];
    <?php foreach ($likebtn_buttons_options as $option_name => $option_value): ?>
        <?php if (is_array($option_value)): ?>
            <?php $option_value = array_shift($option_value); ?>
        <?php endif ?>
        reset_settings['<?php echo str_replace('likebtn_', '', $option_name); ?>'] = '<?php echo $option_value ?>';
    <?php endforeach ?>
    <?php foreach ($likebtn_settings as $option_name => $option_info): ?>
        reset_settings['settings_<?php echo $option_name ?>'] = '<?php echo $option_info['default'] ?>';
    <?php endforeach ?>
        var likebtn_sci = [];
    <?php foreach ($likebtn_buttons_options_shortcode as $option_name => $option_value): ?>
        <?php if (is_array($option_value)): ?>
            <?php $option_value = array_shift($option_value); ?>
        <?php endif ?>
        likebtn_sci['<?php echo $option_value ?>'] = '';
    <?php endforeach ?>

        var likebtn_msg_reset = '<?php _e('Are you sure you want to reset settings for this entity?', 'likebtn-like-button'); ?>';
        var likebtn_msg_set_img = '<?php _e('Select image', 'likebtn-like-button'); ?>';
        //var likebtn_msg_identifier = '<?php _e('likeButton1', 'likebtn-like-button'); ?>';

        var likebtn_path_settings_theme = '//<?php echo LIKEBTN_WEBSITE_DOMAIN; ?>/bundles/likebtnwebsite/i/theme/';
        var likebtn_path_settings_counter_type = '//<?php echo LIKEBTN_WEBSITE_DOMAIN; ?>/bundles/likebtnwebsite/i/counter/';
        var likebtn_default_settings = <?php echo json_encode(array(
            'addthis_service_codes' => $likebtn_settings['addthis_service_codes']
        )) ?>;
        var likebtn_prev_lang = '';
        var likebtn_wp_media = null;
        var likebtn_datetime = "<?php echo date("Y/m/d H:i") ?>";

        jQuery(document).ready(function() {
            likebtnScriptButtons('<?php echo $subpage ?>', '<?php echo get_option('likebtn_plan'); ?>');
        });
    </script>

    <div>
        <form method="post" action="options.php" onsubmit="return likebtnOnSaveButtons()" id="settings_form" autocomplete="off">
            <?php settings_fields('likebtn_buttons'); ?>
            <input type="hidden" name="likebtn_subpage" value="<?php echo $subpage; ?>" id="likebtn_entity_name_field">

            <h3 class="nav-tab-wrapper" style="padding: 0" id="likebtn_subpage_tab_wrapper">
                <?php foreach ($likebtn_entities as $tab_entity_name => $tab_entity_title): ?>
                    <a class="nav-tab likebtn_tab_<?php echo $tab_entity_name; ?> <?php echo ($subpage == $tab_entity_name ? 'nav-tab-active' : '') ?>" href="<?php echo admin_url().'admin.php?page=likebtn_buttons&likebtn_subpage='.$tab_entity_name; ?>"><img src="<?php echo _likebtn_get_public_url() ?>img/check.png" class="likebtn_ttip likebtn_show_marker <?php if (get_option('likebtn_show_' . $tab_entity_name) != '1'): ?>hidden<?php endif ?>" title="<?php _e('Like Button enabled', 'likebtn-like-button'); ?>"><?php _e($tab_entity_title, 'likebtn-like-button'); ?></a>
                <?php endforeach ?>
            </h3>
            <?php
            foreach ($likebtn_entities as $entity_name => $entity_title):

                // Display one entity per page
                if ($subpage != $entity_name) {
                    continue;
                }

                // Entity name without list suffix
                $entity_name_clean = str_replace(LIKEBTN_LIST_FLAG, '', $entity_name);

                $excluded_sections = get_option('likebtn_exclude_sections_' . $entity_name);
                if (!is_array($excluded_sections)) {
                    $excluded_sections = array();
                }

                $excluded_categories = get_option('likebtn_exclude_categories_' . $entity_name);
                if (!is_array($excluded_categories)) {
                    $excluded_categories = array();
                }

                if ($entity_name == LIKEBTN_ENTITY_BBP_POST) {
                    $allow_forums = get_option('likebtn_allow_forums_' . $entity_name);
                    if (!is_array($allow_forums)) {
                        $allow_forums = array();
                    }
                }

                // just in case
                if (!is_array(get_option('likebtn_post_format_' . $entity_name))) {
                    update_option('likebtn_post_format_' . $entity_name, array('all'));
                }

                // AddThis service codes
                $value_addthis_service_codes = get_option('likebtn_settings_addthis_service_codes_' . $entity_name);
                if (!$value_addthis_service_codes) {
                    $lang = get_option('likebtn_settings_lang_' . $entity_name);
                    if (!empty($likebtn_settings['addthis_service_codes']['default_values'][$lang])) {
                        $value_addthis_service_codes = $likebtn_settings['addthis_service_codes']['default_values'][$lang];
                    } else {
                        $value_addthis_service_codes = $likebtn_settings['addthis_service_codes']['default_values']['all'];
                    }
                }

                // Login alert message
                $user_logged_in_alert = get_option('likebtn_user_logged_in_alert_' . $entity_name);
                if (!$user_logged_in_alert) {
                    $user_logged_in_alert = '<p class="alert alert-info fade in" role="alert">'.__($user_logged_in_alert_default, 'likebtn-like-button').'</p>';
                }
                ?>

                <div id="likebtn_subpage_wrapper_<?php echo $entity_name; ?>" class="likebtn_subpage <?php if ($subpage !== $entity_name): ?>hidden<?php endif ?>" >
                    <?php /*<h3><?php _e($entity_title, 'likebtn-like-button'); ?></h3>*/ ?>
                    <div class="inside entity_tab_container">

                        <table class="form-table">
                            <?php if (get_option('likebtn_settings_voting_enabled_' . $entity_name) != '1'): ?>
                                <tr valign="top">
                                    <td colspan="2">
                                        <p class="notice update-nag">
                                            <?php _e('Voting for this post type is disabled on the Voting subtab below. It means that Like button does not allow to vote and only displays results.', 'likebtn-like-button'); ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php endif ?>
                            <tr valign="top">
                                <th scope="row"><label><?php _e('Enable Like Button', 'likebtn-like-button'); ?></label></th>
                                <td>
                                    <input type="checkbox" name="likebtn_show_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_show_' . $entity_name)); ?> onClick="entityShowChange(this, '<?php echo $entity_name; ?>')" />
                                </td>
                            </tr>
                        </table>

                        <div id="entity_container_<?php echo $entity_name; ?>" <?php if (!get_option('likebtn_show_' . $entity_name)): ?>style="display:none"<?php endif ?>>
                            <table class="form-table" >
                                <tr valign="top">
                                    <th scope="row"><label><?php _e('Copy Settings From', 'likebtn-like-button'); ?></label>
                                        <i class="likebtn_help" title="<?php _e('Choose the entity from which you want to copy settings', 'likebtn-like-button'); ?>">&nbsp;</i>
                                    </th>
                                    <td>
                                        <select name="likebtn_use_settings_from_<?php echo $entity_name; ?>" onChange="userSettingsFromChange(this, '<?php echo $entity_name; ?>')">
                                            <option value="" <?php selected('', get_option('likebtn_use_settings_from_' . $entity_name)); ?> >&nbsp;</option>
                                            <?php foreach ($likebtn_entities as $use_entity_name => $use_entity_title): ?>
                                                <?php
                                                if ($use_entity_name == $entity_name) {
                                                    continue;
                                                }
                                                ?>
                                                <option value="<?php echo $use_entity_name; ?>" <?php selected($use_entity_name, get_option('likebtn_use_settings_from_' . $entity_name)); ?> ><?php _e($use_entity_title, 'likebtn-like-button'); ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <?php if (get_option('likebtn_show_' . $entity_name) == '1'): ?>
                                <br/>
                                <div id="preview_fixer" class="likebtn_preview_static postbox" <?php if (get_option('likebtn_use_settings_from_' . $entity_name)): ?>style="display:none"<?php endif ?>>

                                    <h3>
                                        <?php _e('Preview', 'likebtn-like-button'); ?>
                                        <label class="likebtn_pin">
                                            <input type="checkbox" value="1" id="likebtn_pin" <?php if (!$_COOKIE || empty($_COOKIE['likebtn_pin'])): ?>checked="checked"<?php endif ?> /> <small><?php _e('Sticky preview', 'likebtn-like-button'); ?></small>
                                        </label>
                                    </h3>
                                    <div class="inside">
                                        <div class="preview_container">
                                            <?php echo _likebtn_get_markup($entity_name, 'demo', array(), get_option('likebtn_use_settings_from_' . $entity_name), true, true, true) ?>
                                        </div>
                                        <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Changes', 'likebtn-like-button'); ?>" id="likebtn_save_preview"  <?php if (get_option('likebtn_use_settings_from_' . $entity_name)): ?>style="display: none"<?php endif ?>/>

                                        <span class="support_link">
                                            ♥ <?php _e('Like it?', 'likebtn-like-button'); ?>
                                            <a href="https://wordpress.org/support/view/plugin-reviews/likebtn-like-button?filter=5&rate=5#postform" target="_blank">
                                                <?php _e('Support the plugin with ★ 5 Stars', 'likebtn-like-button'); ?>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            <?php endif ?>

                            <div id="use_settings_from_container_<?php echo $entity_name; ?>" <?php if (get_option('likebtn_use_settings_from_' . $entity_name)): ?>style="display:none"<?php endif ?>>
                                <div class="postbox" id="settings_container">
                                    <?php /*<h3><?php _e('Settings', 'likebtn-like-button'); ?></h3>*/ ?>
                                    <div class="inside">

                                        <table class="form-table">
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Theme', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="radio" name="likebtn_theme_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_THEME_TYPE_PREDEFINED; ?>" <?php if (LIKEBTN_THEME_TYPE_PREDEFINED == get_option('likebtn_theme_type_' . $entity_name) || !get_option('likebtn_theme_type_' . $entity_name)): ?>checked="checked"<?php endif ?> class="theme_type_radio" /> 

                                                    <select name="likebtn_settings_theme_<?php echo $entity_name; ?>" class="image_dropdown" id="settings_theme">
                                                        <?php foreach ($style_options as $style): ?>
                                                            <option value="<?php echo $style; ?>" <?php selected($style, get_option('likebtn_settings_theme_' . $entity_name)); ?> ><?php /*echo $style;*/ ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                    <br/><br/>
                                                    <label>
                                                        <input type="radio" name="likebtn_theme_type_<?php echo $entity_name; ?>" class="theme_type_radio" value="<?php echo LIKEBTN_THEME_TYPE_CUSTOM; ?>" <?php if (LIKEBTN_THEME_TYPE_CUSTOM == get_option('likebtn_theme_type_' . $entity_name)): ?>checked="checked"<?php endif ?> /> 
                                                        <?php _e('Custom theme & image', 'likebtn-like-button'); ?>
                                                    </label>
                                                    <input type="hidden" name="likebtn_settings_theme_<?php echo $entity_name; ?>" id="settings_theme_custom" value="custom" <?php if (LIKEBTN_THEME_TYPE_CUSTOM != get_option('likebtn_theme_type_' . $entity_name)): ?> class="disabled" disabled="disabled"<?php endif ?> />
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Button size', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="number" name="likebtn_settings_btn_size_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_btn_size_' . $entity_name) ? get_option('likebtn_settings_btn_size_' . $entity_name) : $likebtn_settings['btn_size']['default']); ?>" class="likebtn_input likebtn_i_sm" min="5" max="500" maxlength="3"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Font size', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="number" name="likebtn_settings_f_size_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_f_size_' . $entity_name) ? get_option('likebtn_settings_f_size_' . $entity_name) : $likebtn_settings['f_size']['default']); ?>" class="likebtn_input likebtn_i_sm" min="5" max="500" maxlength="3"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden param_icon">
                                                <th scope="row"><label><?php _e('Icon size', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="number" name="likebtn_settings_icon_size_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_icon_size_' . $entity_name) ? get_option('likebtn_settings_icon_size_' . $entity_name) : $likebtn_settings['icon_size']['default']); ?>" class="likebtn_input likebtn_i_sm" min="5" max="500" maxlength="3"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Like icon', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="radio" name="likebtn_icon_l_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_ICON_TYPE_ICON; ?>" <?php if (LIKEBTN_ICON_TYPE_ICON == get_option('likebtn_icon_l_type_' . $entity_name) || !get_option('likebtn_icon_l_type_' . $entity_name)): ?>checked="checked"<?php endif ?> class="icon_l_type_radio" /> 
                                                    <select name="likebtn_settings_icon_l_<?php echo $entity_name; ?>" id="settings_icon_l" class="icon_dropdown likebtn_i_sm <?php if (LIKEBTN_ICON_TYPE_ICON != get_option('likebtn_icon_l_type_' . $entity_name)): ?>disabled<?php endif ?>" <?php if (LIKEBTN_ICON_TYPE_ICON != get_option('likebtn_icon_l_type_' . $entity_name)): ?>disabled="disabled"<?php endif ?> >
                                                        <?php foreach ($likebtn_icons as $icon): ?>
                                                            <option value="<?php echo $icon; ?>" <?php selected($icon, get_option('likebtn_settings_icon_l_' . $entity_name)); ?> ><?php echo $icon; ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                    <br/><br/>
                                                    <ul class="likebtn-is">
                                                        <li class="likebtn-is-radio">
                                                            <input type="radio" name="likebtn_icon_l_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_ICON_TYPE_URL; ?>" <?php if (LIKEBTN_ICON_TYPE_URL == get_option('likebtn_icon_l_type_' . $entity_name)): ?>checked="checked"<?php endif ?> class="icon_l_type_radio" /> 
                                                        </li>
                                                        <li class="likebtn-is-block likebtn-is-frst">
                                                            <?php _e('Image', 'likebtn-like-button'); ?>
                                                            <br/>
                                                            <?php $likebtn_icon_url = get_option('likebtn_settings_icon_l_url_'.$entity_name); ?>
                                                            <a title="<?php _e('Select Icon', 'likebtn-like-button'); ?>" class="<?php if (!$likebtn_icon_url): ?>button button-large <?php endif ?>likebtn-i-pick" data-likebtn-wp-title="<?php _e('Like Icon', 'likebtn-like-button'); ?>">
                                                                    <span class="likebtn-is-cap<?php if ($likebtn_icon_url): ?> hidden<?php endif?>"><?php _e('Select', 'likebtn-like-button'); ?></span>
                                                                    <img class="attachment-medium<?php if (!$likebtn_icon_url): ?> hidden<?php endif?>" src="<?php echo $likebtn_icon_url; ?> " />
                                                            </a>
                                                            <div class="likebtn-is-remove <?php if (!$likebtn_icon_url): ?> hidden<?php endif?>"><a onclick="likebtnIconRemove(this);return false;" href="#"><?php _e('Remove', 'likebtn-like-button'); ?></a></div>
                                                            <input type="hidden" name="likebtn_settings_icon_l_url_<?php echo $entity_name; ?>" id="settings_icon_l_url" value="<?php echo $likebtn_icon_url; ?>" class="likebtn-is-inp <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_l_type_' . $entity_name)): ?>disabled<?php endif ?>" <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_l_type_' . $entity_name)): ?>disabled="disabled"<?php endif ?> /> 
                                                        </li>
                                                        <li class="likebtn-is-block">
                                                            <?php _e('Image after voting', 'likebtn-like-button'); ?>
                                                            <br/>
                                                            <?php $likebtn_icon_url = get_option('likebtn_settings_icon_l_url_v_'.$entity_name); ?>
                                                            <a title="<?php _e('Select Icon', 'likebtn-like-button'); ?>" class="<?php if (!$likebtn_icon_url): ?>button button-large <?php endif ?>likebtn-i-pick" data-likebtn-wp-title="<?php _e('Image after voting', 'likebtn-like-button'); ?>">
                                                                    <span class="likebtn-is-cap<?php if ($likebtn_icon_url): ?> hidden<?php endif?>"><?php _e('Select', 'likebtn-like-button'); ?></span>
                                                                    <img class="attachment-medium<?php if (!$likebtn_icon_url): ?> hidden<?php endif?>" src="<?php echo $likebtn_icon_url; ?> " />
                                                            </a>
                                                            <div class="likebtn-is-remove <?php if (!$likebtn_icon_url): ?> hidden<?php endif?>"><a onclick="likebtnIconRemove(this);return false;" href="#"><?php _e('Remove', 'likebtn-like-button'); ?></a></div>
                                                            <input type="hidden" name="likebtn_settings_icon_l_url_v_<?php echo $entity_name; ?>" id="settings_icon_l_url_v" value="<?php echo $likebtn_icon_url; ?>" class="likebtn-is-inp <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_l_type_' . $entity_name)): ?>disabled<?php endif ?>" <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_l_type_' . $entity_name)): ?>disabled="disabled"<?php endif ?>/> 
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Dislike icon', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="radio" name="likebtn_icon_d_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_ICON_TYPE_ICON; ?>" <?php if (LIKEBTN_ICON_TYPE_ICON == get_option('likebtn_icon_d_type_' . $entity_name) || !get_option('likebtn_icon_d_type_' . $entity_name)): ?>checked="checked"<?php endif ?> class="icon_d_type_radio" /> 
                                                    <select name="likebtn_settings_icon_d_<?php echo $entity_name; ?>" id="settings_icon_d" class="icon_dropdown likebtn_i_sm <?php if (LIKEBTN_ICON_TYPE_ICON != get_option('likebtn_icon_d_type_' . $entity_name)): ?>disabled<?php endif ?>" <?php if (LIKEBTN_ICON_TYPE_ICON != get_option('likebtn_icon_d_type_' . $entity_name)): ?>disabled="disabled"<?php endif ?> >
                                                        <?php foreach ($likebtn_icons as $icon): ?>
                                                            <option value="<?php echo $icon; ?>" <?php selected($icon, get_option('likebtn_settings_icon_d_' . $entity_name)); ?> ><?php echo $icon; ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                    <br/><br/>
                                                    <ul class="likebtn-is">
                                                        <li class="likebtn-is-radio">
                                                            <input type="radio" name="likebtn_icon_d_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_ICON_TYPE_URL; ?>" <?php if (LIKEBTN_ICON_TYPE_URL == get_option('likebtn_icon_d_type_' . $entity_name)): ?>checked="checked"<?php endif ?> class="icon_d_type_radio" /> 
                                                        </li>
                                                        <li class="likebtn-is-block likebtn-is-frst">
                                                            <?php _e('Image', 'likebtn-like-button'); ?>
                                                            <br/>
                                                            <?php $likebtn_icon_url = get_option('likebtn_settings_icon_d_url_'.$entity_name); ?>
                                                            <a title="<?php _e('Select Icon', 'likebtn-like-button'); ?>" class="<?php if (!$likebtn_icon_url): ?>button button-large <?php endif ?>likebtn-i-pick" data-likebtn-wp-title="<?php _e('Like Icon', 'likebtn-like-button'); ?>">
                                                                    <span class="likebtn-is-cap<?php if ($likebtn_icon_url): ?> hidden<?php endif?>"><?php _e('Select', 'likebtn-like-button'); ?></span>
                                                                    <img class="attachment-medium<?php if (!$likebtn_icon_url): ?> hidden<?php endif?>" src="<?php echo $likebtn_icon_url; ?> " />
                                                            </a>
                                                            <div class="likebtn-is-remove <?php if (!$likebtn_icon_url): ?> hidden<?php endif?>"><a onclick="likebtnIconRemove(this);return false;" href="#"><?php _e('Remove', 'likebtn-like-button'); ?></a></div>
                                                            <input type="hidden" name="likebtn_settings_icon_d_url_<?php echo $entity_name; ?>" id="settings_icon_d_url" value="<?php echo $likebtn_icon_url; ?>" class="likebtn-is-inp <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_d_type_' . $entity_name)): ?>disabled<?php endif ?>" <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_d_type_' . $entity_name)): ?>disabled="disabled"<?php endif ?> /> 
                                                        </li>
                                                        <li class="likebtn-is-block">
                                                            <?php _e('Image after voting', 'likebtn-like-button'); ?>
                                                            <br/>
                                                            <?php $likebtn_icon_url = get_option('likebtn_settings_icon_d_url_v_'.$entity_name); ?>
                                                            <a title="<?php _e('Select Icon', 'likebtn-like-button'); ?>" class="<?php if (!$likebtn_icon_url): ?>button button-large <?php endif ?>likebtn-i-pick" data-likebtn-wp-title="<?php _e('Image after voting', 'likebtn-like-button'); ?>">
                                                                    <span class="likebtn-is-cap<?php if ($likebtn_icon_url): ?> hidden<?php endif?>"><?php _e('Select', 'likebtn-like-button'); ?></span>
                                                                    <img class="attachment-medium<?php if (!$likebtn_icon_url): ?> hidden<?php endif?>" src="<?php echo $likebtn_icon_url; ?> " />
                                                            </a>
                                                            <div class="likebtn-is-remove <?php if (!$likebtn_icon_url): ?> hidden<?php endif?>"><a onclick="likebtnIconRemove(this);return false;" href="#"><?php _e('Remove', 'likebtn-like-button'); ?></a></div>
                                                            <input type="hidden" name="likebtn_settings_icon_d_url_v_<?php echo $entity_name; ?>" id="settings_icon_d_url_v" value="<?php echo $likebtn_icon_url; ?>" class="likebtn-is-inp <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_d_type_' . $entity_name)): ?>disabled<?php endif ?>" <?php if (LIKEBTN_ICON_TYPE_URL != get_option('likebtn_icon_d_type_' . $entity_name)): ?>disabled="disabled"<?php endif ?>/> 
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden param_icon_l">
                                                <th scope="row"><label><?php _e('Like icon color', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_icon_l_c_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_icon_l_c_' . $entity_name) ? get_option('likebtn_settings_icon_l_c_' . $entity_name) : $likebtn_settings['icon_l_c']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden param_icon_l">
                                                <th scope="row"><label><?php _e('Color after voting', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_icon_l_c_v_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_icon_l_c_v_' . $entity_name) ? get_option('likebtn_settings_icon_l_c_v_' . $entity_name) : $likebtn_settings['icon_l_c_v']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden param_icon_d">
                                                <th scope="row"><label><?php _e('Dislike icon color', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_icon_d_c_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_icon_d_c_' . $entity_name) ? get_option('likebtn_settings_icon_d_c_' . $entity_name) : $likebtn_settings['icon_d_c']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden param_icon_d">
                                                <th scope="row"><label><?php _e('Color after voting', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_icon_d_c_v_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_icon_d_c_v_' . $entity_name) ? get_option('likebtn_settings_icon_d_c_v_' . $entity_name) : $likebtn_settings['icon_d_c_v']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Label color', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_label_c_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_label_c_' . $entity_name) ? get_option('likebtn_settings_label_c_' . $entity_name) : $likebtn_settings['label_c']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Color after voting', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_label_c_v_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_label_c_v_' . $entity_name) ? get_option('likebtn_settings_label_c_v_' . $entity_name) : $likebtn_settings['label_c_v']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Likes counter color', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_counter_l_c_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_counter_l_c_' . $entity_name) ? get_option('likebtn_settings_counter_l_c_' . $entity_name) : $likebtn_settings['counter_l_c']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Dislikes counter color', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_counter_d_c_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_counter_d_c_' . $entity_name) ? get_option('likebtn_settings_counter_d_c_' . $entity_name) : $likebtn_settings['counter_d_c']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Background color', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_bg_c_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_bg_c_' . $entity_name) ? get_option('likebtn_settings_bg_c_' . $entity_name) : $likebtn_settings['bg_c']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Color after voting', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_bg_c_v_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_bg_c_v_' . $entity_name) ? get_option('likebtn_settings_bg_c_v_' . $entity_name) : $likebtn_settings['bg_c_v']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Border color', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_brdr_c_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_settings_brdr_c_' . $entity_name) ? get_option('likebtn_settings_brdr_c_' . $entity_name) : $likebtn_settings['brdr_c']['default']); ?>" data-alpha="true" class="likebtn_input likebtn_i_sm likebtn_cp"/>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Font family', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <select name="likebtn_settings_f_family_<?php echo $entity_name; ?>" class="likebtn_i_sm">
                                                        <?php foreach ($likebtn_fonts as $font): ?>
                                                            <option value="<?php echo $font; ?>" <?php selected($font, get_option('likebtn_settings_f_family_' . $entity_name)); ?> style="font-family: '<?php echo $font?>'"><?php echo $font ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Label font style', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <select name="likebtn_settings_label_fs_<?php echo $entity_name; ?>" class="likebtn_i_sm">
                                                        <?php foreach ($likebtn_fstyles as $fstyle => $fstyle_opts): ?>
                                                            <option value="<?php echo $fstyle; ?>" <?php selected($fstyle, get_option('likebtn_settings_label_fs_' . $entity_name)); ?> style="<?php echo $fstyle_opts['css']; ?>"><?php echo $fstyle_opts['name'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="likebtn_custom hidden">
                                                <th scope="row"><label><?php _e('Counter font style', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <select name="likebtn_settings_counter_fs_<?php echo $entity_name; ?>" class="likebtn_i_sm">
                                                        <?php foreach ($likebtn_fstyles as $fstyle => $fstyle_opts): ?>
                                                            <option value="<?php echo $fstyle; ?>" <?php selected($fstyle, get_option('likebtn_settings_counter_fs_' . $entity_name)); ?> style="<?php echo $fstyle_opts['css']; ?>"><?php echo $fstyle_opts['name'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Language', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <select name="likebtn_settings_lang_<?php echo $entity_name; ?>" id="settings_lang" class="likebtn_i_sm">
                                                        <?php foreach ($languages as $lang_code => $lang_info): ?>
                                                            <option value="<?php echo $lang_code; ?>" <?php selected($lang_code, get_option('likebtn_settings_lang_' . $entity_name)); ?> title="<?php echo $lang_info['en_name']; ?>">[<?php echo $lang_code; ?>] <?php echo $lang_info['name']; ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Show buttons', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="checkbox" name="likebtn_settings_like_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_like_enabled_' . $entity_name)); ?> />
                                                    <i><?php _e('Like', 'likebtn-like-button'); ?></i>
                                                    &nbsp;&nbsp;
                                                    <input type="checkbox" name="likebtn_settings_dislike_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_dislike_enabled_' . $entity_name)); ?> />
                                                    <i><?php _e('Dislike', 'likebtn-like-button'); ?></i>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Show labels', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="checkbox" name="likebtn_settings_show_like_label_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_show_like_label_' . $entity_name)); ?> />
                                                    <i><?php _e('Like', 'likebtn-like-button'); ?></i>
                                                    &nbsp;&nbsp;
                                                    <input type="checkbox" name="likebtn_settings_show_dislike_label_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_show_dislike_label_' . $entity_name)); ?> />
                                                    <i><?php _e('Dislike', 'likebtn-like-button'); ?></i>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                 <th scope="row"><label><?php _e('Show icons', 'likebtn-like-button'); ?></label></th>
                                                 <td>
                                                     <input type="checkbox" name="likebtn_settings_icon_like_show_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_icon_like_show_' . $entity_name)); ?> />
                                                     <i><?php _e('Like', 'likebtn-like-button'); ?></i>
                                                     &nbsp;&nbsp;
                                                     <input type="checkbox" name="likebtn_settings_icon_dislike_show_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_icon_dislike_show_' . $entity_name)); ?> />
                                                     <i><?php _e('Dislike', 'likebtn-like-button'); ?></i>
                                                 </td>
                                             </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Counter type', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <select name="likebtn_settings_counter_type_<?php echo $entity_name; ?>" class="image_dropdown" id="settings_counter_type">
                                                        <option value="number" <?php selected('number', get_option('likebtn_settings_counter_type_' . $entity_name)); ?> ><?php _e('Number', 'likebtn-like-button'); ?></option>
                                                        <option value="percent" <?php selected('percent', get_option('likebtn_settings_counter_type_' . $entity_name)); ?> ><?php _e('Percent', 'likebtn-like-button'); ?></option>
                                                        <option value="subtract_dislikes" <?php selected('subtract_dislikes', get_option('likebtn_settings_counter_type_' . $entity_name)); ?> ><?php _e('Subtract dislikes', 'likebtn-like-button'); ?></option>
                                                        <option value="single_number" <?php selected('single_number', get_option('likebtn_settings_counter_type_' . $entity_name)); ?> ><?php _e('Single number outside', 'likebtn-like-button'); ?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Vertical layout', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="checkbox" name="likebtn_settings_vert_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_vert_' . $entity_name)); ?> />
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Show tooltips', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="checkbox" name="likebtn_settings_tooltip_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_tooltip_enabled_' . $entity_name)); ?> />
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Like button text', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_i18n_like_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_like_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Like', 'likebtn-like-button'); ?>"/>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Dislike button text', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <input type="text" name="likebtn_settings_i18n_dislike_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_dislike_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Dislike', 'likebtn-like-button'); ?>" />
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <th scope="row"><label><?php _e('Voting animation', 'likebtn-like-button'); ?></label></th>
                                                <td>
                                                    <select name="likebtn_settings_ef_voting_<?php echo $entity_name; ?>" id="settings_ef_voting" class="likebtn_i_sm">
                                                        <option value=""></option>
                                                        <?php foreach ($likebtn_voting_effects as $voting_effect): ?>
                                                            <option value="<?php echo $voting_effect; ?>" <?php selected($voting_effect, get_option('likebtn_settings_ef_voting_' . $entity_name)); ?>><?php echo $voting_effect; ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr valign="top" class="plan_dependent plan_vip">
                                                <th scope="row"><label><?php _e('Remove branding', 'likebtn-like-button'); ?> <i class="premium_feature" title="VIP / ULTRA"></i></label>
                                                    <i class="likebtn_help" title="<?php _e('No LikeBtn.com branding link in the popup', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                </th>
                                                <td style="padding-bottom: 0">
                                                    <input type="checkbox" name="likebtn_settings_white_label_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_white_label_' . $entity_name)); ?> /><br/><br/>
                                                    <img src="<?php echo _likebtn_get_public_url() ?>img/branding.png" />
                                                </td>
                                            </tr>
                                            <?php if ($entity_name !== LIKEBTN_ENTITY_COMMENT): ?>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php _e('Google Rich Snippets', 'likebtn-like-button'); ?></label>
                                                    </th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_settings_rich_snippet_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_rich_snippet_' . $entity_name)); ?> <?php if ($entity_name == LIKEBTN_ENTITY_PRODUCT || $entity_name == LIKEBTN_ENTITY_PRODUCT_LIST): ?>disabled="disabled"<?php endif ?>/>
                                                        <?php if ($entity_name == LIKEBTN_ENTITY_PRODUCT || $entity_name == LIKEBTN_ENTITY_PRODUCT_LIST): ?>
                                                            <small style="color:gray"><?php echo __('WooCommerce is adding Rich Snippets to the products pages by default.', 'likebtn-like-button') ?></small>
                                                        <?php else: ?>
                                                            <small><a href="<?php echo __('https://likebtn.com/en/faq#rich_snippets', 'likebtn-like-button') ?>" target="_blank"><?php echo __('What are Google Rich Snippets and how do they boost traffic?', 'likebtn-like-button') ?></a><?php /* / <a href="https://www.google.com/search?q=%D0%A1%D1%82%D0%B0%D0%BB%D0%B8%D0%BD%D1%81%D0%BA%D0%B0%D1%8F+%D1%8D%D0%BA%D0%BE%D0%BD%D0%BE%D0%BC%D0%B8%D0%BA%D0%B0+%D1%8D%D1%82%D0%BE+%D0%BD%D0%B5+%D1%8D%D0%BA%D0%BE%D0%BD%D0%BE%D0%BC%D0%B8%D0%BA%D0%B0+%D0%A1%D0%A1%D0%A1%D0%A0+%D0%94%D0%95%D0%A0%D0%96%D0%90%D0%92%D0%90+%D0%A1%D0%95%D0%93%D0%9E%D0%94%D0%9D%D0%AF" target="_blank"><?php echo __('Live demo', 'likebtn-like-button') ?></a>*/ ?></small>
                                                        <?php endif ?>
                                                    </td>
                                                </tr>
                                            <?php endif ?>
                                            <?php if (empty($likebtn_entities_config['likebtn_alignment'][$entity_name]['hide'])): ?>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php _e('Horizontal alignment', 'likebtn-like-button'); ?></label></th>
                                                    <td>
                                                        <input type="radio" name="likebtn_alignment_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_ALIGNMENT_LEFT; ?>" <?php if (LIKEBTN_ALIGNMENT_LEFT == get_option('likebtn_alignment_' . $entity_name) || !get_option('likebtn_alignment_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php _e('Left'); ?>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <input type="radio" name="likebtn_alignment_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_ALIGNMENT_CENTER; ?>" <?php if (LIKEBTN_ALIGNMENT_CENTER == get_option('likebtn_alignment_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php _e('Center'); ?>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <input type="radio" name="likebtn_alignment_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_ALIGNMENT_RIGHT; ?>" <?php if (LIKEBTN_ALIGNMENT_RIGHT == get_option('likebtn_alignment_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php _e('Right'); ?>

                                                    </td>
                                                </tr>
                                            <?php endif ?>
                                            <?php if (empty($likebtn_entities_config['likebtn_position'][$entity_name]['hide'])): ?>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php _e('Vertical alignment', 'likebtn-like-button'); ?></label></th>
                                                    <td>
                                                        <input type="radio" name="likebtn_position_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_POSITION_TOP ?>" <?php if (LIKEBTN_POSITION_TOP == get_option('likebtn_position_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php _e('Top of Content', 'likebtn-like-button'); ?>&nbsp;&nbsp;&nbsp;
                                                        <input type="radio" name="likebtn_position_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_POSITION_BOTTOM ?>" <?php if (LIKEBTN_POSITION_BOTTOM == get_option('likebtn_position_' . $entity_name) || !get_option('likebtn_position_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php _e('Bottom of Content', 'likebtn-like-button'); ?>&nbsp;&nbsp;&nbsp;
                                                        <input type="radio" name="likebtn_position_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_POSITION_BOTH ?>" <?php if (LIKEBTN_POSITION_BOTH == get_option('likebtn_position_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php _e('Top and Bottom', 'likebtn-like-button'); ?>

                                                    </td>
                                                </tr>
                                            <?php endif ?>
                                        </table>

                                        <br/>

                                        <h3 class="nav-tab-wrapper" style="padding: 0" id="likebtn_extset_tabs">
                                            <a class="nav-tab likebtn_tab_general nav-tab-active" href="javascript:likebtnGotoTab('general', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('General', 'likebtn-like-button'); ?></a>

                                            <a class="nav-tab likebtn_tab_popup" href="javascript:likebtnGotoTab('popup', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Popup', 'likebtn-like-button'); ?></a>

                                            <a class="nav-tab likebtn_tab_voting" href="javascript:likebtnGotoTab('voting', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Voting', 'likebtn-like-button'); ?></a>

                                            <a class="nav-tab likebtn_tab_counter" href="javascript:likebtnGotoTab('counter', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Counter', 'likebtn-like-button'); ?></a>

                                            <a class="nav-tab likebtn_tab_loading" href="javascript:likebtnGotoTab('sharing', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Sharing', 'likebtn-like-button'); ?></a>

                                            <?php /*
                                            <a class="nav-tab likebtn_tab_loading" href="javascript:likebtnGotoTab('loading', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Loading', 'likebtn-like-button'); ?></a>
                                            */ ?>

                                            <a class="nav-tab likebtn_tab_tooltips" href="javascript:likebtnGotoTab('tooltips', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Tooltips', 'likebtn-like-button'); ?></a>

                                            <a class="nav-tab likebtn_tab_misc" href="javascript:likebtnGotoTab('misc', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Misc', 'likebtn-like-button'); ?></a>

                                            <a class="nav-tab likebtn_tab_texts" href="javascript:likebtnGotoTab('texts', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);"><?php _e('Texts', 'likebtn-like-button'); ?></a>

                                            <a class="nav-tab likebtn_tab_buddypress" href="javascript:likebtnGotoTab('buddypress', '.likebtn_tab_extset', '#likebtn_extset_tab_', '#likebtn_extset_tabs');void(0);">BuddyPress</a>
                                        </h3>

                                        <div class="postbox likebtn_tab_extset" id="likebtn_extset_tab_general">
                                            <?php /*<h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_collapse_trigger"><small>►</small> <?php _e('General', 'likebtn-like-button'); ?></h3>*/ ?>
                                            <div class="inside">
                                                <table class="form-table" >
                                                    <?php /*if (!in_array($entity_name, $likebtn_no_excerpts)): ?>
                                                        <tr valign="top">
                                                            <th scope="row"><label><?php _e('View mode', 'likebtn-like-button'); ?></label></th>
                                                            <td>
                                                                <input type="radio" name="likebtn_post_view_mode_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_POST_VIEW_MODE_FULL; ?>" <?php checked(LIKEBTN_POST_VIEW_MODE_FULL, get_option('likebtn_post_view_mode_' . $entity_name)) ?> /> <?php _e('Full', 'likebtn-like-button'); ?>&nbsp;&nbsp;&nbsp;
                                                                <input type="radio" name="likebtn_post_view_mode_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_POST_VIEW_MODE_EXCERPT; ?>" <?php checked(LIKEBTN_POST_VIEW_MODE_EXCERPT, get_option('likebtn_post_view_mode_' . $entity_name)) ?> /> <?php _e('Excerpt', 'likebtn-like-button'); ?>&nbsp;&nbsp;&nbsp;
                                                                <input type="radio" name="likebtn_post_view_mode_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_POST_VIEW_MODE_BOTH; ?>" <?php checked(LIKEBTN_POST_VIEW_MODE_BOTH, get_option('likebtn_post_view_mode_' . $entity_name)) ?> /> <?php _e('Both', 'likebtn-like-button'); ?>

                                                                <i class="likebtn_help" title="<?php _e('Choose post display modes for which you want to show the Like Button', 'likebtn-like-button'); ?>"></i>
                                                            </td>
                                                        </tr>
                                                    <?php endif*/ ?>

                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Format', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e('Select post formats for which you want to show the Like Button', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_post_format_<?php echo $entity_name; ?>[]" value="all" <?php if (in_array('all', get_option('likebtn_post_format_' . $entity_name))): ?>checked="checked"<?php endif ?> onClick="postFormatAllChange(this, '<?php echo $entity_name; ?>')" /> <?php _e('All', 'likebtn-like-button'); ?>&nbsp;&nbsp;&nbsp;
                                                            <span id="post_format_container_<?php echo $entity_name; ?>" <?php if (in_array('all', get_option('likebtn_post_format_' . $entity_name))): ?>style="display:none"<?php endif ?>>
                                                                <?php foreach ($post_formats as $post_format): ?>
                                                                    <input type="checkbox" name="likebtn_post_format_<?php echo $entity_name; ?>[]" value="<?php echo $post_format; ?>" <?php if (in_array($post_format, get_option('likebtn_post_format_' . $entity_name))): ?>checked="checked"<?php endif ?> /> <?php _e(__(ucfirst($post_format), 'likebtn-like-button')); ?>&nbsp;&nbsp;&nbsp;
                                                                <?php endforeach ?>
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Exclude on selected sections', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e('Choose sections where you DO NOT want to show the Like Button', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_exclude_sections_<?php echo $entity_name; ?>[]" value="home" <?php if (in_array('home', $excluded_sections)): ?>checked="checked"<?php endif ?> /> <?php _e('Home'); ?>&nbsp;&nbsp;&nbsp;
                                                            <input type="checkbox" name="likebtn_exclude_sections_<?php echo $entity_name; ?>[]" value="archive" <?php if (in_array('archive', $excluded_sections)): ?>checked="checked"<?php endif ?> /> <?php _e('Archive', 'likebtn-like-button'); ?>&nbsp;&nbsp;&nbsp;
                                                            <input type="checkbox" name="likebtn_exclude_sections_<?php echo $entity_name; ?>[]" value="search" <?php if (in_array('search', $excluded_sections)): ?>checked="checked"<?php endif ?> /> <?php _e('Search', 'likebtn-like-button'); ?>&nbsp;&nbsp;&nbsp;
                                                            <input type="checkbox" name="likebtn_exclude_sections_<?php echo $entity_name; ?>[]" value="category" <?php if (in_array('category', $excluded_sections)): ?>checked="checked"<?php endif ?> /> <?php _e('Category', 'likebtn-like-button'); ?>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Exclude in selected categories', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e('Select categories where you DO NOT want to show the Like Button', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <select name='likebtn_exclude_categories_<?php echo $entity_name; ?>[]' multiple="multiple" id="likebtn_exclude_categories" class="likebtn_input">
                                                                <?php
                                                                $categories = _likebtn_get_categories();

                                                                foreach ($categories as $category) {
                                                                    $selected = (in_array($category->cat_ID, $excluded_categories)) ? 'selected="selected"' : '';
                                                                    $option = '<option value="' . $category->cat_ID . '" ' . $selected . '>';
                                                                    $option .= $category->cat_name;
                                                                    $option .= ' (' . $category->category_count . ')';
                                                                    $option .= '</option>';
                                                                    echo $option;
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <?php if ($entity_name == LIKEBTN_ENTITY_BBP_POST): ?>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Allow in selected forum', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <select name='likebtn_allow_forums_<?php echo $entity_name; ?>[]' multiple="multiple" id="likebtn_allow_forums" class="likebtn_input">
                                                                <?php
                                                                $forums = _likebtn_get_forums();

                                                                foreach ($forums as $forum) {
                                                                    $selected = (in_array($forum->ID, $allow_forums)) ? 'selected="selected"' : '';
                                                                    $option = '<option value="' . $forum->ID . '" ' . $selected . '>';
                                                                    $option .= $forum->post_title;
                                                                    $option .= '</option>';
                                                                    echo $option;
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <?php endif ?>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Allow post/page IDs', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e('Suppose you have a post which belongs to more than one category and you have excluded one of those categories. So the Like Button will not be available for that post. Enter comma separated post ids where you want to show the Like Button irrespective of that post category being excluded.', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="text" name="likebtn_allow_ids_<?php echo $entity_name; ?>" value="<?php _e(get_option('likebtn_allow_ids_' . $entity_name)); ?>" class="likebtn_input" />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Exclude post/page IDs', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e('Comma separated post/page IDs where you DO NOT want to show the Like Button', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="text" name="likebtn_exclude_ids_<?php echo $entity_name; ?>" value="<?php _e(get_option('likebtn_exclude_ids_' . $entity_name)); ?>" class="likebtn_input" />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Track voters by', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <label>
                                                                <input type="radio" class="radio_voter_by" name="likebtn_voter_by_<?php echo $entity_name; ?>" value="" <?php checked(LIKEBTN_VOTER_BY_IP, get_option('likebtn_voter_by_' . $entity_name)) ?> /><?php _e('IP + Device + Cookie', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <br/>
                                                            <label>
                                                                <input type="radio" class="radio_voter_by" name="likebtn_voter_by_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_VOTER_BY_USER; ?>" <?php checked(LIKEBTN_VOTER_BY_USER, get_option('likebtn_voter_by_' . $entity_name)) ?> /><?php _e('Username', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <div class="param_voter_by_alert hidden">
                                                                <p class="notice update-nag">
                                                                    ●  <?php _e('Only registered users will be able to vote', 'likebtn-like-button'); ?><br/>
                                                                    ●  <?php echo strtr(
                                                                    __('<a href="%url_interval%">IP address vote interval</a> parameter has an effect only if "How often visitor can vote" is set on "Voting" tab', 'likebtn-like-button'), 
                                                                    array('%url_interval%'=>"javascript:likebtnPopup('".__('http://likebtn.com/en/', 'likebtn-like-button')."customer.php/websites');void(0);")
                                                                ); ?>
                                                                </p>
                                                            </div>
                                                            
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Who can vote', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <label>
                                                                <input type="radio" name="likebtn_user_logged_in_<?php echo $entity_name; ?>" value="" class="user_logged_in_radio" <?php checked(LIKEBTN_USER_LOGGED_IN_ALL, get_option('likebtn_user_logged_in_' . $entity_name)) ?> /><?php _e('Show to all', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <br/>
                                                            <label>
                                                                <input type="radio" name="likebtn_user_logged_in_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_USER_LOGGED_IN_YES; ?>" class="user_logged_in_radio" <?php checked(LIKEBTN_USER_LOGGED_IN_YES, get_option('likebtn_user_logged_in_' . $entity_name)) ?> /><?php _e('Show to <strong>logged in</strong> users only', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <br/>
                                                            <label>
                                                                <input type="radio" name="likebtn_user_logged_in_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_USER_LOGGED_IN_NO; ?>" class="user_logged_in_radio" <?php checked(LIKEBTN_USER_LOGGED_IN_NO, get_option('likebtn_user_logged_in_' . $entity_name)) ?> /><?php _e('Show to <strong>guests</strong> only', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <br/>
                                                            <label>
                                                                <input type="radio" name="likebtn_user_logged_in_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_USER_LOGGED_IN_ALERT; ?>" class="user_logged_in_radio" <?php checked(LIKEBTN_USER_LOGGED_IN_ALERT, get_option('likebtn_user_logged_in_' . $entity_name)) ?> /><?php _e('Show a <strong>message</strong> asking to login instead of the button to guests', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <br/>
                                                            <label>
                                                                <input type="radio" name="likebtn_user_logged_in_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_USER_LOGGED_IN_ALERT_BTN; ?>" class="user_logged_in_radio" <?php checked(LIKEBTN_USER_LOGGED_IN_ALERT_BTN, get_option('likebtn_user_logged_in_' . $entity_name)) ?> /><?php _e('Show a <strong>message</strong> asking to login <strong>and the button</strong> to guests', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <br/>
                                                            <label>
                                                                <input type="radio" name="likebtn_user_logged_in_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_USER_LOGGED_IN_MODAL; ?>" class="user_logged_in_radio" <?php checked(LIKEBTN_USER_LOGGED_IN_MODAL, get_option('likebtn_user_logged_in_' . $entity_name)) ?> /><?php _e('Show a <strong>modal window</strong> with the message asking to login when guest votes', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <p class="notice update-nag param_user_logged_in_notice"><?php echo strtr(
           __('Make sure not to disable anonymous access to %admin_ajax%, otherwise votes from anonymous visitors will not be accepted.', 'likebtn-like-button'), 
            array('%admin_ajax%'=>'<a href="'.admin_url('admin-ajax.php').'" target="_blank">/wp-admin/admin-ajax.php</a>')) ?></p>

                                                            <p class="description param_user_logged_in_alert hidden">
                                                                <br/>
                                                                <?php _e('Message', 'likebtn-like-button'); ?>:
                                                                <textarea name="likebtn_user_logged_in_alert_<?php echo $entity_name; ?>" class="likebtn_input" rows="2"><?php echo htmlspecialchars($user_logged_in_alert); ?></textarea>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label><?php _e('Like box', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e('List of users who liked an item', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <label>
                                                                <input type="radio" name="likebtn_like_box_<?php echo $entity_name; ?>" value="" class="like_box_radio" <?php checked('', get_option('likebtn_like_box_' . $entity_name)) ?> /><?php _e('Disabled', 'likebtn-like-button'); ?>
                                                            </label>
                                                            &nbsp;&nbsp;&nbsp; 
                                                            <label>
                                                                <input type="radio" name="likebtn_like_box_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BOX_AFTER; ?>" class="like_box_radio" <?php checked(LIKEBTN_LIKE_BOX_AFTER, get_option('likebtn_like_box_' . $entity_name)) ?> /><?php _e('After the button', 'likebtn-like-button'); ?>
                                                            </label>
                                                            &nbsp;&nbsp;&nbsp; 
                                                            <label>
                                                                <input type="radio" name="likebtn_like_box_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BOX_BEFORE; ?>" class="like_box_radio" <?php checked(LIKEBTN_LIKE_BOX_BEFORE, get_option('likebtn_like_box_' . $entity_name)) ?> /><?php _e('Before the button', 'likebtn-like-button'); ?>
                                                            </label>
                                                            <div class="param_like_box hidden" >
                                                                <small><a target="_blank" href="<?php _e('https://likebtn.com/en/', 'likebtn-like-button'); ?>wordpress-like-button-plugin#like_box_template"><?php _e('How to alter like box template?', 'likebtn-like-button'); ?></a></small> | <small><a href="javascript:jQuery('#like_box_help').toggle();void(0);"><?php _e('Do not see voters in the Like box?', 'likebtn-like-button'); ?></a></small>
                                                                <p class="notice update-nag" id="like_box_help" style="display:none">
                                                                    ● <?php _e('Make sure that you are logged in when voting, otherwise the vote will be accepted from anonymous user and not visible in the Like box.', 'likebtn-like-button') ?><br/>
                                                                    ● <?php echo strtr(__('Make sure that you have not voted for the particular post from your current IP address during the %a_begin%"IP voting interval"%a_end% as in this case your vote will not be accepted (unless you\'ve enabled tracking voters by Username).', 'likebtn-like-button'), array('%a_begin%'=>'<a href="'.admin_url().'admin.php?page=likebtn_settings#ip_vote_interval">', '%a_end%'=>'</a>')) ?><br/>
                                                                    ● <?php _e('Like box will not be updated for non-logged in visitors if you have a WP Super Cache or similar plugin enabled, which is caching all the pages: disable page caching or clean the cache in order to see updated Like box.', 'likebtn-like-button') ?>
                                                                </p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top" class="param_like_box hidden">
                                                        <th scope="row"><label><?php _e('Like box size', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e('Number of users to display in the like box', 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="number" name="likebtn_like_box_size_<?php echo $entity_name; ?>" value="<?php echo (get_option('likebtn_like_box_size_' . $entity_name) ? get_option('likebtn_like_box_size_' . $entity_name) : $likebtn_buttons_options['likebtn_like_box_size']); ?>" class="likebtn_input likebtn_i_sm" min="1" maxlength="3"/> (<?php _e('users', 'likebtn-like-button'); ?>)
                                                        </td>
                                                    </tr>
                                                    <tr valign="top" class="param_like_box hidden">
                                                        <th scope="row"><label><?php _e('Like box users', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <label>
                                                                <input type="radio" name="likebtn_like_box_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_VOTE_LIKE; ?>" <?php if (get_option('likebtn_like_box_type_' . $entity_name) == LIKEBTN_VOTE_LIKE || get_option('likebtn_like_box_type_' . $entity_name) == ''): ?>checked="checked"<?php endif ?> /><?php _e('Likers', 'likebtn-like-button'); ?>
                                                            </label>
                                                            &nbsp;&nbsp;&nbsp; 
                                                            <label>
                                                                <input type="radio" name="likebtn_like_box_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_VOTE_DISLIKE; ?>" <?php checked(LIKEBTN_VOTE_DISLIKE, get_option('likebtn_like_box_type_' . $entity_name)) ?> /><?php _e('Dislikers', 'likebtn-like-button'); ?>
                                                            </label>
                                                            &nbsp;&nbsp;&nbsp; 
                                                            <label>
                                                                <input type="radio" name="likebtn_like_box_type_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_VOTE_BOTH; ?>" <?php checked(LIKEBTN_VOTE_BOTH, get_option('likebtn_like_box_type_' . $entity_name)) ?> /><?php _e('Likers & dislikers', 'likebtn-like-button'); ?>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top" class="param_like_box hidden">
                                                        <th scope="row"><label><?php _e('Like box text', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <input type="text" name="likebtn_like_box_text_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_box_text_' . $entity_name); ?>" class="likebtn_input" />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('HTML before the button', 'likebtn-like-button'); ?></label>
                                                            <?php /*<i class="likebtn_help" title="<?php _e('HTML code to insert before the Like Button', 'likebtn-like-button'); ?>">&nbsp;</i>*/ ?>
                                                        </th>
                                                        <td>
                                                            <textarea name="likebtn_html_before_<?php echo $entity_name; ?>" class="likebtn_input" rows="2"><?php echo get_option('likebtn_html_before_' . $entity_name); ?></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('HTML after the button', 'likebtn-like-button'); ?></label>
                                                            <?php /*<i class="likebtn_help" title="<?php _e('HTML code to insert after the Like Button', 'likebtn-like-button'); ?>">&nbsp;</i>*/ ?>
                                                        </th>
                                                        <td>
                                                            <textarea name="likebtn_html_after_<?php echo $entity_name; ?>" class="likebtn_input" rows="2"><?php echo get_option('likebtn_html_after_' . $entity_name); ?></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Display on a new line', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e("Add a 'clear:both' style to the like button container", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_newline_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_newline_' . $entity_name)) ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Wrap button in a div', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e("If disabled alignment and new line options have no affect", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_wrap_<?php echo $entity_name; ?>" value="1" class="likebtn_wrap" <?php checked('1', get_option('likebtn_wrap_' . $entity_name)) ?> />
                                                            <p class="notice update-nag param_wrap"><?php echo __('If wrapping button in a div is disabled horizontal alignment option has no effect.', 'likebtn-like-button') ?></p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="postbox likebtn_tab_extset hidden" id="likebtn_extset_tab_popup">
                                            <?php /*<h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_collapse_trigger"><small>►</small> <?php _e('Popup', 'likebtn-like-button'); ?></h3>*/ ?>
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr valign="top" class="plan_dependent plan_vip">
                                                        <th scope="row"><label><?php _e('Disable popup', 'likebtn-like-button'); ?> <i class="premium_feature" title="VIP / ULTRA"></i></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_popup_disabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_popup_disabled_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Show popup on disliking', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_popup_dislike_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_popup_dislike_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Show popup on button load', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_popup_on_load_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_popup_on_load_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Popup position', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <div class="image_toggle">
                                                                <input type="radio" name="likebtn_settings_popup_position_<?php echo $entity_name; ?>" id="likebtn_settings_popup_position_<?php echo $entity_name; ?>_top" value="top" <?php checked('top', get_option('likebtn_settings_popup_position_' . $entity_name)); ?>>
                                                                <label for="likebtn_settings_popup_position_<?php echo $entity_name; ?>_top"><img src="<?php echo _likebtn_get_public_url() ?>img/popup_position/top.png" alt="<?php _e('top', 'likebtn-like-button'); ?>" title="<?php _e('top', 'likebtn-like-button'); ?>" /></label>

                                                                <input type="radio" name="likebtn_settings_popup_position_<?php echo $entity_name; ?>" id="likebtn_settings_popup_position_<?php echo $entity_name; ?>_right" value="right" <?php checked('right', get_option('likebtn_settings_popup_position_' . $entity_name)); ?>>
                                                                <label for="likebtn_settings_popup_position_<?php echo $entity_name; ?>_right"><img src="<?php echo _likebtn_get_public_url() ?>img/popup_position/right.png" alt="<?php _e('right', 'likebtn-like-button'); ?>" title="<?php _e('right', 'likebtn-like-button'); ?>" /></label>

                                                                <input type="radio" name="likebtn_settings_popup_position_<?php echo $entity_name; ?>" id="likebtn_settings_popup_position_<?php echo $entity_name; ?>_bottom" value="bottom" <?php checked('bottom', get_option('likebtn_settings_popup_position_' . $entity_name)); ?>>
                                                                <label for="likebtn_settings_popup_position_<?php echo $entity_name; ?>_bottom"><img src="<?php echo _likebtn_get_public_url() ?>img/popup_position/bottom.png" alt="<?php _e('bottom', 'likebtn-like-button'); ?>" title="<?php _e('bottom', 'likebtn-like-button'); ?>" /></label>

                                                                <input type="radio" name="likebtn_settings_popup_position_<?php echo $entity_name; ?>" id="likebtn_settings_popup_position_<?php echo $entity_name; ?>_left" value="left" <?php checked('left', get_option('likebtn_settings_popup_position_' . $entity_name)); ?>>
                                                                <label for="likebtn_settings_popup_position_<?php echo $entity_name; ?>_left"><img src="<?php echo _likebtn_get_public_url() ?>img/popup_position/left.png" alt="<?php _e('left', 'likebtn-like-button'); ?>" title="<?php _e('left', 'likebtn-like-button'); ?>" /></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Popup style', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <div class="image_toggle">
                                                                <input type="radio" name="likebtn_settings_popup_style_<?php echo $entity_name; ?>" id="likebtn_settings_popup_style_<?php echo $entity_name; ?>_light" value="light" <?php checked('light', get_option('likebtn_settings_popup_style_' . $entity_name)); ?>>
                                                                <label for="likebtn_settings_popup_style_<?php echo $entity_name; ?>_light"><img src="<?php echo _likebtn_get_public_url() ?>img/popup_style/light.png" alt="<?php _e('light', 'likebtn-like-button'); ?>" title="<?php _e('light', 'likebtn-like-button'); ?>" /></label>                                                                        
                                                                <input type="radio" name="likebtn_settings_popup_style_<?php echo $entity_name; ?>" id="likebtn_settings_popup_style_<?php echo $entity_name; ?>_dark" value="dark" <?php checked('dark', get_option('likebtn_settings_popup_style_' . $entity_name)); ?>>
                                                                <label for="likebtn_settings_popup_style_<?php echo $entity_name; ?>_dark"><img src="<?php echo _likebtn_get_public_url() ?>img/popup_style/dark.png" alt="<?php _e('dark', 'likebtn-like-button'); ?>" title="<?php _e('dark', 'likebtn-like-button'); ?>" /></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Popup width', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="number" name="likebtn_settings_popup_width_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_popup_width_' . $entity_name); ?>" size="8"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Hide popup when clicking outside', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_popup_hide_on_outside_click_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_popup_hide_on_outside_click_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top" class="plan_dependent plan_vip">
                                                        <th scope="row"><label><?php _e('Donate buttons', 'likebtn-like-button'); ?> <i class="premium_feature" title="VIP / ULTRA"></i></label></th>
                                                        <td>
                                                            <div id="donate_wrapper">
                                                                <div id="donate_pveview" class="likebtn_input"></div>

                                                                <a href="javascript:likebtnDG('popup_donate_input', false, {width: '80%'}, {preview_container: '#donate_pveview'});void(0);" id="popup_donate_trigger"><img src="<?php echo _likebtn_get_public_url() ?>img/popup_donate.png" alt="<?php _e('Configure donate buttons', 'likebtn-like-button'); ?>"></a>
                                                            </div>

                                                            <input type="hidden" name="likebtn_settings_popup_donate_<?php echo $entity_name; ?>" value="<?php echo htmlspecialchars(get_option('likebtn_settings_popup_donate_' . $entity_name)); ?>" id="popup_donate_input" class="likebtn_input"/>

                                                            <p class="description">
                                                                <?php _e('Collect donations using', 'likebtn-like-button'); ?> <a href="https://www.paypal.com" target="_blank">PayPal</a>, <a href="https://bitcoin.org" target="_blank">Bitcoin</a>, <a href="https://wallet.google.com" target="_blank">Google Wallet</a>, <a href="https://money.yandex.ru" target="_blank">Yandex.Money</a>, <a href="http://www.webmoney.ru" target="_blank">Webmoney</a>, <a href="https://qiwi.ru" target="_blank">Qiwi</a>, <a href="http://smscoin.com" target="_blank">SmsCoin</a>, <a href="https://zaypay.com" target="_blank"><?php _e('Zaypay Mobile Payments', 'likebtn-like-button'); ?></a>.
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top" class="plan_dependent plan_pro">
                                                        <th scope="row"><label><?php _e('Custom HTML', 'likebtn-like-button'); ?> <i class="premium_feature" title="PRO / VIP / ULTRA"></i></label>
                                                            <i class="likebtn_help" title="<?php _e("Custom HTML to insert into the popup", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <textarea name="likebtn_settings_popup_html_<?php echo $entity_name; ?>" class="likebtn_input" rows="2"><?php echo get_option('likebtn_settings_popup_html_' . $entity_name); ?></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Popup content order', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <select id="settings_popup_content_order" multiple="multiple" class="likebtn_input">
                                                                <option value="popup_donate" ><?php _e('Donate buttons', 'likebtn-like-button'); ?></option>
                                                                <option value="popup_share" ><?php _e('Share buttons', 'likebtn-like-button'); ?></option>
                                                                <option value="popup_html" ><?php _e('Custom HTML', 'likebtn-like-button'); ?></option>
                                                            </select>

                                                            <input type="hidden" id="settings_popup_content_order_input" name="likebtn_settings_popup_content_order_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_popup_content_order_' . $entity_name); ?>" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="postbox likebtn_tab_extset hidden" id="likebtn_extset_tab_voting">
                                            <?php /*<h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_collapse_trigger"><small>►</small> <?php _e('Voting', 'likebtn-like-button'); ?></h3>*/ ?>
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Allow voting', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_voting_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_voting_enabled_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Allow to cancel a vote', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_voting_cancelable_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_voting_cancelable_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Allow to like and dislike at the same time', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_voting_both_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_voting_both_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('How often visitor can vote', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <select name="likebtn_settings_voting_frequency_<?php echo $entity_name; ?>">
                                                                <option value=""><?php _e('Once', 'likebtn-like-button'); ?> (<?php _e('default', 'likebtn-like-button'); ?>)</option>
                                                                <option value="1" <?php selected('1', get_option('likebtn_settings_voting_frequency_' . $entity_name)); ?> ><?php _e('Unlimited', 'likebtn-like-button'); ?> *</option>
                                                                <option value="60" <?php selected('60', get_option('likebtn_settings_voting_frequency_' . $entity_name)); ?> ><?php _e('Every minute', 'likebtn-like-button'); ?> *</option>
                                                                <option value="3600" <?php selected('3600', get_option('likebtn_settings_voting_frequency_' . $entity_name)); ?> ><?php _e('Hourly', 'likebtn-like-button'); ?> *</option>
                                                                <option value="86400" <?php selected('86400', get_option('likebtn_settings_voting_frequency_' . $entity_name)); ?> ><?php _e('Daily', 'likebtn-like-button'); ?></option>
                                                                <option value="604800" <?php selected('604800', get_option('likebtn_settings_voting_frequency_' . $entity_name)); ?> ><?php _e('Weekly', 'likebtn-like-button'); ?></option>
                                                                <option value="2592000" <?php selected('2592000', get_option('likebtn_settings_voting_frequency_' . $entity_name)); ?> ><?php _e('Monthly', 'likebtn-like-button'); ?></option>
                                                                <option value="31536000" <?php selected('31536000', get_option('likebtn_settings_voting_frequency_' . $entity_name)); ?> ><?php _e('Annually', 'likebtn-like-button'); ?></option>
                                                            </select>
                                                            <p class="description">
                                                                * <?php echo strtr(
                                                                    __('Make sure that its value is larger than <a href="%url_interval%">IP address vote interval</a> of your website (default IP address vote interval is 24 hours).', 'likebtn-like-button'), 
                                                                    array('%url_interval%'=>admin_url().'admin.php?page=likebtn_settings#ip_vote_interval')
                                                                ); ?>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Display a read-only button to the post author', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_voting_author_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_voting_author_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Voting period', 'likebtn-like-button'); ?></label>
                                                        <i class="likebtn_help" title="<?php _e("After the specified period of time the button will be displayed in a read-only mode", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <select name="likebtn_voting_period_<?php echo $entity_name; ?>" class="likebtn_input voting_period" id="likebtn_voting_period">
                                                                <option value=""><?php _e('Voting is always active', 'likebtn-like-button'); ?></option>
                                                                <option value="<?php echo LIKEBTN_VOTING_PERIOD_DATE ?>" <?php selected(LIKEBTN_VOTING_PERIOD_DATE, get_option('likebtn_voting_period_' . $entity_name)); ?> ><?php _e('Voting is active till specific date', 'likebtn-like-button'); ?></option>
                                                                <option value="<?php echo LIKEBTN_VOTING_PERIOD_CREATED ?>" <?php selected(LIKEBTN_VOTING_PERIOD_CREATED, get_option('likebtn_voting_period_' . $entity_name)); ?> ><?php _e('Voting is active for a period of time since item publication', 'likebtn-like-button'); ?></option>
                                                            </select>
                                                            <div class="param_voting_period param_vp_date hidden">
                                                                <br/>
                                                                <?php _e('Date', 'likebtn-like-button'); ?>: <input type="text" name="likebtn_voting_date_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_voting_date_' . $entity_name); ?>" id="likebtn_voting_date" size="16" class="disabled" />
                                                                <p class="description">
                                                                    <?php _e('Keep in mind that you have to specify your server date and time.', 'likebtn-like-button'); ?><br/><?php _e('Current server date and time:', 'likebtn-like-button'); ?> <?php echo date("Y/m/d H:i") ?>
                                                                </p>
                                                            </div>
                                                            <div class="param_voting_period param_vp_created hidden">
                                                                <br/>
                                                                <div id="likebtn_voting_created_cntr"></div>
                                                                <input type="number" name="likebtn_voting_created_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_voting_created_' . $entity_name); ?>" id="likebtn_voting_created" class="hidden disabled" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Allow to vote for one post only', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_voting_one_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_voting_one_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="postbox likebtn_tab_extset hidden" id="likebtn_extset_tab_counter">
                                            <?php /*<h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_collapse_trigger"><small>►</small> <?php _e('Counter', 'likebtn-like-button'); ?></h3>*/ ?>
                                            <div class="inside">
                                                <table class="form-table">
                                                 <tr valign="top">
                                                        <th scope="row"><label><?php _e('Show votes counter', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_counter_show_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_counter_show_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Votes counter is clickable', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_counter_clickable_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_counter_clickable_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Counter format', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <select name="likebtn_settings_counter_frmt_<?php echo $entity_name; ?>">
                                                                <option value="" <?php selected('', get_option('likebtn_settings_counter_frmt_' . $entity_name)); ?> ><?php _e('Without thousands separator', 'likebtn-like-button'); ?> (3700)</option>
                                                                <option value="space" <?php selected('space', get_option('likebtn_settings_counter_frmt_' . $entity_name)); ?> ><?php _e('Space as thousands separator', 'likebtn-like-button'); ?> (3 700)</option>
                                                                <option value="comma" <?php selected('comma', get_option('likebtn_settings_counter_frmt_' . $entity_name)); ?> ><?php _e('Comma as thousands separator', 'likebtn-like-button'); ?> (3,700)</option>
                                                                <option value="period" <?php selected('period', get_option('likebtn_settings_counter_frmt_' . $entity_name)); ?> ><?php _e('Period as thousands separator', 'likebtn-like-button'); ?> (3.700)</option>
                                                                <option value="apo" <?php selected('apo', get_option('likebtn_settings_counter_frmt_' . $entity_name)); ?> ><?php _e('Apostrophe as thousands separator', 'likebtn-like-button'); ?> (3'700)</option>
                                                                <option value="km" <?php selected('km', get_option('likebtn_settings_counter_frmt_' . $entity_name)); ?> ><?php _e('K for thousands (3.7K), M for millions (15.2M)', 'likebtn-like-button'); ?></option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Counter mask', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_counter_padding_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_counter_padding_' . $entity_name); ?>" class="likebtn_input" />
                                                            <p class="notice update-nag"><?php _e(
           __('For example set the following mask "0000000" if you need the counter to be displayed as 0000001, 0000002 after receiving first, second and so on votes. This parameter does not set value for buttons. If you need to set number of votes for buttons please do so on Statistics tab.', 'likebtn-like-button')) ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Show zero value in counter', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_counter_zero_show_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_counter_zero_show_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Animate number counting', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_counter_count_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_counter_count_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                       
                                        <div class="postbox likebtn_tab_extset hidden" id="likebtn_extset_tab_sharing">
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr valign="top" class="plan_dependent plan_plus">
                                                        <th scope="row"><label><?php _e('Show share buttons', 'likebtn-like-button'); ?> <i class="premium_feature" title="PLUS / PRO / VIP / ULTRA"></i></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_share_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_share_enabled_' . $entity_name)); ?> />
                                                            <?php /*<span class="description"><?php _e('Use popup_disabled option to enable/disable popup.', 'likebtn-like-button'); ?></span>*/ ?>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top" class="plan_dependent plan_pro">
                                                        <th scope="row">
                                                            <label><?php _e('AddThis share buttons', 'likebtn-like-button'); ?> <i class="premium_feature" title="PLUS / PRO / VIP / ULTRA"></i></label>
                                                        </th>
                                                        <td>
                                                            <select id="settings_addthis_service_codes" class="likebtn_at16 likebtn_input" multiple="multiple" >
                                                                <?php foreach($likebtn_addthis_service_codes as $addthis_service_code): ?>
                                                                    <option value="<?php echo $addthis_service_code ?>"><?php echo $addthis_service_code ?></option>
                                                                <?php endforeach ?>
                                                            </select>

                                                            <input type="hidden" name="likebtn_settings_addthis_service_codes_<?php echo $entity_name; ?>" value="<?php echo $value_addthis_service_codes; ?>" class="likebtn_input" id="settings_addthis_service_codes_input"/>

                                                            <p class="description"><?php _e('<a href="http://www.addthis.com" target="_blank">AddThis</a> is the content sharing and social insights platform helping users to share your content and drive viral traffic.', 'likebtn-like-button'); ?>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Share buttons size', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <div class="image_toggle">
                                                                <input type="radio" name="likebtn_settings_share_size_<?php echo $entity_name; ?>" id="likebtn_settings_share_size_<?php echo $entity_name; ?>_small" value="small" <?php checked('small', get_option('likebtn_settings_share_size_' . $entity_name)); ?> >
                                                                <label for="likebtn_settings_share_size_<?php echo $entity_name; ?>_small"><img src="<?php echo _likebtn_get_public_url() ?>img/share_size/small.png" alt="<?php _e('small', 'likebtn-like-button'); ?>" title="<?php _e('small', 'likebtn-like-button'); ?>" /></label>

                                                                <input type="radio" name="likebtn_settings_share_size_<?php echo $entity_name; ?>" id="likebtn_settings_share_size_<?php echo $entity_name; ?>_medium" value="medium" <?php checked('medium', get_option('likebtn_settings_share_size_' . $entity_name)); ?><?php checked('', get_option('likebtn_settings_share_size_' . $entity_name)); ?> >
                                                                <label for="likebtn_settings_share_size_<?php echo $entity_name; ?>_medium"><img src="<?php echo _likebtn_get_public_url() ?>img/share_size/medium.png" alt="<?php _e('medium', 'likebtn-like-button'); ?>" title="<?php _e('medium', 'likebtn-like-button'); ?>" /></label>

                                                                <input type="radio" name="likebtn_settings_share_size_<?php echo $entity_name; ?>" id="likebtn_settings_share_size_<?php echo $entity_name; ?>_large" value="large" <?php checked('large', get_option('likebtn_settings_share_size_' . $entity_name)); ?>>
                                                                <label for="likebtn_settings_share_size_<?php echo $entity_name; ?>_large"><img src="<?php echo _likebtn_get_public_url() ?>img/share_size/large.png" alt="<?php _e('large', 'likebtn-like-button'); ?>" title="<?php _e('large', 'likebtn-like-button'); ?>" /></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top" class="plan_dependent plan_pro">
                                                        <th scope="row"><label><?php _e('AddThis <a href="https://www.addthis.com/settings/publisher" target="_blank">Profile ID</a>', 'likebtn-like-button'); ?> <i class="premium_feature" title="PLUS / PRO / VIP / ULTRA"></i></label>
                                                            <i class="likebtn_help" title="<?php _e("Enter your AddThis Profile ID to collect sharing statistics and view it on AddThis analytics page", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_addthis_pubid_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_addthis_pubid_' . $entity_name); ?>" class=" likebtn_input likebtn_placeholder" placeholder="ra-511b51aa3d843ec4" />
                                                        </td>
                                                    </tr>
                                                    <?php if (empty($likebtn_entities_config['likebtn_og'][$entity_name]['hide'])): ?>
                                                        <tr valign="top">
                                                            <th scope="row"><label><?php _e('Add Open Graph meta tags', 'likebtn-like-button'); ?></label></th>
                                                            <td>
                                                                <input type="checkbox" name="likebtn_og_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_og_' . $entity_name_clean)); ?> />
                                                                <p class="description"><?php _e('When sharing an item on Facebook it picks up the data (title, thumbnail image, description) from <a href="http://www.addthis.com/blog/2014/07/03/optimize-your-content-recommendations-with-open-graph-tags-2/#.Vn-z8Pl95nJ" target="_blank">Open Graph</a> meta tags. If these tags are not present the social network attempts to fetch information from the page itself. In order to figure out what Facebook sees use <a href="https://developers.facebook.com/tools/debug/" target="_blank">Facebook’s URL Debugger</a>', 'likebtn-like-button'); ?>
                                                                    <?php /*<br/><br/>
                                                                    <?php _e('Open Graph meta tags can be added to the single post/page only. There is no way to add specific meta tag for each post in the list.', 'likebtn-like-button'); ?>*/ ?>
                                                                     
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    <?php endif ?>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="postbox likebtn_tab_extset hidden" id="likebtn_extset_tab_tooltips">
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Always show Like button tooltip', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_tooltip_like_show_always_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_tooltip_like_show_always_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Always show Dislike button tooltip', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_tooltip_dislike_show_always_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_tooltip_dislike_show_always_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="postbox likebtn_tab_extset hidden"  id="likebtn_extset_tab_misc">
                                            <?php /*<h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_collapse_trigger"><small>►</small> <?php _e('Events', 'likebtn-like-button'); ?></h3>*/ ?>
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr valign="top">
                                                        <th colspan="2" scope="row"><h3 class="likebtn_subtitle"><?php _e('Loading', 'likebtn-like-button'); ?></h3></th>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Lazy load', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e("If button is outside a viewport it is loaded when user scrolls to it", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_lazy_load_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_lazy_load_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Show loader', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e("Show loader while loading a button", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_loader_show_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_loader_show_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Loader image', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e("URL of the image to use as loader image (leave empty to display default image)", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_loader_image_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_loader_image_' . $entity_name); ?>" class="likebtn_input" />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th colspan="2" scope="row"><h3 class="likebtn_subtitle"><?php _e('Events', 'likebtn-like-button'); ?></h3></th>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row">
                                                            <label>
                                                                <?php _e('JavaScript callback function', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td class="description">
                                                            <input type="text" name="likebtn_settings_event_handler_<?php echo $entity_name; ?>" value="<?php _e(get_option('likebtn_settings_event_handler_' . $entity_name)); ?>" class="likebtn_input" />
                                                            <p class="description">
                                                                <?php _e('The provided function receives the event object as its single argument. The event object has the following properties:', 'likebtn-like-button'); ?><br/>
                                                                <code>type</code> – <?php _e('indicates which event was dispatched:', 'likebtn-like-button'); ?><br/>
                                                                ● "likebtn.loaded"<br/>
                                                                ● "likebtn.like"<br/>
                                                                ● "likebtn.unlike"<br/>
                                                                ● "likebtn.dislike"<br/>
                                                                ● "likebtn.undislike"<br/>
                                                                <code>settings</code> – <?php _e('button settings', 'likebtn-like-button'); ?><br/>
                                                                <code>wrapper</code> – <?php _e('button DOM-element', 'likebtn-like-button'); ?>
                                                            </p>
                                                        </td>
                                                    </tr>

                                                </table>
                                            </div>
                                        </div>

                                        <div class="postbox likebtn_tab_extset hidden" id="likebtn_extset_tab_texts">
                                            <?php /*<h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_collapse_trigger"><small>►</small> <?php _e('Texts', 'likebtn-like-button'); ?></h3>*/ ?>
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Right-to-left (RTL)', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_settings_rtl_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_settings_rtl_' . $entity_name)); ?> />
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Like button text after liking', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_after_like_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_after_like_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Like', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Dislike button text after disliking', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_after_dislike_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_after_dislike_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Dislike', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Like button tooltip', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_like_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_like_tooltip_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('I like this', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Dislike button tooltip', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_dislike_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_dislike_tooltip_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('I dislike this', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Like button tooltip after liking', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_unlike_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_unlike_tooltip_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Unlike', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Dislike button tooltip after disliking', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_undislike_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_undislike_tooltip_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Undislike', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Text before share buttons', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_share_text_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_share_text_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Would you like to share?', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Popup close button text', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_popup_close_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_popup_close_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Close', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Popup text when sharing disabled', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_popup_text_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_popup_text_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Glad you liked it!', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row"><label><?php _e('Text before donate buttons', 'likebtn-like-button'); ?></label></th>
                                                        <td>
                                                            <input type="text" name="likebtn_settings_i18n_popup_donate_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_settings_i18n_popup_donate_' . $entity_name); ?>" class="likebtn_input likebtn_placeholder" placeholder="<?php _e('Show gratitude in the form of a donation', 'likebtn-like-button'); ?>"/>
                                                        </td>
                                                    </tr>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row" colspan="2">
                                                            <a href="javascript:likebtnPopup('<?php _e('http://likebtn.com/en/translate-like-button-widget', 'likebtn-like-button'); ?>');void(0);"><?php _e('Send us Translation', 'likebtn-like-button'); ?></a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="postbox likebtn_tab_extset hidden" id="likebtn_extset_tab_buddypress">
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr>
                                                        <th scope="row"><label><?php _e('Display notifications on likes & dislikes for users', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e("Display a notification for the author when other authenticated user likes or dislikes author's content", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_bp_notify_<?php echo $entity_name_clean; ?>" value="1" <?php checked('1', get_option('likebtn_bp_notify_' . $entity_name_clean)) ?> <?php if (!_likebtn_is_bp_active()): ?>disabled="disabled"<?php endif ?> />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label><?php _e('Record vote action in activity stream', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_bp_activity_<?php echo $entity_name_clean; ?>" value="1" class="bp_activity" <?php checked('1', get_option('likebtn_bp_activity_' . $entity_name_clean)) ?> <?php if (!_likebtn_is_bp_active()): ?>disabled="disabled"<?php endif ?> />
                                                            <br/><p class="description">
                                                                ● <?php _e('If you want to record vote actions for forums in BuddyPress groups, enable this option for (bbPress) Forum Posts', 'likebtn-like-button'); ?><br/>
                                                                ● <?php _e('Votes in private groups are NOT displayed in the public activity stream', 'likebtn-like-button'); ?>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label><?php _e('Show Votes filter in activity stream', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_bp_filter" value="1" class="bp_activity" <?php checked('1', get_option('likebtn_bp_filter')) ?> <?php if (!_likebtn_is_bp_active()): ?>disabled="disabled"<?php endif ?> />
                                                            <br/><p class="description">
                                                                <?php _e('Global option for all post types', 'likebtn-like-button'); ?>
                                                            </p>
                                                        </td>
                                                    </tr>


                                                    <tr class="param_bp_hide_sitewide">
                                                        <th scope="row"><label><?php _e('Hide vote actions from sitewide activity', 'likebtn-like-button'); ?></label>
                                                            <i class="likebtn_help" title="<?php _e("Activity will be private and only visible for the logged in user when viewing his profile activities", 'likebtn-like-button'); ?>">&nbsp;</i>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_bp_hide_sitewide_<?php echo $entity_name_clean; ?>" value="1" <?php checked('1', get_option('likebtn_bp_hide_sitewide_' . $entity_name_clean)) ?> <?php if (!_likebtn_is_bp_active()): ?>disabled="disabled"<?php endif ?> />
                                                        </td>
                                                    </tr>
                                                    
                                                    <?php if (_likebtn_is_bp_active()): ?>
                                                    <tr class="param_bp_image">
                                                        <th scope="row"><label><?php _e('Include item snippet in activity stream', 'likebtn-like-button'); ?></label>
                                                        </th>
                                                        <td>
                                                            <input type="checkbox" name="likebtn_bp_image_<?php echo $entity_name_clean; ?>" value="1" <?php checked('1', get_option('likebtn_bp_image_' . $entity_name_clean)) ?> <?php if (!_likebtn_is_bp_active()): ?>disabled="disabled"<?php endif ?> />
                                                            <br/><br/>
                                                            <?php _e('Snippet template', 'likebtn-like-button'); ?>:
                                                            <br/>
                                                            <textarea name="likebtn_bp_snippet_tpl_<?php echo $entity_name; ?>" class="likebtn_input" rows="7"><?php if (get_option('likebtn_bp_snippet_tpl_' . $entity_name)): ?><?php echo htmlspecialchars(get_option('likebtn_bp_snippet_tpl_' . $entity_name)); ?><?php elseif (isset($likebtn_entities_config['bp_snippet_tpl'][$entity_name]['value'])): ?><?php echo htmlspecialchars($likebtn_entities_config['bp_snippet_tpl'][$entity_name]['value']); ?><?php else: ?><?php echo htmlspecialchars(LIKEBTN_BP_SNIPPET_TPL); ?><?php endif ?></textarea>
                                                            <p class="description">
                                                                <?php _e('Available placeholders', 'likebtn-like-button'); ?>: @image_thumbnail@, @title@, @excerpt@, @content@
                                                            </p>
                                                            <br/>
                                                            <img src="<?php echo _likebtn_get_public_url() ?>img/buddypress_activity.png" class="likebtn_input" />
                                                            <br/><br/>
                                                            <p class="description">
                                                                <small><?php _e('Keep in mind that BuddyPress has a list of allowed HTML-tags and attributes for activity snippets. If you need extra tags in snippet, add the following code to your theme\'s functions.php:', 'likebtn-like-button'); ?> </small>
                                                                <textarea class="likebtn_input disabled" rows="4" readonly="readonly">function custom_bp_activity_allowed_tags( $allowedtags ) {
    // New tags and attributes
    if (empty($allowedtags['td'])) {
        $allowedtags['td'] = array();
    }
    if (empty($allowedtags['td']['style'])) {
        $allowedtags['td']['style'] = array();
    }
    return $allowedtags;
}
add_filter('bp_activity_allowed_tags', 'custom_bp_activity_allowed_tags');</textarea>
                                                            </p>
                                                            <?php /*
                                                            <?php /*<br/>
                                                            <small class="description"><a target="_blank" href="https://likebtn.com/en/wordpress-like-button-plugin#bb_activity_snippet_template"><?php _e('How to alter snippet template?', 'likebtn-like-button'); ?></a></small>*/ ?>
                                                        </td>
                                                    </tr>
                                                    <?php endif ?>
                                                </table>
                                            </div>
                                        </div>
                                            <?php /*
                                            </div>
                                        </div>*/ ?>
                                        <div class="likebtn_reset_wrapper">
                                            <span class="likebtn_sc_trgr"><a href="javascript:likebtnToggleShortcode('likebtn_sc_wr')"><?php _e('Get shortcode', 'likebtn-like-button'); ?></a> <small>▼</small></span>
                                            <input class="button-secondary" type="button" name="Reset" value="<?php _e('Reset', 'likebtn-like-button'); ?>" onclick="return resetSettings('<?php echo $entity_name; ?>', reset_settings)" />
                                        </div>
                                        <div id="likebtn_sc_wr" class="postbox">
                                            <br/>
                                            <textarea class="likebtn_input likebtn_disabled" rows="5" id="likebtn_sc" readonly="readonly"></textarea>
                                            <table class="form-table">
                                                <tr>
                                                    <th scope="row">
                                                        <label><?php _e('Button identifier', 'likebtn-like-button'); ?></label>
                                                    </th>
                                                    <td>
                                                        <input type="radio" name="likebtn_identifier_type" value="post_id" checked="checked"/> <?php _e('Post ID', 'likebtn-like-button'); ?>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <input type="radio" name="likebtn_identifier_type" value="custom" /> <?php _e('Custom', 'likebtn-like-button'); ?>&nbsp;
                                                        <input type="text" id="likebtn_sc_identifier" value="item_1" class="likebtn_sc_identifier_custom hidden"/>
                                                    </td>
                                                </tr>
                                                <tr class="likebtn_sc_identifier_custom hidden">
                                                    <th scope="row" class="no-padding-top">
                                                        &nbsp;
                                                    </th>
                                                    <td class="no-padding-top">
                                                        <p class="likebtn_error">
                                                            <?php _e('Identifier must be unique for all the buttons inserted using shortcode, otherwise buttons will reflect the same number of likes.', 'likebtn-like-button'); ?>
                                                        </p>
                                                        <p class="likebtn_error">
                                                            <?php _e('If custom identifier is used you will see button identifier in statistics and most liked content widget instead of post title. You also will be unable to sort posts by likes.', 'likebtn-like-button'); ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>

            <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Changes', 'likebtn-like-button'); ?>" <?php /*if (get_option('likebtn_show_' . $entity_name) == '1'): ?>style="display: none"<?php endif*/ ?> /><br/><br/>
        </form>

    </div>
    <?php

    _likebtn_admin_footer();
}

function likebtn_check_max_vars()
{
    $max_vars = (int)ini_get('max_input_vars');
    if (!$max_vars || $max_vars >= LIKEBTN_MAX_INPUT_VARS) {
        return;
    }

    // Try to set
    @ini_set('max_input_vars', LIKEBTN_MAX_INPUT_VARS);
    $max_vars = (int)ini_get('max_input_vars');

    if (!$max_vars || $max_vars >= LIKEBTN_MAX_INPUT_VARS) {
        return;
    }

    _likebtn_notice(
        strtr(__('Value of %tag_start%max_input_vars%tag_end% parameter in your PHP is too low: %current%. You may experience problems saving settings. Make sure to set max_input_vars value to at least %tag_start%%minimum%%tag_end% (contact your hosting provider for help if needed).', 'likebtn-like-button'), array('%current%' => $max_vars, '%minimum%' => LIKEBTN_MAX_INPUT_VARS, '%tag_start%' => '<strong>', '%tag_end%' => '</strong>')),
        'error'
    );
}