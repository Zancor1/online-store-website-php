# Limitações e Melhorias Futuras — Pixel Store

---

## Parte 1 — Limitações reais (o que NÃO existe)

### Persistência e infraestrutura

| Limitação | Evidência |
|-----------|-----------|
| Sem banco MySQL/SQL | Apenas JSON em `data/` |
| Sem API REST | Comunicação server-side direta |
| Sem cache | Leitura completa JSON a cada request |
| Concorrência | `LOCK_EX` básico; risco em alta concorrência |

### Comércio eletrônico

| Limitação | Evidência |
|-----------|-----------|
| Sem pagamento | Checkout salva pedido "pendente" sem cobrança |
| Sem frete | Endereço coletado mas frete não calculado |
| Sem estoque | Quantidade ilimitada; sem baixa |
| Sem cupom/desconto | Não encontrado no código |
| Sem rastreamento pedido (cliente) | Cliente não vê histórico |

### CRUD incompleto

| Limitação | Evidência |
|-----------|-----------|
| Produtos sem edição | Não existe `editar_produto.php` |
| Clientes sem gestão admin | Apenas contagem no dashboard |
| Pedidos sem gestão completa | Só últimos 5 no dashboard |
| Ativo/inativo sem UI | Campo existe, toggle ausente |

### Segurança

| Limitação | Evidência |
|-----------|-----------|
| CSRF não aplicado | Funções em `seguranca.php` não usadas |
| Exclusão via GET | Links diretos em admin |
| Sem recuperação de senha | Não implementado |
| Sem rate limiting login | Tentativas ilimitadas |

### Relacionamentos

| Limitação | Evidência |
|-----------|-----------|
| Fornecedor ≠ Produto | Sem FK ou campo fornecedor_id |
| Categoria por string | Produto guarda nome, não ID |
| Categoria excluída deixa produtos órfãos | Sem integridade referencial |

### Arquivos problemáticos

| Arquivo | Problema |
|---------|----------|
| `editar_categorias.php` | Cópia incorreta de editar usuário |
| `editar_fornecedores.php` | Chama funções inexistentes |
| `fornecedores_excluir.php` | Chama `excluirFornecedor()` inexistente |
| `usuarios.php` | Bug na verificação de login duplicado |
| `fornecedores.php` | Bug `empty('cep')` na validação |

### UX

| Limitação | Evidência |
|-----------|-----------|
| Checkout sem feedback de erro | Redirect silencioso |
| Contato sem formulário | Apenas mailto |
| Remember me admin | Checkbox sem função |
| Botão "Conta" no menu cliente | Sem página vinculada |

---

## Parte 2 — Melhorias futuras (sugestões, NÃO implementadas)

> Estas são recomendações para evolução do projeto, separadas das funcionalidades atuais.

### Prioridade alta

1. **Implementar CSRF** em todos os formulários POST
2. **Converter exclusões para POST** com confirmação
3. **Criar tela editar produto** com re-upload opcional de imagem
4. **Corrigir bugs** em usuarios.php e fornecedores.php
5. **Remover ou consertar** arquivos órfãos (editar_fornecedores.php, etc.)

### Prioridade média

6. Migrar persistência para **MySQL** (já previsto em comentário de `banco_ficticio.php`)
7. Módulo admin de **gestão de pedidos** (listar, alterar status)
8. Módulo admin de **clientes** (listar, desativar)
9. **Vincular produto a fornecedor** via ID
10. **Vincular produto a categoria** via ID (não string)
11. UI para **toggle ativo/inativo** em usuários e fornecedores
12. **Recuperação de senha** por e-mail

### Prioridade baixa / expansão

13. Integração com **gateway de pagamento** (Pix, cartão)
14. **Cálculo de frete** por CEP
15. **Controle de estoque**
16. Notificações por **e-mail** (pedido confirmado)
17. **API REST** para mobile
18. Testes automatizados (PHPUnit)
19. Paginação no catálogo
20. Busca por texto no catálogo

---

## Parte 3 — Funcionalidades parcialmente implementadas

| Funcionalidade | O que existe | O que falta |
|----------------|--------------|-------------|
| Ativo/inativo | Campo JSON + exibição + check no login | UI para alternar |
| Listagem pedidos | Últimos 5 no dashboard | Módulo completo |
| CSRF | Funções prontas | Integração nos forms |
| Documentação | Manual in-app + PDF + docs/ | Sincronizar PDF com docs acadêmica |
| Variações produto | Cadastro e compra | Edição de variações em produto existente |
