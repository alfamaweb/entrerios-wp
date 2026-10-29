<?php
/**
 * Letreiro da Home — "Marquee — Conheça Nossos Produtos (loop)" do Figma.
 *
 * A frase alterna cheia (brand/navy-900) e vazada (contorno 1.5 px
 * brand/navy-500), separada pelo Icon/Drop, e corre em loop contínuo. O loop
 * é só CSS (transform no compositor): a faixa é impressa duas vezes e anda
 * −50%. Só a primeira frase é lida por leitor de tela.
 *
 * Com o texto do Figma ("Conheça Nossos Produtos") a frase é o SVG exportado
 * do Figma (assets/img/marquee-conheca-nossos-produtos.svg): o contorno da
 * versão vazada fica só por fora, sem a sobreposição das letras que o
 * -webkit-text-stroke cria na Montserrat Black. O contorno vai uma vez para a
 * página e cada cópia usa <use>. Outro texto no campo cai no texto HTML.
 *
 * Campo: marquee_texto (grupo "Home — Letreiro e vitrine"). Vazio esconde.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_texto = (string) aw_field('marquee_texto', get_the_ID(), '');

if ('' === $aw_texto) {
	return;
}

// Cada metade da faixa: 2 pares (cheia + vazada) ≈ 4 100 px no Desktop —
// cobre qualquer tela sem buraco no loop.
$aw_pares = 2;

$aw_svg = 'CONHEÇA NOSSOS PRODUTOS' === mb_strtoupper(trim(preg_replace('/\s+/u', ' ', $aw_texto)))
	&& is_file(AW_DIR . '/assets/img/marquee-conheca-nossos-produtos.svg');

$aw_cheia = $aw_svg
	? '<svg class="marquee-er__svg" viewBox="-1.856 -7.46 905.344 64" overflow="visible" focusable="false"><use href="#er-mq-texto" fill="currentColor" /></svg>'
	: '<span class="marquee-er__texto">' . esc_html($aw_texto) . '</span>';

$aw_vazada = $aw_svg
	? '<svg class="marquee-er__svg marquee-er__svg--vazado" viewBox="-1.856 -7.46 905.344 64" overflow="visible" focusable="false"><use href="#er-mq-texto" fill="none" stroke="currentColor" stroke-width="3" mask="url(#er-mq-mascara)" /></svg>'
	: '<span class="marquee-er__texto marquee-er__texto--vazado">' . esc_html($aw_texto) . '</span>';
?>
<div class="marquee-er">
	<p class="sr-only"><?php echo esc_html($aw_texto); ?></p>
	<?php if ($aw_svg) : ?>
		<?php aw_svg('marquee-conheca-nossos-produtos.svg'); ?>
	<?php endif; ?>
	<div class="marquee-er__faixa" aria-hidden="true">
		<?php for ($aw_metade = 0; $aw_metade < 2; $aw_metade++) : ?>
			<div class="marquee-er__grupo">
				<?php for ($aw_i = 0; $aw_i < $aw_pares; $aw_i++) : ?>
					<?php echo $aw_cheia; // phpcs:ignore WordPress.Security.EscapeOutput -- montado acima com esc_html ?>
					<svg class="marquee-er__gota" viewBox="0 0 40 40" focusable="false"><path d="M20 2C20 2 8 17.5 8 25a12 12 0 0 0 24 0C32 17.5 20 2 20 2z" /></svg>
					<?php echo $aw_vazada; // phpcs:ignore WordPress.Security.EscapeOutput -- montado acima com esc_html ?>
					<svg class="marquee-er__gota" viewBox="0 0 40 40" focusable="false"><path d="M20 2C20 2 8 17.5 8 25a12 12 0 0 0 24 0C32 17.5 20 2 20 2z" /></svg>
				<?php endfor; ?>
			</div>
		<?php endfor; ?>
	</div>
</div>
