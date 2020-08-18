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
        } else if ($userIdToFollow == $user['id']) {
            $json['message'] = 'You can\'t follow yourself';
        } else if ($userIdToFollow && $this->userModel->getLoginById($userIdToFollow)) {
            if ($this->followModel->isFollowing($user['id'], $userIdToFollow)) {
                $this->followModel->unfollowUser($user['id'], $userIdToFollow);
                $json['success'] = 'Follow';
            } else {
                $this->followModel->followUser($user['id'], $userIdToFollow);
                $json['success'] =  'Unfollow';
            }
            $json['followers_amount'] = $this->followModel->getFollowersAmount($userIdToFollow);
        } else {
            $json['message'] = 'User does not exists';
        }

        echo json_encode($json);
    }

    // All users that follow and followed by userId
    public function getFollow($userId) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();

        $followers = $this->followModel->getUserFollowers($userId);
        $followed = $this->followModel->getUserFollowed($userId);
        foreach ($followers as $key => $user) {
            $followers[$key] = $this->userModel->getUserInfo($user['user_id_follower']);
        }
        foreach ($followed as $key => $user) {
            $followed[$key] = $this->userModel->getUserInfo($user['user_id_followed']);
        }
        $json['followers'] = $followers;
        $json['followed'] = $followed;

        echo json_encode($json);
    }
}
