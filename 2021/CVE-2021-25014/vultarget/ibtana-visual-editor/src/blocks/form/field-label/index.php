<?php
/**
 * Form field label.
 *
 * @package ive
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Class IVE_Form_Field_Label
 */
class IVE_Form_Field_Label {
  /**
   * Prepare label for field.
   *
   * @param array $attributes block attributes.
   */
  public static function get( $attributes ) {
    $attributes = array_merge(
      array(
        'slug'        => '',
        'label'       => '',
        'required'    => false,
        'hideLabel'   => false,
        'removeLabel' => false,
        'slug'        => null,
      ),
      $attributes
    );

    $label      = $attributes['label'];
    $hide_label = $attributes['hideLabel'];
    $class      = 'ive-form-field-label';

    if ( $label && $attributes['required'] ) {
      $label .= '<span class="required">*</span>';
    }

    $label_attrs = array(
      'for' => $attributes['slug'],
    );

    if ( $hide_label ) {
      $class .= ' ive-form-field-label-hidden ive-form-hidden-label';
    }

    if ( $label ) {
      ?>
      <?php if ( ! $attributes['removeLabel'] ) : ?>
        <label class="<?php echo esc_attr( $class ); ?>" <?php IVE_Form_Field_Attributes::get( $label_attrs ); ?>>
          <?php echo wp_kses_post( $label ); ?>
        </label>
      <?php endif; ?>
      <input type="hidden" name="<?php echo esc_attr( $attributes['slug'] ); ?>[label]" value="<?php echo esc_attr( $attributes['label'] ); ?>" />
      <?php
    }
  }
}
new IVE_Form_Field_Label();
