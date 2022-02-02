<?php

if (!defined("ABSPATH")) {
    exit();
}

interface WpDiscuzConstants {
    /* === OPTIONS SLUGS === */
    const OPTION_SLUG_OPTIONS                         = "wc_options";
    const OPTION_SLUG_VERSION                         = "wc_plugin_version";
    const OPTION_SLUG_DEACTIVATION                    = "wc_deactivation_modal_never_show";
    const OPTION_SLUG_SHOW_DEMO                       = "wc_show_addons_demo";
    const OPTION_SLUG_HASH_KEY                        = "wc_hash_key";
    const OPTION_SLUG_SHOW_VOTE_REG_MESSAGE           = "wpdiscuz_show_vote_regenerate_message";
    const OPTION_SLUG_SHOW_CLOSED_REG_MESSAGE         = "wpdiscuz_show_closed_regenerate_message";
    const OPTION_SLUG_SHOW_VOTE_DATA_REG_MESSAGE      = "wpdiscuz_show_vote_data_regenerate_message";
    const OPTION_SLUG_SHOW_SYNC_COMMENTERS_MESSAGE    = "wpdiscuz_show_sync_commenters_message";
    const OPTION_SLUG_WIZARD_COMPLETED                = "wpdiscuz_wizard_completed";
    const OPTION_SLUG_WIZARD_AFTER_UPDATE             = "wpdiscuz_wizard_after_update";
    const OPTION_SLUG_WIZARD_SHOW_ADDONS_MSG          = "wpdiscuz_wizard_show_addons_msg";
    /* === OPTIONS SLUGS === */
    const PAGE_WPDISCUZ                               = "wpdiscuz";
    const PAGE_SETTINGS                               = "wpdiscuz_options_page";
    const PAGE_PHRASES                                = "wpdiscuz_phrases_page";
    const PAGE_TOOLS                                  = "wpdiscuz_tools_page";
    const PAGE_ADDONS                                 = "wpdiscuz_addons_page"; 
    const PAGE_COMMENTS                               = "edit-comments.php"; 
    /* === TABS SLUGS === */
    const TAB_FORM                                    = "form";
    const TAB_RECAPTCHA                               = "recaptcha";
    const TAB_LOGIN                                   = "login";
    const TAB_SOCIAL                                  = "social";
    const TAB_RATING                                  = "rating";
    const TAB_THREAD_DISPLAY                          = "thread_display";
    const TAB_THREAD_LAYOUTS                          = "thread_layouts";
    const TAB_THREAD_STYLES                           = "thread_styles";
    const TAB_SUBSCRIPTION                            = "subscription";
    const TAB_LABELS                                  = "labels";
    const TAB_MODERATION                              = "moderation";
    const TAB_CONTENT                                 = "content";
    const TAB_LIVE                                    = "live";
    const TAB_INLINE                                  = "inline";
    const TAB_GENERAL                                 = "general";
    /* === META KEYS === */
    const META_KEY_VOTES                              = "wpdiscuz_votes";
    const META_KEY_VOTES_SEPARATE                     = "wpdiscuz_votes_seperate";
    const META_KEY_CLOSED                             = "wpdiscuz_closed";
    const META_KEY_FEEDBACK_FORM_ID                   = "wpdiscuz_feedback_form_id";
    const META_KEY_LAST_EDITED_AT                     = "wpdiscuz_last_edited_at";
    const META_KEY_LAST_EDITED_BY                     = "wpdiscuz_last_edited_by";
    /* === SUBSCRIPTION TYPES === */
    const SUBSCRIPTION_POST                           = "post";
    const SUBSCRIPTION_ALL_COMMENT                    = "all_comment";
    const SUBSCRIPTION_COMMENT                        = "comment";
    /* === TRANSIENT KEYS === */
    const TRS_POSTS_AUTHORS                           = "wpdiscuz_posts_authors";
    /* === COOKIES === */
    const COOKIE_HIDE_BUBBLE_HINT                     = "wpdiscuz_hide_bubble_hint";
    /* === CACHE === */
    const GRAVATARS_CACHE_DIR                         = "/wpdiscuz/cache/gravatars/";
    const GRAVATARS_CACHE_ADD_RECURRENCE              = 3;
    const GRAVATARS_CACHE_ADD_KEY_RECURRENCE          = "wpdiscuz_cache_add_every_3h";
    const GRAVATARS_CACHE_ADD_ACTION                  = "wpdiscuz_gravatars_cache_add";    
    const GRAVATARS_CACHE_DELETE_RECURRENCE           = 48;
    const GRAVATARS_CACHE_DELETE_KEY_RECURRENCE       = "wpdiscuz_cache_delete_every_48h";
    const GRAVATARS_CACHE_DELETE_ACTION               = "wpdiscuz_gravatars_cache_delete";
    /* === STICKY COMMENTS === */
    const WPDISCUZ_STICKY_COMMENT                     = "wpdiscuz_sticky";
    /* === PRIVATE COMMENTS === */
    const WPDISCUZ_PRIVATE_COMMENT                    = "private";
    /* === TOOLS === */
    const OPTIONS_DIR                                 = "/wpdiscuz/options/";
    const OPTIONS_FILENAME                            = "wpdiscuz-options";
    const PHRASES_FILENAME                            = "wpdiscuz-phrases";
    /* === STATISTICS === */
    const POSTMETA_STATISTICS                         = "_wpdiscuz_statistics";
    const POSTMETA_REACTED                            = "reacted";
    const POSTMETA_RATING_COUNT                       = "wpdiscuz_rating_count";
    /* === USER CONTENT === */
    const TRS_USER_HASH                               = "wpdiscuz_user_hash_";
    /* === SOCIAL LOGIN */
    const WPDISCUZ_SOCIAL_PROVIDER_KEY                = "wpdiscuz_social_provider";
    /* === USER META */
    const USERMETA_LAST_VISIT                         = "wpdiscuz_last_visit";
    /* === WPDISCUZ FEEDBACK SHORTCODE */
    const WPDISCUZ_FEEDBACK_SHORTCODE                 = "wpdiscuz-feedback";
    /* === Media Uploading === */       
    const METAKEY_ATTCHMENT_COMMENT_ID                = "_wmu_comment_id";
    const METAKEY_ATTCHMENT_OWNER_IP                  = "_wmu_owner_ip";
    const METAKEY_ATTCHMENT_IMPORTED_FROM             = "_wmu_imported_from";
    const METAKEY_ATTACHMENTS                         = "wmu_attachments";
    const KEY_IMAGES                                  = "images";
    const INPUT_NAME                                  = "wmu_files";
    /* === POST RATING */
    const POSTMETA_POST_RATING                        = "wpdiscuz_post_rating";
    const POSTMETA_POST_RATING_COUNT                  = "wpdiscuz_post_rating_count";
}
