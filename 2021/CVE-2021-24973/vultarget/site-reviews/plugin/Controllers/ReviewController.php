<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\AssignPosts;
use GeminiLabs\SiteReviews\Commands\AssignTerms;
use GeminiLabs\SiteReviews\Commands\AssignUsers;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Commands\UnassignPosts;
use GeminiLabs\SiteReviews\Commands\UnassignTerms;
use GeminiLabs\SiteReviews\Commands\UnassignUsers;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Review;

class ReviewController extends Controller
{
    /**
     * @return void
     * @action admin_action_approve
     */
    public function approve()
    {
        if (glsr()->id == filter_input(INPUT_GET, 'plugin')) {
            check_admin_referer('approve-review_'.($postId = $this->getPostId()));
            $this->execute(new ToggleStatus($postId, 'publish'));
            wp_safe_redirect(wp_get_referer());
            exit;
        }
    }

    /**
     * @param array $posts
     * @return array
     * @filter the_posts
     */
    public function filterPostsToCacheReviews($posts)
    {
        $reviews = array_filter($posts, function ($post) {
            return glsr()->post_type === $post->post_type;
        });
        if ($postIds = wp_list_pluck($reviews, 'ID')) {
            glsr(Query::class)->reviews([], $postIds); // this caches the associated Review objects
        }
        return $posts;
    }

    /**
     * @param array $data
     * @param array $postArr
     * @return array
     * @filter wp_insert_post_data
     */
    public function filterReviewPostData($data, $postArr)
    {
        if (empty($postArr['ID']) || glsr()->post_type !== glsr_get($postArr, 'post_type')) {
            return $data;
        }
        if (empty(filter_input(INPUT_POST, 'post_author'))) {
            $data['post_author'] = 0; // the review has an unknown author
        }
        if (isset($_POST['post_author_override'])) {
            $data['post_author'] = $_POST['post_author_override']; // use the value from the author meta box
        }
        return $data;
    }

    /**
     * @param string $template
     * @return string
     * @filter site-reviews/rendered/template/review
     */
    public function filterReviewTemplate($template, array $data)
    {
        $search = 'id="review-';
        $dataType = Arr::get($data, 'review.type', 'local');
        $replace = sprintf('data-type="%s" %s', $dataType, $search);
        return str_replace($search, $replace, $template);
    }

    /**
     * @param string $operator
     * @return string
     * @filter site-reviews/query/sql/clause/operator
     */
    public function filterSqlClauseOperator($operator)
    {
        $operators = ['loose' => 'OR', 'strict' => 'AND'];
        return Arr::get($operators, glsr_get_option('reviews.assignment', 'strict', 'string'), $operator);
    }

    /**
     * @return array
     * @filter site-reviews/review/build/after
     */
    public function filterTemplateTags(array $tags, Review $review, ReviewHtml $reviewHtml)
    {
        $tags['assigned_links'] = $reviewHtml->buildTemplateTag($review, 'assigned_links', $review->assigned_posts);
        return $tags;
    }

    /**
     * Triggered when one or more categories are added or removed from a review.
     *
     * @param int $postId
     * @param array $terms
     * @param array $newTTIds
     * @param string $taxonomy
     * @param bool $append
     * @param array $oldTTIds
     * @return void
     * @action set_object_terms
     */
    public function onAfterChangeAssignedTerms($postId, $terms, $newTTIds, $taxonomy, $append, $oldTTIds)
    {
        if (Review::isReview($postId)) {
            $review = glsr(Query::class)->review($postId);
            $diff = $this->getAssignedDiffs($oldTTIds, $newTTIds);
            $this->execute(new UnassignTerms($review, $diff['old']));
            $this->execute(new AssignTerms($review, $diff['new']));
        }
    }

    /**
     * Triggered when a post status changes or when a review is approved|unapproved|trashed.
     *
     * @param string $oldStatus
     * @param string $newStatus
     * @param \WP_Post $post
     * @return void
     * @action transition_post_status
     */
    public function onAfterChangeStatus($newStatus, $oldStatus, $post)
    {
        if (in_array($oldStatus, ['new', $newStatus])) {
            return;
        }
        if ('auto-draft' === $oldStatus && 'auto-draft' !== $newStatus) { // create review
            glsr(ReviewManager::class)->createFromPost($post->ID);
        }
        $isPublished = 'publish' === $newStatus;
        if (Review::isReview($post)) {
            glsr(ReviewManager::class)->updateRating($post->ID, ['is_approved' => $isPublished]);
            glsr(Cache::class)->delete($post->ID, 'reviews');
            glsr(CountManager::class)->recalculate();
        } else {
            glsr(ReviewManager::class)->updateAssignedPost($post->ID, $isPublished);
        }
    }

    /**
     * Triggered when a review's assigned post IDs are updated.
     *
     * @return void
     * @action site-reviews/review/updated/post_ids
     */
    public function onChangeAssignedPosts(Review $review, array $postIds = [])
    {
        $diff = $this->getAssignedDiffs($review->assigned_posts, $postIds);
        $this->execute(new UnassignPosts($review, $diff['old']));
        $this->execute(new AssignPosts($review, $diff['new']));
    }

    /**
     * Triggered when a review's assigned users IDs are updated.
     *
     * @return void
     * @action site-reviews/review/updated/user_ids
     */
    public function onChangeAssignedUsers(Review $review, array $userIds = [])
    {
        $diff = $this->getAssignedDiffs($review->assigned_users, $userIds);
        $this->execute(new UnassignUsers($review, $diff['old']));
        $this->execute(new AssignUsers($review, $diff['new']));
    }

    /**
     * Triggered after a review is created.
     *
     * @return void
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review, CreateReview $command)
    {
        $this->execute(new AssignPosts($review, $command->assigned_posts));
        $this->execute(new AssignUsers($review, $command->assigned_users));
    }

    /**
     * Triggered when a review is created.
     *
     * @param int $postId
     * @return void
     * @action site-reviews/review/create
     */
    public function onCreateReview($postId, CreateReview $command)
    {
        $values = glsr()->args($command->toArray()); // this filters the values
        $data = glsr(RatingDefaults::class)->restrict($values->toArray());
        $data['review_id'] = $postId;
        $data['is_approved'] = 'publish' === get_post_status($postId);
        if (false === glsr(Database::class)->insert('ratings', $data)) {
            glsr_log()->error(sprintf('A review could not be created. Here are some things to try which may fix the problem: %s %s %s',
                PHP_EOL.'1. First, run the "Repair Review Relations" tool.',
                PHP_EOL.'2. Next, hold down the ALT key and run the Migrate Plugin tool.',
                PHP_EOL.'3. If the problem persists, please use the "Contact Support" section on the Help page.'
            ))->debug($data);
            wp_delete_post($postId, true); // remove post as review was not created
            return;
        }
        $termIds = wp_set_object_terms($postId, $values->assigned_terms, glsr()->taxonomy);
        if (is_wp_error($termIds)) {
            glsr_log()->error($termIds->get_error_message());
        }
        if (!empty($values->response)) {
            glsr(Database::class)->metaSet($postId, 'response', $values->response); // save the response if one is provided
        }
        foreach ($values->custom as $key => $value) {
            glsr(Database::class)->metaSet($postId, 'custom_'.$key, $value);
        }
    }

    /**
     * Triggered when a review or other post type is deleted and the posts table uses the MyISAM engine.
     *
     * @param int $postId
     * @param \WP_Post $post
     * @return void
     * @action deleted_post
     */
    public function onDeletePost($postId, $post)
    {
        if (glsr()->post_type === $post->post_type) {
            $this->onDeleteReview($postId);
            return;
        }
        $reviews = glsr(Query::class)->reviews([
            'assigned_posts' => $postId,
            'per_page' => -1,
            'status' => 'all',
        ]);
        if (glsr(Database::class)->delete('assigned_posts', ['post_id' => $postId])) {
            array_walk($reviews, function ($review) {
                glsr(Cache::class)->delete($review->ID, 'reviews');
            });
        }
    }

    /**
     * Triggered when a review is deleted and the posts table uses the MyISAM engine.
     *
     * @param int $reviewId
     * @return void
     * @see $this->onDeletePost()
     */
    public function onDeleteReview($reviewId)
    {
        glsr(ReviewManager::class)->delete($reviewId);
    }

    /**
     * Triggered when a user is deleted and the users table uses the MyISAM engine.
     *
     * @param int $userId
     * @return void
     * @action deleted_user
     */
    public function onDeleteUser($userId)
    {
        $reviews = glsr(Query::class)->reviews([
            'assigned_users' => $userId,
            'per_page' => -1,
            'status' => 'all',
        ]);
        if (glsr(Database::class)->delete('assigned_users', ['user_id' => $userId])) {
            array_walk($reviews, function ($review) {
                glsr(Cache::class)->delete($review->ID, 'reviews');
            });
        }
    }

    /**
     * Triggered when a review is edited or trashed.
     * It's unnecessary to trigger a term recount as this is done by the set_object_terms hook
     * We need to use "edit_post" to support revisions (vs "save_post").
     *
     * @param int $postId
     * @param \WP_Post $post
     * @param \WP_Post $oldPost
     * @return void
     * @action post_updated
     */
    public function onEditReview($postId, $post, $oldPost)
    {
        if (!glsr()->can('edit_posts') || !$this->isEditedReview($post, $oldPost)) {
            return;
        }
        $review = glsr(Query::class)->review($postId);
        if ('post' === glsr_current_screen()->base) {
            $this->updateReview($review);
        } else {
            $this->bulkUpdateReview($review);
        }
    }

    /**
     * Triggered after a review is created.
     *
     * @return void
     * @action site-reviews/review/created
     */
    public function sendNotification(Review $review)
    {
        if (!empty(glsr_get_option('general.notifications'))) {
            glsr(Queue::class)->async('queue/notification', ['review_id' => $review->ID]);
        }
    }

    /**
     * @return void
     * @action admin_action_unapprove
     */
    public function unapprove()
    {
        if (glsr()->id == filter_input(INPUT_GET, 'plugin')) {
            check_admin_referer('unapprove-review_'.($postId = $this->getPostId()));
            $this->execute(new ToggleStatus($postId, 'pending'));
            wp_safe_redirect(wp_get_referer());
            exit;
        }
    }

    /**
     * @return void
     */
    protected function bulkUpdateReview(Review $review)
    {
        if ($assignedPostIds = filter_input(INPUT_GET, 'post_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY)) {
            glsr()->action('review/updated/post_ids', $review, Cast::toArray($assignedPostIds)); // trigger a recount of assigned posts
        }
        if ($assignedUserIds = filter_input(INPUT_GET, 'user_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY)) {
            glsr()->action('review/updated/user_ids', $review, Cast::toArray($assignedUserIds)); // trigger a recount of assigned users
        }
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        glsr()->action('review/saved', $review, []); // pass an empty array since review values are unchanged
    }

    /**
     * @return array
     */
    protected function getAssignedDiffs(array $existing, array $replacements)
    {
        sort($existing);
        sort($replacements);
        $new = $old = [];
        if ($existing !== $replacements) {
            $ignored = array_intersect($existing, $replacements);
            $new = array_diff($replacements, $ignored);
            $old = array_diff($existing, $ignored);
        }
        return [
            'new' => $new,
            'old' => $old,
        ];
    }

    /**
     * @param \WP_Post $post
     * @param \WP_Post $oldPost
     * @return bool
     */
    protected function isEditedReview($post, $oldPost)
    {
        if (glsr()->post_type !== $post->post_type) {
            return false;
        }
        if (in_array('trash', [$post->post_status, $oldPost->post_status])) {
            return false; // trashed posts cannot be edited
        }
        $input = 'edit' === glsr_current_screen()->base ? INPUT_GET : INPUT_POST;
        return 'glsr_action' !== filter_input($input, 'action'); // abort if not a proper post update (i.e. approve/unapprove)
    }

    /**
     * @return void
     */
    protected function updateReview(Review $review)
    {
        $assignedPostIds = filter_input(INPUT_POST, 'post_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);
        $assignedUserIds = filter_input(INPUT_POST, 'user_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);
        glsr()->action('review/updated/post_ids', $review, Cast::toArray($assignedPostIds)); // trigger a recount of assigned posts
        glsr()->action('review/updated/user_ids', $review, Cast::toArray($assignedUserIds)); // trigger a recount of assigned users
        glsr(MetaboxController::class)->saveResponseMetabox($review);
        $submittedValues = Helper::filterInputArray(glsr()->id);
        if (Arr::get($submittedValues, 'is_editing_review')) {
            $submittedValues['rating'] = Arr::get($submittedValues, 'rating');
            $submittedValues['terms'] = Arr::get($submittedValues, 'terms', 0);
            glsr(ReviewManager::class)->updateRating($review->ID, $submittedValues); // values are sanitized here
            glsr(ReviewManager::class)->updateCustom($review->ID, $submittedValues); // values are sanitized here
        }
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        glsr()->action('review/saved', $review, $submittedValues);
    }
}
