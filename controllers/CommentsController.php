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
            $json['comment'] = filter_var($data['comment'], FILTER_SANITIZE_STRING);
            if (strlen($json['comment']) > 255) {
                $json['message'] = 'Comment is too long';
            } else if ($this->imageModel->getImagesOwnerId($data['image_id'])) {
                $json['success'] = $this->commentModel->addComment(
                    $user['id'],
                    $data['image_id'],
                    $json['comment']
                );
                $commentId = Db::getLastId();
                $json['created_at'] = $this->commentModel->getCreatedDateOfComment($commentId);
                $json['login'] = $user['login'];
                $json['comments'] =
                    $this->commentModel->getComments($data['image_id']);
                $json['logged_user_id'] = $user['id'];
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
            if ($image = $this->imageModel->getImagesOwnerId($data['image_id'])) {
                $imageOwner = $this->userModel->findUser(['id' => $image['user_id']]);
                if ($imageOwner['notify'] && $imageOwner['login'] != $user['login']) {
                    $message = "<p>" . $user['login'] . " recently commented your photo</p>";
                    $message .= "<p><q>" . $data['comment'] . "</q></p>";
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

    // Delete comment from image $data="commentId?imageId?userId"
    public function delete($data) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $data = array_filter(explode('?', $data));
        $user = $this->getLoggedInUser();

        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else if (!$this->commentModel->isCommentExists($data[0])) {
            $json['message'] = 'Comment does not exists';
        } else if ($data[2] == $user['id']) {
            $json['message'] = 'You can not delete another\'s comment';
        } else if ($this->commentModel->deleteComment($data[0])) {
            $json['message'] = 'success';
            $json['comments'] = $this->commentModel->getComments($data[1]);
            $json['logged_user_id'] = $user['id'];
            $json['user_commented'] = $this->commentModel->isCommented($data[2], $data[1]);
        } else {
            $json['message'] = 'Something went wrong with database';
        }

        echo json_encode($json);
    }
}
