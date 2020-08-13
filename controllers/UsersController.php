<?php
class UsersController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
    }

    private function sendEmail($login, $email, $purpose) {
        $token = str_replace('camagru_token', '', $this->model->getToken($email));
        $header = "From: Camagru web application\r\n";
        $header .= "Reply-To: <hive2020hive@gmail.com>\r\n";
        $header .= "Content-type: text/html; charset=utf-8 \r\n";
        $subject = "Camagru web application";

        $message = "<div style=\"background-color:pink; text-align:center;\">";
        $message .= "<h2 style=\"text-align:center;\">Hello, " . $login . "!</h2>";

        if ($purpose == 'confirmation_email') {
            $message .= "<p>Thank you for joining Camagru</p>";
            $message .= "<p>To activate your account click ";
            $message .= "<a href=\"" . URLROOT . "/email/activateAccount/" . $token . "\">here</a></p>";
            $message .= "<p><small>If you have any questions do not hesitate to contact us.</p>";
        } else if ($purpose == 'reset_password') {
            $message .= "<p>To reset your password click ";
            $message .= "<a href=\"" . URLROOT . "/email/resetPassword/" . $token . "\">here</a></p>";
        }
        $message .= "<p><small>Camagru</p></div>";

        return (mail($email, $subject, $message, $header));
    }

    private function createUserSession($user) {
        $_SESSION[APPNAME]['user'] = $user;
        $this->redirect('');
    }

    public function login() {
        $data = [];

        if ($this->getLoggedInUser()) {
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


    public function register() {
        $data = [];
        if ($this->getLoggedInUser()) {
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
            unset($data['password'], $data['confirm_password']);
            if ($response['errors']) {
                $data = array_merge($data, $response['errors']);
            } else {
                $dataToSend = $this->addMessage(false, 'Could not sent an email to ' . $data['email']);
                if ($this->sendEmail($data['login'], $data['email'], 'confirmation_email')) {
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
                    $this->sendEmail($response['user']['login'], $response['user']['email'], 'reset_password');
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

    public function account() {
        if ($this->getLoggedInUser()) {
            $this->renderView('users/account');
        }
        $this->renderView('images/index');
    }

    public function logout($url = '') {
        unset($_SESSION[APPNAME]['user']);
        $this->redirect($url);
    }
}
