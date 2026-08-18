# Casos de Teste — Pixel Store

Status baseado na análise estática do código. "Observado" indica comportamento esperado conforme implementação.

---

## Testes — Área pública

### T01 — Acessar página inicial

| Campo | Valor |
|-------|-------|
| **ID** | T01 |
| **Funcionalidade** | Página inicial |
| **Pré-condição** | Servidor PHP rodando |
| **Entrada** | GET `/index.php` |
| **Passos** | 1. Abrir URL |
| **Resultado esperado** | Hero banner, menu, footer |
| **Status** | Pass |

### T02 — Listar produtos

| Campo | Valor |
|-------|-------|
| **ID** | T02 |
| **Funcionalidade** | Catálogo |
| **Pré-condição** | produtos.json populado |
| **Entrada** | GET `?pg=produtos` |
| **Passos** | 1. Clicar "Ver Produtos" |
| **Resultado esperado** | Lista de 30 produtos seed |
| **Status** | Pass |

### T03 — Filtrar por categoria

| Campo | Valor |
|-------|-------|
| **ID** | T03 |
| **Entrada** | GET `?pg=produtos&cat=Audio` |
| **Resultado esperado** | Apenas produtos categoria Audio |
| **Status** | Pass |

### T04 — Produto inexistente

| Campo | Valor |
|-------|-------|
| **ID** | T04 |
| **Entrada** | GET `?pg=detalhe&id=9999` |
| **Resultado esperado** | "Produto não encontrado" |
| **Status** | Pass |

### T05 — Adicionar carrinho sem login

| Campo | Valor |
|-------|-------|
| **ID** | T05 |
| **Entrada** | POST adicionar sem sessão cliente |
| **Resultado esperado** | Redirect login + mensagem |
| **Status** | Pass |

### T06 — Registro cliente válido

| Campo | Valor |
|-------|-------|
| **ID** | T06 |
| **Entrada** | nome, email válido, senha 6+ chars |
| **Resultado esperado** | Conta criada, logado, redirect produtos |
| **Status** | Pass |

### T07 — Registro email duplicado

| Campo | Valor |
|-------|-------|
| **ID** | T07 |
| **Entrada** | Email já existente |
| **Resultado esperado** | Erro "Este email ja possui uma conta." |
| **Status** | Pass |

### T08 — Registro senha curta

| Campo | Valor |
|-------|-------|
| **ID** | T08 |
| **Entrada** | Senha com 5 caracteres |
| **Resultado esperado** | Erro de validação |
| **Status** | Pass |

### T09 — Login credenciais inválidas

| Campo | Valor |
|-------|-------|
| **ID** | T09 |
| **Entrada** | Email/senha errados |
| **Resultado esperado** | "Email ou senha invalidos." |
| **Status** | Pass |

### T10 — Fluxo compra completo

| Campo | Valor |
|-------|-------|
| **ID** | T10 |
| **Passos** | Login → detalhe → add carrinho → checkout → confirmar |
| **Resultado esperado** | Pedido em pedidos.json, carrinho vazio, msg sucesso |
| **Status** | Pass |

### T11 — Checkout sem login

| Campo | Valor |
|-------|-------|
| **ID** | T11 |
| **Entrada** | GET `?pg=checkout` sem sessão |
| **Resultado esperado** | Redirect login |
| **Status** | Pass |

### T12 — Checkout campos vazios

| Campo | Valor |
|-------|-------|
| **ID** | T12 |
| **Entrada** | POST finalizar com campos vazios |
| **Resultado esperado** | Redirect checkout sem mensagem explícita |
| **Status** | Pass (UX parcial — sem feedback) |

### T13 — Página contato

| Campo | Valor |
|-------|-------|
| **ID** | T13 |
| **Entrada** | GET `?pg=contato` |
| **Resultado esperado** | Link mailto meuovinho06@gmail.com |
| **Status** | Pass |

---

## Testes — Área administrativa

### T14 — Admin sem login

| Campo | Valor |
|-------|-------|
| **ID** | T14 |
| **Entrada** | GET `/admin/index.php` |
| **Resultado esperado** | Redirect login.php |
| **Status** | Pass |

### T15 — Login admin válido

| Campo | Valor |
|-------|-------|
| **ID** | T15 |
| **Entrada** | Credenciais de usuarios.json |
| **Resultado esperado** | Dashboard |
| **Status** | Pass |

### T16 — Login admin inativo

| Campo | Valor |
|-------|-------|
| **ID** | T16 |
| **Pré-condição** | Usuario com ativo=false |
| **Resultado esperado** | "Esta conta foi desativada" |
| **Status** | Pass (se ativo alterado manualmente no JSON) |

### T17 — Criar categoria

| Campo | Valor |
|-------|-------|
| **ID** | T17 |
| **Entrada** | Nome único |
| **Resultado esperado** | Categoria em categorias.json |
| **Status** | Pass |

### T18 — Categoria duplicada

| Campo | Valor |
|-------|-------|
| **ID** | T18 |
| **Resultado esperado** | Erro "já está cadastrada" |
| **Status** | Pass |

### T19 — Criar produto sem categoria

| Campo | Valor |
|-------|-------|
| **ID** | T19 |
| **Pré-condição** | categorias.json vazio |
| **Resultado esperado** | Botão disabled + aviso |
| **Status** | Pass |

### T20 — Upload imagem inválida

| Campo | Valor |
|-------|-------|
| **ID** | T20 |
| **Entrada** | Arquivo .pdf |
| **Resultado esperado** | Erro MIME |
| **Status** | Pass |

### T21 — Excluir produto

| Campo | Valor |
|-------|-------|
| **ID** | T21 |
| **Entrada** | GET excluir=id |
| **Resultado esperado** | Removido de produtos.json |
| **Status** | Pass |

### T22 — Editar produto

| Campo | Valor |
|-------|-------|
| **ID** | T22 |
| **Resultado esperado** | Funcionalidade disponível |
| **Status** | **Fail** — não implementado |

### T23 — PDF documentação sem login

| Campo | Valor |
|-------|-------|
| **ID** | T23 |
| **Entrada** | GET documentacao_pdf.php |
| **Resultado esperado** | HTTP 403 |
| **Status** | Pass |

### T24 — Dashboard contadores

| Campo | Valor |
|-------|-------|
| **ID** | T24 |
| **Resultado esperado** | Contagens corretas dos JSON |
| **Status** | Pass |

---

## Testes — Segurança

### T25 — CSRF em exclusão

| Campo | Valor |
|-------|-------|
| **ID** | T25 |
| **Entrada** | Link externo com excluir=id |
| **Resultado esperado** | Deveria bloquear |
| **Status** | **Fail** — exclusão ocorre via GET |

### T26 — Acesso direto data/

| Campo | Valor |
|-------|-------|
| **ID** | T26 |
| **Entrada** | GET /data/usuarios.json |
| **Resultado esperado** | 403 (Apache) |
| **Status** | Pass (Apache + .htaccess) |

---

## Resumo

| Status | Quantidade |
|--------|------------|
| Pass | 24 |
| Fail | 2 |
| Parcial | 1 |
