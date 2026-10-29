<?php
/**
 * A Entre Rios — Da água para novos caminhos (Figma: Novos Caminhos).
 *
 * Texto à esquerda (título, subtítulo, parágrafos e intertítulos, frase
 * final no bloco em degradê) e a colagem de produtos à direita: 5 produtos
 * recortados em posições fixas do Figma (600×640), com 4 círculos de cor.
 * Os produtos flutuam devagar, cada um no seu tempo (só CSS; parado com
 * prefers-reduced-motion).
 *
 * Campos: ae_caminhos_titulo (vazio esconde), ae_caminhos_subtitulo,
 * ae_caminhos_blocos, ae_caminhos_destaque e ae_colagem_* (5 imagens).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('ae_caminhos_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_subtitulo = (string) aw_field('ae_caminhos_subtitulo', $aw_id, '');
$aw_blocos    = (array) aw_field('ae_caminhos_blocos', $aw_id, array());
$aw_destaque  = (string) aw_field('ae_caminhos_destaque', $aw_id, '');

// Ordem = empilhamento do Figma (de trás para a frente).
$aw_colagem = array();
foreach (array('esquerda', 'centro', 'direita', 'esquerda-frente', 'direita-frente') as $aw_slot) {
	$aw_img = (int) aw_field('ae_colagem_' . str_replace('-', '_', $aw_slot), $aw_id, 0);
	if ($aw_img) {
		$aw_colagem[$aw_slot] = $aw_img;
	}
}
?>
<section class="ae-caminhos" aria-labelledby="ae-caminhos-titulo">
	<div class="ae-caminhos__texto ae-texto">
		<h2 class="ae-titulo text-heading-h2" id="ae-caminhos-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		<?php if ($aw_subtitulo) : ?>
			<p class="ae-caminhos__subtitulo text-heading-h3"><?php echo esc_html($aw_subtitulo); ?></p>
		<?php endif; ?>

		<?php get_template_part('template-parts/a-entre-rios/blocos', null, array('blocos' => $aw_blocos)); ?>

		<?php if ($aw_destaque) : ?>
			<p class="ae-caminhos__destaque text-heading-h3"><?php echo esc_html($aw_destaque); ?></p>
		<?php endif; ?>
	</div>

	<?php if ($aw_colagem) : ?>
		<div class="ae-colagem" aria-hidden="true">
			<span class="ae-colagem__circulo ae-colagem__circulo--base"></span>
			<span class="ae-colagem__circulo ae-colagem__circulo--guarana"></span>
			<span class="ae-colagem__circulo ae-colagem__circulo--refri"></span>
			<span class="ae-colagem__circulo ae-colagem__circulo--energ"></span>
			<?php foreach ($aw_colagem as $aw_slot => $aw_img) : ?>
				<span class="ae-colagem__produto ae-colagem__produto--<?php echo esc_attr($aw_slot); ?>">
					<?php echo wp_get_attachment_image($aw_img, 'full', false, array(
						'alt'      => '',
						'loading'  => 'lazy',
						'decoding' => 'async',
						'sizes'    => '(min-width: 1280px) 180px, 30vw',
					)); ?>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
