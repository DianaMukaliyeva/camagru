<?php
class ImagesController extends Controller {
    private $userModel;
    private $imageModel;
    private $filterModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
        $this->filterModel = $this->getModel('Filter');
    }

    public function gallery(...$param) {
        if (!empty($param)) {
            $sort = $param[0];
        } else {
            $sort = '';
        }
        $images = $this->imageModel->getImages();
        if ($this->isAjaxRequest()) {
            if ($sort == 'newest') {
                echo json_encode($images);
                // $this->renderView('images/gallery', ['images' => $images]);
            } else {
                echo json_encode($images);
                // $this->renderView('images/gallery', ['images' => null]);
            }
        } else {
            $this->renderView('images/index');
        }
    }

    public function download(...$param) {
        if ($this->isAjaxRequest()) {
            $this->renderView('images/gallery', ['images' => json_decode($_POST['images'], true)]);
        } else {
            $this->renderView('');
        }
    }

    public function add(...$param) {
        if (!isset($_SESSION['user'])) {
            $this->redirect('');
        }
        if ($this->isAjaxRequest()) {
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
                ob_start ();
                imagepng($dest);
                $final_image_data = ob_get_contents();
                ob_end_clean ();
                $final_image_data_base_64 = base64_encode($final_image_data);
                $json['photo'] = 'data:image/png;base64,' . $final_image_data_base_64;
                imagedestroy($dest);
                $json['valid'] = true;
                $json['message'] = "Image added to the list";
                $json['description'] = "description";
            }
            echo json_encode($json);
        } else {
            $filters = $this->filterModel->getFilters();
            $this->renderView('images/add', $filters);
        }
    }

    public function edit(...$param) {
        // $this->renderView('images/takeImage');
        echo "edit image";
    }

    public function delete(...$param) {
        // $this->renderView('images/takeImage');
        echo "delete image";
    }

    public function show(...$param) {
        $this->renderView('images/show');
    }
}
