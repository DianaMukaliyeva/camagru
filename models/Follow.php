<?php
class Follow {

    // Check if user following another
    public function isFollowing($userId, $userIdToFollow) {
        $result = Db::query(
            'SELECT `id` FROM `followers` WHERE `user_id_followed` = ? AND `user_id_follower` = ?',
            [$userIdToFollow, $userId]
        );

        return $result;
    }

    // Add user to followers to another
    public function followUser($userId, $userIdToFollow) {
        $dataToInsert = [
            'user_id_follower' => $userId,
            'user_id_followed' => $userIdToFollow
        ];
        $result = Db::insert('followers', $dataToInsert);

        return $result;
    }

    // Remove user from followers of another users
    public function unfollowUser($userId, $userIdToFollow) {
        $result = Db::query(
            'DELETE FROM `followers` WHERE `user_id_followed` = ? AND `user_id_follower` = ?',
            [$userIdToFollow, $userId]
        );

        return $result;
    }

    // Get how many followers user has
    public function getFollowersAmount($userIdToFollow) {
        $result = Db::queryOne(
            'SELECT COUNT(`id`) FROM `followers` WHERE `user_id_followed` = ?',
            [$userIdToFollow]
        );

        return $result['COUNT(`id`)'];
    }
}
