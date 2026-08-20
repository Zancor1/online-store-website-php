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

/**
 * Ajusta manualmente o estoque de um produto (uso administrativo).
 * Nunca permite gravar um valor negativo.
 */
function atualizarEstoqueAdmin(int $id, int $novoEstoque): bool {
    $novoEstoque = max(0, $novoEstoque);
    return atualizarProdutoAdmin($id, ['estoque' => $novoEstoque]);
}

/**
 * Confere e dá baixa no estoque de todos os itens do carrinho de forma
 * atomica (usando lock exclusivo no arquivo de produtos), evitando que duas
 * compras simultaneas vendam mais unidades do que existem em estoque.
 *
 * Retorna:
 *   ['ok' => true,  'itens' => [...], 'total' => float]
 *   ['ok' => false, 'erro' => 'mensagem para o usuario']
 */
function processarFinalizacaoCompra(array $carrinho): array {
    if (empty($carrinho)) {
        return ['ok' => false, 'erro' => 'Seu carrinho esta vazio.'];
    }

    $caminho = caminhoProdutos();
    $handle = fopen($caminho, 'c+');
    if ($handle === false) {
        return ['ok' => false, 'erro' => 'Nao foi possivel acessar o estoque no momento.'];
    }

    // Trava exclusiva: nenhuma outra requisicao consegue ler/gravar o
    // arquivo de produtos enquanto este bloco estiver em execucao, o que
    // evita a condicao de corrida em compras concorrentes do mesmo produto.
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return ['ok' => false, 'erro' => 'Nao foi possivel processar a compra. Tente novamente.'];
    }

    $conteudo = stream_get_contents($handle);
    $produtos = json_decode($conteudo, true);
    if (!is_array($produtos)) {
        $produtos = [];
    }

    // Indexa por id para checagem/gravacao rapida.
    $indicePorId = [];
    foreach ($produtos as $chave => $produto) {
        $indicePorId[$produto['id']] = $chave;
    }

    $itens = [];
    $total = 0.0;

    // 1) Primeiro valida se ha estoque suficiente para TODOS os itens.
    foreach ($carrinho as $chaveItem => $quantidade) {
        $quantidade = (int) $quantidade;
        $partes = explode(':', (string) $chaveItem, 2);
        $produtoId = (int) $partes[0];

        if (!isset($indicePorId[$produtoId])) {
            continue; // produto removido do catalogo nao entra no pedido
        }
        $produto = $produtos[$indicePorId[$produtoId]];
        $estoqueAtual = (int) ($produto['estoque'] ?? 0);

        if ($quantidade < 1) {
            continue;
        }

        if ($estoqueAtual < $quantidade) {
            flock($handle, LOCK_UN);
            fclose($handle);
            return [
                'ok' => false,
                'erro' => 'Estoque insuficiente para "' . $produto['nome'] . '". Disponivel: ' . $estoqueAtual . '.',
            ];
        }
    }

    // 2) Estoque confirmado para todos os itens: agora da baixa de fato.
    foreach ($carrinho as $chaveItem => $quantidade) {
        $quantidade = (int) $quantidade;
        if ($quantidade < 1) {
            continue;
        }
        $partes = explode(':', (string) $chaveItem, 2);
        $produtoId = (int) $partes[0];
        if (!isset($indicePorId[$produtoId])) {
            continue;
        }
        $chaveProduto = $indicePorId[$produtoId];
        $produto = $produtos[$chaveProduto];

        $indiceVariacao = isset($partes[1]) ? (int) $partes[1] : null;
        $variacao = ($indiceVariacao !== null && isset($produto['variacoes'][$indiceVariacao]))
            ? $produto['variacoes'][$indiceVariacao]
            : null;

        // Baixa de estoque: nunca deixa o valor final ficar negativo.
        $novoEstoque = max(0, (int) $produto['estoque'] - $quantidade);
        $produtos[$chaveProduto]['estoque'] = $novoEstoque;

        $subtotal = $produto['preco'] * $quantidade;
        $total += $subtotal;

        $itens[] = [
            'produto_id' => $produto['id'],
            'nome' => $produto['nome'],
            'preco_unitario' => $produto['preco'],
            'quantidade' => $quantidade,
            'subtotal' => $subtotal,
            'variacao' => $variacao['nome'] ?? null,
        ];
    }

    if (empty($itens)) {
        flock($handle, LOCK_UN);
        fclose($handle);
        return ['ok' => false, 'erro' => 'Nenhum item valido encontrado no carrinho.'];
    }

    // Regrava o arquivo inteiro ainda dentro da trava exclusiva.
    $json = json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, $json);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return ['ok' => true, 'itens' => $itens, 'total' => $total];
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
