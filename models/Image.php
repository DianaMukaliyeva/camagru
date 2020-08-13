<?php
class Image {

    // Get all images ordered by date
    public function getImagesByDate() {
        $result = Db::queryAll('SELECT * FROM `images` ORDER BY `created_at` DESC');
        return $result;
    }

    // Get all images ordered by likes
    public function getImagesByLikes() {
        $result = Db::queryAll(
            'SELECT images.id, images.image_path, images.created_at, images.user_id,
            COUNT(likes.id) AS `likes_amount`, COUNT(comments.id) AS `comments_amount`
            FROM `images` LEFT JOIN `likes` ON likes.image_id = images.id
            LEFT JOIN `comments` ON comments.image_id = images.id
            GROUP BY images.id ORDER BY `likes_amount` DESC, `comments_amount` DESC'
        );
        return $result;
    }

    // Get image by Id
    public function getImageById($imageId) {
        $result = Db::queryAll('SELECT * FROM `images` WHERE `id` = ?', [$imageId]);
        if (isset($result[0]))
            return $result[0];
        return $result;
    }

    // Insert image into database
    public function createImage($userId, $imagePath) {
        $result = Db::query(
            'INSERT INTO `images`(`image_path`, `user_id`) VALUES (?, ?)',
            [$imagePath, $userId]
        );
        return $result;
    }

    // Delete image by id
    public function deleteImage($imageId) {
        $result = Db::query('DELETE FROM `images` WHERE `id` = ?', [$imageId]);
        return $result;
    }

    // Get how many comments image has
    public function getNumberOfComments($imageId) {
        $result = Db::queryOne('SELECT COUNT(*) FROM `comments` WHERE `image_id` = ?', [$imageId]);
        return $result['COUNT(*)'];
    }

    // Check if user liked this image
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

    // Add comment to image
    public function addTag($imageId, $tag) {
        $result = Db::query('INSERT INTO `tags` (`image_id`, `title`) VALUES (?, ?)', [$imageId, $tag]);
        return $result;
    }

    // Get all tags of image
    public function getTagsbyImageId($imageId) {
        $result = Db::queryAll(
            'SELECT tags.title AS `tag` FROM `tags` LEFT JOIN `images` ON images.id = tags.image_id WHERE `image_id` = ?',
            [$imageId]
        );
        return $result;
    }
}
