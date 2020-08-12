<?php require 'views/include/header.php' ?>

<div class="mt-3 input-group justify-content-center">
    <div id="radioBtn" class="btn-group">
        <a class="sort_images btn btn-light active" data-toggle="sort_image" data-title="newest">Newest</a>
        <a class="sort_images btn btn-light" data-toggle="sort_image" data-title="popular">Popular</a>
    </div>
    <input type="hidden" name="sort_image" id="sort_image">
</div>

<!-- gallery -->
<div class="container article-list px-0 px-sm-4" id="article-list"></div>

<!-- modal window for image -->
<div class="modal fade" id="exampleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header p-0">
                <div class="media mt-2">
                    <img class="rounded-circle media-img mx-3" id="modal_profile_photo" src="<?= URLROOT . '/assets/img/images/default.png' ?>" alt="profile image">
                    <div class="media-body">
                        <a class="text-decoration-none" href="#">
                            <p class="pt-3 font-weight-bold" id="modal_profile_login">login</p>
                        </a>
                    </div>
                </div>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="row mb-3">
                    <div class="col-md-6 border py-2">
                        <div class="row">
                            <div class="col embed-responsive embed-responsive-4by3">
                                <img src="<?= URLROOT . '/assets/img/images/default.png' ?>" id="modal_image" alt="image" class="embed-responsive-item">
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-body p-0 py-1 pr-3">
                                <button name="like" data-image-id="" id="modal_like_button" class="btn py-0 shadow-none"><i class="fas fa-heart icon-7x fa-lg"></i><span> 5</span></button>
                                <div class="float-right" id="modal_image_date">created at</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="text-center" id="modal_image_tags"></div>
                        </div>
                    </div>
                    <div class="col-md-6 border py-2">
                        <div class="row mb-3 mb-sm-5 comment_box">
                            <div class="col" id="modal_image_comments"></div>
                        </div>
                        <div class="row position_bottom">
                            <form method="post" action="" id="modal_comment_form" name="send_comment" data-image-id="0">
                                <div class="form-row mx-auto">
                                    <div class="col-8">
                                        <input type="text" class="form-control" placeholder="Comment..." required>
                                    </div>
                                    <div class="col-3">
                                        <button type="submit" class="btn btn-success mb-2">Send</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show d-none" id="backdrop"></div>

<div id="load-more-container">
    <button id="load-more" data-page="0">Loading</button>
</div>
<ul class="article-list__pagination fixed article-list__pagination--inactive" id="post-pagination"></ul>
<script src="<?php echo URLROOT; ?>/assets/js/gallery.js"></script>
<?php require 'views/include/footer.php' ?>