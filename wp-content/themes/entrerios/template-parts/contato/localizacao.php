<?php
/**
 * Contato — Localização (Figma: Localização, 480 × 687; Mobile 342 × 689).
 *
 * Card "Nossos Contatos" em degradê navy-900 → navy-500 (radius 32, padding
 * 40) com endereço, telefone e e-mail — os três vêm das Opções do Site, os
 * mesmos do rodapé e do cabeçalho, para não haver dois lugares para editar.
 * Abaixo, o mapa (Figma: "Mapa — substituir pelo Google Maps embed", 480 × 340)
 * com o botão "Abrir no Google Maps".
 *
 * O iframe do Google Maps é carregado com loading="lazy": no Mobile ele fica
 * abaixo da dobra e só baixa ao se aproximar da tela.
 *
 * Campos: contato_info_titulo (vazio esconde o card), contato_mapa_embed
 * (vazio esconde o mapa), contato_mapa_link (botão; vazio esconde).
 * Opções do Site: endereco, telefone, email_contato.
 *
 * @param array $args { @type string $mapa URL de embed já validada (aw_google_maps_embed). }
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id       = get_the_ID();
$aw_titulo   = (string) aw_field('contato_info_titulo', $aw_id, '');
$aw_mapa     = (string) ($args['mapa'] ?? '');
$aw_link     = aw_field('contato_mapa_link', $aw_id, array());
$aw_endereco = (string) aw_field('endereco', 'option', '');
$aw_telefone = (string) aw_field('telefone', 'option', '');
$aw_email    = (string) aw_field('email_contato', 'option', '');
$aw_tel_href = $aw_telefone ? 'tel:+55' . preg_replace('/\D/', '', $aw_telefone) : '';
$aw_tem_info = $aw_titulo && ($aw_endereco || $aw_telefone || $aw_email);

if (!$aw_tem_info && '' === $aw_mapa) {
	return;
}
?>
<div class="contato-local">
	<?php if ($aw_tem_info) : ?>
		<div class="contato-info">
			<h2 class="contato-info__titulo text-heading-h3"><?php echo esc_html($aw_titulo); ?></h2>

			<ul class="contato-info__lista">
				<?php if ($aw_endereco) : ?>
					<li class="contato-info__item">
						<span class="contato-info__icone" aria-hidden="true"><?php aw_svg('icone-pin.svg'); ?></span>
						<span class="sr-only"><?php esc_html_e('Endereço:', 'alfama-web'); ?></span>
						<address class="contato-info__texto text-label-button"><?php echo nl2br(esc_html($aw_endereco), false); ?></address>
					</li>
				<?php endif; ?>

				<?php if ($aw_telefone) : ?>
					<li class="contato-info__item">
						<span class="contato-info__icone" aria-hidden="true"><?php aw_svg('icone-telefone.svg'); ?></span>
						<a class="contato-info__texto contato-info__link text-label-button" href="<?php echo esc_url($aw_tel_href); ?>">
							<?php esc_html_e('Tel.:', 'alfama-web'); ?> <?php echo esc_html($aw_telefone); ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if ($aw_email) : ?>
					<li class="contato-info__item">
						<span class="contato-info__icone" aria-hidden="true"><?php aw_svg('icone-email.svg'); ?></span>
						<a class="contato-info__texto contato-info__link text-label-button" href="<?php echo esc_url('mailto:' . antispambot($aw_email)); ?>">
							<span class="sr-only"><?php esc_html_e('E-mail:', 'alfama-web'); ?></span> <?php echo esc_html(antispambot($aw_email)); ?>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ($aw_mapa) : ?>
		<div class="contato-mapa">
			<iframe class="contato-mapa__iframe"
				src="<?php echo esc_url($aw_mapa); ?>"
				title="<?php echo esc_attr(sprintf(/* translators: %s: nome do site. */ __('Mapa com a localização da %s', 'alfama-web'), aw_field('rodape_titulo', 'option', get_bloginfo('name')))); ?>"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
				allowfullscreen></iframe>

			<?php if (!empty($aw_link['url']) && !empty($aw_link['title'])) : ?>
				<a class="contato-mapa__abrir text-label-button-sm" href="<?php echo esc_url($aw_link['url']); ?>" target="_blank" rel="noopener">
					<?php echo esc_html($aw_link['title']); ?>
					<span class="sr-only"><?php esc_html_e('(abre em nova aba)', 'alfama-web'); ?></span>
					<svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M1.5 8.5l7-7M3 1.5h5.5V7" /></svg>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
