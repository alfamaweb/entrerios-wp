<?php
/**
 * A Entre Rios — Galeria (Figma: Galeria).
 *
 * Carrossel de rolagem horizontal nativa (dedo, trackpad, teclado), com
 * scroll-snap. As setas, o contador "01 / 02" e os pontos são ligados por
 * assets/js/a-entre-rios.js; sem JS a rolagem continua funcionando e os
 * controles ficam escondidos.
 *
 * Campos: ae_galeria_rotulo, ae_galeria_titulo, ae_galeria_imagens (vazio
 * em qualquer um dos dois últimos esconde a seção).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id      = get_the_ID();
$aw_titulo  = (string) aw_field('ae_galeria_titulo', $aw_id, '');
$aw_imagens = array_filter(array_map('intval', (array) aw_field('ae_galeria_imagens', $aw_id, array())));

if ('' === $aw_titulo || !$aw_imagens) {
	return;
}

$aw_rotulo = (string) aw_field('ae_galeria_rotulo', $aw_id, '');
$aw_total  = count($aw_imagens);
$aw_fmt    = static function ($n) {
	return str_pad((string) $n, 2, '0', STR_PAD_LEFT);
};
?>
<section class="ae-galeria" aria-labelledby="ae-galeria-titulo" data-ae-galeria>
	<div class="ae-galeria__topo">
		<div class="ae-galeria__titulo">
			<?php if ($aw_rotulo) : ?>
				<p class="ae-rotulo text-label-caps"><?php echo esc_html($aw_rotulo); ?></p>
			<?php endif; ?>
			<h2 class="ae-titulo text-heading-h2" id="ae-galeria-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		</div>

		<?php if ($aw_total > 1) : ?>
			<div class="ae-galeria__nav" data-ae-galeria-nav hidden>
				<p class="ae-galeria__contador text-label-caps" aria-live="polite">
					<span data-ae-galeria-atual>01</span> / <?php echo esc_html($aw_fmt($aw_total)); ?>
				</p>
				<button type="button" class="seta-vitrine seta-vitrine--anterior" data-ae-galeria-anterior aria-label="<?php esc_attr_e('Foto anterior', 'alfama-web'); ?>">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 9H3M8 4L3 9l5 5" /></svg>
				</button>
				<button type="button" class="seta-vitrine seta-vitrine--proxima" data-ae-galeria-proxima aria-label="<?php esc_attr_e('Próxima foto', 'alfama-web'); ?>">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9h12M10 4l5 5-5 5" /></svg>
				</button>
			</div>
		<?php endif; ?>
	</div>

	<ul class="ae-galeria__trilho" data-ae-galeria-trilho tabindex="0" aria-label="<?php echo esc_attr($aw_titulo); ?>">
		<?php foreach (array_values($aw_imagens) as $aw_i => $aw_img) : ?>
			<li class="ae-galeria__slide">
				<?php echo wp_get_attachment_image($aw_img, 'er-galeria', false, array(
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => '(min-width: 1280px) 1100px, (min-width: 768px) 80vw, 82vw',
				)); ?>
				<span class="ae-galeria__indice text-label-caps" aria-hidden="true"><?php echo esc_html($aw_fmt($aw_i + 1)); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ($aw_total > 1) : ?>
		<div class="ae-galeria__pontos" data-ae-galeria-pontos hidden>
			<?php for ($aw_i = 1; $aw_i <= $aw_total; $aw_i++) : ?>
				<button type="button" class="ae-galeria__ponto"<?php echo 1 === $aw_i ? ' aria-current="true"' : ''; ?> aria-label="<?php echo esc_attr(sprintf(/* translators: %d: número da foto */ __('Ir para a foto %d', 'alfama-web'), $aw_i)); ?>"></button>
			<?php endfor; ?>
		</div>
	<?php endif; ?>
</section>
