<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * bp_like_print_scripts()
 *
 * Includes the terms required by plugins Javascript.
 * Enqueues jQuery UI.
 *
 */
add_action( 'wp_print_scripts' , 'bp_like_print_scripts' );
function bp_like_print_scripts() {
    wp_register_script( 'bplike-jquery', plugins_url( '/assets/js/bp-like.js', dirname( __FILE__ ) ), array( 'jquery' ), BP_LIKE_VERSION );

    if ( !is_admin() ) {
        wp_enqueue_script( 'bplike-jquery' );
        wp_localize_script( 'bplike-jquery', 'bplikeTerms', array(
                'like' => bp_like_get_text( 'like' ),
                'like_message' => bp_like_get_text( 'like_this_item' ),
                'unlike_message' => bp_like_get_text( 'unlike_this_item' ),
                'view_likes' => bp_like_get_text( 'view_likes' ),
                'hide_likes' => bp_like_get_text( 'hide_likes' ),
                'unlike_1' => bp_like_get_text( 'unlike' ),
                'fav_remove'            => bp_like_get_settings( 'remove_fav_button' ) == 1 ? '1' : '0'
            )
        );
    }
    /* JQuery dialog for likers popup. */
    wp_enqueue_script( 'jquery-ui-dialog' );
}
