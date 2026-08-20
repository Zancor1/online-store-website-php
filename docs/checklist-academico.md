# Checklist Acadêmico — Pixel Store

Verificação dos requisitos da atividade contra o sistema real.

---

## Legenda

| Símbolo | Significado |
|---------|-------------|
| 🟢 | ATENDIDO |
| 🟡 | PARCIAL |
| 🔴 | NÃO ATENDIDO |

---

## Checklist principal

| Requisito da atividade | Status | Evidência |
|------------------------|--------|-----------|
| **Cadastro (geral)** | 🟢 | CRUD em categorias, usuarios, fornecedores; create em produtos e clientes |
| **Listagem** | 🟢 | Tabelas admin + catálogo público |
| **Criação** | 🟢 | Forms em admin e registro cliente |
| **Edição** | 🟡 | Categorias, usuarios, fornecedores: SIM. Produtos: parcial (estoque editável, demais campos não). Clientes: NÃO |
| **Exclusão** | 🟢 | POST + CSRF em admin (categorias, produtos, usuarios, fornecedores) |
| **Ativo/inativo** | 🟢 | Campo existe; verificado no login admin; toggle Ativar/Desativar disponível na listagem de usuários |
| **Movimento (compra)** | 🟢 | Fluxo completo: produto → carrinho → checkout → pedidos.json, com baixa de estoque |
| **Controle de estoque** | 🟢 | Baixa automática na compra, bloqueio de carrinho/checkout acima do disponível, nunca negativo, concorrência tratada com `flock()` |
| **Front-end público** | 🟢 | index.php + pages/ |
| **Página inicial** | 🟢 | Hero em index.php (?pg=inicio) |
| **Contato** | 🟢 | pages/contato.php com mailto |
| **Outras páginas** | 🟢 | produtos, detalhe, carrinho, checkout, login, beneficios |
| **Requisitos funcionais** | 🟢 | 25 RFs documentados em requisitos-funcionais.md |
| **Requisitos não funcionais** | 🟢 | 15 RNFs documentados em requisitos-nao-funcionais.md |
| **Diagramas UML** | 🟢 | 9 diagramas Mermaid em diagramas.md |
| **Diagramas de sequência** | 🟢 | Login, cadastro, produto, compra, login admin |
| **Tabelas de entidades** | 🟢 | modelo-dados.md + ER diagram |
| **Dicionário de dados** | 🟢 | dicionario-dados.md com todos os JSON |
| **Documentação explicativa** | 🟢 | Pasta docs/ completa + admin/documentacao.php |
| **Segurança (CSRF)** | 🟢 | Token validado em todos os formulários POST (público e admin) |

---

## Checklist por entidade CRUD

| Entidade | Criar | Listar | Editar | Excluir | Ativo/Inativo | Status geral |
|----------|-------|--------|--------|---------|---------------|--------------|
| Usuários admin | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 | 🟢 |
| Clientes | 🟢 | 🔴 | 🔴 | 🔴 | 🔴 | 🟡 |
| Produtos | 🟢 | 🟢 | 🟡 | 🟢 | 🔴 | 🟢 |
| Categorias | 🟢 | 🟢 | 🟢 | 🟢 | 🔴 | 🟢 |
| Fornecedores | 🟢 | 🟢 | 🟢 | 🟢 | 🟡 | 🟢 |
| Pedidos | 🟢 | 🟡 | 🔴 | 🔴 | 🔴 | 🟡 |

---

## Checklist documentação entregue

| Documento | Arquivo | Status |
|-----------|---------|--------|
| Introdução e objetivos | docs/README.md | 🟢 |
| RF completos | docs/requisitos-funcionais.md | 🟢 |
| RNF completos | docs/requisitos-nao-funcionais.md | 🟢 |
| Casos de uso | docs/casos-de-uso.md | 🟢 |
| Arquitetura | docs/arquitetura.md | 🟢 |
| CRUD e movimento | docs/cadastros-movimento.md | 🟢 |
| Telas | docs/telas-sistema.md | 🟢 |
| Entidades/modelo | docs/modelo-dados.md | 🟢 |
| Dicionário dados | docs/dicionario-dados.md | 🟢 |
| Diagramas | docs/diagramas.md | 🟢 |
| Segurança | docs/seguranca.md | 🟢 |
| Validações | docs/validacoes.md | 🟢 |
| Testes | docs/testes.md | 🟢 |
| Manual usuário | docs/manual-usuario.md | 🟢 |
| Manual admin | docs/manual-administrador.md | 🟢 |
| Matriz rastreabilidade | docs/matriz-rastreabilidade.md | 🟢 |
| Limitações | docs/limitacoes-melhorias.md | 🟢 |
| Checklist | docs/checklist-academico.md | 🟢 |
| Doc in-app + PDF | admin/pages/documentacao.php | 🟢 |

---

## Resumo final para apresentação

### Totalmente atendidos (🟢)
- Front-end público completo
- Movimento de compra simulado, com baixa de estoque real
- CRUD de categorias, usuarios admin (incluindo ativar/desativar), fornecedores
- Cadastro, listagem e controle de estoque de produtos
- Documentação acadêmica estruturada
- Diagramas UML/sequência/atividades
- Dicionário de dados
- Manuais de uso
- Proteção CSRF em todos os formulários que alteram dados

### Parcialmente atendidos (🟡)
- Edição de produtos (só estoque é editável; nome/preço/categoria/imagem não têm tela de edição após o cadastro)
- Gestão de clientes no admin (não existe)
- Gestão de pedidos (só dashboard)
- CRUD clientes (só auto-registro)

### Não atendidos (🔴)
- Pagamento real
- Frete
- Estoque
- MySQL
- Recuperação de senha
- Edição de produto

---

## Conclusão do checklist

O projeto **atende a maioria dos requisitos acadêmicos** para entrega, com destaque para cadastros administrativos, front-end público, movimento de compra e documentação. As lacunas principais (edição de produto, gestão de clientes, pagamento) estão documentadas honestamente como limitações, não como funcionalidades existentes.

**Recomendação para apresentação:** Enfatizar o fluxo de compra completo, os CRUDs funcionais e a arquitetura JSON; mencionar melhorias futuras da seção de limitações.
