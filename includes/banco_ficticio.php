    <?php
    /*
    * ARQUIVO: includes/banco_ficticio.php
    * Objetivo: Centralizar o acesso aos dados do sistema
    * no futuro, mudaremos o miolo destas funcoes para conectar ao mysql
    */
    //Funcao auxiliar interna para ler o arquivo json e desenvolver um array

    function lerBancoJson(){
        $caminho = "data/produtos.json";
        //Verificacao de seguranca caso o arquivo suma
        if (!file_exists($caminho)){
            return[];
        }
        $conteudo = file_get_contents($caminho);
        return json_decode($conteudo, true) ?? [];
    }
    //Retorne TODOS os produtos para a vitrine
    function listarProdutos() {
        return lerBancoJson();
    }
    //Busca e retorna apenas um produto pelo uid
    function buscarProdutoPorId($id){
        $produto = lerBancoJson();
        foreach($produto as $p){
            if ($p['id'] == $id){
                return $p;
                //Encontrou o produto, retorna ele
            }
        }
        return null; //se rodar o loop todo e nao achar nada
    }

    function listarUsuarios(){
        $caminho = "../data/usuarios.json";
        //Verificacao de seguranca caso o arquivo suma
        if (!file_exists($caminho)){
            return[];
        }
        $conteudo = file_get_contents($caminho);
        return json_decode($conteudo, true) ?? [];
    }
    function salvarUsuario($novoUsuario) {
        $caminho = "../data/usuarios.json";
        $usuarioAtuais = listarUsuarios();

        //logica de id automatico
        if(!empty($usuarioAtuais)) {
            $ultimo = end($usuarioAtuais);
            $novoUsuario['id'] = $ultimo['id'] + 1;
        } else {
            $novoUsuario['id'] = 1;
        }
        $novoUsuario['senha'] = password_hash($novoUsuario['senha'], PASSWORD_DEFAULT);

        $usuarioAtuais[] = $novoUsuario;
        $jsonTexto = json_encode($usuarioAtuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($caminho, $jsonTexto) !== false;
    }

    function excluirUsuarios($id) {
    $caminho = "../data/usuarios.json";
    $usuarios = listarUsuarios();

    foreach ($usuarios as $chave => $user) {
        if ($user['id'] == $id) {
            unset($usuarios[$chave]);
            
            $usuarios = array_values($usuarios);
            $jsonTexto = json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}

    function buscarUsuarioPorId($id) {
        $usuarios = listarUsuarios();
        foreach ($usuarios as $u) {
        if ($u['id'] == $id) {
            return $u;
            }
        }
        return null;
    }

function atualizarUsuario($id, $dadosAtualizado) {
    $caminho = "../data/usuarios.json";
    $usuarios = listarUsuarios();

    foreach($usuarios as $chave => $u) {
        if ($u['id'] == $id) {
            
            if(!empty($dadosAtualizado['senha'])) {
                $dadosAtualizado['senha'] = password_hash($dadosAtualizado['senha'], PASSWORD_DEFAULT);  
            } else {
                unset($dadosAtualizado['senha']);
            }
            $usuarios[$chave] = array_merge($u, $dadosAtualizado);

            $jsonTexto = json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}

    function listarCategorias(){
        $caminho = "../data/categorias.json";
        //Verificacao de seguranca caso o arquivo suma
        if (!file_exists($caminho)){
            return[];
        }
        $conteudo = file_get_contents($caminho);
        return json_decode($conteudo, true) ?? [];
    }


    function salvarCategorias($novaCategoria) {
        $caminho = "../data/categorias.json";
        $categoriaAtuais = listarCategorias();

        //logica de id automatico
        if(!empty($categoriaAtuais)) {
            $ultimo = end($categoriaAtuais);
            $novaCategoria['id'] = $ultimo['id'] + 1;
        } else {
            $novaCategoria['id'] = 1;
        }

        $categoriaAtuais[] = $novaCategoria;
        $jsonTexto = json_encode($categoriaAtuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($caminho, $jsonTexto) !== false;
    }

    function excluirCategoria($id) {
    $caminho = "../data/categorias.json";
    $categorias = listarCategorias();

    foreach ($categorias as $chave => $cat) {
        if ($cat['id'] == $id) {
            unset($categorias[$chave]);
            
            $categorias = array_values($categorias);
            $jsonTexto = json_encode($categorias, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}

    function buscarCategoriaPorId($id) {
        $categorias = listarCategorias();
        foreach ($categorias as $u) {
        if ($u['id'] == $id) {
            return $u;
            }
        }
        return null;
    }

function atualizarCategorias($id, $dadosAtualizado) {
    $caminho = "../data/categorias.json";
    $categorias = listarCategorias();

    foreach($categorias as $chave => $u) {
        if ($u['id'] == $id) {
            
            $categorias[$chave] = array_merge($u, $dadosAtualizado);

            $jsonTexto = json_encode($categorias, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}


function listarFornecedores(){
        $caminho = "../data/fornecedores.json";
        //Verificacao de seguranca caso o arquivo suma
        if (!file_exists($caminho)){
            return[];
        }
        $conteudo = file_get_contents($caminho);
        return json_decode($conteudo, true) ?? [];
    }


    function salvarFornecedores($novaCategoria) {
        $caminho = "../data/fornecedores.json";
        $categoriaAtuais = listarFornecedores();

        //logica de id automatico
        if(!empty($categoriaAtuais)) {
            $ultimo = end($categoriaAtuais);
            $novaCategoria['id'] = $ultimo['id'] + 1;
        } else {
            $novaCategoria['id'] = 1;
        }

        $categoriaAtuais[] = $novaCategoria;
        $jsonTexto = json_encode($categoriaAtuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($caminho, $jsonTexto) !== false;
    }

    function excluirFornecedores($id) {
    $caminho = "../data/fornecedores.json";
    $fornecedores = listarFornecedores();

    foreach ($fornecedores as $chave => $forn) {
        if ($forn['id'] == $id) {
            unset($fornecedores[$chave]);
            
            $fornecedores = array_values($fornecedores);
            $jsonTexto = json_encode($fornecedores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}

    function buscarFornecedoresPorId($id) {
        $fornecedores = listarFornecedores();
        foreach ($fornecedores as $u) {
        if ($u['id'] == $id) {
            return $u;
            }
        }
        return null;
    }

function atualizarFornecedores($id, $dadosAtualizado) {
    $caminho = "../data/fornecedores.json";
    $fornecedores = listarFornecedores();

    foreach($fornecedores as $chave => $u) {
        if ($u['id'] == $id) {
            
            $fornecedores[$chave] = array_merge($u, $dadosAtualizado);

            $jsonTexto = json_encode($fornecedores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}

// MARCADOR PRA TU IA

function caminhoProdutos() {
    return __DIR__ . "/../data/produtos.json";
}

function listarProdutosAdmin() {
    $caminho = caminhoProdutos();

    if (!file_exists($caminho)) {
        return [];
    }

    $conteudo = file_get_contents($caminho);
    return json_decode($conteudo, true) ?? [];
}

function salvarProdutoAdmin($novoProduto) {
    $caminho = caminhoProdutos();
    $produtosAtuais = listarProdutosAdmin();

    if (!empty($produtosAtuais)) {
        $ultimo = end($produtosAtuais);
        $novoProduto['id'] = ($ultimo['id'] ?? 0) + 1;
    } else {
        $novoProduto['id'] = 1;
    }

    $produtosAtuais[] = $novoProduto;
    $jsonTexto = json_encode($produtosAtuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $jsonTexto) !== false;
}

function excluirProdutoAdmin($id) {
    $caminho = caminhoProdutos();
    $produtos = listarProdutosAdmin();

    foreach ($produtos as $chave => $produto) {
        if ($produto['id'] == $id) {
            unset($produtos[$chave]);

            $produtos = array_values($produtos);
            $jsonTexto = json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }

    return false;
}

?>
