<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/* needed for auto redirect back to merchance http://yourdomain.com/checkout/order-received/?utm_nooverride=1 */

/* need to take away the quanity from tickets left meta */

function raffle_woocommerce_order_status_completed( $order_id ) {
  global $wpdb;
  $order = wc_get_order( $order_id );
  $table_name = $wpdb->prefix . 'tickets';
  $items = $order->get_items();
  foreach ($order->get_items() as $item_key => $item_values) {
  	$item_id = $item_values->get_id();
    $item_name = $item_values->get_name(); // Name of the product
    $item_type = $item_values->get_type(); // Type of the order item ("line_item")
    $product_id = $item_values->get_product_id(); // the Product id
    $wc_product = $item_values->get_product(); 
    $item_data = $item_values->get_data();

  	$ticket_numbers = $item_data['meta_data'][1]->value;
    $count = (int)count($ticket_numbers);
    $tickets_left = (int)get_post_meta( $product_id, $key = '_ticket_quantity', $single = false );
    $newQuantity = $tickets_left - $count;

    update_post_meta($product_id, '_ticket_quantity', $newQuantity);
    foreach ($ticket_numbers as $ticket => $value) {
      $value = (int)$value;
      $wpdb->get_results("UPDATE {$table_name} SET ticket_status = 'sold' WHERE product_id = {$product_id} AND ticket_number = {$value}");
    }

  }

}

add_filter( 'woocommerce_email_order_meta_fields', 'custom_woocommerce_email_order_meta_fields', 10, 3 );

function custom_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
    
    foreach ($order->get_items() as $item_key => $item_values) {
      $item_data = $item_values->get_data();
      $ticket_numbers = $item_data['meta_data'][1]->value;
      foreach ($ticket_numbers as $ticket => $value) {
          $values[] = $value;
      }

     $result = implode('-', $values);
     $fields['ticket_numbers'] = array(
            'label' => __( 'Ticket Numbers ' ),
            'value' => $result,
        );
  }
    return $fields;
}