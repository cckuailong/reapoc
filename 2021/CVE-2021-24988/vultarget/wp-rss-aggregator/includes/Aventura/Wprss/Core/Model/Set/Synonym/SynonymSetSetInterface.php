<?php

namespace Aventura\Wprss\Core\Model\Set\Synonym;

use Aventura\Wprss\Core\Model\Set\SetSetInterface;

/**
 * Something that can behave like a set of synonym sets.
 *
 * @since 4.10
 */
interface SynonymSetSetInterface extends SetSetInterface
{
    /**
     * Guaranteed to retrieve a set containing the specified term.
     *
     * If this instance has a set containing the term, that set will be returned.
     * Otherwise, a new set is created, and populated with the specified term.
     *
     * @since 4.10
     *
     * @param string $term The term, for which to get the synonym set.
     *
     * @return SynonymSetInterface The synonym set containing the specified term.
     */
    public function getSetForTerm($term);
}
