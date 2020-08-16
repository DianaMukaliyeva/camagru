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
                        <h5 class="font-weight-bold mt-0" id="profile_login"><?= $data['login'] ?></h5>
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
                    <div class="col text-center"><?= $data['images_amount'] ?> images</div>
                    <div class="col text-center" id="profile_followers_amount"><?= $data['followers_amount'] ?> followers</div>
                    <div class="col text-center"><?= $data['followed_amount'] ?> following</div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="text-center">
            <h1>User not found</h1>
        </div>
    <?php endif ?>
</div>

<!-- modal window for profile settings -->
<div class="modal fade" id="exampleModal" tabindex="-1">
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
<div class="modal-backdrop fade show d-none" id="backdrop"></div>

<?php require 'views/include/footer.php' ?>