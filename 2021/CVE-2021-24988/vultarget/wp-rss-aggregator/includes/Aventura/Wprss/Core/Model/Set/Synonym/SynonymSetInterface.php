<?php

namespace Aventura\Wprss\Core\Model\Set\Synonym;

use Aventura\Wprss\Core\Model\Set as BaseSets;

/**
 * Something that behaves like a synonym set.
 *
 * @since 4.10
 */
interface SynonymSetInterface extends BaseSets\SetInterface
{
    /**
     * Retrieves a list of terms from this set that are synonymous to the specified term.
     *
     * @since 4.10
     *
     * @param string $term The term, for which to get the synonyms.
     *
     * @return string[]|\Traversable A list of terms that are synonymous to the specified term.
     */
    public function getSynonyms($term);
}
