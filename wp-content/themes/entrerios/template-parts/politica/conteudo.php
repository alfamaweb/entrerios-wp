<?php
/**
 * Política de Privacidade — Conteúdo (Figma: Conteúdo — Sumário 320 + gap 80
 * + Seções 880; Mobile empilhado, gap 40).
 *
 * Sumário: caixa surface/muted (radius 24, borda lavender-300) com
 * "SUMÁRIO" (Label/Caps sky-500) e um link por seção — número (Label/Bold
 * sky-500) + título (Label/Button). No Desktop acompanha a rolagem
 * (sticky) e marca a seção visível; o clique rola suave até a seção
 * (Figma: Scroll to, ease in and out, 600 ms — assets/js/politica.js).
 * Sem JS, são âncoras comuns.
 *
 * Seções: selo do número (lavender-50, Label/Bold navy-500) + título
 * (Heading/H3, H2 no HTML) e o texto (Body/Large ink/80) com listas de
 * marcador sky-500. Uma linha divisória entre elas.
 *
 * Campos: pol_secoes (repetidor título + texto; sem seções, nada aparece),
 * pol_sumario_rotulo.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_id     = get_the_ID();
$aw_secoes = aw_politica_secoes($aw_id);

if (!$aw_secoes) {
	return;
}

$aw_rotulo     = (string) aw_field('pol_sumario_rotulo', $aw_id, '');
$aw_tem_indice = count($aw_secoes) > 1;
?>
<div class="pol-conteudo<?php echo $aw_tem_indice ? '' : ' pol-conteudo--sem-sumario'; ?>">
	<?php if ($aw_tem_indice) : ?>
		<nav class="pol-sumario" id="pol-sumario" aria-label="<?php echo esc_attr($aw_rotulo ?: __('Sumário', 'alfama-web')); ?>" data-pol-sumario>
			<?php if ($aw_rotulo) : ?>
				<p class="pol-sumario__rotulo text-label-caps" aria-hidden="true"><?php echo esc_html($aw_rotulo); ?></p>
			<?php endif; ?>

			<ol class="pol-sumario__lista">
				<?php foreach ($aw_secoes as $aw_i => $aw_secao) : ?>
					<li>
						<a class="pol-sumario__link<?php echo 0 === $aw_i ? ' is-ativo' : ''; ?>" href="#<?php echo esc_attr($aw_secao['ancora']); ?>">
							<span class="pol-sumario__num" aria-hidden="true"><?php echo esc_html($aw_secao['numero']); ?></span>
							<span class="pol-sumario__texto text-label-button"><?php echo esc_html($aw_secao['titulo']); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
		</nav>
	<?php endif; ?>

	<div class="pol-secoes">
		<?php foreach ($aw_secoes as $aw_secao) : ?>
			<section class="pol-secao" id="<?php echo esc_attr($aw_secao['ancora']); ?>" aria-labelledby="<?php echo esc_attr($aw_secao['ancora']); ?>-titulo">
				<h2 class="pol-secao__titulo" id="<?php echo esc_attr($aw_secao['ancora']); ?>-titulo">
					<span class="pol-secao__num" aria-hidden="true"><?php echo esc_html($aw_secao['numero']); ?></span>
					<span class="text-heading-h3"><?php echo esc_html($aw_secao['titulo']); ?></span>
				</h2>

				<?php if ('' !== trim(wp_strip_all_tags($aw_secao['texto']))) : ?>
					<div class="pol-secao__texto text-body-large">
						<?php echo wp_kses_post($aw_secao['texto']); ?>
					</div>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
	</div>
</div>
