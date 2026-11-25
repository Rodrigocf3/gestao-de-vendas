<?php
/**
 * conexao.php
 * Conexão com o banco MySQL usando RedBeanPHP.
 * Os dados de acesso ficam em config.php.
 *
 * Se o banco não responder, redireciona para erro.php?tipo=banco.
 */
require_once __DIR__ . '/classes/rb.php';

$config = require __DIR__ . '/config.php';

try {
    R::setup(
        'mysql:host=' . $config['host'] . ';dbname=' . $config['banco'],
        $config['usuario'],
        $config['senha']
    );

    if (!R::testConnection()) {
        header('Location: erro.php?tipo=banco');
        exit;
    }

    if (!empty($config['freeze'])) {
        R::freeze(true);
    }
} catch (Throwable $erro) {
    // Banco inexistente, senha errada, MySQL desligado etc.
    header('Location: erro.php?tipo=banco');
    exit;
}
