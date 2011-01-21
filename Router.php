<?php

class Router {
    
    private $routes;
    
    public function __construct($routes) {
        
        $this->routes = $this->parseRoutes($routes);
    }
    
    
    public function route($request) {
        
        foreach ($this->routes as $route => $resource) {
            if (preg_match($route, $request->path, $parameters)) {
                
                //Filter $parameters
                foreach ($parameters as $key => $value) {
                    if (is_int($key)) {
                        unset($parameters[$key]);
                    }
                }
                
                $request->resource = $resource;
                $request->parameters = $parameters;
                
                return $request;
            }
        }
        
        throw new Exception('Resource does not exist.', 404);
    }
    
    
    private function parseRoutes($routes, $prefix = '') {
        
        foreach ($routes as $route => $resource) {
            if (is_array($resource)) {
                $routes = array_merge($routes, $this->parseRoutes($resource, $route));
            } else {
                $routes[$this->regexifyRoute(substr($prefix, 0, -1).$route)] = $resource;
            }
            
            unset($routes[$route]);
        }
        
        return $routes;
    }
    
    
    private function regexifyRoute($route) {
        
        $parts = explode('/', $route);
        
        $route = '\/';
        
        foreach ($parts as $part) {
            if ($part) {
                if (preg_match('/^{(?P<parameter>\w+)(:(?P<regex>.+))?}$/', $part, $matches)) {
                    $part = '(?P<'.$matches['parameter'].'>';
                    
                    if (key_exists('regex', $matches)) {
                        $part .= $matches['regex'].')';
                    } else {
                        $part .= '\w+)';
                    }
                }
                $route .= $part.'\/';
            }
        }
        
        return '/^'.$route.'$/i';
    }
}
