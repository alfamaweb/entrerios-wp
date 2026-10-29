<?php
/**
 * Integração com o Advanced Custom Fields PRO.
 *
 * DÍVIDAS RESOLVIDAS (v2):
 *  - sincronia acf-json/ ligada, com save e load points no tema: grupo de campo
 *    editado na UI vira arquivo versionado, e o próximo clone do repositório já
 *    nasce com a modelagem;
 *  - a página de opções e o grupo "hero" são definidos em código
 *    (acf_add_local_field_group), porque são boilerplate e não conteúdo de
 *    cliente — não precisam viajar em dump de banco;
 *  - aviso claro no admin quando o ACF está ausente, em vez de fatal error.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

/**
 * Onde o ACF grava os JSON dos grupos editados na UI.
 */
add_filter('acf/settings/save_json', 'aw_acf_save_json');
function aw_acf_save_json($path)
{
	$dir = AW_DIR . '/acf-json';

	return is_dir($dir) ? $dir : $path;
}

/**
 * De onde o ACF carrega os JSON versionados.
 *
 * @param array $paths
 * @return array
 */
add_filter('acf/settings/load_json', 'aw_acf_load_json');
function aw_acf_load_json($paths)
{
	unset($paths[0]);
	$paths[] = AW_DIR . '/acf-json';

	return $paths;
}

/**
 * Página "Opções do Site" — header, footer, redes, contatos e integrações.
 *
 * @return void
 */
add_action('acf/init', 'aw_acf_options_page');
function aw_acf_options_page()
{
	if (!function_exists('acf_add_options_page')) {
		return;
	}

	acf_add_options_page(array(
		'page_title'  => __('Opções do Site', 'alfama-web'),
		'menu_title'  => __('Opções do Site', 'alfama-web'),
		'menu_slug'   => 'aw-opcoes',
		'capability'  => 'edit_theme_options',
		'position'    => '58.9',
		'icon_url'    => 'dashicons-admin-settings',
		'redirect'    => false,
		'update_button' => __('Salvar opções', 'alfama-web'),
	));
}

/**
 * Grupos de campo que são boilerplate: definidos em código, não na UI.
 *
 * @return void
 */
add_action('acf/init', 'aw_acf_grupos_locais');
function aw_acf_grupos_locais()
{
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	/* --------------------------------------------------------------------
	 * Opções do Site
	 * ----------------------------------------------------------------- */
	acf_add_local_field_group(array(
		'key'      => 'group_aw_opcoes',
		'title'    => __('Opções do Site', 'alfama-web'),
		'location' => array(array(array(
			'param'    => 'options_page',
			'operator' => '==',
			'value'    => 'aw-opcoes',
		))),
		'menu_order' => 0,
		'fields'     => array(
			array('key' => 'field_aw_tab_contato', 'label' => __('Contato', 'alfama-web'), 'type' => 'tab'),
			array(
				'key'   => 'field_aw_whatsapp',
				'label' => __('WhatsApp — número', 'alfama-web'),
				'name'  => 'whatsapp_numero',
				'type'  => 'text',
				'instructions' => __('Com DDI e DDD, só dígitos. Ex.: 5541999999999. Vazio esconde o botão flutuante.', 'alfama-web'),
			),
			array('key' => 'field_aw_telefone', 'label' => __('Telefone', 'alfama-web'), 'name' => 'telefone', 'type' => 'text'),
			array('key' => 'field_aw_email', 'label' => __('E-mail de contato', 'alfama-web'), 'name' => 'email_contato', 'type' => 'email',
				'instructions' => __('Destinatário dos formulários do site. Vazio usa o e-mail do administrador.', 'alfama-web')),
			array('key' => 'field_aw_endereco', 'label' => __('Endereço', 'alfama-web'), 'name' => 'endereco', 'type' => 'textarea', 'rows' => 3),

			array('key' => 'field_aw_tab_redes', 'label' => __('Redes sociais', 'alfama-web'), 'type' => 'tab'),
			array(
				'key'          => 'field_aw_redes',
				'label'        => __('Redes sociais', 'alfama-web'),
				'name'         => 'redes_sociais',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => __('Adicionar rede', 'alfama-web'),
				'sub_fields'   => array(
					array('key' => 'field_aw_rede_nome', 'label' => __('Nome', 'alfama-web'), 'name' => 'nome', 'type' => 'text', 'required' => 1),
					array('key' => 'field_aw_rede_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url', 'required' => 1),
					array('key' => 'field_aw_rede_icone', 'label' => __('Ícone (SVG)', 'alfama-web'), 'name' => 'icone', 'type' => 'image', 'return_format' => 'array', 'mime_types' => 'svg'),
				),
			),

			array('key' => 'field_aw_tab_rodape', 'label' => __('Rodapé', 'alfama-web'), 'type' => 'tab'),
			array('key' => 'field_aw_rodape_titulo', 'label' => __('Título do contato', 'alfama-web'), 'name' => 'rodape_titulo', 'type' => 'text',
				'instructions' => __('Acima do endereço. Ex.: Água Mineral Entre Rios.', 'alfama-web')),
			array('key' => 'field_aw_rodape_social_rotulo', 'label' => __('Rótulo da rede social', 'alfama-web'), 'name' => 'rodape_social_rotulo', 'type' => 'text',
				'wrapper' => array('width' => '50')),
			array('key' => 'field_aw_rodape_social', 'label' => __('Perfil em destaque', 'alfama-web'), 'name' => 'rodape_social', 'type' => 'link', 'return_format' => 'array',
				'instructions' => __('Texto = @perfil. Vazio esconde o bloco.', 'alfama-web'), 'wrapper' => array('width' => '50')),
			array('key' => 'field_aw_copyright', 'label' => __('Texto de copyright', 'alfama-web'), 'name' => 'copyright', 'type' => 'text',
				'instructions' => __('O símbolo © e o ano são inseridos automaticamente.', 'alfama-web'), 'wrapper' => array('width' => '50')),
			array('key' => 'field_aw_rodape_politica', 'label' => __('Link legal', 'alfama-web'), 'name' => 'rodape_politica', 'type' => 'link', 'return_format' => 'array',
				'instructions' => __('Ex.: Política de Privacidade.', 'alfama-web'), 'wrapper' => array('width' => '50')),
		),
	));

	/* --------------------------------------------------------------------
	 * Hero das páginas internas
	 * ----------------------------------------------------------------- */
	acf_add_local_field_group(array(
		'key'      => 'group_aw_hero',
		'title'    => __('Hero da página', 'alfama-web'),
		'location' => array(
			array(array('param' => 'post_type', 'operator' => '==', 'value' => 'page')),
			array(array('param' => 'post_type', 'operator' => '==', 'value' => 'empreendimento')),
		),
		'menu_order' => 1,
		'position'   => 'normal',
		'fields'     => array(
			array(
				'key'        => 'field_aw_hero',
				'label'      => __('Hero', 'alfama-web'),
				'name'       => 'hero',
				'type'       => 'group',
				'sub_fields' => array(
					array('key' => 'field_aw_hero_titulo', 'label' => __('Título', 'alfama-web'), 'name' => 'titulo', 'type' => 'text',
						'instructions' => __('Vazio usa o título da página.', 'alfama-web')),
					array('key' => 'field_aw_hero_texto', 'label' => __('Texto de apoio', 'alfama-web'), 'name' => 'texto', 'type' => 'textarea', 'rows' => 3),
					array('key' => 'field_aw_hero_desktop', 'label' => __('Imagem — desktop', 'alfama-web'), 'name' => 'desktop', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium'),
					array('key' => 'field_aw_hero_mobile', 'label' => __('Imagem — mobile', 'alfama-web'), 'name' => 'mobile', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium',
						'instructions' => __('Opcional. Vazio usa a imagem de desktop.', 'alfama-web')),
				),
			),
		),
	));
}

/**
 * Aviso no admin quando o ACF PRO não está ativo.
 *
 * O tema degrada em vez de quebrar: aw_field() devolve os fallbacks e o site
 * continua navegável, mas o alerta deixa a causa explícita.
 *
 * @return void
 */
add_action('admin_notices', 'aw_aviso_acf_ausente');
function aw_aviso_acf_ausente()
{
	if (function_exists('get_field') || !current_user_can('activate_plugins')) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
		esc_html__('Alfama WEB v3:', 'alfama-web'),
		esc_html__('o Advanced Custom Fields PRO não está ativo. O tema continua funcionando com valores de fallback, mas nenhum conteúdo editável será exibido.', 'alfama-web')
	);
}
