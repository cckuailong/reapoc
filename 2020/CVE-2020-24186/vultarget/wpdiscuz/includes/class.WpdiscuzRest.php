<?php

class WpdiscuzRest extends WP_REST_Controller {

    private $dbManager;
    private $options;
    private $helper;
    private $wpdiscuzForm;
    
    public function __construct($dbManager, $options, $helper, $wpdiscuzForm) {
        $this->dbManager = $dbManager;
        $this->options = $options;
        $this->helper = $helper;
        $this->wpdiscuzForm = $wpdiscuzForm;
        $this->namespace = "wpdiscuz/v1";
        $this->resource_name = "update";
    }

    public function registerRoutes() {
        register_rest_route($this->namespace, "/" . $this->resource_name, [
            [
                "methods" => "GET",
                "callback" => [&$this, "checkNewComments"],
                "args" => [
                    "postId" => [
                        "required" => true,
                        "type" => "number",
                    ],
                    "lastId" => [
                        "required" => true,
                        "type" => "number",
                    ],
                    "visibleCommentIds" => [
                        "required" => true,
                        "type" => "string",
                    ],
                ],
            ],
        ]);
    }

    public function checkNewComments($data) {
        $params = $data->get_params();
        $response = ["ids" => []];
        $status = current_user_can("moderate_comments") ? "all" : "approved";
        $args = ["status" => $status, "post_id" => $params["postId"]];
        $commentId = $this->dbManager->getLastCommentId($args);
        if ($commentId > $params["lastId"]) {
            $currentUser = WpdiscuzHelper::getCurrentUser();
            $sentEmail = !empty($_COOKIE["comment_author_email_" . COOKIEHASH]) ? trim($_COOKIE["comment_author_email_" . COOKIEHASH]) : "";
            $email = !empty($currentUser->ID) ? $currentUser->user_email : $sentEmail;
            $newCommentIds = $this->dbManager->getNewCommentIds($args, $params["lastId"], $email, $params["visibleCommentIds"]);
            $newCommentIds = apply_filters("wpdiscuz_bubble_new_comment_ids", $newCommentIds, $params["postId"], $currentUser);
            if (!empty($newCommentIds)) {
                $response["ids"] = $newCommentIds;
                if ($this->options->live["bubbleShowNewCommentMessage"]) {
                    $comment = get_comment($commentId);
                    $comment->comment_content = strip_tags($comment->comment_content);
                    if (stripos($comment->comment_content, "[/spoiler]") === false) {
                        $user = "";
                        if ($comment->user_id) {
                            $user = get_user_by("id", $comment->user_id);
                        } else if ($this->options->login["isUserByEmail"]) {
                            $user = get_user_by("email", $comment->comment_author_email);
                        }
                        if ($user) {
                            $authorName = $user->display_name ? $user->display_name : $comment->comment_author;
                            $authorAvatarField = $user->ID;
                            $gravatarUserId = $user->ID;
                            $gravatarUserEmail = $user->user_email;
                        } else {
                            $authorName = $comment->comment_author ? $comment->comment_author : esc_html($this->options->phrases["wc_anonymous"]);
                            $authorAvatarField = $comment->comment_author_email;
                            $gravatarUserId = 0;
                            $gravatarUserEmail = $comment->comment_author_email;
                        }
                        $gravatarArgs = [
                            "wpdiscuz_gravatar_field" => $authorAvatarField,
                            "wpdiscuz_gravatar_size" => apply_filters("wpdiscuz_gravatar_size", 16),
                            "wpdiscuz_gravatar_user_id" => $gravatarUserId,
                            "wpdiscuz_gravatar_user_email" => $gravatarUserEmail,
                            "wpdiscuz_current_user" => $user,
                        ];
                        if (function_exists("mb_substr")) {
                            $response["commentText"] = mb_substr($comment->comment_content, 0, 50);
                        } else {
                            $response["commentText"] = substr($comment->comment_content, 0, 50);
                        }
                        if (strlen($comment->comment_content) > strlen($response["commentText"])) {
                            $response["commentText"] .= "...";
                        }
                        $response["commentDate"] = esc_html($this->helper->dateDiff($comment->comment_date_gmt));
                        $response["commentLink"] = esc_url_raw(get_comment_link($comment));
                        $response["authorName"] = esc_html($authorName);
                        $response["avatar"] = get_avatar($gravatarArgs["wpdiscuz_gravatar_field"], $gravatarArgs["wpdiscuz_gravatar_size"], "", $authorName, $gravatarArgs);
                    }
                }
                $form = $this->wpdiscuzForm->getForm($params["postId"]);
                $response["all_comments_count"] = esc_html(get_comments_number($params["postId"]));
                $response["all_comments_count_html"] = "<span class='wpdtc'>" . esc_html($response["all_comments_count"]) . "</span> " . esc_html(1 == $response["all_comments_count"] ? $form->getHeaderTextSingle() : $form->getHeaderTextPlural());
            }
        }
        return $response;
    }

}
