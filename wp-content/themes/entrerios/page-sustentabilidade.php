<?php
/**
 * Template Name: Sustentabilidade
 *
 * Página Sustentabilidade (Figma: Screen/Sustainability/Desktop, /Tablet e
 * /Mobile). Cada seção é um template-part em template-parts/sustentabilidade/
 * e some sozinha quando o campo principal dela está vazio (grupo ACF
 * "Sustentabilidade — Página", prefixo sus_).
 *
 * Ordem do Figma: Topo, Introdução, Pilares 01–04, Compromisso e Assinatura
 * (frase com efeito de digitação). O rodapé é global (footer.php).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="conteudo" class="sus">
	<?php
	foreach (array('hero', 'introducao', 'pilares', 'compromisso', 'assinatura') as $aw_secao) {
		get_template_part('template-parts/sustentabilidade/' . $aw_secao);
	}
	?>
</main>
<?php
get_footer();
