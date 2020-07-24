<?php require 'views/include/header.php' ?>

<div class="col-md-6 m-auto">
    <?php if (isset($data['message'])) : ?>
        <div class="mt-5 alert <?= $data['message']['class'] ?>"><?= $data['message']['content'] ?></div>
    <?php endif ?>
    <?php if (isset($data['reset'])) : ?>
        <div class="card card-body bg-light mt-5">
            <h2>Change your password</h2>
            <form action="<?= URLROOT ?>/users/resetPassword" method="post">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="hidden" name="email" value="<?= isset($data['email']) ? $data['email'] : '' ?>">
                    <input type="password" name="password" class="form-control form-control-lg <?= isset($data['password_err']) ? 'is-invalid' : '' ?>">
                    <span class="invalid-feedback"><?= isset($data['password_err']) ? $data['password_err'] : '' ?></span>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm password:</label>
                    <input type="password" name="confirm_password" class="form-control form-control-lg <?= isset($data['confirm_password_err']) ? 'is-invalid' : '' ?>">
                    <span class="invalid-feedback"><?= isset($data['confirm_password_err']) ? $data['confirm_password_err'] : '' ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Reset password" class="btn btn-success btn-block">
                    </div>
                </div>
            </form>
        </div>
    <?php else : ?>
        <div class="card card-body bg-light mt-5">
            <form action="<?= URLROOT ?>/users/resetPassword" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control form-control-lg <?= isset($data['email_err']) ? 'is-invalid' : '' ?>">
                    <span class="invalid-feedback"><?= isset($data['email_err']) ? $data['email_err'] : '' ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Send reset password email" class="btn btn-success btn-block">
                    </div>
                </div>
        </div>
        </form>
    <?php endif ?>
</div>

<?php require 'views/include/footer.php' ?>