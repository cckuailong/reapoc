<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\SlackDefaults;
use GeminiLabs\SiteReviews\Review;
use WP_Error;

class Slack
{
    /**
     * @var string
     */
    public $endpoint;

    /**
     * @var array
     */
    public $notification;

    /**
     * @var Review
     */
    public $review;

    public function __construct()
    {
        $this->endpoint = glsr(OptionManager::class)->get('settings.general.notification_slack');
    }

    /**
     * @return Slack
     */
    public function compose(Review $review, array $notification)
    {
        if (empty($this->endpoint)) {
            return $this;
        }
        $args = shortcode_atts(glsr(SlackDefaults::class)->defaults(), $notification);
        $this->review = $review;
        $notification = [
            'icon_url' => $args['icon_url'],
            'username' => $args['username'],
            'attachments' => [[
                'actions' => $this->buildAction($args),
                'pretext' => $args['pretext'],
                'color' => $args['color'],
                'fallback' => $args['fallback'],
                'fields' => $this->buildFields(),
            ]],
        ];
        $this->notification = glsr()->filterArray('slack/compose', $notification, $this);
        return $this;
    }

    /**
     * @return WP_Error|array
     */
    public function send()
    {
        if (empty($this->endpoint)) {
            return new WP_Error('slack', 'Slack notification was not sent: missing endpoint');
        }
        return wp_remote_post($this->endpoint, [
            'blocking' => false,
            'body' => json_encode($this->notification),
            'headers' => ['Content-Type' => 'application/json'],
            'httpversion' => '1.0',
            'method' => 'POST',
            'redirection' => 5,
            'sslverify' => false,
            'timeout' => 45,
        ]);
    }

    /**
     * @return array
     */
    protected function buildAction(array $args)
    {
        return [[
            'text' => $args['button_text'],
            'type' => 'button',
            'url' => $args['button_url'],
        ]];
    }

    /**
     * @return array
     */
    protected function buildAuthorField()
    {
        $email = !empty($this->review->email)
            ? '<'.$this->review->email.'>'
            : '';
        $author = trim(rtrim($this->review->author).' '.$email);
        return ['value' => implode(' - ', array_filter([$author, $this->review->ip_address]))];
    }

    /**
     * @return array
     */
    protected function buildContentField()
    {
        return !empty($this->review->content)
            ? ['value' => $this->review->content]
            : [];
    }

    /**
     * @return array
     */
    protected function buildFields()
    {
        $fields = [
            $this->buildStarsField(),
            $this->buildTitleField(),
            $this->buildContentField(),
            $this->buildAuthorField(),
        ];
        return array_filter($fields);
    }

    /**
     * @return array
     */
    protected function buildStarsField()
    {
        $solidStars = str_repeat('★', $this->review->rating);
        $emptyStars = str_repeat('☆', max(0, glsr()->constant('MAX_RATING', Rating::class) - $this->review->rating));
        $stars = $solidStars.$emptyStars;
        $stars = glsr()->filterString('slack/stars', $stars, $this->review->rating, glsr()->constant('MAX_RATING', Rating::class));
        return ['title' => $stars];
    }

    /**
     * @return array
     */
    protected function buildTitleField()
    {
        return !empty($this->review->title)
            ? ['title' => $this->review->title]
            : [];
    }
}
