<?php
/**
 * BuddyPress Like - Blog Post Comment Button
 *
 * This function is used to display the BuddyPress Like button on blog post comments on the WordPress site.
 *
 * @package BuddyPress Like
 *
 */
/*
 * bplike_blog_post_comment_button()
 *
 * Outputs Like/Unlike button for blog post comments.
 *
 */
function bplike_blog_post_comment_button( $content ) {
    global $post;

    if (is_admin() || !bp_like_get_settings('bp_like_post_types') ||
        !in_array($post->post_type, bp_like_get_settings('bp_like_post_types')))
        return $content;

    $liked_count = 0;

    if ( is_user_logged_in() ) {

        if ( get_comment_meta( get_comment_ID(), 'liked_count', true ) ) {
            $users_who_like = array_keys( get_comment_meta( get_comment_ID(), 'liked_count', true ) );
            $liked_count = count( $users_who_like );
        }

        ob_start();

        if ( ! bp_like_is_liked( get_comment_ID(), 'blog_post_comment', get_current_user_id() ) ) {
            ?>
            <br><a href="#" class="blogpost_comment like" id="like-blogpost-comment-<?php echo get_comment_ID(); ?>" title="<?php echo bp_like_get_text( 'like_this_item' ); ?>"><?php echo bp_like_get_text( 'like' ); echo ' <span>' . ($liked_count?$liked_count:'') . '</span>'; ?>
            </a>
        <?php } else { ?>
            <br><a href="#" class="blogpost_comment unlike" id="unlike-blogpost-comment-<?php echo get_comment_ID(); ?>" title="<?php echo bp_like_get_text( 'unlike_this_item' ); ?>"><?php echo bp_like_get_text( 'unlike' ); echo ' <span>' . ($liked_count?$liked_count:'') . '</span>'; ?>
            </a>
            <?php
        }

        view_who_likes( get_comment_ID(), 'blog_post_comment', '<span', '</span>');

		$content .= ob_get_clean();
	}
	return $content;
}
add_filter('get_comment_text', 'bplike_blog_post_comment_button');

