<?php

namespace App;

use SparkLib\SparkRouter\RouteDispatcher;

class Loader
{

    private $dispatcher;

    public function __construct()
    {
        $this->dispatcher = new RouteDispatcher;
    }

    public function run()
    {
        $uri = $this->dispatcher->prepareUri($_SERVER['REQUEST_URI'], BASE_URI);
        $routes = $this->dispatcher->getRoutesFile($uri, ROUTE_DIR, ROUTE_FILES);
        $dispatch = $this->dispatcher->dispatch($routes, $_SERVER['REQUEST_METHOD'], $uri);
        
        if ($dispatch['status'] == $this->dispatcher::FOUND) {
            $this->action($dispatch['action'], $dispatch['data']);
        }
        else if ($dispatch['status'] == $this->dispatcher::METHOD_NOT_ALLOWED) {
            $this->methodNotAllowed();
        }
        else {
            $this->notFound();
        }
    }

    public function test()
    {
        $uri = $this->dispatcher->prepareUri($_SERVER['REQUEST_URI'], BASE_URI);
        $routes = $this->dispatcher->getRoutesFile($uri, ROUTE_DIR, ROUTE_FILES);
        $dispatch = $this->dispatcher->dispatch($routes, $_SERVER['REQUEST_METHOD'], $uri);
        // echo var_export($routes);
        echo var_export($dispatch);
    }

    private function action($action, $data)
    {
        if (is_string($action)) {
            $act = explode('@', $action);
            if (isset($act[0]) && isset($act[1])) {
                $class = '\\App\\Controllers\\'. $act[0];
                $controller = new $class;
                call_user_func_array([$controller, $act[1]], $data);
            }
        } else if (is_callable($action)) {
            call_user_func_array($action, $data);
        }
    }

    private function methodNotAllowed()
    {
        http_response_code(405);
        header("Content-Type: application/json");
        echo json_encode(['status' => 'METHOD_NOT_ALLOWED']);
    }

    private function notFound()
    {
        http_response_code(404);
        header("Content-Type: application/json");
        echo json_encode(['status' => 'NOT_FOUND']);
    }

}
