<?php

namespace RebelCode\Wpra\Core\Handlers\Images;

/**
 * The handler that adds the images column to the feed items page.
 *
 * @since 4.14
 */
class AddItemsImageColumnHandler
{
    /**
     * @since 4.14
     *
     * @var string
     */
    protected $key;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $name;

    /**
     * @since 4.14
     *
     * @var int
     */
    protected $pos;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param string $key The column key.
     * @param string $name The column name.
     * @param int    $pos The column position, 0-based.
     */
    public function __construct($key, $name, $pos)
    {
        $this->key = $key;
        $this->name = $name;
        $this->pos = $pos;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke($columns)
    {
        $before = array_slice($columns, 0, $this->pos, true);
        $after = array_slice($columns, $this->pos, null, true);

        return $before + [$this->key => $this->name] + $after;
    }
}
