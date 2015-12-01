<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * bp_like_is_liked()
 *
 * Checks to see whether the user has liked a given item.
 *
 */
function bp_like_is_liked( $item_id = '' , $type = '' , $user_id = '' ) {

    if ( ! $type || ! $item_id ) {
        return false;
    }

    if ( isset( $user_id ) ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
    }

    if ( $type == 'activity' ) {

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
function bp_like_add_user_like( $item_id = '' , $type = '' ) {

    $liked_count = 0;

    if ( ! isset( $user_id ) ) {
        $user_id = get_current_user_id();
    }
    if ( ! $item_id || !is_user_logged_in() ) {
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

        bp_like_post_to_stream( $item_id , $user_id );

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

        /* Remove the update on the users profile from when they liked the activity. */
        $update_id = bp_activity_get_activity_id(
                array(
                    'item_id' => $item_id ,
                    'component' => 'bp-like' ,
                    'type' => 'activity_liked' ,
                    'user_id' => $user_id
                )
        );

        bp_activity_delete(
                array(
                    'id' => $update_id ,
                    'item_id' => $item_id ,
                    'component' => 'bp-like' ,
                    'type' => 'activity_liked' ,
                    'user_id' => $user_id
                )
        );
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
function bp_like_get_some_likes( $type = '', $location = '' ) {

// need to figure out if this is being displayed on blog post or
// about blog post on activity stream
if ( $location == 'single' ) {

}
  if ( $type == 'blog_post' ) {
    if ( is_single() ) {
      $bp_like_id = get_the_ID();
      $users_who_like = array_keys( (array)(get_post_meta( $bp_like_id , 'liked_count' , true )) );
    } else {
      $bp_like_id = bp_get_activity_id();
      $users_who_like = array_keys((array)(bp_activity_get_meta( $bp_like_id , 'liked_count' , true )));
    }


} elseif ( $type == 'activity_update' ) {
    $bp_like_id = bp_get_activity_id();
    $users_who_like = array_keys((array)(bp_activity_get_meta( $bp_like_id , 'liked_count' , true )));

  } else {
    $bp_like_id = bp_get_activity_id();
    $users_who_like = array_keys((array)(bp_activity_get_meta( $bp_like_id , 'liked_count' , true )));
  }

    if ( count( $users_who_like ) == 0 ) {
    // if no user likes this.

    } elseif ( count( $users_who_like ) == 1 ) {
      // If only one person likes the current item.

      if ( get_current_user_id() == $users_who_like[0] ) {
        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $bp_like_id;
        $string .= '"><small>';
        $string .= bp_like_get_text( 'get_likes_only_liker' );
        $string .= '</small></p>';

        print($string);

      } else {

        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $bp_like_id;
        $string .= '"><small>';
        $string .= bp_like_get_text( 'one_likes_this' );
        $string .= '</small></p>';
        //$string .= '"><small>%s likes this.</small></p>';

        $one = bp_core_get_userlink( $users_who_like[0] );

        printf( $string , $one );
      }
    } elseif ( count( $users_who_like ) == 2 ) {
        // If two people like the current item.

        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $bp_like_id;
        $string .= '"><small>';
        $string .= bp_like_get_text( 'two_like_this' );
        $string .= '</small></p>';

        $one = bp_core_get_userlink( $users_who_like[0] );
        $two = bp_core_get_userlink( $users_who_like[1] );

        printf( $string , $one , $two );

    } elseif ( count ($users_who_like) > 2 ) {

        $others = count ($users_who_like);

        // output last two people to like (2 at end of array)
        $one = bp_core_get_userlink( $users_who_like[$others - 1] );
        $two = bp_core_get_userlink( $users_who_like[$others - 2] );

        // $users_who_like will always be greater than 2 in here
        if ( $users_who_like == 3 ) {
            $others = $others - 1;
        } else {
            $others = $others - 2;
        }

        $string = '<p class="users-who-like" id="users-who-like-';
        $string .= $bp_like_id;
        $string .= '"><small>%s, %s and %d ' . _n( 'other', 'others', $others ) . ' like this.</small></p>';

        printf( $string , $one , $two , $others );
    }
}

/**
 *
 * view_who_likes() hook
 * TODO explain better
 *
 */
function view_who_likes( $type = "" ) {

    do_action( 'bp_like_before_view_who_likes' );

    do_action( 'view_who_likes', $type);

    do_action( 'bp_like_after_view_who_likes' );

}

// TODO comment why this is here
add_action( 'view_who_likes' , 'bp_like_get_some_likes', 10, 1 );
