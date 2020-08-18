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
                    <p class="my-auto mx-3">
                        <button id="modal_follow_button" class="shadow-none btn btn-sm" data-dismiss="modal" onclick="follow(this)" data-user-id="0">Follow</button>
                    </p>
                </div>
                <button class="close m-0" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="row mb-3">
                    <!-- image -->
                    <div class="col-md-6 border py-2">
                        <div class="row">
                            <div class="col embed-responsive embed-responsive-4by3">
                                <img src="<?= URLROOT . '/assets/img/images/default.png' ?>" id="modal_image" alt="image" class="embed-responsive-item">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center" id="modal_image_tags"></div>
                        </div>
                        <div class="row">
                            <div class="card-body p-0 py-1">
                                <button data-image-id="0" id="modal_like_button" onclick="like(this)" class="btn py-0 shadow-none"><i class="fas fa-heart icon-7x fa-lg"></i><span> 5</span></button>
                                <button data-image-id="0" type="submit" onclick="deleteImage(this)" id="modal_delete_button" class="btn py-0 shadow-none float-right d-none"><i class="fas fa-trash-alt fa-lg"></i></button>
                                <div class="float-right" id="modal_image_date">created at</div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <button data-image-path="0" data-user-id="0" onclick="changeProfilePicture(this)" id="modal_change_picture" class="btn btn-outline-success py-0 shadow-none float-right d-none">Set as a profile photo</button>
                        </div>
                    </div>
                    <!-- comments -->
                    <div class="col-md-6 border py-2">
                        <div class="row mb-3 mb-sm-5 comment_box">
                            <div class="col" id="modal_image_comments"></div>
                        </div>
                        <!-- form to send comment -->
                        <div class="row position_bottom">
                            <form method="post" onsubmit="addComment(this)" id="modal_comment_form" data-image-id="0">
                                <div class="form-row mx-auto">
                                    <div class="col-8">
                                        <input type="text" class="form-control" placeholder="Comment..." required maxlength="65">
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