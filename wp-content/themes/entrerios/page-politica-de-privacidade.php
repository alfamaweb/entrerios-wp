<?php
/**
 * Template Name: Política de Privacidade
 *
 * Política de Privacidade (Figma: Screen/Privacy-Policy/Desktop e
 * Screen/Privacy-Policy/Mobile). Topo em degradê com a data da última
 * atualização e o conteúdo com sumário (fixo no Desktop) + seções
 * numeradas. Cada parte some sozinha quando o campo-gatilho está vazio
 * (grupo ACF "Política de Privacidade — Página").
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="conteudo" class="pol">
	<?php
	foreach (array('hero', 'conteudo') as $aw_secao) {
		get_template_part('template-parts/politica/' . $aw_secao);
	}
	?>
</main>
<?php
get_footer();
