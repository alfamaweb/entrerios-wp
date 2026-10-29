<?php
/**
 * Produto — Sanfona (Figma: Accordion, itens "Accordion/<título>").
 *
 * <details>/<summary> nativos: abrem e fecham sem JS, com teclado e leitor de
 * tela. O primeiro item nasce aberto (Figma: Composição = Open). Vidro:
 * fechado white/8 + borda white/20; aberto white/16 + borda white/40; blur 20.
 *
 * Campo: produto_sanfona (titulo, texto). Item sem título ou texto é pulado.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_itens = array_values(array_filter((array) aw_field('produto_sanfona', get_the_ID(), array()), function ($aw_item) {
	return !empty($aw_item['titulo']) && !empty($aw_item['texto']);
}));

if (!$aw_itens) {
	return;
}
?>
<div class="produto-sanfona">
	<?php foreach ($aw_itens as $aw_i => $aw_item) : ?>
		<details class="sanfona-item"<?php echo 0 === $aw_i ? ' open' : ''; ?>>
			<summary class="sanfona-item__cabecalho">
				<span class="sanfona-item__titulo"><?php echo esc_html($aw_item['titulo']); ?></span>
				<span class="sanfona-item__botao" aria-hidden="true">
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.73" stroke-linecap="round">
						<path d="M3.33 8h9.34" />
						<path class="sanfona-item__vertical" d="M8 3.33v9.34" />
					</svg>
				</span>
			</summary>
			<div class="sanfona-item__corpo">
				<?php aw_paragrafos($aw_item['texto'], 'text-body-large'); ?>
			</div>
		</details>
	<?php endforeach; ?>
</div>
