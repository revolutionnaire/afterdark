<?php
class Guides_REST_API {
  public static function init() {
    // Route for saving locations
    register_rest_route(
      'guides/v1',
      '/save-locations/(?P<post_id>\d+)',
      array(
        'methods' => 'POST',
        'permission_callback' => array( 'Guides_REST_API', 'privileged_permission_callback' ),
        'callback' => array( 'Guides_REST_API', 'save_guide_locations' )
      )
    );

    // Route for deleting a location
    register_rest_route(
      'guides/v1',
      '/delete-location/(?P<post_id>\d+)/(?P<location_index>\d+)',
      array(
        'methods' => 'DELETE',
        'permission_callback' => array( 'Guides_REST_API', 'privileged_permission_callback' ),
        'callback' => array( 'Guides_REST_API', 'delete_guide_location' )
      )
    );
  }

	/**
	 * Initializes a WordPress hook
	 */

  public static function init_hook() {
    // Enqueue inline JS to create a nonce for the Guides REST API in the block editor
    add_action( 'enqueue_block_editor_assets', array( 'Guides_REST_API', 'enqueue_block_editor_assets' ) );
  }

  public static function enqueue_block_editor_assets() {
    // Check if it's a Guide custom post
    if ( get_post_type() === 'guide' ) :
      // Add inline script to pass the nonce value
      wp_add_inline_script( 'guide-posts', '
        var guidesREST = {
          restURL: "' . esc_js( rest_url( 'guides/v1' ) ) . '",
          nonce: "' . esc_js( wp_create_nonce( 'wp_rest' ) ) . '"
        }'
      );
    endif;
  }

  public static function save_guide_locations( $request ) {
    $post_id = $request->get_param( 'post_id' );

    // Validate parameter
    if ( empty( $post_id ) ) :
      return new WP_Error( 'invalid_params', 'Invalid parameters.', array( 'status' => 400 ) );
    endif;

    // Get the locations data from the request body
    $body = $request->get_body();
    $locations_data = json_decode( $body, true );

    // Check if decoding the JSON was successful
    if ( is_null( $locations_data ) ) {
      return new WP_Error( 'json_decode_error', 'Failed to decode JSON data.', array( 'status' => 400 ) );
    }

    // Sanitize and save the locations data
    update_post_meta( $post_id, 'guide_locations', $locations_data );

    if ( count( $locations_data ) > 1 ) {
      return array( 'message' => 'Locations saved.' );
    } else {
      return array( 'message' => 'Location saved.' );
    }
  }

  public static function delete_guide_location( $request ) {
    $post_id = $request->get_param( 'post_id' );
    $location_index = $request->get_param( 'location_index' );

    // Check if the parameters are valid
    if ( ! isset( $post_id ) || ! isset( $location_index ) ) :
      return new WP_Error( 'invalid_params', 'Invalid post ID or location index.' . $post_id . ' ' . $location_index, array( 'status' => 400 ) );
    endif;

    $locations = get_post_meta( $post_id, 'guide_locations', true );

    // Check if target location exists
    if ( ! array_key_exists( $location_index, $locations ) ) :
      return new WP_Error( 'invalid_index', 'Invalid location index.', array( 'status' => 400 ) );
    endif;

    // Remove the location from the array
    unset( $locations[ $location_index ] );

    // Reindex the array keys
    $locations_data = array_values( $locations );

    // Update the meta data
    update_post_meta( $post_id, 'guide_locations', $locations_data );

    return array( 'message' => 'Location deleted.' );
  }

	public static function privileged_permission_callback() {
		return current_user_can( 'edit_posts' );
	}
}
