<?php

if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzHelperUpload implements WpDiscuzConstants {

    private $options;
    private $dbManager;
    private $wpdiscuzForm;
    private $helper;
    private $imageSizes;
    private $wpUploadsPath;
    private $wpUploadsUrl;
    private $currentUser;
    private $requestUri;
    private $mimeTypes = [];

    public function __construct($options, $dbManager, $wpdiscuzForm, $helper) {
        $this->options = $options;
        $this->dbManager = $dbManager;
        $this->wpdiscuzForm = $wpdiscuzForm;
        $this->helper = $helper;
        $this->imageSizes = $this->getImageSizes();
        $wpUploadsDir = wp_upload_dir();
        $this->wpUploadsPath = $wpUploadsDir["path"];
        $this->wpUploadsUrl = $wpUploadsDir["url"];
        $this->requestUri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
        if ($this->options->content["wmuIsEnabled"]) {
            add_filter("wpdiscuz_editor_buttons_html", [&$this, "uploadButtons"], 1, 2);
            add_action("wpdiscuz_button_actions", [&$this, "uploadPreview"], 1, 2);

            add_filter("wpdiscuz_comment_list_args", [&$this, "commentListArgs"]);
            add_filter("comment_text", [&$this, "commentText"], 100, 3);
            add_filter("wpdiscuz_after_read_more", [&$this, "afterReadMore"], 100, 3);

            add_action("comment_post", [&$this, "addAttachments"]);
            add_filter("wpdiscuz_comment_post", [&$this, "postComment"], 10);
            add_filter("wpdiscuz_ajax_callbacks", [&$this, "wmuImageCallbacks"], 10);

            add_action("wp_ajax_wmuUploadFiles", [&$this, "uploadFiles"]);
            add_action("wp_ajax_nopriv_wmuUploadFiles", [&$this, "uploadFiles"]);

            add_action("wp_ajax_wmuRemoveAttachmentPreview", [&$this, "removeAttachmentPreview"]);
            add_action("wp_ajax_nopriv_wmuRemoveAttachmentPreview", [&$this, "removeAttachmentPreview"]);

            add_action("wp_ajax_wmuDeleteAttachment", [&$this, "deleteAttachment"]);
            add_action("wp_ajax_nopriv_wmuDeleteAttachment", [&$this, "deleteAttachment"]);

            add_action("delete_comment", [&$this, "deleteLinkedAttachments"], 20);
            add_action("delete_attachment", [&$this, "deleteAttachmentIdFromMeta"], 20);

            add_filter("wpdiscuz_privacy_personal_data_export", [&$this, "exportPersonalData"], 10, 2);
            add_filter("wpdiscuz_do_export_personal_data", "__return_true");
        }
    }

    public function uploadButtons($html, $uniqueId) {
        if ($this->isUploadingAllowed()) {
            $type = apply_filters("wpdiscuz_mu_upload_type", "");
            $faIcon = apply_filters("wpdiscuz_mu_upload_icon", "far fa-image");
            $allowedExts = apply_filters("wpdiscuz_mu_allowed_extensions", "accept='image/*'");
            $html .= "<span class='wmu-upload-wrap' wpd-tooltip='" . esc_attr($this->options->phrases["wmuAttachImage"]) . "' wpd-tooltip-position='" . (!is_rtl() ? 'left' : 'right' ) . "'>";
            $html .= "<label class='wmu-add'>";
            $html .= "<i class='$faIcon'></i>";
            $html .= "<input style='display:none;' class='wmu-add-files' type='file' name='" . self::INPUT_NAME . "[]' $type $allowedExts/>";
            $html .= "</label>";
            $html .= "</span>";
            $html .= "<div class='wpd-clear'></div>";
        }
        return $html;
    }

    public function uploadPreview($uniqueId, $currentUser) {
        if ($this->isUploadingAllowed()) {
            $html = "<div class='wmu-action-wrap'>";
            $html .= "<div class='wmu-tabs wmu-" . self::KEY_IMAGES . "-tab wmu-hide'></div>";
            $html .= apply_filters("wpdiscuz_mu_tabs", "");
            $html .= "</div>";
            echo $html;
        }
    }

    public function commentText($content, $comment) {
        if ($comment && strpos($this->requestUri, self::PAGE_COMMENTS) !== false && $this->options->content["wmuIsShowFilesDashboard"]) {
            $content = $this->getAttachments($content, $comment);
        }
        return $content;
    }

    public function afterReadMore($content, $comment) {
        return $this->getAttachments($content, $comment);
    }

    private function getAttachments($content, $comment) {
        $attachments = get_comment_meta($comment->comment_ID, self::METAKEY_ATTACHMENTS, true);
        if ($attachments && is_array($attachments)) {
            // get files from jetpack CDN on ajax calls
            add_filter("jetpack_photon_admin_allow_image_downsize", "__return_true");
            $content .= "<div class='wmu-comment-attachments'>";
            foreach ($attachments as $key => $ids) {
                if (!empty($ids)) {
                    $attachIds = array_map("intval", $ids);
                    $type = (count($attachIds) > 1) ? "multi" : "single";
                    if ($key == self::KEY_IMAGES) {
                        $imgHtml = $this->getAttachedImages($attachIds, $this->currentUser);
                        $content .= "<div class='wmu-attached-images wmu-count-" . $type . "'>" . $imgHtml . "</div>";
                    }
                    $content .= apply_filters("wpdiscuz_mu_get_attachments", "", $attachIds, $this->currentUser, $key);
                }
            }
            $content .= "</div>";
        }
        return $content;
    }

    public function getAttachedImages($attachIds, $currentUser = null, $size = "full", $lazyLoad = true) {
        global $pagenow;
        $images = "";
        if ($attachIds) {
            $attachments = get_posts(["include" => $attachIds, "post_type" => "attachment", "orderby" => "ID", "order" => "asc"]);
            if ($attachments && is_array($attachments)) {
                $style = "";
                if ($pagenow == self::PAGE_COMMENTS) {
                    $style .= "max-height:100px;";
                    $style .= "width:auto;";
                    $height = "";
                    $width = "";
                    $secondarySizeKey = "";
                    $secondarySize = "";
                } else {
                    if (count($attachments) > 1) {
                        $whData = apply_filters("wpdiscuz_mu_image_sizes", ["width" => 90, "height" => 90]);
                        $width = $whData["width"];
                        $height = $whData["height"];
                    } else {
                        $width = $this->options->content["wmuSingleImageWidth"];
                        $height = $this->options->content["wmuSingleImageHeight"];
                    }

                    if (intval($width)) {
                        $primarySizeKey = "width";
                        $primarySize = $width;
                        $secondarySizeKey = "height";
                        $secondarySize = $height;
                    } else {
                        $primarySizeKey = "height";
                        $primarySize = $height;
                        $secondarySizeKey = "width";
                        $secondarySize = $width;
                    }

                    $style .= "max-$primarySizeKey:{$primarySize}px;";
                    $style .= "$primarySizeKey:{$primarySize}px;";
                    $style .= "$secondarySizeKey:auto;";
                }

                if ($pagenow == self::PAGE_COMMENTS) {
                    $size = "thumbnail";
                } else {
                    foreach ($this->imageSizes as $sizeKey => $sizeValue) {
                        if (!intval($sizeValue["height"]) && !intval($sizeValue["width"])) {
                            continue;
                        }

                        if ($sizeValue[$primarySizeKey] > 0 && $primarySize <= $sizeValue[$primarySizeKey]) {
                            $size = $sizeKey;
                            break;
                        } else {
                            $size = "full";
                        }
                    }
                }

                $lightboxCls = $this->options->content["wmuIsLightbox"] ? "wmu-lightbox" : "";
                $wmuLazyLoadImages = apply_filters("wpdiscuz_mu_lazyload_images", "");

                foreach ($attachments as $attachment) {
                    $deleteHtml = $this->getDeleteHtml($currentUser, $attachment, "image");
                    $url = wp_get_attachment_image_url($attachment->ID, "full");
                    $srcData = wp_get_attachment_image_src($attachment->ID, $size);
                    $src = $srcData[0];
                    if ($wmuLazyLoadImages && $lazyLoad) {
                        $srcValue = "data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                        $dataSrcValue = $src;
                    } else {
                        $srcValue = $src;
                        $dataSrcValue = "";
                    }

                    $images .= "<div class='wmu-attachment wmu-attachment-$attachment->ID'>";
                    if ($lightboxCls) {
                        $images .= "<a href='$url' class='wmu-attached-image-link $lightboxCls'>";
                        $images .= "<img style='$style' alt='" . get_post_meta($attachment->ID, "_wp_attachment_image_alt", true) . "' title='" . esc_attr($attachment->post_excerpt) . "' id='wmu-attachemnt-$attachment->ID' class='attachment-$size size-$size wmu-attached-image' src='$srcValue' wmu-data-src='$dataSrcValue' $secondarySizeKey='$secondarySize' />";
                        $images .= "</a>";
                    } else {
                        $images .= apply_filters("wpdiscuz_mu_attached_image_before", "<a href='" . wp_get_attachment_image_url($attachment->ID) . "' class='wmu-attached-image-link' target='_blank' rel='noreferrer ugc'>", $attachment->ID);
                        $images .= "<img style='$style' alt='" . get_post_meta($attachment->ID, "_wp_attachment_image_alt", true) . "' title='" . esc_attr($attachment->post_excerpt) . "' id='wmu-attachemnt-$attachment->ID' class='attachment-$size size-$size wmu-attached-image' src='$srcValue' wmu-data-src='$dataSrcValue' $secondarySizeKey='$secondarySize' />";
                        $images .= apply_filters("wpdiscuz_mu_attached_image_after", "</a>", $attachment->ID);
                    }
                    $images .= $deleteHtml;
                    $images .= "</div>";
                }
            }
        }
        return $images;
    }

    public function addAttachments($cId) {
        if ($cId && !empty($_POST["wmu_attachments"])) {
            $wmuAttachments = json_decode(stripslashes($_POST["wmu_attachments"]), JSON_OBJECT_AS_ARRAY);
            if ($wmuAttachments && is_array($wmuAttachments)) {
                update_comment_meta($cId, self::METAKEY_ATTACHMENTS, $wmuAttachments);
                foreach ($wmuAttachments as $data) {
                    if ($data && is_array($data)) {
                        foreach ($data as $id) {
                            if (!empty($id)) {
                                update_post_meta($id, self::METAKEY_ATTCHMENT_COMMENT_ID, $cId);
                            }
                        }
                    }
                }
            }
        }
    }

    public function postComment($response) {
        $response["callbackFunctions"][] = "wmuHideAll";
        $response["callbackFunctions"][] = "wmuAddLightBox";
        return $response;
    }

    public function wmuImageCallbacks($response) {
        $response["callbackFunctions"][] = "wmuAddLightBox";
        return $response;
    }

    public function uploadFiles() {
        $nonceKey = ($key = get_home_url()) ? md5($key) : "wmu-nonce";
        check_ajax_referer($nonceKey, "wmu_nonce");
        $response = ["errorCode" => "", "error" => "", "errors" => [], "attachmentsHtml" => "", "previewsData" => ""];
        $postId = empty($_POST["postId"]) ? 0 : intval($_POST["postId"]);

        if (!$postId) {
            $response["errorCode"] = "msgPostIdNotExists";
            wp_send_json_error($response);
        }

        if (empty($_FILES[self::INPUT_NAME])) {
            $response["errorCode"] = "msgEmptyFile";
            wp_send_json_error($response);
        }


        $files = $this->combineArray($_FILES[self::INPUT_NAME]);
        $filesCount = count($files);
        $allowedCount = apply_filters("wpdiscuz_mu_file_count", 1);

        if ($filesCount > $allowedCount) {
            $response["errorCode"] = "wmuPhraseMaxFileCount";
            wp_send_json_error($response);
        }

        $post = get_post($postId);
        if (!$this->isUploadingAllowed($post)) {
            $response["errorCode"] = "msgUploadingNotAllowed";
            wp_send_json_error($response);
        }


        // all expected data are correct, continue uploading
        $attachmentIds = apply_filters("wpdiscuz_mu_attachment_ids", [self::KEY_IMAGES => []]);
        $attachmentsData = apply_filters("wpdiscuz_mu_attachments_data", [self::KEY_IMAGES => []]);

        $wmuAttachmentsData = empty($_POST["wmuAttachmentsData"]) ? "" : json_decode(stripslashes($_POST["wmuAttachmentsData"]), JSON_OBJECT_AS_ARRAY);

        if ($wmuAttachmentsData && is_array($wmuAttachmentsData)) {
            if ($allowedCount == 1) {
                foreach ($wmuAttachmentsData as $key => $value) {
                    if ($value && is_array($value)) {
                        foreach ($value as $v) {
                            wp_delete_attachment($v["id"], true);
                        }
                    }
                }
            } else {
                foreach ($wmuAttachmentsData as $key => $value) {
                    if ($value && is_array($value)) {
                        $filesCount += count($value);
                        foreach ($value as $v) {
                            $attachmentIds[$key][] = $v["id"];
                            $attachmentsData[$key][] = $v;
                        }
                    }
                }
            }
        }

        if ($filesCount > $allowedCount) {
            $response["errorCode"] = "wmuPhraseMaxFileCount";
            wp_send_json_error($response);
        }

        $postSize = empty($_SERVER["CONTENT_LENGTH"]) ? 0 : intval($_SERVER["CONTENT_LENGTH"]);
        if ($postSize && $postSize > $this->options->wmuPostMaxSize) {
            $response["errorCode"] = "wmuPhrasePostMaxSize";
            wp_send_json_error($response);
        }

        $size = 0;
        foreach ($files as $file) {
            $size += empty($file["size"]) ? 0 : intval($file["size"]);
        }
        if ($size > ($this->options->content["wmuMaxFileSize"] * 1024 * 1024)) {
            $response["errorCode"] = "wmuPhraseMaxFileSize";
            wp_send_json_error($response);
        }

        require_once(ABSPATH . "wp-admin/includes/image.php");

        foreach ($files as $file) {
            $error = false;
            $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
            if ($mimeType = $this->isImage($file)) {
                if ((strpos($mimeType, "image/") !== false) && empty($extension)) {
                    $file["name"] .= ".jpg";
                }
            } else {
                $mimeType = $this->getMimeType($file, $extension);
            }

            if ($this->isAllowedFileType($mimeType)) {
                if (empty($extension)) {
                    if (strpos($mimeType, "image/") === false) {
                        foreach ($this->mimeTypes as $ext => $mimes) {
                            if (in_array($mimeType, explode("|", $mimes))) {
                                $file["name"] .= "." . $ext;
                            }
                        }
                    }
                }
                $file["type"] = $mimeType;
            } else {
                $error = true;
                $response["errors"][] = $file["name"] . " " . (current_user_can("manage_options") ? "(mimetype - " . $mimeType . ") " : "") . "- " . $this->options->phrases["wmuPhraseNotAllowedFile"];
            }

            do_action("wpdiscuz_mu_preupload", $file);

            if (!$error) {
                $attachmentData = $this->uploadSingleFile($file);
                if ($attachmentData) {
                    if (strpos($file["type"], "image/") !== false) {
                        $attachmentIds[self::KEY_IMAGES][] = $attachmentData["id"];
                        $attachmentsData[self::KEY_IMAGES][] = $attachmentData;
                    } else {
                        $attachmentIds = apply_filters("wpdiscuz_mu_add_attachment_ids", $attachmentIds, $attachmentData, $file);
                        $attachmentsData = apply_filters("wpdiscuz_mu_add_attachments_data", $attachmentsData, $attachmentData, $file);
                    }
                }
            }
        }

        if ($attachmentIds) {
            $response["attachmentsHtml"] = "<div class='wmu-attached-data-info wmu-hide'>";
            $response["attachmentsHtml"] .= "<input class='wmu-attachments-ids' type='hidden' name='wmu_attachments' value='" . esc_attr(json_encode($attachmentIds)) . "'/>";
            $response["attachmentsHtml"] .= "<input class='wmu-attachments-data' type='hidden' value='" . esc_attr(json_encode($attachmentsData)) . "'/>";
            $response["attachmentsHtml"] .= "</div>";
            $response["previewsData"] = $attachmentsData;
            if ($allowedCount == 1) {
                $response["tooltip"] = $this->options->phrases["wmuChangeImage"];
            }
        }


        wp_send_json_success($response);
    }

    private function isAllowedFileType($mimeType) {
        $isAllowed = false;
        if (!empty($this->mimeTypes) && is_array($this->mimeTypes)) {
            foreach ($this->mimeTypes as $ext => $mimes) {
                $isAllowed = in_array($mimeType, explode("|", $mimes));
                if ($isAllowed) {
                    break;
                }
            }
        }
        return $isAllowed;
    }

    private function getMimeType($file, $extension) {
        $mimeType = "";
        if (function_exists("finfo_open") && function_exists("finfo_file")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file["tmp_name"]);
        } elseif (function_exists("mime_content_type")) {
            $mimeType = mime_content_type($file["tmp_name"]);
        } elseif ($extension) {
            foreach ($this->mimeTypes as $ext => $mimeTypes) {
                $exp = explode("|", $mimeTypes);
                if (in_array($extension, $exp)) {
                    $mimeType = $exp[0];
                }
            }
        }
        return $mimeType;
    }

    public function removeAttachmentPreview() {
        $response = ["errorCode" => "", "error" => "", "attachmentsHtml" => ""];
        $attachmentId = isset($_POST["attachmentId"]) ? intval($_POST["attachmentId"]) : 0;
        $attachment = get_post($attachmentId);

        // add attachment not exists message in wpdoptions > jsargs
        if (!$attachment) {
            $response["errorCode"] = "wmuMsgAttachmentNotExists";
            wp_send_json_error($response);
        }

        $ip = WpdiscuzHelper::getRealIPAddr();
        $ownerIp = get_post_meta($attachmentId, self::METAKEY_ATTCHMENT_OWNER_IP, true);
        if (!current_user_can("manage_options") && $ownerIp != $ip) {
            $response["errorCode"] = "msgPermissionDenied";
            wp_send_json_error($response);
        }

        $filesCount = 0;
        $attachmentIds = apply_filters("wpdiscuz_mu_attachment_ids", [self::KEY_IMAGES => []]);
        $attachmentsData = apply_filters("wpdiscuz_mu_attachments_data", [self::KEY_IMAGES => []]);
        wp_delete_attachment($attachmentId, true);

        $wmuAttachmentsData = empty($_POST["wmuAttachmentsData"]) ? "" : json_decode(stripslashes($_POST["wmuAttachmentsData"]), JSON_OBJECT_AS_ARRAY);

        if ($wmuAttachmentsData && is_array($wmuAttachmentsData)) {
            foreach ($wmuAttachmentsData as $key => $value) {
                if ($value && is_array($value)) {
                    foreach ($value as $v) {
                        if ($v["id"] != $attachmentId) {
                            $attachmentIds[$key][] = $v["id"];
                            $attachmentsData[$key][] = $v;
                            $filesCount++;
                        }
                    }
                }
            }
        }

        if ($filesCount) {
            $response["attachmentsHtml"] = "<div class='wmu-attached-data-info wmu-hide'>";
            $response["attachmentsHtml"] .= "<input class='wmu-attachments-ids' type='hidden' name='wmu_attachments' value='" . esc_attr(json_encode($attachmentIds)) . "'/>";
            $response["attachmentsHtml"] .= "<input class='wmu-attachments-data' type='hidden' value='" . esc_attr(json_encode($attachmentsData)) . "'/>";
            $response["attachmentsHtml"] .= "</div>";
        } else {
            $response["tooltip"] = $this->options->phrases["wmuAttachImage"];
        }
        wp_send_json_success($response);
    }

    public function deleteAttachment() {
        $response = ["errorCode" => "", "error" => ""];
        $attachmentId = isset($_POST["attachmentId"]) ? intval($_POST["attachmentId"]) : 0;
        $attachment = get_post($attachmentId);
        $commentId = get_post_meta($attachmentId, self::METAKEY_ATTCHMENT_COMMENT_ID, true);
        $comment = get_comment($commentId);
        if ($attachment && $comment) {
            if (empty($this->currentUser->ID)) {
                $this->setCurrentUser(WpdiscuzHelper::getCurrentUser());
            }
            $args = [];
            if (isset($this->currentUser->user_email)) {
                $args["comment_author_email"] = $this->currentUser->user_email;
            }
            if (current_user_can("moderate_comments") || ($this->helper->isCommentEditable($comment) && $this->helper->canUserEditComment($comment, $this->currentUser, $args))) {
                wp_delete_attachment($attachmentId, true);
                wp_send_json_success($response);
            }
        } else {
            $response["error"] = esc_html__("The attachment not exists", "wpdiscuz");
            wp_send_json_error($response);
        }
    }

    public function isUploadingAllowed($postObj = null) {
        global $post;
        $gPost = $postObj ? $postObj : $post;
        $isAllowed = false;
        $this->mimeTypes = apply_filters("wpdiscuz_mu_mime_types", $this->options->content["wmuMimeTypes"]);
        if ($this->isAllowedPostType($gPost) && !empty($this->mimeTypes)) {
            $currentUser = WpdiscuzHelper::getCurrentUser();
            $isUserLoggedIn = !empty($currentUser->ID);
            $isGuestAllowed = !$isUserLoggedIn && $this->options->content["wmuIsGuestAllowed"];
            $isUserAllowed = $isUserLoggedIn && $this->canUserUpload($currentUser);
            if ($isGuestAllowed || $isUserAllowed) {
                $isAllowed = true;
            }
        }
        return $isAllowed;
    }

    public function isAllowedPostType($post) {
        $allowedPosttypes = apply_filters("wpdiscuz_mu_allowed_posttypes", $this->getDefaultPostTypes());
        return ($post && is_object($post) && isset($post->post_type) && in_array($post->post_type, $allowedPosttypes));
    }

    public function canUserUpload($currentUser) {
        $bool = false;
        if ($currentUser && $currentUser->ID) {
            $userRoles = $currentUser->roles;
            foreach ($userRoles as $role) {
                $allowedRoles = apply_filters("wpdiscuz_mu_allowed_roles", $this->getDefaultRoles());
                if (in_array($role, $allowedRoles)) {
                    $bool = true;
                    break;
                }
            }
        }
        return $bool;
    }

    private function uploadSingleFile($file) {
        $currentTime = WpdiscuzHelper::getMicrotime();
        $attachmentData = [];
        $path = $this->wpUploadsPath . "/";
        $fName = $file["name"];
        $pathInfo = pathinfo($fName);
        $realFileName = $pathInfo["filename"];
        $ext = empty($pathInfo["extension"]) ? "" : strtolower($pathInfo["extension"]);
        $sanitizedName = sanitize_file_name($realFileName);
        $cleanFileName = $sanitizedName . "-" . $currentTime . "." . $ext;
        $cleanRealFileName = $sanitizedName . "." . $ext;
        $fileName = $path . $cleanFileName;

        if (in_array($ext, ["jpeg", "jpg"])) {
            $this->imageFixOrientation($file["tmp_name"]);
        }

        $success = apply_filters("wpdiscuz_mu_compress_image", false, $file["tmp_name"], $fileName, $q = 60);
        if ($success || @move_uploaded_file($file["tmp_name"], $fileName)) {
            $postParent = apply_filters("wpdiscuz_mu_attachment_parent", 0);
            $attachment = [
                "guid" => $this->wpUploadsUrl . "/" . $cleanFileName,
                "post_mime_type" => $file["type"],
                "post_title" => preg_replace("#\.[^.]+$#", "", wp_slash($fName)),
                "post_excerpt" => wp_slash($fName),
                "post_content" => "",
                "post_status" => "inherit",
                "post_parent" => $postParent
            ];

            if ($attachId = wp_insert_attachment($attachment, $fileName)) {
                add_filter("intermediate_image_sizes", [&$this, "getImagesSizes"]);
                $attachData = wp_generate_attachment_metadata($attachId, $fileName);
                wp_update_attachment_metadata($attachId, $attachData);
                update_post_meta($attachId, "_wp_attachment_image_alt", $fName);
                $ip = WpdiscuzHelper::getRealIPAddr();
                update_post_meta($attachId, self::METAKEY_ATTCHMENT_OWNER_IP, $ip);
                update_post_meta($attachId, self::METAKEY_ATTCHMENT_COMMENT_ID, 0);
                $attachmentData["id"] = $attachId;
                $attachmentData["url"] = empty($attachData["sizes"]["thumbnail"]["file"]) ? $this->wpUploadsUrl . "/" . $cleanFileName : $this->wpUploadsUrl . "/" . $attachData["sizes"]["thumbnail"]["file"];
                $attachmentData["fullname"] = $cleanRealFileName;
                $attachmentData["shortname"] = $this->getFileName($cleanRealFileName);
            }
        }
        return $attachmentData;
    }

    private function getImageSizes() {
        $sizes = [];
        foreach ($this->options->content["wmuImageSizes"] as $_size) {
            $sizes[$_size]["width"] = get_option("{$_size}_size_w");
            $sizes[$_size]["height"] = get_option("{$_size}_size_h");
        }
        return $sizes;
    }

    public function getImagesSizes() {
        $sizes = $this->options->content["wmuImageSizes"];
        if ($sizes && is_array($sizes) && !in_array("full", $sizes)) {
            $sizes[] = "full";
        }

        if (!$sizes) {
            $sizes = ["full"];
        }
        return $sizes;
    }

    private function combineArray($array) {
        $combinedArray = [];
        foreach ($array as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $combinedArray[$k1][$k] = $v1;
            }
        }
        return $combinedArray;
    }

    private function imageFixOrientation($filename) {
        $isFunctionsExists = function_exists("exif_read_data") && function_exists("imagecreatefromjpeg") && function_exists("imagerotate") && function_exists("imagejpeg");
        if ($isFunctionsExists) {
            $exif = @exif_read_data($filename);
            if (!empty($exif["Orientation"])) {
                $image = imagecreatefromjpeg($filename);
                switch ($exif["Orientation"]) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, -90, 0);
                        break;
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        break;
                }
                imagejpeg($image, $filename, 90);
            }
        }
    }

    public function getFileName($attachment) {
        $name = false;
        if ($attachment) {
            if (is_object($attachment) && (isset($attachment->post_excerpt) || isset($attachment->post_title))) {
                $name = $attachment->post_excerpt ? $attachment->post_excerpt : $attachment->post_title;
            } else {
                $name = $attachment;
            }
            if (strlen($name) > 40) {
                $name = function_exists("mb_substr") ? mb_substr($name, -40, 40, "UTF-8") : substr($name, -40, 40);
                $name = "..." . $name;
            }
            $name = ucfirst(str_replace(["-", "_"], " ", $name));
        }
        return $name;
    }

    public function deleteLinkedAttachments($commentId) {
        if ($commentId) {
            $metaData = get_comment_meta($commentId, self::METAKEY_ATTACHMENTS, true);
            if ($metaData && is_array($metaData)) {
                foreach ($metaData as $key => $attachments) {
                    if ($attachments && is_array($attachments)) {
                        foreach ($attachments as $attachment) {
                            wp_delete_attachment($attachment);
                        }
                    }
                }
            }
        }
    }

    public function deleteAttachmentIdFromMeta($postId) {
        $commentId = get_post_meta($postId, self::METAKEY_ATTCHMENT_COMMENT_ID, true);
        if ($commentId) {
            $attachments = get_comment_meta($commentId, self::METAKEY_ATTACHMENTS, true);
            if ($attachments && is_array($attachments)) {
                $tmpData = [];
                foreach ($attachments as $key => $value) {
                    $index = array_search($postId, $value);
                    if ($index !== false) {
                        unset($value[$index]);
                        $tmpData[$key] = array_values($value);
                    } else {
                        $tmpData[$key] = $value;
                    }
                }

                if (self::hasAttachments($tmpData)) {
                    update_comment_meta($commentId, self::METAKEY_ATTACHMENTS, $tmpData);
                } else {
                    delete_comment_meta($commentId, self::METAKEY_ATTACHMENTS);
                }
            }
        }
    }

    public static function hasAttachments($attachments) {
        $hasItems = false;
        if ($attachments && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (is_array($attachment) && count($attachment)) {
                    $hasItems = true;
                    break;
                }
            }
        }
        return $hasItems;
    }

    public static function canEditAttachments($currentUser, $attachment) {
        return current_user_can("delete_others_posts") || (!empty($currentUser->ID) && $currentUser->ID == $attachment->post_author) || (WpdiscuzHelper::getRealIPAddr() == get_post_meta($attachment->ID, self::METAKEY_ATTCHMENT_OWNER_IP, true));
    }

    private function getDeleteHtml($currentUser, $attachment, $type) {
        $args = [];
        if (isset($this->currentUser->user_email)) {
            $args["comment_author_email"] = $this->currentUser->user_email;
        }
        $commentId = get_post_meta($attachment->ID, self::METAKEY_ATTCHMENT_COMMENT_ID, true);
        $comment = get_comment($commentId);
        $deleteHtml = "<div class='wmu-attachment-delete wmu-delete-$type' title='" . esc_html__("Delete", "wpdiscuz") . "' data-wmu-attachment='$attachment->ID'>&nbsp;</div>";
        return current_user_can("moderate_comments") || ($this->helper->isCommentEditable($comment) && $this->helper->canUserEditComment($comment, $currentUser, $args)) ? $deleteHtml : "<div class='wmu-separator'></div>";
    }

    public function commentListArgs($args) {
        if (empty($args["current_user"])) {
            $this->currentUser = WpdiscuzHelper::getCurrentUser();
        } else {
            $this->currentUser = $args["current_user"];
        }
        return $args;
    }

    public function setCurrentUser($currentUser) {
        $this->currentUser = $currentUser;
    }

    private function getDefaultPostTypes() {
        return ["post", "page", "attachment"];
    }

    private function getDefaultRoles() {
        return ["administrator", "editor", "author", "contributor", "subscriber"];
    }

    public function isImage($file) {
        return wp_get_image_mime($file["tmp_name"]);
    }

    /**
     * DEPRECATED due to some secuirty issues
     */
    public function getMimeTypeFromContent($path) {
        $fileContent = $path && function_exists("file_get_contents") && ($v = file_get_contents($path)) ? $v : "";
        if ($fileContent && preg_match('/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(?:\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/', $fileContent, $hits)) {
            $type = [
                1 => "jpeg",
                2 => "gif",
                3 => "png",
                4 => "bmp",
                5 => "tiff",
                6 => "ilbm",
            ];
            return $type[count($hits) - 1];
        }
        return false;
    }

    public function exportPersonalData($data, $commentId) {
        $attachments = get_comment_meta($commentId, self::METAKEY_ATTACHMENTS, true);
        if ($attachments && is_array($attachments)) {
            $isWmuExists = apply_filters("wpdiscuz_mu_exists", false);
            foreach ($attachments as $key => $attachIds) {
                if (empty($attachIds)) {
                    continue;
                }

                foreach ($attachIds as $attachId) {
                    if (intval($attachId)) {
                        if ($key === self::KEY_IMAGES) {
                            $data[] = ["name" => esc_html__("Attached Images", "wpdiscuz"), "value" => wp_get_attachment_url($attachId)];
                        } else if ($isWmuExists) {
                            $data = apply_filters("wpdiscuz_mu_export_data", $data, $key, $attachId);
                        }
                    }
                }
            }
        }
        return $data;
    }

}
