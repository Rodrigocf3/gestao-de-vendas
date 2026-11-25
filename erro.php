<?php
/**
 * erro.php - Página de erro simples.
 * Não usa banco nem sessão, pois pode ser chamada justamente quando o banco falha.
 */
$mensagens = [
    'banco'  => 'Não foi possível acessar o banco de dados no momento. Tente novamente mais tarde.',
    'salvar' => 'Não foi possível concluir a operação no banco de dados.',
    'negado' => 'Acesso negado: esta tarefa não existe ou não pertence a você.',
    'csrf'   => 'Requisição inválida ou expirada. Volte e tente novamente.',
];

$tipo = $_GET['tipo'] ?? '';
$tipo = is_string($tipo) ? $tipo : '';

if (in_array($tipo, ['negado', 'csrf'], true)) {
    http_response_code(403);
} elseif (in_array($tipo, ['banco', 'salvar'], true)) {
    http_response_code(500);
}

$mensagem = $mensagens[$tipo] ?? 'Ocorreu um erro inesperado.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erro - Gestão de Tarefas</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container">
    <h1>Operação não concluída</h1>
    <p><?= htmlspecialchars($mensagem, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
    <a class="erro-link" href="index.php">Voltar para a lista</a>
</div>
</body>
</html>
