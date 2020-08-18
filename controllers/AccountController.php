<?php
class AccountController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->getModel('User');
    }

    // Check validity of token to reset password
    public function resetPassword($token = '') {
        if ($this->isAjaxRequest()) {
            $postData = json_decode($_POST['data'], true);
            $email = trim($postData['email']);
            $json = $this->userModel->updatePassword($email, $postData['password'], $postData['confirm_password']);

            if (!$json['errors']) {
                $json['message'] = 'Password has been successfully changed.';
            }
            echo json_encode($json);
        } else {
            if (!empty($token)) {
                $data['email'] = $this->userModel->getEmailByToken("camagru_token" . $token);
                if ($data['email']) {
                    $data = $this->addMessage(true, 'You can change your password', $data);
                    $this->renderView('users/resetPassword', $data);
                }
            }
            $data = $this->addMessage(false, 'Your token is invalid!', $data);
            $this->renderView('users/resetPassword', $data);
        }
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

    // Update user\'s information
    public function update() {
        // Only works only for ajax requests
        $this->onlyAjaxRequests();

        $json['message'] = '';
        $user = $this->getLoggedInUser();

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
                'new_pswd' => $postData['new_pswd'],
                'new_pswd_confirm' => $postData['new_pswd_confirm'],
                'password' => $postData['old_pswd'],
                'notify' => $postData['notify'] ? 1 : 0
            ];

            if ($userData['id'] != $user['id']) {
                $json['message'] = 'You can not change another user\'s information';
            } else if (!$userFound = $this->userModel->findUser(['id' => $userData['id']])) {
                $json['message'] = 'User does not exists';
            } else {
                $errors = $this->userModel->validateUsersData($userData, true, $userFound);

                if (!$errors) {
                    $this->userModel->updateInfo($userData);
                    // Update information of logged user
                    $_SESSION[APPNAME]['user']['login'] = $userData['login'];
                    $_SESSION[APPNAME]['user']['first_name'] = $userData['first_name'];
                    $_SESSION[APPNAME]['user']['last_name'] = $userData['last_name'];
                    $_SESSION[APPNAME]['user']['email'] = $userData['email'];
                    $_SESSION[APPNAME]['user']['notify'] = $userData['notify'];
                } else {
                    $json['errors'] = $errors;
                }
            }
        }

        echo json_encode($json);
    }

    // Get all information about logged in user
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

    // Update profile picture of user
    public function updatePicture() {
        // Only works only for ajax requests
        $this->onlyAjaxRequests();

        $json = [];
        $user = $this->getLoggedInUser();

        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else {
            $postData = json_decode($_POST['data'], true);
            $newPicturePath = 'assets/img/user_' . $user['id'] . '/profile.png';
            if ($postData['user_id'] != $user['id']) {
                $json['message'] = 'You can not update another user\'s information';
            } else if (!file_exists(APPROOT . '/' . $postData['image_path'])) {
                $json['message'] = 'Image does not exists';
            } else if (!copy(APPROOT . '/' . $postData['image_path'], APPROOT . '/' . $newPicturePath)) {
                $json['message'] = 'Failed to copy image';
            } else {
                $this->userModel->updatePicture($postData['user_id'], $newPicturePath);
                $json['path'] = $newPicturePath;
            }
        }
        echo json_encode($json);
    }

    // Show user profile
    public function profile(...$param) {
        $id = empty($param) ? false : $param[0];
        $loggedUser = $this->getLoggedInUser();

        if (!$id) {
            $this->renderView('images/index');
        } else if (!$loggedUser) {
            // not logged user looks at someone's profile
            $userInfo = $this->userModel->getUserInfo($id);
        } else {
            // logged user looks at his or someone's profile
            $userInfo = $this->userModel->getUserInfo($id, $loggedUser['id']);
        }

        $this->renderView('users/profile', $userInfo);
    }
}
