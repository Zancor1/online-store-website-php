# Manual do Administrador — Pixel Store

Guia para gestão do painel administrativo.

---

## 1. Acesso ao painel

1. Acesse `http://localhost/admin/login.php`.
2. Informe **usuário** e **senha** de administrador.
3. Clique em **Log in**.
4. Se autenticado, será redirecionado ao Dashboard.

> Contas com `ativo: false` em `usuarios.json` são bloqueadas.

Para sair: clique em **Sair** no canto superior direito.

---

## 2. Dashboard

Após login, você verá:

- **Contadores:** produtos, categorias, fornecedores, administradores, clientes, pedidos
- **Acesso rápido:** atalhos para módulos
- **Últimos pedidos:** tabela com os 5 pedidos mais recentes

---

## 3. Gerenciar categorias

**Caminho:** Menu → Categorias

### Criar
1. Digite o nome da categoria.
2. Clique em **Criar Categoria**.

### Editar
1. Na tabela, clique **Editar** na linha desejada.
2. Altere o nome.
3. Clique **Atualizar Categoria**.

### Excluir
1. Clique **Remover** na linha.
2. Confirme implicitamente (não há modal).

> **Atenção:** Excluir categoria não remove produtos que já usam esse nome.

---

## 4. Gerenciar produtos

**Caminho:** Menu → Produtos

### Pré-requisito
Cadastre ao menos uma categoria antes.

### Criar produto simples
1. Preencha nome, preço, categoria, descrição.
2. Envie uma imagem (JPG, PNG, WEBP ou GIF).
3. Clique **Cadastrar Produto**.

### Criar produto com variações
1. Marque **Este produto possui variações**.
2. Clique **+ Adicionar variação** para cada opção.
3. Informe nome (ex.: "Tamanho 38") e imagem específica.
4. Cadastre.

### Listar
Tabela exibe ID, nome, categoria, quantidade de variações e preço.

### Excluir
Clique **Remover** na linha do produto.

### Editar
**Não disponível** no sistema atual. Para alterar, exclua e recadastre.

---

## 5. Gerenciar equipe (usuários admin)

**Caminho:** Menu → Equipe / Usuários

### Criar
1. Nome completo, usuário de login, senha.
2. **Cadastrar Usuário**.

### Editar
1. Clique **Editar**.
2. Altere nome/login; senha opcional (deixe em branco para manter).
3. **Atualizar Cadastro**.

### Excluir
Clique **Remover**.

### Ativar/Desativar
Campo `ativo` existe no JSON e é verificado no login, mas **não há botão na interface** para alternar. Alteração manual no JSON se necessário.

---

## 6. Gerenciar fornecedores

**Caminho:** Menu → Fornecedores

### Criar
Preencha: nome, CNPJ, telefone, CEP, rua, número, bairro, cidade.  
Clique **Cadastrar Fornecedor**.

### Editar
1. Clique **Editar**.
2. Altere os campos.
3. **Atualizar Fornecedor**.

### Excluir
Clique **Remover**.

> Fornecedores **não são vinculados** a produtos no sistema.

---

## 7. Documentação

**Caminho:** Menu → Documentacao

- Manual integrado na tela
- Botão **Baixar em PDF** gera `documentacao-pixel-store.pdf`
- Documentação acadêmica completa na pasta `docs/` do projeto

---

## 8. Uploads de imagens

- Imagens salvas em `uploads/produtos/`
- Nome gerado automaticamente (hash aleatório)
- Formatos: JPG, PNG, WEBP, GIF
- Servidor precisa permissão de escrita na pasta

---

## 9. Ordem recomendada de cadastros

1. **Categorias** (obrigatório antes de produtos)
2. **Fornecedores** (opcional, sem vínculo)
3. **Produtos**
4. **Administradores** (conforme necessidade)

---

## 10. Visualizar a loja

No Dashboard, clique **Abrir loja** para abrir a loja pública em nova aba.

---

## 11. O que o admin NÃO pode fazer (limitações reais)

| Ação | Status |
|------|--------|
| Listar/editar clientes | Não implementado |
| Gerenciar todos os pedidos | Apenas últimos 5 no dashboard |
| Editar produtos | Não implementado |
| Alterar status de pedido | Não implementado |
| Toggle ativo/inativo na UI | Não implementado |
| Relatórios/exportação | Não implementado |

---

## Credencial padrão (seed)

Conforme `data/usuarios.json`:

| Campo | Valor |
|-------|-------|
| Login | Zancor |
| Senha | *(definida no cadastro — hash no JSON)* |

> A senha em texto plano não está no repositório; use a senha configurada pelo desenvolvedor.
