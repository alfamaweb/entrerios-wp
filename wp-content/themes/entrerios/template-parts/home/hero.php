<?php
/**
 * Hero Slider da Home — componente "Hero Slider" do Figma (variants Água
 * Mineral / Energéticos / Refrigerantes / Guaraná Mais).
 *
 * Um slide por linha de produto: o fundo em gradiente vem das cores da Linha
 * (taxonomia, ver inc/linhas.php); título, texto, produto e botão vêm do
 * repeater ACF "hero_slides" da página. O seletor de linhas embaixo é montado
 * da mesma lista, na mesma ordem.
 *
 * Camadas (de trás para frente): fundos em crossfade · palco (cena escalável
 * com brilho, palavra vazada, ondas, produtos e bolhas) · arco branco
 * (desktop) · conteúdo (texto, botão circular, anterior/próximo) · seletor.
 *
 * Altura: Desktop = clamp(90svh, proporção 16:10, 100svh); Tablet e Mobile =
 * 100svh. A cena escala para caber (ver o cabeçalho de assets/css/home.css).
 *
 * Sem JS o primeiro slide aparece completo; home-hero.js liga autoplay,
 * crossfade, seletor e anterior/próximo.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_post_id = get_the_ID();
$aw_slides  = array();

foreach ((array) aw_field('hero_slides', $aw_post_id, array()) as $aw_slide) {
	// Slide sem linha, título ou imagem não entra (campos obrigatórios).
	if (empty($aw_slide['linha']) || empty($aw_slide['titulo']) || empty($aw_slide['imagem'])) {
		continue;
	}
	$aw_slides[] = $aw_slide;
}

if (!$aw_slides) {
	return;
}

$aw_total     = count($aw_slides);
$aw_palavra   = (string) aw_field('hero_palavra_fundo', $aw_post_id, '');
$aw_cta       = aw_field('hero_cta_circular', $aw_post_id, array());
$aw_intervalo = (int) aw_field('hero_intervalo', $aw_post_id, 0);

/*
 * Bolhas do Figma (Hero Slider Desktop, 1600×1000): [diâmetro, top, left] da
 * posição inicial de cada uma — o CSS as faz subir. Tablet e mobile reusam
 * as mesmas proporções dentro do palco menor (adaptação, ver README).
 */
$aw_bolhas = array(
	array(34, 820, 700), array(22, 857, 1110), array(48, 894, 1190), array(18, 931, 660),
	array(26, 828, 1230), array(14, 865, 790), array(10, 902, 530), array(16, 939, 1410),
	array(12, 836, 870), array(20, 873, 1090), array(14, 910, 300), array(22, 947, 560),
	array(10, 844, 760), array(30, 881, 980), array(16, 918, 1180), array(12, 955, 1320),
	array(18, 852, 440), array(26, 889, 860), array(12, 926, 1060), array(20, 823, 1260),
	array(10, 860, 200), array(16, 897, 680),
);
?>
<section class="hero-er" data-er-hero data-intervalo="<?php echo esc_attr((string) $aw_intervalo); ?>"
	aria-roledescription="<?php esc_attr_e('carrossel', 'alfama-web'); ?>"
	aria-label="<?php esc_attr_e('Nossas linhas de produto', 'alfama-web'); ?>">

	<div class="hero-er__fundos" aria-hidden="true">
		<?php foreach ($aw_slides as $aw_i => $aw_slide) : ?>
			<div class="hero-er__fundo<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" data-er-slide="<?php echo (int) $aw_i; ?>"
				style="<?php echo aw_linha_css_vars($aw_slide['linha']); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>"></div>
		<?php endforeach; ?>
	</div>

	<div class="hero-er__palco">
		<div class="hero-er__cena">
			<div class="hero-er__brilho" aria-hidden="true"></div>

			<?php if ($aw_palavra) : ?>
				<?php
				// "ENTRE RIOS" usa o vetor exportado do Figma (contorno externo
				// exato); qualquer outro texto cai na fonte com -webkit-text-stroke.
				if ('ENTRE RIOS' === mb_strtoupper(trim($aw_palavra))) :
					aw_svg('hero-palavra-entre-rios.svg', array('class' => 'hero-er__palavra hero-er__palavra--svg'));
				else :
					?>
					<p class="hero-er__palavra" aria-hidden="true"><?php echo esc_html($aw_palavra); ?></p>
				<?php endif; ?>
			<?php endif; ?>

			<div class="hero-er__ondas" aria-hidden="true">
				<span class="hero-er__onda hero-er__onda--1"></span>
				<span class="hero-er__onda hero-er__onda--2"></span>
				<span class="hero-er__onda hero-er__onda--3"></span>
			</div>

			<?php foreach ($aw_slides as $aw_i => $aw_slide) :
				$aw_formato = ('copo' === ($aw_slide['formato'] ?? '')) ? 'copo' : 'garrafa';
				$aw_classes = 'hero-er__produto hero-er__produto--' . $aw_formato;
				$aw_classes .= !empty($aw_slide['inclinado']) ? ' is-inclinado' : '';
				$aw_classes .= 0 === $aw_i ? ' is-ativo' : '';
				?>
				<figure class="<?php echo esc_attr($aw_classes); ?>" data-er-slide="<?php echo (int) $aw_i; ?>">
					<?php echo wp_get_attachment_image((int) $aw_slide['imagem'], 'er-produto-hero', false, array(
						'class'         => 'hero-er__produto-img',
						'sizes'         => '(min-width: 1280px) 25vw, (min-width: 768px) 274px, 53vw',
						'loading'       => 0 === $aw_i ? 'eager' : 'lazy',
						'fetchpriority' => 0 === $aw_i ? 'high' : 'auto',
						'decoding'      => 'async',
					)); ?>
				</figure>
			<?php endforeach; ?>

			<div class="hero-er__bolhas" aria-hidden="true">
				<?php foreach ($aw_bolhas as $aw_b => $aw_bolha) : ?>
					<span class="hero-er__bolha" style="<?php echo esc_attr(sprintf('--b:%d;--y:%d;--x:%d;--n:%d', $aw_bolha[0], $aw_bolha[1], $aw_bolha[2], $aw_b)); ?>"></span>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="hero-er__arco" aria-hidden="true"></div>

	<div class="container hero-er__conteudo">
		<div class="hero-er__copias" id="hero-slides" aria-live="off">
			<?php foreach ($aw_slides as $aw_i => $aw_slide) :
				$aw_botao = $aw_slide['botao'] ?? array();
				$aw_tag   = 0 === $aw_i ? 'h1' : 'h2';
				?>
				<div class="hero-er__copia<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" data-er-slide="<?php echo (int) $aw_i; ?>"
					id="hero-slide-<?php echo (int) $aw_i; ?>" role="group" aria-roledescription="<?php esc_attr_e('slide', 'alfama-web'); ?>"
					aria-label="<?php echo esc_attr(sprintf('%d / %d', $aw_i + 1, $aw_total)); ?>"<?php echo 0 === $aw_i ? '' : ' inert'; ?>>

					<p class="hero-er__contador text-label-caps" aria-hidden="true">
						<span class="hero-er__divisor"></span>
						<?php echo esc_html(sprintf('%d / %d', $aw_i + 1, $aw_total)); ?>
					</p>

					<<?php echo $aw_tag; ?> class="hero-er__titulo text-heading-h1"><?php echo esc_html($aw_slide['titulo']); ?></<?php echo $aw_tag; ?>>

					<?php if (!empty($aw_slide['texto'])) : ?>
						<p class="hero-er__texto text-body-large"><?php echo esc_html($aw_slide['texto']); ?></p>
					<?php endif; ?>

					<?php if (!empty($aw_botao['url']) && !empty($aw_botao['title'])) : ?>
						<a class="botao-claro" href="<?php echo esc_url($aw_botao['url']); ?>"<?php echo !empty($aw_botao['target']) ? ' target="_blank" rel="noopener"' : ''; ?>>
							<span class="text-label-button"><?php echo esc_html($aw_botao['title']); ?></span>
							<span class="botao-claro__icone" aria-hidden="true">
								<svg width="16" height="14" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 7h14M9 1l6 6-6 6" /></svg>
							</span>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if (!empty($aw_cta['url']) && !empty($aw_cta['title'])) : ?>
			<a class="cta-circular" href="<?php echo esc_url($aw_cta['url']); ?>"<?php echo !empty($aw_cta['target']) ? ' target="_blank" rel="noopener"' : ''; ?>>
				<span class="cta-circular__anel" aria-hidden="true"></span>
				<span class="cta-circular__texto text-label-button"><?php echo esc_html($aw_cta['title']); ?></span>
				<svg class="cta-circular__seta" width="16" height="14" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 7h14M9 1l6 6-6 6" /></svg>
			</a>
		<?php endif; ?>

		<?php if ($aw_total > 1) : ?>
			<div class="hero-er__controles">
				<button type="button" class="link-texto text-label-button" data-er-anterior aria-controls="hero-slides"><?php esc_html_e('Anterior', 'alfama-web'); ?></button>
				<span class="text-label-button" aria-hidden="true">/</span>
				<button type="button" class="link-texto text-label-button" data-er-proximo aria-controls="hero-slides"><?php esc_html_e('Próximo', 'alfama-web'); ?></button>
			</div>
		<?php endif; ?>
	</div>

	<?php if ($aw_total > 1) : ?>
		<div class="hero-er__seletor">
			<div class="hero-er__chips" role="tablist" aria-label="<?php esc_attr_e('Escolha a linha', 'alfama-web'); ?>">
				<?php foreach ($aw_slides as $aw_i => $aw_slide) :
					$aw_termo = get_term($aw_slide['linha'], 'linha');
					if (!$aw_termo instanceof WP_Term) {
						continue;
					}
					?>
					<button type="button" class="chip-linha<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" role="tab"
						data-er-chip="<?php echo (int) $aw_i; ?>" aria-controls="hero-slide-<?php echo (int) $aw_i; ?>"
						aria-selected="<?php echo 0 === $aw_i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $aw_i ? '0' : '-1'; ?>"
						style="<?php echo aw_linha_css_vars($aw_termo); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>">
						<span class="chip-linha__thumb">
							<?php echo wp_get_attachment_image((int) $aw_slide['imagem'], 'er-chip', false, array(
								'class'   => 'chip-linha__img',
								'alt'     => '',
								'sizes'   => '112px',
								'loading' => 'lazy',
							)); ?>
						</span>
						<span class="chip-linha__rotulo text-label-caps"><?php echo esc_html($aw_termo->name); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
