<?php require 'views/include/header.php' ?>

<!-- user's profile -->
<div class="container">
    <?php if (!isset($data) || !$data && isset($_SESSION[APPNAME]['user'])) : ?>
        <div class="media pt-5">
            <img src="<?= URLROOT . '/assets/img/images/default.png' ?>" class="account-img mx-3" alt="Profile photo">
            <div class="media-body mx-5" style="width: 120px;">
                <h5 class="mt-0"><?= $_SESSION[APPNAME]['user']['login']?></h5>
                <button class="btn btn-secondary float-right">Settings</button>
                <div><?= $_SESSION[APPNAME]['user']['first_name'] . " " . $_SESSION[APPNAME]['user']['last_name'] ?></div>
                <div><?= $_SESSION[APPNAME]['user']['email'] ?></div>
                <button class="btn btn-success btn-block align-self-end">Follow</button>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center"><?= $data ?></div>
    <?php endif ?>
</div>

<?php require 'views/include/footer.php' ?>