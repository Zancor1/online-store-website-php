<?php
require_once "../includes/banco_ficticio.php";
require_once "../includes/seguranca.php";

$erro = null;
$sucesso = null;

function salvarImagemProduto($arquivo, $pastaUploads, $tiposPermitidos) {
    if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
    $tipoImagem = mime_content_type($arquivo['tmp_name']);
    if (!isset($tiposPermitidos[$tipoImagem])) return null;
    $nomeArquivo = bin2hex(random_bytes(12)) . '.' . $tiposPermitidos[$tipoImagem];
    if (!move_uploaded_file($arquivo['tmp_name'], $pastaUploads . '/' . $nomeArquivo)) return null;
    return 'uploads/produtos/' . $nomeArquivo;
}

// Exclusao de produto (form POST + CSRF, nao mais link GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir_produto') {
    if (!csrfValidar()) {
        $erro = "Sessao expirada ou requisicao invalida. Tente novamente.";
    } else {
        $idExcluir = intval($_POST['id'] ?? 0);
        if (excluirProdutoAdmin($idExcluir)) {
            $sucesso = "Produto removido com sucesso!";
        } else {
            $erro = "Erro ao tentar remover o produto.";
        }
    }
}

// Ajuste rapido de estoque (form POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'atualizar_estoque') {
    if (!csrfValidar()) {
        $erro = "Sessao expirada ou requisicao invalida. Tente novamente.";
    } else {
        $idEstoque = intval($_POST['id'] ?? 0);
        $novoEstoque = filter_input(INPUT_POST, 'novo_estoque', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($novoEstoque === false || $novoEstoque === null) {
            $erro = "Informe uma quantidade de estoque valida (numero inteiro maior ou igual a zero).";
        } elseif (atualizarEstoqueAdmin($idEstoque, $novoEstoque)) {
            $sucesso = "Estoque atualizado com sucesso!";
        } else {
            $erro = "Erro ao tentar atualizar o estoque.";
        }
    }
}

$categorias = listarCategorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? 'cadastrar_produto') === 'cadastrar_produto' && isset($_POST['nome_produto']) && !csrfValidar()) {
    $erro = "Sessao expirada ou requisicao invalida. Atualize a pagina e tente novamente.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? 'cadastrar_produto') === 'cadastrar_produto' && isset($_POST['nome_produto'])) {
    $nome = htmlspecialchars(trim($_POST['nome_produto'] ?? ''));
    $preco = str_replace(',', '.', trim($_POST['preco_produto'] ?? ''));
    $categoriaId = intval($_POST['categoria_produto'] ?? 0);
    $estoqueInformado = filter_input(INPUT_POST, 'estoque_produto', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $imagem = '';
    $descricao = htmlspecialchars(trim($_POST['descricao_produto'] ?? ''));
    $categoriaSelecionada = null;
    $erroImagem = null;
    $arquivoImagem = $_FILES['imagem_produto'] ?? null;
    $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $usarVariacoes = !empty($_POST['usar_variacoes']);
    $nomesVariacoes = $_POST['variacao_nome'] ?? [];
    $arquivosVariacoes = $_FILES['variacao_imagem'] ?? null;

    if (!$usarVariacoes && (!$arquivoImagem || $arquivoImagem['error'] === UPLOAD_ERR_NO_FILE)) {
        $erroImagem = 'Selecione uma imagem para o produto.';
    } elseif (!$usarVariacoes && $arquivoImagem['error'] !== UPLOAD_ERR_OK) {
        $erroImagem = 'Nao foi possivel enviar a imagem. Tente novamente.';
    } elseif (!$usarVariacoes && !isset($tiposPermitidos[mime_content_type($arquivoImagem['tmp_name'])])) {
        $erroImagem = 'Envie uma imagem JPG, PNG, WEBP ou GIF.';
    } elseif ($usarVariacoes) {
        $quantidadeVariacoes = is_array($nomesVariacoes) ? count($nomesVariacoes) : 0;
        if ($quantidadeVariacoes < 1) {
            $erroImagem = 'Adicione pelo menos uma variacao.';
        }
        for ($i = 0; !$erroImagem && $i < $quantidadeVariacoes; $i++) {
            $arquivo = $arquivosVariacoes ? ['name' => $arquivosVariacoes['name'][$i] ?? '', 'tmp_name' => $arquivosVariacoes['tmp_name'][$i] ?? '', 'error' => $arquivosVariacoes['error'][$i] ?? UPLOAD_ERR_NO_FILE, 'size' => $arquivosVariacoes['size'][$i] ?? 0] : null;
            if (trim($nomesVariacoes[$i] ?? '') === '' || !$arquivo || $arquivo['error'] !== UPLOAD_ERR_OK || !isset($tiposPermitidos[mime_content_type($arquivo['tmp_name'])])) {
                $erroImagem = 'Cada variacao precisa ter um nome e uma imagem JPG, PNG, WEBP ou GIF.';
            }
        }
    }

    foreach ($categorias as $cat) {
        if ($cat['id'] == $categoriaId) {
            $categoriaSelecionada = $cat;
            break;
        }
    }

    if ($erroImagem) {
        $erro = $erroImagem;
    } elseif (empty($nome) || empty($preco) || !$categoriaSelecionada || empty($descricao)) {
        $erro = "Nome, preço, categoria e descrição são obrigatórios.";
    } elseif (!is_numeric($preco) || floatval($preco) < 0) {
        $erro = "Informe um preço válido.";
    } elseif ($estoqueInformado === false || $estoqueInformado === null) {
        $erro = "Informe uma quantidade de estoque válida (número inteiro maior ou igual a zero).";
    } else {
        $pastaUploads = __DIR__ . '/../../uploads/produtos';
        if (!is_dir($pastaUploads) && !mkdir($pastaUploads, 0755, true)) {
            $erro = 'Nao foi possivel preparar a pasta de imagens.';
        } else {
            $variacoes = [];
            if ($usarVariacoes) {
                foreach ($nomesVariacoes as $i => $nomeVariacao) {
                    $arquivo = ['name' => $arquivosVariacoes['name'][$i], 'tmp_name' => $arquivosVariacoes['tmp_name'][$i], 'error' => $arquivosVariacoes['error'][$i], 'size' => $arquivosVariacoes['size'][$i]];
                    $imagemVariacao = salvarImagemProduto($arquivo, $pastaUploads, $tiposPermitidos);
                    if (!$imagemVariacao) { $erro = 'Nao foi possivel salvar uma das imagens das variacoes.'; break; }
                    $variacoes[] = ['nome' => htmlspecialchars(trim($nomeVariacao)), 'imagem' => $imagemVariacao];
                }
                $imagem = $variacoes[0]['imagem'] ?? '';
            } else {
                $imagem = salvarImagemProduto($arquivoImagem, $pastaUploads, $tiposPermitidos);
                if (!$imagem) $erro = 'Nao foi possivel salvar a imagem enviada.';
            }
        }

        if (!$erro) {
        $novoProduto = [
            "nome" => $nome,
            "preco" => floatval($preco),
            "categoria" => $categoriaSelecionada['nome'],
            "imagem" => $imagem,
            "descricao" => $descricao,
            "estoque" => $estoqueInformado,
            "variacoes" => $variacoes ?? []
        ];

        if (salvarProdutoAdmin($novoProduto)) {
            $sucesso = "Produto <strong>$nome</strong> cadastrado com sucesso!";
            $_POST = array();
        } else {
            $erro = "Erro técnico ao tentar salvar o produto.";
        }
        }
    }
}

$produtos = listarProdutosAdmin();
?>

<div class="mb-10">
    <h1 class="text-3xl font-black text-gray-800">Produtos do Sistema</h1>
    <p class="text-gray-500 text-sm">Cadastre produtos, escolha a categoria e remova itens da loja.</p>
</div>

<?php if ($erro && !isset($_POST['nome_produto'])): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm flex items-center gap-3">
        <div><?php echo $erro; ?></div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="bg-white border border-gray-100 rounded-3xl p-6 h-fit shadow-sm">
        <h2 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
            Novo Produto
        </h2>

        <?php if ($sucesso): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 p-3 rounded-xl text-xs font-semibold">
                <?php echo $sucesso; ?>
            </div>
        <?php endif; ?>

        <?php if ($erro && isset($_POST['nome_produto'])): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-xl text-xs font-semibold">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($categorias)): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-xl text-xs font-semibold">
                Cadastre uma categoria antes de adicionar produtos.
            </div>
        <?php endif; ?>

        <form action="index.php?pg=produtos" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrfCampo(); ?>
            <input type="hidden" name="acao" value="cadastrar_produto">
            <div class="flex flex-col gap-2">
                <label for="nome_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nome do Produto</label>
                <input type="text" id="nome_produto" name="nome_produto" value="<?php echo htmlspecialchars($_POST['nome_produto'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <div class="flex flex-col gap-2">
                <label for="preco_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Preço</label>
                <input type="number" step="0.01" min="0" id="preco_produto" name="preco_produto" value="<?php echo htmlspecialchars($_POST['preco_produto'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <div class="flex flex-col gap-2">
                <label for="estoque_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Estoque (unidades)</label>
                <input type="number" step="1" min="0" id="estoque_produto" name="estoque_produto" value="<?php echo htmlspecialchars($_POST['estoque_produto'] ?? '0'); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <div class="flex flex-col gap-2">
                <label for="categoria_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Categoria</label>
                <select id="categoria_produto" name="categoria_produto" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (($_POST['categoria_produto'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label for="imagem_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Imagem do Produto</label>
                <input type="file" id="imagem_produto" name="imagem_produto" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
                <label class="flex cursor-pointer items-center gap-3 text-sm font-bold text-gray-700">
                    <input type="checkbox" id="usar_variacoes" name="usar_variacoes" value="1" <?php echo !empty($_POST['usar_variacoes']) ? 'checked' : ''; ?> class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                    Este produto possui variações (tamanho, cor, modelo...)
                </label>
                <p class="mt-2 text-xs leading-5 text-gray-500">Ao ativar, informe cada opção e envie uma foto específica para ela. Ex.: tamanho 38, tamanho 39 e tamanho 40.</p>
                <div id="variacoes_campos" class="mt-4 space-y-3" <?php echo empty($_POST['usar_variacoes']) ? 'hidden' : ''; ?>></div>
                <button type="button" id="adicionar_variacao" class="mt-3 rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100" <?php echo empty($_POST['usar_variacoes']) ? 'hidden' : ''; ?>>+ Adicionar variação</button>
            </div>

            <div class="flex flex-col gap-2">
                <label for="descricao_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Descrição</label>
                <textarea id="descricao_produto" name="descricao_produto" required rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition"><?php echo htmlspecialchars($_POST['descricao_produto'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition text-sm shadow-lg shadow-indigo-100 flex items-center justify-center gap-2" <?php echo empty($categorias) ? 'disabled' : ''; ?>>
                Cadastrar Produto
            </button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Produto</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Categoria</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Variações</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Preço</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estoque</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($produtos)): ?>
                    <tr>
                        <td colspan="7" class="p-8 text-center text-sm text-gray-400">Nenhum produto cadastrado ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produtos as $produto): $estoqueProduto = (int) ($produto['estoque'] ?? 0); ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-sm text-gray-400 font-mono">#<?php echo (int) $produto['id']; ?></td>
                            <td class="p-4 font-bold text-gray-700"><?php echo htmlspecialchars($produto['nome']); ?></td>
                            <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($produto['categoria'] ?? 'Sem categoria'); ?></td>
                            <td class="p-4 text-sm text-gray-500"><?php echo empty($produto['variacoes']) ? 'Produto único' : count($produto['variacoes']) . ' opções'; ?></td>
                            <td class="p-4 text-sm text-gray-500">R$ <?php echo number_format(floatval($produto['preco'] ?? 0), 2, ',', '.'); ?></td>
                            <td class="p-4 text-sm">
                                <form action="index.php?pg=produtos" method="POST" class="flex items-center gap-1">
                                    <?php echo csrfCampo(); ?>
                                    <input type="hidden" name="acao" value="atualizar_estoque">
                                    <input type="hidden" name="id" value="<?php echo (int) $produto['id']; ?>">
                                    <input type="number" name="novo_estoque" min="0" step="1" value="<?php echo $estoqueProduto; ?>" class="w-20 rounded-lg border <?php echo $estoqueProduto <= 0 ? 'border-red-300 text-red-600' : 'border-gray-200'; ?> p-1.5 text-sm">
                                    <button type="submit" class="action-link action-edit" title="Salvar estoque">Salvar</button>
                                </form>
                                <?php if ($estoqueProduto <= 0): ?>
                                    <span class="mt-1 inline-block text-[10px] font-bold text-red-600">Esgotado</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right">
                                <div class="action-group">
                                    <form action="index.php?pg=produtos" method="POST" onsubmit="return confirm('Remover este produto?');">
                                        <?php echo csrfCampo(); ?>
                                        <input type="hidden" name="acao" value="excluir_produto">
                                        <input type="hidden" name="id" value="<?php echo (int) $produto['id']; ?>">
                                        <button type="submit" class="action-link action-remove" title="Remover Produto">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(() => {
    const checkbox = document.getElementById('usar_variacoes');
    const imagemPrincipal = document.getElementById('imagem_produto');
    const campos = document.getElementById('variacoes_campos');
    const botao = document.getElementById('adicionar_variacao');
    const adicionar = () => {
        const linha = document.createElement('div');
        linha.className = 'grid grid-cols-[1fr_1.4fr_auto] gap-2 items-center';
        linha.innerHTML = '<input type="text" name="variacao_nome[]" placeholder="Ex.: Tamanho 38" class="min-w-0 rounded-lg border border-gray-200 bg-white p-2 text-sm"><input type="file" name="variacao_imagem[]" accept="image/jpeg,image/png,image/webp,image/gif" class="min-w-0 rounded-lg border border-gray-200 bg-white p-2 text-xs"><button type="button" class="remover_variacao rounded-lg p-2 text-red-600 hover:bg-red-50" aria-label="Remover variação">×</button>';
        linha.querySelector('.remover_variacao').addEventListener('click', () => linha.remove());
        campos.appendChild(linha);
    };
    const alternar = () => {
        const ativo = checkbox.checked;
        campos.hidden = !ativo;
        botao.hidden = !ativo;
        imagemPrincipal.required = !ativo;
        if (ativo && !campos.children.length) adicionar();
    };
    checkbox.addEventListener('change', alternar);
    botao.addEventListener('click', adicionar);
    alternar();
})();
</script>
