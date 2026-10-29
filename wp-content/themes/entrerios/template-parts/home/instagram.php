<?php
/**
 * Instagram na Home — "Instagram" do Figma.
 *
 * Sobretítulo com ícone, @perfil em Display/XL com gradiente (link para o
 * perfil) e o feed. O feed é curado no admin (imagens + link de cada post),
 * sem API do Instagram: não depende de token, não quebra e não pesa.
 *
 * Campos (grupo "Home — Seções", aba Instagram): insta_rotulo, insta_usuario,
 * insta_url e insta_posts (imagem + link). Sem @perfil e sem posts a seção
 * some.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_post_id = get_the_ID();
$aw_usuario = (string) aw_field('insta_usuario', $aw_post_id, '');
$aw_url     = (string) aw_field('insta_url', $aw_post_id, '');
$aw_rotulo  = (string) aw_field('insta_rotulo', $aw_post_id, '');
$aw_posts   = array_values(array_filter((array) aw_field('insta_posts', $aw_post_id, array()), static function ($p) {
	return !empty($p['imagem']);
}));

if ('' === $aw_usuario && !$aw_posts) {
	return;
}

$aw_externo = ' target="_blank" rel="noopener"';
?>
<section class="insta-er" aria-labelledby="insta-titulo">
	<header class="insta-er__cabecalho">
		<?php if ($aw_rotulo) : ?>
			<p class="insta-er__rotulo text-label-button">
				<svg width="28" height="28" viewBox="0 0 28 28" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="22" height="22" rx="6.5" /><circle cx="14" cy="14" r="5" /><circle cx="20.5" cy="7.5" r="1.2" fill="currentColor" stroke="none" /></svg>
				<?php echo esc_html($aw_rotulo); ?>
			</p>
		<?php endif; ?>

		<?php if ($aw_usuario) : ?>
			<h2 class="insta-er__perfil text-display-xl" id="insta-titulo">
				<?php if ($aw_url) : ?>
					<a href="<?php echo esc_url($aw_url); ?>"<?php echo $aw_externo; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html($aw_usuario); ?></a>
				<?php else : ?>
					<?php echo esc_html($aw_usuario); ?>
				<?php endif; ?>
			</h2>
		<?php endif; ?>
	</header>

	<?php if ($aw_posts) : ?>
		<ul class="insta-er__feed">
			<?php foreach ($aw_posts as $aw_post) :
				$aw_link = !empty($aw_post['link']) ? $aw_post['link'] : $aw_url;
				$aw_img  = wp_get_attachment_image((int) $aw_post['imagem'], 'er-instagram', false, array(
					'class'    => 'insta-er__foto',
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => '(min-width: 1280px) 302px, (min-width: 768px) 45vw, 70vw',
				));
				?>
				<li class="insta-er__post">
					<?php if ($aw_link) : ?>
						<a href="<?php echo esc_url($aw_link); ?>"<?php echo $aw_externo; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
							<?php echo $aw_img; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="sr-only"><?php esc_html_e('Ver no Instagram', 'alfama-web'); ?></span>
						</a>
					<?php else : ?>
						<?php echo $aw_img; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section>
