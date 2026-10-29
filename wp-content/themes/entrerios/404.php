<?php
/**
 * Página não encontrada.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="conteudo">
	<section class="py-20 text-center">
		<div class="container">
			<p class="aw-meta">404</p>
			<h1 class="mt-2 text-3xl font-bold desktop:text-4xl"><?php esc_html_e('Página não encontrada', 'alfama-web'); ?></h1>
			<p class="mx-auto mt-4 max-w-md">
				<?php esc_html_e('O endereço acessado não existe ou foi movido. Use a busca ou volte para a página inicial.', 'alfama-web'); ?>
			</p>

			<div class="mt-8 flex flex-wrap items-center justify-center gap-4">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="btn"><?php esc_html_e('Ir para a home', 'alfama-web'); ?></a>
			</div>

			<div class="mx-auto mt-10 max-w-md">
				<?php get_search_form(); ?>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
