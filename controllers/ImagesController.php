<?php
class ImagesController extends Controller {
    private $userModel;
    private $imageModel;
    private $likeModel;
    private $commentModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
        $this->likeModel = $this->getModel('Like');
        $this->commentModel = $this->getModel('Comment');
    }

    // Main page of app show our gallery
    public function getImages(...$param) {
        $sort = !empty($param) ? $param[0] : '';
        $user = $this->getLoggedInUser();
        $userId = $user ? $user['id'] : 0;
        if ($this->isAjaxRequest()) {
            if ($sort == 'popular') {
                $images = $this->imageModel->getImagesByLikes($userId);
            } else {
                $images = $this->imageModel->getImagesByDate($userId);
            }
            echo json_encode($images);
        } else {
            $this->renderView('images/index');
        }
    }

    // Main page of app show our gallery
    public function userGallery($userId) {
        $this->isAjaxRequest();
        $user = $this->getLoggedInUser();
        $loggedUserId = $user ? $user['id'] : 0;
        $images = $this->imageModel->getImagesByUser($userId, $loggedUserId);
        echo json_encode($images);
    }

    // Render given images
    public function gallery(...$param) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();

        $images = json_decode($_POST['images'], true);
        foreach ($images as $key => $image) {
            $images[$key]['tags'] = $this->imageModel->getTagsbyImageId($image['id']);
            // $images[$key]['comments'] = $this->commentModel->getComments($image['id']);
        }

        $this->renderView('images/gallery', ['images' => $images]);
    }

    // Render given images
    public function loadUsersImage(...$param) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();

        $images = json_decode($_POST['images'], true);
        foreach ($images as $key => $image) {
            $images[$key]['tags'] = $this->imageModel->getTagsbyImageId($image['id']);
            // $images[$key]['comments'] = $this->commentModel->getComments($image['id']);
        }

        $this->renderView('images/account', ['images' => $images]);
    }

    // Delete image from db
    public function delete(...$param) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();
        $imageId = isset($param[0]) ? $param[0] : '0';

        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else if (!$imageId || !$image = $this->imageModel->isImageExists($imageId)) {
            $json['message'] = 'Image does not exists';
        } else if ($this->imageModel->deleteImage($imageId)) {
            $json['message'] = 'success';
            unlink(APPROOT . '/' . $image['image_path']);
        } else {
            $json['message'] = 'Something went wrong with database';
        }

        echo json_encode($json);
    }

    // Show image with comments
    public function imageInfo($imageId = 0) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();

        $loggedUserId = $user ? $user['id'] : 0;
        $json['message'] = 'here';
        $json = $this->imageModel->getImageFullInfo($imageId, $loggedUserId);
        if ($json) {
            $json['tags'] = $this->imageModel->getTagsbyImageId($imageId);
            $json['comments'] = $this->commentModel->getComments($imageId);
            $json['logged_in_user'] = $user ? $user['login'] : false;
            $json['logged_user_id'] = $user ? $user['id'] : 0;
        } else {
            $json['message'] = 'Image does not exists';
        }

        echo json_encode($json);
    }
}
