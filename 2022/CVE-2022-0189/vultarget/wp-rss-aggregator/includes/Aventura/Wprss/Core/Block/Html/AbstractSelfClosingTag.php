<?php

namespace Aventura\Wprss\Core\Block\Html;

/**
 * Base functionality for a regular tag.
 * A regular tag is one that requires a closing tag, e.g. is not self-closing.
 */
class AbstractSelfClosingTag extends AbstractTag
{

    /**
     * Renders the tag HTML.
     *
     * @since 4.10
     */
    public function getOutput()
    {
        $attributes = $this->getAttributes();
        $attributes = count($attributes)
                ? ' '.static::getAttributesStringFromArray($attributes)
                : '';
        $tagName = $this->getTagName();

        return sprintf('<%1$s%2$s>', $tagName, $attributes);
    }

    /**
     * {@inheritdoc}
     * 
     * A self-closing tag has no content, and therefore content is now allowed
     * to be an attribute.
     *
     * @since 4.10
     */
    protected function _getNonAttributeKeys()
    {
        $keys = parent::_getNonAttributeKeys();
        if (isset($keys[static::K_CONTENT])) {
            unset($keys[static::K_CONTENT]);
        }
        
        return $keys;
    }
}
