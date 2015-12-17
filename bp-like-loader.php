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
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/* Only load BuddyPress Like once BuddyPress has loaded and been initialized. */
function bplike_init() {
  // Because we will be using BP_Component, we require BuddyPress 1.5 or greater.
  if ( version_compare( BP_VERSION, '1.5', '>' ) ) {
    require_once 'includes/bplike.php';
  }
}

add_action( 'bp_include' , 'bplike_init' );
