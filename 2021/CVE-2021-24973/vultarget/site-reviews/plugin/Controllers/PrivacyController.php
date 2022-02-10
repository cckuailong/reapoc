<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;

class PrivacyController extends Controller
{
    protected $itemsRemoved;
    protected $itemsRetained;
    protected $messages;
    protected $perPage;

    public function __construct()
    {
        $this->itemsRemoved = false;
        $this->itemsRetained = false;
        $this->messages = [];
        $this->perPage = 100;
        if (!glsr()->filterBool('personal-data/erase-all', true)) {
            $this->itemsRetained = true;
            $this->messages[] = _x('The email and associated name and IP address has been removed from all reviews, but the reviews themselves were not removed.', 'admin-text', 'site-reviews');
        }
    }

    /**
     * @param string $email
     * @param int $page
     * @return array
     * @callback $this->filterPersonalDataErasers
     */
    public function erasePersonalData($email, $page = 1)
    {
        $reviews = $this->reviews($email, $page);
        array_walk($reviews, [$this, 'erase']);
        return [
            'done' => count($reviews) < $this->perPage,
            'items_removed' => $this->itemsRemoved,
            'items_retained' => $this->itemsRetained,
            'messages' => $this->messages,
        ];
    }

    /**
     * @param string $email
     * @param int $page
     * @return array
     * @callback $this->filterPersonalDataExporters
     */
    public function exportPersonalData($email, $page = 1)
    {
        $reviews = $this->reviews($email, $page);
        $data = array_map([$this, 'export'], $reviews);
        return [
            'data' => $data,
            'done' => count($reviews) < $this->perPage,
        ];
    }

    /**
     * @param array $erasers
     * @return array
     * @filter wp_privacy_personal_data_erasers
     */
    public function filterPersonalDataErasers($erasers)
    {
        $erasers[glsr()->id] = [
            'callback' => [$this, 'erasePersonalData'],
            'eraser_friendly_name' => glsr()->name,
        ];
        return $erasers;
    }

    /**
     * @param array $exporters
     * @return array
     * @filter wp_privacy_personal_data_exporters
     */
    public function filterPersonalDataExporters($exporters)
    {
        $exporters[glsr()->id] = [
            'callback' => [$this, 'exportPersonalData'],
            'exporter_friendly_name' => glsr()->name,
        ];
        return $exporters;
    }

    /**
     * @return void
     * @action admin_init
     */
    public function privacyPolicyContent()
    {
        $content = glsr()->build('partials/privacy-policy');
        wp_add_privacy_policy_content(glsr()->name, wp_kses_post(wpautop($content, false)));
    }

    /**
     * @return void
     */
    protected function erase(Review $review)
    {
        glsr()->action('personal-data/erase', $review, $this->itemsRetained);
        if (!$this->itemsRetained) {
            wp_delete_post($review->ID, true);
        } else {
            glsr(ReviewManager::class)->deleteRevisions($review->ID);
            glsr(ReviewManager::class)->updateRating($review->ID, [
                'email' => '',
                'ip_address' => '',
                'name' => '',
            ]);
            delete_post_meta($review->ID,  '_submitted'); // delete the original stored request
        }
        $this->itemsRemoved = true;
    }

    /**
     * @return array
     */
    protected function export(Review $review)
    {
        $data = [];
        $fields = [ // order is intentional
            'title' => _x('Review Title', 'admin-text', 'site-reviews'),
            'content' => _x('Review Content', 'admin-text', 'site-reviews'),
            'name' => _x('Name', 'admin-text', 'site-reviews'),
            'email' => _x('Email', 'admin-text', 'site-reviews'),
            'ip_address' => _x('IP Address', 'admin-text', 'site-reviews'),
            'terms' => _x('Terms Accepted', 'admin-text', 'site-reviews'),
        ];
        foreach ($fields as $field => $name) {
            if ($value = $review->$field) {
                if ('terms' === $field && Cast::toBool($value)) {
                    $value = $review->date_gmt;
                }
                $data[] = ['name' => $name, 'value' => $value];
            }
        }
        return [
            'data' => glsr()->filterArray('personal-data/export', $data, $review),
            'group_id' => glsr()->id,
            'group_label' => _x('Reviews', 'admin-text', 'site-reviews'),
            'item_id' => glsr()->post_type.'-'.$review->ID,
        ];
    }

    /**
     * @param string $email
     * @param int $page
     * @return array
     */
    protected function reviews($email, $page)
    {
        return glsr(Query::class)->reviews([
            'email' => $email,
            'page' => $page,
            'per_page' => $this->perPage,
        ]);
    }
}
