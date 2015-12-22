<?php
/**
 * BuddyPress Like Component
 *
 * The likes component is for users to like updates, comments or posts.
 *
 * @package BuddyPressLike
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Defines the BuddyPress Like Component.
 */
class BPLIKE_Likes_Component extends BP_Component {

  /**
   * Start the likes component creation process.
   *
   * @since 0.4
   */
  public function __construct() {
    $this->includes();
    parent::start(
      'likes',
      _x( 'Likes', 'Likes screen page <title>', 'buddypress-like' ),
      buddypress()->plugin_dir,
      array(
        'adminbar_myaccount_order' => 60
      )
    );
  }

  /**
   * Include bplike files.
   *
   * @see BP_Component::includes() for description of parameters.
   *
   * @param array $includes See {@link BP_Component::includes()}
   *
   * @todo not using this yet, see if we actually need to
   */
  public function includes( $includes = array() ) {
    $includes = array(
  //    'cache',
  //    'actions',
      'screens',
  //    'filters',
  //    'classes',
  //    'activity',
      'template'  // already have templates
  //    'functions', // can start adding some useful functions for other devs
      //'notifications',
      //'widgets', //  add some widgets in a later release
    );

    parent::includes( $includes );
  }


  	/**
  	 * Set up bplike global settings.
  	 *
  	 * @since 0.4
  	 *
  	 * @see BP_Component::setup_globals() for description of parameters.
  	 *
  	 * @param array $args See {@link BP_Component::setup_globals()}.
  	 */
  	public function setup_globals( $args = array() ) {
  		$bp = buddypress();

  		// Define a slug, if necessary.
  		if ( ! defined( 'BPLIKE_LIKES_SLUG' ) ) {
  			define( 'BPLIKE_LIKES_SLUG', $this->id );
  		}

  		// Global tables for the friends component.
  		$global_tables = array(
  			'table_name'      => $bp->table_prefix . 'bplike_likes',
  			'table_name_meta' => $bp->table_prefix . 'bplike_likes_meta',
  		);

  		// All globals for the like component.
  		// Note that global_tables is included in this array.
  		$args = array(
              'slug'                  => BPLIKE_LIKES_SLUG,
              'root_slug'             => isset( $bp->pages->likes->slug ) ? $bp->pages->likes->slug : BPLIKE_LIKES_SLUG,
              'has_directory'         => true,
              'directory_title'       => _x( 'Likes', 'component directory title', 'buddypress-like' ),
              'notification_callback' => 'bplike_likes_format_notifications',
              'global_tables'         => $global_tables, // todo currently not used, either start using or curl_multi_remove_handle
              //  'meta_tables'           => $meta_tables,
       );

  		parent::setup_globals( $args );
  	}

    /**
     * Set up Likes component navigation.
     *
     * @since 0.4
     *
     * @see BP_Component::setup_nav() for a description of arguments.
     * @uses bp_is_active()
     * @uses is_user_logged_in()
     * @uses bplike_get_likes_slug()
     * @uses bplike_total_likes_for_user()
     *
     * @param array $main_nav Optional. See BP_Component::setup_nav() for description.
     * @param array $sub_nav  Optional. See BP_Component::setup_nav() for description.
     */
    public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

      // Stop if there is no user displayed or logged in.
      if ( ! is_user_logged_in() && ! bp_displayed_user_id() ) {
        return;
      }

      // Determine user to use.
      if ( bp_displayed_user_domain() ) {
        $user_domain = bp_displayed_user_domain();
      } elseif ( bp_loggedin_user_domain() ) {
        $user_domain = bp_loggedin_user_domain();
      } else {
        return;
      }

      $slug          = bplike_get_likes_slug();
      $likes_link = trailingslashit( $user_domain . $slug );

      $activity_slug = bp_get_activity_slug();
      $activity_link = trailingslashit( $user_domain . $activity_slug );

      // Only grab count if we're on a user page
  		if ( bp_is_user() ) {
  			$count    = bplike_total_likes_for_user();
  			$class    = ( 0 === $count ) ? 'no-count' : 'count';
  			$nav_name = sprintf( _x( 'Likes <span class="%s">%s</span>', 'Likes screen nav with counter', 'buddypress-like' ), esc_attr( $class ), bp_core_number_format( $count ) );
  		} else {
  			$nav_name = _x( 'Likes', 'Likes screen nav without counter', 'buddypress-like' );
  		}

      // Add 'Likes' to the main navigation.
      $main_nav = array(
        'name'                => $nav_name,
        'slug'                => $slug,
        'position'            => 10,
        'screen_function'     => 'bplike_screen_likes',
        'default_subnav_slug' => 'just-me',
        'item_css_id'         => $this->id
      );

      // Add the subnav items to the activity nav item if we are using a theme that supports this.
      $sub_nav[] = array(
        'name'            => _x( 'Likes', 'Profile activity screen sub nav', 'buddypress-like' ),
        'slug'            => 'likes',
        'parent_url'      => $activity_link,
        'parent_slug'     => 'activity',
        'screen_function' => 'bplike_activity_screen_likes',
        'position'        => 10,
        'item_css_id'     => 'activity-likes'
      );
            
      $sub_nav[] = array(
        'name'            => _x( 'Stats', 'Likes screen sub nav', 'buddypress-like' ),
        'slug'            => 'just-me',
        'parent_url'      => $likes_link,
        'parent_slug'     => $slug,
        'screen_function' => 'bplike_screen_likes',
        'position'        => 10,
         'item_css_id'    => 'your-likes'
      );
      
        // todo add one for blog posts etc, what makes sense
      parent::setup_nav( $main_nav, $sub_nav );
    }


    /**
     * Set up BuddyPress Like integration with the WordPress admin bar.
     *
     * @since 0.4
     *
     * @see BP_Component::setup_admin_bar() for a description of arguments.
     *
     * @param array $wp_admin_nav See BP_Component::setup_admin_bar()
     *                            for description.
     */
    public function setup_admin_bar( $wp_admin_nav = array() ) {

      // Menus for logged in user.
      if ( is_user_logged_in() ) {

        // Setup the logged in user variables.
        $likes_link = trailingslashit( bp_loggedin_user_domain() . bplike_get_likes_slug() );
        $title = _x( 'Likes', 'My Account Likes sub nav', 'buddypress-like' );

        // Add the "My Account" sub menus.
        $wp_admin_nav[] = array(
          'parent' => buddypress()->my_account_menu_id,
          'id'     => 'my-account-' . $this->id,
          'title'  => $title,
          'href'   => $likes_link
        );
        
         // Statistics
        $wp_admin_nav[] = array(
            'parent' => 'my-account-' . $this->id,
            'id'     => 'my-account-' . $this->id . '-stats',
            'title'  => _x( 'Statistics', 'My Account Likes sub nav', 'buddypress-like' ),
            'href'   => $likes_link
        );
      }

      parent::setup_admin_bar( $wp_admin_nav );
    }


  	/**
  	 * Set up the title for pages and <title>.
  	 *
  	 * @since 0.4
  	 *
  	 * @uses bplike_is_likes_component()
  	 * @uses bp_is_my_profile()
  	 * @uses bp_core_fetch_avatar()
     * @uses bp_get_displayed_user_fullname()
     * @uses bp_displayed_user_id()
  	 */
  	public function setup_title() {

  		// Adjust title based on view.
  		if ( bplike_is_likes_component() ) {
  			$bp = buddypress();

  			if ( bp_is_my_profile() ) {
  				$bp->bp_options_title = _x( 'My Likes', 'Page and <title>', 'buddypress-like' );
  			} else {
  				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
  					'item_id' => bp_displayed_user_id(),
  					'type'    => 'thumb',
  					'alt'	  => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_displayed_user_fullname() )
  				) );
  				$bp->bp_options_title  = bp_get_displayed_user_fullname();
  			}
  		}

  		parent::setup_title();
  	}
}

/**
 * Bootstrap the Likes component.
 */
function bplike_setup_likes() {
  buddypress()->likes = new BPLIKE_Likes_Component();
}
add_action( 'bp_setup_components', 'bplike_setup_likes', 6 );
