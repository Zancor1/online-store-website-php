<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = $id ? buscarProdutoPorId($id) : null;
?>

<?php if (!$produto): ?>
    <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center"><i class="ph ph-warning-circle text-4xl text-blue-400"></i><h1 class="mt-4 text-2xl font-extrabold">Produto não encontrado</h1><a href="index.php?pg=produtos" class="mt-5 inline-block text-blue-300 hover:text-white">Voltar para os produtos</a></div>
<?php else: ?>
    <a href="index.php?pg=produtos" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 transition hover:text-white"><i class="ph ph-arrow-left"></i> Voltar aos produtos</a>
    <section class="mt-7 grid overflow-hidden rounded-2xl border border-white/10 bg-[#151e30] md:grid-cols-2">
        <div class="min-h-80 bg-slate-800"><img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="h-full w-full object-cover"></div>
        <div class="flex flex-col p-8 md:p-12">
            <span class="w-fit rounded-full bg-blue-500/15 px-3 py-1 text-xs font-bold text-blue-300"><?php echo htmlspecialchars($produto['categoria']); ?></span>
            <h1 class="mt-5 text-3xl font-extrabold leading-tight md:text-4xl"><?php echo htmlspecialchars($produto['nome']); ?></h1>
            <p class="mt-5 leading-7 text-slate-400"><?php echo htmlspecialchars($produto['descricao']); ?></p>
            <div class="mt-8 border-y border-white/10 py-5"><span class="text-sm text-slate-400">Preço</span><strong class="mt-1 block text-3xl text-white">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong></div>
            <form method="post" action="index.php" class="mt-7 flex flex-col gap-4 sm:flex-row">
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="id" value="<?php echo (int) $produto['id']; ?>">
                <label class="flex items-center gap-3 text-sm font-bold text-slate-300">Quantidade <input type="number" name="quantidade" value="1" min="1" max="99" class="w-20 rounded-md border border-white/15 bg-[#101725] px-3 py-3 text-center text-white outline-none focus:border-blue-400"></label>
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-md bg-blue-500 px-6 py-3 text-sm font-extrabold uppercase tracking-wide transition hover:bg-blue-400"><i class="ph ph-shopping-cart"></i> Adicionar ao carrinho</button>
            </form>
        </div>
    </section>
<?php endif; ?>
