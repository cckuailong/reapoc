<?php

namespace wpdFormAttr;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\DefaultField\Captcha;

class Form {

    public $wpdOptions;
    private $generalOptions;
    private $formeStructure;
    private $formPostTypes;
    private $formFields;
    private $formCustomFields;
    private $defaultsFieldsNames;
    private $formID;
    private $row;
    private $captchaFied;
    private $fieldsBeforeSave = [];
    private $ratings;
    private $ratingsExists = false;
    private $ratingsFieldsKey = [];
    public $isUserCanComment = true;
    public $hasIcon = false;

    public function __construct($options, $formID = 0) {
        $this->defaultsFieldsNames = [
            wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD, wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD,
            wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD, wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD,
            wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD];
        $this->wpdOptions = $options;
        $this->setFormID($formID);
        $this->row = new Row();
        $this->captchaFied = Captcha::getInstance();
    }

    public function initFormMeta() {
        if (!$this->generalOptions) {
            $this->generalOptions = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
        }
        if (!$this->formeStructure) {
            $this->formeStructure = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE, true);
        }
        if (!$this->formPostTypes) {
            $this->formPostTypes = isset($this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES]) ? $this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] : [];
        }
    }

    public function initFormFields() {
        if (!$this->formFields) {
            $this->formCustomFields = [];
            $this->formFields = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_FIELDS, true);
            if (is_array($this->formFields)) {
                foreach ($this->formFields as $key => $field) {
                    if (is_callable($field["type"] . "::getInstance") && !in_array($key, $this->defaultsFieldsNames)) {
                        if (!empty($field["icon"])) {
                            $this->hasIcon = true;
                        }
                        $this->formCustomFields[$key] = $field;
                        if ($field["type"] === "wpdFormAttr\Field\RatingField") {
                            $this->ratingsFieldsKey[] = $key;
                        }
                    }
                }
            }
            if (count($this->ratingsFieldsKey)) {
                $this->ratingsExists = true;
            }
        }
    }

    public function getFormCustomFields() {
        return $this->formCustomFields;
    }

    public function setFormID($formID) {
        if ($formID == 0) {
            $this->formID = $formID;
            return;
        }
        $form = get_post($formID);
        if ($form && $form->post_status == "publish" && $form->post_type == wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE) {
            $this->formID = $formID;
            do_action("wpdiscuz_form_init", $this);
        } else {
            $postRel = $this->wpdOptions->formPostRel;
            $contentRel = $this->wpdOptions->formContentTypeRel;
            foreach ($postRel as $pid => $fid) {
                if ($formID == $fid) {
                    unset($postRel[$pid]);
                }
            }
            foreach ($contentRel as $postType => $postTypeData) {
                foreach ($postTypeData as $lang => $fid) {
                    if ($formID == $fid) {
                        unset($contentRel[$postType][$lang]);
                    }
                }
            }
            update_option(wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE_REL, $contentRel);
            update_option(wpdFormConst::WPDISCUZ_FORMS_POST_REL, $postRel);
            $this->formID = 0;
        }
    }

    public function getFormID() {
        return $this->formID;
    }

    public function getGeneralOptions() {
        return $this->generalOptions;
    }

    public function getHeaderTextSingle() {
        $this->initFormMeta();
        if (!isset($this->generalOptions["header_text_single"])) {
            $this->generalOptions["header_text_single"] = esc_html__("Comment", "wpdiscuz");
        }
        return $this->generalOptions["header_text_single"];
    }

    public function getHeaderTextPlural() {
        $this->initFormMeta();
        if (empty($this->generalOptions["header_text_plural"])) {
            $this->generalOptions["header_text_plural"] = esc_html__("Comments", "wpdiscuz");
        }
        return $this->generalOptions["header_text_plural"];
    }

    public function getTheme() {
        $this->initFormMeta();
        if (empty($this->generalOptions["theme"])) {
            $this->generalOptions["theme"] = $this->getDefaultTheme();
        } else {
            if (!is_dir($this->generalOptions["theme"])) {
                $themeName = wp_basename($this->generalOptions["theme"]);
                if (strpos($this->generalOptions["theme"], "plugins") === false) {
                    $uplDir = wp_upload_dir();
                    $themesDir = str_replace("\\", "/", $uplDir["basedir"]) . wpdFormConst::THEMES_DIR;
                    if (is_dir($themesDir . $themeName)) {
                        $this->generalOptions["theme"] = $themesDir . $themeName;
                    } else {
                        $this->generalOptions["theme"] = $this->getDefaultTheme();
                    }
                } else {
                    $themeDir = str_replace("\\", "/", WPDISCUZ_DIR_PATH) . "/themes/" . $themeName;
                    if (is_dir($themeDir)) {
                        $this->generalOptions["theme"] = $themeDir;
                    } else {
                        $this->generalOptions["theme"] = $this->getDefaultTheme();
                    }
                }
            }
        }
        return $this->generalOptions["theme"];
    }

    public function getLayout() {
        $this->initFormMeta();
        if (empty($this->generalOptions["layout"])) {
            $this->generalOptions["layout"] = 1;
        }
        return $this->generalOptions["layout"];
    }

    public function getEnableRateOnPost() {
        $this->initFormMeta();
        if (!isset($this->generalOptions["enable_post_rating"])) {
            $this->generalOptions["enable_post_rating"] = 1;
        }
        return $this->generalOptions["enable_post_rating"];
    }

    public function getPostRatingTitle() {
        $this->initFormMeta();
        if (!isset($this->generalOptions["post_rating_title"])) {
            $this->generalOptions["post_rating_title"] = esc_html__("Article Rating", "wpdiscuz");
        }
        return $this->generalOptions["post_rating_title"];
    }

    public function getRatingsExists() {
        $this->initFormMeta();
        $this->initFormFields();
        return $this->ratingsExists;
    }

    public function getUserCanRateOnPost() {
        $this->initFormMeta();
        if (!isset($this->generalOptions["allow_guests_rate_on_post"])) {
            $this->generalOptions["allow_guests_rate_on_post"] = 1;
        }
        return $this->generalOptions["allow_guests_rate_on_post"];
    }

    public function getCaptchaFied() {
        return $this->captchaFied;
    }

    public function isShowSubscriptionBar() {
        return $this->generalOptions["show_subscription_bar"];
    }

    public function isShowSubscriptionBarAgreement() {
        $this->initFormMeta();
        return isset($this->generalOptions["show_subscription_agreement"]) ? $this->generalOptions["show_subscription_agreement"] : 0;
    }

    public function subscriptionBarAgreementLabel() {
        return isset($this->generalOptions["subscription_agreement_label"]) ? $this->generalOptions["subscription_agreement_label"] : esc_html__("I allow to use my email address and send notification about new comments and replies (you can unsubscribe at any time).", "wpdiscuz");
    }

    public function getCustomCSS() {
        return get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_CSS, true);
    }

    public function getFormPostTypes() {
        return $this->formPostTypes;
    }

    public function getFormFields() {
        return $this->formFields;
    }

    public function theFormListData($column, $formID) {
        $this->setFormID($formID);
        $this->generalOptions = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
        if ($column === "form_post_types") {
            $postTypes = isset($this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES]) ? $this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] : "";
            echo $postTypes ? htmlentities(implode(", ", $this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES])) : "";
        } else if ($column === "form_post_ids") {
            echo isset($this->generalOptions["postid"]) ? htmlentities($this->generalOptions["postid"]) : "";
        } else if ($column === "form_lang") {
            echo isset($this->generalOptions["lang"]) ? htmlentities($this->generalOptions["lang"]) : "";
        }
    }

    public function saveFormData($formID) {
        $this->setFormID($formID);
        $this->initFormMeta();
        if (isset($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS])) {
            $generalOptions = $this->validateGeneralOptions($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS]);
            $this->saveFormContentTypeRel($generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES], $generalOptions["lang"]);
            $this->saveFormPostRel($generalOptions["postidsArray"]);
            update_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
        }
        if (isset($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE])) {
            $formeStructure = $this->validateFormStructure($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE]);
            update_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE, $formeStructure);
            update_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_FIELDS, $this->formFields);
        }
    }

    public function saveCommentMeta($commentID) {
        $comment = get_comment($commentID);
        $commentApproved = $comment->comment_approved;
        do_action("wpdiscuz_before_save_commentmeta", $comment, $this->fieldsBeforeSave);
        foreach ($this->fieldsBeforeSave as $mettaKey => $data) {
            if ($this->ratingsExists && $this->formCustomFields[$mettaKey]["type"] == "wpdFormAttr\Field\RatingField") {
                $oldCommentRating = get_comment_meta($commentID, $mettaKey, true);
                if ($oldCommentRating && $commentApproved === "1") {
                    $postID = $comment->comment_post_ID;
                    $postRatingMeta = get_post_meta($postID, wpdFormConst::WPDISCUZ_RATING_COUNT, true);
                    $oldCommentRatingCount = $postRatingMeta[$mettaKey][$oldCommentRating] - 1;
                    if ($oldCommentRatingCount > 0) {
                        $postRatingMeta[$mettaKey][$oldCommentRating] = $oldCommentRatingCount;
                    } else {
                        unset($postRatingMeta[$mettaKey][$oldCommentRating]);
                    }
                    update_post_meta($postID, wpdFormConst::WPDISCUZ_RATING_COUNT, $postRatingMeta);
                }
                $this->ratings[] = ["metakey" => $mettaKey, "value" => $data];
            }
            update_comment_meta($commentID, $mettaKey, $data);
        }
        if ($this->ratingsExists && $this->ratings) {
            $ratingSum = 0;
            foreach ($this->ratings as $k => $rating) {
                $ratingSum += $rating["value"];
            }
            $gRating = round($ratingSum / count($this->ratings));
            update_comment_meta($commentID, "rating", $gRating);
            if ($commentApproved === "1") {
                $this->savePostRatingMeta($comment, $gRating);
            }
        }
    }

    private function savePostRatingMeta($comment, $rating) {
        $postID = $comment->comment_post_ID;
        if (class_exists("WooCommerce") && get_post_type($postID) == "product") {
            $ratingCount = get_post_meta($postID, "_wc_rating_count", true);
            $ratingCount = is_string($ratingCount) ? [] : $ratingCount;
            $oldRatingMeta = get_comment_meta($comment->comment_ID, "rating", true);
            $oldRating = $oldRatingMeta ? $oldRatingMeta : 0;
            if (isset($ratingCount[$oldRating])) {
                $oldRatingCount = $ratingCount[$oldRating] - 1;
                if ($oldRatingCount > 0) {
                    $ratingCount[$oldRating] = $oldRatingCount;
                } else {
                    unset($ratingCount[$oldRating]);
                }
            }
            if (isset($ratingCount[$rating])) {
                $ratingCount[$rating] = $ratingCount[$rating] + 1;
            } else if ($rating) {
                $ratingCount[$rating] = 1;
            }
            $allRatingSum = 0;
            $allCount = 0;
            foreach ($ratingCount as $star => $count) {
                $allRatingSum += $star * $count;
                $allCount += $count;
            }
            if ($allCount) {
                $averageRating = round($allRatingSum / $allCount, 2);
                update_post_meta($postID, "_wc_average_rating", $averageRating);
                update_post_meta($postID, "_wc_rating_count", $ratingCount);
            }
        } else {
            $wpdiscuzRatingCountMeta = get_post_meta($postID, wpdFormConst::WPDISCUZ_RATING_COUNT, true);
            $wpdiscuzRatingCount = $wpdiscuzRatingCountMeta && is_array($wpdiscuzRatingCountMeta) ? $wpdiscuzRatingCountMeta : [];
            $wpdiscuzRatingCount = $this->cleanUnusedData($wpdiscuzRatingCount, $this->ratings);
            foreach ($this->ratings as $key => $value) {
                if (isset($wpdiscuzRatingCount[$value["metakey"]][$value["value"]])) {
                    $wpdiscuzRatingCount[$value["metakey"]][$value["value"]] = $wpdiscuzRatingCount[$value["metakey"]][$value["value"]] + 1;
                } else if ($value["value"]) {
                    $wpdiscuzRatingCount[$value["metakey"]][$value["value"]] = 1;
                }
            }
            update_post_meta($postID, wpdFormConst::WPDISCUZ_RATING_COUNT, $wpdiscuzRatingCount);
        }
    }

    private function cleanUnusedData($ratingMeta, $ratings) {
        $ratingMetaKeys = array_keys($ratingMeta);
        foreach ($ratingMetaKeys as $key => $ratingMetaKey) {
            $exists = false;
            foreach ($ratings as $k => $rating) {
                if ($rating["metakey"] == $ratingMetaKey) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                unset($ratingMeta[$ratingMetaKey]);
            }
        }
        return $ratingMeta;
    }

    public function displayRatingMeta($content) {
        global $post;
        if (!(class_exists("WooCommerce") && get_post_type($post) == "product")) {
            $this->initFormFields();
            if (in_array("before", $this->wpdOptions->rating["displayRatingOnPost"])) {
                if ($this->ratingsExists) {
                    $content = $this->getRatingMetaHtml() . $content;
                } else if ($this->getEnableRateOnPost()) {
                    $content = $this->getPostRatingHtml() . $content;
                }
            }
            if (in_array("after", $this->wpdOptions->rating["displayRatingOnPost"])) {
                if ($this->ratingsExists) {
                    $content .= $this->getRatingMetaHtml();
                } else if ($this->getEnableRateOnPost()) {
                    $content .= $this->getPostRatingHtml();
                }
            }
        }
        return $content;
    }

    public function displayRatingMetaBeforeCommentForm() {
        global $post;
        $content = "";
        if (!(class_exists("WooCommerce") && get_post_type($post) == "product")) {
            if (in_array("before_comment_form", $this->wpdOptions->rating["displayRatingOnPost"])) {
                if ($this->ratingsExists) {
                    $content = $this->getRatingMetaHtml();
                } else if ($this->getEnableRateOnPost()) {
                    $content = $this->getPostRatingHtml();
                }
            }
        }
        echo $content;
    }

    public function getPostRatingHtml($can_rate = true) {
        $html = "";
        $wpdiscuz = wpDiscuz();
        if ($wpdiscuz->isWpdiscuzLoaded && $this->getEnableRateOnPost() && (($this->wpdOptions->rating["ratingCssOnNoneSingular"] && !is_singular()) || is_singular())) {
            global $post;
            $currentUserId = get_current_user_id();
            $class = "";
            if ($can_rate && is_singular()) {
                if (!empty($currentUserId)) {
                    $class = wpDiscuz()->dbManager->isUserRated($currentUserId, "", $post->ID) ? "" : " class='wpd-not-rated'";
                } else if ($this->getUserCanRateOnPost()) {
                    $class = wpDiscuz()->dbManager->isUserRated(0, md5(wpDiscuz()->helper->getRealIPAddr()), $post->ID) ? "" : " class='wpd-not-rated'";
                }
            }
            $rating = (float) get_post_meta($post->ID, wpdFormConst::POSTMETA_POST_RATING, true);
            $count = (int) get_post_meta($post->ID, wpdFormConst::POSTMETA_POST_RATING_COUNT, true);
            $prefix = (int) $rating;
            $suffix = $rating - $prefix;
            $fullStarSVG = apply_filters("wpdiscuz_full_star_svg", "<svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 24 24'><path d='M0 0h24v24H0z' fill='none'/><path class='wpd-star' d='M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z'/><path d='M0 0h24v24H0z' fill='none'/></svg>", "post", "fas fa-star");
            $halfStarSVG = apply_filters("wpdiscuz_half_star_svg", "<svg xmlns='https://www.w3.org/2000/svg' xmlns:xlink='https://www.w3.org/1999/xlink' viewBox='0 0 24 24'><defs><path id='a' d='M0 0h24v24H0V0z'/></defs><clipPath id='b'><use xlink:href='#a' overflow='visible'/></clipPath><path class='wpd-star wpd-active' clip-path='url(#b)' d='M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4V6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z'/></svg>", "post", "fas fa-star");
            $html .= "<div id='wpd-post-rating'{$class}>
            <div class='wpd-rating-wrap'>
            <div class='wpd-rating-left'></div>
            <div class='wpd-rating-data'>
                <div class='wpd-rating-value'>
                    <span class='wpdrv'>" . esc_html($rating) . "</span>
                    <span class='wpdrc'>" . esc_html($count) . "</span>
                    <span class='wpdrt'>" . ((int) $count > 1 ? esc_html($this->wpdOptions->phrases["wc_votes_phrase"]) : esc_html($this->wpdOptions->phrases["wc_vote_phrase"])) . "</span>";
            $html .= "</div>
                <div class='wpd-rating-title'>" . esc_html($this->getPostRatingTitle()) . "</div>
                <div class='wpd-rating-stars'>";
            if ($prefix) {
                for ($i = 1; $i < 6; $i++) {
                    if ($i <= $prefix) {
                        $html .= str_replace("wpd-star", "wpd-star wpd-active", $fullStarSVG);
                    } else if ($suffix && $i - $prefix === 1) {
                        $html .= $halfStarSVG;
                    } else {
                        $html .= $fullStarSVG;
                    }
                }
            } else if ($suffix) {
                $html .= $halfStarSVG . str_repeat($fullStarSVG, 4);
            } else {
                $html .= str_repeat($fullStarSVG, 5);
            }
            $html .= "</div>";
            if ($class) {
                $html .= "<div class='wpd-rate-starts'>" . str_repeat($fullStarSVG, 5) . "</div>";
            }
            $html .= "</div>
            <div class='wpd-rating-right'></div></div></div>";
            if ($this->wpdOptions->rating["enablePostRatingSchema"] && $count) {
                $html .= apply_filters("wpdiscuz_rating_schema", "<div style='display: none;' itemscope itemtype='https://schema.org/Product'><meta itemprop='name' content='" . esc_html($this->getPostRatingTitle()) . "'><div style='display: none;' itemprop='aggregateRating' itemscope itemtype='https://schema.org/AggregateRating'><meta itemprop='bestRating' content='5'><meta itemprop='worstRating' content='1'><meta itemprop='ratingValue' content='" . esc_html($rating) . "'><meta itemprop='ratingCount' content='" . esc_attr($count) . "'></div></div>", "post", $post->ID);
            }
        }
        return $html;
    }

    public function getRatingMetaHtml($atts = []) {
        $html = "";
        $atts = shortcode_atts([
            "metakey" => "all",
            "show-label" => true,
            "show-lable" => true,
            "show-count" => true,
            "show-average" => true,
            "itemprop" => !!$this->wpdOptions->rating["enablePostRatingSchema"],
            "post_id" => null,
            "postid" => null,
                ], $atts);
        if (!$atts["postid"]) {
            if ($atts["post_id"]) {
                $post = get_post($atts["post_id"]);
                unset($atts["post_id"]);
            } else {
                global $post;
            }
            if (!empty($post->ID)) {
                $atts["postid"] = $post->ID;
                $this->resetData();
                $wpdiscuz = wpDiscuz();
                $form = $wpdiscuz->wpdiscuzForm->getForm($post->ID);
                if (is_rtl()) {
                    wp_enqueue_style("wpdiscuz-ratings-rtl");
                } else {
                    wp_enqueue_style("wpdiscuz-ratings");
                }
                $html = $form->getRatingMetaHtml($atts);
                $form->resetData();
                global $post;
                $form = $wpdiscuz->wpdiscuzForm->getForm($post->ID);
                return $html;
            }
        } else {
            $post = get_post($atts["postid"]);
            $this->initFormFields();
            if ($this->ratingsExists && (($this->wpdOptions->rating["ratingCssOnNoneSingular"] && !is_singular()) || is_singular())) {
                $wpdiscuzRatingCountMeta = get_post_meta($post->ID, wpdFormConst::WPDISCUZ_RATING_COUNT, true);
                $wpdiscuzRatingCount = $wpdiscuzRatingCountMeta && is_array($wpdiscuzRatingCountMeta) ? $wpdiscuzRatingCountMeta : [];
                $ratingList = [];
                foreach ($wpdiscuzRatingCount as $metaKey => $data) {
                    $tempRating = 0;
                    $tempRatingCount = 0;
                    foreach ($data as $rating => $count) {
                        $tempRating += $rating * $count;
                        $tempRatingCount += $count;
                    }
                    if ($tempRatingCount <= 0) {
                        $ratingList[$metaKey]["average"] = 0;
                        $ratingList[$metaKey]["count"] = 0;
                    } else {
                        $ratingList[$metaKey]["average"] = round($tempRating / $tempRatingCount, 1);
                        $ratingList[$metaKey]["count"] = $tempRatingCount;
                    }
                }
                if ($ratingList) {
                    $atts["show-label"] = filter_var($atts['show-label'], FILTER_VALIDATE_BOOLEAN);
                    if (!filter_var($atts['show-lable'], FILTER_VALIDATE_BOOLEAN)) {
                        $atts["show-label"] = false;
                    }
                    $html .= "<div class='wpdiscuz-post-rating-wrap wpd-custom-field'>";
                    if (!isset($atts["metakey"]) || $atts["metakey"] == "" || $atts["metakey"] == "all") {
                        $avg = 0;
                        $q = 0;
                        foreach ($ratingList as $key => $value) {
                            $html .= $this->getSingleRatingHtml($key, $value, $atts);
                        }
                        if ($atts["itemprop"]) {
                            $html .= $this->getRatingSchema($atts["metakey"], $ratingList, $atts["postid"]);
                        }
                    } else {
                        $html .= $this->getSingleRatingHtml($atts["metakey"], $ratingList[$atts["metakey"]], $atts);
                        if ($atts["itemprop"] && $ratingList[$atts["metakey"]]["count"]) {
                            $html .= $this->getRatingSchema($atts["metakey"], $ratingList, $atts["postid"]);
                        }
                    }
                    $html .= "</div>";
                }
            }
        }
        return $html;
    }

    private function getSingleRatingHtml($metakey, $ratingData, $args) {
        global $post;
        $html = "";
        if (key_exists($metakey, $this->formCustomFields)) {
            $title = !empty($this->formCustomFields[$metakey]["nameForTotal"]) ? $this->formCustomFields[$metakey]["nameForTotal"] : $this->formCustomFields[$metakey]["name"];
            $icon = $this->formCustomFields[$metakey]['icon'];
            $icon = strpos(trim($icon), ' ') ? $icon : 'fas ' . $icon;
            $fullStarSVG = apply_filters("wpdiscuz_full_star_svg", "<svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 24 24'><path d='M0 0h24v24H0z' fill='none'/><path class='wpd-star' d='M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z'/><path d='M0 0h24v24H0z' fill='none'/></svg>", "custom_field", $icon);
            $halfStarSVG = apply_filters("wpdiscuz_half_star_svg", "<svg xmlns='https://www.w3.org/2000/svg' xmlns:xlink='https://www.w3.org/1999/xlink' viewBox='0 0 24 24'><defs><path id='a' d='M0 0h24v24H0V0z'/></defs><clipPath id='b'><use xlink:href='#a' overflow='visible'/></clipPath><path class='wpd-star wpd-active' clip-path='url(#b)' d='M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4V6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z'/></svg>", "custom_field", $icon);
            $html = "<div class='wpd-rating' title='" . esc_attr($title) . "'>
            <div class='wpd-rating-wrap'>
            <div class='wpd-rating-left'></div>
            <div class='wpd-rating-data'>
                <div class='wpd-rating-value'>
                    <span class='wpdrv'>" . esc_html($ratingData["average"]) . "</span>
                    <span class='wpdrc'>" . esc_html($ratingData["count"]) . "</span>
                    <span class='wpdrt'>" . ((int) $ratingData["count"] > 1 ? esc_html($this->wpdOptions->phrases["wc_votes_phrase"]) : esc_html($this->wpdOptions->phrases["wc_vote_phrase"])) . "</span>";
            $html .= "</div>";
            if ($args["show-label"]) {
                $html .= "<div class='wpd-rating-title'>" . esc_html($title) . "</div>";
            }
            $html .= "<div class='wpd-rating-stars'>";
            $prefix = (int) $ratingData['average'];
            $suffix = $ratingData['average'] - $prefix;
            if ($prefix) {
                for ($i = 1; $i < 6; $i++) {
                    if ($i <= $prefix) {
                        $html .= str_replace("wpd-star", "wpd-star wpd-active", $fullStarSVG);
                    } else if ($suffix && $i - $prefix === 1) {
                        $html .= $halfStarSVG;
                    } else {
                        $html .= $fullStarSVG;
                    }
                }
            } else if ($suffix) {
                $html .= $halfStarSVG . str_repeat($fullStarSVG, 4);
            } else {
                $html .= str_repeat($fullStarSVG, 5);
            }
            $html .= "</div>
            </div>
            <div class='wpd-rating-right'></div></div></div>";
        }
        return $html;
    }

    private function getRatingSchema($key, $ratingList, $postId) {
        $average = 0;
        $count = 0;
        if ($key === "" || $key === "all") {
            foreach ($ratingList as $k => $value) {
                if (isset($this->formCustomFields[$k]) && $ratingList[$k]["count"]) {
                    $average += $ratingList[$k]["average"];
                    $count++;
                }
            }
            if ($count) {
                $average = round($average / $count, 1);
            }
        } else if (isset($this->formCustomFields[$key]) && $ratingList[$key]["count"]) {
            $average = $ratingList[$key]["average"];
            $count = $ratingList[$key]["count"];
        }
        $schema = "";
        if ($average) {
            $schema = apply_filters("wpdiscuz_rating_schema", "<div style='display: none;' itemscope itemtype='https://schema.org/Product'><meta itemprop='name' content='" . esc_attr__("Average Rating", "wpdiscuz") . "'><div style='display: none;' itemprop='aggregateRating' itemscope itemtype='https://schema.org/AggregateRating'><meta itemprop='bestRating' content='5'><meta itemprop='worstRating' content='1'><meta itemprop='ratingValue' content='" . esc_attr($average) . "'><meta itemprop='ratingCount' content='" . esc_attr($count) . "'></div></div>", $key, $postId);
        }
        return $schema;
    }

    private function validateGeneralOptions($options) {
        $validData = [
            "lang" => get_locale(),
            "roles_cannot_comment" => [],
            "guest_can_comment" => 1,
            "show_subscription_bar" => 1,
            "header_text_single" => "",
            "header_text_plural" => "",
            wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES => [],
            "postid" => "",
            "postidsArray" => [],
            "show_subscription_agreement" => 0,
            "subscription_agreement_label" => esc_html__("I allow to use my email address and send notification about new comments and replies (you can unsubscribe at any time).", "wpdiscuz"),
            "theme" => $this->getDefaultTheme(),
            "layout" => 1,
            "enable_post_rating" => 1,
            "post_rating_title" => esc_html__("Article Rating", "wpdiscuz"),
            "allow_guests_rate_on_post" => 1,
        ];
        if (isset($options["roles_cannot_comment"])) {
            $validData["roles_cannot_comment"] = array_map("trim", $options["roles_cannot_comment"]);
        }
        if (isset($options["guest_can_comment"])) {
            $validData["guest_can_comment"] = intval($options["guest_can_comment"]);
        }
        if (isset($options["header_text_single"])) {
            $validData["header_text_single"] = $options["header_text_single"];
        }
        if (isset($options["header_text_plural"])) {
            $validData["header_text_plural"] = $options["header_text_plural"];
        }
        if (isset($options["lang"])) {
            $validData["lang"] = $options["lang"];
        }
        if (isset($options["show_subscription_bar"])) {
            $validData["show_subscription_bar"] = intval($options["show_subscription_bar"]);
        }
        if (isset($options["show_subscription_agreement"])) {
            $validData["show_subscription_agreement"] = intval($options["show_subscription_agreement"]);
        }
        if (isset($options["subscription_agreement_label"]) && trim($options["subscription_agreement_label"])) {
            $validData["subscription_agreement_label"] = $options["subscription_agreement_label"];
        }
        $themes = $this->getThemes();
        if (isset($options["theme"]) && isset($themes[$options["theme"]])) {
            $validData["theme"] = $options["theme"];
        }
        $layouts = $this->getLayouts($validData["theme"]);
        if (isset($options["layout"]) && ($layout = intval($options["layout"])) && in_array($layout, $layouts)) {
            $validData["layout"] = $layout;
        } else if (!empty($layouts[0])) {
            $validData["layout"] = $layouts[0];
        }
        if (isset($options["enable_post_rating"])) {
            $validData["enable_post_rating"] = intval($options["enable_post_rating"]);
        }
        if (!empty($options["post_rating_title"])) {
            $validData["post_rating_title"] = $options["post_rating_title"];
        }
        if (isset($options["allow_guests_rate_on_post"])) {
            $validData["allow_guests_rate_on_post"] = intval($options["allow_guests_rate_on_post"]);
        }

        if (isset($options[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES])) {
            $validData[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] = $options[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES];
        }
        if (isset($options["postid"])) {
            $postIds = trim(strip_tags($options["postid"]));
            if ($postIds) {
                $postIdsArray = [];
                $postIdsExplode = explode(",", $postIds);
                foreach ($postIdsExplode as $k => $postId) {
                    $postId = intval($postId);
                    if ($postId) {
                        $postIdsArray[] = $postId;
                    }
                }
                $postIdsArray = array_unique($postIdsArray);
                sort($postIdsArray);
                $validData["postidsArray"] = $postIdsArray;
                $postIds = implode(", ", $postIdsArray);
            }
            $validData["postid"] = $postIds;
        }
        return $validData;
    }

    private function validateFormStructure($formStructure) {
        $this->formFields = [];
        foreach ($formStructure as $rowID => $rowData) {
            $sanitizeData = $this->row->sanitizeRowData($rowData, $this->formFields);
            if ($sanitizeData) {
                $formStructure[$rowID] = $sanitizeData;
            } else {
                unset($formStructure[$rowID]);
            }
        }
        return $formStructure;
    }

    public function validateFields($currentUser) {
        $allowedFieldsType = $this->row->allowedFieldsType();
        foreach ($this->formCustomFields as $fieldName => $fieldArgs) {
            $fieldType = $fieldArgs["type"];
            if (!in_array($fieldType, $allowedFieldsType, true)) {
                continue;
            }
            $field = call_user_func($fieldType . "::getInstance");
            if (isset($fieldArgs["no_insert_meta"])) {
                $field->validateFieldData($fieldName, $fieldArgs, $this->wpdOptions, $currentUser);
            } else {
                $this->fieldsBeforeSave[$fieldName] = $field->validateFieldData($fieldName, $fieldArgs, $this->wpdOptions, $currentUser);
            }
        }
    }

    public function validateDefaultCaptcha($currentUser) {
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD];
        $this->captchaFied->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD, $args, $this->wpdOptions, $currentUser);
    }

    public function validateSubscribtionCaptcha($addSubscription) {
        if (!is_user_logged_in() && $this->wpdOptions->recaptcha["isShowOnSubscribeForm"]) {
            $addSubscription = $this->captchaFied->reCaptchaValidate($this->wpdOptions);
        }
        return $addSubscription;
    }

    public function validateDefaultEmail($currentUser, &$isAnonymous) {
        $emailField = Field\DefaultField\Email::getInstance();
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD];
        $email = $emailField->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD, $args, $this->wpdOptions, $currentUser);
        $isAnonymous = $emailField->isAnonymous();
        return $email;
    }

    public function validateDefaultName($currentUser) {
        $nameField = Field\DefaultField\Name::getInstance();
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD];
        return $nameField->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD, $args, $this->wpdOptions, $currentUser);
    }

    public function validateDefaultWebsite($currentUser) {
        $webSiteField = Field\DefaultField\Website::getInstance();
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD];
        return $webSiteField->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD, $args, $this->wpdOptions, $currentUser);
    }

    public function renderFrontCommentMetaHtml($commentID, &$output) {
        $htmlExists = false;
        if ($this->formCustomFields) {
            $meta = get_comment_meta($commentID);
            $top = $this->_renderFrontCommentMetaHtml($meta, $this->formCustomFields, "top");
            $bottom = $this->_renderFrontCommentMetaHtml($meta, $this->formCustomFields, "bottom");
            if ($top || $bottom) {
                $htmlExists = true;
            }
            $top = ( $top ) ? "<div class='wpd-top-custom-fields'>" . $top . "</div>" : "";
            $bottom = ( $bottom ) ? "<div class='wpd-bottom-custom-fields'>" . $bottom . "</div>" : "";
            $output = $top . $output . $bottom;
        }
        return $htmlExists;
    }

    private function _renderFrontCommentMetaHtml($meta, $formCustomFields, $loc) {
        $html = "";
        $allowedFieldsType = $this->row->allowedFieldsType();
        foreach ($formCustomFields as $key => $value) {
            if (isset($value["loc"]) && $value["loc"] == $loc) {
                $fieldType = $value["type"];
                $metaValuen = isset($meta[$key][0]) ? maybe_unserialize($meta[$key][0]) : "";
                if (in_array($fieldType, $allowedFieldsType, true) && is_callable($fieldType . "::getInstance") && $metaValuen) {
                    $field = call_user_func($fieldType . "::getInstance");
                    $html .= $field->drawContent($metaValuen, $value);
                }
            }
        }
        return $html;
    }

    public function renderFrontForm($isMain, $uniqueId, $commentsCount, $currentUser) {
        $message = "";
        ?>
        <div class="wpd-form wpd-form-wrapper <?php echo!$isMain ? "wpd-secondary-form-wrapper" : "wpd-main-form-wrapper"; ?>" <?php echo!$isMain ? "id='wpd-secondary-form-wrapper-" . esc_attr($uniqueId) . "' style='display: none;'" : "id='wpd-main-form-wrapper-" . esc_attr($uniqueId) . "'"; ?>>
            <?php if (!$isMain) { ?>
                <div class="wpd-secondary-forms-social-content"><?php do_action("comment_reply_form_bar_top", $this); ?></div><div class="clearfix"></div>
            <?php } ?>
            <?php
            if ($this->isUserCanComment($currentUser, $message)) {
                ?>
                <form class="wpd_comm_form <?php echo $isMain ? "wpd_main_comm_form" : "wpd-secondary-form-wrapper"; ?>" method="post" enctype="multipart/form-data">
                    <div class="wpd-field-comment">
                        <div class="wpdiscuz-item wc-field-textarea">
                            <div class="wpdiscuz-textarea-wrap <?php echo $this->wpdOptions->form["richEditor"] === "both" || (!wp_is_mobile() && $this->wpdOptions->form["richEditor"] === "desktop") ? "" : "wpd-txt"; ?>">
                                <?php if ($this->wpdOptions->thread_layouts["showAvatars"] && $this->wpdOptions->wp["showAvatars"]) { ?>
                                    <?php
                                    $authorName = $currentUser->ID ? $currentUser->display_name : "guest";
                                    $authorEmail = $currentUser->ID ? $currentUser->user_email : "unknown@example.com";
                                    ?>
                                    <div class="wpd-avatar">
                                        <?php echo get_avatar($currentUser->ID, 46, "", $authorName, ["wpdiscuz_current_user" => $currentUser, "wpdiscuz_gravatar_user_email" => $authorEmail]); ?>
                                    </div>
                                    <?php
                                }
                                $this->renderTextEditor($uniqueId, $commentsCount);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="wpd-form-foot" <?php echo $this->wpdOptions->form["commentFormView"] === "collapsed" ? "style='display:none;'" : ""; ?>>
                        <div class="wpdiscuz-textarea-foot">
                            <?php do_action("wpdiscuz_button", $uniqueId, $currentUser, $this); ?>
                            <div class="wpdiscuz-button-actions"><?php do_action("wpdiscuz_button_actions", $uniqueId, $currentUser, $this); ?></div>
                        </div>
                        <?php
                        foreach ($this->formeStructure as $k => $row) {
                            $this->row->renderFrontFormRow($row, $this->wpdOptions, $currentUser, $uniqueId, $isMain);
                        }
                        ?>
                    </div>
                    <?php
                    if ($this->wpdOptions->isGoodbyeCaptchaActive) {
                        echo $this->wpdOptions->goodbyeCaptchaTocken;
                    }
                    ?>
                    <input type="hidden" class="wpdiscuz_unique_id" value="<?php echo esc_attr($uniqueId); ?>" name="wpdiscuz_unique_id">
                </form>
                <?php
            }
            do_action("wpdiscuz_form_bottom", $isMain, $this, $currentUser, $commentsCount, $uniqueId);
            ?>
        </div>
        <?php
    }

    private function renderTextEditor($uniqueId, $commentsCount) {
        if ($this->wpdOptions->form["richEditor"] === "both" || (!wp_is_mobile() && $this->wpdOptions->form["richEditor"] === "desktop")) {
            ?>
            <div id="wpd-editor-wraper-<?php echo esc_attr($uniqueId); ?>" style="display: none;">
                <div id="wpd-editor-char-counter-<?php echo esc_attr($uniqueId); ?>" class="wpd-editor-char-counter"></div>
                <textarea id="wc-textarea-<?php echo esc_attr($uniqueId); ?>" required name="wc_comment" class="wc_comment wpd-field"></textarea>
                <div id="wpd-editor-<?php echo esc_attr($uniqueId); ?>"></div>
            <?php $this->renderTextEditorButtons($uniqueId); ?>
            </div>
            <?php
        } else {
            if ($uniqueId !== "0_0" || $commentsCount) {
                $textarea_placeholder = $this->wpdOptions->phrases["wc_comment_join_text"];
            } else {
                $textarea_placeholder = $this->wpdOptions->phrases["wc_be_the_first_text"];
            }
            ?>
            <div class="wpd-textarea-wrap">
                <textarea id="wc-textarea-<?php echo esc_attr($uniqueId); ?>" placeholder="<?php echo esc_attr($textarea_placeholder); ?>" required name="wc_comment" class="wc_comment wpd-field"></textarea>
            </div>
            <?php
            echo apply_filters("wpdiscuz_editor_buttons_html", "", $uniqueId);
        }
    }

    private function renderTextEditorButtons($uniqueId) {
        $editorButtons = [];
        if ($this->wpdOptions->form["boldButton"]) {
            $editorButtons[] = ["class" => "ql-bold", "value" => "", "name" => "", "title" => "Bold", "svg" => ""];
        }
        if ($this->wpdOptions->form["italicButton"]) {
            $editorButtons[] = ["class" => "ql-italic", "value" => "", "name" => "", "title" => "Italic", "svg" => ""];
        }
        if ($this->wpdOptions->form["underlineButton"]) {
            $editorButtons[] = ["class" => "ql-underline", "value" => "", "name" => "", "title" => "Underline", "svg" => ""];
        }
        if ($this->wpdOptions->form["strikeButton"]) {
            $editorButtons[] = ["class" => "ql-strike", "value" => "", "name" => "", "title" => "Strike", "svg" => ""];
        }
        if ($this->wpdOptions->form["olButton"]) {
            $editorButtons[] = ["class" => "ql-list", "value" => "ordered", "name" => "", "title" => "Ordered List", "svg" => ""];
        }
        if ($this->wpdOptions->form["ulButton"]) {
            $editorButtons[] = ["class" => "ql-list", "value" => "bullet", "name" => "", "title" => "Unordered List", "svg" => ""];
        }
        if ($this->wpdOptions->form["blockquoteButton"]) {
            $editorButtons[] = ["class" => "ql-blockquote", "value" => "", "name" => "", "title" => "Blockquote", "svg" => ""];
        }
        if ($this->wpdOptions->form["codeblockButton"]) {
            $editorButtons[] = ["class" => "ql-code-block", "value" => "", "name" => "", "title" => "Code Block", "svg" => ""];
        }
        if ($this->wpdOptions->form["linkButton"]) {
            $editorButtons[] = ["class" => "ql-link", "value" => "", "name" => "", "title" => "Link", "svg" => ""];
        }
        if ($this->wpdOptions->form["sourcecodeButton"]) {
            $editorButtons[] = ["class" => "ql-sourcecode", "value" => "", "name" => "sourcecode", "title" => "Source Code", "svg" => "{}"];
        }
        if ($this->wpdOptions->form["spoilerButton"]) {
            $editorButtons[] = ["class" => "ql-spoiler", "value" => "", "name" => "spoiler", "title" => "Spoiler", "svg" => "[+]"];
        }
        $editorButtons = apply_filters("wpdiscuz_editor_buttons", $editorButtons, $uniqueId);
        $editorButtonsHtml = apply_filters("wpdiscuz_editor_buttons_html", "", $uniqueId);
        ?>
        <div id="wpd-editor-toolbar-<?php echo esc_attr($uniqueId); ?>"<?php echo $editorButtons || $editorButtonsHtml ? "" : " class='wpd-toolbar-hidden'"; ?>>
            <?php
            foreach ($editorButtons as $k => $editorButton) {
                $value = $editorButton["value"] ? "value='" . esc_attr($editorButton["value"]) . "'" : "";
                $dataName = $editorButton["name"] ? "data-wpde_button_name='" . esc_attr($editorButton["name"]) . "'" : "";
                ?>
                <button title="<?php esc_attr_e($editorButton["title"], "wpdiscuz"); ?>" class="<?php echo esc_attr($editorButton["class"]); ?>" <?php echo $value; ?> <?php echo $dataName; ?>><?php echo $editorButton["svg"]; ?></button>
                <?php
            }
            ?>
            <div class="wpd-editor-buttons-right">
                <?php
                echo $editorButtonsHtml;
                ?>
            </div>
        </div>
        <?php
    }

    public function renderEditFrontCommentForm($comment) {
        $uniqueId = $comment->comment_ID . "_" . $comment->comment_parent;
        $html = "<div class='wpdiscuz-edit-form-wrap'><form id='wpdiscuz-edit-form'>";
        $html .= "<div class='wpdiscuz-item wpdiscuz-textarea-wrap'>";
        $content = str_replace(["<code>", "</code>"], ["`", "`"], ($this->wpdOptions->form["richEditor"] === "both" || (!wp_is_mobile() && $this->wpdOptions->form["richEditor"] === "desktop") ? str_replace(["</p>\n", "<br />\n"], ["</p><br>", "<br />"], wpautop($comment->comment_content)) : $comment->comment_content));
        ob_start();
        $this->renderTextEditor("edit_" . $uniqueId, 1);
        $html .= ob_get_clean();
        $html .= "</div>";
        if ($this->formCustomFields) {
            $html .= "<table class='form-table editcomment wpd-form-row'><tbody>";
            $allowedFieldsType = $this->row->allowedFieldsType();
            foreach ($this->formCustomFields as $key => $data) {
                $fieldType = $data["type"];
                if (in_array($fieldType, $allowedFieldsType, true)) {
                    $field = call_user_func($fieldType . "::getInstance");
                    $value = get_comment_meta($comment->comment_ID, $key, true);
                    $html .= $field->editCommentHtml($key, $value, $data, $comment);
                }
            }
            $html .= "</tbody></table>";
        }
        $html .= "<input type='hidden' name='wpdiscuz_unique_id' value='" . esc_attr($uniqueId) . "'>";
        $html .= "<div class='wc_save_wrap'><input class='wc_cancel_edit wpd-second-button' type='button' value='" . esc_attr($this->wpdOptions->phrases["wc_comment_edit_cancel_button"]) . "'><input id='wpd-field-submit-edit_" . esc_attr($uniqueId) . "' class='wc_save_edited_comment wpd-prim-button' type='submit' value='" . esc_attr($this->wpdOptions->phrases["wc_comment_edit_save_button"]) . "'></div>";
        $html .= "</form></div>";
        return wp_send_json_success(['html' => $html, 'content' => $content]);
    }

    public function renderEditAdminCommentForm($comment) {
        if ($this->formCustomFields) {
            ?>
            <div  class="stuffbox">
                <div class="inside">
                    <fieldset>
                        <legend class="edit-comment-author"><?php esc_html_e("Custom Fields", "wpdiscuz"); ?></legend>
                        <table class="form-table editcomment">
                            <tbody>
                                <?php
                                $allowedFieldsType = $this->row->allowedFieldsType();
                                foreach ($this->formCustomFields as $key => $data) {
                                    $fieldType = $data["type"];
                                    if (in_array($fieldType, $allowedFieldsType, true)) {
                                        $field = call_user_func($fieldType . "::getInstance");
                                        $value = get_comment_meta($comment->comment_ID, $key, true);
                                        echo $field->editCommentHtml($key, $value, $data, $comment);
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="wpdiscuz_unique_id" value="<?php echo esc_attr($comment->comment_ID . "_" . $comment->comment_parent); ?>">
                    </fieldset>
                </div>
            </div>
            <?php
        }
    }

    public function renderFormStructure() {
        $this->initFormMeta();
        ?>
        <style>.wpd-form-table td{ position: relative;} .wpd-form-table td i.fa-question-circle{ font-size: 16px; right: 15px; top: 15px; position: absolute;} .wpdiscuz-form-builder-help{text-align: right; padding: 5px; font-size: 16px; margin-top: -15px;}</style>
        <style>[dir=rtl] .wpd-form-table td{ position: relative; padding-left: 25px;}  [dir=rtl] .wpd-form-table td i.fa-question-circle{ font-size: 16px; right:auto; left: 0px; top: 15px; position: absolute;}  [dir=rtl] .wpdiscuz-form-builder-help{text-align: left; padding: 5px; font-size: 16px; margin-top: -15px;}</style>
        <div class="wpdiscuz-wrapper">
            <div class="wpd-form-options" style="width:100%;">
                <table class="wpd-form-table" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:10px 0px 20px 0px;">
                    <tbody>
                        <tr>
                            <th>
        <?php esc_html_e("Language", "wpdiscuz"); ?>
                            </th>
                            <td>
        <?php $lang = isset($this->generalOptions["lang"]) ? $this->generalOptions["lang"] : get_locale(); ?>
                                <input required="" type="text" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[lang]" value="<?php echo htmlentities($lang, ENT_QUOTES); ?>" >
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#language" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>                        
                        <tr>
                            <th>
        <?php esc_html_e("Disable commenting for roles", "wpdiscuz"); ?>
                            </th>
                            <td>
                                <?php
                                $blogRoles = get_editable_roles();
                                $rolesCannotComment = isset($this->generalOptions["roles_cannot_comment"]) ? $this->generalOptions["roles_cannot_comment"] : [];
                                foreach ($blogRoles as $role => $info) {
                                    if ($role != "administrator") {
                                        ?>
                                        <div style="float:<?php echo (is_rtl()) ? 'right' : 'left'; ?>; display:inline-block; padding:3px 5px 3px 7px; min-width:25%;">
                                            <input type="checkbox" <?php checked(in_array($role, $rolesCannotComment) == true); ?> value="<?php echo esc_attr($role); ?>" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[roles_cannot_comment][]" id="wpd-<?php echo esc_attr($role); ?>" style="margin:0px; vertical-align: middle;" />
                                            <label for="wpd-<?php echo esc_attr($role); ?>" style="white-space:nowrap; font-size:13px;"><?php echo esc_html($info["name"]); ?></label>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#disable_commenting_for_roles" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Allow guests to comment", "wpdiscuz"); ?>
                            </th>
                            <td>
        <?php $guestCanComment = isset($this->generalOptions["guest_can_comment"]) ? $this->generalOptions["guest_can_comment"] : 1; ?>
                                <input <?php checked($guestCanComment, 1, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[guest_can_comment]" value="1" id="wpd_cf_guest_yes" > <label for="wpd_cf_guest_yes"><?php esc_html_e("Yes", "wpdiscuz"); ?></label>
                                &nbsp; 
                                <input <?php checked($guestCanComment, 0, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[guest_can_comment]" value="0" id="wpd_cf_guest_no"> <label for="wpd_cf_guest_no"><?php esc_html_e("No", "wpdiscuz"); ?></label>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#only-loggedin" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Enable subscription bar", "wpdiscuz"); ?>
                            </th>
                            <td>
        <?php $showSubscriptionBar = isset($this->generalOptions["show_subscription_bar"]) ? $this->generalOptions["show_subscription_bar"] : 1; ?>
                                <input <?php checked($showSubscriptionBar, 1, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[show_subscription_bar]" value="1" id="wpd_cf_sbbar_yes" > <label for="wpd_cf_sbbar_yes"><?php esc_html_e("Yes", "wpdiscuz"); ?></label>
                                &nbsp; 
                                <input <?php checked($showSubscriptionBar, 0, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[show_subscription_bar]" value="0" id="wpd_cf_sbbar_no"> <label for="wpd_cf_sbbar_no"><?php esc_html_e("No", "wpdiscuz"); ?></label>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#subscription-bar" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Display agreement checkbox in Comment Subscription bar", "wpdiscuz"); ?>
                            </th>
                            <td>
        <?php $showSubscriptionAgreement = isset($this->generalOptions["show_subscription_agreement"]) ? $this->generalOptions["show_subscription_agreement"] : 0; ?>
                                <input <?php checked($showSubscriptionAgreement, 1, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[show_subscription_agreement]" value="1" id="wpd_cf_sbbar_agreement_yes" > <label for="wpd_cf_sbbar_agreement_yes"><?php esc_html_e("Yes", "wpdiscuz"); ?></label>
                                &nbsp; 
                                <input <?php checked($showSubscriptionAgreement, 0, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[show_subscription_agreement]" value="0" id="wpd_cf_sbbar_agreement_no"> <label for="wpd_cf_sbbar_agreement_no"><?php esc_html_e("No", "wpdiscuz"); ?></label>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#sb-checkbox" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Comment Subscription bar agreement checkbox label", "wpdiscuz"); ?>
                            </th>
                            <td>
        <?php $subscriptionAgreementLabel = isset($this->generalOptions["subscription_agreement_label"]) && $this->generalOptions["subscription_agreement_label"] ? $this->generalOptions["subscription_agreement_label"] : esc_html__("I allow to use my email address and send notification about new comments and replies (you can unsubscribe at any time).", "wpdiscuz"); ?>
                                <textarea name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[subscription_agreement_label]" style="width:80%;"><?php echo $subscriptionAgreementLabel; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Comment form header text (singular)", "wpdiscuz"); ?>
                            </th>
                            <td >
                                <div>
                                    <input  type="text" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[header_text_single]" placeholder="<?php esc_attr_e("Comment", "wpdiscuz"); ?>" value="<?php echo isset($this->generalOptions["header_text_single"]) ? htmlentities($this->generalOptions["header_text_single"], ENT_QUOTES) : esc_html__("Comment", "wpdiscuz"); ?>" style="width:80%;">
                                </div>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#comment_form_header_text" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Comment form header text (plural)", "wpdiscuz"); ?>
                            </th>
                            <td >
                                <div>
                                    <input  type="text" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[header_text_plural]" placeholder="<?php esc_attr_e("Comments", "wpdiscuz"); ?>" value="<?php echo isset($this->generalOptions["header_text_plural"]) ? htmlentities($this->generalOptions["header_text_plural"], ENT_QUOTES) : esc_html__("Comments", "wpdiscuz"); ?>" style="width:80%;">
                                </div>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#comment_form_header_text" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th> <?php esc_html_e("Display comment form for post types", "wpdiscuz"); ?></th>
                            <td class="wpd-ct"> 
                                <?php
                                $this->formPostTypes = $this->formPostTypes ? $this->formPostTypes : [];
                                $registeredPostTypes = get_post_types(["public" => true]);
                                $formContentTypeRel = $this->wpdOptions->formContentTypeRel;
                                $hasForm = false;
                                $formRelExistsInfo = "<p class='wpd-info' style='padding-top:3px;'>" . esc_html__("The red marked post types are already attached to other comment form. If you set this form too, the old forms will not be used for them.", "wpdiscuz") . "</p>";
                                foreach ($registeredPostTypes as $typeKey => $typeValue) {
                                    if (!post_type_supports($typeKey, "comments")) {
                                        continue;
                                    }
                                    $checked = array_key_exists($typeKey, $this->formPostTypes) ? "checked" : "";
                                    $formRelExistsClass = "";
                                    if (!$checked && isset($formContentTypeRel[$typeKey][$lang])) {
                                        $formRelExistsClass = "wpd-form-rel-exixts";
                                        $hasForm = true;
                                    }
                                    ?>
                                    <label class="<?php echo esc_attr($formRelExistsClass); ?>" for="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES . "-" . $typeKey); ?>">
                                        <input  value="<?php echo esc_attr($typeKey); ?>" id="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES . "-" . $typeKey); ?>" type="checkbox" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES) . "][" . esc_attr($typeKey) . "]"; ?>" <?php echo $checked; ?>/>
                                        <span><?php echo esc_html($typeValue); ?></span>
                                    </label>
                                <?php } ?>
        <?php if ($hasForm) echo $formRelExistsInfo; ?>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#post-types" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Display comment form for post IDs", "wpdiscuz"); ?>
                                <p class="wpd-info"> <?php esc_html_e("You can use this form for certain posts/pages specified by comma separated IDs.", "wpdiscuz"); ?></p>
                            </th>
                            <td>
                                <input type="text" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[postid]" placeholder="5,26,30..." value="<?php echo isset($this->generalOptions["postid"]) ? htmlentities($this->generalOptions["postid"], ENT_QUOTES) : ""; ?>" style="width:80%;">
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#comment_form_for_post_id" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <?php
                        if ($themes = $this->getThemes()) {
                            $theme = !empty($this->generalOptions["theme"]) && isset($themes[$this->generalOptions["theme"]]) ? $this->generalOptions["theme"] : $this->getDefaultTheme();
                            if (count($themes) > 1) {
                                ?>
                                <tr>
                                    <th>
                <?php esc_html_e("Theme", "wpdiscuz"); ?>
                                    </th>
                                    <td>
                                        <?php
                                        foreach ($themes as $k => $val) {
                                            ?>
                                            <input <?php checked($theme, $k, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[theme]" value="<?php echo esc_attr($k); ?>" id="wpd_cf_theme_<?php echo esc_attr($val["name"]); ?>" > <label for="wpd_cf_theme_<?php echo esc_attr($val["name"]); ?>"><?php echo esc_html($val["name"]); ?></label>
                                            &nbsp;
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            if (($layouts = $this->getLayouts($theme)) && count($layouts) > 1) {
                                ?>
                                <tr>
                                    <th>
                <?php esc_html_e("Comment List Layout", "wpdiscuz"); ?>
                                    </th>
                                    <td>
                                        <div id="wpd_comment_layouts" style="width: 290px; margin: 15px 0 0 0">
                                            <?php
                                            $layout = !empty($this->generalOptions["layout"]) ? $this->generalOptions["layout"] : $layouts[0];
                                            foreach ($layouts as $k => $value) {
                                                ?>
                                                <div class="wpd-box-layout">
                                                    <a href="#img<?php echo esc_attr($value); ?>"><img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-" . $value . "s.png"); ?>" class="wpd-com-layout-<?php echo esc_attr($value); ?>" style="height: 85px;"/></a>
                                                    <a href="#_" class="wpd-lightbox" id="img<?php echo esc_attr($value); ?>"><img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-" . $value . ".png"); ?>"/></a>
                                                    <h4><input <?php checked($layout, $value, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[layout]" value="<?php echo esc_attr($value); ?>" id="wpd_cf_layout_<?php echo esc_attr($value); ?>"> <label for="wpd_cf_layout_<?php echo esc_attr($value); ?>"><?php esc_html_e("Layout", "wpdiscuz") ?> #<?php echo esc_html($value); ?></label></h4>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#comment-thread-layout" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <tr>
                            <th>
        <?php esc_html_e("Enable Post Rating", "wpdiscuz"); ?>
                            </th>
                            <td>
        <?php $enablePostRating = isset($this->generalOptions["enable_post_rating"]) ? $this->generalOptions["enable_post_rating"] : 1; ?>
                                <input <?php checked($enablePostRating, 1, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[enable_post_rating]" value="1" id="wpd_enable_post_rating_yes" > <label for="wpd_enable_post_rating_yes"><?php esc_html_e("Yes", "wpdiscuz"); ?></label>
                                &nbsp; 
                                <input <?php checked($enablePostRating, 0, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[enable_post_rating]" value="0" id="wpd_enable_post_rating_no"> <label for="wpd_enable_post_rating_no"><?php esc_html_e("No", "wpdiscuz"); ?></label>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#enable-post-rating" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Post Rating Title", "wpdiscuz"); ?>
                            </th>
                            <td >
                                <div>
                                    <input  type="text" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[post_rating_title]" placeholder="<?php esc_attr_e("Article Rating", "wpdiscuz"); ?>" value="<?php echo isset($this->generalOptions["post_rating_title"]) ? htmlentities($this->generalOptions["post_rating_title"], ENT_QUOTES) : esc_html__("Article Rating", "wpdiscuz"); ?>" style="width:80%;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
        <?php esc_html_e("Allow Guests to Rate", "wpdiscuz"); ?>
                            </th>
                            <td>
        <?php $allowGuestsRateOnPost = isset($this->generalOptions["allow_guests_rate_on_post"]) ? $this->generalOptions["allow_guests_rate_on_post"] : 1; ?>
                                <input <?php checked($allowGuestsRateOnPost, 1, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[allow_guests_rate_on_post]" value="1" id="wpd_allow_guests_rate_on_post_yes" > <label for="wpd_allow_guests_rate_on_post_yes"><?php esc_html_e("Yes", "wpdiscuz"); ?></label>
                                &nbsp; 
                                <input <?php checked($allowGuestsRateOnPost, 0, true); ?> type="radio" name="<?php echo esc_attr(wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS); ?>[allow_guests_rate_on_post]" value="0" id="wpd_allow_guests_rate_on_post_no"> <label for="wpd_allow_guests_rate_on_post_no"><?php esc_html_e("No", "wpdiscuz"); ?></label>
                                <a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-settings/#enable-post-rating" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="wpdiscuz-wrapper">
                <div class="wpdiscuz-form-builder-help"><a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/comment-form-builder/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a></div>
                <div class="wpd-form">
                    <div class="wpd-col-wrap">
                        <div class="wpd-field">
                            <div class="wpd-field-head-textarea"><?php esc_html_e("Comment Text Field", "wpdiscuz"); ?></div>
                        </div>
                    </div>
                    <div id="wpd-form-sortable-rows">
                        <?php
                        if ($this->formeStructure) {
                            foreach ($this->formeStructure as $id => $rowData) {
                                $this->row->dashboardForm($id, $rowData);
                            }
                        } else {
                            $this->row->dashboardForm("wpd_form_row_wrap_0", $this->defaultFieldsData());
                        }
                        ?>
                    </div>
                    <div id="wpdiscuz_form_add_row" class="wpd-field wpd-field-add" style="width:100%; padding:20px; margin:20px 0px; cursor:pointer;" title="Add new custom field">
                        <div class="wpd-field-head-new"><i class="fas fa-plus-circle"></i> <?php esc_html_e("ADD ROW", "wpdiscuz"); ?></div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <?php
    }

    public function isUserCanComment($currentUser, $postId = 0, &$message = "") {
        global $post;
        if (!$post) {
            $post = get_post($postId);
        }
        $user_can_comment = true;
        $this->initFormMeta();
        if ($currentUser && $currentUser->ID) {
            if ($post->post_author && $post->post_author != $currentUser->ID && $currentUser->roles && is_array($currentUser->roles)) {
                $postId = $post && isset($post->ID) ? $post->ID : $postId;
                $this->generalOptions["roles_cannot_comment"] = isset($this->generalOptions["roles_cannot_comment"]) ? $this->generalOptions["roles_cannot_comment"] : [];
                foreach ($currentUser->roles as $k => $role) {
                    if (in_array($role, $this->generalOptions["roles_cannot_comment"])) {
                        //Filter hook to add extra conditions in user role dependent restriction.
                        $user_can_comment = apply_filters("wpdiscuz_user_role_can_comment", false, $role);
                        $message = $this->wpdOptions->phrases["wc_roles_cannot_comment_message"];
                        break;
                    }
                }
            }
        } else {
            $user_can_comment = $this->generalOptions["guest_can_comment"];
        }
        if ($user_can_comment && class_exists("WooCommerce") && get_post_type($postId) == "product") {
            if (get_option("woocommerce_review_rating_verification_required") === "no" || wc_customer_bought_product("", get_current_user_id(), $postId)) {
                $user_can_comment = TRUE;
            } else {
                $user_can_comment = FALSE;
                $message = "<p class='woocommerce-verification-required'>" . esc_html__("Only logged in customers who have purchased this product may leave a review.", "woocommerce") . "</p>";
            }
        }
        $this->isUserCanComment = $user_can_comment;
        return $user_can_comment;
    }

    public function defaultFieldsData() {
        return [
            "column_type" => "two",
            "row_order" => 0,
            "default" => 1,
            "left" => [
                wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD => [
                    "type" => "wpdFormAttr\Field\DefaultField\Name",
                    "name" => esc_html__("Name", "wpdiscuz"),
                    "desc" => "",
                    "icon" => "fas fa-user",
                    "required" => "1"
                ],
                wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD => [
                    "type" => "wpdFormAttr\Field\DefaultField\Email",
                    "name" => esc_html__("Email", "wpdiscuz"),
                    "desc" => "",
                    "icon" => "fas fa-at",
                    "required" => "1"
                ],
                wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD => [
                    "type" => "wpdFormAttr\Field\DefaultField\Website",
                    "name" => esc_html__("Website", "wpdiscuz"),
                    "desc" => "",
                    "icon" => "fas fa-link",
                    "enable" => "1"
                ],
            ],
            "right" => [
                wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD => [
                    "type" => "wpdFormAttr\Field\DefaultField\Captcha",
                    "name" => "",
                    "desc" => "",
                    "show_for_guests" => "0",
                    "show_for_users" => "0"
                ],
                wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD => [
                    "type" => "wpdFormAttr\Field\DefaultField\Submit",
                    "name" => esc_html__("Post Comment", "wpdiscuz")
                ],
            ],
        ];
    }

    private function saveFormContentTypeRel($data, $lang) {
        $contentType = get_option(wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE_REL, []);
        foreach ($this->formPostTypes as $k => $formPostType) {
            if (!in_array($formPostType, $data)) {
                unset($contentType[$formPostType][$lang]);
            }
        }
        foreach ($data as $type => $lable) {
            if (isset($contentType[$type][$lang]) && $contentType[$type][$lang]) {
                $existsFormID = $contentType[$type][$lang];
                $generalOptions = get_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
                if (!empty($generalOptions)) {
                    unset($generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES][$type]);
                }
                update_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
            }
            $contentType[$type][$lang] = $this->formID;
        }
        update_option(wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE_REL, $contentType);
    }

    private function saveFormPostRel($data) {
        $formPostIds = isset($this->generalOptions["postidsArray"]) ? $this->generalOptions["postidsArray"] : [];
        $ids = get_option(wpdFormConst::WPDISCUZ_FORMS_POST_REL, []);
        foreach ($formPostIds as $k => $formPostId) {
            if (!in_array($formPostId, $data)) {
                unset($ids[$formPostId]);
            }
        }
        foreach ($data as $k1 => $id) {
            if (isset($ids[$id]) && $ids[$id]) {
                $existsFormID = $ids[$id];
                $generalOptions = get_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
                if (!$generalOptions) {
                    $generalOptions = ["postidsArray" => []];
                }
                foreach ($generalOptions["postidsArray"] as $key => $pid) {
                    if ($pid == $id) {
                        unset($generalOptions["postidsArray"][$key]);
                    }
                }
                $generalOptions["postid"] = implode(", ", $generalOptions["postidsArray"]);
                update_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
            }
            $ids[$id] = $this->formID;
        }
        update_option(wpdFormConst::WPDISCUZ_FORMS_POST_REL, $ids);
    }

    public function transferJSData($data) {
        $this->initFormFields();
        $data["is_email_field_required"] = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD]["required"];
        return $data;
    }

    public function showRecaptcha() {
        return ($this->wpdOptions->recaptcha["showForGuests"] && !is_user_logged_in()) || ($this->wpdOptions->recaptcha["showForUsers"] && is_user_logged_in()) || ($this->wpdOptions->recaptcha["isShowOnSubscribeForm"] && !is_user_logged_in());
    }

    public function customFieldsExists() {
        $this->initFormFields();
        $exists = $this->formCustomFields ? true : false;
        return $exists;
    }

    public function resetData() {
        $this->formID = 0;
        $this->generalOptions = [];
        $this->formCustomFields = [];
        $this->formFields = [];
    }

    public function getLayouts($theme = '') {
        $theme = $theme ? $theme : $this->generalOptions["theme"];
        $layouts = [];
        $path = $theme . "/layouts/";
        $scannedLayouts = scandir($path);
        unset($scannedLayouts[0]);
        unset($scannedLayouts[1]);
        foreach ($scannedLayouts as $k => $scannedLayout) {
            if (is_dir($path . $scannedLayout)) {
                $layouts[] = $scannedLayout;
            }
        }
        return $layouts;
    }

    public function getThemes() {
        $themes = [];
        $path = str_replace("\\", "/", WPDISCUZ_DIR_PATH) . "/themes/";
        $scannedThemes = scandir($path);
        unset($scannedThemes[0]);
        unset($scannedThemes[1]);
        foreach ($scannedThemes as $k => $scannedTheme) {
            if (is_dir($path . $scannedTheme)) {
                $theme = wp_get_theme($scannedTheme, $path);
                if ($theme->exists()) {
                    $themes[$path . $scannedTheme] = [
                        "name" => $theme->get("Name"),
                        "desc" => $theme->get("Description"),
                        "url" => plugins_url(WPDISCUZ_DIR_NAME . "/themes/$scannedTheme"),
                    ];
                }
            }
        }
        $uplDir = wp_upload_dir();
        $themesDir = str_replace("\\", "/", $uplDir["basedir"]) . wpdFormConst::THEMES_DIR;
        if (is_dir($themesDir)) {
            $scannedThemes = scandir($themesDir);
            unset($scannedThemes[0]);
            unset($scannedThemes[1]);
            foreach ($scannedThemes as $k => $scannedTheme) {
                if (is_dir($themesDir . $scannedTheme)) {
                    $theme = wp_get_theme($scannedTheme, $themesDir);
                    if ($theme->exists()) {
                        $themes[$themesDir . $scannedTheme] = [
                            "name" => $theme->get("Name"),
                            "desc" => $theme->get("Description"),
                            "url" => $uplDir["baseurl"] . wpdFormConst::THEMES_DIR . $scannedTheme,
                        ];
                    }
                }
            }
        }
        return $themes;
    }

    public function getDefaultTheme() {
        $path = str_replace("\\", "/", WPDISCUZ_DIR_PATH) . "/themes/";
        $scannedThemes = scandir($path);
        unset($scannedThemes[0]);
        unset($scannedThemes[1]);
        foreach ($scannedThemes as $k => $scannedTheme) {
            if (is_dir($path . $scannedTheme)) {
                $theme = wp_get_theme($scannedTheme, $path);
                if ($theme->exists()) {
                    return $path . $scannedTheme;
                }
            }
        }
    }

    public function getAllowedFieldsType() {
        return $this->row->allowedFieldsType();
    }

}
