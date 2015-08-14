<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * bp_like_button()
 *
 * Outputs the 'Like/Unlike' button.
 *
 */
function bp_like_button( $type = '' ) {

    /* Set the type if not already set, and check whether we are outputting the button on a blogpost or not. */
    if ( !$type && !is_single() ) {
        $type = 'activity';
    } elseif ( !$type && is_single() ) {
        $type = 'blogpost';
    }
    if ( $type == 'activity' ) {
        bplike_activity_button();
    } elseif ( $type == 'blogpost' ) {
        bplike_blog_button();
        bp_get_activity_type();
    }
}

// Filters to display BuddyPress Like button.
add_filter( 'bp_activity_entry_meta' , 'bp_like_button', 1000 );
add_action( 'bp_before_blog_single_post' , 'bp_like_button' , 1000 );
add_filter( 'bp_activity_comment_options' , 'bp_like_button', 1000 );

/*
 * bplike_activity_button()
 * 
 * Outputs Like/Unlike button for activity items. 
 * 
 * Make simplier.
 * Get type in a better way. (comment or activty item, etc)
 */
function bplike_activity_button() {

    $liked_count = 0;
    $bp_like_comment_id = bp_get_activity_comment_id();

    if ( empty( $bp_like_comment_id ) ) {

        $bp_like_id = bp_get_activity_id();

        if ( bp_like_is_liked( $bp_like_id , 'activity' ) ) {
            $bp_like_class = 'button unlike bp-primary-action';
        } else {
            $bp_like_class = 'button like bp-primary-action';
        }
    } else {

        $bp_like_id = bp_get_activity_comment_id();

        if ( bp_like_is_liked( $bp_like_id , 'activity' ) ) {
            $bp_like_class = 'acomment-reply unlike bp-primary-action';
        } else {
            $bp_like_class = 'acomment-reply like bp-primary-action';
        }
    }

    $activity_type = bp_get_activity_type();

    if ( $activity_type === null ) {
        $activity_type = 'activity_update';
    }

    if ( is_user_logged_in() && $activity_type !== 'activity_liked' ) {

        if ( bp_activity_get_meta( $bp_like_id , 'liked_count' , true ) ) {
            $users_who_like = array_keys( bp_activity_get_meta( $bp_like_id , 'liked_count' , true ) );
            $liked_count = count( $users_who_like );
        }

        if ( !bp_like_is_liked( $bp_like_id , 'activity' ) ) {
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
            view_who_likes();
        }
    }
}
