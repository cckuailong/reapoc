<?php
/*
  Plugin Name: Like Button Rating ♥ LikeBtn
  Plugin URI: https://likebtn.com/en/wordpress-like-button-plugin
  Description: Add Like button to posts, pages, comments, WooCommerce, BuddyPress, bbPress, custom post types! Sort content by likes! Get instant stats and insights!
  Version: 2.6.37
  Text Domain: likebtn-like-button
  Author: LikeBtn
  Author URI: https://likebtn.com
 */

// Debug
// ini_set('display_errors', 'On');
// ini_set('error_reporting', E_ALL);

// Plugin version
define('LIKEBTN_VERSION', '2.6.37');
// Current DB version
define('LIKEBTN_DB_VERSION', 20);

// Plugin name (for templates)
define('LIKEBTN_PLUGIN_NAME', 'likebtn-like-button');
// For notices
define('LIKEBTN_PLUGIN_TITLE', 'Like Button Rating');

// Shortcodes
define('LIKEBTN_SHORTCODE_OFF', 'likebtn_off');
define('LIKEBTN_SHORTCODE_LIKES', 'likebtn_likes');
define('LIKEBTN_SHORTCODE_DISLIKES', 'likebtn_dislikes');

// LikeBtn plans
define('LIKEBTN_PLAN_FREE', 0);
define('LIKEBTN_PLAN_PLUS', 1);
define('LIKEBTN_PLAN_PRO', 2);
define('LIKEBTN_PLAN_VIP', 3);
define('LIKEBTN_PLAN_ULTRA', 4);
define('LIKEBTN_PLAN_TRIAL', 9);
//update_option('likebtn_plan', LIKEBTN_PLAN_FREE);

// Flag added to entity excerpts
define('LIKEBTN_LIST_FLAG', '_likebtn_list');

// entity names
define('LIKEBTN_ENTITY_POST', 'post');
define('LIKEBTN_ENTITY_POST_LIST', 'post'.LIKEBTN_LIST_FLAG);
define('LIKEBTN_ENTITY_PAGE', 'page');
define('LIKEBTN_ENTITY_PAGE_LIST', 'page'.LIKEBTN_LIST_FLAG);
define('LIKEBTN_ENTITY_COMMENT', 'comment');
define('LIKEBTN_ENTITY_ATTACHMENT', 'attachment');
define('LIKEBTN_ENTITY_ATTACHMENT_LIST', 'attachment'.LIKEBTN_LIST_FLAG);
define('LIKEBTN_ENTITY_CUSTOM_ITEM', 'custom_item');
define('LIKEBTN_ENTITY_USER', 'user'); // invisible type, used for bbPress
define('LIKEBTN_ENTITY_PRODUCT', 'product'); // WooCommerce
define('LIKEBTN_ENTITY_PRODUCT_LIST', 'product'.LIKEBTN_LIST_FLAG); // WooCommerce
define('LIKEBTN_ENTITY_BP_ACTIVITY_POST', 'bp_activity_post');
define('LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE', 'bp_activity_update');
define('LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT', 'bp_activity_comment');
define('LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC', 'bp_activity_topic');
define('LIKEBTN_ENTITY_BP_MEMBER', 'bp_member');
define('LIKEBTN_ENTITY_BBP_POST', 'bbp_post');
define('LIKEBTN_ENTITY_BBP_USER', 'bbp_user');
define('LIKEBTN_ENTITY_UM_USER', 'um_user');
define('LIKEBTN_ENTITY_UM_USER_LIST', 'um_user'.LIKEBTN_LIST_FLAG);

// Like box
define('LIKEBTN_LIKE_BOX_AFTER', 'after');
define('LIKEBTN_LIKE_BOX_BEFORE', 'before');

// Default like box size
define('LIKEBTN_LIKE_BOX_SIZE', '10');

// Templates
define('LIKEBTN_TEMPLATE_LIKE_BOX', 'like-box.php');
define('LIKEBTN_TEMPLATE_ACTIVITY_SNIPPET', 'activity-snippet.php');
define('LIKEBTN_TEMPLATE_ADMIN_MAIL', 'admin_mail.php');

// position
define('LIKEBTN_POSITION_TOP', 'top');
define('LIKEBTN_POSITION_BOTTOM', 'bottom');
define('LIKEBTN_POSITION_BOTH', 'both');

// alignment
define('LIKEBTN_ALIGNMENT_LEFT', 'left');
define('LIKEBTN_ALIGNMENT_CENTER', 'center');
define('LIKEBTN_ALIGNMENT_RIGHT', 'right');

// Theme types
define('LIKEBTN_THEME_TYPE_PREDEFINED', 'predefined');
define('LIKEBTN_THEME_TYPE_CUSTOM', 'custom');

// Icon types
define('LIKEBTN_ICON_TYPE_ICON', 'icon');
define('LIKEBTN_ICON_TYPE_URL', 'url');

// Voting periods
define('LIKEBTN_VOTING_PERIOD_ALWAYS', '');
define('LIKEBTN_VOTING_PERIOD_DATE', 'date');
define('LIKEBTN_VOTING_PERIOD_CREATED', 'created');

// Voter identification
define('LIKEBTN_VOTER_BY_IP', '');
define('LIKEBTN_VOTER_BY_USER', 'user');

// User authorization check
define('LIKEBTN_USER_LOGGED_IN_ALL', '');
define('LIKEBTN_USER_LOGGED_IN_YES', '1');
define('LIKEBTN_USER_LOGGED_IN_NO', '0');
define('LIKEBTN_USER_LOGGED_IN_ALERT', 'alert');
define('LIKEBTN_USER_LOGGED_IN_ALERT_BTN', 'alert_btn');
define('LIKEBTN_USER_LOGGED_IN_MODAL', 'modal');

// BuddyPress xprofile object type used in syncing
define('LIKEBTN_BP_XPROFILE_OBJECT_TYPE', 'data');

// statistics page size
define('LIKEBTN_STATISTIC_PAGE_SIZE', 50);

// show review link after this period
define('LIKEBTN_REVIEW_LINK_PERIOD', '1 month');

// Max length of the title (for comments)
define('LIKEBTN_TITLE_MAX_LENGTH', 50);

// item table name
define('LIKEBTN_TABLE_ITEM', 'likebtn_item');
// Votes table name
define('LIKEBTN_TABLE_VOTE', 'likebtn_vote');

// Vote types
define('LIKEBTN_VOTE_LIKE', 1);
define('LIKEBTN_VOTE_DISLIKE', -1);
define('LIKEBTN_VOTE_BOTH', 2);
define('LIKEBTN_VOTE_CANCEL', 0);

// custom fields names
define('LIKEBTN_META_KEY_LIKES', 'Likes');
define('LIKEBTN_META_KEY_DISLIKES', 'Dislikes');
define('LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES', 'Likes minus dislikes');
global $likebtn_custom_fields;
$likebtn_custom_fields = array(
    LIKEBTN_META_KEY_LIKES,
    LIKEBTN_META_KEY_DISLIKES,
    LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES,
);

// default widget title length
define('LIKEBTN_WIDGET_TITLE_LENGTH', 100);
define('LIKEBTN_WIDGET_EXCERPT_LENGTH', 200);

// Main website address
define('LIKEBTN_WEBSITE_MAIN_PROTOCOL', 'http');
define('LIKEBTN_WEBSITE_DOMAIN', 'likebtn.com');

// Url for sending feedback
define('LIKEBTN_WEBSITE_AJAX', "https://likebtn.com/en/ajax/ajax.php");
// Widget address
define('LIKEBTN_WIDGET_URL', "w.likebtn.com/js/w/widget.js");

define('LIKEBTN_IP_VOTE_INTERVAL', 86400);
define('LIKEBTN_MAX_IP_VOTE_INTERVAL', 31557600);

// Votes graph
define('LIKEBTN_VG_LEVEL_TOP', 0);
define('LIKEBTN_VG_LEVEL_DAY', 1);
define('LIKEBTN_VG_LEVEL_HOUR', 2);

// Minimum max_input_vars
define('LIKEBTN_MAX_INPUT_VARS', 150);

// CloudFlare IPs sync interval
define('LIKEBTN_CF_SYNC_INTERVAL', 86400); // 1 day
define('LIKEBTN_CF_SOURCE', 'https://www.cloudflare.com/ips-v4');
define('LIKEBTN_CF_IPV6_SOURCE', 'https://www.cloudflare.com/ips-v6');

// post format: just to translate
$post_formats = array(
    'standard' => __('Standard'),
    'aside' => __('Aside'),
    'image' => __('Image'),
    'link' => __('Link'),
    'quote' => __('Quote'),
    'status' => __('Status'),
);

global $likebtn_global_disabled;
$likebtn_global_disabled = false;

// post types without excerpts
global $likebtn_no_excerpts;
$likebtn_no_excerpts = array(
    LIKEBTN_ENTITY_COMMENT
);

// post types titles
global $likebtn_entity_titles;
$likebtn_entity_titles = array(
    LIKEBTN_ENTITY_POST => __('Post'),
    LIKEBTN_ENTITY_POST_LIST => __('Post List'),
    LIKEBTN_ENTITY_PAGE => __('Page'),
    LIKEBTN_ENTITY_PAGE_LIST => __('Page List'),
    LIKEBTN_ENTITY_COMMENT => __('Comment'),
    LIKEBTN_ENTITY_USER => __('User'),
    LIKEBTN_ENTITY_CUSTOM_ITEM => __('Custom Item'),
    LIKEBTN_ENTITY_PRODUCT => '(WooCommerce) '.__('Product'),
    LIKEBTN_ENTITY_PRODUCT_LIST => '(WooCommerce) '.__('Product List'),
    LIKEBTN_ENTITY_BP_ACTIVITY_POST => '(BuddyPress) '.__('Activity Post'),
    LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => '(BuddyPress) '.__('Activity Update'),
    LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => '(BuddyPress) '.__('Activity Comment'),
    LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => ' (BuddyPress) '.__('Activity Topic'),
    LIKEBTN_ENTITY_BP_MEMBER => '(BuddyPress) '.__('Member Profile'),
    LIKEBTN_ENTITY_BBP_POST => ' (bbPress) '.__('Forum Post'),
    LIKEBTN_ENTITY_BBP_USER => ' (bbPress) '.__('User Profile'),
    LIKEBTN_ENTITY_UM_USER => ' (UltimateMember) '.__('User Profile'),
    LIKEBTN_ENTITY_UM_USER_LIST => ' (UltimateMember) '.__('Member Directory'),
);

// map entities
global $likebtn_map_entities;
$likebtn_map_entities = array(
    LIKEBTN_ENTITY_BP_MEMBER => LIKEBTN_ENTITY_USER,
    LIKEBTN_ENTITY_BBP_USER => LIKEBTN_ENTITY_USER
);

// entities which are not based on posts
global $likebtn_nonpost_entities;
$likebtn_nonpost_entities = array(
    LIKEBTN_ENTITY_COMMENT,
    LIKEBTN_ENTITY_CUSTOM_ITEM,
    LIKEBTN_ENTITY_BP_ACTIVITY_POST,
    LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE,
    LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT,
    LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC,
    LIKEBTN_ENTITY_BP_MEMBER,
    //LIKEBTN_ENTITY_BBP_POST,
    LIKEBTN_ENTITY_BBP_USER,
    LIKEBTN_ENTITY_USER,
    LIKEBTN_ENTITY_UM_USER,
);

// bbPress post types in posts table
global $likebtn_bbp_post_types;
$likebtn_bbp_post_types = array(/*'forum',*/ 'topic', 'reply');

// languages
global $likebtn_page_sizes;
$likebtn_page_sizes = array(
    10,
    20,
    50,
    100,
    500,
    1000,
    5000,
);
global $likebtn_post_statuses;
$likebtn_post_statuses = array_reverse(get_post_statuses());

// likebtn settings
global $likebtn_settings;
$likebtn_settings = array(
    "theme" => array("default" => 'white'),
    "btn_size"               => array("default" => '22'),
    "icon_l"                 => array("default" => 'thmb5-u'),
    "icon_d"                 => array("default" => 'thmb5-d'),
    "icon_l_url"             => array("default" => ''),
    "icon_d_url"             => array("default" => ''),
    "icon_l_url_v"           => array("default" => ''),
    "icon_d_url_v"           => array("default" => ''),
    "icon_size"              => array("default" => '14'),
    "icon_l_c"               => array("default" => '#777777'),
    "icon_d_c"               => array("default" => '#777777'),
    "icon_l_c_v"             => array("default" => '#fbae05'),
    "icon_d_c_v"             => array("default" => '#fbae05'),
    "label_c"                => array("default" => '#555555'),
    "label_c_v"              => array("default" => '#3b3b3b'),
    "counter_l_c"            => array("default" => '#000000'),
    "counter_d_c"            => array("default" => '#000000'),
    "bg_c"                   => array("default" => '#fafafa'),
    "bg_c_v"                 => array("default" => '#fafafa'),
    "brdr_c"                 => array("default" => '#c6c6c6'),
    "f_size"                 => array("default" => '12'),
    "f_family"               => array("default" => 'Arial'),
    "label_fs"               => array("default" => 'b'),
    "counter_fs"             => array("default" => 'r'),
    "vert"                   => array("default" => '0'),
    "lang" => array("default" => "en"),
    "rtl" => array("default" => '0'),
    "ef_voting" => array("default" => ''),
    "show_like_label" => array("default" => '1'),
    "show_dislike_label" => array("default" => '0'),
    "like_enabled" => array("default" => '1'),
    "lazy_load" => array("default" => '0'),
    "dislike_enabled" => array("default" => '1'),
    "icon_like_show" => array("default" => '1'),
    "icon_dislike_show" => array("default" => '1'),
    "site_id" => array("default" => ""),
    "group_identifier" => array("default" => ""),
    //"local_domain" => array("default" => ''),
    "domain_from_parent" => array("default" => '0'),
    //"subdirectory" => array("default" => ''),
    "item_url" => array("default" => ''),
    "item_date" => array("default" => ''),
    "share_enabled" => array("default" => '1'),
    "item_title" => array("default" => ''),
    "item_description" => array("default" => ''),
    "item_image" => array("default" => ''),
    "popup_dislike" => array("default" => '0'),
    "counter_type" => array("default" => "number"),
    "counter_clickable" => array("default" => '0'),
    "counter_show" => array("default" => '1'),
    "counter_frmt" => array("default" => ''),
    "counter_count" => array("default" => '0'),
    "counter_padding" => array("default" => ''),
    "counter_zero_show" => array("default" => '0'),
    "voting_enabled" => array("default" => '1'),
    "voting_cancelable" => array("default" => '1'),
    "voting_both" => array("default" => '0'),
    "voting_frequency" => array("default" => ''),
    "addthis_pubid" => array("default" => ''),
    "addthis_service_codes" => array("default" => '', 'default_values' => array(
        'all' => 'facebook,twitter,preferred_1,preferred_2,preferred_3,preferred_4,preferred_5,compact',
        'ru' => 'vk,odnoklassniki_ru,twitter,facebook,preferred_1,preferred_2,preferred_3,compact'
    )),
    "share_size" => array("default" => 'medium'),
    "loader_image" => array("default" => ''),
    "loader_show" => array("default" => '0'),
    "loader_image" => array("default" => ''),
    "tooltip_enabled" => array("default" => '1'),
    "tooltip_like_show_always" => array("default" => '0'),
    "tooltip_dislike_show_always" => array("default" => '0'),
    "white_label" => array("default" => '0'),
    "rich_snippet" => array("default" => '0'),
    "popup_html" => array("default" => ''),
    "popup_donate" => array("default" => ''),
    "popup_content_order" => array("default" => 'popup_share,popup_donate,popup_html'),
    "popup_disabled" => array("default" => '0'),
    "popup_position" => array("default" => 'top'),
    "popup_style" => array("default" => 'light'),
    "popup_width" => array("default" => '176'),
    "popup_hide_on_outside_click" => array("default" => '1'),
    "popup_on_load" => array("default" => '0'),
    "event_handler" => array("default" => ''),
    //"info_message" => array("default" => '1'),
    "i18n_like" => array("default" => ''),
    "i18n_dislike" => array("default" => ''),
    "i18n_after_like" => array("default" => ''),
    "i18n_after_dislike" => array("default" => ''),
    "i18n_like_tooltip" => array("default" => ''),
    "i18n_dislike_tooltip" => array("default" => ''),
    "i18n_unlike_tooltip" => array("default" => ''),
    "i18n_undislike_tooltip" => array("default" => ''),
    "i18n_share_text" => array("default" => ''),
    "i18n_popup_close" => array("default" => ''),
    "i18n_popup_text" => array("default" => ''),
    "i18n_popup_donate" => array("default" => '')
);
// removed settings
global $likebtn_settings_deprecated;
$likebtn_settings_deprecated = array(
    'style' => array("default" => 'white'),
    'display_only' => array("default" => '0'),
    'unlike_allowed' => array("default" => '1'),
    'like_dislike_at_the_same_time' => array("default" => '0'),
    'show_copyright' => array("default" => '1')
);

// plans
global $likebtn_plans;
$likebtn_plans = array(
    LIKEBTN_PLAN_TRIAL => 'TRIAL',
    LIKEBTN_PLAN_FREE => 'FREE',
    LIKEBTN_PLAN_PLUS => 'PLUS',
    LIKEBTN_PLAN_PRO => 'PRO',
    LIKEBTN_PLAN_VIP => 'VIP',
    LIKEBTN_PLAN_ULTRA => 'ULTRA',
);

// themes
global $likebtn_styles;
$likebtn_styles = array(
    'white',
    'lightgray',
    'gray',
    'black',
    'padded',
    'drop',
    'line',
    'github',
    'transparent',
    'youtube',
    'habr',
    'heartcross',
    'plusminus',
    'google',
    'greenred',
    'large',
    'elegant',
    'disk',
    'squarespace',
    'slideshare',
    'baidu',
    'uwhite',
    'ublack',
    'uorange',
    'ublue',
    'ugreen',
    'direct',
    'homeshop',
    'nero',
    'sku',
    'review',
    'likebtn',
    'roundthumb',
    'ok',
    'mid',
    'mini',
    'bootstrap',
    'angular',
    'tick',
    'stack',
    'vertplus',
);

// languages
global $likebtn_default_locales;
$likebtn_default_locales = array(
    'en' => array('name'    => 'English',
          'en_name' => 'English',
          'iso'     => 'en'
    ),
    'ru' => array('name'    => 'Русский',
          'en_name' => 'Russian',
          'iso'     => 'ru'
    ),
    'af' => array('name'    => 'Afrikaans',
          'en_name' => 'Afrikaans',
          'iso'     => 'af'
    ),
    'ar' => array('name'    => 'العربية',
          'en_name' => 'Arabic',
          'iso'     => 'ar'
    ),
    'hy' => array('name'     => 'Հայերեն',
                  'en_name'       => 'Armenian',
                  'iso'      => 'hy'
    ),
    'be' => array('name'     => 'Беларуская',
                  'en_name'  => 'Belarusian',
                  'iso'      => 'be'
    ),
    'bn' => array('name'     => 'বাংলা',
                  'en_name'       => 'Bengali',
                  'iso'      => 'bn' // ?
    ),
    'bg' => array('name'     => 'Български език',
                  'en_name'       => 'Bulgarian',
                  'iso'      => 'bg'
    ),
    'bs' => array('name'     => 'bosanski jezik',
                  'en_name'       => 'Bosnian',
                  'iso'      => 'bs' // ?
    ),
    'ca' => array('name'    => 'Català',
          'en_name' => 'Catalan',
          'iso'     => 'ca' // ?
    ),
    'zh_CN' => array('name'    => '简体中文',
          'en_name' => 'Chinese',
          'iso'     => 'zh'
    ),
    'cs' => array('name'    => 'Čeština',
          'en_name' => 'Czech',
          'iso'     => 'cs'
    ),
    'fa' => array('name'     => 'فارسی',
                  'en_name'       => 'Persian (Farsi)',
                  'iso'      => 'fa'
    ),
    'fi' => array('name'     => 'Suomi',
          'en_name'  => 'Finnish',
          'iso'      => 'fi'
    ),
    'fr' => array('name'     => 'Français',
          'en_name'  => 'French',
          'iso'      => 'fr'
    ),
    'da' => array('name'    => 'Dansk',
          'en_name' => 'Danish',
          'iso'     => 'dk'
    ),
    'nl' => array('name'    => 'Nederlands',
          'en_name' => 'Dutch',
          'iso'     => 'nl'
    ),
    'de' => array('name'    => 'Deutsch',
          'en_name' => 'German',
          'iso'     => 'de'
    ),
    'el' => array('name'    => 'Ελληνικά',
          'en_name' => 'Greek',
          'iso'     => 'el'
    ),
    'he' => array('name'     => 'עברית',
                  'en_name'       => 'Hebrew',
                  'iso'      => 'he'
    ),
    'hu' => array('name'    => 'Magyar',
          'en_name' => 'Hungarian',
          'iso'     => 'hu'
    ),
    'id' => array('name'     => 'Bahasa Indonesia',
                  'en_name'  => 'Indonesian',
                  'iso'      => 'id'
    ),
    'it' => array('name'     => 'Italiano',
                  'en_name'  => 'Italian',
                  'iso'      => 'it'
    ),
    'ja' => array('name'    => '日本語',
          'en_name' => 'Japanese',
          'iso'     => 'jp'
    ),
    'kk' => array('name'    => 'Қазақ тілі',
          'en_name' => 'Kazakh',
          'iso'     => 'kk'
    ),
    'ko' => array('name'    => '한국어',
          'en_name' => 'Korean',
          'iso'     => 'kr'
    ),
    'lt' => array('name'    => 'Lietuvių kalba',
          'en_name' => 'Lithuanian',
          'iso'     => 'lt'
    ),
    'ne' => array('name'    => 'नेपाली',
          'en_name' => 'Nepali',
          'iso'     => 'ne'
    ),
    'no' => array('name'     => 'Norsk bokmål',
                  'en_name'       => 'Norwegian (Bokmal)',
                  'iso'      => 'nb'
    ),
    'pl' => array('name'     => 'Polski',
                  'en_name'  => 'Polish',
                  'iso'      => 'pl'
    ),
    'pt' => array('name'    => 'Português',
          'en_name' => 'Portuguese',
          'iso'     => 'pt'
    ),
    'pt_BR' => array('name'    => 'Português do Brasil',
          'en_name' => 'Portuguese (Brazil)',
          'iso'     => 'pt'
    ),
    'ro' => array('name'    => 'Română',
          'en_name' => 'Romanian',
          'iso'     => 'ro'
    ),
    'sr' => array('name'     => 'Српски језик',
                  'en_name'  => 'Serbian',
                  'iso'      => 'sr'
    ),
    'sk' => array('name'     => 'Slovenčina',
                  'en_name'  => 'Slovak',
                  'iso'      => 'sk'
    ),
    'sl' => array('name'     => 'Slovenščina',
                  'en_name'  => 'Slovenian',
                  'iso'      => 'sl'
    ),
    'es' => array('name'    => 'Español',
          'en_name' => 'Spanish',
          'iso'     => 'es'
    ),
    'sv' => array('name'    => 'Svenska',
          'en_name' => 'Swedish',
          'iso'     => 'sv'
    ),
    'th' => array('name'     => 'ไทย',
                  'en_name'       => 'Thai',
                  'iso'      => 'th'
    ),
    'tr' => array('name'    => 'Türkçe',
          'en_name' => 'Turkish',
          'iso'     => 'tr'
    ),
    'uk' => array('name'    => 'Українська мова',
          'en_name' => 'Ukrainian',
          'iso'     => 'uk'
    ),
    'vi' => array('name'    => 'Tiếng Việt',
          'en_name' => 'Vietnamese',
          'iso'     => 'vi'
    ),
);

global $likebtn_voting_effects;
$likebtn_voting_effects = array(
    'push',
    'grow',
    'buzz',
    'wobble',
    'bounce',
    'shake',
    'heartbeat',
);

// languages
global $likebtn_sync_intervals;
$likebtn_sync_intervals = array(
    5,
    15,
    30,
    60,
    90,
    120,
);

// LikeBtn website locales available
global $likebtn_website_locales;
$likebtn_website_locales = array(
    'en', 'ru'
);

// Form options
global $likebtn_settings_options;
$likebtn_settings_options = array(
    'likebtn_account_email' => '',
    'likebtn_account_api_key' => '',
    'likebtn_sync_inerval' => '', // > 0 - sync enabled
    'likebtn_site_id' => '',
    //'likebtn_cf' => '',
    'likebtn_css' => '',
    'likebtn_js' => '',
    'likebtn_bbp_replies_sort' => '',
    'likebtn_bp_act_sort' => '',
    'likebtn_bp_filter' => '1', // displayed on Buttons tab for each post type
    'likebtn_ipvi' => '',
    'likebtn_init_l_from' => '',
    'likebtn_init_l_to' => '',
    'likebtn_gdpr' => '1',
    'likebtn_notify_enabled' => '',
    'likebtn_notify_to' => get_option('admin_email'),
    'likebtn_notify_from' => likebtn_default_notify_from(),
    'likebtn_notify_subject' => '♥ '.__('New {vote_type} on {domain}', 'likebtn-like-button'),
    'likebtn_notify_text' => likebtn_default_notify_text(),
    "likebtn_info_message" => '1',
);
// Form entity options
global $likebtn_buttons_options;
$likebtn_buttons_options = array(
    'likebtn_show' => '1',
    'likebtn_use_settings_from' => '',
    'likebtn_post_format' => array('all'),
    'likebtn_exclude_sections' => array(),
    'likebtn_exclude_categories' => array(),
    'likebtn_allow_forums' => array(),
    'likebtn_allow_ids' => '',
    'likebtn_exclude_ids' => '',
    'likebtn_voter_by' => '',
    'likebtn_user_logged_in' => '',
    'likebtn_user_logged_in_alert' => '',
    'likebtn_like_box' => '',
    'likebtn_like_box_size' => LIKEBTN_LIKE_BOX_SIZE,
    'likebtn_like_box_type' => LIKEBTN_VOTE_LIKE,
    'likebtn_like_box_text' => __('Users who liked this:', 'likebtn-like-button'),
    'likebtn_position' => LIKEBTN_POSITION_BOTTOM,
    'likebtn_alignment' => LIKEBTN_ALIGNMENT_LEFT,
    'likebtn_html_before' => '',
    'likebtn_html_after' => '',
    'likebtn_newline' => '0',
    'likebtn_wrap' => '1',
    'likebtn_theme_type' => '',
    'likebtn_icon_l_type' => '',
    'likebtn_icon_d_type' => '',
    'likebtn_bp_notify' => '1',
    'likebtn_bp_activity' => '0',
    'likebtn_bp_hide_sitewide' => '0',
    'likebtn_bp_image' => '0',
    'likebtn_bp_snippet_tpl' => '',
    'likebtn_voting_author' => '0',
    'likebtn_voting_period' => '',
    'likebtn_voting_date' => '',
    'likebtn_voting_created' => '',
    'likebtn_voting_one' => '0',
    'likebtn_og' => '0',
);
// Options which can be set in shortcode
global $likebtn_buttons_options_shortcode;
$likebtn_buttons_options_shortcode = array(
    'likebtn_voter_by',
    'likebtn_user_logged_in',
    'likebtn_user_logged_in_alert',
    'likebtn_like_box',
    'likebtn_like_box_size',
    'likebtn_like_box_type',
    'likebtn_like_box_text',
    'likebtn_position',
    'likebtn_alignment',
    'likebtn_html_before',
    'likebtn_html_after',
    'likebtn_newline',
    'likebtn_wrap',
    'likebtn_bp_notify',
    'likebtn_bp_activity',
    'likebtn_bp_hide_sitewide',
    'likebtn_bp_image',
    //'likebtn_bp_snippet_tpl',
    'likebtn_voting_author',
    'likebtn_voting_period',
    'likebtn_voting_date',
    'likebtn_voting_created',
    'likebtn_voting_one',
    'likebtn_og',
);
// Internal settings
global $likebtn_internal_options;
$likebtn_internal_options = array(
    'likebtn_plan' => LIKEBTN_PLAN_TRIAL,
    'likebtn_plan_expires_in' => 0,
    'likebtn_plan_expires_on' => '',
    'likebtn_last_sync_time' => 0,
    'likebtn_last_successfull_sync_time' => 0,
    'likebtn_last_locale_sync_time' => 0,
    'likebtn_last_style_sync_time' => 0,
    'likebtn_last_plan_sync_time' => 0,
    'likebtn_last_plan_successfull_sync_time' => 0,
    'likebtn_last_sync_result' => false,
    'likebtn_last_sync_message' => '',
    'likebtn_plugin_v' => '',
    'likebtn_installation_timestamp' => '',
    'likebtn_notice_plan' => 0, // 1 = upgrade, -1 = downgrade
    'likebtn_account_data_hash' => '',
    'likebtn_admin_notices' => array(),
    'likebtn_db_version' => LIKEBTN_DB_VERSION,
    'likebtn_locales' => array(),
    'likebtn_styles' => array(),
    // Correct account data or not
    'likebtn_acc_data_correct' => 0,
    'likebtn_review' => 0, // 1 = display, -1 = dismissed 
    'likebtn_feedback_sent' => 0, 
    'likebtn_ipvi_hash' => '', 
    'likebtn_init_l_from_prev' => '',
    'likebtn_init_l_to_prev' => '',
    'likebtn_cf_ranges' => array(),
    'likebtn_cf_ranges_ipv6' => array(),
    'likebtn_cf_sync_time' => 0,
    'likebtn_cf_ipv6_sync_time' => 0,
);

// Internal settings
global $likebtn_entities_config;
$likebtn_entities_config = array(
    'theme' => array(
        LIKEBTN_ENTITY_PRODUCT => array('value'=>'github'),
        LIKEBTN_ENTITY_COMMENT => array('value'=>'transparent'),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('value'=>'transparent'),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('value'=>'transparent'),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('value'=>'transparent'),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('value'=>'transparent'),
        LIKEBTN_ENTITY_BP_MEMBER => array('value'=>'github'),
        LIKEBTN_ENTITY_BBP_USER => array('value'=>'github'),
        LIKEBTN_ENTITY_UM_USER => array('value'=>'youtube'),
    ),
    'popup_position' => array(
        LIKEBTN_ENTITY_PRODUCT => array('value'=>'top'),
        LIKEBTN_ENTITY_BP_MEMBER => array('value'=>'bottom'),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('value'=>'left'),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('value'=>'left'),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('value'=>'left'),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('value'=>'left')
    ),
    'likebtn_post_format' => array(
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('hide'=>true)
    ),
    'likebtn_exclude_sections' => array(
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('hide'=>true)
    ),
    'likebtn_exclude_categories' => array(
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('hide'=>true)
    ),
    'likebtn_allow_ids' => array(
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('hide'=>true)
    ),
    'likebtn_exclude_ids' => array(
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('hide'=>true)
    ),
    'likebtn_position' => array(
        //LIKEBTN_ENTITY_PRODUCT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BBP_USER => array('hide'=>true),
        LIKEBTN_ENTITY_UM_USER => array('hide'=>true),
        LIKEBTN_ENTITY_UM_USER_LIST => array('hide'=>true),
    ),
    'likebtn_alignment' => array(
        LIKEBTN_ENTITY_PRODUCT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BBP_USER => array('hide'=>true),
        LIKEBTN_ENTITY_UM_USER => array('hide'=>true),
        LIKEBTN_ENTITY_UM_USER_LIST => array('hide'=>true),
    ),
    'likebtn_og' => array(
        //LIKEBTN_ENTITY_PRODUCT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_MEMBER => array('hide'=>true),
        LIKEBTN_ENTITY_BBP_USER => array('hide'=>true),
        LIKEBTN_ENTITY_COMMENT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_POST => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('hide'=>true),
        LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC => array('hide'=>true)
    ),
    'bp_snippet_tpl' => array(
        LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE => array('value'=>'%content%'),
        LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT => array('value'=>'%content%'),
    ),
);

// AddThis services codes list
global $likebtn_icons;
$likebtn_icons = array(
    'hrt1',
    'hrt10',
    'hrt11',
    'hrt12',
    'hrt13',
    'hrt2',
    'hrt3',
    'hrt4',
    'hrt5',
    'hrt6',
    'hrt6-o',
    'hrt7',
    'hrt7-o',
    'hrt8',
    'hrt9',
    'thmb3-u',
    'thmb3-d',
    'thmb4-u',
    'thmb4-d',
    'thmb5-u',
    'thmb5-d',
    'thmb7-u',
    'thmb7-d',
    'thmb8-u',
    'thmb8-d',
    'thmb9-v',
    'thmb9-u',
    'thmb2-u',
    'thmb2-d',
    'thmb1',
    'thmb6',
    'alrt1',
    'alrt2',
    'arr1-d',
    'arr1-u',
    'arr10-d',
    'arr10-u',
    'arr11-d',
    'arr11-l',
    'arr11-r',
    'arr11-u',
    'arr12-d',
    'arr12-u',
    'arr13-d',
    'arr13-u',
    'arr14-d',
    'arr14-u',
    'arr15-d',
    'arr15-u',
    'arr16-d',
    'arr16-u',
    'arr17-d',
    'arr17-u',
    'arr18-d',
    'arr18-u',
    'arr19-d',
    'arr19-u',
    'arr2-d',
    'arr2-u',
    'arr3-d',
    'arr3-u',
    'arr4-d',
    'arr4-u',
    'arr5-d',
    'arr5-u',
    'arr6-d',
    'arr6-u',
    'arr7-d',
    'arr7-u',
    'arr8-d',
    'arr8-u',
    'arr9-d',
    'arr9-u',
    'blb1',
    'blb2',
    'bll',
    'bll-o',
    'bskt',
    'dmnd',
    'flg1',
    'flg2',
    'flg2-f',
    'flg2-o',
    'lck1-c',
    'lck1-o',
    'lck2-c',
    'lck2-o',
    'rnd',
    'rnd-0',
    'sgn1-s',
    'sgn1-t',
    'sgn10-c',
    'sgn10-t',
    'sgn11-i',
    'sgn11-q',
    'sgn12-m',
    'sgn12-p',
    'sgn2-m',
    'sgn2-p',
    'sgn3-c',
    'sgn3-m',
    'sgn3-p',
    'sgn3-t',
    'sgn4-c',
    'sgn4-t',
    'sgn5-t',
    'sgn6-m',
    'sgn6-p',
    'sgn7-c',
    'sgn7-m',
    'sgn7-p',
    'sgn7-t',
    'sgn8-c',
    'sgn8-m',
    'sgn8-p',
    'sgn8-t',
    'sgn9-f',
    'sky-sn',
    'sky-mn',
    'sml1',
    'sml2',
    'sml3-h',
    'sml3-n',
    'sml3-u',
    'stck',
    'str1',
    'str1-o',
    'str2',
    'str2-o',
    'trsh',
    'usr1',
    'usr1-o',
    'usr2-c',
    'usr2-p',
    'vlm1-d',
    'vlm1-u',
    'zap',
    'zm-in',
    'zm-out',
);

// Fonts
global $likebtn_fonts;
$likebtn_fonts = array(
    'Arial',
    'Arial Black',
    'Arial Narrow',
    'Brush Script MT',
    'Comic Sans MS',
    'Copperplate',
    'Courier New',
    'Georgia',
    'Impact',
    'Lucida Console',
    'Lucida Grande',
    'Lucida Sans Unicode',
    'Palatino',
    'Papyrus',
    'Tahoma',
    'Times New Roman',
    'Trebuchet MS',
    'Verdana',
);

// Font styles
global $likebtn_fstyles;
$likebtn_fstyles = array(
    'r'  => array('name' => __('Regular'), 'css' => '' ),
    'ri' => array('name' => __('Regular Italic'), 'css' => 'font-style: italic' ),
    'b'  => array('name' => __('Bold'), 'css' => 'font-weight: bold' ),
    'bi' => array('name' => __('Bold Italic'), 'css' => 'font-weight: bold; font-style: italic' )
);

// Default snippet template
define('LIKEBTN_BP_SNIPPET_TPL', '<table>
    <tr>
        <td>
            <img src="@image_thumbnail@" />
        </td>
        <td>
            <strong>@title@</strong><br/>
            @excerpt@
        <td>
    </tr>
</table>'
);

// AddThis services codes list
global $likebtn_addthis_service_codes;
$likebtn_addthis_service_codes = array(
    /*'facebook_like',
    'foursquare',
    'google_plusone',
    'pinterest',*/
    '100zakladok',
    '2tag',
    '2linkme',
    'a97abi',
    'addressbar',
    'adfty',
    'adifni',
    'advqr',
    'amazonwishlist',
    'amenme',
    'aim',
    'aolmail',
    'apsense',
    'arto',
    'azadegi',
    'baang',
    'baidu',
    'balltribe',
    'beat100',
    'biggerpockets',
    'bitly',
    'bizsugar',
    'bland',
    'blinklist',
    'blogger',
    'bloggy',
    'blogkeen',
    'blogmarks',
    'blurpalicious',
    'bobrdobr',
    'bonzobox',
    'socialbookmarkingnet',
    'bookmarkycz',
    'bookmerkende',
    'box',
    'brainify',
    'bryderi',
    'buddymarks',
    'buffer',
    'buzzzy',
    'camyoo',
    'care2',
    'foodlve',
    'chiq',
    'cirip',
    'citeulike',
    'classicalplace',
    'cleanprint',
    'cleansave',
    'cndig',
    'colivia',
    'technerd',
    'cosmiq',
    'cssbased',
    'curateus',
    'delicious',
    'digaculturanet',
    'digg',
    'diggita',
    'digo',
    'diigo',
    'domelhor',
    'dotnetshoutout',
    'douban',
    'draugiem',
    'dropjack',
    'dudu',
    'dzone',
    'efactor',
    'ekudos',
    'elefantapl',
    'email',
    'mailto',
    'embarkons',
    'evernote',
    'extraplay',
    'ezyspot',
    'stylishhome',
    'fabulously40',
    'facebook',
    'facebook_like',
    'foursquare',
    'informazione',
    'thefancy',
    'fark',
    'farkinda',
    'fashiolista',
    'favable',
    'faves',
    'favlogde',
    'favoritende',
    'favorites',
    'favoritus',
    'financialjuice',
    'flaker',
    'flipboard',
    'folkd',
    'formspring',
    'thefreedictionary',
    'fresqui',
    'friendfeed',
    'funp',
    'fwisp',
    'gamekicker',
    'gg',
    'giftery',
    'gigbasket',
    'givealink',
    'gmail',
    'govn',
    'goodnoows',
    'google',
    'googleplus',
    'googletranslate',
    'google_plusone',
    'google_plusone_share',
    'greaterdebater',
    'hackernews',
    'hatena',
    'gluvsnap',
    'hedgehogs',
    'historious',
    'hootsuite',
    'hotklix',
    'hotmail',
    'w3validator',
    'identica',
    'ihavegot',
    'indexor',
    'instapaper',
    'iorbix',
    'irepeater',
    'isociety',
    'iwiw',
    'jamespot',
    'jappy',
    'jolly',
    'jumptags',
    'kaboodle',
    'kaevur',
    'kaixin',
    'ketnooi',
    'kindleit',
    'kledy',
    'kommenting',
    'latafaneracat',
    'librerio',
    'lidar',
    'linkedin',
    'linksgutter',
    'linkshares',
    'linkuj',
    'livejournal',
    'lockerblogger',
    'logger24',
    'mymailru',
    'margarin',
    'markme',
    'mashant',
    'mashbord',
    'me2day',
    'meinvz',
    'mekusharim',
    'memonic',
    'memori',
    'mendeley',
    'meneame',
    'live',
    'misterwong',
    'misterwong_de',
    'mixi',
    'moemesto',
    'moikrug',
    'mrcnetworkit',
    'myspace',
    'myvidster',
    'n4g',
    'naszaklasa',
    'netlog',
    'netvibes',
    'netvouz',
    'newsmeback',
    'newstrust',
    'newsvine',
    'nujij',
    'odnoklassniki_ru',
    'oknotizie',
    'openthedoor',
    'oyyla',
    'packg',
    'pafnetde',
    'pdfonline',
    'pdfmyurl',
    'phonefavs',
    'pinterest',
    'pinterest_share',
    'planypus',
    'plaxo',
    'plurk',
    'pocket',
    'posteezy',
    'print',
    'printfriendly',
    'pusha',
    'qrfin',
    'qrsrc',
    'quantcast',
    'qzone',
    'reddit',
    'rediff',
    'redkum',
    'researchgate',
    'safelinking',
    'scoopat',
    'scoopit',
    'sekoman',
    'select2gether',
    'shaveh',
    'shetoldme',
    'sinaweibo',
    'skyrock',
    'smiru',
    'sodahead',
    'sonico',
    'spinsnap',
    'yiid',
    'springpad',
    'startaid',
    'startlap',
    'storyfollower',
    'studivz',
    'stuffpit',
    'stumbleupon',
    'stumpedia',
    'sulia',
    'sunlize',
    'supbro',
    'surfingbird',
    'svejo',
    'symbaloo',
    'taaza',
    'tagza',
    'tapiture',
    'taringa',
    'telegram',
    'textme',
    'thewebblend',
    'thinkfinity',
    'thisnext',
    'thrillon',
    'throwpile',
    'toly',
    'topsitelernet',
    'transferr',
    'tuenti',
    'tulinq',
    'tumblr',
    'tvinx',
    'twitter',
    'twitthis',
    'typepad',
    'upnews',
    'urlaubswerkde',
    'viadeo',
    'virb',
    'visitezmonsite',
    'vk',
    'vkrugudruzei',
    'voxopolis',
    'vybralisme',
    'wanelo',
    'internetarchive',
    'sharer',
    'webnews',
    'webshare',
    'werkenntwen',
    'whatsapp',
    'domaintoolswhois',
    'windows',
    'wirefan',
    'wishmindr',
    'wordpress',
    'wowbored',
    'raiseyourvoice',
    'wykop',
    'xanga',
    'xing',
    'yahoobkm',
    'yahoomail',
    'yammer',
    'yardbarker',
    'yigg',
    'yookos',
    'yoolink',
    'yorumcuyum',
    'youmob',
    'yuuby',
    'zakladoknet',
    'ziczac',
    'zingme',
    'more',
    'compact',
    'preferred_1',
    'preferred_2',
    'preferred_3',
    'preferred_4',
    'preferred_5',
    'preferred_6',
    'preferred_7',
    'preferred_8'
);

// Features
global $likebtn_features;
$likebtn_features = array(
    LIKEBTN_PLAN_FREE => array(
        'max_buttons' => 1,
        'statistics' => false,
        'synchronization' => false,
        'most_liked_widget' => false,
        'sorting' => false,
        'changing' => false,
        'graph' => false,
    ),
    LIKEBTN_PLAN_PLUS => array(
        'max_buttons' => 10,
        'statistics' => false,
        'synchronization' => false,
        'most_liked_widget' => false,
        'sorting' => false,
        'changing' => false,
        'graph' => false,
    ),
    LIKEBTN_PLAN_PRO => array(
        'max_buttons' => 25,
        'statistics' => true,
        'synchronization' => true,
        'most_liked_widget' => true,
        'sorting' => true,
        'changing' => false,
        'graph' => false,
    ),
    LIKEBTN_PLAN_VIP => array(
        'max_buttons' => 0,
        'statistics' => true,
        'synchronization' => true,
        'most_liked_widget' => true,
        'sorting' => true,
        'changing' => false,
        'graph' => false,
    ),
    LIKEBTN_PLAN_ULTRA => array(
        'max_buttons' => 0,
        'statistics' => true,
        'synchronization' => true,
        'most_liked_widget' => true,
        'sorting' => true,
        'changing' => true,
        'graph' => true,
    ),
    LIKEBTN_PLAN_TRIAL => array(
        'max_buttons' => 0,
        'statistics' => true,
        'synchronization' => true,
        'most_liked_widget' => true,
        'sorting' => true,
        'changing' => true,
        'graph' => true,
    )
);

// CloudFlare IP ranges
// https://www.cloudflare.com/ips/
global $likebtn_cf_ip_ranges;
$likebtn_cf_ip_ranges = array(
    '103.21.244.0/22',
    '103.22.200.0/22',
    '103.31.4.0/22',
    '104.16.0.0/12',
    '108.162.192.0/18',
    '131.0.72.0/22',
    '141.101.64.0/18',
    '162.158.0.0/15',
    '172.64.0.0/13',
    '173.245.48.0/20',
    '188.114.96.0/20',
    '190.93.240.0/20',
    '197.234.240.0/22',
    '198.41.128.0/17'
);

global $likebtn_cf_ip_ranges_ipv6;
$likebtn_cf_ip_ranges_ipv6 = array(
    '2400:cb00::/32',
    '2405:b500::/32',
    '2606:4700::/32',
    '2803:f800::/32',
    '2c0f:f248::/32',
    '2a06:98c0::/29'
);

global $user_logged_in_alert_default;
$user_logged_in_alert_default = __('You need to <a href="%url_login%">login</a> in order to vote', 'likebtn-like-button');

// Resources to check
global $likebtn_system_check;
$likebtn_system_check = array(
    'http://api.likebtn.com/api/',
    'http://wv.likebtn.com/w/v/1/1',
);

global $likebtn_countries;
$likebtn_countries = array(
    "BD" => "Bangladesh",
    "BE" => "Belgium",
    "BF" => "Burkina Faso",
    "BG" => "Bulgaria",
    "BA" => "Bosnia and Herzegovina",
    "BB" => "Barbados",
    "WF" => "Wallis and Futuna",
    "BL" => "Saint Barthelemy",
    "BM" => "Bermuda",
    "BN" => "Brunei",
    "BO" => "Bolivia",
    "BH" => "Bahrain",
    "BI" => "Burundi",
    "BJ" => "Benin",
    "BT" => "Bhutan",
    "JM" => "Jamaica",
    "BV" => "Bouvet Island",
    "BW" => "Botswana",
    "WS" => "Samoa",
    "BQ" => "Bonaire, Saint Eustatius and Saba",
    "BR" => "Brazil",
    "BS" => "Bahamas",
    "JE" => "Jersey",
    "BY" => "Belarus",
    "BZ" => "Belize",
    "RU" => "Russia",
    "RW" => "Rwanda",
    "RS" => "Serbia",
    "TL" => "East Timor",
    "RE" => "Reunion",
    "TM" => "Turkmenistan",
    "TJ" => "Tajikistan",
    "RO" => "Romania",
    "TK" => "Tokelau",
    "GW" => "Guinea-Bissau",
    "GU" => "Guam",
    "GT" => "Guatemala",
    "GS" => "South Georgia and the South Sandwich Islands",
    "GR" => "Greece",
    "GQ" => "Equatorial Guinea",
    "GP" => "Guadeloupe",
    "JP" => "Japan",
    "GY" => "Guyana",
    "GG" => "Guernsey",
    "GF" => "French Guiana",
    "GE" => "Georgia",
    "GD" => "Grenada",
    "GB" => "United Kingdom",
    "GA" => "Gabon",
    "SV" => "El Salvador",
    "GN" => "Guinea",
    "GM" => "Gambia",
    "GL" => "Greenland",
    "GI" => "Gibraltar",
    "GH" => "Ghana",
    "OM" => "Oman",
    "TN" => "Tunisia",
    "JO" => "Jordan",
    "HR" => "Croatia",
    "HT" => "Haiti",
    "HU" => "Hungary",
    "HK" => "Hong Kong",
    "HN" => "Honduras",
    "HM" => "Heard Island and McDonald Islands",
    "VE" => "Venezuela",
    "PR" => "Puerto Rico",
    "PS" => "Palestinian Territory",
    "PW" => "Palau",
    "PT" => "Portugal",
    "SJ" => "Svalbard and Jan Mayen",
    "PY" => "Paraguay",
    "IQ" => "Iraq",
    "PA" => "Panama",
    "PF" => "French Polynesia",
    "PG" => "Papua New Guinea",
    "PE" => "Peru",
    "PK" => "Pakistan",
    "PH" => "Philippines",
    "PN" => "Pitcairn",
    "PL" => "Poland",
    "PM" => "Saint Pierre and Miquelon",
    "ZM" => "Zambia",
    "EH" => "Western Sahara",
    "EE" => "Estonia",
    "EG" => "Egypt",
    "ZA" => "South Africa",
    "EC" => "Ecuador",
    "IT" => "Italy",
    "VN" => "Vietnam",
    "SB" => "Solomon Islands",
    "ET" => "Ethiopia",
    "SO" => "Somalia",
    "ZW" => "Zimbabwe",
    "SA" => "Saudi Arabia",
    "ES" => "Spain",
    "ER" => "Eritrea",
    "ME" => "Montenegro",
    "MD" => "Moldova",
    "MG" => "Madagascar",
    "MF" => "Saint Martin",
    "MA" => "Morocco",
    "MC" => "Monaco",
    "UZ" => "Uzbekistan",
    "MM" => "Myanmar",
    "ML" => "Mali",
    "MO" => "Macao",
    "MN" => "Mongolia",
    "MH" => "Marshall Islands",
    "MK" => "Macedonia",
    "MU" => "Mauritius",
    "MT" => "Malta",
    "MW" => "Malawi",
    "MV" => "Maldives",
    "MQ" => "Martinique",
    "MP" => "Northern Mariana Islands",
    "MS" => "Montserrat",
    "MR" => "Mauritania",
    "IM" => "Isle of Man",
    "UG" => "Uganda",
    "TZ" => "Tanzania",
    "MY" => "Malaysia",
    "MX" => "Mexico",
    "IL" => "Israel",
    "FR" => "France",
    "IO" => "British Indian Ocean Territory",
    "SH" => "Saint Helena",
    "FI" => "Finland",
    "FJ" => "Fiji",
    "FK" => "Falkland Islands",
    "FM" => "Micronesia",
    "FO" => "Faroe Islands",
    "NI" => "Nicaragua",
    "NL" => "Netherlands",
    "NO" => "Norway",
    "NA" => "Namibia",
    "VU" => "Vanuatu",
    "NC" => "New Caledonia",
    "NE" => "Niger",
    "NF" => "Norfolk Island",
    "NG" => "Nigeria",
    "NZ" => "New Zealand",
    "NP" => "Nepal",
    "NR" => "Nauru",
    "NU" => "Niue",
    "CK" => "Cook Islands",
    "XK" => "Kosovo",
    "CI" => "Ivory Coast",
    "CH" => "Switzerland",
    "CO" => "Colombia",
    "CN" => "China",
    "CM" => "Cameroon",
    "CL" => "Chile",
    "CC" => "Cocos Islands",
    "CA" => "Canada",
    "CG" => "Republic of the Congo",
    "CF" => "Central African Republic",
    "CD" => "Democratic Republic of the Congo",
    "CZ" => "Czech Republic",
    "CY" => "Cyprus",
    "CX" => "Christmas Island",
    "CR" => "Costa Rica",
    "CW" => "Curacao",
    "CV" => "Cape Verde",
    "CU" => "Cuba",
    "SZ" => "Swaziland",
    "SY" => "Syria",
    "SX" => "Sint Maarten",
    "KG" => "Kyrgyzstan",
    "KE" => "Kenya",
    "SS" => "South Sudan",
    "SR" => "Suriname",
    "KI" => "Kiribati",
    "KH" => "Cambodia",
    "KN" => "Saint Kitts and Nevis",
    "KM" => "Comoros",
    "ST" => "Sao Tome and Principe",
    "SK" => "Slovakia",
    "KR" => "South Korea",
    "SI" => "Slovenia",
    "KP" => "North Korea",
    "KW" => "Kuwait",
    "SN" => "Senegal",
    "SM" => "San Marino",
    "SL" => "Sierra Leone",
    "SC" => "Seychelles",
    "KZ" => "Kazakhstan",
    "KY" => "Cayman Islands",
    "SG" => "Singapore",
    "SE" => "Sweden",
    "SD" => "Sudan",
    "DO" => "Dominican Republic",
    "DM" => "Dominica",
    "DJ" => "Djibouti",
    "DK" => "Denmark",
    "VG" => "British Virgin Islands",
    "DE" => "Germany",
    "YE" => "Yemen",
    "DZ" => "Algeria",
    "US" => "United States",
    "UY" => "Uruguay",
    "YT" => "Mayotte",
    "UM" => "United States Minor Outlying Islands",
    "LB" => "Lebanon",
    "LC" => "Saint Lucia",
    "LA" => "Laos",
    "TV" => "Tuvalu",
    "TW" => "Taiwan",
    "TT" => "Trinidad and Tobago",
    "TR" => "Turkey",
    "LK" => "Sri Lanka",
    "LI" => "Liechtenstein",
    "LV" => "Latvia",
    "TO" => "Tonga",
    "LT" => "Lithuania",
    "LU" => "Luxembourg",
    "LR" => "Liberia",
    "LS" => "Lesotho",
    "TH" => "Thailand",
    "TF" => "French Southern Territories",
    "TG" => "Togo",
    "TD" => "Chad",
    "TC" => "Turks and Caicos Islands",
    "LY" => "Libya",
    "VA" => "Vatican",
    "VC" => "Saint Vincent and the Grenadines",
    "AE" => "United Arab Emirates",
    "AD" => "Andorra",
    "AG" => "Antigua and Barbuda",
    "AF" => "Afghanistan",
    "AI" => "Anguilla",
    "VI" => "U.S. Virgin Islands",
    "IS" => "Iceland",
    "IR" => "Iran",
    "AM" => "Armenia",
    "AL" => "Albania",
    "AO" => "Angola",
    "AQ" => "Antarctica",
    "AS" => "American Samoa",
    "AR" => "Argentina",
    "AU" => "Australia",
    "AT" => "Austria",
    "AW" => "Aruba",
    "IN" => "India",
    "AX" => "Aland Islands",
    "AZ" => "Azerbaijan",
    "IE" => "Ireland",
    "ID" => "Indonesia",
    "UA" => "Ukraine",
    "QA" => "Qatar",
    "MZ" => "Mozambique"
);

// initialization
function likebtn_init() {

    if (is_admin()) {
        likebtn_import_config();
    }
    likebtn_full_reset_run();
    _likebtn_plugin_on_load();

    load_plugin_textdomain('likebtn-like-button', false, dirname(plugin_basename(__FILE__)) . '/languages');

    if (is_admin()) {
        // Process forms
        _likebtn_bulk_actions();
        wp_enqueue_script('jquery');
    } else {
        _likebtn_og_init();
    }
}

add_action('init', 'likebtn_init', 11);

// add Settings link to the plugin list page
function likebtn_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="admin.php?page=likebtn_settings">' . __('Settings', 'likebtn-like-button') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'likebtn_links', 10, 2);

// admin options
function likebtn_admin_menu() {
    $logo_url = _likebtn_get_public_url() . 'img/menu_icon.png';

    add_menu_page(__('Like Buttons', 'likebtn-like-button'), __('Like Buttons', 'likebtn-like-button'), 'manage_options', 'likebtn_buttons', '', $logo_url);
    add_submenu_page(
            'likebtn_buttons', __('Buttons', 'likebtn-like-button') . ' ‹ ' . __('LikeBtn Like Button', 'likebtn-like-button'), __('Buttons', 'likebtn-like-button'), 'manage_options', 'likebtn_buttons', 'likebtn_admin_buttons'
    );
    //add_options_page('LikeBtn Like Button', __('LikeBtn Like Button', 'likebtn'), 'activate_plugins', 'likebtn', 'likebtn_admin_content');
    add_submenu_page(
            'likebtn_buttons', __('Settings', 'likebtn-like-button') . ' ‹ ' . __('LikeBtn Like Button', 'likebtn-like-button'), __('Settings', 'likebtn-like-button'), 'manage_options', 'likebtn_settings', 'likebtn_admin_settings'
    );
    add_submenu_page(
            'likebtn_buttons', __('Statistics', 'likebtn-like-button') . ' ‹ LikeBtn Like Button', __('Statistics', 'likebtn-like-button'), 'manage_options', 'likebtn_statistics', 'likebtn_admin_statistics'
    );
    add_submenu_page(
            'likebtn_buttons', __('Votes', 'likebtn-like-button') . ' ‹ LikeBtn Like Button', __('Votes', 'likebtn-like-button'), 'manage_options', 'likebtn_votes', 'likebtn_admin_votes'
    );
    add_submenu_page(
            'likebtn_buttons', __('Reports', 'likebtn-like-button') . ' ‹ LikeBtn Like Button', __('Reports', 'likebtn-like-button'), 'manage_options', 'likebtn_reports', 'likebtn_admin_reports'
    );
    add_submenu_page(
            'likebtn_buttons', __('Widgets', 'likebtn-like-button') . ' ‹ LikeBtn Like Button', __('Widgets', 'likebtn-like-button'), 'manage_options', 'likebtn_widget', 'likebtn_admin_widget'
    );
    add_submenu_page(
            'likebtn_buttons', __('Help') . ' ‹ LikeBtn Like Button', __('Help'), 'manage_options', 'likebtn_help', 'likebtn_admin_help'
    );
}

add_action('admin_menu', 'likebtn_admin_menu');

function likebtn_admin_footer_js()
{
    //update_option('likebtn_feedback_sent', 0);
    ?>
        <script type="text/javascript">
            var likebtn_msg_site_url = "<?php echo likebtn_escape_msg(get_site_url()); ?>";
            var likebtn_msg_account_email = "<?php echo likebtn_escape_msg(get_option('likebtn_account_email')) ?>";
            var likebtn_msg_admin_email = "<?php echo likebtn_escape_msg(get_option('admin_email')) ?>";
            var likebtn_msg_account_site_id = "<?php echo likebtn_escape_msg(get_option('likebtn_site_id')) ?>";
            var likebtn_msg_plan = "<?php echo (int)get_option('likebtn_plan') ?>";
            var likebtn_msg_version = "<?php echo LIKEBTN_VERSION ?>";
            var likebtn_msg_pub_url = "<?php echo _likebtn_get_public_url(); ?>";
            var likebtn_msg_feedback_sent = <?php echo (int)get_option('likebtn_feedback_sent'); ?>;
            var likebtn_msg_locale = "<?php echo get_locale(); ?>";

            var likebtn_msg_website = "<?php echo __('https://likebtn.com/en/', 'likebtn-like-button') ?>";
            var likebtn_msg_d_title = "<?php echo likebtn_esctr_msg(__('Like Button Plugin Deactivation', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_intro = "<?php echo likebtn_esctr_msg(__('We are very sorry that our Like Button plugin did not satisfy your needs. Could you please tell us why you\'ve decided to deactivate the plugin, maybe we still can help. We are not leaving even one client unsatisfied!', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_features = "<?php echo likebtn_esctr_msg(__('I\'m missing some feature(s) in the plugin', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_ph = "<?php echo likebtn_esctr_msg(__('Please list here those features which you are missing in the plugin and we will implement them ASAP', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_notify = "<?php echo likebtn_esctr_msg(__('Notify me by email when features are ready', 'likebtn-like-button')) ?>";
            var likebtn_msg_ye = "<?php echo likebtn_esctr_msg(__('Your email', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_pricing = "<?php echo likebtn_esctr_msg(__('It\'s too expensive — I\'m looking for a free/cheaper plugin', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_pricing_i = "<?php echo likebtn_esctr_msg(__('We approach each client individually — don\'t hesitate and <nobr><a href=\"javascript:likebtnContactUs();likebtn_poll_cntr.dialog(\'close\');void(0);\">drop us a message</a></nobr> and we will provide you with an individual pricing solution.', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_int = "<?php echo likebtn_esctr_msg(__('I could not integrate Like Button into my website', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_i1 = "<?php echo likebtn_esctr_msg(__('Just contact <a href=\"javascript:likebtnContactUs();likebtn_poll_cntr.dialog(\'close\');void(0);\">our support</a> and we will do our best to help you or check our', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_i2 = "<?php echo likebtn_esctr_msg(__('developers directory', 'likebtn-like-button')) ?>";
            var likebtn_msg_other = "<?php echo likebtn_esctr_msg(__('Other reason', 'likebtn-like-button')) ?>";
            var likebtn_msg_other_ph = "<?php echo likebtn_esctr_msg(__('Please tell us why Like Button plugin did not satisfy your needs and we will try to do everything possible to make you happy. Be sure!', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_cancel = "<?php echo likebtn_esctr_msg(__('Cancel', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_close = "<?php echo likebtn_esctr_msg(__('Close', 'likebtn-like-button')) ?>";
            var likebtn_msg_coupon = "LIKE9F452C";
            var likebtn_msg_f_deact_anyway = "<?php echo likebtn_esctr_msg(__('Deactivate plugin anyway', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_submit1 = "<?php echo likebtn_esctr_msg(__('Submit and Deactivate Plugin', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_submit2 = "<?php echo likebtn_esctr_msg(__('Submit and KEEP plugin active', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_tmp = "<?php echo likebtn_esctr_msg(__('Deactivating for time being', 'likebtn-like-button')) ?>";
            var likebtn_msg_f_offer1 = "<?php echo likebtn_esctr_msg(strtr(__('Here is your personal <strong style="font-size:20px;">%discount%% OFF</strong> coupon. Enter the code on %a_start%Upgrade%a_end% page and click Apply!', 'likebtn-like-button'), array('%discount%'=>65, '%a_start%'=>'<a href="'.__("https://likebtn.com/en/customer.php/upgrade/", 'likebtn-like-button')."?site_id=".get_option('likebtn_site_id')."&engine=wordpress&add_website=".$_SERVER['SERVER_NAME'].'" target="_blank">', '%a_end%'=>'</a>'))) ?>";
            var likebtn_msg_f_offer2 = "<?php echo __("Enjoy!", 'likebtn-like-button') ?>";
        </script>
    <?php
}
add_action('admin_footer', 'likebtn_admin_footer_js');

function likebtn_escape_msg($msg)
{
    return str_replace('"', '\"', strip_tags($msg));
}

function likebtn_esctr_msg($msg)
{
    return preg_replace('/([^\\\\])"/', '$1\"', $msg);
}

// Metabox for comments
function _likebtn_comment_meta_box()
{
    global $comment;

    $html = _likebtn_get_markup(LIKEBTN_ENTITY_COMMENT, $comment->comment_ID, array(), get_option('likebtn_use_settings_from_' . LIKEBTN_ENTITY_COMMENT), true, true, true);
    echo $html;
}

// plugin header
function likebtn_admin_head() {

    // Enqueue only on plugin's pages
    if ((!empty($_GET['page']) && (preg_match("/^likebtn_/", $_GET['page']) || preg_match("/^mycred-hooks/", $_GET['page'])))
        || (strstr($_SERVER['PHP_SELF'], 'plugins.php'))
    ) {
        wp_enqueue_style('likebtn-admin', _likebtn_get_public_url() . 'css/admin.css', array(), LIKEBTN_VERSION, 'all');
        wp_enqueue_style('likebtn-tipsy', _likebtn_get_public_url() . 'css/jquery/tipsy.css', array(), LIKEBTN_VERSION, 'all');

        wp_enqueue_script('likebtn-admin', _likebtn_get_public_url() . 'js/admin.js', array(), LIKEBTN_VERSION);
        wp_enqueue_script('likebtn-tipsy', _likebtn_get_public_url() .  'js/jquery/jquery.tipsy.js', array(), LIKEBTN_VERSION);
    }
}

add_action('admin_head', 'likebtn_admin_head');

// admin header
function likebtn_admin_header() {
    $logo_url = _likebtn_get_public_url() . 'img/logo.png';
    $header = <<<HEADER
    <div class="wrap" id="likebtn">
HEADER;

    $header .= '
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                
                <div id="postbox-container-1" class="postbox-container">
                    <div class="meta-box-sortables ui-sortable">
                        <div class="postbox likebtn_logo">
                            <div class="inside likebtn_sidebar_inside">
                                <a href="https://likebtn.com/en/wordpress-like-button-plugin" target="_blank" title="LikeBtn.com"><img alt="" src="'.$logo_url.'" /></a>
                                <input type="submit" id="likebtn_contact" value="' . __('Contact Us', 'likebtn-like-button') . '" class="button-primary" onclick="likebtnContactUs()">
                            </div>
                        </div>
                        '.(get_option('likebtn_acc_data_correct') == '1' ?
                            '<div class="postbox">
                                <h3 class="hndle ui-sortable-handle"><span>' . __('Plan & Features', 'likebtn-like-button') . '</span></h3>
                                ' . _likebtn_sidebar_plan() . '
                            </div>'
                            : '').
                        '<div class="postbox">
                            <h3 class="hndle ui-sortable-handle"><span>' . __('Synchronization', 'likebtn-like-button') . '</span></h3>
                            <div class="inside likebtn_sidebar_inside">
                                ' . _likebtn_sidebar_synchronization() . '
                            </div>
                        </div>' .
                        '<div class="postbox">
                            <h3 class="hndle ui-sortable-handle"><span>' . __('GDPR Compliance', 'likebtn-like-button') . '</span></h3>
                            <div class="inside likebtn_sidebar_inside">
                                <center><a href="'.admin_url() . 'admin.php?page=likebtn_settings#gdpr" style="display:block"><img src="'._likebtn_get_public_url().'img/gdpr_ready.jpg" style="max-width:45%"/></a></center>
                            </div>
                        </div>' .
                        ($_GET['page'] == 'likebtn_buttons' ? 
                        '<div class="postbox">
                            <h3 class="hndle ui-sortable-handle"><span>' . __('Referral Program', 'likebtn-like-button') . '</span></h3>
                            <div class="inside likebtn_sidebar_inside">
                                ' . _likebtn_sidebar_rp() . '
                            </div>
                        </div>' : '') .
                    '</div>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <h1 class="nav-tab-wrapper">
                        <a class="nav-tab ' . ($_GET['page'] == 'likebtn_buttons' ? 'nav-tab-active' : '') . '" href="' . admin_url() . 'admin.php?page=likebtn_buttons">' . __('Buttons', 'likebtn-like-button') . '</a>
                        <a class="nav-tab ' . ($_GET['page'] == 'likebtn_settings' ? 'nav-tab-active' : '') . '" href="' . admin_url() . 'admin.php?page=likebtn_settings">' . __('Settings', 'likebtn-like-button') . '</a>
                        <a class="nav-tab ' . ($_GET['page'] == 'likebtn_statistics' ? 'nav-tab-active' : '') . '" href="' . admin_url() . 'admin.php?page=likebtn_statistics">' . __('Statistics', 'likebtn-like-button') . '</a>
                        <a class="nav-tab ' . ($_GET['page'] == 'likebtn_votes' ? 'nav-tab-active' : '') . '" href="' . admin_url() . 'admin.php?page=likebtn_votes">' . __('Votes', 'likebtn-like-button') . '</a>
                        <a class="nav-tab ' . ($_GET['page'] == 'likebtn_reports' ? 'nav-tab-active' : '') . '" href="' . admin_url() . 'admin.php?page=likebtn_reports">' . __('Reports', 'likebtn-like-button') . '</a>
                        <a class="nav-tab ' . ($_GET['page'] == 'likebtn_widget' ? 'nav-tab-active' : '') . '" href="' . admin_url() . 'admin.php?page=likebtn_widget">' . __('Widgets', 'likebtn-like-button') . '</a>
                        <a class="nav-tab ' . ($_GET['page'] == 'likebtn_help' ? 'nav-tab-active' : '') . '" href="' . admin_url() . 'admin.php?page=likebtn_help">' . __('Help') . '</a>';

    $header .= '</h1>';
    $header .= '<div id="likebtn_content">';

    if (get_option('likebtn_acc_data_correct') != '1' && $_GET['page'] != 'likebtn_settings' && $_GET['page'] != 'likebtn_help') {
        $header .= '<div class="likebtn_overlay"></div>';
    }

    echo $header;
}

// admin side panel and footer
function _likebtn_admin_footer()
{
    ?>
                        </div>
                    </div>
                </div>
                <br class="clear"/>
            </div>

        </div>
        <?php if (get_option('likebtn_js')): ?>
            <!-- LikeBtn.com Custom JS BEGIN -->
            <script type="text/javascript">
                <?php echo get_option('likebtn_js'); ?>
            </script>
            <!-- LikeBtn.com Custom JS END -->
        <?php endif ?>
        <?php if (get_option('likebtn_css')): ?>
            <!-- LikeBtn.com Custom CSS BEGIN -->
            <style type="text/css">
                <?php echo get_option('likebtn_css'); ?>
            </style>
            <!-- LikeBtn.com Custom CSS END -->
        <?php endif ?>
    <?php
}

// sidebar plan
function _likebtn_sidebar_plan()
{
    $plan_synced = false;

    if (get_option('likebtn_last_plan_successfull_sync_time')) {
        $plan_synced = true;
    }

    $likebtn_plan = get_option('likebtn_plan');
    
    $html = '
    <div class="inside likebtn_sidebar_inside">
        <div class="likebtn_sidebar_section">
            <div id="likebtn_plan_wr">
                '._likebtn_plan_html();

    if ($plan_synced && $likebtn_plan == LIKEBTN_PLAN_TRIAL) {
        $html .= ' <a href="javascript:goFree(\''.__('If you want to finish your TRIAL now please type «FREE» to switch to the FREE plan', 'likebtn-like-button').'\', \''.__('Error occured, please try again later.', 'likebtn-like-button').'\', \''.__('Plan data refreshed', 'likebtn-like-button').'\');void(0);">'.__('Switch to FREE now', 'likebtn-like-button').'</a>';
    }

    $html .= '
            </div>
            <div id="likebtn_refresh_msg_wr" style="display:none">
                <div class="likebtn_sidebar_div_simple"></div>
                <small class="likebtn_error" id="likebtn_refresh_error" style="display:none"></small>
            </div>
        </div>';
    
    if ($plan_synced && $likebtn_plan == LIKEBTN_PLAN_TRIAL) {
        $html .= '
            <div class="likebtn_sidebar_div"></div>
            <div id="likebtn_trial_info" class="likebtn_sidebar_section">
                <a href="javascript:jQuery(\'#likebtn_trial_help\').toggle();void(0);">'.__('What is TRIAL?', 'likebtn-like-button').'</a>
                <div id="likebtn_trial_help" style="display:none"><br/>'.strtr(__('During the TRIAL period you can enjoy ULTRA features for 7 days. After TRIAL expires your website is switched to the %a_begin%FREE%a_end% plan (all the votes will be kept!) and you can continue using FREE plan or go Premium.', 'likebtn-like-button'), array('%a_begin%'=>'<a href="javascript:likebtnPopup(\''.__('https://likebtn.com/en/customer.php/upgrade/', 'likebtn-like-button').'?site_id='.get_option('likebtn_site_id').'&engine=wordpress&add_website='.$_SERVER['SERVER_NAME'].'\');void(0);">', '%a_end%'=>'</a>')).'</div>
            </div>';
    }

    if ($plan_synced) {
        $features = _likebtn_get_features();

        $likebtn_alert = '';
        if (/*(*/!_likebtn_is_stat_enabled() /*|| get_option('likebtn_last_sync_message'))*/ && $features['statistics']) {
            $likebtn_alert = ' <i class="likebtn_ttip likebtn_alert" data-likebtn_ttip_gr="e" title="'.__('Configure Synchronization in order to use this feature', 'likebtn-like-button').'"></i>';
        }

        $html .= '
        <div class="likebtn_sidebar_div"></div>
        <div class="likebtn_sidebar_section">
            '.__('Max buttons per page', 'likebtn-like-button').': <strong><nobr>'.($features['max_buttons'] ? $features['max_buttons'] : __('Unlimited', 'likebtn-like-button')).'</nobr></strong>
        </div>
        <div class="likebtn_sidebar_div"></div>
        <div class="likebtn_sidebar_section">
            <ul class="likebtn_features">
                <li class="likebtn_avail"><span class="likebtn_ttip" title="FREE">'.__('Reports', 'likebtn-like-button').'</span></li>
                <li class="likebtn_avail"><span class="likebtn_ttip" title="FREE">'.__('Like box', 'likebtn-like-button').'</span></li>
                <li class="likebtn_avail"><span class="likebtn_ttip" title="FREE">'.__('Google Rich Snippets', 'likebtn-like-button').'</span> <small><a href="'.__('https://likebtn.com/en/faq#rich_snippets', 'likebtn-like-button').'" target="_blank">'.__('what is it?', 'likebtn-like-button').'</a></small></li>
                <li class="'.($features['statistics'] ? 'likebtn_avail' : 'likebtn_unavail').'"><span class="likebtn_ttip" title="PRO / VIP / ULTRA">'.__('Statistics', 'likebtn-like-button').'</span>'.$likebtn_alert.'</li>
                <li class="'.($features['synchronization'] ? 'likebtn_avail' : 'likebtn_unavail').'"><span class="likebtn_ttip" title="PRO / VIP / ULTRA">'.__('Synchronization', 'likebtn-like-button').'</span> <small><a href="'.admin_url().'admin.php?page=likebtn_settings#synchronization" target="_blank">'.__('what is it?', 'likebtn-like-button').'</a></small></li>
                <li class="'.($features['most_liked_widget'] ? 'likebtn_avail' : 'likebtn_unavail').'"><span class="likebtn_ttip" title="PRO / VIP / ULTRA">'.__('Most liked content widget', 'likebtn-like-button').'</span>'.$likebtn_alert.'</li>
                <li class="'.($features['sorting'] ? 'likebtn_avail' : 'likebtn_unavail').'"><span class="likebtn_ttip" title="PRO / VIP / ULTRA">'.__('Sorting content by likes', 'likebtn-like-button').'</span>'.$likebtn_alert.'</li>
                <li class="'.($features['changing'] ? 'likebtn_avail' : 'likebtn_unavail').'"><span class="likebtn_ttip" title="ULTRA">'.__('Change number of likes by admin', 'likebtn-like-button').'</span>'.$likebtn_alert.'</li>
                <li class="'.($features['graph'] ? 'likebtn_avail' : 'likebtn_unavail').'"><span class="likebtn_ttip" title="ULTRA">'.__('Votes graph', 'likebtn-like-button').'</span>'.$likebtn_alert.'</li>
            </ul>
        </div>
        ';
        $html .= '';
    }

    $html .= '
    </div>
    ';

    $html .= '
    <div class="likebtn_upgrade_container">';

    //if ($plan_synced && $likebtn_plan != LIKEBTN_PLAN_ULTRA) {
        $html .= '<input class="button-secondary likebtn_button_upgrade" type="button" value="'.__('Upgrade', 'likebtn-like-button').'" onclick="likebtnPopup(\''.__('https://likebtn.com/en/customer.php/upgrade/', 'likebtn-like-button').'?site_id='.get_option('likebtn_site_id').'&engine=wordpress&add_website='.$_SERVER['SERVER_NAME'].'\')" /> &nbsp;';
    //}
    if ($plan_synced && $likebtn_plan != LIKEBTN_PLAN_FREE && $likebtn_plan != LIKEBTN_PLAN_TRIAL) {
        $html .= '<small><a href="javascript:likebtnPopup(\''.__('https://likebtn.com/en/customer.php/upgrade/', 'likebtn-like-button').'?site_id='.get_option('likebtn_site_id').'&prolong=1&engine=wordpress&add_website='.$_SERVER['SERVER_NAME'].'\');void(0);">'.__('Renew Plan', 'likebtn-like-button').'</a></small>';
    } else {
        $html .= '<small><a href="javascript:likebtnPopup(\''.__('https://likebtn.com/en/customer.php/upgrade/', 'likebtn-like-button').'?site_id='.get_option('likebtn_site_id').'&engine=wordpress&add_website='.$_SERVER['SERVER_NAME'].'\');void(0);">'.__('Plans & Pricing', 'likebtn-like-button').'</a></small>';
    }
    $html .= '</div>';

    return $html;
}

// sidebar syncing
function _likebtn_sidebar_synchronization()
{
    $enabled = false;
    $sync_result_html = '';

    if (_likebtn_is_stat_enabled()) {
        $enabled = true;
        $status = __('Enabled', 'likebtn-like-button');
        $status_class = 'likebtn_success';

    } else {
        $status = __('Disabled', 'likebtn-like-button');
        $status_class = 'likebtn_error';
    }

    if (!$sync_result_html) {
        $html = '
            <div class="likebtn_sidebar_section">
                '.__('Status', 'likebtn-like-button').': <strong class="'.$status_class.'">'.$status.'</strong>
        ';
    } else {
        // Show error in status
        $html = '
            <div class="likebtn_sidebar_section">
                '.__('Status', 'likebtn-like-button').': <strong class="likebtn_error">'.$sync_result_html.'</strong>
        ';
    }
    if (!$enabled) {
        $html .= ' <a href="'.admin_url().'admin.php?page=likebtn_settings#synchronization">'.__('Edit', 'likebtn-like-button').'</a>';
    }
    
    $html .= '</div>';

    return $html;
}

// sidebar social
function _likebtn_sidebar_social()
{
    $html =<<<HTML
<div class="likebtn_social">
    <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FLikeBtn.LikeButton&amp;width&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=192115980991078" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px; width:110px;" allowTransparency="true"></iframe>
</div>
<div class="likebtn_social">
    <a href="https://twitter.com/likebtn" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false" data-width="144px"></a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
</div>
HTML;

    return $html;
}

// sidebar Referral Program
function _likebtn_sidebar_rp()
{
    $public_url = _likebtn_get_public_url();
    $title = __('Earn Money With LikeBtn!', 'likebtn-like-button');
    $href = "javascript:likebtnPopup('".__('https://likebtn.com/en/', 'likebtn-like-button')."referral-program');void(0)";

    $html =<<<HTML
<center><a href="{$href}" class="likebtn_ttip" title="{$title}" style="display:block"><img src="{$public_url}img/rp.png" style="max-width:60%"/></a></center>
HTML;

    return $html;
}

// Get plan html
function _likebtn_plan_html() {
    global $likebtn_plans;
    $plan_synced = false;
    $expires_html = '';
    $expires_on = '';

    if (get_option('likebtn_last_plan_successfull_sync_time')) {
        $plan_synced = true;
    }

    $likebtn_plan = get_option('likebtn_plan');

    $refresh_html = ' <img src="'._likebtn_get_public_url().'img/refresh.gif" class="likebtn_refresh likebtn_ttip" onclick="refreshPlan(\''.__('Error occured, please try again later.', 'likebtn-like-button').__('Disable WP HTTP Compression plugin if you have it enabled.', 'likebtn-like-button').'\', \''.__('Plan data refreshed', 'likebtn-like-button').'\')" title="'.__('Sync Plan', 'likebtn-like-button').'" id="likebtn_refresh_trgr"/>
            <img src="'._likebtn_get_public_url().'img/refresh_loader.gif" class="likebtn_refresh likebtn_refresh_loader likebtn_ttip" style="display:none" title="'.__('Please wait...', 'likebtn-like-button').'" id="likebtn_refresh_ldr"/> 
            <small class="likebtn_success" id="likebtn_refresh_success" style="display:none"></small>';

    if (isset($likebtn_plans[$likebtn_plan]) && $plan_synced) {
        // <a href="javascript: likebtnPopup(\''.__('http://likebtn.com/en/', 'likebtn-like-button').'?add_website='.$_SERVER['SERVER_NAME'].'#plans_pricing\'); void(0)" class="likebtn_ttip" title="'.__('Plans & Pricing', 'likebtn-like-button').'"><strong>'.$likebtn_plans[$likebtn_plan].'</strong></a>
        $plan_html = '<strong>'.$likebtn_plans[$likebtn_plan].'</strong>'.$refresh_html;

        if ($likebtn_plan != LIKEBTN_PLAN_FREE) {
            if ((int)get_option('likebtn_last_plan_successfull_sync_time') >= time() - 86000) {
                $expires_in_option = (int)get_option('likebtn_plan_expires_in');
                $expires_in = '';
                if ($expires_in_option) {
                    $expires_in = ceil($expires_in_option / 86400);
                }
                if ($expires_in) {
                    $expires_on = get_option('likebtn_plan_expires_on');
                    if ($expires_on) {
                        $expires_on = date_i18n(get_option('date_format'), strtotime($expires_on));
                    }
                    $expires_html .= '
                        <div class="likebtn_sidebar_div_simple"></div>
                        '.__('Expires in', 'likebtn-like-button').': <strong class="likebtn_ttip" title="'.$expires_on.'">'.$expires_in.' '.__('day(s)', 'likebtn-like-button').'</strong>';
                }
            } else {
                $expires_html .= '
                    <div class="likebtn_sidebar_div_simple"></div>
                    '.__('Expires in', 'likebtn-like-button').': 
                    <strong class="likebtn_error">'.__('Unknown', 'likebtn-like-button').'</strong>
                    <i class="likebtn_help_simple" title="'.__('Enter your LikeBtn.com account data on Settings tab', 'likebtn-like-button').'"></i> 
                    <a href="'.admin_url().'admin.php?page=likebtn_settings">'.__('Edit', 'likebtn-like-button').'</a>
                ';
            }
        }
    } else {
        $plan_html = '
            <strong class="likebtn_error">'.
                __('Unknown', 'likebtn-like-button').'
            </strong>';
        if (get_option('likebtn_acc_data_correct') == '1') {
            $plan_html .= $refresh_html;
        } else {
            $plan_html = '
                <i class="likebtn_help_simple" title="'.__('Enter your LikeBtn.com account data on Settings tab', 'likebtn-like-button').'"></i> 
                <a href="'.admin_url().'admin.php?page=likebtn_settings">'.__('Edit', 'likebtn-like-button').'</a>';
        }
    }

    $html = __('Plan', 'likebtn-like-button').': '.$plan_html.
        $expires_html;

    return $html;
}

// Check if statistics has been enabled on Statistics tab
function _likebtn_is_stat_enabled() {
    if (
        get_option('likebtn_sync_inerval') && get_option('likebtn_acc_data_correct') == '1' 
        && get_option('likebtn_plan') >= LIKEBTN_PLAN_PRO
    ) {
        return true;
    } else {
        return false;
    }
}

// Get features availability
function _likebtn_get_features() {
    global $likebtn_features;
    $plan = get_option('likebtn_plan');

    if (isset($likebtn_features[$plan])) {
        return $likebtn_features[$plan];
    } else {
        $likebtn_features[LIKEBTN_PLAN_TRIAL];
    }
}

// updated - success (green)
// update-nag - warning (yellow)
// error - error (red)
// http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
function _likebtn_notice($msg, $class = 'updated')
{
    ?>
    <div class="<?php echo $class; ?> notice likebtn_notice">
        <p><?php echo $msg; ?></p>
    </div>
    <?php
}

// Display notice on plan downgrade
function likebtn_account_notice()
{
    if (get_option('likebtn_acc_data_correct') != '1') {
        $msg = strtr(
           '<strong>'.__(LIKEBTN_PLUGIN_TITLE, 'likebtn-like-button').'</strong>: '.__('Please enter your LikeBtn.com account data on <a href="%url_sync%">Settings</a> tab.', 'likebtn-like-button'), 
            array('%url_sync%'=>admin_url().'admin.php?page=likebtn_settings')
        );
        _likebtn_notice($msg, 'update-nag');
    }
}

add_action('admin_notices', 'likebtn_account_notice');

// Display notice on plan downgrade
function likebtn_plan_notice() {
    global $likebtn_plans;

    if (get_option('likebtn_notice_plan')) {
        $msg = '<strong>'.__(LIKEBTN_PLUGIN_TITLE, 'likebtn-like-button').'</strong>: '.__("Your plan has been switched to", 'likebtn-like-button').' <strong>'.$likebtn_plans[get_option('likebtn_plan')].'</strong>';
        $class = '';
        
        if (get_option('likebtn_notice_plan') == 1) {
            // Upgrade
            $class = 'updated';
        } else {
            // Downgrade
            $class = 'updated';
        }
        _likebtn_notice($msg, $class);
        update_option('likebtn_notice_plan', false);
    }
}

add_action('admin_notices', 'likebtn_plan_notice');

// Add notice
function _likebtn_add_notice($notice) {
    $notices = get_option('likebtn_admin_notices');
    if (!is_array($notices)) {
        $notices = array();
    }
    $notices[] = $notice;
    update_option('likebtn_admin_notices', $notices);
}

// Display notices
function likebtn_admin_notices()
{
    $likebtn_admin_notices = get_option('likebtn_admin_notices');

    if (is_array($likebtn_admin_notices) && count($likebtn_admin_notices)) {
        foreach ($likebtn_admin_notices as $notice) {

            // Determine class
            $class = 'updated';
            if (!empty($notice['class'])) {
                $class = $notice['class'];
            }

            _likebtn_notice($notice['msg'], $class);    
        }
        
        update_option('likebtn_admin_notices', array());
    }
}

add_action('admin_notices', 'likebtn_admin_notices');

// uninstall function called from uninstall.php
function likebtn_uninstall() {
    global $wpdb;

    if (is_multisite()) {

        global $blog_id;
   
        $blogs = get_sites();

        // Place current blog on the first place
        foreach ($blogs as $blog) {
            if ($blog->blog_id != 1) {
                $prefix = $wpdb->prefix.$blog->blog_id."_";
            } else {
                $prefix = $wpdb->prefix;
            }

            likebtn_reset_config($prefix);
        }
    } else {
        likebtn_reset_config($wpdb->prefix);
    }

    // Tables
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . LIKEBTN_TABLE_ITEM );
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . LIKEBTN_TABLE_VOTE );
}

function likebtn_reset_config($prefix)
{
    global $wpdb;
    $query_del = "
        DELETE FROM ".$prefix."options
        WHERE option_name like 'likebtn_%'
    ";
    $wpdb->query($query_del);
}

// activation hook
function likebtn_activation_hook()
{
    // Install DB
    _likebtn_db_install();

    // Add options
    _likebtn_add_options();

    set_transient('_likebtn_activation_redirect', true, 60);
}

// Add options
function _likebtn_add_options()
{
    global $likebtn_settings_options;
    global $likebtn_internal_options;

    $likebtn_entities = _likebtn_get_entities();

    // set default values for options
    foreach ($likebtn_settings_options as $option_name=>$option_value) {
        add_option($option_name, $option_value);
    }

    // set default options for entities
    foreach ($likebtn_entities as $entity_name => $entity_title) {
        _likebtn_add_default_options($entity_name);
    }

    // set default values for options
    foreach ($likebtn_internal_options as $option_name=>$option_value) {
        add_option($option_name, $option_value);
    }

    // For showing review link
    if (!get_option('likebtn_installation_timestamp')) {
        add_option('likebtn_installation_timestamp', time());
    }
}

// The latest version of DB
function _likebtn_db_install() {
    global $wpdb;

    $table_item = $wpdb->prefix . LIKEBTN_TABLE_ITEM;
    $table_vote = $wpdb->prefix . LIKEBTN_TABLE_VOTE;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql = "
    CREATE TABLE IF NOT EXISTS {$table_item} (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `identifier` text NOT NULL,
        `identifier_hash` varchar(32) NOT NULL,
        `url` text,
        `title` text,
        `description` text,
        `image` text,
        `likes` int(11) NOT NULL DEFAULT '0',
        `dislikes` int(11) NOT NULL DEFAULT '0',
        `likes_minus_dislikes` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`ID`),
        UNIQUE KEY `identifier_hash` (`identifier_hash`),
        KEY `title` (`title`(1)),
        KEY `likes` (`likes`),
        KEY `dislikes` (`dislikes`),
        KEY `likes_minus_dislikes` (`likes_minus_dislikes`),
        KEY `identifier` (`identifier`(1))
    );";

    dbDelta($sql);

    $sql = "
    CREATE TABLE IF NOT EXISTS {$table_vote} (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `identifier` text NOT NULL,
        `identifier_hash` varchar(32) NOT NULL,
        `client_identifier` varchar(32) NOT NULL,
        `type` tinyint(1) NOT NULL,
        `user_id` bigint(20) DEFAULT 0,
        `ip` varchar(40) NOT NULL,
        `country` varchar(2) DEFAULT NULL,
        `lat` float(10, 6) NULL,
        `lng` float(10, 6) NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`ID`),
        KEY `identifiers` (`identifier_hash`, `client_identifier`, `user_id`),
        KEY `identifier` (`identifier`(7)),
        KEY `created_at` (`created_at`),
        KEY `user_id` (`user_id`),
        KEY `ip` (`ip`)
    );";

    dbDelta($sql);
}

// Plugin loaded
function _likebtn_plugin_on_load()
{
    _likebtn_db_update();
    _likebtn_update_options();

    // Run sunchronization
    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();

    // Sync plan
    if (is_admin()) {
        $likebtn->runSyncPlan();
    }

    $likebtn->runSyncVotes();
}

// Update DB
function _likebtn_db_update() 
{
    require_once(dirname(__FILE__) . '/likebtn_like_button_db_update.php');

    $db_version = (int)get_option('likebtn_db_version');

    if (!$db_version) {
        // Backward compatibility
        $db_version = (int)get_option('likebtn_like_button_db_version');
    }
    //$db_version = 5;
    $db_version++;
    while (function_exists('likebtn_db_update_'.$db_version)) {
        call_user_func('likebtn_db_update_'.$db_version);
        update_option("likebtn_db_version", $db_version);
        $db_version++;
    }
}

// update options
function _likebtn_update_options() {
    $likebtn_entities = _likebtn_get_entities(false, false, false);
    // Update plugin before updating options
    if (function_exists('_maybe_update_plugins')) {
        _maybe_update_plugins();
    }

    foreach ($likebtn_entities as $entity_name => $entity_title) {
        // Set default settings for new entity
        // User lang parameter to check if option exists
        if (!get_option('likebtn_settings_lang_' . $entity_name)) {
            _likebtn_add_default_options($entity_name);
        }
    }
}

// set default options for post type
function _likebtn_add_default_options($entity_name) {
    global $likebtn_settings;
    global $likebtn_buttons_options;
    global $likebtn_entities_config;

    foreach ($likebtn_buttons_options as $option_name=>$option_value) {

        switch ($option_name) {
            case 'likebtn_show':
                if (in_array($entity_name, array(LIKEBTN_ENTITY_COMMENT, LIKEBTN_ENTITY_ATTACHMENT, LIKEBTN_ENTITY_ATTACHMENT_LIST)) ) {
                    $option_value = '0';
                }
                break;
            case 'likebtn_use_settings_from':
                // Set copy settings for list entities
                if (_likebtn_has_list_flag($entity_name)) {
                    $option_value = _likebtn_cut_list_flag($entity_name);
                }
                break;
            case 'likebtn_position':
                if ($entity_name == LIKEBTN_ENTITY_PRODUCT || $entity_name == LIKEBTN_ENTITY_PRODUCT_LIST) {
                    $option_value = LIKEBTN_POSITION_TOP;
                }
                break;
        }

        add_option($option_name.'_'.$entity_name, $option_value);
    }

    // settings
    foreach ($likebtn_settings as $option_name => $option_info) {
        $value = $option_info['default'];
        if (!empty($likebtn_entities_config[$option_name][$entity_name]['value'])) {
            $value = $likebtn_entities_config[$option_name][$entity_name]['value'];
        }
        switch ($option_name) {
            case 'lang':
                $value = likebtn_cur_lang();
                break;
        }
        add_option('likebtn_settings_' . $option_name . '_' . $entity_name, $value);
    }
}

register_activation_hook(__FILE__, 'likebtn_activation_hook');

// admin init hook: registering settings
function likebtn_admin_init()
{
    global $likebtn_settings_options;
    global $likebtn_settings;
    global $likebtn_buttons_options;

    // No output here redirect to work

    // Synchronization
    foreach ($likebtn_settings_options as $option_name=>$option_value) {
        register_setting('likebtn_settings', $option_name);
    }

    // Registering all options
    $entity_name = _likebtn_get_subpage();

    foreach ($likebtn_buttons_options as $option_name=>$option_value) {
        register_setting('likebtn_buttons', $option_name.'_'.$entity_name);
    }
    // This is special - displayed on Buttons page
    register_setting('likebtn_buttons', 'likebtn_bp_filter');

    // settings
    foreach ($likebtn_settings as $option_name => $option_info) {
        register_setting('likebtn_buttons', 'likebtn_settings_' . $option_name.'_'.$entity_name);
    }
    
    // Redirect after activation
    if (get_transient( '_likebtn_activation_redirect' )) {
        delete_transient('_likebtn_activation_redirect');
        $redirect_page = 'likebtn_settings';
        if (get_option('likebtn_account_email')) {
            $redirect_page = 'likebtn_buttons';
        }
        wp_safe_redirect( add_query_arg( array( 'page' => $redirect_page ), admin_url('admin.php') ) );
        die;
    }

    // Check account data on settings update
    if (isset($_GET['page']) && $_GET['page'] == 'likebtn_settings' &&
        isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true'
    ) {
        update_option('likebtn_acc_data_correct', '0');
 
        // Sync plan
        require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
        $likebtn = new LikeBtnLikeButton();

        $test_response = $likebtn->checkAccount(get_option('likebtn_account_email'), get_option('likebtn_account_api_key'), get_option('likebtn_site_id'));

        // Set credentials status
        // "result" determines credentials check result
        if ($test_response['connect_result'] == 'success') {
            if ($test_response['result'] == 'success') {
                update_option('likebtn_acc_data_correct', '1');
                // Enable sync
                update_option('likebtn_sync_inerval', 5);
            }
        }
    }

    // Poll
    $script = basename($_SERVER['REQUEST_URI']);
    if (!$script) {
        $script = basename($_SERVER['SCRIPT_NAME']);
    }
    if (!$script) {
        $script = basename($_SERVER['SCRIPT_FILENAME']);
    }
    $script = preg_replace("/\?.*/", '', $script);

    if (is_admin() && $script == 'plugins.php' && !get_option('likebtn_feedback_sent')) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style("wp-jquery-ui-dialog");
    }
}

add_action('admin_init', 'likebtn_admin_init');

// admin vote statistics
function likebtn_admin_statistics() {
    global $likebtn_page_sizes;
    global $wpdb;
    global $blog_id;

    list($statistics, $likebtn_entities, $likebtn_post_statuses, $entity_name, $sort_by, $sort_by_order, $page_size, $post_id, $post_title, $post_status, $p, $blogs, $statistics_blog_id) = likebtn_stats();

    $total_found = 0;
    if (isset($statistics[0])) {
        $query_found_rows = "SELECT FOUND_ROWS() as found_rows";
        $found_rows = $wpdb->get_results($query_found_rows);

        $total_found = (int) $found_rows[0]->found_rows;

        $p->items($total_found);
        $p->calculate(); // Calculates what to show
        $p->parameterName('paging');
        $p->adjacents(1); // No. of page away from the current page
    }

    likebtn_admin_header();
    ?>

    <script type="text/javascript">
        var likebtn_spinner_src = '<?php echo _likebtn_get_public_url()."img/spinner.gif" ?>';

        var likebtn_msg_select_items = '<?php _e("Please select item(s)", 'likebtn-like-button'); ?>';
        var likebtn_msg_upgrade_vip = '<?php echo strtr(__("Upgrade your website at least to %plan% to be able to use the feature", 'likebtn-like-button'), array('%plan%'=>'VIP')); ?>';
        var likebtn_msg_upgrade_pro = '<?php echo strtr(__("Upgrade your website at least to %plan% to be able to use the feature", 'likebtn-like-button'), array('%plan%'=>'PRO')); ?>';
        var likebtn_msg_export = '<?php _e("Export to CSV", 'likebtn-like-button'); ?>';
    </script>
    <div>

        <?php if (!_likebtn_is_stat_enabled() /*|| (get_option('likebtn_last_sync_result') == 'error' && get_option('likebtn_last_sync_message'))*/): ?>
            <div class="error">
                <br/>
                <?php 
                    echo strtr(
                        __('Statistics not available. Enable synchronization in order to view statistics:', 'likebtn-like-button'), 
                        array('%url_sync%'=>admin_url().'admin.php?page=likebtn_settings#synchronization')
                    );
                ?>
            
                <?php echo _likebtn_enable_stats_msg(); ?>
            </div>
        <?php else: ?>
            <p class="description">
                ● <?php _e('Keep in mind that items appear in Statistics after receiving at least one vote.', 'likebtn-like-button'); ?><br/>
                ● <?php _e('Select <u>Custom item</u> content type to view votes of like buttons added using shortcode or HTML code.', 'likebtn-like-button'); ?><br/>
                ● <?php _e('To edit item votes click on a number of likes/dislikes in the statistics table.', 'likebtn-like-button'); ?>
            </p>
        <?php endif ?>
        <br/>
        <form action="" method="get" id="statistics_form">
            <input type="hidden" name="page" value="likebtn_statistics" />

            <?php if ($blogs): ?>
                <nobr><label><?php _e('Site', 'likebtn-like-button'); ?>:</label>
                <select name="likebtn_blog_id" >
                    <?php foreach ($blogs as $blog_id_value => $blog_title): ?>
                        <option value="<?php echo $blog_id_value; ?>" <?php selected($statistics_blog_id, $blog_id_value); ?> ><?php echo $blog_title; ?></option>
                    <?php endforeach ?>
                </select></nobr>
                &nbsp;&nbsp;
            <?php endif ?>

            <nobr><label><?php _e('Item Type', 'likebtn-like-button'); ?>:</label>
            <select name="likebtn_entity_name" >
                <?php foreach ($likebtn_entities as $entity_name_value => $entity_title): ?>
                    <option value="<?php echo $entity_name_value; ?>" <?php selected($entity_name, $entity_name_value); ?> ><?php _e($entity_title, 'likebtn-like-button'); ?></option>
                <?php endforeach ?>
            </select></nobr>

            &nbsp;&nbsp;
            <nobr><label><?php _e('Page Size', 'likebtn-like-button'); ?>:</label>
            <select name="likebtn_page_size" >
                <?php foreach ($likebtn_page_sizes as $page_size_value): ?>
                    <option value="<?php echo $page_size_value; ?>" <?php selected($page_size, $page_size_value); ?> ><?php echo $page_size_value ?></option>
                <?php endforeach ?>

            </select></nobr>
            <br/><br/>
            <div class="postbox statistics_filter_container">
                <?php /*<h3><?php _e('Filter', 'likebtn-like-button'); ?></h3>*/ ?>
                <div class="inside">
                    <label><?php _e('ID', 'likebtn-like-button'); ?>:</label>
                    <input type="text" name="likebtn_post_id" value="<?php echo htmlspecialchars($post_id) ?>" size="5" />
                    &nbsp;&nbsp;
                    <label><?php _e('Title'); ?>:</label>
                    <input type="text" name="likebtn_post_title" value="<?php echo htmlspecialchars($post_title) ?>" size="25"/>
                    &nbsp;&nbsp;
                    <label><?php _e('Status', 'likebtn-like-button'); ?>:</label>
                    <select name="likebtn_post_status" >
                        <option value=""></option>
                        <?php foreach ($likebtn_post_statuses as $post_status_value => $post_status_title): ?>
                            <option value="<?php echo $post_status_value; ?>" <?php selected($post_status, $post_status_value); ?> ><?php echo _e($post_status_title) ?></option>
                        <?php endforeach ?>
                    </select>

                    &nbsp;&nbsp;
                    <input class="button-secondary" type="button" name="reset" value="<?php _e('Reset filter', 'likebtn-like-button'); ?>" onClick="jQuery('.statistics_filter_container :input[type!=button]').val('');
                jQuery('#statistics_form').submit();"/>
                </div>
            </div>

            <input class="button-primary" type="submit" name="show" value="<?php _e('View', 'likebtn-like-button'); ?>" />
            &nbsp;
            <?php _e('Items Found', 'likebtn-like-button'); ?>: <strong><?php echo $total_found ?></strong>
        </form>
        <br/>
        <form method="post" action="" id="stats_actions_form">
            <input type="hidden" name="bulk_action" value="" id="stats_bulk_action" />
        <?php if (count($statistics) && $p->total_pages > 0): ?>
            <div class="tablenav">
                <input type="button" class="button-secondary likebtn_ttip" onclick="likebtnStatsBulkAction('reset', '<?php echo get_option('likebtn_plan') ?>', '<?php _e("The votes count can not be recovered after resetting. Are you sure you want to reset likes and dislikes for the selected item(s)?", 'likebtn-like-button'); ?>')" value="<?php _e('Reset', 'likebtn-like-button'); ?>" title="<?php _e('Set to zero number of likes and dislikes for selected items', 'likebtn-like-button'); ?>">
                
                <input type="button" class="button-secondary likebtn_ttip" onclick="likebtnStatsBulkAction('delete', '<?php echo get_option('likebtn_plan') ?>', '<?php _e("The votes count can not be recovered after deleting. Are you sure you want to delete selected item(s) from statistics?", 'likebtn-like-button'); ?>')" value="<?php _e('Delete', 'likebtn-like-button'); ?>" title="<?php _e('Delete selected items from statistics: no posts, pages, comments, etc will be deleted, just their votes will be deleted from statistics', 'likebtn-like-button'); ?>">

                <input type="button" class="button-secondary likebtn_ttip" onclick="likebtnStatsExport('<?php echo get_option('likebtn_plan') ?>')" value="<?php _e('Export to CSV', 'likebtn-like-button'); ?>" title="<?php _e('Export current statistics to CSV', 'likebtn-like-button'); ?>">

                <div class="tablenav-pages">
                    <?php echo $p->show(); ?>
                </div>
            </div>
        <?php endif ?>
        <table class="widefat" id="statistics_container">
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="statisticsItemsCheckbox(this)" value="all" style="margin:0"></th>
                    <?php if ($entity_name != LIKEBTN_ENTITY_CUSTOM_ITEM): ?>
                        <th class="<?php if ($sort_by == 'post_id'): ?>sorted <?php echo $sort_by_order; ?><?php else: ?>sortable asc<?php endif ?>">
                            <?php
                                if ($sort_by == 'post_id') {
                                    $build_sort_by_order = ($sort_by_order=='desc' ? 'asc' : 'desc');
                                } else {
                                    $build_sort_by_order = 'desc';
                                }
                            ?>
                            <a href="<?php echo likebtn_build_sort_by('post_id', $build_sort_by_order); ?>">
                                <span>ID</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    <?php endif ?>
                    <?php if ($entity_name == LIKEBTN_ENTITY_BP_MEMBER || $entity_name == LIKEBTN_ENTITY_BBP_USER || $entity_name == LIKEBTN_ENTITY_USER): ?>
                        <th><?php _e('Avatar', 'likebtn-like-button') ?></th>
                    <?php else: ?>
                        <th><?php _e('Featured image', 'likebtn-like-button') ?></th>
                    <?php endif ?>
                    <th width="100%" class="<?php if ($sort_by == 'post_title'): ?>sorted <?php echo $sort_by_order; ?> <?php else: ?>sortable desc<?php endif ?>">
                        <?php
                            if ($sort_by == 'post_title') {
                                $build_sort_by_order = ($sort_by_order=='desc' ? 'asc' : 'desc');
                            } else {
                                $build_sort_by_order = 'asc';
                            }
                        ?>
                        <a href="<?php echo likebtn_build_sort_by('post_title', $build_sort_by_order); ?>">
                            <span><?php _e('Item', 'likebtn-like-button') ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if ($blogs && $statistics_blog_id == 'all'): ?>
                        <th><?php _e('Site') ?></th>
                    <?php endif ?>
                    <th class="<?php if ($sort_by == 'likes'): ?>sorted <?php echo $sort_by_order; ?> <?php else: ?>sortable asc<?php endif ?>">
                        <?php
                            if ($sort_by == 'likes') {
                                $build_sort_by_order = ($sort_by_order=='desc' ? 'asc' : 'desc');
                            } else {
                                $build_sort_by_order = 'desc';
                            }
                        ?>
                        <a href="<?php echo likebtn_build_sort_by('likes', $build_sort_by_order); ?>">
                            <span><?php _e('Likes', 'likebtn-like-button') ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="<?php if ($sort_by == 'dislikes'): ?>sorted <?php echo $sort_by_order; ?> <?php else: ?>sortable asc<?php endif ?>">
                        <?php
                            if ($sort_by == 'dislikes') {
                                $build_sort_by_order = ($sort_by_order=='desc' ? 'asc' : 'desc');
                            } else {
                                $build_sort_by_order = 'desc';
                            }
                        ?>
                        <a href="<?php echo likebtn_build_sort_by('dislikes', $build_sort_by_order); ?>">
                            <span><?php _e('Dislikes', 'likebtn-like-button') ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="<?php if ($sort_by == 'likes_minus_dislikes'): ?>sorted <?php echo $sort_by_order; ?> <?php else: ?>sortable asc<?php endif ?>">
                        <?php
                            if ($sort_by == 'likes_minus_dislikes') {
                                $build_sort_by_order = ($sort_by_order=='desc' ? 'asc' : 'desc');
                            } else {
                                $build_sort_by_order = 'desc';
                            }
                        ?>
                        <a href="<?php echo likebtn_build_sort_by('likes_minus_dislikes', $build_sort_by_order); ?>">
                            <span><?php _e('Likes minus dislikes', 'likebtn-like-button') ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statistics as $statistics_item): ?>
                    <?php
                        // URL
                        if (count($blogs) && isset($statistics_item->blog_id)) {
                            $post_url = _likebtn_get_entity_url($entity_name, $statistics_item->post_id, $statistics_item->url, $statistics_item->blog_id);
                        } else {
                            $post_url = _likebtn_get_entity_url($entity_name, $statistics_item->post_id, $statistics_item->url);
                        }
                    
                        $statistics_item->post_title = _likebtn_prepare_title($entity_name, $statistics_item->post_title);

                        $url_votes = admin_url('admin.php').'?page=likebtn_votes&likebtn_entity_name='.$entity_name.'&likebtn_post_id='.$statistics_item->post_id.'&show=View';

                        $image = _likebtn_get_entity_image($entity_name, $statistics_item->post_id, array(32,32));

                        if ($statistics_item->post_title === '') {
                            $statistics_item->post_title = $statistics_item->post_id;
                        }
                    ?>

                    <tr id="item_<?php echo $statistics_item->post_id; ?>">
                        <td><input type="checkbox" class="item_checkbox likebtn_ttip" value="<?php echo $statistics_item->post_id; ?>" name="item[]" <?php if ($blogs && (int)$statistics_item->blog_id != 0 && $statistics_item->blog_id != $blog_id): ?>disabled="disabled" title="<?php _e('Please switch to the corresponding network site in order to reset votes or delete items from stats.', 'likebtn-like-button') ?>"<?php endif ?>></td>
                        <?php if ($entity_name != LIKEBTN_ENTITY_CUSTOM_ITEM): ?>
                            <td><?php echo $statistics_item->post_id; ?></td>
                        <?php endif ?>
                        <td>
                            <?php if ($image): ?>
                                <a href="<?php echo $post_url ?>" target="_blank"><img src="<?php echo $image; ?>" width="32" height="32" /></a>
                            <?php else: ?>
                                &nbsp;
                            <?php endif ?>
                        </td>
                        <td><a href="<?php echo $post_url ?>" target="_blank"><?php echo htmlspecialchars($statistics_item->post_title); ?></a></td>
                        <?php if ($blogs && $statistics_blog_id == 'all'): ?>
                            <td><?php echo get_blog_option($statistics_item->blog_id, 'blogname') ?></td>
                        <?php endif ?>
                        <td>
                            <?php if ($blogs && (int)$statistics_item->blog_id != 0 && $statistics_item->blog_id != $blog_id): ?>
                                <?php echo $statistics_item->likes; ?>
                            <?php else: ?>
                                <a href="javascript:statisticsEdit('<?php echo $entity_name ?>', '<?php echo $statistics_item->post_id; ?>', 'like', '<?php echo $statistics_item->likes; ?>', '<?php echo get_option('likebtn_plan'); ?>', '<?php _e('Enter new value:', 'likebtn-like-button') ?>', '<?php _e('Upgrade your website plan to the ULTRA plan to use the feature', 'likebtn-like-button') ?>', '<?php _e('Error occured. Please, try again later.', 'likebtn-like-button') ?>');void(0);" title="<?php _e('Click to change', 'likebtn-like-button') ?>" class="item_like likebtn_ttip"><?php echo $statistics_item->likes; ?></a>
                            <?php endif ?>
                        </td>
                        <td>
                            <?php if ($blogs && (int)$statistics_item->blog_id != 0 && $statistics_item->blog_id != $blog_id): ?>
                                <?php echo $statistics_item->dislikes; ?>
                            <?php else: ?>
                                <a href="javascript:statisticsEdit('<?php echo $entity_name ?>', '<?php echo $statistics_item->post_id; ?>', 'dislike', '<?php echo $statistics_item->dislikes; ?>', '<?php echo get_option('likebtn_plan'); ?>', '<?php _e('Enter new value:', 'likebtn-like-button') ?>', '<?php _e('Upgrade your website plan to the ULTRA plan to use the feature', 'likebtn-like-button') ?>', '<?php _e('Error occured. Please, try again later.', 'likebtn-like-button') ?>');void(0);" title="<?php _e('Click to change', 'likebtn-like-button') ?>" class="item_dislike likebtn_ttip"><?php echo $statistics_item->dislikes; ?></a>
                            <?php endif ?>
                        </td>
                        <td><?php echo $statistics_item->likes_minus_dislikes; ?></td>
                        <td><a href="<?php echo $url_votes; ?>" target="_blank" class="likebtn_ttip button button-secondary likebtn-action" title="<?php _e('View votes', 'likebtn-like-button') ?>"><img src="<?php echo _likebtn_get_public_url()?>img/actions/votes.png" /></a></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        </form>
        <?php if (count($statistics) && $p->total_pages > 0): ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php echo $p->show(); ?>
                </div>
            </div>
        <?php endif ?>

    </div>
    <div id="likebtn_export" class="likebtn_export hidden">
        <form action="<?php echo admin_url('admin-ajax.php') ?>?action=likebtn_export&<?php echo $_SERVER['QUERY_STRING'] ?>" method="post" target="_blank">
            <input type="hidden" name="export" value="1" />
            <strong><?php _e('Data to export', 'likebtn-like-button'); ?>:</strong><br/>
            <label><input type="checkbox" name="fields[]" value="id" checked="checked" /> <?php _e('ID', 'likebtn-like-button'); ?></label><br/>
            <label><input type="checkbox" name="fields[]" value="title" checked="checked" /> <?php _e('Title', 'likebtn-like-button'); ?></label><br/>
            <label><input type="checkbox" name="fields[]" value="url" checked="checked" /> <?php _e('URL', 'likebtn-like-button'); ?></label><br/>
            <label><input type="checkbox" name="fields[]" value="likes" checked="checked" /> <?php _e('Likes', 'likebtn-like-button'); ?></label><br/>
            <label><input type="checkbox" name="fields[]" value="dislikes" checked="checked" /> <?php _e('Dislikes', 'likebtn-like-button'); ?></label><br/>
            <label><input type="checkbox" name="fields[]" value="likes_minus_dislikes" /> <?php _e('Likes minus dislikes', 'likebtn-like-button'); ?></label><br/>
            <label><input type="checkbox" name="fields[]" value="ips" /> <?php _e("Voters IPs", 'likebtn-like-button'); ?></label><br/>
            <br/>
            <strong><?php _e('Encoding', 'likebtn-like-button'); ?>:</strong> 
            <select name="encoding">
                <option value="UCS-2LE">UTF-16LE (UCS-2LE) - <?php _e('Recommended'); ?></option>
                <option value="UTF-8">UTF-8</option>
                <option value="Windows-1251">ANSI (Windows-1251)</option>
                <option value="Windows-1252">ANSI (Windows-1252)</option>
            </select>
            <br/><br/>
            <strong><?php _e('Field Separator', 'likebtn-like-button'); ?>:</strong> 
            <select name="separator">
                <option value="TAB">Tab (\t) - <?php _e('Recommended'); ?></option>
                <option value=",">Comma (,)</option>
                <option value=";">Semicolon (;)</option>
                <option value="|">Pipe (|)</option>
                <option value="&">Ampersand (&)</option>
            </select>
            <br/><br/>
            <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
                <div class="ui-dialog-buttonset">
                    <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-primary likebtn-button-close" role="button"><span class="ui-button-text"><?php _e('Export', 'likebtn-like-button'); ?></span></button>
                    <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-secondary likebtn-button-close" role="button"><span class="ui-button-text"><?php _e('Close', 'likebtn-like-button'); ?></span></button>
                </div>
            </div>
        </form>
    </div>

    <?php

    _likebtn_admin_footer();
}

// Process stats
function likebtn_stats()
{
    //global $likebtn_nonpost_entities;
    global $likebtn_post_statuses;
    global $wpdb;
    global $blog_id;

    $export_mode = false;

    if (isset($_POST['action']) && $_POST['action'] == 'export') {
        $export_mode = true;
    }

    if (!$export_mode) {
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style("wp-jquery-ui-dialog");
    }

    $query_parameters = array();

    $likebtn_entities = _likebtn_get_entities(true, false, false);

    // Custom item
    $likebtn_entities[LIKEBTN_ENTITY_CUSTOM_ITEM] = __('Custom Item');

    // get parameters
    $entity_name = _likebtn_statistics_entity();

    // Multisite - available for super admin only
    $blogs = array();
    $blog_list = array();
    $statistics_blog_id = '';
    $prefix_prepared = $wpdb->prefix;
    if (is_multisite() && is_super_admin()) {

        $blog_list = $wpdb->get_results("
            SELECT blog_id, domain
            FROM {$wpdb->blogs}
            WHERE site_id = '{$wpdb->siteid}'
            AND spam = '0'
            AND deleted = '0'
            AND archived = '0'
            ORDER BY blog_id
        ");

        // Place current blog on the first place
        foreach ($blog_list as $blog) {
            if ($blog->blog_id == $blog_id) {
                $blogs["{$blog->blog_id}"] = get_blog_option($blog->blog_id, 'blogname') . ' - ' . $blog->domain;
                break;
            }
        }

        foreach ($blog_list as $blog) {
            if ($blog->blog_id != $blog_id) {
                $blogs["{$blog->blog_id}"] = get_blog_option($blog->blog_id, 'blogname') . ' - ' . $blog->domain;
            }
        }

        // Add all
        $blogs['all'] = __('All Sites');

        // Determine selected blog id
        if (isset($_GET['likebtn_blog_id'])) {
            if ($_GET['likebtn_blog_id'] == 'all') {
                $statistics_blog_id = 'all';
            } else {
                // Check if blog with ID exists
                foreach ($blog_list as $blog) {
                    if ($blog->blog_id == (int)$_GET['likebtn_blog_id']) {
                        $statistics_blog_id = (int)$_GET['likebtn_blog_id'];
                        break;
                    }
                }
            }
        } else {
            if (isset($blog_list[0])) {
                $statistics_blog_id = $blog_id;
            } else {
               // 
            }
        }

        // Prepare prefix if this is not main blog
        if ($blog_id != 1) {
            $prefix_prepared = substr($wpdb->prefix, 0, strlen($wpdb->prefix)-strlen($blog_id)-1);
        }
    }

    // add comment statuses
    $likebtn_post_statuses['0'] = __('Comment not approved', 'likebtn-like-button');
    $likebtn_post_statuses['1'] = __('Comment approved', 'likebtn-like-button');

    $sort_by = '';
    if (isset($_GET['likebtn_sort_by'])) {
        $sort_by = sanitize_text_field($_GET['likebtn_sort_by']);
        if (!in_array($sort_by, array('dislikes', 'likes_minus_dislikes', 'post_id', 'post_title', 'likes'))) {
            $sort_by = '';
        }
    }
    if (!$sort_by) {
        $sort_by = 'post_id';
    } elseif ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM && $sort_by == 'post_id') {
        $sort_by = 'likes';
    }

    $sort_by_order = '';
    if (isset($_GET['likebtn_sort_by_order'])) {
        $sort_by_order = sanitize_text_field($_GET['likebtn_sort_by_order']);
        if (!in_array($sort_by_order, array('asc', 'desc'))) {
            $sort_by_order = '';
        }
    }
    if (!$sort_by_order) {
        $sort_by_order = 'desc';
    }

    $page_size = LIKEBTN_STATISTIC_PAGE_SIZE;
    if (isset($_GET['likebtn_page_size'])) {
        $page_size = (int)$_GET['likebtn_page_size'];
    }

    $post_id = '';
    if (isset($_GET['likebtn_post_id'])) {
        $post_id = (int)$_GET['likebtn_post_id'];
        if (!$post_id) {
            $post_id = '';
        }
    }

    $post_title = '';
    if (isset($_GET['likebtn_post_title'])) {
        // likebtn_post_title must be able to contain any text
        $post_title = sanitize_text_field($_GET['likebtn_post_title']);
        $post_title = stripcslashes($post_title);
    }
    $post_status = '';
    if (isset($_GET['likebtn_post_status'])) {
        $post_status = sanitize_text_field($_GET['likebtn_post_status']);
        if (!array_key_exists($post_status, $likebtn_post_statuses)) {
            $post_status = '';
        }
    }

    // pagination
    require_once(dirname(__FILE__) . '/includes/likebtn_like_button_pagination.class.php');

    $pagination_target = "admin.php?page=likebtn_statistics";
    foreach ($_GET as $get_parameter => $get_value) {
        $pagination_target .= '&' . urlencode($get_parameter) . '=' . urlencode(stripcslashes($get_value));
    }

    $p = new LikeBtnLikeButtonPagination();
    $p->limit($page_size); // Limit entries per page
    $p->target($pagination_target);
    //$p->currentPage(); // Gets and validates the current page
    $p->prevLabel(__('Previous', 'likebtn-like-button'));
    $p->nextLabel(__('Next', 'likebtn-like-button'));

    if (!isset($_GET['paging'])) {
        $p->page = 1;
    } else {
        $p->page = (int)$_GET['paging'];
    }

    // query for limit paging
    if (!$export_mode) {
        $query_limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;
    } else {
        $query_limit = '';
    }

    // query parameters
    $query_where = '';

    // Post type
    switch ($entity_name) {
        case LIKEBTN_ENTITY_COMMENT:
        case LIKEBTN_ENTITY_CUSTOM_ITEM:
        case LIKEBTN_ENTITY_BP_ACTIVITY_POST:
        case LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE:
        case LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT:
        case LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC:
        case LIKEBTN_ENTITY_BP_MEMBER:
        case LIKEBTN_ENTITY_BBP_POST:
        case LIKEBTN_ENTITY_BBP_USER:
        case LIKEBTN_ENTITY_UM_USER:
            break;

        default:
            $query_where .= ' AND p.post_type = %s ';
            $query_parameters[] = $entity_name;
            break;
    }

    // Post ID
    if ($post_id) {
        switch ($entity_name) {
            case LIKEBTN_ENTITY_BP_ACTIVITY_POST:
            case LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE:
            case LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT:
            case LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC:
            case LIKEBTN_ENTITY_BP_MEMBER:
                $query_where .= ' AND p.id = %d ';
                break;
            case LIKEBTN_ENTITY_COMMENT:
                $query_where .= ' AND p.comment_ID = %d ';
                break;
            default:
                $query_where .= ' AND p.ID = %d ';
                break;
        }
        $query_parameters[] = $post_id;
    }

    if ($post_title) {
        switch ($entity_name) {
            case LIKEBTN_ENTITY_BP_ACTIVITY_POST:
            case LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE:
            case LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT:
            case LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC:
                $query_where .= ' AND LOWER(CONCAT(p.action, p.content)) LIKE "%%%s%%" ';
                break;
            case LIKEBTN_ENTITY_BP_MEMBER:
                $query_where .= ' AND LOWER(p.value) LIKE "%%%s%%" ';
                break;
            case LIKEBTN_ENTITY_BBP_USER:
                $query_where .= ' AND LOWER(CONCAT(p.user_login, p.display_name)) LIKE "%%%s%%" ';
                break;          
            case LIKEBTN_ENTITY_COMMENT:
                $query_where .= ' AND LOWER(p.comment_content) LIKE "%%%s%%" ';
                break;
            case LIKEBTN_ENTITY_CUSTOM_ITEM:
                $query_where .= ' AND LOWER(p.identifier) LIKE "%%%s%%" ';
                break;
            default:
                $query_where .= ' AND LOWER(p.post_title) LIKE "%%%s%%" ';
                break;
        }
        $query_parameters[] = strtolower($post_title);
    }
    if ($post_status !== '') {
        if ($entity_name == LIKEBTN_ENTITY_COMMENT) {
            $query_where .= ' AND p.comment_approved = %s ';
        } elseif ($entity_name != LIKEBTN_ENTITY_CUSTOM_ITEM) {
            $query_where .= ' AND p.post_status = %s ';
        }
        $query_parameters[] = $post_status;
    }

    // order by
    switch ($sort_by) {
        case 'dislikes':
            $query_orderby = 'ORDER BY ABS(dislikes) ';
            break;
        case 'likes_minus_dislikes':
            $query_orderby = 'ORDER BY ABS(likes_minus_dislikes)*SIGN(likes_minus_dislikes) ';
            break;
        case 'post_id':
            $query_orderby = 'ORDER BY post_id ';
            break;
        case 'post_title':
            $query_orderby = 'ORDER BY post_title ';
            break;
        case 'likes':
        default:
            $query_orderby = 'ORDER BY ABS(likes) ';
            $sort_by = 'likes';
            break;
    }
    $query_orderby .= " {$sort_by_order} ";

    // For Multisite
    $query = '';
    if ($statistics_blog_id && $statistics_blog_id != 1 && $statistics_blog_id != 'all') {
        $prefix = "{$prefix_prepared}{$statistics_blog_id}_";
        $query = _likebtn_get_statistics_sql($entity_name, $prefix, $query_where, $query_orderby, $query_limit, $statistics_blog_id . ' as blog_id, ');
        $query_prepared = $wpdb->prepare($query, $query_parameters);
    } else if ($statistics_blog_id == 'all') {
        foreach ($blog_list as $blog) {
            if ($blog->blog_id == 1) {
                $prefix = $prefix_prepared;
            } else {
                $prefix = "{$prefix_prepared}{$blog->blog_id}_";
            }
            $query_list[] = $wpdb->prepare(_likebtn_get_statistics_sql($entity_name, $prefix, $query_where, '', '', $blog->blog_id . ' as blog_id, '), $query_parameters);
        }
        $query_prepared = ' SELECT SQL_CALC_FOUND_ROWS * from (' . implode(' UNION ', $query_list) . ") query {$query_orderby} {$query_limit} ";
    } else {
        if ($statistics_blog_id) {
            $query = _likebtn_get_statistics_sql($entity_name, $prefix_prepared, $query_where, $query_orderby, $query_limit, $statistics_blog_id . ' as blog_id, ');
        } else {
            $query = _likebtn_get_statistics_sql($entity_name, $prefix_prepared, $query_where, $query_orderby, $query_limit);
        }
        if (count($query_parameters)) {
            $query_prepared = $wpdb->prepare($query, $query_parameters);
        } else {
            $query_prepared = $query;
        }
    }

    $statistics = $wpdb->get_results($query_prepared);

    return array($statistics, $likebtn_entities, $likebtn_post_statuses, $entity_name, $sort_by, $sort_by_order, $page_size, $post_id, $post_title, $post_status, $p, $blogs, $statistics_blog_id);
}

// admin vote statistics
function likebtn_admin_reports() {
    global $wpdb;

    // Get coordinates
    $created_at = date("Y-m-d H:i:s", strtotime('-2 weeks'));
    $coordinates = [];

    $loader_src = _likebtn_get_public_url() . 'img/ajax_loader_white.gif';

    wp_enqueue_script('likebtn-graph', 'https://likebtn.com/js/graph.js', array(), LIKEBTN_VERSION);

    likebtn_admin_header();
    ?>

    <script type="text/javascript">
        var likebtn_reports_id = '<?php echo htmlspecialchars(get_option('likebtn_site_id')) ?>';
        var likebtn_couch_db_view_main = '_design/doctrine_repositories/_view/equal_constraint';
        var likebtn_couch_db_type = 'Likebtn.WidgetBundle.CouchDocument.Sitestats';
        var likebtn_couch_db_url = 'https://storage.likebtn.com/widget';
        var likebtn_report_store_days = 14;
        var likebtn_msg_votes = '<?php _e('Total Votes', 'likebtn-like-button') ?>';
        var likebtn_msg_likes = '<?php _e('Likes', 'likebtn-like-button') ?>';
        var likebtn_msg_dislikes = '<?php _e('Dislikes', 'likebtn-like-button') ?>';
        var global_graph_lang = {
            rangeSelectorZoom: '',
            rangeSelectorFrom: '',
            rangeSelectorTo: '/',
            loading: "<?php _e('Loading...', 'likebtn-like-button') ?>",
            downloadJPEG: "<?php _e('Download JPEG image', 'likebtn-like-button') ?>",
            downloadPDF: "<?php _e('Download PDF document', 'likebtn-like-button') ?>",
            downloadPNG: "<?php _e('Download PNG image', 'likebtn-like-button') ?>",
            downloadSVG: "<?php _e('Download SVG vector image', 'likebtn-like-button') ?>",
            printChart: "<?php _e('Print chart', 'likebtn-like-button') ?>",
            months: ["<?php _e('January', 'likebtn-like-button') ?>", "<?php _e('February', 'likebtn-like-button') ?>", "<?php _e('March', 'likebtn-like-button') ?>", "<?php _e('April', 'likebtn-like-button') ?>", "<?php _e('May', 'likebtn-like-button') ?>", "<?php _e('June', 'likebtn-like-button') ?>", "<?php _e('July', 'likebtn-like-button') ?>", "<?php _e('August', 'likebtn-like-button') ?>", "<?php _e('September', 'likebtn-like-button') ?>", "<?php _e('October', 'likebtn-like-button') ?>", "<?php _e('November', 'likebtn-like-button') ?>", "<?php _e('December', 'likebtn-like-button') ?>"],
            numericSymbols: null,
            shortMonths: ["<?php _e('Jan', 'likebtn-like-button') ?>", "<?php _e('Feb', 'likebtn-like-button') ?>", "<?php _e('Mar', 'likebtn-like-button') ?>", "<?php _e('Apr', 'likebtn-like-button') ?>", "<?php _e('May', 'likebtn-like-button') ?>", "<?php _e('Jun', 'likebtn-like-button') ?>", "<?php _e('Jul', 'likebtn-like-button') ?>", "<?php _e('Aug', 'likebtn-like-button') ?>", "<?php _e('Sep', 'likebtn-like-button') ?>", "<?php _e('Oct', 'likebtn-like-button') ?>", "<?php _e('Nov', 'likebtn-like-button') ?>", "<?php _e('Dec', 'likebtn-like-button') ?>"],
            weekdays: ["<?php _e('Sunday', 'likebtn-like-button') ?>", "<?php _e('Monday', 'likebtn-like-button') ?>", "<?php _e('Tuesday', 'likebtn-like-button') ?>", "<?php _e('Wednesday', 'likebtn-like-button') ?>", "<?php _e('Thursday', 'likebtn-like-button') ?>", "<?php _e('Friday', 'likebtn-like-button') ?>", "<?php _e('Saturday', 'likebtn-like-button') ?>"],
            noData: "<?php _e('No votes found', 'likebtn-like-button') ?>"
        }

        jQuery(document).ready(function() {
            loadReports();
            //showMap();
        });
    </script>

    <div id="likebtn_reports">
        <?php if (!get_option('likebtn_site_id')): ?>
            <div class="error">
                <br/>
                <?php echo strtr(
                    __('Reports are not available. Enter your account data on <a href="%url_sync%">Settings</a> tab.', 'likebtn-like-button'), 
                    array('%url_sync%'=>admin_url().'admin.php?page=likebtn_settings')
                ); ?>
                <br/><br/>
            </div>
        <?php endif ?>
        <div class="reports-error error"><br/><?php _e('Error occured', 'likebtn-like-button') ?>. &nbsp;<button class="button-secondary" onclick="loadReports()"><?php _e('Retry', 'likebtn-like-button') ?></button><br/><br/></div>
        <h3 class="reports-vals">
            <div class="report-val"><?php _e('Total Votes', 'likebtn-like-button') ?> <span class="reports-label reports-total"><img src="<?php echo $loader_src ?>" /></span></div>
            <div class="report-val"><?php _e('Likes', 'likebtn-like-button') ?> <span class="reports-label reports-like"><img src="<?php echo $loader_src ?>" /></span></div>
            <div class="report-val"><?php _e('Dislikes', 'likebtn-like-button') ?> <span class="reports-label reports-dislike"><img src="<?php echo $loader_src ?>" /></span></div>
        </h3>
        <h4><?php _e('Last Two Weeks', 'likebtn-like-button') ?></h4>
        <div class="postbox likebtn-graph"><div class="reports-graph-d"></div></div>
        <?php if (count($coordinates)): ?>
            <div class="postbox likebtn-graph"><div class="reports-map"></div></div>
        <?php endif ?>

        <h4><?php _e('Last Year', 'likebtn-like-button') ?></h4>
        <div class="postbox likebtn-graph"><div class="reports-graph-m"></div></div>
    </div>

    <?php if (count($coordinates)): ?>
        <script type="text/javascript">
            var likebtn_reports_loc = [
                <?php foreach ($coordinates as $i => $loc): ?>
                    [<?php echo $loc->lat ?>, <?php echo $loc->lng ?>]<?php if ($i !== count($coordinates)-1): ?>,<?php endif ?>
                <?php endforeach ?>
            ];
        </script>
        <?php /*<script async defer
            src="https://maps.googleapis.com/maps/api/js?v=3.exp&callback=showMap&libraries=visualization">
        </script>*/ ?>
    <?php endif ?>

    <?php

    _likebtn_admin_footer();
}

// Stats enable instructions
function _likebtn_enable_stats_msg()
{
    ?>
        <ol>
            <?php if (get_option('likebtn_plan') < LIKEBTN_PLAN_PRO): ?>
                <li>
                    <?php echo strtr(
                        __('<a href="%url_upgrade%">Upgrade</a> your website to PRO or higher plan on LikeBtn.com.', 'likebtn-like-button'), 
                        array('%url_upgrade%'=>"javascript:likebtnPopup('".__('https://likebtn.com/en/', 'likebtn-like-button')."pricing?engine=wordpress');void(0);")
                    ); ?>
                </li>
            <?php endif ?>
            <li>
                <?php echo strtr(
                    __('Enable synchronization on <a href="%url_sync%">Settings</a> tab.', 'likebtn-like-button'), 
                    array('%url_sync%'=>admin_url().'admin.php?page=likebtn_settings#synchronization')
                ); ?>
            </li>
        </ol>
    <?php
}

// Get statistics entity
function _likebtn_statistics_entity()
{
    $entity_name = LIKEBTN_ENTITY_POST;
    if (!empty($_GET['likebtn_entity_name'])) {
        // must contain any text
        // it is safely used in $wpdb->prepare() statement, so does not need sanitizing
        $entity_name = $_GET['likebtn_entity_name'];
    }
    return $entity_name;
}

// get SQL query for retrieving statistics
function _likebtn_get_statistics_sql($entity_name, $prefix, $query_where, $query_orderby, $query_limit, $query_select = 'SQL_CALC_FOUND_ROWS')
{
    global $likebtn_bbp_post_types;

    if ($entity_name == LIKEBTN_ENTITY_COMMENT) {
        // comment
        $query = "
             SELECT {$query_select}
                p.comment_ID as 'post_id',
                p.comment_content as post_title,
                pm_likes.meta_value as 'likes',
                pm_dislikes.meta_value as 'dislikes',
                pm_likes_minus_dislikes.meta_value as 'likes_minus_dislikes',
                '' as url
             FROM {$prefix}commentmeta pm_likes
             LEFT JOIN {$prefix}comments p
                 ON (p.comment_ID = pm_likes.comment_id)
             LEFT JOIN {$prefix}commentmeta pm_dislikes
                 ON (pm_dislikes.comment_id = pm_likes.comment_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
             LEFT JOIN {$prefix}commentmeta pm_likes_minus_dislikes
                 ON (pm_likes_minus_dislikes.comment_id = pm_likes.comment_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
             WHERE
                pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                {$query_where}
             {$query_orderby}
             {$query_limit}";
    } elseif ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM) {
        // custom item
        $query = "
             SELECT {$query_select}
                p.identifier as 'post_id',
                p.identifier as 'post_title',
                p.likes,
                p.dislikes,
                p.likes_minus_dislikes,
                p.url
             FROM {$prefix}" . LIKEBTN_TABLE_ITEM . " p
             WHERE
                1 = 1
                {$query_where}
             {$query_orderby}
             {$query_limit}";    
    } elseif (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC))) {
        // BuddyPress activity
        switch ($entity_name) {
            case LIKEBTN_ENTITY_BP_ACTIVITY_POST:
                $query_where .= " AND p.type = 'new_blog_post' ";
                break;
            case LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC:
                $query_where .= " AND p.type = 'bbp_topic_create' ";
                break;
            case LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT:
                $query_where .= " AND p.type = 'activity_comment' ";
                break;
            default:
                $query_where .= " AND p.type != 'new_blog_post' AND p.type != 'bbp_topic_create' AND p.type != 'activity_comment' ";
                break;
        }
        $query = "
             SELECT {$query_select}
                p.id as 'post_id',
                CONCAT( IF(p.action != '', p.action, IF(p.content !='', p.content, IF(p.primary_link != '', p.primary_link, p.type))), IF(p.content != '' && p.type != 'bbp_topic_create' && p.type != 'new_blog_post', CONCAT(': ', p.content), '') ) as 'post_title',
                pm_likes.meta_value as 'likes',
                pm_dislikes.meta_value as 'dislikes',
                pm_likes_minus_dislikes.meta_value as 'likes_minus_dislikes',
                '' as url
             FROM {$prefix}bp_activity_meta pm_likes
             LEFT JOIN {$prefix}bp_activity p
                 ON (p.id = pm_likes.activity_id)
             LEFT JOIN {$prefix}bp_activity_meta pm_dislikes
                 ON (pm_dislikes.activity_id = pm_likes.activity_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
             LEFT JOIN {$prefix}bp_activity_meta pm_likes_minus_dislikes
                 ON (pm_likes_minus_dislikes.activity_id = pm_likes.activity_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
             WHERE
                pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                {$query_where}
             {$query_orderby}
             {$query_limit}";
    } elseif ($entity_name == LIKEBTN_ENTITY_BP_MEMBER) {
        $query = "
             SELECT {$query_select}
                p.user_id as 'post_id',
                p.value as 'post_title',
                pm_likes.meta_value as 'likes',
                pm_dislikes.meta_value as 'dislikes',
                pm_likes_minus_dislikes.meta_value as 'likes_minus_dislikes',
                '' as url
             FROM {$prefix}bp_xprofile_meta pm_likes
             LEFT JOIN {$prefix}bp_xprofile_data p
                 ON (p.user_id = pm_likes.object_id)
             LEFT JOIN {$prefix}bp_xprofile_meta pm_dislikes
                 ON (pm_dislikes.object_id = pm_likes.object_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
             LEFT JOIN {$prefix}bp_xprofile_meta pm_likes_minus_dislikes
                 ON (pm_likes_minus_dislikes.object_id = pm_likes.object_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
             WHERE
                pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                AND pm_likes.object_type = '" . LIKEBTN_BP_XPROFILE_OBJECT_TYPE . "' 
                {$query_where}
             {$query_orderby}
             {$query_limit}";
    } elseif (in_array($entity_name, array(LIKEBTN_ENTITY_BBP_USER, LIKEBTN_ENTITY_UM_USER))) {
        // User Profile
        $query = "
             SELECT {$query_select}
                p.ID as 'post_id',
                p.display_name as 'post_title',
                pm_likes.meta_value as 'likes',
                pm_dislikes.meta_value as 'dislikes',
                pm_likes_minus_dislikes.meta_value as 'likes_minus_dislikes',
                '' as url
             FROM {$prefix}usermeta pm_likes
             LEFT JOIN {$prefix}users p
                 ON (p.ID = pm_likes.user_id)
             LEFT JOIN {$prefix}usermeta pm_dislikes
                 ON (pm_dislikes.user_id = pm_likes.user_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
             LEFT JOIN {$prefix}usermeta pm_likes_minus_dislikes
                 ON (pm_likes_minus_dislikes.user_id = pm_likes.user_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
             WHERE
                pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                {$query_where}
             {$query_orderby}
             {$query_limit}";
    } elseif ($entity_name == LIKEBTN_ENTITY_BBP_POST) {
        // bbPress Forum Post
        $query = "
             SELECT {$query_select}
                p.ID as 'post_id',
                IF(p.post_title != '', p.post_title, p.post_content) as 'post_title',
                pm_likes.meta_value as 'likes',
                pm_dislikes.meta_value as 'dislikes',
                pm_likes_minus_dislikes.meta_value as 'likes_minus_dislikes',
                '' as url
             FROM {$prefix}postmeta pm_likes
             LEFT JOIN {$prefix}posts p
                 ON (p.ID = pm_likes.post_id)
             LEFT JOIN {$prefix}postmeta pm_dislikes
                 ON (pm_dislikes.post_id = pm_likes.post_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
             LEFT JOIN {$prefix}postmeta pm_likes_minus_dislikes
                 ON (pm_likes_minus_dislikes.post_id = pm_likes.post_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
             WHERE
                pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                AND p.post_type in ('".implode("', '", $likebtn_bbp_post_types)."') 
                {$query_where}
             {$query_orderby}
             {$query_limit}";
    } else {
        // post
        $query = "
             SELECT {$query_select}
                p.ID as 'post_id',
                p.post_title,
                pm_likes.meta_value as 'likes',
                pm_dislikes.meta_value as 'dislikes',
                pm_likes_minus_dislikes.meta_value as 'likes_minus_dislikes',
                '' as url
             FROM {$prefix}postmeta pm_likes
             LEFT JOIN {$prefix}posts p
                 ON (p.ID = pm_likes.post_id)
             LEFT JOIN {$prefix}postmeta pm_dislikes
                 ON (pm_dislikes.post_id = pm_likes.post_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
             LEFT JOIN {$prefix}postmeta pm_likes_minus_dislikes
                 ON (pm_likes_minus_dislikes.post_id = pm_likes.post_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
             WHERE
                pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                {$query_where}
             {$query_orderby}
             {$query_limit}";
    }

    return $query;
}

// admin help
function likebtn_admin_help() 
{
    likebtn_admin_header();
    ?>
    <div>
        <iframe width="100%" height="428" src="https://www.youtube.com/embed/JpMYoKPPbyM" frameborder="0" allowfullscreen></iframe>
        <ul>
            <li>● <?php echo __('<a href="https://likebtn.com/en/wordpress-like-button-plugin#documentation" target="_blank">Documentation</a>', 'likebtn-like-button'); ?></li>
            <li>● <?php echo __('<a href="https://likebtn.com/en/faq" target="_blank">LikeBtn.com FAQ</a>', 'likebtn-like-button'); ?></li>
            <li>● <a href="<?php echo __('https://likebtn.com/en/', 'likebtn-like-button'); ?>developers" target="_blank">Hire developer</a></li>
        </ul>
    </div>
    <?php
    _likebtn_admin_footer();
}

// admin widget
function likebtn_admin_widget() 
{
    likebtn_admin_header();
    ?>
    <div>
        <div class="widget_demo">
            <h1><?php _e( 'Most Liked Content', 'likebtn-like-button'); ?></h1>
            <img alt="" src="<?php echo _likebtn_get_public_url() ?>img/widget.jpg" /><br/>
            <a href="<?php echo admin_url('widgets.php'); ?>" class="button-primary"><?php _e( 'Add Widget Now!', 'likebtn-like-button'); ?></a>
        </div>
        <div class="widget_demo">
            <h1><?php _e( 'Liked by User', 'likebtn-like-button'); ?></h1>
            <img alt="" src="<?php echo _likebtn_get_public_url() ?>img/widget_liked_by_user.jpg" /><br/>
            <a href="<?php echo admin_url('widgets.php'); ?>" class="button-primary"><?php _e( 'Add Widget Now!', 'likebtn-like-button'); ?></a>
        </div>
    </div>
    <?php
    _likebtn_admin_footer();
}

// Process bulk actions
function _likebtn_bulk_actions()
{
    $entity_name = _likebtn_statistics_entity();

    // Resettings
    if (empty($_POST['bulk_action']) || empty($_POST['item']) ) {
        return false;
    }

    switch ($_POST['bulk_action']) {
        case 'reset':
            // $_POST['item'] must be able to contain any symbols
            // It is used only to send to external server, where it is sanitized.
            $reseted = _likebtn_reset($entity_name, $_POST['item']);
            _likebtn_add_notice(array(
                'msg' => __('Likes and dislikes for the following number of items have been successfully reseted:', 'likebtn-like-button').' '.$reseted,
            ));
            break;

        case 'delete':
            // $_POST['item'] must be able to contain any symbols
            // It is used only to send to external server, where it is sanitized.
            $reseted = _likebtn_delete($entity_name, $_POST['item']);
            _likebtn_add_notice(array(
                'msg' => __('The following number of items have been successfully deleted:', 'likebtn-like-button').' '.$reseted,
            ));
            break;

        default:
            return false;
            break;
    }

    wp_redirect($_SERVER['REQUEST_URI']);
    exit();
}

// get URL of the public folder
function _likebtn_get_public_url() {
    //$siteurl = get_option('siteurl');
    //return $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/public/';
    return plugin_dir_url( __FILE__ ) . 'public/';
}

// Get supported by current theme Post Formats
function _likebtn_get_post_formats() {
    $post_formats = get_theme_support('post-formats');
    if (is_array($post_formats[0])) {
        $post_formats = $post_formats[0];
    } else {
        $post_formats = array();
    }

    if (!is_array($post_formats)) {
        $post_formats = array();
    }

    // append Standard format
    array_unshift($post_formats, 'standard');

    return $post_formats;
}

// Get entity types
function _likebtn_get_entities($no_list = false, $include_invisible = false, $builtin = true, $mode = 'assoc') {

    $likebtn_entities = array(
        LIKEBTN_ENTITY_POST => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_POST),
        LIKEBTN_ENTITY_POST_LIST => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_POST_LIST),
        LIKEBTN_ENTITY_PAGE => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_PAGE),
        LIKEBTN_ENTITY_PAGE_LIST => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_PAGE_LIST),
        LIKEBTN_ENTITY_ATTACHMENT => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_ATTACHMENT),
        LIKEBTN_ENTITY_ATTACHMENT_LIST => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_ATTACHMENT_LIST)
    );
    $post_types = get_post_types(array('public'=>true, '_builtin' => $builtin));

    if (!empty($post_types)) {
        foreach ($post_types as $post_type) {

            // If bbPress is active miss bbPress custom post types
            if (_likebtn_is_bbp_active() && in_array($post_type, array('forum', 'topic', 'reply'))) {
                continue;
            }

            $likebtn_entities[$post_type] = _likebtn_get_entity_name_title($post_type);
            $likebtn_entities[$post_type.LIKEBTN_LIST_FLAG] = _likebtn_get_entity_name_title($post_type.LIKEBTN_LIST_FLAG);
        }
    }

    // Append BuddyPress
    if (_likebtn_is_bp_active()) {
        $likebtn_entities[LIKEBTN_ENTITY_BP_ACTIVITY_POST] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_BP_ACTIVITY_POST);
        $likebtn_entities[LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE);
        $likebtn_entities[LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT);
        $likebtn_entities[LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC);
        $likebtn_entities[LIKEBTN_ENTITY_BP_MEMBER] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_BP_MEMBER);
    }

    // Append bbPress
    if (_likebtn_is_bbp_active()) {
        $likebtn_entities[LIKEBTN_ENTITY_BBP_POST] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_BBP_POST);
        $likebtn_entities[LIKEBTN_ENTITY_BBP_USER] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_BBP_USER);
    }

    if (likebtn_is_um_active()) {
        $likebtn_entities[LIKEBTN_ENTITY_UM_USER] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_UM_USER);
        $likebtn_entities[LIKEBTN_ENTITY_UM_USER_LIST] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_UM_USER_LIST);
    }

    // append Comments
    $likebtn_entities[LIKEBTN_ENTITY_COMMENT] = _likebtn_get_entity_name_title(LIKEBTN_ENTITY_COMMENT);

    // Remove excerpt entities
    if ($no_list) {
        foreach ($likebtn_entities as $entity_name=>$entity_title) {
            if (_likebtn_has_list_flag($entity_name)) {
                unset($likebtn_entities[$entity_name]);
            }
        }
    }

    // Add invisible
    if ($include_invisible) {
        $likebtn_entities = array_merge($likebtn_entities, array(
            LIKEBTN_ENTITY_USER => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_USER),
        ));
    }

    switch ($mode) {
        case 'assoc':
            return $likebtn_entities;
            break;
        case 'keys':
            return array_keys($likebtn_entities);
            break;
        case 'titles':
            return array_values($likebtn_entities);
            break;
    }
}

// Get all categories including custom post types categories
function _likebtn_get_categories()
{
    if (function_exists('get_taxonomies')) {
        $taxs = get_taxonomies(array(
            'public' => true,
            // To exclude tags
            'hierarchical' => true
        ));

        return get_categories(array('taxonomy'=>array_keys($taxs)));
    } else {
        return get_categories();
    }
}

// short code
function likebtn_shortcode($values) {
    $entity_name = get_post_type();
    $entity_id = likebtn_get_the_ID($entity_name);

    // check user authorization
    $auth_check = _likebtn_auth_check($entity_name, $values);
    if ($auth_check['return']) {
        return '';
    }
    $values = $auth_check['values'];
    // ($entity_name, $entity_id, $values = null, $use_entity_name = '', $use_entity_settings = true, $wrap = true, $include_script = false, $show_like_box = false) 
    $html = _likebtn_get_markup($entity_name, $entity_id, $values, '', false, false);

    if ($auth_check['user_logged_in_alert']) {
        if ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT) {
            $html = $auth_check['user_logged_in_alert'];
        } elseif ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT_BTN) {
            $html .= $auth_check['user_logged_in_alert'];
        }
    }

    return $html;
}

add_shortcode('likebtn', 'likebtn_shortcode');

// most liked short code
function likebtn_most_liked_widget_shortcode($args) {

    global $LikeBtnLikeButtonMostLiked;
    $options = $args;

    if (!is_array($options)) {
        $options = [];
    }
    if (isset($options['number'])) {
        $options['number'] = (int) $options['number'];
    }
    if (isset($options['entity_name'])) {
        $options['entity_name'] = explode(',', $options['entity_name']);
    } else if (isset($options['content_types'])) {
        $options['entity_name'] = explode(',', $options['content_types']);
    } else {
        $options['entity_name'] = array();
    }
    if (isset($options['exclude_categories'])) {
        $options['exclude_categories'] = explode(',', $options['exclude_categories']);
    } else {
        $options['exclude_categories'] = array();
    }
    return $LikeBtnLikeButtonMostLiked->widget(null, $options, false);
}

add_shortcode('likebtn_most_liked', 'likebtn_most_liked_widget_shortcode');

// liked by user shortcode
function likebtn_liked_by_user_widget_shortcode($args) {

    global $LikeBtnLikeButtonMostLikedByUser;
    $options = $args;

    if (isset($options['number'])) {
        $options['number'] = (int) $options['number'];
    }
    if (isset($options['entity_name'])) {
        $options['entity_name'] = explode(',', $options['entity_name']);
    } else if (isset($options['content_types'])) {
        $options['entity_name'] = explode(',', $options['content_types']);
    } else {
        $options['entity_name'] = array();
    }
    if (isset($options['exclude_categories'])) {
        $options['exclude_categories'] = explode(',', $options['exclude_categories']);
    } else {
        $options['exclude_categories'] = array();
    }
    return $LikeBtnLikeButtonMostLikedByUser->widget(null, $options, false);
}

add_shortcode('likebtn_liked_by_user', 'likebtn_liked_by_user_widget_shortcode');

// Shortcode: display total number of likes for category
function likebtn_category_likes($args)
{
    global $wpdb;

    $category_id = null;
    if (!empty($args['category_id'])) {
        $category_id = (int)$args['category_id'];
    } else {
        $categories = get_the_category();
        if (!empty($categories[0])) {
            $category_id = $categories[0]->cat_ID;
        }
    }
    if (!$category_id) {
        return '';
    }

    $query = "
        SELECT sum(`pm`.`meta_value`)
        FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} `p` ON `p`.`ID` = pm.`post_id`
        LEFT JOIN {$wpdb->term_relationships} `tr` ON `p`.`ID` = `tr`.`object_id`
        LEFT JOIN {$wpdb->term_taxonomy} `tt` ON `tr`.`term_taxonomy_id` = `tt`.`term_taxonomy_id`
        WHERE `pm`.`meta_key` = '".LIKEBTN_META_KEY_LIKES."'
            AND `pm`.`meta_value` is not NULL
            AND `p`.`post_status` = 'publish'
            AND `tt`.`taxonomy` = 'category'
            AND `tt`.`term_id` = %d
    ";
    $query_prepared = $wpdb->prepare($query, array('term_id' => $category_id));
    $likes = (int)$wpdb->get_var($query_prepared);

    return $likes;
}

add_shortcode('likebtn_category_likes', 'likebtn_category_likes');

// number of votes of the user
function likebtn_user_votes_shortcode($args) {

    global $wpdb;

    if (!empty($args['user_id'])) {
        $user_id = $args['user_id'];
    } else {
        $user_id = get_current_user_id();
    }

    if (!empty($args['type'])) {
        if ($args['type'] == 'dislikes') {
            $type = LIKEBTN_VOTE_DISLIKE;
        } elseif ($args['type'] == 'total') {
            $type = '';
        } else {
            $type = LIKEBTN_VOTE_LIKE;
        }
    } else {
        $type = LIKEBTN_VOTE_LIKE;
    }

    $query_parameters = array(
        'user_id' => $user_id,
        'type'    => $type,
    );
    $query = "
        SELECT count(*)
        FROM ".$wpdb->prefix . LIKEBTN_TABLE_VOTE."
        WHERE user_id = %d
    ";

    if ($type) {
        $query .= " AND type = %d ";
    }

    $query_prepared = $wpdb->prepare($query, $query_parameters);
    $votes_count = (int)$wpdb->get_var($query_prepared);

    return $votes_count;
}

add_shortcode('likebtn_user_votes', 'likebtn_user_votes_shortcode');

// Likes shortcode
function likebtn_shortcode_likes($args) {
    if (!empty($args['identifier'])) {
        $identifier = $args['identifier'];
    } else {
        $entity_name = LIKEBTN_ENTITY_COMMENT;
        $entity_id = get_comment_ID();
        // Post
        if (!$entity_id) {
            $entity_name = get_post_type();
            $entity_id = likebtn_get_the_ID($entity_name);
        }
        $identifier = $entity_name.'_'.$entity_id;
    }
    $votes = _likebtn_get_item_votes($identifier, LIKEBTN_META_KEY_LIKES);
    return (int)$votes[LIKEBTN_META_KEY_LIKES];
}

add_shortcode(LIKEBTN_SHORTCODE_LIKES, 'likebtn_shortcode_likes');

// Dislikes shortcode
function likebtn_shortcode_dislikes($args) {
    if (!empty($args['identifier'])) {
        $identifier = $args['identifier'];
    } else {
        $entity_name = LIKEBTN_ENTITY_COMMENT;
        $entity_id = get_comment_ID();
        // Post
        if (!$entity_id) {
            $entity_name = get_post_type();
            $entity_id = likebtn_get_the_ID($entity_name);
        }
        $identifier = $entity_name.'_'.$entity_id;
    }
    $votes = _likebtn_get_item_votes($identifier, LIKEBTN_META_KEY_DISLIKES);
    return (int)$votes[LIKEBTN_META_KEY_DISLIKES];
}

add_shortcode(LIKEBTN_SHORTCODE_DISLIKES, 'likebtn_shortcode_dislikes');

// get current admin subpage
function _likebtn_get_subpage()
{
    $likebtn_entities = _likebtn_get_entities(false, false, false);
    $subpage = LIKEBTN_ENTITY_POST;

    if (!empty($_POST['likebtn_subpage']) && array_key_exists($_POST['likebtn_subpage'], $likebtn_entities) ) {
        $subpage = $_POST['likebtn_subpage'];
    } elseif (!empty($_GET['likebtn_subpage']) && array_key_exists($_GET['likebtn_subpage'], $likebtn_entities) ) {
        $subpage = $_GET['likebtn_subpage'];
    }
    return $subpage;
}

// Save BuddyPress Member Profile votes
function _likebtn_save_bp_member_votes($entity_id, $likes, $dislikes, $likes_minus_dislikes)
{
    global $wpdb;

    if (!_likebtn_is_bp_active()) {
        return false;
    }
    $bp_xprofile = $wpdb->get_row($wpdb->prepare("
        SELECT id
        FROM ".$wpdb->prefix."bp_xprofile_data
        WHERE user_id = %d
    ", $entity_id));

    if (!empty($bp_xprofile)) {
        if ($likes !== null) {
            if (count(bp_xprofile_get_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES)) > 1) {
                bp_xprofile_delete_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES);
                bp_xprofile_add_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES, $likes, true);
            } else {
                bp_xprofile_update_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES, $likes);
            }
        }
        if ($dislikes !== null) {
            if (count(bp_xprofile_get_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_DISLIKES)) > 1) {
                bp_xprofile_delete_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_DISLIKES);
                bp_xprofile_add_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_DISLIKES, $dislikes, true);
            } else {
                bp_xprofile_update_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_DISLIKES, $dislikes);
            }
        }
        if ($likes_minus_dislikes !== null) {
            if (count(bp_xprofile_get_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES)) > 1) {
                bp_xprofile_delete_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES);
                bp_xprofile_add_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes, true);
            } else {
                bp_xprofile_update_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes);
            }
        }
        return true;
    }

    return false;
}

// Delete BuddyPress Member Profile votes
function _likebtn_delete_bp_member_votes($entity_id)
{
    global $wpdb;

    if (!_likebtn_is_bp_active()) {
        return false;
    }
    $bp_xprofile = $wpdb->get_row($wpdb->prepare("
        SELECT id
        FROM ".$wpdb->prefix."bp_xprofile_data
        WHERE user_id = %d
    ", $entity_id));

    if (!empty($bp_xprofile)) {
        bp_xprofile_delete_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES);
        bp_xprofile_delete_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_DISLIKES);
        bp_xprofile_delete_meta($entity_id, LIKEBTN_BP_XPROFILE_OBJECT_TYPE, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES);
        return true;
    }

    return false;
}

// Save user votes
function _likebtn_save_user_votes($entity_id, $likes, $dislikes, $likes_minus_dislikes)
{
    global $wpdb;

    $user = get_user_by('id', $entity_id); 

    if (!empty($user)) {
        if ($likes !== null) {
            if (count(get_user_meta($entity_id, LIKEBTN_META_KEY_LIKES)) > 1) {
                delete_user_meta($entity_id, LIKEBTN_META_KEY_LIKES);
                add_user_meta($entity_id, LIKEBTN_META_KEY_LIKES, $likes, true);
            } else {
                update_user_meta($entity_id, LIKEBTN_META_KEY_LIKES, $likes);
            }
        }
        if ($dislikes !== null) {
            if (count(get_user_meta($entity_id, LIKEBTN_META_KEY_DISLIKES)) > 1) {
                delete_user_meta($entity_id, LIKEBTN_META_KEY_DISLIKES);
                add_user_meta($entity_id, LIKEBTN_META_KEY_DISLIKES, $dislikes, true);
            } else {
                update_user_meta($entity_id, LIKEBTN_META_KEY_DISLIKES, $dislikes);
            }
        }
        if ($likes_minus_dislikes !== null) {
            if (count(get_user_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES)) > 1) {
                delete_user_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES);
                add_user_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes, true);
            } else {
                update_user_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes);
            }
        }
        return true;
    }

    return false;
}

// Delete user votes
function _likebtn_delete_user_votes($entity_id)
{
    global $wpdb;

    $user = get_user_by('id', $entity_id); 

    if (!empty($user)) {
        delete_user_meta($entity_id, LIKEBTN_META_KEY_LIKES);
        delete_user_meta($entity_id, LIKEBTN_META_KEY_DISLIKES);
        delete_user_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES);
        return true;
    }

    return false;
}

/**
 * Add Support for myCRED
 * After the theme has been setup, we register our custom
 * hook which will allow us to give users points for voting.
 * @version 1.0
 */
function _likebtn_add_support_for_mycred() {

    // Make sure myCRED is installed
    if (!function_exists('mycred')) { 
        return;
    }

    // Fetch the hook class
    require_once(dirname(__FILE__) . '/includes/likebtn_mycred.class.php');

    // Add Support for myCRED Badges
    add_filter('mycred_all_references', '_likebtn_add_support_for_mycred_badges');

    // Register our custom hook
    add_filter('mycred_setup_hooks', '_likebtn_register_mycred_hook');
}
add_action('after_setup_theme', '_likebtn_add_support_for_mycred');

// Add Support for Badges. All we have to do is to add in our custom references.
function _likebtn_add_support_for_mycred_badges($hooks) {

    $hooks[LikeBtn_MyCRED::REF_LIKE]   = __( 'Liking Content', 'likebtn-like-button');
    $hooks[LikeBtn_MyCRED::REF_GET_LIKE]   = __( 'Getting Content Like', 'likebtn-like-button');
    $hooks[LikeBtn_MyCRED::REF_DISLIKE] = __( 'Disliking Content', 'likebtn-like-button');
    $hooks[LikeBtn_MyCRED::REF_GET_DISLIKE] = __( 'Getting Content Dislike', 'likebtn-like-button');

    return $hooks;

}

// Register myCRED Hooks. Add our custom hook to the list.
function _likebtn_register_mycred_hook( $installed ) {

    $installed[LikeBtn_MyCRED::ID] = array(
        'title'       => __('Like Button', 'likebtn-like-button'),
        'description' => __('Award points for liking & disliking content.', 'likebtn-like-button'),
        'callback'    => array('LikeBtn_MyCRED')
    );

    return $installed;
}

// Get entity name and from identifier
function _likebtn_parse_identifier($identifier)
{
    preg_match("/(.*)_(\d+)$/", $identifier, $m);
    if ($m) {
        return array(
            'entity_name' => $m[1],
            'entity_id' => $m[2],
        );
    } else {
        return array(
            'entity_name' => '',
            'entity_id' => 0,
        );
    }
}

function _likebtn_get_markup($entity_name, $entity_id, $values = null, $use_entity_name = '', $use_entity_settings = true, $wrap = true, $include_script = false, $show_like_box = false) 
{
    //global $wp_version;
    global $likebtn_settings_deprecated;
    global $likebtn_map_entities;

    $prepared_settings = array();

    if (!$use_entity_name) {
        $use_entity_name = $entity_name;
    }
    $use_entity_name = likebtn_calc_use_settings_from($use_entity_name);

    // Cut excerpt flag from entity_name
    if ($entity_id !== 'demo') {
        $entity_name = _likebtn_cut_list_flag($entity_name);
    }

    // Strip data- from values
    if (is_array($values)) {
        foreach ($values as $key => $value) {
            if (strstr($key, 'data-') && !isset($values[str_replace('data-', '', $key)])) {
                $values[ str_replace('data-', '', $key) ] = $value;
                unset($values[$key]);
            }
        }
    }

    if ($values && isset($values['identifier']) && $values['identifier'] !== '') {
        $identifier = $values['identifier'];
    } else {
        $identifier = _likebtn_entity_to_identifier($entity_name, $entity_id);
    }
    $data = ' data-identifier="' . $identifier . '" ';

    // Site ID
    if (get_option('likebtn_site_id')) {
        $data .= ' data-site_id="' . get_option('likebtn_site_id') . '" ';
    }

    // Authorization check
    if (_likebtn_get_option($use_entity_name, 'likebtn_user_logged_in', $values) == LIKEBTN_USER_LOGGED_IN_MODAL && !is_user_logged_in()) {
        $values['voting_enabled'] = '0';
        $data .= ' data-clk_modal="' . htmlspecialchars(_likebtn_get_user_logged_in_alert($use_entity_name)) . '" ';
    }

    if (_likebtn_get_option($use_entity_name, 'likebtn_voting_author', $values) == '1' && get_current_user_id()) {
        $author_id = _likebtn_get_author_id($use_entity_name, $entity_id);
        if ($author_id == get_current_user_id()) {
            $values['voting_enabled'] = '0';
        }
    }

    // Get item options
    $entity = null;
    $entity_url = '';
    $entity_title = '';
    $entity_image = '';
    $entity_date = '';

    if ($entity_name == LIKEBTN_ENTITY_COMMENT) {
        $entity = get_comment($entity_id);
        if ($entity) {
            $entity_url = get_comment_link($entity->comment_ID);
            $entity_title = _likebtn_shorten_title($entity->comment_content);
            $entity_date = mysql2date("c", $entity->comment_date);
        }
    } else if (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC))) {
        $entity_title = _likebtn_shorten_title(_likebtn_bp_get_activity_title($entity_id));
        if (function_exists('bp_activity_get_permalink')) {
            $entity_url = bp_activity_get_permalink($entity_id);
        }
    } else if ($entity_name == LIKEBTN_ENTITY_BP_MEMBER || $entity_name == LIKEBTN_ENTITY_USER || $entity_name == LIKEBTN_ENTITY_BBP_USER) {
        if (function_exists('bp_core_get_user_displayname')) {
            $entity_title = bp_core_get_user_displayname($entity_id);
        } else {
            $entity_title = get_the_author_meta('user_nicename', $entity_id);
        }
        if (function_exists('bp_core_get_user_domain')) {
            $entity_url = bp_core_get_user_domain($entity_id);
        } else {
            $entity_url = get_author_posts_url($entity_id);
        }
        $entity_image = _likebtn_get_avatar_url($entity_id);
        $user_info = get_userdata($entity_id);
        if (!empty($user_info) && !empty($user_info->user_registered)) {
            $entity_date = mysql2date("c", $user_info->user_registered);
        }
    } else {
        $entity = get_post($entity_id);
        if ($entity) {
            $entity_url = get_permalink($entity->ID);
            $entity_title = $entity->post_title;
            $entity_image = _likebtn_get_entity_image($entity_name, $entity_id);
            $entity_date = mysql2date("c", $entity->post_date);
        }
    }

    // Voting period
    // Must go after item parameters as entity_date is defined here
    $voting_period = _likebtn_get_option($use_entity_name, 'likebtn_voting_period', $values);
    if ($voting_period) {
        switch ($voting_period) {
            case LIKEBTN_VOTING_PERIOD_DATE:
                $voting_date = _likebtn_get_option($use_entity_name, 'likebtn_voting_date', $values);
                if ($voting_date) {
                    if (time() > strtotime($voting_date)) {
                        $values['voting_enabled'] = '0';
                    }
                }
                break;
            case LIKEBTN_VOTING_PERIOD_CREATED:
                $voting_created = (int)_likebtn_get_option($use_entity_name, 'likebtn_voting_created', $values);
                if ($voting_created) {
                    // echo date("Y-m-d H:i", strtotime($voting_date)). ' ';
                    // echo date("Y-m-d H:i", time());
                    // exit();
                    if ($entity_date && time() > strtotime($entity_date) + $voting_created) {
                        $values['voting_enabled'] = '0';
                    }
                }
                break;
        }
    }

    // Allow to vote for one post only
    if (!isset($values['group_identifier']) && _likebtn_get_option($use_entity_name, 'likebtn_voting_one', $values)) {
        $values['group_identifier'] = $entity_name;
    }
    

    $likebtn_settings = _likebtn_get_all_settings();
    foreach ($likebtn_settings as $option_name => $option_info) {

        if ($values && isset($values[$option_name])) {
            // if values passed
            $option_value = $values[$option_name];
        } elseif (!$use_entity_settings && !in_array($option_name, $likebtn_settings_deprecated)) {
            // Do not use entity value - use default. Usually in shortcodes.
            $option_value = $option_info['default'];
        } else {
            $option_value = get_option('likebtn_settings_' . $option_name . '_' . $use_entity_name);
        }

        $option_value_prepared = _likebtn_prepare_option($option_name, $option_value);
        $prepared_settings[$option_name] = $option_value_prepared;

        // do not add option if it has default value
        if ((isset($likebtn_settings[$option_name]['default']) && $option_value == $likebtn_settings[$option_name]['default']) ||
            //$option_value === '' || is_bool($option_value)
            ($option_value === '' && (isset($likebtn_settings[$option_name]['default']) && $likebtn_settings[$option_name]['default'] == '0'))
        ) {
            // option has default value
        } else {
            // Some options need extra procession
            if ($option_name == 'event_handler') {
                $option_name = 'custom_eh';
            }
            if ($option_name == 'lang' && $option_value_prepared == 'auto') {
                $option_value_prepared = likebtn_cur_lang();
            }
            $data .= ' data-' . $option_name . '="' . $option_value_prepared . '" ';
        }
    }

    // Add item options
    if ($entity_url && !$prepared_settings['item_url']) {
        $data .= ' data-item_url="' . $entity_url . '" ';
    }
    if ($entity_title && !$prepared_settings['item_title']) {
        $entity_title = strip_shortcodes($entity_title);
        $entity_title = preg_replace('/\s+/', ' ', $entity_title);
        $entity_title = htmlspecialchars($entity_title);

        $data .= ' data-item_title="' . $entity_title . '" ';
    }
    if ($entity_image && !$prepared_settings['item_image']) {
        $data .= ' data-item_image="' . $entity_image . '" ';
    }
    if ($entity_date && !$prepared_settings['item_date']) {
        $data .= ' data-item_date="' . $entity_date . '" ';
    }

    // User vote type
    if (_likebtn_get_option($use_entity_name, 'likebtn_voter_by', $values) == LIKEBTN_VOTER_BY_USER) {
        $vt = _likebtn_get_vote_type($identifier, $prepared_settings['voting_frequency']);
        if ($vt) {
            $data .= ' data-vt="'.$vt.'" ';
        }
    }

    // Set engine and plugin info
    $data .= ' data-engine="WordPress" ';
    //$data .= ' data-engine_v="' . $wp_version . '" ';
    $plugin_v = LIKEBTN_VERSION;
    if ($plugin_v) {
        $data .= ' data-plugin_v="' . $plugin_v . '" ';
    }

    if (get_option('likebtn_acc_data_correct') == '1') {
        // Proxy
        $prx = admin_url('admin-ajax.php').'?action=likebtn_prx';

        // Pass mapped entity name to distinguish between BBP profile and BP member
        if (in_array($entity_name, array_keys($likebtn_map_entities))) {
            $prx .= '&wpen=' . $entity_name;
        }

        $data .= ' data-prx="' . $prx . '" ';
    }
    // Event handler
    $data .= ' data-event_handler="likebtn_eh" ';

    // Infor essages
    if (get_option('likebtn_info_message') != '1') {
        $data .= ' data-info_message="0" ';
    }

    $public_url = _likebtn_get_public_url();

    $widget_url = LIKEBTN_WIDGET_URL;
    if ($include_script) {
        $markup = <<<MARKUP
<!-- LikeBtn.com BEGIN --><span class="likebtn-wrapper" {$data}></span><script>(function(d, e, s) {a = d.createElement(e);m = d.getElementsByTagName(e)[0];a.async = 1;a.src = s;m.parentNode.insertBefore(a, m)})(document, 'script', '//{$widget_url}'); if (typeof(LikeBtn) != "undefined") { LikeBtn.init(); }</script><!-- LikeBtn.com END -->
MARKUP;
    } else {
        $markup = <<<MARKUP
<!-- LikeBtn.com BEGIN --><span class="likebtn-wrapper" {$data}></span><!-- LikeBtn.com END -->
MARKUP;
    }

    // HTML before
    $html_before = '';
    if (isset($values['html_before'])) {
        $html_before = $values['html_before'];
    } elseif (get_option('likebtn_html_before_' . $use_entity_name)) {
        $html_before = get_option('likebtn_html_before_' . $use_entity_name);
    }
    $html_before = apply_filters('likebtn_html_before', $html_before, $entity_name, $entity_id, $values);
    $markup = $html_before . $markup;


    // HTML after
    $html_after = '';
    if (isset($values['html_after'])) {
        $html_after = $values['html_after'];
    } elseif (get_option('likebtn_html_after_' . $use_entity_name)) {
        $html_after = get_option('likebtn_html_after_' . $use_entity_name);
    }

    $html_after = apply_filters('likebtn_html_after', $html_after, $entity_name, $entity_id, $values);
    $markup = $markup . $html_after;

    if (($wrap || !empty($values['wrap']) || !empty($values['alignment']) || !empty($values['newline'])) && !is_admin()) {

        $likebtn_wrap = _likebtn_get_option($use_entity_name, 'likebtn_wrap', $values);

        if ($likebtn_wrap) {
            $alignment = _likebtn_get_option($use_entity_name, 'likebtn_alignment', $values);
            $newline = _likebtn_get_option($use_entity_name, 'likebtn_newline', $values);

            $style = '';

            if ($newline == '1') {
                $style .= 'clear:both;';
            }

            if ($alignment == LIKEBTN_ALIGNMENT_RIGHT) {
                $style .= 'text-align:right;';
                $markup = '<div class="likebtn_container" style="'.$style.'">' . $markup . '</div>';
            } elseif ($alignment == LIKEBTN_ALIGNMENT_CENTER) {
                $style .= 'text-align:center;';
                $markup = '<div class="likebtn_container" style="'.$style.'">' . $markup . '</div>';
            } else {
                $markup = '<div class="likebtn_container" style="'.$style.'">' . $markup . '</div>';
            }
        }
    }

    // Like box
    if (($show_like_box || !empty($values['like_box'])) && !is_admin()) {
        $like_box = _likebtn_get_option($use_entity_name, 'likebtn_like_box', $values);

        if ($like_box) {
            $like_box_html = _likebtn_like_box($identifier, _likebtn_get_option($entity_name, 'likebtn_like_box_size', $values), _likebtn_get_option($entity_name, 'likebtn_like_box_text', $values), _likebtn_get_option($entity_name, 'likebtn_like_box_type', $values));
            if ($like_box == LIKEBTN_LIKE_BOX_BEFORE) {
                $markup = $like_box_html.$markup;
            } else {
                $markup = $markup.$like_box_html;
            }
        }
    }

    return $markup;
}

// prepare option value
function _likebtn_prepare_option($option_name, $option_value)
{
    global $likebtn_settings;

    $option_value_prepared = $option_value;

    // do not format i18n options
    if (!strstr($option_name, 'i18n') &&
       (!isset($likebtn_settings[$option_name]) || $likebtn_settings[$option_name]['default'] !== ''))
    {
        if (is_int($option_value)) {
            if ($option_value) {
                $option_value_prepared = 'true';
            } else {
                $option_value_prepared = 'false';
            }
        }
        if ($option_value === '1') {
            $option_value_prepared = 'true';
        }
        if ($option_value === '0' || $option_value === '') {
            $option_value_prepared = 'false';
        }
    }

    // Replace quotes with &quot; to avoid XSS.
    //$option_value_prepared = str_replace('"', '&quot;', $option_value_prepared);
    $option_value_prepared = htmlspecialchars($option_value_prepared);

    return $option_value_prepared;
}

// Get like box HTML
function _likebtn_like_box($identifier, $size = LIKEBTN_LIKE_BOX_SIZE, $text = '', $type = LIKEBTN_VOTE_LIKE)
{
    global $wpdb;
    $html = '';

    if (!$size) {
        $size = LIKEBTN_LIKE_BOX_SIZE;
    }

    // Type
    $type = (int)$type;

    $query_where = '';
    
    if ($type == LIKEBTN_VOTE_LIKE || $type === '') {
        $query_where = ' AND type = '.LIKEBTN_VOTE_LIKE;
    } elseif ($type == LIKEBTN_VOTE_DISLIKE) {
        $query_where = ' AND type = '.$type;
    } elseif ($type == LIKEBTN_VOTE_BOTH) {
        $query_where = '';
    }

    $query = "
        SELECT DISTINCT user_id
        FROM ".$wpdb->prefix.LIKEBTN_TABLE_VOTE."
        WHERE identifier = %s
            AND user_id is not NULL
            AND user_id != ''
            {$query_where} 
        ORDER BY created_at DESC
        LIMIT {$size}
    ";
    $query_prepared = $wpdb->prepare($query, $identifier);
    $vote_list = $wpdb->get_results($query_prepared);

    if (!count($vote_list)) {
        return '';
    }

    $user_loop = array();
    foreach ($vote_list as $vote) {
        $name = _likebtn_get_entity_title(LIKEBTN_ENTITY_USER, $vote->user_id);
        if (!$name) {
            continue;
        }
        if (function_exists('bp_core_get_user_domain')) {
            $url = bp_core_get_user_domain($vote->user_id);
        } else {
            $url = get_author_posts_url($vote->user_id);
        }
        $user_loop[] = array(
            'user_id' => $vote->user_id,
            'name'    => $name,
            'avatar'  => _likebtn_get_avatar_url($vote->user_id),
            'url'     => $url,
        );
    }

    // Get and include the template we're going to use
    ob_start();
    include(_likebtn_get_template_path(LIKEBTN_TEMPLATE_LIKE_BOX));
    $html = ob_get_contents();
    ob_get_clean();

    return $html;
}

// Get identifier for entity
function _likebtn_entity_to_identifier($entity_name, $entity_id)
{
    global $likebtn_map_entities;

    $identifier = $entity_name;
    if (!empty($likebtn_map_entities[$entity_name])) {
        $identifier = $likebtn_map_entities[$entity_name];
    }
    return $identifier . '_' . $entity_id;
}

// get Entity settings
function _likebtn_get_entity_settings($entity_name) {

    global $likebtn_settings;
    $settings = array();

    foreach ($likebtn_settings as $option_name => $option_info) {
        $settings[$option_name] = get_option('likebtn_settings_' . $option_name . '_' . $entity_name);
    }
    return $settings;
}

// add Like Button to content
function likebtn_get_content($content, $callback_content_position = '') {

    global $likebtn_no_excerpts;
    global $likebtn_global_disabled;

    $html = '';
    $values = array();

    // $real_entity_name = current entity name with list flag
    // $entity_name = settings from whih params are used

    // No like button in RSS
    if (is_feed()) {
        return $content;
    }

    // Disable of UltimateMember pages
    if (function_exists('is_ultimatemember') && is_ultimatemember()) {
        return $content;
    }

    // Like button has been disabled on this page
    if ($likebtn_global_disabled) {
        return $content;
    }

    // detemine entity type
    $real_entity_name = get_post_type();

    // Excerpt mode
    if ($real_entity_name == LIKEBTN_ENTITY_PAGE) {
        // page
        if (!is_page()) {
            $real_entity_name = $real_entity_name . LIKEBTN_LIST_FLAG;
        }
    } else {
        // everything else
        if (!is_single()) {
            if (!in_array($real_entity_name, $likebtn_no_excerpts)) {
                $real_entity_name = $real_entity_name . LIKEBTN_LIST_FLAG;
            }
        }
    }

    // get entity name whose settings should be copied
    $use_entity_name = likebtn_calc_use_settings_from($real_entity_name);
    if ($use_entity_name) {
        $entity_name = $use_entity_name;
    } else {
        $entity_name = $real_entity_name;
    }

    if (get_option('likebtn_show_' . $real_entity_name) != '1'
        || get_option('likebtn_show_' . $entity_name) != '1')
    {
        return $content;
    }

    $entity_id = likebtn_get_the_ID($entity_name);    

    // get the Posts/Pages IDs where we do not need to show like functionality
    $allow_ids = explode(",", get_option('likebtn_allow_ids_' . $entity_name));
    $exclude_ids = explode(",", get_option('likebtn_exclude_ids_' . $entity_name));
    $exclude_categories = get_option('likebtn_exclude_categories_' . $entity_name);
    $exclude_sections = get_option('likebtn_exclude_sections_' . $entity_name);

    if (empty($exclude_categories)) {
        $exclude_categories = array();
    }

    if (empty($exclude_sections)) {
        $exclude_sections = array();
    }

    // checking if section is excluded
    if ((in_array('home', $exclude_sections) && is_home()) || 
        (in_array('archive', $exclude_sections) && is_archive() && !is_category()) || 
        (in_array('search', $exclude_sections) && is_search()) || 
        (in_array('category', $exclude_sections) && is_category())
    ) {
        return $content;
    }

    // checking if category is excluded
    if (!empty($exclude_categories) && !in_array($entity_id, $allow_ids)) {
        $taxs = get_post_taxonomies($entity_id);
        $taxs = array_diff($taxs, array('post_tag', 'post_format'));

        if (count($taxs) == 1 && $taxs[0] == 'category') {
            $categories = get_the_category();
            if (is_array($categories)) {
                foreach ($categories as $category) {
                    if (in_array($category->cat_ID, $exclude_categories)) {
                        return $content;
                    }
                }
            }
        } else {
            if (is_array($taxs)) {
                foreach ($taxs as $taxonomy) {
                    $categories = get_the_terms($entity_id, $taxonomy);
                    if (is_array($categories)) {
                        foreach ($categories as $category) {
                            if (in_array($category->term_id, $exclude_categories)) {
                                return $content;
                            }
                        }
                    }
                }
            }
        }
    }

    // check if post is excluded
    if (in_array($entity_id, $exclude_ids)) {
        return $content;
    }

    // check Post format
    $post_format = get_post_format($entity_id);
    if (!$post_format) {
        $post_format = 'standard';
    }

    if (!in_array('all', get_option('likebtn_post_format_' . $entity_name)) &&
            !in_array($post_format, get_option('likebtn_post_format_' . $entity_name))
    ) {
        return $content;
    }

    // check user authorization
    $auth_check = _likebtn_auth_check($entity_name, $values);
    if ($auth_check['return']) {
        return $content;
    }
    $values = $auth_check['values'];

    $html = _likebtn_get_markup($real_entity_name, $entity_id, $values, $entity_name, true, true, false, true);

    if ($auth_check['user_logged_in_alert']) {
        if ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT) {
            $html = $auth_check['user_logged_in_alert'];
        } elseif ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT_BTN) {
            $html .= $auth_check['user_logged_in_alert'];
        }
    }

    $position = get_option('likebtn_position_' . $entity_name);

    if ($callback_content_position && function_exists($callback_content_position)) {
        $content = call_user_func($callback_content_position, $content, $html, $position);
    } else {
        if ($position == LIKEBTN_POSITION_TOP) {
            $content = $html . $content;
        } elseif ($position == LIKEBTN_POSITION_BOTTOM) {
            $content = $content . $html;
        } else {
            $content = $html . $content . $html;
        }
    }

    return $content;
}

// User authorization check
function _likebtn_auth_check($entity_name, $values)
{
    $result = array(
        'user_logged_in' => '',
        'user_logged_in_alert' => '',
        'values' => $values,
        'return' => false,
    );
    $result['user_logged_in'] = _likebtn_get_option($entity_name, 'likebtn_user_logged_in', $values);
    $result['user_logged_in_alert'] = '';

    switch ($result['user_logged_in']) {
        case LIKEBTN_USER_LOGGED_IN_YES:
            if (!is_user_logged_in()) {
                $result['return'] = true;
            }
            break;
        case LIKEBTN_USER_LOGGED_IN_NO:
            if (is_user_logged_in()) {
                $result['return'] = true;
            }
            break;
        case LIKEBTN_USER_LOGGED_IN_ALERT:
        case LIKEBTN_USER_LOGGED_IN_ALERT_BTN:
            if (!is_user_logged_in()) {
                $result['user_logged_in_alert'] = _likebtn_get_user_logged_in_alert($entity_name, $values);
                $result['values']['voting_enabled'] = '0';
            }
            break;
        case LIKEBTN_USER_LOGGED_IN_MODAL:
            if (!is_user_logged_in()) {
                $result['values']['voting_enabled'] = '0';
            }
            break;
        case LIKEBTN_USER_LOGGED_IN_ALL:
        default:
            // Show for all
            break;
    }

    return $result;
}

function _likebtn_get_user_logged_in_alert($entity_name, $values = array())
{
    global $user_logged_in_alert_default;

    $user_logged_in_alert = _likebtn_get_option($entity_name, 'likebtn_user_logged_in_alert', $values);
    if (!$user_logged_in_alert) {
        $user_logged_in_alert = '<p class="alert alert-info fade in" role="alert">'.__($user_logged_in_alert_default, 'likebtn-like-button').'</p>';
    }
    $user_logged_in_alert = strtr(
        __($user_logged_in_alert, 'likebtn-like-button'), 
        array('%url_login%' => wp_login_url(get_permalink()))
    );

    return $user_logged_in_alert;
}

// add Like Button to the entity (except Comment)
function likebtn_the_content($content) {
    // MultiFlex theme does not use Loop
    /*if (!in_the_loop()) {
        return $content;
    }*/
    if (_likebtn_has_shortcode($content, LIKEBTN_SHORTCODE_OFF)) {
        $content = _likebtn_remove_shortcode($content, LIKEBTN_SHORTCODE_OFF);
        return $content;
    }
    if (get_post_type() == LIKEBTN_ENTITY_PRODUCT) {
        return $content;
    }

    $content = likebtn_get_content($content);
    return $content;
}
add_filter('the_content', 'likebtn_the_content');
add_filter('the_excerpt', 'likebtn_the_content');

// WooCommerce - top and button
function likebtn_woocommerce_product($content) {
    $content = likebtn_get_content($content);
    echo $content;
}
// WooCommerce - top
function likebtn_woocommerce_product_top($content) {
    $content = likebtn_get_content($content, '_likebtn_woocommerce_content_top');
    echo $content;
}
function _likebtn_woocommerce_content_top($content, $html, $position) {
    // WooCommerce
    if ($position == LIKEBTN_POSITION_BOTTOM) {
        return $content;
    } else {
        if (is_string($content)) {
            return $html . $content;
        } else {
            return $content;
        }
    }
}
// WooCommerce - bottom
function likebtn_woocommerce_after_main_content($content) {
    if (!is_single()) {
        return false;
    }
    $content = likebtn_get_content($content, '_likebtn_woocommerce_content_bottom');
    echo $content;
}
function likebtn_woocommerce_product_bottom($content) {
    $content = likebtn_get_content($content, '_likebtn_woocommerce_content_bottom');
    echo $content;
}
function _likebtn_woocommerce_content_bottom($content, $html, $position) {
    // WooCommerce
    if ($position == LIKEBTN_POSITION_TOP) {
        return $content;
    } else {
        return $html . $content;
    }
}

// Init actions wchich can't be initited right away
function _likebtn_woocommerce_hooks()
{
    // To avoid displaying in Linked products
    if (function_exists('is_product') && !is_product()) {
        add_action('woocommerce_after_shop_loop_item_title', 'likebtn_woocommerce_product_top', 7);
        add_action('woocommerce_after_shop_loop_item_title', 'likebtn_woocommerce_product_bottom', 12);
    }
}

add_action('woocommerce_single_product_summary', 'likebtn_woocommerce_product_top', 7);
add_action('woocommerce_after_main_content', 'likebtn_woocommerce_after_main_content', 7);
// WooCommerce here is not defined yet
add_action('loop_start', '_likebtn_woocommerce_hooks');

// add Like Button to the Comment
function likebtn_comment_text($content, $comment_obj = null) {

    global $comment;

    if (defined('EPOCH_VER')) {
        if (is_object($comment_obj)) {
            $comment = $comment_obj;
        }
    } else {
        // Add like button only if comment is rendered from the list and not from sidebar widget
        // If Epoch is active continue
        if (!_likebtn_has_caller('wp_list_comments')) {
            return $content;
        }
    }

    if (is_feed()) {
        return $content;
    }

    // detemine entity type
    $real_entity_name = LIKEBTN_ENTITY_COMMENT;

    // get entity name whose settings should be copied
    $use_entity_name = likebtn_calc_use_settings_from($real_entity_name);
    if ($use_entity_name) {
        $entity_name = $use_entity_name;
    } else {
        $entity_name = $real_entity_name;
    }

    if (get_option('likebtn_show_' . $real_entity_name) != '1'
        || get_option('likebtn_show_' . $entity_name) != '1')
    {
        return $content;
    }

    $comment_id = $comment->comment_ID;
    //$comment = get_comment($comment_id, ARRAY_A);
    $post_id = $comment->comment_post_ID;

    // get the Posts/Pages IDs where we do not need to show like functionality
    $allow_ids = explode(",", get_option('likebtn_allow_ids_' . $entity_name));
    $exclude_ids = explode(",", get_option('likebtn_exclude_ids_' . $entity_name));
    $exclude_categories = get_option('likebtn_exclude_categories_' . $entity_name);
    $exclude_sections = get_option('likebtn_exclude_sections_' . $entity_name);

    if (empty($exclude_categories)) {
        $exclude_categories = array();
    }

    if (empty($exclude_sections)) {
        $exclude_sections = array();
    }

    // checking if section is excluded
    if ((in_array('home', $exclude_sections) && is_home()) || (in_array('archive', $exclude_sections) && is_archive())) {
        return $content;
    }

    // checking if category is excluded
    $categories = get_the_category();
    foreach ($categories as $category) {
        if (in_array($category->cat_ID, $exclude_categories) && !in_array($post_id, $allow_ids)) {
            return $content;
        }
    }

    // check if post is excluded
    if (in_array($post_id, $exclude_ids)) {
        return $content;
    }

    // check Post view mode - no need
    // check Post format
    $post_format = get_post_format($post_id);
    if (!$post_format) {
        $post_format = 'standard';
    }

    if (!in_array('all', get_option('likebtn_post_format_' . $entity_name)) &&
            !in_array($post_format, get_option('likebtn_post_format_' . $entity_name))
    ) {
        return $content;
    }

    $auth_check = _likebtn_auth_check($entity_name, array());

    if ($auth_check['return']) {
        return $content;
    }
    $values = $auth_check['values'];

    $html = _likebtn_get_markup($real_entity_name, $comment_id, $values, $entity_name, true, true, false, true);

    if ($auth_check['user_logged_in_alert']) {
        if ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT) {
            $html = $auth_check['user_logged_in_alert'];
        } elseif ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT_BTN) {
            $html .= $auth_check['user_logged_in_alert'];
        }
    }

    $position = get_option('likebtn_position_' . $entity_name);

    if ($position == LIKEBTN_POSITION_TOP) {
        $content = $html . $content;
    } elseif ($position == LIKEBTN_POSITION_BOTTOM) {
        $content = $content . $html;
    } else {
        $content = $html . $content . $html;
    }

    return $content;
}

add_filter('comment_text', 'likebtn_comment_text', 10, 2);

// Disable like button on current
function likebtn_disable() {
    global $likebtn_global_disabled;
    $likebtn_global_disabled = true;
}

// show the Like Button in Post/Page
// if Like Button is enabled in admin for Post/Page do not show button twice
function likebtn_post($post_id = null, $values = null) {
    global $post;
    if (empty($post_id)) {
        $post_id = $post->ID;
    }

    // detemine entity type
    if (!empty($post->post_type)) {
        $entity_name = $post->post_type;
    } else {
        $entity_name = LIKEBTN_ENTITY_POST;
    }

    // 'post' here is for the sake of backward compatibility
    $html = _likebtn_get_markup($entity_name, $post_id, $values);

    echo $html;
}

// get or echo the Like Button in comment
function likebtn_comment($comment_id = NULL, $values = null) {
    //global $comment;
    if (empty($comment_id)) {
        $comment_id = get_comment_ID();
    }

    $html = _likebtn_get_markup(LIKEBTN_ENTITY_COMMENT, $comment_id, $values);

    echo $html;
}

// Show the Like Button in WooCommerce Product
// if Like Button is enabled in admin do not show button twice
function likebtn_woocommerce($post_id = NULL, $values = null) {
    global $post;

    if (empty($post_id)) {
        $post_id = $post->ID;
    }

    $html = _likebtn_get_markup(LIKEBTN_ENTITY_PRODUCT, $post_id, $values);

    echo $html;
}

// test synchronization callback
function likebtn_manual_sync_callback() {

    $likebtn_account_email = '';
    if (isset($_POST['likebtn_account_email'])) {
        $likebtn_account_email = sanitize_email($_POST['likebtn_account_email']);
    }

    $likebtn_account_api_key = '';
    if (isset($_POST['likebtn_account_api_key'])) {
        $likebtn_account_api_key = trim(preg_replace("/[^0-9a-zA-Z]/", '', $_POST['likebtn_account_api_key']));
    }

    $likebtn_site_id = '';
    if (isset($_POST['likebtn_site_id'])) {
        $likebtn_site_id = trim(preg_replace("/[^0-9a-zA-Z]/", '', $_POST['likebtn_site_id']));
    }

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();
    $sync_response = $likebtn->syncVotes($likebtn_account_email, $likebtn_account_api_key, $likebtn_site_id, true);

    if ($sync_response['result'] == 'success') {
        $result_text = __('OK', 'likebtn-like-button');
    } else {
        $result_text = __('Error', 'likebtn-like-button');
    }

    $response = array(
        'result' => $sync_response['result'],
        'result_text' => $result_text,
        'message' => $sync_response['message'],
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_manual_sync', 'likebtn_manual_sync_callback');

// System check
function likebtn_system_check() {
    global $likebtn_system_check;

    $response = array(
        'result' => 'success',
        'result_text' => ''
    );

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();

    $index = 1;
    foreach ($likebtn_system_check as $addr) {
        $add_response = $likebtn->curl($addr);
        $addr_result = json_decode($add_response, true);

        if (is_array($addr_result)) {
            if ($addr_result['result'] == 'error' && !isset($addr_result['response'])) {
                $response['result'] = 'error';
                $response['result_text'] .= ' '.$index.') '.strtr(
                    __('%addr% is not available from your server', 'likebtn-like-button'), 
                    array(
                        '%addr%' => $addr
                    )
                );
                if ($addr_result['message']) {
                    $response['result_text'] .= ': '.$addr_result['message'];
                }
                //$response['result_text'] .= "<br/>";
                $index++;
            }
        } else {
            // Proxy error for example
            if (is_string($add_response) && strstr($add_response, '<html>')) {
                $response['result_html'] = $add_response;
                $response['result'] = 'error';
                $response['result_text'] .= ' '.$index.') '.strtr(
                    __('%addr% is not available from your server', 'likebtn-like-button'), 
                    array(
                        '%addr%' => $addr
                    )
                );
            }
        }
    }

    if ($response['result'] != 'error') {
        $response['result_text'] = __('Everything seems to be in order', 'likebtn-like-button');
    }

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_system_check', 'likebtn_system_check');

// Send test vote notification
function likebtn_test_vote_notification() {
    
    $response = array(
        'result' => 'success',
        'result_text' => ''
    );

    $current_user = wp_get_current_user();
    $vars = array(
        'vote_type' => likebtn_vote_title(LIKEBTN_VOTE_LIKE),
        'domain' => likebtn_site_domain(),
        'item_type' => _likebtn_get_entity_name_title(LIKEBTN_ENTITY_POST, true, false),
        'item_url' => get_option('siteurl'),
        'item_title' => __("Test non-existent post", 'likebtn-like-button'),
        'item_likes' => 7,
        'item_dislikes' => 1,
        'item_admin_stats_url' => admin_url() . 'admin.php?page=likebtn_statistics',
        'vote_ip' => _likebtn_get_ip(),
        'vote_ip_url' => admin_url() . 'admin.php?page=likebtn_votes&likebtn_ip='._likebtn_get_ip().'&show=View',
        'user_url' => _likebtn_get_entity_url(LIKEBTN_ENTITY_USER, $current_user->ID),
        'user_login' => $current_user->user_login,
        'user_votes_url' => admin_url() . 'admin.php?page=likebtn_votes&likebtn_user_id='.$current_user->ID.'&show=View',
    );

    $result = likebtn_send_vote_notification($vars, stripslashes_deep($_POST['options']));

    if ($response['result'] != 'error') {
        $response['result_text'] = __("Notification successfully sent, don't forget to save settings", 'likebtn-like-button');
    }

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}
add_action('wp_ajax_likebtn_test_vote_notification', 'likebtn_test_vote_notification');

// test synchronization callback
function likebtn_test_sync_callback() {

    $likebtn_account_email = '';
    if (isset($_POST['likebtn_account_email'])) {
        $likebtn_account_email = sanitize_email($_POST['likebtn_account_email']);
    }
    
    $likebtn_account_api_key = '';
    if (isset($_POST['likebtn_account_api_key'])) {
        $likebtn_account_api_key = trim(preg_replace("/[^0-9a-zA-Z]/", '', $_POST['likebtn_account_api_key']));
    }

    $likebtn_site_id = '';
    if (isset($_POST['likebtn_site_id'])) {
        $likebtn_site_id = trim(preg_replace("/[^0-9a-zA-Z]/", '', $_POST['likebtn_site_id']));
    }

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();
    $test_response = $likebtn->testSync($likebtn_account_email, $likebtn_account_api_key, $likebtn_site_id);

    if ($test_response['result'] == 'success') {

        if (get_option('likebtn_sync_inerval')) {
            $result_text = __('OK', 'likebtn-like-button');
        } else {
            $result_text = __('Enabled', 'likebtn-like-button');
        }
        update_option('likebtn_sync_inerval', 5);
    } else {
        $result_text = __('Error', 'likebtn-like-button');
    }

    $response = array(
        'result' => $test_response['result'],
        'result_text' => $result_text,
        'message' => $test_response['message'],
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_test_sync', 'likebtn_test_sync_callback');

// test synchronization callback
function likebtn_check_account_callback() {

    $likebtn_account_email = '';
    if (isset($_POST['likebtn_account_email'])) {
        $likebtn_account_email = sanitize_email($_POST['likebtn_account_email']);
    }

    $likebtn_account_api_key = '';
    if (isset($_POST['likebtn_account_api_key'])) {
        $likebtn_account_api_key = trim(preg_replace("/[^0-9a-zA-Z]/", '', $_POST['likebtn_account_api_key']));
    }

    $likebtn_site_id = '';
    if (isset($_POST['likebtn_site_id'])) {
        $likebtn_site_id = trim(preg_replace("/[^0-9a-zA-Z]/", '', $_POST['likebtn_site_id']));
    }

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();
    $test_response = $likebtn->checkAccount($likebtn_account_email, $likebtn_account_api_key, $likebtn_site_id);

    if ($test_response['result'] == 'success') {
        $result_text = __('OK', 'likebtn-like-button').' ('.__('Don\'t forget to click "Save Changes"', 'likebtn-like-button').')';
    } else {
        $result_text = __('Error', 'likebtn-like-button');
    }

    $response = array(
        'result' => $test_response['result'],
        'result_text' => $result_text,
        'message' => $test_response['message'],
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_check_account', 'likebtn_check_account_callback');

// force plan sync on upgrade
function likebtn_force_plan_sync_callback() {

    update_option('likebtn_last_plan_sync_time', 0);

    $response = array(
        'result' => 'success'
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response, 'callback');
}

add_action('wp_ajax_nopriv_likebtn_force_plan_sync', 'likebtn_force_plan_sync_callback');

// edit item callback
function likebtn_edit_item_callback() {

    $entity_name = '';
    if (isset($_POST['entity_name'])) {
        // may contain any text
        $entity_name = sanitize_text_field($_POST['entity_name']);
    }

    $entity_id = '';
    if (isset($_POST['entity_id'])) {
        // may contain any text
        $entity_id = sanitize_text_field($_POST['entity_id']);
    }

    $identifier = likebtn_get_identifier($entity_name, $entity_id);

    $type = '';
    if (isset($_POST['type'])) {
        $type = sanitize_text_field($_POST['type']);
        if (!in_array($type, array(LIKEBTN_VOTE_LIKE, LIKEBTN_VOTE_DISLIKE))) {
            $type = '';
        }
    }

    $value = '';
    if (isset($_POST['value'])) {
        $value = abs($_POST['value']);
    }

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();
    $edit_response = $likebtn->edit($identifier, $type, $value);

    if ($edit_response['result'] == 'success') {
        $result_text = __('OK', 'likebtn-like-button');

        // Determine votes to count Likes minus dislikes
        $votes = _likebtn_get_item_votes($identifier);

        // Update custom fields
        if ($type == '1') {
            $likes = abs($value);
            $dislikes = (int)$votes[LIKEBTN_META_KEY_DISLIKES];
        } else {
            $dislikes = abs($value);
            $likes = (int)$votes[LIKEBTN_META_KEY_LIKES];
        }
        $likebtn->updateCustomFields($identifier, $likes, $dislikes);
    } else {
        $result_text = __('Error', 'likebtn-like-button');
    }

    $response = array(
        'result' => $edit_response['result'],
        'result_text' => $result_text,
        'message' => $edit_response['message'],
        'value' => $value
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_edit_item', 'likebtn_edit_item_callback');

// Refresh plan callback
function likebtn_refresh_plan_callback() {
    $html = '';
    $message = '';

    // run sunchronization
    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();
    $refresh_response = $likebtn->syncPlan();

    if (empty($refresh_response['message'])) {
        $html = _likebtn_plan_html();
    }

    if (!empty($refresh_response['message'])) {
        $message = $refresh_response['message'];
    }

    $response = array(
        'html' => $html,
        'message' => $message,
        'reload' => (int)get_option('likebtn_notice_plan'),
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_refresh_plan', 'likebtn_refresh_plan_callback');

// Refresh plan callback
function likebtn_go_free_callback() {
    $html = '';
    $message = '';

    // run sunchronization
    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();
    $refresh_response = $likebtn->goFree();

    if (empty($refresh_response['message'])) {
        $html = _likebtn_plan_html();
    }

    if (!empty($refresh_response['message'])) {
        $message = $refresh_response['message'];
    }

    $response = array(
        'html' => $html,
        'message' => $message,
        'reload' => (int)get_option('likebtn_notice_plan'),
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_go_free', 'likebtn_go_free_callback');

// Get IP vote interval
function likebtn_ipvi_get_callback() {
    global $likebtn_plans;

    $result = 'success';
    $message = '';
    $value = -1;

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();
    $api_response = $likebtn->getIpvi();

    if (empty($api_response['message']) && isset($api_response['response']['value']) && (int)$api_response['response']['value'] >= 0) {
        $value = (int)$api_response['response']['value'];
        update_option('likebtn_ipvi', $value);
    }

    if (!empty($api_response['message'])) {
        $message = $api_response['message'];
        $result = 'error';
    }

    $response = array(
        'result' => $result,
        'value' => $value,
        'message' => $message,
    );

    if (isset($api_response['response']['plan']) && isset($likebtn_plans[(int)$api_response['response']['plan']])) {
        if ((int)$api_response['response']['plan'] < LIKEBTN_PLAN_ULTRA) {
            $response['result'] = 'error';
            $response['message'] = strtr(__('Your current plan %plan% does not allow to change IP vote interval - please upgrade to ULTRA plan.', 'likebtn-like-button'), array('%plan%'=>$likebtn_plans[(int)$api_response['response']['plan']]));
        }
        if ((int)$api_response['response']['plan'] != get_option('likebtn_plan')) {
            update_option('likebtn_plan', (int)$api_response['response']['plan']);
        }
    }

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_ipvi_get', 'likebtn_ipvi_get_callback');

function likebtn_plugin_feedback_callback() {
    $response = array(
        'result' => 'error',
        'error_message' => '',
    );

    try {
        $http = new WP_Http();
        $response = $http->post(LIKEBTN_WEBSITE_AJAX, array('body'=>$_POST));
    } catch (Exception $e) {
        if (is_admin()) {
            $response['error_message'] = $e->getMesssage();
        }
    }

    if (!is_wp_error($response) && isset($response['result'])) {
        $result = $response['result'];
    }

    update_option('likebtn_feedback_sent', 1);

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($response);
}

add_action('wp_ajax_likebtn_plugin_feedback', 'likebtn_plugin_feedback_callback');

// Export statistics
function likebtn_export_callback() {
    
    list($statistics, $likebtn_entities, $likebtn_post_statuses, $entity_name, $sort_by, $sort_by_order, $page_size, $post_id, $post_title, $post_status, $p, $blogs, $statistics_blog_id) = likebtn_stats();

    // No sanitizing needed, it contains array which is checked using switch()
    $post_fields = $_POST['fields'];
    $fields = array();

    foreach ($post_fields as $key => $value) {
        switch ($value) {
            case 'id':
                $fields[] = __('ID', 'likebtn-like-button');
                break;
            case 'title':
                $fields[] = __('Title', 'likebtn-like-button');
                break;
            case 'url':
                $fields[] = __('URL', 'likebtn-like-button');
                break;
            case 'likes':
                $fields[] = __('Likes', 'likebtn-like-button');
                break;
            case 'dislikes':
                $fields[] = __('Dislikes', 'likebtn-like-button');
                break;
            case 'likes_minus_dislikes':
                $fields[] = __('Likes minus dislikes', 'likebtn-like-button');
                break;
            case 'ips':
                $fields[] = __('Voters IPs', 'likebtn-like-button');
                break;
        }
    }

    $results = array();
    foreach ($statistics as $value) {
        $new_row = array();
        if (in_array('id', $post_fields)) {
            $new_row[] = $value->post_id;
        }
        if (in_array('title', $post_fields)) {
            $new_row[] = $value->post_title;
        }
        if (in_array('url', $post_fields)) {
            $new_row[] = _likebtn_get_entity_url($entity_name, $value->post_id, $value->url, $statistics_blog_id);;
        }
        if (in_array('likes', $post_fields)) {
            $new_row[] = $value->likes;
        }
        if (in_array('dislikes', $post_fields)) {
            $new_row[] = $value->dislikes;
        }
        if (in_array('likes_minus_dislikes', $post_fields)) {
            $new_row[] = $value->likes_minus_dislikes;
        }
        if (in_array('ips', $post_fields)) {
            $new_row[] = likebtn_get_voters_ips($entity_name, $value->post_id);
        }
        $results[] = $new_row;
    }

    $filename = 'likebtn_stats_'.date('Y-m-d').'.csv';
    likebtn_to_csv($results, $filename, $fields, sanitize_text_field($_POST['encoding']), sanitize_text_field($_POST['separator']));
}

add_action('wp_ajax_likebtn_export', 'likebtn_export_callback');

// Export statistics
function likebtn_export_votes_callback() {
    
    global $wpdb;

    list($query_prepared, $blogs, $votes_blog_id, $entity_name, $post_id, $user_id, $ip, $vote_type, $country) = likebtn_votes_query();
    $votes = $wpdb->get_results($query_prepared);

    // No sanitizing needed, it contains array which is checked using switch()
    $post_fields = $_POST['fields'];
    $fields = array();

    foreach ($post_fields as $key => $value) {
        switch ($value) {
            case 'user':
                $fields[] = __('User Name', 'likebtn-like-button');
                break;
            case 'user_email':
                $fields[] = __('User Email', 'likebtn-like-button');
                break;
            case 'ip':
                $fields[] = __('IP', 'likebtn-like-button');
                break;
            case 'country':
                $fields[] = __('Country', 'likebtn-like-button');
                break;
            case 'date':
                $fields[] = __('Date', 'likebtn-like-button');
                break;
            case 'type':
                $fields[] = __('Vote type', 'likebtn-like-button');
                break;
            case 'item_id':
                $fields[] = __('Item ID', 'likebtn-like-button');
                break;
            case 'item_title':
                $fields[] = __('Item Title', 'likebtn-like-button');
                break;
            case 'item_url':
                $fields[] = __('Item URL', 'likebtn-like-button');
                break;
            case 'item_type':
                $fields[] = __('Item Type', 'likebtn-like-button');
                break;
        }
    }

    $results = array();
    foreach ($votes as $value) {
        $new_row = array();
        if (in_array('user', $post_fields)) {
            $new_row[] = _likebtn_get_entity_title(LIKEBTN_ENTITY_USER, $value->user_id);
        }
        if (in_array('user_email', $post_fields)) {
            $user_email = '';
            if (!empty($value->user_id)) {
                $user_info = get_userdata($value->user_id);
                if ($user_info) {
                    $user_email = $user_info->user_email;
                }
            }
            $new_row[] = $user_email;
        }
        if (in_array('ip', $post_fields)) {
            $new_row[] = $value->ip;
        }
        if (in_array('country', $post_fields)) {
            $new_row[] = $value->country;
        }
        if (in_array('date', $post_fields)) {
            $new_row[] = date("Y.m.d H:i:s", strtotime($value->created_at));
        }
        if (in_array('type', $post_fields)) {
            if ((int)$value->type == 1) {
                $entity_vote_type = 'like';
            } else {
                $entity_vote_type = 'dislike';
            }
            $new_row[] = $entity_vote_type;
        }
        if (in_array('item_id', $post_fields) || in_array('item_title', $post_fields) 
            || in_array('item_url', $post_fields) || in_array('item_type', $post_fields)
        ) {
            $entity_name = '';
            $entity_id = '';
            $item_title = '';
            $item_url = '';

            if ($value->item_id) {
                // Custom item
                $item_title       = $value->identifier;
                $item_url         = $votes_item->url;
                $entity_type_name = __('Custom Item', 'likebtn-like-button');
                $entity_name      = LIKEBTN_ENTITY_CUSTOM_ITEM;
            } else {
                $entity_info = _likebtn_parse_identifier($value->identifier);
                if ($entity_info['entity_name'] && $entity_info['entity_id']) {
                    $entity_name = $entity_info['entity_name'];
                    $entity_id   = $entity_info['entity_id'];
                    $item_title  = _likebtn_get_entity_title($entity_info['entity_name'], $entity_info['entity_id']);
                    $item_url    = _likebtn_get_entity_url($entity_info['entity_name'], $entity_info['entity_id'], '', $votes_blog_id);
                }
                $entity_type_name = _likebtn_get_entity_name_title($entity_info['entity_name']);
            }
            if (in_array('item_id', $post_fields)) {
                $new_row[] = $entity_id;
            }
            if (in_array('item_title', $post_fields)) {
                $new_row[] = $item_title;
            }
            if (in_array('item_url', $post_fields)) {
                $new_row[] = $item_url;
            }
            if (in_array('item_type', $post_fields)) {
                $new_row[] = $entity_type_name;
            }
        }
        $results[] = $new_row;
    }

    $filename = 'likebtn_votes_'.date('Y-m-d').'.csv';
    likebtn_to_csv($results, $filename, $fields, sanitize_text_field($_POST['encoding']), sanitize_text_field($_POST['separator']));
}

add_action('wp_ajax_likebtn_export_votes', 'likebtn_export_votes_callback');

function likebtn_vgaph_callback() {
    
    global $wpdb;

    $vote_list = array();
    $graph = array(
        't' => array(),
        'l' => array(),
        'd' => array()
    );
    $error_message = '';

    $level = 0;
    if (isset($_GET['level'])) {
        $level = (int)$_GET['level'];
    }
    $timestamp = '';
    if (isset($_GET['timestamp'])) {
        $timestamp = preg_replace("/[^0-9]/", '', $_GET['timestamp']);
    }

    $query_select = " SUM(CASE WHEN v.type = 1 THEN 1 ELSE 0 END) as l, SUM(CASE WHEN v.type = -1 THEN 1 ELSE 0 END) as d ";
    $query_where = '';
    $query_orderby = '';

    try {

        switch ($level) {
            case LIKEBTN_VG_LEVEL_TOP:
                $query_select .= ", DATE(v.created_at) as vdate ";           
                break;

            case LIKEBTN_VG_LEVEL_DAY:
                $query_select .= ", HOUR(v.created_at) as vdate ";

                $timestamp_from = $timestamp/1000;
                $query_where .= " AND v.created_at >= '".date("Y-m-d H:i:s", $timestamp_from)."' ";

                $timestamp_to = $timestamp_from+24*60*60;
                $query_where .= " AND v.created_at <= '".date("Y-m-d H:i:s", $timestamp_to)."' ";
                break;

            case LIKEBTN_VG_LEVEL_HOUR:
                $query_select .= ", MINUTE(v.created_at) as vdate ";

                $timestamp_from = strtotime(date('Y-m-d H:00:00', $timestamp/1000));
                $query_where .= " AND v.created_at >= '".date("Y-m-d H:i:s", $timestamp_from)."' ";

                $timestamp_to = $timestamp_from+60*60;
                $query_where .= " AND v.created_at <= '".date("Y-m-d H:i:s", $timestamp_to)."' ";
                break;
        }
        $query_orderby = " 
            GROUP BY
                vdate
            ORDER BY
                v.created_at
        ";

        list($query_prepared) = likebtn_votes_query('', $query_select, $query_orderby, $query_where);

        $vote_list = $wpdb->get_results($query_prepared, ARRAY_A);

    } catch (\Exception $e) {
        // Return empty JSON
        //echo $e->getMessage();
    }

    // Build intermediate array
    $values = array();

    foreach ($vote_list as $vote) {
        switch ($level) {
            case LIKEBTN_VG_LEVEL_TOP:
                $timestamp = strtotime($vote['vdate'])*1000;
                break;
            case LIKEBTN_VG_LEVEL_DAY:
                $timestamp = ($timestamp_from + ($vote['vdate'])*60*60)*1000;
                break;
            case LIKEBTN_VG_LEVEL_HOUR:
                $timestamp = ($timestamp_from + $vote['vdate']*60)*1000;
                break;
        }
        $values[$timestamp] = $vote;
    }
    unset($vote_list);
    // Sort by date
    ksort($values);
    foreach ($values as $timestamp => $value) {
        $graph['t'][] = array($timestamp, (int)$value['l'] + (int)$value['d']);
        $graph['l'][] = array($timestamp, (int)$value['l']);
        $graph['d'][] = array($timestamp, (int)$value['d']);
    }
    unset($values);

    $json_response = array(
        'data' => $graph,
        'error_message' => $error_message
    );

    if (!DOING_AJAX) {
        define('DOING_AJAX', true);
    }
    if (ob_get_contents()) {
        ob_clean();
    }
    _likebtn_send_json($json_response);
}

add_action('wp_ajax_likebtn_vgaph', 'likebtn_vgaph_callback');

// reset items likes/dislikes
function _likebtn_reset($entity_name, $items)
{
    $counter = 0;

    if (empty($entity_name) || empty($items)) {
        return false;
    }

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();

    // prepare an array for resettings in the CMS
    $reset_list = array();
    $reset_list['response'] = array('items'=>array());

    foreach ($items as $item_identifier) {
        if ($entity_name != LIKEBTN_ENTITY_CUSTOM_ITEM) {
            $identifier = $entity_name . '_' . $item_identifier;
        } else {
            $identifier = $item_identifier;
        }
        // reset votes in LikeBtn
        $likebtn_result = $likebtn->reset($identifier);
        $reset_list['response']['items'][] = array(
            'identifier' => $identifier,
            'likes'      => 0,
            'dislikes'   => 0
        );
        if ($likebtn_result) {
            $counter++;
        }
    }

    if ($reset_list) {
        $likebtn->updateVotes($reset_list);
    }
    return $counter;
}

// delete items
function _likebtn_delete($entity_name, $items)
{
    $counter = 0;

    if (empty($entity_name) || empty($items)) {
        return false;
    }

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();

    // prepare an array for resettings in the CMS
    $list = array();
    $list['response'] = array('items'=>array());

    foreach ($items as $item_identifier) {
        if ($entity_name != LIKEBTN_ENTITY_CUSTOM_ITEM) {
            $identifier = $entity_name . '_' . $item_identifier;
        } else {
            $identifier = $item_identifier;
        }
        // delete votes in LikeBtn
        $likebtn_result = $likebtn->delete($identifier);
        $list['response']['items'][] = array(
            'identifier' => $identifier
        );
        if ($likebtn_result) {
            $counter++;
        }
    }

    if ($list) {
        $likebtn->deleteVotes($list);
    }
    return $counter;
}

// Reset votes & stats
function likebtn_full_reset_run()
{
    if (!empty($_POST['likebtn_full_reset']) && $_POST['likebtn_full_reset'] == 'tZFWPdFC') {
        likebtn_full_reset();
    }
}

function likebtn_full_reset()
{
    global $wpdb;

    require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
    $likebtn = new LikeBtnLikeButton();

    $result = $likebtn->fullReset();

    if ($result > 0) {
        $query_del_votes = "DELETE FROM ".$wpdb->prefix.LIKEBTN_TABLE_VOTE;
        $wpdb->query($query_del_votes);

        $query_del_items = "DELETE FROM ".$wpdb->prefix.LIKEBTN_TABLE_ITEM;
        $wpdb->query($query_del_items);

        // Remove metas
        likebtn_delete_metas();
        
        _likebtn_add_notice(array(
            'msg' => __('Your website votes and stats have been reset.', 'likebtn-like-button'),
        ));
    } else {
        _likebtn_add_notice(array(
            'msg' => __('Error occured resettings votes and stats. Make sure to enter valid account data on the Settings tab.', 'likebtn-like-button'),
            'class' => 'error'
        ));
    }
}

// Detele LikeBtn metas from all entities
function likebtn_delete_metas()
{
    global $wpdb;

    $where = " WHERE meta_key = '".LIKEBTN_META_KEY_LIKES."' OR meta_key = '".LIKEBTN_META_KEY_DISLIKES."' OR meta_key = '".LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES."' ";

    // Posts
    $query = "DELETE FROM {$wpdb->prefix}postmeta {$where}";
    $wpdb->query($query);

    // Comments
    $query = "DELETE FROM {$wpdb->prefix}commentmeta {$where}";
    $wpdb->query($query);

    // Users
    $query = "DELETE FROM {$wpdb->prefix}usermeta {$where}";
    $wpdb->query($query);

    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}bp_activity_meta'") == "{$wpdb->prefix}bp_activity_meta") {
        // BuddyPress activity
        $query = "DELETE FROM {$wpdb->prefix}bp_activity_meta {$where}";
        $wpdb->query($query);
    }
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}bp_xprofile_meta'") == "{$wpdb->prefix}bp_xprofile_meta") {
        // BuddyPress profiles
        $query = "DELETE FROM {$wpdb->prefix}bp_xprofile_meta {$where}";
        $wpdb->query($query);
    }
}

// get entity identifier
function likebtn_get_identifier($entity_name, $entity_id)
{
    if ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM) {
        $identifier = $entity_id;
    } else {
        $identifier = $entity_name . '_' . $entity_id;    
    }
    
    return $identifier;
}

// get comments sorted by likes
function likebtn_comments_sorted_by_likes()
{
    // function for sorting comments by Likes
    function sort_comments_by_likes($comment_a, $comment_b)
    {
        $comment_a_meta = get_comment_meta($comment_a->comment_ID, LIKEBTN_META_KEY_LIKES);
        $comment_b_meta = get_comment_meta($comment_b->comment_ID, LIKEBTN_META_KEY_LIKES);

        $comment_a_likes = (int)$comment_a_meta[0];
        $comment_b_likes = (int)$comment_b_meta[0];

        if ($comment_a_likes > $comment_b_likes) {
            return -1;
        } elseif ($comment_a_likes < $comment_b_likes) {
            return 1;
        }
        return 0;
    }

    global $wp_query;
    $comments = $wp_query->comments;
    usort($comments, 'sort_comments_by_likes');

    return $comments;
}

// get comments sorted by dislikes
function likebtn_comments_sorted_by_dislikes()
{
    // function for sorting comments by Likes
    function sort_comments_by_dislikes($comment_a, $comment_b)
    {
        $comment_a_meta = get_comment_meta($comment_a->comment_ID, LIKEBTN_META_KEY_DISLIKES);
        $comment_b_meta = get_comment_meta($comment_b->comment_ID, LIKEBTN_META_KEY_DISLIKES);

        $comment_a_dislikes = (int)$comment_a_meta[0];
        $comment_b_dislikes = (int)$comment_b_meta[0];

        if ($comment_a_dislikes > $comment_b_dislikes) {
            return -1;
        } elseif ($comment_a_dislikes < $comment_b_dislikes) {
            return 1;
        }
        return 0;
    }

    global $wp_query;
    $comments = $wp_query->comments;
    usort($comments, 'sort_comments_by_dislikes');

    return $comments;
}

// Get the permalink for a comment on another blog.
function _likebtn_get_blog_comment_link( $blog_id, $comment_id ) {
	switch_to_blog( $blog_id );
	$link = get_comment_link( $comment_id );
	restore_current_blog();

	return $link;
}

// Converts entity name to title
function _likebtn_get_entity_name_title($entity_name, $without_prefix = false, $add_list = true)
{
    global $likebtn_entity_titles;

    $title = '';
    $is_excerpt = false;
    if (_likebtn_has_list_flag($entity_name)) {
        $is_excerpt = true;
    }

    if (!array_key_exists($entity_name, $likebtn_entity_titles)) {

        $entity_name = _likebtn_cut_list_flag($entity_name);

        $title = __(str_replace('_', ' ', ucfirst($entity_name)));
        if ($is_excerpt && $add_list) {
            $title .= ' ' . __('List');
        }
    } else {
        $title = __($likebtn_entity_titles[$entity_name]);
    }

    if ($without_prefix) {
        $title = preg_replace("/\(.*\) /", '', $title);
    }

    return $title;
}

// Check if entity has a list flag
function _likebtn_has_list_flag($entity_name)
{
    if (strstr($entity_name, LIKEBTN_LIST_FLAG)) {
        return true;
    } else {
        return false;
    }
}

// Cut list flag from entity name
function _likebtn_cut_list_flag($entity_name)
{
    return str_replace(LIKEBTN_LIST_FLAG, '', $entity_name);
}

// Check if BuddyPress is installed and active
function _likebtn_is_bp_active()
{
    if (function_exists('bp_is_active') && bp_is_active('core')) {
        return true;
    } else {
        return false;
    }
}

// add Like Button to content
function _likebtn_get_content_universal($real_entity_name, $entity_id, $content = '', $wrap = true, $current_position = '', $current_alignment = array(), $use_entity_name = '')
{
    // get entity name whose settings should be copied
    $use_entity_name = likebtn_calc_use_settings_from($use_entity_name);

    if ($use_entity_name) {
        $entity_name = $use_entity_name;
    } else {
        $entity_name = $real_entity_name;
    }

    if (get_option('likebtn_show_' . $real_entity_name) != '1'
        || get_option('likebtn_show_' . $entity_name) != '1')
    {
        return $content;
    }

    // check user authorization
    $user_logged_in = get_option('likebtn_user_logged_in_' . $entity_name);

    if ($user_logged_in === '1' || $user_logged_in === '0') {
        if ($user_logged_in == '1' && !is_user_logged_in()) {
            return $content;
        }
        if ($user_logged_in == '0' && is_user_logged_in()) {
            return $content;
        }
    }

    // check user authorization
    $auth_check = _likebtn_auth_check($entity_name, array());
    if ($auth_check['return']) {
        return $content;
    }
    $values = $auth_check['values'];

    //$html = _likebtn_get_markup($real_entity_name, $entity_id, $values, $entity_name, true, true, false, true);
    $html = _likebtn_get_markup($real_entity_name, $entity_id, $values, $entity_name, true, $wrap, false, true);

    if ($auth_check['user_logged_in_alert']) {
        if ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT) {
            $html = $auth_check['user_logged_in_alert'];
        } elseif ($auth_check['user_logged_in'] == LIKEBTN_USER_LOGGED_IN_ALERT_BTN) {
            $html .= $auth_check['user_logged_in_alert'];
        }
    }

    $position = get_option('likebtn_position_' . $entity_name);
    if ($current_position && $position != LIKEBTN_POSITION_BOTH && $current_position != $position) {
        return $content;
    }

    $alignment = get_option('likebtn_alignment_' . $entity_name);
    if ($current_alignment && !in_array($alignment, $current_alignment)) {
        return $content;
    }
    if ($content) {
        if ($position == LIKEBTN_POSITION_TOP) {
            $html = $html . $content;
        } elseif ($position == LIKEBTN_POSITION_BOTTOM) {
            $html = $content . $html;
        } else {
            if ($real_entity_name == LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT) {
                // Only top for BuddyPress comments
                $html = $html . $content;
            } else {
                $html = $html . $content . $html;
            }
        }
    }

    return $html;
}

// BuddyPress member profile
function likebtn_bp_member()
{
    if (!empty(buddypress()->displayed_user->id)) {
        $content = _likebtn_get_content_universal(LIKEBTN_ENTITY_BP_MEMBER, buddypress()->displayed_user->id);
        echo $content;
    }
}
// User profile page.
add_action('bp_before_member_header_meta', 'likebtn_bp_member');

// BuddyPress activity
function _likebtn_bp_activity($wrap = true, $position = LIKEBTN_POSITION_BOTH, $content = '') {
    $entity_id = '';

    $entity_name = _likebtn_bp_get_entity_name();

    $entity_id = bp_get_activity_id();

    if ($entity_name == LIKEBTN_ENTITY_BP_ACTIVITY_POST) {
        $entity_id = bp_get_activity_secondary_item_id();
    } else if ($entity_name == LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC) {
        $entity_id = bp_get_activity_item_id();
    }

    if (!$entity_name || !$entity_id) {
        return false;
    }

    $use_entity_name = '';
    if ($entity_name == LIKEBTN_ENTITY_BP_ACTIVITY_POST) {
        $use_entity_name = LIKEBTN_ENTITY_BP_ACTIVITY_POST;
        $entity_name = LIKEBTN_ENTITY_POST;
    }
    if ($entity_name == LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC) {
        $use_entity_name = LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC;
        $entity_name = LIKEBTN_ENTITY_BBP_POST;
    }

    echo _likebtn_get_content_universal($entity_name, $entity_id, $content, $wrap, $position, array(), $use_entity_name);
}

// BuddyPress activity comment
function likebtn_bp_activity_comment_top($content) {

    global $activities_template;

    if (empty($activities_template) || empty($activities_template->activity) || empty($activities_template->activity->current_comment)) {
        return $content;
    }

    $entity_id = $activities_template->activity->current_comment->id;

    return _likebtn_get_content_universal(LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, $entity_id, $content, true, LIKEBTN_POSITION_TOP);
}

function likebtn_bp_activity_comment_bottom() {
    $entity_id = bp_get_activity_comment_id();
    echo _likebtn_get_content_universal(LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, $entity_id, '', false);
}

// BuddyPress activity comment ajax - Read more
// bp_dtheme_get_single_activity_content()
function likebtn_bp_activity_comment_ajax($content) {

    global $activities_template;

    if (!empty($_POST['action']) && $_POST['action'] == 'get_single_activity_content' && !empty($_POST['activity_id'])) {
        // Ajax - read more
        // http://oik-plugins.eu/buddypress-a2z/oik_api/bp_activity_get_specific/
        // $_POST['activity_id'] i sanitized and used inside bp_activity_get_specific()
        $activity_array = bp_activity_get_specific( array(
            'activity_ids'     => $_POST['activity_id'],
            'display_comments' => 'stream'
        ) );
        $activity = ! empty( $activity_array['activities'][0] ) ? $activity_array['activities'][0] : false;
        
        if (!empty($activity) && !empty($activity->type) && $activity->type == 'activity_comment') {
            $entity_id = (int)$_POST['activity_id'];

            return _likebtn_get_content_universal(LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, $entity_id, $content, true, LIKEBTN_POSITION_TOP);
        }
    }

    return $content;
}

// BuddyPress activity top
function likebtn_bp_activity_top($content)
{
    return _likebtn_bp_activity(true, LIKEBTN_POSITION_TOP) . $content;
}

// BuddyPress activity bottom
function likebtn_bp_activity_bottom()
{
    return _likebtn_bp_activity(false, LIKEBTN_POSITION_BOTTOM);
}

// BuddyPress activity bottom for non-logged in users
function likebtn_bp_activity_bottom_anon($content)
{
    // If user not logged in and activity is visible to all and position - bottom
    $entity_name = _likebtn_bp_get_entity_name();
    $pos = _likebtn_get_option($entity_name, 'likebtn_position');
    $user_logged_in = _likebtn_get_option($entity_name, 'user_logged_in');

     if (!is_user_logged_in() 
         && in_array($user_logged_in, array(LIKEBTN_USER_LOGGED_IN_ALL, LIKEBTN_USER_LOGGED_IN_NO))
    //     && in_array($pos, array(LIKEBTN_POSITION_TOP, LIKEBTN_POSITION_BOTH))
    ) {
        $content = $content._likebtn_bp_activity(true, LIKEBTN_POSITION_BOTTOM);
    }

    return $content;
}

// Activity page.
//add_action("bp_has_activities", array(&$this, "BuddyPressBeforeActivityLoop"));

// BuddyPress activity top
add_filter('bp_get_activity_action', 'likebtn_bp_activity_top');

// BuddyPress activity bottom for non-logged in users
add_filter('bp_activity_entry_content', 'likebtn_bp_activity_bottom_anon');

// BuddyPress activity bottom
add_action('bp_activity_entry_meta', 'likebtn_bp_activity_bottom');

// BuddyPress activity comment
add_filter('bp_get_activity_content', 'likebtn_bp_activity_comment_top');
add_filter('bp_activity_comment_options', 'likebtn_bp_activity_comment_bottom');
add_filter('bp_get_activity_content_body', 'likebtn_bp_activity_comment_ajax');

// BuddyPress Fetches full an activity's full, non-excerpted content via a POST request.
// Used for the 'Read More' link on long activity items.
//add_action('bp_dtheme_get_single_activity_content',       'likebtn_bp_get_single_activity_content');
//add_action('bp_legacy_theme_get_single_activity_content', 'likebtn_bp_get_single_activity_content');

// Forum topic page
add_filter('bp_has_topic_posts', 'likebtn_bp_activity');

// Get entity name of the current BuddyPress activity
// Not working for comments
function _likebtn_bp_get_entity_name()
{
    $activity_type = bp_get_activity_type();

    switch ($activity_type) {
        case 'bbp_topic_create':
            // Forum topic
            //return LIKEBTN_ENTITY_BBP_POST;
            return LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC;
        case 'new_blog_post':
            // Post
            return LIKEBTN_ENTITY_BP_ACTIVITY_POST;
            //return LIKEBTN_ENTITY_POST;
        case 'new_member':
            return LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE;
        default:
            return LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE;
    }
}

// Get activity title
function _likebtn_bp_get_activity_title($id)
{
    $activity = null;
    $title = '';

    if (function_exists('bp_activity_get_specific')) {
        $activity_list = bp_activity_get_specific(array(
            'activity_ids' => $id,
            'display_comments' => true
        ));

        if (!empty($activity_list['activities']) && !empty($activity_list['activities'][0])) {
            $activity = $activity_list['activities'][0];
        }
    }
    if ($activity) {
        if ($activity->action) {
            $title = $activity->action;
        } elseif ($activity->content) {
            $title = $activity->content;
        } elseif ($activity->primary_link) {
            $title = $activity->primary_link;
        }
        if ($activity->content && $activity->type != 'bbp_topic_create') {
            $title = $title.': '.$activity->content;
        }
        $title = strip_tags($title);
    }
    return $title;
}

// Check if bbPress is installed and active
function _likebtn_is_bbp_active()
{
    if (function_exists('bbp_has_replies')) {
        return true;
    } else {
        return false;
    }
}

// bbPress
function likebtn_bbp_reply($wrap = true, $position = LIKEBTN_POSITION_BOTH, $alignment = '', $content = '') {

    // Reply to topic
    $entity = bbp_get_reply(bbp_get_reply_id());

    // Topic
    if (!is_object($entity)) {
        $entity = bbp_get_topic(bbp_get_topic_id());
    }
   
    if (!$entity) {
        return false;
    }

    // Allow in selected forums
    if (!empty($entity->post_parent)) {
        $allow_forums = get_option('likebtn_allow_forums_' . LIKEBTN_ENTITY_BBP_POST);
        if (!is_array($allow_forums)) {
            $allow_forums = array();
        }
        if ($allow_forums) {
            // Algotithm for topic and replies is different
            if ($entity->post_type == 'topic') {
                if (!in_array($entity->post_parent, $allow_forums)) {
                    return false;
                }
            } else {
                // Reply
                $topic = bbp_get_topic(bbp_get_topic_id());
                if (!in_array($topic->post_parent, $allow_forums)) {
                    return false;
                }
            }
        }
    }

    return _likebtn_get_content_universal(LIKEBTN_ENTITY_BBP_POST, $entity->ID, $content, $wrap, $position, $alignment);
}

// bbPress reply top left & center
function likebtn_bbp_has_replies($has_replies)
{
    add_filter('bbp_theme_before_reply_admin_links', 'likebtn_bbp_reply_top_left');
    add_filter('bbp_theme_after_reply_admin_links', 'likebtn_bbp_reply_top_right');
    //add_filter('bbp_get_reply_content', 'likebtn_bbp_reply_bottom');
    add_filter('bbp_get_reply_author_link', 'likebtn_bbp_author_link');

    return $has_replies;
}

// bbPress reply top left & center
function likebtn_bbp_reply_top_left()
{
    echo likebtn_bbp_reply(false, LIKEBTN_POSITION_TOP, array(LIKEBTN_ALIGNMENT_LEFT, LIKEBTN_ALIGNMENT_CENTER));
}

// bbPress reply top left & center
function likebtn_bbp_reply_top_right()
{
    echo likebtn_bbp_reply(false, LIKEBTN_POSITION_TOP, array(LIKEBTN_ALIGNMENT_RIGHT));
}

// bbPress reply bottom
function likebtn_bbp_reply_bottom()
{
    echo likebtn_bbp_reply(true, LIKEBTN_POSITION_BOTTOM);
}

// bbPress thread
function likebtn_bbp_author_link($author_link)
{
    global $post;

    $reply_id = bbp_get_reply_id($post->ID);
    if (bbp_is_reply_anonymous($reply_id)) {
        return $author_link;
    }
    
    $author_id = bbp_get_reply_author_id($reply_id);

    $content = _likebtn_get_content_universal(LIKEBTN_ENTITY_BBP_USER, $author_id);
    return $author_link . $content;
}

// bbPress reply bottom
function likebtn_bbp_user_profile()
{
    $content = _likebtn_get_content_universal(LIKEBTN_ENTITY_BBP_USER, bbpress()->displayed_user->ID);
    echo $content;
}

add_filter('bbp_has_replies', 'likebtn_bbp_has_replies');
add_action('bbp_theme_after_reply_content', 'likebtn_bbp_reply_bottom' );
add_action('bbp_template_after_user_profile', 'likebtn_bbp_user_profile');

// Add style to frontend
function likebtn_enqueue_style()
{
    $src = _likebtn_get_public_url() . 'css/style.css?ver=' . LIKEBTN_VERSION;
    wp_enqueue_style('likebtn_style', $src); 
}

// Add frontend style
add_action('wp_enqueue_scripts', 'likebtn_enqueue_style');

// Add script to frontend
function likebtn_frontend_script()
{
    $src = _likebtn_get_public_url() . 'js/frontend.js?ver=' . LIKEBTN_VERSION;
    wp_enqueue_script('likebtn_frontend', $src); 

    wp_localize_script('likebtn_frontend', 'likebtn_eh_data', array(
        // URL to wp-admin/admin-ajax.php to process the request
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        // generate a nonce with a unique ID "myajax-post-comment-nonce"
        // so that you can check it later when an AJAX request is sent
        'security' => wp_create_nonce('likebtn_event_handler')
    ));
}

// Add frontend style
add_action('wp_enqueue_scripts', 'likebtn_frontend_script');

// Current + deprecated settings
function _likebtn_get_all_settings()
{
    global $likebtn_settings;
    global $likebtn_settings_deprecated;

    return array_merge($likebtn_settings, $likebtn_settings_deprecated);
}

// Prepare entity title
function _likebtn_prepare_title($entity_name, $title, $max = LIKEBTN_WIDGET_TITLE_LENGTH)
{
    $title = stripslashes($title);

    if (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC))) {
        $title = strip_tags($title);
    }
    if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
        $title = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($title);
    }

    return _likebtn_shorten_title($title, $max);
}

// Shorten title
function _likebtn_shorten_title($title, $max = LIKEBTN_WIDGET_TITLE_LENGTH)
{
    if (mb_strlen($title) > $max) {
        $title = mb_substr($title, 0, $max) . '...';
    }
    return $title;
}

// Shorten title
function _likebtn_shorten_excerpt($excerpt, $max = LIKEBTN_WIDGET_EXCERPT_LENGTH)
{
    if (mb_strlen($excerpt) > $max) {
        $excerpt = mb_substr($excerpt, 0, $max) . '...';
    }
    return $excerpt;
}

// Get entity URL
function _likebtn_get_entity_url($entity_name, $entity_id, $url = '', $blog_id = 0)
{
    if ($url) {
        return $url;
    }

    if (!$entity_name || !$entity_id) {
        return '';
    }

    switch ($entity_name) {
        case LIKEBTN_ENTITY_COMMENT:
            if (!$blog_id) {
                $url = get_comment_link($entity_id);
            } else {
                $url = _likebtn_get_blog_comment_link($blog_id, $entity_id);
            }
            break;
        case LIKEBTN_ENTITY_BP_ACTIVITY_POST:
        case LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE:
        case LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT:
        case LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC:
            if (function_exists('bp_activity_get_permalink')) {
                $url = bp_activity_get_permalink($entity_id);
            }
            break;
        case LIKEBTN_ENTITY_BP_MEMBER:
        case LIKEBTN_ENTITY_BBP_USER:
            if (function_exists('bp_core_get_user_domain')) {
                $url = bp_core_get_user_domain($entity_id);
            }
            if (!$url) {
                $url = get_author_posts_url($entity_id);
            }
            break;
        case LIKEBTN_ENTITY_USER:
            $url = get_author_posts_url($entity_id);
            break;
        case LIKEBTN_ENTITY_UM_USER:
            $url = get_author_posts_url($entity_id);
            break;
        case LIKEBTN_ENTITY_BBP_POST:
            if (!$blog_id) {
                $url = get_permalink($entity_id);
            } else {
                $url = get_blog_permalink($blog_id, $entity_id);
            }
            // If bbPress forum is used in BuddyPress group
            if (strstr($url, "/groups/")) {
                $post_meta = get_post_meta($entity_id);

                if (!empty($post_meta['_bbp_topic_id']) && !empty($post_meta['_bbp_topic_id'][0]) && function_exists('bbp_get_reply_url')) {
                    $url = bbp_get_reply_url($post_meta['_bbp_topic_id'][0]);
                }
                if (!strstr($url, "#")) {
                    $url .= '#post-'.$entity_id;
                }
            }
            break;
            
        default:
            if (!$blog_id) {
                $url = get_permalink($entity_id);
            } else {
                $url = get_blog_permalink($blog_id, $entity_id);
            }
            break;
    }
    return $url;
}

function _likebtn_get_entity_excerpt($entity_name, $entity_id)
{
    global $likebtn_nonpost_entities;

    $excerpt = '';

    if ($entity_name == LIKEBTN_ENTITY_COMMENT) {
        $excerpt = get_comment_excerpt($entity_id);
    } elseif (!in_array($entity_name, $likebtn_nonpost_entities)) {
        $get_post = get_post($entity_id);
        $excerpt = $get_post->post_excerpt;
        if (!$excerpt) {
            $excerpt = $get_post->post_content;
        }
        if ($excerpt) {
            $excerpt = strip_tags($excerpt);
            $excerpt = strip_shortcodes($excerpt);
            //$post['excerpt'] = apply_filters('get_the_excerpt', $post['excerpt']);
            $excerpt = _likebtn_shorten_excerpt($excerpt);
        }
    } elseif (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC))) {
        if (function_exists('bp_activity_get_specific')) {
            $get_activity = bp_activity_get_specific(array('activity_ids' => $entity_id));
            if (!empty($get_activity['activities']) && !empty($get_activity['activities'][0])) {
                $excerpt = $get_activity['activities'][0]->content;
                $excerpt = strip_shortcodes($excerpt);
                $excerpt = _likebtn_shorten_excerpt($excerpt);
            }
        }
    }

    return $excerpt;
}

function _likebtn_get_entity_content($entity_name, $entity_id)
{
    global $likebtn_nonpost_entities;

    $content = '';

    if ($entity_name == LIKEBTN_ENTITY_COMMENT) {
        $content = get_comment_text($entity_id);
    } elseif (!in_array($entity_name, $likebtn_nonpost_entities)) {
        $get_post = get_post($entity_id);
        $content = $get_post->post_content;
    } elseif (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC))) {
        if (function_exists('bp_activity_get_specific')) {
            $get_activity = bp_activity_get_specific(array('activity_ids' => $entity_id));
            if (!empty($get_activity['activities']) && !empty($get_activity['activities'][0])) {
                $content = $get_activity['activities'][0]->content;
            }
        }
    }

    return $content;
}

// Check if function has been called by some other function
function _likebtn_has_caller($function_name)
{
    $e = new Exception;

    if (strstr($e->getTraceAsString(), $function_name.'(')) {
        return true;
    } else {
        return false;
    }
}

// Send JSON to browser
function _likebtn_send_json( $response, $callback = '' ) {
    //@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
    @header( 'Content-Type: application/javascript; charset=' . get_option( 'blog_charset' ) );
    if ($callback) {
        echo $callback.'(';
    }
    echo json_encode( $response );
    if ($callback) {
        echo ')';
    }
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        wp_die();
    } else {
        die;
    }
}

// Checks whether the content passed contains a specific short code. 
function _likebtn_has_shortcode($content, $tag)
{
    if (strstr($content, '['.$tag.']')) {
        return true;
    }

    return false;
}

// Remove specified shortcode from content
function _likebtn_remove_shortcode($content, $tag)
{
    return preg_replace("/\[".$tag."\]/", '', $content);
}

// Like button click handler
function likebtn_event_handler()
{
    $response = array(
        'result' => 'error',
        'message' => '',
    );

    $response['result'] = 'success';

    @header( 'Content-Type: application/javascript; charset=' . get_option( 'blog_charset' ) );
    echo json_encode( $response );
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    $likes = -1;
    if (isset($_POST['l']) && $_POST['l'] !== '?') {
        $likes = (int)$_POST['l'];
    }
    $dislikes = -1;
    if (isset($_POST['d']) && $_POST['d'] !== '?') {
        $dislikes = (int)$_POST['d'];
    }

    // @todo: determine and pass url
    _likebtn_save_vote($_POST['identifier'], (int)$_POST['type'], (int)$_POST['old_type'], '', $likes, $dislikes);

    die;
}
add_action('wp_ajax_likebtn_event_handler', 'likebtn_event_handler' );
add_action('wp_ajax_nopriv_likebtn_event_handler', 'likebtn_event_handler');

// Save vote
// type: 1, -1, 0 (cancel)
// act - u, a, r (if set request to LikeBtn has been sent)
function _likebtn_save_vote($identifier, $type, $old_type = 0, $act = 'u', $wpen = '', $likes = -1, $dislikes = -1, $url = '', $voter_by_user = false)
{
    global $wpdb;

    $result = false;
    $old_type = (int)$old_type;
    $user_id = get_current_user_id();
    $identifier_hash = md5($identifier);
    $ip = _likebtn_get_ip();

    if ($voter_by_user) {
        $client_identifier = $user_id;
    } else {
        $client_identifier = md5($ip.$_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT'].$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }

    if ($type == 2) {
        if ($old_type == 1) {
            $type = -1;
        } else {
            $type = 1;
        }
    }

    if ($type != 1 && $type != -1 && $type != 0) {
        $type = 1;
    }
    $created_at = date("Y-m-d H:i:s");
    $ipinfo = _likebtn_ipinfo($ip);
    $lat = '';
    $lng = '';
    $country = '';
    if ($ipinfo) {
        $lat = $ipinfo['lat'];
        $lng = $ipinfo['lng'];
        $country = $ipinfo['country'];
    }

    if ($type == 0 || $act == 'r') {
        // Cancel vote
        $query_del = "
            DELETE FROM ".$wpdb->prefix.LIKEBTN_TABLE_VOTE." 
            WHERE identifier_hash = '{$identifier_hash}' 
                AND client_identifier = '{$client_identifier}' 
                AND user_id = '{$user_id}'
            
        ";
        $old_type = (int)$old_type;
        if ($old_type) {
            $query_del .= " AND type = {$old_type} ";
        }
        $query_del .= " LIMIT 1 ";

        $wpdb->query($query_del);
    } else {

        // Find
        $vote = null;
        if (!$act || $act == 'u') {
            $query_get = "
                SELECT id
                FROM ".$wpdb->prefix.LIKEBTN_TABLE_VOTE."
                WHERE identifier_hash = %s 
                    AND client_identifier = %s 
                    AND user_id = %d
            ";

            if ($old_type) {
                $query_get .= " AND type = {$old_type} ";
            }
            $query_get .= " ORDER BY created_at DESC";
            $vote = $wpdb->get_row($wpdb->prepare($query_get, $identifier_hash, $client_identifier, $user_id));
        }

        $vote_data = array(
            'identifier' => $identifier,
            'identifier_hash' => $identifier_hash,
            'client_identifier' => $client_identifier,
            'type' => $type,
            'user_id' => $user_id,
            'ip' => $ip,
            'lat' => $lat,
            'lng' => $lng,
            'country' => $country,
            'created_at' => $created_at
        );

        try {
            if ($vote) {
                $update_where = array(
                    'identifier_hash' => $identifier_hash,
                    'client_identifier' => $client_identifier,
                    'user_id' => $user_id
                );
                if ($old_type) {
                    $update_where['type'] = $old_type;
                }
                $result = $wpdb->update($wpdb->prefix.LIKEBTN_TABLE_VOTE, $vote_data, $update_where);
            } else {
                $result = $wpdb->insert($wpdb->prefix.LIKEBTN_TABLE_VOTE, $vote_data);
            }
        } catch(Exception $e) {
            $result = false;
        }

        if ($result) {

            $entity_info = _likebtn_parse_identifier($identifier);

            $args = array(
                'identifier' => $identifier,
                'entity_name' => $entity_info['entity_name'],
                'entity_id' => $entity_info['entity_id'],
                'type' => $type,
                'act' => $act,
                'user_id' => $user_id,
                'ip' => $ip,
                'lat' => $lat,
                'lng' => $lng,
                'country' => $country,
                'created_at' => $created_at
            );
            do_action("likebtn_vote", $args);

            if ($user_id) {

                if ($wpen) {
                    $entity_info['entity_name'] = $wpen;
                }

                // myCRED Award
                if ($type == 1) {
                    do_action('likebtn_mycred_like', $entity_info['entity_name'], $entity_info['entity_id']);
                } else {
                    do_action('likebtn_mycred_dislike', $entity_info['entity_name'], $entity_info['entity_id']);
                }

                // BuddyPress notification
                if (_likebtn_is_bp_active()) {
                    if ($type == 1) {
                        $bp_action = 'like';
                    } else {
                        $bp_action = 'dislike';
                    }

                    if (_likebtn_get_option($entity_info['entity_name'], 'likebtn_bp_notify') == '1') {
                        _likebtn_bp_notifications_add_notification($entity_info['entity_name'], $entity_info['entity_id'], $user_id, $bp_action);
                    }
                    if (_likebtn_get_option($entity_info['entity_name'], 'likebtn_bp_activity') == '1') {
                        _likebtn_bp_activity_add($entity_info['entity_name'], $entity_info['entity_id'], $user_id, $type, _likebtn_get_option($entity_info['entity_name'], 'likebtn_bp_hide_sitewide'), _likebtn_get_option($entity_info['entity_name'], 'likebtn_bp_image'), _likebtn_get_option($entity_info['entity_name'], 'likebtn_bp_snippet_tpl'));
                    }
                }
            }
        }
    }

    // Update item
    if (get_option('likebtn_plan') >= LIKEBTN_PLAN_PRO && $likes != -1 && $dislikes != -1) {
        require_once(dirname(__FILE__) . '/likebtn_like_button.class.php');
        $likebtn = new LikeBtnLikeButton();
        $likebtn->updateCustomFields($identifier, $likes, $dislikes, $url);
    }

    // Send email notification to admin
    if ($result && likebtn_check_plan(LIKEBTN_PLAN_PRO) && get_option('likebtn_notify_enabled') == '1') {
        $current_user = wp_get_current_user();
        $vars = array(
            'vote_type' => likebtn_vote_title($type),
            'domain' => likebtn_site_domain(),
            'item_type' => _likebtn_get_entity_name_title($entity_info['entity_name'], true, false),
            'item_url' => _likebtn_get_entity_url($entity_info['entity_name'], $entity_info['entity_id'], $url),
            'item_title' => _likebtn_get_entity_title($entity_info['entity_name'], $entity_info['entity_id']),
            'item_likes' => $likes,
            'item_dislikes' => $dislikes,
            'item_admin_stats_url' => admin_url() . 'admin.php?page=likebtn_statistics&likebtn_entity_name='.$entity_info['entity_name'].'&likebtn_post_id='.$entity_info['entity_id'].'&show=View',
            'vote_ip' => $ip,
            'vote_ip_url' => admin_url() . 'admin.php?page=likebtn_votes&likebtn_ip='.$ip.'&show=View',
            'user_url' => _likebtn_get_entity_url(LIKEBTN_ENTITY_USER, $user_id),
            'user_login' => $current_user->user_login,
            'user_votes_url' => admin_url() . 'admin.php?page=likebtn_votes&likebtn_user_id='.$user_id.'&show=View',
        );
        likebtn_send_vote_notification($vars);
    }

    return $result;
}

// Get user vote type for an item
function _likebtn_get_vote_type($identifier, $voting_frequency)
{
    global $wpdb;

    $user_id = (int)get_current_user_id();
    $identifier_hash = md5($identifier);

    // Get user last vote
    $query_get = "
        SELECT type
        FROM ".$wpdb->prefix.LIKEBTN_TABLE_VOTE."
        WHERE
            identifier_hash = %s 
            AND user_id = %d
    ";

    if ((int)$voting_frequency) {
        $created_at = date("Y-m-d H:i:s", time() - (int)$voting_frequency);
        $query_get .= " AND created_at > '{$created_at}' ";
    }

    $query_get .= " LIMIT 1";

    $vote = $wpdb->get_row($wpdb->prepare($query_get, $identifier_hash, $user_id));

    if ($vote) {
        return (int)$vote->type;
    } else {
        return '';
    }
}

// Get an option taking Use settings from into account
function _likebtn_get_option($entity_name, $option, $values = array())
{
    if (isset($values[$option])) {
        return $values[$option];
    }
    if (isset($values[str_replace('likebtn_', '', $option)])) {
        return $values[str_replace('likebtn_', '', $option)];
    }
    $use_entity_name = likebtn_calc_use_settings_from($entity_name);
    return get_option($option.'_'.$use_entity_name);
}

// Proxy processing
function likebtn_prx()
{
    global $likebtn_map_entities;

    $likebtn_response = array();
    $settings = array();
    $voting_allowed = true;

    // Response in case of error
    $response = array(
        'result' => 0,
        'err' =>'Unknown error occured'
    );

    $site_id = get_option('likebtn_site_id');
    $api_key = get_option('likebtn_account_api_key');
    $client  = '';
    $voter_by_user = false;

    if (!$site_id || !$api_key) {
        $response['err'] = 'incorrect_credentials';
    } else {
        if (empty($_GET['likebtn_q'])) {
            // If suhosin.get.max_value_length is set
            $_GET = array();
            $params = explode('&', $_SERVER['QUERY_STRING']);
            foreach ($params as $pair) {
                list($key, $value) = explode('=', $pair);
                $_GET[urldecode($key)] = urldecode($value);
            }
        }
        if (empty($_GET['likebtn_q'])) {
            $response['err'] = 'Empty likebtn_q';
        } else {

            $url = esc_url_raw(base64_decode($_GET['likebtn_q']));
            // We are using esc_url_raw() instead of esc_url(), as esc_url() distorts & into &#038;
            // and it breaks our logic.
            if (parse_url($url, PHP_URL_HOST) !=  'wv.likebtn.com') {
                $url = '';
            }
            if (preg_match("/^\/\//", $url)) {
                $url = 'http:'.$url;
            }
            $direct_url = str_replace('wv.likebtn.com', 'direct.wv.likebtn.com', $url);

            if (!$url) {
                $response['err'] = 'Could not parse likebtn_q';
            } else {
            
                // Determine identifier
                $query = parse_url($url, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $settings);
                }

                if (!empty($settings['s'])) {
                    $s = json_decode($settings['s'], true);

                    if (!empty($s['i'])) {
                        $entity_info = _likebtn_parse_identifier($s['i']);
                        if (isset($entity_info['entity_name'])) {
                            $voter_by = _likebtn_get_option($entity_info['entity_name'], 'likebtn_voter_by');
                            if ($voter_by == LIKEBTN_VOTER_BY_USER) {
                                $voter_by_user = true;
                                if (!is_user_logged_in() /*|| !_likebtn_user_vote_allowed($s['i'], get_current_user_id())*/) {
                                    $voting_allowed = false;
                                } else {
                                    $client = get_current_user_id();
                                }
                            }
                        }
                    }
                }

                if ($voting_allowed) {
                    // Check host
                    if (parse_url($url, PHP_URL_HOST) !=  'wv.likebtn.com') {
                        $response['err'] = 'Wrong prx address';
                    } else {
                        try {
                            $http = new WP_Http();
                            $headers = array(
                                "User-Agent" => $_SERVER['HTTP_USER_AGENT'],
                                "Accept-Encoding" => $_SERVER['HTTP_ACCEPT'],
                                "Accept-Language" => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                                "Likebtn-Site-Id" => $site_id,
                                "Likebtn-Api-Key" => $api_key,
                                "Likebtn-Ip"      => _likebtn_get_ip(true),
                                "Likebtn-Client"  => $client,
                            );

                            $likebtn_response = $http->request($url, array('headers' => $headers));
                        } catch (Exception $e) {
                            try {
                                $likebtn_response = $http->request($direct_url, array('headers' => $headers));
                            } catch (Exception $e) {
                                $response['err'] = $e->getMesssage();
                            }
                        }
                        // Error occured
                        if (is_wp_error($likebtn_response)) {
                            $likebtn_response = $http->request($direct_url, array('headers' => $headers));
                            if (is_wp_error($likebtn_response)) {
                                $response['err'] = $likebtn_response->get_error_message();
                            }
                        }
                    }
                }
            }
        }
    }

    @header( 'Content-Type: application/javascript; charset=' . get_option( 'blog_charset' ) );
    if (is_array($likebtn_response) && !empty($likebtn_response['body'])) {
        echo $likebtn_response['body'];
    } else {
        // Error
        echo json_encode($response);
    }
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    // Save vote in DB
    if (is_array($likebtn_response) && !empty($likebtn_response['body'])) {
        $body_json = preg_replace("/^lb_json\(/", '', $likebtn_response['body']);
        $body_json = preg_replace("/\)$/", '', $body_json);
        $body = json_decode($body_json, true);

        if (is_array($body) && empty($body['err']) && !empty($body['data'])) {

            // Vote has been accepted in LikeBtn.com
            if (isset($body['data']['type'])) {

                // Determine identifier
                if (!empty($settings['s'])) {

                    $s = json_decode($settings['s'], true);

                    if (!empty($s['i'])) {

                        $vote_type = (int)$body['data']['type'];
                        $vote_old_type = 0;
                        if (isset($body['data']['old_type'])) {
                            $vote_old_type = (int)$body['data']['old_type'];
                        }
                        $vote_act = 'u';
                        if (isset($body['data']['act'])) {
                            $vote_act = $body['data']['act'];
                        }

                        $wpen = '';
                        if (isset($_GET['wpen'])) {
                            $wpen = sanitize_text_field($_GET['wpen']);
                            if (!in_array($wpen, array_keys($likebtn_map_entities))) {
                                $wpen = '';
                            }
                        }

                        $likes = -1;
                        if (isset($body['data']['l']) && $body['data']['l'] !== '?') {
                            $likes = $body['data']['l'];
                        }
                        $dislikes = -1;
                        if (isset($body['data']['d']) && $body['data']['d'] !== '?') {
                            $dislikes = $body['data']['d'];
                        }

                        $s_url = '';
                        if (!empty($s['url'])) {
                            $s_url = $s['url'];
                        }
                        _likebtn_save_vote($s['i'], $vote_type, $vote_old_type, $vote_act, $wpen, $likes, $dislikes, $s_url, $voter_by_user);
                    }
                }
            }
        }
    }

    die;
}
add_action('wp_ajax_likebtn_prx', 'likebtn_prx' );
add_action('wp_ajax_nopriv_likebtn_prx', 'likebtn_prx');

// Get IP info
function _likebtn_ipinfo($ip)
{
    $url = 'http://ipinfo.io/'.$ip.'/json';

    try {
        $http = new WP_Http();
        $response = $http->request($url);
    } catch (Exception $e) {
        return '';
    }

    // Error occured
    if (is_wp_error($response)) {
        return '';
    }

    $ipinfo = array();
    if (is_array($response) && !empty($response['body'])) {
        try {
            $ipinfo = json_decode($response['body'], true);
        } catch (Exception $e) {}
    }

    if (is_array($ipinfo)) {
        $lat = '';
        $lng = '';
        $country = '';
        if (!empty($ipinfo['loc'])) {
            list($lat, $lng) = explode(',', $ipinfo['loc']);
        }
        if (!empty($ipinfo['country'])) {
            $country = $ipinfo['country'];
        }
        return array('lat' => $lat, 'lng' => $lng, 'country' => $country);
    } else {
        return '';
    }
}

// Get entity by id
function _likebtn_get_entity($entity_name, $entity_id)
{
    if ($entity_name == LIKEBTN_ENTITY_COMMENT) {
        return get_comment($entity_id);
    } else if (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC))) {
        if (function_exists('bp_activity_get_specific')) {
            $activity_list = bp_activity_get_specific(array(
                'activity_ids' => $entity_id,
                'display_comments' => true
            ));

            if (!empty($activity_list['activities']) && !empty($activity_list['activities'][0])) {
                return $activity_list['activities'][0];
            }
        };
    } else if ($entity_name == LIKEBTN_ENTITY_BP_MEMBER) {
        return get_user_by('id', $entity_id);
    } else {
        return get_post($entity_id);
    }

    return null;
}

// Get entity author id
function _likebtn_get_author_id($entity_name, $entity_id)
{
    if ($entity_name == LIKEBTN_ENTITY_COMMENT) {
        $comment = _likebtn_get_entity($entity_name, $entity_id);
        if ($comment && !empty($comment->user_id)) {
            return $comment->user_id;
        }
    } else if (in_array($entity_name, array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC))) {
        $activity = _likebtn_get_entity($entity_name, $entity_id);
        if ($activity && !empty($activity->user_id)) {
            return $activity->user_id;
        }
    } else if ($entity_name == LIKEBTN_ENTITY_BP_MEMBER || $entity_name == LIKEBTN_ENTITY_USER || $entity_name == LIKEBTN_ENTITY_UM_USER) {
        return $entity_id;
    //} else if ($entity_name == LIKEBTN_ENTITY_PRODUCT) {
    } else {
        // Posts, bbPress, WooCommerce products, custom post types
        $post = _likebtn_get_entity($entity_name, $entity_id);

        if ($post && !empty($post->post_author)) {
            return $post->post_author;
        }
    }
    return null;
}

// Insert LikeBtn code in footer
function likebtn_footer()
{
    ?>
    <!-- LikeBtn.com BEGIN -->
    <script type="text/javascript">var likebtn_wl = 1; (function(d, e, s) {a = d.createElement(e);m = d.getElementsByTagName(e)[0];a.async = 1;a.src = s;m.parentNode.insertBefore(a, m)})(document, 'script', '//<?php echo LIKEBTN_WIDGET_URL ?>'); if (typeof(LikeBtn) != "undefined") { LikeBtn.init(); }</script>
    <!-- LikeBtn.com END -->
    <?php if (get_option('likebtn_js')): ?>
        <!-- LikeBtn.com Custom JS BEGIN -->
        <script type="text/javascript">
            <?php echo get_option('likebtn_js'); ?>
        </script>
        <!-- LikeBtn.com Custom JS END -->
    <?php endif ?>
    <?php if (get_option('likebtn_css')): ?>
        <!-- LikeBtn.com Custom CSS BEGIN -->
        <style type="text/css">
            <?php echo get_option('likebtn_css'); ?>
        </style>
        <!-- LikeBtn.com Custom CSS END -->
    <?php endif ?>
    <?php
}

add_action('wp_footer', 'likebtn_footer', 5);

// Get entity title by id
function _likebtn_get_entity_title($entity_name, $entity_id, $max_length = LIKEBTN_TITLE_MAX_LENGTH)
{
    switch ($entity_name) {
        case LIKEBTN_ENTITY_COMMENT:
            $title = get_comment_text($entity_id);
            break;
        case LIKEBTN_ENTITY_BP_ACTIVITY_POST:
        case LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE:
        case LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT:
        case LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC:
            $title = _likebtn_bp_get_activity_title($entity_id);
            break;
        case LIKEBTN_ENTITY_BP_MEMBER:
        case LIKEBTN_ENTITY_BBP_USER:
        case LIKEBTN_ENTITY_USER:
        case LIKEBTN_ENTITY_UM_USER:
            if (function_exists('bp_core_get_user_displayname')) {
                $title = bp_core_get_user_displayname($entity_id);
            } else {
                $title = get_the_author_meta('display_name', $entity_id);
                if (!$title) {
                    $title = get_the_author_meta('user_nicename', $entity_id);
                }
            }
            break;
        default:
            $title = get_the_title($entity_id);
            break;
    }
    if ($max_length) {
        $title = _likebtn_shorten_title($title, $max_length);
    }
    return $title;
}

// Get uer avatar URL
function _likebtn_get_avatar_url($user_id)
{
    $get_avatar = get_avatar($user_id);
    preg_match('/src=["\'](.*?)["\']/i', $get_avatar, $matches);
    if (isset($matches[1])) {
        return $matches[1];
    } else {
        return '';
    }
}

// Size: thumbnail, medium, large, full
function _likebtn_get_entity_image($entity_name, $entity_id, $size = 'large')
{
    $image = '';
    switch ($entity_name) {
        case LIKEBTN_ENTITY_BP_MEMBER:
        case LIKEBTN_ENTITY_BBP_USER:
        case LIKEBTN_ENTITY_USER:
        case LIKEBTN_ENTITY_UM_USER:
            $image = _likebtn_get_avatar_url($entity_id);
            break;
        case LIKEBTN_ENTITY_ATTACHMENT:
            $image_url = wp_get_attachment_image_src($entity_id, $size);
            if (!empty($image_url[0])) {
                $image = $image_url[0];
            }
            break;
        default:
            $image_url = wp_get_attachment_image_src(get_post_thumbnail_id($entity_id), $size);
            if (!empty($image_url[0])) {
                $image = $image_url[0];
            }
            break;
    }
    return $image;
}

function _likebtn_get_template_path($template) {

    if ($theme_file = locate_template( array(LIKEBTN_PLUGIN_NAME . '/' . $template ) )) {
        $file = $theme_file;
    } else {
        $file = dirname(__FILE__).'/templates/' . $template;
    }

    return $file;
}

// Display review notice
function likebtn_review_notice()
{
    global $wpdb;
    $votes_level = 10;

    $likebtn_review = (int)get_option('likebtn_review');

    // Check
    if (_likebtn_is_stat_enabled() && $likebtn_review >= 0) {
        $query_prepared = "
            SELECT count(ID) 
            FROM ".$wpdb->prefix.LIKEBTN_TABLE_VOTE." 
        ";
        $votes_count = $wpdb->get_var($query_prepared);

        if ((int)$votes_count > $votes_level) {
            $votes = (int)(floor($votes_count / 10 ) * 10);
            if ((int)$votes > 0) {
                $likebtn_review = 1;
                update_option('likebtn_review', $likebtn_review);
            }
        }
    }
    if ($likebtn_review > 0) {
        $msg = strtr(
            '<strong>'.__(LIKEBTN_PLUGIN_TITLE, 'likebtn-like-button').'</strong>: '.__('Congrats!</strong> Your website crossed the <strong>%votes% votes</strong> – that’s awesome! If you like the plugin you can submit a review <a href="%url_review%" target="_blank">here</a>.', 'likebtn-like-button'), 
            array(
                '%url_review%' => 'https://wordpress.org/support/plugin/likebtn-like-button/reviews/#new-post',
                '%votes%' => $votes
            )
        );
        $msg .= '
            <p>
                <strong><a href="https://wordpress.org/support/plugin/likebtn-like-button/reviews/#new-post" target="_blank">'.__('Give 5 stars', 'likebtn-like-button').'</a></strong> | <a href="#" class="likebtn_dismiss_review">'.__('Dismiss this notice', 'likebtn-like-button').'</a>
                
            </p>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery(document).on("click", ".likebtn_review_notice .notice-dismiss, .likebtn_review_notice .likebtn_dismiss_review", function(e) {

                        e.preventDefault();
                        jQuery(".likebtn_review_notice").remove();
                        jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: {
                                action: "likebtn_dismiss_review",
                                likebtn_review: "'.$likebtn_review.'"
                            }
                        });
                    });
                });
            </script>
        ';
        _likebtn_notice($msg, 'updated is-dismissible likebtn_review_notice');
    }
}
add_action('admin_notices', 'likebtn_review_notice');

// Dismiss review notice
function likebtn_dismiss_review() {
    $likebtn_review = abs((int)$_POST['likebtn_review']);
    if (!$likebtn_review) {
        $likebtn_review = 1;
    }

    $likebtn_review = -1 * $likebtn_review;
    update_option('likebtn_review', $likebtn_review);
}
add_action('wp_ajax_likebtn_dismiss_review', 'likebtn_dismiss_review');

// Get item votes
function _likebtn_get_item_votes($identifier, $type = '')
{
    global $wpdb;

    $votes = array(
        LIKEBTN_META_KEY_LIKES => null,
        LIKEBTN_META_KEY_DISLIKES => null
    );

    $likebtn_entities = _likebtn_get_entities(true, true);
    $entity_info = _likebtn_parse_identifier($identifier);

    if ($entity_info['entity_name'] && $entity_info['entity_id'] 
        && array_key_exists($entity_info['entity_name'], $likebtn_entities) && is_numeric($entity_info['entity_id'])) 
    {
        // Entity
        switch ($entity_info['entity_name']) {
            case LIKEBTN_ENTITY_COMMENT:
                // Comment
                if (!$type || $type == LIKEBTN_META_KEY_LIKES) {
                    $votes[LIKEBTN_META_KEY_LIKES] = (int)get_comment_meta($entity_info['entity_id'], LIKEBTN_META_KEY_LIKES, true);
                }
                if (!$type || $type == LIKEBTN_META_KEY_DISLIKES) {
                    $votes[LIKEBTN_META_KEY_DISLIKES] = (int)get_comment_meta($entity_info['entity_id'], LIKEBTN_META_KEY_DISLIKES, true);
                }
                break;

            case LIKEBTN_ENTITY_BP_ACTIVITY_POST:
            case LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE:
            case LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT:
            case LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC:
                if (!_likebtn_is_bp_active()) {
                    break;
                }
                if (!$type || $type == LIKEBTN_META_KEY_LIKES) {
                    $votes[LIKEBTN_META_KEY_LIKES] = (int)bp_activity_get_meta($entity_info['entity_id'], LIKEBTN_META_KEY_LIKES, true);
                }
                if (!$type || $type == LIKEBTN_META_KEY_DISLIKES) {
                    $votes[LIKEBTN_META_KEY_DISLIKES] = (int)bp_activity_get_meta($entity_info['entity_id'], LIKEBTN_META_KEY_DISLIKES, true);
                }
                break;

            case LIKEBTN_ENTITY_BP_MEMBER:
                // BuddyPress Member Profile
                if (!$type || $type == LIKEBTN_META_KEY_LIKES) {
                    $votes[LIKEBTN_META_KEY_LIKES] = (int)bp_xprofile_get_meta($entity_info['entity_id'], LIKEBTN_META_KEY_LIKES, true);
                }
                if (!$type || $type == LIKEBTN_META_KEY_DISLIKES) {
                    $votes[LIKEBTN_META_KEY_DISLIKES] = (int)bp_xprofile_get_meta($entity_info['entity_id'], LIKEBTN_META_KEY_DISLIKES, true);
                }
                break;

            case LIKEBTN_ENTITY_BBP_USER:
            case LIKEBTN_ENTITY_USER:
                // bbPress Member Profile
                if (!$type || $type == LIKEBTN_META_KEY_LIKES) {
                    $votes[LIKEBTN_META_KEY_LIKES] = (int)get_user_meta($entity_info['entity_id'], LIKEBTN_META_KEY_LIKES, true);
                }
                if (!$type || $type == LIKEBTN_META_KEY_DISLIKES) {
                    $votes[LIKEBTN_META_KEY_DISLIKES] = (int)get_user_meta($entity_info['entity_id'], LIKEBTN_META_KEY_DISLIKES, true);
                }
                break;
            
            default:
                // Post
                if (!$type || $type == LIKEBTN_META_KEY_LIKES) {
                    $votes[LIKEBTN_META_KEY_LIKES] = get_post_meta($entity_info['entity_id'], LIKEBTN_META_KEY_LIKES, true);
                }
                if (!$type || $type == LIKEBTN_META_KEY_DISLIKES) {
                    $votes[LIKEBTN_META_KEY_DISLIKES] = (int)get_post_meta($entity_info['entity_id'], LIKEBTN_META_KEY_DISLIKES, true);
                }
                break;
        }
    } else {
        // Custom item
        $item_db = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT likes, dislikes
                FROM ".$wpdb->prefix.LIKEBTN_TABLE_ITEM."
                WHERE identifier = %s",
                $identifier
            )
        );

        // Custom identifier
        if ($item_db) {
             $votes[LIKEBTN_META_KEY_LIKES] = $item_db->likes;
             $votes[LIKEBTN_META_KEY_DISLIKES] = $item_db->dislikes;
        }
    }

    return $votes;
}

// Get/update CloudFlare IP ranges
function likebtnGetCfRanges()
{
    global $likebtn_cf_ip_ranges;

    $option_value = get_option('likebtn_cf_ranges');

    if (!$option_value) {
        update_option('likebtn_cf_ranges', $likebtn_cf_ip_ranges);
    } else {
        $likebtn_cf_ip_ranges = $option_value;
    }

    // Sync
    $now = time();
    if ($now - (int)get_option('likebtn_cf_sync_time') >= LIKEBTN_CF_SYNC_INTERVAL ) {

        $data = @file_get_contents(LIKEBTN_CF_SOURCE);

        if ($data) {
            $data_array = explode("\n", $data);
            if (is_array($data_array)) {
                $data_array = array_filter($data_array);
            }
            if (count($data_array)) {
                $likebtn_cf_ip_ranges = $data_array;                
                update_option('likebtn_cf_ranges', $likebtn_cf_ip_ranges);
            }
        }
        update_option('likebtn_cf_sync_time', $now);
    }

    return $likebtn_cf_ip_ranges;
}

function likebtnGetCfRangesIpv6()
{
    global $likebtn_cf_ip_ranges_ipv6;

    $option_value = get_option('likebtn_cf_ranges_ipv6');

    if (!$option_value) {
        update_option('likebtn_cf_ranges_ipv6', $likebtn_cf_ip_ranges_ipv6);
    } else {
        $likebtn_cf_ip_ranges_ipv6 = $option_value;
    }

    // Sync
    $now = time();
    if ($now - (int)get_option('likebtn_cf_ipv6_sync_time') >= LIKEBTN_CF_SYNC_INTERVAL ) {

        $data = @file_get_contents(LIKEBTN_CF_IPV6_SOURCE);

        if ($data) {
            $data_array = explode("\n", $data);
            if (is_array($data_array)) {
                $data_array = array_filter($data_array);
            }
            if (count($data_array)) {
                $likebtn_cf_ip_ranges_ipv6 = $data_array;           
                update_option('likebtn_cf_ranges_ipv6', $likebtn_cf_ip_ranges_ipv6);
            }
        }
        update_option('likebtn_cf_ipv6_sync_time', $now);
    }

    return $likebtn_cf_ip_ranges_ipv6;
}

// Get visitor IP
function _likebtn_get_ip($disable_hashing = false)
{
    // global $likebtn_cf_ip_ranges;
    // global $likebtn_cf_ip_ranges_ipv6;

    $ip = $_SERVER['REMOTE_ADDR'];

    // Behind CloudFlare
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        if (strlen($ip) == 39) {
            // ipv6
            $likebtn_cf_ip_ranges_ipv6 = likebtnGetCfRangesIpv6();
            foreach ($likebtn_cf_ip_ranges_ipv6 as $range) {
                if (_likebtn_ip_in_range_ipv6($ip, $range)) {
                    return $_SERVER['HTTP_CF_CONNECTING_IP'];
                }
            }
        } else {
            // ipv4
            $likebtn_cf_ip_ranges = likebtnGetCfRanges();
            foreach ($likebtn_cf_ip_ranges as $range) {
                if (_likebtn_ip_in_range($ip, $range)) {
                    return $_SERVER['HTTP_CF_CONNECTING_IP'];
                }
            }
        }
    }

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
            $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $x_ip = trim($addr[0]);
        } else {
            $x_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($x_ip != $_SERVER['SERVER_ADDR']) {
            $ip = $x_ip;
        }
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if (get_option('likebtn_gdpr') == '1' && !$disable_hashing) {
        $hash = strtoupper(md5($ip));
        $ip = substr($hash, 0, 12);
    }

    return $ip;
}

// This function takes 2 arguments, an IP address and a "range" in several
// different formats.
// Network ranges can be specified as:
// 1. Wildcard format:     1.2.3.*
// 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
// 3. Start-End IP format: 1.2.3.0-1.2.3.255
// The function will return true if the supplied IP is within the range.
// Note little validation is done on the range inputs - it expects you to
// use one of the above 3 formats.
function _likebtn_ip_in_range($ip, $range) {
    if (strpos($range, '/') !== false) {
        // $range is in IP/NETMASK format
        list($range, $netmask) = explode('/', $range, 2);
        if (strpos($netmask, '.') !== false) {
            // $netmask is a 255.255.0.0 format
            $netmask = str_replace('*', '0', $netmask);
            $netmask_dec = ip2long($netmask);
            return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
        } else {
            // $netmask is a CIDR size block
            // fix the range argument
            $x = explode('.', $range);
            while(count($x)<4) $x[] = '0';
            list($a,$b,$c,$d) = $x;
            $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
            $range_dec = ip2long($range);
            $ip_dec = ip2long($ip);
            
            # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
            #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));
            
            # Strategy 2 - Use math to create it
            $wildcard_dec = pow(2, (32-$netmask)) - 1;
            $netmask_dec = ~ $wildcard_dec;
            
            return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
        }
    } else {
        // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
        if (strpos($range, '*') !==false) { // a.b.*.* format
            // Just convert to A-B format by setting * to 0 for A and 255 for B
            $lower = str_replace('*', '0', $range);
            $upper = str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }
        
        if (strpos($range, '-')!==false) { // A-B format
            list($lower, $upper) = explode('-', $range, 2);
            $lower_dec = (float)sprintf("%u",ip2long($lower));
            $upper_dec = (float)sprintf("%u",ip2long($upper));
            $ip_dec = (float)sprintf("%u",ip2long($ip));
            return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
        }
        return false;
    } 
}

// converts inet_pton output to string with bits
function likebtn_inet_to_bits($inet) 
{
    $unpacked = unpack('A16', $inet);
    $unpacked = str_split($unpacked[1]);
    $binaryip = '';
    foreach ($unpacked as $char) {
         $binaryip .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
    }
    return $binaryip;
} 

function _likebtn_ip_in_range_ipv6($ip, $cidrnet) {
    if (!function_exists('inet_pton')) {
        return false;
    }   

    $ip = inet_pton($ip);
    $binaryip = likebtn_inet_to_bits($ip);

    list($net,$maskbits) = explode('/',$cidrnet);
    $net = inet_pton($net);
    $binarynet = likebtn_inet_to_bits($net);

    $ip_net_bits = substr($binaryip,0,$maskbits);
    $net_bits    = substr($binarynet,0,$maskbits);

    if ($ip_net_bits !== $net_bits) {
        return false;
    } else {
        return true;
    }
}

function _likebtn_set_default_settings($parameter) {
    global $likebtn_settings;

    $likebtn_entities = _likebtn_get_entities();
    foreach ($likebtn_entities as $entity_name => $entity_title) {
        update_option('likebtn_settings_'.$parameter.'_'.$entity_name, $likebtn_settings[$parameter]['default']);
    }
}

// Get all bbPress forums
function _likebtn_get_forums()
{
    global $wpdb;
    return $wpdb->get_results("
        SELECT ID, post_title
        FROM {$wpdb->posts}
        WHERE post_status = 'publish'
        AND post_type = 'forum'
        ORDER BY ID
    ");
}

// Get entity name whose settings should be copied
function _likebtn_used_entity_name($entity_name)
{
    $use_entity_name = get_option('likebtn_use_settings_from_' . $entity_name);
    if ($use_entity_name) {
        return $use_entity_name;
    } else {
        return $entity_name;
    }
}

function likebtn_loggedin_user_posts_where($where, &$wp_query)
{
    global $wpdb;

    $user_id = get_current_user_id();
    if ($user_id) {
        $where .= ' AND EXISTS(SELECT ID FROM '.$wpdb->prefix.LIKEBTN_TABLE_VOTE.' WHERE '.$wpdb->prefix.LIKEBTN_TABLE_VOTE.'.identifier = CONCAT('.$wpdb->posts.'.post_type, "_", '.$wpdb->posts.'.ID) AND '.$wpdb->prefix.LIKEBTN_TABLE_VOTE.'.user_id = '.$user_id.') ';
    }  else {
        $where .= ' AND 1 = 0 ';
    }

    return $where;

}
//add_filter('posts_where', 'likebtn_loggedin_user_posts_where', 10, 2);

function likebtn_to_csv($results = array(), $filename = '', $fields = array(), $encoding = 'UCS-2LE', $separator = "TAB")
{
    if ($separator == 'TAB') {
        $separator = "\t";
    }
    
    $schema_insert = '"'.implode('"'.$separator.'"', $fields).'"';
    $out = $schema_insert."\n";

    foreach($results as $row) {
        $schema_insert = '';
        foreach ($row as $row_value) {
            $value_prepared = str_replace('"', '""', $row_value);
            $value_prepared = str_replace("\t", '', $value_prepared);
            $schema_insert .= '"'.$value_prepared.'"'.$separator;
        }

        $out .= $schema_insert."\n";
    }

    if (ob_get_contents()) {
        ob_clean();
    }

    if ($encoding != 'UTF-8') {
        $out = iconv("UTF-8", $encoding, $out);
        if ($encoding == 'UCS-2LE') {
            $out = "\xFF\xFE".$out;
        }
    }

    header("Cache-Control: must-revalidate, no-cache, no-store, private");
    header("Content-Length: " . strlen($out));
    //header("Content-type: text/csv");
    header("Content-type: application/csv; charset=UCS-2LE");
    header("Content-Disposition: attachment; filename=$filename");
    echo $out;
    exit;
}

// Get voters IPs as comma separated string
function likebtn_get_voters_ips($entity_name, $entity_id)
{
    global $wpdb;

    $identifier = $entity_name.'_'.$entity_id;

    $query = "
        SELECT GROUP_CONCAT(ip) as ips
        FROM ".$wpdb->prefix.LIKEBTN_TABLE_VOTE."
        WHERE identifier = %s
            AND ip is not NULL
            AND ip != ''
        GROUP BY identifier
    ";

    $query_prepared = $wpdb->prepare($query, $identifier);
    $ips = $wpdb->get_row($query_prepared);

    if (isset($ips->ips)) {
        return $ips->ips;
    } else {
        return '';
    }
}

function likebtn_build_sort_by($sort_by, $order)
{
    parse_str($_SERVER['QUERY_STRING'], $args);
    $args['likebtn_sort_by'] = $sort_by;
    $args['likebtn_sort_by_order'] = $order;

    return likebtn_get_script_name().'?'.http_build_query($args);
}

function likebtn_get_script_name()
{
    $script_name = $_SERVER['SCRIPT_NAME'];
    if (!$script_name) {
        $script_name = $_SERVER['SCRIPT_URL'];
    }
    if (!$script_name) {
        $script_name = $_SERVER['PHP_SELF'];
    }
    return $script_name;
}

function likebtn_import_config()
{
    $error_message = '';

    if (empty($_POST['likebtn_import_config']) || !(bool)current_user_can('manage_options')) {
        return;
    }

    $config = '';
    try {
        $config = base64_decode($_POST['likebtn_import_config']);
        // Sanitize
        $config = preg_replace('/^[^\{(\s|\n\s)*(likebtn_settings_options|likebtn_buttons_options|likebtn_settings)(("\w*"):(\s)*("\w*"|\d*|(\{(\s|\n\s)*(("\w*"):(\s)*("\w*(,\w+)*"|\d{1,}|\[(\s|\n\s)*(\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):(\s)*("\w*"|\d{1,}))*(\s|\n)*\})){1}(\s|\n\s)*(,(\s|\n\s)*\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):(\s)*("\w*"|\d{1,}))*(\s|\n)*\})?)*(\s|\n\s)*\]))((,(\s|\n\s)*"\w*"):(\s)*("\w*(,\w+)*"|\d{1,}|\[(\s|\n\s)*(\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):(\s)*("\w*"|\d{1,}))*(\s|\n)*\})){1}(\s|\n\s)*(,(\s|\n\s)*\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):("\w*"|\d{1,}))*(\s|\n)*\})?)*(\s|\n\s)*\]))*(\s|\n\s)*\}){1}))((,(\s|\n\s)*"\w*"):(\s)*("\w*"|\d*|(\{(\s|\n\s)*(("\w*"):(\s)*("\w*(,\w+)*"|\d{1,}|\[(\s|\n\s)*(\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):(\s)*("\w*"|\d{1,}))*(\s|\n)*\})){1}(\s|\n\s)*(,(\s|\n\s)*\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):(\s)*("\w*"|\d{1,}))*(\s|\n)*\})?)*(\s|\n\s)*\]))((,(\s|\n\s)*"\w*"):(\s)*("\w*(,\w+)*"|\d{1,}|\[(\s|\n\s)*(\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):(\s)*("\w*"|\d{1,}))*(\s|\n)*\})){1}(\s|\n\s)*(,(\s|\n\s)*\{(\s|\n\s)*(("\w*"):(\s)*(("\w*"|\d{1,}))((,(\s|\n\s)*"\w*"):("\w*"|\d{1,}))*(\s|\n)*\})?)*(\s|\n\s)*\]))*(\s|\n\s)*\}){1}))*(\s|\n)*\}]$/s', '', $config);
    } catch (Exception $e) {
        $error_message = $e->getMesssage();
    }

    if (!$config || $error_message) {
        _likebtn_add_notice(array(
            'msg' => __('Error occured importing Like button configuration: incorrect configuration string.', 'likebtn-like-button').' '.$error_message,
            'class' => 'error'
        ));
        return;
    }

    try {
        $config = json_decode($config, true);
    } catch (Exception $e) {
        $error_message = $e->getMesssage();
    }
    if (!is_array($config) || $error_message) {
        _likebtn_add_notice(array(
            'msg' => __('Error occured importing Like button configuration: incorrect configuration string.', 'likebtn-like-button').' '.$error_message,
            'class' => 'error'
        ));
        return;
    }

    $imported = false;

    foreach ($config as $section_name => $section_options) {
        if (!in_array($section_name, array('likebtn_settings_options', 'likebtn_buttons_options', 'likebtn_settings'))) {
            continue;
        }
        foreach ($section_options as $option_name => $option_value) {
            if (!preg_match("/^likebtn_/", $option_name)) {
                continue;
            }
            update_option($option_name, sanitize_option($option_name, $option_value));
            if (!$imported) {
                $imported = true;
            }
        }
    }

    if ($imported) {
        _likebtn_add_notice(array(
            'msg' => __('Like Button configuration has been successfully imported!', 'likebtn-like-button')
        ));
    } else {
        _likebtn_add_notice(array(
            'msg' => __('There were no any options imported.', 'likebtn-like-button'),
            'class' => 'error'
        ));
    }
}

// Get encoded Like button config
function likebtn_get_config()
{
    global $likebtn_settings;
    global $likebtn_settings_options;
    global $likebtn_buttons_options;

    $config = array();

    $likebtn_entities = _likebtn_get_entities(false, false, false);

    foreach ($likebtn_settings_options as $option_name=>$option_value) {
        $config['likebtn_settings_options'][$option_name] = get_option($option_name);
    }

    foreach ($likebtn_entities as $entity_name => $entity_title) {
        foreach ($likebtn_buttons_options as $option_name=>$option_value) {
            $config['likebtn_buttons_options'][$option_name.'_'.$entity_name] = get_option($option_name.'_'.$entity_name);
        }
        foreach ($likebtn_settings as $option_name => $option_info) {
            $config['likebtn_settings']['likebtn_settings_'.$option_name.'_'.$entity_name] = get_option('likebtn_settings_'.$option_name.'_'.$entity_name);
        }
    }
    
    return base64_encode(json_encode($config));
}

// Check if UltimateMember plugin is active
function likebtn_is_um_active() {
    return defined('ultimatemember_version');
}

/**
 * Loads theme files in appropriate hierarchy:
 * 1) child theme,
 * 2) parent template
 * 3) plugin resources
 *
 * Will look in the PLUGIN_NAME directory in a theme
 **/
function likebtn_get_template_hierarchy($template) {

    if ( $theme_file = locate_template( array(LIKEBTN_PLUGIN_NAME . '/' . $template ) ) ) {
        $file = $theme_file;
    } else {
        $file = dirname(__FILE__) . '/templates/' . $template;
    }

    return $file;
}

function likebtn_votes_query($query_limit = '', $query_select = '', $query_orderby = '', $query_where = '')
{
    global $wpdb;
    global $likebtn_countries;

    $query_parameters = array();

    // Multisite - available for super admin only
    $blogs = array();
    $blog_list = array();
    $votes_blog_id = '';
    $prefix_prepared = $wpdb->prefix;
    if (is_multisite() && is_super_admin()) {

        global $blog_id;

        $blog_list = $wpdb->get_results("
            SELECT blog_id, domain
            FROM {$wpdb->blogs}
            WHERE site_id = '{$wpdb->siteid}'
            AND spam = '0'
            AND deleted = '0'
            AND archived = '0'
            ORDER BY blog_id
        ");

        // Place current blog on the first place
        foreach ($blog_list as $blog) {
            if ($blog->blog_id == $blog_id) {
                $blogs["{$blog->blog_id}"] = get_blog_option($blog->blog_id, 'blogname') . ' - ' . $blog->domain;
                break;
            }
        }
        foreach ($blog_list as $blog) {
            if ($blog->blog_id != $blog_id) {
                $blogs["{$blog->blog_id}"] = get_blog_option($blog->blog_id, 'blogname') . ' - ' . $blog->domain;
            }
        }

        // Add all
        $blogs['all'] = __('All Sites');

        // Determine selected blog id
        if (isset($_GET['likebtn_blog_id'])) {
            if ($_GET['likebtn_blog_id'] == 'all') {
                $votes_blog_id = 'all';
            } else {
                // Check if blog with ID exists
                foreach ($blog_list as $blog) {
                    if ($blog->blog_id == (int)$_GET['likebtn_blog_id']) {
                        $votes_blog_id = (int)$_GET['likebtn_blog_id'];
                        break;
                    }
                }
            }
        } else {
            if (isset($blog_list[0])) {
                $votes_blog_id = $blog_id;
            } else {
               // 
            }
        }

        // Prepare prefix if this is not main blog
        if ($blog_id != 1) {
            $prefix_prepared = substr($wpdb->prefix, 0, strlen($wpdb->prefix)-strlen($blog_id)-1);
        }
    }

    $entity_name = '';
    if (!empty($_GET['likebtn_entity_name'])) {
        // likebtn_entity_name must be able to contain any text
        // sanitized via $wpdb-prepare() and esc_sql() below
        $entity_name = $_GET['likebtn_entity_name'];
    }
    $post_id = '';
    if (isset($_GET['likebtn_post_id'])) {
        $post_id = (int)$_GET['likebtn_post_id'];
    }
    $user_id = '';
    if (isset($_GET['likebtn_user_id'])) {
        $user_id = (int)$_GET['likebtn_user_id'];
    }
    $ip = '';
    if (isset($_GET['likebtn_ip'])) {
        $ip = preg_replace("/[^0-9\.:a-zA-Z]/", '', $_GET['likebtn_ip']);
    }
    $vote_type = '';
    if (isset($_GET['likebtn_vote_type'])) {
        $vote_type = sanitize_text_field($_GET['likebtn_vote_type']);
        if (!in_array($vote_type, array(LIKEBTN_VOTE_LIKE, LIKEBTN_VOTE_DISLIKE))) {
            $vote_type = '';
        }
    }
    $country = '';
    if (!empty($_GET['likebtn_country'])) {
        $country = sanitize_text_field($_GET['likebtn_country']);
        if (!array_key_exists($country, $likebtn_countries)) {
            $country = '';
        }
    }

    //$query_select = '';
    $query_join = '';
    // query parameters
    //$query_where = '';

    // Post type and ID
    if ($entity_name && $post_id && $entity_name != LIKEBTN_ENTITY_CUSTOM_ITEM) {
        $query_where .= ' AND v.identifier = %s ';
        $query_parameters[] = $entity_name.'_'.$post_id;
    } elseif ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM && $post_id) {
        $query_where .= ' AND i.ID is not NULL AND v.identifier = %s ';
        $query_parameters[] = $post_id;
    } elseif ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM) {
        $query_where .= ' AND i.ID is not NULL ';
    } elseif ($entity_name) {
        $query_where .= ' AND v.identifier LIKE "'.esc_sql($entity_name).'_%%" ';
    } elseif ($post_id) {
        $query_where .= ' AND v.identifier LIKE "%%_%d" ';
        $query_parameters[] = $post_id;
    }

    // Order by
    if (!$query_orderby) {
        $query_orderby = ' ORDER BY created_at DESC ';
    }

    // User ID
    if ($user_id) {
        $query_where .= ' AND v.user_id = %d ';
        $query_parameters[] = $user_id;
    }

    // IP
    if ($ip) {
        $query_where .= ' AND v.ip = %s ';
        $query_parameters[] = $ip;
    }

    // Vote type
    if ($vote_type) {
        $query_where .= ' AND v.type = %d ';
        $query_parameters[] = $vote_type;
    }

    // Country
    if ($country) {
        $query_where .= ' AND v.country = %s ';
        $query_parameters[] = $country;
    }

    // For Multisite
    $query = '';
    if ($votes_blog_id && $votes_blog_id != 1 && $votes_blog_id != 'all') {
        $prefix = "{$prefix_prepared}{$votes_blog_id}_";
        if (!$query_select) {
            $query_select = ' SQL_CALC_FOUND_ROWS v.id, v.identifier, v.type, v.user_id, v.ip, v.created_at, v.country, i.ID as item_id, i.url ';
        }
        $query = _likebtn_get_votes_sql($prefix, $query_where, $query_orderby, $query_limit, $query_select, $query_join);
        $query_prepared = $wpdb->prepare($query, $query_parameters);
    } else if ($votes_blog_id == 'all') {
        foreach ($blog_list as $blog) {
            if ($blog->blog_id == 1) {
                $prefix = $prefix_prepared;
            } else {
                $prefix = "{$prefix_prepared}{$blog->blog_id}_";
            }
            if (!$query_select) {
                $blog_query_select = $blog->blog_id . ' as blog_id, v.id, v.identifier, v.type, v.user_id, v.ip, v.created_at, v.country, i.ID as item_id, i.url ';
            } else {
                $blog_query_select = $query_select;
            }
            $query_list[] = $wpdb->prepare(_likebtn_get_votes_sql($prefix, $query_where, '', '', $blog_query_select, $query_join), $query_parameters);
        }
        $query_prepared = ' SELECT SQL_CALC_FOUND_ROWS * from (' . implode(' UNION ', $query_list) . ") query {$query_orderby} {$query_limit} ";
    } else {
        if (!$query_select) {
            $query_select = ' SQL_CALC_FOUND_ROWS v.id, v.identifier, v.type, v.user_id, v.ip, v.created_at, v.country, i.ID as item_id, i.url ';
        }
        $query = _likebtn_get_votes_sql($prefix_prepared, $query_where, $query_orderby, $query_limit, $query_select, $query_join);
        if (count($query_parameters)) {
            $query_prepared = $wpdb->prepare($query, $query_parameters);
        } else {
            $query_prepared = $query;
        }
    }

    return array($query_prepared, $blogs, $votes_blog_id, $entity_name, $post_id, $user_id, $ip, $vote_type, $country);
}

// Get current language - 2 chars
function likebtn_cur_lang()
{
    $language_code = '';

    if (function_exists('bogo_get_user_locale')) {
        // Bogo
        $language_code = bogo_get_user_locale();
    } else if (function_exists('icl_get_current_language')) {
        // WPML
        $language_code = icl_get_current_language();
    } else if (function_exists('icl_object_id') && defined('ICL_LANGUAGE_CODE')) {
        // WPML
        $language_code = ICL_LANGUAGE_CODE;
    }
    if (!$language_code) {
        $language_code = get_locale();
    }
    if (!$language_code) {
        $language_code = get_bloginfo("language");
    }

    if ($language_code) {
        return substr($language_code, 0, 2);
    } else {
        return '';
    }
}

// Get current post ID taking WPML into account
function likebtn_get_the_ID($entity_name)
{
    if (likebtn_is_wpml_active()) {
        // WPML is active
        global $sitepress;
        if (!$entity_name) {
            $entity_name = LIKEBTN_ENTITY_POST;
        }

        if (has_filter( 'wpml_object_id' )) {
            $entity_id = apply_filters('wpml_object_id', get_the_ID(), $entity_name, false, $sitepress->get_default_language());
        } else {
            $entity_id = icl_object_id(get_the_ID(), $entity_name, false, $sitepress->get_default_language());
        }

        return $entity_id;
    } else {
        return get_the_ID();
    }
}

// Is WPML active
function likebtn_is_wpml_active()
{
    if (class_exists('SitePress')) {
        return true;
    } else {
        return false;
    }
}

// Set votes for a post
function likebtn_set_post_votes($entity_id, $likes, $dislikes, $likes_minus_dislikes)
{
    if ($likes != -1) {
        if (count(get_post_meta($entity_id, LIKEBTN_META_KEY_LIKES)) > 1) {
            delete_post_meta($entity_id, LIKEBTN_META_KEY_LIKES);
            add_post_meta($entity_id, LIKEBTN_META_KEY_LIKES, $likes, true);
        } else {
            update_post_meta($entity_id, LIKEBTN_META_KEY_LIKES, $likes);
        }
    }
    if ($dislikes != -1) {
        if (count(get_post_meta($entity_id, LIKEBTN_META_KEY_DISLIKES)) > 1) {
            delete_post_meta($entity_id, LIKEBTN_META_KEY_DISLIKES);
            add_post_meta($entity_id, LIKEBTN_META_KEY_DISLIKES, $dislikes, true);
        } else {
            update_post_meta($entity_id, LIKEBTN_META_KEY_DISLIKES, $dislikes);
        }
    }
    if ($likes_minus_dislikes !== null) {
        if (count(get_post_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES)) > 1) {
            delete_post_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES);
            add_post_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes, true);
        } else {
            update_post_meta($entity_id, LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, $likes_minus_dislikes);
        }
    }
}

function likebtn_calc_use_settings_from($use_entity_name)
{
    $list = array();

    if (!$use_entity_name) {
        return '';
    }
    $orig_use_entity_name = $use_entity_name;
    $next_use_entity_name = $use_entity_name;
    do {
        if ($next_use_entity_name) {
            if (!in_array($next_use_entity_name, $list)) {
                $list[] = $next_use_entity_name;
                $use_entity_name = $next_use_entity_name;
            } else {
                // cycle
                return $orig_use_entity_name;
            }
        } else {
            return $use_entity_name;
        }
    } while ($next_use_entity_name = get_option('likebtn_use_settings_from_' . $next_use_entity_name));

    return $use_entity_name;
}

function likebtn_default_notify_from()
{
    return 'noreply@'.likebtn_site_domain();
}

function likebtn_site_domain()
{
    return parse_url(get_option('siteurl'), PHP_URL_HOST);
}

function likebtn_default_notify_text()
{
    $text = <<<TEXT
New {vote_type} on {domain}

<strong>Item info</strong>
{item_type}: <a href="{item_url}">{item_title}</a> <small>(<a href="{item_admin_stats_url}">view stats in admin</a>)</small>
Likes: {item_likes}
Dislikes: {item_dislikes}

<strong>Voter info</strong>
IP address: <a href="{vote_ip_url}">{vote_ip}</a>
User: <a href="{user_url}">{user_login}</a> <small>(<a href="{user_votes_url}">view user votes</a>)</small>
TEXT;
    return $text;
}

function likebtn_check_plan($plan)
{
    if (get_option('likebtn_plan') >= $plan) {
        return true;
    } else {
        return false;
    }
}

// Send email to admin on new vote
function likebtn_send_vote_notification($vars, $options = array())
{
    $return = array(
        'result' => 'error',
        'error_message' => ''
    );

    if (!empty($options['likebtn_notify_to'])) {
        $notify_to = $options['likebtn_notify_to'];
    } else {
        $notify_to = get_option('likebtn_notify_to');
    }
    $to_emails = array();
    if ($notify_to) {
        $to_emails = explode(',', $notify_to);
    } else {
        $return['error_message'] = __('Please specify email recipient(s)', 'likebtn-like-button');
        return $return;
    }

    if (!empty($options['likebtn_notify_from'])) {
        $from = $options['likebtn_notify_from'];
    } else {
        $from = get_option('likebtn_notify_from');
    }
    if (!$from) {
        $return['error_message'] = __('Email from field is empty', 'likebtn-like-button');
        return $return;
    }

    if (!empty($options['likebtn_notify_subject'])) {
        $subject = $options['likebtn_notify_subject'];
    } else {
        $subject = get_option('likebtn_notify_subject');
    }
    if (!$subject) {
        $return['error_message'] = __('Please specify email subject', 'likebtn-like-button');
        return $return;
    }

    if (!empty($options['likebtn_notify_text'])) {
        $template = $options['likebtn_notify_text'];
    } else {
        $template = get_option('likebtn_notify_text');
    }
    if (!$template) {
        $return['error_message'] = __('Please specify email notification text', 'likebtn-like-button');
        return $return;
    }
    // Prepare content
    $content = nl2br($template);
    foreach($vars as $key => $value){
        $content = str_replace('{'.strtolower($key).'}', $value, $content);
        $subject = str_replace('{'.strtolower($key).'}', $value, $subject);
    }

    // Get body
    $body = likebtn_admin_mail_body($content);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: '.$from
    );

    $error_emails = 0;
    foreach ($to_emails as $to) {
        $to = trim($to);
        $send_result = wp_mail($to, $subject, $body, $headers);

        if (!$send_result) {
            $error_emails++;
        }
    }
    if ($error_emails == count($to_emails)) {
        $return['error_message'] = __('Error occured sending email(s) to: '.get_option('likebtn_notify_to'), 'likebtn-like-button');
        return $return;
    }

    $return['result'] = 'success';

    return $return;
}

function likebtn_admin_mail_body($content)
{
    $blogname = get_option('blogname');
    $siteurl = get_option('siteurl');

    ob_start();
    include(_likebtn_get_template_path(LIKEBTN_TEMPLATE_ADMIN_MAIL));
    $body = ob_get_contents();
    ob_get_clean();

    return $body;
}

function likebtn_vote_title($vote_type)
{
    if ((int)$vote_type == LIKEBTN_VOTE_LIKE) {
        return __('like', 'likebtn-like-button');
    } else {
        return __('dislike', 'likebtn-like-button');
    }
}

function likebtn_is_real_ip($ip)
{
    if (strstr($ip, '.') || strstr($ip, ':')) {
        return true;
    } else {
        return false;
    }
}

function likebtn_non_post_entity_types()
{
    return array(
        LIKEBTN_ENTITY_COMMENT,
        LIKEBTN_ENTITY_ATTACHMENT, 
        LIKEBTN_ENTITY_CUSTOM_ITEM, 
        LIKEBTN_ENTITY_USER, 
        LIKEBTN_ENTITY_BP_MEMBER, 
        LIKEBTN_ENTITY_BBP_USER, 
        LIKEBTN_ENTITY_UM_USER, 
    );
}

function likebtn_comment_post_id($comment_id)
{
    $comment = get_comment($comment_id);
    if ($comment) {
        return $comment->comment_post_ID;
    }
    return null;
}

/**
 * Buttons tab
 */
require_once(dirname(__FILE__) . '/includes/tab_buttons.php');

/**
 * Settings tab
 */
require_once(dirname(__FILE__) . '/includes/tab_settings.php');

/**
 * Votes tab
 */
require_once(dirname(__FILE__) . '/includes/tab_votes.php');

/**
 * BuddyPress
 */
require_once(dirname(__FILE__) . '/includes/buddypress.php');

/**
 * bbPress
 */
require_once(dirname(__FILE__) . '/includes/bbpress.php');

/**
 * Ultimate Member
 */
require_once(dirname(__FILE__) . '/includes/um.php');

/**
 * GamiPress
 */
require_once(dirname(__FILE__) . '/includes/gamipress.php');

/**
 * Posts meta columns
 */
require_once(dirname(__FILE__) . '/includes/meta_columns.php');

/**
 * Open Graph
 */
require_once(dirname(__FILE__) . '/includes/open_graph.php');

/**
 * Most liked content widget
 */
require_once(dirname(__FILE__) . '/includes/likebtn_like_button_most_liked_widget.class.php');
