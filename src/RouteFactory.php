<?php

namespace SparkLib\SparkRouter;

class RouteFactory
{

    /** Static routes list */
    private $staticRoutes;

    /** Dynamic routes list */
    private $dynamicRoutes;

    /** URI prefix of route group */
    private $groupPrefix;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->staticRoutes = [];
        $this->dynamicRoutes = [];
        $this->groupPrefix = "";
    }

    /** Return static routes list */
    public function staticRoutes(): array
    {
        return $this->staticRoutes;
    }

    /** Return dynamic routes list */
    public function dynamicRoutes(): array
    {
        return $this->dynamicRoutes;
    }

    /**
     * Change URI prefix.
     * @param string $prefix
     */
    public function groupPrefix(string $prefix = ""): void
    {
        $this->groupPrefix = $prefix;
    }

    /**
     * Change URI prefix of route group.
     * @param string $prefix
     * @param callback
     */
    public function group(string $prefix, callable $callback): void
    {
        $prevGroupPrefix = $this->groupPrefix;
        $this->groupPrefix = $prevGroupPrefix. $prefix;
        $callback($this);
        $this->groupPrefix = $prevGroupPrefix;
    }

    /**
     * Add map of action and URI and http method to route list.
     * The map will add to dynamic route list if URI contain placeholder otherwise will add to static route list.
     * @param array $methods
     * @param string $uri
     * @param mixed $action
     */
    public function map($methods, string $uri, $action): void
    {
        $uri = $this->prepareUri($uri);
        
        $uriBreak = $this->breakUri($uri);
        if ($uriBreak == []) {
            foreach ($methods as $method) {
                $this->staticRoutes[$uri][$method] = $action;
            }
        } else {
            $placeholders = $this->parsePlaceholder($uriBreak[1]);
            if ($placeholders != []) {
                foreach ($methods as $method) {
                    $this->dynamicRoutes[] = [$uriBreak[0], $placeholders[0], $placeholders[1], $method, $action];
                }
            }
        }
    }

    /**
     * Add slash at the end of URI string and append URI prefix
     */
    private function prepareUri(string $uri): string
    {
        if (substr($uri, strlen($uri)-1, 1) != '/') {
            $uri = $uri.'/';
        }
        return $this->groupPrefix. $uri;
    }

    /**
     * Break URI string to static part and placeholders part if defined
     */
    private function breakUri(string $uri): array
    {
        if (false !== $pos = strpos($uri, '/{')) {
            return array(
                substr($uri, 0, $pos+1),
                substr($uri, $pos)
            );
        }
        return [];
    }

    /**
     * Parse placeholders part of URI to placeholder name and corresponding regex pattern if defined.
     */
    private function parsePlaceholder($uriPlaceholder): array
    {
        $uriPlaceholder = substr($uriPlaceholder, 1, -1);
        $placeholders = explode('/', $uriPlaceholder);
        $pattern = '~^';
        $regexflag = false;

        foreach ($placeholders as $index => $placeholder) {
            $len = strlen($placeholder);
            if (substr($placeholder, 0, 1) == '{' && substr($placeholder, $len - 1, 1) == '}') {
                if ($pos = strpos($placeholder, ':')) {
                    $placeholders[$index] = substr($placeholder, 1, $pos-1);
                    $pattern .= '/('. substr($placeholder, $pos+1, $len-$pos-2) .')';
                    $regexflag = true;
                } else {
                    $placeholders[$index] = substr($placeholder, 1, $len-2);
                    $pattern .= '/([^/]+)';
                }
            } 
            else {
                $pattern .= '/([^/]+)';
            }
        }

        if ($regexflag) $pattern .= '$~';
        else $pattern = false;
        return [$placeholders, $pattern];
    }

    /**
     * Add route map with GET method
     * @param string $uri
     * @param mixed $action
     */
    public function get(string $uri, $action): void
    {
        $this->map(['GET'], $uri, $action);
    }

    /**
     * Add route map with POST method
     * @param string $uri
     * @param mixed $action
     */
    public function post(string $uri, $action): void
    {
        $this->map(['POST'], $uri, $action);
    }

    /**
     * Add route map with PUT method
     * @param string $uri
     * @param mixed $action
     */
    public function put(string $uri, $action): void
    {
        $this->map(['PUT'], $uri, $action);
    }

    /**
     * Add route map with PATCH method
     * @param string $uri
     * @param mixed $action
     */
    public function patch(string $uri, $action): void
    {
        $this->map(['PATCH'], $uri, $action);
    }

    /**
     * Add route map with DELETE method
     * @param string $uri
     * @param mixed $action
     */
    public function delete(string $uri, $action): void
    {
        $this->map(['DELETE'], $uri, $action);
    }

    /**
     * Add route map with GET, POST, PUT, PATCH, and DELETE method
     * @param string $uri
     * @param mixed $action
     */
    public function all(string $uri, $action): void
    {
        $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $uri, $action);
    }

}