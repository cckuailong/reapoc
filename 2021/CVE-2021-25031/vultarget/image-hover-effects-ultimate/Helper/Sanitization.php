<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Helper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * @author $biplob018
 */
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

trait Sanitization {

    /**
     * font settings sanitize
     * works at layouts page to adding font Settings sanitize
     */
    public function AdminTextSenitize($data) {
        $data = str_replace('\\\\"', '&quot;', $data);
        $data = str_replace('\\\"', '&quot;', $data);
        $data = str_replace('\\"', '&quot;', $data);
        $data = str_replace('\"', '&quot;', $data);
        $data = str_replace('"', '&quot;', $data);
        $data = str_replace('\\\\&quot;', '&quot;', $data);
        $data = str_replace('\\\&quot;', '&quot;', $data);
        $data = str_replace('\\&quot;', '&quot;', $data);
        $data = str_replace('\&quot;', '&quot;', $data);
        $data = str_replace("\\\\'", '&apos;', $data);
        $data = str_replace("\\\'", '&apos;', $data);
        $data = str_replace("\\'", '&apos;', $data);
        $data = str_replace("\'", '&apos;', $data);
        $data = str_replace("\\\\&apos;", '&apos;', $data);
        $data = str_replace("\\\&apos;", '&apos;', $data);
        $data = str_replace("\\&apos;", '&apos;', $data);
        $data = str_replace("\&apos;", '&apos;', $data);
        $data = str_replace("'", '&apos;', $data);
        $data = str_replace('<', '&lt;', $data);
        $data = str_replace('>', '&gt;', $data);
        $data = sanitize_text_field($data);
        return $data;
    }

    /*
     * Image Hover Style Admin Panel Body
     *
     * @since 9.3.0
     */

    public function start_section_tabs($id, array $arg = []) {
        echo '<div class="oxi-addons-tabs-content-tabs" id="shortcode-addons-section-';
        if (array_key_exists('condition', $arg)) :
            foreach ($arg['condition'] as $value) {
                echo $value;
            }
        endif;
        echo '"  ' . (array_key_exists('padding', $arg) ? 'style="padding: ' . $arg['padding'] . '"' : '') . '>';
    }

    /*
     * Image Hover Style Admin Panel header
     *
     * @since 9.3.0
     */

    public function start_section_header($id, array $arg = []) {
        echo '<ul class="oxi-addons-tabs-ul">   ';
        foreach ($arg['options'] as $key => $value) {
            echo '<li ref="#shortcode-addons-section-' . $key . '">' . $value . '</li>';
        }
        echo '</ul>';
    }

    /*
     * Image Hover Style Admin Panel end Body
     *
     * @since 9.3.0
     */

    public function end_section_tabs() {
        echo '</div>';
    }

    /*
     * Image Hover Style Admin Panel Col 6 or Entry devider
     *
     * @since 9.3.0
     */

    public function start_section_devider() {
        echo '<div class="oxi-addons-col-6">';
    }

    /*
     * Image Hover Style Admin Panel end Entry Divider
     *
     * @since 9.3.0
     */

    public function end_section_devider() {
        echo '</div>';
    }

    /*
     * Image Hover Style Admin Panel Form Dependency
     *
     * @since 9.3.0
     */

    public function forms_condition(array $arg = []) {

        if (array_key_exists('condition', $arg)) :
            $i = $arg['condition'] != '' ? count($arg['condition']) : 0;
            // echo $i;
            $data = '';
            $s = 1;
            $form_condition = array_key_exists('form_condition', $arg) ? $arg['form_condition'] : '';
            $notcondition = array_key_exists('notcondition', $arg) ? $arg['notcondition'] : false;

            foreach ($arg['condition'] != '' ? $arg['condition'] : [] as $key => $value) {
                if (is_array($value)) :
                    $c = count($value);
                    $crow = 1;
                    if ($c > 1 && $i > 1) :
                        $data .= '(';
                    endif;
                    foreach ($value as $item) {
                        $data .= $form_condition . $key . ' === \'' . $item . '\'';
                        if ($crow < $c) :
                            $data .= ' || ';
                            $crow++;
                        endif;
                    }
                    if ($c > 1 && $i > 1) :
                        $data .= ')';
                    endif;
                elseif ($value == 'COMPILED') :
                    $data .= $form_condition . $key;
                elseif ($value == 'EMPTY') :
                    $data .= $form_condition . $key . ' !== \'\'';
                elseif (empty($value)) :
                    $data .= $form_condition . $key;
                elseif ($notcondition) :
                    $data .= $form_condition . $key . ' !== \'' . $value . '\'';
                else :
                    $data .= $form_condition . $key . ' === \'' . $value . '\'';
                endif;
                if ($s < $i) :
                    $data .= ' && ';
                    $s++;
                endif;
            }
            if (!empty($data)) :
                return 'data-condition="' . $data . '"';
            endif;
        endif;
    }

    /*
     * Image Hover Style Admin Panel Each Tabs
     *
     * @since 9.3.0
     */

    public function start_controls_section($id, array $arg = []) {
        $defualt = ['showing' => FALSE];
        $arg = array_merge($defualt, $arg);
        $condition = $this->forms_condition($arg);
        echo '<div class="oxi-addons-content-div ' . (($arg['showing']) ? '' : 'oxi-admin-head-d-none') . '" ' . $condition . '>
                    <div class="oxi-head">
                    ' . $arg['label'] . '
                    <div class="oxi-head-toggle"></div>
                    </div>
                    <div class="oxi-addons-content-div-body">';
    }

    /*
     * Image Hover Style Admin Panel end Each Tabs
     *
     * @since 9.3.0
     */

    public function end_controls_section() {
        echo '</div></div>';
    }

    /*
     * Image Hover Style Admin Panel Section Inner Tabs
     * This Tabs like inner tabs as Normal view and Hover View
     *
     * @since 9.3.0
     */

    public function start_controls_tabs($id, array $arg = []) {
        $defualt = ['options' => ['normal' => 'Normal', 'hover' => 'Hover']];
        $arg = array_merge($defualt, $arg);
        echo '<div class="shortcode-form-control shortcode-control-type-control-tabs ' . (array_key_exists('separator', $arg) ? ($arg['separator'] === TRUE ? 'shortcode-form-control-separator-before' : '') : '') . '">
                <div class="shortcode-form-control-content shortcode-form-control-content-tabs">
                    <div class="shortcode-form-control-field">';
        foreach ($arg['options'] as $key => $value) {
            echo '  <div class="shortcode-control-type-control-tab-child">
			<div class="shortcode-control-content">
				' . $value . '
                        </div>
                    </div>';
        }
        echo '</div>
              </div>
              <div class="shortcode-form-control-content">';
    }

    /*
     * Image Hover Style Admin Panel end Section Inner Tabs
     *
     * @since 9.3.0
     */

    public function end_controls_tabs() {
        echo '</div> </div>';
    }

    /*
     * Image Hover Style Admin Panel Section Inner Tabs Child
     *
     * @since 9.3.0
     */

    public function start_controls_tab() {
        echo '<div class="shortcode-form-control-content shortcode-form-control-tabs-content shortcode-control-tab-close">';
    }

    /*
     * Image Hover Style Admin Panel End Section Inner Tabs Child
     *
     * @since 9.3.0
     */

    public function end_controls_tab() {
        echo '</div>';
    }

    /*
     * Image Hover Style Admin Panel  Section Popover
     *
     * @since 9.3.0
     */

    public function start_popover_control($id, array $arg = []) {
        $condition = $this->forms_condition($arg);
        $separator = (array_key_exists('separator', $arg) ? ($arg['separator'] === TRUE ? 'shortcode-form-control-separator-before' : '') : '');
        echo '  <div class="shortcode-form-control shortcode-control-type-popover ' . $separator . '" ' . $condition . '>
                    <div class="shortcode-form-control-content shortcode-form-control-content-popover">
                        <div class="shortcode-form-control-field">
                            <label for="" class="shortcode-form-control-title">' . $arg['label'] . '</label>
                            <div class="shortcode-form-control-input-wrapper">
                                <span class="dashicons popover-set"></span>
                            </div>
                        </div>
                        ' . (array_key_exists('description', $arg) ? '<div class="shortcode-form-control-description">' . $arg['description'] . '</div>' : '') . '

                    </div>
                    <div class="shortcode-form-control-content shortcode-form-control-content-popover-body">

               ';
    }

    /*
     * Image Hover Style Admin Panel end Popover
     *
     * @since 9.3.0
     */

    public function end_popover_control() {
        echo '</div></div>';
    }

    /*
     * Image Hover Style Admin Panel Form Add Control.
     * Call All Input Control from here Based on Control Name.
     *
     * @since 9.3.0
     */

    public function add_control($id, array $data = [], array $arg = []) {
        /*
         * Responsive Control Start
         * @since 9.3.0
         */
        $responsive = $responsiveclass = '';
        if (array_key_exists('responsive', $arg)) :
            if ($arg['responsive'] == 'laptop') :
                $responsiveclass = 'shortcode-addons-form-responsive-laptop';
            elseif ($arg['responsive'] == 'tab') :
                $responsiveclass = 'shortcode-addons-form-responsive-tab';
            elseif ($arg['responsive'] == 'mobile') :
                $responsiveclass = 'shortcode-addons-form-responsive-mobile';
            endif;
            $responsive = '<div class="shortcode-form-control-responsive-switchers">
                                <a class="shortcode-form-responsive-switcher shortcode-form-responsive-switcher-desktop" data-device="desktop">
                                    <span class="dashicons dashicons-desktop"></span>
                                </a>
                                <a class="shortcode-form-responsive-switcher shortcode-form-responsive-switcher-tablet" data-device="tablet">
                                    <span class="dashicons dashicons-tablet"></span>
                                </a>
                                <a class="shortcode-form-responsive-switcher shortcode-form-responsive-switcher-mobile" data-device="mobile">
                                    <span class="dashicons dashicons-smartphone"></span>
                                </a>
                            </div>';

        endif;
        $defualt = [
            'type' => 'text',
            'label' => 'Input Text',
            'default' => '',
            'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'placeholder' => __('', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'selector-data' => TRUE,
            'render' => TRUE,
            'responsive' => 'laptop',
        ];

        /*
         * Data Currection while Its comes from group Control
         */
        if (array_key_exists('selector-value', $arg)) :
            foreach ($arg['selector'] as $key => $value) {
                $arg['selector'][$key] = $arg['selector-value'];
            }
        endif;

        $arg = array_merge($defualt, $arg);
        if ($arg['type'] == 'animation') :
            $arg['type'] = 'select';
            $arg['options'] = [
                '' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'bounce' => __('Bounce', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'flash' => __('Flash', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'pulse' => __('Pulse', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'rubberBand' => __('RubberBand', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'shake' => __('Shake', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'swing' => __('Swing', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'tada' => __('Tada', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'wobble' => __('Wobble', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'jello' => __('Jello', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ];
        endif;

        $condition = $this->forms_condition($arg);
        $toggle = (array_key_exists('toggle', $arg) ? 'shortcode-addons-form-toggle' : '');
        $separator = (array_key_exists('separator', $arg) ? ($arg['separator'] === TRUE ? 'shortcode-form-control-separator-before' : '') : '');

        $loader = (array_key_exists('loader', $arg) ? $arg['loader'] == TRUE ? ' shortcode-addons-control-loader ' : '' : '');
        echo '<div class="shortcode-form-control shortcode-control-type-' . $arg['type'] . ' ' . $separator . ' ' . $toggle . ' ' . $responsiveclass . ' ' . $loader . '" ' . $condition . '>
                <div class="shortcode-form-control-content">
                    <div class="shortcode-form-control-field">';
        echo '<label for="" class="shortcode-form-control-title">' . $arg['label'] . '</label>';
        echo $responsive;

        $fun = $arg['type'] . '_admin_control';
        echo $this->$fun($id, $data, $arg);
        echo '      </div>';
        echo (array_key_exists('description', $arg) ? '<div class="shortcode-form-control-description">' . $arg['description'] . '</div>' : '');

        echo ' </div>
        </div>';
    }

    /*
     * Image Hover Style Admin Panel Responsive Control.
     * Can Possible to modify any Add control to Responsive Control
     *
     * @since 9.3.0
     */

    public function add_responsive_control($id, array $data = [], array $arg = []) {
        $lap = $id . '-lap';
        $tab = $id . '-tab';
        $mob = $id . '-mob';
        $laparg = ['responsive' => 'laptop'];
        $tabarg = ['responsive' => 'tab'];
        $mobarg = ['responsive' => 'mobile'];
        $this->add_control($lap, $data, array_merge($arg, $laparg));
        $this->add_control($tab, $data, array_merge($arg, $tabarg));
        $this->add_control($mob, $data, array_merge($arg, $mobarg));
    }

    /*
     * Image Hover Style Admin Panel Group Control.
     *
     * @since 9.3.0
     */

    public function add_group_control($id, array $data = [], array $arg = []) {
        $defualt = [
            'type' => 'text',
            'label' => 'Input Text',
            'description' => '',
            'simpledescription' => ''
        ];
        $arg = array_merge($defualt, $arg);
        $fun = $arg['type'] . '_admin_group_control';
        echo $this->$fun($id, $data, $arg);
    }

    /*
     * Image Hover Style Admin Panel Repeater Control.
     *
     * @since 9.3.0
     */

    public function add_repeater_control($id, array $data = [], array $arg = []) {
        $condition = $this->forms_condition($arg);
        $separator = (array_key_exists('separator', $arg) ? ($arg['separator'] === TRUE ? 'shortcode-form-control-separator-before' : '') : '');
        $buttontext = (array_key_exists('button', $arg) ? $arg['button'] : 'Add Item');
        echo '<div class="shortcode-form-control shortcode-control-type-' . $arg['type'] . ' ' . $separator . '" ' . $condition . ' id="' . $id . '">
                <div class="shortcode-form-control-content">
                    <div class="shortcode-form-control-field">
                        <label for="" class="shortcode-form-control-title">' . $arg['label'] . '</label>
                    </div>
                    <div class="shortcode-form-repeater-fields-wrapper">';
        if (array_key_exists($id, $data)) :
            foreach ($data[$id] as $k => $vl) {
                $style = [];
                foreach ($vl as $c => $v) {
                    $style[$id . 'saarsa' . $k . 'saarsa' . $c] = $v;
                }
                echo '  <div class="shortcode-form-repeater-fields" tab-title="' . $arg['title_field'] . '">
                            <div class="shortcode-form-repeater-controls">
                                <div class="shortcode-form-repeater-controls-title">
                                    ' . ($vl[$arg['title_field']]) . '
                                </div>
                                <div class="shortcode-form-repeater-controls-duplicate">
                                    <span class="dashicons dashicons-admin-page"></span>
                                </div>
                                <div class="shortcode-form-repeater-controls-remove">
                                    <span class="dashicons dashicons-trash"></span>
                                </div>
                            </div>
                            <div class="shortcode-form-repeater-content">';
                foreach ($arg['fields'] as $key => $value) {
                    $controller = (array_key_exists('controller', $value) ? $value['controller'] : 'add_control');
                    $child = $id . 'saarsa' . $k . 'saarsa' . $key;
                    $value['conditional'] = (array_key_exists('conditional', $value) ? ($value['conditional'] == 'outside') ? 'outside' : 'inside' : '');
                    $value['form_condition'] = (array_key_exists('conditional', $value) ? ($value['conditional'] == 'inside') ? $id . 'saarsa' . $k . 'saarsa' : '' : '');

                    if ($controller == 'add_control' || $controller == 'add_group_control' || $controller == 'add_responsive_control') :
                        $this->$controller($child, $style, $value);
                    else :
                        $this->$controller($child, $value);
                    endif;
                }
                echo '      </div>
                        </div>';
            }
        endif;

        echo '      </div>';

        $this->add_control(
                $id . 'nm',
                $data,
                ['type' => Controls::HIDDEN, 'default' => '0',]
        );
        echo '      <div class="shortcode-form-repeater-button-wrapper"><a href="#" parent-id="' . $id . '" class="shortcode-form-repeater-button"><span class="dashicons dashicons-plus"></span> ' . $buttontext . '</a></div>';

        echo '  </div>
             </div>';

        $this->repeater .= '<div id="repeater-' . $id . '-initial-data">
                                <div class="shortcode-form-repeater-fields" tab-title="' . $arg['title_field'] . '">
                                    <div class="shortcode-form-repeater-controls">
                                        <div class="shortcode-form-repeater-controls-title">
                                            Title Goes Here
                                        </div>
                                        <div class="shortcode-form-repeater-controls-duplicate">
                                            <span class="dashicons dashicons-admin-page"></span>
                                        </div>
                                        <div class="shortcode-form-repeater-controls-remove">
                                            <span class="dashicons dashicons-trash"></span>
                                        </div>
                                    </div>
                                    <div class="shortcode-form-repeater-content">';
        foreach ($arg['fields'] as $key => $value) {
            $controller = (array_key_exists('controller', $value) ? $value['controller'] : 'add_control');
            $child = $id . 'saarsarepidrepsaarsa' . $key;
            $value['conditional'] = (array_key_exists('conditional', $value) ? ($value['conditional'] == 'outside') ? 'outside' : 'inside' : '');
            $value['form_condition'] = (array_key_exists('conditional', $value) ? ($value['conditional'] == 'inside') ? $id . 'saarsarepidrepsaarsa' : '' : '');
            ob_start();
            if ($controller == 'add_control' || $controller == 'add_group_control' || $controller == 'add_responsive_control') :

                $this->$controller($child, [], $value);
            else :
                $this->$controller($child, $value);
            endif;

            $this->repeater .= ob_get_clean();
        }
        $this->repeater .= '         </div>
                                </div>
                            </div>';
    }

    public function add_rearrange_control($id, array $data = [], array $arg = []) {
        $condition = $this->forms_condition($arg);
        $separator = (array_key_exists('separator', $arg) ? ($arg['separator'] === TRUE ? 'shortcode-form-control-separator-before' : '') : '');
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        echo '<div class="shortcode-form-control shortcode-control-type-' . $arg['type'] . ' ' . $separator . '" ' . $condition . '>
                <div class="shortcode-form-control-content">
                    <div class="shortcode-form-control-field">
                        <label for="" class="shortcode-form-control-title">' . $arg['label'] . '</label>
                    </div>
                    <div class="shortcode-form-rearrange-fields-wrapper" vlid="#' . $id . '">';
        $rearrange = explode(',', $value);
        foreach ($rearrange as $k => $vl) {
            if ($vl != '') :
                echo '  <div class="shortcode-form-repeater-fields" id="' . $vl . '">
                            <div class="shortcode-form-repeater-controls">
                                <div class="shortcode-form-repeater-controls-title">
                                    ' . ($arg['fields'][$vl]['label']) . '
                                </div>
                            </div>
                        </div>';
            endif;
        }
        echo '          <div class="shortcode-form-control-input-wrapper">
                            <input type="hidden" value="' . $value . '" name="' . $id . '" id="' . $id . '">
                        </div>
                    </div>
                </div>
            </div>';
    }

    /*
     * Image Hover Style Admin Panel Heading Input.
     *
     * @since 9.3.0
     */

    public function heading_admin_control($id, array $data = [], array $arg = []) {
        echo ' ';
    }

    /*
     * Image Hover Style Admin Panel Switcher Input.
     *
     * @since 9.3.0
     */

    public function separator_admin_control($id, array $data = [], array $arg = []) {
        echo '';
    }

    public function multiple_selector_handler($data, $val) {

        $val = preg_replace_callback('/\{\{\K(.*?)(?=}})/', function ($match) use ($data) {
            $ER = explode('.', $match[0]);
            if (strpos($match[0], 'SIZE') !== FALSE) :
                $size = array_key_exists($ER[0] . '-size', $data) ? $data[$ER[0] . '-size'] : '';
                $match[0] = str_replace('.SIZE', $size, $match[0]);
            endif;
            if (strpos($match[0], 'UNIT') !== FALSE) :
                $size = array_key_exists($ER[0] . '-choices', $data) ? $data[$ER[0] . '-choices'] : '';
                $match[0] = str_replace('.UNIT', $size, $match[0]);
            endif;
            if (strpos($match[0], 'VALUE') !== FALSE) :
                $size = array_key_exists($ER[0], $data) ? $data[$ER[0]] : '';
                $match[0] = str_replace('.VALUE', $size, $match[0]);
            endif;
            return str_replace($ER[0], '', $match[0]);
        }, $val);
        return str_replace("{{", '', str_replace("}}", '', $val));
    }

    /*
     * Image Hover Style Admin Panel Switcher Input.
     *
     * @since 9.3.0
     */

    public function switcher_admin_control($id, array $data = [], array $arg = []) {
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        echo '  <div class="shortcode-form-control-input-wrapper">
                    <label class="shortcode-switcher">
                        <input type="checkbox" ' . ($value == $arg['return_value'] ? 'checked ckdflt="true"' : '') . ' value="' . $arg['return_value'] . '"  name="' . $id . '" id="' . $id . '"/>
                        <span data-on="' . $arg['label_on'] . '" data-off="' . $arg['label_off'] . '"></span>
                    </label>
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Text Input.
     *
     * @since 9.3.0
     */

    public function text_admin_control($id, array $data = [], array $arg = []) {
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        if (array_key_exists('link', $arg)) :
            echo '<div class="shortcode-form-control-input-wrapper shortcode-form-control-input-link">
                     <input type="text"  name="' . $id . '" id="' . $id . '" value="' . htmlspecialchars($value) . '" placeholder="' . $arg['placeholder'] . '" retundata=\'' . $retunvalue . '\'>
                     <span class="dashicons dashicons-admin-generic"></span>';
        else :
            echo '<div class="shortcode-form-control-input-wrapper">
                <input type="text"  name="' . $id . '" id="' . $id . '" value="' . htmlspecialchars($value) . '" placeholder="' . $arg['placeholder'] . '" retundata=\'' . $retunvalue . '\'>
            </div>';
        endif;
    }

    /*
     * Image Hover Style Admin Panel Hidden Input.
     *
     * @since 9.3.0
     */

    public function hidden_admin_control($id, array $data = [], array $arg = []) {
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        echo ' <div class="shortcode-form-control-input-wrapper">
                   <input type="hidden" value="' . $value . '" name="' . $id . '" id="' . $id . '">
               </div>';
    }

    /*
     * Image Hover Style Admin Panel Textarea Input.
     *
     * @since 9.3.0
     */

    public function textarea_admin_control($id, array $data = [], array $arg = []) {
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        echo '<div class="shortcode-form-control-input-wrapper">
                 <textarea  name="' . $id . '" id="' . $id . '" retundata=\'' . $retunvalue . '\' class="shortcode-form-control-tag-area" rows="' . (int) ((strlen($value) / 50) + 2) . '" placeholder="' . $arg['placeholder'] . '">' . str_replace('&nbsp;', '  ', str_replace('<br>', '&#13;&#10;', $value)) . '</textarea>
              </div>';
    }

    /*
     * Image Hover Style Admin Panel WYSIWYG Input.
     *
     * @since 9.3.0
     */

    public function wysiwyg_admin_control($id, array $data = [], array $arg = []) {
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        echo ' <div class="shortcode-form-control-input-wrapper"  retundata=\'' . $retunvalue . '\'>';
        echo wp_editor(
                $value,
                $id,
                $settings = array(
            'textarea_name' => $id,
            'wpautop' => false,
            'textarea_rows' => 7,
            'force_br_newlines' => true,
            'force_p_newlines' => false
                )
        );
        echo ' </div>';
    }

    /*
     * Image Hover Style Admin Panel Image Input.
     *
     * @since 9.3.0
     */

    public function image_admin_control($id, array $data = [], array $arg = []) {
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        echo '  <div class="shortcode-form-control-input-wrapper">
                    <div class="shortcode-addons-media-control ' . (empty($value) ? 'shortcode-addons-media-control-hidden-button' : '') . '">
                        <div class="shortcode-addons-media-control-pre-load">
                        </div>
                        <div class="shortcode-addons-media-control-image-load" style="background-image: url(' . $value . ');" ckdflt="background-image: url(' . $value . ');">
                            <div class="shortcode-addons-media-control-image-load-delete-button">
                            </div>
                        </div>
                        <div class="shortcode-addons-media-control-choose-image">
                            Choose Image
                        </div>
                    </div>
                    <input type="hidden" class="shortcode-addons-media-control-link" id="' . $id . '" name="' . $id . '" value="' . $value . '" >
                    <input type="hidden" class="shortcode-addons-media-control-link-alt" id="' . $id . '-alt" name="' . $id . '-alt" value="" >
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Number Input.
     *
     * @since 9.3.0
     */

    public function number_admin_control($id, array $data = [], array $arg = []) {

        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE && $this->render_condition_control($id, $data, $arg)) :
            if (array_key_exists('selector', $arg)) :
                foreach ($arg['selector'] as $key => $val) {
                    $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                    $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                    $file = str_replace('{{VALUE}}', $value, $val);
                    if (strpos($file, '{{') !== FALSE) :
                        $file = $this->multiple_selector_handler($data, $file);
                    endif;
                    if (!empty($value)) :
                        $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                    endif;
                }
            endif;
        endif;

        $defualt = ['min' => 0, 'max' => 1000, 'step' => 1,];
        $arg = array_merge($defualt, $arg);
        echo '  <div class="shortcode-form-control-input-wrapper">
                    <input id="' . $id . '" name="' . $id . '" type="number" min="' . $arg['min'] . '" max="' . $arg['max'] . '" step="' . $arg['step'] . '" value="' . $value . '"  responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Slider Input.
     *
     * @since 9.3.0
     * Done With Number Information
     */

    public function slider_admin_control($id, array $data = [], array $arg = []) {
        $unit = array_key_exists($id . '-choices', $data) ? $data[$id . '-choices'] : $arg['default']['unit'];
        $size = array_key_exists($id . '-size', $data) ? $data[$id . '-size'] : $arg['default']['size'];
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE && $arg['render'] == TRUE && $this->render_condition_control($id, $data, $arg)) :
            if (array_key_exists('selector', $arg)) :
                foreach ($arg['selector'] as $key => $val) {
                    if ($size != '' && $val != '') :
                        $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                        $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                        $file = str_replace('{{SIZE}}', $size, $val);
                        $file = str_replace('{{UNIT}}', $unit, $file);
                        if (strpos($file, '{{') !== FALSE) :
                            $file = $this->multiple_selector_handler($data, $file);
                        endif;
                        if (!empty($size)) :
                            $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                        endif;
                    endif;
                }
            endif;
        endif;
        if (array_key_exists('range', $arg)) :
            if (count($arg['range']) > 1) :
                echo ' <div class="shortcode-form-units-choices">';
                foreach ($arg['range'] as $key => $val) {
                    $rand = rand(10000, 233333333);
                    echo '<input id="' . $id . '-choices-' . $rand . '" type="radio" name="' . $id . '-choices"  value="' . $key . '" ' . ($key == $unit ? 'checked' : '') . '  min="' . $val['min'] . '" max="' . $val['max'] . '" step="' . $val['step'] . '">
                      <label class="shortcode-form-units-choices-label" for="' . $id . '-choices-' . $rand . '">' . $key . '</label>';
                }
                echo '</div>';
            endif;
        endif;
        $unitvalue = array_key_exists($id . '-choices', $data) ? 'unit="' . $data[$id . '-choices'] . '"' : '';
        echo '  <div class="shortcode-form-control-input-wrapper">
                    <div class="shortcode-form-slider" id="' . $id . '-slider' . '"></div>
                    <div class="shortcode-form-slider-input">
                        <input name="' . $id . '-size" custom="' . (array_key_exists('custom', $arg) ? '' . $arg['custom'] . '' : '') . '" id="' . $id . '-size' . '" type="number" min="' . $arg['range'][$unit]['min'] . '" max="' . $arg['range'][$unit]['max'] . '" step="' . $arg['range'][$unit]['step'] . '" value="' . $size . '" default-value="' . $size . '" ' . $unitvalue . ' responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                    </div>
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Select Input.
     *
     * @since 9.3.0
     */

    public function select_admin_control($id, array $data = [], array $arg = []) {
        $id = (array_key_exists('repeater', $arg) ? $id . ']' : $id);
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retun = [];

        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE && $this->render_condition_control($id, $data, $arg)) {
            if (array_key_exists('selector', $arg)) :
                foreach ($arg['selector'] as $key => $val) {
                    $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                    if (!empty($value) && !empty($val) && $arg['render'] == TRUE) {
                        $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                        $file = str_replace('{{VALUE}}', $value, $val);
                        if (strpos($file, '{{') !== FALSE) :
                            $file = $this->multiple_selector_handler($data, $file);
                        endif;
                        if (!empty($value)) :
                            $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                        endif;
                    }
                    $retun[$key][$key]['type'] = ($val != '' ? 'CSS' : 'HTML');
                    $retun[$key][$key]['value'] = $val;
                }
            endif;
        }
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($retun)) : '';
        $multiple = (array_key_exists('multiple', $arg) && $arg['multiple']) == true ? TRUE : FALSE;

        echo '<div class="shortcode-form-control-input-wrapper">
                <div class="shortcode-form-control-input-select-wrapper">
                <select id="' . $id . '" class="shortcode-addons-select-input ' . ($multiple ? 'js-example-basic-multiple' : '') . '" ' . ($multiple ? 'multiple' : '') . ' name="' . $id . '' . ($multiple ? '[]' : '') . '"  responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>';
        foreach ($arg['options'] as $key => $val) {
            if (is_array($val)) :
                if (isset($val[0]) && $val[0] == true) :
                    echo '<optgroup label="' . $val[1] . '">';
                else :
                    echo '</optgroup>';
                endif;
            else :
                if (is_array($value)) :
                    $new = array_flip($value);
                    echo ' <option value="' . $key . '" ' . (array_key_exists($key, $new) ? 'selected' : '') . '>' . $val . '</option>';
                else :
                    echo ' <option value="' . $key . '" ' . ($value == $key ? 'selected' : '') . '>' . $val . '</option>';
                endif;
            endif;
        }
        echo '</select>
                </div>
            </div>';
    }

    /*
     * Image Hover Style Admin Panel Choose Input.
     *
     * @since 9.3.0
     */

    public function choose_admin_control($id, array $data = [], array $arg = []) {
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retun = [];

        $operator = array_key_exists('operator', $arg) ? $arg['operator'] : 'text';
        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE && $this->render_condition_control($id, $data, $arg)) {
            if (array_key_exists('selector', $arg)) :
                foreach ($arg['selector'] as $key => $val) {
                    $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                    if (!empty($val)) {
                        $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                        $file = str_replace('{{VALUE}}', $value, $val);
                        if (strpos($file, '{{') !== FALSE) :
                            $file = $this->multiple_selector_handler($data, $file);
                        endif;
                        if (!empty($value)) :
                            $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                        endif;
                    }
                    $retun[$key][$key]['type'] = ($val != '' ? 'CSS' : 'HTML');
                    $retun[$key][$key]['value'] = $val;
                }
            endif;
        }
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($retun)) : '';
        echo '<div class="shortcode-form-control-input-wrapper">
                <div class="shortcode-form-choices" responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>';
        foreach ($arg['options'] as $key => $val) {
            echo '  <input id="' . $id . '-' . $key . '" type="radio" name="' . $id . '" value="' . $key . '" ' . ($value == $key ? 'checked  ckdflt="true"' : '') . '>
                                    <label class="shortcode-form-choices-label" for="' . $id . '-' . $key . '" tooltip="' . $val['title'] . '">
                                        ' . (($operator == 'text') ? $val['title'] : '<i class="' . $val['icon'] . '" aria-hidden="true"></i>') . '
                                    </label>';
        }
        echo '</div>
        </div>';
    }

    /*
     * Image Hover Style Admin Panel Color Input.
     *
     * @since 9.3.0
     */

    public function render_condition_control($id, array $data = [], array $arg = []) {

        if (array_key_exists('condition', $arg)) :
            foreach ($arg['condition'] as $key => $value) {
                if (array_key_exists('conditional', $arg) && $arg['conditional'] == 'outside') :
                    $data = $this->style;
                elseif (array_key_exists('conditional', $arg) && $arg['conditional'] == 'inside' && isset($arg['form_condition'])) :
                    $key = $arg['form_condition'] . $key;
                endif;
                if (strpos($key, '&') !== FALSE) :
                    return true;
                endif;
                if (!array_key_exists($key, $data)) :
                    return false;
                endif;
                if ($data[$key] != $value) :
                    if (is_array($value)) :
                        $t = false;
                        foreach ($value as $val) {
                            if ($data[$key] == $val) :
                                $t = true;
                            endif;
                        }
                        echo $t;
                    endif;
                    if ($value == 'EMPTY' && $data[$key] != '0') :
                        return true;
                    endif;
                    if (strpos($data[$key], '&') !== FALSE) :
                        return true;
                    endif;
                    return false;
                endif;
            }
        endif;
        return true;
    }

    public function color_admin_control($id, array $data = [], array $arg = []) {
        $id = (array_key_exists('repeater', $arg) ? $id . ']' : $id);
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE && $arg['render'] == TRUE && $this->render_condition_control($id, $data, $arg)) {
            if (array_key_exists('selector', $arg)) :
                foreach ($arg['selector'] as $key => $val) {
                    $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                    $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                    $file = str_replace('{{VALUE}}', $value, $val);
                    if (!empty($value)) :
                        $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                    endif;
                }
            endif;
        }
        $type = array_key_exists('oparetor', $arg) ? 'data-format="rgb" data-opacity="TRUE"' : '';
        echo '<div class="shortcode-form-control-input-wrapper">
                <input ' . $type . ' type="text"  class="oxi-addons-minicolor" id="' . $id . '" name="' . $id . '" value="' . $value . '" responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\' custom="' . (array_key_exists('custom', $arg) ? '' . $arg['custom'] . '' : '') . '">
             </div>';
    }

    /*
     * Image Hover Style Admin Panel Icon Selector.
     *
     * @since 9.3.0
     */

    public function icon_admin_control($id, array $data = [], array $arg = []) {
        $id = (array_key_exists('repeater', $arg) ? $id . ']' : $id);
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        echo '  <div class="shortcode-form-control-input-wrapper">
                    <input type="text"  class="oxi-admin-icon-selector" id="' . $id . '" name="' . $id . '" value="' . $value . '">
                    <span class="input-group-addon"></span>
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Font Selector.
     *
     * @since 9.3.0
     */

    public function font_admin_control($id, array $data = [], array $arg = []) {
        $id = (array_key_exists('repeater', $arg) ? $id . ']' : $id);
        $retunvalue = '';
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        if ($value != '' && array_key_exists($value, $this->google_font)) :
            $this->font[$value] = $value;
        endif;

        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE) {
            if (array_key_exists('selector', $arg) && $value != '') :
                foreach ($arg['selector'] as $key => $val) {
                    if ($arg['render'] == TRUE && !empty($val)) :
                        $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                        $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                        $file = str_replace('{{VALUE}}', str_replace("+", ' ', $value), $val);
                        if (!empty($value)) :
                            $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                        endif;
                    endif;
                }
            endif;
        }
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';

        echo '  <div class="shortcode-form-control-input-wrapper">
                    <input type="text"  class="shortcode-addons-family" id="' . $id . '" name="' . $id . '" value="' . $value . '" responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Date and Time Selector.
     *
     * @since 9.3.0
     */

    public function date_time_admin_control($id, array $data = [], array $arg = []) {
        $id = (array_key_exists('repeater', $arg) ? $id . ']' : $id);
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $format = 'date';
        if (array_key_exists('time', $arg)) :
            if ($arg['time'] == TRUE) :
                $format = 'datetime-local';
            endif;
        endif;
        echo '  <div class="shortcode-form-control-input-wrapper">
                    <input type="' . $format . '"  id="' . $id . '" name="' . $id . '" value="' . $value . '">
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Gradient Selector.
     *
     * @since 9.3.0
     */

    public function gradient_admin_control($id, array $data = [], array $arg = []) {
        $id = (array_key_exists('repeater', $arg) ? $id . ']' : $id);
        $value = array_key_exists($id, $data) ? $data[$id] : $arg['default'];
        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE) {

            if (array_key_exists('selector', $arg)) :

                foreach ($arg['selector'] as $key => $val) {
                    if ($arg['render'] == TRUE) :

                        $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                        $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                        $file = str_replace('{{VALUE}}', $value, $val);
                        if (!empty($value)) :

                            $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                        endif;
                    endif;
                }
            endif;
        }
        $background = (array_key_exists('gradient', $arg) ? $arg['gradient'] : '');
        echo '  <div class="shortcode-form-control-input-wrapper">
                    <input type="text" background="' . $background . '"  class="oxi-addons-gradient-color" id="' . $id . '" name="' . $id . '" value="' . $value . '" responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                </div>';
    }

    /*
     * Image Hover Style Admin Panel Dimensions Selector.
     *
     * @since 9.3.0
     */

    public function dimensions_admin_control($id, array $data = [], array $arg = []) {
        $unit = array_key_exists($id . '-choices', $data) ? $data[$id . '-choices'] : $arg['default']['unit'];
        $top = array_key_exists($id . '-top', $data) ? $data[$id . '-top'] : $arg['default']['size'];
        $bottom = array_key_exists($id . '-bottom', $data) ? $data[$id . '-bottom'] : $top;
        $left = array_key_exists($id . '-left', $data) ? $data[$id . '-left'] : $top;
        $right = array_key_exists($id . '-right', $data) ? $data[$id . '-right'] : $top;

        $retunvalue = array_key_exists('selector', $arg) ? htmlspecialchars(json_encode($arg['selector'])) : '';
        $ar = [$top, $bottom, $left, $right];
        $unlink = (count(array_unique($ar)) === 1 ? '' : 'link-dimensions-unlink');
        if (array_key_exists('selector-data', $arg) && $arg['selector-data'] == TRUE && $arg['render'] == TRUE) {
            if (array_key_exists('selector', $arg)) :
                if (isset($top) && isset($right) && isset($bottom) && isset($left)) :
                    foreach ($arg['selector'] as $key => $val) {
                        $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                        $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                        $file = str_replace('{{UNIT}}', $unit, $val);
                        $file = str_replace('{{TOP}}', $top, $file);
                        $file = str_replace('{{RIGHT}}', $right, $file);
                        $file = str_replace('{{BOTTOM}}', $bottom, $file);
                        $file = str_replace('{{LEFT}}', $left, $file);
                        $this->CSSDATA[$arg['responsive']][$class][$file] = $file;
                    }
                endif;
            endif;
        }

        if (array_key_exists('range', $arg)) :
            if (count($arg['range']) > 1) :
                echo ' <div class="shortcode-form-units-choices">';
                foreach ($arg['range'] as $key => $val) {
                    $rand = rand(10000, 233333333);
                    echo '<input id="' . $id . '-choices-' . $rand . '" type="radio" name="' . $id . '-choices"  value="' . $key . '" ' . ($key == $unit ? 'checked' : '') . '  min="' . $val['min'] . '" max="' . $val['max'] . '" step="' . $val['step'] . '">
                      <label class="shortcode-form-units-choices-label" for="' . $id . '-choices-' . $rand . '">' . $key . '</label>';
                }
                echo '</div>';
            endif;
        endif;
        $unitvalue = array_key_exists($id . '-choices', $data) ? 'unit="' . $data[$id . '-choices'] . '"' : '';
        echo '<div class="shortcode-form-control-input-wrapper">
                <ul class="shortcode-form-control-dimensions">
                    <li class="shortcode-form-control-dimension">
                        <input id="' . $id . '-top" input-id="' . $id . '" name="' . $id . '-top" type="number"  min="' . $arg['range'][$unit]['min'] . '" max="' . $arg['range'][$unit]['max'] . '" step="' . $arg['range'][$unit]['step'] . '" value="' . $top . '" default-value="' . $top . '" ' . $unitvalue . ' responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                        <label for="' . $id . '-top" class="shortcode-form-control-dimension-label">Top</label>
                    </li>
                    <li class="shortcode-form-control-dimension">
                       <input id="' . $id . '-right" input-id="' . $id . '" name="' . $id . '-right" type="number"  min="' . $arg['range'][$unit]['min'] . '" max="' . $arg['range'][$unit]['max'] . '" step="' . $arg['range'][$unit]['step'] . '" value="' . $right . '" default-value="' . $right . '" ' . $unitvalue . ' responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                         <label for="' . $id . '-right" class="shortcode-form-control-dimension-label">Right</label>
                    </li>
                    <li class="shortcode-form-control-dimension">
                       <input id="' . $id . '-bottom" input-id="' . $id . '" name="' . $id . '-bottom" type="number"  min="' . $arg['range'][$unit]['min'] . '" max="' . $arg['range'][$unit]['max'] . '" step="' . $arg['range'][$unit]['step'] . '" value="' . $bottom . '" default-value="' . $bottom . '" ' . $unitvalue . ' responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                       <label for="' . $id . '-bottom" class="shortcode-form-control-dimension-label">Bottom</label>
                    </li>
                    <li class="shortcode-form-control-dimension">
                        <input id="' . $id . '-left" input-id="' . $id . '" name="' . $id . '-left" type="number"  min="' . $arg['range'][$unit]['min'] . '" max="' . $arg['range'][$unit]['max'] . '" step="' . $arg['range'][$unit]['step'] . '" value="' . $left . '" default-value="' . $left . '" ' . $unitvalue . ' responsive="' . $arg['responsive'] . '" retundata=\'' . $retunvalue . '\'>
                         <label for="' . $id . '-left" class="shortcode-form-control-dimension-label">Left</label>
                    </li>
                    <li class="shortcode-form-control-dimension">
                        <button type="button" class="shortcode-form-link-dimensions ' . $unlink . '"  data-tooltip="Link values together"></button>
                    </li>
                </ul>
            </div>';
    }

    /*
     * Image Hover Style Admin Panel Column Size.
     *
     * @since 9.3.0
     * Complete Simple Interface
     */

    public function column_admin_group_control($id, array $data = [], array $arg = []) {

        $selector = array_key_exists('selector', $arg) ? $arg['selector'] : '';
        $select = array_key_exists('selector', $arg) ? 'selector' : '';
        $cond = $condition = '';
        if (array_key_exists('condition', $arg)) :
            $cond = 'condition';
            $condition = $arg['condition'];
        endif;
        $this->add_control(
                $lap = $id . '-lap',
                $data,
                [
                    'label' => __('Column Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'responsive' => 'laptop',
                    'description' => $arg['description'],
                    'default' => 'oxi-bt-col-lg-12',
                    'options' => [
                        'oxi-bt-col-lg-12' => __('Col 1', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-6' => __('Col 2', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-4' => __('Col 3', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-3' => __('Col 4', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-5' => __('Col 5', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-2' => __('Col 6', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-7' => __('Col 7', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-8' => __('Col 8', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-lg-1' => __('Col 12', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'description' => 'Define how much column you want to show into single rows. Customize possible with desktop or tab or mobile Settings.',
                    $select => $selector,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    $cond => $condition
                ]
        );
        $this->add_control(
                $tab = $id . '-tab',
                $data,
                [
                    'label' => __('Column Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'responsive' => 'tab',
                    'default' => 'oxi-bt-col-md-12',
                    'description' => $arg['description'],
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-md-12' => __('Col 1', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-md-6' => __('Col 2', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-md-4' => __('Col 3', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-md-3' => __('Col 4', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-md-2' => __('Col 6', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-md-1' => __('Col 12', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'description' => 'Define how much column you want to show into single rows. Customize possible with desktop or tab or mobile Settings.',
                    $select => $selector,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    $cond => $condition
                ]
        );
        $this->add_control(
                $mob = $id . '-mob',
                $data,
                [
                    'label' => __('Column Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'oxi-bt-col-lg-12',
                    'responsive' => 'mobile',
                    'description' => $arg['description'],
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-sm-12' => __('Col 1', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-sm-6' => __('Col 2', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-sm-4' => __('Col 3', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-sm-3' => __('Col 4', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-sm-5' => __('Col 5', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-sm-2' => __('Col 6', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-bt-col-sm-1' => __('Col 12', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'description' => 'Define how much column you want to show into single rows. Customize possible with desktop or tab or mobile Settings.',
                    $select => $selector,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    $cond => $condition
                ]
        );
    }

    /*
     * Image Hover Style Admin Panel Typography.
     *
     * @since 9.3.0
     * Simple Interface Enable
     */

    public function typography_admin_group_control($id, array $data = [], array $arg = []) {
        $cond = $condition = '';
        if (array_key_exists('condition', $arg)) :
            $cond = 'condition';
            $condition = $arg['condition'];
        endif;

        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;
        $selector_key = $selector = $selectorvalue = $loader = $loadervalue = '';
        if (array_key_exists('selector', $arg)) :
            $selectorvalue = 'selector-value';
            $selector_key = 'selector';
            $selector = $arg['selector'];
        endif;
        if (array_key_exists('loader', $arg)) :
            $loader = 'loader';
            $loadervalue = $arg['loader'];
        endif;

        $this->start_popover_control(
                $id,
                [
                    'label' => __('Typography', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    $cond => $condition,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    'description' => $arg['description'],
                    'separator' => $separator,
                ]
        );

        $this->add_control(
                $id . '-font',
                $data,
                [
                    'label' => __('Font Family', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::FONT,
                    $selectorvalue => 'font-family:"{{VALUE}}";',
                    $selector_key => $selector,
                    $loader => $loadervalue
                ]
        );
        $this->add_responsive_control(
                $id . '-size',
                $data,
                [
                    'label' => __('Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'font-size: {{SIZE}}{{UNIT}};',
                    $selector_key => $selector,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                        'rem' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                        'vm' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                    ],
                ]
        );
        $this->add_control(
                $id . '-weight',
                $data,
                [
                    'label' => __('Weight', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    $selectorvalue => 'font-weight: {{VALUE}};',
                    $loader => $loadervalue,
                    $selector_key => $selector,
                    'options' => [
                        '100' => __('100', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '200' => __('200', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '300' => __('300', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '400' => __('400', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '500' => __('500', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '600' => __('600', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '700' => __('700', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '800' => __('800', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '900' => __('900', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'normal' => __('Normal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bold' => __('Bold', OXI_IMAGE_HOVER_TEXTDOMAIN)
                    ],
                ]
        );
        $this->add_control(
                $id . '-transform',
                $data,
                [
                    'label' => __('Transform', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'uppercase' => __('Uppercase', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'lowercase' => __('Lowercase', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'capitalize' => __('Capitalize', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'none' => __('Normal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'text-transform: {{VALUE}};',
                    $selector_key => $selector,
                ]
        );
        $this->add_control(
                $id . '-style',
                $data,
                [
                    'label' => __('Style', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'normal' => __('normal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'italic' => __('Italic', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oblique' => __('Oblique', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'font-style: {{VALUE}};',
                    $selector_key => $selector,
                ]
        );
        $this->add_control(
                $id . '-decoration',
                $data,
                [
                    'label' => __('Decoration', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'underline' => __('Underline', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'overline' => __('Overline', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'line-through' => __('Line Through', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'none' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'text-decoration: {{VALUE}};',
                    $selector_key => $selector,
                ]
        );
        if (array_key_exists('include', $arg)) :
            if ($arg['include'] == 'align_normal') :
                $this->add_responsive_control(
                        $id . '-align',
                        $data,
                        [
                            'label' => __('Text Align', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'type' => Controls::SELECT,
                            'default' => '',
                            'options' => [
                                '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'left' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'center' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'right' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            ],
                            $loader => $loadervalue,
                            $selectorvalue => 'text-align: {{VALUE}};',
                            $selector_key => $selector,
                        ]
                );
            else :
                $this->add_responsive_control(
                        $id . '-justify',
                        $data,
                        [
                            'label' => __('Justify Content', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'type' => Controls::SELECT,
                            'default' => '',
                            'options' => [
                                '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'flex-start' => __('Flex Start', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'flex-end' => __('Flex End', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'center' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'space-around' => __('Space Around', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'space-between' => __('Space Between', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            ],
                            $loader => $loadervalue,
                            $selectorvalue => 'justify-content: {{VALUE}};',
                            $selector_key => $selector,
                        ]
                );
                $this->add_responsive_control(
                        $id . '-align',
                        $data,
                        [
                            'label' => __('Align Items', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'type' => Controls::SELECT,
                            'default' => '',
                            'options' => [
                                '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'stretch' => __('Stretch', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'baseline' => __('Baseline', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'center' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'flex-start' => __('Flex Start', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'flex-end' => __('Flex End', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            ],
                            $loader => $loadervalue,
                            $selectorvalue => 'align-items: {{VALUE}};',
                            $selector_key => $selector,
                        ]
                );
            endif;
        endif;

        $this->add_responsive_control(
                $id . '-l-height',
                $data,
                [
                    'label' => __('Line Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'line-height: {{SIZE}}{{UNIT}};',
                    $selector_key => $selector,
                ]
        );
        $this->add_responsive_control(
                $id . '-l-spacing',
                $data,
                [
                    'label' => __('Letter Spacing', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 0.1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.01,
                        ],
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'letter-spacing: {{SIZE}}{{UNIT}};',
                    $selector_key => $selector,
                ]
        );
        $this->end_popover_control();
    }

    /*
     * Image Hover Style Admin Panel Media Group Control.
     *
     * @since 9.3.0
     *
     * Works at any version
     */

    public function media_admin_group_control($id, array $data = [], array $arg = []) {
        $type = array_key_exists('default', $arg) ? $arg['default']['type'] : 'media-library';
        $value = array_key_exists('default', $arg) ? $arg['default']['link'] : '';
        $level = array_key_exists('label', $arg) ? $arg['label'] : 'Photo Source';
        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;
        echo '<div class="shortcode-form-control" style="padding: 0;" ' . $this->forms_condition($arg) . '>';
        $this->add_control(
                $id . '-select',
                $data,
                [
                    'label' => __($level, OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'loader' => TRUE,
                    'default' => $type,
                    'separator' => $separator,
                    'options' => [
                        'media-library' => [
                            'title' => __('Media Library', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'custom-url' => [
                            'title' => __('Custom URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ]
                    ],
                ]
        );
        $this->add_control(
                $id . '-image',
                $data,
                [
                    'label' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::IMAGE,
                    'loader' => TRUE,
                    'default' => $value,
                    'condition' => [
                        $id . '-select' => 'media-library',
                    ],
                    'simpledescription' => $arg['description'],
                    'description' => $arg['description'],
                ]
        );
        $this->add_control(
                $id . '-url',
                $data,
                [
                    'label' => __('Image URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXT,
                    'default' => $value,
                    'loader' => TRUE,
                    'placeholder' => 'www.example.com/image.jpg',
                    'condition' => [
                        $id . '-select' => 'custom-url',
                    ],
                    'simpledescription' => $arg['description'],
                    'description' => $arg['description'],
                ]
        );

        echo '</div>';
    }

    /*
     * Image Hover Style Admin Panel Box Shadow Control.
     *
     * @since 9.3.0
     * Only Works At Customizable Version
     */

    public function boxshadow_admin_group_control($id, array $data = [], array $arg = []) {

        $cond = $condition = $boxshadow = '';
        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;
        if (array_key_exists('condition', $arg)) :
            $cond = 'condition';
            $condition = $arg['condition'];
        endif;
        $true = TRUE;
        $selector_key = $selector = $selectorvalue = $loader = $loadervalue = '';
        if (!array_key_exists($id . '-shadow', $data)) :
            $data[$id . '-shadow'] = 'yes';
        endif;
        if (!array_key_exists($id . '-blur-size', $data)) :
            $data[$id . '-blur-size'] = 0;
        endif;
        if (!array_key_exists($id . '-horizontal-size', $data)) :
            $data[$id . '-horizontal-size'] = 0;
        endif;
        if (!array_key_exists($id . '-vertical-size', $data)) :
            $data[$id . '-vertical-size'] = 0;
        endif;

        if (array_key_exists($id . '-shadow', $data) && $data[$id . '-shadow'] == 'yes' && array_key_exists($id . '-color', $data) && array_key_exists($id . '-blur-size', $data) && array_key_exists($id . '-spread-size', $data) && array_key_exists($id . '-horizontal-size', $data) && array_key_exists($id . '-vertical-size', $data)) :
            $true = ($data[$id . '-blur-size'] == 0 || empty($data[$id . '-blur-size'])) && ($data[$id . '-spread-size'] == 0 || empty($data[$id . '-spread-size'])) && ($data[$id . '-horizontal-size'] == 0 || empty($data[$id . '-horizontal-size'])) && ($data[$id . '-vertical-size'] == 0 || empty($data[$id . '-vertical-size'])) ? TRUE : FALSE;
            $boxshadow = ($true == FALSE ? '-webkit-box-shadow:' . (array_key_exists($id . '-type', $data) ? $data[$id . '-type'] : '') . ' ' . $data[$id . '-horizontal-size'] . 'px ' . $data[$id . '-vertical-size'] . 'px ' . $data[$id . '-blur-size'] . 'px ' . $data[$id . '-spread-size'] . 'px ' . $data[$id . '-color'] . ';' : '');
            $boxshadow .= ($true == FALSE ? '-moz-box-shadow:' . (array_key_exists($id . '-type', $data) ? $data[$id . '-type'] : '') . ' ' . $data[$id . '-horizontal-size'] . 'px ' . $data[$id . '-vertical-size'] . 'px ' . $data[$id . '-blur-size'] . 'px ' . $data[$id . '-spread-size'] . 'px ' . $data[$id . '-color'] . ';' : '');
            $boxshadow .= ($true == FALSE ? 'box-shadow:' . (array_key_exists($id . '-type', $data) ? $data[$id . '-type'] : '') . ' ' . $data[$id . '-horizontal-size'] . 'px ' . $data[$id . '-vertical-size'] . 'px ' . $data[$id . '-blur-size'] . 'px ' . $data[$id . '-spread-size'] . 'px ' . $data[$id . '-color'] . ';' : '');
        endif;

        if (array_key_exists('selector', $arg)) :
            $selectorvalue = 'selector-value';
            $selector_key = 'selector';
            $selector = $arg['selector'];
            $boxshadow = array_key_exists($id . '-shadow', $data) && $data[$id . '-shadow'] == 'yes' ? $boxshadow : '';
            foreach ($arg['selector'] as $key => $val) {
                $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                $this->CSSDATA['laptop'][$class][$boxshadow] = $boxshadow;
            }
        endif;
        $this->start_popover_control(
                $id,
                [
                    'label' => __('Box Shadow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    $cond => $condition,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    'separator' => $separator,
                    'description' => $arg['description'],
                ]
        );
        $this->add_control(
                $id . '-shadow',
                $data,
                [
                    'label' => __('Shadow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => '',
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                ]
        );
        $this->add_control(
                $id . '-type',
                $data,
                [
                    'label' => __('Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'default' => '',
                    'options' => [
                        '' => [
                            'title' => __('Outline', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'inset' => [
                            'title' => __('Inset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                    ],
                    'condition' => [$id . '-shadow' => 'yes']
                ]
        );

        $this->add_control(
                $id . '-horizontal',
                $data,
                [
                    'label' => __('Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'custom' => $id . '|||||box-shadow',
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector,
                    'render' => FALSE,
                    'condition' => [$id . '-shadow' => 'yes']
                ]
        );
        $this->add_control(
                $id . '-vertical',
                $data,
                [
                    'label' => __('Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'custom' => $id . '|||||box-shadow',
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector,
                    'render' => FALSE,
                    'condition' => [$id . '-shadow' => 'yes']
                ]
        );
        $this->add_control(
                $id . '-blur',
                $data,
                [
                    'label' => __('Blur', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'custom' => $id . '|||||box-shadow',
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector,
                    'render' => FALSE,
                    'condition' => [$id . '-shadow' => 'yes']
                ]
        );
        $this->add_control(
                $id . '-spread',
                $data,
                [
                    'label' => __('Spread', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'custom' => $id . '|||||box-shadow',
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector,
                    'render' => FALSE,
                    'condition' => [$id . '-shadow' => 'yes']
                ]
        );
        $this->add_control(
                $id . '-color',
                $data,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'separator' => TRUE,
                    'type' => Controls::COLOR,
                    'oparetor' => 'RGB',
                    'default' => '#CCC',
                    'custom' => $id . '|||||box-shadow',
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector,
                    'render' => FALSE,
                    'condition' => [$id . '-shadow' => 'yes']
                ]
        );
        $this->end_popover_control();
    }

    /*
     * Image Hover Style Admin Panel Text Shadow .
     *
     * @since 9.3.0
     * Only Works at Customizable Options
     */

    public function textshadow_admin_group_control($id, array $data = [], array $arg = []) {

        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;
        $cond = $condition = $textshadow = '';
        if (array_key_exists('condition', $arg)) :
            $cond = 'condition';
            $condition = $arg['condition'];
        endif;
        $true = TRUE;
        $selector_key = $selector = $selectorvalue = $loader = $loadervalue = '';
        if (array_key_exists($id . '-color', $data) && array_key_exists($id . '-blur-size', $data) && array_key_exists($id . '-horizontal-size', $data) && array_key_exists($id . '-vertical-size', $data)) :
            $true = ($data[$id . '-blur-size'] == 0 || empty($data[$id . '-blur-size'])) && ($data[$id . '-horizontal-size'] == 0 || empty($data[$id . '-horizontal-size'])) && ($data[$id . '-vertical-size'] == 0 || empty($data[$id . '-vertical-size'])) ? TRUE : FALSE;
            $textshadow = ($true == FALSE ? 'text-shadow: ' . $data[$id . '-horizontal-size'] . 'px ' . $data[$id . '-vertical-size'] . 'px ' . $data[$id . '-blur-size'] . 'px ' . $data[$id . '-color'] . ';' : '');
        endif;
        if (array_key_exists('selector', $arg)) :
            $selectorvalue = 'selector-value';
            $selector_key = 'selector';
            $selector = $arg['selector'];
            foreach ($arg['selector'] as $key => $val) {
                $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                $this->CSSDATA['laptop'][$class][$textshadow] = $textshadow;
            }
        endif;
        $this->start_popover_control(
                $id,
                [
                    'label' => __('Text Shadow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    $cond => $condition,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    'separator' => $separator,
                    'description' => $arg['description'],
                ]
        );
        $this->add_control(
                $id . '-color',
                $data,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'oparetor' => 'RGB',
                    'default' => '#FFF',
                    'custom' => $id . '|||||text-shadow',
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector,
                    'render' => FALSE,
                ]
        );
        $this->add_control(
                $id . '-blur',
                $data,
                [
                    'label' => __('Blur', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'separator' => TRUE,
                    'custom' => $id . '|||||text-shadow',
                    'render' => FALSE,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector
                ]
        );
        $this->add_control(
                $id . '-horizontal',
                $data,
                [
                    'label' => __('Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'custom' => $id . '|||||text-shadow',
                    'render' => FALSE,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector
                ]
        );
        $this->add_control(
                $id . '-vertical',
                $data,
                [
                    'label' => __('Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'custom' => $id . '|||||text-shadow',
                    'render' => FALSE,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -50,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    $selectorvalue => '{{VALUE}}',
                    $selector_key => $selector
                ]
        );

        $this->end_popover_control();
    }

    /*
     * Image Hover Style Admin Panel Text Shadow .
     *
     * @since 9.3.0
     *
     * Simple Interface Enable
     */

    public function animation_admin_group_control($id, array $data = [], array $arg = []) {
        $cond = $condition = '';
        if (array_key_exists('condition', $arg)) :
            $cond = 'condition';
            $condition = $arg['condition'];
        endif;
        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;

        $this->start_popover_control(
                $id,
                [
                    'label' => __('Animation', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    $cond => $condition,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    'separator' => $separator,
                    'simpledescription' => 'Customize how long your animation will works',
                    'description' => 'Customize animation with animation type, Animation Duration with Delay and Looping Options',
                ]
        );
        $this->add_control(
                $id . '-type',
                $data,
                [
                    'label' => __('Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'optgroup0' => [true, 'Attention Seekers'],
                        '' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup1' => [false],
                        'optgroup2' => [true, 'Attention Seekers'],
                        'bounce' => __('Bounce', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'flash' => __('Flash', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'pulse' => __('Pulse', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'rubberBand' => __('RubberBand', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'shake' => __('Shake', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'swing' => __('Swing', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'tada' => __('Tada', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'wobble' => __('Wobble', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'jello' => __('Jello', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup3' => [false],
                        'optgroup4' => [true, 'Bouncing Entrances'],
                        'bounceIn' => __('BounceIn', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bounceInDown' => __('BounceInDown', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bounceInLeft' => __('BounceInLeft', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bounceInRight' => __('BounceInRight', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bounceInUp' => __('BounceInUp', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup5' => [false],
                        'optgroup6' => [true, 'Fading Entrances'],
                        'fadeIn' => __('FadeIn', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInDown' => __('FadeInDown', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInDownBig' => __('FadeInDownBig', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInLeft' => __('FadeInLeft', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInLeftBig' => __('FadeInLeftBig', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInRight' => __('FadeInRight', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInRightBig' => __('FadeInRightBig', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInUp' => __('FadeInUp', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fadeInUpBig' => __('FadeInUpBig', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup7' => [false],
                        'optgroup8' => [true, 'Flippers'],
                        'flip' => __('Flip', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'flipInX' => __('FlipInX', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'flipInY' => __('FlipInY', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup9' => [false],
                        'optgroup10' => [true, 'Lightspeed'],
                        'lightSpeedIn' => __('LightSpeedIn', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup11' => [false],
                        'optgroup12' => [true, 'Rotating Entrances'],
                        'rotateIn' => __('RotateIn', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'rotateInDownLeft' => __('RotateInDownLeft', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'rotateInDownRight' => __('RotateInDownRight', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'rotateInUpLeft' => __('RotateInUpLeft', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'rotateInUpRight' => __('RotateInUpRight', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup13' => [false],
                        'optgroup14' => [true, 'Sliding Entrances'],
                        'slideInUp' => __('SlideInUp', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'slideInDown' => __('SlideInDown', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'slideInLeft' => __('SlideInLeft', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'slideInRight' => __('SlideInRight', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup15' => [false],
                        'optgroup16' => [true, 'Zoom Entrances'],
                        'zoomIn' => __('ZoomIn', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'zoomInDown' => __('ZoomInDown', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'zoomInLeft' => __('ZoomInLeft', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'zoomInRight' => __('ZoomInRight', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'zoomInUp' => __('ZoomInUp', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup17' => [false],
                        'optgroup18' => [true, 'Specials'],
                        'hinge' => __('Hinge', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'rollIn' => __('RollIn', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'optgroup19' => [false],
                    ],
                ]
        );
        $this->add_control(
                $id . '-duration',
                $data,
                [
                    'label' => __('Duration (ms)', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 1000,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 00,
                            'max' => 10000,
                            'step' => 100,
                        ],
                    ],
                    'condition' => [
                        $id . '-type' => 'EMPTY',
                    ],
                ]
        );
        $this->add_control(
                $id . '-delay',
                $data,
                [
                    'label' => __('Delay (ms)', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 00,
                            'max' => 10000,
                            'step' => 100,
                        ],
                    ],
                    'condition' => [
                        $id . '-type' => 'EMPTY',
                    ],
                ]
        );
        $this->add_control(
                $id . '-offset',
                $data,
                [
                    'label' => __('Offset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 100,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'condition' => [
                        $id . '-type' => 'EMPTY',
                    ],
                ]
        );
        $this->add_control(
                $id . '-looping',
                $data,
                [
                    'label' => __('Looping', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => '',
                    'loader' => TRUE,
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'condition' => [
                        $id . '-type' => 'EMPTY',
                    ],
                ]
        );
        $this->end_popover_control();
    }

    /*
     * Image Hover Style Admin Panel Border .
     *
     * @since 9.3.0
     * Complete Simple Version
     */

    public function border_admin_group_control($id, array $data = [], array $arg = []) {
        $cond = $condition = '';
        if (array_key_exists('condition', $arg)) :
            $cond = 'condition';
            $condition = $arg['condition'];
        endif;
        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;
        $selector_key = $selector = $selectorvalue = $loader = $loadervalue = $render = '';
        if (array_key_exists('selector', $arg)) :
            $selectorvalue = 'selector-value';
            $selector_key = 'selector';
            $selector = $arg['selector'];
        endif;
        if (array_key_exists('loader', $arg)) :
            $loader = 'loader';
            $loadervalue = $arg['loader'];
        endif;
        if (array_key_exists($id . '-type', $data) && $data[$id . '-type'] == '') :
            $render = 'render';
        endif;

        $this->start_popover_control(
                $id,
                [
                    'label' => __('Border', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    $cond => $condition,
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    'separator' => $separator,
                    'description' => $arg['description'],
                ]
        );
        $this->add_control(
                $id . '-type',
                $data,
                [
                    'label' => __('Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        '' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'solid' => __('Solid', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'dotted' => __('Dotted', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'dashed' => __('Dashed', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'double' => __('Double', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'groove' => __('Groove', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'ridge' => __('Ridge', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'inset' => __('Inset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'outset' => __('Outset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'hidden' => __('Hidden', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'border-style: {{VALUE}};',
                    $selector_key => $selector,
                ]
        );
        $this->add_responsive_control(
                $id . '-width',
                $data,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    $render => FALSE,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.01,
                        ],
                    ],
                    'condition' => [
                        $id . '-type' => 'EMPTY',
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    $selector_key => $selector,
                ]
        );
        $this->add_control(
                $id . '-color',
                $data,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    $render => FALSE,
                    'default' => '',
                    $loader => $loadervalue,
                    $selectorvalue => 'border-color: {{VALUE}};',
                    $selector_key => $selector,
                    'condition' => [
                        $id . '-type' => 'EMPTY',
                    ],
                ]
        );
        $this->end_popover_control();
    }

    /*
     * Image Hover Style Admin Panel Background .
     *
     * @since 9.3.0
     * Simple Interface Enable
     */

    public function background_admin_group_control($id, array $data = [], array $arg = []) {

        $backround = '';
        $render = FALSE;
        if (array_key_exists($id . '-color', $data)) :
            $color = $data[$id . '-color'];
            if (array_key_exists($id . '-img', $data) && $data[$id . '-img'] == 'yes') :
                if (strpos(strtolower($color), 'gradient') === FALSE) :
                    $color = 'linear-gradient(0deg, ' . $color . ' 0%, ' . $color . ' 100%)';
                endif;
                if ($data[$id . '-select'] == 'media-library') :
                    $backround .= 'background: ' . $color . ', url(\'' . $data[$id . '-image'] . '\') ' . $data[$id . '-repeat'] . ' ' . $data[$id . '-position'] . ';';
                else :
                    $backround .= 'background: ' . $color . ', url(\'' . $data[$id . '-url'] . '\') ' . $data[$id . '-repeat'] . ' ' . $data[$id . '-position'] . ';';
                endif;
            else :
                $backround .= 'background: ' . $color . ';';
            endif;
        endif;
        if (array_key_exists('selector', $arg)) :
            foreach ($arg['selector'] as $key => $val) {
                $key = (strpos($key, '{{KEY}}') ? str_replace('{{KEY}}', explode('saarsa', $id)[1], $key) : $key);
                $class = str_replace('{{WRAPPER}}', $this->CSSWRAPPER, $key);
                $this->CSSDATA['laptop'][$class][$backround] = $backround;
                $render = TRUE;
            }
        endif;

        $selector_key = $selector = $selectorvalue = $loader = $loadervalue = '';
        if (array_key_exists('selector', $arg)) :
            $selectorvalue = 'selector-value';
            $selector_key = 'selector';
            $selector = $arg['selector'];
        endif;
        if (array_key_exists('loader', $arg)) :
            $loader = 'loader';
            $loadervalue = $arg['loader'];
        endif;
        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;
        $this->start_popover_control(
                $id,
                [
                    'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'condition' => array_key_exists('condition', $arg) ? $arg['condition'] : '',
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    'separator' => $separator,
                    'simpledescription' => $arg['simpledescription'],
                    'description' => $arg['description'],
                ]
        );
        $this->add_control(
                $id . '-color',
                $data,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::GRADIENT,
                    'gradient' => $id,
                    'oparetor' => 'RGB',
                    'render' => FALSE,
                    $selectorvalue => '',
                    $selector_key => $selector,
                ]
        );

        $this->add_control(
                $id . '-img',
                $data,
                [
                    'label' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                ]
        );
        $this->add_control(
                $id . '-select',
                $data,
                [
                    'label' => __('Photo Source', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'separator' => TRUE,
                    'loader' => TRUE,
                    'type' => Controls::CHOOSE,
                    'default' => 'media-library',
                    'options' => [
                        'media-library' => [
                            'title' => __('Media Library', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'custom-url' => [
                            'title' => __('Custom URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ]
                    ],
                    'condition' => [
                        $id . '-img' => 'yes',
                    ],
                ]
        );
        $this->add_control(
                $id . '-image',
                $data,
                [
                    'label' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::IMAGE,
                    'default' => '',
                    'loader' => TRUE,
                    'condition' => [
                        $id . '-select' => 'media-library',
                        $id . '-img' => 'yes',
                    ],
                ]
        );
        $this->add_control(
                $id . '-url',
                $data,
                [
                    'label' => __('Image URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXT,
                    'default' => '',
                    'loader' => TRUE,
                    'placeholder' => 'www.example.com/image.jpg',
                    'condition' => [
                        $id . '-select' => 'custom-url',
                        $id . '-img' => 'yes',
                    ],
                ]
        );
        $this->add_control(
                $id . '-position',
                $data,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'center center',
                    'render' => $render,
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'top left' => __('Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'top center' => __('Top Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'top right' => __('Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'center left' => __('Center Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'center center' => __('Center Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'center right' => __('Center Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bottom left' => __('Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bottom center' => __('Bottom Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bottom right' => __('Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'loader' => TRUE,
                    'condition' => [
                        $id . '-img' => 'yes',
                        '((' . $id . '-select === \'media-library\' && ' . $id . '-image !== \'\') || (' . $id . '-select === \'custom-url\' && ' . $id . '-url !== \'\'))' => 'COMPILED',
                    ],
                ]
        );
        $this->add_control(
                $id . '-attachment',
                $data,
                [
                    'label' => __('Attachment', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'render' => $render,
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'scroll' => __('Scroll', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'fixed' => __('Fixed', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'background-attachment: {{VALUE}};',
                    $selector_key => $selector,
                    'condition' => [
                        $id . '-img' => 'yes',
                        '((' . $id . '-select === \'media-library\' && ' . $id . '-image !== \'\') || (' . $id . '-select === \'custom-url\' && ' . $id . '-url !== \'\'))' => 'COMPILED',
                    ],
                ]
        );
        $this->add_control(
                $id . '-repeat',
                $data,
                [
                    'label' => __('Repeat', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'no-repeat',
                    'render' => $render,
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'no-repeat' => __('No-Repeat', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'repeat' => __('Repeat', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'repeat-x' => __('Repeat-x', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'repeat-y' => __('Repeat-y', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'loader' => TRUE,
                    'condition' => [
                        $id . '-img' => 'yes',
                        '((' . $id . '-select === \'media-library\' && ' . $id . '-image !== \'\') || (' . $id . '-select === \'custom-url\' && ' . $id . '-url !== \'\'))' => 'COMPILED',
                    ],
                ]
        );
        $this->add_responsive_control(
                $id . '-size',
                $data,
                [
                    'label' => __('Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'cover',
                    'render' => $render,
                    'options' => [
                        '' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'auto' => __('Auto', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'cover' => __('Cover', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'contain' => __('Contain', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    $loader => $loadervalue,
                    $selectorvalue => 'background-size: {{VALUE}};',
                    $selector_key => $selector,
                    'condition' => [
                        $id . '-img' => 'yes',
                        '((' . $id . '-select === \'media-library\' && ' . $id . '-image !== \'\') || (' . $id . '-select === \'custom-url\' && ' . $id . '-url !== \'\'))' => 'COMPILED',
                    ],
                ]
        );
        $this->end_popover_control();
    }

    /*
     * Image Hover Style Admin Panel Background .
     *
     * @since 9.3.0
     * Simple Interfaece Enable
     */

    public function url_admin_group_control($id, array $data = [], array $arg = []) {
        if (array_key_exists('condition', $arg)) :
            $cond = 'condition';
            $condition = $arg['condition'];
        else :
            $cond = $condition = '';
        endif;
        $form_condition = array_key_exists('form_condition', $arg) ? $arg['form_condition'] : '';
        $separator = array_key_exists('separator', $arg) ? $arg['separator'] : FALSE;
        $this->add_control(
                $id . '-url',
                $data,
                [
                    'label' => __('Link', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXT,
                    'link' => TRUE,
                    'separator' => $separator,
                    'placeholder' => 'http://www.example.com/',
                    'form_condition' => (array_key_exists('form_condition', $arg) ? $arg['form_condition'] : ''),
                    $cond => $condition
                ]
        );
        echo '<div class="shortcode-form-control-content shortcode-form-control-content-popover-body">';

        $this->add_control(
                $id . '-target',
                $data,
                [
                    'label' => __('New Window?', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => '',
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                ]
        );
        $this->add_control(
                $id . '-follow',
                $data,
                [
                    'label' => __('No Follow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => '',
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                ]
        );
        $this->add_control(
                $id . '-id',
                $data,
                [
                    'label' => __('CSS ID', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXT,
                    'default' => '',
                    'placeholder' => 'Your Custom ID for this link',
                ]
        );
        echo '</div>' . (array_key_exists('description', $arg) ? '<div class="shortcode-form-control-description">' . $arg['description'] . '</div>' : '') . '</div>';
    }

    /*
     *
     *
     * Templates Substitute Data
     *
     *
     *
     *
     */
    /*
     * Image Hover Style Admin Panel Template Substitute Control.
     *
     * @since 9.3.0
     */

    public function add_substitute_control($id, array $data = [], array $arg = []) {
        $fun = $arg['type'] . '_substitute_control';
        echo $this->$fun($id, $data, $arg);
    }

    /*
     * Image Hover Style Admin Panel Template Substitute Modal Opener.
     *
     * @since 9.3.0
     */

    public function modalopener_substitute_control($id, array $data = [], array $arg = []) {
        $default = [
            'showing' => FALSE,
            'title' => 'Add New Items',
            'sub-title' => 'Add New Items'
        ];
        $arg = array_merge($default, $arg);
        /*
         * $arg['title'] = 'Add New Items';
         * $arg['sub-title'] = 'Add New Items 02';
         *
         */
        $condition = $this->forms_condition($arg);
        echo ' <div class="oxi-addons-item-form shortcode-addons-templates-right-panel ' . (($arg['showing']) ? '' : 'oxi-admin-head-d-none') . '"  ' . $condition . '>
                    <div class="oxi-addons-item-form-heading shortcode-addons-templates-right-panel-heading">
                        ' . $arg['title'] . '
                         <div class="oxi-head-toggle"></div>
                         </div>
                    <div class="oxi-addons-item-form-item shortcode-addons-templates-right-panel-body" id="oxi-addons-list-data-modal-open">
                        <span>
                            <i class="dashicons dashicons-plus-alt oxi-icons"></i>
                            ' . $arg['sub-title'] . '
                        </span>
                    </div>
                </div>';
    }

    public function shortcodestyle_substitute_control($id, array $data = [], array $arg = []) {
        if (count($this->StyleChanger) > 0) :
            $default = [
                'showing' => FALSE,
                'title' => 'Template Changer',
            ];
            $arg = array_merge($default, $arg);
            /*
             * $arg['title'] = 'Add New Items';
             * $arg['sub-title'] = 'Add New Items 02';
             *
             */
            echo '  <div class="oxi-addons-shortcode  shortcode-addons-templates-right-panel ' . (($arg['showing']) ? '' : 'oxi-admin-head-d-none') . '">
                        <div class="oxi-addons-shortcode-heading  shortcode-addons-templates-right-panel-heading">
                            ' . $arg['title'] . ' ' . ((apply_filters('oxi-image-hover-plugin-version', false) == false) ? '<span style="color:red">Pro Only</span>' : '') . '
                            <div class="oxi-head-toggle"></div>
                        </div>
                        <div class="oxi-addons-shortcode-body  shortcode-addons-templates-right-panel-body">
                            <form method="post" id="shortcode-addons-style-change-submit">
                                <div class="form-row">
                                    <div class="form-group col-md-8">
                                        <label for="exampleFormControlSelect1">Current Template</label>
                                        <select class="form-control" name="shortcode-current-style-name" id="shortcode-current-style-name">';
            foreach ($this->StyleChanger as $val) {
                echo '                      <option value="' . strtolower($val) . '" ' . (strtolower($this->dbdata['style_name']) == strtolower($val) ? 'selected' : '') . '>' . ucfirst($val) . '</option>';
            }
            echo '                      </select>
                                    </div>
                                    <div class="form-group col-md-4 pt-4 mt-2">
                                        <input type="hidden" class="form-control" name="shortcode-addons-style-change-submit-id" id="shortcode-addons-style-change-submit-id" value="' . $this->oxiid . '">
                                        <button type="submit" id="shortcode-addons-style-change-submit-button" class="btn btn-primary" premium="' . ((apply_filters('oxi-image-hover-plugin-version', false) == false) ? '' : 'ache') . '">Save</button>
                                    </div>
                                </div>
                              <p style="color:red" >Template change is not recommended. We suggest it only for developer. Please change it if you have much knowledge in HTML or can create style from zero level</p>
                            </form>
                        </div>
                    </div>';
        endif;
    }

    /*
     * Image Hover Style Admin Panel Template Shortcode name.
     *
     * @since 9.3.0
     */

    public function shortcodename_substitute_control($id, array $data = [], array $arg = []) {
        $default = [
            'showing' => FALSE,
            'title' => 'Shortcode Name',
            'placeholder' => 'Set Your Shortcode Name'
        ];
        $arg = array_merge($default, $arg);
        /*
         * $arg['title'] = 'Add New Items';
         * $arg['sub-title'] = 'Add New Items 02';
         *
         */
        echo ' <div class="oxi-addons-shortcode  shortcode-addons-templates-right-panel ' . (($arg['showing']) ? '' : 'oxi-admin-head-d-none') . '">
                <div class="oxi-addons-shortcode-heading  shortcode-addons-templates-right-panel-heading">
                    ' . $arg['title'] . '
                    <div class="oxi-head-toggle"></div>
                </div>
                <div class="oxi-addons-shortcode-body  shortcode-addons-templates-right-panel-body">
                    <form method="post" id="shortcode-addons-name-change-submit">
                        <div class="input-group my-2">
                            <input type="hidden" class="form-control" name="addonsstylenameid" value="' . $data['id'] . '">
                            <input type="text" class="form-control" name="addonsstylename" placeholder=" ' . $arg['placeholder'] . '" value="' . $data['name'] . '">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-success" id="addonsstylenamechange">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
    }

    /*
     * Image Hover Style Admin Panel Template Shortcode Info.
     *
     * @since 9.3.0
     */

    public function shortcodeinfo_substitute_control($id, array $data = [], array $arg = []) {
        $default = [
            'showing' => FALSE,
            'title' => 'Shortcode',
        ];
        $arg = array_merge($default, $arg);
        /*
         * $arg['title'] = 'Add New Items';
         * $arg['sub-title'] = 'Add New Items 02';
         *
         */
        echo ' <div class="oxi-addons-shortcode shortcode-addons-templates-right-panel ' . (($arg['showing']) ? '' : 'oxi-admin-head-d-none') . '">
                <div class="oxi-addons-shortcode-heading  shortcode-addons-templates-right-panel-heading">
                    ' . $arg['title'] . '
                    <div class="oxi-head-toggle"></div>
                </div>
                <div class="oxi-addons-shortcode-body shortcode-addons-templates-right-panel-body">
                    <em>Shortcode for posts/pages/plugins</em>
                    <p>Copy &amp;
                    paste the shortcode directly into any WordPress post, page or Page Builder.</p>
                    <input type="text" class="form-control" onclick="this.setSelectionRange(0, this.value.length)" value="[iheu_ultimate_oxi id=&quot;' . $id . '&quot;]">
                    <span></span>
                    <em>Shortcode for templates/themes</em>
                    <p>Copy &amp;
                    paste this code into a template file to include the slideshow within your theme.</p>
                    <input type="text" class="form-control" onclick="this.setSelectionRange(0, this.value.length)" value="<?php echo do_shortcode(\'[iheu_ultimate_oxi  id=&quot;' . $id . '&quot;]\'); ?>">
                    <span></span>
                    </div>
                    </div>';
    }

    public function rearrange_substitute_control($id, array $data = [], array $arg = []) {
        $default = [
            'showing' => FALSE,
            'title' => 'Image Hover Rearrange',
            'sub-title' => 'Image Hover Rearrange'
        ];
        $arg = array_merge($default, $arg);
        /*
         * $arg['title'] = 'Add New Items';
         * $arg['sub-title'] = 'Add New Items 02';
         *
         */
        $condition = $this->forms_condition($arg);
        echo ' <div class="oxi-addons-item-form shortcode-addons-templates-right-panel ' . (($arg['showing']) ? '' : 'oxi-admin-head-d-none') . '" ' . $condition . '>
                    <div class="oxi-addons-item-form-heading shortcode-addons-templates-right-panel-heading">
                        ' . $arg['title'] . '
                        <div class="oxi-head-toggle"></div>
                    </div>
                    <div class="oxi-addons-item-form-item shortcode-addons-templates-right-panel-body"
                        id="oxi-addons-rearrange-data-modal-open">
                        <span>
                            <i class="dashicons dashicons-plus-alt oxi-icons"></i>
                            ' . $arg['sub-title'] . '
                        </span>
                    </div>
                </div>
                <div id="oxi-addons-list-rearrange-modal" class="modal fade bd-example-modal-sm" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <form id="oxi-addons-form-rearrange-submit">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Image Rearrange</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-12 alert text-center" id="oxi-addons-list-rearrange-saving">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <ul class="col-12 list-group" id="oxi-addons-modal-rearrange">
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" id="oxi-addons-list-rearrange-data">
                                    <button type="button" id="oxi-addons-list-rearrange-close" class="btn btn-danger"
                                        data-dismiss="modal">Close</button>
                                    <input type="submit" id="oxi-addons-list-rearrange-submit" class="btn btn-primary" value="Save">
                                </div>
                            </div>
                        </form>
                        <div id="modal-rearrange-store-file">
                            ' . $id . '
                        </div>
                    </div>
                </div>';
    }

}
