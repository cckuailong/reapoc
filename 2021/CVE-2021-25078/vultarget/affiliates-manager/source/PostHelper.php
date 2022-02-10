<?php
/**
 * @author John Hargrove
 * 
 * Date: May 23, 2010
 * Time: 6:00:28 PM
 *
 * @TODO most of this can probably go away
 */

class WPAM_PostHelper
{
	public function __construct() { }

	public function postExists($postName)
	{
		$database = new WPAM_Data_DataAccess();
		return $database->getWordPressRepository()->postExists($postName);
	}

	public function getPostId($postName)
	{
		$database = new WPAM_Data_DataAccess();
		return $database->getWordPressRepository()->getPostId($postName);
	}

	public function getPost($postId)
	{
		return get_post($postId);
	}

	public function createPage($name, $title, $content, $parentId = NULL)
	{
		return $this->createPost('page', $name, $title, $content, $parentId);
	}

	private function createPost($postType, $name, $title, $content, $parentId = NULL)
	{
		$post = array(
			'post_title' => $title,
			'post_name' => $name,
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_content' => $content,
			'post_status' => 'publish',
			'post_type' => $postType
		);

		if ($parentId !== NULL)
			$post['post_parent'] = $parentId;

		$postId = wp_insert_post($post);

		return $postId;
	}
}
