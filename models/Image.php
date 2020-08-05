<?php
class Image {

    // Get images
    public function getImages() {
        $result = Db::queryAll('SELECT * FROM images');
        return $result;
    }
}
