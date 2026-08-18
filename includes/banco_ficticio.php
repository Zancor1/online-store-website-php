<?php
/*
 * ARQUIVO: includes/banco_ficticio.php
 * Objetivo: Centralizar o acesso aos dados do sistema.
 * No futuro, o miolo destas funcoes pode conectar ao MySQL.
 */

function caminhoData(string $arquivo): string {
    return __DIR__ . '/../data/' . $arquivo;
}

function lerJson(string $arquivo): array {
    $caminho = caminhoData($arquivo);
    if (!file_exists($caminho)) {
        return [];
    }
    return json_decode(file_get_contents($caminho), true) ?? [];
}

function salvarJson(string $arquivo, array $dados): bool {
    $caminho = caminhoData($arquivo);
    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $json, LOCK_EX) !== false;
}

function proximoId(array $registros): int {
    if (empty($registros)) {
        return 1;
    }
    return max(array_column($registros, 'id')) + 1;
}

function listarProdutos(): array {
    return lerJson('produtos.json');
}

function buscarProdutoPorId($id): ?array {
    foreach (listarProdutos() as $produto) {
        if ($produto['id'] == $id) {
            return $produto;
        }
    }
    return null;
}

function caminhoProdutos(): string {
    return caminhoData('produtos.json');
}

function listarProdutosAdmin(): array {
    return listarProdutos();
}

function salvarProdutoAdmin(array $novoProduto): bool {
    $produtos = listarProdutosAdmin();
    $novoProduto['id'] = proximoId($produtos);
    $produtos[] = $novoProduto;
    return salvarJson('produtos.json', $produtos);
}

function atualizarProdutoAdmin(int $id, array $dadosAtualizados): bool {
    $produtos = listarProdutosAdmin();
    foreach ($produtos as $chave => $produto) {
        if ($produto['id'] == $id) {
            $produtos[$chave] = array_merge($produto, $dadosAtualizados);
            return salvarJson('produtos.json', $produtos);
        }
    }
    return false;
}

function excluirProdutoAdmin($id): bool {
    $produtos = listarProdutosAdmin();
    foreach ($produtos as $chave => $produto) {
        if ($produto['id'] == $id) {
            unset($produtos[$chave]);
            return salvarJson('produtos.json', array_values($produtos));
        }
    }
    return false;
}

function listarUsuarios(): array {
    return lerJson('usuarios.json');
}

function salvarUsuario(array $novoUsuario): bool {
    $usuarios = listarUsuarios();
    $novoUsuario['id'] = proximoId($usuarios);
    $novoUsuario['senha'] = password_hash($novoUsuario['senha'], PASSWORD_DEFAULT);
    $usuarios[] = $novoUsuario;
    return salvarJson('usuarios.json', $usuarios);
}

function excluirUsuarios($id): bool {
    $usuarios = listarUsuarios();
    foreach ($usuarios as $chave => $user) {
        if ($user['id'] == $id) {
            unset($usuarios[$chave]);
            return salvarJson('usuarios.json', array_values($usuarios));
        }
    }
    return false;
}

function buscarUsuarioPorId($id): ?array {
    foreach (listarUsuarios() as $usuario) {
        if ($usuario['id'] == $id) {
            return $usuario;
        }
    }
    return null;
}

function atualizarUsuario($id, array $dadosAtualizado): bool {
    $usuarios = listarUsuarios();
    foreach ($usuarios as $chave => $usuario) {
        if ($usuario['id'] == $id) {
            if (!empty($dadosAtualizado['senha'])) {
                $dadosAtualizado['senha'] = password_hash($dadosAtualizado['senha'], PASSWORD_DEFAULT);
            } else {
                unset($dadosAtualizado['senha']);
            }
            $usuarios[$chave] = array_merge($usuario, $dadosAtualizado);
            return salvarJson('usuarios.json', $usuarios);
        }
    }
    return false;
}

function caminhoClientes(): string {
    return caminhoData('clientes.json');
}

function listarClientes(): array {
    return lerJson('clientes.json');
}

function buscarClientePorLogin(string $login): ?array {
    foreach (listarClientes() as $cliente) {
        if (strtolower($cliente['login']) === strtolower($login)) {
            return $cliente;
        }
    }
    return null;
}

function cadastrarCliente(string $nome, string $login, string $senha): bool {
    $clientes = listarClientes();
    $clientes[] = [
        'id' => proximoId($clientes),
        'nome' => $nome,
        'login' => $login,
        'senha' => password_hash($senha, PASSWORD_DEFAULT),
        'criado_em' => date('c'),
    ];
    return salvarJson('clientes.json', $clientes);
}

function listarCategorias(): array {
    return lerJson('categorias.json');
}

function salvarCategorias(array $novaCategoria): bool {
    $categorias = listarCategorias();
    $novaCategoria['id'] = proximoId($categorias);
    $categorias[] = $novaCategoria;
    return salvarJson('categorias.json', $categorias);
}

function excluirCategoria($id): bool {
    $categorias = listarCategorias();
    foreach ($categorias as $chave => $cat) {
        if ($cat['id'] == $id) {
            unset($categorias[$chave]);
            return salvarJson('categorias.json', array_values($categorias));
        }
    }
    return false;
}

function buscarCategoriaPorId($id): ?array {
    foreach (listarCategorias() as $categoria) {
        if ($categoria['id'] == $id) {
            return $categoria;
        }
    }
    return null;
}

function atualizarCategorias($id, array $dadosAtualizado): bool {
    $categorias = listarCategorias();
    foreach ($categorias as $chave => $categoria) {
        if ($categoria['id'] == $id) {
            $categorias[$chave] = array_merge($categoria, $dadosAtualizado);
            return salvarJson('categorias.json', $categorias);
        }
    }
    return false;
}

function listarFornecedores(): array {
    return lerJson('fornecedores.json');
}

function salvarFornecedores(array $novoFornecedor): bool {
    $fornecedores = listarFornecedores();
    $novoFornecedor['id'] = proximoId($fornecedores);
    $fornecedores[] = $novoFornecedor;
    return salvarJson('fornecedores.json', $fornecedores);
}

function excluirFornecedores($id): bool {
    $fornecedores = listarFornecedores();
    foreach ($fornecedores as $chave => $forn) {
        if ($forn['id'] == $id) {
            unset($fornecedores[$chave]);
            return salvarJson('fornecedores.json', array_values($fornecedores));
        }
    }
    return false;
}

function buscarFornecedoresPorId($id): ?array {
    foreach (listarFornecedores() as $fornecedor) {
        if ($fornecedor['id'] == $id) {
            return $fornecedor;
        }
    }
    return null;
}

function atualizarFornecedores($id, array $dadosAtualizado): bool {
    $fornecedores = listarFornecedores();
    foreach ($fornecedores as $chave => $fornecedor) {
        if ($fornecedor['id'] == $id) {
            $fornecedores[$chave] = array_merge($fornecedor, $dadosAtualizado);
            return salvarJson('fornecedores.json', $fornecedores);
        }
    }
    return false;
}

function listarPedidos(): array {
    return lerJson('pedidos.json');
}

function salvarPedido(array $pedido): bool {
    $pedidos = listarPedidos();
    $pedido['id'] = proximoId($pedidos);
    $pedidos[] = $pedido;
    return salvarJson('pedidos.json', $pedidos);
}

function montarItensPedido(array $carrinho): array {
    $itens = [];
    $total = 0.0;

    foreach ($carrinho as $chaveItem => $quantidade) {
        $partes = explode(':', (string) $chaveItem, 2);
        $produto = buscarProdutoPorId((int) $partes[0]);
        if (!$produto) {
            continue;
        }

        $indiceVariacao = isset($partes[1]) ? (int) $partes[1] : null;
        $variacao = ($indiceVariacao !== null && isset($produto['variacoes'][$indiceVariacao]))
            ? $produto['variacoes'][$indiceVariacao]
            : null;

        $subtotal = $produto['preco'] * $quantidade;
        $total += $subtotal;

        $itens[] = [
            'produto_id' => $produto['id'],
            'nome' => $produto['nome'],
            'preco_unitario' => $produto['preco'],
            'quantidade' => (int) $quantidade,
            'subtotal' => $subtotal,
            'variacao' => $variacao['nome'] ?? null,
        ];
    }

    return ['itens' => $itens, 'total' => $total];
}
