# Casos de Uso e Fluxos — Pixel Store

## Diagrama de casos de uso (resumo)

Ver diagrama completo em [diagramas.md](diagramas.md#1-diagrama-de-casos-de-uso).

---

## Atores

| Ator | Casos de uso principais |
|------|------------------------|
| Visitante | Navegar catálogo, ver detalhes, contato, benefícios |
| Cliente | Todos do visitante + registrar, login, carrinho, checkout |
| Administrador | Login admin, CRUD entidades, dashboard, documentação |

---

## CU01 — Navegar catálogo de produtos

| Campo | Valor |
|-------|-------|
| **ID** | CU01 |
| **Nome** | Navegar catálogo |
| **Ator** | Visitante, Cliente |
| **RF relacionados** | RF02, RF03 |
| **Pré-condição** | Servidor ativo |
| **Fluxo principal** | Acessar produtos → opcionalmente filtrar categoria → visualizar lista |
| **Pós-condição** | Produtos exibidos |

---

## CU02 — Registrar conta de cliente

| Campo | Valor |
|-------|-------|
| **ID** | CU02 |
| **Nome** | Registrar cliente |
| **Ator** | Visitante |
| **RF** | RF10 |
| **Pré-condição** | E-mail não cadastrado |
| **Fluxo principal** | Login page → aba Register → preencher → submit → conta criada → logado |
| **Fluxo alternativo A1** | E-mail existente → mensagem erro |
| **Fluxo alternativo A2** | Senha < 6 chars → erro |

---

## CU03 — Autenticar cliente

| Campo | Valor |
|-------|-------|
| **ID** | CU03 |
| **Nome** | Login cliente |
| **Ator** | Cliente |
| **RF** | RF11 |
| **Fluxo principal** | Email + senha → verificação → sessão → redirect produtos |

---

## CU04 — Realizar compra (movimento principal)

| Campo | Valor |
|-------|-------|
| **ID** | CU04 |
| **Nome** | Realizar compra |
| **Ator** | Cliente |
| **RF** | RF04–RF09 |
| **Pré-condição** | Cliente logado |

### Fluxo detalhado

```
1. [Produtos] Cliente navega catálogo (pages/produtos.php)
       ↓
2. [Detalhe] Seleciona produto, quantidade e variação (se houver)
       ↓
3. [Adicionar] POST acao=adicionar → sessão carrinho
       ↓
4. [Carrinho] Revisa itens, subtotais e total
       ↓
5. [Checkout] Informa endereço, cidade, CEP
       ↓
6. [Finalizar] POST acao=finalizar
       ↓
7. [Persistência] salvarPedido() → pedidos.json
       ↓
8. [Conclusão] Carrinho limpo; mensagem "Pedido finalizado"
```

**O que NÃO ocorre neste fluxo:**
- Cálculo de frete
- Escolha de pagamento
- Baixa de estoque
- E-mail de confirmação

---

## CU05 — Gerenciar categorias

| Campo | Valor |
|-------|-------|
| **ID** | CU05 |
| **Ator** | Administrador |
| **RF** | RF17 |
| **Fluxo** | Admin → Categorias → criar/editar/excluir |

---

## CU06 — Cadastrar produto com variações

| Campo | Valor |
|-------|-------|
| **ID** | CU06 |
| **Ator** | Administrador |
| **RF** | RF18, RF19 |
| **Fluxo** | Produtos → preencher dados → marcar variações → adicionar linhas nome+imagem → cadastrar |

---

## CU07 — Autenticar administrador

| Campo | Valor |
|-------|-------|
| **ID** | CU07 |
| **Ator** | Administrador |
| **RF** | RF15 |
| **Fluxo alternativo** | Conta inativa → acesso negado |

---

## CU08 — Consultar dashboard

| Campo | Valor |
|-------|-------|
| **ID** | CU08 |
| **Ator** | Administrador |
| **RF** | RF16 |
| **Fluxo** | Login → Dashboard → visualiza totais e últimos pedidos |

---

## Fluxos do sistema

### Fluxo de roteamento público

1. `index.php` lê `$_GET['pg']` (default: `inicio`)
2. Se POST, processa ações (login, carrinho, checkout)
3. Se `pg !== inicio`, inclui `pages/{pg}.php` se existir

### Fluxo de roteamento admin

1. Verifica `$_SESSION['logado']`
2. Lê `$_GET['pg']` (default: `dashboard`)
3. Inclui `admin/pages/{pg}.php`

### Fluxo de dados

```
Formulário → PHP (validação) → banco_ficticio.php → JSON → resposta/redirect
```
