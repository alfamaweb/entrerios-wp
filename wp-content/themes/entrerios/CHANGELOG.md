# Changelog

## Entre Rios — 2026-10-29 · Página Sustentabilidade (revisão + conteúdo)

Detalhes em `docs/SUSTENTABILIDADE.md`.

- Página refeita a partir do Figma (Desktop, Tablet e Mobile): topo, introdução
  com card em degradê, 4 pilares alternados (cards ilustrados montados em
  código, números vazados e frase com barra), Compromisso e a frase final com
  efeito de digitação (`assets/js/sustentabilidade.js`).
- Grupo `group_er_sustentabilidade` reescrito com prefixo `sus_`: a versão
  anterior reutilizava as chaves `field_er_sust_pilar*` da Home.
- `aw_slugs_de_css()` e `aw_registrar_assets()` carregam CSS/JS pelo template.
- Tokens novos (alphas, `illustration/*`, Shadow/XL) em `style.css`.
- Bootstrap v9: liga o template, limpa os campos `sust_*` desta página, grava
  textos e imagens do Figma e importa 7 recortes de produto.

## Entre Rios — 2026-10-27 · Contato e Política de Privacidade

Detalhes em `docs/CONTATO-POLITICA.md`.

- `page-contato.php` reescrito: topo em degradê, formulário do Figma (mesmo
  dispatcher `contato`), card "Nossos Contatos" com os dados das Opções do
  Site e mapa do Google (`loading="lazy"`, URL validada).
- Template novo `page-politica-de-privacidade.php`: topo com data da última
  atualização, sumário sticky (rolagem suave 600 ms e seção ativa,
  `assets/js/politica.js`) e seções em repetidor com numeração automática.
- Grupos `group_er_contato` e `group_er_politica`; `inc/institucional.php`;
  ícones `icone-{pin,telefone,email}.svg`.
- `aw_slugs_de_css()` carrega `contato.css` e `politica-de-privacidade.css`
  pelo template (qualquer slug).
- Bootstrap v7: liga os templates e grava os textos do Figma. v8: publica a
  página Política de Privacidade (era o rascunho padrão do WordPress).

## Entre Rios — 2026-10-26 · Animação do palco do produto

- Movimento do protótipo (Screen/Product/<linha>) no topo da página do
  produto: foto flutua 16 px (4 s), círculo pulsa (5 s) e bolhas sobem
  aparecendo e sumindo. Medidas em `cqw`, escalam com o palco. Ver
  `docs/PRODUTOS.md`, adaptação 6.

## Entre Rios — 2026-10-26 · Página Produtos e página do produto

Detalhes em `docs/PRODUTOS.md`.

- Template `page-produtos.php` (Template Name: Produtos): topo com os
  produtos, abas por linha, filtros (Categoria, Embalagem, Tipo) e "Carregar
  mais" de 12 em 12 — por AJAX com JS, por GET sem JS; a URL guarda o estado.
- `single-produto.php`: palco na cor da linha, versões (TIPO/SABOR/TAMANHO),
  sanfona nativa, "Fale Conosco" e relacionados.
- Taxonomias `embalagem` e `tipo_produto`; grupos `group_er_produtos` e
  `group_er_produto`; bootstrap v6 (termos, fichas, versões, ordem global,
  textos do Figma).
- Product Card leva para a página do produto; `/linha/<slug>/` redireciona
  para a página Produtos filtrada.
- `.botao-primario`, `.botao-claro`, `.aba-linha` e o breadcrumb `.trilha-er`
  passam a ser globais (`assets/css/style.css`).
- Item "Produtos" do menu ativo na página do produto (filtro `aw_itens_do_menu`
  em `aw_itens_do_menu()`).

## Entre Rios — 2026-10-21 · Página A Entre Rios

Detalhes em `docs/A-ENTRE-RIOS.md`.

- Template `page-a-entre-rios.php` (Template Name: A Entre Rios) com 8 seções
  em `template-parts/a-entre-rios/`: topo, origem, novos caminhos (colagem
  flutuando), fábrica (prévia do vídeo em loop + Play), galeria (setas,
  contador e pontos), pessoas, futuro e assinatura.
- Grupo `group_er_a_entre_rios`; bootstrap v5 liga o template e grava textos
  e mídia do Figma.
- `.botao-play` e `.seta-vitrine` passam a ser globais (`assets/css/style.css`).
- Helper `aw_paragrafos()`; tamanho de imagem `er-galeria`.

## Entre Rios — 2026-10-20 · Giro do Guaraná Mais em 70 quadros

- Produto 360° do Guaraná Mais: 70 quadros do vídeo do copo girando, sem o
  fundo preto e com o mesmo recorte (`p360-guarana-mais-v2-*.webp`).
- Bootstrap v4: troca a galeria só se ainda for a do seed (4 fotos).
- Galeria do 360° aceita até 72 quadros.

## Entre Rios — 2026-10-20 · Letreiro em SVG

- "Conheça Nossos Produtos" do letreiro (cheia e vazada) agora é o SVG
  exportado do Figma, em sprite com `<use>`: o vazado não mostra mais as
  letras sobrepostas do `-webkit-text-stroke`. Outro texto no campo segue
  como texto HTML.
- `aw_svg()` aceita `<use>` e `<symbol>`.

## Entre Rios — 2026-10-20 · Restante da Home e rodapé

Detalhes em `docs/HOME-SECOES.md`.

- Linhas de bebidas (bento), Produto 360° (rolagem gira o produto, chips),
  Sobre a Entre Rios (vídeo sob demanda), Sustentabilidade e Instagram.
- Rodapé novo (Figma "Footer") com wordmark que se revela ao rolar.
- Todo texto Montserrat Display vazado agora é SVG exportado do Figma
  (hero, linhas, "2002", números dos pilares) ou gerado da fonte (wordmark).
- Grupo `group_er_home_secoes`; campo `cor_profunda` nas linhas; campos de
  rodapé nas Opções do Site (saíram `tagline` e `certificacoes`).
- Tamanhos de imagem `er-produto-360`, `er-sobre-capa`, `er-instagram`.
- `aw_svg()` aceita `overflow`, `pathlength`, `stroke-dasharray` e afins.
- Bootstrap v3: 360°, vídeo, capas, posts do Instagram, textos do Figma,
  página Política de Privacidade e menu do rodapé.

## Entre Rios — 2026-10-20 · Letreiro e vitrine de produtos da Home

Detalhes em `docs/HOME-VITRINE.md`.

- Letreiro em loop (CSS) e vitrine com abas por linha, carrossel, barra de
  progresso e setas (`assets/js/vitrine.js`).
- Product Card e Card "Ver todos" globais em `assets/css/style.css`
  (`template-parts/card-produto.php`).
- Campo `cor_media` nas linhas; grupo `group_er_home_vitrine` na Home.
- Bootstrap v2: 27 produtos com fotos, campos da vitrine.
- Setas da vitrine em passos de cards inteiros, com o mesmo número de cards
  por clique em todas as abas (a linha mais longa leva 3 cliques; linhas com
  menos cards, menos cliques), em vez de pular direto ao primeiro / último.
- Tokens: `--alpha-neutral-white-12/14`, `--drop-shadow-card-produto(-hover)`.
- Reset do `style.css` movido para `@layer base`: os utilitários do Tailwind
  (text-label-caps, text-heading-h1…) agora vencem o reset em `button` e
  `h1–h6` — antes perdiam o peso e o tamanho em botões.

## Entre Rios — 2026-10-20 · Cabeçalho absoluto e hero do tamanho da tela

- `.site-header`: sempre `position: absolute`, `width: 100%`, sem fundo (a
  faixa navy das páginas internas saiu).
- Hero Desktop: `clamp(90svh, 62.5vw, 100svh)`, com toda a composição em
  `--u` por container query (texto, botões e seletor inclusive).
- Hero Tablet/Mobile: `min-height: 100svh`; a cena (`.hero-er__cena`, nova)
  escala para o espaço livre.
- `sizes` da imagem do produto ajustado à nova escala.
- Pontos decorativos (verde, amarelo, lima) removidos do hero.

## Entre Rios — 2026-10-19 · Cabeçalho e Hero Slider da Home

Detalhes, campos e adaptações em `docs/HOME-HERO.md`.

### Adicionado

- `header.php` + `assets/css/header.css`: Nav do Figma (logo, menu em vidro,
  contato, botões de vidro) e overlay do menu (links numerados, contato).
- `template-parts/home/hero.php`, `assets/css/home.css`,
  `assets/js/home-hero.js`: Hero Slider com crossfade em CSS, autoplay de 5 s,
  seletor de linhas e anterior/próximo, nos três breakpoints.
- CPT `produto` e taxonomia `linha`; `inc/linhas.php` com as cores por linha e
  a validação de contraste.
- mu-plugin `entrerios-bootstrap` (v1): importa 3 grupos ACF, cria as 4 linhas
  com cores, as páginas do menu, a Home como página inicial, o "Menu
  principal", sobe 5 imagens WebP e grava o conteúdo do Figma.
- Tokens: `--shadow-sm` e `--drop-shadow-hero-product(-tablet/-mobile)`.
- Tamanhos de imagem `er-produto-hero` e `er-chip`; helper
  `aw_itens_do_menu()`.

### Alterado

- `page-home.php`: o hero em Swiper e os destaques do starter saíram.
- Swiper não carrega mais na Home.
- Estilos antigos de cabeçalho e de `.hero-home` removidos de
  `assets/css/style.css`.

## Entre Rios — 2026-10-19 · Sombras, blur e motion do Figma

- `style.css`: effect styles `--shadow-md` e `--shadow-2xl`, 12 sombras
  locais por componente/estado, `--blur-16/20/24`, 12 `--alpha-*` e
  `--motion-*` lidos dos componentes do Figma.
- `--aw-sombra` removido: `.card:hover` usa `--shadow-product-card-hover`;
  toast e WhatsApp usam `--shadow-md`.
- Campos de formulário em foco seguem o Input Field do Figma (borda
  navy-500 + `--shadow-focus-ring`).
- `docs/TOKENS-FIGMA.md`: seção de sombras, blur, transparências e motion.

## Entre Rios — 2026-10-19 · Tokens do Figma

Identidade do starter substituída pelo design system do Figma "Entre Rios".
Mapa completo e pendências em `docs/TOKENS-FIGMA.md`.

### Alterado

- `style.css` seção 1: tokens com os nomes do Figma (codeSyntax WEB) — cores
  brand/neutral/accent/category, aliases semânticos `--color-*`, `--radius-*`,
  `--font-size-*` responsivos (Mobile/Tablet/Desktop) e grid.
- Todos os `var(--aw-*)` de cor, raio e fonte trocados pelos tokens do Figma em
  `style.css` e `assets/css/style.css`. Restam só os `--aw-*` que o Figma ainda
  não define (estados, sombra, transição, altura do header).
- `src/input.css`: breakpoints do Bootstrap trocados por `tablet:` (768) e
  `desktop:` (1280); cores e text styles do Figma como utilitários
  (`bg-brand-navy-500`, `text-heading-h1`…); `.grid-figma`.
- Templates migrados: `md:` → `tablet:`, `lg:`/`xl:` → `desktop:` (43 classes,
  14 arquivos).
- `assets/css/style.css`: media query de 992px → 1280px; `#fff` →
  `var(--color-text-inverse)`.

### Corrigido

- `.container` era sobrescrito pelo utilitário nativo do Tailwind v4 (camada
  utilities vence components), que aplicava max-width por breakpoint. Agora é
  declarado com `@utility container`.
- `assets/css/fonts.css` existia mas nunca era enfileirado.

### Adicionado

- Montserrat variável (latin + latin-ext, OFL) em `assets/fonts/`, com
  `@font-face` em `fonts.css`, enfileirada antes do `aw-base` e preload do
  subset latin no `<head>`.
- `docs/TOKENS-FIGMA.md`.

## 3.0.0 — 2026-09-02

Primeira versão. Reescrita do boilerplate **Alfama WEB v2** ("None Plate"), a
partir do levantamento dos seis projetos em `espelho-tema/` (cassind,
condominial, enkan, kraft, stanza, nova-bairros).

### Mantido da v2

Constantes de caminho, um CSS por página, scaffolding na publicação,
desindexação de staging por convenção de domínio, ACF como camada de conteúdo
com página de Opções do Site, AJAX devolvendo HTML pronto, formulários autorais
com SMTP próprio, grade e breakpoints do Bootstrap dentro do Tailwind,
português no domínio e inglês na infraestrutura.

### Removido

- Toda a herança do Twenty Sixteen: `inc/back-compat.php`, `inc/customizer.php`,
  `inc/template-tags.php`, `genericons/`, `css/ie.css`, `css/ie7.css`,
  `css/ie8.css`, `js/html5.js`, `languages/twentysixteen.pot` e o corpo de
  `twentysixteen_scripts()`
- Bootstrap 5 (o CSS de 232 KB e o JS de 80 KB)
- jQuery 1.11.1
- WOW.js e `animate.min.css`
- Overlay `.loading`, que atrasava o primeiro paint

### Corrigido

- Nonce obrigatório em todo endpoint AJAX, inclusive `nopriv`
- Nenhuma credencial no tema; SMTP e reCAPTCHA por constantes de `wp-config.php`
- `blog_public` filtrado na leitura, sem escrita em option a cada request
- Scaffolding restrito a ambiente de desenvolvimento, com checagem de permissão
  e desligável por `AW_SCAFFOLDING`
- Cache de listagem invalidado em `save_post`, `deleted_post` e `edited_term`
- Seed de termos executado uma vez, com flag em option
- Modelagem versionada: CPTs em PHP e sincronia `acf-json/` ligada
- Uploads validados por extensão real, com teto de 10 MB e limpeza dos temporários
- Redirect pós-envio passando por `wp_validate_redirect()`

### Adicionado

- `functions.php` como bootstrap, com um módulo por responsabilidade em `inc/`
- `AW_Query_Service`, `AW_Ajax_Controller`, `AW_Form_Dispatcher`, `AW_Mailer`,
  `AW_Recaptcha`
- `aw_paginacao_html()` — uma implementação de paginação, usada no SSR e no AJAX
- `app.js` em vanilla ES2018: menu, swiper por data-attribute, filtro com
  debounce e `AbortController`, cascata de taxonomia, formulário por AJAX,
  máscara de telefone e toast
- Acessibilidade: skip link, `aria-*` no menu, foco visível,
  `prefers-reduced-motion`, `aria-live` no contador de resultados
- `.gitignore`, `.editorconfig`, `docs/` e caso de referência completo
