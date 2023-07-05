<?php
// Register the Guide custom post type
function gp_type_register() {
  $labels = array(
    'name' => 'Guides',
    'singular_name' => 'Guide',
    'menu_name' => 'Guides',
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'has_archive' => true,
    'publicly_queryable' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'guide' ),
    'capability_type' => 'post',
    'hierarchical' => false,
    'supports' => array( 'title', 'editor', 'thumbnail' ),
    'taxonomies' => array( 'category' ),
    'menu_icon' => 'dashicons-location',
    'show_in_rest' => true,
  );
  register_post_type( 'guide', $args );
}

// Hook the function to the 'init' action which fires before any headers are sent
add_action( 'init', 'gp_type_register' );

// Include Guides in the default WordPress loop
function gp_include_guides_in_loop( $query ) {
  if ( ! is_admin() && $query->is_main_query() && ( $query->is_home() || $query->is_archive() || $query->is_search() || $query->is_category() || $query->is_tag() ) ) :
    $query->set( 'post_type', array( 'post', 'guide' ) );
  endif;
}

// Hook the function to run before the actual query is run
add_action( 'pre_get_posts', 'gp_include_guides_in_loop' );

// Add custom meta boxes for locations
function gp_add_guide_location_meta_boxes() {
  add_meta_box(
    'guide_location_meta_box',
    'Locations',
    'gp_render_guide_location_meta_box',
    'guide',
    'normal',
    'high'
  );
}

// Hook the function to run after the built-in meta boxes have been added
add_action( 'add_meta_boxes', 'gp_add_guide_location_meta_boxes' );

// Render custom meta box for locations
function gp_render_guide_location_meta_box( $post ) {
  $locations = get_post_meta( $post->ID, 'guide_locations', true );
  wp_nonce_field( 'wp_rest', 'wp_rest' );

  // Output HTML fields for each location
?>
  <div id="location-container">
  <?php if ( ! empty( $locations ) ) : foreach ( $locations as $index => $location ) : ?>
    <div class="location-item" data-index="<?php echo $index; ?>">
      <input type="text" name="guide-locations-<?php echo $index; ?>-name" placeholder="Name" value="<?php echo $location['name']; ?>" class="location-name">
      <?php
      $editor_content = $location['description'];
      $editor_id = 'guide-locations-' . $index . '-description';
      $settings = array(
        'textarea_name' => 'guide-locations-' . $index . '-description',
        'media_buttons' => true,
        'textarea_rows' => 5,
        'tinymce' => array(
          'toolbar1' => 'formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | blockquote | link unlink | | pastetext removeformat',
          'toolbar2' => '',
          'toolbar3' => '',
          'toolbar4' => '',
          'content_style' => 'body { font-family: San Francisco, Segoe UI, DejaVu Sans, Helvetica, Arial, sans-serif; }'
        ),
      );

      wp_editor( $editor_content, $editor_id, $settings );
    ?>
      <a href="https://support.google.com/maps/answer/144361?hl=en" target="_blank"><span class="dashicons dashicons-info"></span></a>
      <input type="text" name="guide-locations-<?php echo $index; ?>-map" placeholder="Google Maps Embed Code" value="<?php echo htmlspecialchars( $location['map'] ); ?>">
      <label> Wi-Fi Available </label><input type="checkbox" name="guide-locations-<?php echo $index; ?>-wifi" <?php echo $location['wifi'] ? 'checked' : ''; ?>>
      <input type="text" name="guide-locations-<?php echo $index; ?>-price" placeholder="Price" value="<?php echo $location['price']; ?>" class="location-details">
      <input type="text" name="guide-locations-<?php echo $index; ?>-hours" placeholder="Store Hours" value="<?php echo $location['hours']; ?>" class="location-details">
      <button class="location-delete-button button" data-index="<?php echo $index; ?>">Delete Location</button>
    </div>
  <?php endforeach; else : ?>
    <div class="location-item location-item-new" data-index="0">
      <input type="text" name="guide-locations-0-name" placeholder="Name" value="" class="location-name">
    <?php
    $editor_content = '';
    $editor_id = 'guide-locations-0-description';
    $settings = array(
      'textarea_name' => 'guide-locations-0-description',
      'media_buttons' => true,
      'textarea_rows' => 5,
      'tinymce' => array(
        'toolbar1' => 'formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | blockquote | link unlink | | pastetext removeformat',
        'toolbar2' => '',
        'toolbar3' => '',
        'toolbar4' => '',
        'content_style' => 'body { font-family: San Francisco, Segoe UI, DejaVu Sans, Helvetica, Arial, sans-serif; }'
      ),
    );

    wp_editor( $editor_content, $editor_id, $settings );
    ?>
      <a href="https://support.google.com/maps/answer/144361?hl=en" target="_blank"><span class="dashicons dashicons-info"></span></a>
      <input type="text" name="guide-locations-0-map" placeholder="Google Maps Embed Code" value="">
      <label> Wi-Fi Available </label><input type="checkbox" name="guide-locations-0-wifi">
      <input type="text" name="guide-locations-0-price" placeholder="Price" value="" class="location-details">
      <input type="text" name="guide-locations-0-hours" placeholder="Store Hours" value="" class="location-details">
      <button class="location-delete-button button" data-index="0">Delete Location</button>
    </div>
  <?php endif; ?>
  </div>
  <button id="location-add-button" class="button">Add Location</button>
<?php
}

// Add the locations to the content of the post within the loop
function gp_add_locations_to_content( $content ) {
  // Check if it's a guide
  if ( is_singular( 'guide' ) ) :
    // Get the value of the custom field
    $locations = get_post_meta( get_the_ID(), 'guide_locations', true );

    // Append the locations to the content
    if ( ! empty( $locations ) ) :
      foreach( $locations as $location ) :
        $content .= '<h3 class="location-name">' . esc_html ( $location['name'] ) . '</h3>';
        $content .= $location['description'];
        $content .= '<ul class="location-details">';
        $content .= '<li class="location-wifi">Wi-Fi is <b>';
        $content .= ( $location['wifi'] === true ) ? esc_html( 'available' ) : esc_html( 'not available' );
        $content .= '</b></li>';
        if ( ! empty( $location['price'] ) ) :
          $content .= '<li class="location-price">Price is <b>';
          $content .= esc_html( $location['price'] );
          $content .= '</b></li>';
        endif;
        if ( ! empty( $location['hours'] ) ) :
          $content .= '<li class="location-hours">Place open at <b>';
          $content .= esc_html( $location['hours'] );
          $content .= '</b></li>';
        endif;
        $content .= '</ul>';
        $content .= $location['map'];
      endforeach;
    endif;
  endif;

  return $content;
}

// Hook the locations to the_content and run the function
add_filter( 'the_content', 'gp_add_locations_to_content', 20 );
