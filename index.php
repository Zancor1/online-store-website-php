<?php
session_start();
require_once 'includes/banco_ficticio.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $carrinho = $_SESSION['carrinho'] ?? [];

    if ($acao === 'adicionar' && $id && buscarProdutoPorId($id)) {
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1, 'max_range' => 99]]);
        $carrinho[$id] = min(($carrinho[$id] ?? 0) + $quantidade, 99);
        $_SESSION['carrinho'] = $carrinho;
        $_SESSION['mensagem_carrinho'] = 'Produto adicionado ao carrinho.';
        header('Location: index.php?pg=carrinho');
        exit;
    }

    if ($acao === 'remover' && $id) {
        unset($carrinho[$id]);
        $_SESSION['carrinho'] = $carrinho;
        $_SESSION['mensagem_carrinho'] = 'Produto removido do carrinho.';
        header('Location: index.php?pg=carrinho');
        exit;
    }

    if ($acao === 'finalizar') {
        if (!empty($carrinho)) {
            $_SESSION['carrinho'] = [];
            $_SESSION['pedido_finalizado'] = true;
        }
        header('Location: index.php?pg=carrinho');
        exit;
    }
}

$pagina = $_GET['pg'] ?? 'produtos';
$quantidadeCarrinho = array_sum($_SESSION['carrinho'] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <title>Pixel Store | <?php echo $pagina === 'contato' ? 'Contato' : 'Tecnologia para o seu setup'; ?></title>
    <style>
        body { font-family: Sen, ui-sans-serif, system-ui, sans-serif; }
        .hero-bg { background-image: linear-gradient(90deg, rgba(13,18,31,.92), rgba(13,18,31,.52)), url('https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?auto=format&fit=crop&w=2000&q=85'); }
        .grid-glow { background-image: radial-gradient(circle at 74% 18%, rgba(59,130,246,.22), transparent 26%), linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px); background-size: auto, 42px 42px, 42px 42px; }
    </style>
</head>
<body class="min-h-screen bg-[#101725] text-white antialiased">
    <header class="sticky top-0 z-50 border-b border-white/10 bg-[#101725]/95 backdrop-blur">
        <div class="mx-auto flex h-20 max-w-6xl items-center justify-between px-5">
            <a href="index.php?pg=produtos" class="flex items-center gap-3 text-xl font-extrabold tracking-[.16em]">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-blue-500 text-xl shadow-lg shadow-blue-500/30"><i class="ph ph-cube"></i></span>
                PIXEL <span class="text-blue-400">STORE</span>
            </a>
            <nav class="hidden items-center gap-9 text-sm font-bold text-slate-300 md:flex">
                <a href="index.php?pg=produtos" class="transition hover:text-white <?php echo $pagina === 'produtos' ? 'text-white' : ''; ?>">Produtos</a>
                <a href="#beneficios" class="transition hover:text-white">Por que escolher</a>
                <a href="index.php?pg=contato" class="transition hover:text-white <?php echo $pagina === 'contato' ? 'text-white' : ''; ?>">Contato</a>
            </nav>
            <a href="index.php?pg=carrinho" class="relative grid h-10 w-10 place-items-center rounded-full border border-white/15 text-xl transition hover:border-blue-400 hover:text-blue-300" aria-label="Abrir carrinho">
                <i class="ph ph-shopping-cart"></i><span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-blue-500 text-[10px] font-black"><?php echo $quantidadeCarrinho; ?></span>
            </a>
        </div>
    </header>

    <main>
        <?php if ($pagina === 'produtos'): ?>
            <section class="hero-bg grid-glow relative overflow-hidden bg-cover bg-center">
                <div class="mx-auto flex min-h-[480px] max-w-6xl flex-col justify-center px-5 py-20">
                    <p class="mb-5 text-sm font-bold uppercase tracking-[.28em] text-blue-300">Tecnologia que acompanha você</p>
                    <h1 class="max-w-3xl text-4xl font-extrabold leading-tight md:text-6xl">Seu melhor setup começa aqui.</h1>
                    <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 md:text-lg">Produtos selecionados para melhorar sua rotina, seu trabalho e cada momento de jogo.</p>
                    <div class="mt-9"><a href="#catalogo" class="inline-flex items-center gap-2 rounded-md bg-blue-500 px-6 py-3 text-sm font-extrabold uppercase tracking-wide transition hover:bg-blue-400">Explorar produtos <i class="ph ph-arrow-down"></i></a></div>
                </div>
            </section>
        <?php endif; ?>

        <div id="catalogo" class="mx-auto max-w-6xl px-5 py-16 md:py-20">
            <?php
            $arquivo = 'pages/' . basename($pagina) . '.php';
            if (file_exists($arquivo)) {
                include $arquivo;
            } else {
                echo '<div class="rounded-xl border border-white/10 bg-white/5 p-12 text-center"><h1 class="text-2xl font-bold">Página não encontrada.</h1><a class="mt-4 inline-block text-blue-300" href="index.php?pg=produtos">Voltar para a loja</a></div>';
            }
            ?>
        </div>

        <?php if ($pagina === 'produtos'): ?>
        <section id="beneficios" class="border-y border-white/10 bg-[#151e30]">
            <div class="mx-auto grid max-w-6xl gap-10 px-5 py-16 md:grid-cols-3">
                <div><i class="ph ph-package text-4xl text-blue-400"></i><h2 class="mt-5 text-lg font-extrabold">Produtos selecionados</h2><p class="mt-2 text-sm leading-6 text-slate-400">Tecnologia útil, bonita e pronta para o seu dia a dia.</p></div>
                <div><i class="ph ph-shield-check text-4xl text-blue-400"></i><h2 class="mt-5 text-lg font-extrabold">Compra segura</h2><p class="mt-2 text-sm leading-6 text-slate-400">Uma experiência simples e transparente do início ao fim.</p></div>
                <div><i class="ph ph-headset text-4xl text-blue-400"></i><h2 class="mt-5 text-lg font-extrabold">Atendimento próximo</h2><p class="mt-2 text-sm leading-6 text-slate-400">Conte com a gente sempre que precisar.</p></div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <footer class="bg-[#0c1220]">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-5 py-9 text-sm text-slate-400 md:flex-row"><span class="font-bold tracking-[.14em] text-white">PIXEL <span class="text-blue-400">STORE</span></span><span>© 2026 Pixel Store. Todos os direitos reservados.</span><a href="mailto:meuovinho06@gmail.com" class="transition hover:text-white">meuovinho06@gmail.com</a></div>
   </footer>
</body>
</html>
