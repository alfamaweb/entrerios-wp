<?php
/**
 * Breadcrumb do Figma (Home / Produtos / …) — Label/Small branco, sobre o
 * degradê do topo. O último item é a página atual.
 *
 * Uso: get_template_part('template-parts/produtos/trilha', null, array(
 *     'itens'  => array(array('texto' => 'Home', 'url' => '…'), …),
 *     'classe' => 'prod-hero__trilha',
 * ));
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_itens = array_values(array_filter((array) ($args['itens'] ?? array()), function ($aw_item) {
	return !empty($aw_item['texto']);
}));

if (!$aw_itens) {
	return;
}

$aw_ultimo = count($aw_itens) - 1;
?>
<nav class="trilha-er <?php echo esc_attr($args['classe'] ?? ''); ?>" aria-label="<?php esc_attr_e('Você está aqui', 'alfama-web'); ?>">
	<ol class="trilha-er__lista text-label-small">
		<?php foreach ($aw_itens as $aw_i => $aw_item) : ?>
			<li class="trilha-er__item">
				<?php if ($aw_i === $aw_ultimo || empty($aw_item['url'])) : ?>
					<span<?php echo $aw_i === $aw_ultimo ? ' aria-current="page"' : ''; ?>><?php echo esc_html($aw_item['texto']); ?></span>
				<?php else : ?>
					<a href="<?php echo esc_url($aw_item['url']); ?>"><?php echo esc_html($aw_item['texto']); ?></a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
