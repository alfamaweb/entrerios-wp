<?php
/**
 * Contato — Topo (Figma: Hero, 1600 × 520; Mobile 390 × 289).
 *
 * Degradê navy-500 → sky-500 → sky-300, breadcrumb (Home / Contato) e H1
 * (Heading/H1). Sem imagem: nada de LCP pesado.
 *
 * Campos: contato_hero_titulo (vazio esconde).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('contato_hero_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_home_id = (int) get_option('page_on_front');
?>
<section class="contato-hero" aria-labelledby="contato-hero-titulo">
	<div class="contato-hero__copia">
		<?php
		get_template_part('template-parts/produtos/trilha', null, array(
			'itens' => array(
				array('texto' => $aw_home_id ? get_the_title($aw_home_id) : '', 'url' => home_url('/')),
				array('texto' => get_the_title($aw_id), 'url' => ''),
			),
		));
		?>
		<h1 class="contato-hero__titulo text-heading-h1" id="contato-hero-titulo"><?php echo esc_html($aw_titulo); ?></h1>
	</div>
</section>
