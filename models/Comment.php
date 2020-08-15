<?php
class Comment {

    // Get all comments of image
    public function getComments($imageId) {
        $result = Db::queryAll(
            'SELECT comments.id AS `id`, users.login, `comment`
            ,comments.created_at AS `created_at`,
            `user_id`, `image_id` FROM `comments`
            LEFT JOIN `users` ON users.id = comments.user_id WHERE `image_id` = ?',
            [$imageId]
        );

        return $result;
    }

    // Add comment to image
    public function addComment($userId, $imageId, $comment) {
        $dataToInsert = [
            'user_id' => $userId,
            'image_id' => $imageId,
            'comment' => $comment
        ];
        $result = Db::insert('comments', $dataToInsert);

        return $result;
    }

    // Get how many comments image has
    public function getNumberOfComments($imageId) {
        $result = Db::queryOne(
            'SELECT COUNT(*) FROM `comments` WHERE `image_id` = ?',
            [$imageId]
        );

        return $result['COUNT(*)'];
    }

    // Check if user commented this image
    public function isCommented($userId, $imageId) {
        $result = Db::queryOne(
            'SELECT `id` FROM `comments` WHERE `user_id` = ? AND `image_id` = ?',
            [$userId, $imageId]
        );

        return isset($result['id']) ? $result['id'] : $result;
    }

    // Get user login that commented photo
    public function getLoginByComment($commentId) {
        $result = Db::queryOne(
            'SELECT users.login AS `login` FROM `comments`
            JOIN `users` ON comments.user_id = users.id WHERE comments.id = ?',
            [$commentId]
        );

        return isset($result['login']) ? $result['login'] : $result;
    }

    // Get date of comment
    public function getCreatedDateOfComment($commentId) {
        $result = Db::queryOne(
            'SELECT `created_at` FROM `comments` WHERE `id` = ?',
            [$commentId]
        );

        return isset($result['created_at']) ? $result['created_at'] : $result;
    }

    // Delete comment by id
    public function deleteComment($commentId) {
        $result = Db::query('DELETE FROM `comments` WHERE `id` = ?', [$commentId]);

        return $result;
    }
}
