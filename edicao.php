<?php
/**
 * edicao.php - GET: mostra o formulário de edição da tarefa.
 *              POST: salva a nova descrição.
 * Só permite editar tarefas do próprio usuário.
 */
require_once __DIR__ . '/funcoes.php';
exigirLogin();

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

try {
    $tarefa = tarefaDoUsuario($id);
} catch (Throwable $erro) {
    redirecionar('erro.php?tipo=banco');
}
if ($tarefa === null) {
    redirecionar('erro.php?tipo=negado'); // não existe ou é de outro usuário
}

$erro = '';
$descricao = $tarefa->descricao;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    $descricao = is_string($_POST['descricao'] ?? null) ? trim($_POST['descricao']) : '';

    if ($descricao === '') {
        $erro = 'A descrição não pode ficar vazia.';
    } else {
        try {
            $tarefa->descricao = mb_substr($descricao, 0, 200);
            R::store($tarefa);
        } catch (Throwable $e) {
            redirecionar('erro.php?tipo=salvar');
        }
        definirFlash('ok', 'Tarefa atualizada.');
        voltarParaIndex();
    }
}

$titulo = 'Editar tarefa';
require __DIR__ . '/topo.php';
?>
    <h1>Editar tarefa</h1>

    <?php if ($erro): ?>
        <p class="alerta erro" role="alert"><?= h($erro) ?></p>
    <?php endif; ?>

    <form class="form-auth" action="edicao.php" method="post">
        <?= csrfCampo() ?>
        <input type="hidden" name="id" value="<?= (int) $tarefa->id ?>">

        <label for="descricao">Descrição</label>
        <input type="text" id="descricao" name="descricao" maxlength="200"
               value="<?= h($descricao) ?>" required autofocus>

        <button type="submit" class="btn">Salvar</button>
        <a class="btn-cancelar" href="index.php">Cancelar</a>
    </form>
<?php require __DIR__ . '/rodape.php'; ?>
