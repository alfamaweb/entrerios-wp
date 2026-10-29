<?php
/**
 * Cores por Linha de produto.
 *
 * Cada termo da taxonomia "linha" guarda, em campos ACF (grupo group_er_linha,
 * importado pelo mu-plugin entrerios-bootstrap), as cores que o design system
 * do Figma usa para aquela linha, nomeadas por PAPEL e não por degrau da
 * escala — porque o degrau varia de linha para linha (ver docs/TOKENS-FIGMA.md,
 * "Lógica de cor por linha"):
 *
 *   cor_hero_inicio / cor_hero_meio / cor_hero_fim  → gradiente do Hero Slider
 *   cor_chip_clara / cor_media / cor_chip_forte     → chip, cards e vitrine
 *                                                     (claro 100/300 · médio 500 · forte 700)
 *   cor_destaque                                    → color/category-*
 *   cor_profunda                                    → fundo do Produto 360° (950)
 *
 * O tema nunca lê esses hex direto no CSS: aw_linha_css_vars() devolve um
 * style="--linha-…" para o elemento, e o CSS usa var(--linha-…).
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

/**
 * Campos de cor da linha, na ordem do gradiente, com a variável CSS gerada.
 */
const AW_LINHA_CORES = array(
	'cor_hero_inicio' => '--linha-hero-inicio',
	'cor_hero_meio'   => '--linha-hero-meio',
	'cor_hero_fim'    => '--linha-hero-fim',
	'cor_chip_clara'  => '--linha-chip-clara',
	'cor_media'       => '--linha-media',
	'cor_chip_forte'  => '--linha-chip-forte',
	'cor_destaque'    => '--linha-destaque',
	'cor_profunda'    => '--linha-profunda',
);

/**
 * Contraste mínimo (WCAG AA, texto normal) entre o branco do texto do hero e
 * o início do gradiente, onde ficam título, parágrafo e navegação.
 */
const AW_LINHA_CONTRASTE_MIN = 4.5;

/**
 * Atributo style com as cores da linha como custom properties.
 *
 * Só emite as cores preenchidas e válidas; sem nenhuma, devolve string vazia e
 * o componente cai nas cores do próprio CSS (tokens da marca).
 *
 * @param WP_Term|int|null $termo
 * @return string Conteúdo do atributo style, já escapado.
 */
function aw_linha_css_vars($termo)
{
	$termo = $termo ? get_term($termo, 'linha') : null;

	if (!$termo instanceof WP_Term || !function_exists('get_field')) {
		return '';
	}

	$decl = array();

	foreach (AW_LINHA_CORES as $campo => $var) {
		$hex = sanitize_hex_color((string) get_field($campo, $termo));

		if ($hex) {
			$decl[] = $var . ':' . $hex;
		}
	}

	return esc_attr(implode(';', $decl));
}

/**
 * Luminância relativa (WCAG 2.x) de um hex #rrggbb.
 *
 * @param string $hex
 * @return float|null
 */
function aw_luminancia($hex)
{
	$hex = ltrim((string) sanitize_hex_color($hex), '#');

	if (strlen($hex) === 3) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if (strlen($hex) !== 6) {
		return null;
	}

	$canais = array();

	foreach (str_split($hex, 2) as $par) {
		$c        = hexdec($par) / 255;
		$canais[] = $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
	}

	return 0.2126 * $canais[0] + 0.7152 * $canais[1] + 0.0722 * $canais[2];
}

/**
 * Razão de contraste do branco sobre a cor.
 *
 * @param string $hex
 * @return float|null
 */
function aw_contraste_com_branco($hex)
{
	$l = aw_luminancia($hex);

	return $l === null ? null : 1.05 / ($l + 0.05);
}

/**
 * Bloqueia salvar uma linha cujo início de gradiente não dá leitura ao texto
 * branco do hero. É a regra que mantém uma linha nova (cadastrada sem passar
 * pelo designer) dentro do padrão de acessibilidade do design system.
 */
add_filter('acf/validate_value/name=cor_hero_inicio', 'aw_validar_contraste_linha', 10, 2);
function aw_validar_contraste_linha($valido, $valor)
{
	if ($valido !== true || !$valor) {
		return $valido;
	}

	$contraste = aw_contraste_com_branco($valor);

	if ($contraste !== null && $contraste < AW_LINHA_CONTRASTE_MIN) {
		return sprintf(
			/* translators: 1: contraste calculado, 2: mínimo exigido */
			__('Contraste do texto branco sobre esta cor: %1$s:1. O mínimo é %2$s:1 — escolha um tom mais escuro (o degrau 700 ou 900 da linha).', 'alfama-web'),
			number_format_i18n($contraste, 2),
			number_format_i18n(AW_LINHA_CONTRASTE_MIN, 1)
		);
	}

	return $valido;
}
