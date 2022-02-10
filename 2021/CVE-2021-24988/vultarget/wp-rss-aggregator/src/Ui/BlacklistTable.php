<?php

namespace RebelCode\Wpra\Core\Ui;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use WP_List_Table;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * A custom list table for WP RSS Aggregator blacklisted items.
 *
 * @since 4.17
 */
class BlacklistTable extends WP_List_Table
{
    /**
     * @since 4.17
     *
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * Constructor.
     *
     * @since 4.17
     *
     * @param CollectionInterface $collection The collection of blacklist items.
     */
    public function __construct(CollectionInterface $collection)
    {
        parent::__construct([
            'singular' => __('Blacklist', 'wprss'),
            'plural' => __('Blacklist', 'wprss'),
            'ajax' => false,
        ]);

        $this->collection = $collection;
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function no_items()
    {
        echo __('There are no blacklisted items', 'wprss');
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function has_items() {
        return $this->collection->getCount() > 0;
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'title' => __('Name', 'wprss'),
            'url' => __('URL', 'wprss'),
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    protected function get_sortable_columns()
    {
        return ['title'];
    }

    /**
     * Retrieves the list of hidden columns.
     *
     * @since 4.17
     *
     * @return string[]
     */
    protected function get_hidden_columns()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Renders row content for cells under the "title" column.
     *
     * @since 4.17
     *
     * @param EntityInterface $item The blacklist item.
     */
    public function column_title($item)
    {
        $id = $item['id'];
        $title = $item['title'];

        $editUrl = sprintf(admin_url('post.php?post=%s&action=edit'), $id);
        $title = sprintf('<strong><a href="%s">%s</a></strong>', $editUrl, esc_html($title));

        $actions = [
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                $editUrl, __('Edit')
            ),
            'delete' => sprintf(
                '<a href="javascript: void(0)" data-id="%s" class="wpra-delete-blacklist-link">%s</a>',
                $id, __('Delete', 'wprss')
            ),
        ];

        return $title . $this->row_actions($actions);
    }

    /**
     * Renders row content for cells under the "url" column.
     *
     * @since 4.17
     *
     * @param EntityInterface $item The blacklist item.
     */
    public function column_url($item)
    {
        return sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_html($item['url']));
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    protected function column_default($item, $column)
    {
        return print_r($item, true); // Show the whole array for troubleshooting purposes
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    protected function get_bulk_actions()
    {
        $actions = [
            'wpra_bulk_delete_blacklist' => __('Delete'),
        ];

        return $actions;
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function prepare_items()
    {
        $this->_column_headers = [
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns(),
        ];

        $itemsPerPage = $this->get_items_per_page('wpra_blacklist_per_page', 20);
        $currentPage = $this->get_pagenum();
        $totalItems = $this->collection->getCount();

        $this->set_pagination_args([
            'total_items' => $totalItems,
            'per_page' => $itemsPerPage,
        ]);

        $this->items = $this->collection->filter([
            'page' => $currentPage,
            'num_items' => $itemsPerPage,
        ]);
    }
}
