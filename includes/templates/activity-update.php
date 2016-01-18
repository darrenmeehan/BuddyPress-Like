<?php
/**
 * BuddyPress Like - Activty Update Button
 *
 * This function is used to display the BuddyPress Like button on updates in the activity stream
 *
 * @package BuddyPress Like
 *
 */

/*
 * bplike_activity_update_button()
 *
 * Outputs Like/Unlike button for activity updates.
 *
 */
function bplike_activity_update_button() {

    $liked_count = 0;

    if ( is_user_logged_in() ) {
      if ( bp_get_activity_type() !== 'activity_liked' && bp_get_activity_type() != 'blogpost_liked' ) {
        $liked_count = count(  BPLIKE_LIKES::get_likers(bp_get_activity_id(), 'activity_update') );

        if ( ! bp_like_is_liked( bp_get_activity_id(), 'activity_update', get_current_user_id() ) ) { ?>
            <a href="#" class="button bp-primary-action like" id="like-activity-<?php echo bp_get_activity_id(); ?>" title="<?php echo bp_like_get_text( 'like_this_item' ); ?>">
                <?php echo bp_like_get_text( 'like' ); ?>
        <?php } else { ?>
            <a href="#" class="button bp-primary-action unlike" id="unlike-activity-<?php echo bp_get_activity_id(); ?>" title="<?php echo bp_like_get_text( 'unlike_this_item' ); ?>">
                <?php echo bp_like_get_text( 'unlike' ); ?>
        <?php } ?>
                <span><?php echo ( $liked_count ? $liked_count : '' ) ?></span>
            </a>
        <?php

        // Checking if there are users who like item.
        view_who_likes( bp_get_activity_id(), 'activity_update' );
      }
    }
}
