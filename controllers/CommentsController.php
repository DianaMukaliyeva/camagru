<?php
class CommentsController extends Controller {
    private $imageModel;
    private $userModel;
    private $commentModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
        $this->commentModel = $this->getModel('Comment');
    }

    // Create comment
    public function addComment() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $json = [];
        $user = $this->getLoggedInUser();

        if (!$user) {
            $json['message'] = 'You should be logged in to comment a photo';
        } else if (isset($_POST['data'])) {
            $data = json_decode($_POST['data'], true);
            if ($this->imageModel->getImageById($data['image_id'])) {
                $json['success'] = $this->commentModel->addComment(
                    $user['id'],
                    $data['image_id'],
                    filter_var($data['comment'], FILTER_SANITIZE_STRING)
                );
                $commentId = Db::getLastId();
                $json['created_at'] = $this->commentModel->getCreatedDateOfComment($commentId);
                $json['login'] = $user['login'];
                $json['comment'] = filter_var($data['comment'], FILTER_SANITIZE_STRING);
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

    // Send email about commenting photo
    public function sendCommentEmail() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $json = [];
        $user = $this->getLoggedInUser();

        if ($user && isset($_POST['data'])) {
            $data = json_decode($_POST['data'], true);
            if ($image = $this->imageModel->getImageById($data['image_id'])) {
                $imageOwner = $this->userModel->findUser(['id' => $image['user_id']]);
                if ($imageOwner['notify'] && $imageOwner['login'] != $user['login']) {
                    $message = "<p>" . $user['login'] . " recently commented your post</p>";
                    if (!$this->sendEmail($imageOwner['email'], $imageOwner['login'], $message))
                        $json['message'] = 'Could not sent an email';
                }
            } else {
                $json['message'] = 'Image does not exists';
            }
        } else {
            $json['message'] = 'Image does not exists';
        }

        echo json_encode($json);
    }
}
