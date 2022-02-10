<?php
/* wp-admin-menu-classes.php

Classes to encapsulate access to admin menu global arrays $menu and $submenu

See:
  -- http://core.trac.wordpress.org/ticket/12718
  -- http://core.trac.wordpress.org/ticket/11517

version: 1.05 - Renamed "delete_*" functions to "remove_*" (Oct 6 2010)
version: 1.04 - Another significant update. Attempted to make PHP 4.x compatible (Sept 30 2010)
                Added hookname property to both section and item
                Renamed property with list of items to be "items" instead of "submenus"
version: 1.03 - Major Rewrite over v1.02. Designed for hopeful inclusion within WordPress v3.1 (Sept 29 2010)
version: 1.02 - Added remove_admin_menu_item(), changed "find-by"=="title" to search on RegEx (Sept 27 2010)

Designed for hopeful inclusion within WordPress v3.1

  Examples of use:
  // This example creates one menu in place of Dashboard called "My Menu", adds a few things, and removes all else.
  // This example assumes this might only be done for end users, not administrators

  $dashboard = rename_admin_menu_section('Dashboard','My Menu');

//  // Alternate approach
//  remove_admin_menu_item('index.php');                  // Dashboard
//  $dashboard = add_admin_menu_section(array(
//    'title' => 'My Menu',
//    'slug'  => 'index.php',
//  ));

  remove_admin_menu_item($dashboard,'index.php');         // Dashboard
  remove_admin_menu_item($dashboard,'update-core.php');   // Updates

  $movies = "edit.php?post_type=movie";
  copy_admin_menu_item($dashboard,$movies);
  $movie_genre = 'edit-tags.php?taxonomy=movie-genre&post_type=movie';
  copy_admin_menu_item($dashboard,$movies,$movie_genre);
  rename_admin_menu_item($dashboard,$movie_genre,'Movie Genre');
  remove_admin_menu_item($movies);
  remove_admin_menu_item($movies,$movie_genre);
  remove_admin_menu_item($movies,'post-new.php?post_type=movie');
  remove_admin_menu_section($movies);

  $actors = "edit.php?post_type=actor";
  copy_admin_menu_item($dashboard,$actors);
  remove_admin_menu_item($actors);
  remove_admin_menu_item($actors,'post-new.php?post_type=actor');
  //remove_admin_menu_section($actors);

  rename_admin_menu_item($dashboard,'Pages','Other Pages');

  remove_admin_menu_section('edit.php');                  // Posts
  remove_admin_menu_section('upload.php');                // Media
  remove_admin_menu_section('link-manager.php');          // Links
  remove_admin_menu_section('edit-comments.php');         // Comments
  remove_admin_menu_section('edit.php?post_type=page');   // Pages
  remove_admin_menu_section('plugins.php');               // Plugins
  remove_admin_menu_section('themes.php');                // Appearance
  remove_admin_menu_section('users.php');                 // Users
  remove_admin_menu_section('tools.php');                 // Tools
  remove_admin_menu_section('options-general.php');       // Settings

*/


function add_admin_menu_section($section,$args=array()) {
	$new_section = new WP_AdminMenuSection();
	return $new_section->initialize($section,$args);
}

function add_admin_menu_item($section,$item,$args=array()) {
	$section = $temp = get_admin_menu_section($section);
	$item = ($section ? $section->add_item($item,$args) : false);
	return $item;
}
function remove_admin_menu_item($section,$item=false) {
	if (!$item)
		$item = $section; // These slugs are often identical
	$section = get_admin_menu_section($section);
	if ($section)
		$section->remove_item($item);
}
function remove_admin_menu_section($section) {
	$section = get_admin_menu_section($section);
	if ($section)
		$section->delete();
}
function rename_admin_menu_section($section,$new_title) {
	$section = get_admin_menu_section($section);
	if ($section)
		$section->set_title($new_title);
	return $section;
}
function rename_admin_menu_item($section,$item,$new_title) {
	$item = get_admin_menu_item($section,$item);
	if ($item)
		$item->set_title($new_title);
	return $item;
}
function swap_admin_menu_sections($from_section,$to_section) {
	$from_section = get_admin_menu_section($from_section);
	if ($from_section)
		$from_section->swap_with($to_section);
	return $section;
}
function get_admin_menu_section($section) {
	if (!is_a($section,'WP_AdminMenuSection'))
		$section = new WP_AdminMenuSection($section);
	return $section;
}
function get_admin_menu_item($section,$item) {
	$section = get_admin_menu_section($section);
	if (!is_a($item,'WP_AdminMenuItem')) {
		$item = $section->find_item($item);
	}
	return $item;
}
function copy_admin_menu_item($to_section,$from_section,$item=false) {
	if (!$item)
		$item = $from_section; // These slugs are often identical
	$to_section = get_admin_menu_section($to_section);
	$item = get_admin_menu_item($from_section,$item);
	add_admin_menu_item($to_section,$item);
}

class WP_AdminMenuSection {
	var $index = 0;
	var $items=array();
	var $hookname;
	function __construct($section=false) {
		$this->WP_AdminMenuSection($section);
	}
	function WP_AdminMenuSection($section=false) {
		if ($section) { // $section=false when we need to add one. Static methods would be nicer.
			if (is_a($section,'WP_AdminMenuSection')) {
				$this->index =      &$section->index;
				$this->items =      &$section->items;
				$this->hookname =   &$section->hookname;
			} else {
				global $menu;
				global $submenu;
				$found = false;
				foreach($menu as $index => $section_array) {
					if ($section==$section_array[2] ||                      // Find by File/Slug
							$section==$section_array[0] ||                      // Find by Title
							@preg_match("#^{$section}$#",$section_array[0])) {  // Find by Title via RegEx
						$found = $index;
					} else {
						continue;
					}
					break;
				}
				$this->index = $found;
				if ($found)
					$this->refresh();
			}
		}
	}
	function initialize($section,$args=array()) {
		$args = wp_parse_args($args,array(
			'where' => 'bottom' // top or bottom
		));
		$section = wp_parse_args($section,array(
			'title'       => false,
			'slug'        => false,
			'page_title'  => false,
			'icon_src'    => false,
			'function'    => false,
			'capability'  => 'edit_posts',
			));
		if (!$section['page_title'])
			$section['page_title'] = strip_tags($section['title']);
		switch ($args['where']) {
			case 'bottom':
				$this->hookname = add_menu_page(
					$section['page_title'],
					$section['title'],
					$section['capability'],
					$section['slug'],
					$section['function'],
					$section['icon_src'] );
		    break;
			case 'after':     // TODO: Implement this
			case 'before':    // TODO: Implement this
			case 'top':       // TODO: Implement this
			default:
		    wp_die("where='{$args['where']}' not yet implemented in WP_AdminMenuSection->initialize().");
		}
		$this->refresh($section['slug']);
		return $this;
	}

	function add_item($item,$args=array()) {
        $last_index = '';
		$args = wp_parse_args($args,array(
			'where' => 'bottom' // top or bottom
		));
		global $submenu;
		$slug = $this->get_slug();
		if (!isset($submenu[$slug]))
			$submenu[$slug] = array();
		$item_list = &$submenu[$slug];
		if (is_a($item,'WP_AdminMenuItem')) {
			$item_type = OBJECT;
			$item_array = $item->get_array();
		} else if ($this->is_item_array($item)) {
			$item_type = ARRAY_N;
			$item_array = $item;
		} else {
			$item_type = ARRAY_A;
			$item = wp_parse_args($item,array(
				'title'       => false,
				'slug'        => false,
				'page_title'  => false,
				'function'    => false,
				'capability'  => 'edit_posts',
				));
			if (!$item['page_title'])
				$item['page_title'] = $item['title'];
			$item['hookname'] = add_submenu_page( $slug, $item['page_title'], $item['title'], $item['capability'], $item['slug'], $item['function']);
			if ($args['where']!='bottom')                                // If 'bottom', do nothing more./
				$item_array = array_pop($item_list);
		}
		switch ($args['where']) {
			case 'top':                                       // No, array_unshift() won't do this instead.
				$last_index = $this->get_last_item_index()+5;   // Menus typically go in increments of 5.
				$item_list[$last_index] = null;                 // Create a placeholder at end to allow us to shift them all up
				$item_indexes = array_keys($item_list);
				$new_item_list = array();
				$new_item_list[$item_indexes[0]] = $item_array; // Finally add the item array to the beginning.
				for($i = 1; $i<count($item_indexes); $i++) {
					$new_item_list[$item_indexes[$i]] = $item_list[$item_indexes[$i-1]];
				}
				$item_list = $new_item_list;
		    break;
			case 'bottom':
				if ($item_type != ARRAY_A) {
					// If it's an associative array we need to add it, otherwise it's already part of the menu.
					$last_index = $this->get_last_item_index()+5;
					$item_list[$last_index] = $item_array;
				}
				break;
			case 'after':     // TODO: Implement this
			case 'before':    // TODO: Implement this
			default:
		    wp_die("where='{$args['where']}' not yet implemented in WP_AdminMenuSection->add_item().");
		}
		$this->refresh();
		return new WP_AdminMenuItem($slug,$last_index);
	}
	function rename_item($item,$new_title) {
		$this->find_item($item)->set_title($new_title);
	}
	function swap_with($section) {
		$with = get_admin_menu_section($section);
		global $menu;
		$temp = $menu[$this->index];
		$menu[$this->index] = $menu[$with->index];
		$menu[$with->index] = $temp;
		$temp = $this->index;
		$this->index = $with->index;
		$with->index = $temp;
		$temp = $this->items;
		$this->items = $with->items;
		$with->items = $temp;
	}
	function delete() {
		global $submenu;
		unset($submenu[$this->get_slug()]);
		global $menu;
		unset($menu[$this->index]);
	}
	function remove_item($item) {
		global $submenu;
		$index = $this->find_item_index($item);
		$item = $this->find_item($index);
		unset($submenu[$item->parent_slug][$index]);
		unset($this->items[$index]);

	}
	function find_item($item) {
		if (!is_a($item,'WP_AdminMenuItem')) {
			$index = (is_numeric($item) ? $item : $this->find_item_index($item));
			$item = ($index!==false ? $this->items[$index] : false);
		}
		return $item;
	}
	function find_item_index($item) {
		if (is_a($item,'WP_AdminMenuSection')) {
			wp_die('Unexpected: WP_AdminMenuSection passed when WP_AdminMenuItem or WP_AdminMenuItem->slug expected.');
		}
		if (is_a($item,'WP_AdminMenuItem')) {
			$item = $item->get_slug();
		}
		foreach($this->items as $index => $item_obj) {
			if ($item==$item_obj->get_slug()) {
				break;
			} else if (preg_match("#^{$item}$#",$item_obj->get_title())) {
				break;
			} else {
				$index = false;
			}
		}
		return $index;
	}
	function get_title() {
		return $GLOBALS['menu'][$this->index][0];
	}
	function set_title($new_title) {
		$GLOBALS['menu'][$this->index][0] = $new_title;
	}
	function get_capability() {
		return $GLOBALS['menu'][$this->index][1];
	}
	function set_capability($new_capability) {
		$GLOBALS['menu'][$this->index][1] = $new_capability;
	}
	function get_file() { // 'slug' & 'file' are synonyms for admin menu
		return $GLOBALS['menu'][$this->index][2];
	}
	function set_file($new_file) {
		$GLOBALS['menu'][$this->index][2] = $new_file;
	}
	function get_slug() { // 'slug' & 'file' are synonyms for admin menu
		return $this->get_file();
	}
	function set_slug($new_slug) {
		$this->set_file($new_slug);
	}
	function get_unused() {
		return $GLOBALS['menu'][$this->index][3];
	}
	function set_unused($new_unused) {
		$GLOBALS['menu'][$this->index][3] = $new_unused;
	}
	function get_class() {
		return $GLOBALS['menu'][$this->index][4];
	}
	function set_class($new_class) {
		$GLOBALS['menu'][$this->index][4] = $new_class;
	}
	function get_id() {
		return $GLOBALS['menu'][$this->index][5];
	}
	function set_id($new_id) {
		$GLOBALS['menu'][$this->index][5] = $new_id;
	}
	function get_icon_src() {
		return $GLOBALS['menu'][$this->index][6];
	}
	function set_icon_src($new_icon_src) {
		$GLOBALS['menu'][$this->index][6] = $new_icon_src;
	}
	function is_item_array($item) {
		$is_item_array = true;
		if (!is_array($item)) {
			$is_item_array = false;
		} else {
			foreach(array_keys($item) as $key) {
				if (!is_numeric($key)) {
					$is_item_array = false;
					break;
				}
			}
		}
		return $is_item_array;
	}
	function get_last_item_index() {
		global $submenu;
		$slug = $this->get_slug();
		return ($slug ? end(array_keys($submenu[$slug])) : false);
	}
	function refresh($section=false) { // This in case something external changes the submenu indexes
		if (!$section) {
			$this->items = $this->get_items($this);
		} else {
			$this->items = $this->get_items($section);
			$this->hookname = get_plugin_page_hookname($section,'');
			$this->index = $this->find_index($section);
		}
	}
	function find_index($section) {
		global $menu;
		$found = false;
		foreach($menu as $index => $section_array) {
			if ($section==$section_array[2]) {
				$found = true;
				break;
			}
		}
		return ($found ? $index : false);
	}
	function get_items($section) {
		if (is_a($section,'WP_AdminMenuSection'))
			$slug = $section->get_slug();
		else if (is_string($section))
			$slug = $section;
		global $submenu;
		$items = array();
		if (isset($submenu[$slug])) {
			foreach($submenu[$slug] as $index => $item) {
				$items[$index] = new WP_AdminMenuItem($slug,$index);
			}
		}
		return $items;
	}
}
class WP_AdminMenuItem {
	var $index;
	var $parent_slug;
	var $hookname;
	function __construct($parent_slug,$slug) {
		$this->WP_AdminMenuItem($parent_slug,$slug);
	}
	function WP_AdminMenuItem($parent_slug,$slug) {
		$this->parent_slug = $parent_slug;
		if (is_numeric($slug)) {  // $slug is designed for future non-legacy use
			$this->index = $slug;
			$slug = $this->get_slug();
		} else {
			$section = new WP_AdminMenuSection($parent_slug);
			$item = $section->find_item($slug);
			if ($item)
				$this->index = $item->index;
		}
		$this->hookname = get_plugin_page_hookname($slug,$parent_slug);
	}
	function get_array() { // Only here because WP_AdminMenuSection really needed it.
		return $GLOBALS['submenu'][$this->parent_slug][$this->index];
	}
	function get_title() {
		return $GLOBALS['submenu'][$this->parent_slug][$this->index][0];
	}
	function set_title($new_title) {
		$GLOBALS['submenu'][$this->parent_slug][$this->index][0] = $new_title;
	}
	function get_capability() {
		return $GLOBALS['submenu'][$this->parent_slug][$this->index][1];
	}
	function set_capability($new_capability) {
		$GLOBALS['submenu'][$this->parent_slug][$this->index][1] = $new_capability;
	}
	function get_slug() { // 'slug' & 'file' are synonyms for admin menu
		return html_entity_decode($GLOBALS['submenu'][$this->parent_slug][$this->index][2]);
	}
	function set_slug($new_slug) {
		$GLOBALS['submenu'][$this->parent_slug][$this->index][2] = $new_slug;
	}
	function get_file() { // 'slug' & 'file' are synonyms for admin menu
		return $this->get_slug();
	}
	function set_file($new_file) {
		$this->set_slug($new_slug);
	}
}
