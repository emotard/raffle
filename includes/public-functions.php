<?php 


/* enable the use of ajaxurl as a variable */


add_action('wp_head', 'myplugin_ajaxurl');

function myplugin_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}

/* change price to add per ticket on */

add_filter( 'woocommerce_get_price_html', 'sv_change_product_price_display' );
add_filter( 'woocommerce_cart_item_price', 'sv_change_product_price_display' );

function sv_change_product_price_display( $price ) {
	$price .= ' per ticket';
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

/* set amount of tickets left on single product page */

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


add_action( 'wp_ajax_change_product_price_cart', 'change_product_price_cart' );
add_action( 'wp_ajax_nopriv_change_product_price_cart', 'change_product_price_cart' );

function change_product_price_cart( $cart_object ) {

  

    wp_die();
}

add_filter( 'woocommerce_before_calculate_totals', 'change_product_price_cart', 10, 1 );

/* need to take away the quanity from tickets left meta */

function mysite_woocommerce_order_status_completed( $order_id ) {
  $order = wc_get_order( $order_id );

  $items = $order->get_items();

  foreach ($items as $item) {
  	$_product = $item;
	  $product_name = $item['name'];
    $product_id = $item['product_id'];
  	echo '<pre>' . var_export($_product, true) . '</pre>';
  }

}
add_action( 'woocommerce_thankyou', 'mysite_woocommerce_order_status_completed', 10, 1 );




