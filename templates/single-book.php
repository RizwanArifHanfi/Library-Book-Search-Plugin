<?php
/**
 * The template for displaying all single books.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package library-book-search
 */

/* Book ID */
$book_ID = get_the_ID();

/* Get Author */
$authors = get_the_terms( $book_ID, 'writer' );
if ( $authors && ! is_wp_error( $authors ) ) {
	$authors = array_map( function ( $author ) {
		return sprintf('<a href="%1$s" target="_blank">%2$s</a>', get_category_link( $author->term_id ), $author->name);
	}, $authors );
}
$authors = $authors ? implode(', ', $authors) : '';

/* Get Publisher */
$publishers = get_the_terms( $book_ID, 'publisher' );
if ( $publishers && ! is_wp_error( $publishers ) ) {
	$publishers = array_map( function ( $publisher ) {
		return sprintf('<a href="%1$s" target="_blank">%2$s</a>', get_category_link( $publisher->term_id ), $publisher->name);
	}, $publishers );
}
$publishers = $publishers ? implode(', ', $publishers) : '';

/* Get Price */
$price 	= get_post_meta( $book_ID, '_price', true );

/* Get Rating */
$rating = get_post_meta( $book_ID, '_rating', true );
$rating = LBS_Shortcode::rating_markup( $rating );

get_header(); ?>

<div class="wrap">
	
    <div id="primary" class="content-area">
        <main id="main" class="site-main">

        	<div class="lbs-single-book">
			<?php while ( have_posts() ) : the_post(); ?>

        		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>> 

        			<div class="col">
        				<?php the_post_thumbnail(); ?>
					</div>
					<div class="col">
						<?php the_title( '<h1>', '</h1>' ); ?>
						
						<h4>
							<small>Price:</small><br/>
							$<?php _e( number_format( (float)$price, 2, '.', ','), 'lbs' ); ?>
						</h4>
						
						<p>Author: <?php _e( $authors, 'lbs' ); ?></p>						
						<p>Publisher: <?php _e( $publishers, 'lbs' ); ?></p>						
						<p>Rating: <?php _e( $rating, 'lbs' ); ?></p>
						<p><strong>Description:</strong><?php the_content(); ?></p>
					</div>

        		</div>      		

			<?php endwhile; // End of the loop.?>
        	</div>

        </main><!-- #main -->
    </div><!-- #primary -->

</div>		

<?php
get_footer();