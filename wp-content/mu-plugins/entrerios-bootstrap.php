<?php
/**
 * Plugin Name: Entre Rios — bootstrap de conteúdo
 * Description: Importa os grupos de campos ACF do projeto e grava o conteúdo inicial vindo do Figma (linhas e cores, Home, hero, cabeçalho, menu e imagens). Roda uma vez por versão.
 * Author: Alfama Web
 * Version: 9
 *
 * Padrão Alfama Web (skill figma-wp-tema-classico, seção 4):
 *  - os grupos de campos são JSON no formato de exportação do ACF PRO, em
 *    entrerios-bootstrap/acf/, importados para o BANCO com
 *    acf_import_field_group() — depois disso são editáveis pela tela do ACF e
 *    não dependem de arquivo em produção;
 *  - o conteúdo do Figma vira seed com update_field(), para o site não nascer
 *    com seções vazias (o tema não tem fallback de texto/imagem no PHP);
 *  - a execução é controlada por ENTRERIOS_BOOTSTRAP_VERSION: suba o número
 *    quando acrescentar algo aqui. Rodar de novo não duplica nada e NÃO
 *    sobrescreve conteúdo que o cliente já editou (cada seed só grava campo
 *    vazio).
 *
 * @package EntreRios
 */

defined('ABSPATH') || exit;

define('ENTRERIOS_BOOTSTRAP_VERSION', 9);
define('ENTRERIOS_BOOTSTRAP_DIR', __DIR__ . '/entrerios-bootstrap');

add_action('init', 'entrerios_bootstrap', 99);

/**
 * @return void
 */
function entrerios_bootstrap()
{
	if ((int) get_option('entrerios_bootstrap_version') >= ENTRERIOS_BOOTSTRAP_VERSION) {
		return;
	}

	// Depende do ACF PRO e do tema (que registra a taxonomia "linha").
	// Sem eles, tenta de novo na próxima requisição — não grava a versão.
	if (!function_exists('acf_import_field_group') || !function_exists('update_field') || !taxonomy_exists('linha')) {
		return;
	}

	// Trava contra duas requisições simultâneas rodando o seed.
	if (get_transient('entrerios_bootstrap_lock')) {
		return;
	}
	set_transient('entrerios_bootstrap_lock', 1, 5 * MINUTE_IN_SECONDS);

	// Subir ~30 imagens (e gerar os tamanhos) passa dos 30 s padrão do PHP.
	if (function_exists('set_time_limit')) {
		@set_time_limit(300);
	}

	entrerios_importar_campos();

	$midia  = entrerios_importar_midia();
	$linhas = entrerios_seed_linhas();
	$paginas = entrerios_seed_paginas();

	entrerios_seed_menu($paginas);
	entrerios_seed_opcoes($midia);
	entrerios_seed_hero($paginas['home'], $linhas, $midia, $paginas['produtos']);
	entrerios_seed_produtos($linhas, $midia);
	entrerios_seed_vitrine($paginas['home'], $linhas, $paginas['produtos']);
	entrerios_seed_linhas_cards($paginas['home'], $linhas, $paginas['produtos']);
	entrerios_seed_produto_360($paginas['home'], $linhas, $midia);
	entrerios_atualizar_giro_guarana($paginas['home'], $midia);
	entrerios_seed_sobre($paginas['home'], $midia, $paginas['a-entre-rios']);
	entrerios_seed_sustentabilidade($paginas['home']);
	entrerios_seed_instagram($paginas['home'], $midia);
	entrerios_seed_rodape($paginas['politica-de-privacidade']);
	entrerios_seed_a_entre_rios($paginas['a-entre-rios'], $midia);
	entrerios_seed_catalogo($paginas, $linhas, $midia);
	entrerios_seed_contato($paginas['contato'] ?? 0);
	entrerios_seed_politica($paginas['politica-de-privacidade'] ?? 0);
	entrerios_seed_sustentabilidade_pagina($paginas['sustentabilidade'] ?? 0, $paginas['home'] ?? 0, $midia);

	update_option('entrerios_bootstrap_version', ENTRERIOS_BOOTSTRAP_VERSION, false);
	delete_transient('entrerios_bootstrap_lock');
}

/**
 * Importa (ou atualiza, pela key) cada grupo de entrerios-bootstrap/acf/*.json.
 *
 * @return void
 */
function entrerios_importar_campos()
{
	foreach (glob(ENTRERIOS_BOOTSTRAP_DIR . '/acf/*.json') as $arquivo) {
		$grupos = json_decode((string) file_get_contents($arquivo), true);

		if (!is_array($grupos)) {
			continue;
		}

		// Aceita tanto o array de exportação quanto um grupo solto.
		if (isset($grupos['key'])) {
			$grupos = array($grupos);
		}

		foreach ($grupos as $grupo) {
			if (empty($grupo['key'])) {
				continue;
			}

			$existente = acf_get_field_group($grupo['key']);

			if ($existente && !empty($existente['ID'])) {
				$grupo['ID'] = $existente['ID'];
			}

			acf_import_field_group($grupo);
		}
	}
}

/**
 * Sobe as imagens de entrerios-bootstrap/img/ para a Biblioteca de Mídia.
 *
 * O mapa arquivo → ID fica numa option, então rodar de novo reaproveita o
 * anexo em vez de duplicar.
 *
 * @return array<string,int> Nome do arquivo (sem extensão) => ID do anexo.
 */
function entrerios_importar_midia()
{
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$textos_alt = array(
		'logo-entre-rios'        => 'Entre Rios',
		'hero-agua-mineral'      => 'Garrafas de Água Mineral Entre Rios de 1,5 L e 500 ml, com e sem gás',
		'hero-dub-2l'            => 'Garrafa de DUB Life Energy Drink 2 L',
		'hero-qtal-guarana-2l'   => 'Garrafa de refrigerante Q’tal Fresh Guaraná 2 L',
		'hero-guarana-mais-copo' => 'Copo de Guaraná Mais Açaí 290 ml',
		'sobre-video-capa'       => 'Fábrica da Entre Rios vista de drone, com caminhão da marca no pátio',
		'instagram-agua'         => 'Garrafa de Água Mineral Entre Rios 500 ml sem gás',
		'instagram-energeticos'  => 'Lata de DUB Life Energy Drink 269 ml',
		'instagram-refrigerantes' => 'Lata de Q’tal Fresh Cola 350 ml',
		'instagram-guarana-mais' => 'Copo de Guaraná Mais Açaí 290 ml',
		'sobre-fabrica-drone-01' => 'Vista aérea da fábrica da Entre Rios, em Estância (SE), cercada de mata',
		'sobre-fabrica-drone-02' => 'Vista aérea da fábrica da Entre Rios entre pastos e mata, com a estrada de terra que leva até ela',
		'sobre-fabrica-video-capa' => 'Imagens de drone da fábrica e do caminhão da Entre Rios',
		'sobre-colagem-garrafa-1-5l' => 'Garrafa de Água Mineral Entre Rios 1,5 L sem gás',
		'sobre-colagem-dub-2l' => 'Garrafa de DUB Life Energy Drink 2 L',
		'sobre-colagem-qtal-guarana-2l' => 'Garrafa de refrigerante Q’tal Fresh Guaraná 2 L',
		'sobre-colagem-lata-350ml' => 'Lata de Água Mineral Entre Rios 350 ml sem gás',
		'sobre-colagem-copo-guarana-mais' => 'Copo de Guaraná Mais Açaí 290 ml',
		// v9 — produtos recortados rente (cards dos pilares da Sustentabilidade).
		'sustentabilidade-garrafao-20-litros'    => 'Garrafão de Água Mineral Entre Rios 20 litros',
		'sustentabilidade-garrafa-500ml-sem-gas' => 'Garrafa de Água Mineral Entre Rios 500 ml sem gás',
		'sustentabilidade-lata-350ml-sem-gas'    => 'Lata de Água Mineral Entre Rios 350 ml sem gás',
		'sustentabilidade-copo-200ml'            => 'Copo de Água Mineral Entre Rios 200 ml',
		'sustentabilidade-qtal-laranja-2l'       => 'Garrafa de refrigerante Q’tal Fresh Laranja 2 L',
		'sustentabilidade-copo-guarana-mais'     => 'Copo de Guaraná Mais Açaí 290 ml',
		'sustentabilidade-qtal-uva-2l'           => 'Garrafa de refrigerante Q’tal Fresh Uva 2 L',
	);

	// Produtos da vitrine: alt = nome do produto (produtos.json).
	foreach (entrerios_lista_produtos() as $produto) {
		$textos_alt['produto-' . $produto['slug']] = $produto['titulo'];
	}

	$mapa = (array) get_option('entrerios_bootstrap_midia', array());

	foreach (glob(ENTRERIOS_BOOTSTRAP_DIR . '/img/*.{webp,png,jpg,svg,mp4}', GLOB_BRACE) as $arquivo) {
		$nome = pathinfo($arquivo, PATHINFO_FILENAME);

		if (!empty($mapa[$nome]) && get_post($mapa[$nome])) {
			continue;
		}

		// media_handle_sideload() move o arquivo: trabalha sobre uma cópia.
		$tmp = wp_tempnam($arquivo);
		copy($arquivo, $tmp);

		$id = media_handle_sideload(array(
			'name'     => basename($arquivo),
			'tmp_name' => $tmp,
		), 0, $textos_alt[$nome] ?? '');

		if (is_wp_error($id)) {
			@unlink($tmp);
			continue;
		}

		if (isset($textos_alt[$nome])) {
			update_post_meta($id, '_wp_attachment_image_alt', $textos_alt[$nome]);
		}

		$mapa[$nome] = (int) $id;
		// Grava a cada imagem: se a requisição cair no meio, a próxima
		// continua de onde parou em vez de duplicar anexos.
		update_option('entrerios_bootstrap_midia', $mapa, false);
	}

	update_option('entrerios_bootstrap_midia', $mapa, false);

	return $mapa;
}

/**
 * Cria as 4 linhas do Figma e grava as cores (só nos campos vazios).
 *
 * Cores = variáveis do Figma (05_Documentation) resolvidas para hex; o mapa
 * papel → token está em docs/TOKENS-FIGMA.md do tema. As do chip de
 * Refrigerantes e Guaraná Mais seguem o padrão 300 → 700 visto nas outras
 * linhas (não conferido no Figma — ver README).
 *
 * @return array<string,int> slug => term_id
 */
function entrerios_seed_linhas()
{
	$linhas = array(
		'agua-mineral' => array(
			'nome'  => 'Água Mineral',
			'cores' => array(
				'cor_hero_inicio' => '#353393', // brand/navy-500
				'cor_hero_meio'   => '#2fa4dd', // brand/sky-500
				'cor_hero_fim'    => '#70cff6', // brand/sky-300
				'cor_chip_clara'  => '#8ed6f6', // category/agua/100
				'cor_chip_forte'  => '#1a7dba', // category/agua/700
				'cor_media'       => '#2fa4dd', // category/agua/500
				'cor_destaque'    => '#2fa4dd', // color/category/agua → agua/500
				'cor_profunda'    => '#1b1a4a', // brand/navy-900 (a Água não tem 950)
			),
		),
		'energeticos' => array(
			'nome'  => 'Energéticos',
			'cores' => array(
				'cor_hero_inicio' => '#0e1f5c', // category/energeticos/900
				'cor_hero_meio'   => '#163184', // category/energeticos/700
				'cor_hero_fim'    => '#2f5bc8', // category/energeticos/500
				'cor_chip_clara'  => '#5a84e4', // category/energeticos/300
				'cor_chip_forte'  => '#163184', // category/energeticos/700
				'cor_media'       => '#2f5bc8', // category/energeticos/500
				'cor_destaque'    => '#163184', // color/category/energeticos → 700
				'cor_profunda'    => '#0a1240', // category/energeticos/950
			),
		),
		'refrigerantes' => array(
			'nome'  => 'Refrigerantes',
			'cores' => array(
				'cor_hero_inicio' => '#5e0b0b', // category/refrigerantes/900
				'cor_hero_meio'   => '#a51615', // category/refrigerantes/700
				'cor_hero_fim'    => '#e0484c', // category/refrigerantes/400
				'cor_chip_clara'  => '#f0646a', // category/refrigerantes/300
				'cor_chip_forte'  => '#a51615', // category/refrigerantes/700
				'cor_media'       => '#d52b30', // category/refrigerantes/500
				'cor_destaque'    => '#d52b30', // color/category/refrigerantes → 500
				'cor_profunda'    => '#5e0b0b', // category/refrigerantes/900 (sem 950)
			),
		),
		'guarana-mais' => array(
			'nome'  => 'Guaraná Mais',
			'cores' => array(
				'cor_hero_inicio' => '#3a0a45', // category/guarana-mais/900
				'cor_hero_meio'   => '#5c156b', // category/guarana-mais/700
				'cor_hero_fim'    => '#9a4db0', // category/guarana-mais/400
				'cor_chip_clara'  => '#b067c6', // category/guarana-mais/300
				'cor_chip_forte'  => '#5c156b', // category/guarana-mais/700
				'cor_media'       => '#7e2d96', // category/guarana-mais/500
				'cor_destaque'    => '#5c156b', // color/category/guarana-mais → 700
				'cor_profunda'    => '#24082e', // category/guarana-mais/950
			),
		),
	);

	$ids = array();

	foreach ($linhas as $slug => $linha) {
		$termo = get_term_by('slug', $slug, 'linha');

		if (!$termo) {
			$novo = wp_insert_term($linha['nome'], 'linha', array('slug' => $slug));

			if (is_wp_error($novo)) {
				continue;
			}

			$termo = get_term($novo['term_id'], 'linha');
		}

		$ids[$slug] = (int) $termo->term_id;

		foreach ($linha['cores'] as $campo => $hex) {
			if (!get_field($campo, 'term_' . $termo->term_id)) {
				update_field($campo, $hex, 'term_' . $termo->term_id);
			}
		}
	}

	return $ids;
}

/**
 * Páginas do menu do Figma. A Home usa o template page-home.php e vira a
 * página inicial do site (só se nenhuma página inicial estiver definida).
 *
 * @return array<string,int> chave => ID da página
 */
function entrerios_seed_paginas()
{
	$paginas = array(
		'home'             => array('titulo' => 'Home', 'slug' => 'home', 'template' => 'page-home.php'),
		'a-entre-rios'     => array('titulo' => 'A Entre Rios', 'slug' => 'a-entre-rios', 'template' => ''),
		'produtos'         => array('titulo' => 'Produtos', 'slug' => 'produtos', 'template' => ''),
		'sustentabilidade' => array('titulo' => 'Sustentabilidade', 'slug' => 'sustentabilidade', 'template' => ''),
		'contato'          => array('titulo' => 'Contato', 'slug' => 'contato', 'template' => 'page-contato.php'),
		'politica-de-privacidade' => array('titulo' => 'Política de Privacidade', 'slug' => 'politica-de-privacidade', 'template' => ''),
	);

	$ids = array();

	foreach ($paginas as $chave => $pagina) {
		$existente = get_page_by_path($pagina['slug'], OBJECT, 'page');

		if ($existente) {
			$ids[$chave] = (int) $existente->ID;
			continue;
		}

		$id = wp_insert_post(array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => $pagina['titulo'],
			'post_name'   => $pagina['slug'],
		));

		if (!$id || is_wp_error($id)) {
			continue;
		}

		if ($pagina['template']) {
			update_post_meta($id, '_wp_page_template', $pagina['template']);
		}

		$ids[$chave] = (int) $id;
	}

	if (!empty($ids['home']) && (get_option('show_on_front') !== 'page' || !get_option('page_on_front'))) {
		update_option('show_on_front', 'page');
		update_option('page_on_front', $ids['home']);
	}

	return $ids + array('home' => 0, 'produtos' => 0, 'a-entre-rios' => 0, 'politica-de-privacidade' => 0);
}

/**
 * Menu principal com os 5 itens do Figma, na localização "primary".
 *
 * @param array<string,int> $paginas
 * @return void
 */
function entrerios_seed_menu($paginas)
{
	$nome = 'Menu principal';
	$menu = wp_get_nav_menu_object($nome);

	if (!$menu) {
		$menu_id = wp_create_nav_menu($nome);

		if (is_wp_error($menu_id)) {
			return;
		}

		$ordem = 1;
		foreach (array('home', 'a-entre-rios', 'produtos', 'sustentabilidade', 'contato') as $chave) {
			if (empty($paginas[$chave])) {
				continue;
			}

			wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-object-id' => $paginas[$chave],
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $ordem++,
			));
		}
	} else {
		$menu_id = (int) $menu->term_id;
	}

	$locais = (array) get_theme_mod('nav_menu_locations', array());

	// Figma: o rodapé repete os 5 itens do menu principal.
	$mudou = false;
	foreach (array('primary', 'rodape') as $local) {
		if (empty($locais[$local])) {
			$locais[$local] = $menu_id;
			$mudou = true;
		}
	}

	if ($mudou) {
		set_theme_mod('nav_menu_locations', $locais);
	}
}

/**
 * Opções do Site: cabeçalho (logo, rótulo, imagem do menu) e contato.
 *
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_seed_opcoes($midia)
{
	$valores = array(
		'logo'           => $midia['logo-entre-rios'] ?? 0,
		'contato_rotulo' => 'Entre em Contato',
		'menu_imagem'    => $midia['hero-agua-mineral'] ?? 0,
		'telefone'       => '79 3015-1141',
		'email_contato'  => 'sac@aguaentrerios.com.br',
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, 'option')) {
			update_field($campo, $valor, 'option');
		}
	}

	if (!get_field('redes_sociais', 'option')) {
		update_field('field_aw_redes', array(
			array(
				'field_aw_rede_nome' => 'Instagram',
				'field_aw_rede_url'  => 'https://www.instagram.com/aguaentrerios/',
			),
		), 'option');
	}
}

/**
 * Hero da Home: 4 slides (um por linha) + palavra de fundo, botão circular e
 * intervalo — textos exatamente como no Figma (Hero Slider, Desktop).
 *
 * @param int               $home_id
 * @param array<string,int> $linhas
 * @param array<string,int> $midia
 * @param int               $produtos_id
 * @return void
 */
function entrerios_seed_hero($home_id, $linhas, $midia, $produtos_id)
{
	if (!$home_id) {
		return;
	}

	$link_produtos = $produtos_id ? array(
		'title'  => 'Conheça Nossos Produtos',
		'url'    => get_permalink($produtos_id),
		'target' => '',
	) : '';

	if (!get_field('hero_slides', $home_id)) {
		// No Figma os 4 variants do Hero Slider repetem título e texto da
		// Água Mineral — o seed reproduz o desenho; o cliente troca no admin.
		$titulo = 'ÁGUA MINERAL ENTRE RIOS';
		$texto  = 'Fundamental para sua saúde. Melhor para natureza e para o bem estar';

		$slides = array(
			array('agua-mineral', 'hero-agua-mineral', 'garrafa', 0),
			array('energeticos', 'hero-dub-2l', 'garrafa', 1),
			array('refrigerantes', 'hero-qtal-guarana-2l', 'garrafa', 1),
			array('guarana-mais', 'hero-guarana-mais-copo', 'copo', 1),
		);

		$linhas_repeater = array();

		foreach ($slides as $s) {
			list($linha, $imagem, $formato, $inclinado) = $s;

			if (empty($linhas[$linha]) || empty($midia[$imagem])) {
				continue;
			}

			$linhas_repeater[] = array(
				'field_er_hero_slide_linha'     => $linhas[$linha],
				'field_er_hero_slide_formato'   => $formato,
				'field_er_hero_slide_inclinado' => $inclinado,
				'field_er_hero_slide_titulo'    => $titulo,
				'field_er_hero_slide_texto'     => $texto,
				'field_er_hero_slide_imagem'    => $midia[$imagem],
				'field_er_hero_slide_botao'     => $link_produtos,
			);
		}

		update_field('field_er_hero_slides', $linhas_repeater, $home_id);
	}

	if (!get_field('hero_palavra_fundo', $home_id)) {
		update_field('field_er_hero_palavra_fundo', 'ENTRE RIOS', $home_id);
	}

	if (!get_field('hero_cta_circular', $home_id) && $link_produtos) {
		update_field('field_er_hero_cta_circular', $link_produtos, $home_id);
	}

	if ('' === (string) get_field('hero_intervalo', $home_id)) {
		update_field('field_er_hero_intervalo', 5, $home_id);
	}
}

/**
 * Catálogo inicial — os 27 cards da vitrine do Figma (Produtos / Vitrine,
 * variants por categoria), na mesma ordem. Fonte: produtos.json.
 *
 * @return array<int,array{slug:string,linha:string,titulo:string}>
 */
function entrerios_lista_produtos()
{
	$lista = json_decode((string) file_get_contents(ENTRERIOS_BOOTSTRAP_DIR . '/produtos.json'), true);

	return is_array($lista) ? $lista : array();
}

/**
 * Cria cada produto (CPT "produto") com a foto como imagem destacada, a linha
 * e a ordem do card. Produto já existente (mesmo slug) não é tocado.
 *
 * @param array<string,int> $linhas
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_seed_produtos($linhas, $midia)
{
	if (!post_type_exists('produto')) {
		return;
	}

	$ordem = array();

	foreach (entrerios_lista_produtos() as $produto) {
		$linha = $produto['linha'];
		$ordem[$linha] = isset($ordem[$linha]) ? $ordem[$linha] + 1 : 1;

		// get_page_by_path() também casa anexos de mesmo slug — aqui não serve.
		$existe = get_posts(array(
			'post_type'      => 'produto',
			'name'           => $produto['slug'],
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		));

		if ($existe || empty($linhas[$linha])) {
			continue;
		}

		$id = wp_insert_post(array(
			'post_type'   => 'produto',
			'post_status' => 'publish',
			'post_title'  => $produto['titulo'],
			'post_name'   => $produto['slug'],
			'menu_order'  => $ordem[$linha],
		));

		if (!$id || is_wp_error($id)) {
			continue;
		}

		wp_set_object_terms($id, array($linhas[$linha]), 'linha');

		$foto = $midia['produto-' . $produto['slug']] ?? 0;
		if ($foto) {
			set_post_thumbnail($id, $foto);
		}
	}
}

/**
 * Letreiro e vitrine da Home — textos do Figma e as 4 linhas na ordem das
 * abas (Água Mineral, Energéticos, Refrigerantes, Guaraná Mais).
 *
 * @param int               $home_id
 * @param array<string,int> $linhas
 * @param int               $produtos_id
 * @return void
 */
function entrerios_seed_vitrine($home_id, $linhas, $produtos_id)
{
	if (!$home_id) {
		return;
	}

	if (!get_field('marquee_texto', $home_id)) {
		update_field('field_er_marquee_texto', 'Conheça Nossos Produtos', $home_id);
	}

	if (!get_field('vitrine_rotulo', $home_id)) {
		update_field('field_er_vitrine_rotulo', 'Produtos', $home_id);
	}

	if (!get_field('vitrine_linhas', $home_id)) {
		$ids = array_values(array_filter(array(
			$linhas['agua-mineral'] ?? 0,
			$linhas['energeticos'] ?? 0,
			$linhas['refrigerantes'] ?? 0,
			$linhas['guarana-mais'] ?? 0,
		)));
		update_field('field_er_vitrine_linhas', $ids, $home_id);
	}

	if (!get_field('vitrine_ver_todos', $home_id) && $produtos_id) {
		update_field('field_er_vitrine_ver_todos', array(
			'title'  => 'Ver todos',
			'url'    => get_permalink($produtos_id),
			'target' => '',
		), $home_id);
	}
}

/**
 * ID de um produto pelo slug (0 se não existir).
 *
 * @param string $slug
 * @return int
 */
function entrerios_produto_id($slug)
{
	$ids = get_posts(array(
		'post_type'      => 'produto',
		'name'           => $slug,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	));

	return $ids ? (int) $ids[0] : 0;
}

/**
 * Link do ACF para uma página do seed.
 *
 * @param int    $pagina_id
 * @param string $titulo
 * @return array|string
 */
function entrerios_link($pagina_id, $titulo)
{
	return $pagina_id ? array('title' => $titulo, 'url' => get_permalink($pagina_id), 'target' => '') : '';
}

/**
 * Linhas de Bebidas (bento) — os 4 Linha Cards do Figma, na ordem do Figma,
 * com os produtos das fotos (slots da esquerda para a direita).
 *
 * @param int               $home_id
 * @param array<string,int> $linhas
 * @param int               $produtos_id
 * @return void
 */
function entrerios_seed_linhas_cards($home_id, $linhas, $produtos_id)
{
	if (!$home_id || get_field('linhas_cards', $home_id)) {
		return;
	}

	$cards = array(
		'agua-mineral'  => array(array('garrafa-500ml-sem-gas', 'm'), array('garrafao-20-litros', 'g'), array('garrafa-1-5-litros', 'm')),
		'energeticos'   => array(array('dub-lata-269ml', 'p'), array('dub-1-litro', 'g'), array('dub-2-litros', 'g')),
		'refrigerantes' => array(array('qtal-fresh-cola-2l', 'm'), array('qtal-fresh-guarana-2l', 'm'), array('qtal-fresh-laranja-2l', 'm')),
		'guarana-mais'  => array(array('copo-guarana-mais-acai-290ml', 'g')),
	);

	$linhas_repeater = array();

	foreach ($cards as $linha => $produtos) {
		if (empty($linhas[$linha])) {
			continue;
		}

		$itens = array();
		foreach ($produtos as $p) {
			$id = entrerios_produto_id($p[0]);
			if ($id) {
				$itens[] = array('field_er_linhas_card_produto' => $id, 'field_er_linhas_card_tamanho' => $p[1]);
			}
		}

		$linhas_repeater[] = array(
			'field_er_linhas_card_linha'    => $linhas[$linha],
			'field_er_linhas_card_link'     => entrerios_link($produtos_id, 'Produtos'),
			'field_er_linhas_card_produtos' => $itens,
		);
	}

	update_field('field_er_linhas_cards', $linhas_repeater, $home_id);
}

/**
 * Produto 360° — as 3 variantes do componente do Figma (Guaraná Mais,
 * DUB 2 Litros, DUB Lata 269ml), com textos, destaques e cores do Figma.
 *
 * @param int               $home_id
 * @param array<string,int> $linhas
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_seed_produto_360($home_id, $linhas, $midia)
{
	if (!$home_id) {
		return;
	}

	if ('' === (string) get_field('p360_dica', $home_id)) {
		update_field('field_er_p360_dica', '0° → 360°', $home_id);
	}

	if (get_field('p360_itens', $home_id)) {
		return;
	}

	$produzido = array('field_er_p360_destaque_rotulo' => 'PRODUZIDO COM', 'field_er_p360_destaque_valor' => 'Água mineral');
	$dub_texto = 'Composto líquido pronto para o consumo à base de taurina e cafeína.';

	$itens = array(
		array('guarana-mais', 'p360-guarana-mais-v2', 'Guaraná Mais', 'GUARANÁ MAIS', 'Guaraná Mais Açaí',
			"Refresco misto de guaraná e açaí adoçado.\nConteúdo 290mL · Produzido com água mineral.", '290mL', '#b65ad6', '#c58bdd'),
		array('energeticos', 'p360-dub-2-litros', 'DUB 2 Litros', 'ENERGÉTICOS', 'DUB 2 Litros', $dub_texto, '2L', '#2fb6dd', '#7fb3ff'),
		array('energeticos', 'p360-dub-lata-269ml', 'DUB Lata 269ml', 'ENERGÉTICOS', 'DUB Lata 269ml', $dub_texto, '269ml', '#2fb6dd', '#7fb3ff'),
	);

	$linhas_repeater = array();

	foreach ($itens as $i) {
		list($linha, $prefixo, $rotulo, $sobretitulo, $titulo, $texto, $conteudo, $brilho, $orbita) = $i;

		$vistas = array();
		for ($n = 1; $n <= 72; $n++) {
			$nome = sprintf('%s-%02d', $prefixo, $n);
			if (!empty($midia[$nome])) {
				$vistas[] = $midia[$nome];
			}
		}

		if (!$vistas) {
			continue;
		}

		$linhas_repeater[] = array(
			'field_er_p360_rotulo'      => $rotulo,
			'field_er_p360_linha'       => $linhas[$linha] ?? '',
			'field_er_p360_sobretitulo' => $sobretitulo,
			'field_er_p360_titulo'      => $titulo,
			'field_er_p360_texto'       => $texto,
			'field_er_p360_vistas'      => $vistas,
			'field_er_p360_destaques'   => array(
				array('field_er_p360_destaque_rotulo' => 'CONTEÚDO', 'field_er_p360_destaque_valor' => $conteudo),
				$produzido,
			),
			'field_er_p360_cor_brilho'  => $brilho,
			'field_er_p360_cor_orbita'  => $orbita,
		);
	}

	update_field('field_er_p360_itens', $linhas_repeater, $home_id);
}

/**
 * Sobre a Entre Rios — textos do Figma (Home), vídeo da pasta "Fotos
 * recentes da Fábrica" e a capa (quadro do próprio vídeo).
 *
 * @param int               $home_id
 * @param array<string,int> $midia
 * @param int               $sobre_id Página "A Entre Rios".
 * @return void
 */
function entrerios_seed_sobre($home_id, $midia, $sobre_id)
{
	if (!$home_id) {
		return;
	}

	$valores = array(
		'sobre_rotulo'   => 'A ENTRE RIOS',
		'sobre_titulo'   => 'Sobre a Entre Rios',
		'sobre_destaque' => 'Água Mineral Entre Rios nasceu em 2002, através do processo DNPM 878.053/2002, sendo outorgada a concessão de Lavra em Nº 233 em julho/2006 para as Fontes Entre Rios e São Luiz, no município de Estância.',
		'sobre_texto'    => 'Atualmente, a empresa envasa e comercializa água mineral em embalagens Descartáveis, Garrafões de 20 litros retornáveis, Guaraná com açai e giseng, Energéticos, além do grande lançamento de Refrigerantes no sabores Cola, Guaraná e Laranja.',
		'sobre_capa'     => $midia['sobre-video-capa'] ?? 0,
		'sobre_video'    => $midia['sobre-video'] ?? 0,
		'sobre_botao'    => entrerios_link($sobre_id, 'Saiba Mais sobre a Entre Rios'),
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, $home_id)) {
			update_field('field_er_' . $campo, $valor, $home_id);
		}
	}
}

/**
 * Sustentabilidade — textos do Figma (Home): cabeçalho e os 3 pilares.
 *
 * @param int $home_id
 * @return void
 */
function entrerios_seed_sustentabilidade($home_id)
{
	if (!$home_id) {
		return;
	}

	$valores = array(
		'sust_rotulo' => 'SUSTENTABILIDADE',
		'sust_titulo' => 'Sustentabilidade',
		'sust_texto'  => 'Tendo em mente as preocupações ambientais que venham garantir o bem estar de nossas futuras gerações, preservando o planeta, as cidades e os lares, a ENTRE RIOS desenvolve suas atividades industriais seguindo os mais estritos métodos de preservação ambiental e perfeitamente atualizadas com novas tecnologias e a legislação pertinente.',
	);

	foreach ($valores as $campo => $valor) {
		if (!get_field($campo, $home_id)) {
			update_field('field_er_' . $campo, $valor, $home_id);
		}
	}

	if (get_field('sust_pilares', $home_id)) {
		return;
	}

	$pilares = array(
		array('gota', 'MINAS', 'Localizadas à 60 metros de profundidade, nossas minas estão blindadas de quaisquer possibilidades de focos de contaminação e sofrem um acompanhamento físico e análises sistemáticas executadas por laboratórios renomados, assegurando ao consumidor final um produto com excelente nível de qualidade e segurança.'),
		array('garrafa', 'EMBALAGENS', 'Dentro de nossas instalações as embalagens passam por rigoroso processo de esterilização ao circularem pelos equipamentos que abastecem cada unidade que conterá nossos produtos, e utiliza produtos descartáveis e não descartáveis de procedências que nos asseguram o mesmo padrão de qualidade, portanto seguindo normas da Associação Brasileira de Normas Técnicas – ABNT.'),
		array('folha', 'MATÉRIA-PRIMA', 'As essências e matérias-primas de compõem nossa linha de bebidas são oriundas de processos de excelência do período de pós-colheita assegurando assim o paladar característico de cada SABOR, bem como mantendo os altos padrões de exigência de nossos clientes.'),
	);

	update_field('field_er_sust_pilares', array_map(static function ($p) {
		return array(
			'field_er_sust_pilar_icone'  => $p[0],
			'field_er_sust_pilar_titulo' => $p[1],
			'field_er_sust_pilar_texto'  => $p[2],
		);
	}, $pilares), $home_id);
}

/**
 * Instagram — @perfil do Figma e 4 posts (composições com as fotos dos
 * produtos sobre o gradiente de cada linha, no lugar das fotos do Figma).
 *
 * @param int               $home_id
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_seed_instagram($home_id, $midia)
{
	if (!$home_id) {
		return;
	}

	$valores = array(
		'insta_rotulo'  => 'Siga-nos no Instagram',
		'insta_usuario' => '@aguaentrerios',
		'insta_url'     => 'https://www.instagram.com/aguaentrerios/',
	);

	foreach ($valores as $campo => $valor) {
		if (!get_field($campo, $home_id)) {
			update_field('field_er_' . $campo, $valor, $home_id);
		}
	}

	if (get_field('insta_posts', $home_id)) {
		return;
	}

	$posts = array();
	foreach (array('agua', 'energeticos', 'refrigerantes', 'guarana-mais') as $slug) {
		if (!empty($midia['instagram-' . $slug])) {
			$posts[] = array('field_er_insta_post_imagem' => $midia['instagram-' . $slug], 'field_er_insta_post_link' => '');
		}
	}

	update_field('field_er_insta_posts', $posts, $home_id);
}

/**
 * Rodapé (Opções do Site) — textos do Figma.
 *
 * @param int $politica_id
 * @return void
 */
function entrerios_seed_rodape($politica_id)
{
	$valores = array(
		'rodape_titulo'        => 'Água Mineral Entre Rios',
		'endereco'             => 'Sitio São Luiz, S/N Povoado Grotão Zona Rural, Estância - SE, CEP 49200-000',
		'rodape_social_rotulo' => 'Siga-nos no Instagram',
		'rodape_social'        => array('title' => '@aguaentrerios', 'url' => 'https://www.instagram.com/aguaentrerios/', 'target' => '_blank'),
		'copyright'            => 'Todos os direitos reservados a Entre Rios',
		'rodape_politica'      => entrerios_link($politica_id, 'Política de Privacidade'),
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, 'option')) {
			update_field($campo, $valor, 'option');
		}
	}
}

/**
 * v4 — Guaraná Mais no Produto 360°: troca as 4 fotos por 70 quadros do giro
 * completo (vídeo do copo girando, fundo removido, mesmo recorte em todos).
 *
 * Só troca se a galeria ainda for a do seed v3 (as 4 fotos, na mesma ordem):
 * se o cliente já mexeu nela, não toca.
 *
 * @param int               $home_id
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_atualizar_giro_guarana($home_id, $midia)
{
	if (!$home_id) {
		return;
	}

	$antigas = array();
	for ($n = 1; $n <= 4; $n++) {
		$antigas[] = (int) ($midia[sprintf('p360-guarana-mais-%02d', $n)] ?? 0);
	}

	$novas = array();
	for ($n = 1; $n <= 72; $n++) {
		$nome = sprintf('p360-guarana-mais-v2-%02d', $n);
		if (!empty($midia[$nome])) {
			$novas[] = (int) $midia[$nome];
		}
	}

	$itens = get_field('p360_itens', $home_id);

	if (!$novas || !is_array($itens) || in_array(0, $antigas, true)) {
		return;
	}

	$mudou = false;

	foreach ($itens as $i => $item) {
		$vistas = array_map('intval', (array) ($item['vistas'] ?? array()));

		if ($vistas === $antigas) {
			$itens[$i]['vistas'] = $novas;
			$mudou = true;
		}
	}

	if ($mudou) {
		// Repeater regravado inteiro (o ACF aceita as linhas pelo nome dos subcampos).
		update_field('field_er_p360_itens', $itens, $home_id);
	}
}

/**
 * Página A Entre Rios (Figma: Screen/About/Desktop). Liga o template
 * page-a-entre-rios.php e grava os textos e imagens do Figma — só em campo
 * vazio, como os outros seeds.
 *
 * Textos copiados do Figma como estão (inclusive "Para o futuro" sem ponto).
 *
 * @param int              $pagina_id
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_seed_a_entre_rios($pagina_id, $midia)
{
	if (!$pagina_id) {
		return;
	}

	if (!get_post_meta($pagina_id, '_wp_page_template', true) || 'default' === get_post_meta($pagina_id, '_wp_page_template', true)) {
		update_post_meta($pagina_id, '_wp_page_template', 'page-a-entre-rios.php');
	}

	$valores = array(
		'ae_hero_imagem' => $midia['sobre-fabrica-drone-01'] ?? 0,
		'ae_hero_rotulo' => 'ENTRE RIOS',
		'ae_hero_titulo' => 'Uma história que nasce da nossa terra e cresce com Sergipe.',

		'ae_origem_titulo'      => 'Tudo começou com uma fonte.',
		'ae_origem_subtitulo'   => 'Mas nunca foi apenas sobre água.',
		'ae_origem_texto'       => 'Em 2002, nascia a história da Água Mineral Entre Rios, a partir do processo DNPM nº 878.053/2002. Em julho de 2006, a concessão de Lavra nº 233 consolidou um novo capítulo dessa trajetória, contemplando as fontes Entre Rios e São Luiz, no município de Estância, Sergipe.',
		'ae_origem_destaque'    => 'Ali começava muito mais do que uma indústria.',
		'ae_origem_texto_final' => 'Começava um projeto construído com trabalho, dedicação e a vontade de levar qualidade para milhares de famílias.',

		'ae_caminhos_titulo'    => 'Da água para novos caminhos',
		'ae_caminhos_subtitulo' => 'O tempo passou. A fábrica cresceu. Novas ideias surgiram.',
		'ae_caminhos_destaque'  => 'Representa a nossa vontade de evoluir.',
		'ae_colagem_centro'          => $midia['sobre-colagem-garrafa-1-5l'] ?? 0,
		'ae_colagem_esquerda'        => $midia['sobre-colagem-dub-2l'] ?? 0,
		'ae_colagem_direita'         => $midia['sobre-colagem-qtal-guarana-2l'] ?? 0,
		'ae_colagem_esquerda_frente' => $midia['sobre-colagem-lata-350ml'] ?? 0,
		'ae_colagem_direita_frente'  => $midia['sobre-colagem-copo-guarana-mais'] ?? 0,

		'ae_fabrica_video'          => $midia['sobre-video'] ?? 0,
		'ae_fabrica_previa'         => $midia['sobre-video-previa'] ?? 0,
		'ae_fabrica_capa'           => $midia['sobre-fabrica-video-capa'] ?? 0,
		'ae_fabrica_titulo'         => 'Uma fábrica em transformação',
		'ae_fabrica_subtitulo'      => 'Crescer também significa mudar.',
		'ae_fabrica_citacao'        => 'Porque inovação, para nós, não significa abandonar nossa história.',
		'ae_fabrica_citacao_texto'  => 'Significa honrá-la enquanto construímos o próximo capítulo.',

		'ae_galeria_rotulo'  => 'GALERIA',
		'ae_galeria_titulo'  => 'Entre Rios',
		'ae_galeria_imagens' => array_values(array_filter(array($midia['sobre-fabrica-drone-01'] ?? 0, $midia['sobre-fabrica-drone-02'] ?? 0))),

		'ae_pessoas_titulo'    => 'Pessoas que fazem acontecer',
		'ae_pessoas_subtitulo' => 'Nenhuma máquina substitui aquilo que sempre foi a maior força da Entre Rios: As pessoas.',
		'ae_pessoas_texto'     => "Por trás de cada garrafa, cada lata, cada produto e cada conquista existe uma equipe que trabalha diariamente para fazer a empresa crescer.\n\nSão colaboradores, parceiros, fornecedores, clientes e consumidores que fazem parte dessa trajetória.",
		'ae_pessoas_destaque'  => 'Cada um ajudou e continua ajudando a construir a Entre Rios.',

		'ae_futuro_titulo'    => 'O futuro já começou',
		'ae_futuro_subtitulo' => 'Temos orgulho de olhar para tudo o que construímos.',
		'ae_futuro_texto'     => "Mas temos ainda mais entusiasmo quando pensamos no que está por vir.\n\nEstamos investindo, modernizando, ampliando e preparando nossa indústria para alcançar novos mercados e estar presente em cada vez mais momentos da vida dos nossos consumidores.",

		'ae_assinatura_palavra' => 'ENTRE RIOS',
		'ae_assinatura_frase'   => "Da nossa terra.\nDa nossa gente.\nPara o futuro",
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, $pagina_id)) {
			update_field('field_er_' . $campo, $valor, $pagina_id);
		}
	}

	$repetidores = array(
		'ae_origem_marcos' => array(
			array('field_er_ae_origem_marco_ano' => '2002'),
			array('field_er_ae_origem_marco_ano' => '2006'),
		),
		'ae_caminhos_blocos' => entrerios_blocos(array(
			array('paragrafo', 'E aquilo que começou com a água mineral se transformou em uma indústria de bebidas cada vez mais completa.'),
			array('paragrafo', 'Hoje, a Entre Rios envasa e comercializa água mineral em garrafas PET, latas e garrafões retornáveis de 20 litros, mantendo viva a essência que deu origem à nossa história.'),
			array('intertitulo', 'Mas decidimos ir além.'),
			array('paragrafo', 'Ampliamos nossa produção e levamos nossa experiência para novas categorias, com Guaraná com Açaí, energéticos e refrigerantes nos sabores Cola, Guaraná, Laranja, Uva e Tubaína.'),
			array('paragrafo', 'Cada novo produto representa mais do que uma expansão do nosso portfólio.'),
		), 'ae_caminhos'),
		'ae_fabrica_blocos' => entrerios_blocos(array(
			array('paragrafo', 'Novas tecnologias, novos equipamentos, novos processos, novos produtos e uma equipe preparada para os desafios de uma indústria que não para de evoluir.'),
			array('intertitulo', 'A Entre Rios vive um dos momentos mais importantes de sua história.'),
			array('paragrafo', 'Estamos construindo uma fábrica cada vez mais moderna, eficiente e preparada para o futuro, sem esquecer as raízes que nos trouxeram até aqui.'),
		), 'ae_fabrica'),
		'ae_futuro_manifesto' => array(
			array('field_er_ae_futuro_frase' => 'Nascemos em Sergipe.', 'field_er_ae_futuro_frase_destaque' => 0),
			array('field_er_ae_futuro_frase' => 'Crescemos com a nossa gente.', 'field_er_ae_futuro_frase_destaque' => 0),
			array('field_er_ae_futuro_frase' => 'E estamos apenas começando.', 'field_er_ae_futuro_frase_destaque' => 1),
		),
	);

	foreach ($repetidores as $campo => $linhas) {
		if (!get_field($campo, $pagina_id)) {
			update_field('field_er_' . $campo, $linhas, $pagina_id);
		}
	}
}

/**
 * Linhas do repetidor "Texto" (tipo + texto) no formato de update_field().
 *
 * @param array<int,array{0:string,1:string}> $blocos
 * @param string                             $prefixo ae_caminhos | ae_fabrica
 * @return array<int,array<string,string>>
 */
function entrerios_blocos($blocos, $prefixo)
{
	return array_map(static function ($b) use ($prefixo) {
		return array(
			'field_er_' . $prefixo . '_bloco_tipo'  => $b[0],
			'field_er_' . $prefixo . '_bloco_texto' => $b[1],
		);
	}, $blocos);
}

/**
 * Catálogo (v6): página Produtos (template, textos e fotos do topo) e a ficha
 * de cada produto — embalagem, tipo, tamanho, sabor, versões, ordem global e
 * a sanfona dos produtos que o Figma desenha (Screen/Product/<linha>).
 *
 * Como o resto do seed: só grava o que está vazio. A ordem global só troca a
 * ordem que o próprio seed gravou (posição dentro da linha).
 *
 * @param array<string,int> $paginas
 * @param array<string,int> $linhas
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_seed_catalogo($paginas, $linhas, $midia)
{
	if (!post_type_exists('produto') || !taxonomy_exists('embalagem') || !taxonomy_exists('tipo_produto')) {
		return;
	}

	/* --- Termos dos filtros (na ordem do Figma) --------------------------- */
	$termos = array(
		'embalagem'    => array('copo' => 'Copo', 'garrafa' => 'Garrafa', 'lata' => 'Lata', 'garrafao-galao' => 'Garrafão / Galão'),
		'tipo_produto' => array('com-gas' => 'Com gás', 'sem-gas' => 'Sem gás'),
	);

	foreach ($termos as $taxonomia => $lista) {
		foreach ($lista as $slug => $nome) {
			if (!term_exists($slug, $taxonomia)) {
				wp_insert_term($nome, $taxonomia, array('slug' => $slug));
			}
		}
	}

	/* --- Ficha: slug => embalagem, tipo, tamanho, sabor, família ---------- */
	$fichas = array(
		'copo-200ml'                      => array('copo', 'sem-gas', '200ml', '', 'agua-copo'),
		'copo-295ml'                      => array('copo', 'sem-gas', '295ml', '', 'agua-copo'),
		'garrafa-330ml-sem-gas'           => array('garrafa', 'sem-gas', '330ml', '', 'agua-garrafa'),
		'garrafa-330ml-com-gas'           => array('garrafa', 'com-gas', '330ml', '', 'agua-garrafa'),
		'garrafa-500ml-sem-gas'           => array('garrafa', 'sem-gas', '500ml', '', 'agua-garrafa'),
		'garrafa-500ml-com-gas'           => array('garrafa', 'com-gas', '500ml', '', 'agua-garrafa'),
		'garrafa-1-5-litros'              => array('garrafa', 'sem-gas', '1,5L', '', 'agua-garrafa'),
		'agua-mineral-lata-350ml-sem-gas' => array('lata', 'sem-gas', '350ml', '', 'agua-lata'),
		'agua-mineral-lata-350ml-com-gas' => array('lata', 'com-gas', '350ml', '', 'agua-lata'),
		'agua-mineral-5-litros'           => array('garrafao-galao', 'sem-gas', '5L', '', 'agua-galao'),
		'garrafao-20-litros'              => array('garrafao-galao', 'sem-gas', '20L', '', 'agua-galao'),
		'dub-lata-269ml'                  => array('lata', '', 'Lata 269ml', '', 'dub'),
		'dub-1-litro'                     => array('garrafa', '', '1 Litro', '', 'dub'),
		'dub-2-litros'                    => array('garrafa', '', '2 Litros', '', 'dub'),
		'qtal-fresh-cola-lata-350ml'      => array('lata', '', 'Lata 350ml', 'Cola', 'qtal-lata'),
		'qtal-fresh-guarana-lata-350ml'   => array('lata', '', 'Lata 350ml', 'Guaraná', 'qtal-lata'),
		'qtal-fresh-cola-250ml'           => array('garrafa', '', '250ml', 'Cola', 'qtal-cola'),
		'qtal-fresh-cola-2l'              => array('garrafa', '', '2L', 'Cola', 'qtal-cola'),
		'qtal-fresh-guarana-250ml'        => array('garrafa', '', '250ml', 'Guaraná', 'qtal-guarana'),
		'qtal-fresh-guarana-2l'           => array('garrafa', '', '2L', 'Guaraná', 'qtal-guarana'),
		'qtal-fresh-laranja-250ml'        => array('garrafa', '', '250ml', 'Laranja', 'qtal-laranja'),
		'qtal-fresh-laranja-2l'           => array('garrafa', '', '2L', 'Laranja', 'qtal-laranja'),
		'qtal-fresh-uva-250ml'            => array('garrafa', '', '250ml', 'Uva', 'qtal-uva'),
		'qtal-fresh-uva-2l'               => array('garrafa', '', '2L', 'Uva', 'qtal-uva'),
		'qtal-fresh-tubaina-1l'           => array('garrafa', '', '1L', 'Tubaína', 'qtal-tubaina'),
		'qtal-fresh-tubaina-2l'           => array('garrafa', '', '2L', 'Tubaína', 'qtal-tubaina'),
		'copo-guarana-mais-acai-290ml'    => array('copo', '', '290ml', '', ''),
	);

	$ids     = array();
	$familia = array();

	foreach ($fichas as $slug => $ficha) {
		$id = entrerios_produto_id($slug);

		if (!$id) {
			continue;
		}

		$ids[$slug] = $id;
		list($embalagem, $tipo, $tamanho, $sabor, $chave) = $ficha;

		if ($embalagem && !wp_get_object_terms($id, 'embalagem', array('fields' => 'ids'))) {
			wp_set_object_terms($id, array($embalagem), 'embalagem');
		}

		if ($tipo && !wp_get_object_terms($id, 'tipo_produto', array('fields' => 'ids'))) {
			wp_set_object_terms($id, array($tipo), 'tipo_produto');
		}

		if ($tamanho && !get_field('produto_tamanho', $id)) {
			update_field('field_er_produto_tamanho', $tamanho, $id);
		}

		if ($sabor && !get_field('produto_sabor', $id)) {
			update_field('field_er_produto_sabor', $sabor, $id);
		}

		if ($chave) {
			$familia[$chave][] = $id;
		}
	}

	// Versões: cada produto aponta para os outros da mesma família.
	foreach ($familia as $membros) {
		foreach ($membros as $id) {
			$outros = array_values(array_diff($membros, array($id)));

			if ($outros && !get_field('produto_versoes', $id)) {
				update_field('field_er_produto_versoes', $outros, $id);
			}
		}
	}

	/* --- Ordem global (a página Produtos lista tudo junto) ---------------- */
	$posicao_na_linha = array();

	foreach (entrerios_lista_produtos() as $i => $produto) {
		$linha = $produto['linha'];
		$posicao_na_linha[$linha] = isset($posicao_na_linha[$linha]) ? $posicao_na_linha[$linha] + 1 : 1;
		$id = $ids[$produto['slug']] ?? entrerios_produto_id($produto['slug']);

		if ($id && (int) get_post_field('menu_order', $id) === $posicao_na_linha[$linha]) {
			wp_update_post(array('ID' => $id, 'menu_order' => $i + 1));
		}
	}

	/* --- Sanfona (textos do Figma: Screen/Product/<linha>) ---------------- */
	$sanfonas = array(
		'garrafa-500ml-com-gas' => array(
			array('Composição', 'Água mineral natural gaseificada artificialmente (com adição de gás carbônico), captada nas Fontes Entre Rios e São Luiz, em Estância – SE. Composição química e características físico-químicas conforme laudo de análise impresso no rótulo.'),
			array('Informações', 'Conteúdo: 500 mL. Embalagem: garrafa PET com tampa vermelha (versão com gás). Conservar ao abrigo do sol e calor, em local limpo, seco e arejado. Após aberto, consumir imediatamente.'),
			array('Release', 'Leve e refrescante, a Água Mineral Entre Rios Com Gás leva a pureza das nossas fontes com o toque das bolhinhas que deixam qualquer momento mais especial. Ideal para acompanhar as refeições ou para quem busca uma alternativa leve aos refrigerantes.'),
		),
		'qtal-fresh-guarana-2l' => array(
			array('Composição', "Refrigerante sabor guaraná Q'tal Fresh. Lista de ingredientes e informação nutricional conforme impresso no rótulo do produto."),
			array('Informações', 'Conteúdo: 2 L. Embalagem: garrafa PET. Servir gelado. Após aberto, manter bem fechado e refrigerado. Conservar ao abrigo do sol e calor.'),
			array('Release', "A linha Q'tal Fresh reúne os sabores Cola, Guaraná, Laranja, Uva e Tubaína em embalagens de 250 mL, 350 mL (lata), 1 L e 2 L. O Q'tal Fresh Guaraná é refrescante e ideal para reunir a família e os amigos à mesa."),
		),
		'dub-lata-269ml' => array(
			array('Composição', 'Bebida energética gaseificada DUB Life. Lista de ingredientes e informação nutricional conforme impresso no rótulo do produto.'),
		),
	);

	foreach ($sanfonas as $slug => $itens) {
		$id = $ids[$slug] ?? 0;

		if ($id && !get_field('produto_sanfona', $id)) {
			update_field('field_er_produto_sanfona', array_map(static function ($item) {
				return array(
					'field_er_produto_sanfona_titulo' => $item[0],
					'field_er_produto_sanfona_texto'  => $item[1],
				);
			}, $itens), $id);
		}
	}

	/* --- Página Produtos -------------------------------------------------- */
	$pagina_id = $paginas['produtos'] ?? 0;

	if (!$pagina_id) {
		return;
	}

	$template = get_post_meta($pagina_id, '_wp_page_template', true);
	if (!$template || 'default' === $template) {
		update_post_meta($pagina_id, '_wp_page_template', 'page-produtos.php');
	}

	$valores = array(
		'prod_hero_titulo' => 'Conheça Nossos Produtos',
		'prod_mais_texto'  => 'Carregar mais produtos',
		'prod_rel_titulo'  => 'Produtos relacionados',
		'prod_rel_link'    => 'Ver todos os produtos',
		'prod_cta'         => entrerios_link($paginas['contato'] ?? 0, 'Fale Conosco'),
		// Figma: lata DUB, garrafa 1,5 L, Q'tal Cola 2 L e copo Guaraná Mais.
		'prod_hero_imagens' => array_values(array_filter(array(
			$midia['produto-dub-lata-269ml'] ?? 0,
			$midia['produto-garrafa-1-5-litros'] ?? 0,
			$midia['produto-qtal-fresh-cola-2l'] ?? 0,
			$midia['produto-copo-guarana-mais-acai-290ml'] ?? 0,
		))),
		'prod_linhas' => array_values(array_filter(array(
			$linhas['agua-mineral'] ?? 0,
			$linhas['energeticos'] ?? 0,
			$linhas['refrigerantes'] ?? 0,
			$linhas['guarana-mais'] ?? 0,
		))),
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, $pagina_id)) {
			update_field('field_er_' . $campo, $valor, $pagina_id);
		}
	}

	if (class_exists('AW_Query_Service')) {
		AW_Query_Service::limpar_cache();
	}
}

/**
 * v7 — Página Contato (Figma: Screen/Contact): textos do formulário, do card
 * de contatos e o mapa do Google. Endereço, telefone e e-mail já estão nas
 * Opções do Site (v1/v3). Só grava campo vazio.
 *
 * @param int $pagina_id
 * @return void
 */
function entrerios_seed_contato($pagina_id)
{
	if (!$pagina_id) {
		return;
	}

	$template = get_post_meta($pagina_id, '_wp_page_template', true);
	if (!$template || 'default' === $template) {
		update_post_meta($pagina_id, '_wp_page_template', 'page-contato.php');
	}

	$local = rawurlencode('Água Mineral Entre Rios, Estância - SE');

	$valores = array(
		'contato_hero_titulo' => 'Fale Conosco',
		'contato_form_titulo' => 'Nos envie uma mensagem',
		'contato_form_texto'  => 'Entre em contato conosco! Preencha o formulário abaixo e nossa equipe retornará em breve.',
		'contato_form_botao'  => 'Enviar Mensagem',
		// Fora do Figma: aceite exigido pelo formulário "contato" (LGPD).
		'contato_form_aceite' => 'Li e aceito a Política de Privacidade.',
		'contato_info_titulo' => 'Nossos Contatos',
		// Vista de satélite (t=k), como no Figma.
		'contato_mapa_embed'  => 'https://www.google.com/maps?q=' . $local . '&t=k&z=16&output=embed',
		'contato_mapa_link'   => array(
			'title'  => 'Abrir no Google Maps',
			'url'    => 'https://www.google.com/maps/search/?api=1&query=' . $local,
			'target' => '_blank',
		),
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, $pagina_id)) {
			update_field('field_er_' . $campo, $valor, $pagina_id);
		}
	}
}

/**
 * v7/v8 — Política de Privacidade (Figma: Screen/Privacy-Policy): publica a
 * página (v8), liga o template, grava o topo (com a data do Figma) e as 10 seções. Só grava
 * campo vazio — o texto jurídico é do cliente depois do primeiro seed.
 *
 * @param int $pagina_id
 * @return void
 */
function entrerios_seed_politica($pagina_id)
{
	if (!$pagina_id) {
		return;
	}

	$template = get_post_meta($pagina_id, '_wp_page_template', true);
	if (!$template || 'default' === $template) {
		update_post_meta($pagina_id, '_wp_page_template', 'page-politica-de-privacidade.php');
	}

	// v8 — a página "politica-de-privacidade" encontrada pelo seed pode ser
	// o rascunho padrão do WordPress (Configurações → Privacidade): publica,
	// senão o link do rodapé e o aceite do formulário dão 404.
	if ('publish' !== get_post_status($pagina_id)) {
		wp_update_post(array(
			'ID'          => $pagina_id,
			'post_status' => 'publish',
			'post_name'   => 'politica-de-privacidade',
		));
	}

	$valores = array(
		'pol_hero_titulo'      => 'Política de Privacidade',
		'pol_hero_data_rotulo' => 'Última atualização:',
		'pol_hero_data'        => '20261009',
		'pol_sumario_rotulo'   => 'Sumário',
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, $pagina_id)) {
			update_field('field_er_' . $campo, $valor, $pagina_id);
		}
	}

	if (get_field('pol_secoes', $pagina_id)) {
		return;
	}

	$lista = static function ($itens) {
		return '<ul><li>' . implode('</li><li>', $itens) . '</li></ul>';
	};

	$secoes = array(
		array('Introdução',
			'<p>A Água Mineral Entre Rios ("Entre Rios", "nós") respeita a sua privacidade e está comprometida com a proteção dos dados pessoais de todos que visitam nosso site e se relacionam com a nossa marca.</p>'
			. '<p>Esta Política de Privacidade explica, de forma clara e transparente, quais dados coletamos, como os utilizamos, com quem podemos compartilhá-los e quais são os seus direitos, em conformidade com a Lei Geral de Proteção de Dados Pessoais (Lei nº 13.709/2018 – LGPD).</p>'),
		array('Dados que coletamos',
			'<p>Podemos coletar as seguintes categorias de dados pessoais:</p>' . $lista(array(
				'Dados de identificação e contato informados por você no formulário "Fale Conosco", como nome, e-mail, telefone e o conteúdo da mensagem.',
				'Dados de navegação coletados automaticamente, como endereço IP, tipo de navegador, páginas visitadas, data e horário de acesso.',
				'Informações fornecidas voluntariamente em pesquisas, promoções ou cadastros realizados em nossos canais.',
			))),
		array('Como utilizamos seus dados',
			'<p>Os dados pessoais são utilizados para as seguintes finalidades:</p>' . $lista(array(
				'Responder às suas dúvidas, sugestões, solicitações e reclamações.',
				'Melhorar a experiência de navegação e o funcionamento do nosso site.',
				'Enviar comunicações institucionais e informações sobre produtos, quando autorizado por você.',
				'Cumprir obrigações legais e regulatórias aplicáveis à nossa atividade.',
			))),
		array('Compartilhamento de informações',
			'<p>A Entre Rios não vende, aluga ou comercializa dados pessoais. Os dados poderão ser compartilhados apenas com prestadores de serviços que nos apoiam na operação do site e no atendimento (como hospedagem e ferramentas de comunicação), sempre sob obrigações de confidencialidade, ou com autoridades públicas, quando exigido por lei ou ordem judicial.</p>'),
		array('Cookies e tecnologias semelhantes',
			'<p>Utilizamos cookies para lembrar suas preferências, entender como o site é utilizado e aprimorar nossos conteúdos. Você pode gerenciar ou desativar os cookies a qualquer momento nas configurações do seu navegador, ciente de que algumas funcionalidades poderão ser afetadas.</p>'),
		array('Armazenamento e segurança',
			'<p>Adotamos medidas técnicas e administrativas adequadas para proteger os dados pessoais contra acessos não autorizados, perda, alteração ou qualquer forma de tratamento inadequado. Os dados são armazenados em ambientes seguros e acessados somente por pessoas autorizadas.</p>'),
		array('Seus direitos',
			'<p>Nos termos da LGPD, você pode, a qualquer momento:</p>' . $lista(array(
				'Confirmar a existência de tratamento e acessar os seus dados.',
				'Solicitar a correção de dados incompletos, inexatos ou desatualizados.',
				'Pedir a anonimização, o bloqueio ou a eliminação de dados desnecessários.',
				'Revogar o consentimento e solicitar informações sobre o compartilhamento de dados.',
			))),
		array('Retenção dos dados',
			'<p>Os dados pessoais serão mantidos apenas pelo tempo necessário para cumprir as finalidades descritas nesta política ou para atender a obrigações legais. Após esse período, serão eliminados ou anonimizados de forma segura.</p>'),
		array('Alterações nesta política',
			'<p>Esta Política de Privacidade poderá ser atualizada periodicamente para refletir melhorias em nossos processos ou mudanças na legislação. Recomendamos que você a consulte regularmente. A data da última atualização estará sempre indicada no topo desta página.</p>'),
		array('Contato',
			'<p>Em caso de dúvidas sobre esta política ou para exercer os seus direitos como titular de dados, entre em contato conosco:</p>' . $lista(array(
				'E-mail: <a href="mailto:sac@aguaentrerios.com.br">sac@aguaentrerios.com.br</a>',
				'Telefone: <a href="tel:+557930151141">(79) 3015-1141</a>',
				'Endereço: Sitio São Luiz, S/N Povoado Grotão Zona Rural, Estância - SE, CEP 49200-000',
			))),
	);

	update_field('field_er_pol_secoes', array_map(static function ($secao) {
		return array(
			'field_er_pol_secao_titulo' => $secao[0],
			'field_er_pol_secao_texto'  => $secao[1],
		);
	}, $secoes), $pagina_id);
}

/**
 * v9 — Página Sustentabilidade (Figma: Screen/Sustainability): liga o
 * template, apaga os campos órfãos da primeira versão da página (prefixo
 * sust_, que colidia com os campos da Home) e grava os textos e imagens do
 * Figma. Como o resto do seed: só grava campo vazio.
 *
 * @param int               $pagina_id
 * @param int               $home_id   Nunca é limpo (a Home usa sust_*).
 * @param array<string,int> $midia
 * @return void
 */
function entrerios_seed_sustentabilidade_pagina($pagina_id, $home_id, $midia)
{
	if (!$pagina_id) {
		return;
	}

	$template = get_post_meta($pagina_id, '_wp_page_template', true);
	if (!$template || 'default' === $template) {
		update_post_meta($pagina_id, '_wp_page_template', 'page-sustentabilidade.php');
	}

	// Campos da versão anterior desta página (grupo com chaves field_er_sust_*,
	// as mesmas da Home). Só nesta página — a Home continua com os dela.
	if ((int) $pagina_id !== (int) $home_id) {
		foreach (array_keys((array) get_post_meta($pagina_id)) as $chave) {
			if (0 === strpos($chave, 'sust_') || 0 === strpos($chave, '_sust_')) {
				delete_post_meta($pagina_id, $chave);
			}
		}
	}

	$valores = array(
		'sus_hero_imagem' => $midia['sobre-fabrica-drone-02'] ?? 0,
		'sus_hero_rotulo' => 'Sustentabilidade',
		'sus_hero_titulo' => 'Cuidar da água é cuidar do futuro.',

		'sus_intro_texto'    => "Na Entre Rios, acreditamos que produzir bebidas de qualidade também significa assumir a responsabilidade de cuidar dos recursos naturais que fazem parte da nossa história.\n\nDa proteção das nossas fontes aos processos realizados dentro da fábrica, buscamos unir qualidade, tecnologia e responsabilidade ambiental, trabalhando para que desenvolvimento e preservação caminhem juntos.",
		'sus_intro_citacao'  => 'Porque a água que cuidamos hoje também pertence às próximas gerações.',

		'sus_comp_imagem'   => $midia['sobre-fabrica-drone-02'] ?? 0,
		'sus_comp_rotulo'   => 'NOSSO COMPROMISSO COM O FUTURO',
		'sus_comp_titulo'   => 'Sustentabilidade não é uma ação isolada. É um processo de evolução.',
		'sus_comp_texto'    => 'Por isso, a Entre Rios busca continuamente aperfeiçoar seus processos, incorporar novas tecnologias e encontrar maneiras mais responsáveis de produzir.',
		'sus_comp_destaque' => 'Sabemos que ainda há muito a evoluir e é justamente essa evolução que nos move.',

		'sus_assinatura_frase' => "Entre Rios.\nCuidando da nossa água.\nPreservando nossa origem.\nConstruindo o futuro.",
	);

	foreach ($valores as $campo => $valor) {
		if ($valor && !get_field($campo, $pagina_id)) {
			update_field('field_er_' . $campo, $valor, $pagina_id);
		}
	}

	if (get_field('sus_pilares', $pagina_id)) {
		return;
	}

	$produtos = static function ($nomes) use ($midia) {
		return array_values(array_filter(array_map(static function ($nome) use ($midia) {
			return (int) ($midia[$nome] ?? 0);
		}, $nomes)));
	};

	$p = 'field_er_sus_pilar_';

	update_field('field_er_sus_pilares', array(
		array(
			$p . 'rotulo'   => 'PROTEÇÃO DAS NOSSAS FONTES',
			$p . 'titulo'   => 'Tudo começa na origem.',
			$p . 'texto'    => "Nossas fontes estão localizadas a aproximadamente 60 metros de profundidade, protegidas contra possíveis focos externos de contaminação.\n\nA qualidade da água é acompanhada por meio de controles e análises periódicas, contribuindo para que ela mantenha suas características e chegue ao consumidor dentro dos padrões de qualidade e segurança exigidos.",
			$p . 'destaque' => 'Preservar nossas fontes significa proteger o recurso mais importante para a Entre Rios: a água.',
			$p . 'visual'   => 'profundidade',
			$p . 'valor'    => '60 m',
		),
		array(
			$p . 'rotulo'   => 'PRODUÇÃO RESPONSÁVEL',
			$p . 'titulo'   => 'Qualidade e cuidado em cada etapa.',
			$p . 'texto'    => "Dentro da fábrica, nossos processos seguem procedimentos de controle, higiene e segurança voltados para a qualidade dos produtos.\n\nEquipamentos, embalagens e etapas de produção passam por controles específicos para que cada produto Entre Rios chegue ao mercado com o padrão que buscamos oferecer aos nossos consumidores.",
			$p . 'destaque' => 'Tecnologia, responsabilidade e melhoria contínua fazem parte da nossa forma de produzir.',
			$p . 'visual'   => 'foto',
			$p . 'imagem'   => (int) ($midia['sobre-fabrica-drone-01'] ?? 0),
			$p . 'selo'     => 'escudo',
		),
		array(
			$p . 'rotulo'   => 'EMBALAGENS',
			$p . 'titulo'   => 'Responsabilidade que vai além do produto.',
			$p . 'texto'    => "As embalagens são fundamentais para proteger nossos produtos, mas sabemos que sua responsabilidade não termina depois do consumo.\n\nPor isso, buscamos trabalhar com embalagens adequadas aos nossos processos e incentivar uma consciência cada vez maior sobre descarte correto, reciclagem e reaproveitamento de materiais.",
			$p . 'destaque' => 'Nosso objetivo é continuar evoluindo na busca por soluções que conciliem segurança, qualidade e menor impacto ambiental.',
			$p . 'visual'   => 'produtos',
			$p . 'fundo'    => 'azul',
			$p . 'selo'     => 'reciclagem',
			$p . 'produtos' => $produtos(array('sustentabilidade-garrafao-20-litros', 'sustentabilidade-garrafa-500ml-sem-gas', 'sustentabilidade-lata-350ml-sem-gas', 'sustentabilidade-copo-200ml')),
		),
		array(
			$p . 'rotulo'   => 'MATÉRIAS-PRIMAS',
			$p . 'titulo'   => 'Qualidade começa na escolha.',
			$p . 'texto'    => "A produção das bebidas Entre Rios também envolve uma seleção criteriosa de ingredientes e matérias-primas.\n\nBuscamos fornecedores e insumos que atendam aos padrões necessários para preservar as características, o sabor e a qualidade de cada produto que leva a marca Entre Rios.",
			$p . 'destaque' => 'Cada escolha faz parte do compromisso de entregar produtos que representem a confiança construída pela nossa empresa ao longo dos anos.',
			$p . 'visual'   => 'produtos',
			$p . 'fundo'    => 'laranja',
			$p . 'selo'     => 'folha',
			$p . 'produtos' => $produtos(array('sustentabilidade-qtal-laranja-2l', 'sustentabilidade-copo-guarana-mais', 'sustentabilidade-qtal-uva-2l')),
		),
	), $pagina_id);
}
