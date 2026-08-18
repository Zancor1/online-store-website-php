# Auditoria de Segurança — Pixel Store

Análise **somente documental** — problemas não foram corrigidos nesta tarefa.

---

## Resumo executivo

| Gravidade | Quantidade |
|-----------|------------|
| Alta | 4 |
| Média | 6 |
| Baixa | 4 |

---

## Achados detalhados

### SEC01 — CSRF não implementado nos formulários

| Campo | Valor |
|-------|-------|
| **Arquivo** | `includes/seguranca.php` (funções existem); todos os forms POST |
| **Problema** | `csrfCampo()` e `csrfValidar()` definidos mas nunca chamados |
| **Gravidade** | Alta |
| **Consequência** | Atacante pode forjar requisições POST autenticadas (ex.: excluir produto, finalizar pedido) |
| **Recomendação** | Incluir `csrfCampo()` em todos os forms e validar `csrfValidar()` no processamento POST |

---

### SEC02 — Exclusões via GET sem confirmação

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/produtos.php`, `categorias.php`, `usuarios.php`, `fornecedores.php` |
| **Problema** | Exclusão acionada por link GET `?excluir=id` |
| **Gravidade** | Alta |
| **Consequência** | CSRF + prefetch de link podem excluir registros |
| **Recomendação** | Usar POST com token CSRF e confirmação JavaScript |

---

### SEC03 — Pasta uploads/ sem proteção

| Campo | Valor |
|-------|-------|
| **Arquivo** | `uploads/produtos/` (sem `.htaccess`) |
| **Problema** | Imagens acessíveis publicamente (esperado), mas pasta não restringe execução PHP |
| **Gravidade** | Média |
| **Consequência** | Se upload de PHP disfarçado passasse validação MIME, poderia executar |
| **Recomendação** | Adicionar `.htaccess` negando execução; validar extensão além de MIME |

---

### SEC04 — Ausência de rate limiting no login

| Campo | Valor |
|-------|-------|
| **Arquivo** | `index.php`, `admin/login.php` |
| **Problema** | Tentativas ilimitadas de login |
| **Gravidade** | Média |
| **Consequência** | Brute force em senhas |
| **Recomendação** | Implementar bloqueio temporário após N tentativas |

---

### SEC05 — XSS parcial no painel admin

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/produtos.php` (tabela), outros |
| **Problema** | Alguns campos exibidos com `echo $produto['nome']` sem `htmlspecialchars` |
| **Gravidade** | Média |
| **Consequência** | Stored XSS se admin malicioso cadastrar nome com script |
| **Recomendação** | Escapar toda saída HTML |

---

### SEC06 — Sessão admin simplificada

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/index.php` |
| **Problema** | Verifica apenas `$_SESSION['logado']`; não armazena ID do usuário |
| **Gravidade** | Baixa |
| **Consequência** | Impossível auditar qual admin fez ação; sem controle granular |
| **Recomendação** | Armazenar `usuario_id` na sessão |

---

### SEC07 — Proteção data/ depende de Apache

| Campo | Valor |
|-------|-------|
| **Arquivo** | `data/.htaccess` |
| **Problema** | `Require all denied` só funciona em Apache |
| **Gravidade** | Média |
| **Consequência** | Em `php -S` ou nginx mal configurado, JSON fica exposto |
| **Recomendação** | Mover data/ fora de webroot ou negar no servidor |

---

### SEC08 — Senhas em JSON no disco

| Campo | Valor |
|-------|-------|
| **Arquivo** | `data/usuarios.json`, `data/clientes.json` |
| **Problema** | Hashes bcrypt acessíveis se proteção falhar |
| **Gravidade** | Média |
| **Consequência** | Offline brute force nos hashes |
| **Recomendação** | Banco com permissões restritas; considerar pepper |

---

### SEC09 — Validação MIME upload contornável

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/produtos.php` |
| **Problema** | Confia em `mime_content_type()` do tmp file |
| **Gravidade** | Baixa |
| **Consequência** | Polyglot files em cenários específicos |
| **Recomendação** | Re-encode imagem com GD/Imagick |

---

### SEC10 — Checkbox "Remember me" não funcional

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/login.php` |
| **Problema** | Campo presente sem implementação |
| **Gravidade** | Baixa |
| **Consequência** | Expectativa falsa de persistência |
| **Recomendação** | Implementar ou remover |

---

### SEC11 — Exclusão sem verificação de vínculos

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/categorias.php` |
| **Problema** | Excluir categoria não atualiza produtos que referenciam o nome |
| **Gravidade** | Baixa (integridade, não segurança direta) |
| **Consequência** | Produtos órfãos de categoria |
| **Recomendação** | Bloquear exclusão se houver produtos |

---

### SEC12 — Regeneração de sessão parcial

| Campo | Valor |
|-------|-------|
| **Arquivo** | `includes/seguranca.php` |
| **Problema** | `regenerarSessao()` só no login, não no logout |
| **Gravidade** | Baixa |
| **Recomendação** | Regenerar também após logout |

---

## Pontos positivos encontrados

| Item | Evidência |
|------|-----------|
| Hash de senhas | `password_hash` / `password_verify` |
| Regeneração sessão no login | `regenerarSessao()` |
| Escape XSS na loja pública | `htmlspecialchars` consistente |
| Bloqueio HTTP em data/ | `.htaccess` |
| Whitelist MIME upload | JPG, PNG, WEBP, GIF |
| Nome aleatório upload | `bin2hex(random_bytes(12))` |
| Proteção checkout | Redirect se não logado |
| Proteção admin | Redirect se não logado |
| PDF admin | Verifica sessão |

---

## Matriz de controles

| Controle | Implementado | Efetivo |
|----------|--------------|---------|
| Autenticação cliente | Sim | Sim |
| Autenticação admin | Sim | Sim |
| Autorização por perfil | Não | N/A |
| CSRF | Parcial (código morto) | Não |
| XSS output encoding | Parcial | Parcial |
| SQL injection | N/A (sem SQL) | N/A |
| Upload seguro | Parcial | Parcial |
| HTTPS | N/A (deploy) | Depende deploy |
