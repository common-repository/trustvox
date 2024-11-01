<?php 

/**
 * Pedido
 */

function trustvox_order_create( $order_id ) {
	$order             = new WC_Order( $order_id );
	$client            = $order->get_user();
	$items_order       = $order->get_items();
	$trustvox_options  = get_option('trustvox');
	$items = array();
	$trustvox = $items;
//	$trustvox = $items = [];

	foreach ($items_order as $item) {
		// $item_arr = [];
		$item_arr = array();

		$item_arr['id'] = $item['product_id'];
		$item_arr['name'] = $item['name'];
		$item_arr['price'] = floatval($item['line_total']);

		$item_arr['url'] = get_permalink($item['product_id']);

		if(has_post_thumbnail($item['product_id']))
			$item_arr['photos_urls'] = get_the_post_thumbnail_url($item['product_id']);

		$items[] = $item_arr;
	}

	$trustvox['order_id']             = $order_id;
	$trustvox['delivery_date']        = date('Y-m-d\TH:i:sP');
	$trustvox['client']['first_name'] = !empty($client->first_name) ? $client->first_name : $client->user_login ;
	$trustvox['client']['last_name']  = $client->last_name;
	$trustvox['client']['email']      = $client->user_email;
	$trustvox['items']                = $items;

	$headers = array( 
		"Accept: application/vnd.trustvox.com; version=1",
		"Content-Type: application/json",
		"Authorization: token ".$trustvox_options['loja_token']
	); 

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://trustvox.com.br/api/stores/".$trustvox_options['loja_ID']."/orders");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($trustvox));

	if(curl_exec($ch)){
		$result = curl_getinfo($ch);
		$return = "OK\t".$result['http_code'];
	} else {
		$return = "ORR\t".curl_errno($ch);
	}

	curl_close($ch);

	$log = "[".date('Y-m-d\TH:i:sP')."]\t".$return."\t".json_encode($trustvox)."\r\n";

	error_log($log, 3, get_home_path()."wp-content/uploads/wc-logs/trustvox.log");
}
add_action( 'woocommerce_order_status_completed', 'trustvox_order_create' );
