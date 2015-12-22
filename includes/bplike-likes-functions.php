<?php

/**
 * BuddyPress Like Functions.
 *
 * Functions for the Likes component.
 *
 * @package BuddyPressLike
 * @subpackage LikesFunctions
 *
 * @todo Some functionality is being duplicated by making these functions.
 * Testing as a possible rewrite with the addition of Likes component
 */

 /**
  * Check whether the $bp global lists a likes directory page.
  *
  * @since 0.4
  *
  * @return bool True if likes directory page is found, otherwise false.
  */
 function bplike_has_directory() {
 	return (bool) ! empty( buddypress()->pages->likes->id );
 }


/**
 * Get a users liked items.
 *
 * @since 0.4
 *
 * @uses bp_get_user_meta()
 * @uses apply_filters() To call the 'bplike_likes_get_user_likes' hook.
 *
 * @param int $user_id ID of the user whose likes are being queried.
 * @return array IDs of the user's liked items.
 * @todo need to make different versions for different item types
 */
function bplike_likes_get_user_likes( $user_id = 0 ) {

	// Fallback to logged in user if no user_id is passed.
	if ( empty( $user_id ) ) {
		$user_id = bp_displayed_user_id();
	}

	// Get likes for user.
	$likes = bp_get_user_meta( $user_id, 'bp_liked_activities', true );

	/**
	 * Filters the liked items for a specified user.
	 *
	 * @since 0.4
	 *
	 * @param array $likes Array of user's liked items.
	 */
	return apply_filters( 'bplike_likes_get_user_likes', $likes );
}


/**
 * Retrieve the number of liked items a user has.
 *
 * @since 0.4
 *
 * @uses BPLIKE_LIKES::total_liked_count() {@link BPLIKE_LIKES}
 * @uses bp_displayed_user_id()
 * @uses bp_loggedin_user_id()
 *
 * @param int $user_id ID of the user whose liked count is being requested.
 * @return int Total liked count for the user.
 */
function bplike_total_likes_for_user( $user_id = 0 ) {

	// Fallback on displayed user, and then logged in user.
	if ( empty( $user_id ) ) {
		$user_id = ( bp_displayed_user_id() ) ? bp_displayed_user_id() : bp_loggedin_user_id();
	}

	return BPLIKE_LIKES::total_liked_count( $user_id );
}
