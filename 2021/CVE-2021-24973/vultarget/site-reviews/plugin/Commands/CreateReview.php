<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class CreateReview implements Contract
{
    public $assigned_posts;
    public $assigned_terms;
    public $assigned_users;
    public $avatar;
    public $blacklisted;
    public $content;
    public $custom;
    public $date;
    public $date_gmt;
    public $email;
    public $form_id;
    public $ip_address;
    public $is_approved;
    public $is_pinned;
    public $name;
    public $post_id;
    public $rating;
    public $referer;
    public $request;
    public $response;
    public $terms;
    public $terms_exist;
    public $title;
    public $type;
    public $url;

    protected $errors;
    protected $message;
    protected $recaptcha;
    protected $review;

    public function __construct(Request $request)
    {
        if (!defined('WP_IMPORTING') || empty($request->ip_address)) {
            $request->set('ip_address', Helper::getIpAddress()); // required for Akismet and Blacklist validation
        }
        $this->request = $request;
        $this->sanitize();
    }

    /**
     * @return static
     */
    public function handle()
    {
        if ($this->validate()) {
            $this->create();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function referer()
    {
        if ($referer = $this->redirect($this->referer)) {
            return $referer;
        }
        glsr_log()->warning('The form referer ($_SERVER[REQUEST_URI]) was empty.')->debug($this);
        return Url::home();
    }

    /**
     * @return array
     */
    public function response()
    {
        return [
            'errors' => $this->errors,
            'html' => (string) $this->review,
            'message' => $this->message,
            'recaptcha' => $this->recaptcha,
            'redirect' => $this->redirect(),
            'review' => Cast::toArray($this->review),
        ];
    }

    /**
     * @return bool
     */
    public function success()
    {
        if (false === $this->errors) {
            glsr()->sessionClear();
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $values = get_object_vars($this);
        $values = glsr()->filterArray('create/review-values', $values, $this);
        return glsr(CreateReviewDefaults::class)->merge($values);
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $validator = glsr(ValidateReview::class)->validate($this->request);
        $this->blacklisted = $validator->blacklisted;
        $this->errors = $validator->errors;
        $this->message = $validator->message;
        $this->recaptcha = $validator->recaptcha;
        return $validator->isValid();
    }

    /**
     * This only validates the provided values in the Request.
     * @return bool
     */
    public function isValid()
    {
        return glsr(DefaultValidator::class, ['request' => $this->request])->isValidRequest();
    }

    /**
     * @return string
     */
    protected function avatar()
    {
        if (!empty($this->avatar)) {
            return $this->avatar;
        }
        $review = new Review($this->toArray(), false); // don't init!
        if (empty($this->email)) {
            $review->set('author_id', get_current_user_id());
        }
        return glsr(Avatar::class)->generate($review);
    }

    /**
     * @return void
     */
    protected function create()
    {
        if ($this->review = glsr(ReviewManager::class)->create($this)) {
            $this->message = __('Your review has been submitted!', 'site-reviews');
            return;
        }
        $this->errors = [];
        $this->message = __('Your review could not be submitted and the error has been logged. Please notify the site administrator.', 'site-reviews');
    }

    /**
     * @return array
     */
    protected function custom()
    {
        return glsr(CustomFieldsDefaults::class)->filter($this->request->toArray());
    }

    /**
     * @return string
     */
    protected function redirect($fallback = '')
    {
        $redirect = trim(strval(get_post_meta($this->post_id, 'redirect_to', true)));
        $redirect = glsr()->filterString('review/redirect', $redirect, $this);
        if (empty($redirect)) {
            $redirect = $fallback;
        }
        return sanitize_text_field($redirect);
    }

    /**
     * @return void
     */
    protected function sanitize()
    {
        $values = glsr(CreateReviewDefaults::class)->restrict($this->request->toArray());
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        if (!empty($this->date)) {
            $this->date_gmt = get_gmt_from_date($this->date); // set the GMT date
        }
        $this->custom = $this->custom();
        $this->type = $this->type();
        $this->avatar = $this->avatar(); // do this last
    }

    /**
     * @return string
     */
    protected function type()
    {
        $reviewTypes = glsr()->retrieveAs('array', 'review_types');
        return array_key_exists($this->type, $reviewTypes) ? $this->type : 'local';
    }
}
