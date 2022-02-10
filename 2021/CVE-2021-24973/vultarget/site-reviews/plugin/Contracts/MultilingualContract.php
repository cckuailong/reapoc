<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MultilingualContract
{
    /**
     * @param int|string $postId
     * @return int
     */
    public function getPostId($postId);

    /**
     * @return array
     */
    public function getPostIds(array $postIds);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return bool
     */
    public function isSupported();
}
