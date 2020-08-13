<?php
class Comment {

    // Get all comments of image
    public function getComments($imageId) {
        $result = Db::queryAll(
            'SELECT comments.id AS `id`, users.login, `comment`, comments.created_at AS `created_at`,
            `user_id`, `image_id` FROM `comments` LEFT JOIN `users` ON users.id = comments.user_id WHERE `image_id` = ?',
            [$imageId]
        );
        return $result;
    }

    // Add comment to image
    public function addComment($userId, $imageId, $comment) {
        $result = Db::query('INSERT INTO `comments` (`user_id`, `image_id`, `comment`) VALUES (?, ?, ?)', [$userId, $imageId, $comment]);
        return $result;
    }

    // Get how many comments image has
    public function getNumberOfComments($imageId) {
        $result = Db::queryOne('SELECT COUNT(*) FROM `comments` WHERE `image_id` = ?', [$imageId]);
        return $result['COUNT(*)'];
    }

    // Check if user commented this image
    public function isCommented($userId, $imageId) {
        $result = Db::queryOne('SELECT `id` FROM `comments` WHERE `user_id` = ? AND `image_id` = ?', [$userId, $imageId]);
        if (isset($result['id']))
            return $result['id'];
        return $result;
    }

    // Get date of comment
    public function getCreatedDateOfComment($commentId) {
        $result = Db::queryOne('SELECT `created_at` FROM `comments` WHERE `id` = ?', [$commentId]);
        if (isset($result['created_at']))
            return $result['created_at'];
        return $result;
    }
}
