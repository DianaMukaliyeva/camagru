<?php
class FollowersController extends Controller {
    private $userModel;
    private $followModel;

    public function __construct() {
        $this->userModel = $this->getModel('User');
        $this->followModel = $this->getModel('Follow');
    }

    // Follow or unfollow user
    public function follow($userIdToFollow = false) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();

        $json = [];
        $user = $this->getLoggedInUser();
        if (!$user) {
            $json['message'] = 'You should be logged in to follow an user';
        } else if ($userIdToFollow && $this->userModel->getLoginById($userIdToFollow)) {
            if ($this->followModel->isFollowing($user['id'], $userIdToFollow)) {
                $json['success'] = $this->followModel->unfollowUser(
                    $user['id'],
                    $userIdToFollow
                ) ? 'Follow' : 'db failed';
            } else {
                $json['success'] = $this->followModel->followUser(
                    $user['id'],
                    $userIdToFollow
                ) ? 'Unfollow' : 'db failed';
            }
            $json['followers_amount'] = $this->followModel->getFollowersAmount($userIdToFollow);
        } else {
            $json['message'] = 'User does not exists';
        }

        echo json_encode($json);
    }
}
