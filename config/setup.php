<?php
require_once('database.php');
require_once('../components/Db.php');

try {
    // Connect to mysql
    Db::connect($DB_DSN, $DB_USER, $DB_PASSWORD);

    // Set time zone
    Db::query("SET time_zone = \"+03:00\"");

    // Drop database if it already exists
    // Db::query("DROP DATABASE IF EXISTS $DB_NAME;");

    // Create database
    Db::query("CREATE DATABASE IF NOT EXISTS $DB_NAME");

    // Use database
    Db::query("USE $DB_NAME");

    // Create table users
    $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `users` (
            `id`         int AUTO_INCREMENT PRIMARY KEY,
            `login`      varchar(20) NOT NULL,
            `first_name` varchar(255) NOT NULL,
            `last_name`  varchar(255) NOT NULL,
            `password`   varchar(1000) NOT NULL,
            `token`      varchar(1000),
            `email`      varchar(255) NOT NULL,
            `notify`     tinyint DEFAULT 1,
            `activated`  tinyint DEFAULT 0,
            `picture`    varchar(255) DEFAULT 'assets/img/images/default.png',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table images
    $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `images` (
            `id`         int AUTO_INCREMENT PRIMARY KEY,
            `image_path` varchar(500) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `user_id`    int NOT NULL,

            KEY `fkIdx_46` (`user_id`),
            CONSTRAINT `FK_46` FOREIGN KEY `fkIdx_46` (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table likes
    $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `likes` (
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
        CREATE TABLE IF NOT EXISTS `comments` (
            `id`         int AUTO_INCREMENT PRIMARY KEY,
            `comment`    varchar(255) NOT NULL,
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
        CREATE TABLE IF NOT EXISTS `followers` (
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
        CREATE TABLE IF NOT EXISTS `tags` (
            `id`       int AUTO_INCREMENT PRIMARY KEY,
            `tag`    varchar(45) NOT NULL,
            `image_id`    int NOT NULL,

            KEY `fkIdx_94` (`image_id`),
            CONSTRAINT `FK_94` FOREIGN KEY `fkIdx_94` (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Create table filters
    $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `filters` (
        `id`   int AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(45) NOT NULL,
        `path` varchar(255) NOT NULL
        ) ENGINE=InnoDB;
    SQL;
    Db::query($query);

    // Fill table filters
    $filters = Db::query('SELECT * FROM filters;');
    if ($filters == 0) {
        $query = <<<SQL
        INSERT INTO `filters` (`name`, `path`) VALUES
            ("Beach", "/assets/img/filters/beach.png"),
            ("Sun", "/assets/img/filters/sun.png"),
            ("Hat", "/assets/img/filters/hat.png"),
            ("Frame", "/assets/img/filters/frame.png"),
            ("Catty", "/assets/img/filters/catty.png"),
            ("Wow", "/assets/img/filters/wow.png"),
            ("Drink", "/assets/img/filters/drink.png")
        ;
        SQL;
        Db::query($query);
    }

    echo "db_created.";
    header('Location: /' . basename(dirname(dirname($_SERVER['PHP_SELF']))));
} catch (Exception $e) {
    die($e->getMessage());
}
