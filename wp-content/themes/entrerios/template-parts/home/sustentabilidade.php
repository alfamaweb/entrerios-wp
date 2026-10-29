<?php
/**
 * Sustentabilidade na Home — "Sustentabilidade" do Figma.
 *
 * Cabeçalho (sobretítulo, título Display/L e texto) + pilares em cards de
 * vidro: ícone, número vazado (SVG exportado do Figma, assets/img/
 * pilar-numero-0N.svg), título e texto.
 *
 * Campos (grupo "Home — Seções", aba Sustentabilidade): sust_rotulo,
 * sust_titulo, sust_texto e sust_pilares (ícone, título, texto). Sem título
 * a seção some.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_post_id = get_the_ID();
$aw_titulo  = (string) aw_field('sust_titulo', $aw_post_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_rotulo  = (string) aw_field('sust_rotulo', $aw_post_id, '');
$aw_texto   = (string) aw_field('sust_texto', $aw_post_id, '');
$aw_pilares = array_values(array_filter((array) aw_field('sust_pilares', $aw_post_id, array()), static function ($p) {
	return !empty($p['titulo']);
}));
$aw_icones  = array('gota', 'garrafa', 'folha');
?>
<section class="sust-er" aria-labelledby="sust-titulo">
	<header class="sust-er__cabecalho">
		<div class="sust-er__titulos">
			<?php if ($aw_rotulo) : ?>
				<p class="sust-er__rotulo text-label-caps"><?php echo esc_html($aw_rotulo); ?></p>
			<?php endif; ?>
			<h2 class="sust-er__titulo text-display-l" id="sust-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		</div>
		<?php if ($aw_texto) : ?>
			<p class="sust-er__texto text-body-regular"><?php echo nl2br(esc_html($aw_texto)); ?></p>
		<?php endif; ?>
	</header>

	<?php if ($aw_pilares) : ?>
		<ul class="sust-er__pilares">
			<?php foreach ($aw_pilares as $aw_i => $aw_pilar) :
				$aw_numero = sprintf('%02d', $aw_i + 1);
				$aw_svg    = 'pilar-numero-' . $aw_numero . '.svg';
				$aw_icone  = in_array($aw_pilar['icone'] ?? '', $aw_icones, true) ? $aw_pilar['icone'] : 'gota';
				?>
				<li class="pilar-er">
					<div class="pilar-er__topo">
						<span class="pilar-er__icone"><?php aw_svg('icone-' . $aw_icone . '.svg'); ?></span>
						<?php if (is_file(AW_DIR . '/assets/img/' . $aw_svg)) : ?>
							<?php aw_svg($aw_svg, array('class' => 'pilar-er__numero')); ?>
						<?php else : ?>
							<span class="pilar-er__numero pilar-er__numero--texto text-display-l" aria-hidden="true"><?php echo esc_html($aw_numero); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="pilar-er__titulo text-heading-h3"><?php echo esc_html($aw_pilar['titulo']); ?></h3>
					<?php if (!empty($aw_pilar['texto'])) : ?>
						<p class="pilar-er__texto text-body-regular"><?php echo nl2br(esc_html($aw_pilar['texto'])); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section>
