<?php
/**
 * Form Field Email block.
 *
 * @package ive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class IVE_Form_Field_Email_Block
 */
class IVE_Form_Field_Email_Block {
    /**
     * IVE_Form_Field_Email_Block constructor.
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
            'ive/form-field-email',
            array(
                'parent'          => array( 'ive/form' ),
                'render_callback' => array( $this, 'block_render' ),
                'attributes'      => IVE_Form_Field_Attributes::get_block_attributes(
                    array(
                        'label' => array(
                            'default' => esc_html__( 'Email', 'ibtana-visual-editor' ),
                        ),
                        'uniqueID' => array(
                            'type' => 'string',
                            'default' => ''
                        ),
                        'emailConfirmation' => array(
                            'type' => 'boolean',
                        ),
                        'descriptionConfirmation' => array(
                            'type' => 'string',
                        ),
                        'placeholderConfirmation' => array(
                            'type' => 'string',
                        ),
                        'defaultConfirmation' => array(
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
                'slug'                    => '',
                'description'             => '',
                'descriptionConfirmation' => '',
                'placeholderConfirmation' => '',
                'defaultConfirmation'     => '',
                'emailConfirmation'       => false,
                'hideDescription'         => false,
            ),
            $attributes
        );

        ob_start();

        $uniqueID = $attributes['uniqueID'];

        $class = 'ive-form-field ive-form-field-email';

        if ( isset( $attributes['className'] ) ) {
            $class .= ' ' . $attributes['className'];
        }
        $class .= ' form_Email'.$uniqueID;
        $class .= ' '.$attributes['animationStyle'].'-'.$attributes['animationType'];

        $confirmation_attributes = array(
            'slug'            => $attributes['slug'],
            'slug_sub'        => 'validation',
            'description'     => $attributes['descriptionConfirmation'],
            'placeholder'     => $attributes['placeholderConfirmation'],
            'default'         => $attributes['defaultConfirmation'],
            'hideDescription' => $attributes['hideDescription'],
        );

        ?>

        <div class="<?php echo esc_attr( $class ); ?>">
            <?php IVE_Form_Field_Label::get( $attributes ); ?>

            <?php if ( $attributes['emailConfirmation'] ) : ?>
                <div class="ive-form-field-row">
                    <div class="ive-form-field-email-primary">
            <?php endif; ?>

                <input type="email" <?php IVE_Form_Field_Attributes::get( $attributes ); ?> />

                <?php IVE_Form_Field_Description::get( $attributes ); ?>

            <?php if ( $attributes['emailConfirmation'] ) : ?>
                    </div>
                    <div class="ive-form-field-email-confirm">
                        <input type="email" <?php IVE_Form_Field_Attributes::get( $confirmation_attributes ); ?> data-parsley-confirm-email="[name='<?php echo esc_attr( $attributes['slug'] ); ?>[value]']" data-parsley-validate-if-empty />

                        <?php IVE_Form_Field_Description::get( $confirmation_attributes ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php

        return ob_get_clean();
    }
}
new IVE_Form_Field_Email_Block();
