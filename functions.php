<?php


/* Main includes */

include_once('includes/admin-functions.php');
include_once('includes/public-functions.php');

/* enqueu scripts */

function custom_scripts() {

wp_register_script('ticket-js', get_stylesheet_directory_uri() . '/assets/js/main-js.js', array('jquery'));
wp_enqueue_script('ticket-js');

}
 
add_action( 'wp_enqueue_scripts', 'custom_scripts' );


