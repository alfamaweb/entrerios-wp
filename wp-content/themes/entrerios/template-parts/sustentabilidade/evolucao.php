<?php
/**
 * Sustentabilidade — Evolução (Figma: Banner em degradê navy).
 *
 * Fundo em degradê navy com foto aérea e duas colunas:
 * Esquerda: Título em destaque ("Sustentabilidade não é uma ação isolada. É um processo de evolução.")
 * Direita: Parágrafo explicativo e card com caixa translúcida de destaque.
 *
 * Campos: sust_evolucao_titulo (vazio esconde), sust_evolucao_imagem,
 * sust_evolucao_texto, sust_evolucao_destaque.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_titulo = (string) aw_field('sust_evolucao_titulo', $aw_id, '');

if ('' === $aw_titulo) {
	return;
}

$aw_imagem   = (int) aw_field('sust_evolucao_imagem', $aw_id, 0);
$aw_texto    = (string) aw_field('sust_evolucao_texto', $aw_id, '');
$aw_destaque = (string) aw_field('sust_evolucao_destaque', $aw_id, '');
?>
<section class="sust-evolucao" aria-labelledby="sust-evolucao-titulo">
	<?php if ($aw_imagem) : ?>
		<?php echo wp_get_attachment_image($aw_imagem, 'full', false, array(
			'class'    => 'sust-evolucao__foto',
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'sizes'    => '100vw',
		)); ?>
	<?php endif; ?>

	<div class="sust-evolucao__container">
		<div class="sust-evolucao__col-esq">
			<h2 class="sust-evolucao__titulo text-heading-h1" id="sust-evolucao-titulo">
				<?php echo nl2br(esc_html($aw_titulo), false); ?>
			</h2>
		</div>

		<div class="sust-evolucao__col-dir">
			<?php if ($aw_texto) : ?>
				<div class="sust-evolucao__texto">
					<?php aw_paragrafos($aw_texto, 'text-body-large'); ?>
				</div>
			<?php endif; ?>

			<?php if ($aw_destaque) : ?>
				<div class="sust-evolucao__card-destaque">
					<p class="sust-evolucao__frase text-heading-h3"><?php echo nl2br(esc_html($aw_destaque), false); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

