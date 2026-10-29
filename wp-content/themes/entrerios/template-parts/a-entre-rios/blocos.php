<?php
/**
 * Parágrafos e intertítulos de um repetidor "Texto" (tipo + texto).
 *
 * Uso: get_template_part('template-parts/a-entre-rios/blocos', null, array('blocos' => $linhas));
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

foreach ((array) ($args['blocos'] ?? array()) as $aw_bloco) {
	$aw_texto = trim((string) ($aw_bloco['texto'] ?? ''));

	if ('' === $aw_texto) {
		continue;
	}

	if ('intertitulo' === ($aw_bloco['tipo'] ?? '')) {
		printf('<h3 class="ae-intertitulo text-heading-h3">%s</h3>' . "\n", esc_html($aw_texto));
	} else {
		aw_paragrafos($aw_texto, 'text-body-large');
	}
}
