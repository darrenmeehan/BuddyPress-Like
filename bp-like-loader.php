<?php

/*
  Plugin Name: BuddyPress Like
  Plugin URI: http://darrenmeehan.me/
  Description: Adds the ability for users to like content throughout your BuddyPress site.
  Author: Darren Meehan
  Version: 0.3.0
  Author URI: http://darrenmeehan.me
  Text Domain: buddypress-like

  Credit: The original plugin was built by Alex Hempton-Smith who did a great job. I hope he's in good
  health and enjoying life.
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* Only load BuddyPress Like once BuddyPress has loaded and been initialized. */
function bplike_init() {
  // Because we will be using BP_Component, we require BuddyPress 1.5 or greater.
  if ( version_compare( BP_VERSION, '1.5', '>' ) ) {
    require_once( 'includes/bplike.php' );
    require_once( BPLIKE_PATH . 'includes/bplike-likes-loader.php' );

  }
}

add_action( 'bp_loaded' , 'bplike_init', 2 );

/**
 * Run the activation routine when BP-Like is activated.
 *
 * @uses dbDelta() Executes queries and performs selective upgrades on existing tables.
 */
function bp_like_activate() {
	global $bp, $wpdb;

	$charset_collate = !empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
	if ( !$table_prefix = $bp->table_prefix )
		$table_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );

	$sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}bplike_likes (
			id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			liker_id bigint(20) NOT NULL,
			item_id bigint(20) NOT NULL,
			date_created datetime NOT NULL,
			like_type varchar(20) NOT NULL,
		        KEY likers (item_id, liker_id)
		) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'bp_like_activate' );
