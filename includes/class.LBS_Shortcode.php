<?php
/**
 * @package library-book-search
 */
if ( ! class_exists( 'LBS_Shortcode' ) ) {

	class LBS_Shortcode {

		/**
		 * Instance of current class
		 */
		protected static $instance;

		/**  
		 * @return LBS_Shortcode
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * LBS_Shortcode constructor.
		 */
		public function __construct() {
			/* Register shortcode */			
			add_action( 'init', array( $this, 'register_shortcodes') );			

			/* Add admin submenu of this plugin */
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		/**
		 * Create shortcode
		 *
		 * @return void
		 */
		public function register_shortcodes(){
			add_shortcode( 'library_book_search', array( $this, 'shortcode' ) );
		}

		/**
		 * Create submenu of Books in admin menu
		 *
		 * @return void
		 */
		public function admin_menu() {
			add_submenu_page(
				'edit.php?post_type=book',
				'LBS Shortcode',
				'Shortcode',
				'manage_options',
				'shortcode',
				array( $this, 'page_content' )
			);
		}

		/**
		 * Load page content
		 *
		 * @return string
		 */
		public function page_content() {
			ob_start(); ?>
            <div class="wrap">
                <h1><?php _e( 'LBS Shortcode', 'lbs' ) ?></h1>
                <br />	
                <div class="postbox">
					<div class="inside">
						<h3><?php _e( 'Shortcode:', 'lbs' ) ?></h3>
                		<p><input type="text" value="[library_book_search]" readonly="readonly" autofocus="autofocus"></p>
                		<p class="description">You can user this shortcode in posts, pages or sidebar widgets. Just copy <code>[library_book_search]</code> and insert into content and save.<br />Also can use <code>echo do_shortcode("[library_book_search]");</code> wherever you want in your custom theme contents.</p>
					</div>
				</div>
            </div>
			<?php
			echo ob_get_clean();
		}

		/**
		 * LBS search form shortcode.
		 * 
		 * @return string
		 */
		public function shortcode() {
			$books 	= $this->get_books();

			$args 	= array(
				'hide_empty' => 0, 
				'parent' => 0
			);
			$authors     = get_terms( 'writer', $args );
			$publishers	 = get_terms( 'publisher', $args );

			ob_start();
			require LBS_TEMPLATES . '/shortcode.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'library_book_search', $html, $books, $authors, $publishers );
		}

		/**
		 * Get all books
		 *
		 * @return object
		 */
		public function get_books() {
			$args = array(
				'post_type'      => 'book',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'post_title',
				'order'          => 'ASC',
			);
			$books = get_posts( $args );

			/* Mapping output array as per plugin needs */
			$books = array_map( function ( $book ) {

				/* Get Author */
				$authors = get_the_terms( $book->ID, 'writer' );
				if ( $authors && ! is_wp_error( $authors ) ) {
					$authors = array_map( function ( $author ) {
						return $author->name;
					}, $authors );
				}
				$authors = $authors ? implode(', ', $authors) : '';

				/* Get Publisher */
				$publishers = get_the_terms( $book->ID, 'publisher' );
				if ( $publishers && ! is_wp_error( $publishers ) ) {
					$publishers = array_map( function ( $publisher ) {
						return $publisher->name;
					}, $publishers );
				}
				$publishers = $publishers ? implode(', ', $publishers) : '';

				/* Get Price */
				$price 	= get_post_meta( $book->ID, '_price', true );

				/* Get Rating */
				$rating = get_post_meta( $book->ID, '_rating', true );

				return array(
					'id'        => $book->ID,
					'title'     => esc_attr( $book->post_title ),
					'permalink' => esc_url( get_permalink( $book->ID ) ),
					'price'  	=> number_format( (float) $price, 2, '.', ','),
					'author'  	=> $authors,
					'publisher' => $publishers,
					'rating' 	=> $this->rating_markup( $rating ),
				);
			}, $books );

			// filter array
			$books = array_filter( $books );

			// Return as object
			return json_decode( json_encode( $books ), false );
		}

		/**
		 * Add Rating Star to field
		 *
		 * @param (int) $rating
		 * @return string
		 */
		public static function rating_markup( $rating ){
			$markup = '';

			// Add stars
			for($x=1; $x<=$rating; $x++) {
		        $markup .= '<span class="fa fa-star checked"></span>';
		    }

			// Add Remailning blank stars
		    while ( $x<=5 ) {
		        $markup .= '<span class="fa fa-star"></span>';
		        $x++;
		    }

			return $markup;
		}
	}
}
