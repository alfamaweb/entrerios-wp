# Regras do Projeto — Tema Entre Rios (AlfamaWeb v3)

> Carregado automaticamente pelo Antigravity em toda sessão de trabalho neste workspace.
> Gerado em 2026-10-23 a partir de `page-home.php` e `page-a-entre-rios.php`.

---

## Contexto do Projeto

- **Tema WordPress** custom, pacote `AlfamaWeb`, versão `3.0.0`.
- **PHP** com Advanced Custom Fields PRO. Todos os campos são lidos via `aw_field()` (nunca `get_field()` diretamente).
- **ACF JSON** versionado em `acf-json/`. Todo novo grupo de campos deve ter um arquivo `.json` correspondente nessa pasta.
- **Template-parts** organizam as seções de cada página em pastas próprias: `template-parts/{slug-da-pagina}/`.
- **Nenhuma lógica** vai no `page-*.php` — apenas os `get_template_part()` das seções.

---

## Convenções obrigatórias

### Nomenclatura de variáveis PHP
- Todas as variáveis locais de template-part levam prefixo `$aw_`: `$aw_titulo`, `$aw_imagem`, `$aw_botao`.
- Nunca usar `$data`, `$item`, `$value` sem contexto.

### Guard de saída em seções
- Todo template-part começa lendo o campo de "gatilho" (geralmente `_titulo`).
- Se vazio → `return;` imediatamente. A seção desaparece sozinha do HTML.

### Prefixo de campos ACF
- Formato: `{slug-pagina}_{secao}_{campo}`. Ex.: `ae_hero_titulo`, `vitrine_rotulo`.
- Nunca reutilizar o mesmo `name` de campo entre páginas distintas.

### Escape de output
- Textos: `esc_html()` · Atributos: `esc_attr()` · URLs: `esc_url()`
- Parágrafos de textarea: `aw_paragrafos($texto, 'classe-css')`
- Texto com quebras: `nl2br(esc_html($v), false)`

### HTML e Semântica
- Seções de conteúdo: `<section>` com `aria-labelledby` apontando para o `<h2>` interno.
- Hierarquia: H1 (hero) → H2 (seção) → H3 (subtítulo). Nunca pular nível.
- BEM: `.{bloco}__{elemento}--{modificador}`, estados JS com `.is-{estado}`.
- Imagens LCP (hero): `loading="eager"`, `fetchpriority="high"`.
- Todas as outras imagens: `loading="lazy"`, `decoding="async"`.
- SVG inline: usar `aw_svg('arquivo.svg', ['class' => '...'])` — nunca copiar SVG diretamente no template.

### Botões e links
- Primário: `botao-primario` · Outline: `botao-claro` · Texto: `link-texto` · Play: `botao-play`
- Link ACF (`type: link`, `return_format: array`): verificar `['url']` E `['title']` antes de renderizar.
- `target="_blank"` sempre acompanhado de `rel="noopener"`.

---

## Estrutura de JSON ACF

- Chave do grupo: `group_er_{slug-pagina}`
- Chave de campo: `field_er_{slug-pagina}_{nome-do-campo}`
- `location`: `page_template == page-{slug}.php`
- Uma aba ACF (`type: tab`) por seção da página
- `instructions` em **português** para todo campo
- Imagens: `return_format: id`, `mime_types: jpg,jpeg,png,webp`
- Links: `return_format: array`

---

## Guia completo

O guia detalhado com templates de código, checklist e tabelas de referência está em:

`brain/110922fa-294f-4e19-b513-fc87fbc98d59/entrerios-dev-guide.md`

Ler esse arquivo ao iniciar qualquer tarefa de criação de nova página ou grupo ACF neste projeto.

