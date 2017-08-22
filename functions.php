<?php


/* Main includes */

include_once('includes/admin-functions.php');
include_once('includes/public-functions.php');

/* enqueu scripts */

function custom_scripts() {

wp_register_script('crypto-aes', '//cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js');
wp_enqueue_script('crypto-aes');

wp_register_script('ticket-js', get_stylesheet_directory_uri() . '/assets/js/ticket-js.js', array('jquery', 'crypto-aes'));

    $local_arr = array(
    	'key' => 'dc47888ec4ef8d8ff3620065405e2be30a488585e9eb2664d0827d50bec1a596',
        'security'  => wp_create_nonce( 'CptUFfjS7xhy4JdQ' )
    );

// Assign that data to our script as an JS object
wp_localize_script( 'ticket-js', 'specialObj', $local_arr );

wp_enqueue_script('ticket-js');



}
add_action( 'wp_enqueue_scripts', 'custom_scripts' );


