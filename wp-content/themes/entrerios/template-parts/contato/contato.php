<?php
/**
 * Contato — corpo da página (Figma: Contato, 1600 × 879; Mobile 390 × 1459).
 *
 * Duas colunas no Desktop (Formulário 760 + Localização 480, gap 40) e uma
 * pilha no Tablet/Mobile. Cada coluna some sozinha quando o campo-gatilho
 * dela está vazio; sem nenhuma das duas, a seção inteira some.
 *
 * Gatilhos: contato_form_titulo (formulário) · contato_info_titulo ou
 * contato_mapa_embed (localização).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_form   = (string) aw_field('contato_form_titulo', $aw_id, '');
$aw_info   = (string) aw_field('contato_info_titulo', $aw_id, '');
$aw_mapa   = aw_google_maps_embed((string) aw_field('contato_mapa_embed', $aw_id, ''));

if ('' === $aw_form && '' === $aw_info && '' === $aw_mapa) {
	return;
}
?>
<section class="contato-corpo"<?php echo $aw_form ? ' aria-labelledby="contato-form-titulo"' : ' aria-label="' . esc_attr__('Contato', 'alfama-web') . '"'; ?>>
	<div class="contato-corpo__grade">
		<?php get_template_part('template-parts/contato/formulario'); ?>
		<?php get_template_part('template-parts/contato/localizacao', null, array('mapa' => $aw_mapa)); ?>
	</div>
</section>
