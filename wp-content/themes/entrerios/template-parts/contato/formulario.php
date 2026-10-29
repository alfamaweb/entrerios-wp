<?php
/**
 * Contato — Formulário (Figma: Formulário, 760 × 682; Mobile 342 × 626).
 *
 * Card branco (radius 32, borda lavender-300, padding 48) com título
 * (Heading/H3), texto (Body/Regular) e os campos do componente "Campo"
 * (Nome · E-mail + Telefone · Assunto · Mensagem) + Button Primary.
 *
 * Envio pelo AW_Form_Dispatcher (id "contato", handler em
 * inc/Forms/handlers/contato.php): nonce, honeypot, reCAPTCHA e feedback
 * vêm de AW_Form_Dispatcher::campos_ocultos(). Com JS envia por AJAX
 * (data-aw-form-ajax, assets/js/app.js); sem JS, POST clássico + toast.
 *
 * Os rótulos ficam visíveis só como placeholder (como no Figma) e existem
 * como <label> para leitor de tela.
 *
 * Campos: contato_form_titulo (vazio esconde), contato_form_texto,
 * contato_form_botao, contato_form_aceite (texto do aceite da política;
 * vazio esconde a caixa).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('contato_form_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_texto  = (string) aw_field('contato_form_texto', $aw_id, '');
$aw_botao  = (string) aw_field('contato_form_botao', $aw_id, '') ?: __('Enviar', 'alfama-web');
$aw_aceite = (string) aw_field('contato_form_aceite', $aw_id, '');

AW_Recaptcha::enqueue();

$aw_campos = array(
	'nome'     => array('rotulo' => __('Nome', 'alfama-web'), 'tipo' => 'text', 'auto' => 'name', 'obrigatorio' => true),
	'email'    => array('rotulo' => __('E-mail', 'alfama-web'), 'tipo' => 'email', 'auto' => 'email', 'obrigatorio' => true),
	'telefone' => array('rotulo' => __('Telefone', 'alfama-web'), 'tipo' => 'tel', 'auto' => 'tel', 'obrigatorio' => true),
	'assunto'  => array('rotulo' => __('Assunto', 'alfama-web'), 'tipo' => 'text', 'auto' => 'off', 'obrigatorio' => false),
);

/**
 * Imprime um campo de uma linha (componente "Campo", Type=Text).
 *
 * @param string $aw_nome
 * @param array  $aw_campo
 * @return void
 */
$aw_input = static function ($aw_nome, $aw_campo) {
	$aw_html_id = 'contato-' . $aw_nome;
	?>
	<div class="campo-er">
		<label class="sr-only" for="<?php echo esc_attr($aw_html_id); ?>"><?php echo esc_html($aw_campo['rotulo']); ?><?php echo $aw_campo['obrigatorio'] ? ' ' . esc_html__('(obrigatório)', 'alfama-web') : ''; ?></label>
		<input class="campo-er__controle text-body-regular"
			type="<?php echo esc_attr($aw_campo['tipo']); ?>"
			id="<?php echo esc_attr($aw_html_id); ?>"
			name="<?php echo esc_attr($aw_nome); ?>"
			placeholder="<?php echo esc_attr($aw_campo['rotulo']); ?>"
			autocomplete="<?php echo esc_attr($aw_campo['auto']); ?>"
			<?php echo 'tel' === $aw_campo['tipo'] ? 'inputmode="tel" data-aw-mask="telefone"' : ''; ?>
			<?php echo $aw_campo['obrigatorio'] ? 'required' : ''; ?>>
	</div>
	<?php
};
?>
<div class="contato-form">
	<div class="contato-form__cabeca">
		<h2 class="contato-form__titulo text-heading-h3" id="contato-form-titulo"><?php echo esc_html($aw_titulo); ?></h2>
		<?php if ($aw_texto) : ?>
			<p class="contato-form__texto text-body-regular"><?php echo nl2br(esc_html($aw_texto), false); ?></p>
		<?php endif; ?>
	</div>

	<form class="contato-form__campos" id="aw-form"
		action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
		method="post"
		data-aw-form-ajax>

		<?php AW_Form_Dispatcher::campos_ocultos('contato'); ?>

		<?php $aw_input('nome', $aw_campos['nome']); ?>

		<div class="contato-form__linha">
			<?php $aw_input('email', $aw_campos['email']); ?>
			<?php $aw_input('telefone', $aw_campos['telefone']); ?>
		</div>

		<?php $aw_input('assunto', $aw_campos['assunto']); ?>

		<div class="campo-er campo-er--area">
			<label class="sr-only" for="contato-mensagem"><?php esc_html_e('Mensagem', 'alfama-web'); ?> <?php esc_html_e('(obrigatório)', 'alfama-web'); ?></label>
			<textarea class="campo-er__controle text-body-regular" id="contato-mensagem" name="mensagem" rows="4"
				placeholder="<?php esc_attr_e('Mensagem', 'alfama-web'); ?>" required></textarea>
		</div>

		<?php if ($aw_aceite) : ?>
			<div class="contato-form__aceite">
				<input type="checkbox" id="contato-aceite" name="aceite_lgpd" value="1" required>
				<label class="text-label-small" for="contato-aceite"><?php echo aw_contato_texto_aceite($aw_aceite); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado em aw_contato_texto_aceite() ?></label>
			</div>
		<?php endif; ?>

		<div class="contato-form__envio">
			<button type="submit" class="botao-primario contato-form__enviar" data-aw-form-submit>
				<span class="text-label-button" data-aw-form-label><?php echo esc_html($aw_botao); ?></span>
				<span class="botao-primario__icone" aria-hidden="true">
					<span class="aw-spinner" hidden></span>
					<svg width="16" height="14" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 7h14M9 1l6 6-6 6" /></svg>
				</span>
			</button>
		</div>

		<p class="aw-form-status" role="status" aria-live="polite" hidden></p>
	</form>
</div>
