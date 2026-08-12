<?php
session_start();
if (!isset($_SESSION['logado'])) { http_response_code(403); exit('Acesso não autorizado.'); }

function pdfEscape($text) { return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text); }
function pdfText($text) { return iconv('UTF-8', 'Windows-1252//TRANSLIT', $text) ?: $text; }

$sections = [
    ['DOCUMENTAÇÃO DO SISTEMA', [
        'Pixel Store — Manual completo de uso da loja virtual e do painel administrativo.',
        'Índice: apresentação; área pública; conta; compra; painel administrativo; cadastros; ordem correta; dados; dúvidas frequentes.'
    ]],
    ['1. APRESENTAÇÃO DO PROJETO', [
        'A Pixel Store é uma loja virtual de tecnologia com itens de periféricos, áudio e hardware.',
        'O tema permite demonstrar catálogo, cadastro de clientes, login, carrinho e gerenciamento administrativo.',
        'Tecnologias utilizadas: HTML5, CSS3, JavaScript, PHP e arquivos JSON para armazenamento local.'
    ]],
    ['2. MANUAL DO USUÁRIO — ÁREA PÚBLICA', [
        'Navegação: acesse a página inicial e clique em Ver Produtos para abrir o catálogo.',
        'Use as categorias para filtrar os produtos e clique em um item para ver detalhes, preço e descrição.',
        'Contato: acesse a opção Contato no menu e clique no e-mail exibido para falar com o suporte.',
        'Ao pedir ajuda, informe o produto e descreva a dúvida para facilitar o atendimento.'
    ]],
    ['3. CONTA DO CLIENTE', [
        'Cadastro: clique no ícone de usuário, abra a aba Register e preencha nome, e-mail e senha.',
        'A senha deve ter pelo menos 6 caracteres e o e-mail deve ser único no sistema.',
        'Clique em Cadastrar para concluir a criação da conta.',
        'Login: abra a aba Login, informe o e-mail e a senha e clique em Entrar.',
        'Para sair da conta, abra o menu do usuário e clique em Desconectar.',
        'O login do cliente é diferente do acesso usado no painel administrativo.'
    ]],
    ['4. COMO REALIZAR UMA COMPRA', [
        '1. Abra o produto desejado e defina a quantidade.',
        '2. Clique no botão para adicionar o produto ao carrinho.',
        '3. Faça login caso o sistema solicite autenticação.',
        '4. Abra o carrinho e confira os itens e o valor total.',
        '5. Remova itens desnecessários e clique em finalizar.',
        '6. Informe endereço completo, cidade e CEP e confirme o pedido.',
        'Importante: o checkout é uma simulação. Não há Pix, cartão, cálculo de frete ou cobrança online.'
    ]],
    ['5. PAINEL ADMINISTRATIVO', [
        'Acesso: abra admin/login.php, informe o usuário e a senha de administrador e clique em Entrar.',
        'Somente usuários administrativos cadastrados podem acessar o painel.',
        'Dashboard: entrada principal do painel.',
        'Categorias: cria, altera e remove os grupos de produtos.',
        'Produtos: cadastra, lista e remove os itens exibidos na loja.',
        'Equipe / Usuários: gerencia as pessoas que acessam o painel.',
        'Fornecedores: cadastra e mantém as informações comerciais dos parceiros.',
        'Documentação: apresenta este manual e permite o download em PDF.'
    ]],
    ['6. CADASTROS NO PAINEL', [
        'Categorias: clique em Categorias, informe o nome e clique em Criar Categoria. Use Editar ou Remover na listagem.',
        'Produtos: clique em Produtos, preencha nome, preço, categoria, imagem e descrição e clique em Cadastrar Produto.',
        'Imagens de produto aceitas: JPG, PNG, WEBP e GIF.',
        'Administradores: em Equipe / Usuários, informe nome, usuário de login e senha para criar um novo acesso.',
        'Fornecedores: informe nome, CNPJ, telefone, CEP, rua, número, bairro e cidade e clique em Cadastrar Fornecedor.',
        'Use Editar e Remover nas tabelas de listagem para atualizar os registros existentes.'
    ]],
    ['7. ORDEM CORRETA DE CADASTROS', [
        '1. Cadastre as categorias primeiro, pois cada produto precisa usar uma categoria existente.',
        '2. Cadastre os fornecedores para organizar os dados dos parceiros comerciais.',
        '3. Cadastre os produtos, selecionando uma categoria criada anteriormente.',
        '4. Cadastre administradores apenas para pessoas que devem gerenciar a loja.',
        'Boa prática: não remova uma categoria sem verificar se ela já é usada por produtos.'
    ]],
    ['8. DADOS E ARMAZENAMENTO', [
        'data/produtos.json: produtos exibidos no catálogo.',
        'data/categorias.json: categorias disponíveis.',
        'data/clientes.json: contas de clientes da loja.',
        'data/usuarios.json: acessos de administradores.',
        'data/fornecedores.json: dados comerciais dos fornecedores.',
        'uploads/produtos/: imagens enviadas no cadastro de produtos.',
        'O PHP precisa ter permissão de escrita nas pastas data/ e uploads/produtos/ para salvar os cadastros.'
    ]],
    ['9. DÚVIDAS FREQUENTES', [
        'Não consigo adicionar ao carrinho: faça login em uma conta de cliente.',
        'Não aparece categoria no cadastro do produto: cadastre uma categoria antes de criar o produto.',
        'Quais imagens são aceitas: JPG, PNG, WEBP e GIF.',
        'O pedido está pago após finalizar: não; a finalização é apenas simulada.',
        'Como salvar o manual: clique em Baixar em PDF no topo da página Documentação.'
    ]]
];

$pages = []; $lines = [];
foreach ($sections as [$title, $items]) {
    if (count($lines) > 37) { $pages[] = $lines; $lines = []; }
    $lines[] = ['title', $title];
    foreach ($items as $item) {
        $wrapped = wordwrap($item, 92, "\n", true);
        foreach (explode("\n", $wrapped) as $line) {
            if (count($lines) >= 42) { $pages[] = $lines; $lines = []; }
            $lines[] = ['text', $line];
        }
        $lines[] = ['space', ''];
    }
}
if ($lines) $pages[] = $lines;

$objects = []; $pageObjectNumbers = []; $next = 4;
foreach ($pages as $_) { $pageObjectNumbers[] = $next++; }
$contentObjectNumbers = []; foreach ($pages as $_) { $contentObjectNumbers[] = $next++; }
$objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
$kids = implode(' ', array_map(fn($n) => "$n 0 R", $pageObjectNumbers));
$objects[2] = '<< /Type /Pages /Kids [' . $kids . '] /Count ' . count($pages) . ' >>';
$objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
foreach ($pages as $index => $page) {
    $content = "BT\n/F1 10 Tf\n50 790 Td\n";
    foreach ($page as [$type, $line]) {
        if ($type === 'title') $content .= "/F1 13 Tf\n(" . pdfEscape(pdfText($line)) . ") Tj\n/F1 10 Tf\n0 -22 Td\n";
        elseif ($type === 'text') $content .= "(" . pdfEscape(pdfText($line)) . ") Tj\n0 -14 Td\n";
        else $content .= "0 -5 Td\n";
    }
    $content .= "ET";
    $objects[$pageObjectNumbers[$index]] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents ' . $contentObjectNumbers[$index] . ' 0 R >>';
    $objects[$contentObjectNumbers[$index]] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
}
ksort($objects); $pdf = "%PDF-1.4\n"; $offsets = [0];
foreach ($objects as $number => $object) { $offsets[$number] = strlen($pdf); $pdf .= "$number 0 obj\n$object\nendobj\n"; }
$xref = strlen($pdf); $pdf .= 'xref' . "\n0 " . ($next) . "\n0000000000 65535 f \n";
for ($i = 1; $i < $next; $i++) $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
$pdf .= "trailer\n<< /Size $next /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
header('Content-Type: application/pdf'); header('Content-Disposition: attachment; filename="documentacao-pixel-store.pdf"'); header('Content-Length: ' . strlen($pdf)); echo $pdf;
