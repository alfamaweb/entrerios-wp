<?php
/**
 * Card de empreendimento.
 *
 * Usado no SSR e nas respostas AJAX — é o mesmo arquivo nos dois caminhos, o
 * que impede o card divergir entre a primeira carga e as seguintes.
 *
 * @param array $args {
 *     @type int $post_id ID do empreendimento. Default: post atual do loop.
 * }
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$post_id = (int) ($args['post_id'] ?? get_the_ID());

if (!$post_id) {
	return;
}

$titulo      = get_the_title($post_id);
$link        = get_permalink($post_id);
$local       = aw_field('localizacao_texto', $post_id, '');
$preco       = aw_field('preco_a_partir', $post_id, '');
$esgotado    = aw_esta_esgotado($post_id);
$resumo_difs = aw_field('diferenciais_resumo', $post_id, array());
$status      = get_the_terms($post_id, 'status_obra');
?>
<article class="card card-empreendimento">
	<a href="<?php echo esc_url($link); ?>" class="card-thumb" tabindex="-1" aria-hidden="true">
		<img src="<?php echo esc_url(aw_thumb($post_id)); ?>"
			alt="" width="720" height="480" loading="lazy" decoding="async">
	</a>

	<div class="card-body">
		<?php if ($status && !is_wp_error($status)) : ?>
			<p class="aw-badge"><?php echo esc_html($status[0]->name); ?></p>
		<?php endif; ?>

		<h3 class="card-titulo">
			<a href="<?php echo esc_url($link); ?>"><?php echo esc_html($titulo); ?></a>
		</h3>

		<?php if ($local) : ?>
			<p class="card-local"><?php echo esc_html($local); ?></p>
		<?php endif; ?>

		<?php if (is_array($resumo_difs) && $resumo_difs) : ?>
			<ul class="card-difs">
				<?php foreach (array_slice($resumo_difs, 0, 3) as $item) :
					$rotulo = is_array($item) ? ($item['titulo'] ?? '') : (string) $item;

					if (!$rotulo) {
						continue;
					}
					?>
					<li><?php echo esc_html($rotulo); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="card-rodape">
			<?php if ($esgotado) : ?>
				<p class="aw-badge aw-badge--esgotado"><?php esc_html_e('100% vendido', 'alfama-web'); ?></p>
			<?php elseif ($preco) : ?>
				<p class="card-preco">
					<span><?php esc_html_e('A partir de', 'alfama-web'); ?></span>
					<strong><?php echo esc_html($preco); ?></strong>
				</p>
			<?php endif; ?>

			<a href="<?php echo esc_url($link); ?>" class="btn-texto">
				<?php esc_html_e('Ver detalhes', 'alfama-web'); ?>
			</a>
		</div>
	</div>
</article>
