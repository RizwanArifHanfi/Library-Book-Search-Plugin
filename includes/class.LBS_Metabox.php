<?php
/**
 * @package library-book-search
 */
if ( ! class_exists( 'LBS_Metabox' ) ) {

	class LBS_Metabox {

		/**
		 * Instance of current class
		 */
		protected static $instance;

		/**
		 * @return LBS_Metabox
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Hook into the actions when the class is constructed.
		 */
		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		}

		/**
		 * Save custom meta box
		 *
		 * @param int $post_id The post ID
		 * @return void
		 */
		public function save_meta_boxes( $post_id ) {
			if ( ! isset( $_POST['_lbs_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['_lbs_nonce'], 'lbs_nonce' ) ) {
				return;
			}

			// Check if user has permissions to save data.
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

			// Check if not an autosave.
			if ( wp_is_post_autosave( $post_id ) ) {
				return;
			}

			// Check if not a revision.
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}

			if ( ! isset( $_POST['lbs_meta'] ) ) {
				return;
			}

			foreach ( $_POST['lbs_meta'] as $key => $val ) {
				update_post_meta( $post_id, $key, stripslashes( htmlspecialchars( $val ) ) );
			}
		}

		/**
		 * Adds the meta box container.
		 *
		 * @return void
		 */
		public function add_meta_box() {			
			add_meta_box(
				'lbs-metabox',
				__( 'Pricing & rating', 'lbs' ),
				array( $this, 'meta_box_callback' ),
				'book',
				'advanced',
				'high'
			);
		}

		/**
		 * Create content for the custom meta box
		 *
		 * @param object $post
		 * @return string
		 */
		public function meta_box_callback( $post )
		{			
			wp_nonce_field( 'lbs_nonce', '_lbs_nonce' );

			$table = "<table class='form-table'>";

			// Price
			$_price = get_post_meta( $post->ID, '_price', true );
			$table .= sprintf( 
				'<tr>
					<th><label for="%1$s">%2$s</label></th>
					<td><input type="text" class="regular-text" value="%3$s" id="%4$s" name="%5$s"></td>
				</tr>', 
				'_price', 'Price', ($_price ? $_price : ''), '_price', 'lbs_meta[_price]'
			);

			// Rating
			$_rating = get_post_meta( $post->ID, '_rating', true );
			$_rating = $_rating ? $_rating : '';
			$table .= sprintf( 
				'<tr>
					<th><label for="%1$s">%2$s</label></th>
					<td>
						<select class="regular-text" id="%3$s" name="%4$s">
							<option value="1" '. $this->is_selected($_rating, 1) .'>1</option>
							<option value="2" '. $this->is_selected($_rating, 2) .'>2</option>
							<option value="3" '. $this->is_selected($_rating, 3) .'>3</option>
							<option value="4" '. $this->is_selected($_rating, 4) .'>4</option>
							<option value="5" '. $this->is_selected($_rating, 5) .'>5</option>
						</select>
					</td>
				</tr>',
				'_rating', 'Rating', '_rating', 'lbs_meta[_rating]'
			);

			$table .= "</table>";
			echo $table;
		}

		/**
		 * Check if current value selected
		 *
		 * @param int $value
		 * @param int $selected
		 * @return string
		 */
		public function is_selected($value, $selected){
			return ( $value == $selected ) ? 'selected="selected"' : '';
		}
	}
}
