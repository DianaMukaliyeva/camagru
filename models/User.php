<?php
class User {

    // Check passwords match and correctness
    private function validatePassword($password, $confirm_password, $errors = []) {
        if (!$password || empty($password)) {
            $errors['password_err'] = 'Please enter password';
        } else if (strlen($password) < 6) {
            $errors['password_err'] = 'Password must be at least 6 characters';
        }

        if (!$confirm_password || empty($confirm_password)) {
            $errors['confirm_password_err'] = 'Please confirm password';
        } else if ($password != $confirm_password) {
            $errors['confirm_password_err'] = 'Passwords do not match';
        }

        return $errors;
    }

    // Check all given information
    private function validateRegisterData($data) {
        $errors = [];
        // Validate Email
        if (!$data['email'] || empty($data['email'])) {
            $errors['email_err'] = 'Please enter email';
        } else if ($this->findUserByEmail($data['email'])) {
            $errors['email_err'] = 'Email has been already taken';
        }

        // Validate First Name
        if (!$data['first_name'] || empty($data['first_name'])) {
            $errors['first_name_err'] = 'Please enter first name';
        } else if (!preg_match('/^[a-zA-z]+([ \'-][a-zA-Z]+)*$/', $data['first_name'])) {
            $errors['first_name_err'] =  "First name must include letters and numbers only";
        } else if (strlen($data['first_name']) > 45) {
            $errors['first_name_err'] =  "First name must be less than 45 characters";
        }

        // Validate Last Name
        if (!$data['last_name'] || empty($data['last_name'])) {
            $errors['last_name_err'] = 'Please enter last name';
        } else if (!preg_match('/^[a-zA-z]+([ \'-][a-zA-Z]+)*$/', $data['last_name'])) {
            $errors['last_name_err'] =  "Last name must include letters and numbers only";
        } else if (strlen($data['last_name']) > 45) {
            $errors['last_name_err'] =  "Last name must be less than 45 characters";
        }

        // Validate login
        if (!$data['login'] || empty($data['login'])) {
            $errors['login_err'] = 'Please enter login';
        } else if (!preg_match('/^[A-Za-z0-9]{0,}$/', $data['login'])) {
            $errors['login_err'] =  "Login must include letters and numbers only";
        } else if (strlen($data['login']) > 25) {
            $errors['login_err'] =  "Login must be less than 25 characters";
        } else if ($this->getEmailByLogin($data['login'])) {
            $errors['login_err'] = 'This login has already been taken';
        }

        $errors = $this->validatePassword($data['password'], $data['confirm_password'], $errors);

        return $errors;
    }

    // Update user's token
    private function updateToken($token, $email) {
        if ($token) {
            $token = "camagru_token" . $token;
        }
        $row = Db::query('UPDATE `users` SET `token` = ? WHERE `email` = ?', [$token, $email]);

        return $row;
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

    // Get user with this email
    public function findUserByEmail($email) {
        return (Db::queryOne('SELECT * FROM `users` WHERE `email` = ?', [$email]));
    }

    // Get user with this login
    public function findUserByLogin($login) {
        return (Db::queryOne('SELECT * FROM `users` WHERE `login` = ?', [$login]));
    }

    // Create user
    public function register($data) {
        $errors = $this->validateRegisterData($data);

        if (!$errors) {
            $row = Db::query(
                'INSERT INTO `users` (`login`, `first_name`, `last_name`, `password`, `email`) VALUES (?, ?, ?, ?, ?)',
                [$data['login'], $data['first_name'], $data['last_name'], hash('whirlpool', $data['password']), $data['email']]
            );
            $this->updateToken(bin2hex(random_bytes(50)), $data['email']);
        }

        return ['errors' => $errors];
    }

    // Check all information of user
    public function login($email, $password) {
        $user = $this->findUserByEmail($email);
        $errors = $this->validateExistingEmail($email, $user);

        if (!$errors && $password != $user['password']) {
            $errors['password_err'] = 'Password incorrect';
        }

        return $errors ? ['errors' => $errors] : ['user' => $user];
    }

    // Get user's email by login
    public function getEmailByLogin($login) {
        $row = Db::queryOne('SELECT `email` FROM `users` WHERE `login` = ?', [$login]);

        if (isset($row['email']))
            return $row['email'];

        return $row;
    }

    // Get user's email by token
    public function getEmailByToken($token) {
        $row = Db::queryOne('SELECT `email` FROM `users` WHERE `token` = ?', [$token]);

        if (isset($row['email']))
            return $row['email'];

        return $row;
    }

    // Get user's login by id
    public function getLoginById($id) {
        $row = Db::queryOne('SELECT `login` FROM `users` WHERE `id` = ?', [$id]);

        if (isset($row['login']))
            return $row['login'];

        return $row;
    }

    // Get user by id
    public function getUserById($id) {
        $row = Db::queryAll('SELECT * FROM `users` WHERE `id` = ?', [$id]);

        if (isset($row[0]))
            return $row[0];

        return $row;
    }

    // Activate user's account
    public function activateAccountByEmail($email) {
        $row = Db::query('UPDATE `users` SET `activated` = 1 WHERE `email` = ?', [$email]);
        $this->updateToken(null, $email);

        return $row;
    }

    // Get user's token
    public function getToken($email) {
        $row = Db::queryOne('SELECT `token` FROM `users` WHERE `email` = ?', [$email]);

        if (isset($row['token']))
            return $row['token'];

        return '';
    }

    // Update user's password
    public function updatePassword($email, $reset = false, $password = '', $confirm_password = '') {
        if (!$reset) {
            $user = $this->findUserByEmail($email);
            $errors = $this->validateExistingEmail($email, $user);
            if (!$errors) {
                $this->updateToken(bin2hex(random_bytes(50)), $email);
            }
            return $errors ? ['errors' => $errors] : ['user' => $user];
        }
        $errors = $this->validatePassword($password, $confirm_password);

        if (!$errors) {
            Db::query('UPDATE `users` SET `password` = ? WHERE `email` = ?', [hash('whirlpool', $password), $email]);
            $this->updateToken(null, $email);
        }

        return ['errors' => $errors];
    }
}
