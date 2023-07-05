<?php
// Register custom REST routes for saving and deleting guide locations
function gp_register_guide_posts_rest_routes() {
  // Route for saving locations
  register_rest_route(
    'guide-posts/v1',
    '/save-locations/(?P<post_id>\d+)',
    array(
      'methods' => 'POST',
      'callback' => 'gp_save_guide_locations',
      'permission_callback' => '__return_true'
    )
  );

  // Route for deleting a location
  register_rest_route(
    'guide-posts/v1',
    '/delete-location/(?P<post_id>\d+)/(?P<location_index>\d+)',
    array(
      'methods' => 'DELETE',
      'callback' => 'gp_delete_guide_location',
      'permission_callback' => '__return_true'
    )
  );
}

// Hook the function to when preparing to serve a REST API request
add_action( 'rest_api_init', 'gp_register_guide_posts_rest_routes' );

// REST handler for saving guide locations
function gp_save_guide_locations( $request ) {
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

// REST handler for deleting location
function gp_delete_guide_location( $request ) {
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
  $locations = array_values( $locations );

  // Update the meta data
  update_post_meta( $post_id, 'guide_locations', $locations );

  return array( 'message' => 'Location deleted.' );
}
