<?php

session_start();
require_once __DIR__ . '/../includes/banco_ficticio.php';
require_once __DIR__ . '/../includes/seguranca.php';

$erro = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuarioDigitado = htmlspecialchars(trim($_POST['usuario'] ?? ''));
    $senhaDigitada = $_POST['senha'] ?? '';

    // Lista de usuários cadastrados
    $usuariosCadastrados = listarUsuarios();
    $usuarioEncontrado = null;

    // Procura o usuário
    foreach ($usuariosCadastrados as $u) {
        if (strtolower($u['login']) === strtolower($usuarioDigitado)) {
            $usuarioEncontrado = $u;
            break;
        }
    }

    // Verifica login e senha
    if ($usuarioEncontrado && password_verify($senhaDigitada, $usuarioEncontrado['senha'])) {

        // Verifica se o usuário está ativo
        $usuarioAtivo = isset($usuarioEncontrado['ativo']) ? $usuarioEncontrado['ativo'] : true;

        if (!$usuarioAtivo) {

            $erro = "Esta conta foi desativada pelo sistema.";

        } else {

            regenerarSessao();
            $_SESSION['logado'] = true;
            $_SESSION['usuario_nome'] = $usuarioEncontrado['nome'];

            header("Location: index.php");
            exit;
        }

    } else {

        $erro = "Usuário ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acesse o painel administrativo da Pixel Store.">
    <title>Log In | Pixel Store Admin</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="page">
        <a class="brand" href="../index.php" aria-label="Pixel Store">
            <svg viewBox="0 0 1024 1024" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M57 438.312l109.536 488.72h697.336l109.536-488.72-259.176 156.816-187.856-333.088-205.352 333.088z" fill="#EC9312" />
                <path d="M629.048 211.888c0 58.912-47.752 106.656-106.672 106.656-58.92 0-106.664-47.744-106.664-106.656 0-58.976 47.744-106.656 106.664-106.656s106.672 47.688 106.672 106.656z" fill="#CB1B5B" />
                <path d="M522.376 105.232c-58.92 0-106.664 47.68-106.664 106.656 0 58.912 47.744 106.656 106.664 106.656V105.232z" fill="#E5226B" />
                <path d="M57 438.312l109.536 488.72h697.336z" fill="#F4A832" />
                <path d="M973.408 438.312l-109.536 488.72H166.536z" fill="#F4A832" />
                <path d="M166.536 927.032h697.336L515.2 715.832z" fill="#F5B617" />
                <path d="M1017.856 409.44a55.2 55.2 0 0 1-55.264 55.208 55.184 55.184 0 0 1-55.216-55.208 55.2 55.2 0 0 1 55.216-55.264 55.2 55.2 0 0 1 55.264 55.264z" fill="#0472AF" />
                <path d="M962.592 354.176a55.2 55.2 0 0 0-55.216 55.264 55.184 55.184 0 0 0 55.216 55.208V354.176z" fill="#1A8DCC" />
                <path d="M116.656 409.44a55.216 55.216 0 0 1-55.272 55.208A55.208 55.208 0 0 1 6.144 409.44a55.208 55.208 0 0 1 55.24-55.264 55.224 55.224 0 0 1 55.272 55.264z" fill="#0472AF" />
                <path d="M61.384 354.176A55.216 55.216 0 0 0 6.144 409.44a55.2 55.2 0 0 0 55.24 55.208V354.176z" fill="#0092D2" />
            </svg>
        </a>

        <div class="login-card">
            <?php if ($erro): ?>
                <div class="error">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="field">
                    <label for="usuario">Email or Username</label>
                    <input class="text-input" id="usuario" type="text" name="usuario" required autofocus autocomplete="username">
                </div>

                <div class="field">
                    <label for="senha">Password</label>
                    <input class="text-input" id="senha" type="password" name="senha" required autocomplete="current-password">
                </div>

                <div class="remember">
                    <label for="remember_me">
                        <input type="checkbox" id="remember_me" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <div class="actions">
                    <button type="submit" class="button">Log in</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
