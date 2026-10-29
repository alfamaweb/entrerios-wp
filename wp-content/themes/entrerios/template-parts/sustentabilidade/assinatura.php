<?php
/**
 * Sustentabilidade — Assinatura (Figma: Assinatura + componente "Frase
 * (digitação)").
 *
 * Gota em degradê e a frase final, uma linha por verso, em Display/L com
 * degradê navy-500 → sky-500 no texto. Efeito do Figma: ao entrar na tela,
 * cada linha é "digitada" (linear, ~55 ms por caractere) com o cursor sky-500,
 * que pisca no fim (assets/js/sustentabilidade.js). Sem JS ou com movimento
 * reduzido, a frase aparece inteira. O texto é real (leitores de tela leem
 * tudo de uma vez: o recorte é só visual).
 *
 * Campo: sus_assinatura_frase (uma linha por verso; vazio esconde).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_linhas = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) aw_field('sus_assinatura_frase', get_the_ID(), '')))));

if (!$aw_linhas) {
	return;
}
?>
<section class="sus-assinatura">
	<span class="sus-assinatura__gota" aria-hidden="true"><?php aw_svg('gota-degrade.svg'); ?></span>

	<p class="sus-digitacao" data-sus-digitacao>
		<?php foreach ($aw_linhas as $aw_linha) : ?>
			<span class="sus-digitacao__linha" style="--sus-letras: <?php echo (int) max(1, mb_strlen($aw_linha)); ?>"><span class="sus-digitacao__texto"><?php echo esc_html($aw_linha); ?></span></span>
		<?php endforeach; ?>
	</p>
</section>
