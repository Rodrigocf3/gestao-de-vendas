<?php
/** topo.php - Cabeçalho HTML comum. Defina $titulo antes de incluir. */
$titulo = $titulo ?? 'Gestão de Tarefas';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titulo) ?> - Gestão de Tarefas</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container">
<?php if ($flash = pegarFlash()): ?>
    <p class="alerta <?= h($flash['tipo']) ?>" role="alert"><?= h($flash['texto']) ?></p>
<?php endif; ?>
