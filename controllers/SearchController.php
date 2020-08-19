<?php
class SearchController extends Controller {
    private $imageModel;
    private $userModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
    }

    // Like or unlike image
    public function users() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();

        $search = $_POST['data'];
        $json['users'] = $this->userModel->searchUsers($search);

        if (!$json['users'])
            $json['message'] = 'No results';
        echo json_encode($json);
    }

    // Like or unlike image
    public function tags() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();

        $search = $_POST['data'];
        $json['tags'] = [];
        if ($search != '') {
            $json['tags'] = $this->imageModel->searchTags($search);
        }

        if (!$json['tags'] && $search != '' )
            $json['message'] = 'No results';
        echo json_encode($json);
    }
}
