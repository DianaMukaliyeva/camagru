<?php require 'views/include/header.php' ?>

<!-- user's profile -->
<div class="container pt-5 px-sm-5">
    <?php if (isset($data) && $data) : ?>
        <div class="row pb-3">
            <!-- profile image -->
            <div class="col-4">
                <img src="<?= URLROOT . '/' . $data['picture'] ?>" name="picture_<?= $data['id'] ?>" class="account-img float-right" alt="Profile photo">
            </div>
            <!-- profile info -->
            <div class="col-8">
                <div class="row overflow-auto">
                    <div class="col-sm-8">
                        <h5 class="font-weight-bold mt-0" id="profile_login" data-user-id="<?= $data['id'] ?>"><?= $data['login'] ?></h5>
                    </div>
                    <div class="col-sm-4 px-0">
                        <?php if (isset($_SESSION[APPNAME]['user']) && $_SESSION[APPNAME]['user']['id'] == $data['id']) : ?>
                            <button class="btn btn-outline-success float-right shadow-none" onclick="openModal('editProfile')"><i class="fas fa-user-edit"></i></button>
                        <?php else : ?>
                            <?php if ($data['user_follow'] == 0) : ?>
                                <button class="btn btn-success float-right shadow-none" id="profile_follow" onclick="follow(this)" data-user-id="<?= $data['id'] ?>">Follow</button>
                            <?php else : ?>
                                <button class="btn btn-outline-secondary float-right shadow-none" id="profile_follow" onclick="follow(this)" data-user-id="<?= $data['id'] ?>">Unfollow</button>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row overflow-auto">
                    <div class="col py-2" id="profile_name"><?= $data['first_name'] . " " . $data['last_name'] ?></div>
                </div>
                <div class="row overflow-auto">
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
        <div class="container article-list px-0 px-sm-4" id="article-list"></div>

        <!-- followers -->
        <div class="container d-none text-center" id="followers-list">
            <h5 class="pb-4">Users that follows <?= $data['login'] ?>:</h5>
            <div></div>
        </div>

        <!-- followed -->
        <div class="container d-none text-center" id="followed-list">
            <h5 class="pb-5">Users followed by <?= $data['login'] ?>:</h5>
            <div></div>
        </div>

        <!-- button in case of big screen -->
        <div class="text-center" id="load-more-container">
            <button class="btn btn-outline-info d-none" id="load-more" data-page="0">Load more</button>
        </div>

    <?php else : ?>
        <div class="text-center">
            <h1>User not found</h1>
        </div>
    <?php endif ?>
</div>

<script src="<?php echo URLROOT; ?>/assets/js/gallery.js"></script>

<?php require 'views/include/footer.php' ?>