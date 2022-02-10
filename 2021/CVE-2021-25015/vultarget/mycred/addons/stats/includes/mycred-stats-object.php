<?php
if ( ! defined( 'myCRED_STATS_VERSION' ) ) exit;

/**
 * myCRED_Chart class
 * @see http://codex.mycred.me/objects/mycred_chart/
 * @since 1.8
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Chart' ) ) :
	class myCRED_Chart extends myCRED_Object {

		/**
		 * Global Arguments
		 */
		public $args         = array();

		/**
		 * Current UNIX timestamp
		 */
		public $now          = 0;

		/**
		 * All Point Type Objects
		 */
		public $point_types  = array();

		/**
		 * Point Colors
		 */
		public $point_colors = array();

		/**
		 * Date Format
		 */
		public $date_format  = '';

		/**
		 * Time Format
		 */
		public $time_format  = '';

		/**
		 * Construct
		 */
		public function __construct( $args = array() ) {

			parent::__construct();

			$this->populate();

			$this->args = $this->parse_args( $args );

		}

		/**
		 * Populate
		 * @since 1.8
		 * @version 1.0
		 */
		protected function populate() {

			$this->now          = current_time( 'timestamp' );
			$this->date_format  = get_option( 'date_format' );
			$this->time_format  = get_option( 'time_format' );

			$point_colors       = mycred_get_type_color();
			foreach ( mycred_get_types() as $type_id => $label ) {

				$type_object = new myCRED_Point_Type( $type_id );
				$type_object->color = array( 'positive' => 'green', 'negative' => 'red' );
				if ( array_key_exists( $type_id, $point_colors ) )
					$type_object->color = $point_colors[ $type_id ];

				$this->point_types[ $type_id ] = $type_object;

			}

			$this->multi_type = ( count( $this->point_types ) > 1 ) ? true : false;
			$this->references = mycred_get_all_references();

		}

		/**
		 * Apply Defaults
		 * @since 1.8
		 * @version 1.0
		 */
		public function apply_defaults( $args = array() ) {

			$defaults = array(
				'type'      => 'pie',
				'color'     => '',
				'animate'   => true,
				'bezier'    => true,
				'x_labels'  => true,
				'legend'    => true,
				'title'     => '',
				'width'     => '',
				'height'    => 250,
				'label_max' => 20
			);

			return apply_filters( 'mycred_chart_args', wp_parse_args( $args, $defaults ), $args, $this );

		}

		/**
		 * Parse Arguments
		 * @since 1.8
		 * @version 1.1
		 */
		public function parse_args( $args = array() ) {

			$args              = $this->apply_defaults( $args );
			$original          = $args;

			if ( empty( $args['color'] ) ) {

				$color = array();
				foreach ( mycred_get_color_sets() as $set )
					$color[] = $set['positive'];

				$args['color'] = $color;

			}

			$args['animate']   = (bool) $args['animate'];
			$args['bezier']    = (bool) $args['bezier'];
			$args['x_labels']  = (bool) $args['x_labels'];
			$args['legend']    = (bool) $args['legend'];

			$args['type']      = ( ! in_array( $args['type'], array( 'pie', 'doughnut', 'line', 'bar', 'radar', 'polarArea' ) ) ) ? 'pie' : $args['type'];

			$title_setup       = array(
				'position'   => 'top',
				'fontSize'   => 12,
				'fontFamily' => "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
				'fontColor'  => '#666',
				'fontStyle'  => 'bold',
				'padding'    => 10,
				'lineHeight' => '1.2',
				'text'       => ''
			);

			if ( ! empty( $args['title'] ) ) {

				if ( is_array( $args['title'] ) ) {

					$args['title'] = shortcode_atts( $title_setup, $args['title'] );

				}
				elseif ( ! empty( $args['title'] ) ) {

					$title                 = $args['title'];
					$args['title']         = $title_setup;
					$args['title']['text'] = $title;

				}

			}

			else {

				$args['title']   = $title_setup;

			}

			$args['width']     = ( $args['width'] != '' ) ? absint( $args['width'] ) : '';
			$args['height']    = ( $args['height'] != '' ) ? absint( $args['height'] ) : '';

			$args['label_max'] = absint( $args['label_max'] );

			return apply_filters( 'mycred_chart_parse_args', $args, $original, $this );

		}

		/**
		 * Get Chart Options Object
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_chart_options() {

			$options = new StdClass();

			// Disable Animation
			if ( ! $this->args['animate'] ) {

				$options->animation                   = new StdClass();
				$options->animation->duration         = 0;
				$options->hover                       = new StdClass();
				$options->hover->animationDuration    = 0;
				$options->responsiveAnimationDuration = 0;

			}

			$options->legend          = new StdClass();
			$options->legend->display = $this->args['legend'];

			$options->title           = new StdClass();
			$options->title->display  = false;

			if ( ! empty( $this->args['title'] ) ) {

				foreach ( $this->args['title'] as $key => $value )
					$options->title->$key = $value;

				if ( ! empty( $this->args['title']['text'] ) )
					$options->title->display = true;

			}

			if ( ! $this->args['bezier'] ) {

				$options->elements                = new StdClass();
				$options->elements->line          = new StdClass();
				$options->elements->line->tension = 0;

			}

			if ( ! $this->args['x_labels'] ) {

				$options->scales                 = new StdClass();
				$options->scales->xAxes          = array();

				$xAxes = new StdClass();
				$xAxes->display = false;

				$options->scales->xAxes[]        = $xAxes;

			}

			return apply_filters( 'mycred_chart_get_options', $options, $this );

		}

		/**
		 * Generate Element ID
		 * Each chart element needs a unique HTML id.
		 * @since 1.8
		 * @version 1.0
		 */
		public function generate_element_id() {

			global $mycred_charts;

			return apply_filters( 'mycred_chart_generate_element_id', MYCRED_SLUG . '-chart' . count( $mycred_charts ), $this );

		}

		/**
		 * Get Canvas Styling
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_canvas_styling() {

			$css = array();

			if ( $this->args['width'] != '' )
				$css[] = 'width: ' . $this->args['width'] . ';';

			if ( $this->args['height'] != '' )
				$css[] = 'width: ' . $this->args['height'] . ';';

			if ( ! empty( $css ) )
				$css = 'style="' . implode( ' ', $css ) . '"';
			else
				$css = '';

			return apply_filters( 'mycred_chart_get_canvas_styling', $css, $this );

		}

		/**
		 * Get Reference Label
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_reference_label( $reference = '' ) {

			if ( array_key_exists( $reference, $this->references ) ) {

				$reference = $this->references[ $reference ];

			}

			else {

				$reference = str_replace( '_', ' ', $reference );
				$reference = ucwords( $reference );

			}

			if ( $this->args['x_labels'] && $this->args['type'] != 'radar' ) {

				$label = substr( $reference, 0, $this->args['label_max'] );

				if ( strlen( $reference ) > $this->args['label_max'] )
					$label .= ' ...';

			}

			else $label = $reference;

			return apply_filters( 'mycred_chart_get_reference_label', $label, $reference, $this );

		}

		/**
		 * Generate Chart.js Canvas
		 * @since 1.8
		 * @version 1.0
		 */
		public function generate_canvas( $type = '', $input = array() ) {

			global $mycred_charts;

			$output = '';
			if ( ! empty( $input ) ) {

				$chart                    = new StdClass();
				$chart->type              = $type;
				$chart->data              = new StdClass();
				$chart->data->datasets    = array();
				$chart->data->labels      = array();
				$chart->options           = $this->get_chart_options();

				foreach ( $input as $set_id => $input_set ) {

					if ( empty( $input_set ) ) continue;

					$dataset                  = new StdClass();
					$dataset->fill            = true;
					$dataset->data            = array();
					$dataset->labels          = array();
					$dataset->backgroundColor = array();
					$dataset->borderColor     = array();

					foreach ( $input_set as $row_id => $row ) {

						$value = $row->value;
						$label = $row->label;
						$color = ( array_key_exists( $row_id, $this->args['color'] ) ) ? $this->args['color'][ $row_id ] : '#222';

						if ( $row->type == 'point' ) {

							$label = $this->point_types[ $row->label ]->plural;

							if ( is_array( $row->value ) ) {

								$value = array();
								foreach ( $row->value as $i => $v )
									$value[] = $this->point_types[ $row->label ]->number( $v );

							}
							else {

								$value = $this->point_types[ $row->label ]->number( $row->value );
								$color = ( $row->value < 0 ) ? $this->point_types[ $row->label ]->color['negative'] : $this->point_types[ $row->label ]->color['positive'];

							}

						}

						elseif ( $row->type == 'user' ) {
							$user  = get_userdata( (int) $row->label );
							$label = $user->display_name;
						}

						elseif ( $row->type == 'amount' ) {
							$label = ( $this->multi_type ) ? __( 'Amount', 'mycred' ) : $this->point_types[ MYCRED_DEFAULT_TYPE_KEY ]->color['positive'];
						}

						elseif ( $row->type == 'reference' ) {
							$label = $this->get_reference_label( $row->label );
						}

						if ( isset( $row->color ) ) {
							$color = $row->color;
						}

						$dataset->data[]            = $value;
						$dataset->labels[]          = $label;
						$dataset->backgroundColor[] = ( ( $type != 'radar' ) ? $color : ( ( MYCRED_STATS_COLOR_TYPE == 'hex' ) ? mycred_hex_to_rgb( $color, true, '0.6' ) : $color ) );

						if ( in_array( $type, array( 'line', 'bar', 'radar' ) ) ) {

							$dataset->borderColor   = ( is_array( $dataset->borderColor ) && empty( $dataset->borderColor ) ) ? $color : $dataset->borderColor;
							$dataset->fill          = ( $type == 'line' ) ? false : true;

							if ( $this->args['legend'] )
								$chart->options->legend->display = false;

						}

						$chart->data->labels[]      = $label;

					}

					$chart->data->datasets[] = $dataset;

				}

				$element_id    = $this->generate_element_id();
				$element_style = $this->get_canvas_styling();
				$output        = '<div class="chart-container ' . MYCRED_SLUG . '-chart-container"><canvas class="' . MYCRED_SLUG . '-chart-canvas" ' . $element_style . ' id="' . $element_id . '"></canvas></div>';

				$mycred_charts[ $element_id ] = $chart;

			}

			$output = apply_filters( 'mycred_chart_generate_canvas_' . $type, $output, $input, $this );

			return apply_filters( 'mycred_chart_generate_canvas', $output, $input, $this );

		}

		/**
		 * Generate Pie Chart
		 * @since 1.8
		 * @version 1.0
		 */
		public function pie_chart( $input = array() ) {

			return $this->generate_canvas( 'pie', $input );

		}

		/**
		 * Generate Doughnut Chart
		 * @since 1.8
		 * @version 1.0
		 */
		public function doughnut_chart( $input = array() ) {

			return $this->generate_canvas( 'doughnut', $input );

		}

		/**
		 * Generate Line Chart
		 * @since 1.8
		 * @version 1.0
		 */
		public function line_chart( $input = array() ) {

			return $this->generate_canvas( 'line', $input );

		}

		/**
		 * Generate Bar Chart
		 * @since 1.8
		 * @version 1.0
		 */
		public function bar_chart( $input = array() ) {

			return $this->generate_canvas( 'bar', $input );

		}

		/**
		 * Generate Polar Chart
		 * @since 1.8
		 * @version 1.0
		 */
		public function polar_chart( $input = array() ) {

			return $this->generate_canvas( 'polarArea', $input );

		}

		/**
		 * Generate Radar Chart
		 * @since 1.8
		 * @version 1.0
		 */
		public function radar_chart( $input = array() ) {

			return $this->generate_canvas( 'radar', $input );

		}

		/**
		 * Is Chart
		 * @since 1.8
		 * @version 1.0
		 */
		public function is_chart( $args = array() ) {

			$is_chart = false;
			$args = $this->parse_args( $args );

			if ( $this->args === $args )
				$is_chart = true;

			return $is_chart;

		}

	}
endif;
