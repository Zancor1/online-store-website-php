# Telas do Sistema — Front-end e Admin

## Parte 1 — Front-end público

Todas acessíveis via `index.php?pg={pagina}`.

### Página inicial (`inicio`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php` ou `index.php?pg=inicio` |
| **Conteúdo** | Hero com imagem de fundo, título, subtítulo, botão "Explorar produtos" |
| **Arquivo** | Conteúdo inline em `index.php` (linhas 174–182) |
| **Login** | Não exigido |

### Produtos (`produtos`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php?pg=produtos` |
| **Conteúdo** | Sidebar de categorias + lista de produtos |
| **Filtro** | `&cat=NomeDaCategoria` |
| **Arquivo** | `pages/produtos.php` |

### Detalhe do produto (`detalhe`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php?pg=detalhe&id={id}` |
| **Conteúdo** | Imagem, nome, categoria, descrição, preço, quantidade, botão carrinho |
| **Variações** | Select + JS troca imagem |
| **Arquivo** | `pages/detalhe.php` |

### Carrinho (`carrinho`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php?pg=carrinho` |
| **Conteúdo** | Itens, quantidades, subtotais, total, remover, link checkout |
| **Arquivo** | `pages/carrinho.php` |

### Checkout (`checkout`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php?pg=checkout` |
| **Conteúdo** | Formulário endereço, cidade, CEP |
| **Restrição** | Exige login — redirect se não autenticado |
| **Arquivo** | `pages/checkout.php` |

### Login / Registro (`login`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php?pg=login` ou `&modo=register` |
| **Conteúdo** | Abas Login e Register |
| **Arquivo** | `pages/login.php` |

### Contato (`contato`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php?pg=contato` |
| **Conteúdo** | Texto institucional + mailto `meuovinho06@gmail.com` |
| **Formulário** | Não — apenas link de e-mail |
| **Arquivo** | `pages/contato.php` |

### Benefícios (`beneficios`)

| Item | Detalhe |
|------|---------|
| **URL** | `index.php?pg=beneficios` |
| **Conteúdo** | 3 cards estáticos filtráveis por categoria local |
| **Arquivo** | `pages/beneficios.php` |

### Elementos globais (header/footer)

- Header sticky: logo, menu, carrinho com badge, login/menu conta
- Footer: copyright, e-mail
- Arquivo: `index.php`

---

## Parte 2 — Área administrativa

Acesso: `admin/login.php` → `admin/index.php?pg={pagina}`

### Login admin

| Item | Detalhe |
|------|---------|
| **Arquivo** | `admin/login.php` |
| **Campos** | usuário, senha, checkbox "Remember me" (não funcional) |

### Dashboard

| Item | Detalhe |
|------|---------|
| **Arquivo** | `admin/pages/dashboard.php` |
| **Conteúdo** | 6 cards contadores, atalhos, tabela últimos 5 pedidos |

### Categorias

| Item | Detalhe |
|------|---------|
| **Arquivo** | `admin/pages/categorias.php` |
| **Ações** | Criar, listar, editar, excluir |

### Produtos

| Item | Detalhe |
|------|---------|
| **Arquivo** | `admin/pages/produtos.php` |
| **Ações** | Criar (com upload/variações), listar, excluir |
| **Sem** | Edição |

### Usuários / Equipe

| Item | Detalhe |
|------|---------|
| **Arquivo** | `admin/pages/usuarios.php` |
| **Ações** | Criar, listar, editar, excluir |
| **Status** | Exibe Ativo/Inativo (sem toggle) |

### Fornecedores

| Item | Detalhe |
|------|---------|
| **Arquivo** | `admin/pages/fornecedores.php` |
| **Campos** | nome, CNPJ, telefone, CEP, rua, número, bairro, cidade |
| **Ações** | Criar, listar, editar, excluir |

### Edição (páginas dedicadas)

| Página | Arquivo | Entidade |
|--------|---------|----------|
| Editar categoria | `editar_categoria.php` | Categoria |
| Editar usuário | `editar_usuario.php` | Admin |
| Editar fornecedor | `editar_fornecedor.php` | Fornecedor |

### Documentação

| Item | Detalhe |
|------|---------|
| **Arquivo** | `admin/pages/documentacao.php` |
| **PDF** | `admin/documentacao_pdf.php` (requer sessão admin) |
| **Docs acadêmica** | Pasta `docs/` na raiz do projeto |

---

## Mapa de navegação

```
LOJA                          ADMIN
────                          ─────
Início                        Login
  ├─ Produtos                 Dashboard
  │    └─ Detalhe               ├─ Categorias (+ editar)
  │         └─ Carrinho         ├─ Produtos
  │              └─ Checkout    ├─ Usuários (+ editar)
  ├─ Benefícios                 ├─ Fornecedores (+ editar)
  ├─ Contato                    └─ Documentação (+ PDF)
  └─ Login/Register
```
