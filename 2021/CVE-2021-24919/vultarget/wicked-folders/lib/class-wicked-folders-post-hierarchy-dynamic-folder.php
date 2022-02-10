<?php

/**
 * Displays post hierarchy as a folder structure.
 *
 * @since 2.10
 */
class Wicked_Folders_Post_Hierarchy_Dynamic_Folder extends Wicked_Folders_Dynamic_Folder {

    private $post_id = 0;

    public function __construct( $args ) {
        parent::__construct( $args );

        $this->lazy = true;
        $this->show_item_count = false;
    }

    public function pre_get_posts( $query ) {
        $this->parse_id();

        // Get include children setting
        $include_children = Wicked_Folders::include_children( $this->post_type, $this->id );

        // Include items from child folders if include children is enabled
        if ( $include_children ) {
            $ids            = array( $this->post_id );
            $child_folders  = $this->get_child_folders();

            foreach ( $child_folders as $folder ) {
                $folder->parse_id();

                $ids[] = $folder->post_id;
            }

            $query->set( 'post_parent__in', $ids );
        } else {
            // Otherwise, only show direct descendants
            $query->set( 'post_parent', $this->post_id );
        }
    }

    /**
     * Parses the folder's ID to determine the post parent that the folder
     * should filter by.
     */
    private function parse_id() {
        $this->post_id = ( int ) substr( $this->id, 18 );
    }

    public function get_child_folders() {
        global $wpdb;

        $this->parse_id();

        $folders = array();

        $posts = $wpdb->get_results( "
            SELECT DISTINCT
                parents.ID, parents.post_title
            FROM
                {$wpdb->posts} AS parents
            INNER JOIN
                {$wpdb->posts} AS children
            ON
                parents.ID = children.post_parent
            WHERE
                parents.post_type = '{$this->post_type}'
            AND
                parents.post_status IN ('publish', 'future', 'draft', 'pending', 'private')
            AND
                children.post_status IN ('publish', 'future', 'draft', 'pending', 'private')
            AND
                parents.post_parent = {$this->post_id}
            ORDER BY
                parents.menu_order ASC, parents.post_title ASC
        " );

        foreach ( $posts as $post ) {
            $folders[] = new Wicked_Folders_Post_Hierarchy_Dynamic_Folder( array(
                    'id' 		=> 'dynamic_hierarchy_' . $post->ID,
                    'name' 		=> $post->post_title,
                    'parent' 	=> 'dynamic_hierarchy_' . $this->post_id,
                    'post_type' => $this->post_type,
                    'taxonomy' 	=> $this->taxonomy,
                    'type'      => 'Wicked_Folders_Post_Hierarchy_Dynamic_Folder',
                )
            );
        }

        return $folders;
    }

    public function fetch() {
        $this->parse_id();

        $post = get_post( $this->post_id );

        $children = get_posts( array(
            'post_type'         => $post->post_type,
            'posts_per_page'    => 1,
            'post_parent'       => $post->ID,
        ) );

        // A post hierarchy folder should only 'exist' if it contains children
        if ( empty( $children ) ) return false;

        if ( $post ) {
            $this->name         = $post->post_title;
            $this->parent       = 'dynamic_hierarchy_' . $post->post_parent;
            $this->post_type    = $post->post_type;
            $this->taxonomy     = Wicked_Folders::get_tax_name( $post->post_type );
            $this->type         = 'Wicked_Folders_Post_Hierarchy_Dynamic_Folder';
        }

        return true;
    }

    public function get_ancestor_ids( $id = false ) {
        $ancestors = array();

        if ( false === $id ) {
            $id = $this->id;
        }

        if ( 'dynamic_hierarchy_0' != $id ) {
            $post_id            = ( int ) substr( $id, 18 );
            $parent             = 'dynamic_hierarchy_' . wp_get_post_parent_id( $post_id );
            $ancestors[]        = $parent;
            $parent_ancestors   = $this->get_ancestor_ids( $parent );
            $ancestors          = array_merge( $ancestors, $parent_ancestors );
        } else {
            $ancestors[] = 'dynamic_root';
        }

        return $ancestors;
    }
}
