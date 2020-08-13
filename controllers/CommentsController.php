<?php
class CommentsController extends Controller {
    private $imageModel;
    private $commentModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->commentModel = $this->getModel('Comment');
    }

    // Create comment
    public function addComment() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $json = [];
        $user = $this->getLoggedInUser();

        if (!$user) {
            $json['message'] = 'You should be logged in to like a photo';
        } else if (isset($_POST['data'])) {
            $data = json_decode($_POST['data'], true);
            if ($this->imageModel->getImageById($data['image_id'])) {
                $json['success'] = $this->commentModel->addComment(
                    $user['id'],
                    $data['image_id'],
                    $data['comment']
                );
                $commentId = Db::getLastId();
                $json['created_at'] = $this->commentModel->getCreatedDateOfComment($commentId);
                $json['login'] = $user['login'];
                $json['comment'] = $data['comment'];
                $json['comments_amount'] =
                    $this->commentModel->getNumberOfComments($data['image_id']);
            } else {
                $json['message'] = 'Image does not exists';
            }
        } else {
            $json['message'] = 'Image does not exists';
        }

        echo json_encode($json);
    }
}
