<?php
/**
 * BuddyPress Like Screens.
 *
 * The functions in this file detect, with each page load, whether a Like
 * component page is being requested. If so, it parses any necessary data from
 * the URL, and tells BuddyPress to load the appropriate template.
 *
 * @package BuddyPressLike
 * @subpackage Screens
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


/**
 * Load the Likes directory.
 *
 * @since 0.4
 *
 * @uses bp_displayed_user_id()
 * @uses bplike_is_likes_component()
 * @uses bp_current_action()
 * @uses bp_update_is_directory()
 * @uses do_action() To call the 'bplike_likes_screen_index' hook.
 * @uses bp_core_load_template()
 * @uses apply_filters() To call the 'bplike_likes_screen_index' hook.
 */
function bplike_likes_screen_index() {
	if ( bplike_is_likes_component() ) {
		bp_update_is_directory( true, 'likes' );

		/**
		 * Fires right before the loading of the Likes directory screen template file.
		 *
		 * @since 0.4
		 */
		do_action( 'bplike_likes_screen_index' );

		/**
		 * Filters the template to load for the Likes directory screen.
		 *
		 * @since 0.4
		 *
		 * @todo add content
		 */
	//	bp_core_load_template( apply_filters( 'bplike_likes_screen_index', 'activity/index' ) );

  echo 'this is bplike_likes_screen_index() here, fill in with details after';
	}
}
add_action( 'bp_screens', 'bplike_likes_screen_index' );

/**
 * Catch and process the My Likes page.
 * @todo rename page to My Likes
 */
function bplike_screen_my_likes() {

	/**
	 * Fires before the loading of template for the My Likes page.
	 *
	 * @since 0.4
	 */
	do_action( 'friends_screen_my_friends' );

	/**
	 * 
	 *
	 * @since 0.4
	 */
	function bplike_testing() {
		echo 'this is a test';
	}

	add_action( 'bp_template_content', 'bplike_testing' );

	/**
	 * Filters the template used to display the My Likes page.
	 *
	 * @since 0.4
	 *
	 * @param string $template Path to the plugins template to load.
	 */
	bp_core_load_template( apply_filters( 'likes_template_my_likes', 'members/single/plugins' ) );
}



/**
 * Load the 'Updates' likes page.
 *
 * @since 0.4
 *
 * @uses bp_update_is_item_admin()
 * @uses bp_current_user_can()
 * @uses do_action() To call the 'bp_activity_screen_mentions' hook.
 * @uses bp_core_load_template()
 * @uses apply_filters() To call the 'bp_activity_template_mention_activity' hook.
 */
function bplike_likes_screen_updates() {
//	bp_update_is_item_admin( bp_current_user_can( 'bp_moderate' ), 'activity' );

	/**
	 * Fires right before the loading of the "Mentions" screen template file.
	 *
	 * @since 0.4
	 */
	do_action( 'bplike_likes_screen_updates' );

	/**
	 * Filters the template to load for the Likes comments screen.
	 *
	 * @since 0.4
	 *
	 */
//	bp_core_load_template( apply_filters( 'bp_activity_template_mention_activity', 'members/single/home' ) );
  echo ' bplike_likes_screen_updates() just display update likes here';
}


/**
 * Load the 'Updates' likes page.
 *
 * @since 0.4
 *
 * @uses bp_update_is_item_admin()
 * @uses bp_current_user_can()
 * @uses do_action() To call the 'bp_activity_screen_mentions' hook.
 * @uses bp_core_load_template()
 * @uses apply_filters() To call the 'bp_activity_template_mention_activity' hook.
 */
function bplike_likes_screen_comments() {
//	bp_update_is_item_admin( bp_current_user_can( 'bp_moderate' ), 'activity' );

	/**
	 * Fires right before the loading of the "Mentions" screen template file.
	 *
	 * @since 0.4
	 */
	do_action( 'bplike_likes_screen_comments' );

	/**
	 * Filters the template to load for the Likes comments screen.
	 *
	 * @since 0.4
	 *
	 */
//	bp_core_load_template( apply_filters( 'bp_activity_template_mention_activity', 'members/single/home' ) );
  echo ' bplike_likes_screen_comments() just display update likes here';
}
