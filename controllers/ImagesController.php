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

    private function getImageInfo($imageId, $user, $data = []) {
        $data['user_liked'] =
            $user ? $this->likeModel->isImageLiked($user['id'], $imageId) : false;
        $data['tags'] = $this->imageModel->getTagsbyImageId($imageId);
        $data['comments'] = $this->commentModel->getComments($imageId);
        $data['likes_amount'] = $this->likeModel->getNumberOfLikesByImage($imageId);

        return $data;
    }

    // Main page of app show our gallery
    public function gallery(...$param) {
        $sort = !empty($param) ? $param[0] : '';

        if ($this->isAjaxRequest()) {
            if ($sort == 'popular') {
                $images = $this->imageModel->getImagesByLikes();
            } else {
                $images = $this->imageModel->getImagesByDate();
            }
            echo json_encode($images);
        } else {
            $this->renderView('images/index');
        }
    }

    // Render given images
    public function download(...$param) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();

        $images = json_decode($_POST['images'], true);
        foreach ($images as $key => $image) {
            $images[$key] = $this->getImageInfo($image['id'], $user, $images[$key]);
            $images[$key]['user_login'] = $this->userModel->getLoginById($image['user_id']);
            $images[$key]['comments_amount'] =
                $this->commentModel->getNumberOfComments($image['id']);
            $images[$key]['user_commented'] = $user ?
                $this->commentModel->isCommented($user['id'], $image['id']) : false;
        }

        $this->renderView('images/gallery', ['images' => $images]);
    }

    // Delete image from db
    public function delete(...$param) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();
        $imageId = isset($param[0]) ? $param[0] : '0';

        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else if (!$imageId || !$this->imageModel->getImageById($imageId)) {
            $json['message'] = 'Image does not exists';
        } else if ($this->imageModel->deleteImage($imageId)) {
            $json['message'] = 'success';
        } else {
            $json['message'] = 'Something went wrong with database';
        }

        echo json_encode($json);
    }

    // Show image with comments
    public function imageInfo($imageId = false) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = $this->getLoggedInUser();

        if ($imageId && $json = $this->imageModel->getImageById($imageId)) {
            $json = $this->getImageInfo($imageId, $user, $json);
            $image_user = $this->userModel->findUser(['id' => $json['user_id']]);
            $json['user_login'] = $image_user['login'];
            $json['profile_photo'] = $image_user['picture'] ?
                $image_user['picture'] : 'assets/img/images/default.png';
            $json['logged_in_user'] = $user ? $user['login'] : false;
            $json['logged_user_id'] = $user ? $user['id'] : 0;
        } else {
            $json['message'] = 'Image does not exists';
        }

        echo json_encode($json);
    }
}
