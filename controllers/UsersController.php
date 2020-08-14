<?php
class UsersController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
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
            $data['login'] = trim(filter_var($_POST['login'], FILTER_SANITIZE_STRING));
            $data['first_name'] = trim(filter_var($_POST['first_name'], FILTER_SANITIZE_STRING));
            $data['last_name'] = trim(filter_var($_POST['last_name'], FILTER_SANITIZE_STRING));
            $data['email'] = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
            $data['password'] = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $data['confirm_password'] = filter_var($_POST['confirm_password'], FILTER_SANITIZE_STRING);

            $response = $this->model->register($data);
            unset($data['password'], $data['confirm_password']);
            if ($response['errors']) {
                $data = array_merge($data, $response['errors']);
            } else {
                $token = str_replace('camagru_token', '', $this->model->getToken($data['email']));
                $message = "<p>Thank you for joining Camagru</p>";
                $message .= "<p>To activate your account click ";
                $message .= "<a href=\"" . URLROOT . "/account/activateAccount/" . $token . "\">here</a></p>";
                $message .= "<p><small>If you have any questions do not hesitate to contact us.</p>";
                $dataToSend = $this->addMessage(false, 'Could not sent an email to ' . $data['email']);
                if ($this->sendEmail($data['email'], $data['login'], $message)) {
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

                if (!isset($response['errors']) && isset($response['user'])) {
                    $token = str_replace('camagru_token', '', $this->model->getToken($data['email']));
                    $message = "<p>To reset your password click ";
                    $message .= "<a href=\"" . URLROOT . "/account/resetPassword/" . $token . "\">here</a></p>";
                    if ($this->sendEmail($data['email'], $response['user']['login'], $message))
                        $data = $this->addMessage(true, 'An email was sent to ' . $data['email']);
                    else
                        $data = $this->addMessage(false, 'Could not sent an email to ' . $data['email']);
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

    public function logout($url = '') {
        unset($_SESSION[APPNAME]['user']);
        $this->redirect($url);
    }
}
