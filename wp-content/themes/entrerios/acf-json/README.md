# acf-json

Sincronia local do Advanced Custom Fields.

`inc/acf.php` aponta os *save* e *load points* do ACF para esta pasta. Isso
significa que:

- todo grupo de campos criado ou editado na UI do ACF vira um arquivo `.json`
  aqui, que **entra no commit**;
- em outra máquina (ou em outro ambiente), o ACF mostra os grupos como
  "disponíveis para sincronizar" em **Custom Fields → Field Groups → Sync**;
- clonar o repositório passa a ser suficiente para ter a modelagem — não é mais
  preciso restaurar dump de banco.

## O que NÃO vem para cá

Post types e taxonomias são registrados em PHP, em `inc/post-types.php`. Foi
uma escolha: estrutura é código, conteúdo editável é dado. Se preferir criá-los
pela UI do ACF, os JSON também cairão nesta pasta — mas então mantenha uma
única fonte de verdade e remova o registro em PHP, para não haver conflito.

Grupos que são boilerplate (Opções do Site, Hero) estão em código em
`inc/acf.php`, via `acf_add_local_field_group()`. Eles não aparecem aqui e não
podem ser editados pela UI — de propósito.
