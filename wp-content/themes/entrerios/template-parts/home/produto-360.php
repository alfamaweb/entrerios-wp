<?php
/**
 * Produto 360° da Home — "Produto 360° (animado · filtro de produto)" do Figma.
 *
 * O palco fica preso na tela (sticky) enquanto a página rola: a rolagem gira
 * o produto de 0° a 360° (troca as vistas), move o mostrador e a barra
 * "01 / 72". Os chips trocam o produto com dissolve de 400 ms.
 *
 * Campos (grupo "Home — Seções", aba Produto 360°): p360_itens (repeater:
 * rótulo do chip, linha, sobretítulo, título, texto, vistas, destaques,
 * cores do brilho e das órbitas) e p360_dica. Vazio esconde a seção.
 *
 * Sem JS (ou com prefers-reduced-motion) a seção não prende a rolagem e mostra
 * a vista frontal; os chips seguem funcionando com JS.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_post_id = get_the_ID();
$aw_dica    = (string) aw_field('p360_dica', $aw_post_id, '');
$aw_itens   = array();

foreach ((array) aw_field('p360_itens', $aw_post_id, array()) as $aw_item) {
	$aw_vistas = array_values(array_filter(array_map('intval', (array) ($aw_item['vistas'] ?? array()))));

	if (!$aw_vistas || empty($aw_item['titulo'])) {
		continue;
	}

	$aw_item['vistas'] = $aw_vistas;
	$aw_itens[]        = $aw_item;
}

if (!$aw_itens) {
	return;
}

/**
 * Cores próprias do item (brilho e órbitas) + cores da linha.
 *
 * @param array $item
 * @return string
 */
$aw_estilo = static function ($item) {
	$css = aw_linha_css_vars(!empty($item['linha']) ? (int) $item['linha'] : null);

	foreach (array('cor_brilho' => '--p360-brilho', 'cor_orbita' => '--p360-orbita') as $campo => $var) {
		$hex = sanitize_hex_color((string) ($item[$campo] ?? ''));
		if ($hex) {
			$css .= ($css ? ';' : '') . $var . ':' . $hex;
		}
	}

	return esc_attr($css);
};

$aw_total_quadros = 72; // Figma: "01 / 72" — um quadro a cada 5°.
?>
<section class="p360-er" data-er-p360 aria-label="<?php esc_attr_e('Produto em 360 graus', 'alfama-web'); ?>">
	<div class="p360-er__palco">
		<div class="p360-er__cena">

			<?php foreach ($aw_itens as $aw_i => $aw_item) : ?>
				<div class="p360-er__variante<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" id="p360-variante-<?php echo (int) $aw_i; ?>"
					data-er-p360-variante="<?php echo (int) $aw_i; ?>"
					<?php echo count($aw_itens) > 1 ? 'role="tabpanel" aria-labelledby="p360-chip-' . (int) $aw_i . '"' : ''; ?>
					style="<?php echo $aw_estilo($aw_item); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>"
					<?php echo 0 === $aw_i ? '' : 'hidden'; ?>>

					<div class="p360-er__fundo" aria-hidden="true">
						<span class="p360-er__brilho"></span>
						<span class="p360-er__orbita p360-er__orbita--2"></span>
						<span class="p360-er__orbita p360-er__orbita--1"></span>
						<span class="p360-er__chao"></span>
					</div>

					<div class="p360-er__vistas" data-er-p360-vistas>
						<?php foreach ($aw_item['vistas'] as $aw_v => $aw_vista) : ?>
							<?php echo wp_get_attachment_image($aw_vista, 'er-produto-360', false, array(
								'class'    => 'p360-er__vista' . (0 === $aw_v ? ' is-ativo' : ''),
								'alt'      => 0 === $aw_v ? $aw_item['titulo'] : '',
								'loading'  => 'lazy',
								'decoding' => 'async',
								'sizes'    => '(min-width: 1280px) 25vw, 60vw',
							)); ?>
						<?php endforeach; ?>
					</div>

					<div class="p360-er__conteudo">
						<?php if (!empty($aw_item['sobretitulo'])) : ?>
							<p class="p360-er__sobretitulo text-label-caps"><?php echo esc_html($aw_item['sobretitulo']); ?></p>
						<?php endif; ?>
						<h2 class="p360-er__titulo text-heading-h2"><?php echo esc_html($aw_item['titulo']); ?></h2>
						<?php if (!empty($aw_item['texto'])) : ?>
							<p class="p360-er__texto text-body-large"><?php echo nl2br(esc_html($aw_item['texto'])); ?></p>
						<?php endif; ?>
						<?php if ($aw_dica) : ?>
							<p class="p360-er__dica text-label-caps">
								<svg width="20" height="30" viewBox="0 0 20 30" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="1" y="1" width="18" height="28" rx="9" /><path class="p360-er__dica-roda" d="M10 7v5" stroke-linecap="round" /></svg>
								<span><?php echo esc_html($aw_dica); ?></span>
							</p>
						<?php endif; ?>
					</div>

					<?php foreach (array_slice((array) ($aw_item['destaques'] ?? array()), 0, 2) as $aw_d => $aw_destaque) :
						if (empty($aw_destaque['valor'])) {
							continue;
						}
						?>
						<div class="p360-er__destaque p360-er__destaque--<?php echo (int) $aw_d + 1; ?>" data-er-p360-destaque="<?php echo (int) $aw_d; ?>">
							<span class="p360-er__destaque-ponto" aria-hidden="true"></span>
							<span class="p360-er__destaque-linha" aria-hidden="true"></span>
							<span class="p360-er__destaque-pilula">
								<?php if (!empty($aw_destaque['rotulo'])) : ?>
									<span class="p360-er__destaque-rotulo text-label-caps"><?php echo esc_html($aw_destaque['rotulo']); ?></span>
								<?php endif; ?>
								<span class="p360-er__destaque-valor text-label-button"><?php echo esc_html($aw_destaque['valor']); ?></span>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<?php if (count($aw_itens) > 1) : ?>
				<div class="p360-er__filtro" role="tablist" aria-label="<?php esc_attr_e('Escolha o produto', 'alfama-web'); ?>">
					<?php foreach ($aw_itens as $aw_i => $aw_item) : ?>
						<button type="button" class="p360-er__chip text-label-button-sm<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" role="tab"
							id="p360-chip-<?php echo (int) $aw_i; ?>" aria-controls="p360-variante-<?php echo (int) $aw_i; ?>"
							aria-selected="<?php echo 0 === $aw_i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $aw_i ? '0' : '-1'; ?>"
							data-er-p360-chip="<?php echo (int) $aw_i; ?>">
							<?php echo esc_html(!empty($aw_item['rotulo']) ? $aw_item['rotulo'] : $aw_item['titulo']); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="p360-er__mostrador" aria-hidden="true">
				<svg class="p360-er__anel" viewBox="0 0 200 200">
					<circle class="p360-er__anel-trilho" cx="100" cy="100" r="92" />
					<circle class="p360-er__anel-progresso" cx="100" cy="100" r="92" pathLength="360" data-er-p360-arco />
				</svg>
				<span class="p360-er__botao-giro" data-er-p360-botao></span>
				<span class="p360-er__grau text-heading-h2" data-er-p360-grau>0°</span>
				<span class="p360-er__marca p360-er__marca--0 text-label-small">0°</span>
				<span class="p360-er__marca p360-er__marca--90 text-label-small">90°</span>
				<span class="p360-er__marca p360-er__marca--180 text-label-small">180°</span>
				<span class="p360-er__marca p360-er__marca--270 text-label-small">270°</span>
			</div>

			<div class="p360-er__sequencia" aria-hidden="true">
				<span class="p360-er__barra"><span class="p360-er__barra-cheia" data-er-p360-barra></span></span>
				<span class="p360-er__contador text-label-caps" data-er-p360-contador data-total="<?php echo (int) $aw_total_quadros; ?>">01 / <?php echo (int) $aw_total_quadros; ?></span>
			</div>
		</div>
	</div>
</section>
