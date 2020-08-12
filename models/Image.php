<?php
class Image {

    // Get all images ordered by date
    public function getImagesByDate() {
        $result = Db::queryAll('SELECT * FROM `images` ORDER BY `created_at`');
        return $result;
    }

    // Get all images ordered by likes
    public function getImagesByLikes() {
        $result = Db::queryAll(
            'SELECT images.id, images.image_path, images.created_at, images.user_id,
            COUNT(likes.id) AS `likes_amount`
            FROM images LEFT JOIN likes ON likes.image_id = images.id
            GROUP BY images.id ORDER BY `likes_amount` DESC'
        );
        return $result;
    }

    // Get image by Id
    public function getImageById($imageId) {
        $result = Db::queryAll('SELECT * FROM `images` WHERE `id` = ?', [$imageId]);
        if ($result[0])
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

    // Get all comments of image
    public function getComments($imageId) {
        $result = Db::queryAll('SELECT * FROM `comments` WHERE `image_id` = ?', [$imageId]);
        return $result;
    }

    // Add comment to image
    public function addComment($userId, $imageId, $comment) {
        $result = Db::query('INSERT INTO `comments` (`user_id`, `image_id`, `comment`) VALUES (?, ?, ?)', [$userId, $imageId, $comment]);
        return $result;
    }
}
