<?php
/**
 * BuddyPress Like Template Functions.
 *
 * @package BuddyPressLike
 * @subpackage Template
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Output the likes component slug.
 *
 * @since 0.4
 *
 * @uses bplike_get_likes_slug()
 */
function bplike_likes_slug() {
	echo bplike_get_likes_slug();
}

/**
 * Return the likes component slug.
 *
 * @since 0.4
 */
function bplike_get_likes_slug() {

	/**
	 * Filters the likes component slug.
	 *
	 * @since 0.4
	 */
	return apply_filters( 'bplike_get_likes_slug', buddypress()->likes->slug );
}

/**
 * Output the likes component root slug.
 *
 * @since 0.4
 *
 * @uses bplike_get_likes_root_slug()
 */
function bplike_likes_root_slug() {
	echo bplike_get_likes_root_slug();
}
	/**
	 * Return the likes component root slug.
	 *
	 * @since 0.4
	 */
	function bplike_get_likes_root_slug() {

		/**
		 * Filters the likes component root slug.
		 *
		 * @since 0.4
		 */
		return apply_filters( 'bplike_get_likes_root_slug', buddypress()->likes->root_slug );
	}

/**
 * Is the current page the likes directory?
 *
 * @since 0.4
 *
 * @return True if the current page is the likes directory.
 */
function bplike_is_likes_directory() {
	if ( ! bp_displayed_user_id() && bp_is_activity_component() && ! bp_current_action() ) {
		return true;
	}

	return false;
}


/**
 * Check whether the current page is part of the Activity component.
 *
 * @since 0.4
 *
 * @return bool True if the current page is part of the Activity component.
 */
function bplike_is_likes_component() {
	return (bool) bp_is_current_component( 'likes' );
}

/**
 * Fires before the listing of likes activity type tab.
 *
 * @since 0.4
 */
 function bplike_add_my_likes_to_activity_tab() {

	 do_action( 'bplike_before_activity_type_tab_likes' ); ?>

	 <?php if ( bplike_total_likes_for_user( bp_loggedin_user_id() ) ) : ?>
		 <li id="activity-likes"><a href="<?php echo bp_loggedin_user_domain() . bp_get_activity_slug() . '/' . bplike_get_likes_slug() . '/'; ?>" title="<?php esc_attr_e( "Activity I've liked.", 'buddypress-like' ); ?>"><?php printf( __( 'My Likes %s', 'buddypress-like' ), '<span>' . bplike_total_likes_for_user( bp_loggedin_user_id() ) . '</span>' ); ?></a></li>
	 <?php endif;
}
add_action( 'bp_before_activity_type_tab_mentions', 'bplike_add_my_likes_to_activity_tab');

/**
 * Changes $query for activty stream to only display liked items.
 *
 * @since 0.4
 * @var $query
 *
 * @return var $query with action only containing liked items.
 */
function bplike_filter_all_likes( $query ) {
	if ( empty( $query ) && empty( $_POST ) ) {
		$query = 'action=activity_liked, blogpost_liked';
	}
	return $query;
}

/**
 * Changes $query for activty stream to only display liked items.
 *
 * @since 0.4
 * @var $query
 *
 * @return var $query with action only containing liked items.
 */
function bplike_filter_activity_likes_only( $query ) {
	if ( empty( $query ) && empty( $_POST ) ) {
		$query = 'action=activity_liked';
	}
	return $query;
}

