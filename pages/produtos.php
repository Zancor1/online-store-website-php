<?php
require_once 'includes/banco_ficticio.php';
$produtos = listarProdutos();
$categoria_selecionada = $_GET['cat'] ?? null;
if ($categoria_selecionada) {
    $produtos = array_filter($produtos, fn($produto) => $produto['categoria'] === $categoria_selecionada);
}
$categorias = array_values(array_unique(array_column(listarProdutos(), 'categoria')));
?>

<div class="flex flex-col items-start justify-between gap-5 border-b border-white/10 pb-8 md:flex-row md:items-end">
    <div>
        <p class="text-sm font-bold uppercase tracking-[.2em] text-blue-400">Catálogo</p>
        <h2 class="mt-2 text-3xl font-extrabold">Produtos em destaque</h2>
        <p class="mt-2 text-sm text-slate-400">Escolha o próximo item do seu setup.</p>
    </div>
    <div class="flex flex-wrap gap-2 text-sm font-bold">
        <a href="index.php?pg=produtos" class="rounded-full border px-4 py-2 transition <?php echo !$categoria_selecionada ? 'border-blue-400 bg-blue-500 text-white' : 'border-white/15 text-slate-300 hover:border-white/40'; ?>">Todos</a>
        <?php foreach ($categorias as $categoria): ?>
            <a href="index.php?pg=produtos&amp;cat=<?php echo urlencode($categoria); ?>" class="rounded-full border px-4 py-2 transition <?php echo $categoria_selecionada === $categoria ? 'border-blue-400 bg-blue-500 text-white' : 'border-white/15 text-slate-300 hover:border-white/40'; ?>"><?php echo htmlspecialchars($categoria); ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (empty($produtos)): ?>
    <div class="mt-8 rounded-xl border border-white/10 bg-white/5 p-12 text-center text-slate-400">Nenhum produto encontrado nesta categoria.</div>
<?php else: ?>
    <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($produtos as $p): ?>
            <article class="group overflow-hidden rounded-xl border border-white/10 bg-[#151e30] transition duration-300 hover:-translate-y-1 hover:border-blue-400/60 hover:shadow-2xl hover:shadow-blue-950/40">
                <div class="relative aspect-[16/10] overflow-hidden bg-slate-800">
                    <img src="<?php echo htmlspecialchars($p['imagem']); ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute left-4 top-4 rounded-full bg-[#101725]/90 px-3 py-1 text-xs font-bold text-blue-200 backdrop-blur"><?php echo htmlspecialchars($p['categoria']); ?></span>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-extrabold text-white"><?php echo htmlspecialchars($p['nome']); ?></h3>
                    <p class="mt-3 min-h-12 text-sm leading-6 text-slate-400"><?php echo htmlspecialchars($p['descricao']); ?></p>
                    <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-5"><strong class="text-xl text-white">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></strong><a href="index.php?pg=detalhe&amp;id=<?php echo (int) $p['id']; ?>" class="inline-flex items-center gap-1 rounded-md bg-blue-500 px-4 py-2 text-xs font-extrabold uppercase tracking-wide transition hover:bg-blue-400">Detalhes <i class="ph ph-arrow-right"></i></a></div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
