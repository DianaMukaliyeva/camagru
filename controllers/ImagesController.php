<?php
class ImagesController extends Controller {
    private $userModel;
    private $imageModel;

    public function __construct() {
        $this->imageModel = $this->getModel('Image');
        $this->userModel = $this->getModel('User');
    }

    public function gallery($sort = '') {
        // $images = $this->imageModel->getImages();
        if ($this->isAjaxRequest()) {
            if ($sort == 'newest') {
                $this->renderView('images/gallery', ['images' => 'newest here']);
            } else {
                $this->renderView('images/gallery', ['images' => 'popular there']);
            }
        } else {
            $this->renderView('images/index');
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
