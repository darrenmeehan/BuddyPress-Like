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
 * Load the 'Likes' activity page.
 *
 * @since 0.4
 *
 * @uses bp_update_is_item_admin()
 * @uses bp_current_user_can()
 * @uses do_action() To call the 'bplike_activity_screen_likes' hook.
 * @uses bp_core_load_template()
 * @uses apply_filters() To call the 'bplike_activity_template_like_activity' hook.
 */
function bplike_activity_screen_likes() {

	/**
	 * Fires right before the activity loops starts.
	 * @since 0.4
	 */
	 add_filter( 'bp_ajax_querystring', 'bplike_filter_all_likes', 999 );

	/**
	 * Fires right before the loading of the "Likes" screen template file.
	 *
	 * @since 0.4
	 */
	do_action( 'bplike_activity_screen_likes' );

	/**
	 * Filters the template to load for the "Likes" screen.
	 *
	 * @since 0.4
	 *
	 * @param string $template Path to the activity template to load.
	 */
	bp_core_load_template( apply_filters( 'bplike_activity_template_like_activity', 'members/single/plugins' ) );
}

function bplike_screen_likes() {
	/**
	 * Fires before the loading of a single group's page.
	 *
	 * @since 1.0.0
	 */
	do_action( 'bplike_screen_likes' );
    
    add_action( 'bp_template_content', 'bplike_temp_output' );

	/**
	 * Filters the template to load for a single group's page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a single group's template to load.
	 */
	bp_core_load_template( apply_filters( 'bplike_template_likes', 'members/single/plugins' ) );
}

function bplike_temp_output() {
    echo 'Output stats on how many likes the users content got';
}