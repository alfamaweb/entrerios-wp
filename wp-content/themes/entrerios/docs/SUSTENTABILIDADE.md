# Página Sustentabilidade

Figma: `01_Screens/Desktop` → **Screen/Sustainability/Desktop** (178-100, 1600),
`02_Screens/Tablet` → **Screen/Sustainability/Tablet** (193-569, 768) e
`03_Screens/Mobile` → **Screen/Sustainability/Mobile** (194-1377, 390). Lido em
29/10/2026 em modo leitura, com "Copy as code → CSS (all layers)" de cada
seção (paddings, gaps, degradês com ângulo, posições e rotações dos cards,
text styles) e "Copy as SVG" dos números e ícones.

Substitui a primeira versão da página (feita fora do padrão — ver "Revisão",
no fim).

## Arquivos

| Papel | Arquivo |
|---|---|
| Template (Template Name: Sustentabilidade) | `page-sustentabilidade.php` |
| Seções | `template-parts/sustentabilidade/{hero,introducao,pilares,compromisso,assinatura}.php` |
| Estilos (só nesta página) | `assets/css/sustentabilidade.css` |
| Digitação da frase final (~1 KB, `defer`) | `assets/js/sustentabilidade.js` |
| Números vazados 01–04 | `assets/img/sustentabilidade-numero-0N.svg` (Copy as SVG) |
| Ícones | `assets/img/selo-{escudo,reciclagem,folha}.svg`, `gota-cheia.svg`, `gota-degrade.svg` |
| Campos | `acf-json/group_er_sustentabilidade.json` (cópia em `mu-plugins/entrerios-bootstrap/acf/`) |

`aw_slugs_de_css()` carrega o CSS pelo template (qualquer slug) e
`aw_registrar_assets()` o JS. Tokens novos em `style.css` (raiz):
`--alpha-neutral-white-{5,7,9,11,13,15,50,85,90}`, `--alpha-brand-sky-500-60`,
`--alpha-brand-navy-500-30`, `--alpha-brand-navy-900-60`,
`--illustration-{sky-100,cream-100,sun-400,orange-400,orange-600}` e
`--drop-shadow-xl` (Shadow/XL, que estava pendente).

## Campos (grupo "Sustentabilidade — Página", `group_er_sustentabilidade`)

Só no template `page-sustentabilidade.php`; o editor de blocos fica
escondido. Prefixo `sus_` (o `sust_` é da seção da Home). Cada seção some
quando o campo principal está vazio; não há texto ou imagem de reserva no PHP.

- **Topo**: `sus_hero_imagem`, `sus_hero_rotulo`, `sus_hero_titulo` (H1).
- **Introdução**: `sus_intro_texto` (parágrafos), `sus_intro_citacao` (card).
- **Pilares**: repetidor `sus_pilares` — `rotulo`, `titulo` (vazio esconde o
  pilar), `texto`, `destaque` (frase com barra), `visual`, `selo`, e conforme o
  visual: `valor` (profundidade), `imagem` (foto), `fundo` + `produtos`
  (galeria, até 4, de trás para a frente). O número 01, 02… é automático.
- **Compromisso**: `sus_comp_imagem`, `sus_comp_rotulo`, `sus_comp_titulo`,
  `sus_comp_texto`, `sus_comp_destaque`.
- **Assinatura**: `sus_assinatura_frase` (uma linha por verso).

O bootstrap v9 liga o template, apaga os campos `sust_*` que a primeira
versão gravou nesta página (nunca na Home) e grava os textos do Figma, as
fotos (`sobre-fabrica-drone-02` no topo e no Compromisso, `-01` no Pilar 02)
e 7 recortes de produto (`mu-plugins/entrerios-bootstrap/img/sustentabilidade-*.webp`,
os PNGs do catálogo cortados rente ao produto, como pede o "Fit" do Figma).

## Cards dos pilares

Montados em código, nas medidas do Figma (espaço 560 × 600). Tudo dentro do
card usa `--u = 100cqw / 560`, então o card escala inteiro — o frame Mobile é
exatamente o Desktop × 0,625 (350 × 375, radius 25).

| Visual | Figma | Composição |
|---|---|---|
| `profundidade` | Pilar 01 | degradê sky-300 → navy-900, 5 faixas 600×70 (white 5→13 %, ±1,5°), 5 bolhas, fio 3×420 e gota 64×78, valor em Display/XL |
| `foto` | Pilar 02 | foto + navy-900 0 → 60 % na metade de baixo; selo no canto inferior esquerdo |
| `produtos` + `azul` | Pilar 03 | lavender-50 → illustration/sky-100, círculo sky-300 → navy-500 (90 %), 4 produtos; selo claro (white/85, ícone navy-500) no canto superior direito |
| `produtos` + `laranja` | Pilar 04 | cream-100 → sun-400, círculo orange-400 → 600 (85 %), 3 produtos; selo de vidro no canto superior esquerdo |

Com 4 ou 3 produtos o arranjo (posição, tamanho e rotação de cada um) é o do
Figma; com 1 ou 2 é adaptação.

## Comportamento

- **Pilares**: ímpares com card à esquerda e fundo branco; pares com card à
  direita e fundo lavanda (surface/muted). No Tablet/Mobile, card antes do
  texto, 560 alinhado à esquerda no Tablet.
- **Frase (digitação)** — descrição do componente no Figma: etapa 0 oculta;
  ao entrar na área, cada linha é revelada com Smart Animate linear (~55 ms
  por caractere) e o cursor pisca no fim. Aqui: IntersectionObserver (50 %
  visível), `clip-path` em `steps(nº de letras)` e o cursor andando junto.
  Sem JS ou com `prefers-reduced-motion`, a frase aparece inteira. O texto é
  real e o leitor de tela lê tudo.

## Fiel ao Figma × adaptação

Fiel: paddings/gaps dos três frames, alturas do topo (820/620/520) e
degradês, card da Introdução (135°, radius 36, sombra navy-500 30 %), números
vazados (SVG do Figma, contorno 1,5 px sky-500/60), frase com barra
(Heading/H4 navy-500), cards dos pilares, Compromisso (126,87°, vidro white/8
blur 20) e a frase final (Display/L com degradê; Mobile 22 px).

Adaptações:

1. **Margem lateral no Mobile**: 20 px (grid do tema, como nas outras
   páginas); o frame usa 24.
2. **Card no Mobile**: 100 % da largura útil (342); o frame desenha 350.
3. **Rotação dos produtos e faixas** em torno do centro (o Figma exporta a
   posição sem dizer a origem; diferença de 1–2 px).
4. **"60 m" no Mobile**: Display/L (32), como o frame; Tablet/Desktop
   Display/XL (64/96).
5. **1 ou 2 produtos** e **5º pilar em diante** (número em texto com contorno)
   não existem no Figma.
6. **Gatilho da digitação**: 50 % da frase visível (no protótipo é
   MOUSE_ENTER na área).

## Revisão da primeira versão (o que mudou)

- As chaves `field_er_sust_pilares`, `field_er_sust_pilar_titulo` e
  `field_er_sust_pilar_texto` eram **as mesmas** do grupo da Home
  (`group_er_home_secoes`) — com os dois JSON carregados, o ACF ficava com
  uma definição só para as duas páginas. Agora tudo é `sus_` /
  `field_er_sus_*`.
- O grupo só existia em `acf-json/`, sem cópia no bootstrap e sem seed.
- Havia texto de reserva no PHP (`'60 m'`), cores soltas em hex (`#44a5e3`,
  `#c3e2f5`, `#f59e0b`…), medidas estimadas (alturas, gaps, corte em 1024 em
  vez de 1280) e cor de texto gray-700 (o Figma usa text/primary).
- A estrutura não seguia o Figma: faltavam o sobretítulo do Compromisso, os
  números vazados, a barra das frases, os selos e a frase com digitação (a
  assinatura era só uma gota e uma linha).
