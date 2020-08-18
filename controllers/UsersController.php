<?php
class UsersController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
    }

    private function createUserSession($user) {
        unset($user['password']);
        $_SESSION[APPNAME]['user'] = $user;
        $this->redirect('');
    }

    public function login() {
        if ($this->isAjaxRequest()) {
            $postData = json_decode($_POST['data'], true);
            $email = trim($postData['email']);
            $password = hash('whirlpool', $postData['password']);
            $json = $this->model->login($email, $password);

            if (isset($json['user'])) {
                $this->createUserSession($json['user']);
            }
            echo json_encode($json);
        } else {
            if ($this->getLoggedInUser()) {
                $this->redirect('');
            }
            $this->renderView('users/login');
        }
    }

    public function register() {
        if ($this->isAjaxRequest()) {
            $postData = json_decode($_POST['data'], true);
            $data = [
                'login' => trim(filter_var($postData['login'], FILTER_SANITIZE_STRING)),
                'first_name' => trim(filter_var($postData['first_name'], FILTER_SANITIZE_STRING)),
                'last_name' => trim(filter_var($postData['last_name'], FILTER_SANITIZE_STRING)),
                'email' => trim(filter_var($postData['email'], FILTER_SANITIZE_EMAIL)),
                'password' => $postData['password'],
                'confirm_password' => $postData['confirm_password']
            ];
            $json = $this->model->register($data);
            unset($data['password'], $data['confirm_password']);

            if (empty($json['errors'])) {
                $token = str_replace('camagru_token', '', $this->model->getToken($data['email']));
                $message = "<p>Thank you for joining Camagru</p>";
                $message .= "<p>To activate your account click ";
                $message .= "<a href=\"" . URLROOT . "/account/activateAccount/" . $token . "\">here</a></p>";
                $message .= "<p><small>If you have any questions do not hesitate to contact us.</p>";
                $json['message'] = 'Could not sent an email to ' . $data['email'];
                if ($this->sendEmail($data['email'], $data['login'], $message)) {
                    $json['message'] = 'Confirmation email sent to ' . $data['email'];
                }
            }
            echo json_encode($json);
        } else {
            if ($this->getLoggedInUser()) {
                $this->redirect('');
            }
            $this->renderView('users/register');
        }
    }

    public function forgetPassword() {
        if ($this->isAjaxRequest()) {
            $json['errors'] = [];
            $postData = json_decode($_POST['data'], true);
            $email = trim($postData['email']);
            $result = $this->model->resetPassword($email);

            if (isset($result['errors'])) {
                $json['errors'] = $result['errors'];
            } else if (isset($result['user'])) {
                $token = str_replace('camagru_token', '', $this->model->getToken($email));
                $message = "<p>To reset your password click ";
                $message .= "<a href=\"" . URLROOT . "/account/resetPassword/" . $token . "\">here</a></p>";
                if ($this->sendEmail($email, $result['user']['login'], $message))
                    $json['message'] = 'An email was sent to ' . $email;
            }

            echo json_encode($json);
        } else {
            $this->renderView('users/forgetPassword');
        }
    }

    public function logout($url = '') {
        unset($_SESSION[APPNAME]['user']);
        $this->redirect($url);
    }
}
