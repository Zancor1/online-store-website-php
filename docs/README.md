# Pixel Store — Documentação Acadêmica Completa

> **Sistema:** Loja virtual de produtos de tecnologia  
> **Versão documentada:** conforme código-fonte analisado em agosto/2026  
> **Stack real:** PHP + HTML/CSS/JS + arquivos JSON (sem banco MySQL)

---

## Índice da documentação

| # | Seção | Arquivo |
|---|-------|---------|
| 1 | Introdução, objetivos, visão geral | Este arquivo (seções 1–6) |
| 2 | Requisitos funcionais (RF01–RF25) | [requisitos-funcionais.md](requisitos-funcionais.md) |
| 3 | Requisitos não funcionais (RNF01–RNF15) | [requisitos-nao-funcionais.md](requisitos-nao-funcionais.md) |
| 4 | Casos de uso e fluxos | [casos-de-uso.md](casos-de-uso.md) |
| 5 | Arquitetura e estrutura do projeto | [arquitetura.md](arquitetura.md) |
| 6 | Cadastros (CRUD) e movimento de compra | [cadastros-movimento.md](cadastros-movimento.md) |
| 7 | Front-end público e área administrativa | [telas-sistema.md](telas-sistema.md) |
| 8 | Entidades e modelo de dados | [modelo-dados.md](modelo-dados.md) |
| 9 | Dicionário de dados | [dicionario-dados.md](dicionario-dados.md) |
| 10 | Diagramas UML (Mermaid) | [diagramas.md](diagramas.md) |
| 11 | Segurança (auditoria do código) | [seguranca.md](seguranca.md) |
| 12 | Validações existentes | [validacoes.md](validacoes.md) |
| 13 | Casos de teste | [testes.md](testes.md) |
| 14 | Manual do usuário | [manual-usuario.md](manual-usuario.md) |
| 15 | Manual do administrador | [manual-administrador.md](manual-administrador.md) |
| 16 | Matriz de rastreabilidade | [matriz-rastreabilidade.md](matriz-rastreabilidade.md) |
| 17 | Limitações e melhorias futuras | [limitacoes-melhorias.md](limitacoes-melhorias.md) |
| 18 | Checklist acadêmico | [checklist-academico.md](checklist-academico.md) |

---

## 1. Introdução

### 1.1 O que é o sistema

A **Pixel Store** é uma loja virtual desenvolvida em PHP que comercializa produtos de tecnologia — periféricos, áudio e hardware. O sistema possui duas áreas distintas:

- **Área pública** (`index.php`): vitrine, catálogo, cadastro/login de clientes, carrinho e checkout simulado.
- **Área administrativa** (`admin/`): painel para gerenciar categorias, produtos, usuários administrativos e fornecedores.

### 1.2 Problema que resolve

Organiza a apresentação de produtos de tecnologia em um catálogo navegável, permite que clientes criem conta, montem carrinho e registrem pedidos, e oferece ao administrador ferramentas para manter o cadastro de produtos e entidades auxiliares.

### 1.3 Objetivo do projeto

Demonstrar, em contexto acadêmico, a implementação de um sistema de comércio eletrônico com cadastros CRUD, movimento de compra, front-end público e painel administrativo, utilizando persistência em arquivos JSON.

### 1.4 Público-alvo

| Público | Descrição |
|---------|-----------|
| Visitantes | Pessoas que navegam pelo catálogo sem conta |
| Clientes | Usuários registrados que podem comprar |
| Administradores | Equipe com acesso ao painel `admin/` |

### 1.5 Contexto acadêmico

Projeto desenvolvido para atender requisitos de disciplina que exigem: cadastros com CRUD, movimento principal do sistema, front-end público, documentação com requisitos funcionais/não funcionais, diagramas UML, dicionário de dados e manuais de uso.

---

## 2. Objetivos

### 2.1 Objetivo geral

Desenvolver uma loja virtual funcional que permita a gestão de produtos e a simulação completa de uma jornada de compra, com documentação técnica alinhada ao código real.

### 2.2 Objetivos específicos

1. Disponibilizar catálogo de produtos com filtro por categoria.
2. Permitir cadastro e autenticação de clientes na área pública.
3. Implementar carrinho de compras e checkout com registro de pedido.
4. Oferecer painel administrativo com CRUD de categorias, produtos, usuários e fornecedores.
5. Persistir dados em arquivos JSON locais.
6. Documentar requisitos, entidades, fluxos e diagramas com base no código existente.

---

## 3. Visão geral do sistema

```
Visitante/Cliente                    Administrador
       │                                    │
       ▼                                    ▼
  index.php (?pg=)                  admin/login.php
       │                                    │
       ├─ inicio (hero)                     ▼
       ├─ produtos (catálogo)         admin/index.php (?pg=)
       ├─ detalhe (produto)                 │
       ├─ carrinho (sessão)          dashboard, categorias,
       ├─ checkout (endereço)        produtos, usuarios,
       ├─ login/register             fornecedores, documentacao
       ├─ beneficios
       └─ contato
              │
              ▼
    includes/banco_ficticio.php
              │
              ▼
         data/*.json
```

**Fluxo principal (compra):** Produtos → Detalhe → Quantidade/Variação → Carrinho → Total → Checkout → Finalização (grava em `pedidos.json`).

**Observação:** Não há pagamento real nem cálculo de frete. O controle de estoque (baixa automática na compra, bloqueio de venda acima do disponível, nunca negativo) está implementado.

---

## 4. Tecnologias utilizadas

Documentadas **somente** as tecnologias encontradas no código:

| Tecnologia | Evidência no projeto | Uso |
|------------|---------------------|-----|
| PHP | `*.php` | Lógica de negócio, roteamento, sessões, upload |
| HTML5 | Templates em PHP | Estrutura das páginas |
| CSS3 | `admin/admin.css`, `admin/login.css`, Tailwind inline | Estilização |
| JavaScript | `detalhe.php`, `produtos.php` (admin) | Troca de imagem por variação; formulário dinâmico |
| JSON | `data/*.json` | Persistência de dados |
| Tailwind CSS (CDN) | `index.php` linha 129 | Estilos da loja pública |
| Phosphor Icons (CDN) | `index.php` linha 130 | Ícones |
| Google Fonts — Sen (CDN) | `index.php` linha 133 | Tipografia da loja |
| Apache `.htaccess` | `data/.htaccess` | Bloqueio de acesso direto à pasta `data/` |
| PDF manual (PHP puro) | `admin/documentacao_pdf.php` | Geração de PDF sem biblioteca externa |

**Não utilizado:** MySQL, PostgreSQL, frameworks PHP (Laravel, Symfony), React, Vue, Node.js, APIs REST externas, gateways de pagamento.

---

## 5. Arquitetura do sistema

Arquitetura **monolítica em camadas simplificadas**:

| Camada | Componentes |
|--------|-------------|
| Apresentação | `index.php`, `pages/*`, `admin/index.php`, `admin/pages/*` |
| Negócio | Lógica inline nos controladores + `includes/banco_ficticio.php` |
| Dados | Arquivos JSON em `data/` |
| Sessão | `$_SESSION` para carrinho, cliente logado, admin logado |
| Segurança | `includes/seguranca.php` (funções CSRF — **aplicadas em todos os formulários POST**) |

Detalhes completos em [arquitetura.md](arquitetura.md).

---

## 6. Estrutura do projeto

```
htdocs/
├── index.php                 # Front controller da loja pública
├── includes/
│   ├── banco_ficticio.php    # Camada de acesso a dados (JSON)
│   └── seguranca.php         # Funções CSRF e regeneração de sessão
├── pages/                    # Views da loja (incluídas por index.php)
│   ├── produtos.php
│   ├── detalhe.php
│   ├── carrinho.php
│   ├── checkout.php
│   ├── login.php
│   ├── contato.php
│   └── beneficios.php
├── data/                     # Persistência JSON
│   ├── .htaccess             # Deny all (proteção Apache)
│   ├── produtos.json
│   ├── categorias.json
│   ├── clientes.json
│   ├── usuarios.json
│   ├── fornecedores.json
│   └── pedidos.json
├── uploads/produtos/         # Imagens enviadas pelo admin (criada sob demanda)
├── admin/
│   ├── login.php
│   ├── index.php             # Front controller admin
│   ├── documentacao_pdf.php
│   ├── admin.css
│   ├── login.css
│   └── pages/                # Views administrativas
└── docs/                     # Esta documentação acadêmica
```

---

## 7. Atores

| Ator | Existe? | Descrição |
|------|---------|-----------|
| **Visitante** | Sim | Navega catálogo, benefícios e contato sem login |
| **Cliente** | Sim | Registra-se, faz login, adiciona ao carrinho e finaliza pedido |
| **Administrador** | Sim | Acessa `admin/` após login em `usuarios.json` |

Não existe ator "Fornecedor" com login próprio — fornecedores são apenas cadastro administrativo.

---

## 28. Conclusão

A Pixel Store cumpre os requisitos acadêmicos de uma loja virtual com cadastros administrativos, front-end público estruturado e movimento de compra simulado. A persistência em JSON simplifica a implantação em ambiente XAMPP, mas impõe limitações de escalabilidade e concorrência.

A documentação deste diretório descreve **exatamente** o que o código implementa, incluindo funcionalidades parciais (como a edição de produtos, restrita ao estoque) e ausências deliberadas (pagamento, frete, MySQL).

Para verificação item a item dos requisitos da atividade, consulte [checklist-academico.md](checklist-academico.md).

---

*Documentação gerada com base na análise integral do código-fonte. Nenhuma funcionalidade foi inventada.*
