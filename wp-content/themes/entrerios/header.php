<?php
/**
 * Cabeçalho do site — componente "Nav" do Figma (Hero Slider) + overlay
 * "Screen/Overlay/Menu-Open".
 *
 * Desktop (≥1280): logo 116 · menu em pílula de vidro · contato à direita.
 * Tablet (768–1279): logo 76 · contato · botão de menu (vidro, 52).
 * Mobile (<768): logo 60 · botões de vidro de telefone e menu (48).
 * O menu aberto (tablet e mobile) é o overlay em gradiente com os links
 * numerados e o bloco de contato.
 *
 * Conteúdo: logo, rótulo do contato e imagem do menu vêm do grupo ACF
 * "Cabeçalho e menu"; telefone, e-mail e redes, da aba Contato/Redes das
 * Opções do Site; os links, do menu WordPress "primary". Nada é fixo aqui —
 * campo vazio esconde o elemento.
 *
 * O cabeçalho é sempre transparente e absoluto (width 100%), flutuando sobre o
 * primeiro bloco da página — cada template começa com um hero que reserva o
 * espaço dele no padding-top.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_logo_id    = (int) aw_field('logo', 'option', 0);
$aw_rotulo     = (string) aw_field('contato_rotulo', 'option', '');
$aw_telefone   = (string) aw_field('telefone', 'option', '');
$aw_tel_href   = $aw_telefone ? 'tel:+55' . preg_replace('/\D/', '', $aw_telefone) : '';
$aw_email      = (string) aw_field('email_contato', 'option', '');
$aw_redes      = (array) aw_field('redes_sociais', 'option', array());
$aw_menu_img   = (int) aw_field('menu_imagem', 'option', 0);
$aw_itens_menu = aw_itens_do_menu('primary');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#353393">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="aw-skip-link" href="#conteudo"><?php esc_html_e('Pular para o conteúdo', 'alfama-web'); ?></a>

<header class="site-header" id="topo">
	<div class="container site-header__barra">

		<a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
			<?php if ($aw_logo_id) : ?>
				<?php echo wp_get_attachment_image($aw_logo_id, 'thumbnail', false, array(
					'class'         => 'site-logo__img',
					'alt'           => get_bloginfo('name'),
					'sizes'         => '(min-width: 1280px) 108px, (min-width: 768px) 70px, 54px',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
				)); ?>
			<?php else : ?>
				<span class="site-logo__texto"><?php bloginfo('name'); ?></span>
			<?php endif; ?>
		</a>

		<?php if (has_nav_menu('primary')) : ?>
			<nav class="site-nav" aria-label="<?php esc_attr_e('Menu principal', 'alfama-web'); ?>">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__lista',
					'depth'          => 1,
					'fallback_cb'    => false,
				));
				?>
			</nav>
		<?php endif; ?>

		<div class="site-header__acoes">
			<?php if ($aw_telefone) : ?>
				<a class="site-contato" href="<?php echo esc_url($aw_tel_href); ?>">
					<?php if ($aw_rotulo) : ?>
						<span class="site-contato__rotulo text-label-small"><?php echo esc_html($aw_rotulo); ?></span>
					<?php endif; ?>
					<span class="site-contato__numero text-heading-h3"><?php echo esc_html($aw_telefone); ?></span>
				</a>

				<a class="botao-vidro site-header__tel" href="<?php echo esc_url($aw_tel_href); ?>">
					<span class="sr-only"><?php echo esc_html(trim($aw_rotulo . ' ' . $aw_telefone)); ?></span>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
					</svg>
				</a>
			<?php endif; ?>

			<?php if ($aw_itens_menu) : ?>
				<button type="button" class="botao-vidro site-header__menu" data-aw-menu-toggle
					aria-expanded="false" aria-controls="menu-mobile">
					<span class="sr-only"><?php esc_html_e('Abrir menu', 'alfama-web'); ?></span>
					<svg width="22" height="16" viewBox="0 0 22 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
						<path d="M1 1h20M1 8h20M1 15h20" />
					</svg>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<?php if ($aw_itens_menu) : ?>
		<div class="menu-overlay" id="menu-mobile" role="dialog" aria-modal="true"
			aria-label="<?php esc_attr_e('Menu', 'alfama-web'); ?>" hidden>

			<?php if ($aw_menu_img) : ?>
				<?php echo wp_get_attachment_image($aw_menu_img, 'er-produto-hero', false, array(
					'class'       => 'menu-overlay__decor',
					'alt'         => '',
					'aria-hidden' => 'true',
					'loading'     => 'lazy',
					'sizes'       => '(min-width: 768px) 360px, 240px',
				)); ?>
			<?php endif; ?>

			<div class="container menu-overlay__topo">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
					<?php if ($aw_logo_id) : ?>
						<?php echo wp_get_attachment_image($aw_logo_id, 'thumbnail', false, array(
							'class'   => 'site-logo__img',
							'alt'     => get_bloginfo('name'),
							'sizes'   => '(min-width: 768px) 70px, 54px',
							'loading' => 'lazy',
						)); ?>
					<?php else : ?>
						<span class="site-logo__texto"><?php bloginfo('name'); ?></span>
					<?php endif; ?>
				</a>

				<button type="button" class="botao-fechar" data-aw-menu-close>
					<span class="botao-fechar__texto text-label-button"><?php esc_html_e('Fechar', 'alfama-web'); ?></span>
					<span class="botao-fechar__icone" aria-hidden="true">
						<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
							<path d="M1 1l12 12M13 1L1 13" />
						</svg>
					</span>
				</button>
			</div>

			<nav class="container menu-overlay__nav" aria-label="<?php esc_attr_e('Menu principal', 'alfama-web'); ?>">
				<ol class="menu-overlay__lista">
					<?php foreach ($aw_itens_menu as $aw_i => $aw_item) : ?>
						<li class="menu-overlay__item<?php echo $aw_item->current ? ' is-atual' : ''; ?>">
							<a href="<?php echo esc_url($aw_item->url); ?>"<?php echo $aw_item->current ? ' aria-current="page"' : ''; ?>>
								<span class="menu-overlay__numero text-label-caps" aria-hidden="true"><?php echo esc_html(str_pad((string) ($aw_i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
								<span class="menu-overlay__titulo text-heading-h2"><?php echo esc_html($aw_item->title); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>

			<?php if ($aw_telefone || $aw_email || $aw_redes) : ?>
				<div class="container menu-overlay__contato">
					<?php if ($aw_rotulo) : ?>
						<p class="text-label-small menu-overlay__rotulo"><?php echo esc_html($aw_rotulo); ?></p>
					<?php endif; ?>

					<?php if ($aw_telefone) : ?>
						<a class="text-heading-h2 menu-overlay__telefone" href="<?php echo esc_url($aw_tel_href); ?>"><?php echo esc_html($aw_telefone); ?></a>
					<?php endif; ?>

					<?php if ($aw_email) : ?>
						<a class="text-body-regular menu-overlay__link" href="<?php echo esc_url('mailto:' . $aw_email); ?>"><?php echo esc_html($aw_email); ?></a>
					<?php endif; ?>

					<?php foreach ($aw_redes as $aw_rede) :
						if (empty($aw_rede['url'])) {
							continue;
						}
						$aw_handle = trim((string) wp_parse_url($aw_rede['url'], PHP_URL_PATH), '/');
						?>
						<a class="text-body-regular menu-overlay__link" href="<?php echo esc_url($aw_rede['url']); ?>" target="_blank" rel="noopener">
							<?php echo esc_html($aw_rede['nome'] ?? ''); ?>
							<?php if ($aw_handle) : ?>
								<strong>@<?php echo esc_html($aw_handle); ?></strong>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</header>
