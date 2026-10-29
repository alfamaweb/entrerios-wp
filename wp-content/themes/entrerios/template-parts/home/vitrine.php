<?php
/**
 * Vitrine de produtos da Home — componente "Produtos / Vitrine" do Figma.
 *
 * Abas por linha (Category Tabs) + um carrossel por linha (Carousel
 * Viewport com rolagem horizontal, Product Cards e o card "Ver todos") +
 * navegação (barra de progresso e setas que avançam em passos de cards).
 *
 * Campos (grupo "Home — Letreiro e vitrine"): vitrine_rotulo,
 * vitrine_linhas (ordem das abas) e vitrine_ver_todos. Os produtos vêm do
 * CPT "produto" de cada linha, na ordem do atributo "Ordem". Linha sem
 * produto publicado não ganha aba.
 *
 * Sem JS a primeira aba aparece com o carrossel rolável; assets/js/vitrine.js
 * liga as abas, as setas, a barra de progresso e o arrastar com o mouse.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_post_id   = get_the_ID();
$aw_rotulo    = (string) aw_field('vitrine_rotulo', $aw_post_id, '');
$aw_ver_todos = aw_field('vitrine_ver_todos', $aw_post_id, array());
$aw_abas      = array();

foreach ((array) aw_field('vitrine_linhas', $aw_post_id, array()) as $aw_termo_id) {
	$aw_termo = get_term((int) (is_object($aw_termo_id) ? $aw_termo_id->term_id : $aw_termo_id), 'linha');

	if (!$aw_termo instanceof WP_Term) {
		continue;
	}

	$aw_produtos = get_posts(array(
		'post_type'      => 'produto',
		'posts_per_page' => 30,
		'orderby'        => array('menu_order' => 'ASC', 'title' => 'ASC'),
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'tax_query'      => array(array(
			'taxonomy' => 'linha',
			'terms'    => $aw_termo->term_id,
		)),
	));

	if ($aw_produtos) {
		$aw_abas[] = array('termo' => $aw_termo, 'produtos' => $aw_produtos);
	}
}

if (!$aw_abas) {
	return;
}

$aw_tem_ver_todos = !empty($aw_ver_todos['url']) && !empty($aw_ver_todos['title']);
?>
<section class="vitrine-er" data-er-vitrine<?php echo $aw_rotulo ? ' aria-label="' . esc_attr($aw_rotulo) . '"' : ''; ?>>
	<header class="vitrine-er__cabecalho">
		<div class="vitrine-er__titulos">
			<?php if ($aw_rotulo) : ?>
				<p class="vitrine-er__rotulo text-label-caps"><?php echo esc_html($aw_rotulo); ?></p>
			<?php endif; ?>
			<div class="vitrine-er__h2s">
				<?php foreach ($aw_abas as $aw_i => $aw_aba) : ?>
					<h2 class="vitrine-er__titulo text-heading-h2<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" id="vitrine-titulo-<?php echo (int) $aw_i; ?>"
						data-er-painel-item="<?php echo (int) $aw_i; ?>"
						style="<?php echo aw_linha_css_vars($aw_aba['termo']); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>"
						<?php echo 0 === $aw_i ? '' : 'hidden'; ?>><?php echo esc_html($aw_aba['termo']->name); ?></h2>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if (count($aw_abas) > 1) : ?>
			<div class="vitrine-er__abas-viewport">
				<div class="vitrine-er__abas" role="tablist" aria-label="<?php esc_attr_e('Linhas de produto', 'alfama-web'); ?>">
					<?php foreach ($aw_abas as $aw_i => $aw_aba) : ?>
						<button type="button" class="aba-linha text-label-caps<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" role="tab"
							id="vitrine-aba-<?php echo (int) $aw_i; ?>" aria-controls="vitrine-painel-<?php echo (int) $aw_i; ?>"
							aria-selected="<?php echo 0 === $aw_i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $aw_i ? '0' : '-1'; ?>"
							data-er-aba="<?php echo (int) $aw_i; ?>"
							style="<?php echo aw_linha_css_vars($aw_aba['termo']); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>">
							<?php echo esc_html($aw_aba['termo']->name); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</header>

	<div class="vitrine-er__paineis">
		<?php foreach ($aw_abas as $aw_i => $aw_aba) : ?>
			<div class="vitrine-er__painel<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" id="vitrine-painel-<?php echo (int) $aw_i; ?>"
				role="tabpanel" aria-labelledby="vitrine-aba-<?php echo (int) $aw_i; ?>" data-er-painel-item="<?php echo (int) $aw_i; ?>"
				style="<?php echo aw_linha_css_vars($aw_aba['termo']); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado na função ?>"
				<?php echo 0 === $aw_i ? '' : 'hidden'; ?>>

				<div class="vitrine-er__viewport" data-er-carrossel tabindex="0"
					aria-label="<?php echo esc_attr(sprintf(/* translators: %s: nome da linha */ __('Produtos — %s', 'alfama-web'), $aw_aba['termo']->name)); ?>">
					<ul class="vitrine-er__trilho">
						<?php foreach ($aw_aba['produtos'] as $aw_produto) : ?>
							<li class="vitrine-er__item"><?php get_template_part('template-parts/card-produto', null, array('post' => $aw_produto)); ?></li>
						<?php endforeach; ?>

						<?php if ($aw_tem_ver_todos) : ?>
							<li class="vitrine-er__item">
								<a class="card-ver-todos" href="<?php echo esc_url($aw_ver_todos['url']); ?>"<?php echo !empty($aw_ver_todos['target']) ? ' target="_blank" rel="noopener"' : ''; ?>>
									<span class="card-ver-todos__bolha card-ver-todos__bolha--1" aria-hidden="true"></span>
									<span class="card-ver-todos__bolha card-ver-todos__bolha--2" aria-hidden="true"></span>
									<span class="card-ver-todos__bolha card-ver-todos__bolha--3" aria-hidden="true"></span>
									<span class="card-ver-todos__ir" aria-hidden="true">
										<svg width="22" height="20" viewBox="0 0 22 20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M2 10h18M12 2l8 8-8 8" /></svg>
									</span>
									<span class="card-ver-todos__texto"><?php echo esc_html($aw_ver_todos['title']); ?></span>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>

				<div class="vitrine-er__nav">
					<div class="vitrine-er__progresso" aria-hidden="true"><span class="vitrine-er__progresso-barra" data-er-progresso></span></div>
					<button type="button" class="seta-vitrine seta-vitrine--anterior" data-er-inicio aria-label="<?php esc_attr_e('Produtos anteriores', 'alfama-web'); ?>">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 9H3M8 4L3 9l5 5" /></svg>
					</button>
					<button type="button" class="seta-vitrine seta-vitrine--proxima" data-er-fim aria-label="<?php esc_attr_e('Próximos produtos', 'alfama-web'); ?>">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9h12M10 4l5 5-5 5" /></svg>
					</button>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
