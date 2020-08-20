<?php require 'views/include/header.php' ?>

<div class="container">
    <h2 class="text-center py-4">Make your photos</h2>

    <div class="row">
        <!-- camera screen -->
        <div class="col-md-7 p-0">
            <div class="row border">
                <div class="col-lg-10 px-0 embed-responsive embed-responsive-4by3" id="video_container">
                    <video class="embed-responsive-item" id="video" autoplay=true></video>
                </div>
                <!-- filters -->
                <div class="col-lg-2 filter_container px-0">
                    <div id="filters">
                        <input type="checkbox" onclick="toggleFilter(this.id)" id="filter_0" class='d-none' checked>
                        <label for="filter_0" class="border label-filter">Reset filters</label>
                        <?php if ($data) : ?>
                            <?php foreach ($data as $filter) : ?>
                                <input type="checkbox" onclick="toggleFilter(this.id)" id="filter_<?= $filter['id'] ?>" class='d-none' data-path="<?= URLROOT . $filter['path'] ?>">
                                <label for="filter_<?= $filter['id'] ?>" class="border label-filter"><?= $filter['name'] ?></label>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <!-- tags -->
            <div class="row py-3">
                <div class="col-md-4 text-center"><label>Tags to image:</label></div>
                <div class="col-md-8"><input class="w-100" id="tags" placeholder="Separated by space" maxlength="50"></div>
            </div>
            <!-- buttons -->
            <div class="row py-3">
                <div class="col pt-3 px-0 d-flex justify-content-center">
                    <button class="btn btn-success btn-block" id="video_stream">Stop video</button>
                </div>
                <div class="col pt-3 px-1 text-center">
                    <button class="btn btn-block btn-info" id="take_photo" disabled><img src="<?= URLROOT ?>/assets/img/images/camera.png" alt="Take photo"></button>
                </div>
                <div class="col pt-3 px-0 d-flex justify-content-center">
                    <input id="upload_photo" type="file" class="d-none" accept="image/*">
                    <input id="upload" type="button" value="Upload photo" class="btn btn-success btn-block mb-0 justify-content-center d-flex wspace">
                </div>
            </div>
        </div>
        <!-- captured images -->
        <div class="text-center col-md-4 h-70 ml-auto minus-mb">
            <h4 class="pb-3" id="images_header">Preview (0)</h4>
            <div class="d-none" id="display_list">
                <div id="photo_list"></div>
                <br>
                <button class="btn btn-success" onclick="deletePreview()">Cancel</button>
                <button class="btn btn-success" onclick="saveImages()" id="save_images">Save</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= URLROOT; ?>/assets/js/camera.js"></script>
<script src="<?= URLROOT; ?>/assets/js/photomaker.js"></script>
<?php require 'views/include/footer.php' ?>