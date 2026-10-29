<?php
/**
 * Linhas de bebidas da Home — "Linhas de Bebidas (bento)" + componente
 * "Linha Card" do Figma.
 *
 * Grade de cards (2 por linha no Desktop), um por linha de produto: gradiente
 * da linha (média → forte), nome da linha, nomes dos produtos, fotos em leque
 * e o nome da linha vazado ao fundo (SVG exportado do Figma em
 * assets/img/linha-palavra-<slug>.svg). O card inteiro é o link.
 *
 * Campo (grupo "Home — Seções", aba Linhas): linhas_cards — repeater com
 * linha, link e até 3 produtos (produto + tamanho da foto). Vazio esconde.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_cards = array();

foreach ((array) aw_field('linhas_cards', get_the_ID(), array()) as $aw_card) {
	$aw_termo = !empty($aw_card['linha']) ? get_term((int) $aw_card['linha'], 'linha') : null;

	if (!$aw_termo instanceof WP_Term || empty($aw_card['link']['url'])) {
		continue;
	}

	$aw_produtos = array();

	foreach ((array) ($aw_card['produtos'] ?? array()) as $aw_item) {
		$aw_id = (int) ($aw_item['produto'] ?? 0);

		if ($aw_id && 'publish' === get_post_status($aw_id)) {
			$aw_produtos[] = array(
				'id'      => $aw_id,
				'tamanho' => in_array($aw_item['tamanho'] ?? '', array('p', 'm', 'g'), true) ? $aw_item['tamanho'] : 'm',
			);
		}
	}

	$aw_cards[] = array('termo' => $aw_termo, 'link' => $aw_card['link'], 'produtos' => array_slice($aw_produtos, 0, 3));
}

if (!$aw_cards) {
	return;
}
?>
<section class="linhas-er" aria-label="<?php esc_attr_e('Linhas de bebidas', 'alfama-web'); ?>">
	<ul class="linhas-er__grade">
		<?php foreach ($aw_cards as $aw_card) :
			$aw_termo    = $aw_card['termo'];
			$aw_qtd      = count($aw_card['produtos']);
			$aw_palavra  = 'linha-palavra-' . $aw_termo->slug . '.svg';
			$aw_tem_svg  = is_file(AW_DIR . '/assets/img/' . $aw_palavra);
			?>
			<li class="linhas-er__item">
				<a class="linha-card" href="<?php echo esc_url($aw_card['link']['url']); ?>"<?php echo !empty($aw_card['link']['target']) ? ' target="_blank" rel="noopener"' : ''; ?>
					style="<?php echo aw_linha_css_vars($aw_termo); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>">

					<?php if ($aw_tem_svg) : ?>
						<?php aw_svg($aw_palavra, array('class' => 'linha-card__palavra')); ?>
					<?php else : ?>
						<span class="linha-card__palavra linha-card__palavra--texto" aria-hidden="true"><?php echo esc_html(mb_strtoupper($aw_termo->name)); ?></span>
					<?php endif; ?>

					<span class="linha-card__info">
						<span class="linha-card__titulo text-heading-h3"><?php echo esc_html($aw_termo->name); ?></span>
						<?php if ($aw_card['produtos']) : ?>
							<span class="linha-card__lista">
								<?php foreach ($aw_card['produtos'] as $aw_produto) : ?>
									<span class="text-label-small"><?php echo esc_html(get_the_title($aw_produto['id'])); ?></span>
								<?php endforeach; ?>
							</span>
						<?php endif; ?>
					</span>

					<span class="linha-card__ir" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14L14 4M6 4h8v8" /></svg>
					</span>

					<span class="linha-card__produtos linha-card__produtos--<?php echo (int) $aw_qtd; ?>" aria-hidden="true">
						<?php foreach ($aw_card['produtos'] as $aw_i => $aw_produto) :
							$aw_foto = get_post_thumbnail_id($aw_produto['id']);
							if (!$aw_foto) {
								continue;
							}
							?>
							<span class="linha-card__produto linha-card__produto--<?php echo esc_attr($aw_produto['tamanho']); ?>" style="--i:<?php echo (int) $aw_i; ?>">
								<?php echo wp_get_attachment_image($aw_foto, 'er-card-produto', false, array(
									'class'    => 'linha-card__foto',
									'alt'      => '',
									'loading'  => 'lazy',
									'decoding' => 'async',
									'sizes'    => '(min-width: 1280px) 160px, 30vw',
								)); ?>
							</span>
						<?php endforeach; ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
