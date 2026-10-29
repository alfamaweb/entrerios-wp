# Página Contato e Política de Privacidade

Figma: `01_Screens/Desktop` → **Screen/Contact/Desktop** (183-990, 1600) e
**Screen/Privacy-Policy/Desktop** (225-1644, 1600); `03_Screens/Mobile` →
**Screen/Contact/Mobile** e **Screen/Privacy-Policy/Mobile** (390). Lido em
27/10/2026 em modo leitura: tamanhos, paddings, gaps, raios, text styles,
cores e as interações do sumário. A estrutura segue os wireframes
(`Downloads/Contact/Desktop.jpg` e `Downloads/Privacy-Policy/Desktop.jpg`).

## Arquivos

| Papel | Arquivo |
|---|---|
| Template Contato (Template Name: Contato) | `page-contato.php` |
| Seções do Contato | `template-parts/contato/{hero,contato,formulario,localizacao}.php` |
| Template Política (Template Name: Política de Privacidade) | `page-politica-de-privacidade.php` |
| Seções da Política | `template-parts/politica/{hero,conteudo}.php` |
| Funções (mapa, aceite, seções) | `inc/institucional.php` |
| Estilos | `assets/css/contato.css` · `assets/css/politica-de-privacidade.css` |
| Sumário (rolagem suave + seção ativa, ~1 KB, `defer`) | `assets/js/politica.js` |
| Ícones do card de contatos | `assets/img/icone-{pin,telefone,email}.svg` |
| Campos | `acf-json/group_er_contato.json` · `acf-json/group_er_politica.json` (cópias em `mu-plugins/entrerios-bootstrap/acf/`) |

O breadcrumb é o global (`template-parts/produtos/trilha.php`, `.trilha-er`) e
o botão é o Button Primary global (`.botao-primario`).

## Campos

**Contato — Página** (`contato_`): título do topo; título, texto, botão e
aceite do formulário; título do card de contatos; mapa (código "Incorporar
um mapa" do Google, inteiro ou só o src) e o botão "Abrir no Google Maps".
Endereço, telefone e e-mail do card vêm das **Opções do Site → Contato**
(os mesmos do cabeçalho e do rodapé).

**Política de Privacidade — Página** (`pol_`): título, rótulo e data da
última atualização (campo de data → "9 de outubro de 2026"), título do
sumário e o repetidor **Seções** (título + texto no editor: parágrafos,
listas, links). Número (01, 02…) e âncora do sumário são automáticos.

O bootstrap v7 liga os dois templates e grava os textos do Figma (só campo
vazio); o v8 publica a página da política (no site local ela era o rascunho
padrão do WordPress, `?page_id=3`, e o link do rodapé dava 404).

## Comportamento

- **Formulário**: mesmo dispatcher do starter (`AW_Form_Dispatcher`, id
  `contato`) — nonce, honeypot, reCAPTCHA, AJAX com JS e POST + toast sem JS.
  Rótulos acessíveis escondidos (no Figma só há placeholder). Enquanto envia,
  o spinner ocupa o círculo do botão.
- **Mapa**: iframe do Google com `loading="lazy"`; só URLs `https` de
  google.com/maps com `output=embed` ou `/maps/embed` são aceitas
  (`aw_google_maps_embed()`), qualquer outra coisa esconde o mapa.
- **Sumário**: clique rola em 600 ms ease-in-out (Figma: Scroll to, Ease in
  and out, 600ms), atualiza o `#hash` e leva o foco para a seção; a seção na
  faixa de 15–40 % da tela fica ativa; no fim da página, a última. Sem JS,
  âncoras comuns. Desktop: sticky a 32 px do topo.

## Fiel ao Figma × adaptação

Fiel: paddings/gaps Desktop e Mobile, text styles, degradês, card do
formulário (radius 32, borda, sombra navy-500 10 %), componente Campo (56,
radius 16, borda 1,5, surface/muted), card de contatos (degradê navy-900 →
navy-500, ícones 48 white/10 com traço sky-300), mapa 340 radius 32 com o
botão em 24/16, sumário (320, radius 24, itens radius 12, ativo branco,
inativo navy-900/75), selo do número, divisórias e marcadores 8 px sky-500.

Adaptações:

1. **Tablet** (768–1279): sem frame. Contato: card e mapa lado a lado abaixo
   do formulário. Política: sumário acima do texto, como no Mobile.
2. **Caixa de aceite da política** (não está no Figma): o formulário
   "contato" do tema pede `aceite_lgpd`; o texto é editável e o nome da
   política vira link. Só esvaziar se o handler não exigir o campo.
3. **Hover do campo** = borda navy-500; **foco** = borda + anel (Figma:
   "Clique/hover: Focus"). Erro (`:user-invalid`) na cor de erro do starter.
4. **Ângulo dos degradês**: 165° no topo (igual à página Produtos) e 135° no
   card de contatos.
5. **Título do card**: Desktop diz "Nossos Contatos" e Mobile "Nossa
   Localização"; usamos um campo só (texto do Desktop).
6. **Sumário sticky**, hover dos itens (white/60) e o realce de seção ativa
   durante a rolagem: comportamento não mostrado no modo de visualização.
7. **Abaixo de 375 px** o card do formulário reduz o padding (48 → 32/20).
8. **Topo Mobile**: Contato usa 32 + 64 + 40 e a Política 16 + 64 + 48, como
   cada frame do Figma.

## Para o cliente revisar

- Texto jurídico da política: veio do Figma; validar com o jurídico e
  atualizar a data a cada mudança.
- Mapa: o embed usa a busca "Água Mineral Entre Rios, Estância - SE" em
  satélite. Para fixar o ponto exato, colar o código de "Incorporar um mapa".
