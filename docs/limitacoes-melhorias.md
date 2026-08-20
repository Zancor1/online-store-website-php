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
| Controle de estoque | **Implementado** (baixa automática, bloqueio acima do disponível, nunca negativo, `flock()` para concorrência) |
| Sem cupom/desconto | Não encontrado no código |
| Sem rastreamento pedido (cliente) | Cliente não vê histórico |

### CRUD incompleto

| Limitação | Evidência |
|-----------|-----------|
| Produtos sem edição completa | Não existe `editar_produto.php`; apenas o estoque é editável na listagem |
| Clientes sem gestão admin | Apenas contagem no dashboard |
| Pedidos sem gestão completa | Só últimos 5 no dashboard |
| Ativo/inativo de usuários | **Implementado** (toggle na listagem, com proteção contra autodesativação) |

### Segurança

| Limitação | Evidência |
|-----------|-----------|
| CSRF | **Implementado**: `csrfCampo()`/`csrfValidar()` usados em todos os formulários POST |
| Exclusão via GET | **Corrigido**: todas as exclusões agora usam POST + CSRF |
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

1. ~~**Implementar CSRF** em todos os formulários POST~~ — feito
2. ~~**Converter exclusões para POST** com confirmação~~ — feito
3. **Criar tela editar produto** completa (nome, preço, categoria, imagem), hoje só o estoque é editável
4. **Corrigir bugs** em usuarios.php e fornecedores.php — feito (usuarios.php); fornecedores.php revisado (CNPJ duplicado)
5. **Remover ou consertar** arquivos órfãos (`editar_categorias.php`, `editar_fornecedores.php`, `fornecedores_excluir.php`) — não são referenciados por nenhum link do sistema e continuam inertes; mantidos por não serem necessários à correção solicitada, mas recomenda-se removê-los ou consertá-los depois

### Prioridade média

6. Migrar persistência para **MySQL** (já previsto em comentário de `banco_ficticio.php`)
7. Módulo admin de **gestão de pedidos** (listar, alterar status)
8. Módulo admin de **clientes** (listar, desativar)
9. **Vincular produto a fornecedor** via ID
10. **Vincular produto a categoria** via ID (não string)
11. ~~UI para **toggle ativo/inativo** em usuários~~ — feito para usuários; fornecedores ainda sem toggle
12. **Recuperação de senha** por e-mail

### Prioridade baixa / expansão

13. Integração com **gateway de pagamento** (Pix, cartão)
14. **Cálculo de frete** por CEP
15. ~~**Controle de estoque**~~ — feito
16. Notificações por **e-mail** (pedido confirmado)
17. **API REST** para mobile
18. Testes automatizados (PHPUnit)
19. Paginação no catálogo
20. Busca por texto no catálogo

---

## Parte 3 — Funcionalidades parcialmente implementadas

| Funcionalidade | O que existe | O que falta |
|----------------|--------------|-------------|
| Ativo/inativo (usuários) | Campo JSON + exibição + check no login + toggle na listagem | Nada — completo |
| Ativo/inativo (fornecedores) | Campo JSON + exibição | UI para alternar |
| Listagem pedidos | Últimos 5 no dashboard | Módulo completo |
| CSRF | Funções prontas e integradas em todos os forms | Nada — completo |
| Documentação | Manual in-app + PDF + docs/ | Sincronizar PDF com docs acadêmica |
| Variações produto | Cadastro e compra | Edição de variações em produto existente |
| Controle de estoque | Baixa automática, bloqueio de carrinho/checkout, ajuste manual pelo admin | Histórico/log de movimentações de estoque |
