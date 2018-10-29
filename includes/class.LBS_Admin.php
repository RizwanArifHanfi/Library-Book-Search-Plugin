<?php
/**
 * @package library-book-search
 */
if ( ! class_exists( 'LBS_Admin' ) ) {

	class LBS_Admin {

		/**
		 * Instance of current class
		 */
		protected static $instance;

		/**
		 * @return LBS_Admin
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * LBS Admin constructor.
		 */
		public function __construct() {
			// Hook on plugin activation/deactivation
			add_action( 'lbs_activation', array( $this, 'post_type' ) );
			add_action( 'lbs_activation', array( $this, 'taxonomy' ) );

			// Register custom post type and taxonomy
			add_action( 'init', array( $this, 'post_type') );
			add_action( 'init', array( $this, 'taxonomy' ) );

			// Custom column manupulation
			add_action( 'manage_book_posts_custom_column', array( $this, 'custom_book_column' ), 10, 2 );
			add_filter( 'manage_book_posts_columns', array( $this, 'set_custom_edit_book_columns' ) );

			// Enqueue styles and scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );
		}

		/**
		 * Create book post type
		 *
		 * @return void
		 */
		public function post_type() {
			$labels = array(
				'name'               => __( 'Books', 'lbs' ),
				'singular_name'      => __( 'Book', 'lbs' ),
				'menu_name'          => __( 'Books', 'lbs' ),
				'parent_item_colon'  => __( 'Parent Book:', 'lbs' ),
				'all_items'          => __( 'All Books', 'lbs' ),
				'view_item'          => __( 'View Book', 'lbs' ),
				'add_new_item'       => __( 'Add New Book', 'lbs' ),
				'add_new'            => __( 'Add New', 'lbs' ),
				'edit_item'          => __( 'Edit Book', 'lbs' ),
				'update_item'        => __( 'Update Book', 'lbs' ),
				'search_items'       => __( 'Search Book', 'lbs' ),
				'not_found'          => __( 'Not found', 'lbs' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'lbs' ),
			);

			$args = array(
				'label'               => __( 'Books', 'lbs' ),
				'description'         => __( '', 'lbs' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'thumbnail' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'menu_icon'           => 'dashicons-book',
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'rewrite'             => array(
					'slug'       => 'book',
					'with_front' => false,
				),
				'capability_type'     => 'post',
			);
			register_post_type( 'book', $args );
		}

		/**
		 * Create two taxonomies "writer" and "publisher"
		 * for the post type "book"
		 * Cannot create taxonomy "author", already registered in wordpress user roles
		 *
		 * @return void
		 */
		public function taxonomy() {
			register_taxonomy( 'writer', 'book', array(
				'label'             => __( 'Author', 'lbs' ),
				'singular_label'    => __( 'Author', 'lbs' ),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'args'              => array( 'orderby' => 'term_order' ),
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'writer', 'hierarchical' => true ),
			) );

			register_taxonomy( 'publisher', 'book', array(
				'label'             => __( 'Publisher', 'lbs' ),
				'singular_label'    => __( 'Publisher', 'lbs' ),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'args'              => array( 'orderby' => 'term_order' ),
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'publisher', 'hierarchical' => true ),
			) );
		}

		/**
		 * Add the data to the custom columns for the book post type
		 *
		 * @return string
		 */		
		public function custom_book_column( $column, $post_id ) {
		  switch ( $column ) {
		    case 'price' :
		    	$price = get_post_meta( $post_id, '_price', true );
		      	_e( '$' . number_format( (float) $price, 2, '.', ',' ) );
		    break;
		    case 'rating' :
		    	$rating = get_post_meta( $post_id, '_rating', true );
		      	_e( LBS_Shortcode::rating_markup( $rating ) );
		    break;
		  }
		}
		/**
		 * Add the custom columns to the events post type:
		 *
		 * @return array $columns
		 */		
		public function set_custom_edit_book_columns($columns) {
			unset($columns['taxonomy-writer']);
			unset($columns['taxonomy-publisher']);			
			unset($columns['date']);

			$columns['title'] 				= __( 'Name', 'lbs' );
			$columns['price'] 				= __( 'Price', 'lbs' );
			$columns['taxonomy-writer'] 	= __( 'Author', 'lbs' );
			$columns['taxonomy-publisher'] 	= __( 'Publisher', 'lbs' );
			$columns['rating'] 				= __( 'Rating', 'lbs' );
			$columns['date'] 				= __( 'Date', 'lbs' );

			return $columns;
		}

		/**
		 * Load admin script
		 *
		 * @return void
		 */
		public function admin_scripts() {
			// stylesheet
			wp_enqueue_style( 'fontawesome' );
	        wp_add_inline_style( 'fontawesome', ".fa.checked{color: orange;}" );
		}
	}
}