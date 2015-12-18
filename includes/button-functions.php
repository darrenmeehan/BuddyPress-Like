<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Filters to display BuddyPress Like button.
add_action( 'bp_activity_entry_meta' , 'bplike_activity_update_button' );
add_action( 'bp_before_blog_single_post' , 'bplike_blog_post_button'  );
add_action( 'bp_activity_comment_options' , 'bplike_activity_comment_button' );
