<?php 
/* 
 * Configurações no painel
 */
class trustvox_settings_page {

	// Holds the values to be used in the fields callbacks
	private $options;

	// Adiciona a opção ao menu lateral
	public function trustvox_page() {
		add_options_page(
			__('Trustvox', 'trustvox'),                               // Título da página
			__('Trustvox', 'trustvox'),                               // Opção
			'manage_options',                                         // Capacidade
			'trustvox',                                               // Slug
			array($this, 'trustvox_options')                          // Callback
		);
	}

	// Hooks
	public function __construct() {
		add_action('admin_menu', array( $this, 'trustvox_page'));
		add_action('admin_init', array( $this, 'trustvox_settings'));
		add_action('admin_init', array( $this, 'trustvox_certificate'));
	}

	// Constroi a página de Configurações
	public function trustvox_options() {
		$this->options = get_option('trustvox');
		$this->certificate = get_option('trustvox_certificate');

		echo '<div class="wrap">';
			echo '<span style="float: right"><a href="https://site.trustvox.com.br/" target="_blank"><img src="' . plugins_url( '../assets/trustvox-logo.png', __FILE__ ) . '"></a></span></span>';

			echo '<h1>' . __('Trustvox', 'trustvox') . '</h1>';

			$tabs = array(
				'settings'    => __('Configurações'), 
				'certificate' => __('Selo Site Sincero'), 
				'faq'         => __('FAQ'), 
				'log'         => __('Log'),
				'credits'     => __('Créditos')
			);

			echo '<h2 class="nav-tab-wrapper">';
				foreach( $tabs as $tab => $name ):
					$current = ( isset ( $_GET['tab'] ) ) ? $_GET['tab'] : 'settings';
					$class   = ( $tab == $current ) ? ' nav-tab-active' : '';
					echo "<a class='nav-tab$class' href='?page=trustvox&tab=$tab'>$name</a>";
				endforeach;
			echo '</h2>';

			global $pagenow;
			if ( $pagenow == 'options-general.php' && $_GET['page'] == 'trustvox' ) : 
				$tab = ( isset ( $_GET['tab'] ) ) ? $_GET['tab'] : 'settings';

				// TABS
				switch ($tab): 
					case 'settings': 
						echo '<form method="post" action="options.php">';
							settings_fields('trustvox_settings_group');
							do_settings_sections('trustvox_page');
							submit_button();
						echo '</form>';
					break;
					case 'certificate': 
						echo '<form method="post" action="options.php">';
							settings_fields('trustvox_certificate_group');
							do_settings_sections('trustvox_certificate_page');
							$this->trustvox_certificate_info_extra();
							/*submit_button();*/
						echo '</form>';
					break;
					case 'faq':
						echo '<h2>' . __('FAQ:') . '</h2>';
						$this->trustvox_faq();
					break;
					case 'log':
						echo '<h2>' . __('Log:') . '</h2>';
						$this->trustvox_log();
					break;
					case 'credits':
						echo '<h2>' . __('Créditos:') . '</h2>';
						$this->trustvox_credits();
					break;
				endswitch;
			endif;
		echo '</div>';
	}


	/**
	 * Opções
	 */

	// Constroi as opções
	public function trustvox_settings() {
		register_setting(
			'trustvox_settings_group',
			'trustvox',
			array($this, 'trustvox_settings_sanitize')
		);

		add_settings_section(
			'trustvox_section',
			__('Configurações:', 'trustvox'),
			array($this, 'trustvox_settings_info'),
			'trustvox_page'
		);

		add_settings_field(
			'loja_ID',
			__('Loja ID:', 'trustvox'),
			array($this, 'trustvox_settings_loja_ID_callback'),
			'trustvox_page',
			'trustvox_section'
		);

		add_settings_field(
			'loja_token',
			__('Token:', 'trustvox'),
			array($this, 'trustvox_settings_loja_token_callback'),
			'trustvox_page',
			'trustvox_section'
		);

		add_settings_field(
			'widget_title',
			__('Título do Widget:', 'trustvox'),
			array($this, 'trustvox_settings_widget_title_callback'),
			'trustvox_page',
			'trustvox_section'
		);
	}

		// Verifica e higieniza os dados passados pelos campos
		public function trustvox_settings_sanitize( $input ) {
			$new_input = array();

			if( isset( $input['loja_ID'] ) )
				$new_input['loja_ID'] = sanitize_text_field( $input['loja_ID'] );

			if( isset( $input['loja_token'] ) )
				$new_input['loja_token'] = sanitize_text_field( $input['loja_token'] );

			if( isset( $input['widget_title'] ) )
				$new_input['widget_title'] = sanitize_text_field( $input['widget_title'] );

			return $new_input;
		}

		// Adiciona 
		public function trustvox_settings_info() {
			print __('Adicione nos campos abaixo os dados Loja ID e Token recebidos em seu email na ativação de sua conta Trustvox e clique em Salvar Alterações.', 'trustvox');
			$message = __('Caso não tenha conta TrustVox', 'trustvox');
			$click   = __('clique aqui', 'trustvox');
			printf('<p>%s <a href="https://site.trustvox.com.br/contato/" target="_blank">%s</a>.</p>', $message, $click);
		}

		// Callback para o ID da loja
		public function trustvox_settings_loja_ID_callback() {
			printf(
				'<input type="text" id="loja_ID" name="trustvox[loja_ID]" value="%s" size="50" />',
				isset( $this->options['loja_ID'] ) ? esc_attr( $this->options['loja_ID']) : ''
			);
		}

		// Callback para o Token
		public function trustvox_settings_loja_token_callback() {
			printf(
				'<input type="text" id="loja_token" name="trustvox[loja_token]" value="%s" size="50" />',
				isset( $this->options['loja_token'] ) ? esc_attr( $this->options['loja_token']) : ''
			);
		}

		// Callback para o Token
		public function trustvox_settings_widget_title_callback() {
			printf(
				'<input type="text" id="widget_title" name="trustvox[widget_title]" value="%s" size="50" />',
				isset( $this->options['widget_title'] ) ? esc_attr( $this->options['widget_title']) : 'Veja opiniões de quem já comprou'
			);
		}



	/**  
	 * Selo
	 */

	// Certificado
	public function trustvox_certificate() {
		register_setting(
			'trustvox_certificate_group',
			'trustvox_certificate',
			array($this, 'trustvox_certificate_sanitize')
		);

		add_settings_section(
			'trustvox_certificate_section',
			__('Selo Site Sincero:', 'trustvox'),
			array($this, 'trustvox_certificate_info'),
			'trustvox_certificate_page'
		);

		/*add_settings_field(
			'code',
			__('Código da Loja:', 'trustvox'),
			array($this, 'trustvox_certificate_code_callback'),
			'trustvox_certificate_page',
			'trustvox_certificate_section'
		);*/

		/*add_settings_field(
			'position',
			__('Posição do Selo:', 'trustvox'),
			array($this, 'trustvox_certificate_position_callback'),
			'trustvox_certificate_page',
			'trustvox_certificate_section'
		);*/
	}

		// Adiciona 
		public function trustvox_certificate_info() {
			print __('<p>Sua loja já possui <strong>30 opiniões</strong> de produto publicadas na Trustvox?</p>', 'trustvox');
			print __('<p>Se a resposta foi sim, você já está apto a instalar o Selo Site Sincero em sua loja!</p>', 'trustvox');
			print __('<p><strong>Antes da instalação, entre em contato com a <a href="https://site.trustvox.com.br/suporte#form_suporte" target="_blank">Trustvox</a>, para liberar o seu Certificado de Sinceridade.</strong></p>', 'trustvox');
			
			print __('<br /><p>Com seu certificado em mãos, vamos lá! :D</p>', 'trustvox');
			print __('<p><strong>1.</strong> Para chegar até a tela de configurações basta acessar seu <a href="https://trustvox.com.br/login" target="_blak">Dashboard</a> e seguir estes passos:</p>', 'trustvox');
			print __('<img src="https://d33v4339jhl8k0.cloudfront.net/docs/assets/5596abe2e4b0f49cc3ffe613/images/591cb1e60428634b4a3338d5/file-R0tU3GH5lc.png" width="700" height="auto"/>', 'trustvox');
			
			
			print __('<p><strong>2.</strong> O Selo, dispõe de diversas configurações para um posicionamento adequado na sua página:</p>', 'trustvox');
			print __('<img src="https://d33v4339jhl8k0.cloudfront.net/docs/assets/5596abe2e4b0f49cc3ffe613/images/58ef78c7dd8c8e5c57315dd5/file-Eb0oB1Jo8c.png" width="700" height="auto"/>', 'trustvox');
			print __('<p>O Script para <strong>Selo Flutuante</strong> já está pré configurado! Então precisamos apenas habilitar em seu painel "Usar versão flutuante" e escolher em qual posição você deseja exibir!</p>', 'trustvox');
			print __('<p>Pronto! ;D</p>', 'trustvox');
			
		}

		// Adiciona 
		private function trustvox_certificate_info_extra() {
			print '<br /><h3>' . __('Opcional:', 'trustvox') . '</h3>';
			print '<p>Caso queira utilizar a <strong>Versão Fixa</strong> será necessário ter acesso a edição HTML do template:</p>';
			/*printf('<p>%s <code>%s</code></p>', __('Personalize local do selo fixo, inclua o shortcode em seu tema, ou via widget, páginas e posts com o código:', 'trustvox'), esc_html('[trustvox_selo_site_sincero]') );*/
			printf('<p>%s <code>%s</code></p>', __('Insira a div no local que o selo deve ser carregado:', 'trustvox'), esc_html("<div data-trustvox-certificate-fixed></div>") );
			printf('<br /><p>Caso tenha mais dúvidas: <a href="https://help.trustvox.com.br/collection/1-help-da-trustvox" target="_blank">Consulte nosso Help</a>. :)</p>');			
		}

		// Verifica e higieniza os dados passados pelos campos
		public function trustvox_certificate_sanitize( $input ) {
			$new_input = array();

			if( isset( $input['code'] ) )
				$new_input['code'] = sanitize_text_field( $input['code'] );

			if( isset( $input['position'] ) )
				$new_input['position'] = sanitize_text_field( $input['position'] );

			return $new_input;
		}

		// Callback para o ID da loja
		public function trustvox_certificate_code_callback() {
			printf(
				'<input type="text" id="code" name="trustvox_certificate[code]" value="%s" />',
				isset( $this->certificate['code'] ) ? esc_attr( $this->certificate['code']) : ''
			);
		}

		// Callback para o ID da loja
		public function trustvox_certificate_position_callback() {
			$positions = array(
				array(
					'label' => __('Esquerda'),
					'value' => 'left',
				),
				array(
					'label' => __('Direita'),
					'value' => 'right',
				),
				array(
					'label' => __('Nenhum'),
					'value' => 'none',
				),
			);

			/*foreach ($positions as $position) {
				print '
					<label>
						<input type="radio" id="position" name="trustvox_certificate[position]" value="' . $position['value'] . '"' .  checked( $position['value'], isset( $this->certificate['position'] ) ? esc_attr( $this->certificate['position']): false, false) . '/>
						' . $position['label'] . '
					</label>
				';
			}*/
		}



	/**
	 * FAQ
	 */

	private function trustvox_faq() {
		print '<dl class="trustvox-faq">';
			print '<dt>' . __('A Trustvox é gratuita?') . '</dt>';
			printf('<dd>%s <a href="https://site.trustvox.com.br/contato/" target="_blank">%s</a>.</p>', __('Não. É necessário adquirir um plano.'), __('Comece agora') );
			
			print '<dt>' . __('Eu preciso ter o plugin WooCommerce instalado?') . '</dt>' .
				'<dd>' . __('Sim.') . '</dt>';
				
			print '<dt>' . __('Este plugin funciona com outros plugins de Loja Virtual para WordPress?') . '</dt>' .
				'<dd>' . __('Não, somente com o WooCommerce.') . '</dt>';

			print '<dt>' . __('Como funciona a TrustVox?') . '</dt>';
			printf('<dd>%s <a href="https://site.trustvox.com.br/" target="_blank">%s</a>.</p>', __('Somos uma certificadora de reviews. Mais confiança, mais vendas!'), __('Saiba mais') );

			print '<dt>' . __('Os comentários já existentes da minha loja vão ser desabilitados?') . '</dt>' .
			      '<dd>' . __('Sim. O sistema de comentário padrão do WooCommerce será desabilitado. Caso você tenha outro sistema como Facebook ou Disqus, recomendamos desabilitar.') . '</dt>';

			print '<dt>' . __('É possível migrar/importar as opiniões que já tenho em minha loja para a TrustVox?') . '</dt>' .
			      '<dd>' . __('Não. Porém iremos coletar automaticamente todos os pedidos com o status "Enviado" dos últimos 90 dias.') . '</dt>' .
			      '<dd>' . __('Como a Trustvox é uma certificadora de opiniões verdadeiras, precisamos garantir que todas as opiniões são reais.') . '</dt>';

			print '<dt>' . __('Preciso instalar o HTML da Trustvox ?') . '</dt>' . 
			      '<dd>' . __('Não. A instalação é plug-and-play. A Trustvox irá aparecer automaticamente no detalhe de todas as suas páginas de produtos.') . '</dt>';

			print '<dt>' . __('As vendas são cadastradas automaticamente?') . '</dt>' .
			      '<dd>' . __('Sim! O Plugin Trustvox envia todos os pedidos marcados como "Concluído" no WooCommerce e encaminha um email para coletar a opinião do comprador depois de 5 dias da data estimada de entrega.') . '</dt>';

			print '<dt>' . __('E as vendas/pedidos antigas(os)?') . '</dt>' .
			      '<dd>' . __('Será feita uma importação automática de todos os pedidos dos últimos 90 dias com o status "Concluído" a partir da ativação da Trustvox em sua loja.') . '</dt>';

			print '<dt>' . __('Como posso incluir o Selo Site Sincero em minha loja?') . '</dt>' .
			      '<dd>' . __('Se a sua loja já possui mais de 30 opiniões de produto publicadas na Trustvox, então ela já está qualificada para ter o Selo Site Sincero.') . '</dt>' .
			      '<dd>' . __('É bem simples, é só entrar em contato com a Trustvox em meajuda@trustvox.com.br para gerar o seu Certificado de Sinceridade.') . '</dt>' .
			      
			print '<dt>' . __('Quer saber mais?') . '</dt>';
			printf('<dd>%s <a href="https://help.trustvox.com.br/" target="_blank">https://help.trustvox.com.br/</a></p>', __('Acesse nossa seção Help no endereço: ') );

		print '</dl>';
		print '<style>
				.trustvox-faq {
					margin-top: 0;
				}
				.trustvox-faq dt {
					font-size: 1.2em;
					font-weight: 600;
					margin-top: 30px;
				}
				.trustvox-faq dt:first-child {
					margin-top: 0;
				}
				.trustvox-faq dd {
					margin-left: 0;
					margin: 5px 0;
				}
			</style>';
	}


	/**  
	 * LOG
	 */

	private function trustvox_log() {
		print '<textarea class="trustvox-log" cols="70" rows="30" style="width: 100%; overflow-x: scroll; white-space: nowrap;">';
		if (file_exists( get_home_path() . "wp-content/uploads/wc-logs/trustvox.log" ))
			include( get_home_path() . "wp-content/uploads/wc-logs/trustvox.log" );
		else
			print __('Nenhum registro', 'trustvox');
		print '</textarea>';
	}


	/**  
	 * Credits
	 */

	private function trustvox_credits() {
		print '
			<p>Sinceridade não tem preço. E boas parcerias também não! Este plugin foi desenvolvido pela Lampejos em parceria com a Trustvox.</p>
			<p>Por isso a Trustvox faz aqui um review sincero e indica a Lampejos para todos os lojistas Woocommerce do Brasil! :)</p>

			<div class="trustvox-credits">
				<div class="trustvox-credits__item">
					<div class="trustvox-credits__thumb">
						<a href="https://site.trustvox.com.br/?&utm_source=pluginTrustvox&utm_medium=link&utm_campaign=' . get_site_url() . '" target="_blank"><img src="' . plugins_url( '../assets/credits-logo-trustvox.jpg', __FILE__ ) . '"></a>
					</div>
					<div class="trustvox-credits__infos">
						<p class="trustvox-credits__title"><a href="https://site.trustvox.com.br/?&utm_source=pluginTrustvox&utm_medium=link&utm_campaign=' . get_site_url() . '" target="_blank">TrustVox - Sinceridade não tem preço</a></p>
						<ul>
							<li><a href="https://www.facebook.com/trustvox" target="_blank"><img src="' . plugins_url( '../assets/social-facebook.jpg', __FILE__ ) . '"></a></li>
							<li><a href="https://www.twitter.com/trustvox" target="_blank"><img src="' . plugins_url( '../assets/social-twitter.jpg', __FILE__ ) . '"></a></li>
						</ul>
					</div>
				</div>
				<div class="trustvox-credits__item">
					<div class="trustvox-credits__thumb">
						<a href="https://lampejos.com.br/?&utm_source=pluginTrustvox&utm_medium=link&utm_campaign=' . get_site_url() . '" target="_blank"><img src="' . plugins_url( '../assets/credits-logo-lampejos.jpg', __FILE__ ) . '"></a>
					</div>
					<div class="trustvox-credits__infos">
						<p class="trustvox-credits__title"><a href="https://lampejos.com.br/?&utm_source=pluginTrustvox&utm_medium=link&utm_campaign=' . get_site_url() . '" target="_blank">Lampejos</a></p>
						<ul>
							
							<li><a href="https://www.facebook.com/Lampejos" target="_blank"><img src="' . plugins_url('../assets/social-facebook.jpg', __FILE__ ) . '"></a></li> 
							<li><a href="https://www.linkedin.com/company/lampejos" target="_blank"><img src="' . plugins_url('../assets/social-linkedin.jpg', __FILE__ ) . '"></a></li>
							<li><a href="https://instagram.com/lampejos/" target="_blank"><img src="' . plugins_url('../assets/social-instagram.jpg', __FILE__ ) . '"></a></li>
							<li><a href="https://www.pinterest.com/lampejos/" target="_blank"><img src="' . plugins_url('../assets/social-pinterest.jpg', __FILE__ ) . '"></a></li>
							<li><a href="https://twitter.com/lampejos" target="_blank"><img src="' . plugins_url('../assets/social-twitter.jpg', __FILE__ ) . '"></a></li>
						</ul>
					</div>
				</div>
			</div>

			<style>
				.trustvox-credits { margin-top: 30px; }

				.trustvox-credits__item {
					background-color: #fff;
					display: flex;
					width: 500px;
					margin-bottom: 20px;
				}

				.trustvox-credits__thumb {
					padding: 15px;
					border-right: 1px solid #f1f1f1;
				}

				.trustvox-credits__infos { padding: 15px; }

				.trustvox-credits__title {
					font-weight: 600;
					font-size: 14px;
				}

				.trustvox-credits__title a {
					text-decoration: none;
					color: black;
				}

				.trustvox-credits__infos p { margin: 0 0 10px; }

				.trustvox-credits__infos ul { margin: 0; }

				.trustvox-credits__infos ul li {
					display: inline-block;
					margin-right: 10px;
				}
			</style>
		';
	}

}

/* Aplica a página de configurações ao painel */
if( is_admin() ) {
	$settingsPage = new trustvox_settings_page();
}
