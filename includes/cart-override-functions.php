<?php 

/* Check if the product is already in the cart */ 

function add_the_date_validation( $passed, $product_id ) { 

global $woocommerce, $product;
      foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
          $_product = $values['data']; 
          if( $product_id == $_product->id ) {
            wc_add_notice( __( 'This product is already in your basket to change your numbers please remove and add again.', 'woocommerce' ), 'error' );
            $passed = false;
          }
        
    }

return $passed;
}
add_action( 'woocommerce_add_to_cart_validation', 'add_the_date_validation', 10, 5 ); 


/* set session price with ajax on ticket click */

add_action( 'wp_ajax_change_product_price_cart', 'change_product_price_cart' );
add_action( 'wp_ajax_nopriv_change_product_price_cart', 'change_product_price_cart' );

function change_product_price_cart() {

  check_ajax_referer( 'CptUFfjS7xhy4JdQ', 'security' );

  session_start();
  $encrypted = base64_decode($_REQUEST['custom']);
 
  $_SESSION['key'] = $encrypted;
  $_SESSION['count'] = $_REQUEST['count'];
  $_SESSION['ticket_numbers'] = $_REQUEST['ticket_numbers'];

  wp_die();
}

/* Add out session data into the woocommerce session data and then unset it at the end */

add_filter('woocommerce_add_cart_item_data','wdm_add_item_data',1,2);
 
if(!function_exists('wdm_add_item_data'))
{
    function wdm_add_item_data($cart_item_data,$product_id)
    {
        /*Here, We are adding item in WooCommerce session with, wdm_user_custom_data_value name*/
        global $woocommerce;
        session_start();    
        if (isset($_SESSION['ticket_numbers'])) {
            $option = $_SESSION['ticket_numbers'];       
            $new_value = array('ticket_numbers' => $option);
        }
        if(empty($option))
            return $cart_item_data;
        else
        {    
            if(empty($cart_item_data))
                return $new_value;
            else
                return array_merge($cart_item_data,$new_value);
        }
        unset($_SESSION['ticket_numbers']); 
        //Unset our custom session variable, as it is no longer needed.
    }
}

/* get the cart item from the sessions and set out ticket numbers to that item */

add_filter('woocommerce_get_cart_item_from_session', 'wdm_get_cart_items_from_session', 1, 3 );
if(!function_exists('wdm_get_cart_items_from_session'))
{
    function wdm_get_cart_items_from_session($item,$values,$key)
    {
        if (array_key_exists( 'ticket_numbers', $values ) )
        {
        $item['ticket_numbers'] = $values['ticket_numbers'];
        }       
        return $item;
    }
}

 /*code to add custom data on Cart & checkout Page*/   

add_filter('woocommerce_checkout_cart_item_quantity','wdm_add_user_custom_option_from_session_into_cart',1,3);  
add_filter('woocommerce_widget_cart_item_quantity','wdm_add_user_custom_option_from_session_into_cart',1,3);
if(!function_exists('wdm_add_user_custom_option_from_session_into_cart'))
{
 function wdm_add_user_custom_option_from_session_into_cart($product_name, $values, $cart_item_key )
    {
        /*code to add custom data on Cart & checkout Page*/    
        if(count($values['ticket_numbers']) > 0)
        {
            $return_string = $product_name . "</a><dl class='variation'>";
            $return_string .= "<table class='wdm_options_table' id='" . $values['product_id'] . "'>";
            $return_string .= "<tr><td>";
            $return_string .= "Ticket Numbets: <br>";
                foreach ($values['ticket_numbers'] as $ticket) {
                  $return_string .=  $ticket . '-';
                }
            $return_string .="</td></tr>";
            $return_string .= "</table>"; 
            return $return_string;
        }
        else
        {
            return $product_name;
        }
    }
}

/* add custom meta data to the order item meta */

add_action('woocommerce_add_order_item_meta','wdm_add_values_to_order_item_meta',1,2);
if(!function_exists('wdm_add_values_to_order_item_meta'))
{
  function wdm_add_values_to_order_item_meta($item_id, $values)
  {
        global $woocommerce,$wpdb;
        $user_custom_values = $values['ticket_numbers'];
        if(!empty($user_custom_values))
        {
            wc_add_order_item_meta($item_id,'ticket_numbers',$user_custom_values);  
        }
  }
}

/* once product has been removed from cart unset all out custom values */

add_action('woocommerce_before_cart_item_quantity_zero','wdm_remove_user_custom_data_options_from_cart',1,1);
if(!function_exists('wdm_remove_user_custom_data_options_from_cart'))
{
    function wdm_remove_user_custom_data_options_from_cart($cart_item_key)
    {
        global $woocommerce;
        // Get cart
        $cart = $woocommerce->cart->get_cart();
        // For each item in cart, if item is upsell of deleted product, delete it
        foreach( $cart as $key => $values)
        {
        if ( $values['ticket_numbers'] == $cart_item_key )
            unset( $woocommerce->cart->cart_contents[ $key ] );
        }
    }
}