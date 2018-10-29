<?php
/**
 * @package library-book-search
 */
if ( ! class_exists( 'LBS_Front' ) ) {

	class LBS_Front {

		/**
		 * Instance of current class
		 */
		protected static $instance;

		/**
		 * @return LBS_Front
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * LBS Front constructor.
		 */
		public function __construct() {
			// Single book template.
			add_filter( 'single_template', array( $this, 'single_template' ) );

			// Archive book template
			add_filter( 'archive_template', array( $this, 'archive_template' ) );

			// Change the post per page size for custom post type - book
			add_filter( 'pre_get_posts', array( $this, 'book_posts_per_page' ) );

			// Register and enqueue styles and scripts
			add_action( 'wp_loaded', array( $this, 'register_styles' ) );
			add_action( 'wp_loaded', array( $this, 'register_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 10 );
		}

		/**
		 * Load single book template from plugin.
		 *
		 * @param string $single_template The post template.
		 *
		 * @return string
		 */
		public function single_template( $single_template ) {
			// Check if current single page is book
			if ( is_singular( 'book' ) ) {

				// Include template file from the plugin.
				$single_template = LBS_TEMPLATES . '/single-book.php';

				// Checks if the single post is book.
				if ( file_exists( $single_template ) ) {
					return $single_template;
				}
			}
			return $single_template;
		}

		/**
		 * Load book archive template from plugin.
		 *
		 * @param string $archive_template The post template.
		 *
		 * @return string
		 */
		public function archive_template( $archive_template ) {

			// Checks if the archive is book/author/publisher.
			if ( 
				is_post_type_archive( 'book' ) || 
				is_tax( 'writer' ) || 
				is_tax( 'publisher' ) 
			) {
				
				$archive_template = LBS_TEMPLATES . '/archive-book.php';

				if ( file_exists( $archive_template ) ) {
					return $archive_template;
				}
			}

			return $archive_template;
		}

		/**
		 * Change default post per page for custom post type - book
		 *
		 * @param object $query
		 * @return string
		 */
		public function book_posts_per_page( $query ) {
		    if ( is_admin() || ! $query->is_main_query() ) {
		       return;
		    }

		    if ( is_post_type_archive( 'book' ) ) {
		       $query->set( 'posts_per_page', 12 );
		    }
		}

		/**
		 * Register plugin admin & public styles
		 *
		 * @return void
		 */
		public function register_styles() {
			$styles = array(
				'fontawesome' => array(
					'src'        => '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
					'dependency' => array(),
					'version'    => LBS_VERSION,
					'media'      => 'all',
				),
				'lbs'       => array(
					'src'        => LBS_ASSETS . '/css/style.css',
					'dependency' => array(),
					'version'    => LBS_VERSION,
					'media'      => 'all',
				)
			);

			foreach ( $styles as $handle => $style ) {
				wp_register_style( $handle, $style['src'], $style['dependency'], $style['version'], $style['media'] );
			}
		}

		/**
		 * Register plugin admin & public scripts
		 *
		 * @return void
		 */
		public function register_scripts() {
			$scripts = array(
				'lbs'       => array(
					'src'        => LBS_ASSETS . '/js/script.js',
					'dependency' => array('jquery'),
					'version'    => LBS_VERSION,
					'in_footer'  => true,
				)
			);

			foreach ( $scripts as $handle => $script ) {
				wp_register_script( $handle, $script['src'], $script['dependency'], $script['version'], $script['in_footer'] );
			}
		}

		/**
		 * Load front facing script
		 *
		 * @return void
		 */
		public function frontend_scripts() {
			// stylesheet
			wp_enqueue_style( 'fontawesome' );
			wp_enqueue_style( 'lbs' );

			// scripts
			wp_enqueue_script( 'lbs' );
			wp_localize_script('lbs', 'lbs', array(
	            'ajaxurl' => admin_url('admin-ajax.php')
	        ) );
		}
	}
}
