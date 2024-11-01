<?php 

/** 
 * Ativação do plugin
 */

// Envia os últimos 3 meses de pedidos após a instalação do plugin
function trustvox_inside_settings_page(){
	$currentPage = ( isset($_GET['page']) ) ? $_GET['page'] : false;

	if ( $currentPage == 'trustvox'){
		if( !get_option( 'trustvox_once', false )) {
			add_option( 'trustvox_once', true);

			$args = array(
				'post_type' => 'shop_order',
				'post_status' => 'wc-completed',
				'posts_per_page' => -1,
				'date_query' => array(
					'after' => date( 'Y-m-d', strtotime('-3 months') ),
					'inclusive' => true,
				)
			);

			$orders = get_posts($args);

			foreach ($orders as $order) {
				trustvox_order_create($order->ID);
			}
		}
	}
}
add_action('admin_init', 'trustvox_inside_settings_page', 10, 2);

