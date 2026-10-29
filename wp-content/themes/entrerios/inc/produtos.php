<?php
/**
 * Catálogo de produtos — página Produtos (listagem com filtros) e página do
 * produto (single-produto.php).
 *
 * Figma: Screen/Products/Desktop · Mobile e Screen/Product/<linha>/Desktop ·
 * Mobile. Detalhes em docs/PRODUTOS.md.
 *
 *  - Filtros: Linha (CATEGORIA), Embalagem e Tipo (com/sem gás). Dentro de um
 *    grupo as opções somam (OU); entre grupos, restringem (E).
 *  - A URL carrega o estado (?categoria[]=…&embalagem[]=…&tipo[]=…&pagina=N),
 *    então a página funciona sem JS (o formulário é GET) e um link filtrado
 *    pode ser compartilhado. Com JS, assets/js/produtos.js troca a grade por
 *    AJAX (ação aw_filtrar_produtos) e "Carregar mais" acrescenta 12 cards.
 *  - O mesmo template part (card-produto) desenha o SSR e o AJAX.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

/** Cards por carga (Figma: "Mostrando 12 de 33 produtos"). */
const AW_PRODUTOS_POR_PAGINA = 12;

/** Parâmetro da URL => taxonomia. */
const AW_PRODUTOS_FILTROS = array(
	'categoria' => 'linha',
	'embalagem' => 'embalagem',
	'tipo'      => 'tipo_produto',
);

/**
 * ID da página que usa o template Produtos (0 se não houver).
 *
 * A página do produto lê dela os textos compartilhados (relacionados, botão
 * de contato) — assim nada fica escrito no PHP.
 *
 * @return int
 */
function aw_produtos_pagina_id()
{
	static $id = null;

	if (null !== $id) {
		return $id;
	}

	$ids = get_posts(array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-produtos.php',
		'no_found_rows'  => true,
	));

	$id = $ids ? (int) $ids[0] : 0;

	return $id;
}

/**
 * URL da página Produtos, opcionalmente filtrada.
 *
 * @param array<string,string|string[]> $filtros Ex.: array('categoria' => 'agua-mineral').
 * @return string Vazio se a página não existir.
 */
function aw_produtos_url($filtros = array())
{
	$pagina = aw_produtos_pagina_id();

	if (!$pagina) {
		return '';
	}

	$url = get_permalink($pagina);

	foreach ($filtros as $param => $valores) {
		foreach ((array) $valores as $valor) {
			$url .= (false === strpos($url, '?') ? '?' : '&') . rawurlencode($param . '[]') . '=' . rawurlencode($valor);
		}
	}

	return $url;
}

/**
 * Filtros higienizados a partir de $_GET / $_POST.
 *
 * Aceita "categoria=slug" e "categoria[]=slug". Slugs que não são termos da
 * taxonomia são descartados (não chegam à consulta nem à URL).
 *
 * @param array $cru
 * @return array<string,string[]> taxonomia => slugs
 */
function aw_produtos_filtros(array $cru)
{
	$filtros = array();

	foreach (AW_PRODUTOS_FILTROS as $param => $taxonomia) {
		if (empty($cru[$param]) || !taxonomy_exists($taxonomia)) {
			continue;
		}

		$valores = array_filter(array_map('sanitize_title', array_map('wp_unslash', (array) $cru[$param])));
		$valores = array_values(array_filter(array_unique($valores), function ($slug) use ($taxonomia) {
			return (bool) term_exists($slug, $taxonomia);
		}));

		if ($valores) {
			$filtros[$taxonomia] = $valores;
		}
	}

	return $filtros;
}

/**
 * Consulta do catálogo: ordem do atributo "Ordem" e depois título.
 *
 * @param array<string,string[]> $filtros
 * @param int                    $pagina
 * @param int                    $por_pagina
 * @return AW_Query_Service
 */
function aw_produtos_service(array $filtros, $pagina = 1, $por_pagina = AW_PRODUTOS_POR_PAGINA)
{
	return new AW_Query_Service('produto', array(
		'paged' => max(1, (int) $pagina),
		'tax'   => $filtros,
	), array(
		'por_pagina' => max(1, (int) $por_pagina),
		'orderby'    => 'menu_order title',
		'order'      => 'ASC',
	));
}

/**
 * Opções de um grupo de filtro, com a contagem de produtos publicados.
 *
 * Linhas seguem a ordem do campo "Ordem das categorias" da página Produtos
 * (as que não estão nele vêm depois, por nome). Embalagem e Tipo seguem a
 * ordem de cadastro.
 *
 * @param string $taxonomia
 * @return WP_Term[]
 */
function aw_produtos_opcoes($taxonomia)
{
	$termos = get_terms(array(
		'taxonomy'   => $taxonomia,
		'hide_empty' => true,
		'orderby'    => 'linha' === $taxonomia ? 'name' : 'term_id',
	));

	if (is_wp_error($termos) || !$termos) {
		return array();
	}

	if ('linha' === $taxonomia) {
		$ordem = array_map('intval', (array) aw_field('prod_linhas', aw_produtos_pagina_id(), array()));

		usort($termos, function ($a, $b) use ($ordem) {
			$pa = array_search((int) $a->term_id, $ordem, true);
			$pb = array_search((int) $b->term_id, $ordem, true);
			$pa = false === $pa ? PHP_INT_MAX : $pa;
			$pb = false === $pb ? PHP_INT_MAX : $pb;

			return $pa === $pb ? strcmp($a->name, $b->name) : $pa - $pb;
		});
	}

	return $termos;
}

/**
 * Textos do resumo e da paginação, iguais no SSR e no AJAX.
 *
 * @param int $exibidos
 * @param int $total
 * @return array{resumo:string,mostrando:string,progresso:float}
 */
function aw_produtos_textos($exibidos, $total)
{
	return array(
		/* translators: complemento do número de produtos ("33 produtos encontrados") */
		'resumo'    => _n('produto encontrado', 'produtos encontrados', $total, 'alfama-web'),
		/* translators: 1: produtos exibidos, 2: total */
		'mostrando' => sprintf(__('Mostrando %1$s de %2$s produtos', 'alfama-web'), number_format_i18n($exibidos), number_format_i18n($total)),
		'progresso' => $total ? round(100 * $exibidos / $total, 2) : 0,
	);
}

/**
 * HTML vazio da grade (nenhum produto para os filtros).
 *
 * @return string
 */
function aw_produtos_vazio()
{
	return '<p class="aw-vazio">' . esc_html__('Nenhum produto com esses filtros. Tente tirar algum.', 'alfama-web') . '</p>';
}

/* -------------------------------------------------------------------------
 * AJAX — registrado pelo filtro do controller (nonce verificado lá).
 * ---------------------------------------------------------------------- */
add_filter('aw_ajax_acoes', 'aw_produtos_acao_ajax');
function aw_produtos_acao_ajax($acoes)
{
	$acoes['aw_filtrar_produtos'] = 'aw_ajax_filtrar_produtos';

	return $acoes;
}

/**
 * Uma página de cards (12) para os filtros enviados.
 *
 * Entrada: categoria[], embalagem[], tipo[], paged.
 *
 * @return void
 */
function aw_ajax_filtrar_produtos()
{
	$filtros = aw_produtos_filtros(wp_unslash($_POST)); // phpcs:ignore WordPress.Security.NonceVerification -- verificado em AW_Ajax_Controller::despachar()
	$pagina  = isset($_POST['paged']) ? max(1, absint($_POST['paged'])) : 1; // phpcs:ignore WordPress.Security.NonceVerification

	$service  = aw_produtos_service($filtros, $pagina);
	$total    = $service->total();
	$pagina   = $service->pagina();
	$exibidos = min($total, $pagina * AW_PRODUTOS_POR_PAGINA);

	wp_send_json_success(array_merge(array(
		'cards'    => $service->render('template-parts/card-produto', aw_produtos_vazio()),
		'total'    => $total,
		'exibidos' => $exibidos,
		'pagina'   => $pagina,
		'temMais'  => $exibidos < $total,
	), aw_produtos_textos($exibidos, $total)));
}

/* -------------------------------------------------------------------------
 * A Linha não tem página própria: /linha/<slug>/ leva à página Produtos
 * filtrada por ela (links antigos e o "Ver todos" de cada linha continuam
 * funcionando).
 * ---------------------------------------------------------------------- */
add_action('template_redirect', 'aw_produtos_redirecionar_linha');
function aw_produtos_redirecionar_linha()
{
	if (!is_tax('linha')) {
		return;
	}

	$url = aw_produtos_url(array('categoria' => get_queried_object()->slug));

	if ($url) {
		wp_safe_redirect($url, 301);
		exit;
	}
}

/* -------------------------------------------------------------------------
 * Página do produto
 * ---------------------------------------------------------------------- */

/**
 * Linha (primeiro termo) do produto.
 *
 * @param int $produto_id
 * @return WP_Term|null
 */
function aw_produto_linha($produto_id)
{
	$termos = get_the_terms($produto_id, 'linha');

	return ($termos && !is_wp_error($termos)) ? $termos[0] : null;
}

/**
 * Atributos que formam as versões: tipo (taxonomia), sabor e tamanho (campos).
 *
 * @param int $produto_id
 * @return array{tipo:string,sabor:string,tamanho:string}
 */
function aw_produto_atributos($produto_id)
{
	$tipos = get_the_terms($produto_id, 'tipo_produto');

	return array(
		'tipo'    => ($tipos && !is_wp_error($tipos)) ? $tipos[0]->name : '',
		'sabor'   => trim((string) aw_field('produto_sabor', $produto_id, '')),
		'tamanho' => trim((string) aw_field('produto_tamanho', $produto_id, '')),
	);
}

/**
 * Grupos de versões do Figma (TIPO / SABOR / TAMANHO) — pílulas que levam às
 * outras versões do mesmo produto.
 *
 * As versões são o produto atual + os do campo "Outras versões". Em cada
 * grupo entram as versões que só mudam naquele atributo: em "Garrafa 500ml
 * Com Gás", TAMANHO mostra as garrafas com gás (330ml, 500ml) e TIPO, as de
 * 500ml (com e sem gás). Um grupo só aparece se o produto tiver o atributo.
 *
 * @param int $produto_id
 * @return array<int,array{chave:string,rotulo:string,opcoes:array<int,array{texto:string,url:string,atual:bool}>}>
 */
function aw_produto_versoes($produto_id)
{
	$ids = array_map('intval', (array) aw_field('produto_versoes', $produto_id, array()));
	$ids = array_values(array_unique(array_merge(array((int) $produto_id), $ids)));

	$produtos = get_posts(array(
		'post_type'      => 'produto',
		'post_status'    => 'publish',
		'post__in'       => $ids,
		'posts_per_page' => count($ids),
		'orderby'        => array('menu_order' => 'ASC', 'title' => 'ASC'),
		'no_found_rows'  => true,
	));

	$atributos = array();
	foreach ($produtos as $produto) {
		$atributos[$produto->ID] = aw_produto_atributos($produto->ID);
	}

	$atual = $atributos[$produto_id] ?? aw_produto_atributos($produto_id);

	$grupos = array(
		'tipo'    => __('Tipo', 'alfama-web'),
		'sabor'   => __('Sabor', 'alfama-web'),
		'tamanho' => __('Tamanho', 'alfama-web'),
	);

	$saida = array();

	foreach ($grupos as $chave => $rotulo) {
		if ('' === $atual[$chave]) {
			continue;
		}

		$opcoes = array();

		foreach ($atributos as $id => $attr) {
			if ('' === $attr[$chave]) {
				continue;
			}

			foreach ($grupos as $outra => $_) {
				if ($outra !== $chave && $attr[$outra] !== $atual[$outra]) {
					continue 2;
				}
			}

			$valor = mb_strtolower($attr[$chave]);

			// Mesmo valor em dois produtos: fica o atual.
			if (isset($opcoes[$valor]) && !($id === (int) $produto_id)) {
				continue;
			}

			$opcoes[$valor] = array(
				'texto' => $attr[$chave],
				'url'   => get_permalink($id),
				'atual' => $id === (int) $produto_id,
			);
		}

		$saida[] = array('chave' => $chave, 'rotulo' => $rotulo, 'opcoes' => array_values($opcoes));
	}

	return $saida;
}

/**
 * Produtos relacionados: os da mesma linha, na ordem do catálogo.
 *
 * @param int $produto_id
 * @param int $quantos Figma: 4 cards.
 * @return int[]
 */
function aw_produto_relacionados($produto_id, $quantos = 4)
{
	$linha = aw_produto_linha($produto_id);

	if (!$linha) {
		return array();
	}

	// Primeiro as outras versões do produto (Figma: a garrafa de 500ml com
	// gás mostra as outras garrafas), depois o resto da linha.
	$versoes = array_map('intval', (array) aw_field('produto_versoes', $produto_id, array()));
	$ids     = array();

	if ($versoes) {
		$ids = get_posts(array(
			'post_type'      => 'produto',
			'post_status'    => 'publish',
			'post__in'       => $versoes,
			'post__not_in'   => array((int) $produto_id),
			'posts_per_page' => $quantos,
			'orderby'        => array('menu_order' => 'ASC', 'title' => 'ASC'),
			'fields'         => 'ids',
			'no_found_rows'  => true,
		));
	}

	if (count($ids) < $quantos) {
		$ids = array_merge($ids, get_posts(array(
			'post_type'      => 'produto',
			'post_status'    => 'publish',
			'posts_per_page' => $quantos - count($ids),
			'post__not_in'   => array_merge(array((int) $produto_id), $ids),
			'orderby'        => array('menu_order' => 'ASC', 'title' => 'ASC'),
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array(array(
				'taxonomy' => 'linha',
				'terms'    => $linha->term_id,
			)),
		)));
	}

	return array_map('intval', $ids);
}

/* -------------------------------------------------------------------------
 * Menu: na página de um produto, o item "Produtos" fica ativo (pílula branca
 * do Header Interno), no menu da barra e no overlay.
 * ---------------------------------------------------------------------- */
add_filter('wp_nav_menu_objects', 'aw_produtos_menu_atual');
add_filter('aw_itens_do_menu', 'aw_produtos_menu_atual');
function aw_produtos_menu_atual($itens)
{
	if (!is_singular('produto') || !aw_produtos_pagina_id()) {
		return $itens;
	}

	foreach ($itens as $item) {
		if ('post_type' === $item->type && (int) $item->object_id === aw_produtos_pagina_id()) {
			$item->current   = true;
			$item->classes[] = 'current-menu-item';
		}
	}

	return $itens;
}

/**
 * Embalagem (slug) do produto — o palco usa para enquadrar a foto.
 *
 * @param int $produto_id
 * @return string
 */
function aw_produto_embalagem($produto_id)
{
	$termos = get_the_terms($produto_id, 'embalagem');

	return ($termos && !is_wp_error($termos)) ? $termos[0]->slug : '';
}
