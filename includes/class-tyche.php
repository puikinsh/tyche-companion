<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Tyche_Companion
 * @subpackage Tyche_Companion/includes
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
 * @package    Tyche_Companion
 * @subpackage Tyche_Companion/includes
 * @author     Your Name <email@example.com>
 */
class Tyche_Companion {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tyche_Companion_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
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

		$this->plugin_name = 'tyche-companion';
		$this->version     = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_hooks();

	}

	/**
	 * Create and serve an instance of the plugin
	 *
	 * @return Tyche_Companion
	 */
	public static function getInstance() {
		static $inst;
		if ( ! $inst ) {
			$inst = new Tyche_Companion;
		}

		return $inst;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ensign_Loader. Orchestrates the hooks of the plugin.
	 * - Ensign_i18n. Defines internationalization functionality.
	 * - Ensign_Admin. Defines all hooks for the admin area.
	 * - Ensign_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tyche-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tyche-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/importer/macho-importer.php';
		/**
		 * Helper
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tyche-helper.php';
		$this->loader = new Tyche_Companion_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ensign_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tyche_Companion_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_hooks() {
		$this->loader->add_action( 'widgets_init', $this, 'register_widgets' );
		/**
		 * Load scripts for image uploader
		 */
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'upload_scripts' );
	}

	/**
	 * Upload the Javascripts for the media uploader
	 */
	public function upload_scripts() {
		global $pagenow, $wp_customize;

		if ( 'widgets.php' === $pagenow || isset( $wp_customize ) ) {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );

			wp_enqueue_script( 'tyche-image-upload', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/upload-media.js', array( 'jquery' ) );
			wp_enqueue_style( 'tyche-image-upload', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/upload-media.css' );
		}
	}

	public function register_widgets() {

		$widget_path = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets';
		$dirs        = glob( $widget_path . '/*', GLOB_ONLYDIR );

		foreach ( $dirs as $dir ) {
			$dirname = basename( $dir );

			include_once( $dir . '/class-widget-tyche-' . $dirname . '.php' );

			$widget_class = 'Widget_Tyche_Companion_' . Tyche_Companion_Helper::dirname_to_classname( $dirname );
			if ( class_exists( $widget_class ) ) {
				register_widget( $widget_class );
			}

		}
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
	 * @return    Ensign_Loader    Orchestrates the hooks of the plugin.
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
