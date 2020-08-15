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
        } else if ($imageId && $this->imageModel->getImagesOwnerId($imageId)) {
            if ($this->likeModel->isImageLiked($user['id'], $imageId)) {
                $json['message'] = $this->likeModel->unlikeImage(
                    $user['id'],
                    $imageId
                ) ? false : 'db failed';
            } else {
                $json['message'] = $this->likeModel->likeImage(
                    $user['id'],
                    $imageId
                ) ? false : 'db failed';
            }
            $json['likes_amount'] = $this->likeModel->getNumberOfLikesByImage($imageId);
        } else {
            $json['message'] = 'Image does not exists';
        }

        echo json_encode($json);
    }
}
