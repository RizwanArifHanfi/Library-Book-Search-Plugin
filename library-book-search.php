<?php
/*
Plugin Name: Library Book Search
Description: Integrate live book search feature to your site.
Version: 1.0
Author: Rizwan Arif Hanfi
Author URI: https://github.com/RizwanArifHanfi
Text Domain: lbs
*/

/* Plugin constants */
define( 'LBS_VERSION', '1.0' );
define( 'LBS_DIR', plugin_dir_path( __FILE__ ) );
define( 'LBS_FILE', __FILE__ );
define( 'LBS_PATH', dirname( LBS_FILE ) );
define( 'LBS_INCLUDES', LBS_PATH . '/includes' );
define( 'LBS_TEMPLATES', LBS_PATH . '/templates' );
define( 'LBS_URL', plugins_url( '', LBS_FILE ) );
define( 'LBS_ASSETS', LBS_URL . '/assets' );

if ( ! class_exists( 'LBS' ) ) {
	/**
	 * Main LBS Class.
	 *
	 * @class LBS
	 */
	final class LBS {
		
		/**
		 * Instance of current class
		 */
		protected static $instance;

		/**
		 * @return LBS
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * LBS constructor.
		 */
		public function __construct() {

			// Includes plugin files
			$this->include_files();

			// initialize plugin classes
			$this->init_classes();

			// Register activation hook
			register_activation_hook( __FILE__, array( $this, 'activation' ) );

			// Register deactivation hook
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * To be run when the plugin is activated
		 *
		 * @return void
		 */
		public function activation() {
			do_action( 'lbs_activation' );
			flush_rewrite_rules();
		}

		/**
		 * To be run when the plugin is deactivated
		 *
		 * @return void
		 */
		public function deactivation() {
			do_action( 'lbs_deactivation' );
			flush_rewrite_rules();
		}

		/**
		 * Includes files
		 *
		 * @return void
		 */
		private function include_files() {
			spl_autoload_register( function ( $class ) {

				// If class already exists, not need to include it
				if ( class_exists( $class ) ) {
					return;
				}

				// Include out classes
				$class_path = LBS_INCLUDES . '/class.' . $class . '.php';
				if ( file_exists( $class_path ) ) {
					require_once $class_path;
				}
			} );
		}

		/**
		 * Include admin and front facing files
		 *
		 * @return void
		 */
		private function init_classes() {
			LBS_Front::init();
			LBS_Admin::init();
			LBS_Metabox::init();
			LBS_Shortcode::init();
			LBS_Ajax::init();
		}				
	}
}

/**
 * Begins execution of the plugin.
 */
LBS::init();