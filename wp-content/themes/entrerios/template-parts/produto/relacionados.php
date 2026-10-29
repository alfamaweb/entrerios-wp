<?php
/**
 * Produto — Produtos relacionados (Figma: Produtos Relacionados).
 *
 * Desktop: sobretítulo (nome da linha) + H2 à esquerda, "Ver todos os
 * produtos" (Text Link) à direita e 4 Product Cards em linha.
 * Mobile: carrossel de rolagem horizontal (Carousel Viewport), sem o link.
 *
 * Os textos vêm da página Produtos: prod_rel_titulo (vazio esconde a seção)
 * e prod_rel_link. Os cards são os 4 primeiros da mesma linha.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_pagina = aw_produtos_pagina_id();
$aw_titulo = $aw_pagina ? (string) aw_field('prod_rel_titulo', $aw_pagina, '') : '';
$aw_linha  = $args['linha'] ?? aw_produto_linha($aw_id);
$aw_cards  = ('' !== $aw_titulo) ? aw_produto_relacionados($aw_id) : array();

if (!$aw_cards) {
	return;
}

$aw_link_texto = (string) aw_field('prod_rel_link', $aw_pagina, '');
$aw_link_url   = $aw_linha ? aw_produtos_url(array('categoria' => $aw_linha->slug)) : aw_produtos_url();
?>
<section class="produto-rel" aria-labelledby="produto-rel-titulo">
	<header class="produto-rel__cabecalho">
		<div class="produto-rel__titulos">
			<?php if ($aw_linha) : ?>
				<p class="produto-rel__rotulo text-label-caps"><?php echo esc_html($aw_linha->name); ?></p>
			<?php endif; ?>
			<h2 class="produto-rel__titulo text-heading-h2" id="produto-rel-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		</div>

		<?php if ($aw_link_texto && $aw_link_url) : ?>
			<a class="link-sublinhado text-label-button" href="<?php echo esc_url($aw_link_url); ?>"><?php echo esc_html($aw_link_texto); ?></a>
		<?php endif; ?>
	</header>

	<div class="produto-rel__viewport" tabindex="0" aria-label="<?php echo esc_attr($aw_titulo); ?>">
		<ul class="produto-rel__trilho">
			<?php foreach ($aw_cards as $aw_card) : ?>
				<li class="produto-rel__item"><?php get_template_part('template-parts/card-produto', null, array('post' => $aw_card)); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
