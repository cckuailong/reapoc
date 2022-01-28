<?php

namespace ProfilePress\Core\ContentProtection;

class ContentConditions
{
    /**
     * @var array
     */
    public $conditions;

    /**
     * @param array $conditions
     */
    public function add_conditions($conditions = [])
    {
        foreach ($conditions as $key => $condition) {
            if (empty($condition['id']) && ! is_numeric($key)) {
                $condition['id'] = $key;
            }

            $this->add_condition($condition);
        }
    }

    /**
     * @param array $condition
     */
    public function add_condition($condition = [])
    {
        if ( ! empty($condition['id']) && ! isset ($this->conditions[$condition['id']])) {
            $condition = wp_parse_args($condition, array(
                'id'       => '',
                'callback' => null,
                'group'    => '',
                'title'    => ''
            ));

            $this->conditions[$condition['id']] = $condition;
        }

        return;
    }

    /**
     * @return array
     */
    public function get_conditions()
    {
        static $conditions;

        if ( ! isset($conditions)) {

            if ( ! isset($this->conditions)) {
                $this->register_conditions();
            }

            $conditions = $this->conditions;
        }

        return $conditions;
    }

    /**
     * @return array
     */
    public function get_conditions_by_group()
    {
        static $groups;

        if ( ! isset($groups)) {

            $groups = [];

            foreach ($this->get_conditions() as $condition) {
                $groups[$condition['group']][$condition['id']] = $condition;
            }
        }

        return $groups;
    }

    /**
     * @return array
     */
    public function conditions_dropdown_list()
    {
        $groups = [];

        $conditions_by_group = $this->get_conditions_by_group();

        foreach ($conditions_by_group as $group => $_conditions) {

            $conditions = [];

            foreach ($_conditions as $id => $condition) {
                $conditions[$id] = $condition['title'];
            }

            $groups[$group] = $conditions;
        }

        return $groups;
    }

    public function rule_row($facetListId, $facetId, $savedRule = [])
    {
        $name_attr = sprintf('ppress_cc_data[content][%s][%s][condition]', esc_attr($facetListId), esc_attr($facetId));
        ?>
        <div class="facet" data-facet="<?= esc_attr($facetId) ?>">
            <i class="badge or"><?= esc_html__('or', 'wp-user-avatar') ?></i>
            <div class="col">
                <select class="ppress-content-condition-rule-name" class="ppcr-condition-rule-name" name="<?= $name_attr; ?>">
                    <option value=""><?php _e('Select a condition', 'wp-user-avatar'); ?></option>
                    <?php foreach ($this->get_conditions_by_group() as $group => $conditions) : ?>
                        <optgroup label="<?= $group; ?>">
                            <?php foreach ($conditions as $id => $condition) : ?>
                                <option value="<?php echo $id; ?>" <?php selected(@$savedRule['condition'], $id); ?>>
                                    <?php echo $condition['title'] ?>
                                </option>
                            <?php endforeach ?>
                        </optgroup>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col">
                <div class="ppress-cr-rule-values">
                    <?php if (is_array($savedRule) && ! empty($savedRule)) : ?>
                        <?php if ( ! empty($savedRule['condition'])) : ?>
                            <?= $this->rule_value_field(@$savedRule['condition'], $facetListId, $facetId, @$savedRule['value']); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="actions">
                <a href="javascript:void(0)" class="remove removeFacet">
                    <span class="icon-circle-minus dashicons dashicons-minus"></span>
                </a>
            </div>
        </div>
        <?php
    }

    public function unlinked_and_rule_badge()
    {
        echo '<p class="and"><em>' . esc_html__('AND', 'wp-user-avatar') . '</em></p>';
    }

    public function linked_and_rule_badge()
    {
        echo '<p class="and"><a href="javascript:void(0);" class="addCondition">+ ' . esc_html__('AND', 'wp-user-avatar') . '</a></p>';
    }

    public function rules_group_row($facetListId = '', $facetId = '', $facets = [], $unlink_and = false)
    {
        ?>
        <div>
            <section class="condAction" data-facet-list="<?= esc_attr($facetListId) ?>">

                <div class="facetList">

                    <?php if (is_array($facets) && ! empty($facets)) : ?>
                        <?php foreach ($facets as $facetId => $savedRule) : ?>
                            <?php $this->rule_row($facetListId, $facetId, $savedRule); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ( ! is_array($facets) || empty($facets)) : ?>
                        <?php $this->rule_row($facetListId, $facetId); ?>
                    <?php endif; ?>

                </div>
                <div class="add-or">
                    <a href="javascript:void(0)" class="add addFacet">+ <?= esc_html__('OR', 'wp-user-avatar') ?></a>
                </div>
            </section>

            <?php if ($unlink_and === true) : ?>
                <?php $this->unlinked_and_rule_badge(); ?>
            <?php endif; ?>

            <?php if ($unlink_and === false) : ?>
                <?php $this->linked_and_rule_badge(); ?>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * @param null $condition
     *
     * @return mixed|null
     *
     */
    public function get_condition($condition = null)
    {
        $conditions = $this->get_conditions();

        return isset($conditions[$condition]) ? $conditions[$condition] : null;
    }

    /**
     * @return array
     */
    public function generate_post_type_conditions()
    {
        $conditions = [];
        $post_types = get_post_types(array('public' => true), 'objects');
        unset($post_types['attachment']);

        foreach ($post_types as $name => $post_type) {

            if ($post_type->has_archive) {
                $conditions[$name . '_index'] = array(
                    'group'    => $post_type->labels->name,
                    'title'    => sprintf(esc_html__('%s Archive Page', 'wp-user-avatar'), $post_type->labels->name),
                    'callback' => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'post_type'),
                );
            }

            $conditions[$name . '_all'] = array(
                'group'    => $post_type->labels->name,
                'title'    => sprintf(esc_html__('All %s', 'wp-user-avatar'), $post_type->labels->name),
                'callback' => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'post_type'),
            );

            $conditions[$name . '_selected'] = array(
                'group'          => $post_type->labels->name,
                'overview_title' => $post_type->labels->name,
                'title'          => sprintf(esc_html__('Selected %s', 'wp-user-avatar'), $post_type->labels->name),
                'field'          => array(
                    'placeholder' => sprintf(esc_html__('Select %s', 'wp-user-avatar'), strtolower($post_type->labels->name)),
                    'type'        => 'postselect',
                    'post_type'   => $name
                ),
                'callback'       => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'post_type'),
            );

            if (is_post_type_hierarchical($name)) {
                $conditions[$name . '_children'] = array(
                    'group'          => $post_type->labels->name,
                    'overview_title' => sprintf(esc_html__('Child %s of', 'wp-user-avatar'), $post_type->labels->name),
                    'title'          => sprintf(esc_html__('Child of Selected %s', 'wp-user-avatar'), $post_type->labels->name),
                    'field'          => array(
                        'placeholder' => sprintf(esc_html__('Select %s', 'wp-user-avatar'), strtolower($post_type->labels->name)),
                        'type'        => 'postselect',
                        'post_type'   => $name
                    ),
                    'callback'       => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'post_type'),
                );

                $conditions[$name . '_ancestors'] = array(
                    'group'          => $post_type->labels->name,
                    'overview_title' => sprintf(esc_html__('Parent %s of', 'wp-user-avatar'), $post_type->labels->name),
                    'title'          => sprintf(esc_html__('Parent of Selected %s', 'wp-user-avatar'), $post_type->labels->name),
                    'field'          => array(
                        'placeholder' => sprintf(esc_html__('Select %s', 'wp-user-avatar'), strtolower($post_type->labels->singular_name)),
                        'type'        => 'postselect',
                        'post_type'   => $name
                    ),
                    'callback'       => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'post_type'),
                );
            }

            $templates = wp_get_theme()->get_page_templates();

            if ($name == 'page' && ! empty($templates)) {
                $conditions[$name . '_template'] = array(
                    'group'          => $post_type->labels->name,
                    'overview_title' => esc_html__('Template', 'wp-user-avatar'),
                    'title'          => sprintf(esc_html__('%s with Template', 'wp-user-avatar'), $post_type->labels->name),
                    'field'          => array(
                        'type'        => 'select',
                        'placeholder' => esc_html__('Select Template', 'wp-user-avatar'),
                        'multiple'    => true,
                        'options'     => array_merge(array('default' => __('Default', 'wp-user-avatar')), $templates),
                    ),
                    'callback'       => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'post_type'),
                );
            }

            if ($name == 'page') {
                $conditions['is_front_page'] = array(
                    'group'    => $post_type->labels->name,
                    'title'    => __('Home or Front Page', 'wp-user-avatar'),
                    'callback' => 'is_front_page',
                );

                $conditions['is_home'] = array(
                    'group'    => $post_type->labels->name,
                    'title'    => __('Blog or Posts Page', 'wp-user-avatar'),
                    'callback' => 'is_home',
                );

                $conditions['is_search'] = array(
                    'group'    => $post_type->labels->name,
                    'title'    => __('Search Result Page', 'wp-user-avatar'),
                    'callback' => 'is_search',
                );

                $conditions['is_404'] = array(
                    'group'    => $post_type->labels->name,
                    'title'    => __('404 Error Page', 'wp-user-avatar'),
                    'callback' => 'is_404',
                );
            }

            $conditions = array_merge($conditions, $this->generate_post_type_tax_conditions($name));
        }

        return $conditions;
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function generate_post_type_tax_conditions($name)
    {
        $post_type  = get_post_type_object($name);
        $taxonomies = get_object_taxonomies($name, 'object');
        $conditions = [];
        foreach ($taxonomies as $tax_name => $taxonomy) {

            $conditions[$name . '_w_' . $tax_name] = array(
                'group'          => $post_type->labels->name,
                'overview_title' => $taxonomy->labels->name,
                'title'          => sprintf(esc_html__('%1$s with %2$s', 'wp-user-avatar'), $post_type->labels->name, $taxonomy->labels->name),
                'field'          => array(
                    'placeholder' => sprintf(esc_html__('Select %s', 'wp-user-avatar'), strtolower($taxonomy->labels->name)),
                    'type'        => 'taxonomyselect',
                    'taxonomy'    => $tax_name,
                    'post_type'   => $name
                ),
                'callback'       => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'post_type_tax'),
            );
        }

        return $conditions;
    }

    /**
     * @return array
     */
    public function generate_taxonomy_conditions()
    {
        $conditions = [];
        $taxonomies = get_taxonomies(['public' => true], 'objects');

        foreach ($taxonomies as $tax_name => $taxonomy) {

            $group = sprintf(esc_html__('%s (%s)', 'wp-user-avatar'), $taxonomy->labels->name, $taxonomy->name);

            $conditions['tax_' . $tax_name . '_all'] = array(
                'group'          => $group,
                'title'          => sprintf(esc_html__('All %s Archive Pages', 'wp-user-avatar'), $taxonomy->labels->name),
                'overview_title' => sprintf(esc_html__('%s Archive', 'wp-user-avatar'), $taxonomy->labels->name),
                'callback'       => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'taxonomy'),
            );

            $conditions['tax_' . $tax_name . '_selected'] = array(
                'group'          => $group,
                'overview_title' => sprintf(esc_html__('%s Archive', 'wp-user-avatar'), $taxonomy->labels->name),
                'title'          => sprintf(esc_html__('Selected %s Archive Pages', 'wp-user-avatar'), $taxonomy->labels->name),
                'field'          => array(
                    'placeholder' => sprintf(esc_html__('Select %s', 'wp-user-avatar'), strtolower($taxonomy->labels->name)),
                    'type'        => 'taxonomyselect',
                    'taxonomy'    => $tax_name
                ),
                'callback'       => array('\\ProfilePress\Core\ContentProtection\ConditionCallbacks', 'taxonomy'),
            );

        }

        return $conditions;
    }

    /**
     * @return mixed|void
     */
    public function register_conditions()
    {
        $conditions = array_merge($this->generate_post_type_conditions(), $this->generate_taxonomy_conditions());

        $conditions = apply_filters('ppress_cr_registered_conditions', $conditions);

        $this->add_conditions($conditions);
    }

    public function select_field($name_attr, $args = [])
    {
        $args = wp_parse_args($args, ['selected' => [], 'options' => [], 'multiple' => true]);

        if ($args['multiple']) {
            printf('<select class="ppress-cr-select2" name="%s" multiple>', $name_attr);
        } else {
            printf('<select name="%s">', $name_attr);
        }

        if ( ! empty($args['options'])) {

            foreach ($args['options'] as $id => $label) {
                if (true === $args['multiple']) {
                    $selected = in_array($id, $args['selected']) ? ' selected="selected"' : '';
                } else {
                    $selected = selected($args['selected'], $id, false);
                }

                printf('<option value="%s"%s>%s</option>', $id, $selected, $label);
            }
        }

        echo '</select>';
    }

    public function postselect_field($name_attr, $savedValue = [])
    {
        $options = array_reduce($savedValue, function ($carry, $post_id) {
            $carry[$post_id] = get_the_title($post_id);

            return $carry;
        }, []);

        $this->select_field($name_attr, ['selected' => $savedValue, 'options' => $options]);
    }

    public function taxonomyselect_field($name_attr, $savedValue)
    {
        $options = array_reduce($savedValue, function ($carry, $term_id) {
            $carry[$term_id] = get_term($term_id)->name;

            return $carry;
        }, []);

        $this->select_field($name_attr, ['selected' => $savedValue, 'options' => $options]);
    }

    public function rule_value_field($condition_id, $facetListId, $facetId, $savedValue = [])
    {
        $condition_field_settings = ppress_var($this->get_condition($condition_id), 'field');

        $field_type = ppress_var($condition_field_settings, 'type');

        if ( ! empty($field_type)) {

            $method = $field_type . '_field';

            if (method_exists($this, $method)) {

                ob_start();

                $name_attr = sprintf('ppress_cc_data[content][%s][%s][value][]', $facetListId, $facetId);

                if ($method == 'select_field') {
                    $args['options']  = ppress_var($condition_field_settings, 'options');
                    $args['selected'] = $savedValue;

                    $this->$method($name_attr, $args);

                } else {
                    $this->$method($name_attr, $savedValue);
                }

                return ob_get_clean();
            }
        }

        return false;
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
