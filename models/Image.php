<?php
class Image {

    // Get images
    public function getImages() {
        $result = Db::queryAll('SELECT * FROM images');
        return $result;
    }

    public function createImage($userId, $imagePath) {
        $result = Db::query('INSERT INTO `images`(`image_path`, `user_id`) VALUES (?, ?)', [$imagePath, $userId]);
        return $result;
    }
}
