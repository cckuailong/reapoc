<?php

//mimic the actuall admin-ajax
define("DOING_AJAX", true);
$wpdiscuz_ajax_action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
if (!$wpdiscuz_ajax_action) {
    die('-1');
}
$ABSPATH = wpdiscuz_ABSPATH();
require_once($ABSPATH . "wp-load.php");

header("Content-Type: text/html");
send_nosniff_header();

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$wpdiscuz = wpDiscuz();
$wpdiscuz_ajax_action = esc_attr(trim($wpdiscuz_ajax_action));
$allowedActions = [
    "wpdLoadMoreComments",
    "wpdVoteOnComment",
    "wpdSorting",
    "wpdAddComment",
    "wpdGetSingleComment",
    "wpdCheckNotificationType",
    "wpdRedirect",
    "wpdEditComment",
    "wpdSaveEditedComment",
    "wpdUpdateAutomatically",
    "wpdReadMore",
    "wpdShowReplies",
    "wpdMostReactedComment",
    "wpdHottestThread",
    "wpdGetInfo",
    "wpdGetActivityPage",
    "wpdGetSubscriptionsPage",
    "wpdGetFollowsPage",
    "wpdDeleteComment",
    "wpdCancelSubscription",
    "wpdCancelFollow",
    "wpdEmailDeleteLinks",
    "wpdGuestAction",
    "wpdStickComment",
    "wpdCloseThread",
    "wpdFollowUser",
    "wpdBubbleUpdate",
    "wpdAddInlineComment",
    "wpdGetLastInlineComments",
    "wpdGetInlineCommentForm",
    "wpdAddSubscription",
    "wpdUnsubscribe",
    "wpdUserRate",
];

// Load more comments
add_action("wpdiscuz_wpdLoadMoreComments", [$wpdiscuz, "loadMoreComments"]);
add_action("wpdiscuz_nopriv_wpdLoadMoreComments", [$wpdiscuz, "loadMoreComments"]);
// Vote on comments
add_action("wpdiscuz_wpdVoteOnComment", [$wpdiscuz->helperAjax, "voteOnComment"]);
add_action("wpdiscuz_nopriv_wpdVoteOnComment", [$wpdiscuz->helperAjax, "voteOnComment"]);
// Sorting comments
add_action("wpdiscuz_wpdSorting", [$wpdiscuz, "sorting"]);
add_action("wpdiscuz_nopriv_wpdSorting", [$wpdiscuz, "sorting"]);
// Adding comment
add_action("wpdiscuz_wpdAddComment", [$wpdiscuz, "addComment"]);
add_action("wpdiscuz_nopriv_wpdAddComment", [$wpdiscuz, "addComment"]);
// Get single comment
add_action("wpdiscuz_wpdGetSingleComment", [$wpdiscuz, "getSingleComment"]);
add_action("wpdiscuz_nopriv_wpdGetSingleComment", [$wpdiscuz, "getSingleComment"]);
// Get single comment
add_action("wpdiscuz_wpdCheckNotificationType", [$wpdiscuz->helperEmail, "checkNotificationType"]);
add_action("wpdiscuz_nopriv_wpdCheckNotificationType", [$wpdiscuz->helperEmail, "checkNotificationType"]);
// Redirect first commenter
add_action("wpdiscuz_wpdRedirect", [$wpdiscuz->helperAjax, "redirect"]);
add_action("wpdiscuz_nopriv_wpdRedirect", [$wpdiscuz->helperAjax, "redirect"]);
// Edit comment
add_action("wpdiscuz_wpdEditComment", [$wpdiscuz->helperAjax, "editComment"]);
add_action("wpdiscuz_nopriv_wpdEditComment", [$wpdiscuz->helperAjax, "editComment"]);
// Save edited comment
add_action("wpdiscuz_wpdSaveEditedComment", [$wpdiscuz, "saveEditedComment"]);
add_action("wpdiscuz_nopriv_wpdSaveEditedComment", [$wpdiscuz, "saveEditedComment"]);
// Update comment list automatically
add_action("wpdiscuz_wpdUpdateAutomatically", [$wpdiscuz, "updateAutomatically"]);
add_action("wpdiscuz_nopriv_wpdUpdateAutomatically", [$wpdiscuz, "updateAutomatically"]);
// Read more comment
add_action("wpdiscuz_wpdReadMore", [$wpdiscuz->helperAjax, "readMore"]);
add_action("wpdiscuz_nopriv_wpdReadMore", [$wpdiscuz->helperAjax, "readMore"]);
// Show Comment Replies
add_action("wpdiscuz_wpdShowReplies", [$wpdiscuz, "showReplies"]);
add_action("wpdiscuz_nopriv_wpdShowReplies", [$wpdiscuz, "showReplies"]);
// Most Reacted Comment
add_action("wpdiscuz_wpdMostReactedComment", [$wpdiscuz, "mostReactedComment"]);
add_action("wpdiscuz_nopriv_wpdMostReactedComment", [$wpdiscuz, "mostReactedComment"]);
// Hottest Comment Thread
add_action("wpdiscuz_wpdHottestThread", [$wpdiscuz, "hottestThread"]);
add_action("wpdiscuz_nopriv_wpdHottestThread", [$wpdiscuz, "hottestThread"]);
// Get user content info
add_action("wpdiscuz_wpdGetInfo", [$wpdiscuz->helper, "wpdGetInfo"]);
add_action("wpdiscuz_nopriv_wpdGetInfo", [$wpdiscuz->helper, "wpdGetInfo"]);
// Get user activity page item
add_action("wpdiscuz_wpdGetActivityPage", [$wpdiscuz->helper, "getActivityPage"]);
add_action("wpdiscuz_nopriv_wpdGetActivityPage", [$wpdiscuz->helper, "getActivityPage"]);
// Get user subscription page item
add_action("wpdiscuz_wpdGetSubscriptionsPage", [$wpdiscuz->helper, "getSubscriptionsPage"]);
add_action("wpdiscuz_nopriv_wpdGetSubscriptionsPage", [$wpdiscuz->helper, "getSubscriptionsPage"]);
// Get user follow page item
add_action("wpdiscuz_wpdGetFollowsPage", [$wpdiscuz->helper, "getFollowsPage"]);
add_action("wpdiscuz_nopriv_wpdGetFollowsPage", [$wpdiscuz->helper, "getFollowsPage"]);
// Delete users" comment
add_action("wpdiscuz_wpdDeleteComment", [$wpdiscuz->helperAjax, "deleteComment"]);
add_action("wpdiscuz_nopriv_wpdDeleteComment", [$wpdiscuz->helperAjax, "deleteComment"]);
// Delete users" subscription
add_action("wpdiscuz_wpdCancelSubscription", [$wpdiscuz->helperAjax, "deleteSubscription"]);
add_action("wpdiscuz_nopriv_wpdCancelSubscription", [$wpdiscuz->helperAjax, "deleteSubscription"]);
// Delete users" follow
add_action("wpdiscuz_wpdCancelFollow", [$wpdiscuz->helperAjax, "deleteFollow"]);
add_action("wpdiscuz_nopriv_wpdCancelFollow", [$wpdiscuz->helperAjax, "deleteFollow"]);
// Email to user the delete links
add_action("wpdiscuz_wpdEmailDeleteLinks", [$wpdiscuz->helperAjax, "emailDeleteLinks"]);
// Guest action
add_action("wpdiscuz_nopriv_wpdGuestAction", [$wpdiscuz->helperAjax, "guestAction"]);
// Stick comment
add_action("wpdiscuz_wpdStickComment", [$wpdiscuz->helperAjax, "stickComment"]);
// Close comment
add_action("wpdiscuz_wpdCloseThread", [$wpdiscuz->helperAjax, "closeThread"]);
// Follow user
add_action("wpdiscuz_wpdFollowUser", [$wpdiscuz->helperAjax, "followUser"]);
// Bubble Update
add_action("wpdiscuz_wpdBubbleUpdate", [$wpdiscuz, "bubbleUpdate"]);
add_action("wpdiscuz_nopriv_wpdBubbleUpdate", [$wpdiscuz, "bubbleUpdate"]);
// Inline Comments
add_action("wpdiscuz_wpdAddInlineComment", [$wpdiscuz, "addInlineComment"]);
add_action("wpdiscuz_nopriv_wpdAddInlineComment", [$wpdiscuz, "addInlineComment"]);
add_action("wpdiscuz_wpdGetLastInlineComments", [$wpdiscuz->helperAjax, "getLastInlineComments"]);
add_action("wpdiscuz_nopriv_wpdGetLastInlineComments", [$wpdiscuz->helperAjax, "getLastInlineComments"]);
add_action("wpdiscuz_wpdGetInlineCommentForm", [$wpdiscuz->helperAjax, "getInlineCommentForm"]);
add_action("wpdiscuz_nopriv_wpdGetInlineCommentForm", [$wpdiscuz->helperAjax, "getInlineCommentForm"]);
// Article Rating
add_action("wpdiscuz_wpdUserRate", [$wpdiscuz->helperAjax, "userRate"]);
add_action("wpdiscuz_nopriv_wpdUserRate", [$wpdiscuz->helperAjax, "userRate"]);
// Subscribe
add_action("wpdiscuz_wpdAddSubscription", [$wpdiscuz->helperEmail, "addSubscription"]);
add_action("wpdiscuz_nopriv_wpdAddSubscription", [$wpdiscuz->helperEmail, "addSubscription"]);
// Unsubscribe
add_action("wpdiscuz_wpdUnsubscribe", [$wpdiscuz->helperAjax, "unsubscribe"]);
add_action("wpdiscuz_nopriv_wpdUnsubscribe", [$wpdiscuz->helperAjax, "unsubscribe"]);

if (in_array($wpdiscuz_ajax_action, $allowedActions)) {
    if (is_user_logged_in()) {
        do_action("wpdiscuz_" . $wpdiscuz_ajax_action);
    } else {
        do_action("wpdiscuz_nopriv_" . $wpdiscuz_ajax_action);
    }
} else {
    die("-1");
}

function wpdiscuz_ABSPATH() {
    $path = join(DIRECTORY_SEPARATOR, ["wp-content", "plugins", "wpdiscuz", "utils", "ajax"]);
    return str_replace($path, "", __DIR__);
}
