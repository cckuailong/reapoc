<?php
namespace wpdFormAttr\FormConst;

interface wpdFormConst {
    
    /* === OPTIONS NAMES === */
    const WPDISCUZ_FORMS_CONTENT_TYPE_REL             = "wpdiscuz_form_content_type_rel";
    const WPDISCUZ_FORMS_POST_REL                     = "wpdiscuz_form_post_rel";
    /* === CONTENT TYPES ===*/
    const WPDISCUZ_FORMS_CONTENT_TYPE                 = "wpdiscuz_form";
    /* === FORME META === */
    const WPDISCUZ_META_FORMS_STRUCTURE               = "wpdiscuz_form_structure";
    const WPDISCUZ_META_FORMS_POSTE_TYPES             = "wpdiscuz_form_post_types";
    const WPDISCUZ_META_FORMS_GENERAL_OPTIONS         = "wpdiscuz_form_general_options";
    const WPDISCUZ_META_FORMS_FIELDS                  = "wpdiscuz_form_fields";
    const WPDISCUZ_META_FORMS_CSS                     = "wpd_form_custom_css";
    const WPDISCUZ_RATING_COUNT                       = "wpdiscuz_rating_count";
    const WPDISCUZ_RATINGS_UPDATE_DATE                = "_wpd_ratings_update_date";
    /* === DEFAULT FIELDS NAMES ===*/
    const WPDISCUZ_FORMS_NAME_FIELD                   = "wc_name";
    const WPDISCUZ_FORMS_EMAIL_FIELD                  = "wc_email";
    const WPDISCUZ_FORMS_WEBSITE_FIELD                = "wc_website";
    const WPDISCUZ_FORMS_CAPTCHA_FIELD                = "wc_captcha";
    const WPDISCUZ_FORMS_SUBMIT_FIELD                 = "submit";
    /* === SOCIAL LOGIN */
    const WPDISCUZ_SOCIAL_PROVIDER_KEY                = "wpdiscuz_social_provider";
    const WPDISCUZ_SOCIAL_AVATAR_KEY                  = "wpdiscuz_social_avatar";
    const WPDISCUZ_SOCIAL_USER_ID_KEY                 = "wpdiscuz_social_userid";
    const WPDISCUZ_OAUTH_STATE_PROVIDER               = "provider";
    const WPDISCUZ_OAUTH_STATE_TOKEN                  = "_wpdiscuz_social_";
    const WPDISCUZ_OAUTH_CURRENT_POSTID               = "postid";
    /* === POST RATING */
    const POSTMETA_POST_RATING                        = "wpdiscuz_post_rating";
    const POSTMETA_POST_RATING_COUNT                  = "wpdiscuz_post_rating_count";
    /* === THEMES DIR */
    const THEMES_DIR                                  = "/wpdiscuz/themes/";
}
