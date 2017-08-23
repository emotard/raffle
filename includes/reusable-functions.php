<?php


function how_many_tickets_left(){

 	global $product;
 	$id = $product->id;

	$result = get_post_meta( $id, $key = '_ticket_quantity', $single = false );

	return $result;
}


function get_tickets_by_sql(){

  global $product, $wpdb;
  $id = $product->id;

  $results = $wpdb->get_results( 
    $wpdb->prepare("
        SELECT * FROM wp_tickets
        WHERE product_id = %s
        ", 
        $id
      ) 
    );

  return $results;

}


function random_pick( $a, $n ) 
{
  $N = count($a);
  $n = min($n, $N);
  $picked = array_fill(0, $n, 0); $backup = array_fill(0, $n, 0);
  // partially shuffle the array, and generate unbiased selection simultaneously
  // this is a variation on fisher-yates-knuth shuffle
  for ($i=0; $i<$n; $i++) // O(n) times
  { 
    $selected = mt_rand( 0, --$N ); // unbiased sampling N * N-1 * N-2 * .. * N-n+1
    $value = $a[ $selected ];
    $a[ $selected ] = $a[ $N ];
    $a[ $N ] = $value;
    $backup[ $i ] = $selected;
    $picked[ $i ] = $value;
  }
  // restore partially shuffled input array from backup
  // optional step, if needed it can be ignored, e.g $a is passed by value, hence copied
  for ($i=$n-1; $i>=0; $i--) // O(n) times
  { 
    $selected = $backup[ $i ];
    $value = $a[ $N ];
    $a[ $N ] = $a[ $selected ];
    $a[ $selected ] = $value;
    $N++;
  }
  return $picked;
}