<?php
class Controller {

    // Redirect to the given address
    public function redirect($url) {
        header("Location: " . URLROOT . "/$url");
        header('Connection: close');
        exit;
    }

    // Send mail to given user with given content
    public function sendEmail($email, $login, $content) {
        $header = "From: Camagru web application\r\n";
        $header .= "Reply-To: <hive2020hive@gmail.com>\r\n";
        $header .= "Content-type: text/html; charset=utf-8 \r\n";
        $subject = "Camagru web application";

        $message = "<div style=\"background-color:#bbff99; text-align:center;\">";
        $message .= "<h2 style=\"text-align:center;\">Hello, " . $login . "!</h2>";
        $message .= $content;
        $message .= "<p><small>Camagru</p></div>";

        return (mail($email, $subject, $message, $header));
    }

    // Add to array $data given content
    public function addMessage($success, $content, $data = []) {
        $data['message']['class'] = $success ? 'alert-success' : 'alert-danger';
        $data['message']['content'] = $content;

        return $data;
    }

    // Return instance of model
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
            echo "view does not exist";
        }

        exit;
    }

    // Check if request is ajax or not
    public function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    // Return logged in user
    public function getLoggedInUser() {
        if (isset($_SESSION[APPNAME]['user'])) {
            return $_SESSION[APPNAME]['user'];
        }

        return false;
    }

    // Check that user is logged in and exists in db. Otherwise redirect ot login page
    public function checkUserSession() {
        if (isset($_SESSION[APPNAME]['user'])) {
            $user = $_SESSION[APPNAME]['user'];
            if ($this->getModel('User')->getLoginById($user['id']) == $user['login']) {
                return $user;
            } else {
                unset($_SESSION[APPNAME]['user']);
            }
        }
        $data = $this->addMessage(false, 'You need log in first');
        $this->renderView('users/login', $data);
        exit();
    }

    // Redirect if request is not ajax
    public function onlyAjaxRequests() {
        if (!$this->isAjaxRequest()) {
            $this->redirect('');
        }
        $user = $this->getLoggedInUser();
        if ($user) {
            $_SESSION['user-' . $user['id']]['last_activity'] = time(); // set user's last activity
        }
    }
}
