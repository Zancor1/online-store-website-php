<?php
// Carrega os arquivos reais da pasta docs/ (raiz do projeto) para exibir o
// conteúdo completo aqui dentro, além do resumo já existente abaixo.
require_once __DIR__ . '/../../includes/markdown.php';
$docsFolder = __DIR__ . '/../../docs/';
$docsList = docsFileList();
?>
<style>
    .docs { display: grid; gap: 20px; color: #d1d5db; }
    .docs-card { padding: 24px; border: 1px solid #374151; border-radius: 12px; background: #1f2937; }
    .docs-hero { display: flex; align-items: center; justify-content: space-between; gap: 20px; background: linear-gradient(135deg, #312e81, #1f2937 62%); }
    .docs h1, .docs h2 { margin: 0; color: #fff; }
    .docs h1 { font-size: 28px; }.docs h2 { font-size: 18px; }.docs h3 { margin: 18px 0 6px; color: #f3f4f6; font-size: 15px; }
    .docs p { margin: 8px 0 0; font-size: 14px; line-height: 1.65; }.docs ul, .docs ol { margin: 10px 0 0; padding-left: 20px; font-size: 14px; line-height: 1.75; }
    .docs-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }.docs code { padding: 2px 5px; border-radius: 4px; background: #111827; color: #c4b5fd; }
    .docs-note { margin-top: 16px !important; padding: 12px 14px; border-left: 3px solid #f59e0b; border-radius: 0 6px 6px 0; background: rgb(245 158 11 / .08); }
    .docs-steps { counter-reset: step; list-style: none; padding: 0; }.docs-steps li { position: relative; min-height: 34px; padding: 3px 0 8px 42px; }.docs-steps li::before { counter-increment: step; content: counter(step); position: absolute; left: 0; top: 2px; display: grid; place-items: center; width: 27px; height: 27px; border-radius: 50%; background: #4f46e5; color: #fff; font-size: 12px; font-weight: 800; }
    .docs-download { display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex: 0 0 auto; padding: 12px 16px; border-radius: 8px; background: #fff; color: #312e81; font-size: 14px; font-weight: 800; transition: transform .15s ease, background .15s ease; }.docs-download:hover { transform: translateY(-1px); background: #ede9fe; }
    .docs-toc { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-top: 16px; }.docs-toc a { padding: 10px; border-radius: 7px; background: #111827; color: #c4b5fd; font-size: 13px; font-weight: 700; }.docs-toc a:hover { background: #312e81; color: #fff; }
    .docs-table { width: 100%; margin-top: 14px; border-collapse: collapse; font-size: 14px; }.docs-table th, .docs-table td { padding: 11px; border: 1px solid #374151; text-align: left; vertical-align: top; }.docs-table th { background: #111827; color: #fff; }
    @media (max-width: 700px) { .docs-grid, .docs-toc { grid-template-columns: 1fr; }.docs-card { padding: 18px; }.docs-hero { align-items: flex-start; flex-direction: column; }.docs-download { width: 100%; } }

    /* Conteúdo renderizado a partir dos arquivos .md da pasta docs/ */
    .docs-doc { border: 1px solid #374151; border-radius: 10px; background: #161c2b; overflow: hidden; }
    .docs-doc + .docs-doc { margin-top: 12px; }
    .docs-doc summary { list-style: none; cursor: pointer; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; font-weight: 700; color: #f3f4f6; font-size: 14px; }
    .docs-doc summary::-webkit-details-marker { display: none; }
    .docs-doc summary .docs-doc-file { font-weight: 500; color: #9ca3af; font-size: 12px; font-family: monospace; }
    .docs-doc summary::after { content: '+'; font-size: 18px; color: #a78bfa; }
    .docs-doc[open] summary::after { content: '−'; }
    .docs-doc-body { padding: 4px 20px 20px; border-top: 1px solid #374151; }
    .md-h1, .md-h2 { color: #fff; font-weight: 800; margin: 18px 0 8px; }
    .md-h1 { font-size: 19px; }
    .md-h2 { font-size: 16px; }
    .md-h3, .md-h4 { color: #e5e7eb; font-weight: 700; margin: 14px 0 6px; font-size: 14px; }
    .md-p { margin: 8px 0 0; font-size: 13.5px; line-height: 1.7; color: #d1d5db; }
    .md-list { margin: 8px 0 0; padding-left: 20px; font-size: 13.5px; line-height: 1.75; color: #d1d5db; }
    .md-list li { margin-bottom: 3px; }
    .md-pre { margin: 10px 0 0; padding: 12px 14px; border-radius: 8px; background: #0b0f19; overflow-x: auto; }
    .md-pre code { background: none; padding: 0; color: #93c5fd; font-size: 12.5px; white-space: pre; }
    .md-table { width: 100%; margin-top: 10px; border-collapse: collapse; font-size: 13px; }
    .md-table th, .md-table td { padding: 8px 10px; border: 1px solid #374151; text-align: left; vertical-align: top; }
    .md-table th { background: #111827; color: #fff; }
    .md-quote { margin: 10px 0 0; padding: 10px 14px; border-left: 3px solid #6366f1; background: rgb(99 102 241 / .08); font-style: italic; color: #c7d2fe; font-size: 13.5px; }
    .md-hr { border: none; border-top: 1px solid #374151; margin: 16px 0; }
    .md-link { color: #a78bfa; text-decoration: underline; text-decoration-style: dotted; }
</style>

<section class="docs">
    <div class="docs-card docs-hero">
        <div>
            <p style="margin:0;color:#c4b5fd;font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase">Pixel Store</p>
            <h1>Documentação do sistema</h1>
            <p>Manual completo de uso da loja virtual e do painel administrativo.</p>
        </div>
        <a class="docs-download" href="../docs/Documentacao.pdf" download>↓ Baixar em PDF</a>
    </div>

    <article class="docs-card">
        <h2>Índice</h2>
        <nav class="docs-toc" aria-label="Índice da documentação">
            <a href="#academica">0. Documentação acadêmica</a>
            <a href="#apresentacao">1. Apresentação</a>
            <a href="#publico">2. Área pública</a>
            <a href="#conta">3. Conta do cliente</a>
            <a href="#compra">4. Compra</a>
            <a href="#admin">5. Painel admin</a>
            <a href="#cadastros">6. Cadastros</a>
            <a href="#ordem">7. Ordem de cadastro</a>
            <a href="#dados">8. Dados do sistema</a>
            <a href="#duvidas">9. Dúvidas frequentes</a>
            <a href="#docs-completa">10. Documentação completa (docs/)</a>
        </nav>
    </article>

    <article class="docs-card" id="academica">
        <h2>0. Documentação acadêmica completa</h2>
        <p>A documentação técnica completa para entrega acadêmica está na pasta <code>docs/</code> na raiz do projeto, e o conteúdo de cada arquivo também é exibido integralmente logo abaixo, na seção <a href="#docs-completa">10. Documentação completa</a> — e incluído no PDF baixado pelo botão acima.</p>
        <table class="docs-table">
            <thead><tr><th>Documento</th><th>Conteúdo</th></tr></thead>
            <tbody>
                <?php foreach ($docsList as $doc): if ($doc['slug'] === 'readme') continue; ?>
                <tr><td><a class="md-link" href="#doc-<?php echo $doc['slug']; ?>"><code>docs/<?php echo $doc['file']; ?></code></a></td><td><?php echo htmlspecialchars($doc['title']); ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="docs-note"><strong>Importante:</strong> a documentação acadêmica descreve somente funcionalidades existentes no código. Itens como pagamento, frete, estoque e MySQL estão listados como não implementados.</p>
    </article>

    <article class="docs-card" id="apresentacao">
        <h2>1. Apresentação do projeto</h2>
        <h3>Sobre a Pixel Store</h3>
        <p>A Pixel Store é uma loja virtual voltada a produtos de tecnologia. A loja reúne itens de categorias como periféricos, áudio e hardware e disponibiliza uma navegação simples, com catálogo, página de detalhes, carrinho e confirmação de pedido.</p>
        <h3>Motivo da escolha do tema</h3>
        <p>O tema foi escolhido por ser atual e permitir a organização de produtos variados em categorias. Ele também possibilita demonstrar funcionalidades comuns de um comércio eletrônico, como cadastro, autenticação, carrinho e gerenciamento de itens.</p>
        <h3>Tecnologias utilizadas</h3>
        <table class="docs-table">
            <thead><tr><th>Tecnologia</th><th>Uso no projeto</th></tr></thead>
            <tbody>
                <tr><td>HTML5</td><td>Estrutura das páginas da loja e do painel.</td></tr>
                <tr><td>CSS3</td><td>Estilos, responsividade e aparência visual das telas.</td></tr>
                <tr><td>JavaScript</td><td>Interações executadas no navegador.</td></tr>
                <tr><td>PHP</td><td>Regras do sistema, sessões, login, cadastros e geração do PDF.</td></tr>
                <tr><td>Tailwind CSS (CDN)</td><td>Estilos responsivos da loja pública.</td></tr>
                <tr><td>JSON</td><td>Armazenamento local de produtos, clientes, usuários, categorias, fornecedores e pedidos.</td></tr>
            </tbody>
        </table>
    </article>

    <article class="docs-card" id="publico">
        <h2>2. Manual do usuário — área pública</h2>
        <p>Esta área é destinada aos visitantes da loja. O visitante pode navegar pelo catálogo, consultar informações dos produtos, entrar em contato e criar uma conta.</p>
        <h3>Navegação inicial</h3>
        <ol class="docs-steps">
            <li>Acesse a página inicial da Pixel Store.</li>
            <li>Observe o destaque principal e os atalhos disponíveis no menu.</li>
            <li>Clique em <strong>Ver Produtos</strong> para abrir o catálogo.</li>
            <li>Use os filtros de categoria para localizar produtos semelhantes.</li>
            <li>Clique no nome ou na imagem de um produto para ver seus detalhes.</li>
        </ol>
        <h3>Como consultar um produto</h3>
        <ol class="docs-steps">
            <li>Abra a página <strong>Produtos</strong>.</li>
            <li>Escolha uma categoria ou percorra a lista completa.</li>
            <li>Clique no produto desejado.</li>
            <li>Leia a descrição e confira o preço apresentado.</li>
            <li>Selecione a quantidade antes de adicioná-lo ao carrinho.</li>
        </ol>
        <h3>Contato com a equipe</h3>
        <p>Para pedir ajuda, acesse a opção <strong>Contato</strong> no menu. Clique no endereço de e-mail exibido na tela e escreva a sua mensagem. Informe o nome do produto e descreva a dúvida para facilitar o atendimento.</p>
    </article>

    <div class="docs-grid" id="conta">
        <article class="docs-card">
            <h2>3. Cadastro de conta</h2>
            <p>O cadastro cria uma conta de cliente. Essa conta é diferente dos usuários que acessam o painel administrativo.</p>
            <ol class="docs-steps">
                <li>Clique no ícone de usuário no topo da loja.</li>
                <li>Selecione a aba <strong>Register</strong>.</li>
                <li>Preencha seu nome completo.</li>
                <li>Informe um e-mail válido.</li>
                <li>Crie uma senha com, no mínimo, 6 caracteres.</li>
                <li>Clique em <strong>Cadastrar</strong>.</li>
            </ol>
            <p class="docs-note"><strong>Importante:</strong> use um e-mail que ainda não esteja cadastrado. Cada conta precisa de um e-mail único.</p>
        </article>
        <article class="docs-card">
            <h2>Como fazer login e sair</h2>
            <ol class="docs-steps">
                <li>Clique no ícone de usuário.</li>
                <li>Abra a aba <strong>Login</strong>.</li>
                <li>Digite o e-mail usado no cadastro.</li>
                <li>Digite sua senha.</li>
                <li>Clique em <strong>Entrar</strong>.</li>
            </ol>
            <p>Depois de entrar, o ícone de usuário passa a mostrar os dados da conta. Para encerrar o acesso, abra esse menu e clique em <strong>Desconectar</strong>.</p>
            <p class="docs-note"><strong>Segurança:</strong> não compartilhe sua senha com outras pessoas e desconecte-se ao usar computadores públicos.</p>
        </article>
    </div>

    <article class="docs-card" id="compra">
        <h2>4. Como realizar uma compra</h2>
        <p>Para adicionar produtos e concluir um pedido, o cliente deve estar conectado à sua conta.</p>
        <h3>Adicionar ao carrinho</h3>
        <ol class="docs-steps">
            <li>Abra a página de detalhes do produto.</li>
            <li>Defina a quantidade desejada.</li>
            <li>Clique no botão de adicionar ao carrinho.</li>
            <li>Se ainda não estiver logado, faça login ou crie uma conta.</li>
            <li>Após o acesso, volte ao produto e repita a ação.</li>
        </ol>
        <h3>Revisar o carrinho</h3>
        <ol class="docs-steps">
            <li>Abra o carrinho pelo menu da loja.</li>
            <li>Confira os itens incluídos e o valor total.</li>
            <li>Remova um item caso não deseje mais comprá-lo.</li>
            <li>Clique em finalizar quando a lista estiver correta.</li>
        </ol>
        <h3>Finalizar pedido</h3>
        <ol class="docs-steps">
            <li>Informe o endereço completo de entrega.</li>
            <li>Preencha a cidade.</li>
            <li>Informe o CEP.</li>
            <li>Confirme os dados para concluir o pedido.</li>
        </ol>
        <p class="docs-note"><strong>Observação:</strong> o checkout é demonstrativo. O sistema confirma o pedido e limpa o carrinho, mas não calcula frete e não realiza cobrança por Pix, cartão ou outro meio de pagamento.</p>
    </article>

    <article class="docs-card" id="admin">
        <h2>5. Manual do administrador — área privada</h2>
        <h3>Acesso ao painel</h3>
        <ol class="docs-steps">
            <li>Acesse o endereço <code>admin/login.php</code>.</li>
            <li>Digite o usuário de administrador.</li>
            <li>Digite a senha correspondente.</li>
            <li>Clique no botão para entrar no painel.</li>
        </ol>
        <p>Somente administradores cadastrados podem utilizar essa área. Caso não esteja autenticado, o sistema redireciona a navegação para a tela de login.</p>
        <h3>Navegação do painel</h3>
        <table class="docs-table">
            <thead><tr><th>Menu</th><th>Finalidade</th></tr></thead>
            <tbody>
                <tr><td>Dashboard</td><td>Entrada principal do painel administrativo.</td></tr>
                <tr><td>Categorias</td><td>Criação, edição e remoção dos grupos de produtos.</td></tr>
                <tr><td>Produtos</td><td>Cadastro, visualização e remoção dos itens da vitrine.</td></tr>
                <tr><td>Equipe / Usuários</td><td>Gerenciamento das pessoas com acesso administrativo.</td></tr>
                <tr><td>Fornecedores</td><td>Cadastro e manutenção dos dados dos fornecedores.</td></tr>
                <tr><td>Documentação</td><td>Consulta deste manual e download do PDF.</td></tr>
            </tbody>
        </table>
    </article>

    <div class="docs-grid" id="cadastros">
        <article class="docs-card">
            <h2>6.1 Gerenciar categorias</h2>
            <ol class="docs-steps">
                <li>No painel, clique em <strong>Categorias</strong>.</li>
                <li>Digite o nome da nova categoria.</li>
                <li>Clique em <strong>Criar Categoria</strong>.</li>
                <li>Confira a categoria na tabela de listagem.</li>
            </ol>
            <h3>Editar ou remover</h3>
            <p>Na linha da categoria desejada, clique em <strong>Editar</strong> para alterar o nome. Para apagar o registro, clique em <strong>Remover</strong>.</p>
        </article>
        <article class="docs-card">
            <h2>6.2 Gerenciar produtos</h2>
            <ol class="docs-steps">
                <li>No painel, clique em <strong>Produtos</strong>.</li>
                <li>Preencha nome, preço e descrição.</li>
                <li>Selecione uma categoria já cadastrada.</li>
                <li>Escolha uma imagem para o produto. Para item simples, esta será a foto exibida na loja.</li>
                <li>Se o item tiver opções, marque <strong>Este produto possui variações</strong>.</li>
                <li>Adicione cada variação, como <strong>Tamanho 38</strong>, e envie uma imagem própria para ela.</li>
                <li>Clique em <strong>Cadastrar Produto</strong>.</li>
            </ol>
            <p>Exemplo: para um tênis, cadastre Tamanho 38, 39 e 40. Na página do produto, o cliente escolhe a opção e vê a respectiva imagem; o carrinho registra a variação selecionada. São aceitas imagens JPG, PNG, WEBP e GIF. Para retirar um produto da vitrine, use o botão <strong>Remover</strong> presente na listagem.</p>
        </article>
    </div>

    <div class="docs-grid">
        <article class="docs-card">
            <h2>6.3 Gerenciar administradores</h2>
            <ol class="docs-steps">
                <li>Clique em <strong>Equipe / Usuários</strong>.</li>
                <li>Informe o nome completo do administrador.</li>
                <li>Defina o usuário de login.</li>
                <li>Informe a senha de acesso.</li>
                <li>Clique no botão de cadastro.</li>
            </ol>
            <p>Use os controles da listagem para editar dados ou remover um administrador que não deve mais acessar o painel.</p>
        </article>
        <article class="docs-card">
            <h2>6.4 Gerenciar fornecedores</h2>
            <ol class="docs-steps">
                <li>Clique em <strong>Fornecedores</strong>.</li>
                <li>Informe nome, CNPJ e telefone.</li>
                <li>Preencha CEP, rua, número, bairro e cidade.</li>
                <li>Clique em <strong>Cadastrar Fornecedor</strong>.</li>
                <li>Utilize <strong>Editar</strong> ou <strong>Remover</strong> quando necessário.</li>
            </ol>
            <p>Preencha os dados com atenção para manter as informações comerciais organizadas.</p>
        </article>
    </div>

    <article class="docs-card" id="ordem">
        <h2>7. Ordem correta de cadastros</h2>
        <p>Alguns dados do sistema dependem de outros. Siga esta ordem para evitar problemas durante o cadastro de produtos:</p>
        <ol class="docs-steps">
            <li><strong>Cadastre categorias primeiro.</strong> Todo produto precisa estar ligado a uma categoria existente.</li>
            <li><strong>Cadastre fornecedores.</strong> Registre os parceiros comerciais e seus dados de contato.</li>
            <li><strong>Cadastre os produtos.</strong> Escolha a categoria criada anteriormente e preencha os dados do item.</li>
            <li><strong>Cadastre administradores quando necessário.</strong> Crie acessos apenas para pessoas responsáveis pelo gerenciamento.</li>
        </ol>
        <p class="docs-note"><strong>Regra de ouro:</strong> não remova uma categoria sem antes verificar se existem produtos usando esse agrupamento. A categoria pode continuar aparecendo como referência nos itens já cadastrados.</p>
    </article>

    <article class="docs-card" id="dados">
        <h2>8. Dados e armazenamento do sistema</h2>
        <p>A aplicação utiliza arquivos JSON como banco de dados local. Cada arquivo reúne um tipo de informação do sistema.</p>
        <table class="docs-table">
            <thead><tr><th>Local</th><th>Conteúdo</th></tr></thead>
            <tbody>
                <tr><td><code>data/produtos.json</code></td><td>Produtos exibidos no catálogo, incluindo nome e imagem de cada variação.</td></tr>
                <tr><td><code>data/categorias.json</code></td><td>Categorias disponíveis para os produtos.</td></tr>
                <tr><td><code>data/clientes.json</code></td><td>Contas criadas pelos clientes da loja.</td></tr>
                <tr><td><code>data/usuarios.json</code></td><td>Usuários com acesso ao painel administrativo.</td></tr>
                <tr><td><code>data/fornecedores.json</code></td><td>Dados comerciais dos fornecedores.</td></tr>
                <tr><td><code>data/pedidos.json</code></td><td>Pedidos finalizados pelos clientes (checkout simulado).</td></tr>
                <tr><td><code>uploads/produtos/</code></td><td>Imagens enviadas ao cadastrar produtos.</td></tr>
            </tbody>
        </table>
        <p class="docs-note"><strong>Requisito do servidor:</strong> o PHP precisa ter permissão de escrita nas pastas <code>data/</code> e <code>uploads/produtos/</code>. Sem essa permissão, novos cadastros e imagens não poderão ser salvos.</p>
    </article>

    <article class="docs-card" id="duvidas">
        <h2>9. Dúvidas frequentes</h2>
        <h3>Por que não consigo adicionar um produto ao carrinho?</h3>
        <p>Faça login em uma conta de cliente. O carrinho e a finalização exigem que o usuário esteja autenticado.</p>
        <h3>Por que não aparece categoria para selecionar no produto?</h3>
        <p>Cadastre uma categoria no painel administrativo antes de iniciar o cadastro do produto.</p>
        <h3>Quais imagens posso enviar?</h3>
        <p>O cadastro de produtos aceita arquivos JPG, PNG, WEBP e GIF.</p>
        <h3>Como cadastro tamanhos, cores ou outros modelos?</h3>
        <p>Marque a opção de variações no cadastro do produto, adicione uma linha para cada opção e envie a foto correspondente. Cada variação precisa ter nome e imagem.</p>
        <h3>O pedido já está pago depois de finalizado?</h3>
        <p>Não. A finalização atual é apenas uma simulação e não possui integração com pagamento ou cálculo de frete.</p>
        <h3>Como salvar esta documentação?</h3>
        <p>Clique no botão <strong>Baixar em PDF</strong> no topo da página. O navegador fará o download do manual em formato PDF.</p>
    </article>

    <article class="docs-card" id="docs-completa">
        <h2>10. Documentação completa (arquivos da pasta docs/)</h2>
        <p>Abaixo está o conteúdo integral de cada arquivo Markdown da pasta <code>docs/</code>, lido diretamente do projeto — ou seja, sempre que um arquivo em <code>docs/</code> for atualizado, o texto exibido aqui (e no PDF baixado) acompanha a mudança automaticamente. Clique no título de um documento para abrir ou fechar o conteúdo.</p>
        <div class="docs-doclist">
            <?php foreach ($docsList as $doc): ?>
                <details class="docs-doc" id="doc-<?php echo $doc['slug']; ?>">
                    <summary>
                        <span><?php echo htmlspecialchars($doc['title']); ?></span>
                        <span class="docs-doc-file">docs/<?php echo $doc['file']; ?></span>
                    </summary>
                    <div class="docs-doc-body">
                        <?php echo docsFileToHtml($docsFolder . $doc['file']); ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </article>
</section>
