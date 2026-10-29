<?php
/**
 * Página Contato e Política de Privacidade — funções usadas pelos templates
 * de template-parts/contato/ e template-parts/politica/. Sem hooks.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

/**
 * Normaliza e valida a URL de incorporação do Google Maps.
 *
 * O editor pode colar o código inteiro do "Incorporar um mapa" (<iframe …>)
 * ou só o endereço do src. Só passam URLs https de google.com/maps (ou
 * maps.google.com) com output=embed ou /maps/embed — qualquer outro domínio
 * vira string vazia, e o mapa some em vez de virar um iframe arbitrário.
 *
 * @param string $valor Campo contato_mapa_embed.
 * @return string URL pronta para o src, ou ''.
 */
function aw_google_maps_embed($valor)
{
	$valor = trim((string) $valor);

	if ('' === $valor) {
		return '';
	}

	if (false !== stripos($valor, '<iframe') && preg_match('/\ssrc=["\']([^"\']+)["\']/i', $valor, $m)) {
		$valor = html_entity_decode($m[1], ENT_QUOTES);
	}

	$partes = wp_parse_url($valor);

	if (empty($partes['host']) || 'https' !== strtolower($partes['scheme'] ?? '')) {
		return '';
	}

	$host    = strtolower($partes['host']);
	$caminho = $partes['path'] ?? '';
	$query   = $partes['query'] ?? '';

	$host_ok  = in_array($host, array('www.google.com', 'google.com', 'maps.google.com', 'www.google.com.br', 'google.com.br'), true);
	$embed_ok = 0 === strpos($caminho, '/maps/embed') || (0 === strpos($caminho, '/maps') && false !== strpos($query, 'output=embed'));

	return ($host_ok && $embed_ok) ? $valor : '';
}

/**
 * Texto do aceite da política, com o nome da política virando link.
 *
 * Se o texto contém o título do link legal das Opções do Site (ex.:
 * "Política de Privacidade"), esse trecho vira link para a página.
 *
 * @param string $texto Campo contato_form_aceite.
 * @return string HTML escapado.
 */
function aw_contato_texto_aceite($texto)
{
	$html     = esc_html($texto);
	$politica = aw_field('rodape_politica', 'option', array());

	if (empty($politica['url']) || empty($politica['title'])) {
		return $html;
	}

	$alvo = esc_html($politica['title']);
	$pos  = mb_stripos($html, $alvo);

	if (false === $pos) {
		return $html;
	}

	$trecho = mb_substr($html, $pos, mb_strlen($alvo));
	$link   = sprintf('<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url($politica['url']), $trecho);

	return mb_substr($html, 0, $pos) . $link . mb_substr($html, $pos + mb_strlen($alvo));
}

/**
 * Seções da Política de Privacidade com número e âncora prontos.
 *
 * As âncoras saem do título (sanitize_title) para a URL ficar legível
 * (/politica-de-privacidade/#seus-direitos) e recebem sufixo se repetirem.
 * Seção sem título é ignorada (não entra no sumário nem no corpo).
 *
 * @param int $post_id
 * @return array<int,array{numero:string,titulo:string,ancora:string,texto:string}>
 */
function aw_politica_secoes($post_id)
{
	$linhas = (array) aw_field('pol_secoes', $post_id, array());
	$secoes = array();
	// Ids que o cabeçalho e o template já usam (skip link, menu, <main>).
	$usadas = array_fill_keys(array('conteudo', 'topo', 'menu-mobile', 'aw-form', 'pol-sumario'), true);

	foreach ($linhas as $linha) {
		$titulo = trim((string) ($linha['titulo'] ?? ''));

		if ('' === $titulo) {
			continue;
		}

		$ancora = sanitize_title($titulo) ?: 'secao';
		$base   = $ancora;
		$n      = 2;

		while (isset($usadas[$ancora])) {
			$ancora = $base . '-' . $n++;
		}

		$usadas[$ancora] = true;

		$secoes[] = array(
			'numero' => str_pad((string) (count($secoes) + 1), 2, '0', STR_PAD_LEFT),
			'titulo' => $titulo,
			'ancora' => $ancora,
			'texto'  => (string) ($linha['texto'] ?? ''),
		);
	}

	return $secoes;
}
