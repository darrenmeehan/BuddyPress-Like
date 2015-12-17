<?php
/**
 * BuddyPress Like - Activty Comment Button
 *
 * This function is used to display the BuddyPress Like button on comments in the activity stream
 *
 * @package BuddyPress Like
 *
 */

/*
 * bplike_activity_comment_button()
 *
 * Outputs Like/Unlike button for activity comments.
 *
 */
function bplike_activity_comment_button() {

    $liked_count = 0;

    if ( is_user_logged_in() ) {

        if ( bp_activity_get_meta( bp_get_activity_comment_id() , 'liked_count' , true ) ) {
            $users_who_like = array_keys( bp_activity_get_meta( bp_get_activity_comment_id() , 'liked_count' , true ) );
            $liked_count = count( $users_who_like );
        }

        if ( ! bp_like_is_liked( bp_get_activity_comment_id(), 'activity_comment', get_current_user_id() ) ) {
            ?>
            <a href="#" class="acomment-reply bp-primary-action like" id="like-activity-<?php echo bp_get_activity_comment_id(); ?>" title="<?php echo bp_like_get_text( 'like_this_item' ); ?>"><?php
               echo bp_like_get_text( 'like' );
                if ( $liked_count ) {
                    echo ' <span><small>' . $liked_count . '</small></span>';
                }
                ?></a>
        <?php } else { ?>
            <a href="#" class="acomment-reply bp-primary-action unlike" id="unlike-activity-<?php echo bp_get_activity_comment_id(); ?>" title="<?php echo bp_like_get_text( 'unlike_this_item' ); ?>"><?php
                echo bp_like_get_text( 'unlike' );
                if ( $liked_count ) {
                    echo ' <span><small>' . $liked_count . '</small></span>';
                }
                ?></a>
            <?php
        }
    }
}
