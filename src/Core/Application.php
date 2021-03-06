<?php

namespace Greentea\Core;

use Auryn\InjectionException;
use Auryn\Injector;
use Greentea\Component\RouteVOInterface;
use Greentea\Exception\NoHandlerSpecifiedException;
use Symfony\Component\HttpFoundation\Request;

final class Application
{
    private $injector;

    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * @param $request
     * @param RouteVOInterface $route
     * @throws InjectionException
     * @throws NoHandlerSpecifiedException
     * @throws \Greentea\Exception\TemplatingException
     */
    public function run(Request $request, RouteVOInterface $route): void
    {
        $method = $route->resolveMethod();
        $controllerResource = $route->resolveController();
        $viewResource = $route->resolveView();

        $exists = false;

        if (method_exists($controllerResource, $method)) {
            /**
             * @var Controller $controller
             */
            $controller = $this->injector->make($controllerResource);
            $dto = $controller->handleRequest($request, $method);
            $exists = true;
        }
        if (method_exists($viewResource, $method)) {
            /**
             * @var View $view
             */
            $view = $this->injector->make($viewResource);
            if (isset($dto))
                $view->createResponse($request, $method, $dto)->send();
            else
                $view->createResponse($request, $method)->send();

            $exists = true;
        }

        if (!$exists)
            throw new NoHandlerSpecifiedException($controllerResource, $viewResource, $method);
    }

}