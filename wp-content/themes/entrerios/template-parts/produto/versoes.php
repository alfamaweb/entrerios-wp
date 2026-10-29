<?php
/**
 * Produto — Versões (Figma: Versões → TIPO / SABOR / TAMANHO com "Opção/…").
 *
 * Pílula branca (texto navy) = versão atual; contorno branco = outra versão,
 * com link. Regras em aw_produto_versoes() (inc/produtos.php).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

$aw_grupos = aw_produto_versoes(get_the_ID());

if (!$aw_grupos) {
	return;
}
?>
<div class="produto-versoes">
	<?php foreach ($aw_grupos as $aw_grupo) :
		$aw_rotulo_id = 'produto-versao-' . $aw_grupo['chave'];
		?>
		<div class="produto-versao" role="group" aria-labelledby="<?php echo esc_attr($aw_rotulo_id); ?>">
			<span class="produto-versao__rotulo text-label-caps" id="<?php echo esc_attr($aw_rotulo_id); ?>"><?php echo esc_html($aw_grupo['rotulo']); ?></span>
			<ul class="produto-versao__opcoes">
				<?php foreach ($aw_grupo['opcoes'] as $aw_opcao) : ?>
					<li>
						<?php if ($aw_opcao['atual']) : ?>
							<span class="produto-opcao is-atual text-label-button-sm" aria-current="page"><?php echo esc_html($aw_opcao['texto']); ?></span>
						<?php else : ?>
							<a class="produto-opcao text-label-button-sm" href="<?php echo esc_url($aw_opcao['url']); ?>"><?php echo esc_html($aw_opcao['texto']); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
