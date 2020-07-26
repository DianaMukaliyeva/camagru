<?php
class UsersController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
    }

    private function sendPasswordResetEmail($data) {
        $data['token'] = $data['login'];
        $data['token'] .= "/token=" . $this->model->getToken($data['email']);

        $header = "Content-type: text/html; charset=utf-8 \r\n";
        $subject = "Camagru reset password";

        $message = "<div style=\"background-color:pink;\">";
        $message .= "<h2>Hello, " . $data['login'] . "!</h2>";
        $message .= "<p>To reset your password click ";
        $message .= "<a href=\"" . URLROOT . "/email/resetPassword/" . $data['token'] . "\">here</a></p>";
        $message .= "<p><small>Camagru</p></div>";

        return (mail($data['email'], $subject, $message, $header));
    }

    private function createUserSession($user) {
        $_SESSION['user'] = $user;
        $this->redirect('');
    }

    public function login() {
        $data = [];
        if (isset($_SESSION['user'])) {
            $data = $this->addMessage(false, 'You need logout first!');
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $email = trim($_POST['email']);
            $password = hash('whirlpool', $_POST['password']);

            $response = $this->model->login($email, $password);

            if ($response['errors']) {
                $data = array_merge(['email' => $email], $response['errors']);
            } else if ($response['user']) {
                $this->createUserSession($response['user']);
            }
        }
        $this->renderView('users/login', $data);
    }

    private function sendConfirmationEmail($data) {
        $data['token'] = $data['login'];
        $data['token'] .= '/token=' . $this->model->getToken($data['email']);

        $header = "Content-type: text/html; charset=utf-8 \r\n";
        $subject = "Camagru confirmation email";

        $message = "<div style=\"background-color:pink;\">";
        $message .= "<h2>Hello, " . $data['login'] . "!</h2>";
        $message .= "<p>Thank you for joining Camagru</p>";
        $message .= "<p>To activate your account click ";
        $message .= "<a href=\"" . URLROOT . "/email/activateAccount/" . $data['token'] . "\">here</a></p>";
        $message .= "<p><small>If you have any questions do not hesitate to contact us.</p>";
        $message .= "<p><small>Camagru</p></div>";

        return (mail($data['email'], $subject, $message, $header));
    }

    public function register() {
        $data = [];
        if (isset($_SESSION['user'])) {
            $this->logout('users/register');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['login'] = trim($_POST['login']);
            $data['first_name'] = trim($_POST['first_name']);
            $data['last_name'] = trim($_POST['last_name']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = $_POST['password'];
            $data['confirm_password'] = $_POST['confirm_password'];

            $response = $this->model->register($data);
            if ($response['errors']) {
                unset($data['password'], $data['confirm_password']);
                $data = array_merge($data, $response['errors']);
            } else {
                $dataToSend = $this->addMessage(false, 'Could not sent an email to ' . $data['email']);
                if ($this->sendConfirmationEmail($data)) {
                    $dataToSend = $this->addMessage(true, 'Confirmation email sent to ' . $data['email']);
                }
                $this->renderView('users/login', $dataToSend);
            }
        }
        $this->renderView('users/register', $data);
    }

    public function resetPassword() {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['email']) && !isset($_POST['password'])) {
                $data['email'] = trim($_POST['email']);
                $response = $this->model->updatePassword($data['email']);

                if (!$response['errors'] && $response['user']) {
                    $this->sendPasswordResetEmail($response['user']);
                    $data = $this->addMessage(true, 'An email was sent to ' . $data['email']);
                    $this->renderView('users/login', $data);
                }
                $data = array_merge($data, $response['errors']);
            } else if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
                $data['email'] = trim($_POST['email']);
                $response = $this->model->updatePassword($data['email'], true, $_POST['password'], $_POST['confirm_password']);

                if (!$response['errors']) {
                    $data['reset'] = true;
                    $data = $this->addMessage(true, 'Password has been successfully changed.', $data);
                    $this->renderView('users/login', $data);
                }
                $data = array_merge($data, $response['errors']);
            }
        }
        $this->renderView('users/resetPassword', $data);
    }

    public function profile() {
        $this->renderView('users/profile');
    }

    public function logout($url = '') {
        unset($_SESSION['user']);
        $this->redirect($url);
    }
}
