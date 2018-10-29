<?php
/**
 * Archive template file to show book CPT
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package library-book-search
 */

get_header(); ?>

<div class="wrap">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            
            <header class="page-header">
                <?php if( is_post_type_archive( 'book' ) ) :  ?>
                    <h1 class="page-title">Books</h1>
                <?php else : ?>
                    <?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>
                <?php endif; ?>
            </header><!-- .page-header -->

			<?php if ( have_posts() ) : ?>

            <div class="lbs-books">
                <ul>
				
                <?php while ( have_posts() ) : the_post(); ?>
                    
                    <li class="item-book" id="id-<?php echo get_the_ID(); ?>">
                        <figure>
                            <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                                <?php echo get_the_post_thumbnail( get_the_ID() ); ?> 
                            </a>
                            <figcaption>
                                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                                    <strong><?php echo get_the_title(); ?></strong>
                                </a>
                            </figcaption>
                        </figure>
                    </li>

				<?php endwhile; ?>

                </ul>
            </div>
            <?php the_posts_pagination(); ?>

			<?php endif; ?>
            <div class="clearfix"></div>

        </main><!-- #main -->
    </div><!-- #primary -->
</div>

<?php
get_footer();