<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Aventura\Wprss\Core\Block\Html;

/**
 * Description of Span
 *
 * @author Xedin Unknown
 */
class Span extends AbstractTag
{
    const TAG_NAME = 'span';

    /**
     * Renders the anchor HTML.
     *
     * @since 4.9
     */
    public function getOutput()
    {
        $attributes = $this->getAttributes();
        $attributes = count($attributes)
                ? ' '.static::getAttributesStringFromArray($attributes)
                : '';
        $content = $this->getContent();
        $tagName = $this->getTagName();

        return sprintf('<%1$s%2$s>%3$s</%1$s>', $tagName, $attributes, $content);
    }
}
