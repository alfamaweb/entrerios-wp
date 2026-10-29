<?php
/**
 * Política de Privacidade — Topo (Figma: Hero, 1600 × 547; Mobile 390 × 328).
 *
 * Degradê navy-500 → sky-500 → sky-300, breadcrumb, H1 (Heading/H1) e a
 * data da última atualização (Body/Regular, white/80). A data vem de um
 * campo de data e sai no formato longo (9 de outubro de 2026), no idioma
 * do site, com <time datetime> para buscadores.
 *
 * Campos: pol_hero_titulo (vazio esconde), pol_hero_data_rotulo, pol_hero_data.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('pol_hero_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_rotulo  = (string) aw_field('pol_hero_data_rotulo', $aw_id, '');
$aw_data    = DateTime::createFromFormat('Ymd', (string) aw_field('pol_hero_data', $aw_id, ''), wp_timezone());
$aw_home_id = (int) get_option('page_on_front');
?>
<section class="pol-hero" aria-labelledby="pol-hero-titulo">
	<div class="pol-hero__copia">
		<?php
		get_template_part('template-parts/produtos/trilha', null, array(
			'itens' => array(
				array('texto' => $aw_home_id ? get_the_title($aw_home_id) : '', 'url' => home_url('/')),
				array('texto' => get_the_title($aw_id), 'url' => ''),
			),
		));
		?>
		<h1 class="pol-hero__titulo text-heading-h1" id="pol-hero-titulo"><?php echo esc_html($aw_titulo); ?></h1>

		<?php if ($aw_data) : ?>
			<p class="pol-hero__data text-body-regular">
				<?php echo esc_html($aw_rotulo); ?>
				<time datetime="<?php echo esc_attr($aw_data->format('Y-m-d')); ?>"><?php echo esc_html(wp_date(__('j \d\e F \d\e Y', 'alfama-web'), $aw_data->getTimestamp())); ?></time>
			</p>
		<?php endif; ?>
	</div>
</section>
