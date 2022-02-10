<?php
/**
 * Form field attributes.
 *
 * @package ive
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class IVE_Form_Field_Attributes
 */
class IVE_Form_Field_Attributes {
    /**
     * Prepare attributes array for registering block.
     *
     * @param array $attributes array with custom attributes.
     */
    public static function get_block_attributes( $attributes ) {
        $default_attributes = array(
            'slug' => array(
                'type' => 'string',
            ),

            'label' => array(
                'type'    => 'string',
                'default' => esc_html__( 'Field Label', 'ibtana-visual-editor' ),
            ),
            'description' => array(
                'type' => 'string',
            ),
            'descriptionHidden' => array(
                'type' => 'boolean',
            ),
            'placeholder' => array(
                'type' => 'string',
            ),
            'default' => array(
                'type' => 'string',
            ),
            'hideLabel' => array(
                'type' => 'boolean',
            ),
            'hideDescription' => array(
                'type' => 'boolean',
            ),
            'required' => array(
                'type' => 'boolean',
            ),

            'className' => array(
                'type' => 'string',
            ),
        );

        if ( ! empty( $default_attributes ) ) {
            foreach ( $default_attributes as $k => $val ) {
                if ( isset( $attributes[ $k ] ) ) {
                    $attributes[ $k ] = array_merge(
                        $val,
                        $attributes[ $k ]
                    );
                } else {
                    $attributes[ $k ] = $val;
                }
            }
        }

        return $attributes;
    }

    /**
     * Prepare attributes string for fields.
     *
     * @param array $attributes attributes list.
     */
    public static function get( $attributes ) {
        $allowed_attributes = array(
            'id',
            'placeholder',
            'default',
            'min',
            'max',
            'step',
            'multiple',
            'required',
            'for',
            'name',
        );

        if ( isset( $attributes['slug'] ) ) {
            $attributes['name'] = $attributes['slug'];
        }

        if ( isset( $attributes['slug'] ) && ! isset( $attributes['id'] ) ) {
            $attributes['id'] = $attributes['slug'];
        }

        if ( $attributes && ! empty( $attributes ) ) {
            foreach ( $attributes as $k => $val ) {
                if ( ! empty( $val ) && '' !== $val ) {
                    if ( ! in_array( $k, $allowed_attributes, true ) ) {
                        continue;
                    }

                    // boolean value.
                    if ( is_bool( $val ) ) {
                        if ( $val ) {
                            $val = $k;
                        } else {
                            continue;
                        }
                    }

                    // default attribute.
                    if ( 'default' === $k ) {
                        $k = 'value';
                    }

                    // value attribute.
                    if ( 'name' === $k ) {
                        if ( isset( $attributes['slug_sub'] ) ) {
                            $val .= '[' . $attributes['slug_sub'] . ']';
                        } else {
                            $val .= '[value]';
                        }

                        if ( isset( $attributes['fieldIsArray'] ) && $attributes['fieldIsArray'] ) {
                            $val .= '[]';
                        }
                    }

                    echo ' ' . esc_attr( $k ) . '="' . esc_attr( $val ) . '"';
                }
            }
        }
    }
}
new IVE_Form_Field_Attributes();
