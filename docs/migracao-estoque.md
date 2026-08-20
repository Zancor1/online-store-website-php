# Migração — Campo de Estoque

## Por que não há um arquivo `.sql`

Este projeto **não usa MySQL nem qualquer banco relacional**. Toda a "camada de banco de dados"
é simulada em `includes/banco_ficticio.php`, que lê e grava arquivos `.json` dentro de `data/`
(confirmado em `docs/modelo-dados.md` e no próprio código). Não existe schema SQL para alterar.

O equivalente a uma migração de banco, neste projeto, é uma alteração controlada nos arquivos
JSON de dados — é exatamente isso que foi feito.

## O que foi alterado

Arquivo: `data/produtos.json`

Foi adicionado o campo `"estoque"` (inteiro) a todos os produtos que ainda não o possuíam.
**Nenhum outro campo ou produto foi removido/alterado.** Produtos que já tivessem um campo de
estoque próprio seriam mantidos (não havia nenhum antes desta correção, então todos receberam
o valor padrão `20`).

Antes:
```json
{
  "id": 1,
  "nome": "Mouse Gamer Precision Pro",
  "preco": 189.9,
  "categoria": "Perifericos",
  "imagem": "...",
  "descricao": "..."
}
```

Depois:
```json
{
  "id": 1,
  "nome": "Mouse Gamer Precision Pro",
  "preco": 189.9,
  "categoria": "Perifericos",
  "imagem": "...",
  "descricao": "...",
  "estoque": 20
}
```

## Se este projeto estivesse em MySQL

Caso o professor/avaliador espere ver o equivalente em SQL (por exemplo, para comparar com um
projeto que use banco relacional de verdade), o comando equivalente seria:

```sql
ALTER TABLE produtos
  ADD COLUMN estoque INT NOT NULL DEFAULT 0;

UPDATE produtos
  SET estoque = 20
  WHERE estoque = 0;
```

Isso é apenas ilustrativo — **não se aplica a este projeto**, que não usa SQL.

## Reprodução do script usado

O ajuste foi feito com o script abaixo (Python, usado apenas por não haver PHP disponível no
ambiente de correção; o efeito é idêntico ao que o PHP faria lendo/gravando o mesmo JSON):

```python
import json
path = 'data/produtos.json'
with open(path, encoding='utf-8') as f:
    produtos = json.load(f)
for p in produtos:
    p.setdefault('estoque', 20)
with open(path, 'w', encoding='utf-8') as f:
    json.dump(produtos, f, indent=2, ensure_ascii=False)
```

Se você cadastrar novos produtos pelo painel administrativo, o campo `estoque` já é exigido no
formulário de cadastro (`admin/pages/produtos.php`) — não é necessário rodar nada manualmente.
