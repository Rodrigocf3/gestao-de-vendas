<?php
/**
 * config.php
 * ÚNICO arquivo que precisa ser alterado ao publicar na hospedagem.
 * Use os dados que o painel da hospedagem mostra para o seu banco MySQL.
 */
return [
    'host'    => 'localhost',   // na hospedagem costuma ser outro (ex.: sqlXXX.hospedagem.com)
    'banco'   => 'ABNQRCF',     // nome do banco criado no painel
    'usuario' => 'root',        // usuário MySQL
    'senha'   => 'aluno',       // senha MySQL

    // true = o RedBeanPHP não altera mais a estrutura das tabelas (recomendado
    // em produção, depois de importar o banco.sql).
    'freeze'  => false,
];
