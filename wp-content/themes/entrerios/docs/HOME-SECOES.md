# Home — Linhas, Produto 360°, Sobre, Sustentabilidade, Instagram e Rodapé

Figma: `Screen/Home/Desktop` (seções abaixo da vitrine) e os componentes
`Linha Card`, `Produto 360°` (Componentes do protótipo, 3 variantes ×
Desktop/Tablet/Mobile), `Play Button` e `Footer`. Lido em 20/10/2026.

## Arquivos

| Seção | Template | CSS | JS |
|---|---|---|---|
| Linhas de bebidas (bento) | `template-parts/home/linhas.php` | `home.css` | — |
| Produto 360° | `template-parts/home/produto-360.php` | `home.css` | `assets/js/produto-360.js` |
| Sobre a Entre Rios | `template-parts/home/sobre.php` | `home.css` | `assets/js/sobre.js` |
| Sustentabilidade | `template-parts/home/sustentabilidade.php` | `home.css` | — |
| Instagram | `template-parts/home/instagram.php` | `home.css` | — |
| Rodapé (todas as páginas) | `footer.php` | `assets/css/footer.css` | `assets/js/footer.js` |

## Campos

Grupo **Home — Linhas, 360°, Sobre, Sustentabilidade e Instagram**
(`group_er_home_secoes`, só no template Home), uma aba por seção:

- **Linhas**: `linhas_cards` — linha, link do card e até 3 produtos
  (produto + tamanho da foto P/M/G). Nome, cores e palavra de fundo vêm da
  linha; foto e nome, do produto.
- **Produto 360°**: `p360_dica` e `p360_itens` — rótulo do chip, linha
  (cores do fundo), sobretítulo, título, texto, vistas (galeria em ordem de
  giro), até 2 destaques (rótulo + valor), cor do brilho e cor das órbitas.
- **Sobre**: `sobre_video` (MP4), `sobre_capa`, `sobre_rotulo`,
  `sobre_titulo`, `sobre_destaque`, `sobre_texto`, `sobre_botao`.
- **Sustentabilidade**: `sust_rotulo`, `sust_titulo`, `sust_texto`,
  `sust_pilares` (ícone gota/garrafa/folha, título, texto; até 3).
- **Instagram**: `insta_rotulo`, `insta_usuario`, `insta_url`,
  `insta_posts` (imagem + link). Feed curado no admin, sem API.

Linha: novo campo `cor_profunda` (tom 950, início do gradiente do 360°).

Opções do Site → Rodapé: `rodape_titulo`, `rodape_social_rotulo`,
`rodape_social`, `copyright`, `rodape_politica` (+ `logo`, `endereco`,
`telefone`, `email_contato` que já existiam). Menu: localização "Menu do
rodapé" (o bootstrap aponta para o Menu principal). Os campos sem uso
`tagline` e `certificacoes` saíram.

Cada seção some quando o campo principal está vazio; não há texto ou imagem
de fallback no PHP.

## Texto vazado (Montserrat Display com contorno)

Todos os textos vazados são SVG, não `-webkit-text-stroke` (que desenha o
contorno por dentro e por fora e estraga as junções da Montserrat Black):

| Arquivo | Origem |
|---|---|
| `hero-palavra-entre-rios.svg` | Figma, Copy as SVG (Hero, BG Word) |
| `marquee-conheca-nossos-produtos.svg` | Figma, Copy as SVG (Marquee). Sprite: o contorno vai uma vez e as 8 cópias do letreiro usam `<use>` (cheia navy-900 e vazada com contorno externo 1,5 px navy-500) |
| `linha-palavra-{agua-mineral,energeticos,refrigerantes,guarana-mais}.svg` | Figma, Copy as SVG (Linha Card) |
| `sobre-2002.svg` | Figma, Copy as SVG (Vídeo Institucional › 2002) |
| `pilar-numero-0{1,2,3}.svg` | Figma, Copy as SVG (Pilar › 01/02/03) |
| `footer-wordmark-entre-rios.svg` | Montserrat Black 900 a 206 px* |

Todos conferidos por hash com o que o Figma copiou (o do letreiro também), menos a palavra do hero
(transcrita antes do hash; conferida visualmente).

\* O Figma em modo leitura não exporta os glifos do wordmark do rodapé (estão
recortados, escondidos abaixo da coluna, à espera do reveal). Os contornos
foram gerados da própria fonte com as posições das colunas `L/x/n` do Figma.
Conferido: os contornos da fonte batem 1:1 com os que o Figma exporta (mesma
largura e altura de "0" e "1" nos números dos pilares).

O letreiro só usa o SVG quando o campo `marquee_texto` é "Conheça Nossos
Produtos"; outro texto cai no texto HTML (com o contorno do
`-webkit-text-stroke`).

Padrão de todos: máscara que esconde a metade interna do traço (contorno só
por fora, como o "Outer alignment" do Figma), `stroke="currentColor"` para a
cor vir do CSS e `vector-effect: non-scaling-stroke` para o traço manter a
espessura do Figma em qualquer escala.

Ícones dos pilares (`icone-gota/garrafa/folha.svg`) e a assinatura
`alfama-web.svg` também são exportações do Figma.

## Comportamento

- **Linha Card · Hover (400 ms ease-out)**: produtos sobem 24 px e abrem em
  leque (±6°), a palavra vai para a esquerda e clareia (white/25 → 45),
  botão 56 → 64, sombra da cor da linha.
- **Produto 360°**: com JS e sem `prefers-reduced-motion`, a seção vira uma
  pista de 260svh e o palco fica preso na tela. O quanto se rolou da pista
  vira 0° → 360° em passos de 5° (contador 01 → 72), troca as vistas, gira o
  botão do mostrador e enche o anel e a barra. Destaque 1 na primeira
  metade, destaque 2 na segunda. Chips = abas (setas, Home, End) com
  dissolve de 400 ms. As vistas da variante são pré-carregadas quando a
  seção se aproxima ou o chip é clicado.
- **Vídeo**: só a capa carrega com a página. O MP4 entra no clique em Play
  (com controles nativos).
- **Rodapé**: as letras do wordmark sobem de baixo, em sequência, quando ele
  entra na tela. Sem JS ou com reduced motion, ficam no lugar.

## Mídia (bootstrap v3)

- `p360-guarana-mais-v2-01…70.webp` (bootstrap v4) — 70 quadros do giro
  completo do copo (`gemini_generated_video_b140ba56_frames`, 1280×720 com
  fundo preto). Fundo removido e o MESMO recorte em todos (caixa que contém o
  copo em todos os quadros, 443×672), para o produto não "pular" entre
  quadros. ~29 KB cada, 2 MB no total. O v4 só troca a galeria se ela ainda
  for a do seed v3 (4 fotos); galeria editada pelo cliente não é tocada.
- `p360-{dub-2-litros,dub-lata-269ml}-01…04.webp` — das pastas
  "Imagem 360 Graus" (o DUB 2 L veio com fundo branco: recortado). 796 px de
  largura máxima, 120–200 KB cada.
- `sobre-video.mp4` — `lv_0_20260412134842 2.MP4` (60 MB, 1080×1920)
  recodificado em 720p H.264 / AAC 96k com faststart: 6,5 MB.
- `sobre-video-capa.webp` — quadro do próprio vídeo (painel do meio).
- `instagram-*.webp` — composições: foto do produto sobre o gradiente da
  linha (o Figma usa fotos que não estão na pasta do projeto).

## Fiel ao Figma × adaptação

Fiel: medidas e posições Desktop (anotadas "Figma" no CSS), textos da Home,
cores dos fundos e brilhos do 360°, tokens de vidro, raios e sombras.

Adaptações (não estão no Figma):

1. **Linhas no Tablet/Mobile**: 1 card por fileira; no Mobile o card é 5:6 e
   o leque é centralizado e ampliado.
2. **Tamanho das fotos no Linha Card** em 3 degraus (P 213, M 277, G 345 px
   do Figma) em vez da medida de cada slot.
3. **Água no Linha Card**: a lista mostra os produtos das fotos (500 ml,
   garrafão, 1,5 L); no Figma a lista diz "Copo 200ml / Garrafa 500ml / Garrafão".
4. **Produto 360° no Tablet**: mesmo layout em coluna do Mobile, sem
   mostrador (só o grau) e sem destaques.
5. **Duração da pista** (260svh) e o giro por quadros de 5°. O Guaraná Mais
   tem 70 quadros (giro contínuo); os DUB, 4 fotos (as pastas só têm 4). A
   galeria aceita até 72 quadros, sem mudar código.
6. **Sobre no Tablet**: card do vídeo em cima, texto embaixo (2 colunas só a
   partir de 1024 px).
7. **Pilares, Instagram e rodapé no Tablet/Mobile**: empilhados; o feed do
   Instagram rola na horizontal no Mobile e vira 2 × 2 no Tablet.
8. **Copyright**: "© 2026 - …" (o Figma tem "@Copyrigth").
9. **Botão Primary**: círculo do ícone branco com seta navy (lido só o
   estilo Light no Figma).

## Conteúdo para o cliente revisar

- Textos do Figma com erros, mantidos como estão: "Guaraná com açai e
  giseng", "no sabores", "de compõem", "Localizadas à 60 metros".
- Os textos novos de "Sobre a Entre Rios.docx" e "Sustentabilidade.docx"
  são mais longos e servem às páginas internas; a Home segue o Figma.
- Telefone no rodapé sai igual ao do cabeçalho (`79 3015-1141`); o Figma do
  rodapé mostra `(79) 3015-1141`.
- O DUB aparece com o destaque "PRODUZIDO COM Água mineral", como no Figma.
