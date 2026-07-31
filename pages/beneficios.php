<?php
$itens = [
    ['icone' => 'ph-package', 'categoria' => 'Selecao', 'titulo' => 'Produtos selecionados', 'descricao' => 'Tecnologia util, bonita e escolhida para melhorar seu setup e sua rotina.'],
    ['icone' => 'ph-shield-check', 'categoria' => 'Seguranca', 'titulo' => 'Compra segura', 'descricao' => 'Uma jornada de compra simples, clara e confiavel do inicio ao fim.'],
    ['icone' => 'ph-headset', 'categoria' => 'Atendimento', 'titulo' => 'Atendimento proximo', 'descricao' => 'Conte com nosso suporte sempre que tiver duvidas antes ou depois da compra.'],
];
$categoriaSelecionada = $_GET['beneficio'] ?? null;
$categorias = array_column($itens, 'categoria');
$beneficios = $categoriaSelecionada ? array_filter($itens, fn($item) => $item['categoria'] === $categoriaSelecionada) : $itens;
?>
<div class="border-b border-white/10 pb-8"><p class="text-sm font-bold uppercase tracking-[.2em] text-blue-400">Pixel Store</p><h1 class="mt-2 text-3xl font-extrabold">Por que escolher a gente?</h1><p class="mt-2 text-sm text-slate-400">Conheca, por categoria, o que torna sua experiencia melhor.</p></div>
<div class="mt-8 grid gap-8 lg:grid-cols-[220px_minmax(0,1fr)]">
    <aside class="h-fit rounded-xl border border-white/10 bg-[#151e30] p-4"><h2 class="px-2 pb-3 text-sm font-extrabold uppercase tracking-[.14em] text-slate-300">Categorias</h2><nav class="grid gap-2 text-sm font-bold"><a href="index.php?pg=beneficios" class="rounded-md border px-4 py-3 transition <?php echo !$categoriaSelecionada ? 'border-blue-400 bg-blue-500 text-white' : 'border-white/10 text-slate-300 hover:border-blue-400/60 hover:text-white'; ?>">1. Todos</a><?php foreach ($categorias as $indice => $categoria): ?><a href="index.php?pg=beneficios&amp;beneficio=<?php echo urlencode($categoria); ?>" class="rounded-md border px-4 py-3 transition <?php echo $categoriaSelecionada === $categoria ? 'border-blue-400 bg-blue-500 text-white' : 'border-white/10 text-slate-300 hover:border-blue-400/60 hover:text-white'; ?>"><?php echo $indice + 2; ?>. <?php echo htmlspecialchars($categoria); ?></a><?php endforeach; ?></nav></aside>
    <div class="grid gap-4"><?php foreach ($beneficios as $beneficio): ?><article class="flex gap-5 rounded-xl border border-white/10 bg-[#151e30] p-6 transition hover:border-blue-400/60"><div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-blue-500/15 text-3xl text-blue-400"><i class="ph <?php echo $beneficio['icone']; ?>"></i></div><div><span class="text-xs font-bold uppercase tracking-[.14em] text-blue-300"><?php echo $beneficio['categoria']; ?></span><h2 class="mt-1 text-xl font-extrabold"><?php echo $beneficio['titulo']; ?></h2><p class="mt-2 text-sm leading-6 text-slate-400"><?php echo $beneficio['descricao']; ?></p></div></article><?php endforeach; ?></div>
</div>
