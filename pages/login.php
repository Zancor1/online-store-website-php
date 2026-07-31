<?php
$modo = ($_GET['modo'] ?? 'login') === 'register' ? 'register' : 'login';
$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
$mensagem = $_SESSION['mensagem_login'] ?? null;
unset($_SESSION['mensagem_login']);
?>
<div class="mx-auto max-w-md rounded-2xl border border-white/10 bg-[#151e30] p-7 sm:p-9">
    <div class="grid grid-cols-2 rounded-lg bg-[#101725] p-1 text-sm font-extrabold"><a href="index.php?pg=login" class="rounded-md px-4 py-3 text-center <?php echo $modo === 'login' ? 'bg-blue-500 text-white' : 'text-slate-400'; ?>">Login</a><a href="index.php?pg=login&amp;modo=register" class="rounded-md px-4 py-3 text-center <?php echo $modo === 'register' ? 'bg-blue-500 text-white' : 'text-slate-400'; ?>">Register</a></div>
    <h1 class="mt-8 text-2xl font-extrabold"><?php echo $modo === 'register' ? 'Crie sua conta' : 'Entre na sua conta'; ?></h1>
    <p class="mt-2 text-sm leading-6 text-slate-400"><?php echo $modo === 'register' ? 'Cadastre-se para comprar os produtos da Pixel Store.' : 'Faca login para comprar e finalizar seus pedidos.'; ?></p>
    <?php if ($mensagem || $erro): ?><div class="mt-5 rounded-lg border px-4 py-3 text-sm <?php echo $erro ? 'border-red-400/40 bg-red-500/10 text-red-200' : 'border-blue-400/30 bg-blue-500/10 text-blue-200'; ?>"><?php echo htmlspecialchars($erro ?: $mensagem); ?></div><?php endif; ?>
    <form method="post" class="mt-6 grid gap-4"><input type="hidden" name="acao" value="<?php echo $modo === 'register' ? 'registrar_cliente' : 'login_cliente'; ?>">
        <?php if ($modo === 'register'): ?><label class="grid gap-2 text-sm font-bold">Nome completo<input name="nome" required class="rounded-md border border-white/15 bg-[#101725] px-4 py-3 text-white outline-none focus:border-blue-400"></label><?php endif; ?>
        <label class="grid gap-2 text-sm font-bold">Email<input type="email" name="login" required autocomplete="email" class="rounded-md border border-white/15 bg-[#101725] px-4 py-3 text-white outline-none focus:border-blue-400"></label>
        <label class="grid gap-2 text-sm font-bold">Senha<input type="password" name="senha" required minlength="6" autocomplete="<?php echo $modo === 'register' ? 'new-password' : 'current-password'; ?>" class="rounded-md border border-white/15 bg-[#101725] px-4 py-3 text-white outline-none focus:border-blue-400"></label>
        <button class="mt-2 rounded-md bg-blue-500 px-5 py-3 text-sm font-extrabold uppercase tracking-wide transition hover:bg-blue-400"><?php echo $modo === 'register' ? 'Criar conta' : 'Entrar'; ?></button>
    </form>
</div>
