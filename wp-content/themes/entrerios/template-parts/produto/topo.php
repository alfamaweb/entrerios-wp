<?php
/**
 * Produto — Topo (Figma: Produto → Breadcrumb + Conteúdo: Stage 620 + Informações 580).
 *
 * Informações: linha (Label/Caps sky-300), nome (H1), versões (pílulas),
 * sanfona e o botão de contato (Button Light). A sanfona e as versões somem
 * quando não há conteúdo; o botão vem da página Produtos (prod_cta).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id       = get_the_ID();
$aw_linha    = $args['linha'] ?? aw_produto_linha($aw_id);
$aw_pagina   = aw_produtos_pagina_id();
$aw_home_id  = (int) get_option('page_on_front');
$aw_cta      = $aw_pagina ? aw_field('prod_cta', $aw_pagina, array()) : array();
$aw_tem_cta  = !empty($aw_cta['url']) && !empty($aw_cta['title']);

$aw_trilha = array(
	array('texto' => $aw_home_id ? get_the_title($aw_home_id) : '', 'url' => home_url('/')),
	array('texto' => $aw_pagina ? get_the_title($aw_pagina) : '', 'url' => aw_produtos_url()),
);

if ($aw_linha) {
	$aw_trilha[] = array('texto' => $aw_linha->name, 'url' => aw_produtos_url(array('categoria' => $aw_linha->slug)));
}

$aw_trilha[] = array('texto' => get_the_title($aw_id), 'url' => '');
?>
<section class="produto-topo" aria-labelledby="produto-titulo">
	<?php get_template_part('template-parts/produtos/trilha', null, array('itens' => $aw_trilha, 'classe' => 'produto-topo__trilha')); ?>

	<div class="produto-topo__conteudo">
		<?php get_template_part('template-parts/produto/palco'); ?>

		<div class="produto-topo__info">
			<?php if ($aw_linha) : ?>
				<p class="produto-topo__rotulo text-label-caps"><?php echo esc_html($aw_linha->name); ?></p>
			<?php endif; ?>

			<h1 class="produto-topo__titulo text-heading-h1" id="produto-titulo"><?php the_title(); ?></h1>

			<?php get_template_part('template-parts/produto/versoes'); ?>
			<?php get_template_part('template-parts/produto/sanfona'); ?>

			<?php if ($aw_tem_cta) : ?>
				<div class="produto-topo__cta">
					<a class="botao-claro" href="<?php echo esc_url($aw_cta['url']); ?>"<?php echo !empty($aw_cta['target']) ? ' target="_blank" rel="noopener"' : ''; ?>>
						<span class="text-label-button"><?php echo esc_html($aw_cta['title']); ?></span>
						<span class="botao-claro__icone" aria-hidden="true">
							<svg width="16" height="14" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 7h14M9 1l6 6-6 6" /></svg>
						</span>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
