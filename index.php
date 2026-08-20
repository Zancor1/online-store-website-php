<?php
session_start();
require_once 'includes/banco_ficticio.php';
require_once 'includes/seguranca.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // Protecao CSRF: toda acao que altera dados exige um token valido.
    if (!csrfValidar()) {
        $_SESSION['mensagem_carrinho'] = 'Sua sessao expirou ou a requisicao e invalida. Tente novamente.';
        $_SESSION['erro_login'] = 'Sua sessao expirou ou a requisicao e invalida. Tente novamente.';
        $voltarPara = in_array($acao, ['login_cliente', 'registrar_cliente'], true) ? 'login' : ($acao === 'finalizar' ? 'checkout' : 'carrinho');
        header('Location: index.php?pg=' . $voltarPara);
        exit;
    }

    if (in_array($acao, ['login_cliente', 'registrar_cliente'], true)) {
        $login = trim($_POST['login'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($acao === 'registrar_cliente') {
            $nome = trim($_POST['nome'] ?? '');
            if ($nome === '' || !filter_var($login, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6) {
                $_SESSION['erro_login'] = 'Informe nome, email valido e uma senha com pelo menos 6 caracteres.';
                header('Location: index.php?pg=login&modo=register'); exit;
            }
            if (buscarClientePorLogin($login)) {
                $_SESSION['erro_login'] = 'Este email ja possui uma conta.';
                header('Location: index.php?pg=login&modo=register'); exit;
            }
            if (!cadastrarCliente($nome, $login, $senha)) {
                $_SESSION['erro_login'] = 'Nao foi possivel criar sua conta. Tente novamente.';
                header('Location: index.php?pg=login&modo=register'); exit;
            }
            regenerarSessao();
            $_SESSION['cliente'] = ['nome' => $nome, 'login' => $login];
            header('Location: index.php?pg=produtos'); exit;
        }

        $cliente = buscarClientePorLogin($login);
        if (!$cliente || !password_verify($senha, $cliente['senha'])) {
            $_SESSION['erro_login'] = 'Email ou senha invalidos.';
            header('Location: index.php?pg=login'); exit;
        }
        regenerarSessao();
        $_SESSION['cliente'] = ['nome' => $cliente['nome'], 'login' => $cliente['login']];
        header('Location: index.php?pg=produtos'); exit;
    }
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $carrinho = $_SESSION['carrinho'] ?? [];

    if ($acao === 'logout') {
        unset($_SESSION['cliente']);
        header('Location: index.php');
        exit;
    }

    if ($acao === 'adicionar' && empty($_SESSION['cliente'])) {
        $_SESSION['mensagem_login'] = 'Entre ou crie uma conta para adicionar itens ao carrinho.';
        header('Location: index.php?pg=login');
        exit;
    }

    if ($acao === 'adicionar' && $id && ($produto = buscarProdutoPorId($id))) {
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1, 'max_range' => 99]]);
        $variacao = filter_input(INPUT_POST, 'variacao', FILTER_VALIDATE_INT, ['options' => ['default' => -1, 'min_range' => 0]]);
        $variacoes = $produto['variacoes'] ?? [];
        if (!empty($variacoes) && !isset($variacoes[$variacao])) {
            $_SESSION['mensagem_carrinho'] = 'Selecione uma variacao valida antes de adicionar ao carrinho.';
            header('Location: index.php?pg=detalhe&id=' . $id);
            exit;
        }
        $chaveItem = !empty($variacoes) ? $id . ':' . $variacao : (string) $id;

        // Baixa de estoque so acontece na finalizacao da compra, mas o
        // carrinho nunca pode acumular mais unidades do que ha disponivel.
        $estoqueDisponivel = (int) ($produto['estoque'] ?? 0);
        if ($estoqueDisponivel <= 0) {
            $_SESSION['mensagem_carrinho'] = 'Este produto esta sem estoque no momento.';
            header('Location: index.php?pg=detalhe&id=' . $id);
            exit;
        }

        $quantidadeDesejada = ($carrinho[$chaveItem] ?? 0) + $quantidade;
        if ($quantidadeDesejada > $estoqueDisponivel) {
            $_SESSION['mensagem_carrinho'] = 'Estoque insuficiente. Disponivel: ' . $estoqueDisponivel . ' unidade(s).';
            header('Location: index.php?pg=detalhe&id=' . $id);
            exit;
        }

        $carrinho[$chaveItem] = min($quantidadeDesejada, 99);
        $_SESSION['carrinho'] = $carrinho;
        $_SESSION['mensagem_carrinho'] = 'Produto adicionado ao carrinho.';
        header('Location: index.php?pg=carrinho');
        exit;
    }

    if ($acao === 'remover') {
        $chaveItem = (string) ($_POST['item'] ?? $id ?? '');
        unset($carrinho[$chaveItem]);
        $_SESSION['carrinho'] = $carrinho;
        $_SESSION['mensagem_carrinho'] = 'Produto removido do carrinho.';
        header('Location: index.php?pg=carrinho');
        exit;
    }

    if ($acao === 'finalizar') {
        if (empty($_SESSION['cliente'])) {
            $_SESSION['mensagem_login'] = 'Faca login para finalizar sua compra.';
            header('Location: index.php?pg=login');
            exit;
        }

        $endereco = trim($_POST['endereco'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $cep = trim($_POST['cep'] ?? '');
        if ($endereco === '' || $cidade === '' || $cep === '') {
            header('Location: index.php?pg=checkout');
            exit;
        }

        if (!empty($carrinho)) {
            // Confere e da baixa no estoque de forma atomica (trava exclusiva
            // no arquivo de produtos), bloqueando a compra caso o estoque
            // tenha ficado insuficiente entre a adicao ao carrinho e o
            // fechamento do pedido (inclusive em compras concorrentes).
            $resultado = processarFinalizacaoCompra($carrinho);

            if (!$resultado['ok']) {
                $_SESSION['mensagem_carrinho'] = $resultado['erro'];
                header('Location: index.php?pg=carrinho');
                exit;
            }

            salvarPedido([
                'cliente' => $_SESSION['cliente'],
                'endereco' => ['endereco' => $endereco, 'cidade' => $cidade, 'cep' => $cep],
                'itens' => $resultado['itens'],
                'total' => $resultado['total'],
                'status' => 'pendente',
                'criado_em' => date('c'),
            ]);
            $_SESSION['carrinho'] = [];
            $_SESSION['pedido_finalizado'] = true;
            $_SESSION['endereco_pedido'] = ['endereco' => $endereco, 'cidade' => $cidade, 'cep' => $cep];
        }
        header('Location: index.php?pg=carrinho');
        exit;
    }
}

$pagina = $_GET['pg'] ?? 'inicio';
if ($pagina === 'checkout' && empty($_SESSION['cliente'])) {
    header('Location: index.php?pg=login');
    exit;
}
$quantidadeCarrinho = array_sum($_SESSION['carrinho'] ?? []);
$clienteLogado = $_SESSION['cliente'] ?? null;
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
    <title>Pixel Store | <?php echo $pagina === 'contato' ? 'Contato' : ($pagina === 'beneficios' ? 'Por que escolher' : 'Tecnologia para o seu setup'); ?></title>
    <style>
        body { font-family: Sen, ui-sans-serif, system-ui, sans-serif; }
        .hero-bg { background-image: linear-gradient(90deg, rgba(13,18,31,.92), rgba(13,18,31,.52)), url('https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?auto=format&fit=crop&w=2000&q=85'); }
        .grid-glow { background-image: radial-gradient(circle at 74% 18%, rgba(59,130,246,.22), transparent 26%), linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px); background-size: auto, 42px 42px, 42px 42px; }
    </style>
</head>
<body class="min-h-screen bg-[#101725] text-white antialiased">
    <header class="sticky top-0 z-50 border-b border-white/10 bg-[#101725]/95 backdrop-blur">
        <div class="mx-auto flex h-20 max-w-6xl items-center justify-between px-5">
            <a href="index.php" class="flex items-center gap-3 text-xl font-extrabold tracking-[.16em]">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-blue-500 text-xl shadow-lg shadow-blue-500/30"><i class="ph ph-cube"></i></span>
                PIXEL <span class="text-blue-400">STORE</span>
            </a>
            <nav class="hidden items-center gap-9 text-sm font-bold text-slate-300 md:flex">
                <a href="index.php" class="transition hover:text-white <?php echo $pagina === 'inicio' ? 'text-white' : ''; ?>">Inicio</a>
                <a href="index.php?pg=produtos" class="transition hover:text-white <?php echo $pagina === 'produtos' ? 'text-white' : ''; ?>">Ver Produtos</a>
                <a href="index.php?pg=beneficios" class="transition hover:text-white <?php echo $pagina === 'beneficios' ? 'text-white' : ''; ?>">Beneficios</a>
                <a href="index.php?pg=contato" class="transition hover:text-white <?php echo $pagina === 'contato' ? 'text-white' : ''; ?>">Contato</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="index.php?pg=carrinho" class="relative grid h-10 w-10 place-items-center rounded-full border border-white/15 text-xl transition hover:border-blue-400 hover:text-blue-300" aria-label="Abrir carrinho">
                    <i class="ph ph-shopping-cart"></i><span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-blue-500 text-[10px] font-black"><?php echo $quantidadeCarrinho; ?></span>
                </a>
                <?php if ($clienteLogado): ?>
                    <details class="relative">
                        <summary class="grid h-10 w-10 cursor-pointer list-none place-items-center rounded-full border border-blue-400/50 bg-blue-500/10 text-xl text-blue-300 transition hover:bg-blue-500 hover:text-white [&::-webkit-details-marker]:hidden" title="Abrir menu da conta" aria-label="Abrir menu da conta"><i class="ph ph-user"></i></summary>
                        <div class="absolute right-0 top-12 z-50 w-64 overflow-hidden rounded-xl border border-white/10 bg-[#202636] shadow-2xl shadow-black/50">
                            <div class="border-b border-white/10 px-5 py-4"><p class="font-extrabold text-white"><?php echo htmlspecialchars($clienteLogado['nome']); ?></p><p class="mt-1 text-sm text-slate-400"><?php echo htmlspecialchars($clienteLogado['login']); ?></p></div>
                            <div class="p-2"><button type="button" class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-bold text-white transition hover:bg-white/10"><i class="ph ph-user-circle text-xl"></i> Conta</button><form method="post" action="index.php"><?php echo csrfCampo(); ?><input type="hidden" name="acao" value="logout"><button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-bold text-white transition hover:bg-red-500/15 hover:text-red-200"><i class="ph ph-sign-out text-xl"></i> Desconectar</button></form></div>
                        </div>
                    </details>
                <?php else: ?>
                    <a href="index.php?pg=login" class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-xl text-slate-200 transition hover:border-blue-400 hover:text-blue-300" aria-label="Login" title="Login"><i class="ph ph-user"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <?php if ($pagina === 'inicio'): ?>
            <section class="hero-bg grid-glow relative overflow-hidden bg-cover bg-center">
                <div class="mx-auto flex min-h-[480px] max-w-6xl flex-col justify-center px-5 py-20">
                    <p class="mb-5 text-sm font-bold uppercase tracking-[.28em] text-blue-300">Tecnologia que acompanha você</p>
                    <h1 class="max-w-3xl text-4xl font-extrabold leading-tight md:text-6xl">Seu melhor setup começa aqui.</h1>
                    <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 md:text-lg">Produtos selecionados para melhorar sua rotina, seu trabalho e cada momento de jogo.</p>
                    <div class="mt-9"><a href="index.php?pg=produtos" class="inline-flex items-center gap-2 rounded-md bg-blue-500 px-6 py-3 text-sm font-extrabold uppercase tracking-wide transition hover:bg-blue-400">Explorar produtos <i class="ph ph-arrow-right"></i></a></div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($pagina !== 'inicio'): ?>
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
        <?php endif; ?>

        <?php if (false): ?>
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
