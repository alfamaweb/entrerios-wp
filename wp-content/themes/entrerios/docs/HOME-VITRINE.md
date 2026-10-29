# Letreiro e Vitrine de produtos da Home

Fonte no Figma: `Screen/Home/Desktop` → **Marquee — Conheça Nossos Produtos
(loop)** e **Produtos / Vitrine** (variants por categoria), mais Tablet e
Mobile; estados em `04_Components` → Product Card, Card Ver Todos, Tab e
Arrow Button.

## Arquivos

| Arquivo | Papel |
|---|---|
| `template-parts/home/marquee.php` | Letreiro em loop |
| `template-parts/home/vitrine.php` | Abas + carrossel por linha + navegação |
| `template-parts/card-produto.php` | Product Card (reutilizável na página Produtos) |
| `assets/css/home.css` | Letreiro e vitrine |
| `assets/css/style.css` | Product Card e Card "Ver todos" (globais) |
| `assets/js/vitrine.js` | Abas, setas, barra de progresso, arrastar (~2 KB, `defer`, só na Home) |

## Conteúdo

- **Produtos** = CPT `produto`: título = nome do card, imagem destacada = foto
  recortada (PNG/WebP transparente, 400×580), taxonomia `linha`, atributo
  **Ordem** = posição no carrossel (desde o bootstrap v6 a ordem é global, a mesma da página Produtos). O card leva para a página do produto.
- **Home → "Home — Letreiro e vitrine"** (`group_er_home_vitrine`):
  `marquee_texto`, `vitrine_rotulo`, `vitrine_linhas` (abas, na ordem) e
  `vitrine_ver_todos` (link do último card).
- **Linhas**: novo campo `cor_media` (tom 500). O card usa claro → médio no
  círculo, médio → forte no botão; título, aba ativa, barra e setas usam o
  forte.

O bootstrap (v2) cria os 27 produtos do Figma com as fotos da pasta
`Conteúdo - Entre Rios` (WebP, ~1,2 MB no total) e preenche os campos.

## Comportamento

- Abas: troca título e carrossel em fade de 500 ms (Smart Animate do Figma);
  setas, Home e End no teclado.
- Carrossel: rolagem nativa (dedo, trackpad, teclado) e arrastar com o mouse.
  A barra mostra a fração visível e a posição.
- Setas: avançam / voltam **N cards inteiros por clique** (600 ms
  ease-in-out). N é o mesmo em todas as abas: o que cabe na tela, reduzido
  só o bastante para a linha mais longa levar 3 cliques. Assim a linha com
  menos cards precisa de menos cliques e o ritmo da rolagem é igual em todas
  (ex.: 1600 px com Água 12 e Refrigerantes 8 → 2 cards por clique: Água 4
  cliques, Refrigerantes 2). Ficam esmaecidas quando não há para onde rolar.
  *Ajuste pedido: o protótipo pulava direto ao primeiro / último card.*
- Hover do card (300 ms ease-out): fundo branco com borda e sombra, produto
  sobe 18 px e inclina, círculo cresce (220 → 252), seta gira −45°, nome na
  cor da linha. "Ver todos": círculo 92 → 104, seta ↗, gradiente clareia
  (navy-500 → sky-500).
- `prefers-reduced-motion`: letreiro parado, sem fades nem transições.

## Medidas (Desktop / Tablet / Mobile)

| | Desktop | Tablet | Mobile |
|---|---|---|---|
| Letreiro: padding / gap | 32 / 40 | 24 / 32 | 16 / 16 |
| Vitrine: padding | 96 / 120 | 72 / 72 | 56 / 56 |
| Vitrine: gap | 48 | 32 | 24 |
| Laterais | área útil 1280 (160 em 1600) | 40 | 24 |
| Card | 296×420 em todos | | |

## Adaptações (não estão no Figma)

1. Velocidade do letreiro (~80 px/s) e tamanho do ícone de gota no Tablet e
   Mobile (0,625 × o texto; o Figma diz só "menor").
2. Desenho do ícone de gota refeito em SVG com as medidas do vetor (24×35).
3. Ângulo dos gradientes (círculo e botão: vertical; "Ver todos": 135°).
4. Setas esmaecidas (40%) quando não há rolagem.
5. Gap do cabeçalho no Mobile (16, igual ao Tablet).
6. ~~O card leva para a página da linha~~ — desde 26/10 leva para a página do produto (`docs/PRODUTOS.md`); `/linha/<slug>/` redireciona para a página Produtos filtrada. Nota antiga:
   template próprio.
