<?php
require_once "../includes/banco_ficticio.php";

$idExcluir = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($idExcluir) {
    if (excluirFornecedor($idExcluir)) {
        // Redireciona de volta injetando o aviso de sucesso na URL da listagem unificada
        header("Location: index.php?pg=fornecedores&sucesso=" . urlencode("Fornecedor removido com sucesso!"));
        exit;
    } else {
        header("Location: index.php?pg=fornecedores&erro=" . urlencode("Erro ao tentar remover o fornecedor."));
        exit;
    }
}

header("Location: index.php?pg=fornecedores");
exit;