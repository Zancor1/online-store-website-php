# Auditoria de Segurança — Pixel Store

Auditoria original (documental) seguida das correções aplicadas nesta tarefa. Achados corrigidos permanecem listados para rastreabilidade, com o status atualizado.

---

## Resumo executivo

| Gravidade | Quantidade original | Corrigidos nesta tarefa |
|-----------|------------|------------|
| Alta | 4 | 4 (SEC01, SEC02, SEC13*) |
| Média | 6 | 2 (SEC05, SEC06) |
| Baixa | 4 | 0 |

\* SEC13 é um achado novo identificado durante esta correção (acesso direto às páginas do admin), não fazia parte da auditoria original.

---

## Achados detalhados

### SEC01 — CSRF não implementado nos formulários — **CORRIGIDO**

| Campo | Valor |
|-------|-------|
| **Arquivo** | `includes/seguranca.php` (funções existem); todos os forms POST |
| **Problema** | `csrfCampo()` e `csrfValidar()` definidos mas nunca chamados |
| **Gravidade** | Alta |
| **Consequência** | Atacante pode forjar requisições POST autenticadas (ex.: excluir produto, finalizar pedido) |
| **Correção aplicada** | `csrfCampo()` incluído em todos os formulários (loja pública e admin) e `csrfValidar()` chamado no início de todo processamento POST, tanto em `index.php` (loja) quanto em cada página do `admin/pages/`. Requisição sem token válido é rejeitada com mensagem de erro. |

---

### SEC02 — Exclusões via GET sem confirmação — **CORRIGIDO**

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/produtos.php`, `categorias.php`, `usuarios.php`, `fornecedores.php` |
| **Problema** | Exclusão acionada por link GET `?excluir=id` |
| **Gravidade** | Alta |
| **Consequência** | CSRF + prefetch de link podem excluir registros |
| **Correção aplicada** | Todas as exclusões (e a nova ação de ativar/desativar usuário) passaram a usar formulários `POST` com token CSRF e confirmação via `confirm()` no navegador. |

---

### SEC03 — Pasta uploads/ sem proteção

| Campo | Valor |
|-------|-------|
| **Arquivo** | `uploads/produtos/` (sem `.htaccess`) |
| **Problema** | Imagens acessíveis publicamente (esperado), mas pasta não restringe execução PHP |
| **Gravidade** | Média |
| **Consequência** | Se upload de PHP disfarçado passasse validação MIME, poderia executar |
| **Recomendação** | Adicionar `.htaccess` negando execução; validar extensão além de MIME |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC04 — Ausência de rate limiting no login

| Campo | Valor |
|-------|-------|
| **Arquivo** | `index.php`, `admin/login.php` |
| **Problema** | Tentativas ilimitadas de login |
| **Gravidade** | Média |
| **Consequência** | Brute force em senhas |
| **Recomendação** | Implementar bloqueio temporário após N tentativas |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC05 — XSS parcial no painel admin — **CORRIGIDO**

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/produtos.php` (tabela), outros |
| **Problema** | Alguns campos exibidos com `echo $produto['nome']` sem `htmlspecialchars` |
| **Gravidade** | Média |
| **Consequência** | Stored XSS se admin malicioso cadastrar nome com script |
| **Correção aplicada** | Toda saída de dados vindos do banco fictício ou de `$_POST` passou a usar `htmlspecialchars()` nas páginas do admin (produtos, usuários, categorias, fornecedores). |

---

### SEC06 — Sessão admin simplificada — **CORRIGIDO**

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/index.php` |
| **Problema** | Verifica apenas `$_SESSION['logado']`; não armazena ID do usuário |
| **Gravidade** | Baixa |
| **Consequência** | Impossível auditar qual admin fez ação; sem controle granular |
| **Correção aplicada** | `admin/login.php` agora grava `$_SESSION['usuario_id']`, usado para impedir que um administrador desative/exclua a própria conta. |

---

### SEC07 — Proteção data/ depende de Apache

| Campo | Valor |
|-------|-------|
| **Arquivo** | `data/.htaccess` |
| **Problema** | `Require all denied` só funciona em Apache |
| **Gravidade** | Média |
| **Consequência** | Em `php -S` ou nginx mal configurado, JSON fica exposto |
| **Recomendação** | Mover data/ fora de webroot ou negar no servidor |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC08 — Senhas em JSON no disco

| Campo | Valor |
|-------|-------|
| **Arquivo** | `data/usuarios.json`, `data/clientes.json` |
| **Problema** | Hashes bcrypt acessíveis se proteção falhar |
| **Gravidade** | Média |
| **Consequência** | Offline brute force nos hashes |
| **Recomendação** | Banco com permissões restritas; considerar pepper |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC09 — Validação MIME upload contornável

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/produtos.php` |
| **Problema** | Confia em `mime_content_type()` do tmp file |
| **Gravidade** | Baixa |
| **Consequência** | Polyglot files em cenários específicos |
| **Recomendação** | Re-encode imagem com GD/Imagick |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC10 — Checkbox "Remember me" não funcional

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/login.php` |
| **Problema** | Campo presente sem implementação |
| **Gravidade** | Baixa |
| **Consequência** | Expectativa falsa de persistência |
| **Recomendação** | Implementar ou remover |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC11 — Exclusão sem verificação de vínculos

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/categorias.php` |
| **Problema** | Excluir categoria não atualiza produtos que referenciam o nome |
| **Gravidade** | Baixa (integridade, não segurança direta) |
| **Consequência** | Produtos órfãos de categoria |
| **Recomendação** | Bloquear exclusão se houver produtos |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC12 — Regeneração de sessão parcial

| Campo | Valor |
|-------|-------|
| **Arquivo** | `includes/seguranca.php` |
| **Problema** | `regenerarSessao()` só no login, não no logout |
| **Gravidade** | Baixa |
| **Recomendação** | Regenerar também após logout |
| **Status** | Não corrigido nesta tarefa (fora do escopo solicitado); recomendação mantida |

---

### SEC13 — Acesso direto às páginas do admin e travessia de diretório — **CORRIGIDO** (achado novo)

| Campo | Valor |
|-------|-------|
| **Arquivo** | `admin/pages/*.php`, `admin/index.php` |
| **Problema** | (a) Os arquivos dentro de `admin/pages/` não verificavam sessão por conta própria — uma requisição direta a, por exemplo, `admin/pages/produtos.php` executava sem checar `$_SESSION['logado']`, pulando o guard de `admin/index.php`. (b) O roteador do admin montava `"pages/" . $_GET['pg'] . ".php"` sem sanitizar, permitindo travessia de diretório (ex.: `?pg=../../includes/banco_ficticio`) para incluir outros arquivos `.php` do projeto. |
| **Gravidade** | Alta |
| **Consequência** | Bypass da autenticação administrativa e inclusão de arquivos fora da pasta de páginas do admin. |
| **Correção aplicada** | Criado `admin/pages/.htaccess` com `Require all denied`, bloqueando qualquer requisição HTTP direta a esses arquivos (o `include()` feito internamente por `admin/index.php` continua funcionando normalmente). O roteador agora usa `basename($_GET['pg'])` antes de montar o caminho do arquivo. |

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
| CSRF | Token gerado, validado em todos os forms POST (público e admin) |
| Baixa de estoque concorrente | `flock()` exclusivo no arquivo de produtos durante checagem + baixa |

---

## Matriz de controles

| Controle | Implementado | Efetivo |
|----------|--------------|---------|
| Autenticação cliente | Sim | Sim |
| Autenticação admin | Sim | Sim |
| Autorização por perfil | Não | N/A |
| CSRF | Sim | Sim |
| XSS output encoding | Sim | Sim |
| SQL injection | N/A (sem SQL) | N/A |
| Upload seguro | Parcial | Parcial |
| HTTPS | N/A (deploy) | Depende deploy |
| Acesso direto a páginas administrativas | Sim (`.htaccess` + guard de sessão) | Sim |
| Controle de estoque (sem venda além do disponível) | Sim (`flock` + checagem atômica) | Sim |
