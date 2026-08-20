# Validações Existentes — Pixel Store

Documentação das validações **realmente implementadas** no código.

---

## 1. Loja pública (`index.php`)

### Registro de cliente

| Campo/Regra | Validação | Mensagem de erro |
|-------------|-----------|------------------|
| nome | `trim`, não vazio | "Informe nome, email valido..." |
| login | `filter_var(FILTER_VALIDATE_EMAIL)` | idem |
| senha | `strlen >= 6` | idem |
| email único | `buscarClientePorLogin()` | "Este email ja possui uma conta." |

### Login cliente

| Regra | Validação |
|-------|-----------|
| Credenciais | `buscarClientePorLogin` + `password_verify` |
| Erro | "Email ou senha invalidos." |

### Adicionar ao carrinho

| Regra | Validação |
|-------|-----------|
| Login | `empty($_SESSION['cliente'])` → redirect login |
| ID produto | `FILTER_VALIDATE_INT` |
| Produto existe | `buscarProdutoPorId($id)` |
| Quantidade | int 1–99, default 1 |
| Variação | Se produto tem variações, índice deve existir |
| Acumulo | Máximo 99 por chave |

### Finalizar pedido

| Regra | Validação |
|-------|-----------|
| Login | Obrigatório |
| endereco, cidade, cep | `trim`, não vazios |
| Carrinho | Só salva se não vazio |

### Checkout (GET)

| Regra | Validação |
|-------|-----------|
| Login | Redirect se `empty($_SESSION['cliente'])` |

---

## 2. Detalhe produto (`pages/detalhe.php`)

| Regra | Validação |
|-------|-----------|
| ID | `FILTER_VALIDATE_INT` via GET |
| Produto | Exibe erro se não encontrado |
| Variação | `required` no HTML se existir variações |
| Quantidade | HTML `min=1 max=99` |

---

## 3. Login admin (`admin/login.php`)

| Regra | Validação |
|-------|-----------|
| Usuário | trim + htmlspecialchars na entrada |
| Senha | password_verify |
| Conta ativa | `ativo !== false` |
| Erro inativo | "Esta conta foi desativada pelo sistema." |
| Erro credencial | "Usuário ou senha inválidos!" |

---

## 4. Categorias (`admin/pages/categorias.php`)

| Regra | Validação |
|-------|-----------|
| Nome | trim, htmlspecialchars, não vazio |
| Duplicata | Comparação case-insensitive do nome |
| Exclusão | GET id intval |

---

## 5. Produtos (`admin/pages/produtos.php`)

| Regra | Validação |
|-------|-----------|
| Nome, descrição | trim, htmlspecialchars, obrigatórios |
| Preço | numérico, >= 0 |
| Categoria | ID deve existir em listarCategorias |
| Imagem (simples) | Obrigatória; UPLOAD_ERR_OK; MIME whitelist |
| Variações | Mínimo 1; cada uma com nome e imagem válida |
| Cadastro bloqueado | Se zero categorias, botão disabled |

---

## 6. Usuários admin (`admin/pages/usuarios.php`)

| Regra | Validação |
|-------|-----------|
| Campos | nome, login, senha obrigatórios |
| Login duplicado | Verificação case-insensitive (**bug: lógica dentro do foreach**) |

---

## 7. Fornecedores (`admin/pages/fornecedores.php`)

| Regra | Validação |
|-------|-----------|
| Campos | Todos obrigatórios (**bug: `empty('cep')` sempre true — validação de CEP falha silenciosamente**) |
| CNPJ duplicado | Verificação case-insensitive |

---

## 8. Edição

### editar_usuario.php

| Regra | Validação |
|-------|-----------|
| ID | Deve existir |
| nome, login | Obrigatórios |
| Login duplicado | Outro ID |
| Senha | Opcional; se preenchida, re-hash |

### editar_categoria.php

| Regra | Validação |
|-------|-----------|
| Nome | Obrigatório |

### editar_fornecedor.php

| Regra | Validação |
|-------|-----------|
| Todos campos | Obrigatórios |
| CNPJ duplicado | Outro ID |

---

## 9. Validações ausentes (não implementadas)

| Área | O que falta |
|------|-------------|
| CEP | Formato/máscara |
| CNPJ | Dígitos verificadores |
| E-mail admin | Formato (aceita qualquer string como login) |
| Preço checkout | Revalidação server-side contra JSON (a quantidade e o estoque já são revalidados) |
| Sanitização HTML | Descrição produto armazenada com htmlspecialchars na criação |

## 9.1 Validações implementadas nesta correção

| Área | O que foi implementado |
|------|-------------|
| CSRF | Token validado em todos os formulários POST (loja pública e admin) |
| Estoque | Verificação de disponibilidade ao adicionar ao carrinho e ao finalizar a compra (revalidação atômica com `flock()`) |
| Confirmação exclusão | Todas as exclusões usam POST + CSRF + `confirm()` no navegador |

---

## 10. Validação client-side (HTML5)

| Form | Atributos |
|------|-----------|
| Login cliente | `required`, `type=email`, `minlength=6` |
| Checkout | `required` nos campos |
| Admin forms | `required` na maioria dos inputs |
| Produto admin | `step=0.01`, `min=0`, `accept=image/*` |
