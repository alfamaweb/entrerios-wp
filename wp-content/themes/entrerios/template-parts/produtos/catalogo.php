<?php
/**
 * Produtos — Catálogo (Figma: Catálogo → Toolbar, Body: Filtros + Resultados).
 *
 * Sem JS é um formulário GET: abas são links, os filtros têm "Aplicar" e
 * "Carregar mais" é um link para ?pagina=N (a página mostra N × 12 cards).
 * Com JS (assets/js/produtos.js) tudo responde na hora por AJAX, a URL
 * acompanha o estado e "Carregar mais" acrescenta os próximos 12.
 *
 * Campos: prod_mais_texto (vazio esconde o botão), prod_linhas (ordem).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id      = get_the_ID();
$aw_url     = get_permalink($aw_id);
$aw_filtros = aw_produtos_filtros(wp_unslash($_GET)); // phpcs:ignore WordPress.Security.NonceVerification -- leitura de filtro público
$aw_paginas = isset($_GET['pagina']) ? min(20, max(1, absint($_GET['pagina']))) : 1; // phpcs:ignore WordPress.Security.NonceVerification

$aw_service  = aw_produtos_service($aw_filtros, 1, $aw_paginas * AW_PRODUTOS_POR_PAGINA);
$aw_total    = $aw_service->total();
$aw_exibidos = min($aw_total, $aw_paginas * AW_PRODUTOS_POR_PAGINA);
$aw_textos   = aw_produtos_textos($aw_exibidos, $aw_total);
$aw_mais     = (string) aw_field('prod_mais_texto', $aw_id, '');

$aw_linhas      = aw_produtos_opcoes('linha');
$aw_categorias  = $aw_filtros['linha'] ?? array();
$aw_grupos      = array(
	'categoria' => array('rotulo' => __('Categoria', 'alfama-web'), 'termos' => $aw_linhas),
	'embalagem' => array('rotulo' => __('Embalagem', 'alfama-web'), 'termos' => aw_produtos_opcoes('embalagem')),
	'tipo'      => array('rotulo' => __('Tipo', 'alfama-web'), 'termos' => aw_produtos_opcoes('tipo_produto')),
);

// URL do "Carregar mais" sem JS: mesmos filtros, uma página a mais.
$aw_params_url = array();
foreach (AW_PRODUTOS_FILTROS as $aw_param => $aw_tax) {
	if (!empty($aw_filtros[$aw_tax])) {
		$aw_params_url[$aw_param] = $aw_filtros[$aw_tax];
	}
}
$aw_url_mais = add_query_arg('pagina', $aw_paginas + 1, aw_produtos_url($aw_params_url));
?>
<section class="prod-catalogo" aria-labelledby="prod-catalogo-titulo">
	<h2 class="sr-only" id="prod-catalogo-titulo"><?php esc_html_e('Catálogo', 'alfama-web'); ?></h2>

	<form class="prod-catalogo__form" method="get" action="<?php echo esc_url($aw_url); ?>" data-er-produtos data-por-pagina="<?php echo (int) AW_PRODUTOS_POR_PAGINA; ?>" data-pagina="<?php echo (int) $aw_paginas; ?>">

		<div class="prod-catalogo__toolbar">
			<?php if (count($aw_linhas) > 1) : ?>
				<nav class="prod-abas" aria-label="<?php esc_attr_e('Categorias', 'alfama-web'); ?>">
					<div class="prod-abas__trilho">
						<a class="aba-linha text-label-caps<?php echo $aw_categorias ? '' : ' is-ativo'; ?>" href="<?php echo esc_url($aw_url); ?>" data-er-aba=""<?php echo $aw_categorias ? '' : ' aria-current="true"'; ?>><?php esc_html_e('Todos', 'alfama-web'); ?></a>
						<?php foreach ($aw_linhas as $aw_linha) :
							$aw_ativa = array($aw_linha->slug) === $aw_categorias;
							?>
							<a class="aba-linha text-label-caps<?php echo $aw_ativa ? ' is-ativo' : ''; ?>" href="<?php echo esc_url(aw_produtos_url(array('categoria' => $aw_linha->slug))); ?>"
								data-er-aba="<?php echo esc_attr($aw_linha->slug); ?>"<?php echo $aw_ativa ? ' aria-current="true"' : ''; ?>
								style="<?php echo aw_linha_css_vars($aw_linha); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>"><?php echo esc_html($aw_linha->name); ?></a>
						<?php endforeach; ?>
					</div>
				</nav>
			<?php endif; ?>

			<button type="button" class="prod-filtros-botao text-label-button-sm" aria-controls="prod-filtros" aria-expanded="false" data-er-filtros-botao hidden>
				<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M2 4h14M5 9h8M7.5 14h3" /></svg>
				<?php esc_html_e('Filtros', 'alfama-web'); ?>
				<span class="prod-filtros-botao__total" data-er-filtros-total hidden></span>
			</button>
		</div>

		<div class="prod-catalogo__corpo">
			<aside class="prod-filtros" id="prod-filtros" aria-labelledby="prod-filtros-titulo" data-er-filtros>
				<div class="prod-filtros__cabecalho">
					<h3 class="prod-filtros__titulo" id="prod-filtros-titulo"><?php esc_html_e('Filtros', 'alfama-web'); ?></h3>
					<a class="prod-filtros__limpar text-label-button-sm" href="<?php echo esc_url($aw_url); ?>" data-er-limpar><?php esc_html_e('Limpar', 'alfama-web'); ?></a>
				</div>

				<?php
				$aw_primeiro = true;
				foreach ($aw_grupos as $aw_param => $aw_grupo) :
					if (!$aw_grupo['termos']) {
						continue;
					}
					$aw_marcados = $aw_filtros[AW_PRODUTOS_FILTROS[$aw_param]] ?? array();
					?>
					<?php if (!$aw_primeiro) : ?>
						<hr class="prod-filtros__divisor">
					<?php endif; $aw_primeiro = false; ?>
					<fieldset class="prod-filtro">
						<legend class="prod-filtro__titulo text-label-caps"><?php echo esc_html($aw_grupo['rotulo']); ?></legend>
						<?php foreach ($aw_grupo['termos'] as $aw_termo) : ?>
							<label class="prod-opcao">
								<input class="prod-opcao__input" type="checkbox" name="<?php echo esc_attr($aw_param); ?>[]" value="<?php echo esc_attr($aw_termo->slug); ?>"<?php checked(in_array($aw_termo->slug, $aw_marcados, true)); ?>>
								<span class="prod-opcao__caixa" aria-hidden="true">
									<svg width="12" height="10" viewBox="0 0 12 10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 5.2 4.4 8 10.5 1.8" /></svg>
								</span>
								<span class="prod-opcao__nome text-label-button"><?php echo esc_html($aw_termo->name); ?></span>
								<span class="prod-opcao__total"><?php echo esc_html(number_format_i18n($aw_termo->count)); ?></span>
							</label>
						<?php endforeach; ?>
					</fieldset>
				<?php endforeach; ?>

				<button type="submit" class="prod-filtros__aplicar botao-primario" data-er-sem-js>
					<span class="text-label-button"><?php esc_html_e('Aplicar filtros', 'alfama-web'); ?></span>
				</button>
			</aside>

			<div class="prod-resultados">
				<div class="prod-resultados__topo">
					<p class="prod-resumo" aria-live="polite">
						<strong class="prod-resumo__numero" data-er-total><?php echo esc_html(number_format_i18n($aw_total)); ?></strong>
						<span class="prod-resumo__texto text-body-regular" data-er-resumo><?php echo esc_html($aw_textos['resumo']); ?></span>
					</p>
				</div>

				<div class="prod-grade" data-er-grade aria-live="polite">
					<?php echo $aw_service->render('template-parts/card-produto', aw_produtos_vazio()); // phpcs:ignore WordPress.Security.EscapeOutput -- HTML do template part ?>
				</div>

				<div class="prod-paginacao" data-er-paginacao<?php echo $aw_total ? '' : ' hidden'; ?>>
					<p class="prod-paginacao__texto text-label-small" data-er-mostrando><?php echo esc_html($aw_textos['mostrando']); ?></p>
					<div class="prod-paginacao__progresso" aria-hidden="true">
						<span class="prod-paginacao__barra" data-er-progresso style="width: <?php echo esc_attr($aw_textos['progresso']); ?>%"></span>
					</div>

					<?php if ($aw_mais) : ?>
						<a class="botao-primario prod-paginacao__mais" href="<?php echo esc_url($aw_url_mais); ?>" data-er-mais<?php echo $aw_exibidos < $aw_total ? '' : ' hidden'; ?>>
							<span class="text-label-button"><?php echo esc_html($aw_mais); ?></span>
							<span class="botao-primario__icone" aria-hidden="true">
								<svg width="16" height="14" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 7h14M9 1l6 6-6 6" /></svg>
							</span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</form>
</section>
