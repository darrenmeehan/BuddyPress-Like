<?php
/**
 * BuddyPress Like - BBP Reply Button
 *
 * This function is used to display the BuddyPress Like button on bbp replies on the WordPress site.
 *
 * @package BuddyPress Like
 *
 */
/*
 * bplike_bbp_reply_button()
 *
 * Outputs Like/Unlike button for bbp replies.
 *
 */
function bplike_bbp_reply_button() {
    global $post;

    if (!bp_like_get_settings('bp_like_post_types') ||
        !in_array($post->post_type, bp_like_get_settings('bp_like_post_types')))
        return;

    $liked_count = 0;

    if ( is_user_logged_in() ) {

        $liked_count = count(  BPLIKE_LIKES::get_likers(get_the_ID(), 'bbp_reply') );

        if ( ! bp_like_is_liked( get_the_ID(), 'bbp_reply', get_current_user_id() ) ) { ?>
            <a href="#" class="bbp-reply like" id="like-bbp-reply-<?php echo get_the_ID(); ?>" title="<?php echo bp_like_get_text( 'like_this_item' ); ?>">
                <?php echo bp_like_get_text( 'like' ); ?>
        <?php } else { ?>
            <a href="#" class="bbp-reply unlike" id="unlike-bbp-reply-<?php echo get_the_ID(); ?>" title="<?php echo bp_like_get_text( 'unlike_this_item' ); ?>">
                <?php echo bp_like_get_text( 'unlike' ); ?>
        <?php } ?>
                <span><?php echo ( $liked_count ? $liked_count : '' ) ?></span>
            </a>
        <?php

        view_who_likes( get_the_ID(), 'bbp_reply');
    }
}
add_action('bbp_theme_after_reply_content', 'bplike_bbp_reply_button');
