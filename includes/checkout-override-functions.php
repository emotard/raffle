<?php

/* need to take away the quanity from tickets left meta */

function mysite_woocommerce_order_status_completed( $order_id ) {
  $order = wc_get_order( $order_id );

  $items = $order->get_items();
  $order_meta = get_post_meta($order_id);
  var_dump($order_meta);
  foreach ($items as $item) {
  	$_product = $item;
	  $product_name = $item['name'];
    $product_id = $item['product_id'];
  	echo '<pre>' . var_export($_product, true) . '</pre>';
  }

}
add_action( 'woocommerce_thankyou', 'mysite_woocommerce_order_status_completed', 10, 1 );

