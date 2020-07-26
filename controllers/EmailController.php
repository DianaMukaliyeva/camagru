<?php
class EmailController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
    }

    public function resetPassword($login = '', $token = '') {
        $data = [];
        if (!empty($login) && !empty($token)) {
            if ($data['email'] = $this->model->getEmailByLogin($login)) {
                $generatedToken = "token=" . $this->model->getToken($data['email']);
                if ($token == $generatedToken) {
                    $this->renderView('users/resetPassword', ['email' => $data['email'], 'reset' => true]);
                } else {
                    $data = $this->addMessage(false, 'Your token is invalid!', $data);
                }
            } else {
                $data = $this->addMessage(false, 'User does not exists!', $data);
            }
        }
        $this->renderView('users/resetPassword', $data);
    }

    public function activateAccount($login = '', $token = '') {
        if (!empty($login) && !empty($token)) {
            unset($_SESSION['user']);
            $data['email'] = $this->model->getEmailByLogin($login);
            if ($data['email']) {
                $generatedToken = "token=" . $this->model->getToken($data['email']);
                if ($token == $generatedToken) {
                    $this->model->updateToken([null, $data['email']]);
                    $this->model->activateAccountByEmail($data['email']);
                    $data = $this->addMessage(true, 'Your account has been successfully activated.', $data);
                } else {
                    $data = $this->addMessage(false, 'Your token is invalid!');
                }
                $this->renderView('users/login', $data);
            }
            $data = $this->addMessage(true, 'User does not exists!', $data);
            $this->renderView('users/login', $data);
        }
        $this->renderView('users/login');
    }
}
