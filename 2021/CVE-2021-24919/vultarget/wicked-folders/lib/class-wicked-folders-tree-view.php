<?php

/**
 * Renders a tree view for folder navigation.
 */
class Wicked_Folders_Tree_View {

    /**
     * Internal collection of folder objects.
     *
     * @var array
     */
    private $folders = array();

    /**
     * The post type that the tree view should display folders for.
     *
     * @var string
     */
    public $post_type;

    /**
     * The taxonomy that the tree view should display folders from.  If this
     * isn't specified, the tree view will attempt to use the default folder
     * taxonomy for the post type (i.e. wf_{$post_type}_folders).
     *
     * @var string
     */
    public $taxonomy;

    /**
     * Array of folder IDs that should be expanded when the tree view is
     * displayed.
     *
     * @var array
     */
    public $expanded_folder_ids = array();

    /**
     * The currently active folder ID.
     *
     * @var int|string
     */
    public $active_folder_id = 0;

    /**
     * The base URL to use for generating the links in the tree view.
     *
     * @var string
     */
    public $url;

    /**
     * Whether or not to fetch and display the objects for each folder.
     *
     * @var bool
     */
    public $fetch_objects = false;

    public function __construct( $post_type, $taxonomy = false ) {

        $this->post_type = $post_type;

        if ( $taxonomy ) {
            $this->taxonomy = $taxonomy;
        } else {
            $this->taxonomy = "wicked_{$this->post_type}_folders";
        }

    }

    /**
     * Adds a folder to the tree view's folder collection.
     *
     * @param $folder Wicked_Folders_Folder
     *  The folder object to add.
     */
    public function add_folder( Wicked_Folders_Folder $folder ) {
        $this->folders[] = $folder;
    }

    /**
     * Adds an array of folder objects to the tree view's folder collection
     * and makes sure each item in the array is a folder object.
     *
     * @param array $folders
     *  The folder objects to add.
     *
     * @return array
     *  The tree view's folders collection.
     */
    public function add_folders( array $folders ) {
        foreach ( $folders as $folder ) {
            if ( is_a( $folder, 'Wicked_Folders_Folder' ) ) {
                $this->folders[] = $folder;
            } else {
                throw new Exception( __( 'All items must be of type Wicked_Folders_Folder.', 'wicked-folders' ) );
            }
        }
        return $this->folders;
    }

    public function get_ancestors( $id ) {

        static $_ancestors = array();

        if ( isset( $_ancestors[ $id ] ) ) {
            return $_ancestors[ $id ];
        }

        $ancestors  = array();
        $folder     = $this->get_folder( $id );

        if ( ! $folder ) return array();

        if ( $parent = $this->get_folder( $folder->parent ) ) {
            $ancestors[]        = $parent;
            $parent_ancestors   = $this->get_ancestors( $parent->id );
            $ancestors          = array_merge( $ancestors, $parent_ancestors );
            $_ancestors[ $id ]  = $ancestors;
        }

        return $ancestors;

    }

    public function get_ancestor_ids( $id ) {
        $ids        = array();
        $ancestors  = $this->get_ancestors( $id );
        foreach ( $ancestors as $ancestor ) {
            $ids[] = $ancestor->id;
        }
        return $ids;
    }

    public function get_ancestor_count( $id ) {
        $ancestors = $this->get_ancestors( $id );
        return count( $ancestors );
    }

    public function get_folder( $id ) {
        foreach ( $this->folders as $folder ) {
            if ( $id == $folder->id ) {
                return $folder;
            }
        }
        return false;
    }

    /**
     * Recursive function that generates the markup for the tree view as an
     * unordered list.
     *
     * @param $parent string
     *  The parent folder to build the tree for.
     *
     * @return string
     *  An HTML nested unordered list.
     */
    public function build_tree( $parent = 'root' ) {

        $active_folder_ancestors = $this->get_ancestor_ids( $this->active_folder_id );

        if ( ! $this->url ) {
            $screen     = get_current_screen();
            $this->url  = $screen->parent_file;
        }

		$html 			= '';
		//$inner_html = '';
        $objects_html = '';
        $this->url  = add_query_arg( 'page', $this->taxonomy, $this->url );

        if ( $this->active_folder_id && empty( $active_folder_ancestors ) ) {
			//$active_folder_ancestors = get_ancestors( $this->active_folder_id, $this->taxonomy, 'taxonomy' );
		}

        foreach ( $this->folders as $folder ) {
            if ( $folder->parent == $parent ) {
				$expanded 	= false;
				$classes 	= array(
					'wicked-folder-container',
				);
				if ( $folder->id == $this->active_folder_id || in_array( $folder->id, $active_folder_ancestors ) || in_array( $folder->id, $this->expanded_folder_ids ) ) {
					$expanded = true;
                    $classes[] = 'expanded';
				}
				if ( $folder->id == $this->active_folder_id ) {
					$classes[] = 'wicked-selected';
				}

                $html .= sprintf( '<li class="%s" data-folder-id="%d">', join( ' ', $classes ), $folder->id );
				$html .= '<span class="wicked-folder-inner-container">';
				$html .= '<a class="wicked-toggle" href="#"></a>';
				$html .= '<a class="wicked-folder" href="' . add_query_arg( 'folder', $folder->id, $this->url ) . '">' . $folder->name . '</a>';
				$html .= '</span>';

                // Create HTML for folder's objects
                $objects_html   = '';

                if ( $this->fetch_objects ) {
                    $objects        = $folder->fetch_objects();
                    if ( $objects ) {
                        foreach ( $objects as $object ) {
                            $objects_html .= sprintf( '<li class="wicked-object-container" data-object-id="%d">', $object->ID );
                            $objects_html .= '<a class="wicked-object" href="' . get_edit_post_link( $object->ID ) . '">' . $object->post_title . '</a>';
                            $objects_html .= '</li>';
                        }
                    }
                }

                // Create HTML for children nodes of folder
                $children_html      = '';
				$child_tree_html    = $this->build_tree( $folder->id );

                if ( $child_tree_html || $objects_html ) {
                    $children_html .= '<ul>';
                    $children_html .= $child_tree_html;
                    $children_html .= $objects_html;
                    $children_html .= '</ul>';
                }

                $html .= $children_html;

                $html .= '</li>';
            }
        }

		if ( $html ) {
			if ( 'root' == $parent ) {
				$html = '<ul class="wicked-tree">' . $html . '</ul>';
			}

		}

		return $html;

    }

    public function build_flat_tree_array( $parent = 'root' ) {

        $folders = array();

        foreach ( $this->folders as $folder ) {
            if ( $folder->parent == $parent ) {
                $folders[] = $folder;
				$children = $this->build_flat_tree_array( $folder->id );
                $folders = array_merge( $folders, $children );
            }
        }

        return $folders;

    }

}
