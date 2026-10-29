<?php
/**
 * A Entre Rios — Origem (Figma: Origem).
 *
 * Esquerda: título, subtítulo e os marcos (anos ligados por uma linha).
 * Direita: texto, frase com a barra em degradê e texto final.
 *
 * Campos: ae_origem_titulo (vazio esconde), ae_origem_subtitulo,
 * ae_origem_marcos, ae_origem_texto, ae_origem_destaque, ae_origem_texto_final.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('ae_origem_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_subtitulo = (string) aw_field('ae_origem_subtitulo', $aw_id, '');
$aw_marcos    = array_filter(array_map(static function ($m) {
	return trim((string) ($m['ano'] ?? ''));
}, (array) aw_field('ae_origem_marcos', $aw_id, array())));
$aw_texto     = (string) aw_field('ae_origem_texto', $aw_id, '');
$aw_destaque  = (string) aw_field('ae_origem_destaque', $aw_id, '');
$aw_final     = (string) aw_field('ae_origem_texto_final', $aw_id, '');
?>
<section class="ae-origem" aria-labelledby="ae-origem-titulo">
	<div class="ae-origem__statement">
		<h2 class="ae-titulo text-heading-h2" id="ae-origem-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		<?php if ($aw_subtitulo) : ?>
			<p class="ae-origem__subtitulo text-heading-h3"><?php echo esc_html($aw_subtitulo); ?></p>
		<?php endif; ?>

		<?php if ($aw_marcos) : ?>
			<ol class="ae-marcos">
				<?php foreach (array_values($aw_marcos) as $aw_i => $aw_ano) : ?>
					<?php if ($aw_i > 0) : ?>
						<li class="ae-marcos__linha" aria-hidden="true"></li>
					<?php endif; ?>
					<li class="ae-marcos__marco">
						<span class="ae-marcos__ponto" aria-hidden="true"></span>
						<span class="ae-marcos__ano text-display-l"><?php echo esc_html($aw_ano); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>

	<div class="ae-origem__texto ae-texto">
		<?php aw_paragrafos($aw_texto, 'text-body-large'); ?>

		<?php if ($aw_destaque) : ?>
			<p class="ae-origem__destaque text-heading-h3"><?php echo esc_html($aw_destaque); ?></p>
		<?php endif; ?>

		<?php aw_paragrafos($aw_final, 'text-body-large'); ?>
	</div>
</section>
