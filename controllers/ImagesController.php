<?php
class ImagesController extends Controller {
    private $userModel;
    private $imageModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
    }

    public function gallery($sort = '') {
        $images = $this->imageModel->getImages();
        if ($this->isAjaxRequest()) {
            if ($sort == 'newest') {
                echo json_encode($images);
                // $this->renderView('images/gallery', ['images' => $images]);
            } else {
                echo json_encode(null);
                // $this->renderView('images/gallery', ['images' => null]);
            }
        } else {
            $this->renderView('images/index');
        }
    }

    public function download() {
        if ($this->isAjaxRequest()) {
            // $i = json_decode($_POST['images']);
            // foreach($i as $a) {
            //     print_r($a);
            // }
            $this->renderView('images/gallery', ['images' => json_decode($_POST['images'], true)]);
        } else {
            $this->renderView('');
        }
    }

    public function add() {
        $this->renderView('images/add');
    }

    public function edit() {
        // $this->renderView('images/takeImage');
        echo "edit image";
    }

    public function delete() {
        // $this->renderView('images/takeImage');
        echo "delete image";
    }

    public function show() {
        $this->renderView('images/show');
    }
}
