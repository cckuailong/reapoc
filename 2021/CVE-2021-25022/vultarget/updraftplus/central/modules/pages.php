<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

// Load the posts command class since we're going to be extending it for our page module service/command
// class in order to minimize redundant shareable methods.
if (!class_exists('UpdraftCentral_Posts_Commands')) require_once('posts.php');

/**
 * Handles Pages Commands
 */
class UpdraftCentral_Pages_Commands extends UpdraftCentral_Posts_Commands {

	protected $post_type = 'page';
}
