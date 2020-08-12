<?php
class Like {

    // Check if user liked this image
    public function isImageLiked($userId, $imageId) {
        $result = Db::queryOne('SELECT `id` FROM `likes` WHERE `user_id` = ? AND `image_id` = ?', [$userId, $imageId]);
        if (isset($result['id']))
            return $result['id'];
        return $result;
    }

    // Add like to image
    public function likeImage($userId, $imageId) {
        $result = Db::query('INSERT INTO `likes` (`user_id`, `image_id`) VALUES (?, ?)', [$userId, $imageId]);
        return $result;
    }

    // Remove like from image
    public function unlikeImage($userId, $imageId) {
        $result = Db::query('DELETE FROM `likes` WHERE `user_id` = ? AND `image_id` = ?', [$userId, $imageId]);
        return $result;
    }

    // Get how many likes image has
    public function getNumberOfLikesByImage($imageId) {
        $result = Db::queryOne('SELECT COUNT(*) FROM `likes` WHERE `image_id` = ?', [$imageId]);
        return $result['COUNT(*)'];
    }
}
