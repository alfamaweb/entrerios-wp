<?php
/**
 * Rodapé do site — componente "Footer" do Figma (Screen/Home, todas as telas).
 *
 * Topo: logo (link para a Home), contato, menu e Instagram. Meio: wordmark
 * "ENTRE RIOS" vazado cujas letras sobem quando o rodapé entra na tela
 * (SVG em assets/img/footer-wordmark-entre-rios.svg + assets/js/footer.js).
 * Base: copyright, link legal e assinatura Alfama Web.
 *
 * Campos (Opções do Site): logo, rodape_titulo, endereco, telefone,
 * email_contato, rodape_social_rotulo, rodape_social, copyright e
 * rodape_politica; menu na localização "Menu do rodapé". Cada bloco some
 * quando o campo dele está vazio.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_logo      = (int) aw_field('logo', 'option', 0);
$aw_titulo    = (string) aw_field('rodape_titulo', 'option', '');
$aw_endereco  = (string) aw_field('endereco', 'option', '');
$aw_telefone  = (string) aw_field('telefone', 'option', '');
$aw_email     = (string) aw_field('email_contato', 'option', '');
$aw_soc_rot   = (string) aw_field('rodape_social_rotulo', 'option', '');
$aw_social    = aw_field('rodape_social', 'option', array());
$aw_copyright = (string) aw_field('copyright', 'option', '');
$aw_politica  = aw_field('rodape_politica', 'option', array());
$aw_menu      = aw_itens_do_menu('rodape');
$aw_tel_link  = preg_replace('/[^0-9+]/', '', $aw_telefone);
?>
<footer class="footer-er" data-er-footer>
	<div class="footer-er__topo">
		<div class="footer-er__marca">
			<?php if ($aw_logo) : ?>
				<a class="footer-er__logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
					<?php echo wp_get_attachment_image($aw_logo, 'medium', false, array(
						'alt'      => get_bloginfo('name'),
						'loading'  => 'lazy',
						'decoding' => 'async',
						'sizes'    => '136px',
					)); ?>
				</a>
			<?php endif; ?>

			<address class="footer-er__contato">
				<?php if ($aw_titulo) : ?>
					<p class="footer-er__titulo text-heading-h3"><?php echo esc_html($aw_titulo); ?></p>
				<?php endif; ?>
				<?php if ($aw_endereco) : ?>
					<p class="text-body-regular"><?php echo nl2br(esc_html($aw_endereco)); ?></p>
				<?php endif; ?>
				<?php if ($aw_telefone) : ?>
					<p class="text-body-regular"><?php esc_html_e('Tel.:', 'alfama-web'); ?> <a href="tel:<?php echo esc_attr($aw_tel_link); ?>"><?php echo esc_html($aw_telefone); ?></a></p>
				<?php endif; ?>
				<?php if ($aw_email) : ?>
					<p class="text-body-regular"><a class="footer-er__email" href="mailto:<?php echo esc_attr($aw_email); ?>"><?php echo esc_html($aw_email); ?></a></p>
				<?php endif; ?>
			</address>
		</div>

		<?php if ($aw_menu) : ?>
			<nav class="footer-er__menu" aria-label="<?php esc_attr_e('Rodapé', 'alfama-web'); ?>">
				<ul>
					<?php foreach ($aw_menu as $aw_item) : ?>
						<li><a class="footer-er__link text-label-caps" href="<?php echo esc_url($aw_item->url); ?>"<?php echo !empty($aw_item->current) ? ' aria-current="page"' : ''; ?>><?php echo esc_html($aw_item->title); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php if (!empty($aw_social['url']) && !empty($aw_social['title'])) : ?>
			<div class="footer-er__social">
				<?php if ($aw_soc_rot) : ?>
					<p class="text-label-small"><?php echo esc_html($aw_soc_rot); ?></p>
				<?php endif; ?>
				<a class="footer-er__perfil text-heading-h3" href="<?php echo esc_url($aw_social['url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($aw_social['title']); ?></a>
			</div>
		<?php endif; ?>
	</div>

	<div class="footer-er__wordmark" aria-hidden="true">
		<?php aw_svg('footer-wordmark-entre-rios.svg'); ?>
	</div>

	<div class="footer-er__base">
		<div class="footer-er__legal">
			<?php if ($aw_copyright) : ?>
				<p class="text-label-small">&copy; <?php echo esc_html(wp_date('Y')); ?> - <?php echo esc_html($aw_copyright); ?></p>
			<?php endif; ?>
			<?php if (!empty($aw_politica['url']) && !empty($aw_politica['title'])) : ?>
				<a class="footer-er__politica text-label-button-sm" href="<?php echo esc_url($aw_politica['url']); ?>"><?php echo esc_html($aw_politica['title']); ?></a>
			<?php endif; ?>
		</div>

		<a class="footer-er__alfama" href="https://alfamaweb.com.br" target="_blank" rel="noopener">
			<span class="sr-only"><?php esc_html_e('Desenvolvido pela Alfama Web', 'alfama-web'); ?></span>
			<?php aw_svg('alfama-web.svg', array('aria-hidden' => 'true', 'focusable' => 'false')); ?>
		</a>
	</div>
</footer>

<?php get_template_part('template-parts/whatsapp-flutuante'); ?>
<?php get_template_part('template-parts/form-feedback'); ?>

<?php wp_footer(); ?>
</body>
</html>
