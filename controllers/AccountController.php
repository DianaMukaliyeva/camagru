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

    public function update() {
        // Only works only for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();
        $json['message'] = '';

        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else {
            $postData = json_decode($_POST['data'], true);
            $userData = [
                'id' => trim($postData['id']),
                'first_name' => trim(filter_var($postData['first_name'], FILTER_SANITIZE_STRING)),
                'last_name' => trim(filter_var($postData['last_name'], FILTER_SANITIZE_STRING)),
                'login' => trim(filter_var($postData['login'], FILTER_SANITIZE_STRING)),
                'email' => filter_var($postData['email'], FILTER_SANITIZE_EMAIL),
                'password' => $postData['old_pswd'],
                'notify' => $postData['notify'] ? 1 : 0
            ];
            if ($userData['id'] != $user['id']) {
                $json['message'] = 'You can not change another user\'s information';
            } else if (!$userFound = $this->userModel->findUser(['id' => $userData['id']])) {
                $json['message'] = 'User does not exists';
            } else if ($userFound['password'] != hash('whirlpool', $postData['old_pswd'])) {
                $json['message'] = 'Password incorrect';
            } else {
                if ($userData['login'] != $user['login'] && $this->userModel->getEmailByLogin($userData['login'])) {
                    $json['message'] .= "This login has already been taken\n";
                }
                if ($userData['email'] != $user['email'] && $this->userModel->findUser(['email' => $userData['email']])) {
                    $json['message'] = "This email has already been taken\n";
                }
                $errors = [];
                $errors = $this->userModel->validateInput($userData, $errors);
                if (!empty($postData['new_pswd'])) {
                    $errors = $this->userModel->validatePassword($postData['new_pswd'], $postData['new_pswd_confirm'], $errors);
                }
                if (!$errors) {
                    if (!empty($postData['new_pswd'])) {
                        $this->userModel->updatePassword($userFound['email'], true, $postData['new_pswd'], $postData['new_pswd_confirm']);
                    }
                    $this->userModel->updateInfo($userData);
                    $_SESSION[APPNAME]['user']['login'] = $userData['login'];
                    $_SESSION[APPNAME]['user']['first_name'] = $userData['first_name'];
                    $_SESSION[APPNAME]['user']['last_name'] = $userData['last_name'];
                    $_SESSION[APPNAME]['user']['email'] = $userData['email'];
                    $_SESSION[APPNAME]['user']['notify'] = $userData['notify'];
                } else {
                    foreach ($errors as $error) {
                        $json['message'] .= $error . "\n";
                    }
                }
            }
        }
        echo json_encode($json);
    }

    public function userInfo() {
        // Only works only for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();
        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else {
            $json = $user;
        }
        echo json_encode($json);
    }

    public function profile(...$param) {
        $loggedUser = $this->getLoggedInUser();
        $id = empty($param) ? false : $param[0];
        if (!$id) {
            $this->renderView('images/index');
        } else if (!$loggedUser) {
            // not logged user looks at someone's profile
            $userInfo = $this->userModel->getUserInfo($id);
        } else {
            // logged user looks at his or someone's profile
            $userInfo = $this->userModel->getUserInfo($id, $loggedUser['id']);
        }
        // info pro followers/ all images / following / button follow / user itself
        // $user = $this->userModel->getAllUserInfo($loggedUser['id'], $login);
        $this->renderView('users/profile', $userInfo);
    }
}
