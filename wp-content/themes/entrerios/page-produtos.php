<?php
/**
 * Template Name: Produtos
 *
 * Catálogo (Figma: Screen/Products/Desktop e Screen/Products/Mobile).
 * Topo em degradê com os produtos e o catálogo com abas, filtros e
 * "Carregar mais". Campos: grupo ACF "Produtos — Página" (group_er_produtos).
 * Regras do catálogo: inc/produtos.php · docs/PRODUTOS.md.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="conteudo" class="prod">
	<?php
	foreach (array('hero', 'catalogo') as $aw_secao) {
		get_template_part('template-parts/produtos/' . $aw_secao);
	}
	?>
</main>
<?php
get_footer();
