<?php
require_once "../includes/banco_ficticio.php";

$erro = null;
$sucesso = null;

if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);

    if (excluirProdutoAdmin($idExcluir)) {
        $sucesso = "Produto removido com sucesso!";
    } else {
        $erro = "Erro ao tentar remover o produto.";
    }
}

$categorias = listarCategorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars(trim($_POST['nome_produto'] ?? ''));
    $preco = str_replace(',', '.', trim($_POST['preco_produto'] ?? ''));
    $categoriaId = intval($_POST['categoria_produto'] ?? 0);
    $imagem = '';
    $descricao = htmlspecialchars(trim($_POST['descricao_produto'] ?? ''));
    $categoriaSelecionada = null;
    $erroImagem = null;
    $arquivoImagem = $_FILES['imagem_produto'] ?? null;

    if (!$arquivoImagem || $arquivoImagem['error'] === UPLOAD_ERR_NO_FILE) {
        $erroImagem = 'Selecione uma imagem para o produto.';
    } elseif ($arquivoImagem['error'] !== UPLOAD_ERR_OK) {
        $erroImagem = 'Nao foi possivel enviar a imagem. Tente novamente.';
    } else {
        $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $tipoImagem = mime_content_type($arquivoImagem['tmp_name']);
        if (!isset($tiposPermitidos[$tipoImagem])) $erroImagem = 'Envie uma imagem JPG, PNG, WEBP ou GIF.';
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
    } else {
        $pastaUploads = __DIR__ . '/../../uploads/produtos';
        if (!is_dir($pastaUploads) && !mkdir($pastaUploads, 0755, true)) {
            $erro = 'Nao foi possivel preparar a pasta de imagens.';
        } else {
            $nomeArquivo = bin2hex(random_bytes(12)) . '.' . $tiposPermitidos[$tipoImagem];
            if (move_uploaded_file($arquivoImagem['tmp_name'], $pastaUploads . '/' . $nomeArquivo)) {
                $imagem = 'uploads/produtos/' . $nomeArquivo;
            } else {
                $erro = 'Nao foi possivel salvar a imagem enviada.';
            }
        }

        if (!$erro) {
        $novoProduto = [
            "nome" => $nome,
            "preco" => floatval($preco),
            "categoria" => $categoriaSelecionada['nome'],
            "imagem" => $imagem,
            "descricao" => $descricao
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
            <div class="flex flex-col gap-2">
                <label for="nome_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nome do Produto</label>
                <input type="text" id="nome_produto" name="nome_produto" value="<?php echo $_POST['nome_produto'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <div class="flex flex-col gap-2">
                <label for="preco_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Preço</label>
                <input type="number" step="0.01" min="0" id="preco_produto" name="preco_produto" value="<?php echo $_POST['preco_produto'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <div class="flex flex-col gap-2">
                <label for="categoria_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Categoria</label>
                <select id="categoria_produto" name="categoria_produto" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (($_POST['categoria_produto'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo $cat['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label for="imagem_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Imagem do Produto</label>
                <input type="file" id="imagem_produto" name="imagem_produto" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <div class="flex flex-col gap-2">
                <label for="descricao_produto" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Descrição</label>
                <textarea id="descricao_produto" name="descricao_produto" required rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition"><?php echo $_POST['descricao_produto'] ?? ''; ?></textarea>
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
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Preço</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($produtos)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-sm text-gray-400">Nenhum produto cadastrado ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produtos as $produto): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-sm text-gray-400 font-mono">#<?php echo $produto['id']; ?></td>
                            <td class="p-4 font-bold text-gray-700"><?php echo $produto['nome']; ?></td>
                            <td class="p-4 text-sm text-gray-500"><?php echo $produto['categoria'] ?? 'Sem categoria'; ?></td>
                            <td class="p-4 text-sm text-gray-500">R$ <?php echo number_format(floatval($produto['preco'] ?? 0), 2, ',', '.'); ?></td>
                            <td class="p-4 text-right">
                                <div class="action-group">
                                    <a href="index.php?pg=produtos&excluir=<?php echo $produto['id']; ?>" class="action-link action-remove" title="Remover Produto">
                                        Remover
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
