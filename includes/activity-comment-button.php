<?php
/**
 * BuddyPress Like - Activty Comment Like Button
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
    $bp_like_comment_id = bp_get_activity_comment_id();
    $bp_like_id = bp_get_activity_comment_id();

    if ( bp_like_is_liked( $bp_like_id , 'activity' ) ) {
        $bp_like_class = 'acomment-reply bp-primary-action unlike';
    } else {
        $bp_like_class = 'acomment-reply bp-primary-action like';
    }

    if ( is_user_logged_in() && $activity_type !== 'activity_liked' ) {

        if ( bp_activity_get_meta( $bp_like_id , 'liked_count' , true ) ) {
            $users_who_like = array_keys( bp_activity_get_meta( $bp_like_id , 'liked_count' , true ) );
            $liked_count = count( $users_who_like );
        }

        if ( !bp_like_is_liked( $bp_like_id , 'activity_comment' ) ) {
            ?>
            <a href="#" class="<?php echo $bp_like_class; ?>" id="like-activity-<?php echo $bp_like_id; ?>" title="<?php echo __('Like this item', 'buddypress-like'); ?>"><?php
               echo __('Like ', 'buddypress-like');
                if ( $liked_count ) {
                    echo '<span>' . $liked_count . '</span>';
                }
                ?></a>
        <?php } else { ?>
            <a href="#" class="<?php echo $bp_like_class; ?>" id="unlike-activity-<?php echo $bp_like_id; ?>" title="<?php echo __('Unlike this item', 'buddypress-like'); ?>"><?php
                echo __('Unlike ', 'buddypress-like');
                if ( $liked_count ) {
                    echo '<span>' . $liked_count . '</span>';
                }
                ?></a>
            <?php
        }

        // Checking if there are users who like item.
        if ( isset ($users_who_like) ) {
            view_who_likes('activty_comment');
        }
    }
}
