<?php

if (!class_exists('Tax_CTP_Filter')) {

    class Tax_CTP_Filter {
    public  $cpt;
        function __construct($cpt = array()) {
            $this->cpt = $cpt;
            add_action('restrict_manage_posts', array($this, 'event_restrict_manage_posts'));
            add_filter('parse_query', array($this, 'event_table_filter'));
            add_filter('admin_head',array($this,'hide_month_filter'));
        }
        
        public function hide_month_filter() {
            global $typenow;
            $types = array_keys($this->cpt);
            if (in_array($typenow, $types)) {
                add_filter('months_dropdown_results','__return_empty_array');
            }
        }

        public function event_restrict_manage_posts() {
            global $typenow;
            $types = array_keys($this->cpt);
            if (in_array($typenow, $types)) {
                $filters = $this->cpt[$typenow];
                foreach ($filters as $tax_slug) {
                    if($tax_slug==ECWD_PLUGIN_PREFIX . '_event_category' || $tax_slug==ECWD_PLUGIN_PREFIX . '_event_tag'){

                        $tax_obj = get_taxonomy($tax_slug);
                        $tax_name = $tax_obj->labels->name;
                        echo "<select name='" . strtolower($tax_slug) . "' id='" . strtolower($tax_slug) . "' class='postform'>";
                        echo "<option value=''>All $tax_name</option>";
                        $this->generate_taxonomy_options($tax_slug, 0, 0, (isset($_GET[strtolower($tax_slug)])) ? sanitize_text_field($_GET[strtolower($tax_slug)]) : null);
                    
                    }  else {
                        
                        switch ($tax_slug) {
                            case ECWD_PLUGIN_PREFIX . '_calendar':
                                $tax_name = "Calendars";
                                break;
                            case ECWD_PLUGIN_PREFIX . '_organizer':
                                $tax_name = "Organizers";
                                break;
                            case ECWD_PLUGIN_PREFIX . '_venue':
                                $tax_name = "Venues";
                                break;
                            
                            default :
                                 break;
                        }
                        $tax_obj = get_posts(array(
                                    'post_type' => $tax_slug,
                                    'showposts' => -1,
                                    'order' => 'ASC'
                        ));
                        $selected = isset($_GET[strtolower($tax_slug). '_filter']) ? sanitize_text_field($_GET[strtolower($tax_slug). '_filter']) : null;
                        echo "<select name='" . strtolower($tax_slug) . "_filter' id='" . strtolower($tax_slug) . "_filter' class='postform'>";
                        echo "<option value=''>All $tax_name</option>";
                        foreach ($tax_obj as $term) {
                            echo '<option value=' . $term->ID, $selected == $term->ID ? ' selected="selected"' : '', '>' . $term->post_title . '</option>';
                        }
                    }
                    echo "</select>";
                }
                echo "<div style='display:inline-block;'>";
                _e('From', 'event-calendar-wd');?>
                <input type="text" style="width: 90px"
                       id="<?php echo ECWD_PLUGIN_PREFIX; ?>_date_from_filter"
                       name="<?php echo ECWD_PLUGIN_PREFIX; ?>_date_from_filter"
                       class="<?php echo ECWD_PLUGIN_PREFIX; ?>_event_date"
                       value="<?php echo isset($_GET[ECWD_PLUGIN_PREFIX.'_date_from_filter'])? htmlentities(sanitize_text_field($_GET[ECWD_PLUGIN_PREFIX.'_date_from_filter'])): ''; ?>"  autocomplete="off"/>
                <?php
                 _e('To', 'event-calendar-wd');?>
                <input type="text" style="width: 90px"
                       id="<?php echo ECWD_PLUGIN_PREFIX; ?>_date_to_filter"
                       name="<?php echo ECWD_PLUGIN_PREFIX; ?>_date_to_filter"
                       class="<?php echo ECWD_PLUGIN_PREFIX; ?>_event_date"
                       value="<?php echo isset($_GET[ECWD_PLUGIN_PREFIX.'_date_to_filter'])? htmlentities(sanitize_text_field($_GET[ECWD_PLUGIN_PREFIX.'_date_to_filter'])): ''; ?>"   autocomplete="off"/>
                </div>
                <a href="<?php echo admin_url( 'edit.php?post_type=ecwd_event' );?>" class="button" ><?php _e('Reset','event-calendar-wd')?></a>
                <?php
            }
        }

        public function generate_taxonomy_options($tax_slug, $parent = '', $level = 0, $selected = null) {
            $args = array('show_empty' => 1);
            if (!is_null($parent)) {
                $args = array('parent' => $parent);
            }
            $terms = get_terms($tax_slug, $args);
            $tab = '';
            for ($i = 0; $i < $level; $i++) {
                $tab.='--';
            }
            foreach ($terms as $term) {
                echo '<option value=' . $term->slug, $selected == $term->slug ? ' selected="selected"' : '', '>' . $tab . $term->name . ' (' . $term->count . ')</option>';
                $this->generate_taxonomy_options($tax_slug, $term->term_id, $level + 1, $selected);
            }
        }

        public function event_table_filter($query) {
            global $typenow;
            $types = array_keys($this->cpt);
                if (is_admin() AND isset($query->query['post_type']) AND  in_array($query->query['post_type'], $types) && in_array($typenow, $types)) {
                $qv = &$query->query_vars;
                $qv['meta_query'] = array();
             
                    if (!empty($_GET[ECWD_PLUGIN_PREFIX . '_calendar_filter'])) {
                        $qv['meta_query'][] = array(
                            'key'     => ECWD_PLUGIN_PREFIX . '_event_calendars',
                            'value'   => serialize( strval( sanitize_text_field($_GET[ECWD_PLUGIN_PREFIX . '_calendar_filter']) ) ),
                            'compare' => 'LIKE'
                        );
                    }
                    if (!empty($_GET[ECWD_PLUGIN_PREFIX . '_organizer_filter'])) {
                        $qv['meta_query'][] = array(
                            'key'     => ECWD_PLUGIN_PREFIX . '_event_organizers',
                            'value'   => serialize( strval( sanitize_text_field($_GET[ECWD_PLUGIN_PREFIX . '_organizer_filter']) ) ),
                            'compare' => 'LIKE'
                        );
                    }
                    if (!empty($_GET[ECWD_PLUGIN_PREFIX.'_date_from_filter'])) {
                        $qv['meta_query'][] = array(
                            'key'     => ECWD_PLUGIN_PREFIX . '_event_date_to',
                            'value'   => sanitize_text_field($_GET[ECWD_PLUGIN_PREFIX.'_date_from_filter']),
                            'compare' => '>=',
                            'type'    => 'DATE'
                        );
                    }
                    if (!empty($_GET[ECWD_PLUGIN_PREFIX.'_date_to_filter'])) {
                        $qv['meta_query'][] = array(
                            'key'     => ECWD_PLUGIN_PREFIX . '_event_date_from',
                            'value'   => sanitize_text_field($_GET[ECWD_PLUGIN_PREFIX.'_date_to_filter']),
                            'compare' => '<=',
                            'type'    => 'DATE'
                        );
                    }
                    if (!empty($_GET[ECWD_PLUGIN_PREFIX . '_venue_filter'])) {
                        $qv['meta_query'][] = array(
                            'key'     => ECWD_PLUGIN_PREFIX . '_event_venue',
                            'value'   => sanitize_text_field($_GET[ECWD_PLUGIN_PREFIX . '_venue_filter']),
                            'compare' => '='
                        );
                    }
                
            }
        }

    }

}