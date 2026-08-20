<?php
    require_once "../includes/banco_ficticio.php";
    require_once "../includes/seguranca.php";
    $sucesso = null;
    $erro = null;

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $usuario = $id ? buscarUsuarioPorId($id) : null;

    if (!$usuario) {
        echo "<h2 class='text-xl font-bold text-red-500 p-6'>Administrador não encontrado!</h2>";
        exit;
    }
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrfValidar()) {
            $erro = "Sessão expirada ou requisição inválida. Atualize a página e tente novamente.";
        } else {
        $nome = htmlspecialchars((trim($_POST['nome_usuario'] ?? '')));
        $login = htmlspecialchars((trim($_POST['login_usuario'] ?? '')));
        $senhaNova = $_POST['senha_usuario'] ?? '';

        if (empty($nome) || empty($login)) {
            $erro = "Nome e Usuário de Login são campos obrigatórios.";
        } elseif (!empty($senhaNova) && strlen($senhaNova) < 6) {
            $erro = "A nova senha deve ter pelo menos 6 caracteres.";
        } else {
            $todosUsuarios = listarUsuarios();
            $loginDuplicado = false;

            foreach ($todosUsuarios as $u) {
                if (strtolower($u['login']) === strtolower($login) && $u['id'] != $id) {
                    $loginDuplicado = true;
                    break;
                }
            }
            if ($loginDuplicado) {
                $erro = "Esse login já está sendo utilizado por outros administradores.";
            } else {
                $dadosParaAtualizar = [
                    "nome" => $nome,
                    "login" => $login,
                ];
                if (!empty($senhaNova)) {
                    $dadosParaAtualizar['senha'] = $senhaNova;
                }
                if (atualizarUsuario($id, $dadosParaAtualizar)) {
                    $sucesso = "Dados do administrador atualizados com sucesso!";
                    $usuario = buscarUsuarioPorId($id);
                } else {
                    $erro = "Falha técnica ao salvar as alterações.";
                }
            }
        }
        }
     }
?>

<div class="mb-8">
    <a href="index.php?pg=usuarios" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 mb-4 transition">
        <i class="ph ph-arrow-left"></i> Voltar para a equipe
    </a>
    <h1 class="text-3xl font-black text-gray-800">Editar Administrador</h1>
    <p class="text-gray-500 text-sm">Modifique as credenciais de @<?php echo htmlspecialchars($usuario['login']); ?>.</p>
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

    <form action="index.php?pg=editar_usuario&id=<?php echo (int) $usuario['id']; ?>" method="POST" class="space-y-6">
        <?php echo csrfCampo(); ?>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nome Completo</label>
            <input type="text" name="nome_usuario" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Usuário de Login</label>
            <input type="text" name="login_usuario" value="<?php echo htmlspecialchars($usuario['login']); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nova Senha (Deixe em branco para NÃO alterar)</label>
            <input type="password" name="senha_usuario" placeholder="Digite apenas se quiser mudar a atual..." class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-indigo-600 focus:bg-white transition">
        </div>

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
            <i class="ph ph-check"></i> Atualizar Cadastro
        </button>
    </form>
</div>