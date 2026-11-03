<?php
/**
 * Sustentabilidade — Manifesto (Figma: Introdução).
 *
 * Coluna da esquerda: dois parágrafos de texto corrido.
 * Coluna da direita: Card em degradê azul com ícone de gota e frase em destaque.
 *
 * Campos: sust_manifesto_texto (vazio esconde), sust_manifesto_destaque.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id    = get_the_ID();
$aw_texto = (string) aw_field('sust_manifesto_texto', $aw_id, '');

if ('' === $aw_texto) {
	return;
}

$aw_destaque = (string) aw_field('sust_manifesto_destaque', $aw_id, '');
?>
<section class="sust-manifesto" aria-label="<?php esc_attr_e('Manifesto de Sustentabilidade', 'alfama-web'); ?>">
	<div class="sust-manifesto__container">
		<div class="sust-manifesto__texto">
			<?php aw_paragrafos($aw_texto, 'text-body-large'); ?>
		</div>

		<?php if ($aw_destaque) : ?>
			<div class="sust-manifesto__card">
				<span class="sust-manifesto__icone" aria-hidden="true">
					<?php aw_svg('icone-gota.svg'); ?>
				</span>
				<p class="sust-manifesto__frase text-heading-h3"><?php echo nl2br(esc_html($aw_destaque), false); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

