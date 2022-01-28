<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Walker class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_walker extends Walker
{
    public $tree_type = 'category';
    public $db_fields = array(
        'parent' => 'parent',
        'id'     => 'term_id',
    );

    public $mec_id = array();
    public $mec_include = array();

    /**
     * Constructor method
     * @param array $params
     * @author Webnus <info@webnus.biz>
     */
    public function __construct($params = array())
    {
        $this->mec_id = (isset($params['id']) ? $params['id'] : '');
        $this->mec_include = (isset($params['include']) ? $params['include'] : array());
    }

    /**
     * Starts the list before the elements are added.
     *
     * @see Walker:start_lvl()
     *
     * @since 2.5.1
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent  = str_repeat("\t", $depth);
        $output .= "$indent<ul class='children'>\n";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 2.5.1
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent  = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 2.5.1
     *
     * @param string  $output   Used to append additional content (passed by reference).
     * @param WP_Term $category The current term object.
     * @param int     $depth    Depth of the term in reference to parents. Default 0.
     * @param array   $args     An array of arguments. @see wp_terms_checklist()
     * @param int     $id       ID of the current term.
     */
    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
    {
        // Term is not Included
        if(is_array($this->mec_include) and count($this->mec_include) and !in_array($category->term_id, $this->mec_include)) return;

        if(empty($args['taxonomy'])) $taxonomy = 'category';
        else $taxonomy = $args['taxonomy'];

        if('category' === $taxonomy) $name = 'post_category';
        else $name = 'tax_input[' . $taxonomy . ']';

        $args['popular_cats'] = !empty($args['popular_cats']) ? array_map('intval', $args['popular_cats']) : array();
        $class = in_array($category->term_id, $args['popular_cats'], true) ? ' class="popular-category"' : '';
        $args['selected_cats'] = !empty($args['selected_cats']) ? array_map('intval', $args['selected_cats']) : array();

        $is_selected = in_array($category->term_id, $args['selected_cats'], true);
        $is_disabled = !empty($args['disabled']);

        $output .= "\n<option value='{$category->term_id}' id='{$taxonomy}-{$this->mec_id}-{$category->term_id}'$class>" .
            esc_html__(apply_filters('the_category', $category->name, '', '')) . '</option>';
    }

    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     *
     * @since 2.5.1
     *
     * @param string  $output   Used to append additional content (passed by reference).
     * @param WP_Term $category The current term object.
     * @param int     $depth    Depth of the term in reference to parents. Default 0.
     * @param array   $args     An array of arguments. @see wp_terms_checklist()
     */
    public function end_el(&$output, $category, $depth = 0, $args = array())
    {
        $output .= "";
    }

        public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element ) {
            return;
        }
 
        $id_field = $this->db_fields['id'];
        $id       = $element->$id_field;
 
        // Display this element.
        $this->has_children = ! empty( $children_elements[ $id ] );
        if ( isset( $args[0] ) && is_array( $args[0] ) ) {
            $args[0]['has_children'] = $this->has_children; // Back-compat.
        }
 
        
 
        $this->start_el( $output, $element, $depth, ...array_values( $args ) );
        
        // End this element.
        $this->end_el( $output, $element, $depth, ...array_values( $args ) );
    }

    public function walk( $elements, $max_depth, ...$args ) {
        $output = '<select multiple="multiple">';
        // Invalid parameter or nothing to walk.
        if ( $max_depth < -1 || empty( $elements ) ) {
            return $output;
        }
        
        $parent_field = $this->db_fields['parent'];
        
        foreach ( $elements as $e ) {
            $this->display_element( $e, $empty_array, 1, 0, $args, $output );
        }
        $output .= '</select>';
        
        return $output;

    }
}