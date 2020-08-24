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
        } else {
            $data = json_decode($_POST['data'], true);
            $json['comment'] = htmlspecialchars($data['comment']);

            if (strlen($json['comment']) < 1) {
                $json['message'] = 'Comment is empty';
            } else if (strlen($json['comment']) > 255) {
                $json['message'] = 'Comment is too long';
            } else if (!$this->imageModel->isImageExists($data['image_id'])) {
                $json['message'] = 'Image does not exists';
            } else {
                $this->commentModel->addComment($user['id'], $data['image_id'], $json['comment']);
                $commentId = Db::getLastId();
                $json['created_at'] = $this->commentModel->getCreatedDateOfComment($commentId);
                $json['login'] = $user['login'];
                $json['comments'] = $this->commentModel->getComments($data['image_id']);
                $json['logged_user_id'] = $user['id'];
            }
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
            if ($image = $this->imageModel->isImageExists($data['image_id'])) {
                $imageOwner = $this->userModel->findUser(['id' => $image['user_id']]);
                $message = "<p>" . $user['login'] . " recently commented your photo:</p>";
                $message .= "<p>\"<q>" . $data['comment'] . "</q>\"</p>";
                if (!$this->sendEmail($imageOwner['email'], $imageOwner['login'], $message)) {
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
        } else if ($data[2] != $user['id']) {
            $json['message'] = 'You can not delete another user\'s comment';
        } else if ($this->commentModel->deleteComment($data[0])) {
            $json['comments'] = $this->commentModel->getComments($data[1]);
            $json['logged_user_id'] = $user['id'];
            $json['user_commented'] = $this->commentModel->isCommented($data[2], $data[1]);
        } else {
            $json['message'] = 'Could not delete this comment from database';
        }

        echo json_encode($json);
    }
}
