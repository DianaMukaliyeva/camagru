<?php
class AccountController extends Controller {
    private $userModel;
    private $imageModel;
    private $likeModel;
    private $commentModel;

    public function __construct() {
        $this->userModel = $this->getModel('User');
        $this->userModel = $this->getModel('User');
        $this->likeModel = $this->getModel('Like');
        $this->commentModel = $this->getModel('Comment');
    }

    // Check validity of token to reset password
    public function resetPassword($token = '') {
        $data = [];

        if (!empty($token)) {
            $data['email'] = $this->userModel->getEmailByToken("camagru_token" . $token);
            if ($data['email']) {
                $data['reset'] = true;
            } else {
                $data = $this->addMessage(false, 'Your token is invalid!', $data);
            }
        }

        $this->renderView('users/resetPassword', $data);
    }

    // Check validity of token for account activation
    public function activateAccount($token = '') {
        $data = [];

        if (!empty($token)) {
            $data['email'] = $this->userModel->getEmailByToken("camagru_token" . $token);
            if ($data['email'] && $this->userModel->activateAccountByEmail($data['email'])) {
                $data = $this->addMessage(
                    true,
                    'Your account has been successfully activated.',
                    $data
                );
            } else {
                $data = $this->addMessage(false, 'Your token is invalid!');
            }
        }

        $this->renderView('users/login', $data);
    }

    public function editAccountInfo() {
        if ($this->getLoggedInUser()) {
            $this->renderView('users/account');
        }
        $this->renderView('images/index');
    }

    public function follow() {
        if ($this->getLoggedInUser()) {
            $this->renderView('users/account');
        }
        $this->renderView('images/index');
    }

    public function profile(...$param) {
        $user = empty($param) ? false : $param[0];
        $this->renderView('users/profile', $user);
    }
}
