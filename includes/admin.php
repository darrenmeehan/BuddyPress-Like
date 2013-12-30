<?php


/**
 * bp_like_add_admin_page_menu()
 *
 * Adds "BuddyPress Like" to the main BuddyPress admin menu.
 *
 */
function bp_like_add_admin_page_menu() {
    add_submenu_page(
    	'bp-general-settings',
    	'BuddyPress Like',
    	'BuddyPress Like',
    	'manage_options',
    	'bp-like-settings',
    	'bp_like_admin_page'
    );
}
add_action( 'admin_menu', 'bp_like_add_admin_page_menu' );

/**
 * bp_like_admin_page_verify_nonce()
 *
 * When the settings form is submitted, verifies the nonce to ensure security.
 *
 */
function bp_like_admin_page_verify_nonce() {
	if( isset( $_POST['_wpnonce'] ) && isset( $_POST['bp_like_updated'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce( $nonce, 'bp-like-admin' ) )
			wp_die( __('You do not have permission to do that.') );
	}
}
add_action( 'init', 'bp_like_admin_page_verify_nonce' );

/**
 * bp_like_admin_page()
 *
 * Outputs the admin settings page.
 *
 */
function bp_like_admin_page() {
	global $current_user;

	wp_get_current_user(); //doesnt seem to be doing anything

	/* Update our options if the form has been submitted */
    if( isset( $_POST['_wpnonce'] ) && isset( $_POST['bp_like_updated'] ) ) {
		
		/* Add each text string to the $strings_to_save array */
		foreach ( $_POST as $key => $value ) {
			if ( preg_match( "/text_string_/i", $key )) {
				$default = bp_like_get_text( str_replace( 'bp_like_admin_text_string_', '', $key), 'default' );
				$strings_to_save[str_replace( 'bp_like_admin_text_string_', '', $key )] = array('default' => $default, 'custom' => stripslashes( $value ));
			}
		}
		
		/* Now actually save the data to the options table */
		update_site_option(
			'bp_like_settings',
			array(
				'likers_visibility' => $_POST['bp_like_admin_likers_visibility'],
				'post_to_activity_stream' => $_POST['bp_like_admin_post_to_activity_stream'],
				'show_excerpt' => $_POST['bp_like_admin_show_excerpt'], 
				'excerpt_length' => $_POST['bp_like_admin_excerpt_length'], 
				'text_strings' => $strings_to_save,
				'translate_nag' => bp_like_get_settings( 'translate_nag' ),
				'name_or_avatar' => $_POST['name_or_avatar']
			)
		);
		
		/* Let the user know everything's cool */
		echo '<div class="updated"><p><strong>';
		_e('Settings saved.', 'wordpress');
		echo '</strong></p></div>';
	}
	
	$text_strings = bp_like_get_settings( 'text_strings' );

?>
<style type="text/css">
#icon-bp-like-settings { background: url('<?php echo plugins_url('/img/bp-like-icon32.png', __FILE__); ?>') no-repeat top left; }
table input { width: 100%; }
table label { display: block; }
</style>
<script type="text/javascript">
jQuery(document).ready( function() {
	jQuery('select.name-or-avatar').change(function(){
		var value = jQuery(this).val();
		jQuery('select.name-or-avatar').val(value);
	});
});
</script>

<div class="wrap">
  <div id="icon-bp-like-settings" class="icon32"><br /></div>
  <h2><?php _e('BuddyPress Like Settings', 'buddypress-like'); ?></h2>
  <form action="" method="post" id="bp-like-admin-form">
    <input type="hidden" name="bp_like_updated" value="updated">


    <h3><?php _e('General Settings', 'buddypress-like'); ?></h3>
    <p><input type="checkbox" id="bp_like_admin_post_to_activity_stream" name="bp_like_admin_post_to_activity_stream" value="1"<?php if (bp_like_get_settings( 'post_to_activity_stream' ) == 1) echo ' checked="checked"'?>> <label for="bp_like_admin_post_activity_updates"><?php _e("Post an activity update when something is liked", 'buddypress-like'); ?>, (e.g. "<?php echo $current_user->display_name; ?> likes Bob's activity")</label></p>
    <p><input type="checkbox" id="bp_like_admin_show_excerpt" name="bp_like_admin_show_excerpt" value="1"<?php if (bp_like_get_settings( 'show_excerpt' ) == 1) echo ' checked="checked"'?>> <label for="bp_like_admin_show_excerpt"><?php _e("Show a short excerpt of the activity that has been liked", 'buddypress-like'); ?></label>; limit to <input type="text" maxlength="3" style="width: 40px" value="<?php echo bp_like_get_settings( 'excerpt_length' ); ?>" name="bp_like_admin_excerpt_length" /> characters.</p>
    
    <h3><?php _e("'View Likes' Visibility", "buddypress-like"); ?></h3>
    <p><?php _e("Choose how much information about the 'likers' of a particular item is shown;", "buddypress-like"); ?></p>
    <p style="line-height: 200%;">
      <input type="radio" name="bp_like_admin_likers_visibility" value="show_all"<?php if ( bp_like_get_settings( 'likers_visibility' ) == 'show_all' ) { echo ' checked="checked""'; }; ?> /> Show <select name="name_or_avatar" class="name-or-avatar"><option value="name"<?php if ( bp_like_get_settings( 'name_or_avatar' ) == 'name' ) { echo ' selected="selected""'; }; ?>>names</option><option value="avatar"<?php if ( bp_like_get_settings( 'name_or_avatar' ) == 'avatar' ) { echo ' selected="selected""'; }; ?>>avatars</option></select> of all likers<br />
      <?php if ( bp_is_active( 'friends' ) ) { ?>
      <input type="radio" name="bp_like_admin_likers_visibility" value="friends_names_others_numbers"<?php if ( bp_like_get_settings( 'likers_visibility' ) == 'friends_names_others_numbers' ) { echo ' checked="checked""'; }; ?> /> Show <select name="name_or_avatar" class="name-or-avatar"><option value="name"<?php if ( bp_like_get_settings( 'name_or_avatar' ) == 'name' ) { echo ' selected="selected""'; }; ?>>names</option><option value="avatar"<?php if ( bp_like_get_settings( 'name_or_avatar' ) == 'avatar' ) { echo ' selected="selected""'; }; ?>>avatars</option></select> of friends, and the number of non-friends<br />
      <?php }; ?>
      <input type="radio" name="bp_like_admin_likers_visibility" value="just_numbers"<?php if ( bp_like_get_settings( 'likers_visibility' ) == 'just_numbers' ) { echo ' checked="checked""'; }; ?> /> <?php _e('Show only the number of likers', 'buddypress-like'); ?>
    </p>
    <h3><?php _e('Custom Messages', 'buddypress-like'); ?></h3>
    <p><?php _e("Change what messages are shown to users. For example, they can 'love' or 'dig' items instead of liking them.", "buddypress-like"); ?><br /><br /></p>
    
    <table class="widefat fixed" cellspacing="0">
	  <thead>
	    <tr>
	      <th scope="col" id="default" class="column-name" style="width: 43%;"><?php _e('Default', 'buddypress-like'); ?></th>
	      <th scope="col" id="custom" class="column-name" style=""><?php _e('Custom', 'buddypress-like'); ?></th>
	    </tr>
	  </thead>
	  <tfoot>
	    <tr>
	      <th colspan="2" id="default" class="column-name"></th>
	    </tr>
	  </tfoot>

      <?php foreach ( $text_strings as $key => $string ) : ?>
      <tr valign="top">
          <th scope="row" style="width:400px;"><label for="bp_like_admin_text_string_<?php echo $key; ?>"><?php echo htmlspecialchars( $string['default'] ); ?></label></th>
          <td><input name="bp_like_admin_text_string_<?php echo $key; ?>" id="bp_like_admin_text_string_<?php echo $key; ?>" value="<?php echo htmlspecialchars( $string['custom'] ); ?>" class="regular-text" type="text"></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
	
    <p class="submit">
      <input class="button-primary" type="submit" name="bp-like-admin-submit" id="bp-like-admin-submit" value="<?php _e('Save Changes', 'wordpress'); ?>"/>
    </p>
    <?php wp_nonce_field( 'bp-like-admin' ) ?>
  </form>
</div>
<?php
}