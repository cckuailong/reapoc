<?php

namespace Aventura\Wprss\Core\Model\Set\Synonym;

use Aventura\Wprss\Core\Model\Set as Sets;
use Dhii\Collection;

/**
 * A set of synonym sets.
 *
 * @since 4.10
 */
class Set extends Sets\AbstractGenericSetSet implements SynonymSetSetInterface
{
    /**
     * Get a set, to which the specified set belongs.
     *
     * @since 4.10
     *
     * @param string $term The term, for which to get the synonyms set.
     *
     * @return Simple The synonym set, to which the specified term belongs.
     *  If the set does not exist, creates it and adds the term to it.
     */
    public function getSetForTerm($term)
    {
        $synonymSets = $this->_getSetsForTerm($term);
        $synonymSets = $this->_arrayConvert($synonymSets);
        if (!($set = array_shift($synonymSets))) {
            $set = new Sets\Synonym\Simple(array($term));
            $this->add($set);
        }

        return $set;
    }

    /**
     * Retrieves all internal synonym sets that contain the specified term.
     *
     * @since 4.10
     *
     * @param string $term The term to check for.
     * @return Collection\SetInterface[]|\Traversable A list of sets that contain the specified term.
     */
    protected function _getSetsForTerm($term)
    {
        return $this->_getContaining($term);
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    protected function _validateItem($item)
    {
        if (!($item instanceof Sets\Synonym\Simple)) {
            throw new \RuntimeException(sprintf('The items in this set must be simple synonym sets'));
        }
    }
}
