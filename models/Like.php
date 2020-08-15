<?php
class Like {

    // Check if user liked this image
    public function isImageLiked($userId, $imageId) {
        $result = Db::queryOne(
            'SELECT `id` FROM `likes` WHERE `user_id` = ? AND `image_id` = ?',
            [$userId, $imageId]
        );

        return isset($result['id']) ? $result['id'] : $result;
    }

    // Add like to image
    public function likeImage($userId, $imageId) {
        $dataToInsert = [
            'user_id' => $userId,
            'image_id' => $imageId
        ];
        $result = Db::insert('likes', $dataToInsert);

        return $result;
    }

    // Remove like from image
    public function unlikeImage($userId, $imageId) {
        $result = Db::query(
            'DELETE FROM `likes` WHERE `user_id` = ? AND `image_id` = ?',
            [$userId, $imageId]
        );

        return $result;
    }

    // Get how many likes image has
    public function getNumberOfLikesByImage($imageId) {
        $result = Db::queryOne(
            'SELECT COUNT(`id`) FROM `likes` WHERE `image_id` = ?',
            [$imageId]
        );

        return $result['COUNT(*)'];
    }
}
