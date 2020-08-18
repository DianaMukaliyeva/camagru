<!-- Toast message for different warnings or confirmations -->
<div class="d-flex justify-content-center align-items-center">
    <div id="message">
        <button type="button" class="close" onclick="closeMessage(this)">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="toast-body"></div>
    </div>
</div>

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

<!-- modal window for profile settings -->
<div class="modal fade" id="settings" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile edit settings</h5>
                <button type="button" class="close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" onsubmit="saveChanges(this)">
                    <div class="row py-1">
                        <div class="col-sm-4">Login:</div>
                        <div class="col-sm-8"><input type="text" class="form-control" name="login" required maxlength="15"></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">First name:</div>
                        <div class="col-sm-8"><input type="text" class="form-control" name="first_name" required maxlength="25"></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">Last name:</div>
                        <div class="col-sm-8"><input type="text" class="form-control" name="last_name" required maxlength="25"></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">email:</div>
                        <div class="col-sm-8"><input type="text" class="form-control" name="email" required></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">New password:</div>
                        <div class="col-sm-4 pr-sm-1 pb-1 pb-sm-0"><input type="password" class="form-control" name="new_pswd" placeholder="new password" maxlength="25"></div>
                        <div class="col-sm-4 pl-sm-1 p"><input type="password" class="form-control" name="new_pswd_confirm" placeholder="confirm new password" maxlength="25"></div>
                    </div>
                    <div class="custom-control custom-checkbox py-2 mx-3">
                        <input type="checkbox" class="custom-control-input" name="notifications" id="profile-notifications">
                        <label class="shadow-none custom-control-label" for="profile-notifications">Send me email notifications</label>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">Password:*</div>
                        <div class="col-sm-8"><input type="password" class="form-control" name="old_pswd" placeholder="Required to update account" required></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="editForm" class="btn btn-success">Save changes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-backdrop fade show d-none" id="backdrop"></div>