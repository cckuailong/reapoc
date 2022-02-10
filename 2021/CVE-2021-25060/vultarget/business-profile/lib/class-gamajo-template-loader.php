<?php
/**
 * Template Loader for Plugins.
 *
 * The Gamajo prefix has been replaced with Bpfwp to reduce plugin conflicts.
 *
 * @package   Gamajo_Template_Loader
 * @author    Gary Jones
 * @link      http://github.com/GaryJones/Gamajo-Template-Loader
 * @copyright 2013 Gary Jones
 * @license   GPL-2.0+
 * @version   1.2.0
 */

if ( ! class_exists( 'Bpfwp_Gamajo_Template_Loader' ) ) {

	/**
	 * Template loader.
	 *
	 * Originally based on functions in Easy Digital Downloads (thanks Pippin!).
	 *
	 * When using in a plugin, create a new class that extends this one and just overrides the properties.
	 *
	 * @package Bpfwp_Gamajo_Template_Loader
	 * @author  Gary Jones
	 */
	class Bpfwp_Gamajo_Template_Loader {
		/**
		 * Prefix for filter names.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $filter_prefix = 'your_plugin';

		/**
		 * Directory name where custom templates for this plugin should be found in the theme.
		 *
		 * For example: 'your-plugin-templates'.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $theme_template_directory = 'plugin-templates';

		/**
		 * Reference to the root directory path of this plugin.
		 *
		 * Can either be a defined constant, or a relative reference from where the subclass lives.
		 *
		 * e.g. YOUR_PLUGIN_TEMPLATE or plugin_dir_path( dirname( __FILE__ ) ); etc.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $plugin_directory = 'YOUR_PLUGIN_DIR';

		/**
		 * Directory name where templates are found in this plugin.
		 *
		 * Can either be a defined constant, or a relative reference from where the subclass lives.
		 *
		 * e.g. 'templates' or 'includes/templates', etc.
		 *
		 * @since 1.1.0
		 *
		 * @var string
		 */
		protected $plugin_template_directory = 'templates';

		/**
		 * Clean up template data.
		 *
		 * @since 1.2.0
		 */
		public function __destruct() {
			$this->unset_template_data();
		}

		/**
		 * Retrieve a template part.
		 *
		 * @since 1.0.0
		 *
		 * @param string $slug Template slug.
		 * @param string $name Optional. Template variation name. Default null.
		 * @param bool   $load Optional. Whether to load template. Default true.
		 *
		 * @return string
		 */
		public function get_template_part( $slug, $name = null, $load = true ) {
			// Execute code for this part.
			do_action( 'get_template_part_' . $slug, $slug, $name );

			// Get files names of templates, for given slug and name.
			$templates = $this->get_template_file_names( $slug, $name );

			// Return the part that is found.
			return $this->locate_template( $templates, $load, false );
		}

		/**
		 * Make custom data available to template.
		 *
		 * Data is available to the template as properties under the `$data` variable.
		 * i.e. A value provided here under `$data['foo']` is available as `$data->foo`.
		 *
		 * When an input key has a hyphen, you can use `$data->{foo-bar}` in the template.
		 *
		 * @since 1.2.0
		 *
		 * @param array  $data     Custom data for the template.
		 * @param string $var_name Optional. Variable under which the custom data is available in the template.
		 *                         Default is 'data'.
		 */
		public function set_template_data( array $data, $var_name = 'data' ) {
			global $wp_query;

			$wp_query->query_vars[ $var_name ] = (object) $data;
		}

		/**
		 * Remove access to custom data in template.
		 *
		 * Good to use once the final template part has been requested.
		 *
		 * @since 1.2.0
		 */
		public function unset_template_data() {
			global $wp_query;

			if ( isset( $wp_query->query_vars['data'] ) ) {
				unset( $wp_query->query_vars['data'] );
			}
		}

		/**
		 * Given a slug and optional name, create the file names of templates.
		 *
		 * @since 1.0.0
		 *
		 * @param string $slug Template slug.
		 * @param string $name Template variation name.
		 *
		 * @return array
		 */
		protected function get_template_file_names( $slug, $name ) {
			$templates = array();
			if ( isset( $name ) ) {
				$templates[] = $slug . '-' . $name . '.php';
			}
			$templates[] = $slug . '.php';

			/**
			 * Allow template choices to be filtered.
			 *
			 * The resulting array should be in the order of most specific first, to least specific last.
			 * e.g. 0 => recipe-instructions.php, 1 => recipe.php
			 *
			 * @since 1.0.0
			 *
			 * @param array  $templates Names of template files that should be looked for, for given slug and name.
			 * @param string $slug      Template slug.
			 * @param string $name      Template variation name.
			 */
			return apply_filters( $this->filter_prefix . '_get_template_part', $templates, $slug, $name );
		}

		/**
		 * Retrieve the name of the highest priority template file that exists.
		 *
		 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
		 * inherit from a parent theme can just overload one file. If the template is
		 * not found in either of those, it looks in the theme-compat folder last.
		 *
		 * @since 1.0.0
		 *
		 * @param string|array $template_names Template file(s) to search for, in order.
		 * @param bool         $load           If true the template file will be loaded if it is found.
		 * @param bool         $require_once   Whether to require_once or require. Default true.
		 *                                     Has no effect if $load is false.
		 *
		 * @return string The template filename if one is located.
		 */
		public function locate_template( $template_names, $load = false, $require_once = true ) {
			// No file found yet.
			$located = false;

			// Remove empty entries.
			$template_names = array_filter( (array) $template_names );
			$template_paths = $this->get_template_paths();

			// Try to find a template file.
			foreach ( $template_names as $template_name ) {
				// Trim off any slashes from the template name.
				$template_name = ltrim( $template_name, '/' );

				// Try locating this template file by looping through the template paths.
				foreach ( $template_paths as $template_path ) {
					if ( file_exists( $template_path . $template_name ) ) {
						$located = $template_path . $template_name;
						break 2;
					}
				}
			}

			if ( $load && $located ) {
				load_template( $located, $require_once );
			}

			return $located;
		}

		/**
		 * Return a list of paths to check for template locations.
		 *
		 * Default is to check in a child theme (if relevant) before a parent theme, so that themes which inherit from a
		 * parent theme can just overload one file. If the template is not found in either of those, it looks in the
		 * theme-compat folder last.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed|void
		 */
		protected function get_template_paths() {
			$theme_directory = trailingslashit( $this->theme_template_directory );

			$file_paths = array(
				10  => trailingslashit( get_template_directory() ) . $theme_directory,
				100 => $this->get_templates_dir(),
			);

			// Only add this conditionally, so non-child themes don't redundantly check active theme twice.
			if ( is_child_theme() ) {
				$file_paths[1] = trailingslashit( get_stylesheet_directory() ) . $theme_directory;
			}

			/**
			 * Allow ordered list of template paths to be amended.
			 *
			 * @since 1.0.0
			 *
			 * @param array $var Default is directory in child theme at index 1, parent theme at 10, and plugin at 100.
			 */
			$file_paths = apply_filters( $this->filter_prefix . '_template_paths', $file_paths );

			// Sort the file paths based on priority.
			ksort( $file_paths, SORT_NUMERIC );

			return array_map( 'trailingslashit', $file_paths );
		}

		/**
		 * Return the path to the templates directory in this plugin.
		 *
		 * May be overridden in subclass.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		protected function get_templates_dir() {
			return trailingslashit( $this->plugin_directory ) . $this->plugin_template_directory;
		}
	}
}
