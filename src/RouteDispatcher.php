<?php

namespace SparkLib\SparkRouter;

use SparkLib\SparkRouter\RouteFactory;
use SparkLib\SparkRouter\ClosurePointer;

class RouteDispatcher
{

    /** Route not found constant */
    public const NOT_FOUND = 0;

    /** Route method not allowed constant */
    public const METHOD_NOT_ALLOWED = 1;

    /** Route found constant */
    public const FOUND = 2;

    /** Dispatch status information */
    private $status;

    /** Route action definition */
    private $action;

    /** Array of matched route data */
    private $data;

    /** Matched route file name */
    private $fileName;

    /** Route file directory path */
    private $directory;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->status = self::NOT_FOUND;
        $this->action = null;
        $this->data = [];
    }

    /**
     * Get route list from a route file in route directory.
     * A route file with most matched prefix with URI will be loaded.
     * @param string $uri
     * @param string $dir
     * @param array $files
     * @return RouteFactory $routes
     */
    public function getRoutesFile(string $uri, string $dir, array $files): RouteFactory
    {
        $uri = $this->addSlash($uri);
        $matchIndex = null;
        $matchCount = 0;

        foreach ($files as $index => $file) {
            $prefix = $this->addSlash($file[0]);
            $len = strlen($prefix);
            $strUri = substr($uri, 0, $len);

            if ($strUri == $prefix && $len > $matchCount) {
                $matchIndex = $index;
                $matchCount = $len;
            }
        }

        if ($matchIndex !== null) {
            if (file_exists($filePath = $dir. $files[$matchIndex][1])) {
                $routes = require $filePath;
                //echo var_export($routes);
                if ($routes instanceof RouteFactory) {
                    $this->fileName = $files[$matchIndex][1];
                    return $routes;
                }
                // else bad route file exception
            }
            // else throw file not found exception
        }
        // else no file matched with request exception
        return new RouteFactory;
    }

    /**
     * Get route list from a cached route file in cached directory.
     * @param string $uri
     * @param string $dir
     * @param string $cachedDir
     * @param array $files
     * @return RouteFactory $routes
     */
    public function getCachedRoutesFile(string $uri, string $dir, string $cachedDir, array $files): RouteFactory
    {
        $this->directory = $dir;
        return $this->getRoutesFile($uri, $cachedDir, $files);
    }

    /**
     * Set route file path for fallback from dispatchCached
     * @param string $filePath
     */
    public function setFallbackRouteFile($filePath): void
    {
        if (file_exists($filePath)) {
            $routes = require $filePath;
            if ($routes instanceof RouteFactory) {
                $this->directory = substr($filePath, 0, strrpos($filePath, '/'));
                $this->fileName = substr($filePath, strrpos($filePath, '/'));
            }
        }
    }

    /**
     * Add slash to end of string
     */
    private function addSlash(string $string): string
    {
        if (substr($string, -1, 1) != '/') {
            $string = $string.'/';
        }
        return $string;
    }

    /**
     * Prepare URI string to dispatch.
     * @param string $uri
     * @param string $baseUri
     * @return string $uri
     */
    public function prepareUri(string $uri, string $baseUri=''): string
    {
        if ($baseUri !== '' && strpos($uri, $baseUri) === 0) {
            $pos = substr($baseUri, -1, 1) == '/' ? strlen($baseUri)-1 : strlen($baseUri);
            $uri = substr($uri, $pos);
        }
        $posSlash = strrpos($uri, '/');
        if (false !== $posQuest = strpos($uri, '?', $posSlash)) {
            $uri = substr($uri, 0, $posQuest);
        }
        return $uri;
    }

    /**
     * Dispatch between request and route list by matching URI and http method.
     * @param RouteFactory $routes
     * @param string $method
     * @param string $uri
     * @return dispatchInfo
     */
    public function dispatch(RouteFactory $routes, string $method, string $uri): array
    {
        $uri = $this->addSlash($uri);
        //var_dump($uri); echo "<br><br>";
        $staticRoutes = $routes->staticRoutes();
        if (!$this->matchSimpleRoute($staticRoutes, $method, $uri)) {
            
            $dynamicRoutes = $routes->dynamicRoutes();
            foreach ($dynamicRoutes as $dynamicRoute) {
                if ($this->matchDynamicRoute($dynamicRoute, $method, $uri)) {
                    return $this->routeInfo();
                }
            }
        }

        return $this->routeInfo();
    }

    /**
     * Dispatch between request and route list by matching URI and http method.
     * Fallback to normal dispatch for closure action
     * @param RouteFactory $routes
     * @param string $method
     * @param string $uri
     * @return dispatchInfo
     */
    public function dispatchCached(RouteFactory $routes, string $method, string $uri):array
    {
        $uri = $this->addSlash($uri);
        $staticRoutes = $routes->staticRoutes();
        if (!$this->matchSimpleRoute($staticRoutes, $method, $uri)) {
            
            $dynamicRoutes = $routes->dynamicRoutes();
            foreach ($dynamicRoutes as $dynamicRoute) {
                if ($this->matchDynamicRoute($dynamicRoute, $method, $uri)) {
                    break;
                }
            }
        }

        if ($this->action instanceof ClosurePointer) {
            $this->action = self::getClosureAction($this->action);
        }
        return $this->routeInfo();
    }

    /**
     * Matching URI with static routes list.
     * Route match if URI match exactly with an URI in route list.
     */
    private function matchSimpleRoute($staticRoutes, string $method, string $uri): bool
    {
        //var_dump($staticRoutes); echo "<br>";
        if (isset($staticRoutes[$uri][$method])) {
            $this->status = self::FOUND;
            $this->action = $staticRoutes[$uri][$method];
            return true;
        } else if (isset($staticRoutes[$uri])) {
            $this->status = self::METHOD_NOT_ALLOWED;
        }
        return false;
    }

    /**
     * Matching URI with dynamic routes list.
     * Route match if URI match with base static URI in route list and 
     * number of URI data equal to number of route placeholders.
     * If route require regex matching, URI data must match regex pattern.
     */
    private function matchDynamicRoute($dynamicRoute, string $method, string $uri): bool
    {
        list($baseUri, $placeholders, $pattern, $routeMethod, $action) = $dynamicRoute;
        // echo $uri."<br>".$baseUri."<br>"; echo var_export($placeholders); echo "<br><br>";
        if (strpos($uri, $baseUri) === 0) {
            $uriData = substr($uri, strlen($baseUri), -1);
            $uriDataArr = explode('/', $uriData);
            // echo var_export($uriDataArr); echo "<br>"; echo var_export($placeholders); echo "<br><br>";

            if (count($uriDataArr) == count($placeholders) && $uriData !== false) {

                $regexFlag = true;
                if ($pattern) {
                    $uriData = '/'. $uriData;
                    $regexFlag = preg_match($pattern, $uriData);
                }
                // echo "pattern: '".$pattern."'<br>uriData: '".$uriData."'<br>"; echo var_export($regexFlag)."<br><br>";

                if ($regexFlag) {
                    if ($method == $routeMethod) {
                        $this->status = self::FOUND;
                        $this->action = $action;
                        $this->data = array_combine($placeholders, $uriDataArr);
                        return true;
                    } else {
                        $this->status = self::METHOD_NOT_ALLOWED;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Getting original closure from a ClosurePointer object
     * @param ClosurePointer $action
     */
    private static function getClosureAction(ClosurePointer $action)
    {
        $routes = require $action->filePath;
        if ($routes instanceof RouteFactory) {
            if (is_int($action->index)) {
                return $routes->dynamicRoutes()[$action->index][4];
            } else {
                return $routes->staticRoutes()[$action->index[0]][$action->index[1]];
            }
        }
    }

    /**
     * Return route status, action, and URI data
     */
    private function routeInfo(): array
    {
        return array(
            'status' => $this->status,
            'action' => $this->action,
            'data' => $this->data
        );
    }

}
