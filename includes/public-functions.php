<?php 

/* enable the use of ajaxurl as a variable fuck fuck fuck */


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




