<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_enqueue_scripts()
 *
 * Includes the terms required by plugins Javascript.
 *
 */
function bp_like_enqueue_scripts() {

    wp_register_script( 'bplike', plugins_url( '/assets/js/bp-like.js', dirname( __FILE__ ) ), array( 'jquery' ), BP_LIKE_VERSION );

    if ( ! is_admin() ) {

        wp_enqueue_script( 'bplike' );

        wp_localize_script( 'bplike', 'bplikeTerms', array(
                'like'           => bp_like_get_text( 'like' ),
                'unlike'         => bp_like_get_text('unlike'),
                'like_message'   => bp_like_get_text( 'like_this_item' ),
                'unlike_message' => bp_like_get_text( 'unlike_this_item' ),
                'you_like_this'  => bp_like_get_text( 'get_likes_only_liker' ),
                'fav_remove'     => bp_like_get_settings( 'remove_fav_button' ) == 1 ? '1' : '0'
            )
        );
    }
}
add_action( 'wp_enqueue_scripts' , 'bp_like_enqueue_scripts' );
