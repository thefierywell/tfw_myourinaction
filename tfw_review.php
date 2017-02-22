<?php
/**
 * @package Review_example_plugin
 * @version 1.0
 */
/*

/**
 * Plugin setup
 * Register review post type
 */
function review_setup() {
	$labels = array(
		'name' => __( 'Review', 'review_example_plugin' ),
		'singular_name' => __( 'Review', 'review_example_plugin' ),
		'add_new_item' => __( 'Add New Review', 'review_example_plugin' ),
		'edit_item' => __( 'Edit Review', 'review_example_plugin' ),
		'new_item' => __( 'New Review', 'review_example_plugin' ),
		'not_found' => __( 'No Review found', 'review_example_plugin' ),
		'all_items' => __( 'All Review', 'review_example_plugin' )
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'has_archive' => true,
		'map_meta_cap' => true,
		'menu_icon' => 'dashicons-carrot',		
		'supports' => array( 'title', 'editor', 'thumbnail', 'author' ),
		'taxonomies' => array( 'review-family' )
	);
	register_post_type( 'review', $args );
}
add_action( 'init', 'review_setup' );

/**
 * Register taxonomies
 */
function review_register_taxonomies(){

	$labels = array(
		'name' => __( 'Family', 'review_example_plugin' ),
		'label' => __( 'Family', 'review_example_plugin' ),
		'add_new_item' => __( 'Add New Review Family', 'review_example_plugin' ),
	);

	$args = array(
		'labels' => $labels,
		'label' => __( 'Family', 'review_example_plugin' ),
		'show_ui' => true,
		'show_admin_column' => true
	);
	register_taxonomy( 'review-family', array( 'review' ), $args );
}
add_action( 'init', 'review_register_taxonomies' );

/**
 * Add meta box
 *
 * @param post $post The post object
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
function review_add_meta_boxes( $post ){
	add_meta_box( 'review_meta_box', __( 'Nutrition facts', 'review_example_plugin' ), 'review_build_meta_box', 'review', 'side', 'low' );
}
add_action( 'add_meta_boxes_review', 'review_add_meta_boxes' );

/**
 * Build custom field meta box
 *
 * @param post $post The post object
 */
function review_build_meta_box( $post ){
	// make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'review_meta_box_nonce' );

	// retrieve the _review_cholesterol current value
	$current_cholesterol = get_post_meta( $post->ID, '_review_cholesterol', true );

	// retrieve the _review_carbohydrates current value
	$current_carbohydrates = get_post_meta( $post->ID, '_review_carbohydrates', true );

	$vitamins = array( 'Vitamin A', 'Thiamin (B1)', 'Riboflavin (B2)', 'Niacin (B3)', 'Pantothenic Acid (B5)', 'Vitamin B6', 'Vitamin B12', 'Vitamin C', 'Vitamin D', 'Vitamin E', 'Vitamin K' );
	
	// stores _review_vitamins array 
	$current_vitamins = ( get_post_meta( $post->ID, '_review_vitamins', true ) ) ? get_post_meta( $post->ID, '_review_vitamins', true ) : array();

	?>
	<div class='inside'>
	<table>
		<tr>
			<th>Criteria</th>
			<th>Excellent</th>
			<th>Good</th>
			<th>Ok</th>
			<th>Poor</th>
		</tr>
		<tr>
			<td>Criteria #1</td>
			<td>Criterian</td>
			</tr> 
	</table>

		<h3><?php _e( 'Cholesterol', 'review_example_plugin' ); ?></h3>
		<p>
			<input type="radio" name="cholesterol" value="0" <?php checked( $current_cholesterol, '0' ); ?> /> Yes<br />
			<input type="radio" name="cholesterol" value="1" <?php checked( $current_cholesterol, '1' ); ?> /> No
		</p>

		<h3><?php _e( 'Carbohydrates', 'review_example_plugin' ); ?></h3>
		<p>
			<input type="text" name="carbohydrates" value="<?php echo $current_carbohydrates; ?>" /> 
		</p>

		<h3><?php _e( 'Vitamins', 'review_example_plugin' ); ?></h3>
		<p>
		<?php
		foreach ( $vitamins as $vitamin ) {
			?>
			<input type="checkbox" name="vitamins[]" value="<?php echo $vitamin; ?>" <?php checked( ( in_array( $vitamin, $current_vitamins ) ) ? $vitamin : '', $vitamin ); ?> /><?php echo $vitamin; ?> <br />
			<?php
		}
		?>
		</p>
	</div>
	<?php
}

/**
 * Store custom field meta box data
 *
 * @param int $post_id The post ID.
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
 */
function review_save_meta_box_data( $post_id ){
	// verify meta box nonce
	if ( !isset( $_POST['review_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['review_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	// return if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}

  // Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	// store custom fields values
	// cholesterol string
	if ( isset( $_REQUEST['cholesterol'] ) ) {
		update_post_meta( $post_id, '_review_cholesterol', sanitize_text_field( $_POST['cholesterol'] ) );
	}

	// store custom fields values
	// carbohydrates string
	if ( isset( $_REQUEST['carbohydrates'] ) ) {
		update_post_meta( $post_id, '_review_carbohydrates', sanitize_text_field( $_POST['carbohydrates'] ) );
	}

	// store custom fields values
	// vitamins array
	if( isset( $_POST['vitamins'] ) ){
		$vitamins = (array) $_POST['vitamins'];

		// sinitize array
		$vitamins = array_map( 'sanitize_text_field', $vitamins );

		// save data
		update_post_meta( $post_id, '_review_vitamins', $vitamins );
	}else{
		// delete data
		delete_post_meta( $post_id, '_review_vitamins' );
	}
}
add_action( 'save_post_review', 'review_save_meta_box_data' );