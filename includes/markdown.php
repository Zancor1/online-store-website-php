<?php
/**
 * Conversor simples de Markdown para os arquivos da pasta docs/.
 *
 * Objetivo: ler os .md reais de docs/ e transformá-los em blocos estruturados
 * que podem ser exibidos tanto em HTML (admin/pages/documentacao.php) quanto
 * em texto simples paginado (admin/documentacao_pdf.php). Assim, o conteúdo
 * mostrado no painel e no PDF gerado sempre reflete o que está escrito nos
 * arquivos .md, sem precisar copiar/colar o texto em dois lugares.
 */

/**
 * Divide o markdown em uma lista de blocos (heading, para, list, code, table, quote, hr).
 */
function docsParseMarkdown(string $md): array
{
    $md = str_replace("\r\n", "\n", $md);
    $lines = explode("\n", $md);
    $blocks = [];
    $i = 0;
    $n = count($lines);

    while ($i < $n) {
        $trim = trim($lines[$i]);

        if ($trim === '') {
            $i++;
            continue;
        }

        // Bloco de código ```...```
        if (preg_match('/^```/', $trim)) {
            $code = [];
            $i++;
            while ($i < $n && !preg_match('/^```/', trim($lines[$i]))) {
                $code[] = $lines[$i];
                $i++;
            }
            $i++; // pula a cerca de fechamento
            $blocks[] = ['type' => 'code', 'text' => implode("\n", $code)];
            continue;
        }

        // Título (#, ##, ### ...)
        if (preg_match('/^(#{1,6})\s+(.*)$/', $trim, $m)) {
            $blocks[] = ['type' => 'heading', 'level' => strlen($m[1]), 'text' => trim($m[2])];
            $i++;
            continue;
        }

        // Linha horizontal
        if (preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trim)) {
            $blocks[] = ['type' => 'hr'];
            $i++;
            continue;
        }

        // Tabela: linha com | seguida de linha separadora ---|---
        if (
            strpos($trim, '|') !== false && $i + 1 < $n &&
            preg_match('/^\|?[\s:\-\|]+\|?[\s:\-\|]*$/', trim($lines[$i + 1])) &&
            strpos(trim($lines[$i + 1]), '-') !== false
        ) {
            $rows = [docsSplitTableRow($trim)];
            $i += 2; // pula cabeçalho + separador
            while ($i < $n && trim($lines[$i]) !== '' && strpos(trim($lines[$i]), '|') !== false) {
                $rows[] = docsSplitTableRow(trim($lines[$i]));
                $i++;
            }
            $blocks[] = ['type' => 'table', 'rows' => $rows];
            continue;
        }

        // Citação
        if (preg_match('/^>\s?(.*)$/', $trim, $m)) {
            $quote = [$m[1]];
            $i++;
            while ($i < $n && preg_match('/^>\s?(.*)$/', trim($lines[$i]), $m2)) {
                $quote[] = $m2[1];
                $i++;
            }
            $blocks[] = ['type' => 'quote', 'text' => implode(' ', array_filter($quote))];
            continue;
        }

        // Lista com marcador (-, *, +) ou numerada (1.)
        if (preg_match('/^([\-\*\+]|\d+\.)\s+(.*)$/', $trim, $m)) {
            $ordered = (bool) preg_match('/^\d+\./', $m[1]);
            $items = [];
            while ($i < $n) {
                $t = trim($lines[$i]);
                if ($t === '') {
                    $i++;
                    break;
                }
                if (preg_match('/^([\-\*\+]|\d+\.)\s+(.*)$/', $t, $mi)) {
                    $items[] = $mi[2];
                    $i++;
                } elseif (preg_match('/^\s{2,}\S/', $lines[$i]) && $items) {
                    // continuação indentada do item anterior
                    $items[count($items) - 1] .= ' ' . $t;
                    $i++;
                } else {
                    break;
                }
            }
            $blocks[] = ['type' => 'list', 'ordered' => $ordered, 'items' => $items];
            continue;
        }

        // Parágrafo: junta linhas até achar uma linha em branco ou início de outro bloco
        $para = [$trim];
        $i++;
        while ($i < $n) {
            $t = trim($lines[$i]);
            if ($t === '' || preg_match('/^(#{1,6}\s|```|\||>|[\-\*\+]\s|\d+\.\s|-{3,}$)/', $t)) {
                break;
            }
            $para[] = $t;
            $i++;
        }
        $blocks[] = ['type' => 'para', 'text' => implode(' ', $para)];
    }

    return $blocks;
}

function docsSplitTableRow(string $row): array
{
    $row = trim($row, "| \t");
    return array_map('trim', explode('|', $row));
}

function docsInlineToHtml(string $text): string
{
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<span class="md-link">$1</span>', $text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    $text = preg_replace('/\*\*([^\*]+)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/(?<!\*)\*([^\*]+)\*(?!\*)/', '<em>$1</em>', $text);
    return $text;
}

function docsInlineToPlain(string $text): string
{
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '$1', $text);
    $text = preg_replace('/`([^`]+)`/', '$1', $text);
    $text = preg_replace('/\*\*([^\*]+)\*\*/', '$1', $text);
    $text = preg_replace('/(?<!\*)\*([^\*]+)\*(?!\*)/', '$1', $text);
    return trim($text);
}

/**
 * Converte os blocos em HTML pronto para exibir dentro de admin/pages/documentacao.php.
 */
function docsBlocksToHtml(array $blocks): string
{
    $html = '';
    foreach ($blocks as $block) {
        switch ($block['type']) {
            case 'heading':
                $cls = 'md-h' . min((int) $block['level'], 4);
                $html .= '<p class="' . $cls . '">' . docsInlineToHtml($block['text']) . '</p>' . "\n";
                break;
            case 'para':
                $html .= '<p class="md-p">' . docsInlineToHtml($block['text']) . '</p>' . "\n";
                break;
            case 'list':
                $tag = $block['ordered'] ? 'ol' : 'ul';
                $html .= "<$tag class=\"md-list\">";
                foreach ($block['items'] as $li) {
                    $html .= '<li>' . docsInlineToHtml($li) . '</li>';
                }
                $html .= "</$tag>\n";
                break;
            case 'code':
                $html .= '<pre class="md-pre"><code>' . htmlspecialchars($block['text'], ENT_QUOTES, 'UTF-8') . '</code></pre>' . "\n";
                break;
            case 'quote':
                $html .= '<p class="md-quote">' . docsInlineToHtml($block['text']) . '</p>' . "\n";
                break;
            case 'table':
                $rows = $block['rows'];
                $header = array_shift($rows);
                $html .= '<table class="md-table"><thead><tr>';
                foreach ($header as $h) {
                    $html .= '<th>' . docsInlineToHtml($h) . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>';
                    foreach ($row as $c) {
                        $html .= '<td>' . docsInlineToHtml($c) . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>' . "\n";
                break;
            case 'hr':
                $html .= '<hr class="md-hr">' . "\n";
                break;
        }
    }
    return $html;
}

/**
 * Converte os blocos no mesmo formato [titulo, [linhas...]] usado pelo
 * gerador de PDF em admin/documentacao_pdf.php.
 */
function docsBlocksToPdfSections(array $blocks, string $fallbackTitle): array
{
    $sections = [];
    $currentTitle = mb_strtoupper($fallbackTitle);
    $currentItems = [];

    $flush = function () use (&$sections, &$currentTitle, &$currentItems) {
        if ($currentItems) {
            $sections[] = [$currentTitle, $currentItems];
        }
        $currentItems = [];
    };

    foreach ($blocks as $block) {
        switch ($block['type']) {
            case 'heading':
                $flush();
                $currentTitle = $block['level'] <= 2
                    ? mb_strtoupper(docsInlineToPlain($block['text']))
                    : docsInlineToPlain($block['text']);
                break;
            case 'para':
                $currentItems[] = docsInlineToPlain($block['text']);
                break;
            case 'list':
                foreach ($block['items'] as $idx => $li) {
                    $prefix = $block['ordered'] ? ($idx + 1) . '. ' : '- ';
                    $currentItems[] = $prefix . docsInlineToPlain($li);
                }
                break;
            case 'code':
                foreach (explode("\n", $block['text']) as $ln) {
                    if (trim($ln) !== '') {
                        $currentItems[] = '  ' . rtrim($ln);
                    }
                }
                break;
            case 'quote':
                $currentItems[] = '"' . docsInlineToPlain($block['text']) . '"';
                break;
            case 'table':
                foreach ($block['rows'] as $row) {
                    $currentItems[] = implode('  |  ', array_map('docsInlineToPlain', $row));
                }
                break;
            case 'hr':
                break;
        }
    }
    $flush();

    return $sections;
}

/** Lê um arquivo .md de docs/ e devolve o HTML já convertido. */
function docsFileToHtml(string $path): string
{
    if (!is_file($path)) {
        return '<p class="md-p">Arquivo não encontrado.</p>';
    }
    return docsBlocksToHtml(docsParseMarkdown((string) file_get_contents($path)));
}

/** Lê um arquivo .md de docs/ e devolve as seções prontas para o PDF. */
function docsFileToPdfSections(string $path, string $fallbackTitle): array
{
    if (!is_file($path)) {
        return [];
    }
    return docsBlocksToPdfSections(docsParseMarkdown((string) file_get_contents($path)), $fallbackTitle);
}

/**
 * Lista oficial dos documentos da pasta docs/, na mesma ordem do índice em docs/README.md.
 * Usada tanto pela página do painel quanto pelo PDF para não perder nenhum arquivo.
 */
function docsFileList(): array
{
    return [
        ['file' => 'README.md', 'title' => 'Visão geral e introdução', 'slug' => 'readme'],
        ['file' => 'requisitos-funcionais.md', 'title' => 'Requisitos funcionais (RF01–RF25)', 'slug' => 'requisitos-funcionais'],
        ['file' => 'requisitos-nao-funcionais.md', 'title' => 'Requisitos não funcionais (RNF01–RNF15)', 'slug' => 'requisitos-nao-funcionais'],
        ['file' => 'casos-de-uso.md', 'title' => 'Casos de uso e fluxos', 'slug' => 'casos-de-uso'],
        ['file' => 'arquitetura.md', 'title' => 'Arquitetura e estrutura do projeto', 'slug' => 'arquitetura'],
        ['file' => 'cadastros-movimento.md', 'title' => 'Cadastros (CRUD) e movimento de compra', 'slug' => 'cadastros-movimento'],
        ['file' => 'telas-sistema.md', 'title' => 'Front-end público e área administrativa', 'slug' => 'telas-sistema'],
        ['file' => 'modelo-dados.md', 'title' => 'Entidades e modelo de dados', 'slug' => 'modelo-dados'],
        ['file' => 'dicionario-dados.md', 'title' => 'Dicionário de dados', 'slug' => 'dicionario-dados'],
        ['file' => 'diagramas.md', 'title' => 'Diagramas UML (Mermaid)', 'slug' => 'diagramas'],
        ['file' => 'seguranca.md', 'title' => 'Segurança (auditoria do código)', 'slug' => 'seguranca'],
        ['file' => 'validacoes.md', 'title' => 'Validações existentes', 'slug' => 'validacoes'],
        ['file' => 'testes.md', 'title' => 'Casos de teste', 'slug' => 'testes'],
        ['file' => 'manual-usuario.md', 'title' => 'Manual do usuário', 'slug' => 'manual-usuario'],
        ['file' => 'manual-administrador.md', 'title' => 'Manual do administrador', 'slug' => 'manual-administrador'],
        ['file' => 'matriz-rastreabilidade.md', 'title' => 'Matriz de rastreabilidade', 'slug' => 'matriz-rastreabilidade'],
        ['file' => 'limitacoes-melhorias.md', 'title' => 'Limitações e melhorias futuras', 'slug' => 'limitacoes-melhorias'],
        ['file' => 'checklist-academico.md', 'title' => 'Checklist acadêmico', 'slug' => 'checklist-academico'],
    ];
}
