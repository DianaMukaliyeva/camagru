<?php
class CameraController extends Controller {
    private $imageModel;
    private $filterModel;
    private $tagModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->filterModel = $this->getModel('Filter');
        $this->tagModel = $this->getModel('Tag');
    }

    // Render view of page with camera
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
        $user = $this->getLoggedInUser();

        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else if (isset($_POST['data']) && $data = json_decode($_POST['data'], true)) {
            $path = 'assets/img/user_' . $user['id'];

            // create folder for user if it does not exist
            if (!file_exists(APPROOT . '/' . $path))
                mkdir(APPROOT . '/' . $path);

            // go through each image to save
            foreach ($data as $key => $image) {
                if (substr($image['src'], 0, 22) === "data:image/png;base64,") {
                    $filename = $path . '/' . md5(uniqid()) . '.png';
                    $image['src'] = str_replace('data:image/png;base64,', '', $image['src']);
                    $image['src'] = str_replace(' ', '+', $image['src']);
                    file_put_contents(APPROOT . '/' . $filename, base64_decode($image['src']));
                    $this->imageModel->createImage($user['id'], $filename);
                    $imageId = Db::getLastId();
                    $json['tags'] = array_filter(explode('#', $image['tags']));
                    foreach ($json['tags'] as $tag) {
                        $this->tagModel->addTag($imageId, $tag);
                    }
                } else {
                    $json['message'] = "You can't upload something else than images";
                }
            }
        } else {
            $json['message'] = 'No data is provided';
        }

        echo json_encode($json);
    }

    // Combine image and filters from frontend
    public function combine() {
        // Only works for ajax requests
        $this->onlyAjaxRequests();

        // Check if user is logged in
        $user = $this->getLoggedInUser();

        if (!$user) {
            $json['message'] = 'You should be logged in';
        } else if (isset($_POST['data']) && $data = json_decode($_POST['data'], true)) {
            $img_data = str_replace('data:image/png;base64,', '', $data['img_data']);
            $img_data = str_replace(' ', '+', $img_data);
            $img_data = base64_decode($img_data);
            $dest = imagecreatefromstring($img_data);
            imagealphablending($dest, true);
            imagesavealpha($dest, true);
            foreach ($data['filters'] as $filter) {
                $src = imagecreatefrompng($filter);
                imagecopyresized(
                    $dest,
                    $src,
                    0,
                    0,
                    0,
                    0,
                    $data['width'],
                    $data['height'],
                    imagesx($src),
                    imagesy($src)
                );
                imagedestroy($src);
            }
            ob_start();
            imagepng($dest);
            $json['photo'] = 'data:image/png;base64,' . base64_encode(ob_get_contents());
            ob_end_clean();
            imagedestroy($dest);
            // delete all # characters before tags
            $data['tags'] = str_replace('#', '', $data['tags']);
            if (strlen($data['tags']) > 50) {
                $json['message'] = 'All tags should be less tha 50 characters';
            }
            $data['tags'] = filter_var($data['tags'], FILTER_SANITIZE_STRING);
            $json['tags'] = array_filter(explode(' ', $data['tags']));
            foreach ($json['tags'] as $tag) {
                if (strlen($tag) > 45) {
                    $json['message'] = 'One tag should be less than 15 characters';
                }
            }
        } else {
            $json['message'] = 'No data is provided';
        }

        echo json_encode($json);
    }
}
