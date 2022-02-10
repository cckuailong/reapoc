<?php

/**
 * Represents a Wicked Folders plugin folder object.
 */
class Wicked_Folders_Folder implements \JsonSerializable {

    /**
     * The folder's ID.  The folder ID should be unique for a given post type
     * and taxonomy combination.
     *
     * @var string
     */
    public $id = false;

    /**
     * The ID of the user that owns the folder.
     *
     * @var int
     */
    public $owner_id = 0;

    /**
     * The display name of the owner.
     *
     * @var string
     */
    public $owner_name;

    /**
     * The ID of the folder's parent.
     *
     * @var string
     */
    public $parent = '0';

    /**
     * The folder's name.
     *
     * @var string
     */
    public $name;

    /**
     * The post type the folder belongs to.
     *
     * @var string
     */
    public $post_type;

    /**
     * The taxonomy the folder belongs to.
     *
     * @var string
     */
    public $taxonomy;

    /**
     * Whether or not the folder can be moved into other folders.
     *
     * @var boolean
     */
    public $movable = true;

    /**
     * Whether or not the folder can be edited.
     *
     * @var boolean
     */
    public $editable = true;

    /**
     * Whether or not the folder can be deleted.
     *
     * @var boolean
     */
    public $deletable = true;

    /**
     * Whether or not items can be assigned to the folder.
     *
     * @var boolean
     */
    public $assignable = true;

    /**
     * Whether or not the folder's sub folders should be lazy loaded.
     *
     * @var boolean
     */
    public $lazy = false;

    /**
     * The number of items in the folder.
     *
     * @var integer
     */
    public $item_count = 0;

    /**
     * Whether or not to display the number of items in the folder.
     *
     * @var bool
     */
    public $show_item_count = false;

    /**
     * The order of this folder relative to other folders with the same parent.
     *
     * @var int
     */
    public $order = 0;

    public function __construct( array $args ) {
        // TODO: throw error if ID argument is set and contains reserved characters
        // such as periods
        $args = wp_parse_args( $args, array(
            'parent'    => '0',
            'name'      => __( 'Untitled folder', 'wicked-folders' ),
        ) );
        foreach ( $args as $property => $arg ) {
            $this->{$property} = $arg;
        }
        /*
        if ( false === $this->id ) {
            throw new Exception( __( 'Folder requires an ID.', 'wicked-folders' ) );
        }
        */
        if ( ! $this->post_type ) {
            throw new Exception( __( 'Folder requires a post type.', 'wicked-folders' ) );
        }
        if ( ! $this->taxonomy ) {
            $this->taxonomy = "wicked_{$this->post_type}_folders";
        }
        // Change IDs to strings so that they compare correctly regardless of type
        $this->id       = ( string ) $this->id;
        $this->parent   = ( string ) $this->parent;
    }

    public function ancestors() {
        return array();
    }

    public function fetch_posts() {
        return array();
    }

    public function get_child_folders() {
        return array();
    }

    /**
     * Load the folder from the database.
     *
     * @return boolean
     *  True if the folder was successfully  loaded, false otherwise.
     */
    public function fetch() {
        return false;
    }

    public function get_ancestor_ids( $id = false ) {
        return array();
    }

    public function jsonSerialize() {
        return array(
            'id'            => $this->id,
            'parent'        => $this->parent,
            'ownerId'       => $this->owner_id,
            'ownerName'     => $this->owner_name,
            'name'          => $this->name,
            'postType'      => $this->post_type,
            'taxonomy'      => $this->taxonomy,
            'movable'       => $this->movable,
            'editable'      => $this->editable,
            'deletable'     => $this->deletable,
            'assignable'    => $this->assignable,
            'lazy'          => $this->lazy,
            'itemCount'     => $this->item_count,
            'showItemCount' => $this->show_item_count,
            'order'         => $this->order,
            'type'          => get_class( $this ),
        );
    }
}
