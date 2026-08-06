<?php

namespace app\core;

use Exception;

class Router
{
    private array $routes = [];

    public function get($route, $action){

        $this->routes[] = [
            'method' => 'get',
            'route' => $route,
            'action' => $action
        ];
    }

    public function post($route, $action){

        $this->routes[] = [
            'method' => 'post',
            'route' => $route,
            'action' => $action
        ];
    }


    public function run()
    {
        $uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri  = rawurldecode($uri);

        // Descobre em qual subpasta o index.php está rodando (ex: /Xampp Visual Studio/DFLG/public)
        // e remove esse trecho da URI, pra comparar só a parte da rota (ex: /dashboard).
        // Isso permite rodar o projeto tanto na raiz do servidor quanto dentro de uma subpasta do htdocs.
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        if ($uri === '') {
            $uri = '/';
        }

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        // 1ª passada: rotas exatas (mantém o comportamento original, sem custo extra de regex)
        foreach ($this->routes as $route) {

            if ($route['route'] == $uri && $route['method'] == $method) {

                return $this->dispatch($route);
            }
        }

        // 2ª passada: rotas dinâmicas, com segmentos tipo /calculadoras/{slug}
        foreach ($this->routes as $route) {

            if ($route['method'] !== $method || strpos($route['route'], '{') === false) {
                continue;
            }

            $params = $this->match($route['route'], $uri);
            if ($params !== null) {
                return $this->dispatch($route, $params);
            }
        }

        http_response_code(404);
        exit('Rota não encontrada');
    }

    /**
     * Tenta casar a URI recebida com um padrão de rota que contenha
     * segmentos dinâmicos, ex: '/calculadoras/{slug}'.
     * Retorna um array associativo ['slug' => 'tesouro'] em caso de match,
     * ou null se a rota não corresponder.
     */
    private function match(string $pattern, string $uri): ?array
    {
        $paramNames = [];
        $regex = preg_replace_callback('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', function ($m) use (&$paramNames) {
            $paramNames[] = $m[1];
            return '([^/]+)';
        }, $pattern);

        if (!preg_match('#^' . $regex . '$#', $uri, $matches)) {
            return null;
        }

        array_shift($matches);
        return array_combine($paramNames, $matches);
    }

    public function dispatch($route, array $params = []){

        list($controller, $method) = explode('@', $route['action']);

        $controllerClass = "app\\controllers\\$controller";

        if (!class_exists($controllerClass)) {
            print "Controller $controller não encontrado";
            die;
        }

        if (!method_exists($controllerClass, $method)) {
            print "Método $method não encontrado em $controllerClass";
            die;
        }
        
        $controller = new $controllerClass;
        call_user_func_array([$controller, $method], $params);

    }

    public function getAllRoutes(){
        return $this->routes;
    }

}
