<?php 

/** 
 * Single de produtos
 */

// Remove a aba de reviews na single de produtos
function trustvox_disable_woocommerce_reviews_remove_tab($tabs) {
	unset($tabs['reviews']);
	return $tabs;
}
add_filter('woocommerce_product_tabs', 'trustvox_disable_woocommerce_reviews_remove_tab', 99);

// Remove os reviews padrões do wocommerce
function trustvox_disable_wocommerce_reviews_stars() {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' );
}
add_action('plugins_loaded', 'trustvox_disable_wocommerce_reviews_stars');

// Adiciona os scripts para exibir os reviews na single de produtos no <head>
function trustvox_script_reviews() { 
	if ( is_singular('product') ) {
		$trustvox_options = get_option('trustvox');

		print '
			<script type="text/javascript">
				var _trustvox = _trustvox || [];
					_trustvox.push([\'_storeId\',     ' . wp_json_encode($trustvox_options['loja_ID']) . ']);
					_trustvox.push([\'_productId\',   ' . wp_json_encode(get_the_ID())                 . ']);
					_trustvox.push([\'_productName\', ' . wp_json_encode(get_the_title())              . ']);
			';

		if ( has_post_thumbnail() ) {
			print '_trustvox.push([\'_productPhotos\', [' . wp_json_encode( esc_url(get_the_post_thumbnail_url()) ) . '] ]);';
		} else {
			print '_trustvox.push([\'_productPhotos\', []]);';
		}

		print '
				(function() {
					var tv = document.createElement(\'script\'); tv.type = \'text/javascript\'; tv.async = true;
					tv.charset = "UTF-8";
					tv.src = \'//static.trustvox.com.br/sincero/sincero.js\';
					var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(tv, s);
				})();
			</script>';
	}
}
add_action( 'wp_head', 'trustvox_script_reviews' );

// Adiciona o widget dos reviews
function trustvox_template_single_reviews() {
	$trustvox_options = get_option('trustvox');
	$trustvox_options_widget_title = isset( $trustvox_options['widget_title'] ) ? $trustvox_options['widget_title'] : 'Veja opiniões de quem já comprou';

	print '<div id="_trustvox_widget_container" class="trustvox-widget-container" style="margin: 30px 0; clear: both;"><h2 class="trustvox-widget-title">' . $trustvox_options_widget_title . '</h2><div id="_trustvox_widget"></div></div>';
}
add_action( 'woocommerce_after_single_product_summary', 'trustvox_template_single_reviews', 15);


// Adiciona os scripts para exibir os ratings (estrelas) no <head>
function trustvox_scripts_reviews_stars() { 
	if ( !is_admin() ) {
		$trustvox_options = get_option('trustvox');

		print '
			<script type="text/javascript">
				var _trustvox_shelf_rate = _trustvox_shelf_rate || [];
					_trustvox_shelf_rate.push(["_storeId", ' . wp_json_encode($trustvox_options['loja_ID']) . ']);
			</script>
			<script type="text/javascript" async="true" src="' . esc_url('//rate.trustvox.com.br/widget.js') . '"></script>
			<script type="text/javascript" src="//certificate.trustvox.com.br/widget.js"></script>';

		wp_register_style('trustvox', plugins_url( '../css/trustvox.css', __FILE__ ), array(), '1.0', 'all');
		wp_enqueue_style( 'trustvox');
	}
}
add_action( 'wp_head', 'trustvox_scripts_reviews_stars' );

// Adiciona o widget para exibiros ratings (estrelas) na single abaixo do título do produto
function trustvox_template_single_reviews_stars() {
	print '
		<a class="trustvox-fluid-jump trustvox-widget-rating" href="#_trustvox_widget" title="Pergunte e veja opiniões de quem já comprou">
			<div class="trustvox-shelf-container" data-trustvox-product-code="' . esc_attr(get_the_id()) . '" data-trustvox-should-skip-filter="true" data-trustvox-display-rate-schema="true"></div><span class="rating-click-here">Clique e veja!</span>
		</a>';
}
add_action( 'woocommerce_single_product_summary', 'trustvox_template_single_reviews_stars', 10);


/**
 * Archives e loops
 */

// Remove os reviews padrões do wocommerce
function trustvox_disable_wocommerce_reviews_stars_loop() {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
}
add_action( 'plugins_loaded', 'trustvox_disable_wocommerce_reviews_stars_loop' );

// Remove a ordenação por reviews
function trustvox_disable_wocommerce_order_by_reviews( $catalog_orderby_options ) {
	unset( $catalog_orderby_options['rating'] );
	return $catalog_orderby_options;
}
add_filter( 'woocommerce_catalog_orderby', 'trustvox_disable_wocommerce_order_by_reviews' );

// Adiciona o rating da trustvox nos loops
function trustvox_template_loop_reviews_stars() {
	print '<div class="trustvox-loop-reviews-stars" data-trustvox-product-code="' . esc_attr(get_the_id()) . '"></div>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'trustvox_template_loop_reviews_stars', 5);


/**
 * Selo site sincero
 */

// Adiciona o selo de site sincero
function trustvox_selo_site_sincero() {
	if ( ! is_admin() ) {
		/*$trustvox_certificate = get_option('trustvox_certificate', false);*/

		/*if ( isset($trustvox_certificate['code']) and isset($trustvox_certificate['position']) and $trustvox_certificate['position'] != 'none' ) {*/
			print '
				<div data-trustvox-certificate-fixed></div>
				<script type="text/javascript" src="//certificate.trustvox.com.br/widget.js"></script>
			';
		/*}*/
	}
}
//add_action( 'wp_footer', 'trustvox_selo_site_sincero', 10, 0);


/*function trustvox_shortcode_selo_site_sincero(){
	$trustvox_certificate = get_option('trustvox_certificate', false);

	if ( isset($trustvox_certificate['code']) ) {
		return '
			<div data-trustvox-certificate-fixed></div>
			<script type="text/javascript" src="//certificate.trustvox.com.br/widget.js"></script>
		';
	} else {
		return '[' . __('Informe o código da loja nas configurações do selo sincero do plugin Trustvox', 'trustvox') . ']';
	}
}*/
add_shortcode( 'trustvox_selo_site_sincero', 'trustvox_shortcode_selo_site_sincero' );


// widget selo site sincero
class trustvox_widget_selo_site_sincero extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'trustvox_widget_selo_site_sincero',
			'description' => '',
		);
		parent::__construct( 'trustvox_widget_selo_site_sincero', 'Trustvox - Selo site sincero', $widget_ops );
	}

	/*public function widget( $args, $instance ) {
		$trustvox_certificate = get_option('trustvox_certificate', false);
		print '
			<div data-trustvox-certificate-fixed></div>
			<script type="text/javascript" src="//certificate.trustvox.com.br/widget.js"></script>
		';
	}*/
}

function trustvox_register_widget_selo_site_sincero() {
	$trustvox_certificate = get_option('trustvox_certificate', false);

	if ( isset($trustvox_certificate['code']) ) {
    	register_widget( 'trustvox_widget_selo_site_sincero' );
	}
}
add_action( 'widgets_init', 'trustvox_register_widget_selo_site_sincero' );