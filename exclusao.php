<?php
/**
 * exclusao.php - Remove uma tarefa do banco.
 * Só atua em tarefas do próprio usuário.
 */
require_once __DIR__ . '/funcoes.php';
exigirLogin();
exigirPost();
csrfValidar();

try {
    $tarefa = tarefaDoUsuario((int) ($_POST['id'] ?? 0));
    if ($tarefa === null) {
        redirecionar('erro.php?tipo=negado');
    }
    R::trash($tarefa);
} catch (Throwable $erro) {
    redirecionar('erro.php?tipo=salvar');
}

voltarParaIndex();
