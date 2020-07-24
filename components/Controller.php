<?php
class Controller {
    public function redirect($url) {
        header("Location: " . URLROOT . "/$url");
        header('Connection: close');
        exit;
    }

    public function getModel($model) {
        if (file_exists('models/' . $model . '.php')) {
            // Require model file
            require_once 'models/' . $model . '.php';
        } else {
            // Model does not exist
            echo "something went wrong";
            return null;
        }
        // Instatiate model
        return new $model();
    }

    // Load view
    public function renderView($view, $data = []) {
        // Check for view file
        if (file_exists('views/' . $view . '.php')) {
            require_once 'views/' . $view . '.php';
        } else {
            // View does not exist
            echo "something went wrong";
        }
    }

    public function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }
}
