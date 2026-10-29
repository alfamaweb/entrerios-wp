<?php
/**
 * A Entre Rios — Topo (Figma: Hero).
 *
 * Foto aérea com dois degradês por cima (navy da esquerda para a direita e
 * escurecimento no alto, atrás do cabeçalho), sobretítulo com traço e o H1.
 * A foto é a imagem do LCP: carrega com prioridade alta e sem lazy.
 *
 * Campos: ae_hero_imagem, ae_hero_rotulo, ae_hero_titulo (vazio esconde).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_titulo = (string) aw_field('ae_hero_titulo', get_the_ID(), '');

if ('' === $aw_titulo) {
	return;
}

$aw_imagem = (int) aw_field('ae_hero_imagem', get_the_ID(), 0);
$aw_rotulo = (string) aw_field('ae_hero_rotulo', get_the_ID(), '');
?>
<section class="ae-hero" aria-labelledby="ae-hero-titulo">
	<?php if ($aw_imagem) : ?>
		<?php echo wp_get_attachment_image($aw_imagem, 'full', false, array(
			'class'         => 'ae-hero__foto',
			'alt'           => '',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'decoding'      => 'async',
			'sizes'         => '100vw',
		)); ?>
	<?php endif; ?>

	<div class="ae-hero__conteudo">
		<?php if ($aw_rotulo) : ?>
			<p class="ae-rotulo ae-rotulo--traco text-label-caps"><?php echo esc_html($aw_rotulo); ?></p>
		<?php endif; ?>
		<h1 class="ae-hero__titulo text-heading-h1" id="ae-hero-titulo"><?php echo nl2br(esc_html($aw_titulo), false); ?></h1>
	</div>
</section>
