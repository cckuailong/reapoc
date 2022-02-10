<?php

/**
 * Represents a folder object that is represented as a taxonomy term.
 */
class Wicked_Folders_Term_Folder extends Wicked_Folders_Folder {

    public function __construct( $args ) {
        parent::__construct( $args );

        $this->show_item_count = true;
    }

    public function ancestors() {
        return get_ancestors( $this->id, $this->taxonomy, 'taxonomy' );
    }

    public function fetch_posts() {

        return get_posts( array(
            'post_type'         => $this->post_type,
            'orderby'           => 'title',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
            'tax_query' => array(
                array(
                    'taxonomy'          => $this->taxonomy,
                    'field'             => 'term_id',
                    'terms'             => ( int )$this->id,
                    'include_children'  => false,
                ),
            ),
        ) );

    }

    /**
     * Returns a new folder instance containing the same objects as the current
     * instance.
     *
     * @param bool $clone_children
     *  If true, clones all descendant folders as well.
     *
     * @param string $parent
     *  Optional parent ID to clone the folder to. If omitted, the folder will
     *  be cloned to the current parent.
     *
     * @return array
     *  Array of Wicked_Folders_Term_Folder objects.
     */
    public function clone_folder( $clone_children = false, $parent = false ) {
        global $wpdb;

        $folders                = array();
        $folder                 = clone $this;
        $name_index             = 0;
        $unique_name_generated  = false;
        $sort_key               = '_wicked_folder_order__' . $this->taxonomy . '__' . $this->id;

        if ( false !== $parent ) $folder->parent = $parent;

        // Get folder siblings so we can generate a unique name
        if ( version_compare( get_bloginfo( 'version' ), '4.5.0', '<' ) ) {
            $siblings = get_terms( $this->taxonomy, array(
                'hide_empty' 	=> false,
                'parent'        => $folder->parent,
                'fields'        => 'names',
            ) );
        } else {
            $siblings = get_terms( array(
                'taxonomy' 		=> $this->taxonomy,
                'hide_empty' 	=> false,
                'parent'        => $folder->parent,
                'fields'        => 'names',
            ) );
        }

        // Generate a unique name
        while ( ! $unique_name_generated ) {
            if ( ! in_array( $folder->name, $siblings ) ) {
                $unique_name_generated = true;
                break;
            }

            $name_index++;

            $folder->name = $this->name . ' ' . sprintf( __( '(Copy %1$d)', 'wicked-folders' ), $name_index );
        }

        // Create a new folder term
        $term = wp_insert_term( $folder->name, $folder->taxonomy, array(
            'parent' => $folder->parent,
        ) );

        if ( is_wp_error( $term ) ) {
            throw new Exception( $term->get_error_message() );
        }

        // Store owner ID for new folder
        add_term_meta( $term['term_id'], 'wf_owner_id', $folder->owner_id );

        // Update the new folder's ID
        $folder->id = ( string ) $term['term_id'];

        $cloned_folder_sort_key = '_wicked_folder_order__' . $folder->taxonomy . '__' . $folder->id;

        // Get the IDs of objects assigned to the current folder
        $posts_ids = $wpdb->get_col(
            $wpdb->prepare(
                "
                    SELECT object_id FROM {$wpdb->term_relationships} AS wf_term_relationships
                    INNER JOIN {$wpdb->term_taxonomy} AS wf_term_taxonomy ON wf_term_relationships.term_taxonomy_id = wf_term_taxonomy.term_taxonomy_id
                    WHERE wf_term_taxonomy.taxonomy = %s AND wf_term_relationships.term_taxonomy_id = %d
                ", $this->taxonomy, $this->id
            )
        );

        // Assign the posts in the current folder to the new folder
        foreach ( $posts_ids as $id ) {
            $result = wp_set_object_terms( $id, ( int ) $folder->id, $folder->taxonomy, true );
        }

        // Copy the existing folder's sort order
        $wpdb->query( "
            INSERT INTO
                {$wpdb->prefix}postmeta (post_id, meta_key, meta_value)
            SELECT
                pm.post_id, '{$cloned_folder_sort_key}', pm.meta_value FROM {$wpdb->prefix}postmeta pm WHERE pm.meta_key = '{$sort_key}'
        " );

        // Add the cloned folder to the array of cloned folders
        $folders[] = $folder;

        if ( $clone_children ) {
            // Get ID's of direct child folders
            if ( version_compare( get_bloginfo( 'version' ), '4.5.0', '<' ) ) {
                $children = get_terms( $this->taxonomy, array(
                    'hide_empty' 	=> false,
                    'parent'        => $this->id,
                ) );
            } else {
                $children = get_terms( array(
                    'taxonomy' 		=> $this->taxonomy,
                    'hide_empty' 	=> false,
                    'parent'        => $this->id,
                ) );
            }

            foreach ( $children as $term ) {
                $child_folder = Wicked_Folders::get_folder( $term->term_id, $this->post_type, $term->taxonomy );

                // Clone each child folder
                $cloned_child_folders = $child_folder->clone_folder( $clone_children, $folder->id );

                // Merge cloned child folders into cloned folders array
                $folders = array_merge( $folders, $cloned_child_folders );
            }
        }

        return $folders;
    }

    /**
     * Generates a unique slug to facilitate creating folders with the same name.
     *
     * @param string $name
     *  The name of the folder to generate a slug for.
     *
     * @param string $taxonomy
     *  The taxonomy to search for name collisions in.
     *
     * @return string
     *  A unique slug.
     */
    public static function generate_unique_slug( $name, $taxonomy ) {
        $unique     = false;
        $slug       = sanitize_title( $name );
        $base_slug  = $slug;
        $index      = 0;

        while ( ! $unique ) {
            $term = get_term_by( 'slug', $slug, $taxonomy );

            if ( false == $term ) {
                $unique = true;
            } else {
                $index++;
                $slug = "{$base_slug}-{$index}";
            }
        }

        return $slug;
    }
}
