<?php

/**
 * Dynamic folder that displays items that haven't been assigened to any folders.
 */
class Wicked_Folders_Unassigned_Dynamic_Folder extends Wicked_Folders_Dynamic_Folder {

    private $extension = false;

    public function __construct( $args ) {
        parent::__construct( $args );

        $this->show_item_count = true;
        $this->assignable = true;
    }

    public function pre_get_posts( $query ) {

        $folder_ids = get_terms( $this->taxonomy, array( 'fields' => 'ids', 'hide_empty' => false ) );

        $tax_query = array(
            array(
                'taxonomy' 	=> $this->taxonomy,
                'field' 	=> 'term_id',
                'terms' 	=> $folder_ids,
                'operator' 	=> 'NOT IN',
            ),
        );

        $query->set( 'tax_query', $tax_query );

    }

}
