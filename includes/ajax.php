<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_process_ajax()
 *
 * Runs the relevant function depending on what AJAX call has been made.
 *
 */
function bp_like_process_ajax() {

    // ensuring $id only contains an integer
    $id = preg_replace( "/\D/" , "" , $_POST['id'] );

    if ( $_POST['type'] == 'activity_update like' ) {
        bp_like_add_user_like( $id , 'activity_update' );
    }

    if ( $_POST['type'] == 'activity_update unlike' ) {
        bp_like_remove_user_like( $id , 'activity_update' );
    }

    if ( $_POST['type'] == 'activity_comment like' ) {
        bp_like_add_user_like( $id , 'activity_comment' );
    }

    if ( $_POST['type'] == 'activity_comment unlike' ) {
        bp_like_remove_user_like( $id , 'activity_comment' );
    }

    if ( $_POST['type'] == 'button view-likes' ) {
        bp_like_get_some_likes( $id , 'activity' );
    }

    if ( $_POST['type'] == 'blog_post like' ) {
        bp_like_add_user_like( $id , 'blog_post' );
    }

    if ( $_POST['type'] == 'blog_post unlike' ) {
        bp_like_remove_user_like( $id , 'blog_post' );
    }

    if ( $_POST['type'] == 'acomment-reply bp-primary-action view-likes' ) {
        bp_like_get_some_likes( $id , 'activity' );
    }

    die();

}

add_action( 'wp_ajax_activity_like' , 'bp_like_process_ajax' );
