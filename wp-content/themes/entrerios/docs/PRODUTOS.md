# Página Produtos e página do produto

Figma: `01_Screens/Desktop` → **Screen/Products/Desktop** (180-635, 1600) e
**Screen/Product/Agua-Mineral/Desktop** (182-862), mais as variantes
Energéticos, Refrigerantes e Guaraná Mais; `03_Screens/Mobile` →
**Screen/Products/Mobile** (194-1882) e **Screen/Product/Agua-Mineral/Mobile**
(194-2192). Lido em 26/10/2026 em modo leitura: tamanhos, paddings, gaps,
text styles, cores, posições do topo e do palco e os textos da sanfona. A
estrutura segue os wireframes (`Products/Desktop.png`, `Product/Agua-Mineral/Desktop.png`).

## Arquivos

| Papel | Arquivo |
|---|---|
| Template (Template Name: Produtos) | `page-produtos.php` |
| Topo e catálogo | `template-parts/produtos/{hero,catalogo}.php` |
| Breadcrumb (as duas páginas) | `template-parts/produtos/trilha.php` |
| Página do produto | `single-produto.php` |
| Partes do produto | `template-parts/produto/{topo,palco,versoes,sanfona,relacionados}.php` |
| Regras (filtros, AJAX, versões, relacionados, menu) | `inc/produtos.php` |
| Estilos | `assets/css/produtos.css` · `assets/css/produto.css` |
| Filtros sem recarregar (~4 KB, `defer`) | `assets/js/produtos.js` |
| Campos | `acf-json/group_er_produtos.json` · `acf-json/group_er_produto.json` (cópias em `mu-plugins/entrerios-bootstrap/acf/`) |

`.botao-primario`, `.botao-claro` e `.aba-linha` saíram de `home.css` para
`assets/css/style.css` (agora as três páginas usam); `.trilha-er` (breadcrumb)
também é global. O Product Card (`card-produto.php`) agora leva para a página
do produto, e não mais para a linha.

## Conteúdo

**Taxonomias novas** do CPT `produto` (caixas no editor do produto):
`embalagem` (Copo, Garrafa, Lata, Garrafão / Galão) e `tipo_produto`
(Com gás, Sem gás). Sem arquivo público — a listagem é a página Produtos.
A linha continua na taxonomia `linha`; `/linha/<slug>/` redireciona para
`/produtos/?categoria[]=<slug>`.

**Página Produtos** (grupo "Produtos — Página", `prod_`):

- Topo: `prod_hero_titulo` (vazio esconde), `prod_hero_imagens` (até 4 fotos,
  na ordem lata, garrafa, garrafa, copo).
- Catálogo: `prod_linhas` (ordem das abas/categorias), `prod_mais_texto`
  (botão "Carregar mais"; vazio esconde).
- Página de cada produto: `prod_rel_titulo` (vazio esconde os relacionados),
  `prod_rel_link`, `prod_cta` (botão "Fale Conosco").

**Produto** (grupo "Produto — Ficha", `produto_`): `produto_tamanho`,
`produto_sabor`, `produto_imagem` (foto maior, opcional), `produto_versoes`
(relação com as outras versões) e `produto_sanfona` (título + texto; o
primeiro abre sozinho).

O bootstrap v6 liga o template, cria os termos, preenche embalagem, tipo,
tamanho, sabor e versões dos 27 produtos, passa a ordem para global (a
página lista tudo junto), grava os textos da página e a sanfona dos três
produtos que o Figma desenha (Garrafa 500ml Com Gás, Q'tal Fresh Guaraná 2L e
DUB Lata 269ml). Só grava campo vazio.

## Comportamento

- **Filtros**: dentro de um grupo as opções somam (OU); entre grupos,
  restringem (E). Aba = uma categoria; marcar várias categorias deixa nenhuma
  aba ativa; nenhuma = TODOS. Contagem de cada opção = produtos publicados.
- **Sem JS**: o catálogo é um formulário GET (abas são links, "Aplicar
  filtros" aparece, "Carregar mais" vai para `?pagina=N` e mostra N × 12).
- **Com JS**: cada clique busca por AJAX (`aw_filtrar_produtos`, nonce do
  `AW_Ajax_Controller`), "Carregar mais" acrescenta 12 e leva o foco ao
  primeiro card novo, a barra e o "Mostrando X de Y" acompanham e a URL
  guarda o estado (recarregar ou compartilhar mantém os filtros).
- **Versões** (TIPO / SABOR / TAMANHO): o produto + os do campo "Outras
  versões". Em cada grupo entram as versões que só mudam naquele atributo —
  Garrafa 500ml Com Gás mostra TIPO Sem gás/Com gás e TAMANHO 330ml/500ml;
  DUB Lata mostra os três tamanhos; Q'tal Guaraná 2L mostra SABOR Guaraná e
  TAMANHO 250ml/2L (como no Figma). Pílula branca = atual; contorno = link.
- **Sanfona**: `<details>` nativo (sem JS); animado onde o navegador anima
  até `auto` (Chrome 131+).
- **Relacionados**: primeiro as outras versões, depois o resto da linha
  (4 cards). Mobile: carrossel de rolagem.
- **Menu**: na página do produto, "Produtos" fica ativo (barra e overlay).

## Fiel ao Figma × adaptação

Fiel: paddings/gaps Desktop e Mobile, text styles, Category Tabs, painel de
filtros (290, radius 28, opções de 40, contagem), grade de 296 com gap 24,
paginação (texto, barra 280 × 4, Button Primary), degradês, palco (Glow 700,
Blob 480 na cor da linha, Ring 580 tracejado 4/10, 8 bolhas, sombra da foto),
pílulas de versão, sanfona de vidro (blur 20) e relacionados.

Adaptações:

1. **Tablet** (768–1279): sem frame conferido. Grade de 2 colunas, palco
   até 460, topo com o mesmo respiro das outras páginas internas.
2. **Filtros abaixo de 1280**: o Mobile do Figma esconde o painel; aqui um
   botão "Filtros" (com o número de filtros marcados) abre e fecha o painel.
   No Desktop o painel acompanha a rolagem (sticky).
3. **Fotos do topo da página Produtos**: só no Desktop (no Mobile do Figma o
   grupo não aparece). Posição do grupo medida no wireframe.
4. **Ângulo dos degradês**: não aparece no modo de visualização; 165° (lista)
   e 140° (produto) reproduzem as cores medidas nos wireframes.
5. **Copo e garrafão no palco**: o Figma só desenha garrafa (720 de altura) e
   lata (640); copo e garrafão ficam com 500, centrados no círculo.
6. **Movimento do palco**: tirado do protótipo (o modo de visualização não
   mostra as keyframes, então foi medido quadro a quadro). Foto sobe 16 px e
   desce (ciclo de 4 s, ease-in-out); círculo pulsa até escala 1,06 (ciclo de
   5 s); bolhas sobem em linha reta aparecendo e sumindo (4–6 s, ~22 a
   ~56 px/s, fases desencontradas); anel e brilho parados. Durações e
   distâncias das bolhas são aproximadas. Desliga com `prefers-reduced-motion`.
7. **Checkbox marcado** e **hover das pílulas**: estados não lidos no Figma —
   navy-500 com check branco; pílula white/16.
8. **Título da sanfona**: estilo sem nome no Figma (20/120% no Desktop);
   segue a escala do Body/Large.
9. **Aba TODOS ativa**: o Figma liga `brand/navy-500` mas resolve preto
   (`#000`); seguimos o token (navy-500).
10. **Relacionados no Mobile**: o Figma usa um card menor (~262 × 303); aqui é
    o Product Card padrão (296 × 420), o mesmo da vitrine.

## Para o cliente revisar

- Sanfona: só 3 produtos têm texto (os do Figma). Os outros ficam sem
  sanfona até alguém preencher Composição / Informações / Release.
- Fotos: os PNGs dos produtos são 400 × 580 — no palco do Desktop a garrafa
  aparece com 720 de altura. Para nitidez, subir uma foto maior no campo
  "Foto em alta" (≈ 800 × 1500).
- O wireframe mostra 33 produtos; o catálogo cadastrado tem 27.
- Pílula "Com gás" (filtro e ficha) com g minúsculo, como no filtro do
  Figma; a ficha do Figma escreve "Com Gás".
