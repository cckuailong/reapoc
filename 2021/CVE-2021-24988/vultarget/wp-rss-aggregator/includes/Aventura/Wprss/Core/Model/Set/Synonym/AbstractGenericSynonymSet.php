<?php

namespace Aventura\Wprss\Core\Model\Set\Synonym;

/**
 * Base functionality of a synonym set.
 *
 * @since 4.10
 */
abstract class AbstractGenericSynonymSet extends AbstractSynonymSet implements SynonymSetInterface
{
    public function __construct(array $items = array())
    {
        parent::__construct($items);
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function getSynonyms($term)
    {
        return $this->_getSynonyms($term);
    }
}
