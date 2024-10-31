<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Nearby_Locations
 * @subpackage Nearby_Locations/includes
 * @author     Aaron Frey <aaron.frey@gmail.com>
 */
class AJF_Nearby_Locations {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Nearby_Locations_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'ajf-nearby-locations';
		$this->version = '1.1.1';

		$this->check_upgrade();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function check_upgrade() {
		// Retrieve initial plugin version
		$initial_version = get_option('ajf-nl-version', '1.0.0');

		// If initial plugin version is not the current version
		if ($initial_version !== $this->version) {

			switch ($initial_version) {
				case '1.0.0':
					// Add the custom color option defaults
					$background_color = get_option('ajf-nl-color-background');
					if (!$background_color) {
						add_option('ajf-nl-color-background', '#0073aa');
					}

					$panel_color = get_option('ajf-nl-color-panel');
					if (!$panel_color) {
						add_option('ajf-nl-color-panel', '#005883');
					}

					$text_color = get_option('ajf-nl-color-text');
					if (!$text_color) {
						add_option('ajf-nl-color-text', '#ffffff');
					}
				break;
			}

			// Set the new plugin version
			update_option('ajf-nl-version', $this->version);
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Nearby_Locations_Loader. Orchestrates the hooks of the plugin.
	 * - Nearby_Locations_i18n. Defines internationalization functionality.
	 * - Nearby_Locations_Admin. Defines all hooks for the admin area.
	 * - Nearby_Locations_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nearby-locations-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nearby-locations-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nearby-locations-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nearby-locations-public.php';

		$this->loader = new AJF_Nearby_Locations_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Nearby_Locations_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new AJF_Nearby_Locations_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		if (is_admin()) {
			
			$plugin_admin = new AJF_Nearby_Locations_Admin($this->get_plugin_name(), $this->get_version());

			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

			$this->loader->add_action('admin_menu', $plugin_admin, 'ajf_nearby_locations_page');
			$this->loader->add_action('wp_ajax_nopriv_nearby_locations_crud', $plugin_admin, 'ajf_nearby_locations_process_ajax');
			$this->loader->add_action('wp_ajax_nearby_locations_crud', $plugin_admin, 'ajf_nearby_locations_process_ajax');
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new AJF_Nearby_Locations_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$this->loader->add_action('init', $plugin_public, 'ajf_nearby_locations_shortcodes_init');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Nearby_Locations_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}