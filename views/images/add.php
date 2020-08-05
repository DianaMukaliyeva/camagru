<?php require 'views/include/header.php' ?>

<div class="container">
    <h2 class="text-center py-4">Make your photos</h2>

    <div class="row">
        <!-- camera screen -->
        <div class="col-md-8">
            <div class="row border">
                <div class="col-lg-10 px-0">
                    <div class="embed-responsive embed-responsive-4by3">
                        <video class="embed-responsive-item" id="video" autoplay=true></video>
                    </div>
                </div>
                <div class="col-lg-2 filter_container px-0">
                    <select name="filters[]" id="filters" multiple>
                        <option value="">No filters</option>
                        <?php if ($data) : ?>
                            <?php foreach ($data as $filter) : ?>
                                <option value="<?= URLROOT . $filter['path'] ?>"><?= $filter['name'] ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </div>
            </div>
            <div class="row pt-3">
                <div class="col-md-4 text-center"><label>Tags to image:</label></div>
                <div class="col-md-8"><input class="w-100" id="tags" placeholder="Separated by space"></div>
            </div>
            <div class="row">
                <div class="col pt-3 px-0 d-flex justify-content-center">
                    <button class="btn btn-success btn-block" id="video_stream">Stop video</button>
                </div>
                <div class="col pt-3 px-1 text-center">
                    <button class="btn btn-block btn-info" id="take_photo"><img src="<?= URLROOT ?>/assets/img/images/camera.png" alt="Take photo"></button>
                </div>
                <div class="col pt-3 px-0 d-flex justify-content-center">
                    <button class="btn btn-success btn-block" id="upload_photo">Upload photo</button>
                </div>
            </div>
        </div>
        <!-- show captured image -->
        <div class="text-center col-md-4 h-70 pt-5">
            <h4 class="py-3" id="images_header">Preview (0)</h4>
            <div id="display_list">
                <div id="photo_list"></div>
                <br>
                <button class="btn btn-success">Cancel</button>
                <button class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= URLROOT; ?>/assets/js/image.js"></script>
<?php require 'views/include/footer.php' ?>