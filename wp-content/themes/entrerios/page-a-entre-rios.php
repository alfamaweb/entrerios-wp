<?php
/**
 * Template Name: A Entre Rios
 *
 * Página institucional (Figma: Screen/About/Desktop e Screen/About/Mobile).
 * Cada seção é um template-part em template-parts/a-entre-rios/ e some
 * sozinha quando o título dela está vazio (grupo ACF "A Entre Rios — Página").
 *
 * Ordem do Figma: Topo, Origem, Novos Caminhos, Fábrica em Transformação,
 * Galeria, Pessoas, Futuro e Assinatura. O rodapé é global (footer.php).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="conteudo" class="ae">
	<?php
	foreach (array('hero', 'origem', 'caminhos', 'fabrica', 'galeria', 'pessoas', 'futuro', 'assinatura') as $aw_secao) {
		get_template_part('template-parts/a-entre-rios/' . $aw_secao);
	}
	?>
</main>
<?php
get_footer();
