<?php

namespace Aventura\Wprss\Core\Model\AdminAjaxNotice;

use \Aventura\Wprss\Core;

/**
 * Basic functionality for a notice.
 *
 * @since 4.11
 */
abstract class NoticeAbstract extends Core\Model\ModelAbstract implements NoticeInterface
{
    protected static $dismissModes = array(
        NoticeInterface::DISMISS_MODE_NONE          => NoticeInterface::DISMISS_MODE_NONE,
        NoticeInterface::DISMISS_MODE_FRONTEND      => NoticeInterface::DISMISS_MODE_FRONTEND,
        NoticeInterface::DISMISS_MODE_AJAX          => NoticeInterface::DISMISS_MODE_AJAX,
    );

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function isActive()
    {
        return (bool) $this->getData('active', true);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getType()
    {
        return $this->getData('type', static::TYPE_UPDATED);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getStyle()
    {
        return $this->getData('style', static::STYLE_NORMAL);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getContent()
    {
        return $this->getData('content', '');
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getConditions()
    {
        return $this->getData('condition', array());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getConditionType()
    {
        return $this->getData('condition_type', static::CONDITION_TYPE_ALL);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function isDismissable()
    {
        return $this->getDismissMode() !== NoticeInterface::DISMISS_MODE_NONE;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getDismissMode()
    {
        return $this->getData('dismiss_mode', NoticeInterface::DISMISS_MODE_AJAX);
    }
}
