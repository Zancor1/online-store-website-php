<?php
    require_once "../includes/banco_ficticio.php";
    $sucesso = null;
    $erro = null;

    $id = $_GET['id'] ?? null;
    // Busca o fornecedor correto pelo ID
    $fornecedor = buscarFornecedoresPorId($id);

    if (!$fornecedor) {
        echo "<h2 class='text-xl font-bold text-red-500 p-6'>Fornecedor não encontrado!</h2>";
        exit;
    }

    // Processa a atualização do formulário (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome     = htmlspecialchars(trim($_POST['nome_fornecedor'] ?? ''));
        $cnpj     = htmlspecialchars(trim($_POST['cnpj_fornecedor'] ?? ''));
        $telefone = htmlspecialchars(trim($_POST['telefone_fornecedor'] ?? ''));
        $cep      = htmlspecialchars(trim($_POST['cep_fornecedor'] ?? ''));
        $rua      = htmlspecialchars(trim($_POST['rua_fornecedor'] ?? ''));
        $numero   = htmlspecialchars(trim($_POST['numero_fornecedor'] ?? ''));
        $bairro   = htmlspecialchars(trim($_POST['bairro_fornecedor'] ?? ''));
        $cidade   = htmlspecialchars(trim($_POST['cidade_fornecedor'] ?? ''));

        // Validação de campos obrigatórios
        if (empty($nome) || empty($cnpj) || empty($telefone) || empty($cep) || empty($rua) || empty($numero) || empty($bairro) || empty($cidade)) {
            $erro = "Todos os campos são obrigatórios para a atualização.";
        } else {
            $todosFornecedores = listarFornecedores();
            $cnpjDuplicado = false;

            // Verifica se o CNPJ editado já pertence a OUTRO fornecedor
            foreach ($todosFornecedores as $f) {
                if (isset($f['cnpj_fornecedor']) && strtolower($f['cnpj_fornecedor']) === strtolower($cnpj) && $f['id'] != $id) {
                    $cnpjDuplicado = true;
                    break;
                }
            }

            if ($cnpjDuplicated) { // Correção interna se houver
                $erro = "Este CNPJ já está sendo utilizado por outro fornecedor.";
            } if ($cnpjDuplicado) {
                $erro = "Este CNPJ já está sendo utilizado por outro fornecedor.";
            } else {
                // Monta o array com a estrutura correta dos fornecedores
                $dadosParaAtualizar = [
                    "nome_fornecedor"     => $nome,
                    "cnpj_fornecedor"     => $cnpj,
                    "telefone_fornecedor" => $telefone,
                    "cep_fornecedor"      => $cep,
                    "rua_fornecedor"      => $rua,
                    "numero_fornecedor"   => $numero,
                    "bairro_fornecedor"   => $bairro,
                    "cidade_fornecedor"   => $cidade,
                    "ativo"               => isset($fornecedor['ativo']) ? $fornecedor['ativo'] : true
                ];

                // ATENÇÃO: Certifique-se de que a função atualizarFornecedor existe no seu banco_ficticio.php
                if (atualizarFornecedores($id, $dadosParaAtualizar)) {
                    $sucesso = "Dados do fornecedor atualizados com sucesso!";
                    // Atualiza a variável local para exibir os novos dados nos inputs
                    $fornecedor = buscarFornecedoresPorId($id);
                } else {
                    $erro = "Falha técnica ao salvar as alterações.";
                }
            }
        }
    }
?>

<div class="mb-8">
    <a href="index.php?pg=fornecedores" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 mb-4 transition">
        <i class="ph ph-arrow-left"></i> Voltar para fornecedores
    </a>
    <h1 class="text-3xl font-black text-gray-800">Editar Fornecedor</h1>
    <p class="text-gray-500 text-sm">Editar os dados de <strong><?php echo $fornecedor['nome_fornecedor'] ?? ''; ?></strong>.</p>
</div>

<div class="bg-white border border-gray-100 rounded-3xl p-8 max-w-xl shadow-sm">
    <?php if ($sucesso): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-sm flex items-center gap-3">
            <i class="ph ph-check-circle text-xl text-green-600"></i>
            <div><?php echo $sucesso; ?></div>
        </div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm flex items-center gap-3">
            <i class="ph ph-warning-circle text-xl text-red-600"></i>
            <div><?php echo $erro; ?></div>
        </div>
    <?php endif; ?>

    <form action="index.php?pg=editar_fornecedor&id=<?php echo $id; ?>" method="POST" class="space-y-4">
        
        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nome Completo</label>
            <input type="text" name="nome_fornecedor" value="<?php echo $fornecedor['nome_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">CNPJ</label>
            <input type="text" name="cnpj_fornecedor" value="<?php echo $fornecedor['cnpj_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Telefone</label>
            <input type="text" name="telefone_fornecedor" value="<?php echo $fornecedor['telefone_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>  
        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">CEP</label>
            <input type="text" name="cep_fornecedor" value="<?php echo $fornecedor['cep_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Rua</label>
            <input type="text" name="rua_fornecedor" value="<?php echo $fornecedor['rua_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Número</label>
            <input type="text" name="numero_fornecedor" value="<?php echo $fornecedor['numero_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Bairro</label>
            <input type="text" name="bairro_fornecedor" value="<?php echo $fornecedor['bairro_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Cidade</label>
            <input type="text" name="cidade_fornecedor" value="<?php echo $fornecedor['cidade_fornecedor'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>
        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
            <i class="ph ph-check"></i> Atualizar Fornecedor
</button>
    </form>
</div>