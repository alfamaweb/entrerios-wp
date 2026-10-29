<?php
/**
 * Produtos — Topo (Figma: Hero, 1600 × 521).
 *
 * Degradê navy-500 → sky-500 → sky-300, breadcrumb, H1 (estilo Heading/H2
 * do Figma) e, à direita, o grupo "Produtos (decor)": até 4 fotos com as
 * posições, inclinações e sombras do Figma (CSS por ordem).
 *
 * Campos: prod_hero_titulo (vazio esconde), prod_hero_imagens.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('prod_hero_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_imagens = array_slice(array_filter(array_map('intval', (array) aw_field('prod_hero_imagens', $aw_id, array()))), 0, 4);
$aw_home_id = (int) get_option('page_on_front');
?>
<section class="prod-hero" aria-labelledby="prod-hero-titulo">
	<div class="prod-hero__copia">
		<?php
		get_template_part('template-parts/produtos/trilha', null, array(
			'itens' => array(
				array('texto' => $aw_home_id ? get_the_title($aw_home_id) : '', 'url' => home_url('/')),
				array('texto' => get_the_title($aw_id), 'url' => ''),
			),
		));
		?>
		<h1 class="prod-hero__titulo text-heading-h2" id="prod-hero-titulo"><?php echo esc_html($aw_titulo); ?></h1>
	</div>

	<?php if ($aw_imagens) : ?>
		<div class="prod-hero__decor" aria-hidden="true">
			<?php foreach ($aw_imagens as $aw_imagem) : ?>
				<?php echo wp_get_attachment_image($aw_imagem, 'er-card-produto', false, array(
					'class'         => 'prod-hero__produto',
					'alt'           => '',
					'sizes'         => '(min-width: 1280px) 220px, 130px', // foto 400×580 em "contain": a largura renderizada é a da altura da caixa
					'loading'       => 'eager',
					'decoding'      => 'async',
				)); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
