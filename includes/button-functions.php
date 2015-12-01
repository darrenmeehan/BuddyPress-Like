<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_button()
 *
 * Outputs the 'Like/Unlike' button.
 *
 */
function bp_like_button( $type = '' ) {

    /* Set the type if not already set, and check whether we are outputting the button on a blogpost or not. */
    if ( ! $type && ! is_single() ) {

        $type = 'activity';

    } elseif ( ! $type && is_single() ) {

        $type = 'blog_post';

    }
    if ( $type == 'activity' || $type == 'activity_update' ) {

        // TODO change this to use hook
        bplike_activity_update_button();

    } elseif ( $type == 'activity_comment') {

        // TODO change this to hook
        bplike_activity_comment_button();

    } elseif ( $type == 'blog_post' ) {

        // TODO change this to hook
        bplike_blog_button();
       //bp_get_activity_type();
    }
}

// Filters to display BuddyPress Like button.
add_action( 'bp_activity_entry_meta' , 'bplike_activity_update_button' );
add_action( 'bp_before_blog_single_post' , 'bplike_blog_post_button'  );
add_action( 'bp_activity_comment_options' , 'bplike_activity_comment_button' );
