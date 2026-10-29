<?php
/**
 * Product Card do Figma (componente "Product Card", 296×420).
 *
 * Cores pela Linha do produto (style="--linha-…"): círculo claro → médio,
 * botão médio → forte, nome em forte no hover. Leva para a página do produto
 * (single-produto.php — Figma: "Navigate to Screen/Product/<linha>").
 *
 * Uso: get_template_part('template-parts/card-produto', null, array('post' => $id));
 * Também aceita 'post_id' (contrato do AW_Query_Service::render(), usado na
 * página Produtos) e, sem nenhum dos dois, o post do loop.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id    = (int) ($args['post'] ?? $args['post_id'] ?? get_the_ID());
$aw_foto  = (int) get_post_thumbnail_id($aw_id);
$aw_nome  = get_the_title($aw_id);
$aw_linha = wp_get_post_terms($aw_id, 'linha', array('number' => 1));
$aw_linha = (!is_wp_error($aw_linha) && $aw_linha) ? $aw_linha[0] : null;
$aw_link  = get_permalink($aw_id);

if (!$aw_foto || !$aw_link) {
	return;
}
?>
<a class="card-produto" href="<?php echo esc_url($aw_link); ?>"<?php echo $aw_linha ? ' style="' . aw_linha_css_vars($aw_linha) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>>
	<span class="card-produto__palco">
		<span class="card-produto__blob" aria-hidden="true"></span>
		<?php echo wp_get_attachment_image($aw_foto, 'er-card-produto', false, array(
			'class'    => 'card-produto__img',
			'alt'      => '',
			'sizes'    => '200px',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)); ?>
	</span>
	<span class="card-produto__rodape">
		<span class="card-produto__nome text-label-button"><?php echo esc_html($aw_nome); ?></span>
		<span class="card-produto__ir" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h12M10 4l5 5-5 5" /></svg>
		</span>
	</span>
</a>
