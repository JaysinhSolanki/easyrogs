<?php

// Remove HTML comment
if ( !(defined('WP_DEBUG') && WP_DEBUG) ) {
	add_filter( 'w3tc_can_print_comment', '__return_false' );
}
