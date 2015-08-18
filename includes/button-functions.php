<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_button()
 *
 * Outputs the 'Like/Unlike' button.
 *
 */
function bp_like_button( $type = '' ) {

    /* Set the type if not already set, and check whether we are outputting the button on a blogpost or not. */
    if ( ! $type && ! is_single() ) {
        
        $type = 'activity';

    } elseif ( ! $type && is_single() ) {
        
        $type = 'blogpost';

    }
    if ( $type == 'activity' || $type == 'activity_update' ) {

        bplike_activity_update_button();

    } elseif ( $type == 'activity_comment') {

        bplike_activity_comment_button();

    } elseif ( $type == 'blogpost' ) {

        //bplike_blog_button();
       // bp_get_activity_type();
    }
}

// Filters to display BuddyPress Like button.
add_action( 'bp_activity_entry_meta' , 'bp_like_button' );
add_action( 'bp_before_blog_single_post' , 'bp_like_button'  );
add_action( 'bp_activity_comment_options' , 'bplike_activity_comment_button' );

/*
 * bplike_activity_update_button()
 * 
 * Outputs Like/Unlike button for activity updates. 
 * 
 */
function bplike_activity_update_button() {

    $liked_count = 0; // is this really needed?

    if ( bp_like_is_liked( bp_get_activity_id() , 'activity' ) ) {
        $bp_like_class = 'unlike';
    } else {
        $bp_like_class = 'like';
    }

    if ( is_user_logged_in() && bp_get_activity_type() !== 'activity_liked' ) {

        if ( bp_activity_get_meta( bp_get_activity_id() , 'liked_count' , true ) ) {
            $users_who_like = array_keys( bp_activity_get_meta( bp_get_activity_id() , 'liked_count' , true ) );
            $liked_count = count( $users_who_like );
        }

        if ( !bp_like_is_liked( bp_get_activity_id() , 'activity' ) ) {
            ?>
            <a href="#" class="button bp-primary-action <?php echo $bp_like_class; ?>" id="like-activity-<?php echo bp_get_activity_id(); ?>" title="<?php echo __('Like this item', 'buddypress-like'); ?>">
                <?php 
                    echo __('Like ', 'buddypress-like');
                    if ( $liked_count ) {
                        echo '<span>' . $liked_count . '</span>';
                    }
                ?>
            </a>
        <?php } else { ?>
            <a href="#" class="button bp-primary-action <?php echo $bp_like_class; ?>" id="unlike-activity-<?php echo bp_get_activity_id(); ?>" title="<?php echo __('Unlike this item', 'buddypress-like'); ?>">
                <?php echo __('Unlike ', 'buddypress-like');
                    if ( $liked_count ) {
                        echo '<span>' . $liked_count . '</span>';
                    }
                ?>
            </a>
            <?php
        }

        // Checking if there are users who like item.
        if ( isset ($users_who_like) ) {
            view_who_likes();
        }
    }
}
