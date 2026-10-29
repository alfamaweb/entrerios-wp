<?php
/**
 * Sobre a Entre Rios na Home — "Sobre a Entre Rios" do Figma.
 *
 * Card do vídeo institucional (capa + Play Button + "2002" vazado, SVG
 * exportado do Figma) e o texto com o botão para a página A Entre Rios.
 * O vídeo só é baixado quando a pessoa clica em Play (assets/js/sobre.js).
 *
 * Campos (grupo "Home — Seções", aba Sobre): sobre_video, sobre_capa,
 * sobre_rotulo, sobre_titulo, sobre_destaque, sobre_texto e sobre_botao.
 * Sem título a seção some; sem vídeo, o card mostra só a capa.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_post_id  = get_the_ID();
$aw_titulo   = (string) aw_field('sobre_titulo', $aw_post_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_rotulo   = (string) aw_field('sobre_rotulo', $aw_post_id, '');
$aw_destaque = (string) aw_field('sobre_destaque', $aw_post_id, '');
$aw_texto    = (string) aw_field('sobre_texto', $aw_post_id, '');
$aw_botao    = aw_field('sobre_botao', $aw_post_id, array());
$aw_capa     = (int) aw_field('sobre_capa', $aw_post_id, 0);
$aw_video    = aw_field('sobre_video', $aw_post_id, array());
$aw_video    = is_array($aw_video) && !empty($aw_video['url']) ? $aw_video : null;
?>
<section class="sobre-er" aria-labelledby="sobre-titulo">
	<?php if ($aw_capa || $aw_video) : ?>
		<div class="sobre-er__video" data-er-video<?php echo $aw_video ? ' data-src="' . esc_url($aw_video['url']) . '" data-tipo="' . esc_attr($aw_video['mime_type'] ?? 'video/mp4') . '"' : ''; ?>>
			<?php if ($aw_capa) : ?>
				<?php echo wp_get_attachment_image($aw_capa, 'er-sobre-capa', false, array(
					'class'    => 'sobre-er__capa',
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => '(min-width: 1280px) 560px, (min-width: 768px) 50vw, 100vw',
				)); ?>
			<?php endif; ?>

			<?php aw_svg('sobre-2002.svg', array('class' => 'sobre-er__ano')); ?>

			<?php if ($aw_video) : ?>
				<button type="button" class="botao-play" data-er-video-play>
					<span class="sr-only"><?php echo esc_html(sprintf(/* translators: %s: título da seção */ __('Assistir ao vídeo — %s', 'alfama-web'), $aw_titulo)); ?></span>
					<svg width="34" height="38" viewBox="0 0 34 38" aria-hidden="true"><path d="M32 16.4a3 3 0 010 5.2L5.5 36.9A3 3 0 011 34.3V3.7a3 3 0 014.5-2.6z" fill="currentColor" /></svg>
				</button>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="sobre-er__conteudo">
		<?php if ($aw_rotulo) : ?>
			<p class="sobre-er__rotulo text-label-caps"><?php echo esc_html($aw_rotulo); ?></p>
		<?php endif; ?>
		<h2 class="sobre-er__titulo text-heading-h2" id="sobre-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		<?php if ($aw_destaque) : ?>
			<p class="sobre-er__destaque text-body-large"><?php echo nl2br(esc_html($aw_destaque)); ?></p>
		<?php endif; ?>
		<?php if ($aw_texto) : ?>
			<p class="sobre-er__texto text-body-regular"><?php echo nl2br(esc_html($aw_texto)); ?></p>
		<?php endif; ?>

		<?php if (!empty($aw_botao['url']) && !empty($aw_botao['title'])) : ?>
			<a class="botao-primario sobre-er__botao" href="<?php echo esc_url($aw_botao['url']); ?>"<?php echo !empty($aw_botao['target']) ? ' target="_blank" rel="noopener"' : ''; ?>>
				<span class="text-label-button"><?php echo esc_html($aw_botao['title']); ?></span>
				<span class="botao-primario__icone" aria-hidden="true">
					<svg width="16" height="14" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 7h14M9 1l6 6-6 6" /></svg>
				</span>
			</a>
		<?php endif; ?>
	</div>
</section>
