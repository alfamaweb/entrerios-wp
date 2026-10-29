<?php
/**
 * A Entre Rios — O futuro já começou (Figma: Futuro).
 *
 * Fundo em degradê navy-500 → royal-500 → sky-500, título Display/XL e duas
 * colunas: texto à esquerda e o manifesto (frases H2, a marcada em
 * azul-claro) à direita.
 *
 * Campos: ae_futuro_titulo (vazio esconde), ae_futuro_subtitulo,
 * ae_futuro_texto, ae_futuro_manifesto (frase + destaque).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('ae_futuro_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_subtitulo = (string) aw_field('ae_futuro_subtitulo', $aw_id, '');
$aw_texto     = (string) aw_field('ae_futuro_texto', $aw_id, '');
$aw_frases    = array_filter((array) aw_field('ae_futuro_manifesto', $aw_id, array()), static function ($f) {
	return '' !== trim((string) ($f['frase'] ?? ''));
});
?>
<section class="ae-futuro" aria-labelledby="ae-futuro-titulo">
	<h2 class="ae-futuro__titulo text-display-xl" id="ae-futuro-titulo"><?php echo esc_html($aw_titulo); ?></h2>

	<div class="ae-futuro__conteudo">
		<div class="ae-futuro__texto ae-texto">
			<?php if ($aw_subtitulo) : ?>
				<p class="ae-futuro__subtitulo text-heading-h3"><?php echo esc_html($aw_subtitulo); ?></p>
			<?php endif; ?>
			<?php aw_paragrafos($aw_texto, 'text-body-large'); ?>
		</div>

		<?php if ($aw_frases) : ?>
			<div class="ae-futuro__manifesto">
				<?php foreach ($aw_frases as $aw_frase) : ?>
					<p class="ae-futuro__frase text-heading-h2<?php echo !empty($aw_frase['destaque']) ? ' is-destaque' : ''; ?>"><?php echo esc_html(trim((string) $aw_frase['frase'])); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
