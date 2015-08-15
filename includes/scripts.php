<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_print_scripts()
 *
 * Includes the terms required by plugins Javascript.
 *
 */
add_action( 'wp_enqueue_scripts' , 'bp_like_enqueue_scripts' );

function bp_like_enqueue_scripts() {
    wp_register_script( 'bplike', plugins_url( '/assets/js/bp-like.js', dirname( __FILE__ ) ), array( 'jquery' ), BP_LIKE_VERSION );

    if ( !is_admin() ) {
        wp_enqueue_script( 'bplike' );
        wp_localize_script( 'bplike', 'bplikeTerms', array(
                'ajaxurl'        => admin_url( 'admin-ajax.php' ),
                'like'           => bp_like_get_text( 'like' ),
                'unlike'       => bp_like_get_text('unlike'), // todo why is there no unlike?
                'like_message'   => bp_like_get_text( 'like_this_item' ),
                'unlike_message' => bp_like_get_text( 'unlike_this_item' ),
                'you_like_this'  => __('You like this.', 'buddypress-like'),
                'view_likes'     => bp_like_get_text( 'view_likes' ),
                'hide_likes'     => bp_like_get_text( 'hide_likes' ),
          //      'unlike_1'       => sprintf( __('Unlike %s', 'buddypress-like'), '<span>1</span>' ),
                'fav_remove'     => bp_like_get_settings( 'remove_fav_button' ) == 1 ? '1' : '0'
            )
        );
    }
}
function bp_like_remove_favourites() {
    if( bp_like_get_settings('remove_fav_button') == 1 ) {
        
        add_filter( 'bp_activity_can_favorite', '__return_false', 1000 );
        add_filter( 'bp_get_total_favorite_count_for_user', '__return_false', 1000 );
        bp_core_remove_nav_item('favorites');

        function bp_like_admin_bar_render_remove_favorites() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('my-account-activity-favorites');
        }
        add_action( 'wp_before_admin_bar_render' , 'bp_like_admin_bar_render_remove_favorites' );
    }
}
add_action( 'init', 'bp_like_remove_favourites' );