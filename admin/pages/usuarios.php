<?php
    require_once "../includes/banco_ficticio.php";
    $sucesso = null;
    $erro = null;

if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    if (excluirUsuarios($idExcluir)) {
        $sucesso = "Categoria excluída com sucesso!";
    } else {
        $erro = "Erro ao tentar excluir a usuario.";
    }
}
    
    //processa o envio do formjulario denovo cadastro (Post)
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = htmlspecialchars((trim($_POST['nome_usuario'] ?? '')));
        $login = htmlspecialchars((trim($_POST['login_usuario'] ?? '')));
        $senha = $_POST['senha_usuario'] ?? '';

        if (empty($nome) || empty($login) || empty($senha)) {
            $erro = "Todos os campos sao obrigatorios para o cadastro";
        } else {
            $usuarioExistentes = listarUsuarios();
            $loginJaExiste = false;
            foreach ($usuarioExistentes as $u) {
                if (strtolower($u['login']) === strtolower($login)){
                    $loginJaExiste = true;
                    break;
                }
                if ($loginJaExiste) {
                    $erro = "Este usuario de login ja esta em uso por outro adiministrador";
                } else {
                    $novoAdmin = [
                        "nome" => $nome,
                        "login" => $login,
                        "senha" => $senha,
                        "ativo" => true //todes usuario por padrao ja nasce ativo
                    ];
                    if(salvarUsuario($novoAdmin)) {
                        $sucesso = "Administrador <strong>$nome</strong> Cadastrado com sucesso!";
                        $_POST = array();
                    } else {
                        $erro = "Erro ao tentar salvar o usuario.";
                    }
                }
            }
        }
    }

    $usuarios = listarUsuarios();
?>
<div class="mb-10">
    <h1 class="text-3xl font-black text-gray-800">Administradores do Sistema</h1>
    <p class="text-gray-500 text-sm">Gerencie quem tem acesso ao painel de controle da loja.</p>
</div>

<?php if ($erro && !isset($_POST['nome_usuario'])): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm flex items-center gap-3">
        <i class="ph ph-warning-circle text-xl text-red-600"></i>
        <div><?php echo $erro; ?></div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <div class="bg-white border border-gray-100 rounded-3xl p-6 h-fit shadow-sm">
        <h2 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
            <i class="ph ph-user-plus text-indigo-600"></i> Novo Administrador
        </h2>

        <?php if ($sucesso): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 p-3 rounded-xl text-xs font-semibold"><?php echo $sucesso; ?></div>
        <?php endif; ?>

        <?php if ($erro && isset($_POST['nome_usuario'])): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-xl text-xs font-semibold"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form action="index.php?pg=usuarios" method="POST" class="space-y-4">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nome Completo</label>
                <input type="text" name="nome_usuario" value="<?php echo $_POST['nome_usuario'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Usuário de Login</label>
                <input type="text" name="login_usuario" value="<?php echo $_POST['login_usuario'] ?? ''; ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Senha de Acesso</label>
                <input type="password" name="senha_usuario" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl transition text-sm shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                <i class="ph ph-floppy-disk"></i> Cadastrar Usuário
            </button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nome</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Login</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($usuarios as $user): 
                    $userAtivo = isset($user['ativo']) ? $user['ativo'] : true;
                ?>
                <tr class="hover:bg-gray-50 transition <?php echo !$userAtivo ? 'opacity-60 bg-gray-50/50' : ''; ?>">
                    <td class="p-4 font-bold text-gray-700"><?php echo $user['nome']; ?></td>
                    <td class="p-4 text-sm text-gray-500">@<?php echo $user['login']; ?></td>
                    
                    <td class="p-4">
                        <?php if ($userAtivo): ?>
                            <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full border border-green-100">Ativo</span>
                        <?php else: ?>
                            <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2.5 py-1 rounded-full border border-gray-200">Inativo</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="p-4 text-right">
                        <div class="action-group">
                        <a href="index.php?pg=editar_usuario&id=<?php echo $user['id']; ?>" class="action-link action-edit" title="Editar">
                            Editar
                        </a>
                        <a href="index.php?pg=usuarios&excluir=<?php echo $user['id']; ?>" class="action-link action-remove" title="Excluir Usuario">
                            Remover
                        </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
