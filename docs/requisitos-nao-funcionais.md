# Requisitos Não Funcionais — Pixel Store

Análise baseada no código real. Itens marcados como **Parcial** indicam implementação incompleta.

---

## RNF01 — Usabilidade da interface pública

| Campo | Valor |
|-------|-------|
| **Código** | RNF01 |
| **Categoria** | Usabilidade |
| **Descrição** | Interface da loja utiliza layout responsivo com Tailwind CSS, menu sticky, ícones e tipografia legível. |
| **Evidência** | `index.php`: viewport meta, classes `md:`, `sm:` |
| **Status** | Atendido |

---

## RNF02 — Usabilidade do painel administrativo

| Campo | Valor |
|-------|-------|
| **Código** | RNF02 |
| **Categoria** | Usabilidade |
| **Descrição** | Admin possui topbar, quick actions coloridas e formulários com labels. |
| **Evidência** | `admin/index.php`, `admin/admin.css` |
| **Status** | Atendido |

---

## RNF03 — Responsividade

| Campo | Valor |
|-------|-------|
| **Código** | RNF03 |
| **Categoria** | Compatibilidade |
| **Descrição** | Loja pública adapta grid e flex em breakpoints (`md:`, `lg:`). Admin usa `grid-cols-1 md:grid-cols-3`. |
| **Evidência** | `pages/produtos.php`, `admin/pages/produtos.php` |
| **Status** | Atendido (parcial no admin — tabelas largas exigem scroll horizontal) |

---

## RNF04 — Compatibilidade com XAMPP/Apache

| Campo | Valor |
|-------|-------|
| **Código** | RNF04 |
| **Categoria** | Compatibilidade |
| **Descrição** | Sistema roda em PHP puro sem dependências Composer; adequado a XAMPP. |
| **Evidência** | Estrutura `htdocs/`, sem `composer.json` |
| **Status** | Atendido |

---

## RNF05 — Armazenamento em arquivos JSON

| Campo | Valor |
|-------|-------|
| **Código** | RNF05 |
| **Categoria** | Armazenamento |
| **Descrição** | Dados persistidos em JSON com `file_put_contents(..., LOCK_EX)`. |
| **Evidência** | `includes/banco_ficticio.php` (`lerJson`, `salvarJson`) |
| **Status** | Atendido |
| **Limitação** | Sem transações; concorrência simultânea pode corromper dados |

---

## RNF06 — Integridade básica de IDs

| Campo | Valor |
|-------|-------|
| **Código** | RNF06 |
| **Categoria** | Integridade dos dados |
| **Descrição** | Função `proximoId()` gera IDs incrementais por entidade. |
| **Evidência** | `includes/banco_ficticio.php` |
| **Status** | Atendido |
| **Exceção** | `fornecedores.php` também define `id => time()` antes de `salvarFornecedores()` que sobrescreve com `proximoId()` |

---

## RNF07 — Hash de senhas

| Campo | Valor |
|-------|-------|
| **Código** | RNF07 |
| **Categoria** | Segurança |
| **Descrição** | Senhas de clientes e admins armazenadas com `password_hash(PASSWORD_DEFAULT)`. |
| **Evidência** | `cadastrarCliente()`, `salvarUsuario()`, `atualizarUsuario()` |
| **Status** | Atendido |

---

## RNF08 — Proteção XSS na saída

| Campo | Valor |
|-------|-------|
| **Código** | RNF08 |
| **Categoria** | Segurança |
| **Descrição** | Dados dinâmicos escapados com `htmlspecialchars()` na loja pública. |
| **Evidência** | `index.php`, `pages/*` |
| **Status** | Parcial — admin exibe alguns campos sem escape (`$produto['nome']` na tabela) |

---

## RNF09 — Proteção CSRF

| Campo | Valor |
|-------|-------|
| **Código** | RNF09 |
| **Categoria** | Segurança |
| **Descrição** | Funções `csrfToken()`, `csrfCampo()`, `csrfValidar()` existem mas **não são usadas** em nenhum formulário. |
| **Evidência** | `includes/seguranca.php`; grep confirma ausência de uso |
| **Status** | Não atendido |

---

## RNF10 — Controle de acesso administrativo

| Campo | Valor |
|-------|-------|
| **Código** | RNF10 |
| **Categoria** | Segurança / Autorização |
| **Descrição** | Páginas admin exigem sessão `logado`. PDF exige mesma sessão. |
| **Evidência** | `admin/index.php`, `admin/documentacao_pdf.php` |
| **Status** | Atendido |
| **Limitação** | Não há papéis/perfis — todo admin tem acesso total |

---

## RNF11 — Proteção da pasta data/

| Campo | Valor |
|-------|-------|
| **Código** | RNF11 |
| **Categoria** | Segurança |
| **Descrição** | `.htaccess` com `Require all denied` impede acesso HTTP direto aos JSON (Apache). |
| **Evidência** | `data/.htaccess` |
| **Status** | Parcial — depende de Apache; uploads/ não protegido |

---

## RNF12 — Validação de upload de imagens

| Campo | Valor |
|-------|-------|
| **Código** | RNF12 |
| **Categoria** | Segurança |
| **Descrição** | Valida MIME type via `mime_content_type()` e whitelist JPG/PNG/WEBP/GIF; nome aleatório. |
| **Evidência** | `admin/pages/produtos.php` |
| **Status** | Atendido |

---

## RNF13 — Desempenho

| Campo | Valor |
|-------|-------|
| **Código** | RNF13 |
| **Categoria** | Desempenho |
| **Descrição** | Leitura completa de JSON a cada requisição; sem cache. Adequado para demo/pequeno volume. |
| **Status** | Parcial — não escala para alto tráfego |

---

## RNF14 — Manutenibilidade

| Campo | Valor |
|-------|-------|
| **Código** | RNF14 |
| **Categoria** | Manutenibilidade |
| **Descrição** | Camada `banco_ficticio.php` centraliza acesso a dados; comentário indica migração futura para MySQL. |
| **Status** | Parcial — lógica de negócio misturada nas views/controladores |

---

## RNF15 — Sessões PHP

| Campo | Valor |
|-------|-------|
| **Código** | RNF15 |
| **Categoria** | Sessões |
| **Descrição** | Carrinho, cliente, admin e mensagens flash usam `session_start()`. Regeneração de ID no login. |
| **Evidência** | `index.php`, `admin/login.php`, `includes/seguranca.php` |
| **Status** | Atendido |
| **Limitação** | Sem timeout configurado; sem opção "remember me" funcional no admin |

---

## Tabela resumo

| ID | Categoria | Status |
|----|-----------|--------|
| RNF01 | Usabilidade | Atendido |
| RNF02 | Usabilidade | Atendido |
| RNF03 | Responsividade | Parcial |
| RNF04 | Compatibilidade | Atendido |
| RNF05 | Armazenamento | Atendido |
| RNF06 | Integridade | Atendido |
| RNF07 | Senhas | Atendido |
| RNF08 | XSS | Parcial |
| RNF09 | CSRF | Não atendido |
| RNF10 | Autorização | Atendido |
| RNF11 | Exposição arquivos | Parcial |
| RNF12 | Upload | Atendido |
| RNF13 | Desempenho | Parcial |
| RNF14 | Manutenibilidade | Parcial |
| RNF15 | Sessões | Atendido |
