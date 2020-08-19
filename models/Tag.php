<?php
class Tag {

    // Add tag to image
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

    // Search tags
    public function searchTags($search) {
        $search = '%' . $search . '%';
        $result = Db::queryAll(
            "SELECT * FROM `tags`
            WHERE `tag` LIKE ?",
            [$search]
        );

        return $result;
    }

    // Check if tag exists
    public function isExistTag($tag) {
        $result = Db::queryOne(
            "SELECT `tag` FROM `tags`
            WHERE `tag` LIKE ?",
            [$tag]
        );

        return isset($result['tag']) ? $result['tag'] : $result;
    }
}
