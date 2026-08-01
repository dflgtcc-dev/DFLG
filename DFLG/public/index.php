<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

// Home (landing page pública)
$router->get('/', 'HomeController@index');

// Dashboard (Visão Geral) — front-end com dados de exemplo por enquanto
$router->get('/dashboard', 'DashboardController@index');

// Autenticação
$router->get('/login', 'AutenticacaoController@login');
$router->post('/logar', 'AutenticacaoController@logar');
$router->post('/cadastro', 'AutenticacaoController@cadastrar');
$router->get('/logout', 'AutenticacaoController@logout');

// Transações
$router->get('/transacoes', 'TransacaoController@index');
$router->post('/transacoes', 'TransacaoController@criar');

// Calculadoras
$router->get('/calculadoras', 'SimuladorController@index');

// Categorias
$router->get('/categorias', 'CategoriaController@index');
$router->post('/categorias', 'CategoriaController@atualizar');

// Parcelamentos
$router->get('/parcelamentos', 'ParcelaController@index');
$router->post('/parcelamentos', 'ParcelaController@criar');

// Usuário / Perfil
$router->get('/perfil', 'UsuarioController@perfil');
$router->post('/perfil', 'UsuarioController@atualizar');

$router->run();

