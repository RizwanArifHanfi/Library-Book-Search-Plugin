<?php
/**
 * @package library-book-search
 */
if ( ! class_exists( 'LBS_Ajax' ) ) {

	class LBS_Ajax {

		/**
		 * Instance of current class
		 */
		protected static $instance;

		/**
		 * @return LBS_Ajax
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * LBS_Ajax constructor.
		 */
		public function __construct() {
			add_action( 'wp_ajax_nopriv_search_books', array( $this, 'search_books' ) );
			add_action( 'wp_ajax_search_books', array( $this, 'search_books' ) );
		}

		/**
		 * WP Ajax action request handler
		 *
		 * @return string
		 */
		public function search_books() {
			global $wpdb;

			// Default status and message
			$status  	= 'error';
			$message 	= 'Something went wrong! Please try again later.';
			$html 		= '';

			// getting values of post as index
			extract( $_POST );

			// Sanitizing post data
			$book_name 		= sanitize_text_field( $book_name );
			$book_author 	= sanitize_text_field( $book_author );
			$book_publisher = sanitize_text_field( $book_publisher );
			$book_price  	= sanitize_text_field( $book_price );
			$book_rating 	= sanitize_text_field( $book_rating );

			// Query arguments
			$args = array(
			    'post_type'     => 'book',
				'post_status'   => 'publish',
				'posts_per_page'=> -1,
				'orderby'       => 'post_title',
				'order'         => 'ASC'
			);

			// If Book name is not blank
		 	if( $book_name != '' ){
		    	$args['s'] = $book_name;
		    }

			// Taxonomy Search fields
			$tax_query 	= array();
			
			// If author is not blank
		    if( $book_author != '' ){
		    	array_push($tax_query, array(
		            'taxonomy'	=> 'writer',
		            'field' 	=> 'name',
		            'terms' 	=> $book_author,
		        ) );
		    }

			// If publisher is not blank
		    if( $book_publisher != '' ){
		    	array_push($tax_query, array(
		            'taxonomy'	=> 'publisher',
		            'field' 	=> 'name',
		            'terms' 	=> $book_publisher,
		        ) );
		    }

			// If $tax_query is not empty
		    if( !empty($tax_query) ){
		    	$tax_query['relation'] = 'AND';
		    	$args['tax_query'] = $tax_query;
		    }

		    // Meta search fields			
		    $meta_query = array();

		    // If price is not blank
		    if( $book_price != '' ){
		    	array_push($meta_query, array(
		            'key' 		=> '_price',
		            'value'   => array( 0, $book_price ),
		            'type'    => 'numeric',
		            'compare' => 'BETWEEN'
		        ) );
		    }

		    // If rating is not blank
		    if( $book_rating != '' ){
		    	array_push($meta_query, array(
		            'key' 		=> '_rating',
		            'value' 	=> $book_rating,
		            'compare' 	=> 'LIKE'
		        ) );
		    }

		    // If $meta_query is not empty
		    if( !empty($meta_query) ){
		    	$meta_query['relation'] = 'AND';
		    	$args['meta_query'] = $meta_query;
		    }

		    /* Get results */
			$books 	 = get_posts( $args );

			/* If not empty */
			if( !empty($books) ) :

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
						'rating' 	=> LBS_Shortcode::rating_markup( $rating ),
					);
				}, $books );

				// filter array
				$books = array_filter( $books );

				// Convert to object
				$books = json_decode( json_encode( $books ), false );

				$i = 1;
				foreach ($books as $book) :				
					$html .= '<tr>
						<td>'. $i .'</td>
						<td>'. sprintf( '<a href="%1$s" target="_blank">%2$s</a>', $book->permalink, $book->title ) .'</td>
						<td>'. sprintf('$%s', $book->price) .'</td>
						<td>'. $book->author .'</td>
						<td>'. $book->publisher .'</td>
						<td>'. $book->rating .'</td>
					</tr>';
					$i++; 
				endforeach;
				$status = 'success';
			else :
				$html .= '<tr>
					<td colspan="6" class="text-center">No results found!</td>
				</tr>';
			endif;

			// response message
			$message = sprintf('%s result(s) found!', count($books));

			// return as JSON reponse
			echo json_encode( array(
				'status' 	=> $status, 
				'message' 	=> $message, 
				'html' 		=> $html 
			) );

			// terminate request
			wp_die(); 
		}
	}
}
