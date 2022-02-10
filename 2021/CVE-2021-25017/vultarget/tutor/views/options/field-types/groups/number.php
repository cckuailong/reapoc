
<input type="number" name="<?php esc_attr_e( $input_name ); ?>" value="<?php esc_attr_e( $input_value ); ?>" >
<?php
if ( $label ) {
    echo '<p>' . esc_html_e( $label ) . '</p>';
} 
?>