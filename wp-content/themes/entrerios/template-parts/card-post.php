<?php
/**
 * Card de post do blog.
 *
 * @param array $args {
 *     @type int $post_id ID do post. Default: post atual do loop.
 * }
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$post_id = (int) ($args['post_id'] ?? get_the_ID());

if (!$post_id) {
	return;
}

$categorias = get_the_category($post_id);
?>
<article class="card card-post">
	<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="card-thumb" tabindex="-1" aria-hidden="true">
		<img src="<?php echo esc_url(aw_thumb($post_id)); ?>"
			alt="" width="720" height="480" loading="lazy" decoding="async">
	</a>

	<div class="card-body">
		<p class="aw-meta">
			<time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>">
				<?php echo esc_html(get_the_date('d/m/Y', $post_id)); ?>
			</time>
			<?php if ($categorias) : ?>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html($categorias[0]->name); ?></span>
			<?php endif; ?>
		</p>

		<h3 class="card-titulo">
			<a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a>
		</h3>

		<p class="card-resumo"><?php echo esc_html(aw_resumo($post_id)); ?></p>

		<span class="btn-texto" aria-hidden="true"><?php esc_html_e('Ler mais', 'alfama-web'); ?></span>
	</div>
</article>
