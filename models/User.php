<?php
class User {

    // Find user by email
    public function findUserByEmail($data) {
        $row = Db::queryOne('SELECT * FROM `users` WHERE `email` = ?', $data);
        return $row;
    }

    // Register an user
    public function register($data) {
        $row = Db::query('INSERT INTO `users` (`login`, `first_name`, `last_name`, `password`, `email`) VALUES (?, ?, ?, ?, ?)', $data);
        return $row;
    }

    public function getEmailByLogin($data) {
        $row = Db::queryOne('SELECT `email` FROM `users` WHERE login = ?', $data);
        return $row['email'];
    }

    public function activateAccountByEmail($data) {
        $row = Db::query('UPDATE `users` SET `activated` = 1 WHERE email = ?', $data);
        return $row;
    }

    public function getToken($data) {
        $row = Db::queryOne('SELECT `token` FROM `users` WHERE `email` = ?', $data);
        return $row['token'];
    }

    public function updateToken($data) {
        $row = Db::query('UPDATE `users` SET `token` = ? WHERE `email` = ?', $data);
        return $row;
    }

    public function updatePassword($data) {
        $row = Db::query('UPDATE `users` SET `password` = ? WHERE `email` = ?', $data);
        return $row;
    }
}
