<?php
require_once('database.php');
require_once('../components/Db.php');

try {
    // Connect to mysql
    Db::connect($DB_DSN, $DB_USER, $DB_PASSWORD);

    // Set time zone
    Db::query("SET time_zone = \"+03:00\"");

    // Drop database if it already exists
    Db::query("DROP DATABASE IF EXISTS $DB_NAME;");

    // Create database
    Db::query("CREATE DATABASE $DB_NAME");

    // Use database
    Db::query("USE $DB_NAME");

    // Create table users
    $query = <<<SQL
        CREATE TABLE `users` (
            `id`         int AUTO_INCREMENT PRIMARY KEY,
            `login`      varchar(20) NOT NULL,
            `first_name` varchar(45) NOT NULL,
            `last_name`  varchar(45) NOT NULL,
            `password`   varchar(1000) NOT NULL,
            `email`      varchar(45) NOT NULL,
            `notify`     tinyint DEFAULT 1,
            `activated`  tinyint DEFAULT 0,
            `picture`    varchar(45),
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table images
    $query = <<<SQL
        CREATE TABLE `images` (
            `id`         int AUTO_INCREMENT PRIMARY KEY,
            `image_path` varchar(45) NOT NULL,
            `title`      varchar(45),
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `user_id`    int NOT NULL,

            KEY `fkIdx_46` (`user_id`),
            CONSTRAINT `FK_46` FOREIGN KEY `fkIdx_46` (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table likes
    $query = <<<SQL
        CREATE TABLE `likes` (
            `id`         int AUTO_INCREMENT PRIMARY KEY,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `user_id`    int NOT NULL,
            `image_id`    int NOT NULL,

            KEY `fkIdx_79` (`user_id`),
            CONSTRAINT `FK_79` FOREIGN KEY `fkIdx_79` (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            KEY `fkIdx_82` (`image_id`),
            CONSTRAINT `FK_82` FOREIGN KEY `fkIdx_82` (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table comments
    $query = <<<SQL
        CREATE TABLE `comments` (
            `id`         int AUTO_INCREMENT PRIMARY KEY,
            `comment`    varchar(100) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `user_id`    int NOT NULL,
            `image_id`    int NOT NULL,

            KEY `fkIdx_56` (`image_id`),
            CONSTRAINT `FK_56` FOREIGN KEY `fkIdx_56` (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
            KEY `fkIdx_59` (`user_id`),
            CONSTRAINT `FK_59` FOREIGN KEY `fkIdx_59` (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table followers
    $query = <<<SQL
        CREATE TABLE `followers` (
            `id`               int AUTO_INCREMENT PRIMARY KEY,
            `created_at`       datetime DEFAULT CURRENT_TIMESTAMP,
            `user_id_followed`    int NOT NULL,
            `user_id_follower`    int NOT NULL,

            KEY `fkIdx_69` (`user_id_followed`),
            CONSTRAINT `FK_69` FOREIGN KEY `fkIdx_69` (`user_id_followed`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            KEY `fkIdx_72` (`user_id_follower`),
            CONSTRAINT `FK_72` FOREIGN KEY `fkIdx_72` (`user_id_follower`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table tags
    $query = <<<SQL
        CREATE TABLE `tags` (
            `id`       int AUTO_INCREMENT PRIMARY KEY,
            `title`    varchar(45) NOT NULL,
            `image_id`    int NOT NULL,

            KEY `fkIdx_94` (`image_id`),
            CONSTRAINT `FK_94` FOREIGN KEY `fkIdx_94` (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table filters
    $query = <<<SQL
        CREATE TABLE `filters` (
        `id`   int AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(45) NOT NULL,
        `path` varchar(45) NOT NULL
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    echo "db_created.";
} catch (Exception $e) {
    die($e->getMessage());
}
