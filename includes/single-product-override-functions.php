<?php 

/* enable the use of ajaxurl as a variable */


add_action('wp_head', 'myplugin_ajaxurl');

function myplugin_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}

/* change price to add per ticket on the end */

add_filter( 'woocommerce_get_price_html', 'sv_change_product_price_display' );
add_filter( 'woocommerce_cart_item_price', 'sv_change_product_price_display' );

function sv_change_product_price_display( $price ) {
	$price = $price .' per ticket';
	return $price;
}

/* set amount of tickets left on single product page */

add_action( 'woocommerce_single_product_summary', 'tickets_left', 5 );

function tickets_left(){
	global $product;

	$id = $product->id;

	$tickets_left = get_post_meta( $id, $key = '_ticket_quantity', $single = false );

	echo 'Tickets Left ' . $tickets_left[0];
}

/* Display tickets from db  */

add_action( 'woocommerce_single_product_summary', 'display_tickets', 10 );


function display_tickets(){
  global $product, $wpdb;

  $id = $product->id;
  $_product = wc_get_product($id);
  $price = $_product->get_regular_price();

  $results = $wpdb->get_results( 
    $wpdb->prepare("
        SELECT * FROM wp_tickets
        WHERE product_id = %s
        ", 
        $id
      ) 
    );

  require('template-parts/ticket-loop.php');
}




/* modify price based on session not needed not that quantity is set */
/*
add_filter( 'woocommerce_before_calculate_totals', 'sv_change_product_price_cart', 10, 1 );
function sv_change_product_price_cart( $cart_object ) {
     global $woocommerce;
     $key = pack("H*", "dc47888ec4ef8d8ff3620065405e2be30a488585e9eb2664d0827d50bec1a596");
     $iv =  pack("H*", "101112131415161718191a1b1c1d1e1f");
     $encrypted = $_SESSION['key'];
     $count = $_SESSION['count'];
     $decrypt_string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv); 
     $price = number_format((float)$decrypt_string, 2, '.', '');

    foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
        if (isset($price)) {
          $price = $price;
          // WooCommerce versions compatibility
            if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
                $cart_item['data']->price = $price; // Before WC 3.0
            } else {
                $cart_item['data']->set_price( $price ); // WC 3.0+
                
            }
        }
    }

    //session_destroy();
}
*/




