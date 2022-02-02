<?php
include_once WPDISCUZ_DIR_PATH . "/forms/autoload.php";

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Form;
use wpdFormAttr\Login\SocialLogin;
use wpdFormAttr\Tools\PersonalDataExporter;

class wpDiscuzForm implements wpdFormConst {

    private $options;
    private $pluginVersion;
    public $wpdFormAdminOptions;
    private $form;
    private $formContentTypeRel;
    private $formPostRel;

    public function __construct($options, $pluginVersion) {
        $this->options = $options;
        $this->pluginVersion = $pluginVersion;
        $this->form = new Form($this->options);
        $this->initAdminPhrases();
        $this->formContentTypeRel = $options->formContentTypeRel;
        $this->formPostRel = $options->formPostRel;
        SocialLogin::getInstance($this->options);

        add_action("init", [&$this, "registerPostType"], 1);
        add_action("admin_init", [&$this, "custoFormRoleCaps"], 999);
        add_action("admin_menu", [&$this, "addFormToAdminMenu"], 874);
        add_action("admin_enqueue_scripts", [&$this, "customFormAdminScripts"], 245);
        add_action("manage_wpdiscuz_form_posts_custom_column", [&$this, "displayContentTypesOnList"], 10, 2);
        add_filter("manage_wpdiscuz_form_posts_columns", [&$this, "addContentTypeColumn"]);
        add_action("edit_form_after_title", [&$this, "renderFormGeneralSettings"]);
        add_action("wp_ajax_wpdiscuzCustomFields", [&$this, "wpdiscuzFieldsDialogContent"]);
        add_action("wp_ajax_adminFieldForm", [&$this, "adminFieldForm"]);
        add_action("transition_comment_status", [&$this, "changeCommentStatus"], 10, 3);
        add_action("delete_comment", [&$this, "deleteCommentRating"], 269);
        add_filter("wpdiscuz_before_subscription_added", [&$this->form, "validateSubscribtionCaptcha"]);

        add_filter("wpdiscuz_js_options", [$this, "transferJSData"], 10);
        add_action("save_post", [&$this, "saveFormData"], 10, 3);
        add_action("wp_trash_post", [&$this, "deleteOrTrashForm"]);
        add_action("add_meta_boxes", [$this, "formCustomCssMetabox"]);
        add_action("add_meta_boxes_comment", [&$this, "renderEditCommentForm"], 10);
        add_filter("comment_save_pre", [&$this, "validateMetaCommentSavePre"], 10);
        add_action("edit_comment", [&$this, "updateCommentMeta"], 10);
        add_filter("comment_text", [&$this, "renderCommentMetaHtml"], 10, 2);
        add_filter("wpdiscuz_after_read_more", [&$this, "afterReadMore"], 10, 2);
        add_filter("post_row_actions", [&$this, "addCloneFormAction"], 10, 2);
        add_filter("admin_post_cloneWpdiscuzForm", [&$this, "cloneForm"]);
        add_filter("the_content", [&$this->form, "displayRatingMeta"], 10);
        add_shortcode("wpdrating", [&$this->form, "getRatingMetaHtml"]);
        add_filter("wpdiscuz_comment_form_before", [&$this->form, "displayRatingMetaBeforeCommentForm"], 10);
        add_action("admin_notices", [&$this, "formExists"]);
        add_action("wp_loaded", [&$this, "initPersonalDataExporter"]);
    }

    public function initPersonalDataExporter() {
        PersonalDataExporter::getInstance($this->options);
    }

    public function validateMetaCommentSavePre($commentContent) {
        if (filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING) == "editedcomment") {
            $postID = filter_input(INPUT_POST, "comment_post_ID", FILTER_SANITIZE_NUMBER_INT);
            $this->getForm($postID);
            if ($this->form) {
                $currentUser = WpdiscuzHelper::getCurrentUser();
                $this->form->initFormFields();
                $this->form->validateFields($currentUser);
            }
        }
        return $commentContent;
    }

    public function updateCommentMeta($commentID) {
        if (filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING) == "editedcomment") {
            $postID = filter_input(INPUT_POST, "comment_post_ID", FILTER_SANITIZE_NUMBER_INT);
            $this->getForm($postID);
            if ($this->form) {
                $this->form->saveCommentMeta($commentID);
            }
        }
    }

    public function adminFieldForm() {
        $this->canManageOptions();
        $field = filter_input(INPUT_POST, "fieldType", FILTER_SANITIZE_STRING);
        $isDefault = filter_input(INPUT_POST, "defaultField", FILTER_SANITIZE_NUMBER_INT);
        $row = filter_input(INPUT_POST, "row", FILTER_SANITIZE_STRING);
        $col = filter_input(INPUT_POST, "col", FILTER_SANITIZE_STRING);
        if ($field && $row && $col) {
            if ($isDefault) {
                $field = "wpdFormAttr\Field\\$field";
            }
            $allowedFieldsType = $this->form->getAllowedFieldsType();
            if (!in_array($field, $allowedFieldsType, true)) {
                esc_html_e("Not whitelisted value detected");
            }
            $fieldClass = call_user_func($field . '::getInstance');
            $fieldClass->dashboardFormDialogHtml($row, $col);
        } else {
            esc_html_e("Invalid Data !!!");
        }
        wp_die();
    }

    public function registerPostType() {
        register_post_type(self::WPDISCUZ_FORMS_CONTENT_TYPE, [
            "labels" => [
                "name" => esc_html__("Forms", "wpdiscuz"),
                "singular_name" => esc_html__("Form", "wpdiscuz"),
                "add_new" => esc_html__("Add New", "wpdiscuz"),
                "add_new_item" => esc_html__("Add New Form", "wpdiscuz"),
                "edit_item" => esc_html__("Edit Form", "wpdiscuz"),
                "not_found" => esc_html__("You did not create any forms yet", "wpdiscuz"),
                "not_found_in_trash" => esc_html__("Nothing found in Trash", "wpdiscuz"),
                "search_items" => esc_html__("Search Forms", "wpdiscuz")
            ],
            "show_ui" => true,
            "show_in_menu" => false,
            "public" => false,
            "supports" => ["title"],
            "capability_type" => self::WPDISCUZ_FORMS_CONTENT_TYPE,
            "map_meta_cap" => true,
                ]
        );
    }

    public function custoFormRoleCaps() {
        $role = get_role("administrator");
        $role->add_cap("read");
        $role->add_cap("read_" . self::WPDISCUZ_FORMS_CONTENT_TYPE);
        $role->add_cap("read_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("edit_" . self::WPDISCUZ_FORMS_CONTENT_TYPE);
        $role->add_cap("edit_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("edit_others_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("edit_published_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("publish_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("delete_" . self::WPDISCUZ_FORMS_CONTENT_TYPE);
        $role->add_cap("delete_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("delete_others_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("delete_private_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
        $role->add_cap("delete_published_" . self::WPDISCUZ_FORMS_CONTENT_TYPE . "s");
    }

    public function saveFormData($postId, $post, $update) {
        if ($post->post_type != self::WPDISCUZ_FORMS_CONTENT_TYPE || (isset($_REQUEST["action"]) && $_REQUEST["action"] == "inline-save")) {
            return;
        }
        $this->canManageOptions();
        $this->form->saveFormData($postId);
        $css = filter_input(INPUT_POST, self::WPDISCUZ_META_FORMS_CSS, FILTER_SANITIZE_STRING);
        update_post_meta($postId, self::WPDISCUZ_META_FORMS_CSS, $css);
    }

    public function addFormToAdminMenu() {
        global $submenu;
        if (!empty($submenu["wpdiscuz"])) {
            $submenu["wpdiscuz"][] = ["<div id='wpd-form-menu-item'></div>&raquo; " . esc_html__("Forms", "wpdiscuz"), "manage_options", "edit.php?post_type=" . self::WPDISCUZ_FORMS_CONTENT_TYPE];
        }
    }

    /* Display custom column */

    public function displayContentTypesOnList($column, $post_id) {
        $this->form->theFormListData($column, $post_id);
    }

    /* Add custom column to post list */

    public function addContentTypeColumn($columns) {
        return [
            "cb" => "<input type='checkbox' />",
            "title" => esc_html__("Title", "default"),
            "form_post_types" => esc_html__("Post Types", "wpdiscuz"),
            "form_post_ids" => esc_html__("Post IDs", "wpdiscuz"),
            "form_lang" => esc_html__("Language", "wpdiscuz"),
            "date" => esc_html__("Date", "default"),
        ];
    }

    public function customFormAdminScripts() {
        global $current_screen;
        if ($current_screen->id == self::WPDISCUZ_FORMS_CONTENT_TYPE) {
            wp_register_style("fontawesome-iconpicker-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css"), [], "1.12.1");
            wp_enqueue_style("fontawesome-iconpicker-css");
            wp_register_script("fontawesome-iconpicker-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/fontawesome-iconpicker/js/fontawesome-iconpicker.js"), ["jquery"], "1.12.1", true);
            wp_enqueue_script("fontawesome-iconpicker-js");
            wp_register_style("wpdiscuz-custom-form-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-custom-form.css"), [], $this->pluginVersion);
            wp_enqueue_style("wpdiscuz-custom-form-css");
            wp_register_script("wpdiscuz-custom-form", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-custom-form.js"), ["jquery"], $this->pluginVersion, true);
            wp_enqueue_script("wpdiscuz-custom-form");
            wp_localize_script("wpdiscuz-custom-form", "wpdFormAdminOptions", $this->wpdFormAdminOptions);
            wp_register_script("wpdiscuz-form-menu-item", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-admin-menu-item.js"), ["jquery"], $this->pluginVersion, true);
            wp_enqueue_script("wpdiscuz-form-menu-item");
            wp_enqueue_style("thickbox");
            wp_enqueue_script("thickbox");
            wp_enqueue_script("jquery-ui-sortable");
        }
        if ($current_screen->id == "edit-" . self::WPDISCUZ_FORMS_CONTENT_TYPE) {
            wp_register_script("wpdiscuz-form-menu-item", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-admin-menu-item.js"), ["jquery"], $this->pluginVersion, true);
            wp_enqueue_script("wpdiscuz-form-menu-item");
        }
    }

    public function renderFormGeneralSettings($post) {
        global $current_screen;
        if ($current_screen->id == self::WPDISCUZ_FORMS_CONTENT_TYPE) {
            $this->form->setFormID($post->ID);
            $this->form->renderFormStructure();
        }
    }

    public function wpdiscuzFieldsDialogContent() {
        $this->canManageOptions();
        include_once WPDISCUZ_DIR_PATH . "/forms/wpdFormAttr/html/admin-form-fields-list.php";
        wp_die();
    }

    private function initAdminPhrases() {
        $this->wpdFormAdminOptions = [
            "wpdiscuz_form_structure" => wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE,
            "wpd_form_fields" => esc_html__("Field Types", "wpdiscuz"),
            "two_column" => esc_html__("Two column", "wpdiscuz"),
            "delete" => esc_html__("Delete", "wpdiscuz"),
            "move" => esc_html__("Move", "wpdiscuz"),
            "add_field" => esc_html__("Add Field", "wpdiscuz"),
            "edit_field" => esc_html__("Edit", "wpdiscuz"),
            "can_not_delete_field" => esc_html__("You can not delete default field.", "wpdiscuz"),
            "confirm_delete_message" => esc_html__("You really want to delete this item ?", "wpdiscuz"),
            "loaderImg" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/form-loading.gif"),
        ];
    }

    private function canManageOptions() {
        if (!current_user_can("manage_options")) {
            wp_die(esc_html__("Permission Denied !!!", "wpdiscuz"));
        }
    }

    public function renderFrontForm($commentsCount, $currentUser) {
        global $post;
        $this->getForm($post->ID);
        $this->form->initFormMeta();
        $this->form->renderFrontForm("main", "0_0", $commentsCount, $currentUser);
        ?>
        <div id="wpdiscuz_hidden_secondary_form" style="display: none;">
            <?php $this->form->renderFrontForm(0, "wpdiscuzuniqueid", $commentsCount, $currentUser); ?>
        </div>
        <?php
    }

    public function renderAdminCommentMetaHtml($content, $comment) {
        $this->form->resetData();
        $this->getForm($comment->comment_post_ID);
        if ($this->form && $this->form->customFieldsExists()) {
            $html = "<div class='wpd-comments-table'>";
            $html .= "<h3>" . esc_html__("Custom Fields", "wpdiscuz") . "</h3>";
            $html .= "<div class='wpd-comments-table-meta'>";
            $htmlExists = $this->form->renderFrontCommentMetaHtml($comment->comment_ID, $tempHtml);
            if ($htmlExists) {
                $content .= $html . $tempHtml . "</div></div>";
            }
        }
        return $content;
    }
    
    public function afterReadMore($output, $comment) {
        return $this->renderFrontCommentMetaHtml($output, $comment);
    }

    public function renderCommentMetaHtml($output, $comment) {
        global $pagenow;
        if (is_admin() && strpos("edit-comments.php", $pagenow) !== false) {
            return $this->renderAdminCommentMetaHtml($output, $comment);
        }
        return $output;
    }

    public function renderFrontCommentMetaHtml($output, $comment) {
        $this->getForm($comment->comment_post_ID);
        if ($this->form) {
            $this->form->initFormMeta();
            $this->form->initFormFields();
            $this->form->renderFrontCommentMetaHtml($comment->comment_ID, $output);
        }
        return $output;
    }

    public function renderEditCommentForm($comment) {
        $postID = $comment->comment_post_ID;
        $this->getForm($postID);
        if ($this->form) {
            $this->form->initFormMeta();
            $this->form->initFormFields();
            $this->form->renderEditAdminCommentForm($comment);
        }
    }

    public function getForm($postID) {
        $formID = 0;
        if (!$this->form->getFormID()) {
            $postType = get_post_type($postID);
            if (isset($this->formPostRel[$postID])) {
                $formID = $this->formPostRel[$postID];
            } elseif (is_string($postType) && isset($this->formContentTypeRel[$postType])) {
                $tempContentTypeRel = $this->formContentTypeRel[$postType];
                $defaultFormID = array_shift($tempContentTypeRel);
                $lang = get_locale();
                $formID = isset($this->formContentTypeRel[$postType][$lang]) && $this->formContentTypeRel[$postType][$lang] ? $this->formContentTypeRel[$postType][$lang] : $defaultFormID;
            }
            $this->form->setFormID($formID);
        }
        return apply_filters("wpdiscuz_get_form", $this->form);
    }

    public function formCustomCssMetabox() {
        add_meta_box(self::WPDISCUZ_META_FORMS_CSS, esc_html__("Custom CSS", "wpdiscuz"), [&$this, "formCustomCssMetaboxHtml"], self::WPDISCUZ_FORMS_CONTENT_TYPE, "side");
    }

    public function formCustomCssMetaboxHtml() {
        global $post;
        $cssMeta = get_post_meta($post->ID, self::WPDISCUZ_META_FORMS_CSS, true);
        $css = $cssMeta ? $cssMeta : "";
        echo "<textarea style='width:100%;' name='" . esc_attr(self::WPDISCUZ_META_FORMS_CSS) . "' class='" . esc_attr(self::WPDISCUZ_META_FORMS_CSS) . "'>" . $css . "</textarea>";
    }

    public function transferJSData($data) {
        global $post;
        $this->getForm($post->ID);
        return $this->form->transferJSData($data);
    }

    public function deleteOrTrashForm($formId) {
        if (get_post_type($formId) != wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE) {
            return;
        }
        foreach ($this->formPostRel as $postId => $value) {
            if ($formId == $value) {
                unset($this->formPostRel[$postId]);
            }
        }
        foreach ($this->formContentTypeRel as $type => $value) {
            foreach ($value as $lang => $id) {
                if ($formId == $id) {
                    unset($this->formContentTypeRel[$type][$lang]);
                }
            }
        }
        $this->form->setFormID($formId);
        $this->form->initFormMeta();
        $generalOptions = $this->form->getGeneralOptions();
        $generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] = [];
        $generalOptions["postidsArray"] = [];
        $generalOptions["postid"] = "";
        update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
        update_option("wpdiscuz_form_content_type_rel", $this->formContentTypeRel);
        update_option("wpdiscuz_form_post_rel", $this->formPostRel);
    }

    public function createDefaultForm($version) {
        if ($version == "1.0.0" || version_compare($version, "4.0.0", "<")) {
            $oldForms = get_posts(["posts_per_page" => 1, "post_type" => self::WPDISCUZ_FORMS_CONTENT_TYPE]);
            if ($oldForms) {
                return;
            }
            $wpdGeneralOptions = maybe_unserialize(get_option("wc_options"));
            $phrases = [];
            if (!$this->options->general["isUsePoMo"] && $this->options->dbManager->isPhraseExists("wc_be_the_first_text")) {
                $phrases = $this->options->dbManager->getPhrases();
            }
            $form = [
                "post_title" => esc_html__("Default Form", "wpdiscuz"),
                "post_type" => wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE,
                "post_status" => "publish",
                "comment_status" => "closed",
                "ping_status" => "closed",
            ];
            $lang = get_locale();
            $formId = wp_insert_post($form);
            $defaultFields = [];
            $postTypes = [
                "post" => "post",
                "attachment" => "attachment",
                "page" => "page",
            ];
            $this->options->initPhrasesOnLoad();
            $generalOptions = $this->getDefaultFormGeneralOptions($version, $lang, $wpdGeneralOptions, $phrases, $postTypes);
            $formStructure = $this->getDefaultFormStructure($version, $wpdGeneralOptions, $phrases, $defaultFields);
            update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
            update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE, add_magic_quotes($formStructure));
            update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_FIELDS, add_magic_quotes($defaultFields));
            foreach ($postTypes as $key => $vale) {
                $this->formContentTypeRel[$key][$lang] = $formId;
            }
            update_option("wpdiscuz_form_content_type_rel", $this->formContentTypeRel);
        }
    }

    private function getDefaultFormGeneralOptions($version, $lang, $wpdGeneralOptions, $phrases, &$postTypes) {
        $generalOptions = [
            "lang" => $lang,
            "roles_cannot_comment" => [],
            "guest_can_comment" => get_option("comment_registration") ? 0 : 1,
            "show_subscription_bar" => 1,
            "header_text_single" => esc_html__("Comment", "wpdiscuz"),
            "header_text_plural" => esc_html__("Comments", "wpdiscuz"),
            "wpdiscuz_form_post_types" => $postTypes,
            "postid" => "",
            "postidsArray" => [],
            "theme" => $this->form->getDefaultTheme(),
            "layout" => 1,
            "enable_post_rating" => 1,
            "post_rating_title" => esc_html__("Article Rating", "wpdiscuz"),
            "allow_guests_rate_on_post" => 1,
        ];

        if (version_compare($version, "4.0.0", "<=") && version_compare($version, "1.0.0", "!=") && is_array($wpdGeneralOptions)) {
            $generalOptions["show_subscription_bar"] = 1;
            $optionPostTypes = $wpdGeneralOptions["wc_post_types"];
            $generalOptions["wpdiscuz_form_post_types"] = [];
            foreach ($optionPostTypes as $k => $optionPostType) {
                $generalOptions["wpdiscuz_form_post_types"][$optionPostType] = $optionPostType;
            }
            $postTypes = $generalOptions["wpdiscuz_form_post_types"];
        }
        return $generalOptions;
    }

    private function getDefaultFormStructure($version, $wpdGeneralOptions, $phrases, &$defaultFileds) {
        $formStructure = $this->form->defaultFieldsData();
        if (version_compare($version, "4.0.0", "<=") && version_compare($version, "1.0.0", "!=")) {
            $formStructure["left"][wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD]["required"] = 1;
            $formStructure["left"][wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD]["name"] = isset($phrases["wc_name_text"]) ? $phrases["wc_name_text"] : esc_html__("Name", "wpdiscuz");
            $formStructure["left"][wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD]["required"] = 1;
            $formStructure["left"][wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD]["name"] = isset($phrases["wc_email_text"]) ? $phrases["wc_email_text"] : esc_html__("Email", "wpdiscuz");
            $formStructure["left"][wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD]["enable"] = $wpdGeneralOptions["wc_weburl_show_hide"];
            $formStructure["left"][wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD]["name"] = isset($phrases["wc_website_text"]) ? $phrases["wc_website_text"] : esc_html__("WebSite URL", "wpdiscuz");
            $formStructure["right"][wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD]["show_for_guests"] = $wpdGeneralOptions["wc_captcha_show_hide"] ? 0 : 1;
            $formStructure["right"][wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD]["show_for_users"] = $wpdGeneralOptions["wc_captcha_show_hide_for_members"];
            $formStructure["right"][wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD]["name"] = isset($phrases["wc_submit_text"]) ? $phrases["wc_submit_text"] : esc_html__("Post Comment", "wpdiscuz");
        }
        $defaultFileds = array_merge($formStructure["left"], $formStructure["right"]);
        return ["wpd_form_row_wrap_0" => $formStructure];
    }

    public function addCloneFormAction($actions, $post) {
        if ($post->post_type == self::WPDISCUZ_FORMS_CONTENT_TYPE && $post->post_status == "publish") {
            $url = wp_nonce_url(admin_url("admin-post.php") . "?form_id=" . $post->ID . "&action=cloneWpdiscuzForm", "clone-form_" . $post->ID, "clone_form_nonce");
            $actions["inline hide-if-no-js"] = "<a href='" . esc_url_raw($url) . "'>" . esc_html__("Clone Form") . "</a>";
        }
        return $actions;
    }

    public function cloneForm() {
        $formID = filter_input(INPUT_GET, "form_id", FILTER_SANITIZE_NUMBER_INT);
        $nonce = filter_input(INPUT_GET, "clone_form_nonce", FILTER_SANITIZE_STRING);
        if ($formID && $nonce && wp_verify_nonce($nonce, "clone-form_" . $formID)) {
            $form = get_post($formID);
            if ($form && $form->post_type == self::WPDISCUZ_FORMS_CONTENT_TYPE) {
                $cform = [
                    "post_title" => $form->post_title . " ( " . esc_html__("Clone", "wpdiscuz") . " )",
                    "post_type" => wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE,
                    "post_status" => "publish",
                    "comment_status" => "closed",
                    "ping_status" => "closed"
                ];
                $cfGeneralOptions = get_post_meta($formID, self::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
                $cfGeneralOptions["wpdiscuz_form_post_types"] = [];
                $cfGeneralOptions["postid"] = "";
                $cfGeneralOptions["postidsArray"] = [];
                $cfFormFields = get_post_meta($formID, self::WPDISCUZ_META_FORMS_FIELDS, true);
                $cfFormStructure = get_post_meta($formID, self::WPDISCUZ_META_FORMS_STRUCTURE, true);
                $cfFormID = wp_insert_post($cform);
                update_post_meta($cfFormID, self::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $cfGeneralOptions);
                update_post_meta($cfFormID, self::WPDISCUZ_META_FORMS_FIELDS, add_magic_quotes($cfFormFields));
                update_post_meta($cfFormID, self::WPDISCUZ_META_FORMS_STRUCTURE, add_magic_quotes($cfFormStructure));
            }
        } else {
            wp_die("Permission denied !");
        }
        wp_redirect(esc_url_raw(admin_url("edit.php?post_type=" . self::WPDISCUZ_FORMS_CONTENT_TYPE)));
        exit();
    }

    public function formExists() {
        if (current_user_can("manage_options")) {
            add_filter("parse_query", [$this, "preventElementor"], 999);
            $forms = get_posts(["posts_per_page" => 1,
                "post_type" => self::WPDISCUZ_FORMS_CONTENT_TYPE,
                "post_status" => "publish"]);
            if (!$forms) {
                ?>
                <div class="error" style="padding-top: 5px;padding-bottom: 5px;">
                    <p>
                        <?php esc_html_e("Comment Form is not detected, please navigate to form manager page to create it. ", "wpdiscuz"); ?> 
                        <a href="<?php echo esc_url_raw(admin_url("post-new.php?post_type=" . self::WPDISCUZ_FORMS_CONTENT_TYPE)); ?>" class="button button-primary"><?php esc_attr_e("Add Comment Form", "wpdiscuz"); ?></a>
                    </p>
                </div>
                <?php
            }
        }
    }

    public function preventElementor($q) {
        if (!empty($q->query_vars["post_type"]) && $q->query_vars["post_type"] === self::WPDISCUZ_FORMS_CONTENT_TYPE) {
            if (isset($q->query_vars["elementor_library_category"])) {
                unset($q->query_vars["elementor_library_category"]);
            }
            $q->query_vars["meta_key"] = "";
            $q->query_vars["meta_value"] = "";
        }
        return $q;
    }

    public function deleteCommentRating($commentId) {
        $rating = get_comment_meta($commentId, "rating", true);
        $comment = get_comment($commentId);
        if ($rating && $comment->comment_approved === "1") {
            $this->updatePostRating($comment, -1);
        }
    }

    public function changeCommentStatus($new_status, $old_status, $comment) {
        $rating = get_comment_meta($comment->comment_ID, "rating", true);
        if ($old_status == "approved" && $rating) {
            $this->updatePostRating($comment, -1);
        } else if ($new_status == "approved" && $rating) {
            $this->updatePostRating($comment, 1);
        }
    }

    private function updatePostRating($comment, $difference) {
        $postRatings = get_post_meta($comment->comment_post_ID, self::WPDISCUZ_RATING_COUNT, true);
        $form = $this->getForm($comment->comment_post_ID);
        $form->initFormFields();
        $formFields = $form->getFormFields();
        foreach ($formFields as $key => $value) {
            if ($value["type"] == "wpdFormAttr\Field\RatingField") {
                $postRatings = $this->chagePostSingleRating($key, $comment->comment_ID, $difference, $postRatings);
            }
        }
        update_post_meta($comment->comment_post_ID, self::WPDISCUZ_RATING_COUNT, $postRatings);
    }

    private function chagePostSingleRating($metaKey, $commentID, $difference, $postRatings) {
        $commentFieldRating = get_comment_meta($commentID, $metaKey, true);
        if (!$postRatings) {
            $postRatings = [$metaKey => []];
        }
        if ($commentFieldRating) {
            if (isset($postRatings[$metaKey][$commentFieldRating])) {
                $postRatings[$metaKey][$commentFieldRating] = $postRatings[$metaKey][$commentFieldRating] + $difference;
            } else {
                $postRatings[$metaKey][$commentFieldRating] = $difference;
            }
        }
        return $postRatings;
    }

}
