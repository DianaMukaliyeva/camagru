<?php
class Image {

    // Get all images ordered by date
    public function getImagesByDate($userId = 0) {
        $result = Db::queryAll(
            'SELECT images.id, images.image_path, images.created_at,
                images.user_id, users.login, users.picture,
                (SELECT COUNT(id) FROM `likes` where likes.user_id = ? AND likes.image_id = images.id) AS user_liked,
                (SELECT COUNT(id) FROM `comments` where comments.user_id = ? AND comments.image_id = images.id) AS user_commented,
                COUNT(DISTINCT(likes.id)) AS `likes_amount`,
                COUNT(DISTINCT(comments.id)) AS `comments_amount`
                FROM `images`
                LEFT JOIN `users` ON users.id = images.user_id
                LEFT JOIN `likes` ON likes.image_id = images.id
                LEFT JOIN `comments` ON comments.image_id = images.id
                GROUP BY images.id
                ORDER BY `created_at` DESC',
            [$userId, $userId]
        );

        return $result;
    }

    // Get all images ordered by likes and comments
    public function getImagesByLikes($userId = 0) {
        $result = Db::queryAll(
            'SELECT images.id, images.image_path, images.created_at,
                images.user_id, users.login, users.picture,
                (SELECT COUNT(id) FROM `likes` where likes.user_id = ? AND image_id = images.id) AS user_liked,
                (SELECT COUNT(id) FROM `comments` where comments.user_id = ? AND image_id = images.id) AS user_commented,
                COUNT(DISTINCT(likes.id)) AS `likes_amount`,
                COUNT(DISTINCT(comments.id)) AS `comments_amount`
                FROM `images`
                LEFT JOIN `users` ON users.id = images.user_id
                LEFT JOIN `likes` ON likes.image_id = images.id
                LEFT JOIN `comments` ON comments.image_id = images.id
                GROUP BY images.id
                ORDER BY `likes_amount` DESC, `comments_amount` DESC',
            [$userId, $userId]
        );

        return $result;
    }

    // Check if image exists
    public function getImagesOwnerId($imageId) {
        $result = Db::queryOne('SELECT `user_id` FROM `images` WHERE id = ?', [$imageId]);

        return $result;
    }

    // Get an image by Id
    public function getImageFullInfo($imageId, $loggedUserId) {
        $result = Db::queryAll(
            'SELECT images.id, images.image_path, images.created_at,
            images.user_id, users.login , users.picture,
            COUNT(DISTINCT(likes.id)) AS like_amount,
            (SELECT COUNT(`id`) FROM `likes` where `user_id` = ? AND `image_id` = images.id) AS user_liked,
            (SELECT COUNT(`id`) FROM `followers`
            WHERE `user_id_followed` = images.user_id AND `user_id_follower` = ?) AS user_follow,
            COUNT(DISTINCT(comments.id)) AS comment_amount
            FROM `images`
            LEFT JOIN `likes` ON images.id = likes.image_id
            LEFT JOIN `users` ON images.user_id = users.id
            LEFT JOIN `comments` ON images.id = comments.image_id
            WHERE images.id = ?',
            [$loggedUserId, $loggedUserId, $imageId]
        );

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
            'tag' => filter_var($tag, FILTER_SANITIZE_STRING)
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
