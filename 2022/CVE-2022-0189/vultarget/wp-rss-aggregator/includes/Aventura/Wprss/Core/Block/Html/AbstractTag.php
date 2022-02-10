<?php

namespace Aventura\Wprss\Core\Block\Html;

/**
 * Base functionlity for HTML tags.
 *
 * @since 4.9
 */
abstract class AbstractTag extends AbstractHtml implements TagInterface
{
    const TAG_NAME = 'div';

    const K_CONTENT = 'content';
    const K_BASE_NAMESPACE_DEPTH = 'base_namespace_depth';

    /**
     * {@inheritdoc}
     *
     * @since 4.9
     */
    public function getAttributes($attributes = false)
    {
        return !empty($attributes) && is_array($attributes)
                ? $this->getDataForKeys($attributes)
                : $this->getDataForKeys($this->_getNonAttributeKeys(), true);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.9
     */
    public function setAttributes($attributes)
    {
        $this->addData($attributes);
        return $this;
    }

    /**
     * Gets names of data keys which do not count as attribute names.
     *
     * @since 4.9
     * @return array A numeric array of key names.
     */
    protected function _getNonAttributeKeys()
    {
        return array(static::K_CONTENT, static::K_BASE_NAMESPACE_DEPTH);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.9
     * @return string This tag's content.
     */
    public function getContent()
    {
        return $this->getData(static::K_CONTENT);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.9
     */
    public function setContent($content)
    {
        $this->setData(static::K_CONTENT, $content);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.9
     */
    public function getTagName()
    {
        return static::TAG_NAME;
    }
}
