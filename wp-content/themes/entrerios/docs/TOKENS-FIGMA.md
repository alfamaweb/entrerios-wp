# Tokens do Figma — Entre Rios

Fonte: arquivo Figma **Entre Rios** (`kw3L2k3sg0s7WMvrhWZA2p`), página
`05_Documentation` — quadros *Documentation/Foundations*, *Documentation/Motion*,
*Design system / Cores*, *Tipografia*, *Raios* e *Visão geral*. Lido em
19/10/2026, modo **Light** (Colors/Semantic) e modos **Desktop/Tablet/Mobile**
(Typography).

Os nomes no CSS seguem o **codeSyntax WEB** das variables do Figma
(`brand/navy-500` → `--brand-navy-500`). Valores em `style.css`, seção 1.
O Figma é a fonte da verdade: mudou lá, muda aqui.

## Cores

### Primitivos

| Token | Hex | | Token | Hex |
|---|---|---|---|---|
| `--brand-navy-500` | `#353393` | | `--neutral-ink` | `#212529` |
| `--brand-navy-700` | `#28276F` | | `--neutral-gray-500` | `#626E79` |
| `--brand-navy-900` | `#1B1A4A` | | `--neutral-gray-700` | `#494949` |
| `--brand-sky-300` | `#70CFF6` | | `--neutral-white` | `#FFFFFF` |
| `--brand-sky-500` | `#2FA4DD` | | `--neutral-lavender-25` | `#F8F8FD` |
| `--brand-lavender-50` | `#EEEFFA` | | `--neutral-lavender-100` | `#F4F4FB` |
| `--brand-royal-500` | `#2E5FB8` | | `--neutral-lavender-200` | `#EAEAF7` |
| `--accent-green` | `#00CE66` | | `--neutral-lavender-300` | `#E6E6F4` |
| `--accent-lime` | `#86CF49` | | `--neutral-lavender-400` | `#E2E2F4` |
| `--accent-yellow` | `#FCBF02` | | `--neutral-lavender-600` | `#C9C9E6` |

Os `accent/*` **não** são cores de estado (sucesso/alerta/erro) — o próprio
Figma avisa.

### Rampas de categoria

| Linha | Níveis |
|---|---|
| Água Mineral | 100 `#8ED6F6` · 500 `#2FA4DD` · 700 `#1A7DBA` |
| Energéticos | 300 `#5A84E4` · 500 `#2F5BC8` · 700 `#163184` · 900 `#0E1F5C` · 950 `#0A1240` |
| Refrigerantes | 300 `#F0646A` · 400 `#E0484C` · 500 `#D52B30` · 700 `#A51615` · 900 `#5E0B0B` |
| Guaraná Mais | 300 `#B067C6` · 400 `#9A4DB0` · 500 `#7E2D96` · 700 `#5C156B` · 900 `#3A0A45` · 950 `#24082E` |

Cada linha tem níveis próprios. **Não interpole** níveis que não existem.

### Semânticos (use estes nos componentes)

| Token | Aponta para |
|---|---|
| `--color-text-primary` / `-inverse` / `-accent` | neutral/ink · neutral/white · brand/sky-500 |
| `--color-surface-default` / `-subtle` / `-inverse` / `-muted` / `-selected` | white · lavender-50 · navy-900 · lavender-100 · lavender-400 |
| `--color-action-primary` / `-primary-hover` / `-accent` | navy-500 · navy-700 · sky-500 |
| `--color-border-default` / `-inverse` | lavender-300 · white |
| `--color-category-agua` / `-energeticos` / `-refrigerantes` / `-guarana-mais` | agua/500 · energeticos/700 · refrigerantes/500 · guarana-mais/700 |

### Fora do código

- `canvas/*` (`section-dark`, `steel`, `mist`, `ice`) — só fundo de quadros no Figma.
- `illustration/*` — só ilustrações. Exceção: os cards ilustrados da
  Sustentabilidade usam `sky-100` `#B9E6FA`, `cream-100` `#FFE7C2`, `sun-400`
  `#F4B703`, `orange-400` `#FFA040` e `orange-600` `#E36C16` (`--illustration-*`).
- `alpha/<cor>/<opacidade>` (82 tokens) — **ainda não transcritos**. Quando um
  componente precisar, use `color-mix(in srgb, var(--brand-navy-500) 40%, transparent)`
  com a opacidade do token e anote o nome Figma no comentário.

## Sombras, blur e transparências

Lidos componente a componente em *Design system / Componentes* e na tela
Home (19/10/2026). O Figma tem os effect styles `Shadow/SM`, `MD`, `LG`, `XL`
e `2XL`, mas a maioria dos componentes usa sombras aplicadas direto (sem
style) — por isso os tokens locais levam o nome do componente.

### Effect styles

| Token | Valor | Onde aparece |
|---|---|---|
| `--shadow-md` (Shadow/MD) | 0 10 24 0 `#120F40` 35% | Logo do header |
| `--shadow-2xl` (Shadow/2XL) | 0 24 30 0 `#000` 25% | Produtos da Linha Card |
| `--drop-shadow-xl` (Shadow/XL) | 0 24 30 0 `#0D0D33` 30% | Produtos dos cards da Sustentabilidade (`drop-shadow`) |
| Shadow/SM, LG | — | Não encontrados em uso; pendentes |

### Sombras locais (X Y Blur Spread · cor)

| Token | Valor | Componente |
|---|---|---|
| `--shadow-button-primary-hover` | 0 12 28 −4 · navy-500 40% | Button Primary · Hover |
| `--shadow-button-light-hover` | 0 12 28 −4 · navy-900 35% | Button Light · Hover |
| `--shadow-circle-cta-hover` | 0 16 40 0 · navy-900 25% | Circle CTA · Hover |
| `--shadow-play-button-hover` | 0 0 40 8 · branco 45% (brilho) | Play Button · Hover |
| `--shadow-arrow-button-hover` | 0 10 20 −4 · navy-900 22% | Arrow Button · Hover |
| `--shadow-category-chip-active` | 0 16 32 0 · navy-900 25% | Category Chip · Active (Thumb) |
| `--shadow-product-card-hover` | 0 28 48 −8 · navy-900 18% | Product Card · Hover |
| `--shadow-card-ver-todos-hover` | 0 28 48 −8 · navy-500 35% | Card Ver Todos · Hover |
| `--shadow-linha-card-hover-energeticos` | 0 32 64 −12 · category/energeticos/500 40% | Linha Card · Hover (cor da linha) |
| `--shadow-hero-product` | 0 40 60 0 · navy-900 35% | Produto do Hero |
| `--shadow-produto-360` | 0 30 50 0 · `#0D0014` 35% | Produto 360° |
| `--shadow-focus-ring` | 0 0 0 4 · sky-500 25% | Input Field · Focus (+ borda 2px navy-500) |

Sem sombra: Button Outline Light (hover), Circle CTA default, Nav Link,
Tab, Category Chip hover, Product Card default, Card Ver Todos default.

**Divergência:** a descrição do Product Card diz "sombra da cor da
categoria", mas o componente usa navy-900 18% nas linhas que conferi (Água
Mineral e Refrigerantes). Segui o componente. Na Linha Card a sombra é, de
fato, da cor da linha — só a variante Energéticos foi lida; as outras usam
`category/<linha>/500` a 40% pelo mesmo padrão (conferir ao implementar).

### Background blur (vidro)

| Token | Componentes |
|---|---|
| `--blur-16` | Play Button default (fundo white/25, borda white/80), Pill do callout 360° (white/8, borda white/20) |
| `--blur-20` | Accordion Item (fechado white/8 + borda white/20; aberto white/16 + borda white/40) |
| `--blur-24` | Menu (glass) do header (white/16, borda white/35) |

Brilho e chão do 360° (*Glow* e *Floor Shadow*) e as bolhas do Hero são
**gradientes radiais**, não blur — mais baratos para o navegador.

### Transparências usadas

`--alpha-neutral-white-8/10/16/20/25/35/40/45/60/80/95` e
`--alpha-brand-navy-500-40`. As demais `alpha/*` entram sob demanda.

### Desempenho

`backdrop-filter` é caro: usar só nos quatro casos acima, nunca em listas
longas, e com fallback de fundo mais opaco em `@supports not (backdrop-filter: blur(1px))`.
Animar sombra também custa; nos hovers, preferir transicionar `opacity` de
um pseudo-elemento com a sombra pronta.

## Motion dos componentes

| Token | Valor | Origem |
|---|---|---|
| `--motion-button` | 250ms ease-out | Button (seta se afasta, fundo escurece) |
| `--motion-circle-cta` | 300ms | Circle CTA (anel tracejado gira 12s linear) |
| `--motion-product-card` | 300ms ease-out | Product Card (produto sobe 18px, inclina −5°, seta −45°) |
| `--motion-linha-card` | 400ms ease-out | Linha Card (produtos em leque) |
| `--motion-chip-360` | 400ms | Dissolve entre variantes do 360° |
| `--motion-category-chip` | 600ms ease-in-out | Troca de slide pelo chip |
| `--motion-hero-slide` | 700ms ease-in-out | Hero (autoplay 5s) |

## Tipografia

Família **Montserrat** (variável, `assets/fonts/`). Tamanho por modo
(Mobile / Tablet / Desktop) em `--font-size-*`; o resto vem do text style.
No Tailwind, cada estilo vira um utilitário: `text-heading-h1`, `text-label-caps`…

| Estilo | M / T / D (px) | Peso | LH | Tracking |
|---|---|---|---|---|
| Display/Wordmark | 54 / 106 / 206 | 900 | Auto | 0 |
| Display/XXL | 56 / 112 / 220 | 900 | 85% | −4px |
| Display/XL | 40 / 64 / 96 | 900 | 95% | −2px |
| Display/L | 32 / 44 / 64 | 900 | 100% | 0 |
| Heading/H1 | 40 / 56 / 72 | 800 | 100% | −1,5px |
| Heading/H2 | 30 / 40 / 56 | 800 | 105% | −1px |
| Heading/H3 | 22 / 24 / 28 | 700 | 120% | −0,5px |
| Heading/H4 | 18 / 20 / 22 | 700 | 130% | 0 |
| Body/Large | 16 / 18 / 20 | 400 | 160% | 0 |
| Body/Regular | 14 / 14 / 16 | 400 | 170% | 0 |
| Label/Caps | 12 / 12 / 14 | 700 | 120% | 2px (caixa alta) |
| Label/Bold | 14 / 14 / 14 | 700 | 140% | 0 |
| Label/Button | 14 / 16 / 16 | 600 | 120% | 0,2px |
| Label/Button-SM | 14 / 14 / 14 | 600 | 120% | 0 |
| Label/Small | 12 / 12 / 14 | 500 | 140% | 0 |

## Raios

`--radius-2` 2px · `--radius-12` · `-16` · `-20` · `-24` · `-28` · `-32` ·
`-36` · `-40` · `--radius-full` 999px.

## Grid e breakpoints

| Layout guide | Canvas | Colunas | Gutter | Margem |
|---|---|---|---|---|
| Mobile | 390 | 4 | 16 | 20 |
| Tablet | 768 | 8 | 24 | 40 |
| Desktop | 1600 | 12 | 24 | 160 |

- Tailwind: `tablet:` (≥ 768px) e `desktop:` (≥ 1280px). Os breakpoints padrão
  (`sm`, `md`, `lg`, `xl`, `2xl`) foram removidos e os templates migrados
  (`md:` → `tablet:`, `lg:`/`xl:` → `desktop:`).
- `.container`: margem lateral `--grid-margin` e área útil máxima de 1280px.
- `.grid-figma`: 4 / 8 / 12 colunas com o gutter do modo.

**Decisão nossa (confirmar com o designer):** o Figma desenha o Desktop em
1600px, mas não define onde o Desktop começa. Usamos **1280px** (a largura útil
do Desktop). Entre 1280 e 1600 a margem lateral é a de 40px do Tablet até
centralizar em 1280.

## Motion (Documentation/Motion)

- Hero: 4 slides, autoplay de **5 s**, chips clicáveis, bolhas contínuas,
  produto flutuando (timeline 20 s).
- 360°: componente Produto 360°, chips trocam a variante (dissolve **0,4 s**),
  scroll sticky.
- Vitrine: abas por categoria (smart animate **0,5 s**); letreiro em loop contínuo.
- Rodapé: "ENTRE RIOS" em onda ao entrar na viewport. Sustentabilidade:
  digitação ao entrar na área.
- Gatilhos de scroll: IntersectionObserver / GSAP ScrollTrigger; respeitar
  `prefers-reduced-motion`.

## Cores por linha (campos ACF)

Cada termo da taxonomia `linha` guarda as cores por papel (`cor_hero_inicio/
meio/fim`, `cor_chip_clara/forte`, `cor_destaque`). Valores iniciais:

| Linha | Hero (início → meio → fim) | Chip (claro → forte) | Destaque |
|---|---|---|---|
| Água Mineral | brand/navy-500 → brand/sky-500 → brand/sky-300 | agua/100 → agua/700 | agua/500 |
| Energéticos | 900 → 700 → 500 | 300 → 700 | 700 |
| Refrigerantes | 900 → 700 → 400 | 300 → 700* | 500 |
| Guaraná Mais | 900 → 700 → 400 | 300 → 700* | 700 |

\* não conferido no Figma. Contraste mínimo do início do gradiente com o
branco: 4.5:1 (validado ao salvar).

`cor_media` (tom 500 de cada linha) alimenta o círculo e o botão dos Product
Cards. `--alpha-neutral-white-12/14`: bolhas do card "Ver todos".
`--drop-shadow-card-produto` (0 20 24 #000 18%) e `-hover` (0 32 30 #000 28%).

Sombras de produto como `filter`: `--drop-shadow-hero-product` (Desktop),
`-tablet` e `-mobile`. `--shadow-sm` = Shadow/SM (logo no Tablet/Mobile).

## Pendências

| Item | Situação |
|---|---|
| **Espaçamento** (`space/*`, 20 tokens, 4 → 200) | O quadro *Design system / Espaçamento* está **atrás** do quadro de Cores no canvas e não pode ser lido sem acesso de edição. Pedir ao designer para separar os quadros, ou acesso de edição/API. |
| Elevação: `Shadow/SM`, `LG`, `XL` | Não encontrados em uso nos componentes; ler o quadro Elevação quando ele for separado do de Cores. |
| `alpha/*` (82) | 12 já transcritos; o resto sob demanda. |
| Estados (sucesso/alerta/erro) | O Figma não define. `--aw-sucesso`, `--aw-atencao`, `--aw-erro` são do starter. |
| Transição de hover | O Figma não define duração. `--aw-transicao` (180ms) é do starter. |
| Corte do Desktop (1280px) | Decisão nossa, ver acima. |
