<?php

//Configuração do ambiente
define('DEV_ENVIRONMENT', true);

if (DEV_ENVIRONMENT == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Configuração do Sistema
define('APP_NAME', 'Projeto Integrador');
// Ajustado para a pasta de vocês dentro do htdocs do XAMPP.
// Se renomearem a pasta (recomendado: sem espaços), atualizem aqui também.
define('URL_BASE', 'http://localhost/Xampp%20Visual%20Studio/DFLG/public');

//Configurações do Banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_dflg');

define('DB_USER', 'root');
define('DB_PASS', '');
