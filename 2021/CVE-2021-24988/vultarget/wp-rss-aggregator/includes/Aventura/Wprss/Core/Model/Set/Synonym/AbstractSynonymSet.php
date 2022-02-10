<?php

namespace Aventura\Wprss\Core\Model\Set\Synonym;

use Aventura\Wprss\Core\Model\Set\AbstractGenericSet;

/**
 * Common functionality for synonym sets.
 *
 * @since 4.10
 */
class AbstractSynonymSet extends AbstractGenericSet
{
    /**
     * Gets a list of terms in this instance that are synonymous to the specified term.
     *
     * @since 4.10
     *
     * @param string $term The term to get synonyms for.
     *
     * @return string[]|\Traversable A list of terms that are synonymous to the specified term.
     */
    protected function _getSynonyms($term)
    {
        if (!$this->_hasItem($term)) {
            return array();
        }

        $items = $this->_getItems();
        $items = $this->_arrayConvert($items);
        return array_values(array_diff($items, array($term)));
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    protected function _validateItem($item)
    {
        if (!is_string($item)) {
            throw new \RuntimeException('The items in this set must be strings');
        }
    }
}
