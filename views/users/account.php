<?php require 'views/include/header.php' ?>

<div class="container">
    <?php if (isset($_SESSION['user'])) : ?>
        <div class="media pt-5">
            <img src="<?= URLROOT . '/assets/img/images/default.png' ?>" class="account-img mx-3" alt="Profile photo">
            <div class="media-body mx-5" style="width: 120px;">
                <h5 class="mt-0"><?= $_SESSION['user']['login']?></h5>
                <button class="btn btn-secondary float-right">Settings</button>
                <div><?= $_SESSION['user']['first_name'] . " " . $_SESSION['user']['last_name'] ?></div>
                <div><?= $_SESSION['user']['email'] ?></div>
                <button class="btn btn-success btn-block align-self-end">Follow</button>
            </div>
        </div>
    <?php endif ?>
</div>

<?php require 'views/include/footer.php' ?>