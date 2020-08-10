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
                <div class="col-lg-2 filter_container px-0">
                    <select name="filters[]" id="filters" multiple>
                        <option value="" selected>No filters</option>
                        <?php if ($data) : ?>
                            <?php foreach ($data as $filter) : ?>
                                <option value="<?= URLROOT . $filter['path'] ?>"><?= $filter['name'] ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </div>
            </div>
            <div class="row py-3">
                <div class="col-md-4 text-center"><label>Tags to image:</label></div>
                <div class="col-md-8"><input class="w-100" id="tags" placeholder="Separated by space"></div>
            </div>
            <div class="row py-3">
                <div class="col pt-3 px-0 d-flex justify-content-center">
                    <button class="btn btn-success btn-block" id="video_stream">Stop video</button>
                </div>
                <div class="col pt-3 px-1 text-center">
                    <button class="btn btn-block btn-info" id="take_photo"><img src="<?= URLROOT ?>/assets/img/images/camera.png" alt="Take photo"></button>
                </div>
                <div class="col pt-3 px-0 d-flex justify-content-center">
                    <input id="upload_photo" type="file" class="d-none" accept="image/*">
                    <input id="upload" type="button" value="Upload photo" class="btn btn-success btn-block mb-0 justify-content-center d-flex">
                </div>
            </div>
        </div>
        <!-- show captured image -->
        <div class="text-center col-md-4 h-70 ml-auto">
            <h4 class="pb-3" id="images_header">Preview (0)</h4>
            <div id="display_list">
                <div id="photo_list"></div>
                <br>
                <button class="btn btn-success" id="delete_images">Cancel</button>
                <button class="btn btn-success" id="save_images">Save</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= URLROOT; ?>/assets/js/image.js"></script>
<?php require 'views/include/footer.php' ?>