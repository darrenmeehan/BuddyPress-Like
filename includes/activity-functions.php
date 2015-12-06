<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_activity_filter()
 *
 * Adds activity stream filter options for 'Update Likes' and 'Blog Post Likes'.
 *
 */
function bp_like_activity_filter() {
    echo '<option value="activity_liked">' . bp_like_get_text('update_likes') . '</option>';
  //  echo '<option value="blogpost_liked">' . bp_like_get_text('show_blogpost_likes') . '</option>';
}

add_action( 'bp_activity_filter_options' , 'bp_like_activity_filter' );
add_action( 'bp_member_activity_filter_options' , 'bp_like_activity_filter' );
add_action( 'bp_group_activity_filter_options' , 'bp_like_activity_filter' );

/**
 * bp_like_post_to_stream()
 *
 * Posts to stream, depending on settings
 *
 */
function bp_like_post_to_stream( $item_id , $user_id, $group_id ) {

    // check if posting to activity stream option is enabled or not.
    // 1 = turned on, 0 = turned off.
    if ( bp_like_get_settings( 'post_to_activity_stream' ) == 1 ) {

        $activity = bp_activity_get_specific(
                    array(
                      'activity_ids' => $item_id ,
                      'component' => 'buddypress-like'
                    ) );

        $author_id = $activity['activities'][0]->user_id;

        if ( $user_id == $author_id ) {

            $action = bp_like_get_text( 'record_activity_likes_own' );

        } elseif ( $user_id == 0 ) {
          // TODO why would this be needed?
          // we should be able to get user_id
            $action = bp_like_get_text( 'record_activity_likes_an' );
        } else {
            $action = bp_like_get_text( 'record_activity_likes_users' );
        }

        $liker = bp_core_get_userlink( $user_id );
        $author = bp_core_get_userlink( $author_id );
        $activity_url = bp_activity_get_permalink( $item_id );
        $content = '';

        /* Grab the content and make it into an excerpt of 140 chars if we're allowed */
        if ( bp_like_get_settings( 'show_excerpt' ) == 1 ) {
        error_log('testing');
            $content = $activity['activities'][0]->content;
            if ( strlen( $content ) > bp_like_get_settings( 'excerpt_length' ) ) {
                $content = substr( $content , 0 , bp_like_get_settings( 'excerpt_length' ) );
                $content = strip_tags($content);
                $content = $content . '...';
            }
        }

        /* Filter out the placeholders */
        $action = str_replace( '%user%' , $liker , $action );
        $action = str_replace( '%permalink%' , $activity_url , $action );
        $action = str_replace( '%author%' , $author , $action );

        // if not in a group post as normal to activty stream
        if ( $group_id == 0 ) {
          bp_activity_add(
                  array(
                      'action' => $action,
                      'content' => $content,
                      'primary_link' => $activity_url,
                      'component' => 'bp-like',
                      'type' => 'activity_liked',
                      'user_id' => $user_id,
                      'item_id' => $item_id
                  )
          );

        } else {
          // Record in groups activity stream
           groups_record_activity( array(
          		'action'  => $action,
              'primary_link' => $activity_url,
          		'type'    => 'activity_liked',
          		'item_id' => $group_id,
          		'user_id' => $user_id,
              'hide_sitewide' => true
          	) );
        }

    }
}
