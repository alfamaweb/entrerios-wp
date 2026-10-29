<?php
/**
 * A Entre Rios — Uma fábrica em transformação (Figma: Fábrica em Transformação).
 *
 * Card do vídeo 9:16 (530×746) com a capa e o Play Button. Quando o card
 * entra na tela, uma prévia curta e sem som toca em loop por baixo do botão
 * (como no protótipo); o vídeo completo, com som e controles, só é baixado no
 * clique em Play (assets/js/a-entre-rios.js). Sem JS, com
 * prefers-reduced-motion ou com economia de dados, fica só a capa.
 *
 * Campos: ae_fabrica_titulo (vazio esconde), ae_fabrica_subtitulo,
 * ae_fabrica_blocos, ae_fabrica_citacao, ae_fabrica_citacao_texto,
 * ae_fabrica_video, ae_fabrica_previa, ae_fabrica_capa.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('ae_fabrica_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_subtitulo = (string) aw_field('ae_fabrica_subtitulo', $aw_id, '');
$aw_blocos    = (array) aw_field('ae_fabrica_blocos', $aw_id, array());
$aw_citacao   = (string) aw_field('ae_fabrica_citacao', $aw_id, '');
$aw_cit_texto = (string) aw_field('ae_fabrica_citacao_texto', $aw_id, '');
$aw_capa      = (int) aw_field('ae_fabrica_capa', $aw_id, 0);
$aw_video     = aw_field('ae_fabrica_video', $aw_id, array());
$aw_video     = is_array($aw_video) && !empty($aw_video['url']) ? $aw_video : null;
$aw_previa    = aw_field('ae_fabrica_previa', $aw_id, array());
$aw_previa    = is_array($aw_previa) && !empty($aw_previa['url']) ? $aw_previa : null;

$aw_atributos = '';
if ($aw_video) {
	$aw_atributos .= ' data-src="' . esc_url($aw_video['url']) . '"';
}
if ($aw_previa) {
	$aw_atributos .= ' data-previa="' . esc_url($aw_previa['url']) . '"';
}
?>
<section class="ae-fabrica" aria-labelledby="ae-fabrica-titulo">
	<?php if ($aw_capa || $aw_video) : ?>
		<div class="ae-video" data-ae-video<?php echo $aw_atributos; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com esc_url acima ?>>
			<?php if ($aw_capa) : ?>
				<?php echo wp_get_attachment_image($aw_capa, 'full', false, array(
					'class'    => 'ae-video__capa',
					'alt'      => '',
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => '(min-width: 1280px) 530px, (min-width: 768px) 460px, 100vw',
				)); ?>
			<?php endif; ?>

			<?php if ($aw_video) : ?>
				<button type="button" class="botao-play" data-ae-video-play>
					<span class="sr-only"><?php echo esc_html(sprintf(/* translators: %s: título da seção */ __('Assistir ao vídeo — %s', 'alfama-web'), $aw_titulo)); ?></span>
					<svg width="34" height="38" viewBox="0 0 34 38" aria-hidden="true"><path d="M32 16.4a3 3 0 010 5.2L5.5 36.9A3 3 0 011 34.3V3.7a3 3 0 014.5-2.6z" fill="currentColor" /></svg>
				</button>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="ae-fabrica__texto ae-texto">
		<h2 class="ae-titulo text-heading-h2" id="ae-fabrica-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		<?php if ($aw_subtitulo) : ?>
			<p class="ae-fabrica__subtitulo text-heading-h3"><?php echo esc_html($aw_subtitulo); ?></p>
		<?php endif; ?>

		<?php get_template_part('template-parts/a-entre-rios/blocos', null, array('blocos' => $aw_blocos)); ?>

		<?php if ($aw_citacao) : ?>
			<figure class="ae-citacao">
				<svg class="ae-citacao__aspas" width="34" height="26" viewBox="0 0 34 26" aria-hidden="true"><path fill="currentColor" d="M0 26V15.6C0 6.9 4.3 1.7 12.6 0l1.6 3.4C9.5 4.9 7.2 7.8 7 12h6.6v14H0zm20.4 0V15.6c0-8.7 4.3-13.9 12.6-15.6L34.6 3.4C29.9 4.9 27.6 7.8 27.4 12H34v14H20.4z"/></svg>
				<blockquote class="ae-citacao__frase text-heading-h3"><p><?php echo esc_html($aw_citacao); ?></p></blockquote>
				<?php if ($aw_cit_texto) : ?>
					<figcaption class="ae-citacao__texto text-body-large"><?php echo nl2br(esc_html($aw_cit_texto), false); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>
	</div>
</section>
