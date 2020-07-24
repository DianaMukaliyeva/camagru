<?php
class UsersController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->getModel('User');
    }

    private function sendPasswordResetEmail($data) {
        $data['token'] = $data['login'];
        $data['token'] .= "/token=" . $this->model->getToken([$data['email']]);

        $header = "Content-type: text/html; charset=utf-8 \r\n";
        $subject = "Camagru reset password";

        $message = "<div style=\"background-color:pink;\">";
        $message .= "<h2>Hello, " . $data['login'] . "!</h2>";
        $message .= "<p>To reset your password click ";
        $message .= "<a href=\"" . URLROOT . "/users/resetPassword/" . $data['token'] . "\">here</a></p>";
        $message .= "<p><small>Camagru</p></div>";

        return (mail($data['email'], $subject, $message, $header));
    }

    public function activateAccount($login = '', $token = '') {
        if (!empty($login) && !empty($token)) {
            unset($_SESSION['user']);
            $email = $this->model->getEmailByLogin([$login]);
            if ($email) {
                $generatedToken = "token=" . $this->model->getToken([$email]);
                if ($token == $generatedToken) {
                    $this->model->updateToken([null, $email]);
                    $this->model->activateAccountByEmail([$email]);
                    $this->renderView('users/login', ['email' => $email, 'message' => ['class' => 'alert-success', 'content' => 'Your account has been successfully activated. You can login now.']]);
                } else {
                    $this->renderView('users/login', ['email' => '', 'message' => ['class' => 'alert-danger', 'content' => 'Your token is invalid!']]);
                }
            } else {
                $this->renderView('users/login', ['email' => '', 'message' => ['class' => 'alert-danger', 'content' => 'User does not exists!']]);
            }
        } else {
            $this->redirect('');
        }
    }

    private function createUserSession($user) {
        $_SESSION['user'] = $user;
        $this->redirect('');
    }

    private function validateLoginData($data) {
        // Validate Email
        if (empty($data['email'])) {
            $data['email_err'] = 'Please enter email';
        } else {
            $user = $this->model->findUserByEmail([$data['email']]);
            if (!$user) {
                $data['email_err'] = 'User with this email donesn\'t exists';
            } else if ($user['activated'] == 0) {
                $data = [
                    'email' => $data['email'],
                    'message' => ['class' => 'alert-danger', 'content' => 'Your account has not been activated yet.']
                ];
                $this->renderView('users/login', $data);
            } else if (hash('whirlpool', $data['password']) != $user['password']) {
                $data['password_err'] = 'Password incorrect';
            }
        }

        // Validate Password
        if (empty($data['password'])) {
            $data['password_err'] = 'Please enter password';
        }

        return $data;
    }

    public function login() {
        if (isset($_SESSION['user'])) {
            $this->renderView('users/login', ['message' => ['class' => 'alert-danger', 'content' => 'You need logout first!']]);
        }
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password'])
            ];

            $data = $this->validateLoginData($data);

            // Make sure errors are empty
            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->model->findUserByEmail([$data['email']]);
                $this->createUserSession($loggedInUser);
            } else {
                $this->renderView('users/login', $data);
            }
        } else {
            $this->renderView('users/login');
        }
    }

    private function sendConfirmationEmail($data) {
        $data['token'] = $data['login'];
        $data['token'] .= '/token=' . $this->model->getToken([$data['email']]);

        $header = "Content-type: text/html; charset=utf-8 \r\n";
        $subject = "Camagru confirmation email";

        $message = "<div style=\"background-color:pink;\">";
        $message .= "<h2>Hello, " . $data['login'] . "!</h2>";
        $message .= "<p>Thank you for joining Camagru</p>";
        $message .= "<p>To activate your account click ";
        $message .= "<a href=\"" . URLROOT . "/users/activateAccount/" . $data['token'] . "\">here</a></p>";
        $message .= "<p><small>If you have any questions do not hesitate to contact us.</p>";
        $message .= "<p><small>Camagru</p></div>";

        return (mail($data['email'], $subject, $message, $header));
    }

    private function validateSignupData($data) {
        // Validate Email
        if (empty($data['email'])) {
            $data['email_err'] = 'Please enter email';
        } else if ($this->model->findUserByEmail([$data['email']])) {
            $data['email_err'] = 'Email has been already taken';
        }

        // Validate First Name
        if (empty($data['first_name'])) {
            $data['first_name_err'] = 'Please enter first name';
        }

        // Validate Last Name
        if (empty($data['last_name'])) {
            $data['last_name_err'] = 'Please enter last name';
        }

        // Validate login
        if (empty($data['login'])) {
            $data['login_err'] = 'Please enter last name';
        } else if ($this->model->getEmailByLogin([$data['login']])) {
            $data['login_err'] = 'This login has already been taken';
        }

        // Validate Password
        if (empty($data['password'])) {
            $data['password_err'] = 'Please enter password';
        } else if (strlen($data['password']) < 6) {
            $data['password_err'] = 'Password must be at least 6 characters';
        }

        // Validate Confirm Password
        if (empty($data['confirm_password'])) {
            $data['confirm_password_err'] = 'Please confirm password';
        } else if ($data['password'] != $data['confirm_password']) {
            $data['confirm_password_err'] = 'Passwords do not match';
        }

        return $data;
    }

    public function signup() {
        if (isset($_SESSION['user'])) {
            $this->renderView('users/signup', ['message' => ['class' => 'alert-danger', 'content' => 'You need logout first!']]);
        }
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Init data
            $data = [
                'login' => trim($_POST['login']),
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password'])
            ];

            $data = $this->validateSignupData($data);

            // Make sure errors are empty
            if (
                empty($data['email_err']) && empty($data['login_err']) && empty($data['first_name_err']) &&
                empty($data['last_name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])
            ) {
                $dbData = [$data['login'], $data['first_name'], $data['last_name'], hash('whirlpool', $data['password']), $data['email']];

                if ($this->model->register($dbData)) {
                    $this->model->updateToken([bin2hex(random_bytes(strlen(hash('whirlpool', $data['password'])))), $data['email']]);
                    if ($this->sendConfirmationEmail($data)) {
                        $data = [
                            'message' => ['class' => 'alert-success', 'content' => 'Confirmation email sent to ' . $data['email']]
                        ];
                        $this->renderView('users/login', $data);
                    } else {
                        $data = [
                            'message' => ['class' => 'alert-danger', 'content' => 'Could not sent an email to ' . $data['email']]
                        ];
                        $this->renderView('users/login', $data);
                    }
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->renderView('users/signup', $data);
            }
        } else {
            $this->renderView('users/signup');
        }
    }

    public function resetPassword($login = '', $token = '') {
        if (!empty($login) && !empty($token)) {
            $email = $this->model->getEmailByLogin([$login]);
            if ($email) {
                $generatedToken = "token=";
                $generatedToken .= $this->model->getToken([$email]);
                if ($token == $generatedToken) {
                    unset($_SESSION['user']);
                    $this->model->updateToken([null, $email]);
                    $this->renderView('users/resetPassword', ['email' => $email, 'reset' => true]);
                } else {
                    $data = [
                        'message' => ['class' => 'alert-danger', 'content' => 'Your token is invalid!']
                    ];
                    $this->renderView('users/login', $data);
                }
            } else {
                $data = [
                    'message' => ['class' => 'alert-danger', 'content' => 'User does not exists!']
                ];
                $this->renderView('users/login', $data);
            }
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (isset($_POST['email']) && !isset($_POST['password'])) {
                $email = trim($_POST['email']);
                $user = $this->model->findUserByEmail([$email]);
                if (empty($email)) {
                    $data['email_err'] = 'Please enter email';
                } else if (!$user) {
                    $data['email_err'] = 'User with this email does not exists';
                } else if ($user['activated'] == 0) {
                    $data['email_err'] = 'Your account has not been activated yet';
                }
                if ($data) {
                    $this->renderView('users/resetPassword', $data);
                } else {
                    $this->model->updateToken([bin2hex(random_bytes(strlen($user['password']))), $user['email']]);
                    $this->sendPasswordResetEmail($user);
                    $data = [
                        'email' => $email,
                        'message' => ['class' => 'alert-success', 'content' => 'An email was sent to ' . $user['email']]
                    ];
                    $this->renderView('users/login', $data);
                }
            } else if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
                $data = [
                    'email' => trim($_POST['email']),
                    'password' => trim($_POST['password']),
                    'confirm_password' => trim($_POST['confirm_password'])
                ];

                // Validate Password
                if (empty($data['password'])) {
                    $data['password_err'] = 'Please enter password';
                } else if (strlen($data['password']) < 6) {
                    $data['password_err'] = 'Password must be at least 6 characters';
                }

                // Validate Confirm Password
                if (empty($data['confirm_password'])) {
                    $data['confirm_password_err'] = 'Please confirm password';
                } else if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }

                // Make sure errors are empty
                if (empty($data['password_err']) && empty($data['confirm_password_err'])) {
                    $data['password'] = hash('whirlpool', $data['password']);
                    $this->model->updatePassword([$data['password'], $data['email']]);
                    $data = [
                        'email' => $data['email'],
                        'message' => ['class' => 'alert-success', 'content' => 'Password has been successfully changed.']
                    ];
                    $this->renderView('users/login', $data);
                } else {
                    $data['reset'] = true;
                    $this->renderView('users/resetPassword', $data);
                }
            }
        } else {
            $this->renderView('users/resetPassword');
        }
    }

    public function profile() {
        $this->renderView('users/profile');
    }

    public function logout($url = '') {
        unset($_SESSION['user']);
        $this->redirect($url);
    }
}
