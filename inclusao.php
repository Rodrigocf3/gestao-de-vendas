<?php
/**
 * inclusao.php - Recebe a descrição via POST e grava a tarefa (pendente)
 * vinculada ao usuário logado.
 */
require_once __DIR__ . '/funcoes.php';
exigirLogin();
exigirPost();
csrfValidar();

$descricao = $_POST['descricao'] ?? '';

if (is_string($descricao)) {
    $descricao = trim($descricao);

    if ($descricao !== '') {
        try {
            $tarefa = R::dispense('tarefa');
            $tarefa->descricao  = mb_substr($descricao, 0, 200);
            $tarefa->concluida  = 0;                          // nova tarefa começa pendente
            $tarefa->usuario_id = (int) usuarioLogado()->id;  // dono da tarefa
            $tarefa->criada_em  = date('Y-m-d H:i:s');
            R::store($tarefa);
        } catch (Throwable $erro) {
            redirecionar('erro.php?tipo=salvar');
        }
    }
}

voltarParaIndex();
