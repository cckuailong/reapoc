<?php
global $wpdm_message, $btnclass;


use WPDM\__\__;
use WPDM\__\Email;
use WPDM\__\Crypt;
use WPDM\__\Messages;
use WPDM\__\Session;
use WPDM\__\Template;
use WPDM\__\TempStorage;
use WPDM\Category\CategoryController;
use WPDM\Package\Package;
use WPDM\Package\PackageLocks;

function wpdm_zip_package($package)
{
    return WPDM()->package->zip($package['ID']);
}

/**
 * Download contents as a file
 * @param $filename
 * @param $content
 */
function wpdm_download_data($filename, $content)
{
    WPDM()->fileSystem->downloadData($filename, $content);
}


/**
 * @usage Create ZIP from given file list
 * @param $files
 * @param $zipname
 * @return bool|string
 */
function wpdm_zip_files($files, $zipname)
{
    return \WPDM\__\FileSystem::zipFiles($files, $zipname);
}

/**
 * @usage Download Given File
 * @param $filepath
 * @param $filename
 * @param int $speed
 * @param int $resume_support
 * @param array $extras
 */

function wpdm_download_file($filepath, $filename, $speed = 0, $resume_support = 1, $extras = array())
{
    do_action("wpdm_download_success", $extras);
    if (!file_exists($filepath)) Messages::fullPage("Download Error", "<div class='card bg-danger text-white text-left' style='min-width: 300px'><div class='card-header'>" . __("Download Error", "download-manager") . "</div><div class='card-body'>" . __("File Not Found!", "download-manager") . "</div></div>");
    if (isset($_GET['play'])) $extras['play'] = $_GET['play'];
    \WPDM\__\FileSystem::downloadFile($filepath, $filename, $speed, $resume_support, $extras);
}


/**
 * @param $id
 * @usage Returns the user roles who has access to specified package
 * @return array|mixed
 */
function wpdm_allowed_roles($id)
{
    return WPDM()->package->allowedRoles($id);
}


/**
 * @usage Check if current user has access to package or category
 * @param $id
 * @param string $type
 *
 * @return bool
 */
function wpdm_user_has_access($id, $type = 'package')
{
    return WPDM()->package->userCanAccess($id, $type);
}

/**
 * @usage Verify Email Address
 * @param $email
 * @return bool
 */
function wpdm_verify_email($email)
{
    $dns_verify = get_option('__wpdm_verify_dns', 0);
    $blocked_domains = explode("\n", str_replace("\r", "", get_option('__wpdm_blocked_domains', '')));
    $blocked_emails = explode("\n", str_replace("\r", "", get_option('__wpdm_blocked_emails', '')));
    $eparts = explode("@", $email);
    if (!isset($eparts[1])) return false;
    $domain = $eparts[1];
    if (!is_email($email)) return false;
    if (in_array($email, $blocked_emails)) return false;
    if (in_array($domain, $blocked_domains)) return false;
    if ($dns_verify && !checkdnsrr($domain, 'MX')) return false;
    return true;
}


/**
 * @usage Count files in a package
 * @param $id
 * @return int
 */
function wpdm_package_filecount($id)
{
    return WPDM()->package->fileCount($id);

}

/**
 * @usage Calculate package size
 * @param $id
 * @return float|int|mixed|string
 */
function wpdm_package_size($id)
{
    return WPDM()->package->Size($id);
}

/**
 * @usage Calculate file size
 * @param $file
 * @return float|int|mixed|string
 */
function wpdm_file_size($file)
{
    if (file_exists($file))
        $size = filesize($file);
    else if (file_exists(UPLOAD_DIR . $file))
        $size = filesize(UPLOAD_DIR . $file);
    else $size = 0;
    $size = $size / 1024;
    if ($size > 1024) $size = number_format($size / 1024, 2) . ' MB';
    else $size = number_format($size, 2) . ' KB';
    return $size;
}

/**
 * Get post excerpt
 * @param $post
 * @param int $length
 * @param bool $word_break
 * @param string $continue
 * @return string
 */
function wpdm_get_excerpt($post, $length = 100, $word_break = false, $continue = "...")
{
    $post = is_object($post) ? $post : get_post($post);
    if (!is_object($post)) return '';
    $excerpt = get_the_excerpt($post);
    if (!$excerpt) $excerpt = $post->post_content;
    $excerpt = strip_tags($excerpt);
    $excerpt = substr(trim($excerpt), 0, $length);
    if (!$word_break) {
        $excerpt = explode(" ", $excerpt);
        array_pop($excerpt);
        $excerpt = implode(" ", $excerpt);
    }
    return $excerpt . $continue;
}

/**
 * @param $file
 * @return array|mixed
 */
function wpdm_basename($file)
{
    if (strpos("~" . $file, "\\"))
        $basename = explode("\\", $file);
    else
        $basename = explode("/", $file);
    $basename = end($basename);
    return $basename;
}

/**
 * @usage Generate thumbnail dynamically
 * @param $path
 * @param $size
 * @return mixed
 */

function wpdm_dynamic_thumb($path, $size, $crop = false, $cache = true)
{
    return \WPDM\__\FileSystem::imageThumbnail($path, $size[0], $size[1], $crop, $cache);
}


/**
 * @usage Return Post Thumbail
 * @param string $size
 * @param bool $echo
 * @param null $extra
 * @return mixed|string|void
 */
function wpdm_post_thumb($size = '', $echo = true, $extra = null)
{
    global $post;
    $size = $size ? $size : 'thumbnail';
    $class = isset($extra['class']) ? $extra['class'] : '';
    $crop = isset($extra['crop']) ? $extra['crop'] : get_option('__wpdm_crop_thumbs', false);
    $alt = $post->post_title;
    if (is_array($size)) {
        $large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $large_image_url = isset($large_image_url[0]) ? $large_image_url[0] : '';
        if ($large_image_url == '' && isset($extra['default'])) $large_image_url = $extra['default'];
        if ($large_image_url != '') {
            $path = str_replace(site_url('/'), ABSPATH, $large_image_url);
            $thumb = wpdm_dynamic_thumb($path, $size, $crop);
            $thumb = str_replace(ABSPATH, site_url('/'), $thumb);
            $alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
            $img = "<img src='" . $thumb . "' alt='{$alt}' class='{$class}' />";
            if ($echo) {
                echo $img;
                return true;
            } else
                return $img;
        }
    }
    if ($echo && has_post_thumbnail($post->ID))
        echo get_the_post_thumbnail($post->ID, $size, $extra);
    else if (!$echo && has_post_thumbnail($post->ID))
        return get_the_post_thumbnail($post->ID, $size, $extra);
    else if ($echo)
        echo "";
    else
        return "";
}

/**
 * @usage Generate Thumnail for the given package
 * @param $post
 * @param string $size
 * @param bool $echo
 * @param null $extra
 * @return mixed|string|void
 */
function wpdm_thumb($post, $size = '', $echo = true, $extra = null)
{
    if (is_int($post))
        $post = get_post($post);
    if(!$post) return '';
    $size = $size ? $size : 'thumbnail';
    $class = isset($extra['class']) ? $extra['class'] : '';
    $crop = isset($extra['crop']) ? $extra['crop'] : get_option('__wpdm_crop_thumbs', false);
    $alt = $post->post_title;
    if (is_array($size)) {
        $large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        if (!$large_image_url) return '';
        $large_image_url = $large_image_url[0];
        if ($large_image_url != '') {
            $thumb = wpdm_dynamic_thumb($large_image_url, $size, $crop);
            $thumb = str_replace(ABSPATH, site_url('/'), $thumb);
            $alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
            if ($echo === 'url') return $thumb;
            if ($alt === '') $alt = esc_attr(strip_tags(get_the_title($post->ID)));
            $img = "<img src='" . $thumb . "' alt='{$alt}' class='{$class}' />";
            if ($echo) {
                echo $img;
                return;
            } else
                return $img;
        }
    }
    if ($echo && has_post_thumbnail($post->ID))
        echo get_the_post_thumbnail($post->ID, $size, $extra);
    else if (!$echo && has_post_thumbnail($post->ID))
        return get_the_post_thumbnail($post->ID, $size, $extra);
    else if ($echo)
        echo "";
    else
        return "";
}

function wpdm_media_field($data)
{
    ob_start();
    $attrs = '';
    if (isset($data['attrs'])) {
        foreach ($data['attrs'] as $attr => $value) {
            $attrs .= "$attr='$value' ";
        }
    }
    ?>
    <div class="input-group">
        <input placeholder="<?php echo $data['placeholder']; ?>" <?php echo $attrs; ?> type="url"
               name="<?php echo $data['name']; ?>"
               id="<?php echo isset($data['id']) ? $data['id'] : ($id = uniqid()); ?>" class="form-control"
               value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>
        <span class="input-group-btn">
                        <button class="btn btn-secondary btn-media-upload" type="button"
                                rel="#<?php echo isset($data['id']) ? $data['id'] : $id; ?>"><i
                                    class="far fa-image"></i></button>
                    </span>
    </div>
    <?php
    return ob_get_clean();
}

function wpdm_image_selector($data)
{
    ob_start();
    $attrs = '';
    if (isset($data['attrs'])) {
        foreach ($data['attrs'] as $attr => $value) {
            $attrs .= "$attr='$value' ";
        }
    }
    $id = uniqid();
    ?>
    <div class="panel panel-default text-center image-selector-panel" style="width: 250px">
        <div class="panel-body">
            <img id="<?php echo isset($data['id']) ? $data['id'] : $id; ?>"
                 src="<?php echo isset($data['value']) && $data['value'] != '' ? $data['value'] : WPDM_BASE_URL . 'assets/images/image.png'; ?>"/>
        </div>
        <div class="panel-footer">
            <input id="<?php echo isset($data['id']) ? $data['id'] : $id; ?>_hidden" type="hidden"
                   name="<?php echo $data['name']; ?>"
                   value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>
            <button class="btn btn-info btn-block btn-image-selector" type="button"
                    rel="#<?php echo isset($data['id']) ? $data['id'] : $id; ?>"><i
                        class="far fa-image"></i> <?php isset($data['btnlabel']) ? $data['btnlabel'] : _e('Select Image', 'download-manager'); ?>
            </button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function wpdm_image_uploader($data)
{
    ob_start();
    $attrs = '';
    if (isset($data['attrs'])) {
        foreach ($data['attrs'] as $attr => $value) {
            $attrs .= "$attr='$value' ";
        }
    }
    $default = isset($data['default']) ? $data['default'] : WPDM_BASE_URL . 'assets/images/image.png';
    $id = uniqid();
    ?>
    <div id="wpdm-upload-ui" class="panel panel-default text-center image-selector-panel" style="width: 250px">
        <div class="panel-header text-muted" id="del-img">
            Delete Image
        </div>
        <div id="wpdm-drag-drop-area">
            <div class="panel-body">
                <img id="<?php echo isset($data['id']) ? $data['id'] : $id; ?>"
                     src="<?php echo isset($data['value']) && $data['value'] != '' ? $data['value'] : $default; ?>"/>
            </div>
            <div class="panel-footer">
                <input id="<?php echo isset($data['id']) ? $data['id'] : $id; ?>_hidden" type="hidden"
                       name="<?php echo $data['name']; ?>"
                       value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>

                <button id="wpdm-browse-button" style="font-size: 9px;text-transform: unset" type="button"
                        class="btn btn-info btn-block"><?php echo isset($data['btnlabel']) ? $data['btnlabel'] : __('SELECT IMAGE', 'download-manager'); ?></button>
                <div class="progress" id="wmprogressbar"
                     style="height: 30px !important;border-radius: 3px !important;margin: 0;position: relative;background: #0d406799;display: none;box-shadow: none">
                    <div id="wmprogress" class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                         style="width: 0%;line-height: 30px;background-color: #007bff"></div>
                    <div class="fetfont"
                         style="font-size:9px;position: absolute;line-height: 30px;height: 30px;width: 100%;z-index: 999;text-align: center;color: #ffffff;font-weight: 800;letter-spacing: 1px">
                        UPLOADING... <span id="wmloaded">0</span>%
                    </div>
                </div>

                <?php

                $plupload_init = array(
                    'runtimes' => 'html5,silverlight,flash,html4',
                    'browse_button' => 'wpdm-browse-button',
                    'container' => 'wpdm-upload-ui',
                    'drop_element' => 'wpdm-drag-drop-area',
                    'file_data_name' => 'wpdm_file',
                    'multiple_queues' => false,
                    'url' => admin_url('admin-ajax.php'),
                    'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
                    'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                    'filters' => array(array('title' => __('Allowed Files'), 'extensions' => 'png,jpg,jpeg')),
                    'multipart' => true,
                    'urlstream_upload' => true,

                    // additional post data to send to our ajax hook
                    'multipart_params' => array(
                        '_ajax_nonce' => wp_create_nonce(NONCE_KEY),
                        'action' => $data['action'],            // the ajax action name
                    ),
                );

                $plupload_init['max_file_size'] = wp_max_upload_size() . 'b';

                // we should probably not apply this filter, plugins may expect wp's media uploader...
                $plupload_init = apply_filters('plupload_init', $plupload_init); ?>
                <style>
                    #del-img {
                        position: absolute;
                        width: 100%;
                        padding: 5px;
                        z-index: 999999;
                        background: rgba(255, 255, 255, 0.9);
                        display: none;
                        cursor: pointer;
                    }

                    #wpdm-upload-ui:hover #del-img {
                        display: block;
                    }
                </style>
                <script type="text/javascript">

                    jQuery(function ($) {


                        var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                        uploader.bind('Init', function (up) {
                            var uploaddiv = $('#wpdm-upload-ui');

                            if (up.features.dragdrop) {
                                uploaddiv.addClass('drag-drop');
                                $('#drag-drop-area')
                                    .bind('dragover.wp-uploader', function () {
                                        uploaddiv.addClass('drag-over');
                                    })
                                    .bind('dragleave.wp-uploader, drop.wp-uploader', function () {
                                        uploaddiv.removeClass('drag-over');
                                    });

                            } else {
                                uploaddiv.removeClass('drag-drop');
                                $('#drag-drop-area').unbind('.wp-uploader');
                            }
                        });

                        uploader.init();

                        uploader.bind('Error', function (uploader, error) {
                            wpdm_bootModal('Error', error.message);
                            $('#wmprogressbar').hide();
                            $('#wpdm-browse-button').show();
                        });


                        uploader.bind('FilesAdded', function (up, files) {
                            /*var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10); */

                            $('#wpdm-browse-button').hide(); /*attr('disabled', 'disabled'); */
                            $('#wmprogressbar').show();

                            plupload.each(files, function (file) {
                                $('#wmprogress').css('width', file.percent + "%");
                                $('#wmloaded').html(file.percent);
                                /*jQuery('#wpdm-browse-button').hide(); //.html('<span id="' + file.id + '"><i class="fas fa-sun fa-spin"></i> Uploading (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') </span>');*/
                            });

                            up.refresh();
                            up.start();
                        });

                        uploader.bind('UploadProgress', function (up, file) {
                            /*jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));*/
                            $('#wmprogress').css('width', file.percent + "%");
                            $('#wmloaded').html(file.percent);
                        });


                        uploader.bind('FileUploaded', function (up, file, response) {
                            res = JSON.parse(response.response);
                            $('#<?php echo isset($data['id']) ? $data['id'] : $id; ?>').attr('src', res.image_url);
                            $('#wmprogressbar').hide();
                            $('#wpdm-browse-button').show();


                        });

                        $('#del-img').on('click', function () {
                            $(this).html('<i class="fa fa-sun fa-spin"></i> Deleting...');
                            $.post(wpdm_url.ajax, {action: 'delete_<?php echo $data['name']; ?>'}, res => {
                                $('#<?php echo isset($data['id']) ? $data['id'] : $id; ?>').attr('src', '<?php echo WPDM_BASE_URL . 'assets/images/image.png'; ?>');
                                $('#<?php echo isset($data['id']) ? $data['id'] : $id; ?>_hidden').val('');
                                $('#del-img').html('Delete Image');
                            });
                        });

                    });

                </script>
                <div id="filelist"></div>

                <div class="clear"></div>

            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}


/**
 * @usage Generate option fields
 * @param $data
 * @return mixed|string
 */
function wpdm_option_field($data)
{
    $desc = isset($data['description']) ? "<em class='note'>{$data['description']}</em>" : "";
    $class = isset($data['class']) ? $data['class'] : "";
    $data['placeholder'] = isset($data['placeholder']) ? $data['placeholder'] : '';
    switch ($data['type']):
        case 'text':
            return "<input type='text' name='$data[name]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
            break;
        case 'select':
        case 'dropdown':
            $html = "<select name='{$data['name']}'  id='{$data['id']}' class='form-control {$class}' style='width:100%;min-width:150px;' >";
            foreach ($data['options'] as $value => $label) {

                $html .= "<option value='{$value}' " . selected($data['selected'], $value, false) . ">$label</option>";
            }
            $html .= "</select>";
            return $html . $desc;
            break;
        case 'radio':
            $html = "";
            foreach ($data['options'] as $value => $label) {
                $html .= "<label style='display: inline-block;margin-right: 5px'><input type='radio' name='{$data['name']}' class='{$class}' value='{$value}' " . selected($data['selected'], $value, false) . " /> $label</label>";
            }
            $html .= "";
            return $html . $desc;
            break;
        case 'notice':
            return "<div class='alert alert-info' style='margin: 0'>$data[notice]</div>" . $desc;
        case 'textarea':
            return "<textarea name='$data[name]' id='$data[id]' class='form-control {$class}' style='min-height: 100px'>$data[value]</textarea>$desc";
            break;
        case 'checkbox':
            return "<input type='hidden' name='$data[name]' value='0' /><input type='checkbox' class='{$class}' name='$data[name]' id='$data[id]' value='$data[value]' " . checked($data['checked'], $data['value'], false) . " />" . $desc;
            break;
        case 'callback':
            return call_user_func($data['dom_callback'], $data['dom_callback_params']) . $desc;
            break;
        case 'heading':
            return "<h3>" . $data['label'] . "</h3>";
            break;
        case 'media':
            return wpdm_media_field($data);
            break;
        default:
            return "<input type='{$data['type']}' name='$data[name]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
            break;
            break;
    endswitch;
}

/**
 * @param $options
 * @return string
 */
function wpdm_option_page($options)
{
    $html = "<div class='wpdm-settings-fields'>";
    foreach ($options as $id => $option) {
        if (!isset($option['id'])) $option['id'] = $id;
        if (!isset($option['name'])) $option['name'] = $id;
        if (!isset($option['label'])) $option['label'] = '';
        if (in_array($option['type'], array('checkbox', 'radio')))
            $html .= "<div class='form-group'><label>" . wpdm_option_field($option) . " {$option['label']}</label></div>";
        else if ($option['type'] == 'heading')
            $html .= "<h3>{$option['label']}</h3>";
        else
            $html .= "<div class='form-group'><label>{$option['label']}</label>" . wpdm_option_field($option) . "</div>";
    }
    $html .= "</div>";
    return $html;
}


/**
 * @param $name
 * @param $options
 * @return string
 */
function wpdm_settings_section($name, $options)
{
    return "<div class='panel panel-default'><div class='panel-heading'>{$name}</div><div class='panel-body'>" . wpdm_option_page($options) . "</div></div>";
}


/**
 * @usage Get All Custom Data of a Package
 * @param $pid
 * @return array
 */
function wpdm_custom_data($pid)
{
    return WPDM()->package->metaData($pid);
}

/**
 * @usage Organize package data using all available variable
 * @param $vars
 * @param string $template
 * @return array
 */
function wpdm_setup_package_data($vars, $template = '')
{
    if (isset($vars['formatted'])) return $vars;
    if (!isset($vars['ID'])) return $vars;
    $pack = new Package($vars['ID']);
    $pack->prepare($vars['ID'], $template);
    return $pack->packageData;
}

/**
 * @usage Check if a package is locked or public
 * @param $id
 * @return bool
 */
function wpdm_is_locked($id)
{

    return WPDM()->package->isLocked($id);

}


/**
 * @usage Fetch link/page template and return generated html
 * @param $template
 * @param $vars
 * @param string $type
 * @return mixed|string|void
 */
function FetchTemplate($template, $vars, $type = 'link')
{
    return WPDM()->package->fetchTemplate($template, $vars, $type);
}

/**
 * @usage Fetch link/page template and return generated html
 * @param $template
 * @param $vars
 * @param string $type
 * @return mixed|string|void
 */
function wpdm_fetch_template($template, $vars, $type = 'link')
{
    return WPDM()->package->fetchTemplate($template, $vars, $type);
}

/**
 * @usage Callback function for [wpdm_login_form] short-code
 * @return string
 */
function wpdm_loginform()
{
    return wpdm_login_form(array('redirect' => $_SERVER['REQUEST_URI']));
}


/**
 * @return bool
 */
function wpdm_is_ajax()
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        return true;
    return false;
}


/**
 * @usage Get Package Data By Package ID
 * @param $ID
 * @return bool|mixed|null|void|WP_Post
 */
function wpdm_get_package($ID)
{
    return WPDM()->package->get($ID);
}

/**
 * @usage Get download manager package data
 * @param $ID
 * @param $meta
 * @return mixed
 */
function get_package_data($ID, $key, $default = '')
{
    $data = WPDM()->package->get($ID, $key);
    $data = $data ? $data : $default;
    return $data;
}

/**
 * @usage Show Login Form
 */
function wpdm_login_form($params = array())
{
    return WPDM()->user->login->form($params);
}

function wpdm_user_dashboard_url($params = array())
{
    $id = get_option('__wpdm_user_dashboard', 0);
    if ($id > 0) {
        $url = add_query_arg($params, get_permalink($id));
    } else $url = home_url('/');
    return $url;
}

function wpdm_registration_url()
{
    $id = get_option('__wpdm_register_url', 0);
    if ($id > 0) {
        $url = get_permalink($id);

    } else $url = wp_registration_url();
    return $url;
}

function wpdm_login_url($redirect = '')
{
    $id = get_option('__wpdm_login_url', 0);
    if ($id > 0) {
        $url = get_permalink($id);
        if ($redirect != '')
            $url .= (strstr($url, '?') ? '&' : '?') . 'redirect_to=' . $redirect;

    } else $url = wp_login_url($redirect);
    return $url;
}

function wpdm_lostpassword_url()
{
    return add_query_arg(array('action' => 'lostpassword'), wpdm_login_url());
}

function wpdm_logout_url($redirect = '')
{
    $logout_url = home_url("/?logout=" . wp_create_nonce(NONCE_KEY));
    return $redirect != '' ? add_query_arg(array('redirect_to' => $redirect), $logout_url) : $logout_url;
}

function wpdm_rest_url($request)
{
    return get_rest_url(null, "wpdm/{$request}");
}

/**
 * @usage Returns download manager template file path
 * @param $file
 * @param string $tpldir
 * @return string
 */
function wpdm_tpl_path($file, $tpldir = '', $fallback = '')
{
    return Template::locate($file, $tpldir, $fallback);
}

/**
 * @usage Returns download manager template file path
 * @param $file
 * @param string $tpldir
 * @return string
 */
function wpdm_admin_tpl_path($file, $tpldir = '', $fallback = '')
{
    if (file_exists(get_stylesheet_directory() . '/download-manager/admin/' . $file))
        $path = get_stylesheet_directory() . '/download-manager/admin/' . $file;
    else if (file_exists(get_template_directory() . '/download-manager/admin/' . $file))
        $path = get_template_directory() . '/download-manager/admin/' . $file;
    else if ($tpldir != '' && file_exists($tpldir . '/' . $file))
        $path = $tpldir . '/' . $file;
    else if ($tpldir != '' && file_exists(get_template_directory() . '/download-manager/admin/' . $tpldir . '/' . $file))
        $path = get_template_directory() . '/download-manager/admin/' . $tpldir . '/' . $file;
    else $path = WPDM_BASE_DIR . "src/Admin/views/" . $file;

    /* Fallack template directory*/
    if ($fallback != '' && !file_exists($path))
        $path = $fallback . $file;

    return $path;

}

function wpdm_user_space_limit($uid = null)
{
    global $current_user;
    $global = get_option('__wpdm_author_space', 500);
    $uid = $uid ? $uid : $current_user->ID;
    $user = get_user_meta($uid, '__wpdm_space', true);
    $space = $user > 0 ? $user : $global;
    return $space;
}

/**
 * Validate the given string is an url or not
 * @param $url
 * @return mixed|void
 */
function wpdm_is_url($url)
{
    return __::is_url($url);
}

function wpdm_total_downloads($uid = null, $pid = null)
{
    global $wpdb;

    if ($uid > 0 && !$pid)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p where meta_key='__wpdm_download_count' and p.ID = pm.post_id and p.post_author = '{$uid}'");
    else if ($pid > 0 && !$uid)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count' and post_id = '{$pid}'");
    else if ($uid > 0 && $pid > 0)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p where meta_key='__wpdm_download_count' and p.ID = pm.post_id and p.post_author = '{$uid}' and pm.post_id = '{$pid}'");
    else
        $download_count = $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count'");
    return (int)$download_count;
}

function wpdm_total_views($uid = null)
{
    global $wpdb;
    if (isset($uid) && $uid > 0)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p where meta_key='__wpdm_view_count' and p.ID = pm.post_id and p.post_author = '{$uid}'");
    else
        $download_count = $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_view_count'");
    return $download_count;
}

/**
 * Find if user is downloaded an item or not
 * @param $pid
 * @param $uid
 * @return bool
 */
function wpdm_is_user_downloaded($pid, $uid)
{
    global $wpdb;
    $uid = (int)$uid;
    $pid = (int)$pid;
    $ret = $wpdb->get_var("select uid from {$wpdb->prefix}ahm_download_stats where uid='$uid' and pid = '$pid'");
    if ($ret && $ret == $uid) return true;
    return false;
}


/**
 * @param $ip
 * @param $range
 * @return bool
 */
function wpdm_ip_in_range($ip, $range)
{
    // Check IP range
    list($subnet, $bits) = explode('/', $range);
    // Convert subnet to binary string of $bits length
    $subnet = unpack('H*', inet_pton($subnet)); // Subnet in Hex
    foreach ($subnet as $i => $h) $subnet[$i] = base_convert($h, 16, 2); // Array of Binary
    $subnet = substr(implode('', $subnet), 0, $bits); // Subnet in Binary, only network bits

    // Convert remote IP to binary string of $bits length
    $ip = unpack('H*', inet_pton($ip)); // IP in Hex
    foreach ($ip as $i => $h) $ip[$i] = base_convert($h, 16, 2); // Array of Binary
    $ip = substr(implode('', $ip), 0, $bits); // IP in Binary, only network bits

    // Check network bits match
    if ($subnet == $ip) {
        return true;
    }
    return false;
}

/**
 * @param null $ip
 * @return bool
 */
function wpdm_ip_blocked($ip = null)
{
    $ip = $ip ? $ip : wpdm_get_client_ip();
    $allblocked = get_option('__wpdm_blocked_ips', '');
    $allblocked = explode("\n", str_replace("\r", "", $allblocked));
    $isblocked = false;
    foreach ($allblocked as $blocked) {
        if (strstr($blocked, '/'))
            $isblocked = wpdm_ip_in_range($ip, $blocked);
        else if (strstr($blocked, '*')) {
            preg_match('/' . $blocked . '/', $ip, $matches);
            $isblocked = count($matches) > 0 ? true : false;
        } else if ($ip == $blocked)
            $isblocked = true;

        if ($isblocked == true) return $isblocked;

    }
    return $isblocked;
}

/**
 * @return string or bool
 */
function wpdm_get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = false;
    if ($ipaddress) {
        $ipaddress = explode(",", $ipaddress);
        $ipaddress = $ipaddress[0];
    }
    return $ipaddress;
}

/**
 * Validate download link
 * @param $ID
 * @param $_key
 * @param bool $execute
 * @return bool|int
 * @since 4.7.4
 */
function is_wpdmkey_valid($ID, $_key, $update = false)
{
    if ($_key == '') return 0; // Invalid
    $ID = (int)$ID;
    $_key = wpdm_sanitize_var($_key);
    $key = "__wpdmkey_{$_key}";

    $xlimit = TempStorage::get($key);

    if (!$xlimit)
        $xlimit = get_post_meta($ID, $key, true);

    if (!$xlimit) return 0; // Invalid

    if (!is_array($xlimit) && (int)$xlimit > 0) {
        $xlimit = array('use' => (int)$xlimit, 'expire' => time() + 360);
    }

    $xlimit = maybe_unserialize($xlimit);

    if (!is_array($xlimit)) return 0;

    $limit = isset($xlimit['use']) ? (int)$xlimit['use'] : 0;

    $expired = false;

    if ($limit <= 0) {
        delete_post_meta($ID, $key);
        TempStorage::kill($key);
        return -1; // Limit exceeded
    } else {

        $limit--;
        $xlimit['use'] = $limit;

        if ((int)$xlimit['expire'] < time()) {
            $xlimit['use'] = $limit = 0;
            $expired = true;
            delete_post_meta($ID, $key);
            TempStorage::kill($key);
        }
        if ($update) {
            update_post_meta($ID, $key, $xlimit);
            TempStorage::set($key, $xlimit);
        }
        if ($expired) return -2; // Time expired
    }

    return 1;
}

/**
 * @param $var
 * @param $index
 * @param array $params
 * @return array|bool|float|int|mixed|string|string[]|null
 */

function wpdm_valueof($var, $index, $params = [])
{
    $index = explode("/", $index);
    $default = is_string($params) ? $params : '';
    if (is_object($var)) $var = (array)$var;
    $default = is_array($params) && isset($params['default']) ? $params['default'] : $default;
    if (count($index) > 1) {
        $val = $var;
        foreach ($index as $key) {
            $val = is_array($val) && isset($val[$key]) ? $val[$key] : '__not__set__';
            if ($val === '__not__set__') return $default;
        }
    } else
        $val = isset($var[$index[0]]) ? $var[$index[0]] : $default;

    if (is_array($params) && isset($params['validate'])) {
        if (!is_array($val))
            $val = wpdm_sanitize_var($val, $params['validate']);
        else
            $val = wpdm_sanitize_array($val, $params['validate']);
    }

    return $val;
}

/**
 * Validate and sanitize input data
 * @param $var
 * @param array $validate
 * @param null $default
 * @return array|float|int|mixed|string|string[]|null
 */
function wpdm_query_var($var, $validate = array(), $default = null)
{
    return __::query_var($var, $validate, $default);
}

/**
 * Sanitize an array or any single value
 * @param $array
 * @return mixed
 */
function wpdm_sanitize_array($array, $sanitize = 'kses')
{
    return __::sanitize_array($array, $sanitize);
}

/**
 * Sanitize any single value
 * @param $value
 * @return string
 */
function wpdm_sanitize_var($value, $sanitize = 'kses')
{
    return __::sanitize_var($value, $sanitize);
}

/**
 * @param $total
 * @param $item_per_page
 * @param int $page
 * @param string $var
 * @return string
 */
function wpdm_paginate_links($total, $items_per_page, $current_page = 1, $var = 'cp', $params = array())
{
    $items_per_page = $items_per_page > 0 ? $items_per_page : 10;
    $pages = ceil($total / $items_per_page);
    if($current_page < 1) $current_page = 1;
    $format = isset($params['format']) ? $params['format'] : "?{$var}=%#%";
    $args = array(
        //'base'               => '%_%',
        'format' => $format,
        'total' => $pages,
        'current' => $current_page,
        //'show_all'           => false,
        'end_size'           => 2,
        //'mid_size'           => 1,
        'prev_next'          => true,
        'prev_text' => isset($params['prev_text']) ? $params['prev_text'] : __('Previous'),
        'next_text' => isset($params['prev_text']) ? $params['next_text'] : __('Next'),
        'type' => 'array',
        //'add_args'           => false,
        //'add_fragment'       => '',
        //'before_page_number' => '',
        //'after_page_number'  => ''
    );
    if (isset($params['base'])) {
        $args['base'] = $params['base'];
    }
    //wpdmprecho($args);
    $pags = paginate_links($args);
    //wpdmprecho($pags);
    $phtml = "";
    if (is_array($pags)) {
        foreach ($pags as $pagl) {
            if (isset($params['container'])) {
                $pagl = str_replace("<a", "<a data-container='{$params['container']}'", $pagl);
            }
            $phtml .= "<li>{$pagl}</li>";
        }
    }
    $async = isset($params['async']) && $params['async'] ? ' async' : '';
    $phtml = "<div class='text-center'><ul class='pagination wpdm-pagination pagination-centered text-center{$async}'>{$phtml}</ul></div>";
    return $phtml;
}

/**
 * @usage Escape script tag
 * @param $html
 * @return null|string|string[]
 */
function wpdm_escs($html)
{
    return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
}

/**
 * @param null $page_template
 * @param null $pacakge_ID
 * @return mixed|string
 */
function wpdm_download_button_style($page_template = false, $pacakge_ID = null)
{
    if (is_singular('wpdmpro') || $page_template === true)
        $ui_button = get_option('__wpdm_ui_download_button');
    else
        $ui_button = get_option('__wpdm_ui_download_button_sc');
    $ui_button = wpdm_sanitize_array($ui_button);
    $class = "btn " . (isset($ui_button['color']) ? $ui_button['color'] : 'btn-primary') . " " . (isset($ui_button['size']) ? $ui_button['size'] : '');
    $class = apply_filters("wpdm_download_button_style", $class, $pacakge_ID);
    return $class;
}

function wpdm_hex2rgb($hex)
{
    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
    return "$r, $g, $b";
}

/*** developer fns **/
function wpdmdd($data)
{
    echo "<pre>" . print_r($data, 1) . "</pre>";
    die();
}

function wpdmprecho($data, $ret = 0)
{
    $echo = "<pre>" . print_r($data, 1) . "</pre>";
    if ($ret == 1) return $echo;
    echo $echo;
}
/*** developer fns **/

add_action("admin_head", function (){
    $pages = ['settings', 'wpdm-asset-manager', 'importable-files', 'wpdm-stats', 'templates', 'wpdm-subscribers', 'wpdm-addons', 'orders', 'pp-license', 'pp-coupon-codes', 'customers', 'payouts'];
    $pages = apply_filters("wpdm_admin_notices", $pages);
    if(wpdm_query_var('post_type') !== 'wpdmpro' || !in_array(wpdm_query_var('page'), $pages))  return;
    ?>
    <style>
        #wpbody-content > .notice,
        .wrap > .notice{
            display: none;
        }
        #wpdm-an-side-panel.hide{
            display: none !important;
        }
        #wpdm-an-side-panel{
            width: 400px;
            position: fixed;
            right: -400px;
            top: 0;
            height: 100%;
            transition: all ease-in-out 300ms;
            z-index: 999999 !important;
        }
        #wpdm-an-side-panel.panel-open {
            right: 0;
        }
        #wpdm-an-side-panel-body,
        #wpdm-an-side-panel #wpdm-an-side-panel-trigger{
            background: #f4f6ff;
            position: absolute;
        }
        #wpdm-an-side-panel #wpdm-an-side-panel-trigger{
            top: 160px;
            left: -56px;
            width: 58px;
            height: 54px;
            line-height: 54px;
            padding: 0;
            text-align: center;
            color: #355eff;
            border-radius: 3px 0 0 3px;
            font-size: 16pt;
            cursor: pointer;
            z-index: 999;
            border: 1px solid #d5d9ec;
            border-right: 0;
        }
        #wpdm-an-side-panel-body{
            height: 100%;
            width: 400px;
            left: 0px;
            padding: 32px;
            z-index: 998;
            border-left: 1px solid #d5d9ec;
        }
        #wpdm-an-side-panel-body .alert {
            position: relative;
        }
        #wpdm-an-side-panel-body .alert .dismiss {
            position: absolute;
            right: -9px;
            top: -9px;
            border-radius: 500px;
            width: 20px;
            height: 20px;
            background: #ffffff;
            border: 1px solid var(--color-danger-active);
            color: var(--color-danger) !important;
            font-size: 10px;
            line-height: 17px;
            text-align: center;
            box-shadow: 0 0 0 2px #ec000036;
            opacity: 0;
            transition: all ease-in-out 300ms;
            cursor: pointer;
        }
        #wpdm-an-side-panel-body .alert:hover .dismiss {
            opacity: 1;
        }
        #wpdm-an-side-panel-body .alert .dismiss i.fa{
            color: var(--color-danger) !important;
        }
        #wpdm-an-notif-count {
            line-height: 12px;
            font-size: 8px;
            position: absolute;
            color: #fff;
            border-radius: 500px;
            background: #c163ec;
            border: 2px solid #f4f6ff;
            height: 16px;
            top: 12px;
            left: 30px;
            padding: 0 4px;
        }
    </style>
    <?php
});
add_action("admin_footer", function (){
    $pages = ['settings', 'wpdm-asset-manager', 'importable-files', 'wpdm-stats', 'templates', 'wpdm-subscribers', 'wpdm-addons', 'orders', 'pp-license', 'pp-coupon-codes', 'customers', 'payouts'];
    $pages = apply_filters("wpdm_admin_notices", $pages);
    if(wpdm_query_var('post_type') !== 'wpdmpro' || !in_array(wpdm_query_var('page'), $pages))  return;
    ?>
    <div id="wpdm-an-side-panel" class="w3eden hide">
        <div id="wpdm-an-side-panel-trigger">
            <div id="wpdm-an-notif-count">0</div>
            <i class="fas fa-bell"></i>
        </div>
        <div id="wpdm-an-side-panel-body">
        </div>
    </div>
    <script>
        jQuery(function ($) {
            var notif = 0;
            $('.notice:not(.hide-if-js):not(.hidden)').each(function (index) {
                var _type = 'info';
                var _class = $(this).attr('class');
                if(_class.indexOf('error') > 0) _type = 'danger';
                else if(_class.indexOf('success') > 0) _type = 'success';
                else if(_class.indexOf('info') > 0) _type = 'info';
                else if(_class.indexOf('warning') > 0) _type = 'warning';
                var notice = $(this).html();
                var notice_txt = $(this).text().trim();
                if(notice_txt !== '') {
                    var hash = "notif_"+notice.wpdm_hash();
                    if(!localStorage.getItem(hash)) {
                        $('#wpdm-an-side-panel-body').append("<div id='" + hash + "' class='alert alert-" + _type + "'>" + notice + "<div class='dismiss' data-target='" + hash + "'><i class='fa fa-times'></i></div></div>");
                        notif++;
                    }
                }
            });
            $('#wpdm-an-notif-count').html(notif);
            if(notif > 0) $('#wpdm-an-side-panel').removeClass('hide');
            $('#wpdm-an-side-panel-trigger').on('click', function () {
                $('#wpdm-an-side-panel').toggleClass('panel-open')
            });

            $('body').on('click', '#wpdm-an-side-panel-body .alert .dismiss', function (){
                var $this = $(this);
                var alert = '#' + $this.data('target');
                localStorage.setItem($this.data('target'), 1);
                $(alert).slideUp();
            });
        })
    </script>
    <?php
});
