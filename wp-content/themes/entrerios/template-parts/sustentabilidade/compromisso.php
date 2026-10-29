<?php
/**
 * Sustentabilidade — Nosso compromisso com o futuro (Figma: Compromisso).
 *
 * Foto aérea sob o degradê navy-900/97 → navy-500/88 (126,87°). Esquerda:
 * sobretítulo sky-300 e H2. Direita: texto e card translúcido (vidro, blur 20)
 * com a frase em Heading/H3.
 *
 * Campos: sus_comp_imagem, sus_comp_rotulo, sus_comp_titulo (vazio esconde),
 * sus_comp_texto, sus_comp_destaque.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = trim((string) aw_field('sus_comp_titulo', $aw_id, ''));

if ('' === $aw_titulo) {
	return;
}

$aw_imagem   = (int) aw_field('sus_comp_imagem', $aw_id, 0);
$aw_rotulo   = (string) aw_field('sus_comp_rotulo', $aw_id, '');
$aw_texto    = (string) aw_field('sus_comp_texto', $aw_id, '');
$aw_destaque = trim((string) aw_field('sus_comp_destaque', $aw_id, ''));
?>
<section class="sus-compromisso" aria-labelledby="sus-compromisso-titulo">
	<?php if ($aw_imagem) : ?>
		<?php echo wp_get_attachment_image($aw_imagem, 'full', false, array(
			'class'    => 'sus-compromisso__foto',
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'sizes'    => '100vw',
		)); ?>
	<?php endif; ?>

	<div class="sus-compromisso__titulo">
		<?php if ($aw_rotulo) : ?>
			<p class="sus-rotulo sus-rotulo--claro text-label-caps"><?php echo esc_html($aw_rotulo); ?></p>
		<?php endif; ?>
		<h2 class="text-heading-h2" id="sus-compromisso-titulo"><?php echo nl2br(esc_html($aw_titulo), false); ?></h2>
	</div>

	<div class="sus-compromisso__texto sus-texto">
		<?php aw_paragrafos($aw_texto, 'text-body-large'); ?>

		<?php if ($aw_destaque) : ?>
			<p class="sus-compromisso__destaque text-heading-h3"><?php echo nl2br(esc_html($aw_destaque), false); ?></p>
		<?php endif; ?>
	</div>
</section>
