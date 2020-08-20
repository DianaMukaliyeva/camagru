<?php
class Router {
    // Current controller
    protected $controller = 'ImagesController';
    // Current method of controller
    protected $method = 'getImages';
    // Parameters
    protected $params = [];

    // Renders given url and calls Controller class
    public function render($url) {
        $urlParts = explode('/', $url);

        // Look if our root folder is an array's first element, delete it
        if (isset($urlParts[0]) && $urlParts[0] == APPNAME) {
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

        // Require the parent controller
        require_once 'components/Controller.php';
        $parentController = new Controller;
        // Instantiate controller class
        $this->controller = new $this->controller;

        // Check for second part of url
        if (isset($urlParts[1])) {
            // Check to see if method exists in controller
            if (method_exists($this->controller, $urlParts[1]) && !method_exists($parentController, $urlParts[1])) {
                $this->method = $urlParts[1];
                // Unset 1 index
                unset($urlParts[1]);
            }
        }

        // Get params
        $this->params = $urlParts ? array_values($urlParts) : [];

        if (method_exists($this->controller, $this->method)) {
            // Call a callback with array of params
            call_user_func_array([$this->controller, $this->method], $this->params);
        } else {
            $this->controller->redirect('');
        }
    }
}
