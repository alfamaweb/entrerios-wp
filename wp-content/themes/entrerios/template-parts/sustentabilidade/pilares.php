<?php
/**
 * Sustentabilidade — Pilares 01–04 (Figma: Pilar 0N — <TEMA>).
 *
 * Cada pilar é uma seção: card visual 560 × 600 e o texto (número vazado,
 * sobretítulo, H2, parágrafos e frase com a barra). Ímpares: card à esquerda
 * e fundo branco; pares: card à direita e fundo lavanda — como no Figma. No
 * Tablet/Mobile o card vem sempre antes do texto.
 *
 * O número é automático (assets/img/sustentabilidade-numero-0N.svg, contorno
 * exportado do Figma; do 05 em diante, texto). O card é montado em código a
 * partir dos campos, nas medidas do Figma:
 *  - profundidade: degradê sky-300 → navy-900, faixas, bolhas, fio + gota e o
 *    valor (ex.: 60 m);
 *  - foto: imagem + degradê navy-900 embaixo;
 *  - produtos: fundo azul ou laranja, círculo e até 4 produtos recortados.
 *
 * Campo: sus_pilares (rotulo, titulo — vazio esconde o pilar —, texto,
 * destaque, visual, selo, valor, imagem, fundo, produtos).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_pilares = array_values(array_filter((array) aw_field('sus_pilares', get_the_ID(), array()), static function ($p) {
	return is_array($p) && '' !== trim((string) ($p['titulo'] ?? ''));
}));

if (!$aw_pilares) {
	return;
}

$aw_selos = array('escudo', 'reciclagem', 'folha');

foreach ($aw_pilares as $aw_i => $aw_pilar) :
	$aw_numero   = sprintf('%02d', $aw_i + 1);
	$aw_id       = 'sus-pilar-' . $aw_numero;
	$aw_visual   = in_array($aw_pilar['visual'] ?? '', array('profundidade', 'foto', 'produtos'), true) ? $aw_pilar['visual'] : 'foto';
	$aw_selo     = in_array($aw_pilar['selo'] ?? '', $aw_selos, true) && 'profundidade' !== $aw_visual ? $aw_pilar['selo'] : '';
	$aw_fundo    = 'laranja' === ($aw_pilar['fundo'] ?? '') ? 'laranja' : 'azul';
	$aw_imagem   = (int) ($aw_pilar['imagem'] ?? 0);
	$aw_produtos = array_slice(array_filter(array_map('intval', (array) ($aw_pilar['produtos'] ?? array()))), 0, 4);
	$aw_valor    = trim((string) ($aw_pilar['valor'] ?? ''));
	$aw_svg_num  = 'sustentabilidade-numero-' . $aw_numero . '.svg';

	$aw_classes = array('sus-visual', 'sus-visual--' . $aw_visual);
	if ('produtos' === $aw_visual) {
		$aw_classes[] = 'sus-visual--' . $aw_fundo;
	}
	?>
	<section class="sus-pilar<?php echo 1 === $aw_i % 2 ? ' sus-pilar--par' : ''; ?><?php echo 0 === $aw_i ? ' sus-pilar--primeiro' : ''; ?>" id="<?php echo esc_attr($aw_id); ?>" aria-labelledby="<?php echo esc_attr($aw_id); ?>-titulo">
		<div class="<?php echo esc_attr(implode(' ', $aw_classes)); ?>"<?php echo 'produtos' === $aw_visual ? ' data-qtd="' . count($aw_produtos) . '"' : ''; ?>>
			<?php if ('profundidade' === $aw_visual) : ?>
				<div class="sus-fonte" aria-hidden="true">
					<?php for ($aw_c = 1; $aw_c <= 5; $aw_c++) : ?>
						<span class="sus-fonte__camada"></span>
					<?php endfor; ?>
					<?php for ($aw_b = 1; $aw_b <= 5; $aw_b++) : ?>
						<span class="sus-fonte__bolha"></span>
					<?php endfor; ?>
					<span class="sus-fonte__fio"></span>
					<span class="sus-fonte__gota"><?php aw_svg('gota-cheia.svg'); ?></span>
				</div>
				<?php if ($aw_valor) : ?>
					<p class="sus-fonte__valor"><?php echo esc_html($aw_valor); ?></p>
				<?php endif; ?>
			<?php elseif ('foto' === $aw_visual && $aw_imagem) : ?>
				<?php echo wp_get_attachment_image($aw_imagem, 'large', false, array(
					'class'    => 'sus-visual__foto',
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => '(min-width: 768px) 560px, calc(100vw - 48px)',
				)); ?>
			<?php elseif ('produtos' === $aw_visual) : ?>
				<span class="sus-visual__circulo" aria-hidden="true"></span>
				<?php foreach ($aw_produtos as $aw_produto) : ?>
					<?php echo wp_get_attachment_image($aw_produto, 'medium_large', false, array(
						'class'    => 'sus-visual__produto',
						'loading'  => 'lazy',
						'decoding' => 'async',
						'sizes'    => '(min-width: 768px) 460px, 80vw',
					)); ?>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ($aw_selo) : ?>
				<span class="sus-selo" aria-hidden="true"><?php aw_svg('selo-' . $aw_selo . '.svg'); ?></span>
			<?php endif; ?>
		</div>

		<div class="sus-pilar__texto sus-texto">
			<?php if (is_file(AW_DIR . '/assets/img/' . $aw_svg_num)) : ?>
				<span class="sus-pilar__numero" aria-hidden="true"><?php aw_svg($aw_svg_num); ?></span>
			<?php else : ?>
				<span class="sus-pilar__numero sus-pilar__numero--texto" aria-hidden="true"><?php echo esc_html($aw_numero); ?></span>
			<?php endif; ?>

			<?php if (!empty($aw_pilar['rotulo'])) : ?>
				<p class="sus-rotulo text-label-caps"><?php echo esc_html($aw_pilar['rotulo']); ?></p>
			<?php endif; ?>

			<h2 class="sus-pilar__titulo text-heading-h2" id="<?php echo esc_attr($aw_id); ?>-titulo"><?php echo esc_html(trim($aw_pilar['titulo'])); ?></h2>

			<?php aw_paragrafos((string) ($aw_pilar['texto'] ?? ''), 'text-body-large'); ?>

			<?php if ('' !== trim((string) ($aw_pilar['destaque'] ?? ''))) : ?>
				<p class="sus-destaque"><?php echo esc_html(trim($aw_pilar['destaque'])); ?></p>
			<?php endif; ?>
		</div>
	</section>
<?php endforeach; ?>
