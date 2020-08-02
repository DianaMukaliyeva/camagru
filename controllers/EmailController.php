<?php
class EmailController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
    }

    public function resetPassword($token = '') {
        $data = [];

        if (!empty($token)) {
            $data['email'] = $this->model->getEmailByToken("camagru_token" . $token);
            if ($data['email']) {
                $data['reset'] = true;
            } else {
                $data = $this->addMessage(false, 'Your token is invalid!', $data);
            }
        }

        $this->renderView('users/resetPassword', $data);
    }

    public function activateAccount($token = '') {
        $data = [];

        if (!empty($token)) {
            $data['email'] = $this->model->getEmailByToken("camagru_token" . $token);
            if ($data['email'] && $this->model->activateAccountByEmail($data['email'])) {
                $data = $this->addMessage(true, 'Your account has been successfully activated.', $data);
            } else {
                $data = $this->addMessage(false, 'Your token is invalid!');
            }
        }

        $this->renderView('users/login', $data);
    }
}
