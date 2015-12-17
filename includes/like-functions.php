<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_is_liked()
 *
 * Checks to see whether the user has liked a given item.
 *
 */
function bp_like_is_liked( $item_id, $type, $user_id) {

    if ( ! $type || ! $item_id ) {
        return false;
    }

    if ( isset( $user_id ) ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
    }

    if ( $type == 'activity_update' || $type == 'activity_comment' ) {

        $user_likes = get_user_meta( $user_id , 'bp_liked_activities' , true );

    } elseif ( $type == 'blog_post' ) {

      $user_likes =  get_user_meta( $user_id , 'bp_liked_blogposts' , true );
    }

    if ( ! isset( $user_likes ) || ! $user_likes ) {
        return false;
    } elseif ( ! array_key_exists( $item_id , $user_likes ) ) {
        return false;
    } else {
        return true;
    }
}

/**
 * bp_like_add_user_like()
 *
 * Registers that the user likes a given item.
 *
 */
function bp_like_add_user_like( $item_id, $type ) {

    $liked_count = 0;

    if ( ! isset( $user_id ) ) {
        $user_id = get_current_user_id();
    }
    if ( ! $item_id || ! is_user_logged_in() ) {
        return false;
    }

    if ( $type == 'activity_update' ) {

        /* Add to the  users liked activities. */
        $user_likes = get_user_meta( $user_id , 'bp_liked_activities' , true );
        $user_likes[$item_id] = 'activity_liked';
        update_user_meta( $user_id , 'bp_liked_activities' , $user_likes );

        /* Add to the total likes for this activity. */
        $users_who_like = bp_activity_get_meta( $item_id , 'liked_count' , true );
        $users_who_like[$user_id] = 'user_likes';
        bp_activity_update_meta( $item_id , 'liked_count' , $users_who_like );

        $liked_count = count( $users_who_like );
        $group_id = 0;

        // check if this item is in a group or not, assign group id if so
        if ( bp_is_active( 'groups' ) && bp_is_group() ) {
          $group_id = bp_get_current_group_id();
        }

        bp_like_post_to_stream( $item_id , $user_id, $group_id );

    } elseif ($type == 'activity_comment') {

        /* Add to the  users liked activities. */
        $user_likes = get_user_meta( $user_id , 'bp_liked_activities' , true );
        $user_likes[$item_id] = 'activity_liked';
        update_user_meta( $user_id , 'bp_liked_activities' , $user_likes );

        /* Add to the total likes for this activity. */
        $users_who_like = bp_activity_get_meta( $item_id , 'liked_count' , true );
        $users_who_like[$user_id] = 'user_likes';
        bp_activity_update_meta( $item_id , 'liked_count' , $users_who_like );

        $liked_count = count( $users_who_like );

        // not publishing to activity stream for comments

    } elseif ( $type == 'blog_post' ) {

        /* Add to the users liked blog posts. */
        $user_likes = get_user_meta( $user_id , 'bp_liked_blogposts' , true );
        $user_likes[$item_id] = 'blogpost_liked';
        update_user_meta( $user_id , 'bp_liked_blogposts' , $user_likes );

        /* Add to the total likes for this blog post. */
        $users_who_like = get_post_meta( $item_id , 'liked_count' , true );
        $users_who_like[$user_id] = 'user_likes';
        update_post_meta( $item_id , 'liked_count' , $users_who_like );

        $liked_count = count( $users_who_like );

        if ( bp_like_get_settings( 'post_to_activity_stream' ) == 1 ) {
            $post = get_post( $item_id );
            $author_id = $post->post_author;

            $liker = bp_core_get_userlink( $user_id );
            $permalink = get_permalink( $item_id );
            $title = $post->post_title;
            $author = bp_core_get_userlink( $post->post_author );

            if ( $user_id == $author_id ) {
                $action = bp_like_get_text( 'record_activity_likes_own_blogpost' );
            } elseif ( $user_id == 0 ) {
                $action = bp_like_get_text( 'record_activity_likes_a_blogpost' );
            } else {
                $action = bp_like_get_text( 'record_activity_likes_users_blogpost' );
            }

            /* Filter out the placeholders */
            $action = str_replace( '%user%' , $liker , $action );
            $action = str_replace( '%permalink%' , $permalink , $action );
            $action = str_replace( '%title%' , $title , $action );
            $action = str_replace( '%author%' , $author , $action );

            /* Grab the content and make it into an excerpt of 140 chars if we're allowed */
            if ( bp_like_get_settings( 'show_excerpt' ) == 1 ) {
                $content = $post->post_content;
                if ( strlen( $content ) > bp_like_get_settings( 'excerpt_length' ) ) {
                    $content = substr( $content , 0 , bp_like_get_settings( 'excerpt_length' ) );
                    $content = $content . '...';
                }
            };

            bp_activity_add(
                    array(
                        'action' => $action ,
                        'content' => $content ,
                        'component' => 'bp-like' ,
                        'type' => 'blogpost_liked' ,
                        'user_id' => $user_id ,
                        'item_id' => $item_id ,
                        'primary_link' => $permalink
                    )
            );
        }
    }
    echo bp_like_get_text( 'unlike' );

    if ( $liked_count ) {
        echo ' <span>' . $liked_count . '</span>';
    }
}

/**
 * bp_like_remove_user_like()
 *
 * Registers that the user has unliked a given item.
 *
 */
function bp_like_remove_user_like( $item_id = '' , $type = '' ) {

    if ( ! $item_id ) {
        return false;
    }

    if ( ! isset( $user_id ) ) {

        $user_id = get_current_user_id();
    }

    if ( 0 == $user_id ) {

        __('Sorry, you must be logged in to like that.', 'buddypress-like');
        return false;
    }

    if ( $type == 'activity_update' ) {

        /* Remove this from the users liked activities. */
        $user_likes = get_user_meta( $user_id , 'bp_liked_activities' , true );
        unset( $user_likes[$item_id] );
        update_user_meta( $user_id , 'bp_liked_activities' , $user_likes );

        /* Update the total number of users who have liked this activity. */
        $users_who_like = bp_activity_get_meta( $item_id , 'liked_count' , true );
        unset( $users_who_like[$user_id] );

        /* If nobody likes the activity, delete the meta for it to save space, otherwise, update the meta */
        if ( empty( $users_who_like ) ) {
            bp_activity_delete_meta( $item_id , 'liked_count' );
        } else {
            bp_activity_update_meta( $item_id , 'liked_count' , $users_who_like );
        }

        $liked_count = count( $users_who_like );

        if ( bp_is_group() ) {

          $bp = buddypress();
          $update_id = bp_activity_get_activity_id(
                array(
                  'user_id'     => $user_id,
                  'component'   => $bp->groups->id,
                  'type'        => 'activity_liked',
                  'item_id'             => bp_get_current_group_id(),
                  'secondary_item_id' => $item_id ,
                )
        );

        $result = bp_activity_delete(
                array(
                   'id' => $update_id,
                   'user_id' => $user_id,
                   'secondary_item_id' => $item_id,
                   'type' => 'activity_liked' , // need to check if this is correct
                   'component'         => $bp->groups->id,
                   'item_id'           => bp_get_current_group_id()
                )
        );
      //  error_log('update_id: ' . $update_id);
      //  error_log('item_id: ' . $item_id);
      //  error_log('result: ' . $result);
      //  error_log(bp_get_current_group_id());

      } else {
        /* Remove the update on the users profile from when they liked the activity. */
        $update_id = bp_activity_get_activity_id(
                array(
                    'item_id' => $item_id ,
                    'component' => 'bp-like',
                    'type' => 'activity_liked' ,
                    'user_id' => $user_id
                )
            );

            bp_activity_delete(
                    array(
                       'id' => $update_id ,
                       'user_id' => $user_id
                    )
            );
      }

    } elseif ( $type == 'activity_comment' ) {

        /* Remove this from the users liked activities. */
        $user_likes = get_user_meta( $user_id , 'bp_liked_activities' , true );
        unset( $user_likes[$item_id] );
        update_user_meta( $user_id , 'bp_liked_activities' , $user_likes );

        /* Update the total number of users who have liked this activity. */
        $users_who_like = bp_activity_get_meta( $item_id , 'liked_count' , true );
        unset( $users_who_like[$user_id] );

        /* If nobody likes the activity, delete the meta for it to save space, otherwise, update the meta */
        if ( empty( $users_who_like ) ) {
            bp_activity_delete_meta( $item_id , 'liked_count' );
        } else {
            bp_activity_update_meta( $item_id , 'liked_count' , $users_who_like );
        }

        $liked_count = count( $users_who_like );

        /* Remove the update on the users profile from when they liked the activity. */
        $update_id = bp_activity_get_activity_id(
                array(
                    'item_id' => $item_id ,
                    'component' => 'bp-like' ,
                    'type' => 'activity_liked' ,
                    'user_id' => $user_id
                )
        );

    } elseif ( $type == 'blog_post' ) {

        /* Remove this from the users liked activities. */
        $user_likes = get_user_meta( $user_id , 'bp_liked_blogposts' , true );
        unset( $user_likes[$item_id] );
        update_user_meta( $user_id , 'bp_liked_blogposts' , $user_likes );

        /* Update the total number of users who have liked this blog post. */
        $users_who_like = get_post_meta( $item_id , 'liked_count' , true );
        unset( $users_who_like[$user_id] );

        /* If nobody likes the blog post, delete the meta for it to save space, otherwise, update the meta */
        if ( empty( $users_who_like ) ) {
            delete_post_meta( $item_id , 'liked_count' );
        } else {
            update_post_meta( $item_id , 'liked_count' , $users_who_like );
        }

        $liked_count = count( $users_who_like );

        /* Remove the update on the users profile from when they liked the activity. */
        $update_id = bp_activity_get_activity_id(
                array(
                    'item_id' => $item_id ,
                    'component' => 'bp-like' ,
                    'type' => 'blogpost_liked' ,
                    'user_id' => $user_id
                )
        );

        bp_activity_delete(
                array(
                    'id' => $update_id ,
                    'item_id' => $item_id ,
                    'component' => 'bp-like' ,
                    'type' => 'blogpost_liked' ,
                    'user_id' => $user_id
                )
        );
    }

    echo bp_like_get_text( 'like' );

    if ( $liked_count ) {
        echo ' <span>' . $liked_count . '</span>';
    }
}

/*
 * bp_like_get_some_likes()
 *
 * Description: Returns a defined number of likers, beginning with more recent.
 *
 */
function bp_like_get_some_likes( $id, $type ) {

  if ( $type == 'blog_post' ) {
    if ( is_single() ) { // todo is this needed?
      $users_who_like = array_keys( (array)(get_post_meta( $id , 'liked_count' , true )) );
    } else {
      $users_who_like = array_keys((array)(bp_activity_get_meta( $id , 'liked_count' , true )));
    }
  } elseif ( $type == 'activity_update' ) {
    $users_who_like = array_keys((array) (bp_activity_get_meta( $id , 'liked_count' , true )));
  }

  // print_r(bp_activity_get_types());

  // if the current users likes the item
  if ( in_array( get_current_user_id(), $users_who_like ) ) {

    if ( count( $users_who_like ) == 0 ) {
      // if noone likes this, do nothing as nothing gets outputted

    } elseif ( count( $users_who_like ) == 1 ) {

        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $id;
        $string .= '"><small>';
        $string .= bp_like_get_text( 'get_likes_only_liker' );
        $string .= '</small></p>';

        print($string);

    } elseif ( count( $users_who_like ) == 2 ) {

        // find where the current_user is in the array $users_who_like
        $key = array_search( get_current_user_id(), $users_who_like, true );

        // removing current user from $users_who_like
        // TODO is key the same as offset?
        array_splice( $users_who_like, $key, 1 );

        $one = bp_core_get_userlink( $users_who_like[0] );

        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $id;
        $string .= '"><small>You and %s like this.</small></p>'; // TODO translate

        printf( $string , $one );

    } elseif ( count( $users_who_like ) == 3 ) {

          $key = array_search( get_current_user_id(), $users_who_like, true );

          // removing current user from $users_who_like
          array_splice( $users_who_like, $key, 1 );

          $others = count ($users_who_like);
          $one = bp_core_get_userlink( $users_who_like[$others - 1] );
          $two = bp_core_get_userlink( $users_who_like[$others - 2] );

          $string = '<p class="users-who-like" id="users-who-like-';
          $string .= $id;
          $string .= '"><small>You, %s and %s like this.</small></p>';

          printf( $string , $one , $two );

    } elseif (  count( $users_who_like ) > 3 ) {

      $key = array_search( get_current_user_id(), $users_who_like, true );

      // removing current user from $users_who_like
      array_splice( $users_who_like, $key, 1 );

          $others = count ($users_who_like);

          // output last two people to like (2 at end of array)
          $one = bp_core_get_userlink( $users_who_like[$others - 2] );
          $two = bp_core_get_userlink( $users_who_like[$others - 1] );

          $others = $others - 2;

          $string = '<p class="users-who-like" id="users-who-like-';
          $string .= $id;
          $string .= '"><small>You, %s, %s and %d ' . _n( 'other', 'others', $others ) . ' like this.</small></p>';

          printf( $string , $one , $two , $others );

        }
    } else {

    if ( count( $users_who_like ) == 0 ) {
      // if noone likes this, do nothing as nothing gets outputted

    } elseif ( count( $users_who_like ) == 1 ) {

        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $id;
        $string .= '"><small>';
        $string .= bp_like_get_text( 'one_likes_this' );
        $string .= '</small></p>';

        $one = bp_core_get_userlink( $users_who_like[0] );

        printf($string, $one);

    } elseif ( count( $users_who_like ) == 2 ) {

        $one = bp_core_get_userlink( $users_who_like[0] );
        $two = bp_core_get_userlink( $users_who_like[1] );

        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $id;
        $string .= '"><small>';
        $string .= '%s and %s like this.</small></p>'; // TODO translate

        printf( $string , $one, $two );

    } elseif ( count( $users_who_like ) == 3 ) {

          $one = bp_core_get_userlink( $users_who_like[0] );
          $two = bp_core_get_userlink( $users_who_like[1] );
          $three = bp_core_get_userlink( $users_who_like[2] );

          $string = '<p class="users-who-like" id="users-who-like-';
          $string .= $id;
          $string .= '"><small>%s, %s and %s like this.</small></p>';

          printf( $string , $one , $two, $three );

        } elseif (  count( $users_who_like ) > 3 ) {

          $others = count ($users_who_like);

          // output last two people to like (3 at end of array)
          $one = bp_core_get_userlink( $users_who_like[$others - 1] );
          $two = bp_core_get_userlink( $users_who_like[$others - 2] );
          $three = bp_core_get_userlink( $users_who_like[$others - 3] );

          $others = $others - 3;

          $string = '<p class="users-who-like" id="users-who-like-';
          $string .= $id;
          $string .= '"><small>';
          $string .= '%s, %s, %s and %d ' . _n( 'other', 'others', $others ) . ' like this.</small></p>';

          printf( $string , $one , $two , $three, $others );
        }
  }

    }

/**
 *
 * view_who_likes() hook
 * TODO explain better
 *
 */
function view_who_likes( $id,  $type ) {

    do_action( 'bp_like_before_view_who_likes' );

    do_action( 'view_who_likes', $id, $type );

    do_action( 'bp_like_after_view_who_likes' );

}

// TODO comment why this is here
add_action( 'view_who_likes' , 'bp_like_get_some_likes', 10, 2 );
