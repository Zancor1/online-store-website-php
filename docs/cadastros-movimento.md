# Cadastros (CRUD) e Movimento de Compra

## 1. Matriz CRUD

| Entidade | Criar | Listar | Consultar | Editar | Excluir | Ativar/Desativar |
|----------|-------|--------|-----------|--------|---------|------------------|
| **Usuários (admin)** | SIM | SIM | SIM | SIM | SIM | PARCIAL |
| **Clientes** | SIM | NÃO | PARCIAL | NÃO | NÃO | NÃO |
| **Produtos** | SIM | SIM | SIM | NÃO | SIM | NÃO |
| **Categorias** | SIM | SIM | SIM | SIM | SIM | NÃO |
| **Fornecedores** | SIM | SIM | SIM | SIM | SIM | PARCIAL |
| **Pedidos** | SIM | PARCIAL | NÃO | NÃO | NÃO | NÃO |

### Legenda dos status parciais

**Usuários — Ativar/Desativar PARCIAL:**
- Campo `ativo` existe em `usuarios.json` e é verificado no login (`admin/login.php`)
- Novos usuários nascem com `ativo: true`
- **Não há interface** para alternar ativo/inativo na edição

**Clientes — Consultar PARCIAL:**
- Cliente consulta apenas seus próprios dados via menu (nome/email na sessão)
- Admin **não possui** tela de listagem/edição de clientes
- Dashboard mostra apenas contagem total

**Produtos — Editar NÃO:**
- Apenas criar (form) e excluir (GET `excluir`)
- Não existe `editar_produto.php` funcional

**Fornecedores — Ativar/Desativar PARCIAL:**
- Campo `ativo` gravado e exibido na listagem
- **Não há toggle** na interface de edição

**Pedidos — Listar PARCIAL:**
- Dashboard lista últimos 5 pedidos
- Não há módulo completo de gestão de pedidos

---

## 2. Detalhamento por entidade

### 2.1 Usuários (administradores)

| Operação | Como | Arquivo |
|----------|------|---------|
| Criar | Form POST nome, login, senha | `admin/pages/usuarios.php` |
| Listar | Tabela com status | `admin/pages/usuarios.php` |
| Editar | Form separado | `admin/pages/editar_usuario.php` |
| Excluir | GET `?excluir=id` | `admin/pages/usuarios.php` |

### 2.2 Clientes

| Operação | Como | Arquivo |
|----------|------|---------|
| Criar | Auto-registro na loja | `index.php` + `cadastrarCliente()` |
| Listar | Apenas contagem no dashboard | `admin/pages/dashboard.php` |

Armazenamento: `data/clientes.json`

### 2.3 Produtos

| Operação | Como | Arquivo |
|----------|------|---------|
| Criar | Form com upload e variações | `admin/pages/produtos.php` |
| Listar | Tabela admin + catálogo público | `admin/pages/produtos.php`, `pages/produtos.php` |
| Consultar | Página detalhe | `pages/detalhe.php` |
| Excluir | GET `?excluir=id` | `admin/pages/produtos.php` |

**Relacionamento com categoria:** produto armazena `categoria` como **string** (nome), não ID.

### 2.4 Categorias

| Operação | Arquivo |
|----------|---------|
| CRUD completo | `categorias.php`, `editar_categoria.php` |

### 2.5 Fornecedores

| Operação | Arquivo |
|----------|---------|
| CRUD principal | `fornecedores.php`, `editar_fornecedor.php` |

**Nota:** Fornecedores **não estão vinculados** a produtos no código.

---

## 3. Movimento principal — Compra

### 3.1 Descrição

O movimento de compra é a operação central do sistema do ponto de vista do cliente. Registra intenção de compra sem processar pagamento.

### 3.2 Etapas

| # | Etapa | Implementação | Persistência |
|---|-------|---------------|--------------|
| 1 | **Produto** | Catálogo com filtros | `produtos.json` (leitura) |
| 2 | **Detalhes** | Página com preço, descrição, variação | Leitura por ID |
| 3 | **Quantidade** | Input 1–99 | Form POST |
| 4 | **Carrinho** | Acumula itens | `$_SESSION['carrinho']` |
| 5 | **Total** | Calculado em `carrinho.php` | Runtime |
| 6 | **Checkout** | Form endereço/cidade/CEP | — |
| 7 | **Finalização** | `salvarPedido()` | `pedidos.json` |

### 3.3 Estrutura do pedido salvo

```json
{
  "id": 1,
  "cliente": { "nome": "...", "login": "email@..." },
  "endereco": { "endereco": "...", "cidade": "...", "cep": "..." },
  "itens": [
    {
      "produto_id": 1,
      "nome": "...",
      "preco_unitario": 189.90,
      "quantidade": 2,
      "subtotal": 379.80,
      "variacao": "Tamanho 38"
    }
  ],
  "total": 379.80,
  "status": "pendente",
  "criado_em": "2026-08-18T17:00:00+00:00"
}
```

### 3.4 Chave do carrinho

- Produto simples: `"1"` (ID como string)
- Com variação: `"1:0"` (ID + índice da variação)

Função `montarItensPedido()` em `banco_ficticio.php` interpreta essa chave.

### 3.5 Limitações do movimento

| Funcionalidade | Status |
|----------------|--------|
| Pagamento (Pix, cartão) | Não implementado |
| Cálculo de frete | Não implementado |
| Controle de estoque | Não implementado |
| Confirmação por e-mail | Não implementado |
| Rastreamento de pedido (cliente) | Não implementado |
| Gestão completa de pedidos (admin) | Parcial (só dashboard) |
