# Cabeçalho e Hero Slider da Home

Fonte no Figma: arquivo "Entre Rios" → `01_Screens/Desktop` → "Componentes do
protótipo" → **Hero Slider** (variants Água Mineral, Energéticos, Refrigerantes
e Guaraná Mais), mais os frames Tablet (768) e Mobile (390) e o overlay
`Screen/Overlay/Menu-Open/Mobile`.

## Arquivos

| Arquivo | Papel |
|---|---|
| `header.php` | Barra (logo, menu em vidro, contato, botões de vidro) e overlay do menu |
| `assets/css/header.css` | Estilos do cabeçalho e do overlay, em todas as páginas |
| `page-home.php` | Template "Home": monta as seções em `template-parts/home/` |
| `template-parts/home/hero.php` | Hero Slider |
| `assets/css/home.css` | Hero Slider (carregado só na Home) |
| `assets/js/home-hero.js` | Autoplay, seletor, anterior/próximo (~2 KB, `defer`, só na Home) |
| `inc/linhas.php` | Cores por linha → `style="--linha-…"` + validação de contraste |
| `inc/post-types.php` | CPT `produto` + taxonomia `linha` |
| `wp-content/mu-plugins/entrerios-bootstrap.php` | Importa campos e grava o conteúdo inicial |

## Campos (ACF PRO)

Grupos em `mu-plugins/entrerios-bootstrap/acf/*.json`, no formato de exportação
do ACF, importados para o banco pelo bootstrap. Depois da importação, edite
pela tela do ACF.

### Opções do Site → "Cabeçalho e menu" (`group_er_cabecalho`)

| Campo | Nome | Tipo |
|---|---|---|
| Logo | `logo` | imagem (ID) — selo redondo, o tema desenha a borda branca |
| Rótulo do contato | `contato_rotulo` | texto ("Entre em Contato") |
| Imagem do menu | `menu_imagem` | imagem (ID) — produto em transparência no overlay |

O telefone, o e-mail e as redes vêm das abas já existentes das Opções do Site
(`telefone`, `email_contato`, `redes_sociais`). Os links do menu vêm de
Aparência → Menus → "Menu principal" (localização `primary`).

### Linhas → cores (`group_er_linha`, na taxonomia `linha`)

| Campo | Nome | Uso |
|---|---|---|
| Gradiente — início / meio / fim | `cor_hero_inicio`, `cor_hero_meio`, `cor_hero_fim` | Fundo do hero |
| Miniatura — tom claro / forte | `cor_chip_clara`, `cor_chip_forte` | Círculo do seletor |
| Destaque | `cor_destaque` | `color/category` da linha |

Ao salvar, o WordPress recusa um `cor_hero_inicio` com contraste menor que
4.5:1 contra o branco (o texto do hero fica sobre ele). Uma linha nova vira um
slide novo só com as cores e uma imagem: não precisa de CSS.

### Home → "Home — Hero" (`group_er_home_hero`, no template `page-home.php`)

| Campo | Nome | Tipo |
|---|---|---|
| Slides do hero | `hero_slides` | repeater: `linha`, `formato` (garrafa/copo), `inclinado`, `titulo`, `texto`, `imagem`, `botao` (link) |
| Palavra de fundo | `hero_palavra_fundo` | texto vazado atrás do produto |
| Botão circular | `hero_cta_circular` | link (só no desktop) |
| Troca automática | `hero_intervalo` | segundos (0 desliga) |

O seletor de linhas embaixo do hero é montado a partir dos próprios slides, na
mesma ordem, com o nome do termo e a miniatura gerada da imagem do slide.

## Imagens

Geradas a partir de `Conteúdo - Entre Rios/` e importadas para a Biblioteca de
Mídia pelo bootstrap (WebP com transparência):

| Arquivo | Origem | Tamanho |
|---|---|---|
| `logo-entre-rios.webp` | `Marca Entre Rios/Entre Rios Nova Marca 2025.pdf` | 464×464 |
| `hero-agua-mineral.webp` | composição: Garrafa 1,5 L ao centro, 500 ml sem gás à esquerda e com gás à direita | 800×1520 |
| `hero-dub-2l.webp` | `DUB - 2 LITROS NOVA MARCA 2026 SLEEVE.png` | 800×1520 |
| `hero-qtal-guarana-2l.webp` | `REFRIGERANTE FRESH QTAL GUARANÁ 2L 2026.png` | 800×1520 |
| `hero-guarana-mais-copo.webp` | `COPO GUARANÁ MAIS 2026 OFICIAL.png` | 600×1120 |

Tamanhos registrados: `er-produto-hero` (800×1520, sem corte) e `er-chip`
(224×224). Os PNGs originais tinham de 4 a 28 MB; os WebP somam ~0,8 MB.

## Altura e escala

- **Cabeçalho**: sempre `position: absolute; width: 100%`, sem fundo, por cima
  do primeiro bloco da página (o hero reserva o espaço no `padding-top`).
- **Hero Desktop (≥1280)**: `height: clamp(90svh, 62.5vw, 100svh)` — proporção
  1600:1000 do Figma, presa entre 90% e 100% da tela. Tudo dentro dele usa a
  unidade `--u = min(altura/1000, largura/1600)` (container query), então a
  composição do Figma escala inteira, sem cortes nem sobreposição.
- **Hero Tablet/Mobile**: `min-height: 100svh`. Texto, botão e seletor em
  tamanho real; a cena (produto, ondas, bolhas) escala para caber no espaço
  restante. Só passa da tela em telas muito baixas (ex.: celular deitado).
- `svh` não muda quando a barra do navegador some ao rolar: sem salto de
  layout (CLS) e a imagem do LCP já nasce no tamanho final.

## Desempenho

- Sem Swiper na Home: o crossfade é CSS (`opacity`/`transform`), e o JS só
  troca classes. O Swiper continua disponível para o single de produto.
- Imagem do primeiro slide com `fetchpriority="high"`; as demais `lazy`.
- `backdrop-filter` só nos elementos de vidro (menu e botões).
- Autoplay pausa fora da tela, com a aba em segundo plano, com o ponteiro
  sobre o hero ou com foco dentro dele, e não roda com
  `prefers-reduced-motion` (que também desliga flutuação, bolhas e anel).

## Acessibilidade

- Seletor de linhas = `tablist` com setas, Home e End; slides inativos com
  `inert`; um único `<h1>` (título do primeiro slide).
- Menu aberto = `dialog` modal; Esc fecha; foco volta ao botão.

## Fiel ao Figma × adaptação

Fiel ao Figma: medidas, posições e tokens dos três breakpoints anotados nos
CSS com "Figma", tempos de interação (5 s, 900 ms, 600 ms do chip, 400 ms do
menu, 12 s do anel), estados hover/active dos componentes.

Adaptações próprias, não conferidas:

1. **Ângulo do gradiente**: 135°. O modo de visualização do Figma não mostra o
   ângulo.
2. **Escala**: fora de 1600×1000 o hero escala proporcionalmente (Desktop)
   ou só a cena escala (Tablet/Mobile) — o Figma só tem os três frames fixos.
3. **Bolhas no Tablet/Mobile**: reposicionadas proporcionalmente a
   partir do Desktop (o Figma só dá as coordenadas do Desktop). Velocidade da
   subida e amplitude da flutuação do produto também são próprias.
4. **Ondas, brilho e produto inclinado/copo no Tablet/Mobile**: escalados a
   partir das medidas lidas (Ripple1 e produto do Tablet; produto do Mobile).
5. **Cabeçalho das páginas internas**: o mesmo cabeçalho transparente; cada
   página precisa abrir com um bloco de fundo escuro que reserve o espaço dele.
6. **Overlay do menu**: tamanhos do botão "Fechar" e espaçamentos da lista
   aproximados da tela; "Anterior/Próximo" com sublinhado de 1,5 px.
7. **Miniatura do seletor de Refrigerantes e Guaraná Mais**: gradiente 300 →
   700, mesmo padrão das outras duas linhas.

## Conteúdo que precisa de revisão do cliente

- No Figma os 4 slides repetem o título e o texto da Água Mineral. O seed
  reproduz o desenho; troque em Páginas → Home.
- A descrição do componente fala em "5 slides"; o desenho tem 4.
