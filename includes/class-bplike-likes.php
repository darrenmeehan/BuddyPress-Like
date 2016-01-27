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
	public $liker_id;

	/**
	 * Date the like was created.
	 *
	 * @var string
	 */
	public $date_created;

	/**
	 * Item ID of the item being liked.
	 *
	 * @var int
	 */
	public $item_id;

	/**
	 * Type of the item being liked.
	 *
	 * @var string
	 */
	public $like_type;


  /**
	 * Constructor method.
	 *
	 * @param int  $id                      Optional. The ID of an existing like.
	 */
	public function __construct( $id = null ) {

		if ( !empty( $id ) ) {
			$this->id = $id;
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
			$this->liker_id  	= (int) $like->liker_id;
			$this->item_id 		= (int) $like->item_id;
			$this->like_type 	= $like->like_type;
			$this->date_created = $like->date_created;
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

    $this->liker_id  	  = apply_filters( 'likes_like_liker_id_before_save',  		$this->liker_id,  	 $this->id );
	$this->item_id		  = apply_filters( 'likes_like_item_id_before_save', 		$this->item_id, 	 $this->id );
	$this->like_type 	  = apply_filters( 'likes_like_like_type_before_save',		$this->like_type, 	 $this->id );
    $this->date_created   = apply_filters( 'likes_like_date_created_before_save',   $this->date_created, $this->id );

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
      $result = $wpdb->query( $wpdb->prepare( "UPDATE {$bp->likes->table_name} SET liker_id = %d, item_id = %d, like_type = %s, date_created = %s WHERE id = %d", $this->liker_id, $this->item_id, $this->like_type, $this->date_created, $this->id ) );

    // Save.
    } else {
      $result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->likes->table_name} ( liker_id, item_id, like_type, date_created ) VALUES ( %d, %d, %s, %s )", $this->liker_id, $this->item_id, $this->like_type, $this->date_created ) );
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

	public static function get_user_like($item_id, $type, $user_id) {
		global $wpdb, $bp;

		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->likes->table_name} WHERE item_id = %d AND liker_id = %d AND like_type = %s", $item_id, $user_id, $type ) ) ) {

			$like = new BPLIKE_LIKES();
			$like->id  			= (int) $row->id;
			$like->liker_id  	= (int) $row->liker_id;
			$like->item_id 		= (int) $row->item_id;
			$like->like_type 	= $row->like_type;
			$like->date_created = $row->date_created;
			return $like;
		}
		return false;
	}

	public static function item_is_liked($item_id, $type, $user_id) {
		global $wpdb, $bp;

		if ( BPLIKE_LIKES::get_user_like($item_id, $type, $user_id) ) {
			return true;
		}
		return false;
	}

	public static function get_likers($item_id, $type) {
		global $wpdb, $bp;

		return $wpdb->get_col( $wpdb->prepare( "SELECT liker_id FROM {$bp->likes->table_name} WHERE item_id = %d AND like_type = %s", $item_id, $type ) );
	}
}
