<?php
class ImagesController extends Controller {
    private $userModel;
    private $imageModel;
    private $filterModel;
    private $likeModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
        $this->filterModel = $this->getModel('Filter');
        $this->likeModel = $this->getModel('Like');
    }

    // Main page of app show our gallery
    public function gallery(...$param) {
        $sort = !empty($param) ? $param[0] : '';
        if ($this->isAjaxRequest()) {
            if ($sort == 'popular') {
                $images = $this->imageModel->getImagesByLikes();
                echo json_encode($images);
            } else {
                $images = $this->imageModel->getImagesByDate();
                echo json_encode($images);
            }
        } else {
            $this->renderView('images/index');
        }
    }

    public function takePhoto(...$param) {
        // Check if user is logged in
        $this->checkUserSession();
        $filters = $this->filterModel->getFilters();
        $this->renderView('images/photomaker', $filters);
    }

    // Save images in folders and in db
    public function saveImages() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        // Check if user is logged in
        $user = $this->checkUserSession();

        if (isset($_POST['data'])) {
            $json = [];
            $path = 'assets/img/users/' . $user['login'];
            $data = json_decode($_POST['data'], true);
            // create folder for user if it does not exists
            if (!file_exists(APPROOT . '/' . $path))
                mkdir(APPROOT . '/' . $path);

            foreach ($data as $key => $image) {
                if (substr($image['src'], 0, 22) === "data:image/png;base64,") {
                    $filename = $path . '/' . md5(uniqid()) . '.png';
                    $file = APPROOT . '/' . $filename;
                    $image['src'] = str_replace('data:image/png;base64,', '', $image['src']);
                    $image['src'] = str_replace(' ', '+', $image['src']);
                    file_put_contents($file, base64_decode($image['src']));
                    $this->imageModel->createImage($user['id'], $filename);
                } else {
                    $json['message'] = "You can't upload something else than images";
                    echo json_encode($json);
                    exit();
                }
                $tags = $image['tags'];
                $json['tags'] = $tags;
            }
            echo json_encode($json);
        }
    }

    // Render given images
    public function download(...$param) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        $user = isset($_SESSION[APPNAME]['user']) ? $_SESSION[APPNAME]['user'] : null;
        $images = json_decode($_POST['images'], true);
        foreach ($images as $key => $image) {
            $images[$key]['user_login'] = $this->userModel->getLoginById($image['user_id']);
            $images[$key]['user_liked'] = $user ? $this->likeModel->isImageLiked($user['id'], $image['id']) : false;
            $images[$key]['likes_amount'] = $this->likeModel->getNumberOfLikesByImage($image['id']);
        }
        $this->renderView('images/gallery', ['images' => $images]);
    }

    // Combine image and filters from frontend
    public function combine() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();
        // Check if user is logged in
        $this->checkUserSession();
        if (isset($_POST['data'])) {
            $data = json_decode($_POST['data'], true);
            $img_data = str_replace('data:image/png;base64,', '', $data['img_data']);
            $img_data = str_replace(' ', '+', $img_data);
            $img_data = base64_decode($img_data);
            $dest = imagecreatefromstring($img_data);
            imagealphablending($dest, true);
            imagesavealpha($dest, true);
            foreach ($data['filters'] as $filter) {
                $src = imagecreatefrompng($filter);
                imagecopyresized($dest, $src, 0, 0, 0, 0, $data['width'], $data['height'], imagesx($src), imagesy($src));
                imagedestroy($src);
            }
            ob_start();
            imagepng($dest);
            $final_image_data = ob_get_contents();
            ob_end_clean();
            $final_image_data_base_64 = base64_encode($final_image_data);
            $json['photo'] = 'data:image/png;base64,' . $final_image_data_base_64;
            imagedestroy($dest);
            // delete all # characters before
            $data['tags'] = str_replace('#', '', $data['tags']);
            $json['tags'] = array_filter(explode(' ', $data['tags']));
        }
        echo json_encode($json);
    }

    // Edit image
    public function edit(...$param) {
        $this->onlyAjaxRequests();
        echo "edit image";
    }

    // Delete image from db
    public function delete(...$param) {
        $this->onlyAjaxRequests();
        echo "delete image";
    }

    // Show an image with comments
    public function imageInfo($imageId = false) {
        // Only works for ajax requests
        $this->onlyAjaxRequests();

        $json = [];
        $user = isset($_SESSION[APPNAME]['user']) ? $_SESSION[APPNAME]['user'] : null;
        if ($imageId && $json = $this->imageModel->getImageById($imageId)) {
            // $json['tags'] = $this->likeModel->unlikeImage($user['id'], $imageId) ? 'unliked' : 'db failed';
            // $json['comments'] = $this->likeModel->likeImage($user['id'], $imageId) ? 'liked' : 'db failed';
            $json['likes_amount'] = $this->likeModel->getNumberOfLikesByImage($imageId);
            $json['message'] = $user && $this->likeModel->isImageLiked($user['id'], $imageId) ? 'liked' : 'unliked';
            $image_user = $this->userModel->getUserById($json['user_id']);
            $json['user_login'] = $image_user['login'];
            $json['profile_photo'] = $image_user['picture'] ? $image_user['picture'] : 'assets/img/images/default.png';
        } else {
            $json['message'] = 'Image does not exists';
        }
        echo json_encode($json);
    }
}
