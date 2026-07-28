<?php
require_once "../includes/banco_ficticio.php";

$mensagemSucesso = null;
$mensagemErro = null;

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$fornecedor = $id ? buscarFornecedorPorId($id) : null;

if (!$fornecedor) {
    echo "<p class='text-red-500 font-bold p-6'>Fornecedor não encontrado.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        "nome"     => $_POST['nome'] ?? '',
        "cnpj"     => $_POST['cnpj'] ?? '',
        "telefone" => $_POST['telefone'] ?? '',
        "rua"      => $_POST['rua'] ?? '',
        "numero"   => $_POST['numero'] ?? '',
        "bairro"   => $_POST['bairro'] ?? '',
        "cidade"   => $_POST['cidade'] ?? '',
        "estado"   => $_POST['estado'] ?? ''
    ];

    if (empty($dados['nome']) || empty($dados['cnpj'])) {
        $mensagemErro = "Os campos Nome e CNPJ são obrigatórios!";
    } else {
        if (atualizarFornecedor($id, $dados)) {
            $mensagemSucesso = "Fornecedor atualizado com sucesso!";
            $fornecedor = $dados; // Atualiza a variável local para refletir no form
        } else {
            $mensagemErro = "Erro ao tentar salvar as alterações.";
        }
    }
}
?>

<div class="max-w-2xl mx-auto mt-8 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
    <div class="flex items-center justify-between mb-6">
        <div>
            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest block mb-1">Painel Admin</span>
            <h1 class="text-xl font-black text-gray-800 uppercase tracking-tighter">Editar Fornecedor<span class="text-indigo-600">.</span></h1>
        </div>
        <a href="index.php?pg=fornecedores" class="text-xs text-gray-400 hover:text-indigo-600 font-bold transition">
            Voltar para a lista
        </a>
    </div>

    <hr class="border-gray-50 mb-6">

    <?php if ($mensagemSucesso): ?>
        <div class="bg-green-50 text-green-600 p-4 rounded-xl text-xs font-bold mb-6 border border-green-100">✅ <?php echo $mensagemSucesso; ?></div>
    <?php endif; ?>

    <?php if ($mensagemErro): ?>
        <div class="bg-red-50 text-red-600 p-4 rounded-xl text-xs font-bold mb-6 border border-red-100">❌ <?php echo $mensagemErro; ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">Nome</label>
                <input type="text" name="nome" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white" value="<?php echo htmlspecialchars($fornecedor['nome']); ?>">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">CNPJ</label>
                <input type="text" name="cnpj" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white" value="<?php echo htmlspecialchars($fornecedor['cnpj']); ?>">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">Telefone</label>
                <input type="text" name="telefone" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white" value="<?php echo htmlspecialchars($fornecedor['telefone']); ?>">
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">Rua</label>
                <input type="text" name="rua" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white" value="<?php echo htmlspecialchars($fornecedor['rua']); ?>">
            </div>
        </div>

        <div class="grid grid-cols-4 gap-2">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">Número</label>
                <input type="text" name="numero" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white" value="<?php echo htmlspecialchars($fornecedor['numero']); ?>">
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">Bairro</label>
                <input type="text" name="bairro" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white" value="<?php echo htmlspecialchars($fornecedor['bairro']); ?>">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">UF</label>
                <input type="text" name="estado" maxlength="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white uppercase" value="<?php echo htmlspecialchars($fornecedor['estado']); ?>">
            </div>
        </div>

        <div>
            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">Cidade</label>
            <input type="text" name="cidade" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:border-indigo-600 focus:bg-white" value="<?php echo htmlspecialchars($fornecedor['cidade']); ?>">
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="flex-grow bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl text-xs transition">
                Salvar Alterações
            </button>
            <a href="index.php?pg=fornecedores" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-3 px-6 rounded-xl text-xs text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>