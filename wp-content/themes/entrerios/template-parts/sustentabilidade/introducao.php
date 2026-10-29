<?php
/**
 * Sustentabilidade — Introdução (Figma: Introdução).
 *
 * Texto à esquerda e, à direita, o card em degradê navy-500 → sky-500 com a
 * gota e a frase (Heading/H3). No Tablet/Mobile o card vai para baixo.
 *
 * Campos: sus_intro_texto (vazio esconde), sus_intro_citacao.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_texto = (string) aw_field('sus_intro_texto', get_the_ID(), '');

if ('' === trim($aw_texto)) {
	return;
}

$aw_citacao = trim((string) aw_field('sus_intro_citacao', get_the_ID(), ''));
?>
<section class="sus-intro" aria-label="<?php esc_attr_e('Introdução', 'alfama-web'); ?>">
	<div class="sus-intro__texto sus-texto">
		<?php aw_paragrafos($aw_texto, 'text-body-large'); ?>
	</div>

	<?php if ($aw_citacao) : ?>
		<blockquote class="sus-intro__citacao">
			<span class="sus-intro__gota" aria-hidden="true"><?php aw_svg('gota-cheia.svg'); ?></span>
			<p class="text-heading-h3"><?php echo nl2br(esc_html($aw_citacao), false); ?></p>
		</blockquote>
	<?php endif; ?>
</section>
