<?php
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
		die();

	// Remove os dados do banco
	delete_option( 'trustvox' );
	delete_option( 'trustvox_certificate' );
	delete_option( 'trustvox_once' );
?>