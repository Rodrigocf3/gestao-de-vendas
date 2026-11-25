<?php
/**
 * index.php - Tela principal: lista SOMENTE as tarefas do usuário logado.
 */
require_once __DIR__ . '/funcoes.php';
exigirLogin();
$usuario = usuarioLogado();

// 1) Atualiza o filtro escolhido (se válido) e guarda na sessão
$filtrosValidos = ['todas', 'concluidas', 'pendentes'];
if (isset($_GET['filtro']) && is_string($_GET['filtro']) && in_array($_GET['filtro'], $filtrosValidos, true)) {
    $_SESSION['filtro'] = $_GET['filtro'];
}
$filtro = $_SESSION['filtro'];

// 2) Busca as tarefas do usuário conforme o filtro
try {
    $uid = (int) $usuario->id;
    if ($filtro === 'concluidas') {
        $tarefas = R::find('tarefa', 'usuario_id = ? AND concluida = ? ORDER BY id DESC', [$uid, 1]);
    } elseif ($filtro === 'pendentes') {
        $tarefas = R::find('tarefa', 'usuario_id = ? AND concluida = ? ORDER BY id DESC', [$uid, 0]);
    } else {
        $tarefas = R::find('tarefa', 'usuario_id = ? ORDER BY id DESC', [$uid]);
    }
} catch (Throwable $e) {
    redirecionar('erro.php?tipo=banco');
}

$rotulos = [
    'todas'      => 'TODAS',
    'concluidas' => 'CONCLUÍDAS',
    'pendentes'  => 'NÃO CONCLUÍDAS',
];

$titulo = 'Minhas tarefas';
require __DIR__ . '/topo.php';
?>
    <div class="barra-usuario">
        <span>Olá, <strong><?= h($usuario->nome) ?></strong></span>
        <form action="logout.php" method="post">
            <?= csrfCampo() ?>
            <button type="submit" class="btn-link">Sair</button>
        </form>
    </div>

    <h1>(TODO LIST) Gestão de Tarefas</h1>

    <!-- Inclusão: envia para inclusao.php -->
    <form class="form-inclusao" action="inclusao.php" method="post">
        <?= csrfCampo() ?>
        <input type="text" name="descricao" placeholder="Nova tarefa..." maxlength="200" required
               aria-label="Descrição da nova tarefa">
        <button type="submit" class="btn-add" title="Adicionar tarefa" aria-label="Adicionar">+</button>
    </form>

    <!-- Filtros -->
    <nav class="filtros">
        <?php foreach ($rotulos as $chave => $rotulo): ?>
            <a href="index.php?filtro=<?= h($chave) ?>"
               class="<?= $filtro === $chave ? 'ativo' : '' ?>"><?= h($rotulo) ?></a>
        <?php endforeach; ?>
    </nav>

    <!-- Lista -->
    <?php if (empty($tarefas)): ?>
        <p class="vazio">Nenhuma tarefa por aqui.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($tarefas as $t): ?>
                <li class="<?= $t->concluida ? 'concluida' : '' ?>">

                    <!-- Checkbox: alterna o status via status.php -->
                    <form action="status.php" method="post">
                        <?= csrfCampo() ?>
                        <input type="hidden" name="id" value="<?= (int) $t->id ?>">
                        <input type="checkbox"
                               <?= $t->concluida ? 'checked' : '' ?>
                               onchange="this.form.submit()"
                               aria-label="Alternar status">
                        <noscript><button type="submit">OK</button></noscript>
                    </form>

                    <span class="texto"><?= h($t->descricao) ?></span>

                    <!-- Editar: abre edicao.php -->
                    <a class="btn-editar" href="edicao.php?id=<?= (int) $t->id ?>"
                       title="Editar" aria-label="Editar">✏️</a>

                    <!-- Lixeira: envia para exclusao.php -->
                    <form action="exclusao.php" method="post"
                          onsubmit="return confirm('Excluir esta tarefa?');">
                        <?= csrfCampo() ?>
                        <input type="hidden" name="id" value="<?= (int) $t->id ?>">
                        <button type="submit" class="btn-lixeira" title="Excluir" aria-label="Excluir">🗑️</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php require __DIR__ . '/rodape.php'; ?>
