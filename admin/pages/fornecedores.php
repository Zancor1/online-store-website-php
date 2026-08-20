<?php
    require_once "../includes/banco_ficticio.php";
    require_once "../includes/seguranca.php";
    $sucesso = null;
    $erro = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir_fornecedor') {
        if (!csrfValidar()) {
            $erro = "Sessão expirada ou requisição inválida. Tente novamente.";
        } else {
            $idExcluir = intval($_POST['id'] ?? 0);
            if (excluirFornecedores($idExcluir)) {
                $sucesso = "Fornecedor excluído com sucesso!";
            } else {
                $erro = "Erro ao tentar excluir o fornecedor.";
            }
        }
    }

    // Processa o envio do formulário de novo cadastro (Post)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? 'cadastrar_fornecedor') === 'cadastrar_fornecedor' && isset($_POST['nome_fornecedor']) && !csrfValidar()) {
        $erro = "Sessão expirada ou requisição inválida. Atualize a página e tente novamente.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? 'cadastrar_fornecedor') === 'cadastrar_fornecedor' && isset($_POST['nome_fornecedor'])) {
        $nome     = htmlspecialchars(trim($_POST['nome_fornecedor'] ?? ''));
        $cnpj     = htmlspecialchars(trim($_POST['cnpj_fornecedor'] ?? ''));
        $telefone = htmlspecialchars(trim($_POST['telefone_fornecedor'] ?? ''));
        $cep      = htmlspecialchars(trim($_POST['cep_fornecedor'] ?? ''));
        $rua      = htmlspecialchars(trim($_POST['rua_fornecedor'] ?? ''));
        $numero   = htmlspecialchars(trim($_POST['numero_fornecedor'] ?? ''));
        $bairro   = htmlspecialchars(trim($_POST['bairro_fornecedor'] ?? ''));
        $cidade   = htmlspecialchars(trim($_POST['cidade_fornecedor'] ?? ''));

        if (empty($nome) || empty($cnpj) || empty($telefone) || empty($cep) || empty($rua) || empty($numero) || empty($bairro) || empty($cidade)) {
            $erro = "Todos os campos são obrigatórios para o cadastro.";
        } else {
            $fornecedoresExistentes = listarFornecedores();
            $fornJaExiste = false;

            // Loop apenas para checar se já existe
            foreach ($fornecedoresExistentes as $f) {
                // Ajustado para checar a chave correta salva no banco fictício
                if (isset($f['cnpj_fornecedor']) && strtolower($f['cnpj_fornecedor']) === strtolower($cnpj)) {
                    $fornJaExiste = true;
                    break;
                }
            }

            // Condicional movida para FORA do loop foreach
            if ($fornJaExiste) {
                $erro = "Este CNPJ já está em uso por outro fornecedor.";
            } else {
                // Criando o array usando as mesmas chaves que a tabela vai ler
                $novoForn = [
                    "id"                  => time(), // Gerando um ID temporário/fictício baseado no timestamp
                    "nome_fornecedor"     => $nome,
                    "cnpj_fornecedor"     => $cnpj,
                    "telefone_fornecedor" => $telefone,
                    "cep_fornecedor"      => $cep,
                    "rua_fornecedor"      => $rua,
                    "numero_fornecedor"   => $numero,
                    "bairro_fornecedor"   => $bairro,
                    "cidade_fornecedor"   => $cidade,
                    "ativo"               => true
                ];

                if (salvarFornecedores($novoForn)) {
                    $sucesso = "Fornecedor <strong>$nome</strong> cadastrado com sucesso!";
                    $_POST = array(); // Limpa o form
                } else {
                    $erro = "Erro ao tentar salvar o fornecedor.";
                }
            }
        }
    }

    $fornecedores = listarFornecedores();
?>
<div class="mb-10">
    <h1 class="text-3xl font-black text-gray-800">Fornecedores do Sistema</h1>
    <p class="text-gray-500 text-sm">Gerencie os fornecedores cadastrados na loja.</p>
</div>

<?php if ($erro && !isset($_POST['nome_fornecedor'])): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm flex items-center gap-3">
        <i class="ph ph-warning-circle text-xl text-red-600"></i>
        <div><?php echo $erro; ?></div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <div class="bg-white border border-gray-100 rounded-3xl p-6 h-fit shadow-sm">
        <h2 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
            <i class="ph ph-user-plus text-indigo-600"></i> Novo Fornecedor
        </h2>

        <?php if ($sucesso): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 p-3 rounded-xl text-xs font-semibold"><?php echo $sucesso; ?></div>
        <?php endif; ?>

        <?php if ($erro && isset($_POST['nome_fornecedor'])): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-xl text-xs font-semibold"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form action="index.php?pg=fornecedores" method="POST" class="space-y-4">
            <?php echo csrfCampo(); ?>
            <input type="hidden" name="acao" value="cadastrar_fornecedor">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nome Completo</label>
                <input type="text" name="nome_fornecedor" value="<?php echo htmlspecialchars($_POST['nome_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">CNPJ</label>
                <input type="text" name="cnpj_fornecedor" value="<?php echo htmlspecialchars($_POST['cnpj_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Telefone</label>
                <input type="text" name="telefone_fornecedor" value="<?php echo htmlspecialchars($_POST['telefone_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>    
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">CEP</label>
                <input type="text" name="cep_fornecedor" value="<?php echo htmlspecialchars($_POST['cep_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Rua</label>
                <input type="text" name="rua_fornecedor" value="<?php echo htmlspecialchars($_POST['rua_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Número</label>
                <input type="text" name="numero_fornecedor" value="<?php echo htmlspecialchars($_POST['numero_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Bairro</label>
                <input type="text" name="bairro_fornecedor" value="<?php echo htmlspecialchars($_POST['bairro_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Cidade</label>
                <input type="text" name="cidade_fornecedor" value="<?php echo htmlspecialchars($_POST['cidade_fornecedor'] ?? ''); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl transition text-sm shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                <i class="ph ph-floppy-disk"></i> Cadastrar Fornecedor
            </button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nome</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">CNPJ</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Telefone</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">CEP</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rua</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Numero</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Bairro</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cidade</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($fornecedores as $forn): 
                    $fornAtivo = isset($forn['ativo']) ? $forn['ativo'] : true;
                ?>
                <tr class="hover:bg-gray-50 transition <?php echo !$fornAtivo ? 'opacity-60 bg-gray-50/50' : ''; ?>">
                    <td class="p-4 font-bold text-gray-700"><?php echo htmlspecialchars($forn['nome_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($forn['cnpj_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($forn['telefone_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($forn['cep_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($forn['rua_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($forn['numero_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($forn['bairro_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm text-gray-500"><?php echo htmlspecialchars($forn['cidade_fornecedor'] ?? 'N/A'); ?></td>
                    <td class="p-4 text-sm">
                        <?php if ($fornAtivo): ?>
                            <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full border border-green-100">Ativo</span>
                        <?php else: ?>
                            <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2.5 py-1 rounded-full border border-gray-200">Inativo</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="p-4 text-right whitespace-nowrap">
                        <div class="action-group">
                        <a href="index.php?pg=editar_fornecedor&id=<?php echo (int) ($forn['id'] ?? 0); ?>" class="action-link action-edit" title="Editar">
                            Editar
                        </a>
                        <form action="index.php?pg=fornecedores" method="POST" style="display:inline" onsubmit="return confirm('Excluir este fornecedor?');">
                            <?php echo csrfCampo(); ?>
                            <input type="hidden" name="acao" value="excluir_fornecedor">
                            <input type="hidden" name="id" value="<?php echo (int) $forn['id']; ?>">
                            <button type="submit" class="action-link action-remove" title="Excluir Fornecedor">Remover</button>
                        </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
