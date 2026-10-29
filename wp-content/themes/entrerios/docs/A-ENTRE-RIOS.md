# Página A Entre Rios

Figma: `01_Screens/Desktop` → **Screen/About/Desktop** (node 176-2, 1600) e
`03_Screens/Mobile` → **Screen/About/Mobile** (390). Lido em 21/10/2026 em
modo leitura: tamanhos de texto (text styles), paddings, gaps, raios, cores e
posições da colagem. A estrutura e as animações seguem a tela estática e o
vídeo do protótipo enviados pelo cliente.

## Arquivos

| Papel | Arquivo |
|---|---|
| Template (Template Name: A Entre Rios) | `page-a-entre-rios.php` |
| Seções | `template-parts/a-entre-rios/{hero,origem,caminhos,fabrica,galeria,pessoas,futuro,assinatura}.php` |
| Parágrafos + intertítulos (repetidor "Texto") | `template-parts/a-entre-rios/blocos.php` |
| Estilos (só nesta página) | `assets/css/a-entre-rios.css` |
| Vídeo e galeria (~2 KB, `defer`) | `assets/js/a-entre-rios.js` |
| Campos | `mu-plugins/entrerios-bootstrap/acf/group_er_a_entre_rios.json` |

`.botao-play` (Play Button) e `.seta-vitrine` (Arrow Button) saíram de
`home.css` para `assets/css/style.css`, porque agora a Home e esta página usam.
Novo helper `aw_paragrafos()` (textarea → `<p>`, linha em branco separa) e
tamanho `er-galeria` (2200×1240).

## Campos (grupo "A Entre Rios — Página", `group_er_a_entre_rios`)

Só no template `page-a-entre-rios.php`; o editor de blocos fica escondido.
Uma aba por seção, todos com prefixo `ae_`. Cada seção some quando o título
dela está vazio (a galeria também sem fotos). Sem texto de reserva no PHP.

- **Topo**: foto, sobretítulo, título (H1).
- **Origem**: título, subtítulo, marcos (anos), texto, frase com barra, texto final.
- **Novos caminhos**: título, subtítulo, Texto (repetidor parágrafo/intertítulo),
  frase final no bloco em degradê e as 5 posições da colagem (centro,
  esquerda, direita, esquerda na frente, direita na frente).
- **Fábrica**: vídeo completo, prévia em loop, capa, título, subtítulo, Texto,
  citação e complemento.
- **Galeria**: sobretítulo, título, fotos (galeria do ACF).
- **Pessoas**: título, subtítulo, texto, frase do card.
- **Futuro**: título, subtítulo, texto, manifesto (frase + "azul-claro").
- **Assinatura**: palavra grande e frase (uma linha por verso).

O bootstrap v5 liga o template na página `a-entre-rios` e grava os textos do
Figma e a mídia (só em campo vazio).

## Mídia (bootstrap v5)

| Arquivo | Origem |
|---|---|
| `sobre-fabrica-drone-01/02.webp` (2400×1350) | `Fotos recentes da Fábrica/dji_fly_…` — topo e galeria |
| `sobre-colagem-*.webp` | PNGs dos produtos, recortados no alfa (garrafa 1200 px de altura; DUB e Q'tal 1000; lata 600; copo 560) |
| `sobre-fabrica-video-capa.webp` | Quadro de 1,5 s do vídeo, no recorte 530×746 |
| `sobre-video-previa.mp4` (770 KB) | 14 s iniciais do `sobre-video.mp4`, 540 px, sem som |
| Vídeo completo | o mesmo `sobre-video.mp4` da Home |

## Comportamento

- **Colagem**: cada produto flutua (sobe ~1,4 % da própria altura, 5–7 s,
  alternado), com atrasos diferentes. Só `translate`, sem JS.
- **Vídeo**: perto da tela, a prévia (muda, em loop) é baixada e toca por
  baixo do Play; fora da tela pausa. O vídeo completo só é baixado no clique.
  Sem prévia com `prefers-reduced-motion` ou economia de dados.
- **Galeria**: rolagem nativa com scroll-snap; setas avançam um slide,
  contador e pontos acompanham. Sem JS, só a rolagem (controles escondidos).

## Fiel ao Figma × adaptação

Fiel: text styles, paddings/gaps Desktop e Mobile, raios, cores dos fundos e
destaques, posições e rotações da colagem, card do vídeo, citação, galeria.

Adaptações:

1. **Tablet** (768–1279): a página não tem frame Tablet. Seções empilhadas,
   paddings intermediários (96), card do vídeo com até 460 px, topo 680.
2. **Degradê do topo no Tablet/Mobile**: o lado direito fica mais escuro
   (45 %) porque o texto ocupa a largura toda.
3. **Círculo azul pequeno da colagem**: Figma usa `illustration/cyan-400`, não
   transcrito; usado `sky-500 → energeticos/700`.
4. **Escurecimento do card do vídeo**: o Figma tem um degradê 0 → 85 % cuja cor
   não aparece em modo leitura; usado navy-900 até 45 % na base.
5. **Aspas da citação**: redesenhadas em SVG (34×26, sky-300).
6. **Galeria Mobile**: 16 px entre título e setas (Figma: 0).
7. **Seta "anterior" esmaecida** no primeiro slide, como na vitrine da Home.
8. **"ENTRE RIOS" da assinatura**: no Figma (1368 px) passa da área útil; aqui
   encolhe para nunca sair da tela.
9. **Novos caminhos**: em 1600 a colagem avança 46 px sobre a margem direita,
   como no Figma (630 + 96 + 600 = 1326).

## Para o cliente revisar

- "Para o futuro" sem ponto final e "As pessoas." com maiúscula (como no Figma).
- O Figma cita o vídeo "WhatsApp Video 2026-10-09 at 11.54.56.mp4", que não
  está na pasta de conteúdo; usamos o vídeo da fábrica da Home.
