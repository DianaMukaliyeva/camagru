<?php require 'views/include/header.php' ?>

<!-- user's profile -->
<div class="container pt-5 px-sm-5">
    <div class="row pb-3">
        <div class="col-4">
            <img src="<?= URLROOT . '/assets/img/images/default.png' ?>" class="account-img float-right" alt="Profile photo">
        </div>
        <div class="col-8">
            <div class="row">
                <div class="col-8">
                    <h5 class="font-weight-bold mt-0"><?= $_SESSION[APPNAME]['user']['login'] ?></h5>
                </div>
                <div class="col-4 px-0"><button class="btn btn-outline-success float-right"><i class="fas fa-user-edit"></i></button></div>
            </div>
            <div class="row">
                <div class="col py-2"><?= $_SESSION[APPNAME]['user']['first_name'] . " " . $_SESSION[APPNAME]['user']['last_name'] ?></div>
            </div>
            <div class="row">
                <div class="col py-2"><?= $_SESSION[APPNAME]['user']['email'] ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-8">
            <div class="row pb-2">
                <div class="col text-center">images</div>
                <div class="col text-center">followers</div>
                <div class="col text-center">following</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-8">
            <button class="btn btn-success btn-block focus-btn">Follow</button>
        </div>
    </div>
    <!-- <?php if (!isset($data) || !$data && isset($_SESSION[APPNAME]['user'])) : ?>
        <div class="media pt-5">
            <img src="<?= URLROOT . '/assets/img/images/default.png' ?>" class="account-img mx-3" alt="Profile photo">
            <div class="media-body mx-5" style="width: 120px;">
                <h5 class="mt-0"><?= $_SESSION[APPNAME]['user']['login'] ?></h5>
                <button class="btn btn-secondary float-right">Settings</button>
                <div><?= $_SESSION[APPNAME]['user']['first_name'] . " " . $_SESSION[APPNAME]['user']['last_name'] ?></div>
                <div><?= $_SESSION[APPNAME]['user']['email'] ?></div>
                <button class="btn btn-success btn-block align-self-end">Follow</button>
            </div>
        </div>
    <?php else : ?>
        <div class="text-center"><?= $data ?></div>
    <?php endif ?> -->
</div>

<?php require 'views/include/footer.php' ?>