<?php
/**
 * Página do produto (Figma: Screen/Product/<linha>/Desktop e Mobile).
 *
 * Topo em degradê navy com o palco (brilho, anel, círculo na cor da linha,
 * bolhas e a foto) e a ficha (linha, nome, versões, sanfona e contato);
 * depois os produtos relacionados da mesma linha. As cores da linha entram
 * como --linha-* no <main> (inc/linhas.php).
 *
 * Campos: grupo "Produto — Ficha" (group_er_produto) + textos compartilhados
 * na página Produtos (group_er_produtos). Ver docs/PRODUTOS.md.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) :
	the_post();
	$aw_linha = aw_produto_linha(get_the_ID());
	?>
	<main id="conteudo" class="produto-er"<?php echo $aw_linha ? ' style="' . aw_linha_css_vars($aw_linha) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>>
		<?php
		get_template_part('template-parts/produto/topo', null, array('linha' => $aw_linha));
		get_template_part('template-parts/produto/relacionados', null, array('linha' => $aw_linha));
		?>
	</main>
	<?php
endwhile;

get_footer();
