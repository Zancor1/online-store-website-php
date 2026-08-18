<?php
require_once "../includes/banco_ficticio.php";

$totalProdutos = count(listarProdutos());
$totalCategorias = count(listarCategorias());
$totalFornecedores = count(listarFornecedores());
$totalAdmins = count(listarUsuarios());
$totalClientes = count(listarClientes());
$totalPedidos = count(listarPedidos());
$pedidos = array_reverse(listarPedidos());
$ultimosPedidos = array_slice($pedidos, 0, 5);
?>

<div class="mb-10">
    <h1 class="text-3xl font-black text-gray-800">Dashboard</h1>
    <p class="text-gray-500 text-sm">Visao geral da Pixel Store e do painel administrativo.</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Produtos</p>
        <p class="mt-2 text-3xl font-black text-indigo-600"><?php echo $totalProdutos; ?></p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Categorias</p>
        <p class="mt-2 text-3xl font-black text-blue-600"><?php echo $totalCategorias; ?></p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Fornecedores</p>
        <p class="mt-2 text-3xl font-black text-slate-600"><?php echo $totalFornecedores; ?></p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Administradores</p>
        <p class="mt-2 text-3xl font-black text-green-600"><?php echo $totalAdmins; ?></p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Clientes</p>
        <p class="mt-2 text-3xl font-black text-amber-600"><?php echo $totalClientes; ?></p>
    </div>
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pedidos</p>
        <p class="mt-2 text-3xl font-black text-purple-600"><?php echo $totalPedidos; ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
        <h2 class="font-bold text-gray-800 text-lg mb-4">Acesso rapido</h2>
        <div class="grid grid-cols-2 gap-3">
            <a href="index.php?pg=produtos" class="rounded-xl border border-gray-100 px-4 py-3 text-sm font-bold text-gray-700 hover:border-indigo-300 hover:text-indigo-700 transition">Gerenciar produtos</a>
            <a href="index.php?pg=categorias" class="rounded-xl border border-gray-100 px-4 py-3 text-sm font-bold text-gray-700 hover:border-indigo-300 hover:text-indigo-700 transition">Gerenciar categorias</a>
            <a href="index.php?pg=fornecedores" class="rounded-xl border border-gray-100 px-4 py-3 text-sm font-bold text-gray-700 hover:border-indigo-300 hover:text-indigo-700 transition">Gerenciar fornecedores</a>
            <a href="index.php?pg=usuarios" class="rounded-xl border border-gray-100 px-4 py-3 text-sm font-bold text-gray-700 hover:border-indigo-300 hover:text-indigo-700 transition">Gerenciar equipe</a>
            <a href="../index.php" target="_blank" class="rounded-xl border border-gray-100 px-4 py-3 text-sm font-bold text-gray-700 hover:border-indigo-300 hover:text-indigo-700 transition">Abrir loja</a>
            <a href="index.php?pg=documentacao" class="rounded-xl border border-gray-100 px-4 py-3 text-sm font-bold text-gray-700 hover:border-indigo-300 hover:text-indigo-700 transition">Documentacao</a>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="font-bold text-gray-800 text-lg">Ultimos pedidos</h2>
        </div>
        <?php if (empty($ultimosPedidos)): ?>
            <p class="p-6 text-sm text-gray-400">Nenhum pedido registrado ainda.</p>
        <?php else: ?>
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">ID</th>
                        <th class="p-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Cliente</th>
                        <th class="p-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</th>
                        <th class="p-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($ultimosPedidos as $pedido): ?>
                        <tr>
                            <td class="p-3 text-sm font-mono text-gray-500">#<?php echo (int) $pedido['id']; ?></td>
                            <td class="p-3 text-sm text-gray-700"><?php echo htmlspecialchars($pedido['cliente']['nome'] ?? 'N/A'); ?></td>
                            <td class="p-3 text-sm text-gray-700">R$ <?php echo number_format((float) ($pedido['total'] ?? 0), 2, ',', '.'); ?></td>
                            <td class="p-3 text-sm text-gray-500"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($pedido['criado_em'] ?? 'now'))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
