<?php
/**
 * status.php - Alterna uma tarefa entre concluída e não concluída.
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
    $tarefa->concluida = $tarefa->concluida ? 0 : 1; // inverte o status
    R::store($tarefa);
} catch (Throwable $erro) {
    redirecionar('erro.php?tipo=salvar');
}

voltarParaIndex();
