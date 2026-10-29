<?php
/**
 * Template Name: Home
 *
 * Home Entre Rios (Figma: Screen/Desktop/Home, Tablet e Mobile). Cada seção é
 * um template-part em template-parts/home/ e se esconde sozinha quando os
 * campos ACF dela estão vazios.
 *
 * Ordem do Figma: Hero Slider, Letreiro, Vitrine de produtos, Linhas de
 * bebidas (bento), Produto 360°, Sobre a Entre Rios, Sustentabilidade e
 * Instagram. O rodapé é global (footer.php).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="conteudo">
	<?php get_template_part('template-parts/home/hero'); ?>
	<?php get_template_part('template-parts/home/marquee'); ?>
	<?php get_template_part('template-parts/home/vitrine'); ?>
	<?php get_template_part('template-parts/home/linhas'); ?>
	<?php get_template_part('template-parts/home/produto-360'); ?>
	<?php get_template_part('template-parts/home/sobre'); ?>
	<?php get_template_part('template-parts/home/sustentabilidade'); ?>
	<?php get_template_part('template-parts/home/instagram'); ?>
</main>
<?php
get_footer();
