# Gestão de Tarefas (multiusuário) — HTML, CSS, PHP e RedBeanPHP

## Funcionalidades
- Autocadastro (nome, e-mail, senha) **sem confirmação por e-mail**; o usuário já entra logado.
- Login / logout. Senhas guardadas com `password_hash`.
- Cada usuário vê e altera **somente** as próprias tarefas.
- CRUD de tarefas: criar, editar, marcar/desmarcar como concluída, excluir; filtros Todas / Concluídas / Não concluídas.
- Páginas protegidas redirecionam para `login.php` quando não há login; tarefa de outro usuário → `erro.php?tipo=negado` (403).
- Proteções: token CSRF em todos os formulários POST, escape de HTML (`h()`), consultas com parâmetros (RedBeanPHP), cookie de sessão `httponly` + `SameSite`, `session_regenerate_id` no login.

## Publicação na hospedagem
1. No painel, crie um banco MySQL e anote host, nome do banco, usuário e senha.
2. No phpMyAdmin, selecione o banco e importe `banco.sql`.
3. Edite **somente** `config.php` com os dados do passo 1 (e coloque `'freeze' => true`).
4. Envie todos os arquivos (incluindo `classes/rb.php` e `css/`) para a pasta pública (`public_html`/`htdocs`) por FTP.
5. Acesse `https://seu-site/login.php`.

## Estrutura
| Arquivo | Função |
|---|---|
| `config.php` / `conexao.php` | dados e conexão com o banco (RedBeanPHP) |
| `funcoes.php` | sessão, login, CSRF e utilitários |
| `login.php`, `cadastro.php`, `logout.php` | autenticação |
| `index.php` | lista e filtros |
| `inclusao.php`, `edicao.php`, `status.php`, `exclusao.php` | CRUD |
| `erro.php` | mensagens de erro (403/500) |
| `topo.php`, `rodape.php`, `css/estilo.css` | layout |
| `wireframe.html` | wireframes e mapa de navegação |

## Roteiro de teste (apresentação)
1. Cadastre o usuário A → já entra na lista. Sair. Cadastre o usuário B.
2. Como B crie 2 tarefas; saia; como A crie outras. Cada um vê só as suas.
3. Como A: criar, editar (✏️), marcar, desmarcar, excluir, recarregar, sair, entrar de novo (dados persistem).
4. Sem login, abra `index.php`, `edicao.php?id=1` → vai para o login.

## Limitações conhecidas / melhorias futuras
Sem recuperação de senha, sem limite de tentativas de login, sem paginação, sem prazo/prioridade nas tarefas.
