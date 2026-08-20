# Dicionário de Dados — Pixel Store

Para cada campo: tipo lógico conforme uso no PHP/JSON.

---

## 1. usuarios.json — Usuário Administrador

| Entidade | Campo | Tipo | Obrigatório | Identificador | Valor padrão | Descrição | Exemplo |
|----------|-------|------|-------------|---------------|--------------|-----------|---------|
| Usuario | id | inteiro | Sim | PK | auto (`proximoId`) | Identificador único | `2` |
| Usuario | nome | string | Sim | Não | — | Nome completo do admin | `"Zancor"` |
| Usuario | login | string | Sim | UK (lógico) | — | Usuário de acesso | `"Zancor"` |
| Usuario | senha | string | Sim | Não | — | Hash bcrypt (`password_hash`) | `"$2y$10$..."` |
| Usuario | ativo | boolean | Não | Não | `true` | Se false, login bloqueado | `true` |

---

## 2. clientes.json — Cliente

| Entidade | Campo | Tipo | Obrigatório | Identificador | Valor padrão | Descrição | Exemplo |
|----------|-------|------|-------------|---------------|--------------|-----------|---------|
| Cliente | id | inteiro | Sim | PK | auto | Identificador único | `1` |
| Cliente | nome | string | Sim | Não | — | Nome completo | `"Maria Silva"` |
| Cliente | login | string | Sim | UK (lógico) | — | E-mail de login | `"maria@email.com"` |
| Cliente | senha | string | Sim | Não | — | Hash bcrypt | `"$2y$10$..."` |
| Cliente | criado_em | string (ISO 8601) | Sim | Não | `date('c')` | Data de criação | `"2026-08-18T14:30:00-03:00"` |

---

## 3. categorias.json — Categoria

| Entidade | Campo | Tipo | Obrigatório | Identificador | Valor padrão | Descrição | Exemplo |
|----------|-------|------|-------------|---------------|--------------|-----------|---------|
| Categoria | id | inteiro | Sim | PK | auto | Identificador | `1` |
| Categoria | nome | string | Sim | UK (lógico) | — | Nome da categoria | `"Perifericos"` |

---

## 4. produtos.json — Produto

| Entidade | Campo | Tipo | Obrigatório | Identificador | Valor padrão | Descrição | Exemplo |
|----------|-------|------|-------------|---------------|--------------|-----------|---------|
| Produto | id | inteiro | Sim | PK | auto | Identificador | `1` |
| Produto | nome | string | Sim | Não | — | Nome comercial | `"Mouse Gamer Precision Pro"` |
| Produto | preco | float | Sim | Não | — | Preço unitário em BRL | `189.90` |
| Produto | categoria | string | Sim | Não | — | Nome da categoria (cópia) | `"Perifericos"` |
| Produto | imagem | string (URL/path) | Sim | Não | — | URL externa ou path local | `"uploads/produtos/abc.jpg"` |
| Produto | descricao | string | Sim | Não | — | Descrição do produto | `"Mouse gamer ergonomico..."` |
| Produto | estoque | inteiro | Sim | Não | `0` | Quantidade disponível em estoque; nunca negativo, decrementado na finalização da compra | `20` |
| Produto | variacoes | array | Não | Não | `[]` | Lista de variações | ver abaixo |

### 4.1 Objeto Variação (dentro de produto.variacoes[])

| Entidade | Campo | Tipo | Obrigatório | Identificador | Descrição | Exemplo |
|----------|-------|------|-------------|---------------|-----------|---------|
| Variacao | nome | string | Sim* | Não | Nome da opção | `"Tamanho 38"` |
| Variacao | imagem | string | Sim* | Não | Path da imagem | `"uploads/produtos/xyz.jpg"` |

*Obrigatório quando `usar_variacoes` está marcado no cadastro.

---

## 5. fornecedores.json — Fornecedor

| Entidade | Campo | Tipo | Obrigatório | Identificador | Valor padrão | Descrição | Exemplo |
|----------|-------|------|-------------|---------------|--------------|-----------|---------|
| Fornecedor | id | inteiro | Sim | PK | auto | Identificador | `1` |
| Fornecedor | nome_fornecedor | string | Sim | Não | — | Razão/nome | `"Tech Distribuidora"` |
| Fornecedor | cnpj_fornecedor | string | Sim | UK (lógico) | — | CNPJ | `"12.345.678/0001-90"` |
| Fornecedor | telefone_fornecedor | string | Sim | Não | — | Telefone | `"(11) 99999-0000"` |
| Fornecedor | cep_fornecedor | string | Sim | Não | — | CEP | `"01310-100"` |
| Fornecedor | rua_fornecedor | string | Sim | Não | — | Logradouro | `"Av. Paulista"` |
| Fornecedor | numero_fornecedor | string | Sim | Não | — | Número | `"1000"` |
| Fornecedor | bairro_fornecedor | string | Sim | Não | — | Bairro | `"Bela Vista"` |
| Fornecedor | cidade_fornecedor | string | Sim | Não | — | Cidade | `"Sao Paulo"` |
| Fornecedor | ativo | boolean | Não | Não | `true` | Status (sem UI toggle) | `true` |

---

## 6. pedidos.json — Pedido

| Entidade | Campo | Tipo | Obrigatório | Identificador | Valor padrão | Descrição | Exemplo |
|----------|-------|------|-------------|---------------|--------------|-----------|---------|
| Pedido | id | inteiro | Sim | PK | auto | Identificador | `1` |
| Pedido | cliente | objeto | Sim | Não | — | Snapshot do cliente | `{ "nome": "...", "login": "..." }` |
| Pedido | endereco | objeto | Sim | Não | — | Endereço de entrega | ver 6.1 |
| Pedido | itens | array | Sim | Não | — | Itens do pedido | ver 6.2 |
| Pedido | total | float | Sim | Não | calculado | Valor total | `379.80` |
| Pedido | status | string | Sim | Não | `"pendente"` | Status fixo na criação | `"pendente"` |
| Pedido | criado_em | string (ISO 8601) | Sim | Não | `date('c')` | Data/hora | `"2026-08-18T17:00:00-03:00"` |

### 6.1 Objeto endereco

| Campo | Tipo | Obrigatório | Descrição | Exemplo |
|-------|------|-------------|-----------|---------|
| endereco | string | Sim | Endereço completo | `"Rua X, 123"` |
| cidade | string | Sim | Cidade | `"Sao Paulo"` |
| cep | string | Sim | CEP | `"01310-100"` |

### 6.2 Objeto item (dentro de itens[])

| Campo | Tipo | Obrigatório | Descrição | Exemplo |
|-------|------|-------------|-----------|---------|
| produto_id | inteiro | Sim | ID do produto | `1` |
| nome | string | Sim | Nome no momento da compra | `"Mouse Gamer..."` |
| preco_unitario | float | Sim | Preço unitário | `189.90` |
| quantidade | inteiro | Sim | Quantidade | `2` |
| subtotal | float | Sim | preco × quantidade | `379.80` |
| variacao | string/null | Não | Nome da variação ou null | `"Tamanho 38"` |

---

## 7. Estruturas de sessão

### 7.1 carrinho

| Campo | Tipo | Descrição | Exemplo |
|-------|------|-----------|---------|
| chave | string | `"produtoId"` ou `"produtoId:varIdx"` | `"5:0"` |
| valor | inteiro | Quantidade (1–99) | `3` |

### 7.2 cliente (sessão)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| nome | string | Nome do cliente logado |
| login | string | E-mail |

### 7.3 Admin (sessão)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| logado | boolean | Flag de autenticação |
| usuario_nome | string | Nome exibido no topbar |

---

## 8. Uploads — uploads/produtos/

| Campo | Tipo | Descrição | Exemplo |
|-------|------|-----------|---------|
| nome arquivo | string | 24 hex chars + extensão | `a1b2c3d4e5f6.jpg` |
| tipos aceitos | — | JPG, PNG, WEBP, GIF | — |
| path salvo | string | Relativo à raiz | `uploads/produtos/a1b2....jpg` |
