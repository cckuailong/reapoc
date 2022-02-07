<?php

class DLM_LU_Content_Upgrader {

	/** @var array temporary id map so we don't query the same legacy id multiple times per post/page */
	private $id_map = array();

	private $download_queue;

	public function __construct() {
		$this->download_queue = new DLM_LU_Download_Queue();
	}

	/**
	 * Regex callback. Use match parts to construct new download shortcode.
	 *
	 * @param $m array
	 *
	 * @return string
	 */
	public function preg_replace_cb( $m ) {

		// check map, if not in map, fetch
		if ( ! isset( $this->id_map[ $m[3] ] ) ) {
			$this->id_map[ $m[3] ] = $this->download_queue->get_new_id( $m[3] );
		}

		return "[download ".$m[1]."id=".$m[2].$this->id_map[ $m[3] ].$m[2].$m[4]."]";
	}

	/**
	 * Upgrade item
	 *
	 * @param $item_id int
	 *
	 * @return bool
	 */
	public function upgrade_item( $item_id ) {
		global $wpdb;

		// make sure item id is int
		$item_id = absint( $item_id );

		// queue item
		$queue = new DLM_LU_Content_Queue();

		// mark content item as upgrading
		$queue->mark_upgrading( $item_id );

		// get 'post'
		$post = get_post( $item_id );

		// content
		$content = $post->post_content;

		// generate new content
		$regex = "`\[download ([^\]]*)(?:id=([\"|']{0,1})([0-9]+)(?:[\"|']{0,1}))([^\]]*)\]`";
		$new_content = preg_replace_callback( $regex, array( $this, 'preg_replace_cb' ), $content );

		// update content in database
		$wpdb->update( $wpdb->posts, array( 'post_content' => $new_content ), array('ID'=>$post->ID), array('%s'), array('%d') );

		// mark content item as upgraded
		$queue->mark_upgraded( $item_id );

		return true;
	}

}