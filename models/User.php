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
        } else if ($this->findUser(['email' => $data['email']])) {
            $errors['email_err'] = 'Email has been already taken';
        }

        // Validate First Name
        if (!$data['first_name'] || empty($data['first_name'])) {
            $errors['first_name_err'] = 'Please enter first name';
        } else if (!preg_match('/^[a-zA-z]+([ \'-][a-zA-Z]+)*$/', $data['first_name'])) {
            $errors['first_name_err'] =  "Name must include letters and numbers only";
        } else if (strlen($data['first_name']) > 45) {
            $errors['first_name_err'] =  "Name must be less than 45 characters";
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

        $errors = $this->validatePassword(
            $data['password'],
            $data['confirm_password'],
            $errors
        );

        return $errors;
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

    // Get user by given parameter
    // Ex: $data = ['id' => userid]
    public function findUser($data) {
        return (Db::queryOne(
            "SELECT * FROM `users` WHERE `" . implode('`, `', array_keys($data)) . "` = ?",
            array_values($data)
        ));
    }

    // Create user
    public function register($data) {
        $errors = $this->validateRegisterData($data);

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

    // Update user's password
    public function updatePassword($email, $reset = false, $password, $confirm_password) {
        if (!$reset) {
            $user = $this->findUser(['email' => $email]);
            $errors = $this->validateExistingEmail($email, $user);
            if (!$errors) {
                $this->updateToken(bin2hex(random_bytes(50)), $email);
            }
            return $errors ? ['errors' => $errors] : ['user' => $user];
        }
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
}
