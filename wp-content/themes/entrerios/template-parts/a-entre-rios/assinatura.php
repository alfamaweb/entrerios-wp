<?php
/**
 * A Entre Rios — Assinatura (Figma: Assinatura).
 *
 * "ENTRE RIOS" em Display/XXL com degradê navy-500 → sky-500 no texto,
 * ocupando a largura, e a frase centralizada embaixo. Texto de verdade (não
 * imagem): o degradê é background-clip: text.
 *
 * Campos: ae_assinatura_palavra (vazio esconde), ae_assinatura_frase.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_palavra = (string) aw_field('ae_assinatura_palavra', get_the_ID(), '');

if ('' === $aw_palavra) {
	return;
}

$aw_frase = (string) aw_field('ae_assinatura_frase', get_the_ID(), '');
?>
<section class="ae-assinatura">
	<p class="ae-assinatura__palavra"><?php echo esc_html($aw_palavra); ?></p>
	<?php if ($aw_frase) : ?>
		<p class="ae-assinatura__frase text-heading-h3"><?php echo nl2br(esc_html(trim($aw_frase)), false); ?></p>
	<?php endif; ?>
</section>
