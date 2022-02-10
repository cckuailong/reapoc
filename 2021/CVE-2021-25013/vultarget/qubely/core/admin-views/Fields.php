<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class QUBELY_Fields {

    /**
     * Get Field Type
     * @param $type
     * @param $info
     * @since 1.3.91
     */

    public static function get( $type, $info ) {
        if (
            'text' === $type ||
            'number' === $type ||
            'date' === $type ||
            'email' === $type ||
            'month' === $type ||
            'search' === $type ||
            'url' === $type ||
            'time' === $type ||
            'tel' === $type ||
            'week' === $type ||
            'color' === $type
        ) {
            return self::text( $info );
        }
        if ( 'select' === $type ) {
            return self::select( $info );
        }
        if ('checkbox' === $type ) {
            return self::checkbox( $info );
        }
    }

    /**
     * Get text field
     * @param $info
     * @since 1.3.91
     */
    private static function text( $info )
    {
        ?>
            <tr>
                <?php self::label( $info['label'] ); ?>
                <td>
                    <input
                        name="qubely_options[<?php echo esc_attr( $info['key'] ); ?>]"
                        value="<?php echo esc_attr( $info['value'] ); ?>"
                        type="<?php echo esc_attr( $info['type'] ); ?>"
                        class="<?php echo esc_attr( $info['size'] === 'regular' ? 'regular-text' : '' ); ?>"
                    >
                    <?php echo isset( $info['suffix'] ) ? esc_html( $info['suffix'] ) : '' ?>
                    <?php self::description( $info['desc'] ); ?>
                </td>
            </tr>
        <?php
    }

    /**
     * Get Select Field
     * @param $info
     * @since 1.3.91
     */
    private static function select( $info )
    {
        $info['options'] = isset( $info['options'] ) ? $info['options'] : array();

        ?>
        <tr>
            <?php self::label( $info['label'] ); ?>
            <td>
                <select name="qubely_options[<?php echo esc_attr( $info['key'] ) ?>]" id="<?php echo esc_attr( $info['key'] ) ?>">
                    <?php
                        foreach ( $info['options'] as $key => $label ) {
                            ?>
                                <option <?php selected( $info['value'], $key, true ); ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $label ); ?></option>
                            <?php
                        }
                    ?>
                </select>
                <?php self::description( $info['desc'] ); ?>
            </td>
        </tr>
        <?php
    }
    /**
     * Get Checkbox Field
     * @param $info
     * @since 1.5.2
     */
    private static function checkbox( $info )
    {
        $info['options'] = isset( $info['options'] ) ? $info['options'] : array();

        ?>
        <tr>
            <?php self::label( $info['label'] ); ?>
            <td>
                 <input
                        <?php echo esc_attr( $info['value'] === 'true' ? 'checked' : '' ); ?>
                        name="qubely_options[<?php echo esc_attr( $info['key'] ) ?>]"
                        value="<?php echo esc_html( $info['value'] ) ?>"
                        type="checkbox"
                    >
                <?php self::description( $info['desc'] ); ?>
            </td>
        </tr>
        <?php
    }

    /**
     * Label markup
     * @param $label
     * @since 1.3.91
     */
    private static function label( $label ) {
        if ( isset( $label ) ) {
            echo "<th class='row'>". esc_html( $label ) ."</th>";
        }
    }

    /**
     * Description Markup
     * @param $description
     * @since 1.3.91
     */
    private static function description( $description ) {
        if ( isset( $description ) ) {
            echo '<p class="description">' . wp_kses_post( $description ) . '</p>';
        }
    }
}