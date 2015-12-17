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

  $liked_count = 0;

    if ( is_user_logged_in() ) {

        if ( get_post_meta( get_the_ID() , 'liked_count' , true ) ) {
            $users_who_like = array_keys( get_post_meta( get_the_ID() , 'liked_count' , true ) );
            $liked_count = count( $users_who_like );
          //  print_r($users_who_like);

        }

        ob_start();

        if ( ! bp_like_is_liked( get_the_ID(), 'blog_post', get_current_user_id() ) ) {
            ?>
            <a href="#" class="blogpost like" id="like-blogpost-<?php echo get_the_ID(); ?>" title="<?php echo bp_like_get_text( 'like_this_item' ); ?>">
                <?php
                    echo bp_like_get_text( 'like' );
                    if ( $liked_count ) {
                        echo ' <span>' . $liked_count . '</span>';
                    }
                ?>
            </a>
        <?php } else { ?>
            <a href="#" class="blogpost unlike" id="unlike-blogpost-<?php echo get_the_ID(); ?>" title="<?php echo bp_like_get_text( 'unlike_this_item' ); ?>">
                <?php
                    echo bp_like_get_text( 'unlike' );
                    if ( $liked_count ) {
                        echo '<span>' . $liked_count . '</span>';
                    }
                ?>
            </a>
            <?php
        }

        if ( isset ( $users_who_like ) ) {
            view_who_likes('blog_post'); // may need to add params here
        }

		$content .= ob_get_clean();
	}
	return $content;
}
add_filter('the_content', 'bplike_blog_post_button');
