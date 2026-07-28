<?php
$itensCarrinho = $_SESSION['carrinho'] ?? [];
$mensagem = $_SESSION['mensagem_carrinho'] ?? null;
unset($_SESSION['mensagem_carrinho']);
$pedidoFinalizado = !empty($_SESSION['pedido_finalizado']);
unset($_SESSION['pedido_finalizado']);
$total = 0;
?>

<div class="mx-auto max-w-4xl">
    <div class="flex items-end justify-between border-b border-white/10 pb-7"><div><p class="text-sm font-bold uppercase tracking-[.2em] text-blue-400">Sua seleção</p><h1 class="mt-2 text-3xl font-extrabold">Carrinho</h1></div><a href="index.php?pg=produtos" class="text-sm font-bold text-blue-300 hover:text-white">Continuar comprando</a></div>
    <?php if ($mensagem): ?><div class="mt-6 rounded-lg border border-blue-400/30 bg-blue-500/10 px-4 py-3 text-sm font-semibold text-blue-200"><?php echo htmlspecialchars($mensagem); ?></div><?php endif; ?>
    <?php if ($pedidoFinalizado): ?><div class="mt-6 rounded-lg border border-green-400/30 bg-green-500/10 px-4 py-3 text-sm font-semibold text-green-200">Pedido finalizado com sucesso! Entraremos em contato para confirmar o pagamento.</div><?php endif; ?>

    <?php if (empty($itensCarrinho)): ?>
        <div class="mt-8 rounded-2xl border border-white/10 bg-[#151e30] p-12 text-center"><i class="ph ph-shopping-cart-simple text-5xl text-slate-500"></i><h2 class="mt-4 text-xl font-extrabold">Seu carrinho está vazio</h2><a href="index.php?pg=produtos" class="mt-5 inline-block rounded-md bg-blue-500 px-5 py-3 text-sm font-bold hover:bg-blue-400">Ver produtos</a></div>
    <?php else: ?>
        <div class="mt-8 overflow-hidden rounded-2xl border border-white/10 bg-[#151e30]">
            <?php foreach ($itensCarrinho as $id => $quantidade): $produto = buscarProdutoPorId((int) $id); if (!$produto) continue; $subtotal = $produto['preco'] * $quantidade; $total += $subtotal; ?>
                <article class="flex flex-col gap-5 border-b border-white/10 p-5 last:border-0 sm:flex-row sm:items-center"><img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="" class="h-24 w-full rounded-lg object-cover sm:w-32"><div class="flex-1"><h2 class="font-extrabold"><?php echo htmlspecialchars($produto['nome']); ?></h2><p class="mt-1 text-sm text-slate-400">Quantidade: <?php echo (int) $quantidade; ?></p></div><strong class="text-lg">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></strong><form method="post" action="index.php"><input type="hidden" name="acao" value="remover"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button type="submit" class="rounded-md border border-red-400/40 px-3 py-2 text-sm font-bold text-red-300 transition hover:bg-red-500 hover:text-white" aria-label="Remover <?php echo htmlspecialchars($produto['nome']); ?>"><i class="ph ph-trash"></i></button></form></article>
            <?php endforeach; ?>
            <div class="flex flex-col gap-5 bg-[#101725] p-6 sm:flex-row sm:items-center sm:justify-between"><div><span class="text-sm text-slate-400">Total do pedido</span><strong class="mt-1 block text-3xl">R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></div><form method="post" action="index.php"><input type="hidden" name="acao" value="finalizar"><button type="submit" class="rounded-md bg-blue-500 px-6 py-3 text-sm font-extrabold uppercase tracking-wide transition hover:bg-blue-400">Finalizar compra</button></form></div>
        </div>
    <?php endif; ?>
</div>
