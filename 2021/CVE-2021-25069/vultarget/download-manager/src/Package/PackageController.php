<?php


namespace WPDM\Package;


use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\FileSystem;
use WPDM\__\Messages;
use WPDM\__\Query;
use WPDM\__\Session;
use WPDM\__\TempStorage;

class PackageController extends PackageTemplate
{

    public $ID;
    public $package;
    public $shortCodes;
    public $packageData = [];
    public $files = [];
    public $restAPI;
    public $templateDir;


    function __construct($ID = null)
    {
        parent::__construct();

        $this->templateDir = __DIR__.'/views/';
        $this->shortCodes = new Shortcodes();
        new Hooks();

        new RestAPI();

        global $post;
        if (!$ID && is_object($post) && $post->post_type == 'wpdmpro') $ID = $post->ID;
        $this->ID = $ID;

    }

    function prepare($ID = null, $template = null, $template_type = 'page')
    {
        global $post;

        if (!$ID) $ID = $this->ID;
        if (!$ID && isset($post->ID) && get_post_type($post) == 'wpdmpro') $ID = $post->ID;
        if (!$ID) {
            $this->packageData = array('error' => __("ID missing!", "download-manager"));
            return $this;
        }

        if (!is_object($post) || $post->ID != $ID) {
            $_post = get_post($ID);
            if (get_class($_post) != 'WP_Post') return new \WP_Error('broke', __("Invalid Package ID!", "download-manager"));
            $post_vars = (array)$_post;
        } else
            $post_vars = (array)$post;

        $ID = $post_vars['ID'];

        $post_vars['title'] = stripcslashes($post_vars['post_title']);
        $post_vars['description'] = stripcslashes($post_vars['post_content']);
        $post_vars['description'] = wpautop(stripslashes($post_vars['description']));

        if (!has_shortcode($post_vars['description'], 'wpdm_package'))
            $post_vars['description'] = do_shortcode(stripslashes($post_vars['description']));

	    $post_vars['excerpt'] = wpautop(stripcslashes(wpdm_escs($post_vars['post_excerpt'])));
        $author = get_user_by('id', $post_vars['post_author']);
        if (is_object($author)) $post_vars['author_name'] = $author->display_name;
        $post_vars['author_profile_url'] = get_author_posts_url($post_vars['post_author']);
        if (is_object($author)) $post_vars['avatar_url'] = get_avatar_url($author->user_email);
        if (is_object($author)) $post_vars['avatar'] = get_avatar($author->user_email, 96);
        $post_vars['author_package_count'] = count_user_posts($post_vars['post_author'], "wpdmpro");

        $template_tags = $this->parseTemplate($template, $ID, $template_type);

        //Featured Image
        $post_vars['preview'] = $post_vars['thumb'] = "";
        if (has_post_thumbnail($ID)) {
            $src = wp_get_attachment_image_src(get_post_thumbnail_id($ID), 'full', false);
            $post_vars['preview'] = $src['0'];
            $post_vars['featured_image'] = get_the_post_thumbnail($ID, 'full');

            if (in_array('thumb', $template_tags))
                $post_vars['thumb'] = get_the_post_thumbnail($ID);
        }
        if (in_array('create_date', $template_tags))
            $post_vars['create_date'] = get_the_date('', $ID);

        if (in_array('update_date', $template_tags))
            $post_vars['update_date'] = date_i18n(get_option('date_format'), strtotime($post_vars['post_modified']));

        if (in_array('categories', $template_tags))
            $post_vars['categories'] = get_the_term_list($ID, 'wpdmcategory', '', ', ', '');

        if (in_array('category', $template_tags)) {
            $cats = get_the_terms($ID, 'wpdmcategory');
            if (is_array($cats) && count($cats) > 0)
                $post_vars['category'] = $cats[0]->name;
        }

        $post_vars['comment_count'] = wp_count_comments($ID)->approved;

        if (in_array('cats_class', $template_tags)) {
            $cats = wp_get_post_terms($ID, 'wpdmcategory');
            $post_vars['cats_class'] = "";
            foreach ($cats as $cat) {
                $post_vars['cats_class'] .= " wpdmcat-{$cat->id} {$cat->slug}";
            }
        }

        $data = $this->metaData($post_vars['ID']);

        if (is_array($data)) {
            $post_vars = array_merge($data, $post_vars);
        }

        if (!isset($post_vars['files']) || !is_array($post_vars['files']))
            $post_vars['files'] = self::getFiles($ID, true);

        if (in_array('file_count', $template_tags))
            $post_vars['file_count'] = count($post_vars['files']);

        if (in_array('play_button', $template_tags))
            $post_vars['play_button'] = self::audioPlayer($post_vars);


        $post_vars['link_label'] = isset($post_vars['link_label']) ? esc_attr($post_vars['link_label']) : esc_attr__("Download", "download-manager");
        $post_vars['page_url'] = get_permalink($post_vars['ID']);
        $post_vars['page_link'] = "<a href='" . $post_vars['page_url'] . "'>{$post_vars['title']}</a>";
        $post_vars['page_url_qr'] = "<img class='wpdm-qr-code wpdm-qr-code{$post_vars['ID']}' style='max-width: 250px' src='http://chart.googleapis.com/chart?cht=qr&chs=450x450&choe=UTF-8&chld=H|0&chl={$post_vars['page_url']}' alt='{$post_vars['title']}' />";


        if (!isset($post_vars['btnclass']))
            $post_vars['btnclass'] = wpdm_download_button_style(null, $ID);

        if (in_array('tags', $template_tags)) {
            $tags = wp_get_post_terms($post_vars['ID'], 'wpdmtag');
            $taghtml = "";
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    $taghtml .= "<a class='btn btn-secondary btn-xs' style='margin:0 5px 5px 0' href=\""
                        . get_tag_link($tag->term_id)
                        . "\"><i class='fa fa-tag'></i> &nbsp; " . $tag->name . "</a> &nbsp;";
                }
            }
            $post_vars['tags'] = $taghtml;
        }

        if (in_array('file_ext', $template_tags)) {
            $post_vars['files'] = isset($post_vars['files']) && is_array($post_vars['files']) ? $post_vars['files'] : self::getFiles($ID);
            if (count($post_vars['files']) > 1) $post_vars['file_ext'] = 'zip';
            if (is_array($post_vars['files']) && count($post_vars['files']) == 1) {
                $tmpdata = $post_vars['files'];
                $tmpdata = array_shift($tmpdata);
                $tmpdata = explode(".", $tmpdata);
                $post_vars['file_ext'] = end($tmpdata);
            }
        }

        if (in_array('file_size', $template_tags))
            $post_vars['file_size'] = self::Size($post_vars['ID']);

        if (in_array('audio_player_single', $template_tags))
            $post_vars['audio_player_single'] = self::audioPlayer($post_vars, true);

        if (array_intersect($template_tags, array('youtube_player', 'youtube_thumb_0', 'youtube_thumb_1', 'youtube_thumb_2', 'youtube_thumb_3'))) {
            $post_vars['files'] = isset($post_vars['files']) && is_array($post_vars['files']) ? $post_vars['files'] : self::getFiles($ID);
            $tmplfile = $post_vars['files'];
            $tmpfile = is_array($tmplfile) && count($tmplfile) > 0 ? array_shift($tmplfile) : '';
            if (strpos($tmpfile, 'youtu')) {
                if (preg_match('/youtu\.be\/([^\/]+)/', $tmpfile, $match))
                    $vid = $match[1];
                else if (preg_match('/watch\?v=([^\/]+)/', $tmpfile, $match))
                    $vid = $match[1];
                if (isset($vid)) {
                    $post_vars['youtube_thumb_0'] = '<img src="https://img.youtube.com/vi/' . $vid . '/0.jpg" alt="Thumb 0" />';
                    $post_vars['youtube_thumb_1'] = '<img src="https://img.youtube.com/vi/' . $vid . '/1.jpg" alt="Thumb 1" />';
                    $post_vars['youtube_thumb_2'] = '<img src="https://img.youtube.com/vi/' . $vid . '/2.jpg" alt="Thumb 2" />';
                    $post_vars['youtube_thumb_3'] = '<img src="https://img.youtube.com/vi/' . $vid . '/3.jpg" alt="Thumb 3" />';
                    $post_vars['youtube_player'] = '<iframe width="1280" height="720" src="https://www.youtube.com/embed/' . $vid . '" frameborder="0" allowfullscreen></iframe>';
                }
            }
        }

        if (array_intersect(array('file_type_icon', 'icon'), $template_tags)) {
            $post_vars['files'] = isset($post_vars['files']) && is_array($post_vars['files']) ? $post_vars['files'] : self::getFiles($ID);
            if (is_array($post_vars['files'])) {
                $tifn = $ifn = @end($post_vars['files']);
                $ifn = @explode('.', $ifn);
                $ifn = @end($ifn);
                if (strlen($ifn) > 4 && strstr($tifn, "://"))
                    $ifn = 'web';
                if (strlen($ifn) > 4 && !strstr($tifn, "://"))
                    $ifn = 'unknown';
            } else
                $ifn = 'unknown';

            $post_vars['file_type_icon'] = '<img class="wpdm_icon" alt="' . __("Icon", "download-manager") . '" src="' . FileSystem::fileTypeIcon((@count($post_vars['files']) <= 1 ? $ifn : 'zip')) . '" />';

            if (!isset($post_vars['icon']) || $post_vars['icon'] == '') {
                $post_vars['icon'] = $post_vars['file_type_icon'];
            } else if (!strpos($post_vars['icon'], '://'))
                $post_vars['icon'] = '<img class="wpdm_icon" alt="' . __("Icon", "download-manager") . '"   src="' . plugins_url(str_replace('download-manager/file-type-icons/', 'download-manager/assets/file-type-icons/', $post_vars['icon'])) . '" />';
            else if (!strpos($post_vars['icon'], ">"))
                $post_vars['icon'] = '<img class="wpdm_icon" alt="' . __("Icon", "download-manager") . '"   src="' . str_replace('download-manager/file-type-icons/', 'download-manager/assets/file-type-icons/', $post_vars['icon']) . '" />';

        }


        $post_vars['link_label'] = apply_filters('wpdm_button_image', $post_vars['link_label'], $post_vars, $template_type);

        $post_vars['link_label'] = $post_vars['link_label'] ? $post_vars['link_label'] : __("Download", "download-manager");

        $post_vars['download_url'] = $this->getDownloadURL($post_vars['ID']);
        $post_vars['download_link_popup'] =
        $post_vars['download_link_extended'] =
        $post_vars['download_link'] = (int)get_option('__wpdm_mask_dlink', 1) == 1 ? "<a class='wpdm-download-link download-on-click {$post_vars['btnclass']}' rel='nofollow' href='#' data-downloadurl=\"{$post_vars['download_url']}\">{$post_vars['link_label']}</a>" : "<a class='wpdm-download-link {$post_vars['btnclass']}' rel='nofollow' href='{$post_vars['download_url']}'>{$post_vars['link_label']}</a>";

        $limit_over = 0;
        $alert_size = ($template_type == 'link') ? 'alert-sm' : '';

        $loginmsg = Messages::login_required($post_vars['ID']);

        //Download limit is over
        if (self::userDownloadLimitExceeded($post_vars['ID'])) {
            $limit_over = 1;
            $limit_msg = Messages::download_limit_exceeded($post_vars['ID']);
            $post_vars['download_url'] = '#';
            $post_vars['link_label'] = $limit_msg;
            $post_vars['download_link_popup'] =
            $post_vars['download_link_extended'] =
            $post_vars['download_link'] = $post_vars['link_label'];
        } //Item is expired
        else if (isset($post_vars['expire_date']) && $post_vars['expire_date'] != "" && strtotime($post_vars['expire_date']) < time()) {
            $post_vars['download_url'] = '#';
            $post_vars['link_label'] = __("Download was expired on", "download-manager") . " " . date_i18n(get_option('date_format') . " h:i A", strtotime($post_vars['expire_date']));
            $post_vars['download_link'] =
            $post_vars['download_link_extended'] =
            $post_vars['download_link_popup'] = "<div class='alert alert-warning {$alert_size}' data-title='" . __("DOWNLOAD ERROR:", "download-manager") . "'>{$post_vars['link_label']}</div>";
        } //Not available yet
        else if (isset($post_vars['publish_date']) && $post_vars['publish_date'] != '' && strtotime($post_vars['publish_date']) > time()) {
            $post_vars['download_url'] = '#';
            $post_vars['link_label'] = __("Download will be available from ", "download-manager") . " " . date_i18n(get_option('date_format') . " h:i A", strtotime($post_vars['publish_date']));
            $post_vars['download_link'] =
            $post_vars['download_link_extended'] =
            $post_vars['download_link_popup'] = "<div class='alert alert-warning {$alert_size}' data-title='" . __("DOWNLOAD ERROR:", "download-manager") . "'>{$post_vars['link_label']}</div>";
        } //User is not allowed
        else if (is_user_logged_in() && !self::userCanAccess($post_vars['ID'])) {
            $post_vars['download_url'] = '#';
            $post_vars['link_label'] = stripslashes(get_option('__wpdm_permission_denied_msg'));
            $post_vars['link_label'] = $post_vars['link_label'] !== '' ? $post_vars['link_label'] : __('You are not allowed to download.', 'download-manager');
            $post_vars['download_link'] =
            $post_vars['download_link_extended'] =
            $post_vars['download_link_popup'] = "<div class='alert alert-danger {$alert_size}' data-title='" . __("DOWNLOAD ERROR:", "download-manager") . "'>{$post_vars['link_label']}</div>";
        } //User is not logged in  and guest access is not allowed
        else if (!is_user_logged_in() && !self::userCanAccess($post_vars['ID'])) {
            $packurl = get_permalink($post_vars['ID']);
            $loginform = WPDM()->user->login->form(array('redirect' => $packurl));
            $post_vars['download_url'] = WPDM()->user->login->url($_SERVER['REQUEST_URI']);
            $post_vars['download_link'] =
            $post_vars['download_link_extended'] =
            $post_vars['download_link_popup'] = stripcslashes(str_replace(array("[loginform]", "[this_url]", "[package_url]"), array($loginform, $_SERVER['REQUEST_URI'], $packurl), $loginmsg));
            $post_vars['download_link'] =
            $post_vars['download_link_extended'] =
            $post_vars['download_link_popup'] = get_option('__wpdm_login_form', 0) == 1 ? $loginform : $post_vars['download_link'];
        } //PACKAGE is locked
        else if ($this->isLocked($post_vars['ID'])) {
            $post_vars['download_url'] = '#';
            $post_vars['download_link'] = $post_vars['download_link_popup'] = "<a href='#unlock' class='wpdm-download-link wpdm-download-locked {$post_vars['btnclass']}' data-package='{$post_vars['ID']}'>{$post_vars['link_label']}</a>"; //self::activeLocks($post_vars);
            $post_vars['download_link_extended'] = self::activeLocks($post_vars['ID'], array('embed' => 1));
            //$post_vars['download_link_popup'] = self::activeLocks($post_vars, array('popstyle' => 'popup'));
        }

        if (isset($data['terms_lock']) && $data['terms_lock'] != 0 && (!function_exists('wpdmpp_effective_price') || wpdmpp_effective_price($post_vars['ID']) == 0) && $limit_over == 0 && self::userCanAccess($post_vars['ID'])) {
            //$data['terms_conditions'] = wpautop(strip_tags($data['terms_conditions'], "<p><br><a><strong><b><i>"));
            /*$data['terms_conditions'] = wpautop(wpdm_escs(get_post_meta($post_vars['ID'], '__wpdm_terms_conditions', true)));
            $data['terms_title'] = wpdm_escs(get_post_meta($post_vars['ID'], '__wpdm_terms_title', true));
            $data['terms_title'] = !isset($data['terms_title']) || $data['terms_title'] == '' ? __("Terms and Conditions", 'download-manager') : sanitize_text_field($data['terms_title']);
            $data['terms_check_label'] = !isset($data['terms_check_label']) || $data['terms_check_label'] == '' ? __("I Agree With Terms and Conditions", 'download-manager') : sanitize_text_field($data['terms_check_label']);
            if (!self::isLocked($post_vars)) {
                $post_vars['download_link_popup'] = $post_vars['download_link'] = "<a href='#unlock' class='wpdm-download-link wpdm-download-locked {$post_vars['btnclass']}' data-package='{$post_vars['ID']}'>{$post_vars['link_label']}</a>";
            }
            //$data['terms_conditions'] = wpautop(strip_tags($data['terms_conditions'], "<p><br><a><strong><b><i>"));
            $data['terms_conditions'] = wpautop(wpdm_escs($data['terms_conditions']));*/

            $terms_page = (int)get_post_meta($ID, '__wpdm_terms_page', true);
            $terms_title = get_post_meta($ID, '__wpdm_terms_title', true);
            $terms_conditions = get_post_meta($ID, '__wpdm_terms_conditions', true);
            if ($terms_page) {
                $terms_page = get_post($terms_page);
                $terms_title = $terms_page->post_title;
                $terms_conditions = $terms_page->post_content;
            }
            $terms_title =  $terms_title !== '' ? $terms_title : __("Terms and Conditions", 'download-manager');
            $terms_check_label = get_post_meta($ID, '__wpdm_terms_check_label', true);
            $terms_check_label = $terms_check_label ? $terms_check_label : __("I Agree With Terms and Conditions", 'download-manager');

            $data['terms_title'] = $terms_title;
            $data['terms_check_label'] = $terms_check_label;
            $data['terms_conditions'] = $terms_conditions;

            if (!self::isLocked($post_vars)) {
                $post_vars['download_link_popup'] = $post_vars['download_link'] = "<a href='#unlock' class='wpdm-download-link wpdm-download-locked {$post_vars['btnclass']}' data-package='{$post_vars['ID']}'>{$post_vars['link_label']}</a>";
            }

            $post_vars['download_link_extended'] = "<div class='panel panel-default card card terms-panel' style='margin: 0'><div class='panel-heading card-header'>{$terms_title}</div><div class='panel-body card-body' style='max-height: 200px;overflow: auto'>{$terms_conditions}</div><div class='panel-footer card-footer'><label><input data-pid='{$post_vars['ID']}' class='wpdm-checkbox terms_checkbox terms_checkbox_{$post_vars['ID']}' type='checkbox' onclick='jQuery(\".download_footer_{$post_vars['ID']}\").slideToggle();'> {$terms_check_label}</label></div><div class='panel-footer card-footer download_footer_{$post_vars['ID']}' style='display:none;'>{$post_vars['download_link_extended']}</div></div><script>jQuery(function($){ $('#wpdm-filelist-{$post_vars['ID']} .btn.inddl, #xfilelist .btn.inddl').attr('disabled', 'disabled'); });</script>";

        }

        if (!isset($post_vars['formatted'])) $post_vars['formatted'] = 0;
        ++$post_vars['formatted'];

        $post_vars['__template'] = $template;
        $post_vars['__template_type'] = $template_type;
        $post_vars = apply_filters('wpdm_after_prepare_package_data', $post_vars, $template_type);

        $this->packageData = $post_vars;

        foreach ($post_vars as $key => $val) {
            try {
                if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $key))
                    $this->{$key} = $val;
            } catch (\Exception $e) {
            }
        }
        return $this;
    }

    /**
     * @usage Get all or any specific package info
     * @param $ID
     * @param null $meta
     * @return mixed
     */
    public static function get($ID, $meta = null)
    {
        $ID = (int)$ID;
        if ($ID == 0) return null;
        if ($meta != null)
            return get_post_meta($ID, "__wpdm_" . $meta, true);
        $p = new PackageController();
        $package = $p->prepare($ID);
        return $package->packageData;
    }

    /**
     * @usage Verify single file download option
     * @param null $ID
     * @return mixed
     */
    public function isSingleFileDownloadAllowed($ID = null)
    {
        global $post;
        if (!$ID && $post->post_type == 'wpdmpro') $ID = $post->ID;
        $global = get_option('__wpdm_individual_file_download', 1);
        $package = get_post_meta($ID, '__wpdm_individual_file_download', true);
        $effective = $package == -1 || $package == '' ? $global : $package;
        return apply_filters("wpdm_is_single_file_download_allowed", $effective, $ID);
    }

    /**
     * @param $id
     * @usage Returns the user roles who has access to specified package
     * @return array|mixed
     */
    public function allowedRoles($id)
    {
        $roles = get_post_meta($id, '__wpdm_access', true);
        $roles = maybe_unserialize($roles);

        $cats = get_the_terms($id, 'wpdmcategory');
        if (!is_array($roles)) $roles = array();
        if (is_array($cats)) {
            foreach ($cats as $cat) {
                $croles = WPDM()->categories->getAllowedRoles($cat->term_id);
                $roles = array_merge($roles, $croles);
            }
        }

        $roles = array_unique($roles);

        $roles = apply_filters("wpdm_allowed_roles", $roles, $id);
        if (!is_array($roles)) $roles = array();
        return $roles;
    }

    /**
     * @usage Check if a package is locked or public
     * @param $id
     * @return bool
     */
    public function isLocked($package = null)
    {
        if(!$package) $package = $this->ID;
        if (!is_array($package) && (int)$package > 0) {
            $id = $package;
            $package = array();
            $package['ID'] = $id;
            $package['email_lock'] = get_post_meta($id, '__wpdm_email_lock', true);
            $package['password_lock'] = get_post_meta($id, '__wpdm_password_lock', true);
            $package['gplusone_lock'] = get_post_meta($id, '__wpdm_gplusone_lock', true);
            $package['tweet_lock'] = get_post_meta($id, '__wpdm_tweet_lock', true);
            $package['twitterfollow_lock'] = get_post_meta($id, '__wpdm_twitterfollow_lock', true);
            $package['facebooklike_lock'] = get_post_meta($id, '__wpdm_facebooklike_lock', true);
            $package['linkedin_lock'] = get_post_meta($id, '__wpdm_linkedin_lock', true);
            $package['captcha_lock'] = get_post_meta($id, '__wpdm_captcha_lock', true);
        } else
            $id = $package['ID'];
        $lock = '';
        $package = apply_filters('wpdm_custom_data', $package, $id);
        if (isset($package['email_lock']) && (int)$package['email_lock'] == 1) $lock = 'locked';
        if (isset($package['password_lock']) && (int)$package['password_lock'] == 1) $lock = 'locked';
        if (isset($package['gplusone_lock']) && (int)$package['gplusone_lock'] == 1) $lock = 'locked';
        if (isset($package['tweet_lock']) && (int)$package['tweet_lock'] == 1) $lock = 'locked';
        if (isset($package['twitterfollow_lock']) && (int)$package['twitterfollow_lock'] == 1) $lock = 'locked';
        if (isset($package['facebooklike_lock']) && (int)$package['facebooklike_lock'] == 1) $lock = 'locked';
        if (isset($package['linkedin_lock']) && (int)$package['linkedin_lock'] == 1) $lock = 'locked';
        if (isset($package['captcha_lock']) && (int)$package['captcha_lock'] == 1) $lock = 'locked';

        if ($lock !== 'locked')
            $lock = apply_filters('wpdm_check_lock', $lock, $id);
        return ($lock === 'locked');
    }

    /**
     * Check if a package with the given $ID is password protected
     * @param $ID
     * @return bool
     */
    function isPasswordProtected($ID)
    {
        $locked = (int)get_post_meta($ID, '__wpdm_password_lock', true);
        $password = get_post_meta($ID, '__wpdm_password', true);
        return $locked && $password ? $password : false;
    }

    /**
     * @usage Check if current user has access to package or category
     * @param $id
     * @param string $type
     *
     * @return bool
     */
    public function userCanAccess($ID = null, $type = 'package')
    {
        global $current_user;
        $ID = $ID ? $ID : $this->ID;
        if(!$ID) return false;
        if ($type == 'package')
            $roles = $this->allowedRoles($ID);
        else $roles = WPDM()->categories->getAllowedRoles($ID);

        if (!is_array($roles)) $roles = array();
        $matched = is_user_logged_in() ? array_intersect($current_user->roles, $roles) : array();

        if ($type === 'category' && count($roles) == 0) return true;
        if (in_array('guest', $roles)) return true;
        if (count($matched) > 0) return true;

        return false;

    }

    /**
     * @usage Check if current user has access to package or category based on roles
     * @param $id
     * @param string $type
     *
     * @return bool
     */
    public function userHasAccess($user_id, $ID, $type = 'package')
    {

        $user = get_user_by('ID', $user_id);

        if ($type == 'package')
            $roles = $this->allowedRoles($ID);
        else $roles = WPDM()->categories->getAllowedRoles($ID);

        if (!is_array($roles)) $roles = array();
        $matched = is_user_logged_in() ? array_intersect($user->roles, $roles) : array();
        if ($type === 'category' && count($roles) == 0) return true;
        if (in_array('guest', $roles)) return true;
        if (count($matched) > 0) return true;
        return false;

    }

    /**
     * @usage Check user's download limit
     * @param $ID
     * @return bool
     */
    public static function userDownloadLimitExceeded($ID)
    {
        global $current_user;

        if (is_user_logged_in())
            $index = $current_user->ID;
        else
            $index = $_SERVER['REMOTE_ADDR'];

        $stock = (int)get_post_meta($ID, '__wpdm_quota', true);
        $download_count = (int)get_post_meta($ID, '__wpdm_download_count', true);

        $user_download_count = WPDM()->downloadHistory->userDownloadCount($ID);

        $download_limit_per_user = (int)get_post_meta($ID, '__wpdm_download_limit_per_user', true);

        if ($download_limit_per_user > 0 && $user_download_count >= $download_limit_per_user) return apply_filters("wpdm_user_download_limit_exceeded", true, $ID);

        $exceeded = false;

        if ($stock > 0 && $download_count >= $stock) $exceeded = true;;

        $exceeded = apply_filters("wpdm_user_download_limit_exceeded", $exceeded, $ID);

        return $exceeded;
    }

    function userDownloadCount($ID)
    {
        global $wpdb;
        //$wpdb->
    }

    /**
     * @usage Check if user is can download this package
     * @param $ID
     * @return bool
     */
    public function userCanDownload($ID)
    {
        return $this->userCanAccess($ID) && !$this->userDownloadLimitExceeded($ID);
    }

    /**
     * @usage Count files in a package
     * @param $id
     * @return int
     */
    public function fileCount($ID)
    {

        $count = count($this->getFiles($ID, true));
        return $count;

    }

    /**
     * @usage Get list of attached files & all files inside attached dir with a package
     * @param $ID
     * @return array|mixed
     */
    public function getFiles($ID, $include_dir = false)
    {
        $files = maybe_unserialize(get_post_meta($ID, '__wpdm_files', true));
        if (!$files || !is_array($files)) $files = array();
        foreach ($files as &$file) {
            $file = trim($file);
        }
        if ($include_dir) {
            $package_dir = get_post_meta($ID, '__wpdm_package_dir', true);
            $package_dir = file_exists($package_dir) ? $package_dir : Crypt::decrypt($package_dir);
            if ($package_dir != '') {
                $package_dir = trailingslashit($package_dir);
                $package_dir = realpath($package_dir);
                if($package_dir) {
                    $dfiles = FileSystem::scanDir($package_dir, true, true, '', true);
                    $files += $dfiles;
                }
            }
        }
        $files = apply_filters("wpdm_get_files", $files, $ID);

        return $files;
    }

    /**
     * @usage Create zip from attached files
     * @param $ID
     * @return mixed|string|\WP_Error
     */
    public function zip($ID)
    {
        $files = $this->getFiles($ID);
        $zipped = get_post_meta($ID, "__wpdm_zipped_file", true);
        if (count($files) > 0) {
            if ($zipped == '' || !file_exists($zipped)) {
                $zipped = UPLOAD_DIR . sanitize_file_name(get_the_title($ID)) . '-' . $ID . '.zip';
                $zipped = FileSystem::zipFiles($files, $zipped);
                return $zipped;
            }
        }
        return new \WP_Error(404, __("No File Attached!", "download-manager"));
    }

    /**
     * @usage Calculate package size
     * @param $ID
     * @param bool|false $recalculate
     * @return bool|float|int|mixed|string
     */
    public function size($ID, $recalculate = false)
    {

        if (get_post_type($ID) != 'wpdmpro') return false;

        $size = get_post_meta($ID, '__wpdm_package_size', true);

        if ($size != "" && !$recalculate) return $size;

        $files = $this->getFiles($ID);

        $size = 0;
        if (is_array($files)) {
            foreach ($files as $f) {
                $f = trim($f);
                if (__::is_url($f)) continue;
                if (file_exists($f))
                    $size += @filesize($f);
                else
                    $size += @filesize(UPLOAD_DIR . $f);
            }
        }

        update_post_meta($ID, '__wpdm_package_size_b', $size);
        $size = $size / 1024;
        if ($size > 1024) $size = number_format($size / 1024, 2) . ' MB';
        else $size = number_format($size, 2) . ' KB';
        update_post_meta($ID, '__wpdm_package_size', $size);
        return $size;
    }

    /**
     * @usage Generate play button for link template
     * @param $package
     * @param bool $return
     * @param $style
     * @return mixed|string|void
     */
    public function audioPlayer($package, $return = true, $style = 'primary btn-lg wpdm-btn-play-lg')
    {

        $audiohtml = "";

        if (!is_array($package['files']) || count($package['files']) == 0) return;
        $audios = array();
        $nonce = wp_create_nonce($_SERVER['REQUEST_URI']);
        $audio = $audx = null;
        foreach ($package['files'] as $index => $file) {
            $realpath = file_exists($file) ? $file : UPLOAD_DIR . $file;
            $filetype = wp_check_filetype($realpath);
            $tmpvar = explode('/', $filetype['type']);
            if ($tmpvar[0] == 'audio') {
                $audio = $file;
                $audx = $index;
                break;
            }
        }

        if ($audio != null) {
            $song = FileSystem::mediaURL($package['ID'], $audx, basename($audio));  //home_url("/?wpdmdl={$package['ID']}&ind=".$audx."&play=".basename($audio));
            $audiohtml = "<button data-player='wpdm-audio-player' data-song='{$song}' class='btn btn-{$style} wpdm-btn-play'><i class='fa fa-play'></i></button>";
            $audiohtml = apply_filters("wpdm_audio_play_button", $audiohtml, $song, $package, 0);
        }

        if ($return)
            return $audiohtml;

        echo $audiohtml;

    }

    /**
     * @param $ID
     * @param $files
     * @param int $width
     * @return string
     */
    public static function videoPlayer($ID, $files = null, $width = 800)
    {

        if (!$files)
            $files = WPDM()->package->getFiles($ID);

        $videos = array();
        foreach ($files as $index => $file) {
            $realpath = file_exists($file) ? $file : UPLOAD_DIR . $file;
            $filetype = wp_check_filetype($realpath);
            $tmpvar = explode('/', $filetype['type']);
            if ($tmpvar[0] == 'video') {
                $videos[] = $file;
                $vidx[] = $index;
            }

        }

        $videothumbs = "";
        $mpvs = get_post_meta($ID, '__wpdm_additional_previews', true);
        $mmv = 0;

        if (is_array($mpvs) && count($mpvs) > 1 && count($videos) > 1) {

            foreach ($mpvs as $i => $mpv) {
                if ($mmv < count($videos)) {
                    //$url = self::expirableDownloadLink($ID, 3);
                    $ind = $vidx[$i]; //\WPDM_Crypt::Encrypt($videos[$mmv]);
                    //$video = $url . "&ind={$ind}&play=" . basename($videos[$mmv]);
                    $video = FileSystem::mediaURL($ID, $ind, wpdm_basename($videos[$mmv]));

                    $videothumbs .= "<a href='#' data-video='{$video}' class='__wpdm_playvideo'><img class='thumbnail' src='" . wpdm_dynamic_thumb($mpv, array(64, 64)) . "'/></a>";
                }
                $mmv++;
            }
        }

        $player_html = '';
        if (count($videos) > 0) {
            //$url = self::expirableDownloadLink($ID, 10);
            $ind = $vidx[0]; //\WPDM_Crypt::Encrypt($videos[0]);
            //$video = $url . "&ind={$ind}&play=" . basename($videos[0]);
            $video = FileSystem::mediaURL($ID, $ind, wpdm_basename($videos[0]));

            $player_html = "<video id='__wpdm_videoplayer' class='thumbnail' width=\"{$width}\" controls controlsList='nodownload'><source src=\"{$video}\" type=\"video/mp4\">Your browser does not support HTML5 video.</video><div class='videothumbs'>{$videothumbs}</div>";
            //if(!WPDM()->package->userCanAccess($ID)) $player_html = \WPDM\__\Messages::Error(stripslashes(get_option('wpdm_permission_msg')), -1);
        }

        $player_html = apply_filters("wpdm_video_player_html", $player_html, $ID, $file, $width);
        return $player_html;
    }

    /**
     * @param $ID
     * @param $files
     * @param int $width
     * @param int $height
     * @return mixed|string
     */
    public function productPreview($ID, $files = null, $width = 800, $height = 600)
    {
        if (!$files)
            $files = WPDM()->package->getFiles($ID);
        $tfiles = $files;
        $keys = array_keys($files);
        $ind = $keys[0];
        $file = array_shift($tfiles);
        $realpath = file_exists($file) ? $file : UPLOAD_DIR . $file;
        $filetype = wp_check_filetype($realpath);
        $type = explode('/', $filetype['type']);
        $type = $type[1];
        switch ($type) {
            case 'mpeg':
            case 'mp4':
                return self::videoPlayer($ID, $files, $width);
            case 'png':
            case 'jpg':
            case 'jpeg':
                return "<img src='" . FileSystem::imageThumbnail($realpath, $width, $height) . "' alt='" . get_the_title($ID) . "'/>";
            case 'pdf':
                $url = self::expirableDownloadLink($ID, 1, 300);
                //$ind = \WPDM_Crypt::Encrypt($file);
                $url .= "&ind={$ind}";
                return FileSystem::docViewer($url, $ID, 'pdf');
            default:
                return '';
        }
    }

    /**
     * @usage Get All Custom Data of a Package
     * @param $pid
     * @return array
     */
    public function metaData($ID)
    {
        global $wpdb;

        $metaValidate = ['__wpdm_icon' => 'txt', '__wpdm_view_count' => 'int', '__wpdm_download_count' => 'int', '__wpdm_link_label' => 'txt', '__wpdm_version' => 'txt', '__wpdm_quota' => 'int', '__wpdm_access' => 'array', '__wpdm_package_size' => 'txt', '__wpdm_download_limit_per_user' => 'int', '__wpdm_terms_lock' => 'int', '__wpdm_publish_date' => 'txt', '__wpdm_expire_date' => 'txt'];
        $metaKeys = array_keys($metaValidate);
        $metaData = $wpdb->get_results("select * from {$wpdb->prefix}postmeta where meta_key in ('" . implode('\',\'', $metaKeys) . "') and post_id = '{$ID}'");

        $data = [];
        if (is_array($metaData)) {
            foreach ($metaData as $metaDataRow) {
                $key = str_replace("__wpdm_", "", $metaDataRow->meta_key);
                if($key === 'publish_date')  {
                    $key = 'avail_date';
                    $metaDataRow->meta_value = wp_date(get_option('date_format')." ".get_option('time_format'), strtotime($metaDataRow->meta_value));
                }
                if($key === 'expire_date')  {
                    $metaDataRow->meta_value = wp_date(get_option('date_format')." ".get_option('time_format'), strtotime($metaDataRow->meta_value));
                }
                //if($metaValidate[$metaDataRow->meta_key] === 'array' && !is_array($metaDataRow->meta_value)) $metaDataRow->meta_value = unserialize($metaDataRow->meta_value);
                //else
                //    $metaDataRow->meta_value = __::sanitize_var($metaDataRow->meta_value, $metaValidate[$metaDataRow->meta_key]);
                $data[$key] = wpdm_sanitize_var(maybe_unserialize($metaDataRow->meta_value), $metaValidate[$metaDataRow->meta_key]);
            }
        }
        unset($metaData);
        $data = apply_filters('wpdm_custom_data', $data, $ID);
        if (!is_array($data)) $data = [];

        return $data;
    }

    /**
     * @usage Generate download link of a package
     * @param $package
     * @param int $embed
     * @param array $extras
     * @return string
     */
    function prepareDownloadLink(&$package, $embed = 0, $extras = array())
    {
        global $wpdb, $current_user, $wpdm_download_icon, $wpdm_download_lock_icon, $btnclass;
        if (is_array($extras))
            extract($extras);
        $data = '';

        $package['link_url'] = home_url('/?download=1&');
        $package['link_label'] = !isset($package['link_label']) || $package['link_label'] == '' ? __("Download", "download-manager") : $package['link_label'];

        //Change link label using a button image
        $template_type = isset($template_type) ? $template_type : 'link';
        $package['link_label'] = apply_filters('wpdm_button_image', $package['link_label'], $package, $template_type);


        $package['download_url'] = wpdm_download_url($package);
        if (wpdm_is_download_limit_exceed($package['ID'])) {
            $limit_msg = Messages::download_limit_exceeded($package['ID']);
            $package['download_url'] = '#';
            $package['link_label'] = $limit_msg;
        }
        if (isset($package['expire_date']) && $package['expire_date'] != "" && strtotime($package['expire_date']) < time()) {
            $package['download_url'] = '#';
            $package['link_label'] = apply_filters("wpdm_download_expired_message", __("Download was expired on", "download-manager") . " " . date_i18n(get_option('date_format') . " h:i A", strtotime($package['expire_date'])), $package);
            $package['download_link'] = $vars['download_link_extended'] = $vars['download_link_popup'] = "<a href='#'>{$package['link_label']}</a>";
            return "<div class='alert alert-warning'><b>" . __("Download:", "download-manager") . "</b><br/>{$package['link_label']}</div>";
        }

        if (isset($package['publish_date']) && $package['publish_date'] != '' && strtotime($package['publish_date']) > time()) {
            $package['download_url'] = '#';
            $package['link_label'] = apply_filters("wpdm_download_availability_message", __("Download will be available from ", "download-manager") . " " . date_i18n(get_option('date_format') . " h:i A", strtotime($package['publish_date'])), $package);
            $package['download_link'] = $vars['download_link_extended'] = $vars['download_link_popup'] = "<a href='#'>{$package['link_label']}</a>";
            return "<div class='alert alert-warning'><b>" . __("Download:", "download-manager") . "</b><br/>{$package['link_label']}</div>";
        }

        $link_label = isset($package['link_label']) ? $package['link_label'] : __("Download", "download-manager");

        $package['access'] = wpdm_allowed_roles($package['ID']);

        if ($package['download_url'] != '#')
            $package['download_link'] = $vars['download_link_extended'] = $vars['download_link_popup'] = "<a class='wpdm-download-link {$btnclass}' rel='nofollow' href='#' onclick=\"location.href='{$package['download_url']}';return false;\"><i class='$wpdm_download_icon'></i>{$link_label}</a>";
        else
            $package['download_link'] = $vars['download_link_extended'] = $vars['download_link_popup'] = "<div class='alert alert-warning'><b>" . __("Download:", "download-manager") . "</b><br/>{$link_label}</div>";
        $caps = array_keys($current_user->caps);
        $role = array_shift($caps);

        $matched = (is_array(@maybe_unserialize($package['access'])) && is_user_logged_in()) ? array_intersect($current_user->roles, @maybe_unserialize($package['access'])) : array();

        $skiplink = 0;

        if (is_user_logged_in() && count($matched) <= 0 && !@in_array('guest', @maybe_unserialize($package['access']))) {
            $package['download_url'] = "#";
            $package['download_link'] = $vars['download_link_extended'] = $vars['download_link_popup'] = stripslashes(get_option('__wpdm_permission_denied_msg'));
            $package = apply_filters('download_link', $package);
            if (get_option('_wpdm_hide_all', 0) == 1) {
                $package['download_link'] = $package['download_link_extended'] = 'blocked';
            }
            return $package['download_link'];
        }
        if (!@in_array('guest', @maybe_unserialize($package['access'])) && !is_user_logged_in()) {

            $loginform = wpdm_login_form(array('redirect' => get_permalink($package['ID'])));
            if (get_option('_wpdm_hide_all', 0) == 1) return 'loginform';
            $package['download_url'] = $vars['download_link_extended'] = $vars['download_link_popup'] = home_url('/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
            $loginmsg = Messages::login_required($package['ID']);
            $package['download_link'] = stripcslashes(str_replace(array("[loginform]", "[this_url]", "[package_url]"), array($loginform, $_SERVER['REQUEST_URI'], get_permalink($package['ID'])), $loginmsg));
            return get_option('__wpdm_login_form', 0) == 1 ? $loginform : $package['download_link'];

        }

        $package = apply_filters('download_link', $package);

        $unqid = uniqid();

        if (!isset($package['quota']) || (isset($package['quota']) && $package['quota'] > 0 && $package['quota'] > $package['download_count']) || $package['quota'] == 0) {
            $lock = 0;

            if (isset($package['password_lock']) && (int)$package['password_lock'] == 1 && $package['password'] != '') {
                $lock = 'locked';
                $data = PackageLocks::AskPassword($package);
            }


            $sociallock = "";

            if (isset($package['email_lock']) && (int)$package['email_lock'] == 1) {
                $data .= PackageLocks::AskEmail($package);
                $lock = 'locked';
            }

            if (isset($package['linkedin_lock']) && (int)$package['linkedin_lock'] == 1) {
                $lock = 'locked';
                $sociallock .= PackageLocks::LinkedInShare($package);

            }

            if (isset($package['twitterfollow_lock']) && (int)$package['twitterfollow_lock'] == 1) {
                $lock = 'locked';
                $sociallock .= PackageLocks::TwitterFollow($package);

            }

            if (isset($package['gplusone_lock']) && (int)$package['gplusone_lock'] == 1) {
                $lock = 'locked';
                $sociallock .= PackageLocks::GooglePlusOne($package, true);

            }

            if (isset($package['tweet_lock']) && (int)$package['tweet_lock'] == 1) {
                $lock = 'locked';
                $sociallock .= PackageLocks::Tweet($package);

            }

            if (isset($package['facebooklike_lock']) && (int)$package['facebooklike_lock'] == 1) {
                $lock = 'locked';
                $sociallock .= PackageLocks::FacebookLike($package, true);

            }


            if (isset($package['captcha_lock']) && (int)$package['captcha_lock'] == 1) {
                $lock = 'locked';
                $sociallock .= PackageLocks::reCaptchaLock($package, true);

            }

            $extralocks = '';
            $extralocks = apply_filters("wpdm_download_lock", $extralocks, $package);

            if (is_array($extralocks) && $extralocks['lock'] === 'locked') {

                if (isset($extralocks['type']) && $extralocks['type'] == 'social')
                    $sociallock .= $extralocks['html'];
                else
                    $data .= $extralocks['html'];

                $lock = 'locked';
            }

            if ($sociallock != "") {
                $data .= "<div class='panel panel-default card card-default'><div class='panel-heading card-header'>" . __("Download", "download-manager") . "</div><div class='panel-body card-body wpdm-social-locks text-center'>{$sociallock}</div></div>";
            }

            if ($lock === 'locked') {
                $popstyle = isset($popstyle) && in_array($popstyle, array('popup', 'pop-over')) ? $popstyle : 'pop-over';
                if ($embed == 1)
                    $adata = "</strong>{$data}";
                else {
                    //$dataattrs = $popstyle == 'pop-over'? 'data-title="'.__( "Download" , "download-manager" ).' ' . $package['title'] . '"' : 'data-toggle="modal" data-target="#pkg_' . $package['ID'] . "_" . $unqid . '"';
                    $adata = '<a href="#pkg_' . $package['ID'] . "_" . $unqid . '" data-trigger="manual" data-package="' . $package['ID'] . '" class="wpdm-download-link wpdm-download-locked ' . $popstyle . ' ' . $btnclass . '"><i class=\'' . $wpdm_download_lock_icon . '\'></i>' . $package['link_label'] . '</a>';

//                    if ($popstyle == 'pop-over') {
//                        if(!get_option('__wpdm_ajax_popup', false))
//                            $adata .= '<div class="modal fade"><div class="row all-locks"  id="pkg_' . $package['ID'] . "_" . $unqid . '">' . $data . '</div></div>';
//                    }
//                    else
//                        $adata .= '<div class="modal fade" role="modal" id="pkg_' . $package['ID'] . "_" . $unqid . '"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><strong style="margin:0px;font-size:12pt">' . __('Download') . '</strong></div><div class="modal-body">' . $data . '</div><div class="modal-footer text-right"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button></div></div></div></div>';

                }

                $data = $adata;
            }
            if ($lock !== 'locked') {

                $data = $package['download_link'];


            }
        } else {
            $data = Messages::download_limit_exceeded($package['ID']);
        }


        //return str_replace(array("\r","\n"),"",$data);
        return $data;

    }

    private static function activeLocks($ID, $params = array('embed' => 0, 'popstyle' => 'pop-over'))
    {

        $embed = isset($params['embed']) ? $params['embed'] : 0;
        $template_type = isset($params['template_type']) ? $params['template_type'] : 'link';
        $popstyle = isset($params['popstyle']) ? $params['popstyle'] : 'pop-over';

        $package = array(
            'ID' => $ID,
            'password' => get_post_meta($ID, '__wpdm_password', true),
            'password_lock' => get_post_meta($ID, '__wpdm_password_lock', true),
            'email_lock' => get_post_meta($ID, '__wpdm_email_lock', true),
            'linkedin_lock' => get_post_meta($ID, '__wpdm_linkedin_lock', true),
            'twitterfollow_lock' => get_post_meta($ID, '__wpdm_twitterfollow_lock', true),
            'gplusone_lock' => get_post_meta($ID, '__wpdm_gplusone_lock', true),
            'tweet_lock' => get_post_meta($ID, '__wpdm_tweet_lock', true),
            'facebooklike_lock' => get_post_meta($ID, '__wpdm_facebooklike_lock', true),
            'captcha_lock' => get_post_meta($ID, '__wpdm_captcha_lock', true),
        );

        $package = apply_filters('wpdm_before_apply_locks', $package);
        $lock = $data = "";
        $unqid = uniqid();

        if (isset($package['password_lock']) && (int)$package['password_lock'] == 1 && $package['password'] != '') {
            $lock = 'locked';
            $data = PackageLocks::AskPassword($package);
        }

        $sociallock = "";

        if (isset($package['email_lock']) && (int)$package['email_lock'] == 1) {
            $data .= PackageLocks::AskEmail($package);
            $lock = 'locked';
        }

        if (isset($package['linkedin_lock']) && (int)$package['linkedin_lock'] == 1) {
            $lock = 'locked';
            $sociallock .= PackageLocks::LinkedInShare($package);

        }

        if (isset($package['twitterfollow_lock']) && (int)$package['twitterfollow_lock'] == 1) {
            $lock = 'locked';
            $sociallock .= PackageLocks::TwitterFollow($package);

        }

        if (isset($package['gplusone_lock']) && (int)$package['gplusone_lock'] == 1) {
            $lock = 'locked';
            $sociallock .= PackageLocks::GooglePlusOne($package, true);

        }

        if (isset($package['tweet_lock']) && (int)$package['tweet_lock'] == 1) {
            $lock = 'locked';
            $sociallock .= PackageLocks::Tweet($package);

        }

        if (isset($package['facebooklike_lock']) && (int)$package['facebooklike_lock'] == 1) {
            $lock = 'locked';
            $sociallock .= PackageLocks::FacebookLike($package, true);

        }


        $extralocks = '';
        $extralocks = apply_filters("wpdm_download_lock", $extralocks, $package);

        if (is_array($extralocks) && $extralocks['lock'] === 'locked') {

            if (isset($extralocks['type']) && $extralocks['type'] == 'social')
                $sociallock .= $extralocks['html'];
            else
                $data .= $extralocks['html'];

            $lock = 'locked';
        }

        if ($sociallock !== "") {
            $socdesc = get_option('_wpdm_social_lock_panel_desc', '');
            $socdesc = $socdesc !== '' ? "<p>{$socdesc}</p>" : "";
            $data .= "<div class='panel panel-default card'><div class='panel-heading card-header'>" . get_option('_wpdm_social_lock_panel_title', 'Like or Share to Download') . "</div><div class='panel-body card-body wpdm-social-locks text-center'>{$socdesc}{$sociallock}</div></div>";
        }


        if (isset($package['captcha_lock']) && (int)$package['captcha_lock'] == 1) {
            $lock = 'locked';
            $captcha = PackageLocks::reCaptchaLock($package, true);
            $data .= $captcha; //"<div class='panel panel-default card'><div class='panel-heading card-header'>" . __("Verify CAPTCHA to Download", "download-manager") . "</div><div class='panel-body card-body wpdm-social-locks text-center'>{$captcha}</div></div>";
        }

        if ($lock === 'locked') {
            $popstyle = isset($popstyle) && in_array($popstyle, array('popup', 'pop-over')) ? $popstyle : 'pop-over';
            if ($embed == 1)
                $adata = "</strong>{$data}";
            else {
                $link_label = get_post_meta($ID, '__wpdm_link_label', true);
                $link_label = trim($link_label) ? $link_label : __("Download", "download-manager");
                $style = wpdm_download_button_style(($template_type === 'page'), $ID);
                $style = isset($params['btnclass']) && $params['btnclass'] !== '' ? $params['btnclass'] : $style;
                $adata = '<a href="#pkg_' . $ID . "_" . $unqid . '"  data-package="' . $ID . '" data-trigger="manual" class="wpdm-download-link wpdm-download-locked ' . $style . '">' . $link_label . '</a>';

            }

            $data = $adata;
        }
        return $data;
    }


    /**
     * @usage Generate download link of a package
     * @param $package
     * @param int $embed
     * @param array $extras
     * @return string
     */
    public function downloadLink($ID, $embed = 0, $extras = array())
    {
        global $wpdb, $current_user, $wpdm_download_icon, $wpdm_download_lock_icon;
        if (is_array($extras))
            extract($extras);
        $data = '';

        $template_type = isset($extras['template_type']) ? $extras['template_type'] : 'page';

        //$package = self::get($ID);

        $link_label = get_post_meta($ID, '__wpdm_link_label', true);
        $link_label = (trim($link_label) === '') ? __("Download", "download-manager") : $link_label;

        $template_type = isset($template_type) ? $template_type : 'link';
        $link_label = apply_filters("wpdm_link_label", $link_label, $ID, $template_type);

        $loginmsg = Messages::login_required($ID);

        $download_url = $this->getDownloadURL($ID);

        $limit_over = 0;
        $alert_size = ($template_type == 'link') ? 'alert-sm' : '';

        if (self::userDownloadLimitExceeded($ID)) {
            $limit_msg = Messages::download_limit_exceeded($ID);
            $download_url = '#';
            $link_label = $limit_msg;
            $limit_over = 1;
        }

        $expired = get_post_meta($ID, '__wpdm_expire_date', true);
        $publish = get_post_meta($ID, '__wpdm_publish_date', true);

        if ($expired !== "" && strtotime($expired) < time()) {
            $download_url = '#';
            $link_label = __("Download was expired on", "download-manager") . " " . date_i18n(get_option('date_format') . " h:i A", strtotime($expired));
            return "<div class='alert alert-warning  {$alert_size}' data-title='" . __("DOWNLOAD ERROR:", "download-manager") . "'>{$link_label}</div>";
        }

        if ($publish !== "" && strtotime($publish) > time()) {
            $download_url = '#';
            $link_label = __("Download will be available from ", "download-manager") . " " . date_i18n(get_option('date_format') . " h:i A", strtotime($publish));
            return "<div class='alert alert-warning  {$alert_size}' data-title='" . __("DOWNLOAD ERROR:", "download-manager") . "'>{$link_label}</div>";
        }


        $style = wpdm_download_button_style(($template_type === 'page'), $ID);
        $style = isset($btnclass) && $btnclass !== '' ? $btnclass : $style;

        if ($download_url != '#')
            $download_link = $download_link_extended = $download_link_popup = (int)get_option('__wpdm_mask_dlink', 1) === 1 ? "<a class='wpdm-download-link download-on-click {$style}' rel='nofollow' href='#' data-downloadurl=\"{$download_url}\">{$link_label}</a>" : "<a class='wpdm-download-link {$style}' rel='nofollow' href='{$download_url}'>{$link_label}</a>";
        //$download_link = $download_link_extended = $download_link_popup = (int)get_option('__wpdm_mask_dlink', 1) === 1 ? "<a class='wpdm-download-link {$style}' rel='nofollow' href='#' onclick=\"location.href='{$download_url}';return false;\">{$link_label}</a>" : "<a class='wpdm-download-link {$style}' rel='nofollow' href='{$download_url}'>{$link_label}</a>";
        else
            $download_link = "<div class='alert alert-warning {$alert_size}' data-title='" . __("DOWNLOAD ERROR:", "download-manager") . "'>{$link_label}</div>";


        $access = self::allowedRoles($ID);
        if (!is_array($access)) $access = array();
        $matched = (is_array($access) && is_user_logged_in()) ? array_intersect($current_user->roles, $access) : array();
        if (!$matched) $matched = [];

        $skiplink = 0;

        //User does't have permission to download
        if (is_user_logged_in() && count($matched) <= 0 && !@in_array('guest', $access)) {
            $download_url = "#";
            $download_link = $download_link_extended = $download_link_popup = Messages::permission_denied($ID);
            if (get_option('_wpdm_hide_all', 0) == 1) {
                $download_link = $download_link_extended = $download_link_popup = 'blocked';
            }
            return $download_link;
        }

        //Login is required to download
        if (!@in_array('guest', $access) && !is_user_logged_in()) {

            $loginform = WPDM()->user->login->form(array('redirect' => $_SERVER['REQUEST_URI']));
            if (get_option('_wpdm_hide_all', 0) == 1) {
                $hide_all_message = get_option('__wpdm_login_form', 0) == 1 ? $loginform : stripcslashes(str_replace(array("[loginform]", "[this_url]", "[package_url]"), array($loginform, $_SERVER['REQUEST_URI'], get_permalink($ID)), $loginmsg));
                if ($template_type == 'link')
                    return "<a href='" . wpdm_login_url($_SERVER['REQUEST_URI']) . "' class='btn btn-danger'>" . __("Login", "download-manager") . "</a>";
                else
                    return $hide_all_message;
            }
            $download_url = wpdm_login_url($_SERVER['REQUEST_URI']);
            $download_link = $download_link_extended = $download_link_popup = stripcslashes(str_replace(array("[loginform]", "[this_url]", "[package_url]"), array($loginform, $_SERVER['REQUEST_URI'], get_permalink($ID)), $loginmsg));
            return get_option('__wpdm_login_form', 0) == 1 ? $loginform : $download_link;

        }

        //$package = apply_filters('wpdm_before_apply_locks', $package);
        //$package = apply_filters('wpdm_after_prepare_package_data', $package);

        $unqid = uniqid();
        $stock_limit = (int)get_post_meta($ID, '__wpdm_quota', true);
        $download_count = (int)get_post_meta($ID, '__wpdm_download_count', true);
        if ($stock_limit > $download_count || $stock_limit == 0) {
            $lock = 0;

            $extras['embed'] = $embed;
            $data = self::activeLocks($ID, $extras);

            $terms_lock = (int)get_post_meta($ID, '__wpdm_terms_lock', true);
            $terms_page = (int)get_post_meta($ID, '__wpdm_terms_page', true);
            $terms_title = get_post_meta($ID, '__wpdm_terms_title', true);
            $terms_conditions = get_post_meta($ID, '__wpdm_terms_conditions', true);
            if ($terms_page) {
                $terms_page = get_post($terms_page);
                $terms_title = $terms_page->post_title;
                $terms_conditions = $terms_page->post_content;
            }
            $terms_check_label = get_post_meta($ID, '__wpdm_terms_check_label', true);
            if ($terms_lock !== 0 && (!function_exists('wpdmpp_effective_price') || wpdmpp_effective_price($ID) == 0)) {
                if (!self::isLocked($ID) && !$embed) {
                    $data = "<a href='#unlock' class='wpdm-download-link wpdm-download-locked {$style}' data-package='{$ID}'>{$link_label}</a>";
                } else {
                    $data = $data ? $data : $download_link;
                }
                if ($embed == 1)
                    $data = "<div class='panel panel-default card terms-panel' style='margin: 0 0 10px 0'><div class='panel-heading card-header'>{$terms_title}</div><div class='panel-body card-body' style='max-height: 200px;overflow: auto'>{$terms_conditions}</div><div class='panel-footer card-footer'><label><input data-pid='{$ID}' class='wpdm-checkbox terms_checkbox terms_checkbox_{$ID}' type='checkbox'> {$terms_check_label}</label></div><div class='panel-footer card-footer bg-white download_footer_{$ID}' style='display:none;'>{$data}</div></div><script>jQuery(function($){ $('#wpdm-filelist-{$ID} .btn.inddl, #xfilelist .btn.inddl').attr('disabled', 'disabled'); });</script>";

            }

            if ($data != "") {
                $data = apply_filters('wpdm_download_link', $data, $extras + array('ID' => $ID, 'id' => $ID));
                return $data;
            }


            $data = $download_link;


        } else {
            $data = "<button class='btn btn-danger btn-block' type='button' disabled='disabled' data-title='DOWNLOAD ERROR:'>" . __("Limit Over!", "download-manager") . "</button>";
        }
        if ($data == 'loginform') return WPDM()->user->login->form();
        $data = apply_filters('wpdm_download_link', $data, $extras + array('ID' => $ID, 'id' => $ID));
        return $data;

    }

    /**
     * @usage Generate download url for public/open downloads, the url will not work for the packages with lock option
     * @param $ID
     * @param $ext
     * @return string
     */
    public function getDownloadURL($ID, $ext = array())
    {
        if (self::isLocked($ID) && !Session::get('__wpdm_unlocked_' . $ID)) return '#locked';
        if (!is_array($ext)) $ext = [];
        $ext['wpdmdl'] = $ID;
        $ext['refresh'] = uniqid() . time();
        $permalink = get_permalink($ID);
        $permalink = apply_filters("wpdm_download_url_base", $permalink, $ID);
        $download_url = add_query_arg($ext, $permalink);
        $flat = (int)get_option('__wpdm_flat_download_url', 0);
        $code = json_encode($ext);
        $code = base64_encode($code);
        $code = rtrim($code, '=');
        $filename = isset($ext['filename']) ? $ext['filename'] : '';
        if (isset($ext['ind']) && $filename == '') {
            $files = $this->getFiles($ID);
            $filename = wpdm_basename($files[$ext['ind']]);
        }
        if (!isset($ext['ind']) && $filename == '') {
            $files = $this->getFiles($ID);
            if (count($files) > 1)
                $filename = sanitize_file_name(get_the_title($ID)) . ".zip";
            else {
                $filename = array_shift($files);
                $filename = wpdm_basename($filename);
            }

        }
        if ($flat) $download_url = home_url("/wpdmdl/{$ID}-{$code}/{$filename}");
        return $download_url;
    }

    /**
     * @param $ID
     * @return false|string
     */
    public function getMasterDownloadURL($ID)
    {
        $package_url = get_permalink($ID);
        $params['wpdmdl'] = $ID;
        $params['masterkey'] = get_post_meta($ID, '__wpdm_masterkey', true);
        $download_url = add_query_arg($params, $package_url);
        return $download_url;
    }

    /**
     * @param $ID
     * @param $Key
     * @return bool
     */
    public function validateMasterKey($ID, $Key)
    {
        if ($Key === '') return false;
        $masterKey = get_post_meta($ID, '__wpdm_masterkey', true);
        if ($masterKey === '') return false;
        if ($masterKey === $Key) return true;
        return false;
    }

    /**
     * @param $ID
     * @param int $usageLimit
     * @param int $expirePeriod seconds
     * @return string
     */
    function expirableDownloadLink($ID, $usageLimit = 10, $expirePeriod = 999999, $sessionOnly = true)
    {
        $key = uniqid();
        $exp = array('use' => $usageLimit, 'expire' => time() + $expirePeriod);
        if (!$sessionOnly)
            update_post_meta($ID, "__wpdmkey_" . $key, $exp);
        else
            TempStorage::set("__wpdmkey_{$key}", $exp, time() + $expirePeriod);
        //Session::set( '__wpdm_unlocked_'.$ID , 1 );
        //$download_url = $this->getDownloadURL($ID, "_wpdmkey={$key}");
        $permalink = get_permalink($ID);
        $permalink = apply_filters("wpdm_download_url_base", $permalink, $ID);
        $download_url = add_query_arg(array("wpdmdl" => $ID, "_wpdmkey" => $key), $permalink);
        return $download_url;
    }

    /**
     * @param $ID
     * @param int $usageLimit
     * @param int $expirePeriod seconds
     * @return string
     */
    static function expirableDownloadPage($ID, $usageLimit = 10, $expirePeriod = 604800, $sessionOnly = true)
    {
        $key = uniqid();
        $exp = array('use' => $usageLimit, 'expire' => time() + $expirePeriod);
        if (!$sessionOnly)
            update_post_meta($ID, "__wpdmkey_" . $key, $exp);
        else
            TempStorage::set("__wpdmkey_{$key}", $exp, time() + $expirePeriod);
        $download_page_key = Crypt::encrypt(array('pid' => $ID, 'key' => $key));
        $download_page = home_url("wpdm-download/{$download_page_key}");
        return $download_page;
    }


    /**
     * @usage Fetch link/page template and return generated html
     * @param $template
     * @param $vars
     * @param string $type
     * @return mixed|string
     */
    public function fetchTemplate($template, $vars, $type = 'link')
    {
        if (!is_array($vars) && is_int($vars) && $vars > 0) $vars = array('ID' => $vars);
        if (!isset($vars['ID']) || intval($vars['ID']) < 1) return '';

        $loginmsg = Messages::login_required($vars['ID']);

        if (!is_user_logged_in() && count(self::allowedRoles($vars['ID'])) >= 0 && !self::userCanAccess($vars['ID'])) {
            $loginform = wpdm_login_form(array('redirect' => get_permalink($vars['ID'])));
            $hide_all_message = get_option('__wpdm_login_form', 0) == 1 ? $loginform : stripcslashes(str_replace(array("[loginform]", "[this_url]", "[package_url]"), array($loginform, $_SERVER['REQUEST_URI'], get_permalink($vars['ID'])), $loginmsg));
            if (get_option('_wpdm_hide_all', 0) == 1) return $type == 'page' ? $hide_all_message : '';
        }

        if (is_user_logged_in() && !self::userCanAccess($vars['ID']) && get_option('_wpdm_hide_all', 0) == 1) return $type != 'page' ? "" : get_option('__wpdm_permission_denied_msg', __("You are not allowed to download this item!", "download-manager"));


        /*$default['link'] = 'link-template-default.php';
        $default['page'] = 'page-template-default.php';


        if ($template == '') {
            if (!isset($vars['page_template'])) $vars['page_template'] = 'page-template-1col.php';
            if (!isset($vars['template'])) $vars['template'] = 'link-template-calltoaction3.php';
            $template = $type == 'page' ? $vars['page_template'] : $vars['template'];
        }

        if ($template == '')
            $template = $default[$type];

        //$templates = maybe_unserialize(get_option("_fm_".$type."_templates", true));
        //if(isset($templates[$template]) && isset($templates[$template]['content'])) $template = $templates[$template]['content'];
        $template_content = WPDM()->packageTemplate->get($template, $type, true);

        if ($template_content)
            $template = $template_content;
        else
            if (!strpos(strip_tags($template), "]")) {
                $template = wpdm_basename($template);
                $template = str_replace(".php", "", $template) . ".php";
                $themeltpldir = get_stylesheet_directory() . '/download-manager/' . $type . '-templates/';
                $pthemeltpldir = get_template_directory() . '/download-manager/' . $type . '-templates/';
                //if(!file_exists($ltpldir) || !file_exists($ltpldir.$template))
                $ltpldir = WPDM_TPL_DIR . $type . '-templates/';

                $template_file = '';
                if (file_exists($themeltpldir . $template)) $template_file = ($themeltpldir . $template); // Apply if available in the child theme
                else if (file_exists($pthemeltpldir . $template)) $template_file = ($pthemeltpldir . $template); // Apply if available in the parent theme
                else if (file_exists($ltpldir . $template)) $template_file = ($ltpldir . $template);
                else if (file_exists($ltpldir . $type . "-template-" . $template)) $template_file = ($ltpldir . $type . "-template-" . $template);
                else $template_file = (wpdm_tpl_path($default[$type], $ltpldir));

                if ($template_file !== '') {
                    ob_start();
                    $ID = $vars['ID'];
                    global $wp_filter;
                    $all_tc = $wp_filter['the_content'];
                    unset($wp_filter['the_content']);
                    remove_filter("the_content", "wpdm_downloadable");
                    include $template_file;
                    $template = ob_get_clean();
                    $wp_filter['the_content'] = $all_tc;
                    //add_filter("the_content", "wpdm_downloadable");
                    if (!preg_match("/\[([^\]]+)\]/", $template, $found)) {
                        return $template;
                    }
                }

            }*/

        $ret = $this->prepare($vars['ID'], $template, $type);
        if (!is_wp_error($ret))
            $vars = $this->packageData;
        else
            return '';

        // Get template content
        $template = $this->getTemplateContent($template, $vars['ID'], $type);

        if (isset($vars['__loginform_only']) && $vars['__loginform_only'] != '') return $vars['__loginform_only'];

        preg_match_all("/\[cf ([^\]]+)\]/", $template, $cfmatches);
        preg_match_all("/\[thumb_([0-9]+)x([0-9]+)\]/", $template, $matches);
        preg_match_all("/\[thumb_url_([0-9]+)x([0-9]+)\]/", $template, $umatches);
        preg_match_all("/\[thumb_gallery_([0-9]+)x([0-9]+)\]/", $template, $gmatches);
        preg_match_all("/\[excerpt_([0-9]+)\]/", $template, $xmatches);
        preg_match_all("/\[pdf_thumb_([0-9]+)x([0-9]+)\]/", $template, $pmatches);
        preg_match_all("/\[txt=([^\]]+)\]/", $template, $txtmatches);
        preg_match_all("/\[hide_empty:([^\]]+)\]/", $template, $hematches);
        preg_match_all("/\[video_player_([0-9]+)\]/", $template, $vdmatches);
        preg_match_all("/\[product_preview_([0-9]+)x([0-9]+)\]/", $template, $ppmatches);
        preg_match_all("/\[file_list_extended_([0-9]+)x([0-9]+)x([0-9]+)\]/", $template, $flematches);
        preg_match_all("/\[image_gallery_([0-9]+)x([0-9]+)x([0-9]+)\]/", $template, $igematches);


        //$thumb = wp_get_attachment_image_src(get_post_thumbnail_id($vars['ID']), 'full');
        //$vars['preview'] = $thumb['0'];
        //$vars['featured_image'] = ($vars['preview'] != '')?"<img src='{$vars['preview']}' alt='{$vars['title']}' />":"";

        $tfiles = $vars['files'];
        $pdf = is_array($tfiles) ? array_shift($tfiles) : "";
        $ext = FileSystem::fileExt($pdf);

        // [video_player_...]
        foreach ($vdmatches[0] as $nd => $scode) {
            $scode = str_replace(array('[', ']'), '', $scode);
            $vars[$scode] = self::videoPlayer($vars['ID'], $vars['files'], $vdmatches[1][$nd]);
        }

        //Replace all file list extended tags
        foreach ($flematches[0] as $nd => $scode) {
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = FileList::Box($vars, $flematches[1][$nd], $flematches[2][$nd], $flematches[3][$nd]);
        }

        //Replace all image gallery tags
        foreach ($igematches[0] as $nd => $scode) {
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = FileList::imageGallery($vars, $igematches[1][$nd], $igematches[2][$nd], $igematches[3][$nd]);
        }


        //Replace all txt variables
        foreach ($txtmatches[0] as $nd => $scode) {
            $scode = str_replace(array('[', ']'), '', $scode);
            $vars[$scode] = __($txtmatches[1][$nd], "download-manager");
        }

        // Parse [pdf_thumb] tag in link/page template
        if (strpos($template, 'pdf_thumb')) {
            if ($ext == 'pdf') {
                $pdf_preview = FileSystem::pdfThumbnail($pdf, $vars['ID']);
                $vars['pdf_thumb'] = "<img alt='{$vars['title']}' src='" . $pdf_preview . "' />";
                $vars['pdf_thumb_url'] = $pdf_preview;
                $vars['pdf_name'] = str_replace(["pdf", "PDF"], "", wp_basename($pdf));
            }
            else $vars['pdf_thumb'] = $vars['preview'] != '' ? "<img alt='{$vars['title']}' src='{$vars['preview']}' />" : "";
        }

        // Parse [pdf_thumb_WxH] tag in link/page template
        foreach ($pmatches[0] as $nd => $scode) {
            $imsrc = wpdm_dynamic_thumb(FileSystem::pdfThumbnail($pdf, $vars['ID']), array($pmatches[1][$nd], $pmatches[2][$nd]));
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = $imsrc != '' ? "<img src='" . $imsrc . "' alt='{$vars['title']}' />" : '';
        }

        // Parse [file_type] tag in link/page template
        if (strpos($template, 'file_type')) {
            $vars['file_types'] = self::fileTypes($vars['ID'], false);
            if (is_array($vars['file_types']))
                $vars['file_types'] = implode(", ", $vars['file_types']);
            $vars['file_type_icons'] = self::fileTypes($vars['ID']);
        }

        $crop = get_option('__wpdm_crop_thumbs', true);

        // [thumb_WxH]
        foreach ($matches[0] as $nd => $scode) {
            $imsrc = wpdm_dynamic_thumb($vars['preview'], array($matches[1][$nd], $matches[2][$nd]), $crop);
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = $vars['preview'] != '' ? "<img class='wpdm-thumb wpdm-thumb-{$matches[1][$nd]}x{$matches[2][$nd]} wpdm-thumb-{$vars['ID']}' src='" . $imsrc . "' alt='{$vars['title']}' />" : '';
        }

        // [thumb_url...]
        foreach ($umatches[0] as $nd => $scode) {
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = $vars['preview'] != '' ? wpdm_dynamic_thumb($vars['preview'], array($umatches[1][$nd], $umatches[2][$nd]), $crop) : '';
        }

        // [thumb_gallery...]
        foreach ($gmatches[0] as $nd => $scode) {
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = $this->additionalPreviewImages($vars, $gmatches[1][$nd], $gmatches[2][$nd]);
        }

        // [product_preview...]
        foreach ($ppmatches[0] as $nd => $scode) {
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = self::productPreview($vars['ID'], $vars['files'], $ppmatches[1][$nd], $ppmatches[2][$nd]);
        }

        // [excerpt_...]
        foreach ($xmatches[0] as $nd => $scode) {
            $ss = substr(strip_tags($vars['description']), 0, intval($xmatches[1][$nd]));
            $tmp = explode(" ", substr(strip_tags($vars['description']), intval($xmatches[1][$nd])));
            $bw = array_shift($tmp);
            $ss .= $bw;
            $scode = str_replace(array("[", "]"), "", $scode);
            $vars[$scode] = $ss . '...';
        }

        if ($type == 'page' && (strpos($template, '[similar_downloads]') || strpos($vars['description'], '[similar_downloads]')))
            $vars['similar_downloads'] = $this->similarPackages($vars, 6);

        if (strpos($template, 'doc_preview'))
            $vars['doc_preview'] = self::docPreview($vars);

        if (substr_count($template, 'video_preview_modal'))
            $vars['video_preview_modal'] = self::videoPreviewModal($vars, $type);


        $vars['fav_button'] = self::favBtn($vars['ID']);
        $vars['fav_button_sm'] = self::favBtn($vars['ID'], array('size' => 'btn-sm', 'a2f_label' => "<i class='fa fa-heart'></i> &nbsp; " . __("Add to favourite", "download-manager"), 'rff_label' => "<i class='fa fa-heart'></i> &nbsp; " . __("Remove from favourite", "download-manager")));
        $vars['fav_button_ico_sm'] = self::favBtn($vars['ID'], array('size' => 'btn-sm', 'a2f_label' => "<i class='far fa-heart'></i>", 'rff_label' => "<i class='fas fa-heart'></i>"));
        $vars['fav_button_ico'] = self::favBtn($vars['ID'], array('size' => '', 'a2f_label' => "<i class='fa fa-heart'></i>", 'rff_label' => "<i class='fa fa-heart'></i>"));


        // If need to re-process any data before fetch template
        $vars['__template_type'] = $type;
        $vars = apply_filters("wdm_before_fetch_template", $vars, $template, $type);

        foreach ($hematches[0] as $index => $hide_empty) {
            $hide_empty = str_replace(array('[', ']'), '', $hide_empty);
            if (!isset($vars[$hematches[1][$index]]) || ($vars[$hematches[1][$index]] == '' || $vars[$hematches[1][$index]] == '0'))
                $vars[$hide_empty] = 'wpdm_hide wpdm_remove_empty';
            else
                $value[$hide_empty] = '';
        }


        $keys = array();
        $values = array();

        foreach ($vars as $key => $value) {
            if (!is_array($value) && !is_object($value)) {
                $keys[] = "[$key]";
                $values[] = $value;
            }
        }


        $loginform = wpdm_login_form(array('redirect' => get_permalink($vars['ID'])));
        $hide_all_message = get_option('__wpdm_login_form', 0) == 1 ? $loginform : stripcslashes(str_replace(array("[loginform]", "[this_url]", "[package_url]"), array($loginform, $_SERVER['REQUEST_URI'], get_permalink($vars['ID'])), $loginmsg));

        if ($vars['download_link'] == 'blocked' && $type == 'link') return "";
        if ($vars['download_link'] == 'blocked' && $type == 'page') return get_option('__wpdm_permission_denied_msg');
        if ($vars['download_link'] == 'loginform' && $type == 'link') return "";
        if ($vars['download_link'] == 'loginform' && $type == 'page') return $hide_all_message;


        $template = str_replace($keys, $values, @stripcslashes($template));

        $template = apply_filters("wpdm_after_fetch_template", $template, $vars);

        //wp_reset_query();
        //wp_reset_postdata();
        return $template;
    }

    /*public static function parseTemplate($template, $post, $type = 'link')
    {

        if (!strpos(strip_tags($template), "]")) {

            $ltpldir = get_stylesheet_directory() . '/download-manager/' . $type . '-templates/';
            if (!file_exists($ltpldir) || !file_exists($ltpldir . $template))
                $ltpldir = WPDM_BASE_DIR . '/tpls/' . $type . '-templates/';
            if (file_exists(TEMPLATEPATH . '/' . $template)) $template = file_get_contents(TEMPLATEPATH . '/' . $template);
            else if (file_exists($ltpldir . $template)) $template = file_get_contents($ltpldir . $template);
            else if (file_exists($ltpldir . $template . '.php')) $template = file_get_contents($ltpldir . $template . '.php');
            else if (file_exists($ltpldir . $type . "-template-" . $template . '.php')) $template = file_get_contents($ltpldir . $type . "-template-" . $template . '.php');
        }

        preg_match_all("/\[([^\]]+)\]/", $template, $matched);
        $post = (array)$post;
        $post['title'] = $post['post_title'];
        foreach ($matched[1] as $id => $key) {
            switch ($key) {
                case 'page_link':
                    $post[$key] = "<a href='" . get_permalink($post['ID']) . "'>{$post['post_title']}</a>";
                    break;
                case 'page_url':
                    $post[$key] = get_permalink($post['ID']);
                    break;
                case 'file_size':
                    $post[$key] = get_post_meta($post['ID'], '__wpdm_package_size', true);
                    break;
                default:
                    $post[$key] = get_post_meta($post['ID'], '__wpdm_' . $key, true);
                    break;
            }
        }
        $post = apply_filters("wdm_before_fetch_template", $post, $template, $type);
        $vars = array_keys($post);
        $vals = array_values($post);
        foreach ($vars as &$var) {
            $var = "[$var]";
        }
        $template = str_replace($vars, $vals, $template);
        $template = apply_filters("wpdm_after_fetch_template", $template, $vars);
        wp_reset_query();
        return $template;
    }*/

    /**
     * @usage Find attached files types with a package
     * @param $ID
     * @param bool|true $img
     * @return array|string
     */
    public static function fileTypes($ID, $img = true, $size = 16)
    {
        $files = maybe_unserialize(get_post_meta($ID, '__wpdm_files', true));
        $ext = array();
        if (is_array($files)) {
            foreach ($files as $f) {
                $f = trim($f);
                $ext[] = FileSystem::fileExt($f);
            }
        }

        $ext = array_unique($ext);
        $exico = '';
        foreach ($ext as $exi) {
            $exico .= "<img alt='{$exi}' title='{$exi}' class='ttip' style='width:{$size}px;height:{$size}px;' src='" . FileSystem::fileTypeIcon($exi) . "' /> ";
        }
        if ($img) return $exico;
        return $ext;
    }


    /**
     * @param $package
     * @return string
     * @usage Generate Google Doc Preview
     */
    public function docPreview($package)
    {

        //$files = $package['files'];
        $files = $this->getFiles($package['ID']);
        if (!is_array($files)) return "";
        $ind = -1;
        $fext = '';
        foreach ($files as $i => $sfile) {
            $ifile = $sfile;
            $sfile = explode(".", $sfile);
            $fext = end($sfile);
            if (in_array(end($sfile), array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'))) {
                $ind = $i;
                break;
            }
        }

        if ($ind == -1) return "";
        $ext = count($files) > 1 ? 'ind=' . $ind : '';
        $params = array('ind' => $ind, 'filename' => $files[$ind]);
        $url = $this->getDownloadURL($package['ID'], $params);
        $url .= "&open=1";
        if (strpos($ifile, "://")) $url = $ifile;
        $doc_preview_html = FileSystem::docViewer($url, $package['ID'], $fext);
        $doc_preview_html = apply_filters('wpdm_doc_preview', $doc_preview_html, $package, $url, $fext);
        return $doc_preview_html;
    }

    /**
     * Get additional preview images
     * @param $file
     * @param $w
     * @param $h
     * @return string
     */
    function additionalPreviewImages($file, $w, $h){

        $file['additional_previews'] = maybe_unserialize(get_post_meta($file['ID'],'__wpdm_additional_previews', true));
        $k = 0;
        $img = '';
        $id = uniqid();
        if($file['additional_previews']){
            foreach($file['additional_previews'] as $p){
                ++$k;
                if( is_numeric($p) )
                    $img .= "<a href='".wp_get_attachment_url($p)."' id='more_previews_a_{$k}' class='more_previews_a imgpreview wpdm-lightbox' data-lightbox-gallery='gallery_{$id}' rel='previews'><img id='more_previews_{$k}' class='more_previews img-rounded' src='". wpdm_dynamic_thumb( get_attached_file( $p ), array($w, $h)) ."'/></a>";
                else
                    $img .= "<a href='{$p}' id='more_previews_a_{$k}' class='more_previews_a imgpreview wpdm-lightbox' data-lightbox-gallery='gallery_{$id}' rel='previews' ><img id='more_previews_{$k}' class='more_previews img-rounded' src='". wpdm_dynamic_thumb($p, array($w, $h)) ."'/></a>";
            }}
        $js = ""; // "<script>jQuery(function($){ $('a.more_previews_a').nivoLightbox(); });</script>";
        return $img.$js;
    }

    /**
     * @usage Generates view preview modal link of the given package
     * @param $package
     * @param int $embed
     * @param array $extras
     * @return string
     */
    public function videoPreviewModal($package, $template_type = 'link')
    {
        if (is_int($package)) $package = get_post($package, ARRAY_A);
        $link_label = wpdm_valueof($package, 'link_label');
        $link_label = $link_label ? $link_label : $this->linkLabel($package['ID']);
        $style = wpdm_download_button_style($template_type === 'page', $package['ID']);
        $files = isset($package['files']) ? $package['files'] : $this->getFiles($package['ID']);
        $video = "";
        foreach ($files as $file) {
            if (substr_count($file, 'youtu.be') || substr_count($file, 'youtube.com') || substr_count($file, 'vimeo.com')) {
                $video = $file;
            }
        }
        $link = "<a class='wpdm-lightbox {$style}' href='{$video}'>{$link_label}</a>";
        return $link;

    }

    /**
     * Get package link label
     * @param $ID
     * @return mixed|string|void
     */
    public function linkLabel($ID)
    {
        $link_label = get_post_meta($ID, '__wpdm_link_label', true);
        $link_label = esc_attr($link_label);
        $link_label = $link_label ? $link_label : __("Download", "download-manager");
        return $link_label;
    }

    /**
     * @usage Create New Package
     * @param $data
     * @return mixed
     */
    public static function create($package_data)
    {

        if (isset($package_data['post_type']))
            unset($package_data['post_type']);

        $package_data_core = array(
            'post_title' => '',
            'post_content' => '',
            'post_excerpt' => '',
            'post_status' => 'publish',
            'post_type' => 'wpdmpro',
            'post_author' => get_current_user_id(),
            'ping_status' => get_option('default_ping_status'),
            'post_parent' => 0,
            'menu_order' => 0,
            'to_ping' => '',
            'pinged' => '',
            'post_password' => '',
            'guid' => '',
            'post_content_filtered' => '',
            'import_id' => 0
        );

        $package_data_meta = array(
            'files' => array(),
            'fileinfo' => array(),
            'package_dir' => '',
            'link_label' => __("Download", "download-manager"),
            'download_count' => 0,
            'view_count' => 0,
            'version' => '1.0.0',
            'stock' => 0,
            'package_size' => 0,
            'package_size_b' => 0,
            'access' => '',
            'individual_file_download' => -1,
            'cache_zip' => -1,
            'template' => 'link-template-panel.php',
            'page_template' => 'page-template-1col-flat.php',
            'password_lock' => '0',
            'facebook_lock' => '0',
            'gplusone_lock' => '0',
            'linkedin_lock' => '0',
            'tweet_lock' => '0',
            'email_lock' => '0',
            'icon' => '',
            'import_id' => 0
        );

        foreach ($package_data_core as $key => &$value) {
            $value = isset($package_data[$key]) ? $package_data[$key] : $package_data_core[$key];
        }

        if (!isset($package_data['ID']))
            $post_id = wp_insert_post($package_data_core);
        else {
            $post_id = $package_data['ID'];
            $package_data_core['ID'] = $post_id;
            wp_update_post($package_data_core);
        }

        foreach ($package_data_meta as $key => $value) {
            $value = isset($package_data[$key]) ? $package_data[$key] : $package_data_meta[$key];
            update_post_meta($post_id, '__wpdm_' . $key, $value);
        }

        if (isset($package_data['cats']))
            wp_set_post_terms($post_id, $package_data['cats'], 'wpdmcategory');

        if (isset($package_data['featured_image'])) {

            $wp_filetype = wp_check_filetype(wp_basename($package_data['featured_image']), null);

            if (__::is_url($package_data['featured_image'])) {
                $upload_dir = wp_upload_dir();
                $file_path = $upload_dir['path'] . '/' . sanitize_file_name($package_data['post_title']) . '.' . $wp_filetype['ext'];
                //$data = remote_get($package_data['featured_image']);
                //$ret = copy($package_data['featured_image'], $file_path);
                //if(!$ret) wpdmdd($package_data['featured_image']);
                file_put_contents($file_path, wpdm_remote_get($package_data['featured_image']));
                $package_data['featured_image'] = $file_path;
            }

            $mime_type = '';

            if (isset($wp_filetype['type']) && $wp_filetype['type'])
                $mime_type = $wp_filetype['type'];
            unset($wp_filetype);
            $attachment = array(
                'post_mime_type' => $mime_type,
                'post_parent' => $post_id,
                'post_title' => wp_basename($package_data['featured_image']),
                'post_status' => 'inherit'
            );
            $attachment_id = wp_insert_attachment($attachment, $package_data['featured_image'], $post_id);
            unset($attachment);

            if (!is_wp_error($attachment_id)) {
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $package_data['featured_image']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                unset($attachment_data);
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        return $post_id;
    }

    /**
     * @param $id
     * @param array $atfb
     * @return string
     */
    public static function favBtn($id, $atfb = array(), $count = true)
    {
        if (empty($atfb))
            $atfb = array('size' => '', 'a2f_label' => "<i class='fa fa-heart'></i> &nbsp;" . __("Add to favourite", "download-manager"), 'rff_label' => "<i class='fa fa-heart'></i> &nbsp; " . __("Remove from favourite", "download-manager"));
        $atfb = apply_filters("wpdm_fav_btn", $atfb, $id);
        $myfavs = maybe_unserialize(get_user_meta(get_current_user_id(), '__wpdm_favs', true));
        $ufavs = maybe_unserialize(get_post_meta($id, '__wpdm_favs', true));
        $pfc = is_array($ufavs) ? count($ufavs) : 0;
        $btnclass = is_array($myfavs) && in_array($id, $myfavs) ? 'btn-danger' : 'btn-secondary';
        $label = is_array($myfavs) && in_array($id, $myfavs) ? $atfb['rff_label'] : $atfb['a2f_label'];
        extract($atfb);
        if ($count)
            return "<div class='btn-group'><button type='button' data-alabel=\"{$atfb['a2f_label']}\" data-rlabel=\"{$atfb['rff_label']}\" data-package='{$id}' class='btn btn-wpdm-a2f  {$btnclass} {$size} btn-simple'>{$label}</button><button class='btn btn-secondary btn-simple {$size}' disabled='disabled'>{$pfc}</button></div>";
        else
            return "<button type='button' data-alabel=\"{$atfb['a2f_label']}\" data-rlabel=\"{$atfb['rff_label']}\" data-package='{$id}' class='btn btn-wpdm-a2f {$btnclass} {$size} btn-simple'>{$label}</button>";

    }

    /**
     * Shows favourite count
     * @param $id
     * @return int
     */
    public static function favCount($id)
    {
        $ufavs = maybe_unserialize(get_post_meta($id, '__wpdm_favs', true));
        $pfc = is_array($ufavs) ? count($ufavs) : 0;
        return $pfc;
    }

    /**
     * @param $ID
     * @param $emails
     * @param string $names
     * @param int $usageLimit
     * @param int $expireTime
     * @usage mail package link to specified email address
     * @since 4.7.4
     */
    static function emailDownloadLink($ID, $emails, $names = '', $usageLimit = 3, $expireTime = 604800)
    {
        if (!is_array($emails)) $emails = explode(",", $emails);
        if (!is_array($names)) $names = explode(",", $names);
        $title = get_the_title($ID);
        $banner = get_the_post_thumbnail_url($ID, array(600, 400));
        $logo = get_site_icon_url();
        foreach ($emails as $index => $email) {
            $download_link = WPDM()->package->expirableDownloadLink($ID, $usageLimit, $expireTime);
            $download_page_link = WPDM()->package->expirableDownloadPage($ID, $usageLimit, $expireTime);
            $params = array(
                'to_email' => $email,
                'name' => isset($names[$index]) ? $names[$index] : '',
                'package_name' => $title,
                'download_url' => $download_link,
                'download_page_url' => $download_page_link,
                'img_logo' => $logo,
                'banner' => $banner
            );
            \WPDM\__\Email::send("email-lock", $params);
        }
    }

    /**
     * Check if specified link or page template have the tag
     * @param null $template
     * @param $tag
     * @return bool|string
     */
    static function templateHasTag($template = null, $tag = '')
    {
        if (!$template) return true;
        else if (is_string($tag)) return substr_count($template, "[{$tag}]");
        else if (is_array($tag)) {
            foreach ($tag as $t) {
                if (substr_count($template, "[{$t}]")) return true;
            }
        }
        return false;

    }

    /**
     * Returns package icon
     * @param $ID
     * @return string
     */
    static function icon($ID, $html = false, $class = '')
    {
        $icon = get_post_meta($ID, '__wpdm_icon', true);
        if ($icon == '') {
            $file_types = WPDM()->package->fileTypes($ID, false);
            if (count($file_types)) {
                if (count($file_types) == 1) {
                    $tmpavar = $file_types;
                    $ext = $tmpvar = array_shift($tmpavar);
                } else
                    $ext = 'zip';
            } else
                $ext = "unknown";
            if ($ext === '') $ext = 'wpdm';
            $icon = FileSystem::fileTypeIcon($ext);
        }
        if ($html) $icon = "<img src='{$icon}' alt='Icon' class='$class' />";
        return apply_filters("wpdm_package_icon", $icon, $ID);
    }

    /**
     * Create a copy of a given package
     * @param $ID
     * @return int|\WP_Error
     */
    static function copy($ID, $author = null, $new_meta = array())
    {
        $old_pack = (array)get_post($ID);
        $package = array(
            'post_title' => $old_pack['post_title'],
            'post_content' => $old_pack['post_content'],
            'post_status' => $old_pack['post_status'],
            'comment_status' => $old_pack['comment_status'],
            'ping_status' => $old_pack['ping_status'],
            'post_type' => 'wpdmpro'
        );

        if ($author)
            $package['post_author'] = $author;
        $new_ID = wp_insert_post($package);

        $meta = get_post_meta($ID);

        foreach ($meta as $key => $value) {
            foreach ($value as $v) {
                update_post_meta($new_ID, $key, maybe_unserialize($v));
            }
        }
        if (is_array($new_meta)) {
            foreach ($new_meta as $key => $value) {
                update_post_meta($new_ID, $key, maybe_unserialize($value));
            }
        }
        return $new_ID;
    }

    static function dummy()
    {
        $package = array(
            'post_title' => __('Sample Package', 'download-manager'),
            'post_content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ',
            'excerpt' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s',
            'post_status' => 'publish',
            'download_link' => '<a href="#">Download</a>',
            'download_link_extended' => '<a href="#">Download</a>',
        );
        return $package;
    }

    /**
     * @param null $ID
     * @return array Additional preview image urls
     */
    function additionalPreviews($ID = null)
    {
        $ID = $ID ? $ID : $this->ID;
        if (!$ID && is_singular('wpdmpro')) $ID = get_the_ID();
        if (!$ID) return array();
        $additional_previews = get_post_meta($ID, '__wpdm_additional_previews', true);
        $previews = array();
        foreach ($additional_previews as $media_id) {
            $previews[] = wp_get_attachment_url($media_id);
        }
        return $previews;
    }

    function getThumbnail($ID, $FILEID, $size)
    {
        if (!$this->files)
            $this->files = self::getFiles($ID, true);
        $file = wpdm_valueof($this->files, $FILEID);

        $imgext = array('png', 'jpg', 'jpeg', 'gif');
        $ext = FileSystem::fileExt($file);
        $thumb = '';
        $abspath = WPDM()->package->locateFile($file);

        if (in_array($ext, $imgext) && $abspath)
            $thumb = FileSystem::imageThumbnail($abspath, $size[0], $size[1], WPDM_USE_GLOBAL, true);
        else if ($ext === 'svg')
            $thumb = str_replace(ABSPATH, home_url('/'), $file);
        else if (strtolower($ext) === 'pdf' && class_exists('Imagick'))
            $thumb = FileSystem::pdfThumbnail($file, md5($file));
        else {
            $thumb = FileSystem::fileTypeIcon($ext);
        }

        return apply_filters("wpdm_file_thumbnail", $thumb, ['file' => $file, 'FILEID' => $FILEID, 'ID' => $ID, 'size' => $size]);
    }

    function locateFile($file)
    {
        if (file_exists($file))
            return $file;
        if (file_exists(UPLOAD_DIR . $file))
            return UPLOAD_DIR . $file;
        return false;
    }

    function addViewCount()
    {
        if (isset($_REQUEST['__wpdm_view_count']) && wp_verify_nonce($_REQUEST['__wpdm_view_count'], NONCE_KEY)) {

            $id = (int)($_REQUEST['id']);
            $views = (int)get_post_meta($id, '__wpdm_view_count', true);
            update_post_meta($id, '__wpdm_view_count', $views + 1);
            wp_send_json(['views' => $views + 1]);
        }
    }


    /**
     * @usage Find similar packages
     * @param null $package_id
     * @param int $count
     * @param bool|true $html
     * @return array|bool|string
     */
    function similarPackages($package_id = null, $count = 5, $html = true)
    {
        $id = $package_id ? $package_id : get_the_ID();
        if (is_array($package_id)) $id = $package_id['ID'];
        $tags = wp_get_post_terms($id, 'wpdmtag');
        $cats = wp_get_post_terms($id, 'wpdmcategory');
        $posts = array();
        if ($tags) {
            $tag_ids = array();
            foreach ($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
            foreach ($cats as $individual_cat) $cat_ids[] = $individual_cat->term_id;
            $args = array(
                'post_type' => 'wpdmpro',
                'tax_query' => [
                    [
                    'taxonomy' => 'wpdmtag',
                    'field' => 'id',
                    'terms' => $tag_ids,
                    'operator' => 'IN'
                    ],
                    [
                        'taxonomy' => 'wpdmcategory',
                        'field' => 'id',
                        'terms' => $cat_ids,
                        'operator' => 'IN'
                    ],
                    'relation' => 'OR'
                ],
                'post__not_in' => array($id),
                'posts_per_page' => $count
            );

            $posts = get_posts($args);

            if (!$html) return $posts;

            $html = "";
            //Filter hook to change related packages/downloads template
            $template = apply_filters("wpdm_replated_package_template", "link-template-panel.php", $package_id);
            $cols = apply_filters("wpdm_replated_package_columns", 6, $package_id);
            foreach ($posts as $p) {

                $package['ID'] = $p->ID;
                $package['post_title'] = $p->post_title;
                $package['post_content'] = $p->post_content;
                $package['post_excerpt'] = $p->post_excerpt;
                $html .= "<div class='col-md-{$cols}'>" . wpdm_fetch_template($template, $package, 'link') . "</div>";

            }
        }
        if (count($posts) == 0) $html = "<div class='col-md-12'><div class='alert alert-info'>" . __("No related download found!", "download-manager") . "</div> </div>";
        $html = "<div class='w3eden'><div class='row'>" . $html . "</div></div>";
        wp_reset_query();
        return $html;
    }

    function search($keyword = '')
    {
        $keyword = wpdm_query_var('search') ? : $keyword;
        if($keyword) {
            $query = new Query();
            $query->search($keyword);
            if(wpdm_query_var('premium', 'int') > 0) {
                $query->meta('__wpdm_base_price', 0, '>');
                $query->meta_relation('AND');
            }
            $query->process();
            $packages = $query->packages();
            if(wpdm_query_var('premium', 'int') > 0 && function_exists('wpdmpp_product_license_options')) {
                foreach ($packages as &$package) {
                    $licenses = wpdmpp_product_license_options($package->ID);
                    $package->licenses = count($licenses) > 0 ? $licenses : null;
                }
            }
            wp_send_json(['total' => $query->count, 'packages' => $packages, 'q' => $query->params]);
        }
    }


}
