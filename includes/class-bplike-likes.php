<?php
/**
 * BuddyPress Like Main Class.

 * @todo good place to add caching
 * @todo need to look at best way to handle a lot of likes on a post
 * @todo implement more static methods
 * @package BuddyPressLike
 * @subpackage LikesClasses
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BuddyPress Like object.
 */
class BPLIKE_LIKES {

	/**
	 * ID of the like.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * User ID of the item liker.
	 * @todo possibly change this to an array, so we dont have to create multiple objects for one item
	 * @var int
	 */
	public $liker_user_id;

	/**
	 * User ID of the 'poster' - the user who created the item being liked.
	 *
	 * @var int
	 */
	public $poster_user_id;

	/**
	 * Date the like was created.
	 *
	 * @var string
	 */
	public $date_created;


  /**
	 * Constructor method.
	 *
	 * @param int  $id                      Optional. The ID of an existing like.
	 */
	public function __construct( $id = null ) {

		if ( !empty( $id ) ) {
			$this->id                      = $id;
			$this->populate( $this->id );
		}
	}

  /**
	 * Set up data about the current like.
   * @todo do I change $bp->likes to use constant?
	 */
	public function populate() {
		global $wpdb;

		$bp = buddypress();

		if ( $like = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->likes->table_name} WHERE id = %d", $this->id ) ) ) {
			$this->liker_user_id  = (int) $like->liker_user_id;
			$this->poster_user_id = (int) $like->poster_user_id;
			$this->date_created   = $like->date_created;
		}
	}


  /**
   * Save the current like to the database.
   *
   * @return bool True on success, false on failure.
   */
  public function save() {
    global $wpdb;

    $bp = buddypress();

    $this->liker_user_id  = apply_filters( 'likes_like_liker_user_id_before_save',  $this->liker_user_id,  $this->id );
    $this->poster_user_id = apply_filters( 'likes_like_poster_user_id_before_save', $this->poster_user_id, $this->id );
    $this->date_created   = apply_filters( 'likes_like_date_created_before_save',   $this->date_created,   $this->id );

    /**
     * Fires before processing and saving the current like.
     *
     * @since 0.4
     *
     * @param Object $value Current like object.
     */
    do_action_ref_array( 'likes_like_before_save', array( &$this ) );

    // Update.
    if (! empty( $this->id ) ) {
      $result = $wpdb->query( $wpdb->prepare( "UPDATE {$bp->likes->table_name} SET liker_user_id = %d, poster_user_id = %d, date_created = %s WHERE id = %d", $this->liker_user_id, $this->poster_user_id, $this->date_created, $this->id ) );

    // Save.
    } else {
      $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->friends->table_name} ( liker_user_id, poster_user_id, date_created ) VALUES ( %d, %d, %s )", $this->liker_user_id, $this->poster_user_id, $this->date_created ) );
      $this->id = $wpdb->insert_id;
    }

    /**
     * Fires after processing and saving the current like.
     *
     * @since 0.4
     *
     * @param Object $value Current like object.
     */
    do_action( 'likes_like_after_save', array( &$this ) );

    return $result;
  }

  /**
   * Delete the current like from the database.
   * @return bool|int
   */
  public function delete() {
    global $wpdb;

    $bp = buddypress();

    return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->likes->table_name} WHERE id = %d", $this->id ) );
  }

  /**
	 * Get liked count for a given user.
	 *
	 * @since 0.4

   * @todo need to make distintion on a users likes/liked items.
   * @todo is a users likes their posts that have been liked by other users?
	 * - And then is it total num of posts liked? Or total likes across their posts?
   * @todo and then we need to organise the posts a user likes themselves.
   * @todo this is only for activity items for now..
	 * @param int $user_id The ID of the user whose likes you're counting.
	 * @return int $value A count of the user's likes.
	 */
	public static function total_liked_count( $user_id ) {

		// Get activities from user meta.
		$liked_activity_entries = bp_get_user_meta( $user_id, 'bp_liked_activities', true );
		if ( ! empty( $liked_activity_entries ) ) {
			return count( maybe_unserialize( $liked_activity_entries ) );
		}

		// No likes.
		return 0;
	}



}
