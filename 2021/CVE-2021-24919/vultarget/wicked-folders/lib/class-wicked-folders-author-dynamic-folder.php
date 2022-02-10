<?php

/**
 * Represents a dynamically-generated author folder.
 */
class Wicked_Folders_Author_Dynamic_Folder extends Wicked_Folders_Dynamic_Folder {

    private $user_id = false;

    public function __construct( $args ) {
        parent::__construct( $args );
    }

    public function pre_get_posts( $query ) {

        $this->parse_id();

        if ( $this->user_id ) {
            $query->set( 'author', $this->user_id );
        }

    }

    /**
     * Parses the folder's ID to determine author ID that the folder should
     * filter by.
     */
    private function parse_id() {

        $this->user_id = ( int ) substr( $this->id, 15 );

    }

}
