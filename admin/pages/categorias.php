<?php
require_once "../includes/banco_ficticio.php";
require_once "../includes/seguranca.php";
$erro = null;
$sucesso = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir_categoria') {
    if (!csrfValidar()) {
        $erro = "Sessão expirada ou requisição inválida. Tente novamente.";
    } else {
        $idExcluir = intval($_POST['id'] ?? 0);
        if (excluirCategoria($idExcluir)) {
            $sucesso = "Categoria excluída com sucesso!";
        } else {
            $erro = "Erro ao tentar excluir a categoria.";
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? 'cadastrar_categoria') === 'cadastrar_categoria' && isset($_POST['nome_categoria']) && !csrfValidar()) {
    $erro = "Sessão expirada ou requisição inválida. Atualize a página e tente novamente.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? 'cadastrar_categoria') === 'cadastrar_categoria' && isset($_POST['nome_categoria'])) {
    $nome = htmlspecialchars(trim($_POST['nome_categoria'] ?? ''));

    if (empty($nome)) {
        $erro = "Todos os campos são obrigatórios para o cadastro.";
    } else {
        $categoriasExistentes = listarCategorias();
        $categoriaJaExiste = false;

        foreach ($categoriasExistentes as $u) {
            if (strtolower($u['nome']) === strtolower($nome)) {
                $categoriaJaExiste = true;
                break;
            }
        }

        if ($categoriaJaExiste) {
            $erro = "Esta categoria já está cadastrada.";
        } else {
            $novaCategoria = [
                "nome" => $nome
            ];

            if (salvarCategorias($novaCategoria)){
                $sucesso = "Categoria <strong>$nome</strong> cadastrada com sucesso!";
                $_POST = array();
            } else {
                $erro = "Erro técnico ao tentar salvar a categoria.";
            }
        }
    }
}

$categorias = listarCategorias();
?>

<div class="mb-10">
    <h1 class="text-3xl font-black text-gray-800">Categorias do Sistema</h1>
    <p class="text-gray-500 text-sm">Cadastre e organize os agrupamentos de produtos da sua loja.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <div class="bg-white border border-gray-100 rounded-3xl p-6 h-fit shadow-sm">
        <h2 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
            <i class="ph ph-plus-circle text-indigo-600"></i> Nova Categoria
        </h2>

        <?php if ($sucesso): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 p-3 rounded-xl text-xs font-semibold">
                <?php echo $sucesso; ?>
            </div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-xl text-xs font-semibold">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?pg=categorias" method="POST" class="space-y-4">
            <?php echo csrfCampo(); ?>
            <input type="hidden" name="acao" value="cadastrar_categoria">
            <div class="flex flex-col gap-2">
                <label for="nome_categoria" class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nome da Categoria</label>
                <input type="text" id="nome_categoria" name="nome_categoria" value="<?php echo htmlspecialchars($_POST['nome_categoria'] ?? ''); ?>" required placeholder="Ex: Componentes, Monitores..."
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition text-sm shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                <i class="ph ph-floppy-disk"></i> Criar Categoria
            </button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nome da Categoria</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="3" class="p-8 text-center text-sm text-gray-400">Nenhuma categoria cadastrada ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categorias as $cat): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm text-gray-400 font-mono">#<?php echo (int) $cat['id']; ?></td>
                        <td class="p-4 font-bold text-gray-700"><?php echo htmlspecialchars($cat['nome']); ?></td>
                        <td class="p-4 text-right">
                            <div class="action-group">
                            <a href="index.php?pg=editar_categoria&id=<?php echo (int) $cat['id']; ?>" class="action-link action-edit" title="Editar">
                                Editar
                            </a>
                            <form action="index.php?pg=categorias" method="POST" style="display:inline" onsubmit="return confirm('Excluir esta categoria?');">
                                <?php echo csrfCampo(); ?>
                                <input type="hidden" name="acao" value="excluir_categoria">
                                <input type="hidden" name="id" value="<?php echo (int) $cat['id']; ?>">
                                <button type="submit" class="action-link action-remove" title="Excluir Categoria">Remover</button>
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
