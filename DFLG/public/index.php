<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

// Home (landing page pública)
$router->get('/', 'HomeController@index');

// Dashboard (Visão Geral) — front-end com dados de exemplo por enquanto
$router->get('/dashboard', 'DashboardController@index');

// -----------------------------------------------------------------------
// As rotas abaixo apontam para controllers que ainda não existem/estão
// vazios no projeto. Foram comentadas para não quebrar a aplicação.
// Descomente cada uma conforme o respectivo Controller for implementado.
// -----------------------------------------------------------------------

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

// Categorias — CategoriaController.php existe mas está vazio
// $router->get('/categorias', 'CategoriaController@index');

// Parcelas — ParcelaController.php existe mas está vazio
// $router->get('/parcelamentos', 'ParcelaController@index');

// Usuário / Perfil — UsuarioController.php existe mas está vazio
// $router->get('/perfil', 'UsuarioController@perfil');

$router->run();

