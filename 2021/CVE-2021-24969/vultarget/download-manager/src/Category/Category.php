<?php
/**
 * Download manager category
 */

namespace WPDM\Category;


class Category
{
    public $ID;
    public $name;
    public $slug;
    public $description;
    public $icon;
    public $access;
    public $parent;
    public $packageCount;

    function __construct($ID_SLUG_NAME = null)
    {
        if($ID_SLUG_NAME && $_term = term_exists($ID_SLUG_NAME, 'wpdmcategory'))
        {
            $term = get_term($_term['term_id']);

            $this->ID               = $term->term_id;
            $this->name             = $term->name;
            $this->slug             = $term->slug;
            $this->description      = $term->description;
            $this->access           = maybe_unserialize(get_term_meta($this->ID, "__wpdm_access", true));
            $this->parent           = $term->parent;
            $this->packageCount     = $term->count;
            $this->icon             = CategoryController::icon($this->ID);

        }
    }

    function get($ID_SLUG_NAME)
    {
        return new Category($ID_SLUG_NAME);
    }
}

