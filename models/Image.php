<?php
class Image {

    // Get all images
    public function getImagesByDate() {
        $result = Db::queryAll('SELECT * FROM `images` ORDER BY `created_at`');
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
}
