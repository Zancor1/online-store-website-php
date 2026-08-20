<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = $id ? buscarProdutoPorId($id) : null;
$mensagemCarrinho = $_SESSION['mensagem_carrinho'] ?? null;
unset($_SESSION['mensagem_carrinho']);
?>

<?php if (!$produto): ?>
    <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center"><i class="ph ph-warning-circle text-4xl text-blue-400"></i><h1 class="mt-4 text-2xl font-extrabold">Produto não encontrado</h1><a href="index.php?pg=produtos" class="mt-5 inline-block text-blue-300 hover:text-white">Voltar para os produtos</a></div>
<?php else: ?>
    <?php $variacoes = $produto['variacoes'] ?? []; $estoqueProduto = (int) ($produto['estoque'] ?? 0); ?>
    <a href="index.php?pg=produtos" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 transition hover:text-white"><i class="ph ph-arrow-left"></i> Voltar aos produtos</a>
    <?php if ($mensagemCarrinho): ?>
        <div class="mt-5 rounded-lg border border-blue-400/30 bg-blue-500/10 px-4 py-3 text-sm text-blue-200"><?php echo htmlspecialchars($mensagemCarrinho); ?></div>
    <?php endif; ?>
    <section class="mt-7 grid overflow-hidden rounded-2xl border border-white/10 bg-[#151e30] md:grid-cols-2">
        <div class="min-h-80 bg-slate-800"><img id="imagem-produto" src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="h-full w-full object-cover"></div>
        <div class="flex flex-col p-8 md:p-12">
            <span class="w-fit rounded-full bg-blue-500/15 px-3 py-1 text-xs font-bold text-blue-300"><?php echo htmlspecialchars($produto['categoria']); ?></span>
            <h1 class="mt-5 text-3xl font-extrabold leading-tight md:text-4xl"><?php echo htmlspecialchars($produto['nome']); ?></h1>
            <p class="mt-5 leading-7 text-slate-400"><?php echo htmlspecialchars($produto['descricao']); ?></p>
            <div class="mt-8 border-y border-white/10 py-5">
                <span class="text-sm text-slate-400">Preço</span><strong class="mt-1 block text-3xl text-white">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong>
                <?php if ($estoqueProduto > 0): ?>
                    <span class="mt-2 inline-block text-xs font-bold text-emerald-400"><?php echo $estoqueProduto; ?> unidade(s) em estoque</span>
                <?php else: ?>
                    <span class="mt-2 inline-block text-xs font-bold text-red-400">Produto esgotado</span>
                <?php endif; ?>
            </div>
            <?php if ($estoqueProduto > 0): ?>
            <form method="post" action="index.php" class="mt-7 flex flex-col gap-4 sm:flex-row">
                <?php echo csrfCampo(); ?>
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="id" value="<?php echo (int) $produto['id']; ?>">
                <?php if (!empty($variacoes)): ?>
                    <label class="flex flex-col gap-2 text-sm font-bold text-slate-300">Escolha a variação
                        <select name="variacao" id="variacao-produto" required class="rounded-md border border-white/15 bg-[#101725] px-3 py-3 text-white outline-none focus:border-blue-400">
                            <?php foreach ($variacoes as $indice => $variacao): ?>
                                <option value="<?php echo (int) $indice; ?>" data-imagem="<?php echo htmlspecialchars($variacao['imagem']); ?>"><?php echo htmlspecialchars($variacao['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endif; ?>
                <label class="flex items-center gap-3 text-sm font-bold text-slate-300">Quantidade <input type="number" name="quantidade" value="1" min="1" max="<?php echo min(99, $estoqueProduto); ?>" class="w-20 rounded-md border border-white/15 bg-[#101725] px-3 py-3 text-center text-white outline-none focus:border-blue-400"></label>
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-md bg-blue-500 px-6 py-3 text-sm font-extrabold uppercase tracking-wide transition hover:bg-blue-400"><i class="ph ph-shopping-cart"></i> Adicionar ao carrinho</button>
            </form>
            <?php else: ?>
                <button type="button" disabled class="mt-7 inline-flex items-center justify-center gap-2 rounded-md bg-slate-600 px-6 py-3 text-sm font-extrabold uppercase tracking-wide text-slate-300 cursor-not-allowed"><i class="ph ph-x-circle"></i> Indisponível no momento</button>
            <?php endif; ?>
        </div>
    </section>
    <?php if (!empty($variacoes)): ?>
        <script>
            document.getElementById('variacao-produto').addEventListener('change', function () {
                document.getElementById('imagem-produto').src = this.options[this.selectedIndex].dataset.imagem;
            });
        </script>
    <?php endif; ?>
<?php endif; ?>
