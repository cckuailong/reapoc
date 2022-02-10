<?php

/**
 * Represents a dynamically-generated term folder.
 */
class Wicked_Folders_Term_Dynamic_Folder extends Wicked_Folders_Dynamic_Folder {

    public $term_id = false;

    public function __construct( $args ) {
        parent::__construct( $args );
    }

    public function pre_get_posts( $query ) {

        $this->parse_id();

        if ( $this->taxonomy && $this->term_id ) {
            $tax_query = ( array ) $query->get( 'tax_query' );
            $tax_query[] = array(
                'taxonomy'  => $this->taxonomy,
                'terms'     => $this->term_id,
            );
            $query->set( 'tax_query', $tax_query );
        }

    }

    /**
     * Parses the folder's ID to determine the taxonomy and term ID to filter by.
     */
    private function parse_id() {

        $id = substr( $this->id, 13 );

        $id = explode( '__id__', $id );

        if ( isset( $id[0] ) ) $this->taxonomy = $id[0];
        if ( isset( $id[1] ) ) $this->term_id  = $id[1];

    }

    public function jsonSerialize() {
        $data = parent::jsonSerialize();

        $data['termId'] = $this->term_id;

        return $data;
    }
}
