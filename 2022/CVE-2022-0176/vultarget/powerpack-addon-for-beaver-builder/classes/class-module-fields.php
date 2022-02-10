<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'PP_Module_Fields' ) ) {
    /**
     * @class PPFields
     */
    class PP_Module_Fields {

        /**
         * Holds the class object.
         *
         * @since 1.0.0
         *
         * @var object
         */
		public static $instance;

        /**
         * Primary class constructor.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            add_action( 'wp_enqueue_scripts', array( $this, 'field_assets' ) );

            add_action( 'fl_builder_control_pp-radio', array( $this, 'radio' ), 1, 4 );
            add_action( 'fl_builder_control_pp-checkbox', array( $this, 'checkbox' ), 1, 4 );
            add_action( 'fl_builder_control_pp-toggle', array( $this, 'toggle' ), 1, 4 );
            add_action( 'fl_builder_control_pp-multitext', array( $this, 'multitext' ), 1, 4 );
            add_action( 'fl_builder_control_pp-color', array( $this, '_color' ), 1, 4 );
            add_action( 'fl_builder_control_pp-switch', array( $this, '_switch' ), 1, 4 );
            add_action( 'fl_builder_control_pp-separator', array( $this, '_separator' ), 1, 4 );
            add_action( 'fl_builder_control_pp-css-class', array( $this, '_css_class' ), 1, 4 );
            add_action( 'fl_builder_control_pp-datepicker', array( $this, '_datepicker' ), 1, 4 );
            add_action( 'fl_builder_control_pp-hidden', array( $this, '_hidden' ), 1, 4 );
			add_action( 'fl_builder_control_pp-hidden-textarea', array( $this, '_hidden_textarea' ), 1, 4 );
			add_action('fl_builder_control_pp-normal-date', array( $this, '_normal_date' ), 1, 4);
        	add_action('fl_builder_control_pp-evergreen-date', array( $this, '_evergreen_date' ), 1, 4);

            add_action( 'fl_builder_before_render_module', array( $this, 'fallback_settings' ), 10, 1 );
			add_action( 'fl_builder_custom_fields', array( $this, 'ui_fields' ), 10, 1 );
		}

		public function field_assets()
		{
			if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
			}
		}

        public function fallback_settings( $module )
        {
            $fields = FLBuilderModel::get_settings_form_fields( $module->form );

            foreach ( $fields as $name => $field ) {
                if ( $field['type'] != 'pp-multitext' ) {
                    continue;
                }
                if ( !isset( $field['responsive'] ) ) {
                    continue;
                }

                $value = $module->settings->$name;
                $defaults = $value;

                if ( is_array( $value ) ) {
                    if ( ! isset( $value['responsive_medium'] ) && ! isset( $value['responsive_small'] ) ) {
                        $value['responsive_medium'] = $defaults;
                        $value['responsive_small'] = $defaults;
                    }
                }

                $module->settings->$name = $value;
            }

            return $module;
        }

        /**
         * Radio input
         */
        public function radio( $name, $value, $field, $settings )
        {
            if ( ! isset( $field['options'] ) ) {
                return;
            }

            $options    = ( isset( $field['options'] ) && is_array( $field['options'] ) ) ? $field['options'] : array();
            $default    = ( isset( $field['default'] ) ) ? $field['default'] : '';
            $class      = ( isset( $field['class'] ) ) ? $field['class'] : '';
            $toggle     = ( isset( $field['toggle'] ) && is_array( $field['toggle'] ) ) ? $field['toggle'] : array();
            $hide       = ( isset( $field['hide'] ) && is_array( $field['hide'] ) ) ? $field['hide'] : array();
            $trigger    = ( isset( $field['trigger'] ) && is_array( $field['trigger'] ) ) ? $field['trigger'] : array();

            foreach ( $options as $opt_key => $opt_value ) {
                ?>
                <div class="pp-field-wrap">
                    <label class="pp-label pp-option-<?php echo $opt_key; ?> <?php echo $name; ?> <?php echo ( $opt_key == $value || ( '' == $value && $opt_key == $default ) ) ? 'selected' : ''; ?>">
                        <input type="radio" class="pp-field-radio <?php echo $class; ?>" name="<?php echo $name; ?>" value="<?php echo $opt_key; ?>" <?php echo ( $opt_key == $value || ( '' == $value && $opt_key == $default ) ) ? 'checked="checked"' : ''; ?> />
                        <span><?php echo $opt_value; ?></span>
                    </label>
                </div>
                <?php
            }
            ?>
            <input type="hidden" class="pp-field-radio-data" value="<?php echo ( $value && '' !== $value ) ? $value : $default; ?>" data-name="<?php echo $name; ?>" <?php echo count( $toggle ) ? "data-toggle='". json_encode( $toggle ) . "'" : ''; ?> <?php echo count( $hide ) ? "data-hide='". json_encode( $hide ) . "'" : ''; ?> <?php echo count( $trigger ) ? "data-trigger='". json_encode( $trigger ) . "'" : ''; ?> />
            <script> PPFields._initRadioFields(); </script>
            <?php
        }

        /**
         * Checkbox input
         */
        public function checkbox( $name, $value, $field, $settings )
        {
            if ( ! isset( $field['options'] ) ) {
                return;
            }

            $options    = ( isset( $field['options'] ) && is_array( $field['options'] ) ) ? $field['options'] : array();
            $default    = ( isset( $field['default'] ) ) ? $field['default'] : '';
            $class      = ( isset( $field['class'] ) ) ? $field['class'] : '';
            $toggle     = ( isset( $field['toggle'] ) && is_array( $field['toggle'] ) ) ? $field['toggle'] : array();
            $hide       = ( isset( $field['hide'] ) && is_array( $field['hide'] ) ) ? $field['hide'] : array();
            $trigger    = ( isset( $field['trigger'] ) && is_array( $field['trigger'] ) ) ? $field['trigger'] : array();

            foreach ( $options as $opt_key => $opt_value ) {
                ?>
                <div class="pp-field-wrap">
                    <label class="pp-label pp-option-<?php echo $opt_key; ?> <?php echo $name; ?> <?php echo ( ( is_array( $value ) && in_array( $opt_key, $value ) ) || ( '' == $value && $opt_key == $default ) ) ? 'selected' : ''; ?>">
                        <input type="checkbox" class="pp-field-checkbox <?php echo $class; ?>" data-name="<?php echo $name; ?>" value="<?php echo $opt_key; ?>" <?php echo ( ( is_array( $value ) && in_array( $opt_key, $value ) ) || ( '' == $value && $opt_key == $default ) ) ? 'checked="checked"' : ''; ?> />
                        <span><?php echo $opt_value; ?></span>
                    </label>
                </div>
                <?php
            }
            ?>
            <input type="hidden" class="pp-field-checkbox-data" name="<?php echo $name; ?>" value="[<?php echo ( is_array( $value ) ) ? implode( ', ', $value ) : $default; ?>]" data-name="<?php echo $name; ?>" <?php echo count( $toggle ) ? "data-toggle='". json_encode( $toggle ) . "'" : ''; ?> <?php echo count( $hide ) ? "data-hide='". json_encode( $hide ) . "'" : ''; ?> <?php echo count( $trigger ) ? "data-trigger='". json_encode( $trigger ) . "'" : ''; ?> />
            <script> PPFields._initCheckboxFields(); </script>
            <?php
        }

        /**
         * Toggle
         */
        public function toggle( $name, $value, $field, $settings )
        {
            $default    = ( isset( $field['default'] ) ) ? $field['default'] : 'disabled';
            $class      = ( isset( $field['class'] ) ) ? $field['class'] : '';
            $toggle     = ( isset( $field['toggle'] ) && is_array( $field['toggle'] ) ) ? $field['toggle'] : array();
            $hide       = ( isset( $field['hide'] ) && is_array( $field['hide'] ) ) ? $field['hide'] : array();
            $trigger    = ( isset( $field['trigger'] ) && is_array( $field['trigger'] ) ) ? $field['trigger'] : array();
            $value      = ( '' != $value ) ? ( 'enabled' != $value ? 'disabled' : $value ) : $default;

            ?>
            <div class="pp-field-wrap">
                <div class="pp-toggle <?php echo $value; ?>">
                    <span class="track"></span>
                    <span class="thumb"></span>
                    <input type="checkbox" class="pp-field-toggle" name="<?php echo $name; ?>" value="<?php echo $value; ?>" checked="checked" <?php echo count( $toggle ) ? "data-toggle='". json_encode( $toggle ) . "'" : ''; ?> <?php echo count( $hide ) ? "data-hide='". json_encode( $hide ) . "'" : ''; ?> <?php echo count( $trigger ) ? "data-trigger='". json_encode( $trigger ) . "'" : ''; ?> />
                </div>
            </div>
            <script> PPFields._initToggleFields(); </script>
            <?php
        }

        /**
         * Multi input text fields
         */
        public function multitext( $name, $value, $field, $settings )
        {

            if ( ! isset( $field['options'] ) || ! is_array( $field['options'] ) ) {
               return;
            }

            $options    = $field['options'];
            $class      = ( isset( $field['class'] ) ) ? $field['class'] : '';
            $default    = isset($field['default']) ? $field['default'] : array();
            $value      = (array) $value;
            $responsive = isset( $field['responsive'] ) ? $field['responsive'] : array();
            $count      = 0;
            ?>
            <div class="pp-multitext-wrap">
                <?php if ( count($responsive) ) { ?>
                    <div class="pp-multitext-responsive-toggle">
                        <span class="fa fa-desktop pp-multitext-default" data-field-target="medium" title="<?php esc_html_e('Default', 'bb-powerpack-lite'); ?>"></span>
                        <span class="fa fa-tablet pp-multitext-medium" data-field-target="small" title="<?php esc_html_e('Medium Devices', 'bb-powerpack-lite'); ?>"></span>
                        <span class="fa fa-mobile pp-multitext-small" data-field-target="default" title="<?php esc_html_e('Responsive Devices', 'bb-powerpack-lite'); ?>"></span>
                    </div>
                <?php } ?>
                <?php
                if ( ! isset( $responsive['medium'] ) ) {
                    $responsive['medium'] = array();
                }
                if ( ! isset( $responsive['small'] ) ) {
                    $responsive['small'] = array();
                }
                foreach ( $options as $key => $opt ) {
                    $placeholder = isset($opt['placeholder']) ? $opt['placeholder'] : '';
                    $size        = isset($opt['size']) ? 'size="'.$opt['size'].'"' : '';
                    $maxlength   = isset($opt['maxlength']) ? 'maxlength="'.$opt['maxlength'].'"' : '';
                    $icon        = isset($opt['icon']) ? 'fa ' . $opt['icon'] : '';
                    $preview     = isset($opt['preview']) ? $opt['preview'] : array();
                    $tooltip     = isset($opt['tooltip']) ? $opt['tooltip'] : '';

                    if ( ! isset( $responsive['medium'][$key] ) || empty( $responsive['medium'][$key] ) ) {
                        $responsive['medium'][$key] = $value[$key];
                    }
                    if ( ! isset( $responsive['small'][$key] ) || empty( $responsive['small'][$key] ) ) {
                        $responsive['small'][$key] = $value[$key];
                    }
                    if ( ! isset( $value['responsive_medium'][$key] ) || empty( $value['responsive_medium'][$key] ) ) {
                        $value['responsive_medium'][$key] = $responsive['medium'][$key];
                    }
                    if ( ! isset( $value['responsive_small'][$key] ) || empty( $value['responsive_small'][$key] ) ) {
                        $value['responsive_small'][$key] = $responsive['small'][$key];
                    }
                ?>
                <span class="pp-multitext <?php echo $icon; ?><?php echo '' != $tooltip ? ' pp-tip' : ''; ?> pp-field<?php echo count( $responsive ) ? ' pp-responsive-enabled' : ''; ?>" <?php echo count( $preview ) ? "data-preview='". json_encode( $preview ) . "'" : ''; ?> title="<?php echo $tooltip; ?>">
                    <input type="text" name="<?php echo $name . '[]['. $key .']'; ?>" value="<?php echo $value[$key]; ?>" class="text pp-field-multitext pp-field-multitext-default input-small-m valid" placeholder="<?php echo $placeholder; ?>" <?php echo $size; ?> <?php echo $maxlength; ?> />
                    <?php
                    if ( isset( $field['responsive'] ) && count($responsive) ) {
                        ?>
                        <input type="text" name="<?php echo $name . '[][responsive_medium]['. $key .']'; ?>" value="<?php echo $value['responsive_medium'][$key]; ?>" class="text pp-field-multitext pp-field-multitext-responsive pp-field-multitext-medium input-small-m valid" placeholder="<?php echo $placeholder; ?>" <?php echo $size; ?> <?php echo $maxlength; ?> />
                        <input type="text" name="<?php echo $name . '[][responsive_small]['. $key .']'; ?>" value="<?php echo $value['responsive_small'][$key]; ?>" class="text pp-field-multitext pp-field-multitext-responsive pp-field-multitext-small input-small-m valid" placeholder="<?php echo $placeholder; ?>" <?php echo $size; ?> <?php echo $maxlength; ?> />
                        <?php
                    }
                    ?>
                    <?php if ( 0 == $count ) { ?>
                        <span class="pp-responsive-toggle fa fa-chevron-right pp-tip" title="<?php esc_html_e( 'Responsive Options', 'bb-powerpack-lite' ); ?>"></span>
                    <?php } ?>
                </span>
                <?php
                $count++;
                }
                ?>
            </div>
            <?php
        }

        /**
         * Dual color
         */
        public function _color( $name, $value, $field, $settings )
        {
            $primary    = isset( $field['options'] ) ? ( isset( $field['options']['primary'] ) ? $field['options']['primary'] : esc_html__( 'Primary', 'bb-powerpack-lite' ) ) : esc_html__( 'Primary', 'bb-powerpack-lite' );
            $secondary  = isset( $field['options'] ) ? ( isset( $field['options']['secondary'] ) ? $field['options']['secondary'] : esc_html__( 'Secondary', 'bb-powerpack-lite' ) ) : esc_html__( 'Secondary', 'bb-powerpack-lite' );
            $value      = (array) $value;
            ?>
            <div class="pp-color-picker fl-color-picker<?php if( isset( $field['class'] ) ) echo ' ' . $field['class']; ?>">
                <button class="fl-color-picker-color<?php echo ( ! isset( $value['primary'] ) || '' == $value['primary'] ) ? ' fl-color-picker-empty' : '' ?>">
                    <svg class="fl-color-picker-icon" width="18px" height="18px" viewBox="0 0 18 18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g fill-rule="evenodd">
                            <path d="M17.7037706,2.62786498 L15.3689327,0.292540631 C14.9789598,-0.0975135435 14.3440039,-0.0975135435 13.954031,0.292540631 L10.829248,3.41797472 L8.91438095,1.49770802 L7.4994792,2.91290457 L8.9193806,4.33310182 L0,13.2493402 L0,18 L4.74967016,18 L13.6690508,9.07876094 L15.0839525,10.4989582 L16.4988542,9.08376163 L14.5789876,7.16349493 L17.7037706,4.03806084 C18.0987431,3.64800667 18.0987431,3.01791916 17.7037706,2.62786498 Z M3.92288433,16 L2,14.0771157 L10.0771157,6 L12,7.92288433 L3.92288433,16 Z"></path>
                        </g>
                    </svg>
                </button>
                <div class="pp-color-text"><?php echo $primary; ?></div>
            	<?php if(isset($field['show_reset']) && $field['show_reset']) : ?>
            		<div class="fl-color-picker-clear"><div class="fl-color-picker-icon-remove"></div></div>
            	<?php endif; ?>
            	<input name="<?php echo $name . '[][primary]'; ?>" type="hidden" value="<?php echo isset( $value['primary'] ) ? $value['primary'] : ''; ?>" class="fl-color-picker-value pp-field-color pp-color-primary" />
            </div>
            <div class="pp-color-picker fl-color-picker<?php if(isset($field['class'])) echo ' ' . $field['class']; ?>">
                <button class="fl-color-picker-color<?php echo (!isset( $value['secondary'] ) || '' == $value['secondary']) ? ' fl-color-picker-empty' : '' ?>">
                    <svg class="fl-color-picker-icon" width="18px" height="18px" viewBox="0 0 18 18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g fill-rule="evenodd">
                            <path d="M17.7037706,2.62786498 L15.3689327,0.292540631 C14.9789598,-0.0975135435 14.3440039,-0.0975135435 13.954031,0.292540631 L10.829248,3.41797472 L8.91438095,1.49770802 L7.4994792,2.91290457 L8.9193806,4.33310182 L0,13.2493402 L0,18 L4.74967016,18 L13.6690508,9.07876094 L15.0839525,10.4989582 L16.4988542,9.08376163 L14.5789876,7.16349493 L17.7037706,4.03806084 C18.0987431,3.64800667 18.0987431,3.01791916 17.7037706,2.62786498 Z M3.92288433,16 L2,14.0771157 L10.0771157,6 L12,7.92288433 L3.92288433,16 Z"></path>
                        </g>
                    </svg>
                </button>
                <div class="pp-color-text"><?php echo $secondary; ?></div>
            	<?php if(isset($field['show_reset']) && $field['show_reset']) : ?>
            		<div class="fl-color-picker-clear"><div class="fl-color-picker-icon-remove"></div></div>
            	<?php endif; ?>
            	<input name="<?php echo $name . '[][secondary]'; ?>" type="hidden" value="<?php echo isset( $value['secondary'] ) ? $value['secondary'] : ''; ?>" class="fl-color-picker-value pp-field-color pp-color-secondary" />
            </div>
            <?php
        }

        /**
         * Switch
         */
        public function _switch( $name, $value, $field, $settings )
        {
            if ( ! isset( $field['options'] ) || ! is_array( $field['options'] ) ) {
               return;
            }
            $options = $field['options'];
            $class      = ( isset( $field['class'] ) ) ? $field['class'] : '';
            $toggle     = ( isset( $field['toggle'] ) && is_array( $field['toggle'] ) ) ? $field['toggle'] : array();
            $hide       = ( isset( $field['hide'] ) && is_array( $field['hide'] ) ) ? $field['hide'] : array();
            $trigger    = ( isset( $field['trigger'] ) && is_array( $field['trigger'] ) ) ? $field['trigger'] : array();
            ?>
            <div class="pp-switch" data-options="<?php echo count($options); ?>">
                <?php foreach ( $options as $key => $label ) { ?>
                <span class="pp-switch-button<?php echo $key == $value ? ' pp-switch-active' : ''; ?>" data-value="<?php echo $key; ?>"><?php echo $label; ?></span>
                <?php } ?>
                <input type="hidden" class="pp-field-switch" name="<?php echo $name; ?>" value="<?php echo $value; ?>" <?php echo count( $toggle ) ? "data-toggle='". json_encode( $toggle ) . "'" : ''; ?> <?php echo count( $hide ) ? "data-hide='". json_encode( $hide ) . "'" : ''; ?> <?php echo count( $trigger ) ? "data-trigger='". json_encode( $trigger ) . "'" : ''; ?> />
            </div>
            <?php
        }

        /**
        * Separator
        */
        public function _separator( $name, $value, $field, $settings )
        {
            ?>
            <div class="pp-field-separator" style="height: 1px; background: <?php echo isset($field['color']) ? '#'.$field['color'] : '#ddd'; ?>;"></div>
            <?php
        }

        /**
         * CSS Class label
         */
        public function _css_class( $name, $value, $field, $settings )
        {
            ?>
            <span class="pp-field-css-class"></span>
            <script>
                jQuery('.pp-field-css-class').text('<?php echo $value; ?>' + jQuery('.pp-field-css-class').parents('form.fl-builder-settings').data('node'));
            </script>
            <?php
        }

        /**
         * Datepicker
         */
        public function _datepicker( $name, $value, $field, $settings )
        {
            $format = isset( $field['format'] ) ? $field['format'] : 'MM d, yy';
            echo '<input type="text" class="pp-field-datepicker" name="' . $name . '" value="' . $value . '" data-format="' . $format . '"/>';
        }

		function _normal_date($name, $value, $field, $settings) {

			$custom_class = isset( $field['class'] ) ? $field['class'] : '';

			$preview = isset( $field['preview'] ) ? json_encode( $field['preview'] ) : json_encode( array( 'type' => 'refresh' ) );

			echo '<div class="pp-date-wrap fl-field" data-type="select" data-preview=\'' . $preview . '\'><div class="pp-countdown-custom-fields pp-countdown-field-days"><select class="text text-full" name="' . $name . '_days" ><option value="0">' . __( 'Days', 'bb-powerpack-lite' ) . '</option>';

			for ( $i=1; $i <= 31; $i++ ) {
			    $selected = "";
			    if ( isset( $settings->fixed_date_days ) ) {
			          if ( $i == $settings->fixed_date_days ) {
			              $selected = "selected";
			          } else{
			              $selected = "";
			          }
			    } else if( $i == 29 ) {
			          $selected = "selected";
			    }

			    if( $i <= 9 ) {
			          echo '<option value="' . $i . '" ' . $selected . '>0' . $i . '</option>';
			    } else {
			          echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
			    }

			}

			$selected_month = ( isset( $settings->fixed_date_month ) && ! empty( $settings->fixed_date_month ) ) ? $settings->fixed_date_month : "01";

			echo '</select></br><label>' . __( 'Days', 'bb-powerpack-lite' ) . '</label></div>';

			// Month
			echo '<div class="pp-countdown-custom-fields pp-countdown-field-months"><select class="text text-full" name="' . $name . '_month" >';
			echo '<option value="0">' . __( 'Months', 'bb-powerpack-lite' ) . '</option>';
			echo '<option value="01" ' . ( ( $selected_month == "01" ) ? 'selected' : '' ) . ' >Jan</option>';
			echo '<option value="02" ' . ( ( $selected_month == "02" ) ? 'selected' : '' ) . ' >Feb</option>';
			echo '<option value="03" ' . ( ( $selected_month == "03" ) ? 'selected' : '' ) . ' >Mar</option>';
			echo '<option value="04" ' . ( ( $selected_month == "04" ) ? 'selected' : '' ) . ' >Apr</option>';
			echo '<option value="05" ' . ( ( $selected_month == "05" ) ? 'selected' : '' ) . ' >May</option>';
			echo '<option value="06" ' . ( ( $selected_month == "06" ) ? 'selected' : '' ) . ' >Jun</option>';
			echo '<option value="07" ' . ( ( $selected_month == "07" ) ? 'selected' : '' ) . ' >Jul</option>';
			echo '<option value="08" ' . ( ( $selected_month == "08" ) ? 'selected' : '' ) . ' >Aug</option>';
			echo '<option value="09" ' . ( ( $selected_month == "09" ) ? 'selected' : '' ) . ' >Sep</option>';
			echo '<option value="10" ' . ( ( $selected_month == "10" ) ? 'selected' : '' ) . ' >Oct</option>';
			echo '<option value="11" ' . ( ( $selected_month == "11" ) ? 'selected' : '' ) . ' >Nov</option>';
			echo '<option value="12" ' . ( ( $selected_month == "12" ) ? 'selected' : '' ) . ' >Dec</option>';
			echo '</select></br><label>' . __( 'Months', 'bb-powerpack-lite' ) . '</label></div>';

			// Year
			echo '<div class="pp-countdown-custom-fields pp-countdown-field-years"><select class="text text-full" name="'.$name.'_year" >';
			echo '<option value="0">' . __( 'Years', 'bb-powerpack-lite' ) . '</option>';
			for ( $i = date('Y'); $i < date('Y') + 6; $i++ ) {
			    $selected = "";
			    if ( isset( $settings->fixed_date_year ) ) {
			        if ( $i == $settings->fixed_date_year ) {
			            $selected = "selected";
			        } else {
			            $selected = "";
			        }
			    } else if ( $i == date('Y') + 5 ) {
			        $selected = "selected";
			    }
			    echo '<option value="'.$i.'" '. $selected .'>'.$i.'</option>';
			}
			echo '</select></br><label>' . __( 'Years', 'bb-powerpack-lite' ) . '</label></div>';

			// Hour
			echo '<div class="pp-countdown-custom-fields pp-countdown-field-hours"><select class="text text-full" name="'.$name.'_hours" >';
			echo '<option value="0">' . __( 'Hours', 'bb-powerpack-lite' ) . '</option>';
			for ( $i = 0; $i < 24; $i++ ) {
			$selected = "";
			if ( isset( $settings->fixed_date_hours ) ) {
			  if ( $i == $settings->fixed_date_hours ) {
			      $selected = "selected";
			  } else {
			      $selected = "";
			  }
			} else if( $i == 23 ) {
			  $selected = "selected";
			}

			if( $i <= 9 ){
			  echo '<option value="'.$i.'" '.$selected.'>0'.$i.'</option>';
			} else {
			  echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			}
			}
			echo '</select></br><label>' . __( 'Hours', 'bb-powerpack-lite' ) . '</label></div>';

			// Minute
			echo '<div class="pp-countdown-custom-fields pp-countdown-field-minutes"><select class="text text-full" name="'.$name.'_minutes" >';
			echo '<option value="0">' . __( 'Minutes', 'bb-powerpack-lite' ) . '</option>';
			for ( $i = 0; $i < 60; $i++ ) {
			    $selected = "";
			    if ( isset( $settings->fixed_date_minutes ) ) {
			        if ( $i == $settings->fixed_date_minutes ) {
			            $selected = "selected";
			        } else {
			            $selected = "";
			        }
			    } else if( $i == 59 ) {
			        $selected = "selected";
			    }

			    if( $i <= 9 ) {
			        echo '<option value="'.$i.'" '.$selected.'>0'.$i.'</option>';
			    } else {
			        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			    }
			}
			echo '</select></br><label>' . __( 'Minutes', 'bb-powerpack-lite' ) . '</label></div>';
		}

		function _evergreen_date($name, $value, $field, $settings) {

			$custom_class = isset( $field['class'] ) ? $field['class'] : '';
			$selected = '';
			$preview = isset( $field['preview'] ) ? json_encode( $field['preview'] ) : json_encode( array( 'type' => 'refresh' ) );


			echo '<div class="fl-field pp-evergreen-wrap" data-type="select" data-preview=\'' . $preview . '\'><div class="pp-countdown-custom-fields pp-countdown-field-days"><select class="text text-full" name="' . $name . '_days" >';
			echo '<option value="0">' . __( 'Days', 'bb-powerpack-lite' ) . '</option>';
			for ( $i=0; $i <= 31; $i++ ) {
			    if ( isset( $settings->evergreen_date_days ) ) {
			          if ( $i == $settings->evergreen_date_days ) {
			              $selected = "selected";
			          } else {
			              $selected = "";
			          }
			    } else if( $i == 30 ) {
			          $selected = "selected";
			    }
			    if( $i <= 9 ) {
			          echo '<option value="'.$i.'" '.$selected.'>0'.$i.'</option>';
			    } else {
			          echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			    }
			}
			echo '</select></br><label>' . __( 'Days', 'bb-powerpack-lite' ) . '</label></div>';



			echo '<div class="pp-countdown-custom-fields pp-countdown-field-hours"><select class="text text-full" name="' . $name . '_hours" >';
			echo '<option value="0">' . __( 'Hours', 'bb-powerpack-lite' ) . '</option>';
			for ( $i = 0; $i < 24; $i++ ) {
			    if ( isset( $settings->evergreen_date_hours ) ) {
			        if ( $i == $settings->evergreen_date_hours ) {
			            $selected = "selected";
			        } else {
			            $selected = "";
			        }
			    } else if( $i == 23 ) {
			        $selected = "selected";
			    }
			    if( $i <= 9 ) {
			        echo '<option value="'.$i.'" '.$selected.'>0'.$i.'</option>';
			    } else {
			        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
					}
			}
		echo '</select></br><label>' . __( 'Hours', 'bb-powerpack-lite' ) . '</label></div>';
		echo '<div class="pp-countdown-custom-fields pp-countdown-field-minutes"><select class="text text-full" name="' . $name . '_minutes" >';
		echo '<option value="0">' . __( 'Minutes', 'bb-powerpack-lite' ) . '</option>';
		for ( $i = 0; $i < 60; $i++ ) {
		    if ( isset( $settings->evergreen_date_minutes ) ) {
		        if ( $i == $settings->evergreen_date_minutes ) {
		            $selected = "selected";
		        } else {
		            $selected = "";
		        }
		    } else if( $i == 59 ) {
		        $selected = "selected";
		    }

		    if( $i <= 9 ) {
		        echo '<option value="'.$i.'" '.$selected.'>0'.$i.'</option>';
		    } else {
		        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		    }
		}
		echo '</select></br><label>' . __( 'Minutes', 'bb-powerpack-lite' ) . '</label></div>';
		echo '<div class="pp-countdown-custom-fields pp-countdown-field-seconds"><select class="text text-full" name="' . $name . '_seconds" >';
		echo '<option value="0">' . __( 'Seconds', 'bb-powerpack-lite' ) . '</option>';
		for ( $i = 0; $i < 60; $i++ ) {
		    if ( isset( $settings->evergreen_date_seconds ) ) {
		        if ( $i == $settings->evergreen_date_seconds ) {
		            $selected = "selected";
		        } else {
		            $selected = "";
		        }
		    } else if ( $i == 59 ) {
		        $selected = "selected";
		    }
		    if( $i <= 9 ){
		        echo '<option value="'.$i.'" '.$selected.'>0'.$i.'</option>';
		    } else {
		        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		    }
		}
		echo '</select></br><label>' . __( 'Seconds', 'bb-powerpack-lite' ) . '</label></div></div>';
	}
        /**
         * Hidden
         */
        public function _hidden( $name, $value, $field, $settings )
        {
            if ( isset( $field['value'] ) ) {
                $value = $field['value'];
            }
            echo '<input type="hidden" class="pp-field-hidden" name="' . $name . '" value="' . $value . '" />';
        }

        /**
         * Hidden textarea
         */
        public function _hidden_textarea( $name, $value, $field, $settings )
        {
            if ( isset( $field['value'] ) ) {
                $value = $field['value'];
            }
            ?>
            <textarea name="<?php echo $name; ?>"<?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; if(isset($field['placeholder'])) echo ' placeholder="'. $field['placeholder'] .'"'; if(isset($field['rows'])) echo ' rows="'. $field['rows'] .'"'; ?> style="display: none;"><?php echo $value; ?></textarea>
            <?php
        }

        /**
         * UI fields for BB 2.0 JS templates.
         */
        public function ui_fields( $fields )
        {
            $fields['pp-switch']        = BB_POWERPACK_DIR . 'includes/ui-field-pp-switch.php';
            $fields['pp-color']         = BB_POWERPACK_DIR . 'includes/ui-field-pp-color.php';
            $fields['pp-multitext']     = BB_POWERPACK_DIR . 'includes/ui-field-pp-multitext.php';
            $fields['pp-separator']     = BB_POWERPACK_DIR . 'includes/ui-field-pp-separator.php';
            $fields['pp-hidden']        = BB_POWERPACK_DIR . 'includes/ui-field-pp-hidden.php';
            $fields['pp-hidden-textarea'] = BB_POWERPACK_DIR . 'includes/ui-field-pp-hidden-textarea.php';
            $fields['pp-css-class']     = BB_POWERPACK_DIR . 'includes/ui-field-pp-css-class.php';

            return $fields;
		}
		
		public static function get_field( $type, $label = '', $args = array() )
		{
			$field 		= array();
			$default 	= ( isset( $args['default'] ) ) ? $args['default'] : '';
			$desc 		= ( isset( $args['desc'] ) ) ? $args['desc'] : '';
			$help 		= ( isset( $args['help'] ) ) ? $args['help'] : '';
			$responsive = ( isset( $args['responsive'] ) ) ? $args['responsive'] : true;
			$preview	= ( isset( $args['preview'] ) ) ? $args['preview'] : array(
				'type'	=> 'refresh'
			);

			switch ( $type ) {
				case 'color':
					$field = array(
						'type'			=> 'color',
						'show_alpha'	=> ( isset( $args['alpha'] ) && ! $args['alpha'] ) ? false : true,
						'show_reset'	=> ( isset( $args['reset'] ) && ! $args['reset'] ) ? false : true,
					);
					break;
				case 'border':
					$field = array(
						'type'			=> 'border',
						'responsive'	=> true,
					);
					break;
				case 'border-style':
					$field = array(
						'type'		=> 'pp-switch',
						'default'	=> 'none',
						'options'	=> array(
							'none'		=> __('None', 'bb-powerpack-lite'),
							'dashed'	=> __('Dashed', 'bb-powerpack-lite'),
							'dotted'	=> __('Dotted', 'bb-powerpack-lite'),
							'solid'		=> __('Solid', 'bb-powerpack-lite'),
						)
					);
					break;
				case 'unit':
					$field = array(
						'type'		=> 'unit',
						'slider'	=> true,
						'units'		=> array('px'),
						'responsive'	=> $responsive
					);
					break;
				case 'dimension':
					$field = array(
						'type'		=> 'dimension',
						'slider'	=> true,
						'units'		=> array('px'),
						'responsive'	=> $responsive
					);
					break;
				case 'align':
					$field = array(
						'type'		=> 'align',
						'default'	=> 'left'
					);
					break;
				default:
					break;
			}

			$field['label'] = $label;

			if ( ! empty( $desc ) ) {
				$field['description'] = $desc;
			}

			if ( ! empty( $default ) ) {
				$field['default'] = $default;
			}

			if ( isset( $args['units'] ) ) {
				$field['units'] = $args['units'];
			}

			if ( ! empty( $help ) ) {
				$field['help'] = $help;
			}

			if ( isset( $args['toggle'] ) ) {
				$field['toggle'] 	= $args['toggle'];
			}

			$field['preview'] = $preview;

			return $field;
		}

		public static function get_button_style_fields( $prefix = '', $fields_data = array() )
		{
			$fields = array(
				'bg_color'			=> self::get_field( 'color', __('Background Color', 'bb-powerpack-lite') ),
				'bg_hover_color'	=> self::get_field( 'color', __('Background Hover Color', 'bb-powerpack-lite'), array( 'preview'	=> array( 'type' => 'none' ) ) ),
				'text_color'		=> self::get_field( 'color', __('Text Color', 'bb-powerpack-lite') ),
				'text_hover_color'	=> self::get_field( 'color', __('Text Hover Color', 'bb-powerpack-lite'), array( 'preview'	=> array( 'type' => 'none' ) ) ),
				'border'			=> self::get_field( 'border', __('Border', 'bb-powerpack-lite') ),
				// 'border_style'		=> self::get_field( 'border-style', __('Border Style', 'bb-powerpack-lite') ),
				// 'border_width'		=> self::get_field( 'unit', __('Border Width', 'bb-powerpack-lite'), array( 'responsive' => false ) ),
				// 'border_color'		=> self::get_field( 'color', __('Border Color', 'bb-powerpack-lite') ),
				'border_hover_color' => self::get_field( 'color', __('Border Hover Color', 'bb-powerpack-lite'), array( 'preview'	=> array( 'type' => 'none' ) ) ),
				// 'border_radius'		=> self::get_field( 'unit', __('Round Corners', 'bb-powerpack-lite'), array( 'responsive' => false ) ),
				'margin_top'		=> self::get_field( 'unit', __('Margin Top', 'bb-powerpack-lite') ),
				'padding'			=> self::get_field( 'dimension', __('Padding', 'bb-powerpack-lite') ),
				'alignment'			=> self::get_field( 'align', __('Alignment', 'bb-powerpack-lite') ),
			);

			$final_fields = $fields;

			if ( ! empty( $fields_data ) ) {

				foreach ( $fields_data as $key => $field ) {
					if ( isset( $final_fields[ $key ] ) ) {
						if ( isset( $field['include'] ) && ! $field['include'] ) {
							unset( $final_fields[ $key ] );
						}
						if ( isset( $field['label'] ) ) {
							$final_fields[ $key ]['label'] = $field['label'];
						}
						if ( isset( $field['description'] ) ) {
							$final_fields[ $key ]['description'] = $field['description'];
						}
						if ( isset( $field['units'] ) ) {
							$final_fields[ $key ]['units'] = $field['units'];
						}
						if ( isset( $field['default'] ) ) {
							$final_fields[ $key ]['default'] = $field['default'];
						}
						if ( isset( $field['help'] ) ) {
							$final_fields[ $key ]['help'] = $field['help'];
						}
						if ( isset( $field['preview'] ) ) {
							$final_fields[ $key ]['preview'] = $field['preview'];
						}
						if ( isset( $field['toggle'] ) ) {
							$final_fields[ $key ]['toggle'] = $field['toggle'];
						}
					}
				}
			}

			$fields_with_prefix = array();

			foreach ( $final_fields as $key => $field ) {
				$field_name = $prefix . '_' . $key;

				$fields_with_prefix[ $field_name ] = $field;
			}

			return $fields_with_prefix;
		}

		/**
		 * Convert old multitext field value to new dimension field.
		 * 
		 * @since 2.6.8
		 * 
		 * @param object $settings	module settings
		 * @param string $field	name of the setting field
		 * @param string $type	margin | padding | responsive
		 * @param string $prefix	A prefix for new field
		 * 
		 * @return object $settings
		 */
		public static function handle_multitext_field( $settings, $field = '', $type = '', $new_field = '', $custom_keys = array() )
		{
			if ( ! is_object( $settings ) ) {
				return $settings;
			}

			if ( empty( $field ) || ! isset( $settings->{$field} ) ) {
				return $settings;
			}

			$value = $settings->{$field};

			if ( is_object( $value ) ) {
				$value = (array) $value;
			}

			if ( ! is_array( $value ) ) {
				return $settings;
			}

			if ( ! empty( $custom_keys ) ) {
				$new_val = array();
				foreach ( $custom_keys as $key => $value_key ) {
					$new_val[ $key ] = ( isset( $value[ $value_key ] ) ) ? $value[ $value_key ] : '';
				}
				$value = $new_val;
			}

			if ( empty( $new_field ) ) {
				$new_field = $field;
			}

			switch ( $type ) {
				case 'dimension':
				case 'margin':
				case 'padding':
					$settings->{$new_field . '_top'} = isset( $value['top'] ) ? $value['top'] : '';
					$settings->{$new_field . '_right'} = isset( $value['right'] ) ? $value['right'] : '';
					$settings->{$new_field . '_bottom'} = isset( $value['bottom'] ) ? $value['bottom'] : '';
					$settings->{$new_field . '_left'} = isset( $value['left'] ) ? $value['left'] : '';
					unset( $settings->{$field} );
					break;
				case 'responsive':
					$settings->{$new_field} = $value['desktop'];
					$settings->{$new_field . '_medium'} = isset( $value['tablet'] ) ? $value['tablet'] : '';
					$settings->{$new_field . '_responsive'} = isset( $value['mobile'] ) ? $value['mobile'] : '';
					break;
			}

			return $settings;
		}

		/**
		 * Convert old typography fields value into single typography field.
		 * 
		 * @since 2.6.8
		 * 
		 * @param object $settings	Module settings
		 * @param string $fields	Array of old fields
		 * @param string $new_field	Name of the new field
		 * 
		 * @return object $settings
		 */
		public static function handle_typography_field( $settings, $fields = array(), $new_field = 'typography' )
		{
			if ( ! is_object( $settings ) || empty( $fields ) || empty( $new_field ) ) {
				return $settings;
			}

			$typography 			= array();
			$typography_medium 		= array();
			$typography_responsive 	= array();

			foreach ( $fields as $name => $field ) {
				if ( ! isset( $settings->{$name} ) ) {
					continue;
				}

				$type		= $field['type'];
				$value 		= $settings->{$name};
				$condition 	= isset( $field['condition'] ) ? $field['condition'] : true;

				if ( ! $condition ) {
					continue;
				}
				
				switch ( $type ) {
					case 'font':
						$typography['font_family'] = $value['family'];
						$typography['font_weight'] = $value['weight'] === 'regular' ? 'normal' : $value['weight'];
						break;

					case 'font_size':
					case 'letter_spacing':
					case 'line_height':
						$unit = ( 'line_height' == $type ) ? '' : 'px';
						if ( ! is_array( $value ) ) {
							$typography[ $type ] = array(
								'length'	=> $value,
								'unit'		=> $unit
							);
							if ( isset( $settings->{ $name . '_medium' } ) ) {
								$typography_medium[ $type ] = array(
									'length'	=> $settings->{ $name . '_medium' },
									'unit'		=> $unit
								);

								unset( $settings->{ $name . '_medium' } );
							}
							if ( isset( $settings->{ $name . '_responsive' } ) ) {
								$typography_responsive[ $type ] = array(
									'length'	=> $settings->{ $name . '_responsive' },
									'unit'		=> $unit
								);

								unset( $settings->{ $name . '_responsive' } );
							}
						} else {
							$desktop = 'desktop';
							$tablet = 'tablet';
							$mobile = 'mobile';
							if ( isset( $field['keys'] ) ) {
								if ( isset( $field['keys']['desktop'] ) ) {
									$desktop = $field['keys']['desktop'];
								}
								if ( isset( $field['keys']['tablet'] ) ) {
									$tablet = $field['keys']['tablet'];
								}
								if ( isset( $field['keys']['mobile'] ) ) {
									$mobile = $field['keys']['mobile'];
								}
							}
							$typography[ $type ] = array(
								'length'	=> $value[ $desktop ],
								'unit'		=> $unit
							);
							$typography_medium[ $type ] = array(
								'length'	=> $value[ $tablet ],
								'unit'		=> $unit
							);
							$typography_responsive[ $type ] = array(
								'length'	=> $value[ $mobile ],
								'unit'		=> $unit
							);
						}
						break;

					case 'medium_font_size':
					case 'medium_line_height':
						$orig_type = str_replace( 'medium_', '', $type );
						$typography_medium[ $orig_type ] = array(
							'length'	=> $value,
							'unit'		=> ( 'line_height' == $orig_type ) ? '' : 'px'
						);
						break;

					case 'responsive_font_size':
					case 'responsive_line_height':
						$orig_type = str_replace( 'responsive_', '', $type );
						$typography_responsive[ $orig_type ] = array(
							'length'	=> $value,
							'unit'		=> ( 'line_height' == $orig_type ) ? '' : 'px'
						);
						break;

					case 'text_align':
					case 'text_transform':
					case 'font_style':
						$typography[ $type ] = $value;
						break;

					case 'text_shadow':
						$typography[ $type ] = array(
							'horizontal'	=> isset( $value['horizontal'] ) ? $value['horizontal'] : '',
							'vertical'		=> isset( $value['vertical'] ) ? $value['vertical'] : '',
							'blur'			=> isset( $value['blur'] ) ? $value['blur'] : '',
						);
						if ( isset( $field['color'] ) ) {
							$typography[ $type ]['color'] = $field['color'];
						}
						break;
				}

				unset( $settings->{$name} );
			}

			if ( ! empty( $typography ) ) {
				$settings->{$new_field} = $typography;
			}
			if ( ! empty( $typography_medium ) ) {
				$settings->{$new_field . '_medium'} = $typography_medium;
			}
			if ( ! empty( $typography_responsive ) ) {
				$settings->{$new_field . '_responsive'} = $typography_responsive;
			}

			return $settings;
		}

		public static function handle_border_field( $settings, $fields = array(), $new_field = '' )
		{
			if ( ! is_object( $settings ) || empty( $fields ) || empty( $new_field ) ) {
				return $settings;
			}

			$border = array();

			foreach ( $fields as $name => $field ) {
				if ( ! isset( $settings->{$name} ) ) {
					continue;
				}

				$type		= $field['type'];
				$value 		= isset( $field['value'] ) ? $field['value'] : $settings->{$name};
				$condition 	= isset( $field['condition'] ) ? $field['condition'] : true;

				if ( ! $condition ) {
					continue;
				}

				if ( is_object( $value ) ) {
					$value = (array) $value;
				}

				switch ( $type ) {
					case 'style':
					case 'color':
						$border[ $type ] = $value;
						break;

					case 'width':
						if ( ! is_array( $value ) ) {
							$border['width']['top'] = $value;
							$border['width']['right'] = $value;
							$border['width']['bottom'] = $value;
							$border['width']['left'] = $value;
						} else {
							$border['width']['top'] = isset( $value['top'] ) ? $value['top'] : '';
							$border['width']['right'] = isset( $value['right'] ) ? $value['right'] : '';
							$border['width']['bottom'] = isset( $value['bottom'] ) ? $value['bottom'] : '';
							$border['width']['left'] = isset( $value['left'] ) ? $value['left'] : '';
						}
						break;

					case 'radius':
						if ( ! is_array( $value ) ) {
							$border['radius'] = array(
								'top_left'		=> $value,
								'top_right'		=> $value,
								'bottom_left'	=> $value,
								'bottom_right'	=> $value,
							);
						} else {
							$border['radius'] = $value;
						}
						break;

					case 'shadow':
						if ( is_array( $value ) ) {
							$values = array(
								'horizontal'	=> isset( $value['horizontal'] ) ? $value['horizontal'] : '',
								'vertical'		=> isset( $value['vertical'] ) ? $value['vertical'] : '',
								'blur'			=> isset( $value['blur'] ) ? $value['blur'] : '',
								'spread'		=> isset( $value['spread'] ) ? $value['spread'] : '',
							);
							if ( isset( $field['keys'] ) ) {
								if ( is_object( $field['keys'] ) ) {
									$field['keys'] = (array) $field['keys'];
								}
								foreach ( $field['keys'] as $key => $old_key ) {
									if ( isset( $values[ $key ] ) && isset( $value[ $old_key ] ) ) {
										$values[ $key ] = $value[ $old_key ];
									}
								}
							}
							$border['shadow'] = $values;
						}
						break;

					case 'shadow_color':
						$opacity = ( isset( $field['opacity'] ) && ! empty( $field['opacity'] ) ) ? $field['opacity'] : 1;
						$border['shadow']['color'] = pp_hex2rgba( $value, $opacity );
						break;
				}

				unset( $settings->{$name} );
			}

			if ( ! empty( $border ) ) {
				$settings->{$new_field} = $border;
			}

			return $settings;
		}

		public static function handle_link_field( $settings, $fields = array(), $new_field = '' )
		{
			if ( ! is_object( $settings ) || empty( $fields ) || empty( $new_field ) ) {
				return $settings;
			}

			foreach ( $fields as $name => $field ) {
				if ( ! isset( $settings->{$name} ) ) {
					continue;
				}

				$type		= $field['type'];
				$value 		= isset( $field['value'] ) ? $field['value'] : $settings->{$name};
				$condition 	= isset( $field['condition'] ) ? $field['condition'] : true;

				if ( ! $condition ) {
					continue;
				}

				switch ( $type ) {
					case 'link':
						if ( $name != $new_field ) {
							// let's unset old field first.
							unset( $settings->{$name} );
						}
						// $value should be URL
						$settings->{$new_field} = $value;
						break;

					case 'target':
						// let's unset old field first.
						unset( $settings->{$name} );

						// $value should be _blank or _self
						$settings->{$new_field . '_target'} = $value;
						break;

					case 'nofollow':
						// let's unset old field first.
						unset( $settings->{$name} );

						// $value should be yes or no
						$settings->{$new_field . '_nofollow'} = $value;
						break;
				}
			}

			return $settings;
		}

		static public function handle_alignment_field( $settings, $field = '', $responsive_field = '' )
		{
			if ( ! is_object( $settings ) || empty( $field ) || ! isset( $settings->{$field} ) ) {
				return $settings;
			}

			if ( ! isset( $settings->{$field . '_medium'} ) || empty( $settings->{$field . '_medium'} ) ) {
				$settings->{$field . '_medium'} = $settings->{$field};
			}

			if ( ! isset( $settings->{$field . '_responsive'} ) || empty( $settings->{$field . '_responsive'} ) ) {
				$settings->{$field . '_responsive'} = $settings->{$field . '_medium'};
			}

			if ( empty( $responsive_field ) || ! isset( $settings->{$responsive_field} ) ) {
				if ( ! isset( $settings->{$field . '_responsive'} ) || empty( $settings->{$field . '_responsive'} ) ) {
					$settings->{$field . '_responsive'} = $settings->{$field . '_medium'};
				}
			} else {
				$responsive_align = $settings->{$responsive_field};

				if ( empty( $responsive_align ) || 'default' == $responsive_align ) {
					$settings->{$field . '_responsive'} = $settings->{$field . '_medium'};
				}

				if ( in_array( $responsive_align, array( 'left', 'center', 'right' ) ) ) {
					$settings->{$field . '_responsive'} = $responsive_align;
				}

				if ( $responsive_field != $field . '_responsive' ) {
					unset( $settings->{$responsive_field} );
				}
			}

			return $settings;
		}

		public static function handle_gradient_field( $settings, $fields = array(), $new_field = '' )
		{
			if ( ! is_object( $settings ) || empty( $fields ) || empty( $new_field ) ) {
				return $settings;
			}

			$gradient = array();

			if ( isset( $settings->{$new_field} ) ) {
				$gradient = $settings->{$new_field};
			}
			if ( ! is_array( $gradient ) ) {
				$gradient = array();
			}
			if ( ! isset( $gradient['colors'] ) || ! is_array( $gradient['colors'] ) ) {
				$gradient['colors'] = array();
			}
			if ( ! isset( $gradient['type'] ) ) {
				$gradient['type'] = 'linear';
			}
			if ( ! isset( $gradient['stops'] ) || ! is_array( $gradient['stops'] ) ) {
				$gradient['stops'] = array(0, 100);
			}
			if ( ! isset( $gradient['angle'] ) ) {
				$gradient['angle'] = '90';
			}

			foreach ( $fields as $name => $field ) {
				if ( ! isset( $settings->{$name} ) ) {
					continue;
				}

				$type		= $field['type'];
				$value 		= isset( $field['value'] ) ? $field['value'] : $settings->{$name};
				$condition 	= isset( $field['condition'] ) ? $field['condition'] : true;

				if ( ! $condition ) {
					continue;
				}

				switch ( $type ) {
					case 'type':
						$gradient['type'] = $value;
						break;
					case 'primary_color':
						$gradient['colors'][0] = $value;
						break;
					case 'secondary_color':
						$gradient['colors'][1] = $value;
						break;
					case 'angle':
						if ( '-90' == $value ) {
							$value = '180';
						}
						$gradient['angle'] = $value;
						break;
				}
				
				unset( $settings->{$name} );
			}

			if ( ! empty( $gradient ) ) {
				$settings->{$new_field} = $gradient;
			}

			return $settings;
		}

		public static function handle_dual_color_field( $settings, $field = '', $fields = array() )
		{
			if ( ! is_object( $settings ) || empty( $field ) || empty( $fields ) ) {
				return $settings;
			}

			if ( isset( $settings->{$field} ) ) {
				if ( is_object( $settings->{$field} ) ) {
					$settings->{$field} = (array) $settings->{$field};
				}
				$value = $settings->{$field};
				$has_opacity = false;
				$opacity = 1;
				if ( isset( $fields['opacity'] ) && isset( $settings->{$fields['opacity']} ) ) {
					if ( ! empty( $settings->{$fields['opacity']} ) ) {
						$opacity = ( $settings->{$fields['opacity']} / 100 );
						$has_opacity = true;
					}
					unset( $settings->{$fields['opacity']} );
				}
				if ( isset( $fields['primary'] ) && isset( $value['primary'] ) ) {
					$primary_field = $fields['primary'];
					$color = empty( $value['primary'] ) ? '' : $value['primary'];
					if ( ! $has_opacity && ! empty( $color ) ) {
						if ( isset( $fields['primary_opacity'] ) && $fields['primary_opacity'] !== false ) {
							$primary_opacity = $fields['primary_opacity'];
							$color = pp_hex2rgba( $color, $primary_opacity );
						}
					}
					if ( $has_opacity && ! empty( $color ) ) {
						$color = pp_hex2rgba( $color, $opacity );
					}
					$settings->{$primary_field} = $color;
				}
				if ( isset( $fields['secondary'] ) && isset( $value['secondary'] ) ) {
					$secondary_field = $fields['secondary'];
					$color = empty( $value['secondary'] ) ? '' : $value['secondary'];
					if ( ! $has_opacity && ! empty( $color ) ) {
						if ( isset( $fields['secondary_opacity'] ) && $fields['secondary_opacity'] !== false ) {
							$secondary_opacity = $fields['secondary_opacity'];
							$color = pp_hex2rgba( $color, $secondary_opacity );
						}
					}
					if ( $has_opacity && ! empty( $color ) ) {
						$color = pp_hex2rgba( $color, $opacity );
					}
					$settings->{$secondary_field} = $color;
				}

				unset( $settings->{$field} );
			}

			return $settings;
		}

        /**
         * Returns the singleton instance of the class.
         *
         * @since 1.0.0
         *
         * @return object
         */
        public static function get_instance()
        {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PP_Module_Fields ) ) {
                self::$instance = new PP_Module_Fields();
			}
			return self::$instance;
		}
	}

    $pp_fields = PP_Module_Fields::get_instance();
}
