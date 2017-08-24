<?php 

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


/* 
  save custom ticket quantity,
  check if it exits,
  check if the post ticket quantity is not the same as the meta,
  display error else update and save quantity.
*/

/* error display notice if above fails */

function ticket_already_set() {
            ?>
            <div class="error notice is-dismissible">
                <p><?php _e( 'Once ticket quantity has been set it can not be change please delete the product and start again', 'my_plugin_textdomain' ); ?></p>
                <button type="button" class="notice-dismiss">
                  <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
            <?php
        }

add_action( 'woocommerce_product_options_general_product_data', 'wc_custom_add_custom_fields' );

function wc_custom_add_custom_fields() {
    // Print a custom text field
    woocommerce_wp_text_input( array(
        'id' => '_ticket_quantity',
        'type' => 'number',
        'label' => 'Ticket Quantity',
        'description' => 'Once set can not be changed, to change please delete and re add',
        'desc_tip' => 'true',
        'placeholder' => 'Max quantity 100'
    ) );
}

function wc_custom_save_custom_fields( $post_id ) {

    $exits = get_post_meta($post_id, '_ticket_set');
    $ticket_q = get_post_meta($post_id, '_ticket_quantity');
    $new_q = $_POST['_ticket_quantity'];

    if($exits[0] == "ticket_set"){
      if($ticket_q[0] != $new_q ){
          add_action( 'admin_notices', 'ticket_already_set' );
      }

    }else{

       if ( ! empty( $_POST['_ticket_quantity'] ) ) {
        update_post_meta($post_id, '_ticket_set', 'ticket_set');
        update_post_meta( $post_id, '_ticket_quantity', esc_attr( $_POST['_ticket_quantity'] ) );
        save_tickets_savePost($post_id);
       }
       
    }
   
}

add_action( 'woocommerce_process_product_meta', 'wc_custom_save_custom_fields' );


/* Set ticket quantity ran if wc_custom_save_custom_fields does not fail */

function save_tickets_savePost($postID) {
        global $wpdb;

        $ticket_q = get_post_meta( $postID, $key = '_ticket_quantity', $single = false);

        for ($i = 1; $i <= $ticket_q[0]; $i++){
            $table_name = $wpdb->prefix . "tickets";
            $wpdb->insert( $table_name, 
              array( 
                 'product_id'     => $postID,
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




add_action( 'delete_post', 'delete_tickets', 10 );

function delete_tickets( $pid ) {
    global $wpdb;
    if ( $wpdb->get_var( $wpdb->prepare( 'SELECT product_id FROM wp_tickets WHERE product_id = %d', $pid ) ) ) {
        $wpdb->query( $wpdb->prepare( 'DELETE FROM wp_tickets WHERE product_id = %d', $pid ) );
    }
}


/* add custom meta to admin orders from cart-override-functions */
add_action('woocommerce_before_order_itemmeta','woocommerce_before_order_itemmeta',10,3);

function woocommerce_before_order_itemmeta($item_id, $item, $product){
   $item_data = $item->get_data();

   $string .= " <strong>Ticket Numbers: ";
   foreach ($item_data['meta_data'][1]->value as $ticket => $value) {
     $string .= $value . '-';
    }
    $string .= "</strong>";

    echo $string;
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