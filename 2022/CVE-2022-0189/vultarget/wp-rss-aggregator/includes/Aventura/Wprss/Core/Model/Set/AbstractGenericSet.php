<?php

namespace Aventura\Wprss\Core\Model\Set;

/**
 * Base functionality for generic sets with a set interface.
 *
 * @since 4.10
 */
abstract class AbstractGenericSet extends AbstractSet implements SetInterface
{
    /**
     * @since 4.10
     *
     * @param mixed[]|\Traversable $items The items to add to the set.
     */
    public function __construct(array $items = array())
    {
        $this->_construct();

        $this->addMany($items);
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function add($item)
    {
        $this->_validateItem($item);
        $this->_addItem($item);
        $this->_clearItemCache();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function addMany($items)
    {
        $this->_addItems($items);
        $this->_clearItemCache();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function remove($item)
    {
        $this->_removeItem($item);
        $this->_clearItemCache();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function removeMany($items)
    {
        foreach ($items as $_item) {
            $this->_removeItem($_item);
        }
        $this->_clearItemCache();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function has($item)
    {
        return $this->_hasItem($item);
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function clear()
    {
        $this->_construct();
        $this->_clearItemCache();
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function items()
    {
        return $this->_getCachedItems();
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function count()
    {
        return $this->_count();
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    protected function _validateItem($item)
    {
        // No exception means all items are valid.
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function hasOneOf($items)
    {
        return $this->_hasOneOf($items);
    }
}
