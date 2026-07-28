<?php
// Inicia o sistema de sessões para podermos verificar quem está acessando
session_start();

// SEGURANÇA: Se a variável 'logado' não existir na sessão, significa que o usuário não fez login
if (!isset($_SESSION['logado'])) {
    // "Chuta" o usuário invasor de volta para a tela de login
    header('Location: login.php');
    exit; // Interrompe o carregamento da página por segurança
}

// LOGOFF: Se o link de 'sair' for clicado (index.php?sair=true)
if (isset($_GET['sair'])) {
    // Destrói todas as variáveis gravadas na sessão (apaga o crachá)
    session_destroy();
    // Manda de volta para a tela de login
    header('Location: login.php');
    exit;
}

$pagina = $_GET['pg'] ?? 'dashboard';

$titulos = [
    'dashboard' => 'Dashboard',
    'categorias' => 'Categorias',
    'produtos' => 'Produtos',
    'usuarios' => 'Equipe / Usuários',
    'fornecedores' => 'Fornecedores',
    'editar_categoria' => 'Editar Categoria',
    'editar_categorias' => 'Editar Categorias',
    'editar_produto' => 'Editar Produto',
    'editar_usuario' => 'Editar Usuário',
    'editar_fornecedor' => 'Editar Fornecedor',
];

$tituloPagina = $titulos[$pagina] ?? 'Página não encontrada';

$navItems = [
    ['pg' => 'dashboard', 'label' => 'Dashboard', 'icon' => '◆', 'color' => 'indigo'],
    ['pg' => 'categorias', 'label' => 'Categorias', 'icon' => '◈', 'color' => 'blue'],
    ['pg' => 'produtos', 'label' => 'Produtos', 'icon' => '▣', 'color' => 'yellow'],
    ['pg' => 'usuarios', 'label' => 'Equipe / Usuários', 'icon' => '●', 'color' => 'green'],
    ['pg' => 'fornecedores', 'label' => 'Fornecedores', 'icon' => '▰', 'color' => 'slate'],
];
?>

<!DOCTYPE html>
<html lang="pt-br" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | MinimalShop</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="admin-shell">
        <nav class="topbar">
            <div class="topbar-inner">
                <div class="topbar-left">
                    <a href="index.php?pg=dashboard" class="admin-logo" aria-label="Dashboard">
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

                    <div class="primary-links">
                        <a href="index.php?pg=dashboard" class="<?php echo $pagina == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                        <a href="index.php?pg=produtos" class="<?php echo $pagina == 'produtos' ? 'active' : ''; ?>">Produtos</a>
                    </div>
                </div>

                <div class="user-menu">
                    <span><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                    <a href="index.php?sair=true" title="Sair">Sair</a>
                </div>
            </div>
        </nav>

        <header class="page-heading">
            <div class="content-wrap">
                <h1><?php echo $tituloPagina; ?></h1>
            </div>
        </header>

        <main class="admin-main">
            <div class="content-wrap">
                <div class="nav-card">
                    <div class="quick-actions">
                        <?php foreach ($navItems as $item): ?>
                            <a href="index.php?pg=<?php echo $item['pg']; ?>" class="quick-button quick-<?php echo $item['color']; ?> <?php echo $pagina == $item['pg'] ? 'is-active' : ''; ?>">
                                <span><?php echo $item['icon']; ?></span>
                                <?php echo $item['label']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <section class="page-content">
                    <?php
                    // Monta o caminho do arquivo de forma dinâmica (ex: pages/produtos.php)
                    $arquivo = "pages/" . $pagina . ".php";

                    // Verifica se o arquivo físico existe dentro da pasta antes de chamá-lo
                    if (file_exists($arquivo)) {
                        include($arquivo); // Carrega o conteúdo aqui dentro do <main>
                    } else {
                        echo "<h1 class='text-2xl font-bold'>Página não encontrada no Admin.</h1>";
                    }
                    ?>
                </section>
            </div>
        </main>
    </div>
</body>

</html>
