<?php
/**
 * Shortcode front view
 *
 * @package library-book-search
 */
?>
<div class="lbs-wrapper">
	
	<!-- Search Form -->
	<form class="lbs-form" method="POST">
		<div class="header">
			<h3>Book Search</h3>			
		</div>
		<div class="col">
			<div class="col"><label for="book_name">Book Name:</label></div>
			<div class="col"><input type="text" name="book_name" id="book_name" /></div>
		</div>
		<div class="col">
			<div class="col"><label for="book_author">Author:</label></div>
			<div class="col"><input type="text" name="book_author" id="book_author" /></div>
		</div>
		<div class="clearfix"></div>

		<div class="col">
			<div class="col"><label for="book_publisher">Publisher:</label></div>
			<div class="col">
				<select name="book_publisher" id="book_publisher">
					<option value="">Choose</option>						
				<?php if( !empty($publishers) ): ?>
					<?php foreach ($publishers as $publisher): ?>
					<?php _e( sprintf('<option value="%1$s">%1$s</option>', $publisher->name), 'lbs' ); ?>
					<?php endforeach; ?>
				<?php endif; ?>
				</select>
			</div>
		</div>
		<div class="col">
			<div class="col"><label for="book_rating">Rating:</label></div>
			<div class="col">
				<select name="book_rating" id="book_rating">
					<option value="">Choose</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
			</div>
		</div>
		<div class="clearfix"></div>

		<div class="col">
			<div class="col"><label id="book_price">Price:</label></div>
			<div class="col">
				<input type="range" name="book_price" id="book_price" min="1" max="3000" value="1500" />
				<label>$0 - $<span class="count"></span></label>
			</div>
		</div>
		<div class="clearfix"></div>

		<div class="footer">
			<input type="hidden" name="action" value="search_books" />
			<button class="search-book">Search</button>			
		</div>
	</form>

	<div class="lbs-result-notice"><?php _e( sprintf('%s result(s) found!', count($books)), 'lbs' ); ?></div>

	<!-- Book Listing -->
	<table class="table">
		<thead>			
			<tr>
				<th>S. No.</th>
				<th>Book name</th>
				<th>Price</th>
				<th>Author</th>
				<th>Publisher</th>
				<th>Rating</th>
			</tr>
		</thead>
		<tbody class="lbs-results">	

		<?php if ( count( $books ) > 0 ): ?>
			<?php $i=1; foreach ($books as $book) : ?>

			<tr>
				<td><?php _e( $i, 'lbs' ); ?>.</td>
				<td><?php _e( sprintf( '<a href="%1$s" target="_blank">%2$s</a>', $book->permalink, $book->title ), 'lbs' ); ?></td>
				<td><?php _e( sprintf('$%s', $book->price), 'lbs' ); ?></td>
				<td><?php _e( $book->author, 'lbs' ); ?></td>
				<td><?php _e( $book->publisher, 'lbs' ); ?></td>
				<td><?php _e( $book->rating, 'lbs' ); ?></td>
			</tr>

			<?php $i++; endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="6" class="text-center">No results found!</td>
			</tr>
		<?php endif; ?>

		</tbody>
	</table>
</div>