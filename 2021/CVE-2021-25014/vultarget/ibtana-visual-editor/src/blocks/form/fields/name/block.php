<?php
/**
 * Form Field Name block.
 *
 * @package ive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class IVE_Form_Field_Name_Block
 */
class IVE_Form_Field_Name_Block {
    /**
     * IVE_Form_Field_Name_Block constructor.
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
            'ive/form-field-name',
            array(
                'parent'          => array( 'ive/form' ),
                'render_callback' => array( $this, 'block_render' ),
                'attributes'      => IVE_Form_Field_Attributes::get_block_attributes(
                    array(
                        'label' => array(
                            'default' => esc_html__( 'Name', 'ibtana-visual-editor' ),
                        ),
                        'uniqueID' => array(
                            'type' => 'string',
                            'default' => ''
                        ),
                        'nameFields' => array(
                            'type'    => 'string',
                            'default' => 'first',
                        ),
                        'descriptionLast' => array(
                            'type' => 'string',
                        ),
                        'placeholderLast' => array(
                            'type' => 'string',
                        ),
                        'defaultLast' => array(
                            'type' => 'string',
                        ),
                        'descriptionMiddle' => array(
                            'type' => 'string',
                        ),
                        'placeholderMiddle' => array(
                            'type' => 'string',
                        ),
                        'defaultMiddle' => array(
                            'type' => 'string',
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
                'slug'              => '',
                'nameFields'        => 'first',
                'description'       => '',
                'descriptionLast'   => '',
                'placeholderLast'   => '',
                'defaultLast'       => '',
                'descriptionMiddle' => '',
                'placeholderMiddle' => '',
                'defaultMiddle'     => '',
                'hideDescription'   => false,
            ),
            $attributes
        );

        ob_start();

        $uniqueID = $attributes['uniqueID'];

        $class = 'ive-form-field ive-form-field-name';

        if ( 'first-middle-last' === $attributes['nameFields'] || 'first-last' === $attributes['nameFields'] ) {
            $class .= ' ive-form-field-name-with-last';
        }
        if ( 'first-middle-last' === $attributes['nameFields'] ) {
            $class .= ' ive-form-field-name-with-middle';
        }

        if ( isset( $attributes['className'] ) ) {
            $class .= ' ' . $attributes['className'];
        }
        $class .= ' form_name'.$uniqueID;
        $class .= ' '.$attributes['animationStyle'].'-'.$attributes['animationType'];

        $last_attributes = array(
            'slug'            => $attributes['slug'],
            'slug_sub'        => 'last',
            'description'     => $attributes['descriptionLast'],
            'placeholder'     => $attributes['placeholderLast'],
            'default'         => $attributes['defaultLast'],
            'hideDescription' => $attributes['hideDescription'],
        );

        $middle_attributes = array(
            'slug'            => $attributes['slug'],
            'slug_sub'        => 'middle',
            'description'     => $attributes['descriptionMiddle'],
            'placeholder'     => $attributes['placeholderMiddle'],
            'default'         => $attributes['defaultMiddle'],
            'hideDescription' => $attributes['hideDescription'],
        );

        ?>

        <div class="<?php echo esc_attr( $class ); ?>">
            <?php IVE_Form_Field_Label::get( $attributes ); ?>

            <?php if ( 'first-last' === $attributes['nameFields'] || 'first-middle-last' === $attributes['nameFields'] ) : ?>
                <div class="ive-form-field-row">
                    <div class="ive-form-field-name-first">
            <?php endif; ?>

                <input type="text" <?php IVE_Form_Field_Attributes::get( $attributes ); ?> />

                <?php IVE_Form_Field_Description::get( $attributes ); ?>

            <?php if ( 'first-middle-last' === $attributes['nameFields'] ) : ?>
                    </div>
                    <div class="ive-form-field-name-middle">
                        <input type="text" <?php IVE_Form_Field_Attributes::get( $middle_attributes ); ?> />

                        <?php IVE_Form_Field_Description::get( $middle_attributes ); ?>
            <?php endif; ?>

            <?php if ( 'first-last' === $attributes['nameFields'] || 'first-middle-last' === $attributes['nameFields'] ) : ?>
                    </div>
                    <div class="ive-form-field-name-last">
                        <input type="text" <?php IVE_Form_Field_Attributes::get( $last_attributes ); ?> />

                        <?php IVE_Form_Field_Description::get( $last_attributes ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php

        return ob_get_clean();
    }
}
new IVE_Form_Field_Name_Block();
