<?php
/**
 * Form Field Select block.
 *
 * @package ive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class IVE_Form_Field_Select_Block
 */
class IVE_Form_Field_Select_Block {
    /**
     * IVE_Form_Field_Select_Block constructor.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    /**
     * Init.
     */
    public function init() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type(
            'ive/form-field-select',
            array(
                'parent'          => array( 'ive/form' ),
                'render_callback' => array( $this, 'block_render' ),
                'attributes'      => IVE_Form_Field_Attributes::get_block_attributes(
                    array(
                        'label' => array(
                            'default' => esc_html__( 'Select', 'ibtana-visual-editor' ),
                        ),
                        'uniqueID' => array(
                            'type' => 'string',
                            'default' => ''
                        ),
                        'options' => array(
                            'type'  => 'array',
                            'items' => array(
                                'type' => 'object',
                            ),
                        ),
                        'multiple' => array(
                            'type' => 'boolean',
                        ),
                        'frameNormalBorderStyle' => array(
                            'type' => 'array',
                            'default' => array( 'none', 'none', 'none' )
                        ),
                        'frameNormalBorderColor' => array(
                            'type' => 'array',
                            'default' => array( '', '', '' )
                        ),
                        'frameNormalBorderWidth' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalboxshadcolor' => array(
                            'type' => 'array',
                            'default' => array( '', '', '' )
                        ),
                        'frameNormalboxshadx' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalboxshady' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalboxshadblur' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalboxshadspread' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalBorderRadius' => array(
                            'type' => 'array',
                            'default' => array(
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 )
                            ),
                        ),
                        'frameNormalHovBorderStyle' => array(
                            'type' => 'array',
                            'default' => array( 'none', 'none', 'none' )
                        ),
                        'frameNormalHovBorderColor' => array(
                            'type' => 'array',
                            'default' => array( '', '', '' )
                        ),
                        'frameNormalHovBorderWidth' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalHovboxshadcolor' => array(
                            'type' => 'array',
                            'default' => array( '', '', '' )
                        ),
                        'frameNormalHovboxshadx' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalHovboxshady' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalHovboxshadblur' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalHovboxshadspread' => array(
                            'type' => 'array',
                            'default' => array( 0, 0, 0 )
                        ),
                        'frameNormalHovBorderRadius' => array(
                            'type' => 'array',
                            'default' => array(
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 )
                            ),
                        ),
                        'spacingMargin' => array(
                            'type' => 'array',
                            'default' => array(
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 )
                            ),
                        ),
                        'spacingPadding' => array(
                            'type' => 'array',
                            'default' => array(
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 ),
                              array( 0, 0, 0, 0 )
                            ),
                        ),
                        'displayFields' => array(
                            'type' => 'array',
                            'default' => array( 'true', 'true', 'true' )
                        ),
                        'animationStyle' => array(
                            'type' => 'string',
                            'default' => 'none'
                        ),
                        'animationType' => array(
                            'type' => 'string',
                            'default' => 'center'
                        ),
                    )
                ),
            )
        );
    }

    /**
     * Register gutenberg block output
     *
     * @param array $attributes - block attributes.
     *
     * @return string
     */
    public function block_render( $attributes ) {
        $attributes = array_merge(
            array(
                'options'  => array(),
                'multiple' => false,
            ),
            $attributes
        );

        $options = $attributes['options'];
        if ( ! is_array( $options ) ) {
            $options = array();
        }

        // Add null option.
        if ( ! $attributes['multiple'] ) {
            $add_null_option = true;

            foreach ( $options as $data ) {
                if ( $data['selected'] ) {
                    $add_null_option = false;
                }
            }

            if ( $add_null_option ) {
                array_unshift(
                    $options,
                    array(
                        'label'    => esc_attr__( '--- Select ---', 'ibtana-visual-editor' ),
                        'value'    => '',
                        'selected' => true,
                    )
                );
            }
        }

        ob_start();

        $uniqueID = $attributes['uniqueID'];

        $class = 'ive-form-field ive-form-field-select';

        if ( isset( $attributes['className'] ) ) {
            $class .= ' ' . $attributes['className'];
        }
        $class .= ' form_select'.$uniqueID;
        $class .= ' '.$attributes['animationStyle'].'-'.$attributes['animationType'];

        ?>

        <div class="<?php echo esc_attr( $class ); ?>">
            <?php IVE_Form_Field_Label::get( $attributes ); ?>

            <select <?php IVE_Form_Field_Attributes::get( $attributes ); ?>>
                <?php foreach ( $options as $option ) : ?>
                    <option <?php selected( $option['selected'] ); ?> value="<?php echo esc_attr( $option['value'] ); ?>">
                        <?php echo esc_html( $option['label'] ); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php IVE_Form_Field_Description::get( $attributes ); ?>
        </div>

        <?php

        return ob_get_clean();
    }
}
new IVE_Form_Field_Select_Block();
