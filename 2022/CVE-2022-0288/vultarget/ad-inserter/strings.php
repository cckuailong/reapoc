<?php

define ('DEFAULT_AD_NAME',              __('Block', 'ad-inserter'));
define ('DEFAULT_AD_TITLE',             __('Advertisements', 'ad-inserter'));
define ('AI_DEFAULT_ADB_MESSAGE',       __("<p><strong>Blocked because of Ad Blocker</strong></p>\n<p>It seems that you are using some ad blocking software which is preventing the page from fully loading. Please whitelist this website or disable ad blocking software.</p>", 'ad-inserter'));

define ('DEFAULT_VIEWPORT_NAME_1',      _x('Desktop', 'Viewport name', 'ad-inserter'));
define ('DEFAULT_VIEWPORT_NAME_2',      _x('Tablet', 'Viewport name', 'ad-inserter'));
define ('DEFAULT_VIEWPORT_NAME_3',      _x('Phone', 'Viewport name', 'ad-inserter'));

define ('DEFAULT_COUNTRY_GROUP_NAME',   _x('Group', 'ad-inserter'));

define ('AI_TEXT_INSERT',               __('Insert', 'ad-inserter'));

                                        // translators: Menu items
define ('AI_TEXT_DISABLED',             __('Disabled', 'ad-inserter'));
define ('AI_TEXT_BEFORE_POST',          __('Before post', 'ad-inserter'));
define ('AI_TEXT_AFTER_POST',           __('After post', 'ad-inserter'));
define ('AI_TEXT_BEFORE_CONTENT',       __('Before content', 'ad-inserter'));
define ('AI_TEXT_AFTER_CONTENT',        __('After content', 'ad-inserter'));
define ('AI_TEXT_BEFORE_PARAGRAPH',     __('Before paragraph', 'ad-inserter'));
define ('AI_TEXT_AFTER_PARAGRAPH',      __('After paragraph', 'ad-inserter'));
define ('AI_TEXT_BEFORE_EXCERPT',       __('Before excerpt', 'ad-inserter'));
define ('AI_TEXT_AFTER_EXCERPT',        __('After excerpt', 'ad-inserter'));
define ('AI_TEXT_BETWEEN_POSTS',        __('Between posts', 'ad-inserter'));
define ('AI_TEXT_BEFORE_COMMENTS',      __('Before comments', 'ad-inserter'));
define ('AI_TEXT_BETWEEN_COMMENTS',     __('Between comments', 'ad-inserter'));
define ('AI_TEXT_AFTER_COMMENTS',       __('After comments', 'ad-inserter'));
define ('AI_TEXT_ABOVE_HEADER',         __('Above header', 'ad-inserter'));
define ('AI_TEXT_FOOTER',               __('Footer', 'ad-inserter'));
define ('AI_TEXT_BEFORE_HTML_ELEMENT',  __('Before HTML element', 'ad-inserter'));
define ('AI_TEXT_AFTER_HTML_ELEMENT',   __('After HTML element', 'ad-inserter'));
define ('AI_TEXT_INSIDE_HTML_ELEMENT',  __('Inside HTML element', 'ad-inserter'));
define ('AI_TEXT_BEFORE_IMAGE',         __('Before image', 'ad-inserter'));
define ('AI_TEXT_AFTER_IMAGE',          __('After image', 'ad-inserter'));

define('AI_TEXT_DO_NOT_INSERT',         __('do not insert', 'ad-inserter'));
define('AI_TEXT_TRY_TO_SHIFT_POSITION', __('try to shift position', 'ad-inserter'));

define ('AI_TEXT_ABOVE',                __('above', 'ad-inserter'));
define ('AI_TEXT_BELOW',                __('below', 'ad-inserter'));
define ('AI_TEXT_ABOVE_AND_THEN_BELOW', __('above and then below', 'ad-inserter'));
define ('AI_TEXT_BELOW_AND_THEN_ABOVE', __('below and then above', 'ad-inserter'));

define ('AI_TEXT_DIRECTION_FROM_TOP',   __('from top', 'ad-inserter'));
define ('AI_TEXT_DIRECTION_FROM_BOTTOM',__('from bottom', 'ad-inserter'));

define ('AI_TEXT_CONTAIN',              _x('contain', 'paragraphs', 'ad-inserter'));
define ('AI_TEXT_DO_NOT_CONTAIN',       _x('do not contain', 'paragraphs', 'ad-inserter'));

define ('AI_TEXT_DO_NOT_COUNT',         __('Do not count', 'ad-inserter'));
define ('AI_TEXT_COUNT_ONLY',           __('Count only',  'ad-inserter'));

define('AI_TEXT_DISPLAY_ALL_USERS',           _x('all users', 'insert for', 'ad-inserter'));
define('AI_TEXT_DISPLAY_LOGGED_IN_USERS',     _x('logged in users', 'insert for', 'ad-inserter'));
define('AI_TEXT_DISPLAY_NOT_LOGGED_IN_USERS', _x('not logged in users', 'insert for', 'ad-inserter'));
define('AI_TEXT_DISPLAY_ADMINISTRATORS',      _x('administrators', 'insert for', 'ad-inserter'));

define('AI_TEXT_BLACK_LIST',            __('Black list', 'ad-inserter'));
define('AI_TEXT_WHITE_LIST',            __('White list', 'ad-inserter'));

define('AI_TEXT_DEFAULT',               _x('Default', 'alignment', 'ad-inserter'));
define('AI_TEXT_LEFT',                  _x('Left', 'alignment', 'ad-inserter'));
define('AI_TEXT_RIGHT',                 _x('Right', 'alignment', 'ad-inserter'));
define('AI_TEXT_CENTER',                _x('Center', 'alignment', 'ad-inserter'));
define('AI_TEXT_POSITION_CENTER',       _x('Center', 'position', 'ad-inserter'));
define('AI_TEXT_FLOAT_LEFT',            _x('Float left', 'alignment', 'ad-inserter'));
define('AI_TEXT_FLOAT_RIGHT',           _x('Float right', 'alignment', 'ad-inserter'));
define('AI_TEXT_NO_WRAPPING',           _x('No wrapping', 'alignment', 'ad-inserter'));
define('AI_TEXT_CUSTOM_CSS',            __('Custom CSS', 'ad-inserter'));
define('AI_TEXT_STICKY_LEFT',           __('Sticky left', 'ad-inserter'));
define('AI_TEXT_STICKY_RIGHT',          __('Sticky right', 'ad-inserter'));
define('AI_TEXT_STICKY_TOP',            __('Sticky top', 'ad-inserter'));
define('AI_TEXT_STICKY_BOTTOM',         __('Sticky bottom', 'ad-inserter'));
define('AI_TEXT_STICKY',                _x('Sticky', 'alignment', 'ad-inserter'));

define ('AI_TEXT_AUTO_COUNTER',                   _x('auto counter', 'using', 'ad-inserter'));
define ('AI_TEXT_PHP_FUNCTION_CALLS_COUNTER',     _x('PHP function calls counter', 'using', 'ad-inserter'));
define ('AI_TEXT_CONTENT_PROCESSING_COUNTER',     _x('content processing counter', 'using', 'ad-inserter'));
define ('AI_TEXT_EXCERPT_PROCESSING_COUNTER',     _x('excerpt processing counter', 'using', 'ad-inserter'));
define ('AI_TEXT_BEFORE_POST_PROCESSING_COUNTER', _x('before post processing counter', 'using', 'ad-inserter'));
define ('AI_TEXT_AFTER_POST_PROCESSING_COUNTER',  _x('after post processing counter', 'using', 'ad-inserter'));
define ('AI_TEXT_WIDGET_DRAWING_COUNTER',         _x('widget drawing counter', 'using', 'ad-inserter'));
define ('AI_TEXT_SUBPAGES_COUNTER',               _x('subpages counter', 'using', 'ad-inserter'));
define ('AI_TEXT_POSTS_COUNTER',                  _x('posts counter', 'using', 'ad-inserter'));
define ('AI_TEXT_PARAGRAPHS_COUNTER',             _x('paragraphs counter', 'using', 'ad-inserter'));
define ('AI_TEXT_COMMENTS_COUNTER',               _x('comments counter', 'using', 'ad-inserter'));
define ('AI_TEXT_IMAGES_COUNTER',                 _x('images counter', 'using', 'ad-inserter'));

define ('AI_TEXT_POSTS_NO_INDIVIDUALL_EXCEPTIONS', '');
define ('AI_TEXT_POSTS_INDIVIDUALLY_DISABLED',     _x('Individually disabled', 'posts', 'ad-inserter'));
define ('AI_TEXT_POSTS_INDIVIDUALLY_ENABLED',      _x('Individually enabled', 'posts', 'ad-inserter'));
define ('AI_TEXT_PAGES_NO_INDIVIDUAL_EXCEPTIONS',  '');
define ('AI_TEXT_PAGES_INDIVIDUALLY_DISABLED',     _x('Individually disabled', 'static pages', 'ad-inserter'));
define ('AI_TEXT_PAGES_INDIVIDUALLY_ENABLED',      _x('Individually enabled', 'static pages', 'ad-inserter'));

define ('AI_TEXT_SERVER_SIDE',            __('Server-side', 'ad-inserter'));
define ('AI_TEXT_CLIENT_SIDE',            _x('Client-side', 'Insertion', 'ad-inserter'));
define ('AI_TEXT_CLIENT_SIDE_SHOW',       _x('Client-side show', 'Dynamic blocks', 'ad-inserter'));
define ('AI_TEXT_CLIENT_SIDE_INSERT',     _x('Client-side insert', 'Dynamic blocks', 'ad-inserter'));
define ('AI_TEXT_SERVER_SIDE_W3TC',       _x('Server-side using W3 Total Cache', 'Insertion', 'ad-inserter'));
//define ('AI_TEXT_CLIENT_SIDE_DOM_READY',  _x('Client-side when DOM ready', 'Insertion', 'ad-inserter'));

define ('AI_TEXT_PREPEND_CONTENT',        __('Prepend content', 'ad-inserter'));
define ('AI_TEXT_APPEND_CONTENT',         __('Append content', 'ad-inserter'));
define ('AI_TEXT_REPLACE_CONTENT',        __('Replace content', 'ad-inserter'));
define ('AI_TEXT_REPLACE_ELEMENT',        __('Replace element', 'ad-inserter'));

define ('AI_TEXT_DESKTOP_DEVICES',        __('desktop devices', 'ad-inserter'));
define ('AI_TEXT_MOBILE_DEVICES',         __('mobile devices', 'ad-inserter'));
define ('AI_TEXT_TABLET_DEVICES',         __('tablet devices', 'ad-inserter'));
define ('AI_TEXT_PHONE_DEVICES',          __('phone devices', 'ad-inserter'));
define ('AI_TEXT_DESKTOP_TABLET_DEVICES', __('desktop and tablet devices', 'ad-inserter'));
define ('AI_TEXT_DESKTOP_PHONE_DEVICES',  __('desktop and phone devices', 'ad-inserter'));

define ('AI_TEXT_STICK_TO_THE_LEFT',           __('Stick to the left', 'ad-inserter'));
define ('AI_TEXT_STICK_TO_THE_CONTENT_LEFT',   __('Stick to the content left', 'ad-inserter'));
define ('AI_TEXT_STICK_TO_THE_CONTENT_RIGHT',  __('Stick to the content right', 'ad-inserter'));
define ('AI_TEXT_STICK_TO_THE_RIGHT',          __('Stick to the right', 'ad-inserter'));

define ('AI_TEXT_STICK_TO_THE_TOP',            __('Stick to the top', 'ad-inserter'));
define ('AI_TEXT_SCROLL_WITH_THE_CONTENT',     __('Scroll with the content', 'ad-inserter'));
define ('AI_TEXT_STICK_TO_THE_BOTTOM',         __('Stick to the bottom', 'ad-inserter'));

define ('AI_TEXT_FADE',                   __('Fade', 'ad-inserter'));
define ('AI_TEXT_SLIDE',                  __('Slide', 'ad-inserter'));
define ('AI_TEXT_SLIDE_FADE',             __('Slide and Fade', 'ad-inserter'));
define ('AI_TEXT_FLIP',                   __('Flip', 'ad-inserter'));
define ('AI_TEXT_ZOOM_IN',                __('Zoom In', 'ad-inserter'));
define ('AI_TEXT_ZOOM_OUT',               __('Zoom Out', 'ad-inserter'));
define ('AI_TEXT_TURN',                   __('Turn', 'ad-inserter'));

define ('AI_TEXT_PAGE_LOADED',            __('Page loaded', 'ad-inserter'));
define ('AI_TEXT_PAGE_SCROLLED_PC',       __('Page scrolled (%)', 'ad-inserter'));
define ('AI_TEXT_PAGE_SCROLLED_PX',       __('Page scrolled (px)', 'ad-inserter'));
define ('AI_TEXT_ELEMENT_SCROLLS_IN',     __('Element scrolls in', 'ad-inserter'));
define ('AI_TEXT_ELEMENT_SCROLLS_OUT',    __('Element scrolls out', 'ad-inserter'));

define('AI_TEXT_DEFAULT_BKG_REPEAT',      _x('Default', 'image repeat', 'ad-inserter'));
define('AI_TEXT_NO',                      __('No', 'ad-inserter'));
define('AI_TEXT_YES',                     __('Yes', 'ad-inserter'));
define('AI_TEXT_HORIZONTALY',             __('Horizontally', 'ad-inserter'));
define('AI_TEXT_VERTICALLY',              __('Vertically', 'ad-inserter'));
define('AI_TEXT_SPACE',                   __('Space', 'ad-inserter'));
define('AI_TEXT_ROUND',                   __('Round', 'ad-inserter'));

define('AI_TEXT_DEFAULT_BKG_SIZE',        _x('Default', 'image size', 'ad-inserter'));
define('AI_TEXT_COVER',                   __('Cover', 'ad-inserter'));
define('AI_TEXT_FIT_BKG_SIZE',            _x('Fit', 'image size', 'ad-inserter'));
define('AI_TEXT_FILL',                    __('Fill', 'ad-inserter'));

define ('AI_TEXT_INSERT_IMMEDIATELY',              __('Insert immediately', 'ad-inserter'));
define ('AI_TEXT_DELAY_INSERTION',                 __('Delay insertion', 'ad-inserter'));
define ('AI_TEXT_INSERT_BETWEEN_DATES',            __('Insert between dates', 'ad-inserter'));
define ('AI_TEXT_INSERT_OUTSIDE_DATES',            __('Insert outside dates', 'ad-inserter'));
define ('AI_TEXT_INSERT_ONLY',                     __('Insert only', 'ad-inserter'));
define ('AI_TEXT_INSERT_PUBLISHED_BETWEEN_DATES',  __('Insert for posts published between dates', 'ad-inserter'));
define ('AI_TEXT_INSERT_PUBLISHED_OUTSIDE_DATES',  __('Insert for posts published outside dates', 'ad-inserter'));

define ('AI_TEXT_FUNCTIONS_STANDARD',     _x('Standard', 'functions', 'ad-inserter'));
define ('AI_TEXT_STANDARD',               _x('Standard', 'detection', 'ad-inserter'));
define ('AI_TEXT_MULTIBYTE',              _x('Multibyte', 'functions', 'ad-inserter'));

define ('AI_TEXT_NONE',                   _x('None', 'action', 'ad-inserter'));
define ('AI_TEXT_BUTTON_NONE',            _x('None', 'button', 'ad-inserter'));
define ('AI_TEXT_POPUP_MESSAGE',          __('Popup Message', 'ad-inserter'));
define ('AI_TEXT_REDIRECTION',            __('Redirection', 'ad-inserter'));

define ('AI_TEXT_DO_NOTHING',             __('Do nothing', 'ad-inserter'));
define ('AI_TEXT_REPLACE',                __('Replace', 'ad-inserter'));
define ('AI_TEXT_SHOW',                   _x('Show', 'Action when ad blocking detected', 'ad-inserter'));
define ('AI_TEXT_HIDE',                   _x('Hide', 'Action when ad blocking detected', 'ad-inserter'));

define ('AI_TEXT_INTERNAL',               _x('Internal', 'tracking', 'ad-inserter'));
define ('AI_TEXT_ADVANCED',               _x('Advanced', 'detection', 'ad-inserter'));
define ('AI_TEXT_ENABLED',                __('Enabled', 'ad-inserter'));

define ('AI_TEXT_AUTO',                   _x('Auto', 'Manual loading', 'ad-inserter'));
define ('AI_TEXT_ALWAYS',                 _x('Always', 'Manual loading', 'ad-inserter'));

define ('AI_TEXT_TOP_RIGHT',              __('Top right', 'ad-inserter'));
define ('AI_TEXT_TOP_LEFT',               __('Top left', 'ad-inserter'));
define ('AI_TEXT_BOTTOM_RIGHT',           __('Bottom right', 'ad-inserter'));
define ('AI_TEXT_BOTTOM_LEFT',            __('Bottom left', 'ad-inserter'));

define ('AI_TEXT_ADSENSE_STANDARD',       _x('Standard', 'AdSense Ad Type', 'ad-inserter'));
define ('AI_TEXT_LINK',                   _x('Link', 'AdSense Ad Type', 'ad-inserter'));
define ('AI_TEXT_IN_ARTICLE',             _x('In-article', 'AdSense Ad Type', 'ad-inserter'));
define ('AI_TEXT_IN_FEED',                _x('In-feed', 'AdSense Ad Type', 'ad-inserter'));
define ('AI_TEXT_MATCHED_CONTENT',        _x('Matched content', 'AdSense Ad Type', 'ad-inserter'));
define ('AI_TEXT_ADSENSE_AUTO',           _x('Auto Ads', 'AdSense Ad Type', 'ad-inserter'));
define ('AI_TEXT_ADSENSE_AMP_ONLY',       _x('AMP Only', 'AdSense Ad Type', 'ad-inserter'));

define ('AI_TEXT_ADSENSE_DISABLED',       _x('Disabled', 'AMP ad', 'ad-inserter'));
define ('AI_TEXT_ABOVE_THE_FOLD',         __('Above the fold', 'ad-inserter'));
define ('AI_TEXT_BELOW_THE_FOLD',         __('Below the fold', 'ad-inserter'));
define ('AI_TEXT_STICKY_AMP',             _x('Sticky', 'AMP ad', 'ad-inserter'));

define ('AI_TEXT_FIXED',                  _x('Fixed', 'size', 'ad-inserter'));
define ('AI_TEXT_RESPONSIVE',             _x('Responsive', 'size', 'ad-inserter'));
define ('AI_TEXT_FIXED_BY_VIEWPORT',      _x('Fixed by viewport', 'size', 'ad-inserter'));

                                                // Translators: %s: Ad Inserter Pro
define ('DEFAULT_REPORT_HEADER_TITLE',          sprintf (__('%s Report', 'ad-inserter'), 'Ad Inserter Pro'));
define ('DEFAULT_REPORT_HEADER_DESCRIPTION',    __('Impressions and clicks', 'ad-inserter'));
define ('DEFAULT_REPORT_FOOTER',                AD_INSERTER_NAME . '        '. __('Advanced WordPress Ad Management Plugin', 'ad-inserter') . '        https://adinserter.pro/');


global $ai_admin_translations, $ai_front_translations, $wp_version;

$ai_admin_translations = array (
  'hide'                        => _x('Hide', 'Button', 'ad-inserter'),
  'show'                        => _x('Show', 'Button', 'ad-inserter'),
  'insertion_expired'           => __('Insertion expired', 'ad-inserter'),
  'duration'                    => __('Duration', 'ad-inserter'),
  'invalid_end_date'            => __('Invalid end date - must be after start date', 'ad-inserter'),
  'invalid_start_date'          => __('Invalid start date - only data for 1 year back is available', 'ad-inserter'),
  'invalid_date_range'          => __('Invalid date range - only data for 1 year can be displayed', 'ad-inserter'),
  'days_0'                      => _n('day', 'days', 0, 'ad-inserter'),
  'days_1'                      => _n('day', 'days', 1, 'ad-inserter'),
  'days_2'                      => _n('day', 'days', 2, 'ad-inserter'),
  'days_3'                      => _n('day', 'days', 3, 'ad-inserter'),
  'days_4'                      => _n('day', 'days', 4, 'ad-inserter'),
  'days_5'                      => _n('day', 'days', 5, 'ad-inserter'),
  'warning'                     => __('Warning', 'ad-inserter'),
  'delete'                      => __('Delete', 'ad-inserter'),
  'delete_all'                  => __('Delete all', 'ad-inserter'),
  'switch'                      => __('Switch', 'ad-inserter'),
  'cancel'                      => __('Cancel', 'ad-inserter'),
  'ok'                          => __('OK', 'ad-inserter'),
  'delete_all_statistics'       => __('Delete all statistics data?', 'ad-inserter'),
  'rotation_active'             => __('Rotation code editor active. Click on the rotation button to generate code.', 'ad-inserter'),

                                    // translators: %s: dates
  'delete_statistics_between'   => sprintf (__('Delete statistics data between %s and %s?', 'ad-inserter'), '{start_date}', '{end_date}'),
  'delete_website'              => __('Delete website?', 'ad-inserter'),
  'cancel_rearrangement'        => __('Cancel block order rearrangement', 'ad-inserter'),
  'rearrange_block_order'       => __('Rearrange block order', 'ad-inserter'),
  'downloading'                 => __('downloading...', 'ad-inserter'),
  'download_error'              => __('download error', 'ad-inserter'),
  'update_error'                => __('update error', 'ad-inserter'),
  'updating'                    => __('Updating...', 'ad-inserter'),
  'loading'                     => __('Loading...', 'ad-inserter'),
  'error'                       => __('ERROR', 'ad-inserter'),
  'error_reloading_settings'    => __('Error reloading settings', 'ad-inserter'),
  'google_adsense_homepage'     => __('Google AdSense Homepage', 'ad-inserter'),
  'search'                      => _x('Search...', 'Search field placeholder', 'ad-inserter'),
  'filter'                      => _x('Filter...', 'Search field placeholder', 'ad-inserter'),
  'filter_title'                => __('Use filter to limit names in the list', 'ad-inserter'),
  'button_filter'               => _x('Filter', 'Button', 'ad-inserter'),
  'position_not_checked'        => __('Position not checked yet', 'ad-inserter'),
  'position_not_available'      => __('Position not available', 'ad-inserter'),
  'position_might_not_available'=> __('Theme check | Selected position for automatic insertion might not be not available on this page type', 'ad-inserter'),
  'position_available'          => __('Position available', 'ad-inserter'),
  'select_header_image'         => __('Select or upload header image', 'ad-inserter'),
  'select_banner_image'         => __('Select or upload banner image', 'ad-inserter'),
  'select_background_image'     => __('Select or upload background image', 'ad-inserter'),
  'use_this_image'              => __('Use this image', 'ad-inserter'),
  'switch_to_physical_ads_txt'  => __('Switching to physical ads.txt file will delete virtual ads.txt file.', 'ad-inserter'),

  'day_mo'                      => _x('MO', 'Monday', 'ad-inserter'),
  'day_tu'                      => _x('TU', 'Tuesday', 'ad-inserter'),
  'day_we'                      => _x('WE', 'Wednesday', 'ad-inserter'),
  'day_th'                      => _x('TH', 'Thursday', 'ad-inserter'),
  'day_fr'                      => _x('FR', 'Friday', 'ad-inserter'),
  'day_sa'                      => _x('SA', 'Saturday', 'ad-inserter'),
  'day_su'                      => _x('SU', 'Sunday', 'ad-inserter'),
);


$ai_front_translations = array (
//  'wp_ai'                       => $wp_version . '+' . AD_INSERTER_VERSION,

  // Debugging
  'insertion_before'            => __('BEFORE', 'ad-inserter'),
  'insertion_after'             => __('AFTER', 'ad-inserter'),
  'insertion_prepend'           => __('PREPEND CONTENT', 'ad-inserter'),
  'insertion_append'            => __('APPEND CONTENT', 'ad-inserter'),
  'insertion_replace_content'   => __('REPLACE CONTENT', 'ad-inserter'),
  'insertion_replace_element'   => __('REPLACE ELEMENT', 'ad-inserter'),
  'visible'                     => _x('VISIBLE', 'Block', 'ad-inserter'),
  'hidden'                      => _x('HIDDEN', 'Block', 'ad-inserter'),
  'fallback'                    => _x('FALLBACK', 'alternative block', 'ad-inserter'),
  'automatically_placed'        => __('Automatically placed by AdSense Auto ads code', 'ad-inserter'),

  // Element selection
  'cancel'                      => __('Cancel', 'ad-inserter'),
  'use'                         => __('Use', 'ad-inserter'),
  'add'                         => __('Add', 'ad-inserter'),
  'parent'                      => _x('Parent', 'Element', 'ad-inserter'),
  'cancel_element_selection'    => __('Cancel element selection', 'ad-inserter'),
  'select_parent_element'       => __('Select parent element', 'ad-inserter'),
  'css_selector'                => __('CSS selector', 'ad-inserter'),
  'use_current_selector'        => __('Use current selector', 'ad-inserter'),
  'element'                     => __('ELEMENT', 'ad-inserter'),
  'path'                        => __('PATH', 'ad-inserter'),
  'selector'                    => __('SELECTOR', 'ad-inserter'),
);
