<?php
/**
 * A Entre Rios — Pessoas que fazem acontecer (Figma: Pessoas).
 *
 * Campos: ae_pessoas_titulo (vazio esconde), ae_pessoas_subtitulo,
 * ae_pessoas_texto, ae_pessoas_destaque (card branco com ícone).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('ae_pessoas_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_subtitulo = (string) aw_field('ae_pessoas_subtitulo', $aw_id, '');
$aw_texto     = (string) aw_field('ae_pessoas_texto', $aw_id, '');
$aw_destaque  = (string) aw_field('ae_pessoas_destaque', $aw_id, '');
?>
<section class="ae-pessoas" aria-labelledby="ae-pessoas-titulo">
	<div class="ae-pessoas__statement">
		<h2 class="ae-titulo text-heading-h2" id="ae-pessoas-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		<?php if ($aw_subtitulo) : ?>
			<p class="ae-pessoas__subtitulo text-heading-h3"><?php echo nl2br(esc_html($aw_subtitulo), false); ?></p>
		<?php endif; ?>
	</div>

	<div class="ae-pessoas__texto ae-texto">
		<?php aw_paragrafos($aw_texto, 'text-body-large'); ?>

		<?php if ($aw_destaque) : ?>
			<p class="ae-pessoas__destaque">
				<span class="ae-pessoas__icone" aria-hidden="true">
					<svg width="26" height="26" viewBox="0 0 26 26" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="9.7" cy="8.7" r="3.5"/><path d="M3.3 20.6v-1a4.9 4.9 0 0 1 4.9-4.9h3a4.9 4.9 0 0 1 4.9 4.9v1"/><circle cx="18.4" cy="9.5" r="2.7"/><path d="M18.4 14.9a4.4 4.4 0 0 1 4.3 4.4v1"/></svg>
				</span>
				<span class="ae-pessoas__frase"><?php echo esc_html($aw_destaque); ?></span>
			</p>
		<?php endif; ?>
	</div>
</section>
