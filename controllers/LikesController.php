<?php
class LikesController extends Controller {
    private $userModel;
    private $imageModel;
    private $filterModel;
    private $likeModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
        $this->filterModel = $this->getModel('Filter');
        $this->likeModel = $this->getModel('Like');
    }

    public function like($imageId = false) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $json = [];
        $user = isset($_SESSION[APPNAME]['user']) ? $_SESSION[APPNAME]['user'] : false;
        if (!$user) {
            $json['message'] = 'You should be logged in to like a photo';
        } else if ($imageId && $this->imageModel->getImageById($imageId)) {
            if ($this->likeModel->isImageLiked($user['id'], $imageId)) {
                $json['message'] = $this->likeModel->unlikeImage($user['id'], $imageId) ? 'unliked' : 'db failed';
            } else {
                $json['message'] = $this->likeModel->likeImage($user['id'], $imageId) ? 'liked' : 'db failed';
            }
            $json['likes_amount'] = $this->likeModel->getNumberOfLikesByImage($imageId);
        } else {
            $json['message'] = 'Image does not exists';
        }
        echo json_encode($json);
    }
}
