# Requisitos Funcionais — Pixel Store

Cada RF foi derivado do código-fonte real. Arquivos entre parênteses indicam implementação principal.

---

## RF01 — Visualizar página inicial

| Campo | Valor |
|-------|-------|
| **Código** | RF01 |
| **Nome** | Visualizar página inicial |
| **Descrição** | O visitante acessa a home com hero banner, menu e rodapé. |
| **Ator** | Visitante, Cliente |
| **Pré-condições** | Servidor PHP ativo |
| **Fluxo principal** | 1. Acessar `index.php`. 2. Sistema exibe seção hero com CTA "Explorar produtos". |
| **Fluxos alternativos** | Se `?pg=` inválido em outras rotas, exibe "Página não encontrada". |
| **Pós-condições** | Página renderizada |
| **Arquivos** | `index.php` (linhas 174–182) |

---

## RF02 — Listar produtos do catálogo

| Campo | Valor |
|-------|-------|
| **Código** | RF02 |
| **Nome** | Listar produtos |
| **Descrição** | Exibe grid/lista de produtos com nome, preço, categoria, imagem e link para detalhes. |
| **Ator** | Visitante, Cliente |
| **Pré-condições** | Existem produtos em `data/produtos.json` |
| **Fluxo principal** | 1. Acessar `index.php?pg=produtos`. 2. Sistema carrega produtos via `listarProdutos()`. 3. Exibe cards. |
| **Fluxos alternativos** | Filtro por categoria via `?cat=NomeCategoria`. Lista vazia exibe mensagem. |
| **Pós-condições** | Catálogo exibido |
| **Arquivos** | `pages/produtos.php`, `includes/banco_ficticio.php` |

---

## RF03 — Filtrar produtos por categoria

| Campo | Valor |
|-------|-------|
| **Código** | RF03 |
| **Nome** | Filtrar produtos por categoria |
| **Descrição** | Sidebar permite filtrar produtos pela string de categoria armazenada no produto. |
| **Ator** | Visitante, Cliente |
| **Pré-condições** | Produtos possuem campo `categoria` |
| **Fluxo principal** | 1. Na página produtos, clicar categoria na sidebar. 2. URL recebe `&cat=`. 3. Lista filtrada. |
| **Fluxos alternativos** | "Todos" remove filtro. |
| **Pós-condições** | Subconjunto de produtos exibido |
| **Arquivos** | `pages/produtos.php` (linhas 4–7, 21–24) |

---

## RF04 — Visualizar detalhes do produto

| Campo | Valor |
|-------|-------|
| **Código** | RF04 |
| **Nome** | Visualizar detalhes do produto |
| **Descrição** | Página com imagem, nome, descrição, preço, seleção de variação (se houver) e quantidade. |
| **Ator** | Visitante, Cliente |
| **Pré-condições** | ID válido em `?pg=detalhe&id=` |
| **Fluxo principal** | 1. Clicar "Detalhes" no catálogo. 2. Sistema busca produto por ID. 3. Renderiza página. |
| **Fluxos alternativos** | ID inválido → mensagem "Produto não encontrado". |
| **Pós-condições** | Detalhe exibido |
| **Arquivos** | `pages/detalhe.php`, `includes/banco_ficticio.php` (`buscarProdutoPorId`) |

---

## RF05 — Selecionar variação de produto

| Campo | Valor |
|-------|-------|
| **Código** | RF05 |
| **Nome** | Selecionar variação |
| **Descrição** | Produtos com array `variacoes` exibem `<select>`; JS troca imagem ao mudar opção. |
| **Ator** | Cliente (para comprar) |
| **Pré-condições** | Produto possui `variacoes[]` com `nome` e `imagem` |
| **Fluxo principal** | 1. Abrir detalhe. 2. Selecionar variação. 3. Imagem atualiza via JavaScript. |
| **Fluxos alternativos** | Produto sem variações não exibe select. |
| **Pós-condições** | Variação selecionada no formulário |
| **Arquivos** | `pages/detalhe.php` (linhas 21–40) |

---

## RF06 — Adicionar produto ao carrinho

| Campo | Valor |
|-------|-------|
| **Código** | RF06 |
| **Nome** | Adicionar ao carrinho |
| **Descrição** | Cliente logado adiciona produto (com quantidade 1–99 e variação opcional) ao carrinho em sessão. |
| **Ator** | Cliente |
| **Pré-condições** | Cliente autenticado (`$_SESSION['cliente']`) |
| **Fluxo principal** | 1. No detalhe, definir quantidade. 2. POST `acao=adicionar`. 3. Item gravado em `$_SESSION['carrinho']`. 4. Redirect ao carrinho. |
| **Fluxos alternativos** | Sem login → redirect login com mensagem. Variação inválida → redirect detalhe com erro. Quantidade acumula até máx. 99. |
| **Pós-condições** | Carrinho atualizado |
| **Arquivos** | `index.php` (linhas 50–70), `pages/detalhe.php` |

---

## RF07 — Visualizar carrinho

| Campo | Valor |
|-------|-------|
| **Código** | RF07 |
| **Nome** | Visualizar carrinho |
| **Descrição** | Lista itens, quantidades, subtotais e total geral. |
| **Ator** | Cliente |
| **Pré-condições** | Sessão ativa |
| **Fluxo principal** | 1. Acessar `?pg=carrinho` ou ícone do header. 2. Sistema monta lista a partir da sessão. |
| **Fluxos alternativos** | Carrinho vazio → mensagem e link para produtos. |
| **Pós-condições** | Carrinho exibido |
| **Arquivos** | `pages/carrinho.php`, `index.php` (contador header) |

---

## RF08 — Remover item do carrinho

| Campo | Valor |
|-------|-------|
| **Código** | RF08 |
| **Nome** | Remover item do carrinho |
| **Descrição** | Remove chave do carrinho (id ou id:variacao). |
| **Ator** | Cliente |
| **Pré-condições** | Item existente no carrinho |
| **Fluxo principal** | 1. Clicar remover. 2. POST `acao=remover`. 3. Item removido da sessão. |
| **Fluxos alternativos** | — |
| **Pós-condições** | Carrinho atualizado |
| **Arquivos** | `index.php` (linhas 73–79), `pages/carrinho.php` |

---

## RF09 — Finalizar compra (checkout)

| Campo | Valor |
|-------|-------|
| **Código** | RF09 |
| **Nome** | Finalizar compra |
| **Descrição** | Cliente informa endereço, cidade e CEP; pedido salvo em `pedidos.json` com status "pendente". |
| **Ator** | Cliente |
| **Pré-condições** | Login ativo; carrinho não vazio |
| **Fluxo principal** | 1. Carrinho → "Finalizar compra". 2. Preencher checkout. 3. POST `acao=finalizar`. 4. `salvarPedido()`. 5. Carrinho limpo. 6. Mensagem de sucesso. |
| **Fluxos alternativos** | Sem login → redirect login. Campos vazios → redirect checkout. Carrinho vazio → nada salvo. |
| **Pós-condições** | Pedido persistido; carrinho vazio |
| **Arquivos** | `index.php` (82–112), `pages/checkout.php`, `pages/carrinho.php`, `includes/banco_ficticio.php` |

---

## RF10 — Cadastrar conta de cliente

| Campo | Valor |
|-------|-------|
| **Código** | RF10 |
| **Nome** | Cadastrar cliente |
| **Descrição** | Registro com nome, e-mail e senha (mín. 6 caracteres). Senha hasheada com `password_hash`. |
| **Ator** | Visitante |
| **Pré-condições** | E-mail não cadastrado |
| **Fluxo principal** | 1. `?pg=login&modo=register`. 2. Preencher formulário. 3. POST `acao=registrar_cliente`. 4. Grava em `clientes.json`. 5. Login automático. |
| **Fluxos alternativos** | E-mail duplicado, validação falha → mensagem de erro. |
| **Pós-condições** | Cliente criado e logado |
| **Arquivos** | `index.php` (13–29), `pages/login.php`, `includes/banco_ficticio.php` |

---

## RF11 — Login de cliente

| Campo | Valor |
|-------|-------|
| **Código** | RF11 |
| **Nome** | Login de cliente |
| **Descrição** | Autenticação por e-mail/senha com `password_verify`. |
| **Ator** | Cliente |
| **Pré-condições** | Conta existente |
| **Fluxo principal** | 1. Formulário login. 2. POST `acao=login_cliente`. 3. Sessão `cliente` criada. 4. Redirect produtos. |
| **Fluxos alternativos** | Credenciais inválidas → erro. |
| **Pós-condições** | Cliente autenticado |
| **Arquivos** | `index.php` (32–39), `pages/login.php` |

---

## RF12 — Logout de cliente

| Campo | Valor |
|-------|-------|
| **Código** | RF12 |
| **Nome** | Logout de cliente |
| **Descrição** | Remove `$_SESSION['cliente']`. |
| **Ator** | Cliente |
| **Pré-condições** | Cliente logado |
| **Fluxo principal** | POST `acao=logout` → redirect home. |
| **Arquivos** | `index.php` (44–47) |

---

## RF13 — Acessar página de contato

| Campo | Valor |
|-------|-------|
| **Código** | RF13 |
| **Nome** | Página de contato |
| **Descrição** | Exibe informações e link `mailto:meuovinho06@gmail.com`. |
| **Ator** | Visitante |
| **Pré-condições** | — |
| **Fluxo principal** | Acessar `?pg=contato`. |
| **Arquivos** | `pages/contato.php` |

---

## RF14 — Acessar página de benefícios

| Campo | Valor |
|-------|-------|
| **Código** | RF14 |
| **Nome** | Página de benefícios |
| **Descrição** | Lista benefícios estáticos com filtro por categoria local (Selecao, Seguranca, Atendimento). |
| **Ator** | Visitante |
| **Arquivos** | `pages/beneficios.php` |

---

## RF15 — Login administrativo

| Campo | Valor |
|-------|-------|
| **Código** | RF15 |
| **Nome** | Login administrativo |
| **Descrição** | Autentica contra `usuarios.json`; verifica campo `ativo`. |
| **Ator** | Administrador |
| **Pré-condições** | Usuário admin cadastrado e ativo |
| **Fluxo principal** | 1. `admin/login.php`. 2. POST usuário/senha. 3. `$_SESSION['logado']=true`. 4. Redirect dashboard. |
| **Fluxos alternativos** | Conta inativa → erro. Credenciais inválidas → erro. |
| **Arquivos** | `admin/login.php` |

---

## RF16 — Visualizar dashboard administrativo

| Campo | Valor |
|-------|-------|
| **Código** | RF16 |
| **Nome** | Dashboard admin |
| **Descrição** | Contadores de produtos, categorias, fornecedores, admins, clientes, pedidos; últimos 5 pedidos. |
| **Ator** | Administrador |
| **Pré-condições** | Sessão admin |
| **Arquivos** | `admin/pages/dashboard.php` |

---

## RF17 — CRUD de categorias (criar, listar, editar, excluir)

| Campo | Valor |
|-------|-------|
| **Código** | RF17 |
| **Nome** | Gerenciar categorias |
| **Ator** | Administrador |
| **Fluxo principal** | Criar via form POST; listar tabela; editar em `editar_categoria.php`; excluir via GET `excluir`. |
| **Fluxos alternativos** | Nome duplicado rejeitado na criação. |
| **Arquivos** | `admin/pages/categorias.php`, `admin/pages/editar_categoria.php` |

---

## RF18 — Cadastrar e excluir produtos

| Campo | Valor |
|-------|-------|
| **Código** | RF18 |
| **Nome** | Gerenciar produtos |
| **Descrição** | Criar produto com upload de imagem e variações opcionais; listar; excluir. **Edição não implementada.** |
| **Ator** | Administrador |
| **Arquivos** | `admin/pages/produtos.php` |

---

## RF19 — Upload de imagem de produto

| Campo | Valor |
|-------|-------|
| **Código** | RF19 |
| **Nome** | Upload de imagem |
| **Descrição** | Aceita JPG, PNG, WEBP, GIF; salva em `uploads/produtos/` com nome aleatório. |
| **Ator** | Administrador |
| **Arquivos** | `admin/pages/produtos.php` (`salvarImagemProduto`) |

---

## RF20 — CRUD de usuários administrativos

| Campo | Valor |
|-------|-------|
| **Código** | RF20 |
| **Nome** | Gerenciar administradores |
| **Descrição** | Criar, listar, editar (`editar_usuario.php`), excluir. Campo `ativo` existe mas sem UI de toggle. |
| **Ator** | Administrador |
| **Arquivos** | `admin/pages/usuarios.php`, `admin/pages/editar_usuario.php` |

---

## RF21 — CRUD de fornecedores

| Campo | Valor |
|-------|-------|
| **Código** | RF21 |
| **Nome** | Gerenciar fornecedores |
| **Descrição** | Criar, listar, editar (`editar_fornecedor.php`), excluir. |
| **Ator** | Administrador |
| **Arquivos** | `admin/pages/fornecedores.php`, `admin/pages/editar_fornecedor.php` |

---

## RF22 — Consultar documentação no painel

| Campo | Valor |
|-------|-------|
| **Código** | RF22 |
| **Nome** | Documentação in-app |
| **Descrição** | Página de manual integrada ao admin com download PDF. |
| **Ator** | Administrador |
| **Arquivos** | `admin/pages/documentacao.php`, `admin/documentacao_pdf.php` |

---

## RF23 — Proteger área administrativa

| Campo | Valor |
|-------|-------|
| **Código** | RF23 |
| **Nome** | Controle de acesso admin |
| **Descrição** | `admin/index.php` redireciona para login se `$_SESSION['logado']` ausente. |
| **Ator** | Sistema |
| **Arquivos** | `admin/index.php` (linhas 7–11) |

---

## RF24 — Proteger checkout

| Campo | Valor |
|-------|-------|
| **Código** | RF24 |
| **Nome** | Checkout exige login |
| **Descrição** | Acesso a `?pg=checkout` sem cliente logado redireciona ao login. |
| **Arquivos** | `index.php` (117–119) |

---

## RF25 — Regenerar ID de sessão no login

| Campo | Valor |
|-------|-------|
| **Código** | RF25 |
| **Nome** | Regeneração de sessão |
| **Descrição** | Após login bem-sucedido (cliente ou admin), chama `regenerarSessao()`. |
| **Arquivos** | `includes/seguranca.php`, `index.php`, `admin/login.php` |

---

## Resumo quantitativo

| Total RF documentados | 25 |
|-----------------------|-----|
| Área pública | RF01–RF14 |
| Área administrativa | RF15–RF23 |
| Transversal | RF24–RF25 |
