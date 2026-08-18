# Diagramas UML — Pixel Store

Diagramas em Mermaid baseados no código real.

---

## 1. Diagrama de casos de uso

```mermaid
flowchart LR
    subgraph Atores
        V((Visitante))
        C((Cliente))
        A((Administrador))
    end

    subgraph Loja["Área Pública"]
        UC1[Navegar catálogo]
        UC2[Ver detalhe produto]
        UC3[Contato]
        UC4[Benefícios]
        UC5[Registrar conta]
        UC6[Login cliente]
        UC7[Gerenciar carrinho]
        UC8[Finalizar compra]
    end

    subgraph Admin["Painel Admin"]
        UC9[Login admin]
        UC10[Dashboard]
        UC11[CRUD Categorias]
        UC12[Cadastrar/Excluir Produtos]
        UC13[CRUD Usuários]
        UC14[CRUD Fornecedores]
        UC15[Documentação/PDF]
    end

    V --> UC1
    V --> UC2
    V --> UC3
    V --> UC4
    V --> UC5

    C --> UC1
    C --> UC2
    C --> UC3
    C --> UC4
    C --> UC6
    C --> UC7
    C --> UC8

    A --> UC9
    A --> UC10
    A --> UC11
    A --> UC12
    A --> UC13
    A --> UC14
    A --> UC15
```

---

## 2. Diagrama de sequência — Login cliente

```mermaid
sequenceDiagram
    actor Cliente
    participant Browser
    participant index as index.php
    participant BF as banco_ficticio.php
    participant JSON as clientes.json
    participant Sessao as $_SESSION

    Cliente->>Browser: Preenche email/senha
    Browser->>index: POST acao=login_cliente
    index->>BF: buscarClientePorLogin(email)
    BF->>JSON: lerJson(clientes.json)
    JSON-->>BF: array clientes
    BF-->>index: cliente ou null
    alt credenciais válidas
        index->>index: password_verify(senha, hash)
        index->>Sessao: regenerarSessao()
        index->>Sessao: cliente = {nome, login}
        index-->>Browser: Redirect ?pg=produtos
    else inválidas
        index->>Sessao: erro_login
        index-->>Browser: Redirect ?pg=login
    end
```

---

## 3. Diagrama de sequência — Cadastro cliente

```mermaid
sequenceDiagram
    actor Visitante
    participant Browser
    participant index as index.php
    participant BF as banco_ficticio.php
    participant JSON as clientes.json

    Visitante->>Browser: Register (nome, email, senha)
    Browser->>index: POST acao=registrar_cliente
    index->>index: Validar nome, email, senha>=6
    index->>BF: buscarClientePorLogin(email)
    BF-->>index: null (email livre)
    index->>BF: cadastrarCliente(nome, email, senha)
    BF->>BF: password_hash(senha)
    BF->>JSON: salvarJson(clientes.json)
    JSON-->>BF: ok
    BF-->>index: true
    index->>index: regenerarSessao + login automático
    index-->>Browser: Redirect ?pg=produtos
```

---

## 4. Diagrama de sequência — Cadastro de produto (admin)

```mermaid
sequenceDiagram
    actor Admin
    participant Browser
    participant Prod as admin/pages/produtos.php
    participant BF as banco_ficticio.php
    participant FS as uploads/produtos/
    participant JSON as produtos.json

    Admin->>Browser: Preenche form + imagem
    Browser->>Prod: POST multipart/form-data
    Prod->>Prod: Validar campos, MIME, categoria
    alt com variações
        loop cada variação
            Prod->>FS: move_uploaded_file(imagem)
        end
    else produto simples
        Prod->>FS: move_uploaded_file(imagem)
    end
    Prod->>BF: salvarProdutoAdmin(produto)
    BF->>BF: proximoId()
    BF->>JSON: salvarJson(produtos.json)
    JSON-->>BF: ok
    BF-->>Prod: true
    Prod-->>Browser: Mensagem sucesso + tabela atualizada
```

---

## 5. Diagrama de sequência — Compra

```mermaid
sequenceDiagram
    actor Cliente
    participant Det as pages/detalhe.php
    participant index as index.php
    participant Carr as pages/carrinho.php
    participant Check as pages/checkout.php
    participant BF as banco_ficticio.php
    participant JSON as pedidos.json
    participant Sessao as $_SESSION

    Cliente->>Det: Seleciona qty + variação
    Det->>index: POST acao=adicionar
    index->>Sessao: carrinho[chave] += qty
    index-->>Cliente: Redirect carrinho

    Cliente->>Carr: Revisa total
    Cliente->>Check: Finalizar compra
    Cliente->>Check: Preenche endereço
    Check->>index: POST acao=finalizar
    index->>BF: montarItensPedido(carrinho)
    BF-->>index: {itens, total}
    index->>BF: salvarPedido(dados)
    BF->>JSON: salvarJson(pedidos.json)
    index->>Sessao: carrinho = []
    index-->>Cliente: Redirect carrinho + msg sucesso
```

---

## 6. Diagrama de atividades — Compra

```mermaid
flowchart TD
    A([Início]) --> B{Navegar produtos}
    B --> C[Abrir detalhe]
    C --> D{Cliente logado?}
    D -->|Não| E[Redirect login]
    E --> C
    D -->|Sim| F[Definir quantidade/variação]
    F --> G[Adicionar ao carrinho]
    G --> H{Carrinho ok?}
    H -->|Remover item| I[POST remover]
    I --> H
    H --> J[Ir ao checkout]
    J --> K{Endereço preenchido?}
    K -->|Não| L[Permanece checkout]
    K -->|Sim| M[Salvar pedido JSON]
    M --> N[Limpar carrinho]
    N --> O([Pedido finalizado])
```

---

## 7. Diagrama de classes/entidades

```mermaid
classDiagram
    class UsuarioAdmin {
        +int id
        +string nome
        +string login
        +string senha
        +bool ativo
    }

    class Cliente {
        +int id
        +string nome
        +string login
        +string senha
        +string criado_em
    }

    class Categoria {
        +int id
        +string nome
    }

    class Produto {
        +int id
        +string nome
        +float preco
        +string categoria
        +string imagem
        +string descricao
        +Variacao[] variacoes
    }

    class Variacao {
        +string nome
        +string imagem
    }

    class Fornecedor {
        +int id
        +string nome_fornecedor
        +string cnpj_fornecedor
        +bool ativo
    }

    class Pedido {
        +int id
        +object cliente
        +object endereco
        +ItemPedido[] itens
        +float total
        +string status
        +string criado_em
    }

    class ItemPedido {
        +int produto_id
        +string nome
        +float preco_unitario
        +int quantidade
        +float subtotal
        +string variacao
    }

    class CarrinhoSessao {
        +map itens
    }

    Produto *-- Variacao
    Pedido *-- ItemPedido
    Cliente ..> Pedido : gera
    Categoria ..> Produto : nome referencia
```

---

## 8. Diagrama de arquitetura (implantação)

```mermaid
flowchart TB
    subgraph ClienteBrowser["Cliente (Browser)"]
        UI[Loja / Admin UI]
    end

    subgraph XAMPP["XAMPP"]
        subgraph Apache
            PHP[PHP Interpreter]
        end
        subgraph FS["Filesystem"]
            DATA[data/*.json]
            UP[uploads/]
        end
    end

    UI -->|HTTP| Apache
    Apache --> PHP
    PHP --> DATA
    PHP --> UP
    PHP -->|session.save_path| SESS[(Sessões PHP)]
```

---

## 9. Diagrama de sequência — Login administrador

```mermaid
sequenceDiagram
    actor Admin
    participant Login as admin/login.php
    participant BF as banco_ficticio.php
    participant JSON as usuarios.json
    participant Sessao as $_SESSION

    Admin->>Login: POST usuario/senha
    Login->>BF: listarUsuarios()
    BF->>JSON: lerJson
    JSON-->>BF: usuarios
    Login->>Login: password_verify
    alt ativo = true
        Login->>Sessao: regenerarSessao()
        Login->>Sessao: logado=true, usuario_nome
        Login-->>Admin: Redirect index.php
    else inativo
        Login-->>Admin: Erro conta desativada
    end
```
