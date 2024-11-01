<?php 
/*
	Plugin Name: Trustvox
	Plugin URI: https://site.trustvox.com.br/
	Description: Certificação de reviews
	Author: cleytontrustvox
	Author URI: https://lampejos.com.br/
	Text Domain: trustvox
	Domain Path: /languages
	Version: 0.6.5
*/

/**
 * Evita de ser acessado diretamente
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Verifica se o woocomerce esta ativo
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// load textdomain
	load_plugin_textdomain('trustvox', false, basename( dirname( __FILE__ ) ) . '/languages' );

	// Cria a página de configurações
	require dirname(__FILE__) . '/includes/settings.php';

	// Adiciona o trustvox ao thema
	require dirname(__FILE__) . '/includes/front.php';

	// Verifica se já contém o ID e o Token
	$trustvox_options = get_option('trustvox', false);

	if ( $trustvox_options != false and !empty($trustvox_options['loja_ID']) and !empty($trustvox_options['loja_token']) ) {

		// Passa os pedidos concluídos para a trustvox
		require dirname(__FILE__) . '/includes/api.php';

		// Ativa o envio retroativo dos pedidos feitos nos últimos 3 meses
		require dirname(__FILE__) . '/includes/retro.php';

	} else {
		// Mensagem de alerta para o usuário adicionar o ID e o Token da loja
		function trustvox_missing_token() {
			$class   = 'notice notice-info';
			$message = __('Trustvox: Informe o ID e o Token de sua loja.', 'Trustvox');
			$link    = '<a href="' . esc_url(admin_url( 'options-general.php?page=trustvox' )) . '">' . __('Configurações', 'trustvox') . '</a>';
			printf('<div class="%s"><p>%s %s</p></div>', $class, $message, $link);
		}
		add_action('admin_notices', 'trustvox_missing_token');
	}

} else {
	// Mensagem de erro para o plugin rodar apenas se tiver woocommerce
	function trustvox_woocommerce_not_found() {
		$class   = 'notice notice-error';
		$message = __('Trustvox: Woocommerce não encontrado.', 'Trustvox');
		printf('<div class="%s"><p>%s</p></div>', $class, $message);
	}
	add_action('admin_notices', 'trustvox_woocommerce_not_found');
}
