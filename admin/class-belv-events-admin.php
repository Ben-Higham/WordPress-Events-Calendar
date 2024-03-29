<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://belvidere.org.uk
 * @since      1.0.0
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/admin
 * @author     Ben Higham <web@belvidere.org.uk>
 */
class Belv_Events_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Belv_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Belv_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/belv-events-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery_style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
		wp_enqueue_style( 'toastr_style', plugin_dir_url( __FILE__ ) . 'css/toastr.css' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Belv_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Belv_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/belv-events-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, false );
		wp_enqueue_script( 'toastr', plugin_dir_url( __FILE__ ) . 'js/toastr.js' );

		$calendar_nonce = wp_create_nonce('calendar_nonce');
		wp_localize_script($this->plugin_name, 'belvajaxobject', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => $calendar_nonce,
			)
		);
	}

	public function add_plugin_admin_menu() {

		/*
		* Add a settings page for this plugin to the Settings menu.
		*/
		add_menu_page( 'Belvidere Events Calendar', 'Calendar', 'manage_options',
						$this->plugin_name, array($this, 'display_plugin_setup_page'), 'dashicons-calendar-alt', 21
		);

	}

	public function add_action_links( $links ) {

		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		*/
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
		);
		return array_merge(  $settings_link, $links );

	}

	/**
	 * Display the admin settings page HTML UI
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {

    	include_once( 'partials/belv-events-admin-display.php' );

	}

}