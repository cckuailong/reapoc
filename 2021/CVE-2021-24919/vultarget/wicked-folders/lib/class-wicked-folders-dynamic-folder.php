<?php

/**
 * Represents a dynamic folder.
 */
abstract class Wicked_Folders_Dynamic_Folder extends Wicked_Folders_Folder {

    public $movable = false;
    
    public $editable = false;

    public $assignable = false;

    public function __construct( $args ) {
        parent::__construct( $args );
    }

    public abstract function pre_get_posts( $query );

}
