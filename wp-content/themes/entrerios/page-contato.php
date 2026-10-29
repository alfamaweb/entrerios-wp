<?php
/**
 * Template Name: Contato
 *
 * Página Fale Conosco (Figma: Screen/Contact/Desktop e Screen/Contact/Mobile).
 * Cada seção é um template-part em template-parts/contato/ e some sozinha
 * quando o título dela está vazio (grupo ACF "Contato — Página").
 *
 * Ordem do Figma: Topo (degradê + breadcrumb + H1) e Contato (formulário à
 * esquerda; contatos e mapa à direita). O rodapé é global (footer.php).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="conteudo" class="contato">
	<?php
	foreach (array('hero', 'contato') as $aw_secao) {
		get_template_part('template-parts/contato/' . $aw_secao);
	}
	?>
</main>
<?php
get_footer();
