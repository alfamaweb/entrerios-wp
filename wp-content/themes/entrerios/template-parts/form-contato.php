<?php
/**
 * Formulário de contato — o único <form> que um projeto precisa copiar.
 *
 * Todos os campos de segurança vêm de AW_Form_Dispatcher::campos_ocultos():
 * nonce, id do formulário, URL de retorno, honeypot e slot do reCAPTCHA.
 *
 * @param array $args {
 *     @type string $titulo         Título acima do formulário.
 *     @type string $empreendimento Preenche o campo oculto de interesse.
 *     @type bool   $compacto       Layout de coluna única, para sidebar.
 *     @type bool   $ajax           Envia por AJAX em vez de POST clássico. Default: true.
 * }
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$args = wp_parse_args($args ?? array(), array(
	'titulo'         => __('Fale com a gente', 'alfama-web'),
	'empreendimento' => '',
	'compacto'       => false,
	'ajax'           => true,
));

AW_Recaptcha::enqueue();

$politica = get_page_by_path('politica-de-privacidade');
$id_base  = 'contato-' . wp_generate_password(4, false, false);
?>
<form class="aw-form<?php echo $args['compacto'] ? ' aw-form--compacto' : ''; ?>"
	id="aw-form"
	action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
	method="post"
	<?php echo $args['ajax'] ? 'data-aw-form-ajax' : ''; ?>>

	<?php AW_Form_Dispatcher::campos_ocultos('contato'); ?>

	<?php if ($args['titulo']) : ?>
		<h2 class="aw-form-titulo"><?php echo esc_html($args['titulo']); ?></h2>
	<?php endif; ?>

	<?php if ($args['empreendimento']) : ?>
		<input type="hidden" name="empreendimento" value="<?php echo esc_attr($args['empreendimento']); ?>">
	<?php endif; ?>

	<div class="aw-form-grid">
		<div class="form-group">
			<label for="<?php echo esc_attr($id_base); ?>-nome">
				<?php esc_html_e('Nome', 'alfama-web'); ?> <span aria-hidden="true">*</span>
			</label>
			<input type="text" id="<?php echo esc_attr($id_base); ?>-nome" name="nome"
				autocomplete="name" required>
		</div>

		<div class="form-group">
			<label for="<?php echo esc_attr($id_base); ?>-email">
				<?php esc_html_e('E-mail', 'alfama-web'); ?> <span aria-hidden="true">*</span>
			</label>
			<input type="email" id="<?php echo esc_attr($id_base); ?>-email" name="email"
				autocomplete="email" required>
		</div>

		<div class="form-group">
			<label for="<?php echo esc_attr($id_base); ?>-telefone">
				<?php esc_html_e('Telefone', 'alfama-web'); ?> <span aria-hidden="true">*</span>
			</label>
			<input type="tel" id="<?php echo esc_attr($id_base); ?>-telefone" name="telefone"
				autocomplete="tel" inputmode="tel" data-aw-mask="telefone" required>
		</div>

		<?php if (!$args['compacto']) : ?>
			<div class="form-group">
				<label for="<?php echo esc_attr($id_base); ?>-assunto"><?php esc_html_e('Assunto', 'alfama-web'); ?></label>
				<input type="text" id="<?php echo esc_attr($id_base); ?>-assunto" name="assunto">
			</div>
		<?php endif; ?>

		<div class="form-group form-group--full">
			<label for="<?php echo esc_attr($id_base); ?>-mensagem"><?php esc_html_e('Mensagem', 'alfama-web'); ?></label>
			<textarea id="<?php echo esc_attr($id_base); ?>-mensagem" name="mensagem" rows="<?php echo $args['compacto'] ? 3 : 5; ?>"></textarea>
		</div>

		<div class="form-group form-group--full form-check">
			<input type="checkbox" id="<?php echo esc_attr($id_base); ?>-lgpd" name="aceite_lgpd" value="1" required>
			<label for="<?php echo esc_attr($id_base); ?>-lgpd">
				<?php
				if ($politica) {
					printf(
						/* translators: %s: link para a política de privacidade. */
						esc_html__('Li e aceito a %s.', 'alfama-web'),
						sprintf(
							'<a href="%s" target="_blank" rel="noopener">%s</a>',
							esc_url(get_permalink($politica)),
							esc_html__('política de privacidade', 'alfama-web')
						)
					);
				} else {
					esc_html_e('Li e aceito a política de privacidade.', 'alfama-web');
				}
				?>
			</label>
		</div>
	</div>

	<button type="submit" class="btn mt-6 w-full" data-aw-form-submit>
		<span data-aw-form-label><?php esc_html_e('Enviar', 'alfama-web'); ?></span>
		<span class="aw-spinner" hidden aria-hidden="true"></span>
	</button>

	<p class="aw-form-status" role="status" aria-live="polite" hidden></p>
</form>
