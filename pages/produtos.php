<?php
require_once 'includes/banco_ficticio.php';
$todosProdutos = listarProdutos();
$categoriaSelecionada = $_GET['cat'] ?? null;
$produtos = $categoriaSelecionada
    ? array_filter($todosProdutos, fn($produto) => $produto['categoria'] === $categoriaSelecionada)
    : $todosProdutos;
$categorias = array_values(array_unique(array_column($todosProdutos, 'categoria')));
?>

<div class="border-b border-white/10 pb-8">
    <p class="text-sm font-bold uppercase tracking-[.2em] text-blue-400">Catalogo</p>
    <h2 class="mt-2 text-3xl font-extrabold">Produtos em destaque</h2>
    <p class="mt-2 text-sm text-slate-400">Escolha o proximo item do seu setup.</p>
</div>

<div class="mt-8 grid gap-8 lg:grid-cols-[220px_minmax(0,1fr)]">
    <aside class="h-fit rounded-xl border border-white/10 bg-[#151e30] p-4">
        <h3 class="px-2 pb-3 text-sm font-extrabold uppercase tracking-[.14em] text-slate-300">Categorias</h3>
        <nav class="grid gap-2 text-sm font-bold">
            <a href="index.php?pg=produtos" class="rounded-md border px-4 py-3 transition <?php echo !$categoriaSelecionada ? 'border-blue-400 bg-blue-500 text-white' : 'border-white/10 text-slate-300 hover:border-blue-400/60 hover:text-white'; ?>">1. Todos</a>
            <?php foreach ($categorias as $indice => $categoria): ?>
                <a href="index.php?pg=produtos&amp;cat=<?php echo urlencode($categoria); ?>" class="rounded-md border px-4 py-3 transition <?php echo $categoriaSelecionada === $categoria ? 'border-blue-400 bg-blue-500 text-white' : 'border-white/10 text-slate-300 hover:border-blue-400/60 hover:text-white'; ?>"><?php echo $indice + 2; ?>. <?php echo htmlspecialchars($categoria); ?></a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div>
        <?php if (empty($produtos)): ?>
            <div class="rounded-xl border border-white/10 bg-white/5 p-12 text-center text-slate-400">Nenhum produto encontrado nesta categoria.</div>
        <?php else: ?>
            <div class="grid gap-4">
                <?php foreach ($produtos as $p): ?>
                    <article class="group flex flex-col overflow-hidden rounded-xl border border-white/10 bg-[#151e30] transition hover:border-blue-400/60 hover:shadow-xl hover:shadow-blue-950/30 sm:flex-row">
                        <img src="<?php echo htmlspecialchars($p['imagem']); ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105 sm:h-auto sm:w-52">
                        <div class="flex flex-1 flex-col justify-between gap-5 p-5">
                            <div><span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-bold text-blue-300"><?php echo htmlspecialchars($p['categoria']); ?></span><h3 class="mt-4 text-lg font-extrabold text-white"><?php echo htmlspecialchars($p['nome']); ?></h3><p class="mt-2 text-sm leading-6 text-slate-400"><?php echo htmlspecialchars($p['descricao']); ?></p></div>
                            <div class="flex items-center justify-between gap-4"><strong class="text-xl text-white">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></strong><a href="index.php?pg=detalhe&amp;id=<?php echo (int) $p['id']; ?>" class="inline-flex shrink-0 items-center gap-1 rounded-md bg-blue-500 px-4 py-2 text-xs font-extrabold uppercase tracking-wide transition hover:bg-blue-400">Detalhes <i class="ph ph-arrow-right"></i></a></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
