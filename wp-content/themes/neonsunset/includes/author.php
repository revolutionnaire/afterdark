<?php
/**
 * ------------------------------------------------------------------------
 * Theme's For The Author Page
 * ------------------------------------------------------------------------
 * This file is for related functions for the author page
 *
 * @package NeonSunset
 */
/**
 * Function to display the author's role on the author page.
 *
 * @param int|WP_User $author_id Optional. The author ID or WP_User object. If not provided, uses the current author.
 * @return string|void The author's role, or nothing if no author or role is found.  Returns a string for display, or you can modify it to return the role for use in logic.
 */
function ns_get_the_author_role() {
	if ( is_null( $author_id ) ) {
    	// Get the current author ID if no ID is provided.
    	$author_id = get_the_author_meta( 'ID' );

    	if ( !$author_id ) {
        	return; // No author found.
    	}
	} elseif ( is_object( $author_id ) && $author_id instanceof WP_User ) {
    	//If a WP_User object is passed.
    	$user = $author_id;
    	$author_id = $user->ID;
	} else if ( !is_numeric($author_id) ) {
    	return; //Invalid author ID
	}

	$user = get_user_by( 'id', $author_id );

	if ( !$user ) {
    	return; // No user found.
	}

	$roles = ( array ) $user->roles;

	if ( empty( $roles ) ) {
    	return; // No roles found for the user.
	}

	// Get the most prominent role (the first one in the array). You might want to modify this to handle multiple roles differently.
	$role = $roles[0];


	// Get the display name of the role (more user-friendly).
	global $wp_roles;
	$role_name = isset( $wp_roles->roles[$role]['name'] ) ? $wp_roles->roles[$role]['name'] : ucfirst($role); //Fallback if name not found.

	return $role_name;
}

/**
 * Template tag function to display the author's role.  Good for putting directly in your templates.
 *
 * @param int|WP_User $author_id Optional. The author ID. If not provided, uses the current author.
 */
function ns_the_author_role( $author_id = null ) {
	$role = ns_get_the_author_role( $author_id );
	if ($role) {
    	echo $role;
	}
}
