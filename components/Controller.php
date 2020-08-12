<?php
class Controller {
    public function redirect($url) {
        header("Location: " . URLROOT . "/$url");
        header('Connection: close');
        exit;
    }

    public function addMessage($success, $content, $data = []) {
        if ($success)
            $data['message']['class'] = 'alert-success';
        else
            $data['message']['class'] = 'alert-danger';
        $data['message']['content'] = $content;
        return $data;
    }

    public function getModel($model) {
        // Check model exists
        if (file_exists('models/' . $model . '.php')) {
            require_once 'models/' . $model . '.php';
        } else {
            echo "could not find model";
            return null;
        }
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
        exit;
    }

    public function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    public function checkUserSession() {
        if (!isset($_SESSION[APPNAME]['user'])) {
            $this->redirect('');
        }
        return $_SESSION[APPNAME]['user'];
    }

    public function onlyAjaxRequests() {
        if (!$this->isAjaxRequest()) {
            $this->redirect('');
        }
    }
}
