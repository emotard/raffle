<?php 


/** * @desc Remove quantity drop down */ 

function woo_remove_all_quantity_fields( $return, $product ) { 

  return true; 

} 

add_filter( 'woocommerce_is_sold_individually', 'woo_remove_all_quantity_fields', 10, 2 );


/* Set ticket quantity */

add_action('save_post_product', 'save_tickets_savePost', 10, 3);

function save_tickets_savePost($postID, $post, $update) {
    if (!$update) {
        update_post_meta($postID, '_ticket_quantity', '50');

        $ticket_q = get_post_meta( $postID, $key = '_ticket_quantity', $single = false);

        for ($i = 1; $i <= $ticket_q[0]; $i++){
            global $wpdb;
            $table_name = $wpdb->prefix . "tickets";
            $wpdb->insert( $table_name, 
              array( 
                  'product_id'     => $post->ID,
                  'ticket_number' => $i,
                  'ticket_status'  => 'not_sold'
                ), 
                array( 
                  '%s',
                  '%s',
                  '%s',
                ) 
            );
        }
    } 
}



add_action( 'delete_post', 'delete_tickets', 10 );

function delete_tickets( $pid ) {
    global $wpdb;
    if ( $wpdb->get_var( $wpdb->prepare( 'SELECT product_id FROM wp_tickets WHERE product_id = %d', $pid ) ) ) {
        $wpdb->query( $wpdb->prepare( 'DELETE FROM wp_tickets WHERE product_id = %d', $pid ) );
    }
}




/* unset product data which is not used */ 


add_filter( 'product_type_selector', 'remove_product_types' );

function remove_product_types( $types ){
    unset( $types['grouped'] );
    unset( $types['external'] );
    unset( $types['variable']);
    return $types;
}

/* unset product data which is not used */ 

add_filter( 'woocommerce_product_data_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

   unset($tabs['attribute']);
   unset($tabs['linked_product']);
   unset($tabs['advanced']);

  return $tabs;

}