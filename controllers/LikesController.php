<?php
class LikesController extends Controller {
    private $imageModel;
    private $likeModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->likeModel = $this->getModel('Like');
    }

    // Like or unlike image
    public function like($imageId = false) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();

        $json = [];
        $user = $this->getLoggedInUser();

        if (!$user) {
            $json['message'] = 'You should be logged in to like a photo';
        } else if (!$imageId || !$this->imageModel->isImageExists($imageId)) {
            $json['message'] = 'Image does not exists';
        } else {
            if ($this->likeModel->isImageLiked($user['id'], $imageId)) {
                $this->likeModel->unlikeImage($user['id'], $imageId);
                $json['success'] = 'unliked';
            } else {
                $this->likeModel->likeImage($user['id'], $imageId);
                $json['success'] = 'liked';
            }
            $json['likes_amount'] = $this->likeModel->getNumberOfLikesByImage($imageId);
        }

        echo json_encode($json);
    }
}
