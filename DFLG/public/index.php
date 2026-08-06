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

// Calculadoras — /calculadoras é o hub (grade com todas), cada uma abre
// em sua própria página/aba: /calculadoras/tesouro-direto, etc (RF10 / UC-10)
$router->get('/calculadoras', 'SimuladorController@index');
$router->get('/calculadoras/{slug}', 'SimuladorController@calculadora');

// Categorias
$router->get('/categorias', 'CategoriaController@index');
$router->post('/categorias', 'CategoriaController@atualizar');

// Parcelamentos
$router->get('/parcelamentos', 'ParcelaController@index');
$router->post('/parcelamentos', 'ParcelaController@criar');
$router->post('/parcelamentos/{id}/atualizar', 'ParcelaController@atualizar');
$router->post('/parcelamentos/{id}/excluir', 'ParcelaController@excluir');

// Metas financeiras (RF08 / RF19)
$router->get('/metas', 'MetaController@index');
$router->post('/metas', 'MetaController@criar');
$router->post('/metas/{id}/atualizar', 'MetaController@atualizar');
$router->post('/metas/{id}/excluir', 'MetaController@excluir');
$router->post('/metas/{id}/aportar', 'MetaController@aportar');
$router->post('/metas/{id}/fixar', 'MetaController@fixar');

// Usuário / Perfil
$router->get('/perfil', 'UsuarioController@perfil');
$router->post('/perfil', 'UsuarioController@atualizar');

$router->run();

