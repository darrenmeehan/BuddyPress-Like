<?php
/**
 * BuddyPress Like - Blog Post Button
 *
 * This function is used to display the BuddyPress Like button on blog posts on the WordPress site.
 *
 * @package BuddyPress Like
 *
 */
/*
 * bplike_blog_post_button()
 *
 * Outputs Like/Unlike button for blog posts.
 *
 */
function bplike_blog_post_button( $content ) {
    global $post;

    if (!is_singular($post->post_type) || !bp_like_get_settings('bp_like_post_types') ||
        !in_array($post->post_type, bp_like_get_settings('bp_like_post_types')))
        return $content;

    $liked_count = 0;

    if ( is_user_logged_in() ) {

        if ( get_post_meta( get_the_ID(), 'liked_count', true ) ) {
            $users_who_like = array_keys( get_post_meta( get_the_ID(), 'liked_count', true ) );
            $liked_count = count( $users_who_like );
        }

        ob_start();

        if ( ! bp_like_is_liked( get_the_ID(), 'blog_post', get_current_user_id() ) ) {
            ?>
            <a href="#" class="blogpost like" id="like-blogpost-<?php echo get_the_ID(); ?>" title="<?php echo bp_like_get_text( 'like_this_item' ); ?>">
                <?php
                    echo bp_like_get_text( 'like' );
                    echo ' <span>' . ( $liked_count ? $liked_count : '0' ) . '</span>';
                ?>
            </a>
        <?php } else { ?>
            <a href="#" class="blogpost unlike" id="unlike-blogpost-<?php echo get_the_ID(); ?>" title="<?php echo bp_like_get_text( 'unlike_this_item' ); ?>">
                <?php
                    echo bp_like_get_text( 'unlike' );
                    echo ' <span>' . ( $liked_count ? $liked_count : '0' ) . '</span>';
                ?>
            </a>
            <?php
        }

        view_who_likes( get_the_ID(), 'blog_post');

		$content .= ob_get_clean();
	}
	return $content;
}
add_filter('the_content', 'bplike_blog_post_button');
