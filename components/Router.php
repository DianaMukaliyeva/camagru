<?php
class Router {
    protected $controller = 'GalleryController';
    protected $method = 'index';
    protected $params = [];

    public function render($url) {
        $urlParts = explode('/', $url);

        // Look if first parameter is our root folder
        if (isset($urlParts[0]) && $urlParts[0] == APPROOT) {
            // If our root folder in array, delete it
            array_shift($urlParts);
        }

        // If our url contains path
        if (isset($urlParts[0])) {
            // Look in controllers for first value
            if (file_exists('controllers/' . ucwords($urlParts[0]) . 'Controller.php')) {
                // If exists, set as controller
                $this->controller = ucwords($urlParts[0]) . 'Controller';
                // Unset 0 Index
                unset($urlParts[0]);
            }
        }

        // Require the controller
        require_once 'controllers/' . $this->controller . '.php';

        // Instantiate controller class
        $this->controller = new $this->controller;

        // Check for second part of url
        if (isset($urlParts[1])) {
            // Check to see if method exists in controller
            if (method_exists($this->controller, $urlParts[1])) {
                $this->method = $urlParts[1];
                // Unset 1 index
                unset($urlParts[1]);
            }
        }

        // Get params
        $this->params = $urlParts ? array_values($urlParts) : [];

        // Call a callback with array of params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
}
