<?php require 'views/include/header.php' ?>

<!-- user's profile -->
<div class="container pt-5 px-sm-5">
    <?php if (isset($data) && $data) : ?>
        <div class="row pb-3">
            <!-- profile image -->
            <div class="col-4">
                <img src="<?= URLROOT . '/' . $data['picture'] ?>" class="account-img float-right" alt="Profile photo">
            </div>
            <!-- profile info -->
            <div class="col-8">
                <div class="row">
                    <div class="col-sm-8">
                        <h5 class="font-weight-bold mt-0" id="profile_login" data-user-id="<?= $data['id'] ?>"><?= $data['login'] ?></h5>
                    </div>
                    <div class="col-sm-4 px-0">
                        <?php if (isset($_SESSION[APPNAME]['user']) && $_SESSION[APPNAME]['user']['id'] == $data['id']) : ?>
                            <button class="btn btn-outline-success float-right focus-btn" onclick="openModal('editProfile')"><i class="fas fa-user-edit"></i></button>
                        <?php else : ?>
                            <?php if ($data['user_follow'] == 0) : ?>
                                <button class="btn btn-success float-right focus-btn" id="profile_follow" onclick="follow(this)" data-user-id="<?= $data['id'] ?>">Follow</button>
                            <?php else : ?>
                                <button class="btn btn-outline-secondary float-right focus-btn" id="profile_follow" onclick="follow(this)" data-user-id="<?= $data['id'] ?>">Unfollow</button>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col py-2" id="profile_name"><?= $data['first_name'] . " " . $data['last_name'] ?></div>
                </div>
                <div class="row">
                    <div class="col py-2" id="profile_email"><?= $data['email'] ?></div>
                </div>
            </div>
        </div>
        <!-- amount of images, followed users and following users -->
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-8">
                <div class="row pb-2">
                    <div class="col text-center" role="button" onclick="switchtab(this.id)" id="images"><?= $data['images_amount'] ?> images</div>
                    <div class="col text-center" role="button" onclick="switchtab(this.id)" id="profile_followers_amount"><?= $data['followers_amount'] ?> followers</div>
                    <div class="col text-center" role="button" onclick="switchtab(this.id)" id="followed"><?= $data['followed_amount'] ?> following</div>
                </div>
            </div>
        </div>
        <hr>

        <!-- gallery -->
        <div class="container article-list px-0 px-sm-4" id="image-list"></div>

        <!-- followers -->
        <div class="container d-none text-center" id="followers-list">
            <h5>Users that follows <?= $data['login'] ?>:</h5>
            <div></div>
        </div>

        <!-- followed -->
        <div class="container d-none text-center" id="followed-list">
            <h5>Users followed by <?= $data['login'] ?>:</h5>
            <div></div>
        </div>

        <!-- button in case of big screen -->
        <div class="text-center" id="load-more-container">
            <button class="btn btn-outline-info d-none" id="load-more-image" data-page="0">Load more</button>
        </div>

    <?php else : ?>
        <div class="text-center">
            <h1>User not found</h1>
        </div>
    <?php endif ?>
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
                        <div class="col-sm-8"><input type="text" class="form-control" name="login" required></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">First name:</div>
                        <div class="col-sm-8"><input type="text" class="form-control" name="first_name" required></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">Last name:</div>
                        <div class="col-sm-8"><input type="text" class="form-control" name="last_name" required></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">email:</div>
                        <div class="col-sm-8"><input type="text" class="form-control" name="email" required></div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">New password:</div>
                        <div class="col-sm-4 pr-sm-1 pb-1 pb-sm-0"><input type="password" class="form-control" name="new_pswd" placeholder="new password"></div>
                        <div class="col-sm-4 pl-sm-1 p"><input type="password" class="form-control" name="new_pswd_confirm" placeholder="confirm new password"></div>
                    </div>
                    <div class="custom-control custom-checkbox py-2 mx-3">
                        <input type="checkbox" class="custom-control-input" name="notifications" id="profile-notifications">
                        <label class="focus-btn custom-control-label" for="profile-notifications">Send me email notifications</label>
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
                        <button id="modal_follow_button" class="focus-btn btn btn-sm" data-dismiss="modal" onclick="follow(this)" data-user-id="0">Follow</button>
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
                                <button type="submit" data-image-id="0" onclick="deleteImage(this)" id="modal_delete_button" class="btn py-0 shadow-none float-right d-none"><i class="fas fa-trash-alt fa-lg"></i></button>
                                <div class="float-right" id="modal_image_date">created at</div>
                            </div>
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

<script src="<?php echo URLROOT; ?>/assets/js/profile.js"></script>

<?php require 'views/include/footer.php' ?>