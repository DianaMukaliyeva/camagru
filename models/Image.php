<?php
class Image {

    // Find user by email
    public function getImages() {
        $result = Db::queryAll('SELECT * FROM images');
        return $result;
    }

}
