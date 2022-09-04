<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://belvidere.org.uk
 * @since      1.0.0
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 */

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
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 * @author     Ben Higham <web@belvidere.org.uk>
 */
class Belv_Events {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Belv_Events_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'belv-events';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Belv_Events_Loader. Orchestrates the hooks of the plugin.
	 * - Belv_Events_i18n. Defines internationalization functionality.
	 * - Belv_Events_Admin. Defines all hooks for the admin area.
	 * - Belv_Events_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-belv-events-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-belv-events-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-belv-events-admin.php';

		/**
		* The class responsible for defining the custom post type
		*/
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/belv-events-cpt.php';

		/**
		* The class responsible for custom metaboxes
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'CMB2/init.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-belv-events-public.php';

		/**
		 * The class responsible for defining shortcodes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-belv-events-shortcodes.php';

		/**
		 * The class responsible for defining REST API Endpoints.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-belv-events-rest-api.php';

		/**
		 * The class responsible for defining REST API Endpoints.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-belv-events-data.php';

		/**
		 * The class responsible for definining the Upcoming Events Widget
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-belv-events-widget-upcoming.php';

		$this->loader = new Belv_Events_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Belv_Events_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Belv_Events_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Belv_Events_Admin( $this->get_plugin_name(), $this->get_version() );
		// $custom_post_type = new Belv_Custom_Post_Type();
		$plugin_rest_api = new Belv_Events_Rest_Controller();
		$plugin_data = new Belv_Events_Data( $this->get_plugin_name(), $this->get_version() );
	

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add Settings page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Add Upcoming Events Widget
		//$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_upcoming_events_widget' );

		// Add Settings link to the plugin page
		// $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		// $this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

		// Add Insert data function
		$this->loader->add_action( 'wp_ajax_nopriv_belv_post_new_event', $plugin_data, 'belv_post_new_event' );
		$this->loader->add_action( 'wp_ajax_belv_post_new_event', $plugin_data, 'belv_post_new_event' );

		// Add Get data function
		$this->loader->add_action( 'wp_ajax_nopriv_belv_get_months_events', $plugin_data, 'belv_get_months_events' );
		$this->loader->add_action( 'wp_ajax_belv_get_months_events', $plugin_data, 'belv_get_months_events' );

		// Add Update data function
		$this->loader->add_action( 'wp_ajax_nopriv_belv_update_event', $plugin_data, 'belv_update_event' );
		$this->loader->add_action( 'wp_ajax_belv_update_event', $plugin_data, 'belv_update_event' );

		// Add Delete data function
		$this->loader->add_action( 'wp_ajax_nopriv_belv_remove_event', $plugin_data, 'belv_remove_event' );
		$this->loader->add_action( 'wp_ajax_belv_remove_event', $plugin_data, 'belv_remove_event' );

		$this->loader->add_action( 'rest_api_init', $plugin_rest_api, 'belv_events_register_routes' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Belv_Events_Public( $this->get_plugin_name(), $this->get_version() );
		$belv_shortcodes = new Belv_Events_Shortcodes( $this->get_plugin_name(), $this->get_version() );
		$belv_data = new Belv_Events_Data( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Add action for getting data
		$this->loader->add_action('wp_ajax_nopriv_belv_calendar_get_events', $belv_data, 'belv_calendar_get_events');
		$this->loader->add_action('wp_ajax_belv_calendar_get_events', $belv_data, 'belv_calendar_get_events');

		add_shortcode( 'belv_calendar', array($belv_shortcodes, 'belv_calendar_shortcode') );

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
	 * @return    Belv_Events_Loader    Orchestrates the hooks of the plugin.
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