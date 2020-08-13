<?php
class Image {

    // Get all images ordered by date
    public function getImagesByDate() {
        $result = Db::queryAll('SELECT * FROM `images` ORDER BY `created_at` DESC');

        return $result;
    }

    // Get all images ordered by likes and comments
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

    // Get an image by Id
    public function getImageById($imageId) {
        $result = Db::queryAll('SELECT * FROM `images` WHERE `id` = ?', [$imageId]);

        return isset($result[0]) ? $result[0] : $result;
    }

    // Insert image into database
    public function createImage($userId, $imagePath) {
        $dataToInsert = [
            'user_id' => $userId,
            'image_path' => $imagePath
        ];
        $result = Db::insert('images', $dataToInsert);

        return $result;
    }

    // Delete image by id
    public function deleteImage($imageId) {
        $result = Db::query('DELETE FROM `images` WHERE `id` = ?', [$imageId]);

        return $result;
    }

    // Add comment to image
    public function addTag($imageId, $tag) {
        $dataToInsert = [
            'image_id' => $imageId,
            'tag' => $tag
        ];
        $result = Db::insert('tags', $dataToInsert);

        return $result;
    }

    // Get all tags of image
    public function getTagsbyImageId($imageId) {
        $result = Db::queryAll(
            'SELECT tag FROM `tags`
            LEFT JOIN `images` ON images.id = tags.image_id WHERE `image_id` = ?',
            [$imageId]
        );

        return $result;
    }
}
