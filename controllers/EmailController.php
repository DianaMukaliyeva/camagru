<?php
class EmailController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
    }

    public function resetPassword($login = '', $token = '') {
        $data = [];

        if (!empty($login) && !empty($token)) {
            $data['email'] = $this->model->getEmailByLogin($login);
            if ($data['email']) {
                $generatedToken = "token=" . $this->model->getToken($data['email']);
                if ($token == $generatedToken) {
                    $data['reset'] = true;
                } else {
                    $data = $this->addMessage(false, 'Your token is invalid!', $data);
                }
            } else {
                $data = $this->addMessage(false, 'User does not exists!');
            }
        }

        $this->renderView('users/resetPassword', $data);
    }

    public function activateAccount($login = '', $token = '') {
        $data = [];

        if (!empty($login) && !empty($token)) {
            $data['email'] = $this->model->getEmailByLogin($login);
            if ($data['email']) {
                $generatedToken = "token=" . $this->model->getToken($data['email']);
                if ($token == $generatedToken && $this->model->activateAccountByEmail($data['email'])) {
                    $data = $this->addMessage(true, 'Your account has been successfully activated.', $data);
                } else {
                    $data = $this->addMessage(false, 'Your token is invalid!');
                }
            } else {
                $data = $this->addMessage(false, 'User does not exists!', $data);
            }
        }

        $this->renderView('users/login', $data);
    }
}
