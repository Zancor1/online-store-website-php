# Matriz de Rastreabilidade — Pixel Store

Relacionamento entre Requisitos Funcionais, Casos de Uso, Arquivos e Funcionalidades.

---

| RF | Caso de Uso | Arquivo(s) | Funcionalidade |
|----|-------------|------------|----------------|
| RF01 | CU01 | `index.php` | Página inicial hero |
| RF02 | CU01 | `pages/produtos.php`, `includes/banco_ficticio.php` | Listar catálogo |
| RF03 | CU01 | `pages/produtos.php` | Filtro por categoria |
| RF04 | CU01 | `pages/detalhe.php` | Detalhe do produto |
| RF05 | CU01 | `pages/detalhe.php` | Seleção de variação |
| RF06 | CU04 | `index.php`, `pages/detalhe.php` | Adicionar ao carrinho |
| RF07 | CU04 | `pages/carrinho.php` | Visualizar carrinho |
| RF08 | CU04 | `index.php`, `pages/carrinho.php` | Remover do carrinho |
| RF09 | CU04 | `index.php`, `pages/checkout.php`, `includes/banco_ficticio.php` | Finalizar compra |
| RF10 | CU02 | `index.php`, `pages/login.php` | Registro cliente |
| RF11 | CU03 | `index.php`, `pages/login.php` | Login cliente |
| RF12 | CU03 | `index.php` | Logout cliente |
| RF13 | — | `pages/contato.php` | Página contato |
| RF14 | — | `pages/beneficios.php` | Página benefícios |
| RF15 | CU07 | `admin/login.php` | Login admin |
| RF16 | CU08 | `admin/pages/dashboard.php` | Dashboard |
| RF17 | CU05 | `admin/pages/categorias.php`, `editar_categoria.php` | CRUD categorias |
| RF18 | CU06 | `admin/pages/produtos.php` | Cadastrar/excluir produtos |
| RF19 | CU06 | `admin/pages/produtos.php` | Upload imagem |
| RF20 | — | `admin/pages/usuarios.php`, `editar_usuario.php` | CRUD admins |
| RF21 | — | `admin/pages/fornecedores.php`, `editar_fornecedor.php` | CRUD fornecedores |
| RF22 | — | `admin/pages/documentacao.php`, `documentacao_pdf.php` | Documentação/PDF |
| RF23 | CU07 | `admin/index.php` | Proteção área admin |
| RF24 | CU04 | `index.php` | Proteção checkout |
| RF25 | CU03, CU07 | `includes/seguranca.php` | Regeneração sessão |

---

## Rastreabilidade RF → Entidade de dados

| RF | Entidade JSON | Operação |
|----|---------------|----------|
| RF09 | pedidos.json | CREATE |
| RF10 | clientes.json | CREATE |
| RF11 | clientes.json | READ |
| RF17 | categorias.json | CRUD |
| RF18 | produtos.json | CREATE, DELETE |
| RF19 | uploads/produtos/ | CREATE |
| RF20 | usuarios.json | CRUD |
| RF21 | fornecedores.json | CRUD |

---

## Rastreabilidade RF → RNF

| RF | RNF relacionados |
|----|------------------|
| RF10, RF11, RF15 | RNF07 (hash senhas), RNF15 (sessões) |
| RF06–RF09 | RNF15 (carrinho em sessão) |
| RF19 | RNF12 (upload) |
| RF23, RF24 | RNF10 (controle acesso) |
| RF25 | RNF15 (regeneração sessão) |
| Todos forms POST | RNF09 (CSRF — atendido) |

---

## Rastreabilidade Caso de Uso → Testes

| CU | Testes |
|----|--------|
| CU01 | T01–T04 |
| CU02 | T06–T08 |
| CU03 | T09, T11 |
| CU04 | T05, T10–T12 |
| CU05 | T17–T18 |
| CU06 | T19–T22 |
| CU07 | T14–T16 |
| CU08 | T24 |

---

## Cobertura de arquivos principais

| Arquivo | RF cobertos |
|---------|-------------|
| index.php | RF01, RF06–RF12, RF24 |
| includes/banco_ficticio.php | RF02, RF04, RF09–RF11, RF17–RF21 |
| includes/seguranca.php | RF25 |
| pages/*.php | RF02–RF04, RF07, RF13–RF14 |
| admin/login.php | RF15 |
| admin/index.php | RF23 |
| admin/pages/*.php | RF16–RF22 |

---

## Lacunas identificadas (sem RF)

| Funcionalidade | Motivo |
|----------------|--------|
| Editar produto | Não implementado — sem RF |
| Gestão clientes admin | Não implementado |
| Gestão completa pedidos | Parcial — sem RF dedicado |
| Recuperação senha | Não implementado |
