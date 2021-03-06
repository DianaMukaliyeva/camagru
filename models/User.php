<?php
class User {

    // Search user's
    public function searchUsers($search) {
        $search = '%' . $search . '%';
        $result = Db::queryAll(
            "SELECT * FROM `users` WHERE `login` LIKE ? OR `first_name` LIKE ? OR `last_name` LIKE ? ",
            [$search, $search, $search]
        );

        return $result;
    }

    // Get all user's information
    public function getUserInfo($userId, $loggedUserId = 0) {
        $result = Db::queryOne(
            "SELECT users.id, users.login, users.first_name, users.last_name,
                users.email, users.notify, users.picture,
                (SELECT COUNT(`id`) FROM `followers`
                WHERE `user_id_followed` = users.id) AS followers_amount,
                (SELECT COUNT(`id`) FROM `followers`
                WHERE `user_id_follower` = users.id) AS followed_amount,
                (SELECT COUNT(`id`) FROM `followers`
                WHERE `user_id_followed` = users.id AND `user_id_follower` = ?) AS user_follow,
                (SELECT COUNT(`id`) FROM `images`
                WHERE `user_id` = users.id) AS images_amount FROM `users` WHERE users.id = ?",
            [$loggedUserId, $userId]
        );
        return isset($result[0]) ? $result[0] : $result;
    }

    // Get user by given parameter
    // Example of data: $data = ['id' => userid]
    public function findUser($data) {
        $result = Db::queryOne(
            "SELECT `id`, `login`, `first_name`, `last_name`, `password`, `token`,
                `email`, `notify`, `activated`, `picture`, `created_at`
                FROM `users` WHERE `" . implode('`, `', array_keys($data)) . "` = ?",
            array_values($data)
        );

        return isset($result[0]) ? $result[0] : $result;
    }

    // Create user
    public function register($data) {
        $errors = $this->validateUsersData($data);

        if (!$errors) {
            $dataToInsert = [
                'login' => $data['login'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'password' => hash('whirlpool', $data['password']),
                'email' => $data['email']
            ];
            $result = Db::insert('users', $dataToInsert);
            $this->updateToken(bin2hex(random_bytes(50)), $data['email']);
        }

        return ['errors' => $errors];
    }

    // Check all information of user
    public function login($email, $password) {
        $user = $this->findUser(['email' => $email]);
        $errors = $this->validateExistingEmail($email, $user);

        if (!$errors && $password != $user['password']) {
            $errors['password_err'] = 'Password incorrect';
        }
        if (!$errors)
            $this->updateToken(false, $email);

        return $errors ? ['errors' => $errors] : ['user' => $user];
    }

    // Get user's email by login
    public function getEmailByLogin($login) {
        $result = Db::queryOne('SELECT `email` FROM `users` WHERE `login` = ?', [$login]);

        return isset($result['email']) ? $result['email'] : $result;
    }

    // Get user's email by token
    public function getEmailByToken($token) {
        $result = Db::queryOne('SELECT `email` FROM `users` WHERE `token` = ?', [$token]);

        return isset($result['email']) ? $result['email'] : $result;
    }

    // Get user's login by id
    public function getLoginById($id) {
        $result = Db::queryOne('SELECT `login` FROM `users` WHERE `id` = ?', [$id]);

        return isset($result['login']) ? $result['login'] : $result;
    }

    // Activate user's account
    public function activateAccountByEmail($email) {
        $result = Db::query(
            'UPDATE `users` SET `activated` = 1 WHERE `email` = ?',
            [$email]
        );
        $this->updateToken(null, $email);

        return $result;
    }

    // Get user's token
    public function getToken($email) {
        $result = Db::queryOne('SELECT `token` FROM `users` WHERE `email` = ?', [$email]);

        return isset($result['token']) ? $result['token'] : '';
    }

    // Reset user\'s password
    public function resetPassword($email) {
        $user = $this->findUser(['email' => $email]);
        $errors = $this->validateExistingEmail($email, $user);

        if (!$errors) {
            $this->updateToken(bin2hex(random_bytes(50)), $email);
        }

        return $errors ? ['errors' => $errors] : ['user' => $user];
    }

    // Update user's password
    public function updatePassword($email, $password, $confirm_password) {
        $errors = $this->validatePassword($password, $confirm_password);

        if (!$errors) {
            Db::query(
                'UPDATE `users` SET `password` = ? WHERE `email` = ?',
                [hash('whirlpool', $password), $email]
            );
            $this->updateToken(null, $email);
        }

        return ['errors' => $errors];
    }

    // Update user's token
    private function updateToken($token, $email) {
        $token =  $token ? "camagru_token" . $token : false;
        $result = Db::query(
            'UPDATE `users` SET `token` = ? WHERE `email` = ?',
            [$token, $email]
        );

        return $result;
    }

    // Update user's profile picture
    public function updatePicture($userId, $pathToImage) {
        $result = Db::query(
            'UPDATE `users` SET `picture` = ? WHERE `id` = ?',
            [$pathToImage, $userId]
        );

        return $result;
    }

    // Update user's information
    // $data keys = {'id', 'email', 'first_name', 'last_name', 'notify',
    //              'login', 'old_pswd', 'new_pswd', 'new_pswd_confirm'}
    public function updateInfo($data) {
        $result = Db::query(
            'UPDATE `users` SET `login` = ?, `first_name` = ?, `last_name` = ?,
                `email` = ?, `notify` = ? WHERE `id` = ?',
            [
                $data['login'],
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['notify'],
                $data['id']
            ]
        );

        if (!empty($data['new_pswd'])) {
            $this->updatePassword($data['email'], $data['new_pswd'], $data['new_pswd_confirm']);
        }

        return $result;
    }

    // Check email
    private function validateExistingEmail($email, $user) {
        $errors = [];

        if (empty($email)) {
            $errors['email_err'] = 'Please enter email';
        } else if (!$user) {
            $errors['email_err'] = 'User with this email doesn\'t exists';
        } else if ($user['activated'] == 0) {
            $errors['email_err'] = 'Your account has not been activated yet.';
        }

        return $errors;
    }

    // Check passwords match and correctness
    public function validatePassword($password, $confirm_password, $errors = []) {
        if (!$password || empty($password)) {
            $errors['password_err'] = 'Please enter password';
        } else if (strlen($password) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/', $password)) {
            $errors['password_err'] = 'Password must be at least 8 characters and contain at least 1 uppercase,
                1 lowercase letter and at least 1 number';
        }

        if (!$confirm_password || empty($confirm_password)) {
            $errors['confirm_password_err'] = 'Please confirm password';
        } else if ($password != $confirm_password) {
            $errors['confirm_password_err'] = 'Passwords do not match';
        }

        return $errors;
    }

    // Check all given information
    public function validateUsersData($data, $update = false, $loggedUser = []) {
        $errors = [];
        // Validate Email
        if (!$data['email'] || empty($data['email'])) {
            $errors['email_err'] = 'Please enter email';
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_err'] = 'Invalid mail';
        } else if (!$update && $this->findUser(['email' => $data['email']])) {
            $errors['email_err'] = 'Email has been already taken';
        }

        // Validate First Name
        if (!$data['first_name'] || empty($data['first_name'])) {
            $errors['first_name_err'] = 'Please enter first name';
        } else if (!preg_match('/^[A-Za-z0-9]{0,}$/', $data['first_name'])) {
            $errors['first_name_err'] = "Name must include letters and numbers only";
        } else if (strlen($data['first_name']) > 45) {
            $errors['first_name_err'] = "Name must be less than 45 characters";
        }

        // Validate Last Name
        if (!$data['last_name'] || empty($data['last_name'])) {
            $errors['last_name_err'] = 'Please enter last name';
        } else if (!preg_match('/^[A-Za-z0-9]{0,}$/', $data['last_name'])) {
            $errors['last_name_err'] = "Last name must include letters and numbers only";
        } else if (strlen($data['last_name']) > 45) {
            $errors['last_name_err'] = "Last name must be less than 45 characters";
        }

        // Validate login
        if (!$data['login'] || empty($data['login'])) {
            $errors['login_err'] = 'Please enter login';
        } else if (!preg_match('/^[A-Za-z0-9]{0,}$/', $data['login'])) {
            $errors['login_err'] = "Login must include letters and numbers only";
        } else if (strlen($data['login']) > 25) {
            $errors['login_err'] = "Login must be less than 25 characters";
        } else if (!$update && $this->getEmailByLogin($data['login'])) {
            $errors['login_err'] = 'This login has already been taken';
        }

        if (!$update) {
            $errors = $this->validatePassword($data['password'], $data['confirm_password'], $errors);
        } else {
            if ($loggedUser['password'] != hash('whirlpool', $data['password'])) {
                $errors['pass_err'] = 'Password incorrect';
            }
            if ($data['login'] != $loggedUser['login'] && $this->getEmailByLogin($data['login'])) {
                $errors['login_err'] = "This login has already been taken\n";
            }
            if ($data['email'] != $loggedUser['email'] && $this->findUser(['email' => $data['email']])) {
                $errors['email_err'] = "This email has already been taken\n";
            }
            if (!empty($data['new_pswd']) || !empty($data['new_pswd_confirm'])) {
                $errors = $this->validatePassword($data['new_pswd'], $data['new_pswd_confirm'], $errors);
            }
        }

        return $errors;
    }
}
