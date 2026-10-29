<?php
/**
 * Produto — Palco (Figma: Stage, 620 × 760 no Desktop · 350 × 429 no Mobile).
 *
 * Camadas, de trás para a frente: Glow (radial na cor média da linha, 50% → 0),
 * Blob 480 (degradê tom claro → tom forte da linha), Ring 580 tracejado
 * (4/10, white/25), 8 bolhas e a foto com sombra. Tudo em % do palco, que
 * escala junto com a largura da coluna.
 *
 * A foto: produto_imagem (alta resolução, opcional) ou a imagem destacada.
 * A altura da foto muda pela embalagem — as fotos ocupam a altura toda do
 * arquivo, então a altura do Figma vale direto: garrafa 720 em y 20, lata 640
 * em y 60. Copo e garrafão (mais largos) não foram conferidos no Figma:
 * 500 centrados no círculo, para não passarem da largura dele.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id      = get_the_ID();
$aw_foto    = (int) aw_field('produto_imagem', $aw_id, 0) ?: (int) get_post_thumbnail_id($aw_id);
$aw_formatos = array('lata' => 'lata', 'copo' => 'largo', 'garrafao-galao' => 'largo');
$aw_formato  = $aw_formatos[aw_produto_embalagem($aw_id)] ?? 'alto';

if (!$aw_foto) {
	return;
}
?>
<div class="produto-palco produto-palco--<?php echo esc_attr($aw_formato); ?>">
	<span class="produto-palco__brilho" aria-hidden="true"></span>
	<span class="produto-palco__blob" aria-hidden="true"></span>
	<svg class="produto-palco__anel" viewBox="0 0 580 580" aria-hidden="true" focusable="false">
		<circle cx="290" cy="290" r="289.25" fill="none" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 10" />
	</svg>
	<?php for ($aw_i = 1; $aw_i <= 8; $aw_i++) : ?>
		<span class="produto-palco__bolha produto-palco__bolha--<?php echo (int) $aw_i; ?>" aria-hidden="true"></span>
	<?php endfor; ?>

	<?php echo wp_get_attachment_image($aw_foto, 'full', false, array(
		'class'         => 'produto-palco__foto',
		'alt'           => get_the_title($aw_id),
		'sizes'         => '(min-width: 1280px) 300px, (min-width: 768px) 40vw, 60vw',
		'loading'       => 'eager',
		'fetchpriority' => 'high',
		'decoding'      => 'async',
	)); ?>
</div>
