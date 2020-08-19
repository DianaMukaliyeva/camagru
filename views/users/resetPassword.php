<?php require 'views/include/header.php' ?>

<!-- form to change password -->
<div class="col-md-6 m-auto">
    <?php if (isset($data['message'])) : ?>
        <div class="mt-5 alert <?= $data['message']['class'] ?>"><?= $data['message']['content'] ?></div>
    <?php endif ?>
    <div class="card card-body bg-light mt-5">
        <h2>Change your password</h2>
        <form onsubmit="resetPassword(this)" method="post" data-email="<?= isset($data['email']) ? $data['email'] : '' ?>">
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" onkeyup="checkPasswordStrength(this)" name="password" class="shadow-none form-control form-control-lg" maxlength="128" required>
                <span name="password_err" class="invalid-feedback"></span>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password:</label>
                <input type="password" name="confirm_password" class="shadow-none form-control form-control-lg" maxlength="128" required>
                <span name="confirm_password_err" class="invalid-feedback"></span>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-success btn-block">Reset password</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require 'views/include/footer.php' ?>