<?php
/**
 * Sustentabilidade — Topo (Figma: Hero).
 *
 * Foto aérea com dois degradês por cima (navy da esquerda para a direita e
 * escurecimento no alto, atrás do cabeçalho), sobretítulo com traço e o H1.
 * A foto é a imagem do LCP: carrega com prioridade alta e sem lazy.
 *
 * Campos: sus_hero_imagem, sus_hero_rotulo, sus_hero_titulo (vazio esconde).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_titulo = (string) aw_field('sus_hero_titulo', get_the_ID(), '');

if ('' === trim($aw_titulo)) {
	return;
}

$aw_imagem = (int) aw_field('sus_hero_imagem', get_the_ID(), 0);
$aw_rotulo = (string) aw_field('sus_hero_rotulo', get_the_ID(), '');
?>
<section class="sus-hero" aria-labelledby="sus-hero-titulo">
	<?php if ($aw_imagem) : ?>
		<?php echo wp_get_attachment_image($aw_imagem, 'full', false, array(
			'class'         => 'sus-hero__foto',
			'alt'           => '',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'decoding'      => 'async',
			'sizes'         => '100vw',
		)); ?>
	<?php endif; ?>

	<div class="sus-hero__conteudo">
		<?php if ($aw_rotulo) : ?>
			<p class="sus-rotulo sus-rotulo--traco text-label-caps"><?php echo esc_html($aw_rotulo); ?></p>
		<?php endif; ?>
		<h1 class="sus-hero__titulo text-heading-h1" id="sus-hero-titulo"><?php echo nl2br(esc_html(trim($aw_titulo)), false); ?></h1>
	</div>
</section>
